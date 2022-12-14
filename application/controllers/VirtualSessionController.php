<?php

class VirtualSessionController extends MyAppController
{

    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $contactBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_CONTACT_BANNER_SECTION, $this->siteLangId);
        $contactLeftSection = ExtraPage::getBlockContent(ExtraPage::BLOCK_CONTACT_LEFT_SECTION, $this->siteLangId);
        $this->_template->addCss('css/virtual-session.css');
        $this->set('contactBanner', $contactBanner);
        $this->set('contactLeftSection', $contactLeftSection);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    public function virtualSessionListing($current = '')
    {
        $virtual_session = new SearchBase('tbl_virtual_main_session');
        $current_year = date("Y");
        $virtual_session->addCondition('virtual_main_session_deleted', '=', 0);
        $virtual_session->addCondition('virtual_main_session_active', '=', 1);
        if ($current != '') {
            $virtual_session->addCondition('virtual_main_session_year', '=', $current);
        } else {
            $virtual_session->addCondition('virtual_main_session_year', '=', $current_year);
        }
        $virtual_session_listing = $virtual_session->getResultSet();
        $VirtualSessionList = FatApp::getDb()->fetchAll($virtual_session_listing);
        foreach ($VirtualSessionList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_IMAGE, $value['virtual_main_session_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $records[$key] = $value;
        }
        $this->set('records', $records);
        $this->_template->addCss('css/virtual-session.css');
        $this->_template->render(false, false);
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('contact'));
        }
        if (!CommonHelper::verifyCaptcha()) {
            Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::generateUrl('contact'));
        }
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->sendContactFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('contact'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Name', $langId), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Your_Phone', $langId), 'phone');
        $fld_phn->requirements()->setRegularExpressionToValidate('^[\s()+-]*([0-9][\s()+-]*){5,20}$');
        $fld_phn->requirements()->setCustomErrorMessage(Label::getLabel('VLD_ADD_VALID_PHONE_NUMBER', $langId));
        $frm->addTextArea(Label::getLabel('LBL_Your_Message', $langId), 'message')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }
    public function image($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_VIRTUAL_SESSION_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_VIRTUAL_SESSION_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
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
                $w = 346;
                $h = 231;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function imageSession($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
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
                $w = 346;
                $h = 231;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 1024;
                $h = 770;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function sessionWiseListing($virtual_main_session_slug)
    {
        $virtual_session = new SearchBase('tbl_virtual_main_session');
        $virtual_session->addCondition('virtual_main_session_deleted', '=', 0);
        $virtual_session->addCondition('virtual_main_session_active', '=', 1);
        $virtual_session->addCondition('virtual_main_session_slug', '=', $virtual_main_session_slug);
        $virtual_session_listing = $virtual_session->getResultSet();
        $VirtualSessionList = FatApp::getDb()->fetch($virtual_session_listing);
        $Session = new SearchBase('tbl_virtual_session');
        $Session->addCondition('virtual_session_deleted', '=', 0);
        $Session->addCondition('virtual_session_active', '=', 1);
        $Session->addCondition('virtual_session_main_session', '=', $VirtualSessionList['virtual_main_session_id']);
        $SessionListing = $Session->getResultSet();
        $SessionWiseListing = FatApp::getDb()->fetchAll($SessionListing);
        foreach ($SessionWiseListing as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $value['virtual_session_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $records[$key] = $value;
        }
        $virtual_main_session_slug  = CommonHelper::htmlEntitiesDecode($virtual_main_session_slug);
        $this->set('virtual_main_session_slug', $virtual_main_session_slug);
        $this->set('SessionWiseListing', $SessionWiseListing);
        $this->set('records', $records);
        $this->set('VirtualSessionList', $VirtualSessionList);
        $this->_template->addCss('css/virtual-session.css');
        $this->_template->render();
    }

    public function sessionDetails($virtual_session_slug)
    {
        $Session = new SearchBase('tbl_virtual_session');
        $Session->addCondition('virtual_session_deleted', '=', 0);
        $Session->addCondition('virtual_session_active', '=', 1);
        $Session->addCondition('virtual_session_slug', '=', $virtual_session_slug);
        $SessionListing = $Session->getResultSet();
        $SessionWiseListing = FatApp::getDb()->fetch($SessionListing);
        $virtual_session = new SearchBase('tbl_virtual_main_session');
        $virtual_session->addCondition('virtual_main_session_deleted', '=', 0);
        $virtual_session->addCondition('virtual_main_session_active', '=', 1);
        $virtual_session->addCondition('virtual_main_session_id', '=', $SessionWiseListing['virtual_session_main_session']);
        $virtual_session_listing = $virtual_session->getResultSet();
        $VirtualSessionList = FatApp::getDb()->fetch($virtual_session_listing);
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $SessionWiseListing['virtual_session_id'], 0, -1);
        $SessionWiseListing['speaker_image'] = $testimonialImages;
        $virtual_session_slug  = CommonHelper::htmlEntitiesDecode($virtual_session_slug);

        /* More Explore */
        $ExploreSession = new SearchBase('tbl_virtual_session');
        $ExploreSession->addCondition('virtual_session_deleted', '=', 0);
        $ExploreSession->addCondition('virtual_session_active', '=', 1);
        $ExploreSession->addCondition('virtual_session_slug', 'not like', $virtual_session_slug);
        $ExploreSession->addCondition('virtual_session_main_session', '=', $SessionWiseListing['virtual_session_main_session']);
        $ExploreSessionListing = $ExploreSession->getResultSet();
        $ExploreSessionWiseListing = FatApp::getDb()->fetchAll($ExploreSessionListing);
        foreach ($ExploreSessionWiseListing as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_WISE_IMAGE, $value['virtual_session_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;

            $records[$key] = $value;
        }
        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = $this->getAgendaForm($testimonialId, $SessionWiseListing['virtual_session_id']);
        if (0 < $testimonialId) {
            $data = VirtualSessionsComments::getAttributesById($testimonialId, [
                'virtual_session_comments_id',
                'user_id',
                'virtual_session_comments_information'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $agendafrm->fill($data);
            $this->set('records', $data);
        }

        /* Comment-Section */
        $CommentSections = new SearchBase('tbl_virtual_session_comments');
        $CommentSections->addCondition('virtual_session_id', '=', $SessionWiseListing['virtual_session_id']);
        $CommentSectionsListing = $CommentSections->getResultSet();
        $CommentSectionWiseListing = FatApp::getDb()->fetchAll($CommentSectionsListing);
        foreach ($CommentSectionWiseListing as $key => $value) {
            $UserCommentsSession = new SearchBase('tbl_event_users');
            $UserCommentsSession->addCondition('user_id', '=', $value['user_id']);
            $UserCommentSessionListing = $UserCommentsSession->getResultSet();
            $UserCommentSessionWiseListing = FatApp::getDb()->fetch($UserCommentSessionListing);
            $value['user_info'] = $UserCommentSessionWiseListing;
            $value['main_session_time'] = $VirtualSessionList['virtual_main_session_end_time'];
            $CommentSectionWiseListing[$key] = $value;
        }
        $CommentCountSections = new SearchBase('tbl_virtual_session_comments');
        $CommentCountSections->addCondition('virtual_session_id', '=', $SessionWiseListing['virtual_session_id']);
        $CommentCountSections->addMultipleFields([
            'COUNT(user_id) as counting',
        ]);
        $CommentSectionsCountListing = $CommentCountSections->getResultSet();
        $CommentSectionWiseCountListing = FatApp::getDb()->fetch($CommentSectionsCountListing);
        $this->set('VirtualSessionList', $VirtualSessionList);
        $this->set('CommentSectionWiseCountListing', $CommentSectionWiseCountListing);
        $this->set('CommentSectionWiseListing', $CommentSectionWiseListing);
        $this->set('languages', Language::getAllNames());
        $this->set('virtual_session_comments_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('agendafrm', $agendafrm);
        $this->set('ExploreSessionWiseListing', $records);
        $this->set('virtual_session_slug', $virtual_session_slug);
        $this->set('SessionWiseListing', $SessionWiseListing);
        $this->set('records', $SessionWiseListing);
        $this->set('VirtualSessionList', $VirtualSessionList);
        $this->_template->addCss('css/virtual-session.css');
        $this->_template->render();
    }

    private function getAgendaForm($testimonialId, $session_id)
    {
      //  $userId = EventUserAuthentication::getLoggedUserId();
        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = new Form('frmAgendaTestimonials');
        $agendafrm->addHiddenField(Label::getLabel('LBl_Id'), 'virtual_session_id', $session_id);
        $agendafrm->addHiddenField(Label::getLabel('LBl_Id'), 'virtual_session_comments_id', $testimonialId);
        $agendafrm->addHiddenField('', 'user_id', $userId);
        $agendafrm->addTextarea(Label::getLabel('LBL_Event_Location', $this->adminLangId), 'virtual_session_comments_information');
        $agendafrm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Post_Comment', $this->adminLangId));
        return $agendafrm;
    }

    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = VirtualSessionsComments::getAttributesById($testimonialId, [
                'user_id',
                'virtual_session_comments_information'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('virtual_session_comments_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function agendasetup()
    {
        $post = FatApp::getPostedData();
        $testimonialId = $post['virtual_session_comments_id'];
        unset($post['virtual_session_comments_id']);
        if ($testimonialId == 0) {
            $post['virtual_session_comments_added_on'] = date('Y-m-d H:i:s');
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $post['user_id']=$userId;
        $record = new VirtualSessionsComments($testimonialId);

        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        // $res = EmailHandler::sendSmtpEmail('admin@ubuntutalks.com', 'Report Issue', $post['virtual_session_comments_information'], '', '', $this->siteLangId, '', '');
        $this->set('msg', 'Comment successfully sent');
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function previousYears($years)
    {
        $virtual_session = new SearchBase('tbl_virtual_main_session');
        $virtual_session->addCondition('virtual_main_session_deleted', '=', 0);
        $virtual_session->addCondition('virtual_main_session_active', '=', 1);
        $virtual_session->addCondition('virtual_main_session_year', '=', $years);
        $virtual_session_listing = $virtual_session->getResultSet();
        $VirtualSessionList = FatApp::getDb()->fetchAll($virtual_session_listing);
        foreach ($VirtualSessionList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_VIRTUAL_SESSION_IMAGE, $value['virtual_main_session_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $testimonialImages[$key] = $value;
            $records[$key] = $value;
        }
        $this->set('records', $records);

        $years  = CommonHelper::htmlEntitiesDecode($years);
        $this->set('years', $years);
        $this->set('VirtualSessionList', $VirtualSessionList);
        $this->_template->addCss('css/virtual-session.css');
        $this->_template->render();
    }


    public function navigationMenu()
    {
        $Navigation_virtual_session = new SearchBase('tbl_virtual_main_session');
        $Currentyear = date("Y");
        $Navigation_virtual_session->addCondition('virtual_main_session_deleted', '=', 0);
        $Navigation_virtual_session->addCondition('virtual_main_session_active', '=', 1);
        $Navigation_virtual_session->addCondition('virtual_main_session_year', '=', $Currentyear);
        $Navigation_virtual_session_listing = $Navigation_virtual_session->getResultSet();
        $NavigationVirtualSessionList = FatApp::getDb()->fetchAll($Navigation_virtual_session_listing);
        $Navigation_virtual_session_years = new SearchBase('tbl_virtual_main_session');
        $Navigation_virtual_session_years->addCondition('virtual_main_session_deleted', '=', 0);
        $Navigation_virtual_session_years->addCondition('virtual_main_session_active', '=', 1);
        $Navigation_virtual_session_years->addCondition('virtual_main_session_year', '<', $Currentyear);
        $Navigation_virtual_session_years->addGroupBy('virtual_main_session_year', 'DESC');
        $Navigation_virtual_session_years_listing = $Navigation_virtual_session_years->getResultSet();
        $NavigationVirtualSessionYearList = FatApp::getDb()->fetchAll($Navigation_virtual_session_years_listing);
        $Navigation_virtual_session_all = new SearchBase('tbl_virtual_main_session');
        $Navigation_virtual_session_all->addCondition('virtual_main_session_deleted', '=', 0);
        $Navigation_virtual_session_all->addCondition('virtual_main_session_active', '=', 1);
        $Navigation_virtual_session_all->addCondition('virtual_main_session_year', '<', $Currentyear);
        $Navigation_virtual_session_all_listing = $Navigation_virtual_session_all->getResultSet();
        $NavigationVirtualSessionAllList = FatApp::getDb()->fetchAll($Navigation_virtual_session_all_listing);
        if (EventUserAuthentication::isUserLogged()) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $userObj = new EventUser($userId);
            $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
            $this->set('userDetails', $userDetails);
        }
        $this->set('NavigationVirtualSessionAllList', $NavigationVirtualSessionAllList);
        $this->set('NavigationVirtualSessionYearList', $NavigationVirtualSessionYearList);
        $this->set('NavigationVirtualSessionList', $NavigationVirtualSessionList);
        $this->_template->addCss('css/virtual-session.css');
        $this->_template->render(false, false);
    }
}
