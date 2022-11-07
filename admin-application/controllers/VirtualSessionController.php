<?php

class VirtualSessionController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTestimonial();
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
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
        $srch = VirtualSession::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['t.*', 't_l.virtual_session_title', 't_l.virtual_session_text']);
        $srch->addOrder('virtual_session_active', 'desc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);

        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        foreach ($records as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $value['virtual_session_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $virtual_main = new SearchBase('tbl_virtual_main_session');
            $virtual_main->addCondition('virtual_main_session_id', '=', $value['virtual_session_main_session']);
            $virtual_main->addCondition('virtual_main_session_deleted', '=', '0');
            $virtual_main->addCondition('virtual_main_session_active', '=', '1');
            $virtual_main_dropdown_value = FatApp::getDb()->fetch($virtual_main->getResultSet());
            $value['main_session']=$virtual_main_dropdown_value['virtual_main_session_title'] . ' | '. $virtual_main_dropdown_value['virtual_main_session_sub_title'];
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
            $data = VirtualSession::getAttributesById($testimonialId, [
                'virtual_session_id',
                'virtual_session_identifier',
                'virtual_session_active',
                'virtual_session_title_name',
                'virtual_session_author_name',
                'virtual_session_video_link',
                'virtual_session_facebook_link',
                'virtual_session_twitter_link',
                'virtual_session_linkedin_link',
                'virtual_session_email_link',
                'virtual_session_research_poster',
                'virtual_session_research_brief',
                'virtual_session_code_list'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('virtual_session_id', $testimonialId);
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
        $testimonialId = $post['virtual_session_id'];
        unset($post['virtual_session_id']);
        if ($testimonialId == 0) {
            $post['virtual_session_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new VirtualSession($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $db = FatApp::getDb();
        $db->startTransaction();

        $seoUrl = CommonHelper::seoUrl($post['virtual_session_title_name']);
        if (!$db->updateFromArray(
            VirtualSession::DB_TBL,
            ['virtual_session_slug' => $seoUrl],
            ['smt' => 'virtual_session_id = ?', 'vals' => [$record->getMainTableRecordId()]]
        )) {
            $this->error = $db->getError();
            $db->rollbackTransaction();
            return false;
        }
        $db->commitTransaction();

        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                // if (!$row = VirtualSession::getAttributesByLangId($langId, $testimonialId)) {
                //     $newTabLangId = $langId;
                //     break;
                // }
            }
        } else {
            $testimonialId = $record->getMainTableRecordId();
            // $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
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
        $langData = VirtualSession::getAttributesByLangId($lang_id, $testimonialId);
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
        $testimonialId = $post['virtual_session_id'];
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
        unset($post['virtual_session_id']);
        unset($post['lang_id']);
        $data = [
            'testimoniallang_lang_id' => $lang_id,
            'testimoniallang_speakers_id' => $testimonialId,
            'virtual_session_title' => $post['virtual_session_title'],
            'virtual_session_text' => $post['virtual_session_text']
        ];
        $obj = new VirtualSession($testimonialId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = VirtualSession::getAttributesByLangId($langId, $testimonialId)) {
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
            $pMethodObj = new VirtualSession();
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
        //$status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = VirtualSession::getAttributesById($testimonialId, ['virtual_session_id', 'virtual_session_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['virtual_session_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $obj = new VirtualSession($testimonialId);
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
        $testimonialObj = new VirtualSession($testimonial_id);
        if (!$testimonialObj->canRecordMarkDelete($testimonial_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj->assignValues([VirtualSession::tblFld('deleted') => 1]);
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
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $testimonialId, 0, -1);
        $this->set('languages', Language::getAllNames());
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonialMediaFrm', $testimonialMediaFrm);
        $this->set('testimonialImages', $testimonialImages);
        $this->_template->render(false, false);
    }

    public function getMediaForm($testimonialId)
    {
        $frm = new Form('frmTestimonialMedia');
        $frm->addButton(Label::getLabel('Lbl_Image', $this->adminLangId), 'testimonial_image', Label::getLabel('LBL_Upload_Image', $this->adminLangId), [
            'class' => 'uploadFile-Js',
            'id' => 'testimonial_image',
            'data-file_type' => AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE,
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
        $fileHandlerObj->deleteFile($fileHandlerObj::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $testimonialId, 0, 0, $lang_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $fileHandlerObj::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $testimonialId, 0, $_FILES['file']['name'], -1, $unique_record = false, $lang_id)) {
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
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $testimonialId, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($testimonialId = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = new Form('frmTestimonial');
        $speakers_positions_listing_data = new SearchBase('tbl_speakers_position_listing');
        $speakers_positions_listing_data->addCondition('speakers_position_deleted', '=', '0');
        $speakers_positions_listing_data->addCondition('speakers_position_active', '=', '1');
        $speakers_positions_dropdown_value = FatApp::getDb()->fetchAll($speakers_positions_listing_data->getResultSet());
        $speakers_positions = array();
        foreach ($speakers_positions_dropdown_value as $key => $value) {
            $speakers_positions[$value['speakers_position_user_name']] = $value['speakers_position_user_name'];
        }
        $virtual_main = new SearchBase('tbl_virtual_main_session');
        $virtual_main->addCondition('virtual_main_session_deleted', '=', '0');
        $virtual_main->addCondition('virtual_main_session_active', '=', '1');
        $virtual_main_dropdown_value = FatApp::getDb()->fetchAll($virtual_main->getResultSet());
        $virtual_main_positions = array();
        foreach ($virtual_main_dropdown_value as $key => $value) {
            $virtual_main_positions[$value['virtual_main_session_id']] = $value['virtual_main_session_title'] . ' | ' . $value['virtual_main_session_sub_title'];
       
        }

        $frm->addHiddenField('', 'virtual_session_id', $testimonialId);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        $frm->addSelectBox(Label::getLabel('LBL_Positions', $this->adminLangId), 'virtual_session_main_session', $virtual_main_positions, '', [], '')->requirements()->setRequired();
        $frm->addRequiredField(Label::getLabel('LBL_Virtual_Session_Title', $this->adminLangId), 'virtual_session_title_name');
        $frm->addRequiredField(Label::getLabel('LBL_Author_Name', $this->adminLangId), 'virtual_session_author_name');
        $video = $frm->addRequiredField(Label::getLabel('LBL_Video_Link', $this->adminLangId), 'virtual_session_video_link');
        $video->requirements()->setRequired(false);
        $facebook = $frm->addRequiredField(Label::getLabel('LBL_Facebook_Link', $this->adminLangId), 'virtual_session_facebook_link');
        $facebook->requirements()->setRequired(false);
        $twitter = $frm->addRequiredField(Label::getLabel('LBL_Twitter_Link', $this->adminLangId), 'virtual_session_twitter_link');
        $twitter->requirements()->setRequired(false);
        $linkedin = $frm->addRequiredField(Label::getLabel('LBL_Linkedin_Link', $this->adminLangId), 'virtual_session_linkedin_link');
        $linkedin->requirements()->setRequired(false);
        $email = $frm->addRequiredField(Label::getLabel('LBL_Email', $this->adminLangId), 'virtual_session_email_link');
        $email->requirements()->setRequired(false);
        $research_poster = $frm->addRequiredField(Label::getLabel('LBL_Full_Size_Research_Poster', $this->adminLangId), 'virtual_session_research_poster');
        $research_poster->requirements()->setRequired(false);
        $reasearch_brief = $frm->addRequiredField(Label::getLabel('LBL_Research_Brief', $this->adminLangId), 'virtual_session_research_brief');
        $reasearch_brief->requirements()->setRequired(false);
        $code_list = $frm->addRequiredField(Label::getLabel('LBL_Code_List', $this->adminLangId), 'virtual_session_code_list');
        $code_list->requirements()->setRequired(false);

        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'virtual_session_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($testimonialId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTestimonialLang');
        $frm->addHiddenField('', 'virtual_session_id', $testimonialId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Testimonial_Title', $this->adminLangId), 'virtual_session_title');
        $frm->addTextarea(Label::getLabel('LBL_Testimonial_Text', $this->adminLangId), 'virtual_session_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function isMediaUploaded($testimonialId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $testimonialId, 0)) {
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
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
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
