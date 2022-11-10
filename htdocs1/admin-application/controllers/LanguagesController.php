<?php

class LanguagesController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewLanguage($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditLanguage($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->objPrivilege->canViewLanguage();
        $this->set("search", $this->getSearchForm());
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
        $this->objPrivilege->canViewLanguage();
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = Language::getSearchObject(false, $this->adminLangId);
        $srch->addFld('l.* ');
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('l.language_code', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('l.language_name', 'like', '%' . $post['keyword'] . '%', 'OR');
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
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form($languageId)
    {
        $this->objPrivilege->canEditLanguage();
        $languageId = FatUtility::int($languageId);
        $frm = $this->getForm($languageId);
        if (0 < $languageId) {
            $data = Language::getAttributesById($languageId, ['language_id', 'language_code', 'language_name', 'language_active', 'language_layout_direction']);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('language_id', $languageId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditLanguage();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $languageId = FatApp::getPostedData('language_id', FatUtility::VAR_INT, 0);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $record = new Language($languageId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage(Label::getLabel('MSG_This_language_code_is_not_available', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('LBL_Language_Setup_Succesfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($languageId = 0)
    {
        $this->objPrivilege->canViewLanguage();
        $languageId = FatUtility::int($languageId);
        $frm = new Form('frmLanguage');
        $frm->addHiddenField('', 'language_id', $languageId);
        $frm->addRequiredField(Label::getLabel('LBL_Language_code', $this->adminLangId), 'language_code');
        $frm->addRequiredField(Label::getLabel('LBL_Language_name', $this->adminLangId), 'language_name');
        $frm->addRadioButtons(Label::getLabel("LBL_Language_Layout_Direction", $this->adminLangId), 'language_layout_direction', applicationConstants::getLayoutDirections($this->adminLangId), '', ['class' => 'list-inline']);
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'language_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function getLanguageFlags()
    {
        $dir = CONF_INSTALLATION_PATH . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'flags';
        return array_diff(scandir($dir, 1), [".", ".."]);
    }

    public function media($languageId)
    {
        $this->objPrivilege->canEditLanguage();
        if (0 >= $languageId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $selectedFlag = Language::getAttributesById($languageId, 'language_flag');
        $flags = $this->getlanguageFlags();
        $this->set('selectedFlag', $selectedFlag);
        $this->set('flags', $flags);
        $this->set('language_id', $languageId);
        $this->_template->render(false, false);
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditLanguage();
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        if (0 >= $languageId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Language::getAttributesById($languageId, ['language_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['language_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $countryObj = new Language($languageId);
        if (!$countryObj->changeStatus($status)) {
            Message::addErrorMessage($countryObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function updateImage()
    {
        $this->objPrivilege->canEditLanguage();
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $flag = FatApp::getPostedData('flag', FatUtility::VAR_STRING, '');
        if (0 >= $languageId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Language::getAttributesById($languageId, ['language_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data['language_flag'] = $flag;
        $record = new Language($languageId);
        $record->assignValues($data);
        if (!$record->save()) {
            Message::addErrorMessage(Label::getLabel('MSG_Unable_to_set_image', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

}
