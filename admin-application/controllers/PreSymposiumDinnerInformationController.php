<?php

class PreSymposiumDinnerInformationController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewPurchasedLessons($this->admin_id, true);
        $this->canView = $this->objPrivilege->canViewPurchasedLessons($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditPurchasedLessons($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $jsVariables = [
            'confirmUnLockPrice' => Label::getLabel('LBL_Are_you_sure_to_unlock_this_price!'),
            'confirmRemove' => Label::getLabel('LBL_Do_you_want_to_remove'),
            'confirmCancel' => Label::getLabel('LBL_Do_you_want_to_cancel'),
            'languageUpdateAlert' => Label::getLabel('LBL_On_Submit_Price_Needs_To_Be_Set'),
            'layoutDirection' => CommonHelper::getLayoutDirection(),
            'processing' => Label::getLabel('LBL_Processing...'),
            'requestProcessing' => Label::getLabel('LBL_Request_Processing...'),
            'isMandatory' => Label::getLabel('LBL_is_mandatory'),
            'pleaseEnterValidEmailId' => Label::getLabel('LBL_Please_enter_valid_email_ID_for'),
            'charactersSupportedFor' => Label::getLabel('VLBL_Only_characters_are_supported_for'),
            'pleaseEnterIntegerValue' => Label::getLabel('VLBL_Please_enter_integer_value_for'),
            'pleaseEnterNumericValue' => Label::getLabel('VLBL_Please_enter_numeric_value_for'),
            'startWithLetterOnlyAlphanumeric' => Label::getLabel('VLBL_startWithLetterOnlyAlphanumeric'),
            'mustBeBetweenCharacters' => Label::getLabel('VLBL_Length_Must_be_between_6_to_20_characters'),
            'invalidValues' => Label::getLabel('VLBL_Length_Invalid_value_for'),
            'shouldNotBeSameAs' => Label::getLabel('VLBL_should_not_be_same_as'),
            'mustBeSameAs' => Label::getLabel('VLBL_must_be_same_as'),
            'mustBeGreaterOrEqual' => Label::getLabel('VLBL_must_be_greater_than_or_equal_to'),
            'mustBeGreaterThan' => Label::getLabel('VLBL_must_be_greater_than'),
            'mustBeLessOrEqual' => Label::getLabel('VLBL_must_be_less_than_or_equal_to'),
            'mustBeLessThan' => Label::getLabel('VLBL_must_be_less_than'),
            'lengthOf' => Label::getLabel('VLBL_Length_of'),
            'valueOf' => Label::getLabel('VLBL_Value_of'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'and' => Label::getLabel('VLBL_and'),
            'Quit' => Label::getLabel('LBL_Quit'),
            'Reschedule' => Label::getLabel('LBL_Reschedule'),
            'chargelearner' => Label::getLabel('LBL_Charge_Learner'),
            'bookedSlotAlert' => Label::getLabel('VLBL_You_have_already_booked_this_slot._Do_you_want_to_continue?'),
            'endLessonAlert' => Label::getLabel('VLBL_Are_you_sure_to_end_this_Lesson?'),
            'Proceed' => Label::getLabel('LBL_Proceed'),
            'Confirm' => Label::getLabel('LBL_Confirm'),
            'pleaseSelect' => Label::getLabel('VLBL_Please_select'),
            'confirmCancelessonText' => Label::getLabel('LBL_Are_you_sure_want_to_cancel_this_lesson'),
            'teacherProfileIncompleteMsg' => Label::getLabel('LBL_Please_Complete_Profile_to_be_visible_on_teachers_listing_page'),
            'requriedRescheduleMesssage' => Label::getLabel('Lbl_Reschedule_Reason_Is_Requried'),
            'language' => Label::getLabel('Lbl_Language'),
            'myTimeZoneLabel' => Label::getLabel('Lbl_My_Current_Time'),
            'timezoneString' => Label::getLabel('LBL_TIMEZONE_STRING'),
            'lessonMints' => Label::getLabel('LBL_%s_Mins/Lesson'),
            'userFilterLabel' => Label::getLabel('LBL_User'),
            'confirmDeleteLessonPlanText' => Label::getLabel('LBL_DELETE_LESSON_PLAN_CONFIRM_TEXT'),
            'today' => Label::getLabel('LBL_Today')
        ];
        $this->set('jsVariables', $jsVariables);
    }

    public function index($classType = '')
    {
        $frmSearch = $this->getOrderPurchasedLessonsForm();
        $data = FatApp::getPostedData();
        if ($data) {
            $frmSearch->fill($data);
        }
        $this->set('frmSearch', $frmSearch);
        $this->set('setMonthAndWeekName', true);
        $this->_template->addJs('js/learnerLessonCommon.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs(['js/jquery.barrating.min.js']);
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->render();
    }

    private function getPurchasedLessonsSearchForm($status = "all", $orderId = null)
    {
        $frm = new Form('purchasedLessonsSearchForm');
        $isFreeTrialOption = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getYesNoArr($this->adminLangId);
        $lessonStatusOption = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + ScheduledLesson::getStatusArr();
        $frm->addTextBox(Label::getLabel('LBL_Teacher', $this->adminLangId), 'teacher', '', ['id' => 'teacher', 'autocomplete' => 'off']);
        $frm->addTextBox(Label::getLabel('LBL_Learner', $this->adminLangId), 'learner', '', ['id' => 'learner', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_Free_Trial', $this->adminLangId), 'op_lpackage_is_free_trial', $isFreeTrialOption, -1, [], '');
        $statusFld = $frm->addSelectBox(Label::getLabel('Lesson_Status', $this->adminLangId), 'slesson_status', $lessonStatusOption, -1, [], '');
        if ($status != "all" && array_key_exists($status, $lessonStatusOption)) {
            $statusFld->value = $status;
        }
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'slesson_teacher_id', '');
        $frm->addHiddenField('', 'sldetail_learner_id', '');
        $orderIdFld = $frm->addHiddenField('', 'sldetail_order_id', '');
        if (!empty($orderId)) {
            $orderIdFld->value = $orderId;
        }
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function purchasedLessonsSearch()
    {
        $PreSymposiumDinnerInformationSection = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $PreSymposiumDinnerInformationSection->addOrder('pre_symposium_dinner_ticket_plan_id', 'DESC');
        $PreSymposiumDinnerInformation_categories = $PreSymposiumDinnerInformationSection->getResultSet();
        $PreSymposiumDinnerInformationCategoriesList = FatApp::getDb()->fetchAll($PreSymposiumDinnerInformation_categories);
        foreach ($PreSymposiumDinnerInformationCategoriesList as $key => $value) {
            $eventsDetails = new SearchBase('tbl_pre_symposium_dinner');
            $eventsDetails->addCondition('pre_symposium_dinner_id', ' = ', $value['event_user_pre_symposium_dinner_id']);
            $eventsDetailsInformation = $eventsDetails->getResultSet();
            $eventsDeatailsCategoriesListing = FatApp::getDb()->fetch($eventsDetailsInformation);
            $value['registration_plan_title'] = $eventsDeatailsCategoriesListing['pre_symposium_dinner_plan_title'];
            $value['registration_starting_date'] = $eventsDeatailsCategoriesListing['pre_symposium_dinner_starting_date'];
            $value['registration_ending_date'] = $eventsDeatailsCategoriesListing['pre_symposium_dinner_ending_date'];
            $userObj = new SearchBase('tbl_event_user_credentials');
            $userObj->addCondition('credential_user_id', ' = ', $value['event_user_id']);
            $userSet = $userObj->getResultSet();
            $userData = FatApp::getDb()->fetch($userSet);
            $userObjs = new SearchBase('tbl_event_users');
            $userObjs->addCondition('user_id', ' = ', $value['event_user_id']);
            $userSets = $userObjs->getResultSet();
            $userDatas = FatApp::getDb()->fetch($userSets);
            $value['user_email'] = $userData['credential_email'];
            $value['user_username'] = $userData['credential_username'];
            $value['user_first_name'] = $userDatas['user_first_name'];
            $value['user_last_name'] = $userDatas['user_last_name'];
            $value['user_phone_code'] = $userDatas['user_phone_code'];
            $value['user_phone'] = $userDatas['user_phone'];
            $PreSymposiumDinnerInformationCategoriesList[$key] = $value;
        }
        // echo "<pre>";
        // print_r($PreSymposiumDinnerInformationCategoriesList);
        $this->set("PreSymposiumDinnerInformationCategoriesList", $PreSymposiumDinnerInformationCategoriesList);
        $this->_template->render(false, false, null, false, false);
    }

    public function search()
    {
        $frmSearch = $this->getOrderPurchasedLessonsForm();
        $post = $frmSearch->getFormDataFromArray(FatApp::getPostedData());
        $srch = new OrderSearch(false, false);
        if (isset($post['op_teacher_id']) and $post['op_teacher_id'] > 0) {
            $user_is_teacher = FatUtility::int($post['op_teacher_id']);
            $srch->addCondition('op_teacher_id', '=', $user_is_teacher);
        }
        if (isset($post['order_user_id']) and $post['order_user_id'] > 0) {
            $user_is_learner = FatUtility::int($post['order_user_id']);
            $srch->addCondition('order_user_id', '=', $user_is_learner);
        }
        if (isset($post['order_is_paid']) and $post['order_is_paid'] > -2) {
            $is_paid = FatUtility::int($post['order_is_paid']);
            $srch->addCondition('order_is_paid', '=', $is_paid);
        }
        if (isset($post['op_lpackage_is_free_trial']) and $post['op_lpackage_is_free_trial'] > -1) {
            $is_trial = FatUtility::int($post['op_lpackage_is_free_trial']);
            $srch->addCondition('op_lpackage_is_free_trial', '=', $is_trial);
        }
        if (!empty($post['class_type'])) {
            if ($post['class_type'] == applicationConstants::CLASS_TYPE_GROUP) {
                $srch->addCondition('grpcls_id', '>', 0);
            } else {
                $srch->addDirectCondition('grpcls_id IS NULL');
            }
        }
        $srch->addOrder('order_date_added', 'desc');
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false, null, false, false);
    }

    public function viewSchedules($status = "all", $orderId = null)
    {
        if (empty($status)) {
            $status = "all";
        }
        $searchForm = $this->getPurchasedLessonsSearchForm($status, $orderId);
        $newClass = new SearchBase('tbl_events_report_comments');
        $new_rs_lesson = $newClass->getResultSet();
        $new_class_Data = FatApp::getDb()->fetchAll($new_rs_lesson);
        $this->set('new_class_Data', $new_class_Data);
        $this->set('searchForm', $searchForm);
        $this->set('setMonthAndWeekName', true);
        $this->_template->addJs('js/learnerLessonCommon.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs(['js/jquery.barrating.min.js']);
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->addCss('css/common-ltr.css');
        $this->_template->render();
    }
}
