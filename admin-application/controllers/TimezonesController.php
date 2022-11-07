<?php

class TimezonesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTimezones();
    }

    public function index()
    {
        $search = $this->getSearchForm();
        $this->set("canEdit", $this->objPrivilege->canEditTimezones(AdminAuthentication::getLoggedAdminId(), true));
        $this->set("search", $search);
        $this->_template->addJs('js/import-export.js');
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = Timezone::getSearchObject(false, $this->adminLangId);
        $srch->addFld('tz.* , tz_l.timezonelang_text, IFNULL(timezonelang_text, timezone_identifier) as timezone_name');
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('tz.timezone_identifier', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('tz_l.timezonelang_text', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $records = [];
        if ($rs) {
            $records = FatApp::getDb()->fetchAll($rs);
        }
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditTimezones($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form($timezoneId)
    {
        $this->objPrivilege->canEditTimezones();
        $frm = $this->getForm($timezoneId);
        if (!empty($timezoneId)) {
            $data = Timezone::getAttributesById($timezoneId, [
                        'timezone_id',
                        'timezone_offset',
                        'timezone_identifier',
                        'timezone_name',
                        'timezone_active'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('timezone_id', $timezoneId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditTimezones();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $timezoneId = $post['timezone_id'];
        unset($post['timezone_id']);
        $record = new Timezone($timezoneId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($timezoneId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Timezone::getAttributesByLangId($langId, $timezoneId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $timezoneId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', Label::getLabel('LBL_Updated_Successfully', $this->adminLangId));
        $this->set('timezoneId', $timezoneId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($timezoneId = 0, $lang_id = 0)
    {
        $timezoneId = FatUtility::int($timezoneId);
        $lang_id = FatUtility::int($lang_id);
        if ($timezoneId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($timezoneId, $lang_id);
        $langData = Timezone::getAttributesByLangId($lang_id, $timezoneId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('timezoneId', $timezoneId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditTimezones();
        $post = FatApp::getPostedData();
        $timezoneId = $post['timezone_id'];
        $lang_id = $post['lang_id'];
        if ($timezoneId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($timezoneId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['timezone_id']);
        unset($post['lang_id']);
        $data = [
            'timezonelang_lang_id' => $lang_id,
            'timezonelang_timezone_id' => $timezoneId,
            'timezonelang_text' => $post['timezonelang_text']
        ];
        $timezoneObj = new Timezone($timezoneId);
        if (!$timezoneObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($timezoneObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Timezone::getAttributesByLangId($langId, $timezoneId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('timezoneId', $timezoneId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($timezoneId = 0)
    {
        $timezoneId = FatUtility::int($timezoneId);
        $frm = new Form('frmTimezone');
        $frm->addHiddenField('', 'timezone_id', $timezoneId);
        $frm->addRequiredField(Label::getLabel('LBL_Timezone_Identifier', $this->adminLangId), 'timezone_identifier', '', ['readonly' => 'readonly']);
        $frm->addRequiredField(Label::getLabel('LBL_Timezone_name', $this->adminLangId), 'timezone_name');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'timezone_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($timezoneId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTimezoneLang');
        $frm->addHiddenField('', 'timezone_id', $timezoneId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Timezone_Name', $this->adminLangId), 'timezonelang_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditTimezones();
        $timezoneId = FatApp::getPostedData('timezoneId');
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $timezoneObj = new Timezone($timezoneId);
        if (!$timezoneObj->changeStatus($status)) {
            Message::addErrorMessage($timezoneObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

}
