<?php

class TeacherController extends TeacherBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $viewProfile = true;
        if (false == $this->teacherProfileProgress['isProfileCompleted']) {
            $viewProfile = false;
        }
        $userId = UserAuthentication::getLoggedUserId();
        $userObj = new User($userId);
        $userDetails = $userObj->getDashboardData($this->siteLangId, true);
        $durationArr = Statistics::getDurationTypesArr($this->siteLangId);
        $statistics = new Statistics($userId);
        $earningData = $statistics->getEarning(Statistics::TYPE_ALL);
        $frmSrch = $this->getSearchForm();
        $frmSrch->fill([
            'status' => ScheduledLesson::STATUS_UPCOMING,
            'show_group_classes' => ApplicationConstants::YES,
            'listingView' => 'shortDetail',
        ]);
        $reportSearchForm = $this->reportSearchForm($this->siteLangId);
        $reportSearchForm->fill(['forGraph' => ApplicationConstants::YES]);
        $this->set('frmSrch', $frmSrch);
        $this->set('durationArr', $durationArr);
        $this->set('earningData', $earningData);
        $this->set('teacherProfileProgress', $this->teacherProfileProgress);
        $this->set('userDetails', $userDetails);
        $this->set('viewProfile', $viewProfile);
        $this->set('reportSearchForm', $reportSearchForm);
        $this->set('userTotalWalletBalance', User::getUserBalance($userId, false));
        $this->set('userTimezone', Mydate::getUserTimeZone());
        $this->set('setMonthAndWeekName', true);
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->render();
    }

    public function settingsInfoForm()
    {
        $showAdminSlab = FatApp::getPostedData('showAdminSlab', FatUtility::VAR_BOOLEAN, false);
        $userTeachingLang = $this->getUserTeachLangData();
        $priceSlab = new PriceSlab();
        $slabData = $priceSlab->getAllSlabs(true, ['prislab_min as minSlab', 'prislab_max as maxSlab', 'CONCAT(prislab_min,"-",prislab_max) as minMaxKey']);
        $teacherAddedSlabs = array_column($userTeachingLang, 'minMaxKey', 'minMaxKey'); 
        unset($teacherAddedSlabs['0-0']);
        $slabDifference = [];
        if (!empty($userTeachingLang)) {
            $adminAddedSlabs = array_column($slabData, 'minMaxKey', 'minMaxKey');
            $slabDifference = array_merge(array_diff($adminAddedSlabs, $teacherAddedSlabs), array_diff($teacherAddedSlabs, $adminAddedSlabs));
        }
        $priceSum = array_sum(array_column($userTeachingLang, 'ustelgpr_price'));
        $slabs = $slabData;
        
        if ($priceSum > 0 && !$showAdminSlab) {
            $slabs = $this->formatTeacherSlabs($userTeachingLang);
        }
        $frm = $this->getSettingsForm($userTeachingLang, $slabs, $showAdminSlab);
        $this->set('frm', $frm);
        $this->set('userToTeachLangRows', $userTeachingLang);
        $this->set('slabDifference', $slabDifference);
        $this->set('showAdminSlab', $showAdminSlab);
        $this->set('slabs', $slabs);
        $this->set('priceSum', $priceSum);
        $this->_template->render(false, false);
    }

    public function setUpSettings()
    {
        $showAdminSlab = FatApp::getPostedData('showAdminSlab', FatUtility::VAR_BOOLEAN, false);
        $form = $this->getSettingsForm(null, null, $showAdminSlab);
        $post = FatApp::getPostedData();
        $postData = $form->getFormDataFromArray(FatApp::getPostedData());
        // if (false === $postData) {
        //     FatUtility::dieJsonError(current($form->getValidationErrors()));
        // }
        if (empty($post['duration'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_DURATION_IS_REQURIED'));
        }
        $userId = UserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        $db->startTransaction();
        $teachLangPrice = new TeachLangPrice();
        if (!$teachLangPrice->deleteAllUserPrice($userId)) {
            FatUtility::dieJsonError($teachLangPrice);
        }
        foreach ($post['duration'] as $durationKey => $duration) {
            if (empty($duration) || $durationKey != $duration) {
                continue;
            }
            if (empty($post['ustelgpr_price'])) {
                continue;
            }
            $slabs = $post['ustelgpr_price'][$duration];
            foreach ($slabs as $slabKey => $languages) {
                if (empty($languages)) {
                    continue;
                }
                $slabAarray = explode('-', $slabKey);
                foreach ($languages as $userTeachLangang => $price) {
                    $teachLangPrice = new TeachLangPrice($duration, $userTeachLangang);
                    if (!$teachLangPrice->saveTeachLangPrice($slabAarray[0], $slabAarray[1], $price)) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($teachLangPrice->getError());
                    }
                }
            }
        }
        // Update Teach Lang Minimum & Maximum Prices
        (new TeacherStat($userId))->setTeachLangPrices();
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_Setup_successful'));
    }

    public function teacherLanguagesForm()
    {
        $speakLangs = SpokenLanguage::getAllLangs($this->siteLangId, true);
        $frm = $this->getTeacherLanguagesForm($this->siteLangId, $speakLangs);
        $profArr = SpokenLanguage::getProficiencyArr($this->siteLangId);
        $this->set('frm', $frm);
        $this->set('speakLangs', $speakLangs);
        $this->set('profArr', $profArr);
        $this->_template->render(false, false);
    }

    public function getTeacherProfileProgress()
    {
        $teacherProfileProgress = User::getTeacherProfileProgress();
        $this->set('teacherProfileProgress', $teacherProfileProgress);
        $msg = '';
        if (false == $teacherProfileProgress['isProfileCompleted']) {
            $msg = Label::getLabel('LBL_Please_Complete_Profile_to_be_visible_on_teachers_listing_page');
        }
        FatUtility::dieJsonSuccess(['msg' => $msg, 'teacherProfileProgress' => $teacherProfileProgress]);
    }

    public function deleteLanguageRow($id = 0)
    {
        $id = FatUtility::int($id);
        $teacherId = UserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        if (!$db->deleteRecords(UserToLanguage::DB_TBL, ['smt' => 'utsl_user_id = ? and utsl_slanguage_id = ?', 'vals' => [$teacherId, $id]])) {
            Message::addErrorMessage(Label::getLabel($db->getError()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        // Update Teacher's teach language stat
        (new TeacherStat($teacherId))->setSpeakLang();
        $this->set('msg', Label::getLabel('MSG_Language_Removed_Successfuly!'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteTeachLanguageRow($id = 0)
    {
        $userId = UserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        if (!$db->deleteRecords('tbl_user_teach_languages', ['smt' => 'utl_user_id = ? and utl_slanguage_id = ?', 'vals' => [$userId, FatUtility::int($id)]])) {
            Message::addErrorMessage(Label::getLabel($db->getError()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        // Update Teach Lang Minimum & Maximum Prices
        (new TeacherStat($userId))->setTeachLangPrices();
        $this->set('msg', Label::getLabel('MSG_Language_Removed_Successfuly!'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function setupTeacherLanguages()
    {
        $frm = $this->getTeacherLanguagesForm($this->siteLangId, []);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $speakLangs = array_filter(FatUtility::int($post['utsl_slanguage_id']));
        if (empty($speakLangs)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SPEAK_LANGUAGE_IS_REQURIED'));
        }
        $teacherId = UserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        $db->startTransaction();
        $error = '';
        if (!$this->deleteUserTeachLang($db, $post['teach_lang_id'], $error)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($error);
        }
        foreach ($post['teach_lang_id'] as $tlang) {
            $userTeachLanguage = new UserTeachLanguage($teacherId);
            if (!$userTeachLanguage->saveTeachLang($tlang)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($userTeachLanguage->getError());
            }
        }
        if (!$this->deleteUserSpeakLang($db, $speakLangs, $error)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($error);
        }
        foreach ($speakLangs as $key => $lang) {
            $insertArr = ['utsl_slanguage_id' => $lang, 'utsl_proficiency' => $post['utsl_proficiency'][$key], 'utsl_user_id' => $teacherId];
            if (!$db->insertFromArray(UserToLanguage::DB_TBL, $insertArr, false, [], $insertArr)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($db->getError());
            }
        }
        (new TeacherStat($teacherId))->setTeachLangPrices();
        (new TeacherStat($teacherId))->setSpeakLang();
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_Setup_successful'));
    }

    public function teacherQualificationForm($uqualification_id = 0)
    {
        $uqualification_id = FatUtility::int($uqualification_id);
        $experienceFrm = $this->getTeacherQualificationForm(true);
        if ($uqualification_id > 0) {
            $srch = new UserQualificationSearch();
            $srch->addCondition('uqualification_user_id', '=', UserAuthentication::getLoggedUserId());
            $srch->addCondition('uqualification_id', '=', $uqualification_id);
            $rs = $srch->getResultSet();
            $row = FatApp::getDb()->fetch($rs);
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, UserAuthentication::getLoggedUserId(), $uqualification_id);
            $certificateRequried = false; //(empty($file_row)) ? true : false;
            $field = $experienceFrm->getField('certificate');
            $field->requirements()->setRequired($certificateRequried);
            $experienceFrm->fill($row);
        }
        $this->set('experienceFrm', $experienceFrm);
        $this->_template->render(false, false);
    }

    public function setUpTeacherQualification()
    {
        $frm = $this->getTeacherQualificationForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $uqualification_id = FatApp::getPostedData('uqualification_id', FatUtility::VAR_INT, 0);
        $qualification = new UserQualification($uqualification_id);
        $post['uqualification_active'] = 1;
        $post['uqualification_user_id'] = UserAuthentication::getLoggedUserId();
        $qualification->assignValues($post);
        if (true !== $qualification->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($qualification->getError());
        }
        if (!empty($_FILES['certificate']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['certificate']['tmp_name'])) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(Label::getLabel('LBL_Please_select_a_file'));
            }
            $uqualification_id = $qualification->getMainTableRecordId();
            $fileHandlerObj = new AttachedFile();
            $res = $fileHandlerObj->saveDoc($_FILES['certificate']['tmp_name'], AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $post['uqualification_user_id'], $uqualification_id, $_FILES['certificate']['name'], -1, $unique_record = true, 0, $_FILES['certificate']['type']);
            if (!$res) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($fileHandlerObj->getError());
            }
        }
        // Update Teacher's Qualification stat
        (new TeacherStat(UserAuthentication::getLoggedUserId()))->setQualification();
        $db->commitTransaction();
        $this->set('msg', Label::getLabel('MSG_Qualification_Setup_Successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteTeacherQualification($uqualification_id = 0)
    {
        $uqualification_id = FatUtility::int($uqualification_id);
        if ($uqualification_id < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        // [
        $teacherId = UserAuthentication::getLoggedUserId();
        $srch = new UserQualificationSearch();
        $srch->addCondition('uqualification_user_id', '=', $teacherId);
        $srch->addCondition('uqualification_id', '=', $uqualification_id);
        $srch->addMultiplefields(['uqualification_id']);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (false == $row) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        // ]
        $userQualification = new UserQualification($uqualification_id);
        if (true !== $userQualification->deleteRecord()) {
            Message::addErrorMessage($userQualification->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        // Update Teacher's Qualification stat
        (new TeacherStat($teacherId))->setQualification();
        $this->set('msg', Label::getLabel('MSG_Qualification_Removed_Successfuly'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function teacherQualification()
    {
        $srch = new UserQualificationSearch();
        $srch->addMultiplefields(['uqualification_id', 'afile_name', 'uqualification_title', 'uqualification_institute_name', 'uqualification_institute_address', 'uqualification_description', 'uqualification_start_year', 'uqualification_end_year']);
        $srch->joinTable(AttachedFile::DB_TBL, 'Left Outer Join', 'uqualification_id=afile_record_subid');
        $srch->addCondition('uqualification_user_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addCondition('uqualification_active', '=', 1);
        $qualificationData = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('qualificationData', $qualificationData);
        $this->_template->render(false, false);
    }

    public function teacherPreferencesForm()
    {
        $userId = UserAuthentication::getLoggedUserId();
        $frm = $this->getTeacherPreferencesForm();
        $db = FatApp::getDb();
        $teacherPreferenceSrch = new UserToPreferenceSearch();
        $teacherPreferenceSrch->joinToPreference();
        $teacherPreferenceSrch->addMultiplefields(['utpref_preference_id', 'preference_type']);
        $teacherPreferenceSrch->addCondition('utpref_user_id', '=', $userId);
        $rs = $teacherPreferenceSrch->getResultSet();
        $teacherPrefArr = $db->fetchAll($rs);
        $arrOptions = [];
        foreach ($teacherPrefArr as $val) {
            $arrOptions['pref_' . $val['preference_type']][] = $val['utpref_preference_id'];
        }
        $frm->fill($arrOptions);
        $this->set('teacherPreferencesFrm', $frm);
        $this->_template->render(false, false);
    }

    public function setupTeacherPreferences()
    {
        $frm = $this->getTeacherPreferencesForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
        $db = FatApp::getDb();
        $userId = UserAuthentication::getLoggedUserId();
        $deleteRecords = $db->deleteRecords(Preference::DB_TBL_USER_PREF, ['smt' => 'utpref_user_id = ?', 'vals' => [$userId]]);
        if (!$deleteRecords) {
            FatUtility::dieWithError($db->getError());
        }
        unset($post['teach_lang']);
        $preference = 0;
        foreach ($post as $key => $val) {
            if (empty($val)) {
                continue;
            }
            foreach ($val as $innerVal) {
                if (!$db->insertFromArray(Preference::DB_TBL_USER_PREF, ['utpref_preference_id' => $innerVal, 'utpref_user_id' => $userId])) {
                    FatUtility::dieWithError($db->getError());
                }
                $preference = 1;
            }
        }
        // Update Teacher's Preferences
        (new TeacherStat($userId))->setPreference($preference);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Preferences_updated_successfully!'));
    }

    public function availability()
    {
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $this->set('cssClassArr', $cssClassNamesArr);
        $userId = UserAuthentication::getLoggedUserId();
        $this->set('userId', $userId);
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->set('setMonthAndWeekName', true);
        $this->_template->render();
    }

    public function teacherGeneralAvailability()
    {
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $this->set('cssClassArr', $cssClassNamesArr);
        $userId = UserAuthentication::getLoggedUserId();
        $this->set('userId', $userId);
        $this->_template->render(false, false);
    }

    public function teacherWeeklySchedule()
    {
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $this->set('cssClassArr', $cssClassNamesArr);
        $userId = UserAuthentication::getLoggedUserId();
        $this->set('userId', $userId);
        $this->_template->render(false, false);
    }

    public function deleteTeacherGeneralAvailability($tgavl_id = 0)
    {
        $tgavl_id = FatUtility::int($tgavl_id);
        $tGAvail = new TeacherGeneralAvailability();
        if (!$tGAvail->deleteTeacherGeneralAvailability($tgavl_id, UserAuthentication::getLoggedUserId())) {
            FatUtility::dieWithError($tGAvail->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Availability_deleted_successfully!'));
    }

    public function deleteTeacherWeeklySchedule()
    {
        $post = FatApp::getPostedData();
        if (false === $post) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $postJson = json_decode($post['data']);
        $userId = UserAuthentication::getLoggedUserId();
        $tWsch = new TeacherWeeklySchedule();
        if (!$tWsch->deleteTeacherWeeklySchedule($userId, $postJson->start, $postJson->end, $postJson->date, $postJson->day, $postJson->_id)) {
            FatUtility::dieWithError($tWsch->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Availability_deleted_successfully!'));
    }

    public function setupTeacherGeneralAvailability()
    {
        $post = FatApp::getPostedData();
        if (false === $post) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userId = UserAuthentication::getLoggedUserId();
        $tGAvail = new TeacherGeneralAvailability();
        if (!$tGAvail->addTeacherGeneralAvailability($post, $userId)) {
            FatUtility::dieWithError($tGAvail->getError());
        }
        /* Update Teacher's General Availability Stat */
        $available = empty(json_decode($post['data'] ?? '')) ? 0 : 1;
        (new TeacherStat($userId))->setAvailability($available);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Availability_updated_successfully!'));
    }

    public function setupTeacherWeeklySchedule()
    {
        $post = FatApp::getPostedData();
        if (false === $post) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userId = UserAuthentication::getLoggedUserId();
        $tWsch = new TeacherWeeklySchedule();
        if (!$tWsch->addTeacherWeeklySchedule($post, $userId)) {
            FatUtility::dieWithError($tWsch->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Availability_updated_successfully!'));
    }

    public function qualificationFile($userId = 0, $subRecordId = 0, $sizeType = '', $cropedImage = false)
    {
        $userId = UserAuthentication::getLoggedUserId();
        $recordId = FatUtility::int($userId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_QUALIFICATION_FILE, $recordId, $subRecordId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name);
                break;
        }
    }

    public function paypalEmailAddressForm()
    {
        $payoutMethods = PaymentMethods::getPayoutMethods(true, $this->siteLangId);
        if (empty($payoutMethods[PaypalPayout::KEY_NAME])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PAYMENT_METHOD_NOT_ACTIVE_YET'));
        }
        $frm = $this->getPaypalEmailAddressForm();
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $data = $userObj->getUserPaypalInfo();
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('payoutMethods', $payoutMethods);
        $this->_template->render(false, false);
    }

    public function setUpPaypalInfo()
    {
        $frm = $this->getPaypalEmailAddressForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $userObj = new User(UserAuthentication::getLoggedUserId());
        if (!$userObj->updatePaypalInfo($post)) {
            Message::addErrorMessage(Label::getLabel($userObj->getError()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function bankInfoForm()
    {
        $payoutMethods = PaymentMethods::getPayoutMethods(true, $this->siteLangId);
        if (empty($payoutMethods[PaymentMethods::BANK_PAYOUT_KEY])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PAYMENT_METHOD_NOT_ACTIVE_YET'));
        }
        $frm = $this->getBankInfoForm();
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $data = $userObj->getUserBankInfo();
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('payoutMethods', $payoutMethods);
        $this->_template->render(false, false);
    }

    public function setUpBankInfo()
    {
        $frm = $this->getBankInfoForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $userObj = new User(UserAuthentication::getLoggedUserId());
        if (!$userObj->updateBankInfo($post)) {
            Message::addErrorMessage(Label::getLabel($userObj->getError()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function message($userId = 0)
    {
        $userId = FatUtility::int($userId);
        $userObj = new User($userId);
        $userDetails = $userObj->getUserInfo(null, true, true);
        if (!$userDetails || $userId == UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $teacherObj = new User(UserAuthentication::getLoggedUserId());
        $teacherDetails = $teacherObj->getUserInfo(null, true, true);
        $this->set('teacherDetails', $teacherDetails);
        $this->set('userDetails', $userDetails);
        $this->_template->render();
    }

    public function orders()
    {
        $frmOrderSrch = $this->getOrderSearchForm($this->siteLangId);
        $this->set('frmOrderSrch', $frmOrderSrch);
        $this->set('setMonthAndWeekName', true);
        $this->_template->render();
    }

    public function getOrders()
    {
        $frm = $this->getOrderSearchForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $ordersData = Order::getOrders($post, User::USER_TYPE_TEACHER, UserAuthentication::getLoggedUserId());
        $statusArr = Order::getPaymentStatusArr($this->siteLangId);
        $this->set('statusArr', $statusArr);
        $this->set('ordersData', $ordersData);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    private function getSettingsForm(array $userTeachingLang = null, array $slabs = null, bool $showAdminSlab = false): Form
    {
        $frm = new Form('frmSettings');
        $lessonDurations = CommonHelper::getPaidLessonDurations();
        if (null === $userTeachingLang) {
            $userTeachingLang = $this->getUserTeachLangData();
        }
        // prx($userTeachingLang);
        if (null === $slabs) {
            $priceSum = array_sum(array_column($userTeachingLang, 'ustelgpr_price'));
            if ($priceSum > 0 && !$showAdminSlab) {
                $slabs = $this->formatTeacherSlabs($userTeachingLang);
            } else {
                $priceSlab = new PriceSlab();
                $slabs = $priceSlab->getAllSlabs(true, ['prislab_min as minSlab', 'prislab_max as maxSlab', 'CONCAT(prislab_min,"-",prislab_max) as minMaxKey']);
            }
        }
        $userTeachLangData = array_column($userTeachingLang, 'teachLangName', 'utl_id');
        $teacherLessonDuration = array_column($userTeachingLang, 'ustelgpr_slot', 'ustelgpr_slot');
        $updatePrice = $frm->addFloatField(Label::getLabel('Lbl_Add_price'), 'price_update'); // only use for view and update value by ajax
        $updatePrice->requirements()->setRange(1, 99999);
        $updatePrice->requirements()->setRequired(false);
        $showAdminSlabField = $frm->addHiddenField('', 'showAdminSlab', ($showAdminSlab) ? applicationConstants::YES : applicationConstants::NO);
        $showAdminSlabField->requirements()->setRange(0, 1);
        $showAdminSlabField->requirements()->setRequired(true);
        $defaultSlot = FatApp::getConfig('conf_default_paid_lesson_duration', FatUtility::VAR_STRING, 60);
        foreach ($lessonDurations as $lessonDuration) {
            $durationFld = $frm->addCheckBox(sprintf(Label::getLabel('LBL_%d_mins'), $lessonDuration), 'duration[' . $lessonDuration . ']', $lessonDuration, [], false, 0);
            if ($lessonDuration == $defaultSlot) {
                $durationFld->requirements()->setRequired(true);
            }
            if (array_key_exists($lessonDuration, $teacherLessonDuration) || $lessonDuration == $defaultSlot) {
                $durationFld->checked = true;
            }
            foreach ($slabs as $slab) {
                foreach ($userTeachLangData as $uTeachLangId => $uTeachLang) {
                    $filedName = 'ustelgpr_price[' . $lessonDuration . '][' . $slab['minMaxKey'] . '][' . $uTeachLangId . ']';
                    $label = $filedName;
                    $fld = $frm->addFloatField($uTeachLang, $filedName);
                    $fld->requirements()->setRange(1, 99999);
                    $fld->requirements()->setRequired(true);
                    $keyField = $uTeachLangId . '-' . $slab['minMaxKey'] . '-' . $lessonDuration;
                    if (!empty($userTeachingLang[$keyField]['ustelgpr_price']) && !$showAdminSlab) {
                        $fld->value = $userTeachingLang[$keyField]['ustelgpr_price'];
                    }
                    $durationFld->requirements()->addOnChangerequirementUpdate($lessonDuration, 'eq', $filedName, $fld->requirements());
                    $fieldRequirement = new FormFieldRequirement($filedName, $label);
                    $fieldRequirement->setRequired(false);
                    $fieldRequirement->setRange(0, 99999);
                    $durationFld->requirements()->addOnChangerequirementUpdate($lessonDuration, 'ne', $filedName, $fieldRequirement);
                }
            }
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE_CHANGES'));
        $frm->addButton('', 'next_btn', Label::getLabel('LBL_Next'));
        $frm->addButton('', 'back_btn', Label::getLabel('LBL_Back'));
        return $frm;
    }

    private function getTeacherLanguagesForm(int $langId, array $spokenLangs = [])
    {
        $frm = new Form('frmTeacherLanguages');
        $userId = UserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        // Get Teaching Languages
        $teacherTeachLangArr = TeachingLanguage::getAllLangs($langId, true);
        // Get Spoken Languages
        $langArr = $spokenLangs ?: SpokenLanguage::getAllLangs($langId, true);
        // ]
        $profArr = SpokenLanguage::getProficiencyArr($this->siteLangId);
        $userTeachLanguage = new UserTeachLanguage($userId);
        $userTeachlangs = $userTeachLanguage->getUserTeachlanguages($this->siteLangId);
        $userTeachlangs->addMultiplefields(['utl_tlanguage_id']);
        $userTeachlangs->addCondition('utl_user_id', '=', $userId);
        $userToTeachLangRows = $db->fetchAll($userTeachlangs->getResultSet(), 'utl_tlanguage_id');
        $userToLangSrch = new SearchBase('tbl_user_to_spoken_languages');
        $userToLangSrch->addMultiplefields(['utsl_slanguage_id', 'utsl_proficiency']);
        $userToLangSrch->addCondition('utsl_user_id', '=', $userId);
        $userToLangRs = $userToLangSrch->getResultSet();
        $spokenLangRows = $db->fetchAllAssoc($userToLangRs);
        $frm->addCheckBoxes(Label::getLabel('LBL_Language_To_Teach'), 'teach_lang_id', $teacherTeachLangArr, array_keys($userToTeachLangRows))->requirements()->setRequired();
        $proficiencyLabel = stripslashes(Label::getLabel("LBL_I_don't_speak_this_language"));
        $speekLangFieldLabel = Label::getLabel('LBL_Language_I_Speak');
        $proficiencyFieldLabel = Label::getLabel('LBL_Language_Proficiency');
        foreach ($langArr as $key => $lang) {
            $speekLangField = $frm->addCheckBox($speekLangFieldLabel, 'utsl_slanguage_id[' . $key . ']', $key, ['class' => 'utsl_slanguage_id'], false, 0);
            $proficiencyField = $frm->addSelectBox($proficiencyFieldLabel, 'utsl_proficiency[' . $key . ']', $profArr, '', ['class' => 'utsl_proficiency select__dropdown'], $proficiencyLabel);
            if (array_key_exists($key, $spokenLangRows)) {
                $proficiencyField->value = $spokenLangRows[$key];
                $speekLangField->checked = true;
                $speekLangField->value = $key;
            }
            $proficiencyFieldRequirement = new FormFieldRequirement($proficiencyField->getName(), $proficiencyField->getCaption());
            $proficiencyFieldRequirement->setRequired();
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'gt', $proficiencyField->getName(), $proficiencyFieldRequirement);
            $proficiencyFieldRequirement = new FormFieldRequirement($proficiencyField->getName(), $proficiencyField->getCaption());
            $proficiencyFieldRequirement->setRequired(false);
            $speekLangField->requirements()->addOnChangerequirementUpdate(0, 'le', $proficiencyField->getName(), $proficiencyFieldRequirement);
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SAVE_CHANGES'));
        $frm->addButton('', 'next_btn', Label::getLabel('LBL_Next'));
        $frm->addButton('', 'back_btn', Label::getLabel('LBL_Back'));
        return $frm;
    }

    private function deleteUserTeachLang(Database $db, array $langIds = [], &$error = ''): bool
    {
        $teacherId = UserAuthentication::getLoggedUserId();
        $teachLangPriceQuery = 'DELETE ' . UserTeachLanguage::DB_TBL . ', ustelgpr FROM ' . UserTeachLanguage::DB_TBL . ' LEFT JOIN ' . TeachLangPrice::DB_TBL . ' ustelgpr ON ustelgpr.ustelgpr_utl_id = utl_id WHERE utl_user_id = ' . $teacherId;
        if (!empty($langIds)) {
            $langIds = implode(',', $langIds);
            $teachLangPriceQuery .= ' and utl_tlanguage_id NOT IN (' . $langIds . ')';
        }
        $db->query($teachLangPriceQuery);
        if ($db->getError()) {
            $error = $db->getError();
            return false;
        }
        return true;
    }

    private function deleteUserSpeakLang(Database $db, array $langIds = [], &$error = ''): bool
    {
        $teacherId = UserAuthentication::getLoggedUserId();
        $query = 'DELETE  FROM ' . UserToLanguage::DB_TBL . ' WHERE utsl_user_id = ' . $teacherId;
        if (!empty($langIds)) {
            $langIds = implode(',', $langIds);
            $query .= ' and utsl_slanguage_id NOT IN (' . $langIds . ')';
        }
        $db->query($query);
        if ($db->getError()) {
            $error = $db->getError();
            return false;
        }
        return true;
    }

    private function getTeacherPreferencesForm()
    {
        $frm = new Form('teacherPreferencesFrm');

        $preferenceTypeArr = Preference::getPreferenceTypeArr($this->siteLangId);
        $preferencesArr = Preference::getPreferencesArr($this->siteLangId);
        foreach ($preferenceTypeArr as $type => $preference) {
            if (empty($preferencesArr[$type])) {
                continue;
            }
            $frm->addCheckBoxes($preference, 'pref_' . $type, $preferencesArr[$type], [], ['class' => 'list-onethird list-onethird--bg']);
        }
        $frm->addButton('', 'btn_back', Label::getLabel('LBL_Back'));
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Save_Changes'));
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_next'));
        return $frm;
    }

    private function getPaypalEmailAddressForm()
    {
        $frm = new Form('frmBankInfo');
        $frm->addEmailField(Label::getLabel('M_Paypal_Email_Address'), 'ub_paypal_email_address');
        $frm->addButton('', 'btn_back', Label::getLabel('LBL_Back'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    private function getBankInfoForm()
    {
        $frm = new Form('frmBankInfo');
        $frm->addRequiredField(Label::getLabel('M_Bank_Name'), 'ub_bank_name', '');
        $frm->addRequiredField(Label::getLabel('M_Beneficiary/Account_Holder_Name'), 'ub_account_holder_name', '');
        $frm->addRequiredField(Label::getLabel('M_Bank_Account_Number'), 'ub_account_number', '');
        $frm->addRequiredField(Label::getLabel('M_IFSC_Code/Swift_Code'), 'ub_ifsc_swift_code', '');
        $frm->addTextArea(Label::getLabel('M_Bank_Address'), 'ub_bank_address', '');
        $frm->addButton('', 'btn_back', Label::getLabel('LBL_Back'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));
        // $frm->addButton('', 'btn_next', Label::getLabel('LBL_Next'));
        return $frm;
    }

    private function getOrderSearchForm($langId)
    {
        $frm = new Form('frmOrderSrch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $langId), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Keyword', $langId)]);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $langId), 'status', [-2 => Label::getLabel('LBL_Does_Not_Matter', $langId)] + Order::getPaymentStatusArr($langId), '', ['placeholder' => 'Select Status'], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $langId), 'date_from', '', ['placeholder' => '', 'readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $langId), 'date_to', '', ['placeholder' => '', 'readonly' => 'readonly']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Submit', $langId), ['class' => 'btn btn--primary']);
        $fld_cancel = $frm->addResetButton('', 'btn_clear', Label::getLabel('LBL_Clear', $langId), ['onclick' => 'clearSearch();', 'class' => 'btn--clear']);
        $fld_submit->attachField($fld_cancel);
        $frm->addHiddenField('', 'page', 1);
        return $frm;
    }

    private function getUserTeachLangData()
    {
        $teacherId = UserAuthentication::getLoggedUserId();
        $userTeachLanguage = new UserTeachLanguage($teacherId);
        $userTeachlangs = $userTeachLanguage->getUserTeachlanguages($this->siteLangId, true);
        $userTeachlangs->doNotCalculateRecords();
        $userTeachlangs->addMultiplefields([
            'IFNULL(`ustelgpr_slot`, 0) as ustelgpr_slot',
            'utl_tlanguage_id',
            'ustelgpr_price',
            'utl_id',
            'ustelgpr_min_slab',
            'ustelgpr_max_slab',
            'CONCAT(IFNULL(ustelgpr_min_slab,0),"-",IFNULL(ustelgpr_max_slab,0)) as minMaxKey',
            'CONCAT(`utl_id`, "-", IFNULL(`ustelgpr_min_slab`,0),"-", IFNULL(`ustelgpr_max_slab`,0), "-", IFNULL(`ustelgpr_slot`, 0)) as keyField',
            'IFNULL(tlanguage_name, tlanguage_identifier) as teachLangName',
        ]);
        return FatApp::getDb()->fetchAll($userTeachlangs->getResultSet(), 'keyField');
    }

    private function formatTeacherSlabs(array $slabData): array
    {
        $returnArray = [];
        foreach ($slabData as $key => $value) {
            if ($value['ustelgpr_min_slab'] > 0 && $value['ustelgpr_max_slab'] >= $value['ustelgpr_min_slab']) {
                $returnArray[$value['minMaxKey']] = [
                    'minSlab' => $value['ustelgpr_min_slab'],
                    'maxSlab' => $value['ustelgpr_max_slab'],
                    'minMaxKey' => $value['minMaxKey'],
                ];
            }
        }
        return $returnArray;
    }

}
