<?php

class SponsorshipInformationController extends AdminBaseController
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
        $SponsorshipeventplanData = new SearchBase('tbl_event_user_become_sponser');
        $SponsorshipeventplanData->addCondition('event_user_payment_status', '=', 1);
        $SponsorshipeventplanResult = FatApp::getDb()->fetchAll($SponsorshipeventplanData->getResultSet());
        $eventList = array();
        $events = array();
        $plan = '';
        $index = 0;
        $SponserEvent = array();
        foreach ($SponsorshipeventplanResult as $key => $value) {
            $plan_name = '';
            $plan_qty = '';
            $sponserId = unserialize($value['event_user_sponsrship_id']);
            $sponser_qty = unserialize($value['event_user_sponsership_qty']);
            $qty_json = json_decode($sponser_qty);
            $allValues = array_values((array)$qty_json);
            $qty_index = 0;
            $qty_plan = 0;
            $json = json_decode($sponserId);
            $allKeysOfEmployee = array_keys((array)$json);
            $total_qty = 0;
            $SponEventsSelectionData = new SearchBase('tbl_events_sponsorship_categories');
            $SponEventsSelectionData->addCondition('events_sponsorship_categories_id', '=', $value['event_user_sponser_selected_plan']);
            $SponSorshipEventsSelectionplanResult = FatApp::getDb()->fetch($SponEventsSelectionData->getResultSet());
            $events['event_name'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'];
            $events['event_ending_time'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_ending_date'];
            if (!empty($SponSorshipEventsSelectionplanResult)) {
                foreach ($allKeysOfEmployee as $tempKey) {
                    $sponserPlan = new SearchBase('tbl_sponsorshipcategories');
                    $sponserPlan->addCondition('sponsorshipcategories_id', '=', $tempKey);
                    $sponserPlanResult = FatApp::getDb()->fetch($sponserPlan->getResultSet());
                    $plan_name = $plan_name . " " . $sponserPlanResult['sponsorshipcategories_name'] . " - " . $allValues[$qty_index] . ",";
                    $plan_qty = $plan_qty . " " . $allValues[$qty_index] . ",";
                    $qty_plan = $allValues[$qty_index];
                    $total_qty = $total_qty + $qty_plan;
                    if (array_key_exists($SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'], $events)) {
                        $plans = $events['plan'];
                        unset($events['plan']);
                        $plans = $plans . ',' . $sponserPlanResult['sponsorshipcategories_name'];
                        $unique = implode(',', array_unique(str_word_count($plans, 1)));
                        $events['plan'] = $plan_name;
                        $total = $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] + $qty_plan;;
                        unset($events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']]);
                        $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $total;
                    } else {
                        $plan = $sponserPlanResult['sponsorshipcategories_name'];
                        $unique = implode(',', array_unique(str_word_count($plan, 1)));
                        $events['plan'] = $plan_name;
                        $events['index'] = $index;
                        $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $qty_plan;
                    }
                    if (array_key_exists($sponserPlanResult['sponsorshipcategories_name'], $eventList)) {
                        $total = $eventList[$sponserPlanResult['sponsorshipcategories_name']] + $qty_plan;
                        unset($eventList[$sponserPlanResult['sponsorshipcategories_name']]);
                        $eventList[$sponserPlanResult['sponsorshipcategories_name']] = $total;
                    } else {
                        $plan = $sponserPlanResult['sponsorshipcategories_name'];
                        $eventList[$sponserPlanResult['sponsorshipcategories_name']] = $qty_plan;
                    }
                    $qty_index++;
                }
                $OrderProductData = new SearchBase('tbl_order_products');
                $OrderProductData->addCondition('op_grpcls_id', '=', $value['event_user_become_id']);
                $OrderProductData->addOrder('op_id', 'DESC');
                $OrderProductsResult = FatApp::getDb()->fetch($OrderProductData->getResultSet());

                $OrderData = new SearchBase('tbl_orders');
                $OrderData->addCondition('order_id', '=', $OrderProductsResult['op_order_id']);
                $OrderData->addCondition('order_is_paid', '=', 1);
                $OrderResult = FatApp::getDb()->fetch($OrderData->getResultSet());

                $userObjs = new SearchBase('tbl_event_users');
                $userObjs->addCondition('user_id', ' = ', $OrderProductsResult['op_teacher_id']);
                $userSets = $userObjs->getResultSet();
                $userDatas = FatApp::getDb()->fetch($userSets);
                $value['user_email'] = $userData['credential_email'];
                $value['user_username'] = $userData['credential_username'];
                $value['user_first_name'] = $userDatas['user_first_name'];
                $value['user_last_name'] = $userDatas['user_last_name'];
                $value['user_phone_code'] = $userDatas['user_phone_code'];
                $value['user_phone'] = $userDatas['user_phone'];
                $SponsorishipInformationCategoriesList[$key] = $value;
                $events['sponsorship'] = $SponsorishipInformationCategoriesList;
                $events['order_data'] = $OrderProductsResult;
                $events['coupon_code'] = $OrderResult['order_discount_coupon_code'];
                $value['total'] = $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']];
                $value['event_name'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'];
                $value['event_ending_time'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_ending_date'];
                $value['sponser_plan'] = $plan_name;
                $value['sponser_plan_qty'] = $plan_qty;
                $SponsorshipeventplanResult[$key] = $value;
                $SponserEvent[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $events;
            }
            $index++;
        }
        $sponserEventData = $SponserEvent;
        // echo "<pre>";
        // print_r($sponserEventData);
        $this->set("sponserEventData", $sponserEventData);
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
