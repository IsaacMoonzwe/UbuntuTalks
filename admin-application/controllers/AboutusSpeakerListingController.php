<?php

class AboutusSpeakerListingController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTestimonial();
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
        $statusArr = ['-1' => Label::getLabel('LBL_All', $this->adminLangId)] + TeacherRequest::getStatusArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'status', $statusArr, '', [], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }
    public function search()
    {
        $srchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $srchForm->getFormDataFromArray($data);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $srch = AboutusSpeakerListing::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['t.*', 't_l.aboutus_speaker_listing_title', 't_l.aboutus_speaker_listing_text']);
        $srch->addOrder('aboutus_speaker_listing_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        foreach ($records as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $value['aboutus_speaker_listing_id'], 0, -1);
            $value['aboutus_speaker_listing_image'] = $testimonialImages;
            $records[$key] = $value;
        }
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $pagingArr = [
            'page' => $page,
            'pageCount' => $srch->pages(),
            'recordCount' => $srch->recordCount(),
            'adminLangId' => $this->adminLangId
        ];
        $this->set('pagingArr', $pagingArr);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = AboutusSpeakerListing::getAttributesById($testimonialId, [
                'aboutus_speaker_listing_id',
                'aboutus_speaker_listing_identifier',
                'aboutus_speaker_listing_active',
                'aboutus_speaker_listing_user_name',
                'aboutus_speaker_listing_positions',
                'aboutus_speaker_listing_description',
                'aboutus_speaker_listing_positions_listing'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('aboutus_speaker_listing_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditTestimonial();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = $post['aboutus_speaker_listing_id'];
        unset($post['aboutus_speaker_listing_id']);
        if ($testimonialId == 0) {
            $post['aboutus_speaker_listing_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new AboutusSpeakerListing($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
            }
        } else {
            $testimonialId = $record->getMainTableRecordId();
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($testimonialId)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($testimonialId = 0, $lang_id = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $lang_id = FatUtility::int($lang_id);
        if ($testimonialId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($testimonialId, $lang_id);
        $langData = AboutusSpeakerListing::getAttributesByLangId($lang_id, $testimonialId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('testimonialId', $testimonialId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        $testimonialId = $post['aboutus_speaker_listing_id'];
        $lang_id = $post['lang_id'];
        if ($testimonialId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($testimonialId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatUtility::dieWithError(Message::getHtml());
        }
        unset($post['aboutus_speaker_listing_id']);
        unset($post['lang_id']);
        $data = [
            'testimoniallang_lang_id' => $lang_id,
            'testimoniallang_aboutus_speaker_listing_id' => $testimonialId,
            'aboutus_speaker_listing_title' => $post['aboutus_speaker_listing_title'],
            'aboutus_speaker_listing_text' => $post['aboutus_speaker_listing_text']
        ];
        $obj = new AboutusSpeakerListing($testimonialId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = AboutusSpeakerListing::getAttributesByLangId($langId, $testimonialId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($testimonialId)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updateOrder()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $pMethodObj = new AboutusSpeakerListing();
            if (!$pMethodObj->updateOrder($post['paymentMethod'])) {
                Message::addErrorMessage($pMethodObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }


    public function changeStatus()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        if (0 >= $testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = AboutusSpeakerListing::getAttributesById($testimonialId, ['aboutus_speaker_listing_id', 'aboutus_speaker_listing_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['aboutus_speaker_listing_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $obj = new AboutusSpeakerListing($testimonialId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', $this->str_update_record);
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonial_id = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        if ($testimonial_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj = new AboutusSpeakerListing($testimonial_id);
        if (!$testimonialObj->canRecordMarkDelete($testimonial_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj->assignValues([AboutusSpeakerListing::tblFld('deleted') => 1]);
        if (!$testimonialObj->save()) {
            Message::addErrorMessage($testimonialObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function media($testimonialId = 0)
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatUtility::int($testimonialId);
        $testimonialMediaFrm = $this->getMediaForm($testimonialId);
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $testimonialId, 0, -1);
        $this->set('languages', Language::getAllNames());
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonialMediaFrm', $testimonialMediaFrm);
        $this->set('testimonialImages', $testimonialImages);
        $this->_template->render(false, false);
    }

    public function getMediaForm($testimonialId)
    {
        $frm = new Form('frmAboutusSpeakerListingMedia');
        $frm->addButton(Label::getLabel('Lbl_Image', $this->adminLangId), 'testimonial_image', Label::getLabel('LBL_Upload_Image', $this->adminLangId), [
            'class' => 'uploadFile-Js',
            'id' => 'testimonial_image',
            'data-file_type' => AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE,
            'data-testimonial_id' => $testimonialId
        ]);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        return $frm;
    }

    public function uploadTestimonialMedia()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        $lang_id = FatApp::getPostedData('lang_id', FatUtility::VAR_INT, 0);
        if (!$testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile($fileHandlerObj::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $testimonialId, 0, 0, $lang_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $fileHandlerObj::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $testimonialId, 0, $_FILES['file']['name'], -1, $unique_record = false, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('testimonialId', $testimonialId);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeTestimonialImage($testimonialId = 0, $lang_id = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $lang_id = FatUtility::int($lang_id);
        if (!$testimonialId) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $testimonialId, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($testimonialId = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = new Form('frmAboutusSpeakerListing');
        $frm->addHiddenField('', 'aboutus_speaker_listing_id', $testimonialId);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        $frm->addRequiredField(Label::getLabel('LBL_aboutus_speaker_listing_Name', $this->adminLangId), 'aboutus_speaker_listing_user_name');
        $frm->addRequiredField(Label::getLabel('LBL_aboutus_speaker_listing_Name', $this->adminLangId), 'aboutus_speaker_listing_positions');
        $frm->addTextarea(Label::getLabel('LBL_Description', $this->adminLangId), 'aboutus_speaker_listing_description')->requirements()->setRequired();
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'aboutus_speaker_listing_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($testimonialId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTestimonialLang');
        $frm->addHiddenField('', 'aboutus_speaker_listing_id', $testimonialId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Testimonial_Title', $this->adminLangId), 'aboutus_speaker_listing_title');
        $frm->addTextarea(Label::getLabel('LBL_Testimonial_Text', $this->adminLangId), 'aboutus_speaker_listing_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function isMediaUploaded($testimonialId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $testimonialId, 0)) {
            return true;
        }
        return false;
    }

    public function image($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ABOUTUS_SPEAKER_LISTING_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }
}
