<?php

class EventFaqController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        //$this->objPrivilege->canViewFaq();
    }

    public function index($faq_catid = 0)
    {   

        $canEdit = $this->objPrivilege->canEditFaq($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set('includeEditor', true);
        $faq_catid = FatUtility::int($faq_catid);
        $this->set("faq_catid", $faq_catid);
    
        //$this->_template->render();
        $this->_template->render(true, true, 'event-faq/index.php');
    }

    public function search()
    {
       
        $srch = EventFaq::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['faq_identifier', 'faq_id', 'faq_category', 'faq_active', 'faq_title']);
        $srch->addOrder('faq_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditFaq($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        // $this->_template->render(false, false);
        $this->_template->render(false, false, 'event-faq/search.php');
    }

    public function form($faqId)
    {
        
        $faqId = FatUtility::int($faqId);
        $frm = $this->getForm($faqId);
        if (0 < $faqId) {
            $data = EventFaq::getAttributesById($faqId, ['faq_id', 'faq_identifier', 'faq_category', 'faq_active']);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqId', $faqId);
        $this->set('frm', $frm);
        //$this->_template->render(false, false);
        $this->_template->render(false, false, 'event-faq/form.php');
    }

    public function setup()
    {
        $this->objPrivilege->canEditFaq();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $faqId = $post['faq_id'];
        unset($post['faq_id']);
        if ($faqId == 0) {
            $post['faq_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new EventFaq($faqId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($faqId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = EventFaq::getAttributesByLangId($langId, $faqId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $faqId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('faqId', $faqId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($faqId = 0, $lang_id = 0)
    {
       
        $faqId = FatUtility::int($faqId);
        $lang_id = FatUtility::int($lang_id);
        if ($faqId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        // echo "Hello World...!!";
        $langFrm = $this->getLangForm($faqId, $lang_id);
        

        $langData = EventFaq::getAttributesByLangId($lang_id, $faqId);
        

        
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('faqId', $faqId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        //$this->_template->render(false, false);
        
        $this->_template->render(false, false, 'event-faq/lang-form.php');
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditFaq();
        $post = FatApp::getPostedData();
        $faqId = $post['faq_id'];
        $lang_id = $post['lang_id'];
        if ($faqId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($faqId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['faq_id']);
        unset($post['lang_id']);
        $data = [
            'faqlang_lang_id' => $lang_id,
            'faqlang_faq_id' => $faqId,
            'faq_title' => $post['faq_title'],
            'faq_description' => $post['faq_description']
        ];
        $obj = new EventFaq($faqId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = EventFaq::getAttributesByLangId($langId, $faqId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('faqId', $faqId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditFaq();
        $faqId = FatApp::getPostedData('faqId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $faqId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = EventFaq::getAttributesById($faqId, ['faq_id', 'faq_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new EventFaq($faqId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditFaq();
        $faq_id = FatApp::getPostedData('faqId', FatUtility::VAR_INT, 0);
        if ($faq_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $LessonPackageObj = new EventFaq($faq_id);
        if (!$LessonPackageObj->deleteRecord($faq_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function getForm($faqId = 0)
    {
        $faqId = FatUtility::int($faqId);
        $frm = new Form('frmFaq');
        $frm->addHiddenField('', 'faq_id', $faqId);
        $frm->addRequiredField(Label::getLabel('LBL_Faq_Identifier', $this->adminLangId), 'faq_identifier');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_faq_category', $this->adminLangId), 'faq_category', Faq::getFaqCategoryArr($this->adminLangId));
        $fld->requirement->setRequired(true);
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'faq_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($faqId = 0, $lang_id = 0)
    {
        $frm = new Form('frmFaqLang');
        $frm->addHiddenField('', 'faq_id', $faqId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Faq_Title', $this->adminLangId), 'faq_title');
        $frm->addHtmlEditor(Label::getLabel('LBL_Faq_Text', $this->adminLangId), 'faq_description');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
