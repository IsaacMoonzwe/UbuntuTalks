<?php

class SpokenLanguageController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewSpokenLanguage();
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditSpokenLanguage($this->admin_id, true);
        $this->set('frmSearch', $this->getSearchForm());
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $data = FatApp::getPostedData();
        $searchForm = $this->getSearchForm();
        $post = $searchForm->getFormDataFromArray($data);
        $srch = SpokenLanguage::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['slanguage_id', 'slanguage_code', 'slanguage_identifier', 'slanguage_active', 'slanguage_name',]);
        if (!empty($post['keyword'])) {
            $srch->addCondition('slanguage_identifier', 'like', '%' . $post['keyword'] . '%');
        }
        $srch->addOrder('slanguage_display_order', 'asc');
        $srch->addOrder('slanguage_active', 'desc');
        $rs = $srch->getResultSet();
        $records = [];
        if ($rs) {
            $records = FatApp::getDb()->fetchAll($rs);
        }
        $canEdit = $this->objPrivilege->canEditSpokenLanguage($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    public function form($sLangId)
    {
        $sLangId = FatUtility::int($sLangId);
        $frm = $this->getForm($sLangId);
        if (0 < $sLangId) {
            $data = SpokenLanguage::getAttributesById($sLangId, [
                        'slanguage_id', 'slanguage_identifier', 'slanguage_code', 'slanguage_flag', 'slanguage_active',]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('sLangId', $sLangId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $sLangId = $post['slanguage_id'];
        unset($post['slanguage_id']);
        $record = new SpokenLanguage($sLangId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if ($post['slanguage_active'] == applicationConstants::NO) {
            $userToLanguage = new UserToLanguage();
            $userToLanguage->removeSpeakLang([$sLangId]);
            $teacherStat = new TeacherStat(0);
            $teacherStat->setSpeakLangBulk();
        }
        $newTabLangId = 0;
        if ($sLangId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = SpokenLanguage::getAttributesByLangId($langId, $sLangId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $sLangId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('sLangId', $sLangId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($sLangId = 0, $lang_id = 0)
    {
        $sLangId = FatUtility::int($sLangId);
        $lang_id = FatUtility::int($lang_id);
        if ($sLangId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($sLangId, $lang_id);
        $langData = SpokenLanguage::getAttributesByLangId($lang_id, $sLangId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('sLangId', $sLangId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $post = FatApp::getPostedData();
        $sLangId = $post['slanguage_id'];
        $lang_id = $post['lang_id'];
        if ($sLangId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($sLangId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['slanguage_id']);
        unset($post['lang_id']);
        $data = [
            'slanguagelang_lang_id' => $lang_id,
            'slanguagelang_slanguage_id' => $sLangId,
            'slanguage_name' => $post['slanguage_name']
        ];
        $obj = new SpokenLanguage($sLangId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = SpokenLanguage::getAttributesByLangId($langId, $sLangId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('sLangId', $sLangId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $sLangId = FatApp::getPostedData('sLangId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $sLangId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = SpokenLanguage::getAttributesById($sLangId, ['slanguage_id', 'slanguage_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new SpokenLanguage($sLangId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($status == applicationConstants::NO) {
            $userToLanguage = new UserToLanguage();
            $userToLanguage->removeSpeakLang([$sLangId]);
            $teacherStat = new TeacherStat(0);
            $teacherStat->setSpeakLangBulk();
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $slanguage_id = FatApp::getPostedData('sLangId', FatUtility::VAR_INT, 0);
        if ($slanguage_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $LessonPackageObj = new SpokenLanguage($slanguage_id);
        if (!$LessonPackageObj->deleteRecord($slanguage_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $userToLanguage = new UserToLanguage();
        $userToLanguage->removeSpeakLang([$slanguage_id]);

        $teacherStat = new TeacherStat(0);
        $teacherStat->setSpeakLangBulk();

        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function getForm($sLangId = 0)
    {
        $sLangId = FatUtility::int($sLangId);
        $frm = new Form('frmLessonPackage');
        $frm->addHiddenField('', 'slanguage_id', $sLangId);
        $frm->addRequiredField(Label::getLabel('LBL_Language_Code_Identifier', $this->adminLangId), 'slanguage_code');
        $frm->addRequiredField(Label::getLabel('LBL_Language_Identifier', $this->adminLangId), 'slanguage_identifier');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'slanguage_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($sLangId = 0, $lang_id = 0)
    {
        $frm = new Form('frmLessonPackageLang');
        $frm->addHiddenField('', 'slanguage_id', $sLangId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Language_Name', $this->adminLangId), 'slanguage_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function mediaForm($sLangId)
    {
        $sLangId = FatUtility::int($sLangId);
        if (1 > $sLangId) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $sLangDetail = SpokenLanguage::getAttributesById($sLangId);
        if (!false == $sLangDetail && ($sLangDetail['slanguage_active'] != applicationConstants::ACTIVE)) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_request_Or_Inactive_Record', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $mediaFrm = $this->getMediaForm($sLangId);
        if (!false == $sLangDetail) {
            $slangImage = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_SPOKEN_LANGUAGES, $sLangId, 0, -1);
            $this->set('slangImage', $slangImage);
        }
        $this->set('mediaFrm', $mediaFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('sLangId', $sLangId);
        $this->_template->render(false, false);
    }

    private function getMediaForm($slanguage_id)
    {
        $frm = new Form('frmSpokenLanguageMedia');
        $frm->addHiddenField('', 'slanguage_id', $slanguage_id);
        $fld = $frm->addButton(Label::getLabel('LBL_Language_Image', $this->adminLangId), 'slanguage_image', Label::getLabel('LBL_Upload_File', $this->adminLangId), [
            'class' => 'slanguageFile-Js',
            'id' => 'slanguage_image',
            'data-slanguage_id' => $slanguage_id
        ]);
        $fld = $frm->addButton(Label::getLabel('LBL_Language_Flag_Image', $this->adminLangId), 'slanguage_flag_image', Label::getLabel('LBL_Upload_File', $this->adminLangId), [
            'class' => 'slanguageFlagFile-Js',
            'id' => 'slanguage_flag_image',
            'data-slanguage_id' => $slanguage_id
        ]);
        return $frm;
    }

    public function images($slanguage_id, $lang_id = 0, $screen = 0)
    {
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $sLanguageDetail = SpokenLanguage::getAttributesById($slanguage_id);
        if (!false == $sLanguageDetail && ($sLanguageDetail['slanguage_active'] != applicationConstants::ACTIVE)) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_request_Or_Inactive_Record', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $imgType = AttachedFile::FILETYPE_SPOKEN_LANGUAGES;
        if (!false == $sLanguageDetail) {
            $languageImgArr = AttachedFile::getMultipleAttachments($imgType, $slanguage_id, 0, $lang_id, false, $screen);
            $this->set('images', $languageImgArr);
        }
        $canEdit = $this->objPrivilege->canEditSpokenLanguage($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set('languages', Language::getAllNames());
        $this->set('slanguage_id', $slanguage_id);
        $this->_template->render(false, false);
    }

    public function flagImages($slanguage_id, $lang_id = 0, $screen = 0)
    {
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $sLanguageDetail = SpokenLanguage::getAttributesById($slanguage_id);
        if (!false == $sLanguageDetail && ($sLanguageDetail['slanguage_active'] != applicationConstants::ACTIVE)) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_request_Or_Inactive_Record', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $imgType = AttachedFile::FILETYPE_FLAG_SPOKEN_LANGUAGES;
        if (!false == $sLanguageDetail) {
            $languageImgArr = AttachedFile::getMultipleAttachments($imgType, $slanguage_id, 0, $lang_id, false, $screen);
            $this->set('images', $languageImgArr);
        }
        $canEdit = $this->objPrivilege->canEditSpokenLanguage($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set('languages', Language::getAllNames());
        $this->set('slanguage_id', $slanguage_id);
        $this->_template->render(false, false);
    }

    public function upload($slanguage_id)
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
        }
        $lang_id = FatUtility::int($post['lang_id']);
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
        }
        $imgType = AttachedFile::FILETYPE_SPOKEN_LANGUAGES;
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile($imgType, $slanguage_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $imgType, $slanguage_id, 0, $_FILES['file']['name'], -1, true, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $this->set('slanguage_id', $slanguage_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function flagUpload($slanguage_id)
    {
        $this->objPrivilege->canEditSpokenLanguage();
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
        }
        $lang_id = FatUtility::int($post['lang_id']);
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
        }
        $imgType = AttachedFile::FILETYPE_FLAG_SPOKEN_LANGUAGES;
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile($imgType, $slanguage_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $imgType, $slanguage_id, 0, $_FILES['file']['name'], -1, true, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $this->set('slanguage_id', $slanguage_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeImage($slanguage_id)
    {
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        $imgType = AttachedFile::FILETYPE_SPOKEN_LANGUAGES;
        if (!$fileHandlerObj->deleteFile($imgType, $slanguage_id, 0, 0)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeFlagImage($slanguage_id)
    {
        $slanguage_id = FatUtility::int($slanguage_id);
        if (1 > $slanguage_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        $imgType = AttachedFile::FILETYPE_FLAG_SPOKEN_LANGUAGES;
        if (!$fileHandlerObj->deleteFile($imgType, $slanguage_id, 0, 0)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function Thumb($bannerId, $imgType, $langId = 0, $screen = 0)
    {
        $this->showBanner($bannerId, $imgType, $langId, 100, 100, $screen);
    }

    public function showBanner($bannerId, $imgType, $langId, $w = '200', $h = '200', $screen = 0)
    {
        $bannerId = FatUtility::int($bannerId);
        $langId = FatUtility::int($langId);
        $fileRow = AttachedFile::getAttachment($imgType, $bannerId, 0, $langId, true, $screen);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        AttachedFile::displayImage($image_name, $w, $h, '', '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
    }

    public function updateOrder()
    {
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $spokeLangObj = new SpokenLanguage();
            if (!$spokeLangObj->updateOrder($post['spokenLangages'])) {
                Message::addErrorMessage($spokeLangObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSpokenLanguageSearch');
        $frm->addTextBox(Label::getLabel('LBL_Language_Identifier', $this->adminLangId), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
