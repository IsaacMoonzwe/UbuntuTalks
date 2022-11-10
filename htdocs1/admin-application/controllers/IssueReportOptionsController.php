<?php

class IssueReportOptionsController extends AdminBaseController
{

    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewIssueReportOptions();
        $this->canEdit = $this->objPrivilege->canEditIssueReportOptions($this->admin_id, true);
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm($this->adminLangId);
        $this->set('frmSearch', $frmSearch);
        $this->set('canEdit', $this->canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $searchForm = $this->getSearchForm($this->adminLangId);
        if (!$post = $searchForm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError($searchForm->getValidationErrors());
        }
        $issoptobj = new IssueReportOptions($this->adminLangId);
        $srch = $issoptobj->getAllOptions($this->adminLangId, false);
        $srch->addMultipleFields([
            'tissueopt_id',
            'tissueopt_user_type',
            'tissueopt_active',
            'tissueopt_display_order',
            'IFNULL(tissueoptlang_title, tissueopt_identifier) as optLabel',
        ]);
        $srch->addOrder('tissueopt_display_order', 'asc');
        if (!empty($post['keyword'])) {
            $srch->addCondition('tissueopt_identifier', 'like', '%' . $post['keyword'] . '%');
        }
        if (isset($post['tissueopt_user_type']) && $post['tissueopt_user_type'] != '') {
            $srch->addCondition('tissueopt_user_type', '=', $post['tissueopt_user_type']);
        }
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("canEdit", $this->canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->set('userTypesOptionsArr', $this->getUserTypesOptions($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function form($optId)
    {
        $optId = FatUtility::int($optId);
        $frm = $this->getForm($optId);
        if (0 < $optId) {
            $data = IssueReportOptions::getAttributesById($optId, [
                        'tissueopt_id',
                        'tissueopt_user_type',
                        'tissueopt_identifier',
                        'tissueopt_display_order'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('optId', $optId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditIssueReportOptions();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $optId = $post['tissueopt_id'];
        unset($post['tissueopt_id']);
        $record = new IssueReportOptions($optId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($optId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = IssueReportOptions::getAttributesByLangId($langId, $optId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $optId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('optId', $optId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($optId = 0, $lang_id = 0)
    {
        $optId = FatUtility::int($optId);
        $lang_id = FatUtility::int($lang_id);
        if ($optId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($optId, $lang_id);
        $langData = IssueReportOptions::getAttributesByLangId($lang_id, $optId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('optId', $optId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditIssueReportOptions();
        $post = FatApp::getPostedData();
        $optId = $post['tissueopt_id'];
        $lang_id = $post['lang_id'];
        if ($optId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($optId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['tissueopt_id']);
        unset($post['lang_id']);
        $data = ['tissueoptlang_lang_id' => $lang_id, 'tissueoptlang_title' => $post['tissueoptlang_title']];
        $obj = new IssueReportOptions($optId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = IssueReportOptions::getAttributesByLangId($langId, $optId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('optId', $optId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditIssueReportOptions();
        $optId = FatApp::getPostedData('optId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $optId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = IssueReportOptions::getAttributesById($optId, ['tissueopt_id', 'tissueopt_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new IssueReportOptions($optId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditIssueReportOptions();
        $optId = FatApp::getPostedData('optId', FatUtility::VAR_INT, 0);
        if ($optId < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $optObj = new IssueReportOptions($optId);
        if (!$optObj->deleteOption($optId)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function getForm($sLangId = 0)
    {
        $sLangId = FatUtility::int($sLangId);
        $userTypesOptions = $this->getUserTypesOptions($this->adminLangId);
        $frm = new Form('frmIssueReoprtOption');
        $frm->addHiddenField('', 'tissueopt_id', $sLangId);
        $frm->addSelectBox(Label::getLabel('LBL_User_Type', $this->adminLangId), 'tissueopt_user_type', $userTypesOptions, 0, [], '');
        $frm->addRequiredField(Label::getLabel('LBL_Option_Identifier', $this->adminLangId), 'tissueopt_identifier');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'tissueopt_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($optId = 0, $lang_id = 0)
    {
        $frm = new Form('frmIssueReoprtOption');
        $frm->addHiddenField('', 'tissueopt_id', $optId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Option_Identifier', $this->adminLangId), 'tissueoptlang_title');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function updateOrder()
    {
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $optObj = new IssueReportOptions();
            if (!$optObj->updateOrder($post['IssueReportOptions'])) {
                Message::addErrorMessage($optObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }

    private function getSearchForm(int $langId)
    {
        $frm = new Form('frmIssueReoprtOptions');
        $userTypesOptions = $this->getUserTypesOptions($langId);
        $frm->addSelectBox(Label::getLabel('LBL_User_Type', $langId), 'tissueopt_user_type', $userTypesOptions, '', [], Label::getLabel('LBL_SELECT'));
        $frm->addTextBox(Label::getLabel('LBL_Option_Identifier', $langId), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $langId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $langId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function getUserTypesOptions(int $langId): array
    {
        return ['0' => Label::getLabel('LBL_Both', $langId)] + User::getUserTypesArr($langId);
    }

}
