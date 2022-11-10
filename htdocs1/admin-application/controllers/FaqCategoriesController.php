<?php

class FaqCategoriesController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewFaqCategory($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditFaqCategory($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->objPrivilege->canViewFaqCategory();
        $searchFrm = $this->getSearchForm();
        $this->set("searchFrm", $searchFrm);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewFaqCategory();
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = FaqCategory::getSearchObject($this->adminLangId);
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('fc.faqcat_identifier', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('fc_l.faqcat_name', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->doNotLimitRecords();
        $srch->doNotCalculateRecords();
        $srch->addOrder('faqcat_active', 'DESC');
        $srch->addOrder('faqcat_display_order', 'asc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canViewFaq = $this->objPrivilege->canViewFaq(0, true);
        $this->set("canViewFaq", $canViewFaq);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form($faqcat_id = 0)
    {
        $this->objPrivilege->canEditFaqCategory();
        $faqcat_id = FatUtility::int($faqcat_id);
        $faqCatFrm = $this->getForm();
        $faqCatFrm->fill(['faqcat_id' => $faqcat_id]);
        if (0 < $faqcat_id) {
            $data = FaqCategory::getAttributesById($faqcat_id, ['faqcat_id', 'faqcat_identifier', 'faqcat_active', 'faqcat_type', 'faqcat_featured']);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $faqCatFrm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqcat_id', $faqcat_id);
        $this->set('faqCatFrm', $faqCatFrm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditFaqCategory();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $faqcat_id = FatUtility::int($post['faqcat_id']);
        unset($post['faqcat_id']);
        $record = new FaqCategory($faqcat_id);
        if ($faqcat_id == 0) {
            $display_order = $record->getMaxOrder();
            $post['faqcat_display_order'] = $display_order;
        }
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($faqcat_id > 0) {
            $catId = $faqcat_id;
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = FaqCategory::getAttributesByLangId($langId, $faqcat_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $catId = $record->getMainTableRecordId();
            $newTabLangId = $this->adminLangId;
        }
        $this->set('msg', Label::getLabel('LBL_Category_Setup_Successful'));
        $this->set('catId', $catId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($faqcat_id = 0, $lang_id = 0)
    {
        $this->objPrivilege->canViewFaqCategory();
        $faqcat_id = FatUtility::int($faqcat_id);
        $lang_id = FatUtility::int($lang_id);
        if ($faqcat_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $faqCatLangFrm = $this->getLangForm();
        $langData = FaqCategory::getAttributesByLangId($lang_id, $faqcat_id);
        $langData['faqcat_id'] = $faqcat_id;
        $langData['lang_id'] = $lang_id;
        if ($langData) {
            $faqCatLangFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqcat_id', $faqcat_id);
        $this->set('faqcat_lang_id', $lang_id);
        $this->set('faqCatLangFrm', $faqCatLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditFaqCategory();
        $post = FatApp::getPostedData();
        $faqcat_id = $post['faqcat_id'];
        $lang_id = $post['lang_id'];
        if ($faqcat_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($faqcat_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['faqcat_id']);
        unset($post['lang_id']);
        $data = [
            'faqcatlang_lang_id' => $lang_id,
            'faqcatlang_faqcat_id' => $faqcat_id,
            'faqcat_name' => $post['faqcat_name'],
        ];
        $faqcatObj = new FaqCategory($faqcat_id);
        if (!$faqcatObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($faqcatObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = FaqCategory::getAttributesByLangId($langId, $faqcat_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('LBL_Category_Setup_Successful'));
        $this->set('catId', $faqcat_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditFaqCategory();
        $faqcat_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($faqcat_id < 1) {
            FatUtility::dieJsonError($this->str_invalid_request_id);
        }
        $res = FaqCategory::getAttributesById($faqcat_id, ['faqcat_id']);
        if ($res == false) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->markAsDeleted($faqcat_id);
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function deleteSelected()
    {
        $this->objPrivilege->canEditFaqCategory();
        $faqcatIdsArr = FatUtility::int(FatApp::getPostedData('faqcat_ids'));
        if (empty($faqcatIdsArr)) {
            FatUtility::dieWithError(
                    Label::getLabel('MSG_INVALID_REQUEST')
            );
        }
        foreach ($faqcatIdsArr as $faqcatId) {
            if (1 > $faqcatId) {
                continue;
            }
            $this->markAsDeleted($faqcatId);
        }
        $this->set('msg', $this->str_delete_record);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function markAsDeleted($faqcatId)
    {
        $faqcatId = FatUtility::int($faqcatId);
        if (1 > $faqcatId) {
            FatUtility::dieWithError(
                    Label::getLabel('MSG_INVALID_REQUEST')
            );
        }
        $faqCatObj = new FaqCategory($faqcatId);
        $faqCatObj->assignValues(array(FaqCategory::tblFld('deleted') => 1));
        if (!$faqCatObj->save()) {
            Message::addErrorMessage($faqCatObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
    }

    public function updateOrder()
    {
        $this->objPrivilege->canEditFaqCategory();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $faqCatObj = new FaqCategory();
            if (!$faqCatObj->updateOrder($post['faqcat'])) {
                Message::addErrorMessage($faqCatObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_Order_Updated_Successfully'));
        }
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditFaqCategory();
        $faqcatId = FatApp::getPostedData('faqcatId', FatUtility::VAR_INT, 0);
        if (0 >= $faqcatId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = FaqCategory::getAttributesById($faqcatId, ['faqcat_id', 'faqcat_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['faqcat_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $this->updateFaqCatStatus($faqcatId, $status);
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function toggleBulkStatuses()
    {
        $this->objPrivilege->canEditFaqCategory();
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, -1);
        $faqcatIdsArr = FatUtility::int(FatApp::getPostedData('faqcat_ids'));
        if (empty($faqcatIdsArr) || -1 == $status) {
            FatUtility::dieWithError(
                    Label::getLabel('MSG_INVALID_REQUEST')
            );
        }
        foreach ($faqcatIdsArr as $faqcatId) {
            if (1 > $faqcatId) {
                continue;
            }
            $this->updateFaqCatStatus($faqcatId, $status);
        }
        $this->set('msg', $this->str_update_record);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function updateFaqCatStatus($faqcatId, $status)
    {
        $status = FatUtility::int($status);
        $faqcatId = FatUtility::int($faqcatId);
        if (1 > $faqcatId || -1 == $status) {
            FatUtility::dieWithError(
                    Label::getLabel('MSG_INVALID_REQUEST')
            );
        }
        $obj = new FaqCategory($faqcatId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Keyword'), 'keyword');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search'), ['onclick' => 'clearSearch();']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function getForm()
    {
        $this->objPrivilege->canEditFaqCategory();
        $langId = $this->adminLangId;
        $frm = new Form('frmFaqCat');
        $frm->addHiddenField('', 'faqcat_id');
        $frm->addRequiredField(Label::getLabel('LBL_Category_Identifier'), 'faqcat_identifier');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($langId);
        $faqCatTypeArr = FaqCategory::getFaqCatTypeArr($langId);
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'faqcat_active', $activeInactiveArr, '', [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Type'), 'faqcat_type', $faqCatTypeArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    private function getLangForm()
    {
        $frm = new Form('frmFaqCatLang');
        $frm->addHiddenField('', 'faqcat_id');
        $frm->addHiddenField('', 'lang_id');
        $frm->addRequiredField(Label::getLabel('LBL_Category_Name'), 'faqcat_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update'));
        return $frm;
    }

}
