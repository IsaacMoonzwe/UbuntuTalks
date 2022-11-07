<?php

class AccommodationsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewContentBlocks();
        $this->admin_id = AdminAuthentication::getLoggedAdminId();
    }

    public function index()
    {
        $this->set('includeEditor', true);
        $this->_template->render();
    }

    public function search()
    {
        $srch = Accommodations::getSearchObject($this->adminLangId, false);
        $srch->addOrder('accommodations_active', 'DESC');
        $srch->addOrder('epage_id', 'DESC');
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $this->set("activeInactiveArr", $activeInactiveArr);
        $this->set("arr_listing", $records);
        $this->set("canEdit", $this->objPrivilege->canEditContentBlocks());
        $this->_template->render(false, false);
    }

    public function form($epage_id = 0)
    {
        $this->objPrivilege->canViewContentBlocks();
        $epage_id = FatUtility::int($epage_id);
        $blockFrm = $this->getForm($epage_id, $this->adminLangId);
        if (0 < $epage_id) {
            $data = Accommodations::getAttributesById($epage_id, array('epage_id', 'accommodations_identifier', 'accommodations_active'));
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $blockFrm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('epage_id', $epage_id);
        $this->set('blockFrm', $blockFrm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditContentBlocks();
        $frm = $this->getForm(0, $this->adminLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $epage_id = $post['epage_id'];
        $extraPage = new Accommodations($epage_id);
        if (!$extraPage->loadFromDb()) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }

        $extraPage->assignValues($post);
        if (!$extraPage->save()) {
            Message::addErrorMessage($extraPage->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Accommodations::getAttributesByLangId($langId, $epage_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('LBL_Setup_Successful', $this->adminLangId));
        $this->set('epageId', $epage_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($epage_id = 0, $lang_id = 0)
    {
        $epage_id = FatUtility::int($epage_id);
        $lang_id = FatUtility::int($lang_id);
        if ($epage_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $epageData = Accommodations::getAttributesById($epage_id);
        $blockLangFrm = $this->getLangForm($epage_id, $lang_id);
        $langData = Accommodations::getAttributesByLangId($lang_id, $epage_id);
        if ($langData) {
            $blockLangFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('epage_id', $epage_id);
        $this->set('epage_lang_id', $lang_id);
        $this->set('blockLangFrm', $blockLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->set('epageData', $epageData);
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $post = FatApp::getPostedData();
        $epage_id = FatUtility::int($post['epage_id']);
        $lang_id = FatUtility::int($post['lang_id']);
        if ($epage_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($epage_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJSONError(Message::getHtml());
        }
        unset($post['epage_id']);
        unset($post['lang_id']);
        $data = array(
            'epagelang_lang_id' => $lang_id,
            'epagelang_epage_id' => $epage_id,
            'accommodations_label' => $post['accommodations_label'],
            'accommodations_content' => $post['accommodations_content'],
        );
        $epageObj = new Accommodations($epage_id);
        if (!$epageObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($epageObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Accommodations::getAttributesByLangId($langId, $epage_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('LBL_Setup_Successful', $this->adminLangId));
        $this->set('epageId', $epage_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditContentBlocks();
        $epageId = FatApp::getPostedData('epageId', FatUtility::VAR_INT, 0);
        if (0 == $epageId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $contentBlockData = Accommodations::getAttributesById($epageId, array('accommodations_active'));
        if (!$contentBlockData) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($contentBlockData['accommodations_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $this->updateEPageStatus($epageId, $status);
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    private function updateEPageStatus($epageId, $status)
    {
        $status = FatUtility::int($status);
        $epageId = FatUtility::int($epageId);
        if (1 > $epageId || -1 == $status) {
            FatUtility::dieWithError(
                    Label::getLabel('MSG_INVALID_REQUEST', $this->adminLangId)
            );
        }
        $EPageObj = new Accommodations($epageId);
        if (!$EPageObj->changeStatus($status)) {
            Message::addErrorMessage($EPageObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
    }

    private function getForm($epage_id = 0, $langId = 0)
    {
        $this->objPrivilege->canViewContentBlocks();
        $epage_id = FatUtility::int($epage_id);
        $frm = new Form('frmBlock');
        $frm->addHiddenField('', 'epage_id', 0);
        $frm->addRequiredField(Label::getLabel('LBL_Page_Identifier', $this->adminLangId), 'accommodations_identifier');

        $activeInactiveArr = applicationConstants::getActiveInactiveArr($langId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'accommodations_active', $activeInactiveArr, '', array(), '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($epage_id = 0, $lang_id = 0)
    {
        $frm = new Form('frmBlockLang');
        $frm->addHiddenField('', 'epage_id', $epage_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Page_Title', $this->adminLangId), 'accommodations_label');
        $frm->addHtmlEditor(Label::getLabel('LBL_Page_Content', $this->adminLangId), 'accommodations_content');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

}
