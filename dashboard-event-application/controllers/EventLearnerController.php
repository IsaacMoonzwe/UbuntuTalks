<?php

class EventLearnerController extends LearnerBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        if (true != EventUser::isLearnerProfileCompleted()) {
            Message::addInfo(Label::getLabel('LBL_Please_Update_Your_Timezone'));
            FatApp::redirectUser(CommonHelper::generateUrl('account', 'profileInfo'));
        }
        $frmSrch = $this->getSearchForm();
        $frmSrch->fill([
            'status' => ScheduledLesson::STATUS_UPCOMING,
            'show_group_classes' => ApplicationConstants::YES
        ]);
        $this->set('frmSrch', $frmSrch);
        $userId = EventUserAuthentication::getLoggedUserId();
        $userObj = new EventUser($userId);
        $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
        $this->set('userDetails', $userDetails);
        $this->set('userTotalWalletBalance', EventUser::getUserBalance($userId, false));
        $this->_template->addJs('js/learnerLessonCommon.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        if ($currentLangCode = strtolower(Language::getLangCode($this->siteLangId))) {
            if (file_exists(CONF_THEME_PATH . "js/locales/{$currentLangCode}.js")) {
                $this->_template->addJs("js/locales/{$currentLangCode}.js");
            }
        }
        $this->_template->render();
    }

    public function message($userId = 0)
    {
        $userId = FatUtility::int($userId);
        $teacherObj = new EventUser($userId);
        $teacherDetails = $teacherObj->getUserInfo(null, true, true);
        if (!$teacherDetails || $userId == EventUserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $userObj = new EventUser(EventUserAuthentication::getLoggedUserId());
        $userDetails = $userObj->getUserInfo(null, true, true);
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

    private function getOrderSearchForm($langId)
    {
        $frm = new Form('frmOrderSrch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $langId), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Keyword', $langId)]);
        $frm->addSelectBox('Status', 'status', [-2 => Label::getLabel('LBL_Does_Not_Matter', $langId)] + Order::getPaymentStatusArr($langId), '', [], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $langId), 'date_from', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $langId), 'date_to', '', ['readonly' => 'readonly']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Submit', $langId), ['class' => 'btn btn--primary']);
        $fld_cancel = $frm->addResetButton("", "btn_clear", Label::getLabel('LBL_Clear', $langId), ['onclick' => 'clearSearch();', 'class' => 'btn--clear']);
        $frm->addHiddenField('', 'page', 1);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function getOrders()
    {
        $frm = $this->getOrderSearchForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $ordersData = Order::getOrders($post, EventUser::USER_TYPE_LEANER, EventUserAuthentication::getLoggedUserId());
        $statusArr = Order::getPaymentStatusArr($this->siteLangId);
        $this->set('statusArr', $statusArr);
        $this->set('ordersData', $ordersData);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function myTeacher($user_name)
    {
        $srchTeacher = new EventUserSearch();
        $srchTeacher->addMultipleFields(['user_id']);
        $srchTeacher->addCondition('user_url_name', '=', $user_name);
        $srchTeacher->setPageSize(1);
        $rsTeacher = $srchTeacher->getResultSet();
        $teacherData = FatApp::getDb()->fetch($rsTeacher);
        if (empty($teacherData)) {
            FatUtility::exitWithErrorCode(404);
        }
        $teacherId = $teacherData['user_id'];
        $teacherId = FatUtility::int($teacherId);
        $srch = new EventUserSearch();
        $srch->setTeacherDefinedCriteria();
        $srch->joinUserLang($this->siteLangId);
        $srch->joinUserSpokenLanguages($this->siteLangId);
        $srch->joinUserTeachLanguage($this->siteLangId);
        $srch->joinUserCountry($this->siteLangId);
        $srch->joinFavouriteTeachers(EventUserAuthentication::getLoggedUserId());
        $srch->joinTeacherLessonData();
        $srch->joinRatingReview();
        $srch->setPageSize(1);
        $srch->addCondition('user_id', '=', $teacherId);
        $srch->addMultipleFields([
            'user_id',
            'user_url_name',
            'user_first_name',
            'user_last_name',
            'CONCAT(user_first_name," ", user_last_name) as user_full_name',
            'user_country_id',
            'IFNULL(userlang_user_profile_Info, user_profile_info) as user_profile_info',
            'user_timezone',
            'IFNULL(uft_id, 0) as uft_id',
            'IFNULL(country_name, country_code) as user_country_name',
            'IFNULL(tlanguage_name, tlanguage_identifier) as teachlanguage_name',
            'utsl.spoken_language_names',
            'utsl.spoken_languages_proficiency',
            'us_video_link',
            'us_is_trial_lesson_enabled',
            'utl_tlanguage_ids'
        ]);
        $rs = $srch->getResultSet();
        $teacher = FatApp::getDb()->fetch($rs);
        if (empty($teacher)) {
            FatUtility::exitWithErrorCode(404);
        }
        $proficiencyArr = SpokenLanguage::getProficiencyArr(CommonHelper::getLangId());
        $teacher['proficiencyArr'] = $proficiencyArr;
        $this->set('teacher', $teacher);
        $this->_template->render();
    }

    public function toggleTeacherFavorite()
    {

        $post = FatApp::getPostedData();
        $teacherId = FatUtility::int($post['teacher_id']);
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        $db = FatApp::getDb();
        $srch = new EventUserSearch();
        $srch->setTeacherDefinedCriteria();
        $srch->joinUserSpokenLanguages($this->siteLangId);
        $srch->joinUserCountry($this->siteLangId);
        $srch->setPageSize(1);
        $srch->addCondition('user_id', '=', $teacherId);
        $srch->addMultipleFields(['user_id', 'user_first_name', 'user_last_name']);
        $teacher = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($teacher)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request', $this->siteLangId));
        }
        $message = '';
        $action = 'N'; //nothing happened
        $srch = new UserFavoriteTeacherSearch();
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('uft_user_id', '=', $loggedUserId);
        $srch->addCondition('uft_teacher_id', '=', $teacherId);
        if (!$row = $db->fetch($srch->getResultSet())) {
            $userObj = new EventUser($loggedUserId);
            if (!$userObj->addUpdateUserFavoriteTeacher($teacherId)) {
                $message = Label::getLabel('LBL_Some_problem_occurred,_Please_contact_webmaster', $this->siteLangId);
            }
            $action = 'A'; //Added to favorite
            $message = Label::getLabel('LBL_Teacher_has_been_marked_as_favourite_successfully', $this->siteLangId);
        } else {
            if (!$db->deleteRecords(EventUser::DB_TBL_TEACHER_FAVORITE, ['smt' => 'uft_user_id = ? AND uft_teacher_id = ?', 'vals' => [$loggedUserId, $teacherId]])) {
                $message = Label::getLabel('LBL_Some_problem_occurred,_Please_contact_webmaster', $this->siteLangId);
            }
            $action = 'R'; //Removed from favorite
            $message = Label::getLabel('LBL_Teacher_has_been_removed_from_favourite_list', $this->siteLangId);
        }
        FatUtility::dieJsonSuccess(['msg' => $message, 'action' => $action]);
    }

    public function favourites()
    {
        $frmFavSrch = $this->getFavouriteSearchForm($this->siteLangId);
        $this->set('frmFavSrch', $frmFavSrch);
        $this->_template->render();
    }

    private function getFavouriteSearchForm($langId)
    {
        $frm = new Form('frmFavSrch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $langId), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Keyword', $langId)]);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Submit', $langId), ['class' => 'btn btn--primary']);
        $fld_cancel = $frm->addResetButton('', "btn_clear", Label::getLabel('LBL_Clear', $langId), ['onclick' => 'clearSearch();', 'class' => 'btn--clear']);
        $fld_submit->attachField($fld_cancel);
        $frm->addHiddenField('', 'page', 1);
        return $frm;
    }

    public function getFavourites()
    {
        $frm = $this->getFavouriteSearchForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $userObj = new EventUser(EventUserAuthentication::getLoggedUserId());
        $favouritesData = $userObj->getFavourites($post, $this->siteLangId);
        $countryObj = new Country();
        $countriesArr = $countryObj->getCountriesArr($this->siteLangId);
        $this->set('countriesArr', $countriesArr);
        $this->set('favouritesData', $favouritesData);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

}
