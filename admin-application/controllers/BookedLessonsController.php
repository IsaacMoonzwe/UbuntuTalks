<?php

class BookedLessonsController extends AdminBaseController
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


    public function viewCalendar()
    {
        MyDate::setUserTimeZone();
        $user_timezone = MyDate::getUserTimeZone();
        $nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
        $this->set('user_timezone', $user_timezone);
        $this->set('nowDate', $nowDate);
        $this->_template->render(false, false);
    }

    public function calendarJsonData()
    {
        $startDate = Fatapp::getPostedData('start', FatUtility::VAR_STRING, '');
        $endDate = Fatapp::getPostedData('end', FatUtility::VAR_STRING, '');
        $userTimezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        if (empty($startDate) || empty($endDate)) {
            $monthStartAndEndDate = MyDate::getMonthStartAndEndDate(new DateTime());
            $startDate = $monthStartAndEndDate['monthStart'];
            $endDate = $monthStartAndEndDate['monthEnd'];
        } else {
            $startDate = MyDate::changeDateTimezone($startDate, $userTimezone, $systemTimeZone);
            $endDate = MyDate::changeDateTimezone($endDate, $userTimezone, $systemTimeZone);
        }
        $cssClassNamesArr = ScheduledLesson::getStatusArr();
        $srch = new ScheduledLessonSearch();
        $srch->joinGroupClass($this->adminLangId);
        $srch->addMultipleFields([
            'slns.slesson_teacher_id',
            'sld.sldetail_learner_id',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'ut.user_first_name',
            'ut.user_id',
            'ut.user_url_name',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'concat(slns.slesson_date," ",slns.slesson_start_time) AS slesson_date_time',
        ]);
        $srch->addCondition('sld.sldetail_learner_id', ' = ', UserAuthentication::getLoggedUserId());
        $srch->addCondition('slns.slesson_status', 'NOT IN', [ScheduledLesson::STATUS_CANCELLED, ScheduledLesson::STATUS_NEED_SCHEDULING]);
        $srch->addCondition('CONCAT(slns.`slesson_date`, " ", slns.`slesson_start_time` )', '< ', $endDate);
        $srch->addCondition('CONCAT(slns.`slesson_end_date`, " ", slns.`slesson_end_time` )', ' > ', $startDate);
        $srch->joinTeacher();
        $srch->addGroupBy('slesson_id');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $jsonArr = [];
        if (!empty($rows)) {
            $user_timezone = MyDate::getUserTimeZone();
            foreach ($rows as $k => $row) {
                $slesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['slesson_date'] . ' ' . $row['slesson_start_time'], true, $user_timezone);
                $slesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['slesson_end_date'] . ' ' . $row['slesson_end_time'], true, $user_timezone);
                $jsonArr[$k] = [
                    'title' => $row['grpcls_title'] ? $row['grpcls_title'] : $row['user_first_name'],
                    'date' => $slesson_start_time,
                    'start' => $slesson_start_time,
                    'end' => $slesson_end_time,
                    'lid' => $row['sldetail_learner_id'],
                    'liFname' => substr($row['user_first_name'], 0, 1),
                    'classType' => $row['slesson_status'],
                    'className' => $cssClassNamesArr[$row['slesson_status']],
                ];
                if (true == User::isProfilePicUploaded($row['user_id'])) {
                    $teacherUrl =  CommonHelper::generateUrl('teachers', 'view', [CommonHelper::htmlEntitiesDecode($row['user_url_name'])]);
                    $img = CommonHelper::generateFullUrl('Image', 'User', [$row['user_id']]);
                    $jsonArr[$k]['imgTag'] = '<a href="' . $teacherUrl . '"><img src="' . $img . '" /></a>';
                } else {
                    $jsonArr[$k]['imgTag'] = '';
                }
            }
        }
        echo FatUtility::convertToJson($jsonArr);
    }

    public function view(string $orderId = '')
    {
        if (empty($orderId)) {
            FatApp::redirectUser(CommonHelper::generateUrl('PurchasedLessons'));
        }
        $orderSearch = new OrderSearch();
        $orderSearch->addMultipleFields([
            'order_id', 'order_user_id', 'order_date_added', 'order_is_paid', 'order_net_amount',
            'order_wallet_amount_charge', 'order_discount_total', 'order_date_added', 'op_invoice_number',
            'slesson_date', 'slesson_start_time', 'slesson_end_date', 'slesson_end_time', 'op_teacher_id',
            'op_grpcls_id', 'op_qty', 'op_unit_price', 'op_commission_charged', 'op_commission_percentage',
            'op_refund_qty', 'op_total_refund_amount', 'op_lpackage_is_free_trial', 'op_lesson_duration',
            'CONCAT(u.user_first_name, " ", u.user_last_name) as userFullName', 'CONCAT(t.user_first_name, " ", t.user_last_name) as teacherFullName',
            't.user_timezone as teacherTimezone', 'u.user_timezone as userTimezone', 'tcred.credential_email as teacherEmail',
            'cred.credential_email as userEmail', 'grpcls_title', 'grpcls_status',
            'IFNULL(tlanguage_name, tlanguage_identifier) as teachLang',
            'IFNULL(uCountryLang.country_name, " ") as uCountryName',
            'IFNULL(tCountryLang.country_name, " ") as tCountryName',
        ]);
        $orderSearch->joinOrderProduct($this->adminLangId);
        $orderSearch->joinUser();
        $orderSearch->joinTable(Country::DB_TBL, 'LEFT JOIN', 'uCountry.country_id = u.user_country_id', 'uCountry');
        $orderSearch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'uCountry.country_id = uCountryLang.countrylang_country_id', 'uCountryLang');
        $orderSearch->joinUserCredentials();
        $orderSearch->joinTeacherLessonLanguage($this->adminLangId);
        $orderSearch->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 't.user_id = tcred.credential_user_id', 'tcred');
        $orderSearch->joinTable(Country::DB_TBL, 'LEFT JOIN', 'tCountry.country_id = t.user_country_id', 'tCountry');
        $orderSearch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'tCountry.country_id = tCountryLang.countrylang_country_id', 'tCountryLang');
        $orderSearch->joinTable(TeacherGroupClasses::DB_TBL, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = op_grpcls_id', 'grpcls');
        $orderSearch->addCondition('order_id', '=', $orderId);
        $orderSearch->addCondition('order_type', '=', Order::TYPE_LESSON_BOOKING);
        $resultSet = $orderSearch->getResultSet();
        $orderDeatils = FatApp::getDb()->fetch($resultSet);
        if (empty($orderDeatils)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST.'));
            FatApp::redirectUser(CommonHelper::generateUrl('PurchasedLessons'));
        }
        $order = new Order($orderId);
        $orderPayments = $order->getOrderPayments(["order_id" => $orderId]);
        $form = $this->getPaymentForm($orderId);
        $this->set('yesNoArr', applicationConstants::getYesNoArr($this->adminLangId));
        $this->set('order', $orderDeatils);
        $this->set('orderPayments', $orderPayments);
        $this->set('adminLangId', $this->adminLangId);
        $this->set('form', $form);
        $this->_template->render();
    }

    private function getPaymentForm(string $orderId)
    {
        $form = new Form('frmPayment');
        $form->addHiddenField('', 'opayment_order_id', $orderId);
        $form->addTextArea(Label::getLabel('LBL_Comments', $this->adminLangId), 'opayment_comments', '')->requirements()->setRequired();
        $form->addRequiredField(Label::getLabel('LBL_Payment_Method', $this->adminLangId), 'opayment_method');
        $form->addRequiredField(Label::getLabel('LBL_Txn_ID', $this->adminLangId), 'opayment_gateway_txn_id');
        $form->addRequiredField(Label::getLabel('LBL_Amount', $this->adminLangId), 'opayment_amount')->requirements()->setFloatPositive(true);
        $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $form;
    }

    protected function getOrderPurchasedLessonsForm()
    {
        $frm = new Form('orderPurchasedLessonsSearchForm');
        $arr_options = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getYesNoArr($this->adminLangId);
        $arr_options1 = ['-2' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + Order::getPaymentStatusArr($this->adminLangId);
        $keyword = $frm->addTextBox(Label::getLabel('LBL_Teacher', $this->adminLangId), 'teacher', '', ['id' => 'teacher', 'autocomplete' => 'off']);
        $keyword = $frm->addTextBox(Label::getLabel('LBL_Learner', $this->adminLangId), 'learner', '', ['id' => 'learner', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_Free_Trial', $this->adminLangId), 'op_lpackage_is_free_trial', $arr_options, -1, [], '');
        $frm->addSelectBox(Label::getLabel('Payment Status', $this->adminLangId), 'order_is_paid', $arr_options1, -2, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Class_Type', $this->adminLangId), 'class_type', ApplicationConstants::getClassTypes($this->adminLangId));
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'order_user_id', '');
        $frm->addHiddenField('', 'op_teacher_id', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
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
        $langId = CommonHelper::getLangId();
        $srch = new TeacherSearch($langId);
        $srch->addCondition('user_is_teacher', '=', '1');
        $srch->addMultipleFields([
            'user_id',
            'CONCAT(user_first_name, " " ,user_last_name) AS teacher_name',
        ]);
        $rawData = FatApp::getDb()->fetchAll($srch->getResultSet());
        $searchFrm = $this->getPurchasedLessonsSearchForm();
        $data = $searchFrm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new ScheduledLessonSearch(false);
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacherTeachLanguage();
        $srch->addMultipleFields([
            'sldetail_id', 'slesson_id', 'slesson_grpcls_id', 'slesson_status','slesson_kids_class',
            'slesson_ended_by', 'slesson_date', 'slesson_end_date', 'slesson_ended_on',
            'slesson_start_time', 'slesson_end_time', 'slesson_teacher_join_time',
            'sldetail_learner_join_time', 'slesson_teacher_end_time', 'sldetail_learner_end_time',
            'slesson_added_on', 'op_lpackage_is_free_trial', 'order_is_paid',
            'CONCAT(ul.user_first_name, " " , ul.user_last_name) AS learner_name',
            'CONCAT(ut.user_first_name, " " , ut.user_last_name) AS teacher_name',
            'CONCAT(cred.credential_email) AS learner_email',
            'CONCAT(credut.credential_email) AS instructor_email',
            'IFNULL(tl_l.tlanguage_name, t_t_lang.tlanguage_identifier) as teacherTeachLanguageName',
        ]);
        if (isset($data['op_lpackage_is_free_trial']) && $data['op_lpackage_is_free_trial'] > -1) {
            $is_trial = FatUtility::int($data['op_lpackage_is_free_trial']);
            $srch->addCondition('op_lpackage_is_free_trial', '=', $is_trial);
        }
        if (!empty($data['slesson_teacher_id'])) {
            $teacherId = FatUtility::int($data['slesson_teacher_id']);
            $srch->addCondition('slesson_teacher_id', '=', $teacherId);
        }
        if (!empty($data['sldetail_learner_id'])) {
            $learnerId = FatUtility::int($data['sldetail_learner_id']);
            $srch->addCondition('sldetail_learner_id', '=', $learnerId);
        }
        if (!empty($data['sldetail_order_id'])) {
            $srch->addCondition('sldetail_order_id', '=', $data['sldetail_order_id']);
        }
        if ($data['slesson_status'] > 0) {
            $status = FatUtility::int($data['slesson_status']);
            switch ($status) {
                case ScheduledLesson::STATUS_ISSUE_REPORTED:
                    $srch->joinTable(ReportedIssue::DB_TBL, 'INNER JOIN', 'repiss.repiss_sldetail_id = sld.sldetail_id', 'repiss');
                    $srch->addCondition('repiss.repiss_id', '>', 0);
                    break;
                case ScheduledLesson::STATUS_UPCOMING:
                    $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>=', date('Y-m-d H:i:s'), 'AND', true);
                    $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                    break;
                case ScheduledLesson::STATUS_SCHEDULED:
                    $srch->addCondition('slns.slesson_status', '=', $status);
                    break;
                default:
                    $srch->addCondition('slns.slesson_status', '=', $status);
                    break;
            }
        }
        $srch->addOrder('slesson_id', 'desc');
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $resultSet = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($resultSet);
        $this->set("rawData", $rawData);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $data);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false, null, false, false);
    }

    public function search()
    {
        $frmSearch = $this->getOrderPurchasedLessonsForm();
        $post = $frmSearch->getFormDataFromArray(FatApp::getPostedData());
        $srch = new OrderSearch(false, false);
        // $srch->addGroupBy('order_id');
        // $srch->joinOrderProduct();
        // $srch->joinUser();
        // $srch->joinUserCredentials();
        // $srch->joinTeacherLessonLanguage($this->adminLangId);
        // $srch->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'op_teacher_id = tCredentials.credential_user_id', 'tCredentials');
        // $srch->joinGroupClass($this->adminLangId);
        // $srch->addMultipleFields(['order_id', 'grpcls_id', 'op_qty',
        //     'grpcls.grpcls_title', 'order_user_id', 'op_teacher_id',
        //     'op_lpackage_is_free_trial', 'order_is_paid', 'order_net_amount',
        //     'CONCAT(u.user_first_name, " " , u.user_last_name) AS learner_username',
        //     'CONCAT(t.user_first_name, " " , t.user_last_name) AS teacher_username',
        //     'tCredentials.credential_email as teacherEmail',
        //     'cred.credential_email as userEmail', 'order_currency_code',
        //     'COALESCE(NULLIF(sl.tlanguage_name, ""), tlang.tlanguage_identifier) AS language',
        // ]);
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

    public function setUpLessonSchedule()
    {
        $post = FatApp::getPostedData();
        $db = FatApp::getDb();
        if (empty($post)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $lDetailId = FatApp::getPostedData('lDetailId', FatUtility::VAR_INT, 0);
        if (1 > $lDetailId) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $lessonStsLog = new LessonStatusLog($lDetailId);
        $isRescheduleRequest = FatApp::getPostedData('isRescheduleRequest', FatUtility::VAR_INT, 0);
        $rescheduleReason = FatApp::getPostedData('rescheduleReason', FatUtility::VAR_STRING, '');
        $lessonsStatus = [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_NEED_SCHEDULING];
        $getLessonDetailObj = ScheduledLessonDetails::getLessonDetailSearchObj();
        $getLessonDetailObj->joinOrderProduct();
        $getLessonDetailObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'uts.us_user_id = ut.user_id', 'uts');
        $getLessonDetailObj->addCondition('sldetail_id', '=', $lDetailId);
        // $getLessonDetailObj->addCondition('slesson_grpcls_id', '=', 0);
        // $getLessonDetailObj->addCondition('sldetail_learner_id', '=', UserAuthentication::getLoggedUserId());
        $getLessonDetailObj->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $getLessonDetailObj->addCondition('sldetail_learner_status', 'IN', $lessonsStatus);
        $getLessonDetailObj->addMultipleFields([
            'uts.us_booking_before as teacherBookingBefore',
            'ut.user_country_id as teacherCountryId',
            'ut.user_first_name as teacherFirstName',
            'op_lpackage_is_free_trial',
            'tcred.credential_email as teacherEmailId',
            'lcred.credential_email as learnerEmailId',
            'ut.user_timezone as teacherTimeZone',
            'slesson_date',
            'op_lesson_duration',
            'slesson_start_time',
        ]);
        $getResultSet = $getLessonDetailObj->getResultSet();
        $lessonDetail = $db->fetch($getResultSet);
        if (empty($lessonDetail)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $teacher_id = $lessonDetail['teacherId'];
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $startTime = MyDate::changeDateTimezone($post['startTime'], $user_timezone, $systemTimeZone);
        $endTime = MyDate::changeDateTimezone($post['endTime'], $user_timezone, $systemTimeZone);
        $teacherBookingBefore = FatUtility::int($lessonDetail['teacherBookingBefore']);
        $validDate = date('Y-m-d H:i:s', strtotime('+0 hours', strtotime(date('Y-m-d H:i:s'))));
        $validDateTimeStamp = strtotime($validDate);
        $SelectedDateTimeStamp = strtotime($startTime); //== always should be greater then current date
        $endDateTimeStamp = strtotime($endTime);
        $difference = $SelectedDateTimeStamp - $validDateTimeStamp; //== Difference should be always greaten then 0
        if ($difference < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_TEACHER_DISABLE_THE_BOOKING_BEFORE'));
        }

        $lessonDuration = $lessonDetail['op_lesson_duration'];
        $lessonDuration = $lessonDuration * 60;
        $timeDuration = abs(strtotime($endTime) - strtotime($startTime));
        if ($timeDuration != $lessonDuration) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }

        $userIds = [$teacher_id, UserAuthentication::getLoggedUserId()];
        $scheduledLessonSearchObj = new ScheduledLessonSearch();
        $scheduledLessonSearchObj->checkUserLessonBooking($userIds, $startTime, $endTime);
        $scheduledLessonSearchObj->setPageSize(1);
        $getResultSet = $scheduledLessonSearchObj->getResultSet();
        $scheduledLessonData = $db->fetchAll($getResultSet);
        if (!empty($scheduledLessonData)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_REQUESTED_SLOT_IS_NOT_AVAILABLE'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $sLessonArr = [
            'slesson_date' => date('Y-m-d', $SelectedDateTimeStamp),
            'slesson_end_date' => date('Y-m-d', $endDateTimeStamp),
            'slesson_start_time' => date('H:i:s', $SelectedDateTimeStamp),
            'slesson_end_time' => date('H:i:s', $endDateTimeStamp),
            'slesson_status' => ScheduledLesson::STATUS_SCHEDULED,
            'slesson_teacher_join_time' => '',
            'slesson_teacher_end_time' => '',
        ];
        $sLessonObj = new ScheduledLesson($lessonDetail['slesson_id']);
        $sLessonObj->assignValues($sLessonArr);
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        $sLessonDetailObj = new ScheduledLessonDetails($lessonDetail['sldetail_id']);
        $sLessonDetailObj->assignValues([
            'sldetail_learner_status' => ScheduledLesson::STATUS_SCHEDULED,
            'sldetail_learner_join_time' => '',
            'sldetail_learner_end_time' => '',
        ]);
        if (!$sLessonDetailObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonDetailObj->getError());
        }
        if ($cls = TeacherGroupClassesSearch::getTeacherClassByTime($teacher_id, date('Y-m-d H:i:s', $SelectedDateTimeStamp), date('Y-m-d H:i:s', $endDateTimeStamp))) {
            $grpclsId = $cls['grpcls_id'];
            $grpclsObj = new TeacherGroupClasses($grpclsId);

            $grpclsObj->cancelClass();
        }
        $action = Label::getLabel('VERB_Scheduled', $this->adminLangId);
        if (ScheduledLesson::STATUS_SCHEDULED == $lessonDetail['sldetail_learner_status'] && $rescheduleReason) {
            $lessonResLogArr = [
                'lesreschlog_slesson_id' => $lessonDetail['slesson_id'],
                'lesreschlog_reschedule_by' => UserAuthentication::getLoggedUserId(),
                'lesreschlog_user_type' => User::USER_TYPE_LEANER,
                'lesreschlog_comment' => $rescheduleReason,
            ];
            $lessonResLogObj = new LessonRescheduleLog();
            $lessonResLogObj->assignValues($lessonResLogArr);
            if (!$lessonResLogObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($lessonResLogObj->getError());
            }
            $lessonStsLog->addLog(ScheduledLesson::STATUS_SCHEDULED, User::USER_TYPE_LEANER, UserAuthentication::getLoggedUserId(), $rescheduleReason);
            // $action = Label::getLabel('VERB_Rescheduled', $this->siteLangId);
        }
        $db->commitTransaction();
        $vars = [
            '{learner_name}' => $lessonDetail['learnerFullName'],
            '{teacher_name}' => $lessonDetail['teacherFullName'],
            '{lesson_name}' => (applicationConstants::NO == $lessonDetail['op_lpackage_is_free_trial']) ? $lessonDetail['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial', $this->siteLangId),
            '{lesson_date}' => MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', date('Y-m-d H:i:s', $SelectedDateTimeStamp), false, $lessonDetail['teacherTimeZone']),
            '{lesson_start_time}' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', date('Y-m-d H:i:s', $SelectedDateTimeStamp), true, $lessonDetail['teacherTimeZone']),
            '{lesson_end_time}' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', date('Y-m-d H:i:s', $endDateTimeStamp), true, $lessonDetail['teacherTimeZone']),
            '{learner_comment}' => $rescheduleReason,
            '{action}' => strtolower($action),
        ];
        EmailHandler::sendMailTpl($lessonDetail['teacherEmailId'], 'admin_schedule_email', $this->siteLangId, $vars);
        EmailHandler::sendMailTpl($lessonDetail['learnerEmailId'], 'admin_schedule_email', $this->siteLangId, $vars);

        $userNotification = new UserNotifications($lessonDetail['teacherId']);
        $userNotification->sendSchLessonByLearnerNotification($lessonDetail['slesson_id']);
        $msg = 'LBL_LESSON_SCHEDULED_SUCCESSFULLY';
        if ($isRescheduleRequest) {
            $msg = 'LBL_LESSON_RESCHEDULED_SUCCESSFULLY';
        }
        FatUtility::dieJsonSuccess(Label::getLabel($msg));
    }

    public function isSlotTaken()
    {
        $post = FatApp::getPostedData();
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $date = FatApp::getPostedData('date', FatUtility::VAR_STRING, '');
        $startTime = FatApp::getPostedData('startTime', FatUtility::VAR_STRING, '');
        $endTime = FatApp::getPostedData('endTime', FatUtility::VAR_STRING, '');
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        if (empty($startTime) || empty($endTime) || empty($teacherId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $startDateTime = MyDate::changeDateTimezone($post['startTime'], $user_timezone, $systemTimeZone);
        $endDateTime = MyDate::changeDateTimezone($post['endTime'], $user_timezone, $systemTimeZone);
        $db = FatApp::getDb();
        $userIds = [$teacherId, UserAuthentication::getLoggedUserId()];
        $scheduledLessonSearchObj = new ScheduledLessonSearch();
        $scheduledLessonSearchObj->checkUserLessonBooking($userIds, $startDateTime, $endDateTime);
        $scheduledLessonSearchObj->setPageSize(1);
        $getResultSet = $scheduledLessonSearchObj->getResultSet();
        $scheduledLessonData = $db->fetchAll($getResultSet);
        $this->set('count', count($scheduledLessonData));
        $this->_template->render(false, false, 'json-success.php');
    }
    
    public function reportDetail($slesson_id){
        $ReportIssueSection = new SearchBase('tbl_report_comments');
        $ReportIssue_categories = $ReportIssueSection->getResultSet();
        $ReportIssueCategoriesList = FatApp::getDb()->fetchAll($ReportIssue_categories);
        $this->set('ReportIssueCategoriesList', $ReportIssueCategoriesList);
        $this->_template->render(false, false);
    }

    public function viewDetail($slesson_id)
    {
        $slesson_id = FatUtility::int($slesson_id);
        if (1 > $slesson_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $srch = new ScheduledLessonSearch(false);
        $srch->joinKidsClass($this->adminLangId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinLearnerCountry($this->adminLangId);
        $srch->addCondition('slns.slesson_id', ' = ', $slesson_id);
        $srch->joinTeacherSettings();
        $srch->joinLessonLanguage($this->adminLangId);
        $srch->addMultipleFields([
            'slns.slesson_id',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'grpcls.grpcls_description',
            'sld.sldetail_learner_id as learnerId',
            'ul.user_first_name as learnerFname',
            'ul.user_last_name as learnerLname',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'IFNULL(learnercountry_lang.country_name, learnercountry.country_code) as learnerCountryName',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'IFNULL(sl.tlanguage_name, tlang.tlanguage_identifier) as teacherTeachLanguageName',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration',
            'ut.user_first_name as instructorFname',
            'ut.user_last_name as instructorLname',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as instructorFullName',
            'CONCAT(cred.credential_email) AS learner_email',
        ]);
        $lessonRow = FatApp::getDb()->fetch($srch->getResultSet());

        if (empty($lessonRow)) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $this->set("lessonRow", $lessonRow);
        $this->set("statusArr", ScheduledLesson::getStatusArr());
        $this->_template->render(false, false);
    }

    public function getRescheduleFrm()
    {
        $frm = new Form('rescheduleFrm');
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'reschedule_lesson_msg', '');
        $fld->requirement->setRequired(true);
        $fld = $frm->addHiddenField('', 'sldetail_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Send'));
        return $frm;
    }

    public function requestReschedule($lDetailId)
    {
        $lDetailId = FatUtility::int($lDetailId);
        $db = FatApp::getDb();
        $getLessonDetailObj = ScheduledLessonDetails::getLessonDetailSearchObj();
        $getLessonDetailObj->joinOrderProduct();
        $getLessonDetailObj->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'uts.us_user_id = ut.user_id', 'uts');
        $getLessonDetailObj->addCondition('sldetail_id', '=', $lDetailId);
        $getLessonDetailObj->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $getLessonDetailObj->addMultipleFields([
            'uts.us_booking_before as teacherBookingBefore',
            'ut.user_country_id as teacherCountryId',
            'ut.user_first_name as teacherFirstName',
            'op_lpackage_is_free_trial',
            'op_lesson_duration',
        ]);
        $getLessonDetailObj->addCondition('sldetail_learner_status', '=', 1);
        $getResultSet = $getLessonDetailObj->getResultSet();
        $data = $db->fetch($getResultSet);
        if (empty($data)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $hoursDiff = MyDate::hoursDiff($data['slesson_date'] . ' ' . $data['slesson_start_time']);
        // if ($hoursDiff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) {
        //     Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
        //     FatUtility::dieWithError(Message::getHtml());
        // }
        $teacherBookingBefore = 0;
        if (!empty($data['teacherBookingBefore'])) {
            $teacherBookingBefore = $data['teacherBookingBefore'];
        }
        $user_timezone = MyDate::getUserTimeZone();
        $nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
        $userRow = [
            'user_full_name' => $data['teacherFullName'],
            'user_first_name' => $data['teacherFirstName'],
            'user_country_id' => $data['teacherCountryId'],
        ];
        $action = (applicationConstants::YES == $data['op_lpackage_is_free_trial']) ? 'free_trial' : '';
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $frm = $this->getRescheduleFrm();
        $frm->fill(['sldetail_id' => $lDetailId]);
        $this->set('rescheduleRequestfrm', $frm);
        $this->set('teacherBookingBefore', $teacherBookingBefore);
        $this->set('user_timezone', $user_timezone);
        $this->set('nowDate', $nowDate);
        $this->set('userRow', $userRow);
        $this->set('isRescheduleRequest', true);
        $this->set('action', $action);
        $this->set('teacher_id', $data['teacherId']);
        $this->set('lessonId', $data['slesson_id']);
        $this->set('lessonRow', $data);
        $this->set('lDetailId', $lDetailId);
        $this->set('cssClassArr', $cssClassNamesArr);
        $this->_template->render(false, false, 'purchased-lessons/view-booking-calendar.php');
    }

    private function searchLessons($post = [], $getCancelledOrder = false, $addLessonDateOrder = true)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinGroupClass($this->adminLangId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinTeacherCountry($this->adminLangId);
        $orderIsPaidCondition = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        if ($getCancelledOrder) {
            $orderIsPaidCondition->attachCondition('order_is_paid', '=', Order::ORDER_IS_CANCELLED, 'OR');
        }
        $srch->addCondition('sldetail_learner_id', '=', UserAuthentication::getLoggedUserId());
        $srch->joinTeacherSettings();
        if ($addLessonDateOrder) {
            $srch->addOrder('slesson_date', 'ASC');
        }
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'sld.sldetail_id',
            'slns.slesson_id',
            'slns.slesson_grpcls_id',
            'slns.slesson_slanguage_id',
            'slns.slesson_has_issue',
            'order_is_paid',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'sldetail_learner_id as learnerId',
            'slns.slesson_teacher_id as teacherId',
            'ut.user_first_name as teacherFname',
            'ut.user_last_name as teacherLname',
            'ut.user_url_name',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'IFNULL(teachercountry_lang.country_name, teachercountry.country_code) as teacherCountryName',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'sld.sldetail_learner_status',
            'sld.sldetail_is_teacher_paid',
            '"-" as teacherTeachLanguageName',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration',
        ]);
        if (isset($post) && !empty($post['keyword'])) {
            $keywordsArr = array_unique(array_filter(explode(' ', $post['keyword'])));
            foreach ($keywordsArr as $keyword) {
                $cnd = $srch->addCondition('ut.user_first_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ut.user_last_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('sldetail_order_id', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('grpcls_title', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('grpclslang_grpcls_title', 'like', '%' . $keyword . '%');
            }
        }
        if (isset($post) && !empty($post['status'])) {
            switch ($post['status']) {
                case ScheduledLesson::STATUS_ISSUE_REPORTED:
                    $srch->addCondition('repiss_id', '>', 0);
                    break;
                case ScheduledLesson::STATUS_UPCOMING:
                    $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>=', date('Y-m-d H:i:s'), 'AND', true);
                    $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                    $srch->addCondition('sld.sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                    break;
                case ScheduledLesson::STATUS_RESCHEDULED:
                    break;
                default:
                    $srch->addCondition('slns.slesson_status', '=', $post['status']);
                    $srch->addCondition('sld.sldetail_learner_status', '=', $post['status']);
                    break;
            }
        }
        return $srch;
    }

    public function viewBookingCalendar($lDetailId)
    {
        $lDetailId = FatUtility::int($lDetailId);
        if (1 > $lDetailId) {
            Message::addError(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $srch = $this->searchLessons();
        $srch->joinTeacherCredentials();
        $srch->joinLearnerCredentials();
        $srch->joinTeacherSettings();
        $srch->doNotCalculateRecords();
        $srch->addCondition('sldetail_id', '=', $lDetailId);
        // $srch->addCondition('slesson_grpcls_id', '=', 0);
        $srch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        $srch->addFld(['us_booking_before as teacherBookingBefore', 'ut.user_country_id as teacherCountryId']);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (!$lessonRow || $lessonRow['learnerId'] != UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $teacher_id = $lessonRow['teacherId'];
        $userRow = User::getAttributesById($teacher_id, ['user_first_name', 'CONCAT(user_first_name," ",user_last_name) AS user_full_name', 'user_country_id']);
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        MyDate::setUserTimeZone();
        $user_timezone = MyDate::getUserTimeZone();
        $nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
        $teacherBookingBefore = UserSetting::getUserSettings($teacher_id)['us_booking_before'];
        if ('' == $teacherBookingBefore) {
            $teacherBookingBefore = 0;
        }
        $this->set('teacherBookingBefore', $teacherBookingBefore);
        $this->set('user_timezone', $user_timezone);
        $this->set('nowDate', $nowDate);
        $this->set('userRow', $userRow);
        $this->set('lessonRow', $lessonRow);
        $this->set('action', FatApp::getPostedData('action'));
        $this->set('teacher_id', $teacher_id);
        $this->set('lessonId', $lessonRow['slesson_id']);
        $this->set('lDetailId', $lDetailId);
        $this->set('cssClassArr', $cssClassNamesArr);
        $this->_template->render(false, false);
    }
    public function updateAssignClass()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError($this->unAuthorizeAccess);
        }
        $sldetailId = FatApp::getPostedData('sldetail_id', FatUtility::VAR_INT, 0);
        $teacher_id = FatApp::getPostedData('teachers_id', FatUtility::VAR_INT, 0); //20 email yv@gmail.com
        $status = ScheduledLesson::STATUS_CANCELLED;
        $statusArr = ScheduledLesson::getStatusArr();
        unset($statusArr[ScheduledLesson::STATUS_RESCHEDULED]);
        if (1 > $sldetailId || !array_key_exists($status, $statusArr)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $srch = new ScheduledLessonSearch(false);
        $srch->addCondition('sldetail_id', '=', $sldetailId);
        $lessonRow = FatApp::getDb()->fetch($srch->getResultSet());
        $newLesson = $lessonRow;
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $slesson_id = $lessonRow['slesson_id'];

        $db = FatApp::getDb();
        $db->startTransaction();
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $sLessonObj->loadFromDb();
        /**
         * @todo Wallet transactions
         * 
         * Update lesson detail statuses
         */
        // if ($status == ScheduledLesson::STATUS_COMPLETED) {
        //     $sLessonObj->setFldValue('slesson_ended_on', date('Y-m-d H:i:s'));
        // }

        if ($status == ScheduledLesson::STATUS_CANCELLED) {

            if (!$sLessonObj->assignLessonByAdmin($sldetailId, '', $teacher_id)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
            // remove from teacher google calendar
            $setting = UserSetting::getUserSettings($sLessonObj->getFldValue('slesson_teacher_id'));
            if (!empty($setting['us_google_access_token'])) {
                $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
                if ($oldCalId) {
                    SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $oldCalId);
                }
                $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', '');
            }
        }
        $sLessonObj->setFldValue('slesson_status', $status);
        // $sLessonObj->setFldValue('slesson_teacher_id', $teacher_id);
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($sLessonObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newLessonObj = new ScheduledLesson();
        $newLesson = array(
            'slesson_teacher_id' => $teacher_id,
            'slesson_grpcls_id' => $lessonRow['slesson_grpcls_id'],
            'slesson_kids_class' => $lessonRow['slesson_kids_class'],
            'slesson_slanguage_id' => $lessonRow['slesson_slanguage_id'],
            'slesson_date' => $lessonRow['slesson_date'],
            'slesson_end_date' => $lessonRow['slesson_end_date'],
            'slesson_start_time' => $lessonRow['slesson_start_time'],
            'slesson_end_time' => $lessonRow['slesson_end_time'],
            'slesson_status' => $lessonRow['slesson_status'],
            'slesson_learner_fname' => $lessonRow['slesson_learner_fname'],
            'slesson_learner_lname' => $lessonRow['slesson_learner_lname'],
            'slesson_learner_code' => $lessonRow['slesson_learner_code'],
            'slesson_learner_code_status' => $lessonRow['slesson_learner_code_status'],
            'slesson_learner_no_of_child' => $lessonRow['slesson_learner_no_of_child']
        );
        $newLessonObj->assignValues($newLesson);
        if (!$newLessonObj->save()) {
            $this->error = $newLessonObj->getError();
            return false;
        }
        $new_slesson_id = $newLessonObj->getMainTableRecordId();
        $new_teacher_data = new SearchBase('tbl_user_credentials');
        $new_teacher_data->addCondition('credential_user_id', '=', $teacher_id);
        $new_teacher_Row = FatApp::getDb()->fetch($new_teacher_data->getResultSet());
        $record = new ScheduledLessonDetails($sldetailId);
        $record->assignValues(['sldetail_learner_status' => $status]);
        if (!$record->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $sLessonDetailAr = array(
            'sldetail_slesson_id' => $new_slesson_id,
            'sldetail_order_id' => $lessonRow['sldetail_order_id'],
            'sldetail_learner_id' => $lessonRow['sldetail_learner_id'],
            'sldetail_learner_status' => $lessonRow['sldetail_learner_status']
        );
        $slDetailsObj = new ScheduledLessonDetails();
        $slDetailsObj->assignValues($sLessonDetailAr);
        if (!$slDetailsObj->save()) {
            $this->error = $slDetailsObj->getError();
            return false;
        }
        $new_sldetailId = $slDetailsObj->getMainTableRecordId();
        $db->commitTransaction();
        $userNotification = new UserNotifications($lessonRow['sldetail_learner_id']);
        // $userNotification->sendSchLessonUpdateNotificationByAdmin($sldetailId, $lessonRow['sldetail_learner_id'], $status, User::USER_TYPE_TEACHER);
        // $userNotification->sendSchLessonUpdateNotificationByAdmin($new_sldetailId, $lessonRow['sldetail_learner_id'], $status, User::USER_TYPE_TEACHER);
        $this->set('msg', 'Updated Successfully.');
        $this->set('slessonId', $slesson_id);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updateStatusSetup()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError($this->unAuthorizeAccess);
        }
        $sldetailId = FatApp::getPostedData('sldetail_id', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('slesson_status', FatUtility::VAR_INT, 0);
        $statusArr = ScheduledLesson::getStatusArr();
        unset($statusArr[ScheduledLesson::STATUS_RESCHEDULED]);
        if (1 > $sldetailId || !array_key_exists($status, $statusArr)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $srch = new ScheduledLessonSearch(false);
        $srch->addCondition('sldetail_id', '=', $sldetailId);
        $lessonRow = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $slesson_id = $lessonRow['slesson_id'];
        if ($lessonRow['slesson_status'] == ScheduledLesson::STATUS_CANCELLED) {
            $this->error = Label::getLabel("LBL_You_can_not_change_status_of_cancelled_lesson", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        if ($status == ScheduledLesson::STATUS_CANCELLED && $lessonRow['slesson_status'] != ScheduledLesson::STATUS_NEED_SCHEDULING) {
            FatUtility::dieJsonError(Label::getLabel('LBL_You_are_not_cancelled_this_lesson'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $sLessonObj->loadFromDb();
        /**
         * @todo Wallet transactions
         * 
         * Update lesson detail statuses
         */
        if ($status == ScheduledLesson::STATUS_COMPLETED) {
            $sLessonObj->setFldValue('slesson_ended_on', date('Y-m-d H:i:s'));
        }
        if ($status == ScheduledLesson::STATUS_CANCELLED) {
            if (!$sLessonObj->cancelLessonByTeacher('')) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
            // remove from teacher google calendar
            $setting = UserSetting::getUserSettings($sLessonObj->getFldValue('slesson_teacher_id'));
            if (!empty($setting['us_google_access_token'])) {
                $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
                if ($oldCalId) {
                    SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $oldCalId);
                }
                $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', '');
            }
        }
        $sLessonObj->setFldValue('slesson_status', $status);
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($sLessonObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $record = new ScheduledLessonDetails($sldetailId);
        $record->assignValues(['sldetail_learner_status' => $status]);
        if (!$record->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $db->commitTransaction();
        $userNotification = new UserNotifications($lessonRow['sldetail_learner_id']);
        $userNotification->sendSchLessonUpdateNotificationByAdmin($sldetailId, $lessonRow['sldetail_learner_id'], $status, User::USER_TYPE_TEACHER);
        $this->set('msg', 'Updated Successfully.');
        $this->set('slessonId', $slesson_id);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updateOrderStatus()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError($this->unAuthorizeAccess);
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $data = FatApp::getPostedData();
        $orderSearch = new OrderSearch();
        $orderSearch->joinScheduledLessonDetail();
        $orderSearch->joinScheduledLesson();
        $orderSearch->addMultipleFields([
            'order_id',
            'order_is_paid',
            'order_user_id',
            'order_net_amount',
            'sldetail_order_id',
            'slesson_id',
            'slesson_grpcls_id',
            'count(sld.sldetail_order_id) as totalLessons',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_SCHEDULED . ' THEN 1 ELSE 0 END) scheduledLessonsCount',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ' THEN 1 ELSE 0 END) needToscheduledLessonsCount',
        ]);
        $orderSearch->addCondition('o.order_id', '=', FatApp::getPostedData('order_id', FatUtility::VAR_STRING, ''));
        $orderSearch->addGroupBy('sld.sldetail_order_id');
        $resultSet = $orderSearch->getResultSet();
        $orderInfo = $db->fetch($resultSet);
        if (empty($orderInfo)) {
            $this->error = Label::getLabel("LBL_Invalid_Request", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        if ($orderInfo['order_is_paid'] == Order::ORDER_IS_CANCELLED) {
            $this->error = Label::getLabel("LBL_You_can_not_change_status_of_cancelled_order", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        $orderInfo['order_net_amount'] = FatUtility::float($orderInfo['order_net_amount']);
        if ($orderInfo['slesson_grpcls_id'] == 0 && $data['order_is_paid'] == Order::ORDER_IS_CANCELLED && $orderInfo['needToscheduledLessonsCount'] != $orderInfo['totalLessons']) {
            $this->error = Label::getLabel("LBL_You_can_not_cancel_the_order", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        if ($orderInfo['slesson_grpcls_id'] == 0 && $data['order_is_paid'] == Order::ORDER_IS_CANCELLED && $orderInfo['scheduledLessonsCount'] > 0) {
            $this->error = Label::getLabel("LBL_You_can_not_cancel_the_order_because_some_lesson_are_scheduled", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        if ($data['order_is_paid'] == Order::ORDER_IS_PENDING && $orderInfo['order_is_paid'] == Order::ORDER_IS_CANCELLED) {
            $this->error = Label::getLabel("LBL_Order_already_cancelled", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        $assignValues = ['order_is_paid' => $data['order_is_paid']];
        if (!$db->updateFromArray(Order::DB_TBL, $assignValues, ['smt' => 'order_id = ?', 'vals' => [$data['order_id']]])) {
            $db->rollbackTransaction();
            $this->error = Label::getLabel("LBL_SYSTEM_ERROR", CommonHelper::getLangId());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError($this->error);
            }
            return false;
        }
        /* [ */
        if ($data['order_is_paid'] == Order::ORDER_IS_CANCELLED && $orderInfo['order_net_amount'] > 0) {
            $scheduledLessonSrch = new ScheduledLessonSearch();
            $scheduledLessonSrch->addMultipleFields([
                'sldetail_id',
                'slesson_id',
                'slesson_grpcls_id',
                'slesson_status',
                'sldetail_learner_status',
            ]);
            $scheduledLessonSrch->addCondition('sldetail_order_id', '=', $orderInfo['order_id']);
            $scheduledLessonSrch->addCondition('sldetail_learner_status', '!=', ScheduledLesson::STATUS_CANCELLED);
            $scheduledLessonSrch->addCondition('slesson_status', '!=', ScheduledLesson::STATUS_CANCELLED);
            $orderLessons = FatApp::getDb()->fetchAll($scheduledLessonSrch->getResultSet());
            foreach ($orderLessons as $orderLesson) {
                if (
                    $orderLesson['slesson_grpcls_id'] == 0 &&
                    !$db->updateFromArray(
                        ScheduledLesson::DB_TBL,
                        ['slesson_status' => ScheduledLesson::STATUS_CANCELLED],
                        ['smt' => 'slesson_id = ?', 'vals' => [$orderLesson['slesson_id']]]
                    )
                ) {
                    $db->rollbackTransaction();
                    $this->error = $db->getError();
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieJsonError($this->error);
                    }
                    return false;
                }
                $schLesDetObj = new ScheduledLessonDetails($orderLesson['sldetail_id']);
                if (!$schLesDetObj->refundToLearner()) {
                    $db->rollbackTransaction();
                    FatUtility::dieJsonError($db->getError());
                }
                if (!$db->updateFromArray(
                    ScheduledLessonDetails::DB_TBL,
                    ['sldetail_learner_status' => ScheduledLesson::STATUS_CANCELLED],
                    ['smt' => 'sldetail_order_id = ?', 'vals' => [$data['order_id']]]
                )) {
                    $db->rollbackTransaction();
                    $this->error = $db->getError();
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieJsonError($this->error);
                    }
                    return false;
                }
            }
        }
        $db->commitTransaction();
        if (FatUtility::isAjaxCall()) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_Updated_Successfully.'));
        }
        $this->set('msg', Label::getLabel('LBL_Updated_Successfully.'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updatePayment()
    {
        if (!$this->canEdit) {
            FatUtility::dieJsonError($this->unAuthorizeAccess);
        }
        $orderId = FatApp::getPostedData('opayment_order_id', FatUtility::VAR_STRING, '');
        if ($orderId == '' || $orderId == null) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $form = $this->getPaymentForm($orderId);
        $post = $form->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($form->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $orderSearch = new OrderSearch();
        $orderSearch->addMultipleFields(['order_id']);
        $orderSearch->joinOrderProduct();
        $orderSearch->joinUser();
        $orderSearch->joinTable(User::DB_TBL, 'INNER JOIN', 'op.op_teacher_id = t.user_id', 't');
        $orderSearch->addCondition('order_id', '=', $orderId);
        $orderSearch->addCondition('order_type', '=', Order::TYPE_LESSON_BOOKING);
        $orderSearch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
        $resultSet = $orderSearch->getResultSet();
        $orderDeatils = FatApp::getDb()->fetch($resultSet);
        if (empty($orderDeatils)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST.'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->adminLangId);
        if (!$orderPaymentObj->addOrderPayment($post["opayment_method"], $post['opayment_gateway_txn_id'], $post["opayment_amount"], $post["opayment_comments"])) {
            Message::addErrorMessage($orderPaymentObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $giftcardObj = new Giftcard();
        if (!$giftcardObj->addGiftcardDetails($orderId)) {
            Message::addErrorMessage($giftcardObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('LBL_Payment_Details_Added_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
}
