<?php

class TeacherRequestController extends MyAppController
{

    private $userId;

    public function __construct($action)
    {
        parent::__construct($action);
        if (UserAuthentication::getLoggedUserId(true)) {
            $this->userId = UserAuthentication::getLoggedUserId();
        } elseif (UserAuthentication::getGuestTeacherUserId()) {
            $this->userId = UserAuthentication::getGuestTeacherUserId();
        } else {
            $this->userId = 0;
        }
    }

    public function index()
    {
        $applyTeachFrm = $this->getApplyTeachFrm($this->siteLangId);
        $sectionAfterBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_BENEFITS_SECTION, $this->siteLangId);
        $featursSection = ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_FEATURES_SECTION, $this->siteLangId);
        $becometutorSection = ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_BECOME_A_TUTOR_SECTION, $this->siteLangId);
        $staticBannerSection = ExtraPage::getBlockContent(ExtraPage::BLOCK_APPLY_TO_TEACH_STATIC_BANNER, $this->siteLangId);
        $this->set('faqs', $this->getApplyToTeachFaqs());
        $this->set('sectionAfterBanner', $sectionAfterBanner);
        $this->set('featuresSection', $featursSection);
        $this->set('becometutorSection', $becometutorSection);
        $this->set('staticBannerSection', $staticBannerSection);
        $this->set('applyTeachFrm', $applyTeachFrm);
        $this->_template->render();
    }

    private function getRequest(int $userId)
    {
        $srch = new SearchBase('tbl_user_teacher_requests');
        $srch->addCondition('utrequest_user_id', '=', $userId);
        $srch->addOrder('utrequest_id', 'DESC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    private function getRequestCount(int $userId)
    {
        $srch = new SearchBase('tbl_user_teacher_requests');
        $srch->addCondition('utrequest_user_id', '=', $userId);
        $srch->getResultSet();
        return $srch->recordCount();
    }

    public function form()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            UserAuthentication::logoutGuestTeacher();
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequest', '', [], CONF_WEBROOT_FRONTEND));
        }
        $requestCount = $this->getRequestCount($userId);
        $allowedCount = FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT');
        if ($requestCount == $allowedCount) {
            $this->set('step', 5);
        } else {
            $this->set('step', $this->getRequest($userId)['utrequest_step'] ?? 1);
        }
        $this->set('exculdeMainHeaderDiv', false);
        $this->_template->addJs('js/jquery.form.js');
        $this->_template->addJs('js/cropper.js');
        $this->_template->addJs('js/jquery.inputmask.bundle.js');
        $this->_template->addJs('js/intlTelInput.js');
        $this->_template->addCss('css/intlTelInput.css');
        $this->_template->render(true, false);
    }

    private function attemptReachedCheck()
    {
        if ($this->getRequestCount($this->userId) <= FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT')) {
            return true;
        }
        FatUtility::dieJsonError(Label::getLabel('LBL_YOU_HAVE_REACH_MAX_ATTEMPTS_TO_SUBMIT_REQUEST'));
    }

    public function formStep1()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $resubmit = FatApp::getPostedData('resubmit', FatUtility::VAR_INT, 0);
        $this->attemptReachedCheck();
        $frm = $this->getFormStep1($this->siteLangId);
        $request = $this->getRequest($userId);
        if (!empty($request) && $resubmit == 0) {
            $frm->fill($request);
        } else {
            $frm->fill(['resubmit' => $resubmit]);
        }
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    public function formStep2()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep2($this->siteLangId);
        $frm->fill($request);
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    public function formStep3()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $request['utrequest_teach_slanguage_id'] = empty($request['utrequest_teach_slanguage_id']) ? '[]' : $request['utrequest_teach_slanguage_id'];
        $request['utrequest_language_speak'] = empty($request['utrequest_language_speak']) ? '[]' : $request['utrequest_language_speak'];
        $request['utrequest_language_speak_proficiency'] = empty($request['utrequest_language_speak_proficiency']) ? '[]' : $request['utrequest_language_speak_proficiency'];
        $request['utrequest_teach_slanguage_id'] = json_decode($request['utrequest_teach_slanguage_id'], true);
        $request['utrequest_language_speak'] = json_decode($request['utrequest_language_speak'], true);
        $request['utrequest_language_speak_proficiency'] = json_decode($request['utrequest_language_speak_proficiency'], true);
        $spokenLangs = SpokenLanguage::getAllLangs($this->siteLangId, true);
        $frm = $this->getFormStep3($this->siteLangId, $spokenLangs);
        $frm->fill($request);
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('spokenLangs', $spokenLangs);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    public function formStep4()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->attemptReachedCheck();
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep4();
        $frm->fill($request);
        $this->set('frm', $frm);
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->_template->render(false, false);
    }

    public function formStep5()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $fileType = AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_APPROVAL_PROOF;
        $fileRow = AttachedFile::getAttachment($fileType, $userId);
        $this->set('fileRow', $fileRow);
        $this->set('request', $request);
        $this->set('user', User::getAttributesById($userId));
        $this->set('requestCount', $this->getRequestCount($userId));
        $this->set('allowedCount', FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT'));
        $this->_template->render(false, false);
    }

    public function setupStep1()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $resubmit = FatApp::getPostedData('resubmit', FatUtility::VAR_INT, 0);
        $frm = $this->getFormStep1($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (!empty($_FILES['user_photo_id']['tmp_name'] ?? '')) {
            $file = new AttachedFile();
            $photoName = $_FILES['user_photo_id']['name'];
            $photoTmpName = $_FILES['user_photo_id']['tmp_name'];
            $photoType = AttachedFile::FILETYPE_TEACHER_APPROVAL_USER_APPROVAL_PROOF;
            if (!$file->saveDoc($photoTmpName, $photoType, $userId, 0, $photoName, -1, true)) {
                FatUtility::dieJsonError($file->getError());
            }
        }
        $data = [
            'utrequest_step' => 2,
            'utrequest_user_id' => $userId,
            'utrequest_language_id' => $this->siteLangId,
            'utrequest_reference' => $userId . '-' . time(),
            'utrequest_date' => date('Y-m-d H:i:s'),
            'utrequest_first_name' => $post['utrequest_first_name'],
            'utrequest_last_name' => $post['utrequest_last_name'],
            'utrequest_gender' => $post['utrequest_gender'],
            'utrequest_phone_code' => $post['utrequest_phone_code'],
            'utrequest_phone_number' => $post['utrequest_phone_number'],
        ];
        $request = $this->getRequest($userId);
        if (!empty($request) && $resubmit == applicationConstants::NO) {
            $data = [
                'utrequest_id' => $request['utrequest_id'],
                'utrequest_first_name' => $post['utrequest_first_name'],
                'utrequest_last_name' => $post['utrequest_last_name'],
                'utrequest_gender' => $post['utrequest_gender'],
                'utrequest_phone_code' => $post['utrequest_phone_code'],
                'utrequest_phone_number' => $post['utrequest_phone_number'],
            ];
        }
        $record = new TableRecord('tbl_user_teacher_requests');
        $record->assignValues($data);
        if ($resubmit == applicationConstants::YES) {
            if (!$record->addNew()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
        } else {
            if (!$record->addNew([], $data)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
        }
        FatUtility::dieJsonSuccess(['step' => 2, 'msg' => Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY')]);
    }

    public function setupStep2()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep2($this->siteLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $imageFile = '';
        $fileType = AttachedFile::FILETYPE_USER_PROFILE_IMAGE;
        if ($post['action'] == "avatar") {
            $fileType = AttachedFile::FILETYPE_USER_PROFILE_CROPED_IMAGE;
        }
        if (!empty($_FILES['user_profile_image']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                $msgLblKey = CommonHelper::getFileUploadErrorLblKeyFromCode($_FILES['user_profile_image']['error']);
                FatUtility::dieJsonError(Label::getLabel($msgLblKey));
            }
            $file = new AttachedFile();
            $fileName = $_FILES['user_profile_image']['name'];
            $tmpName = $_FILES['user_profile_image']['tmp_name'];
            if (!$res = $file->saveImage($tmpName, $fileType, $userId, 0, $fileName, -1, true)) {
                FatUtility::dieJsonError($file->getError());
            }
            $data = json_decode(stripslashes($post['img_data']));
            CommonHelper::crop($data, CONF_UPLOADS_PATH . $res);
            $imageFile = CommonHelper::generateFullUrl('Image', 'userFull', [$userId], CONF_WEBROOT_FRONTEND) . '?' . time();
        }
        if (!User::isProfilePicUploaded($userId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PROFILE_PICTURE_REQURED'));
        }
        $record = new TableRecord('tbl_user_teacher_requests');
        $data = [
            'utrequest_step' => 3,
            'utrequest_id' => $request['utrequest_id'],
            'utrequest_video_link' => $post['utrequest_video_link'],
            'utrequest_profile_info' => $post['utrequest_profile_info'],
        ];
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(['step' => 3, 'file' => $imageFile, 'msg' => Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY')]);
    }

    public function setupStep3()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $spokenLangs = SpokenLanguage::getAllLangs($this->siteLangId, true);
        $frm = $this->getFormStep3($this->siteLangId, $spokenLangs);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $teachLangs = json_encode(array_filter(FatUtility::int($post['utrequest_teach_slanguage_id'])));
        $speakLangs = [];
        $speakLangArr = array_filter(FatUtility::int(array_values($post['utrequest_language_speak'])));
        foreach ($speakLangArr as $key => $value) {
            array_push($speakLangs, $value);
        }
        $speakLangsProf = [];
        $speakLangsProfArr = array_filter(FatUtility::int(array_values($post['utrequest_language_speak_proficiency'])));
        foreach ($speakLangsProfArr as $key => $value) {
            array_push($speakLangsProf, $value);
        }
        if (empty($speakLangs) || empty($speakLangsProf)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SPEAK_LANGUAGE_AND_PROFICIENCY_REQUIRED'));
        }
        $record = new TableRecord('tbl_user_teacher_requests');
        $data = [
            'utrequest_step' => 4,
            'utrequest_id' => $request['utrequest_id'],
            'utrequest_teach_slanguage_id' => $teachLangs,
            'utrequest_language_speak' => json_encode($speakLangs),
            'utrequest_language_speak_proficiency' => json_encode($speakLangsProf),
        ];
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(['step' => 4, 'msg' => Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY')]);
    }

    public function setupStep4()
    {
        $userId = FatUtility::int($this->userId);
        if ($userId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFormStep4();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $request = $this->getRequest($userId);
        if (empty($request)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $qualifications = new UserQualificationSearch();
        $rows = $qualifications->getUserQualification($userId);
        if (empty($rows)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_TEACHER_QUALIFICATION_REQUIRED'));
        }
        $record = new TableRecord('tbl_user_teacher_requests');
        $record->assignValues(['utrequest_step' => 5, 'utrequest_terms' => $post['utrequest_terms']]);
        if (!$record->update(['smt' => 'utrequest_id = ?', 'vals' => [$request['utrequest_id']]])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(['step' => 5, 'msg' => Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY')]);
    }

    private function getFormStep1($langId)
    {
        $frm = new Form('frmFormStep1', ['id' => 'frmFormStep1']);
        $firstNameFld = $frm->addTextBox(Label::getLabel('LBL_First_Name'), 'utrequest_first_name');
        $firstNameFld->requirements()->setRequired();
        $firstNameFld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $firstNameFld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_First_Name'), '{A-Z,a-z and 0-9}'));
        $lastNameFld = $frm->addTextBox(Label::getLabel('LBL_Last_Name'), 'utrequest_last_name');
        $lastNameFld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $lastNameFld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_Last_Name'), '{A-Z,a-z and 0-9}'));
        $frm->addRadioButtons(Label::getLabel('LBL_Gender'), 'utrequest_gender', User::getGenderArr($langId), User::GENDER_MALE)->requirements()->setRequired();
        $fldPhn = $frm->addTextBox(Label::getLabel('LBL_Phone_Number'), 'utrequest_phone_number');
        $fldPhn->requirements()->setRegularExpressionToValidate(applicationConstants::PHONE_NO_REGEX);
        $fldPhn->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));
        $frm->addHiddenField('', 'utrequest_phone_code');
        $frm->addHiddenField('', 'resubmit', 0);
        $frm->addFileUpload(Label::getLabel('LBL_Photo_Id'), 'user_photo_id');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    private function getFormStep2($langId)
    {
        $frm = new Form('frmFormStep2', ['id' => 'frmFormStep2']);
        $frm->addFileUpload(Label::getLabel('LBL_Profile_Picture'), 'user_profile_image', ['onchange' => 'popupImage(this)', 'accept' => 'image/*']);
        $frm->addTextArea(Label::getLabel('LBL_Biography', $langId), 'utrequest_profile_info')->requirements()->setLength(1, 500);
        $fld = $frm->addTextBox(Label::getLabel('LBL_Introduction_video', $langId), 'utrequest_video_link');
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::INTRODUCTION_VIDEO_LINK_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_Valid_Video_Link'));
        $frm->addHiddenField('', 'update_profile_img', Label::getLabel('LBL_Update_Profile_Picture'), ['id' => 'update_profile_img']);
        $frm->addHiddenField('', 'rotate_left', Label::getLabel('LBL_Rotate_Left'), ['id' => 'rotate_left']);
        $frm->addHiddenField('', 'rotate_right', Label::getLabel('LBL_Rotate_Right'), ['id' => 'rotate_right']);
        $frm->addHiddenField('', 'remove_profile_img', 0, ['id' => 'remove_profile_img']);
        $frm->addHiddenField('', 'img_data', '', ['id' => 'img_data']);
        $frm->addHiddenField('', 'action', 'avatar', ['id' => 'avatar-action']);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    private function getFormStep3($langId, $spokenLangs)
    {
        $frm = new Form('frmFormStep3', ['id' => 'frmFormStep3']);
        $profArr = SpokenLanguage::getProficiencyArr($this->siteLangId);
        $teachingLanguagesArr = TeachingLanguage::getAllLangs($langId, true);
        $fld = $frm->addCheckBoxes(Label::getLabel('LBL_Language_To_Teach'), 'utrequest_teach_slanguage_id', $teachingLanguagesArr);
        $fld->requirements()->setSelectionRange(1, count($teachingLanguagesArr));
        $fld->requirements()->setRequired();
        $langArr = $spokenLangs ?: SpokenLanguage::getAllLangs($langId, true);
        $proficiencyLabel = stripslashes(Label::getLabel("LBL_I_don't_speak_this_language"));
        foreach ($langArr as $key => $lang) {
            $speekLangField = $frm->addCheckBox(Label::getLabel('LBL_Language_I_Speak'), 'utrequest_language_speak[' . $key . ']', $key, ['class' => 'utsl_slanguage_id'], false, '0');
            $proficiencyField = $frm->addSelectBox(Label::getLabel('LBL_Language_Proficiency'), 'utrequest_language_speak_proficiency[' . $key . ']', $profArr, '', ['class' => 'utsl_proficiency select__dropdown'], $proficiencyLabel);
            $proficiencyField->requirements()->setRequired();
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $proficiencyField->requirements());
            $proficiencyField->requirements()->setRequired(false);
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $proficiencyField->requirements());
            $speekLangField->requirements()->setRequired();
            $proficiencyField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $speekLangField->requirements());
            $speekLangField->requirements()->setRequired(false);
            $proficiencyField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $speekLangField->requirements());
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    private function getFormStep4()
    {
        $frm = new Form('frmFormStep4', ['id' => 'frmFormStep4']);
        $frm->addCheckBox(Label::getLabel('LBL_Accept_Teacher_Approval_Terms_&_condition'), 'utrequest_terms', 1, [], false, 0)->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    public function searchTeacherQualification()
    {
        $srch = new UserQualificationSearch();
        $srch->addCondition('uqualification_user_id', '=', $this->userId);
        $srch->addMultiplefields([
            'uqualification_id',
            'uqualification_title',
            'uqualification_experience_type',
            'uqualification_start_year',
            'uqualification_end_year',
            'uqualification_institute_address',
            'uqualification_institute_name',
        ]);
        $this->set("rows", FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->set("user", User::getAttributesById($this->userId));
        $this->_template->render(false, false);
    }

    public function teacherQualificationForm()
    {
        $uqualification_id = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        $frm = $this->getTeacherQualificationForm(true);
        if ($uqualification_id > 0) {
            $srch = new UserQualificationSearch();
            $srch->addCondition('uqualification_user_id', '=', $this->userId);
            $srch->addCondition('uqualification_id', '=', $uqualification_id);
            $rs = $srch->getResultSet();
            $row = FatApp::getDb()->fetch($rs);
            $frm->fill($row);
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $this->userId, $uqualification_id);
            $field = $frm->getField('certificate');
            $certificateRequried = false; //(empty($file_row)) ? true : false;
            $field->requirements()->setRequired($certificateRequried);
        }
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setUpTeacherQualification()
    {
        $frm = $this->getTeacherQualificationForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $uqualification_id = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        $qualification = new UserQualification($uqualification_id);
        $post['uqualification_user_id'] = $this->userId;
        $db = FatApp::getDb();
        $db->startTransaction();
        $qualification->assignValues($post);
        if (true !== $qualification->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($qualification->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $attachedFile = new AttachedFile();
        if (!empty($_FILES['certificate']['name']) && !empty($_FILES['certificate']['error'])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($attachedFile->getErrMsgByErrCode($_FILES['certificate']['error']));
        }
        if (!empty($_FILES['certificate']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['certificate']['tmp_name'])) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('LBL_Please_select_a_file'));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $uqualification_id = $qualification->getMainTableRecordId();
            $res = $attachedFile->saveDoc($_FILES['certificate']['tmp_name'], AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $post['uqualification_user_id'], $uqualification_id, $_FILES['certificate']['name'], -1, $unique_record = true, 0, $_FILES['certificate']['type']);
            if (!$res) {
                $db->rollbackTransaction();
                Message::addErrorMessage($attachedFile->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
        }
        $db->commitTransaction();
        $this->set('msg', Label::getLabel('MSG_Qualification_Setup_Successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteTeacherQualification()
    {
        $uqualification_id = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        if ($uqualification_id < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* [ */
        $srch = new UserQualificationSearch();
        $srch->addCondition('uqualification_user_id', '=', $this->userId);
        $srch->addCondition('uqualification_id', '=', $uqualification_id);
        $srch->addMultiplefields(['uqualification_id']);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (false == $row) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $userQualification = new UserQualification($uqualification_id);
        if (true !== $userQualification->deleteRecord()) {
            Message::addErrorMessage($userQualification->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Qualification_Removed_Successfuly'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function downloadResume($subRecordId)
    {
        $subRecordId = FatUtility::int($subRecordId);
        if ($subRecordId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequest', 'form'));
        }
        $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $this->userId, $subRecordId);
        if (!$fileRow || $fileRow['afile_physical_path'] == "") {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequest', 'form'));
        }
        AttachedFile::downloadFile($fileRow['afile_name'], $fileRow['afile_physical_path']);
    }

    public function setUpProfileImage()
    {
        $userId = $this->userId;
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        if ($post['action'] == "demo_avatar") {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                $msgLblKey = CommonHelper::getFileUploadErrorLblKeyFromCode($_FILES['user_profile_image']['error']);
                Message::addErrorMessage(Label::getLabel($msgLblKey));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('file', CommonHelper::generateFullUrl('Image', 'user', array($userId, 'ORIGINAL', 0)) . '?' . time());
        }
        if ($post['action'] == "avatar") {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                $msgLblKey = CommonHelper::getFileUploadErrorLblKeyFromCode($_FILES['user_profile_image']['error']);
                Message::addErrorMessage(Label::getLabel($msgLblKey));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_USER_PROFILE_CROPED_IMAGE, $userId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $data = json_decode(stripslashes($post['img_data']));
            CommonHelper::crop($data, CONF_UPLOADS_PATH . $res);
            $this->set('file', CommonHelper::generateFullUrl('Image', 'user', array($userId, 'MEDIUM', true)) . '?' . time());
        }
        $this->set('msg', Label::getLabel('MSG_File_uploaded_successfully'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function logoutGuestUser()
    {
        UserAuthentication::logoutGuestTeacher();
        FatApp::redirectUser(CommonHelper::generateUrl());
    }

    private function getProfileImageForm()
    {
        $frm = new Form('frmProfileImage', ['id' => 'frmProfileImage']);
        $frm->addFileUpload(Label::getLabel('LBL_Profile_Picture'), 'user_profile_image', ['onchange' => 'popupImage(this)', 'accept' => 'image/*']);
        $frm->addHiddenField('', 'update_profile_img', Label::getLabel('LBL_Update_Profile_Picture'), ['id' => 'update_profile_img']);
        $frm->addHiddenField('', 'rotate_left', Label::getLabel('LBL_Rotate_Left'), ['id' => 'rotate_left']);
        $frm->addHiddenField('', 'rotate_right', Label::getLabel('LBL_Rotate_Right'), ['id' => 'rotate_right']);
        $frm->addHiddenField('', 'remove_profile_img', 0, ['id' => 'remove_profile_img']);
        $frm->addHiddenField('', 'action', 'avatar', ['id' => 'avatar-action']);
        $frm->addHiddenField('', 'img_data', '', ['id' => 'img_data']);
        return $frm;
    }

    private function getApplyTeachFrm(int $langId)
    {
        $frm = new Form('frmApplyTeachFrm');
        $frm->addHiddenField('', 'user_id', 0);
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email_ID', $langId), 'user_email', '', ['autocomplete' => 'off']);
        $fld->setUnique('tbl_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_Password', $langId), 'user_password');
        $fld->requirements()->setRequired();
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_8_Digit_AlphaNumeric_Password', $langId));
        $frm->addHiddenField('', 'user_preferred_dashboard', User::USER_TEACHER_DASHBOARD);
        $frm->addHiddenField('', 'agree', 1)->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_REGISTER_WITH_EMAIL', $this->siteLangId));
        return $frm;
    }

    private function getApplyToTeachFaqs()
    {
        $srch = Faq::getSearchObject($this->siteLangId, false);
        $srch->addMultipleFields(['faq_identifier', 'faq_id', 'faq_category', 'faq_active', 'faq_title', 'faq_description']);
        $srch->addCondition('faq_category', '=', Faq::CATEGORY_APPLY_TO_TEACH);
        $srch->addOrder('faq_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        return $records;
    }

    private function verifyTeacherRequestRow()
    {
        $srch = new TeacherRequestSearch();
        $srch->joinUsers();
        $srch->addCondition('utrequest_user_id', '=', $this->userId);
        $srch->addMultiplefields(array(
            'utrequest_attempts',
            'utrequest_id',
            'utrequest_status',
            'concat(user_first_name, " ", user_last_name) as user_name',
            'utrequest_reference'
        ));
        $srch->addOrder('utrequest_id', 'desc');
        $rs = $srch->getResultSet();
        $teacherRequestRow = FatApp::getDb()->fetch($rs);
        return $teacherRequestRow;
    }

}
