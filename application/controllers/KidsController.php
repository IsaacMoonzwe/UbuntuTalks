<?php
session_start();
class KidsController extends MyAppController
{
    public function index()
    {
        $this->set('frmSrch', $this->getSearchForm());
        $siteLangId = CommonHelper::getLangId();
        $daysArr = applicationConstants::getWeekDays();
        $timeSlotArr = TeacherGeneralAvailability::timeSlotArr();
        $srch = TeacherKidsClassesSearch::getSearchObj($this->siteLangId);
        if (isset($post['language']) && $post['language'] !== "") {
            $srch->addCondition('grpcls_tlanguage_id', '=', $post['language']);
        }
        $rs = $srch->getResultSet();
        $classesList = FatApp::getDb()->fetchAll($rs);
        $newArr = [];
        $newArr['minPrice'] = min(array_column($classesList, 'minPrice'));
        $newArr['maxPrice'] = max(array_column($classesList, 'maxPrice'));
        $priceArr = $newArr;
        $group = new TeacherKidsClassesSearch();
        $group->addSearchListingFields();
        $group->addGroupBy('grpcls_ages');
        $newRs = $group->getResultSet();
        $groupList = FatApp::getDb()->fetchAll($newRs);
        $this->set('groupList', $groupList);
        $this->set('priceArr', $priceArr);
        $this->set('daysArr', $daysArr);
        $this->set('timeSlotArr', $timeSlotArr);
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addJs('js/ion.rangeSlider.js');
        $this->set('setMonthAndWeekName', true);
        $kidsBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_KIDS_BANNER, $this->siteLangId);
        $this->set('kidsBanner', $kidsBanner);
        $this->set('languages', TeachingLanguage::getAllLangsWithUserCount($this->siteLangId));
        $this->_template->render(true, true, 'kids/index.php');
    }

    public function joinnow()
    {
        $schecule_id = FatApp::getPostedData('myArrfay', FatUtility::VAR_INT, 0);
        $srch_lesson = new SearchBase('tbl_scheduled_lessons');
        $srch_lesson->addCondition('slesson_id', "=", $schecule_id);
        $rs_lesson = $srch_lesson->getResultSet();
        $langData = FatApp::getDb()->fetchAll($rs_lesson);
        $lastDataIndex = sizeOf($langData);
        $schedule = end($langData);
        $srch_data = new SearchBase('tbl_talkkids_classes');
        $srch_data->addCondition('grpcls_id', "=", $schedule['slesson_grpcls_id']);
        $rs_data = $srch_data->getResultSet();
        $kidsData = FatApp::getDb()->fetchAll($rs_data);
        $lastKidsDataIndex = sizeOf($kidsData);
        $kidsLast = end($kidsData);
        $totalLesson = (int)$kidsLast['grpcls_total_lesson'];
        if ($kidsLast['grpcls_total_lesson'] > $lastDataIndex) {
            $week_dates = array();
            $week_days = explode(',', $kidsLast['grpcls_weeks']);
            $next_date = date('Y-m-d', strtotime($kidsLast['grpcls_start_datetime']));
            array_push($week_dates, $next_date);
            foreach ($week_days as $weeks) {
                if ($kids['grpcls_start_datetime'] < date('Y-m-d H:i:s')) {
                    $next_date = date('Y-m-d', strtotime('next ' . $weeks));
                } else {
                    $next_date = date('Y-m-d', strtotime('next ' . $weeks, strtotime($kids['grpcls_start_datetime'])));
                }
                array_push($week_dates, $next_date);
            }

            function sortFunction($a, $b)
            {
                return strtotime($a) - strtotime($b);
            }
            usort($week_dates, "sortFunction");
            $days = date('l', $schedule['slesson_date']);
            $key = array_search($days, $week_dates);
            if ($key >= sizeof($week_dates)) {
                $key = 0;
            } else {
                $key = $key + 1;
            }
            $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $week_dates[$key], true, $userTimezone);
            $id = $schedule['slesson_id'];
            unset($schedule['slesson_id']);
            unset($schedule['sldetail_learner_google_calendar_id']);
            $schedule['slesson_date'] = $startDateTime;
            $schedule['slesson_end_date'] = $startDateTime;
            $sLessonObj = new ScheduledLesson();
            $sLessonObj->assignValues($schedule);
            if (!$sLessonObj->save()) {
                $this->error = $sLessonObj->getError();
                return false;
            }
            $slesson_id = $sLessonObj->getMainTableRecordId();
            $srch_detail = new SearchBase('tbl_scheduled_lesson_details');
            $srch_detail->addCondition('sldetail_slesson_id', "=", $id);
            $rs_Detail = $srch_detail->getResultSet();
            $detailData = FatApp::getDb()->fetchAll($rs_Detail);
            $d_data = end($detailData);
            $sLessonDetailAr = array(
                'sldetail_slesson_id' => $slesson_id,
                'sldetail_order_id' => $d_data['sldetail_order_id'],
                'sldetail_learner_id' => $d_data['sldetail_learner_id'],
                'sldetail_learner_status' => $d_data['sldetail_learner_status']
            );
            $slDetailsObj = new ScheduledLessonDetails();
            $slDetailsObj->assignValues($sLessonDetailAr);
            if (!$slDetailsObj->save()) {
                $this->error = $slDetailsObj->getError();
                return false;
            }
            $sldetailId = $slDetailsObj->getMainTableRecordId();
            $orders = new SearchBase('tbl_order_payments');
            $orderInfo = $order->getOrderById($d_data['sldetail_order_id']);
            if ($orderInfo['op_grpcls_id'] > 0) {
                $tgrpcls = new TeacherGroupClassesSearch(false);
                if (empty($tgrpcls)) {
                    $tgrpcls = new TeacherKidsClassesSearch(false);
                }
                $grpClsRow = $tgrpcls->getClassBasicDetails($orderInfo['op_grpcls_id'], $orderInfo['order_user_id']);
                $vars = [
                    '{learner_name}' => $grpClsRow['learner_full_name'],
                    '{teacher_name}' => $grpClsRow['teacher_full_name'],
                    '{class_name}' => $grpClsRow['grpcls_title'],
                    '{class_date}' => MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $grpClsRow['grpcls_start_datetime'], false, $grpClsRow['teacherTimeZone']),
                    '{class_start_time}' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $grpClsRow['grpcls_start_datetime'], true, $grpClsRow['teacherTimeZone']),
                    '{class_end_time}' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $grpClsRow['grpcls_end_datetime'], true, $grpClsRow['teacherTimeZone']),
                    '{learner_comment}' => '',
                    '{status}' => Label::getLabel('VERB_Scheduled'),
                ];
                EmailHandler::sendMailTpl($grpClsRow['teacherEmailId'], 'learner_class_book_email', $defaultSiteLangId, $vars);
                $vars['{class_date}'] = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $grpClsRow['grpcls_start_datetime'], false, $grpClsRow['learnerTimeZone']);
                $vars['{class_start_time}'] = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $grpClsRow['grpcls_start_datetime'], true, $grpClsRow['learnerTimeZone']);
                $vars['{class_end_time}'] = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $grpClsRow['grpcls_end_datetime'], true, $grpClsRow['learnerTimeZone']);
                EmailHandler::sendMailTpl($grpClsRow['learnerEmailId'], 'class_book_email_confirmation', $defaultSiteLangId, $vars);
                // share on student google calendar
                $userSettings = UserSetting::getUserSettings($orderInfo['order_user_id']);
                if (!empty($userSettings['us_google_access_token'])) {
                    $view_url = CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$sldetailId]);
                    $google_cal_data = [
                        'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $defaultSiteLangId),
                        'summary' => sprintf(Label::getLabel("LBL_Group_Class_Scheduled_for_%s"), $grpClsDetails['grpcls_title']),
                        'description' => sprintf(Label::getLabel("LBL_Click_here_to_join_the_class:_%s"), $view_url),
                        'url' => $view_url,
                        'start_time' => date('c', strtotime($slesson_date . ' ' . $slesson_start_time)),
                        'end_time' => date('c', strtotime($slesson_end_date . ' ' . $slesson_end_time)),
                        'timezone' => MyDate::getTimeZone(),
                    ];
                    $calId = SocialMedia::addEventOnGoogleCalendar($userSettings['us_google_access_token'], $google_cal_data);
                    if (!empty($calId)) {
                        $sLessonDetailObj = new ScheduledLessonDetails($sldetailId);
                        $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', $calId);
                        $sLessonDetailObj->save();
                    }
                }
                $teacherSettings = UserSetting::getUserSettings($orderInfo['op_teacher_id']);
                if (!empty($teacherSettings['us_google_access_token'])) {
                    $sLessonObj = new ScheduledLesson($slesson_id);
                    $sLessonObj->loadFromDb();
                    $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
                    // if (empty($oldCalId)) {
                    $view_url = CommonHelper::generateFullUrl('TeacherScheduledLessons', 'view', [$slesson_id]);
                    $google_cal_data = [
                        'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $defaultSiteLangId),
                        'summary' => sprintf(Label::getLabel("LBL_Group_Class_Scheduled_for_%s"), $grpClsDetails['grpcls_title']),
                        'description' => sprintf(Label::getLabel("LBL_Click_here_to_deliver_the_class:_%s"), $view_url),
                        'url' => $view_url,
                        'start_time' => date('c', strtotime($slesson_date . ' ' . $slesson_start_time)),
                        'end_time' => date('c', strtotime($slesson_end_date . ' ' . $slesson_end_time)),
                        'timezone' => MyDate::getTimeZone(),
                    ];
                    $calId = SocialMedia::addEventOnGoogleCalendar($teacherSettings['us_google_access_token'], $google_cal_data);
                    if (!empty($calId)) {
                        $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', $calId);
                        $sLessonObj->save();
                    }
                }
            }
        }
    }

    public function setup()
    {
        $grpcls_id = FatApp::getPostedData('myArrfay', FatUtility::VAR_INT, 0);
        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $srch2 = new SearchBase('tbl_scheduled_lesson_details');
        $srch2->joinTable('tbl_scheduled_lessons', 'INNER JOIN', 'slesson_id=sldetail_slesson_id');
        $srch2->addDirectCondition('slesson_grpcls_id=grpcls_id');
        $cnd = $srch2->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        $cnd->attachCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch2->doNotCalculateRecords();
        $srch2->doNotLimitRecords();
        $srch2->addFld('COUNT(DISTINCT sldetail_learner_id)');
        $srch3 = new SearchBase('tbl_scheduled_lessons');
        $srch3->addDirectCondition('slesson_grpcls_id=grpcls_id');
        $srch3->doNotCalculateRecords();
        $srch3->doNotLimitRecords();
        $srch3->addFld('slesson_teacher_join_time > 0');
        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_id', '=', $grpcls_id);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $flag = false;
        foreach ($classes as $class) {
            $post = $class;
            $weeks_list  = $class['grpcls_weeks'];
            if ($weeks_list == $grpcls_id) {
                $flag = true;
            } else if ($weeks_list !== '' && $weeks_list && !$flag) {
                $dbTtime = explode(' ', $class['grpcls_start_datetime']);
                $weekNames = explode(',', $weeks_list);
                foreach ($weekNames as $w) {
                    $nextTime = strtotime('next ' . $w);
                    $time = date("Y-m-d", $nextTime);
                    $nT = $time . $dbTtime[1];
                    $newEndTime = date($nT);
                    $post['grpcls_weeks'] = '';
                    $post['grpcls_start_datetime'] = MyDate::changeDateTimezone($newEndTime, $systemTimeZone, $user_timezone);
                    $post['grpcls_slug'] = isset($class['grpcls_slug']) ? $class['grpcls_slug'] : str_replace(" ", '-', strtolower($class['grpcls_title']));
                    $tGrpClsSrchObj = new TeacherKidsClassesSearch();
                    unset($post['grpcls_id']);
                    unset($post['grpcls_slug']);
                    $tGrpClsObj = new TeacherKidsClasses();
                    $tGrpClsObj->assignValues($post);
                    $db = FatApp::getDb();
                    $db->startTransaction();
                    if (true !== $tGrpClsObj->save()) {
                        $db->rollbackTransaction();
                        FatUtility::dieJsonError($tGrpClsObj->getError());
                    }
                    $seoUrl = CommonHelper::seoUrl($post['grpcls_title'] . '-' . $tGrpClsObj->getMainTableRecordId());
                    if (!$db->updateFromArray(
                        TeacherKidsClasses::DB_TBL,
                        ['grpcls_slug' => $seoUrl],
                        ['smt' => 'grpcls_id = ?', 'vals' => [$tGrpClsObj->getMainTableRecordId()]]
                    )) {
                        $this->error = $db->getError();
                        $db->rollbackTransaction();
                        return false;
                    }
                    $db->commitTransaction();
                    $msg = Label::getLabel('LBL_Group_Class_Saved_Successfully!');
                }
            }
        }
        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_id', '!=', $grpcls_id);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('totalRecords', $totalRecords);
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('classes', $classes);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('referer', CommonHelper::redirectUserReferer(true));
        $this->set('classStatusArr', TeacherKidsClasses::getStatusArr());
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->set('msg', $msg);
        $this->set('grpcls_id', $grpcls_id);
        $this->set('lang_id', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }


    public function kidsClassList()
    {
        $frm = $this->getSearchForm();
        $post = FatApp::getPostedData();
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pageSize = FatApp::getPostedData('pageSize', FatUtility::VAR_INT, 12);
        $sortOrder = FatApp::getPostedData('sortOrder', FatUtility::VAR_STRING, '');
        $langId = CommonHelper::getLangId();
        $_SESSION['search_filters'] = $post;
        $srch = new TeacherKidsClassesSearch($langId);
        $srch->addSearchListingFields();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->applyOrderBy($sortOrder);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $rawData = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('classes', $rawData);
        $this->set('postedData', $post);
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('frm', $frm);
        $this->set('slots', TeacherGeneralAvailability::timeSlotArr());
        $this->_template->render(false, false);
    }

    public function searchFilter()
    {
        $frm = $this->getSearchForm();
        $sortOrder = FatApp::getPostedData('sortOrder', FatUtility::VAR_STRING, '');
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        if ($page < 2) {
            $page = 1;
        }
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = TeacherKidsClassesSearch::getSearchObj($this->siteLangId);
        if (isset($post['language']) && $post['language'] !== "") {
            $srch->addCondition('grpcls_tlanguage_id', '=', $post['language']);
        }
        $srch->addCondition('grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
        $rs = $srch->getResultSet();
        $classesList = FatApp::getDb()->fetchAll($rs);
        $group = new TeacherKidsClassesSearch();
        $group->addSearchListingFields();
        $group->addGroupBy('grpcls_ages');
        $newRs = $group->getResultSet();
        $groupList = FatApp::getDb()->fetchAll($newRs);
        $newArr = [];
        $newArr['minPrice'] = min(array_column($classesList, 'minPrice'));
        $newArr['maxPrice'] = max(array_column($classesList, 'maxPrice'));
        $priceArr = $newArr;
        $_SESSION['min'] = $priceArr['minPrice'];
        $_SESSION['max'] = $priceArr['maxPrice'];
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount() == "" ? $srch->getRecordCount() : $srch->recordCount(),
        ];
        $this->set('priceArr', $priceArr);
        $this->set('classes', $classesList);
        $this->set('filterDefaultMinValue', $filterDefaultMinValue);
        $this->set('filterDefaultMaxValue', $filterDefaultMaxValue);
        $this->set('priceArray', $priceArr);
        $this->set('newArr', $newArr);
        $min_booking_time = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $this->set('min_booking_time', $min_booking_time);
        $post['page'] = $page;
        $this->set('postedData', $post);
        $this->set('frm', $frm);
        $this->set('pagingArr', $pagingArr);
        $this->set('groupList', $groupList);
        $this->_template->render(false, false);
    }

    public function search()
    {
        $frm = $this->getSearchForm();
        $sortOrder = FatApp::getPostedData('sortOrder', FatUtility::VAR_STRING, '');
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $_SESSION['search_filters_kids'] = $post;
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        if ($page < 2) {
            $page = 1;
        }
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = TeacherKidsClassesSearch::getSearchObj($this->siteLangId);
        if (isset($post['language']) && $post['language'] !== "") {
            $srch->addCondition('grpcls_tlanguage_id', '=', $post['language']);
        }
        $rs = $srch->getResultSet();
        $classesList = FatApp::getDb()->fetchAll($rs);
        $group = new TeacherKidsClassesSearch();
        $group->addSearchListingFields();
        $group->addGroupBy('grpcls_ages');
        $newRs = $group->getResultSet();
        $groupList = FatApp::getDb()->fetchAll($newRs);
        $newArr = [];
        $newArr['minPrice'] = min(array_column($classesList, 'minPrice'));
        $newArr['maxPrice'] = max(array_column($classesList, 'maxPrice'));
        $priceArr = $newArr;
        $_SESSION['min'] = $priceArr['minPrice'];
        $_SESSION['max'] = $priceArr['maxPrice'];
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount() == "" ? $srch->getRecordCount() : $srch->recordCount(),
        ];
        $this->set('priceArr', $priceArr);
        $this->set('classes', $classesList);
        $this->set('filterDefaultMinValue', $filterDefaultMinValue);
        $this->set('filterDefaultMaxValue', $filterDefaultMaxValue);
        $this->set('priceArray', $priceArr);
        $this->set('newArr', $newArr);
        $min_booking_time = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $this->set('min_booking_time', $min_booking_time);
        $post['page'] = $page;
        $this->set('postedData', $post);
        $this->set('frm', $frm);
        $this->set('pagingArr', $pagingArr);
        $this->set('groupList', $groupList);
        $this->_template->render(false, false);
    }

    public function viewCalendar($teacherId = 0, $languageId = 1)
    {
        $teacherId = FatUtility::int($teacherId);
        $languageId = FatUtility::int($languageId);
        $user = new User($teacherId);
        $postedAction = FatApp::getPostedData('action');
        $allowedActionArr = ['free_trial', 'paid'];
        if (!in_array($postedAction, $allowedActionArr)) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        if (!$user->loadFromDb()) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userRow = $user->getFlds();
        $bookingMinutesDuration = FatApp::getConfig('CONF_DEFAULT_PAID_LESSON_DURATION', FatUtility::VAR_INT, 60);
        if ('free_trial' == $postedAction) {
            $bookingMinutesDuration = FatApp::getConfig('conf_trial_lesson_duration', FatUtility::VAR_INT, 30);
            $freeTrialEnable = FatApp::getConfig('CONF_ENABLE_FREE_TRIAL', FatUtility::VAR_INT, 0);
            if ($freeTrialEnable == applicationConstants::NO) {
                FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
            }
        }
        $hour = floor($bookingMinutesDuration / 60);
        $hour = ($hour > 9) ? $hour : '0' . $hour;
        $min = ($bookingMinutesDuration - floor($bookingMinutesDuration / 60) * 60);
        $min = ($min > 9) ? $min : '0' . $min;
        $bookingSnapDuration = $hour . ':' . $min;
        $this->set('bookingMinutesDuration', $bookingMinutesDuration);
        $this->set('bookingSnapDuration', $bookingSnapDuration);
        $user_timezone = MyDate::getUserTimeZone();
        $nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
        $teacherBookingBefore = UserSetting::getUserSettings($teacherId)['us_booking_before'];
        if ('' == $teacherBookingBefore) {
            $teacherBookingBefore = 0;
        }
        $this->set('teacherBookingBefore', $teacherBookingBefore);
        $this->set('user_timezone', $user_timezone);
        $this->set('nowDate', $nowDate);
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $currentLangCode = strtolower(Language::getLangCode($this->siteLangId));
        $this->set('currentLangCode', $currentLangCode);
        $this->set('userRow', $userRow);
        $this->set('action', $postedAction);
        $this->set('teacher_name', $userRow['user_first_name']);
        $this->set('teacher_country_id', $userRow['user_country_id']);
        $this->set('teacher_id', $teacherId);
        $this->set('languageId', $languageId);
        $this->set('cssClassArr', $cssClassNamesArr);
        $this->_template->render(false, false);
    }

    public function checkCalendarTimeSlotAvailability($userId = 0)
    {
        $userId = FatUtility::int($userId);
        $post = FatApp::getPostedData();
        if (false === $post) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        if ($userId < 1) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $systemTimeZone = MyDate::getTimeZone();
        $userTimezone = MyDate::getUserTimeZone();
        $startDateTime = MyDate::changeDateTimezone($post['start'], $userTimezone, $systemTimeZone);
        $endDateTime = MyDate::changeDateTimezone($post['end'], $userTimezone, $systemTimeZone);
        if (strtotime($startDateTime) < strtotime(date('Y-m-d H:i:s'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Can_not_schdule_lesson_for_old_date'));
        }
        if (UserAuthentication::isUserLogged()) {
            $loggedUserId = UserAuthentication::getLoggedUserId();
            $checkGroupClassTiming = TeacherGroupClassesSearch::checkGroupClassTiming([$loggedUserId], $startDateTime, $endDateTime);
            $checkGroupClassTiming->setPageSize(1);
            $checkGroupClassTiming->addCondition('grpcls_status', '=', TeacherGroupClasses::STATUS_ACTIVE);
            $getResultSet = $checkGroupClassTiming->getResultSet();
            $scheduledLessonData = FatApp::getDb()->fetch($getResultSet);
            if (!empty($scheduledLessonData)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_YOU_ALREDY_HAVE_A_GROUP_CLASS_BETWEEN_THIS_TIME_RANGE'));
            }
        }
        $tWsch = new TeacherWeeklySchedule();
        $checkAvialSlots = $tWsch->checkCalendarTimeSlotAvailability($userId, $startDateTime, $endDateTime);
        $returnArray = [
            'status' => ($checkAvialSlots) ? applicationConstants::YES : applicationConstants::NO,
        ];
        if (!empty($tWsch->getError())) {
            $returnArray['msg'] = $tWsch->getError();
        }
        FatUtility::dieJsonSuccess($returnArray);
    }

    public function getTeacherGeneralAvailabilityJsonData(int $userId)
    {
        $post = FatApp::getPostedData();
        if ($userId < 1) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTimezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $startDate = MyDate::changeDateTimezone($post['start'], $userTimezone, $systemTimeZone);
        $endDate = MyDate::changeDateTimezone($post['end'], $userTimezone, $systemTimeZone);
        $midPoint = (strtotime($startDate) + strtotime($endDate)) / 2;
        $weekRange = CommonHelper::getWeekRangeByDate(date('Y-m-d', $midPoint));
        $jsonArr = TeacherGeneralAvailability::getGenaralAvailabilityJsonArr($userId, ['WeekStart' => $weekRange['start'], 'WeekEnd' => $weekRange['end']]);
        echo FatUtility::convertToJson($jsonArr);
    }

    public function getTeacherScheduledLessonData($userId = 0)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $weekStartDate = Fatapp::getPostedData('start', FatUtility::VAR_STRING, '');
        $weekEndDate = Fatapp::getPostedData('end', FatUtility::VAR_STRING, '');
        $userTimezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        if (empty($weekStartDate) || empty($weekEndDate)) {
            $weekStartAndEndDate = MyDate::getWeekStartAndEndDate(new DateTime());
            $weekStartDate = $weekStartAndEndDate['weekStart'];
            $weekEndDate = $weekStartAndEndDate['weekEnd'];
        } else {
            $weekStartDate = MyDate::changeDateTimezone($weekStartDate, $userTimezone, $systemTimeZone);
            $weekEndDate = MyDate::changeDateTimezone($weekEndDate, $userTimezone, $systemTimeZone);
        }
        $db = FatApp::getDb();
        $srch = new ScheduledLessonSearch();
        $srch->addGroupBy('slesson_id');
        $srch->joinTeacher();
        $srch->joinTeacherSettings();
        $srch->joinTeacherTeachLanguageView($this->siteLangId);
        $srch->addMultipleFields([
            'slns.slesson_date',
            'slns.slesson_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_end_date',
            'slns.slesson_grpcls_id',
        ]);
        $userIds = [];
        $userIds[] = $userId;
        if (UserAuthentication::isUserLogged()) {
            $userIds[] = UserAuthentication::getLoggedUserId();
        }
        $condition = $srch->addCondition('slns.slesson_teacher_id', 'IN', $userIds);
        $condition->attachCondition('sldetail_learner_id', 'IN', $userIds);
        $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch->addCondition('CONCAT(slns.`slesson_date`, " ", slns.`slesson_start_time` )', '< ', $weekEndDate);
        $srch->addCondition('CONCAT(slns.`slesson_end_date`, " ", slns.`slesson_end_time` )', ' > ', $weekStartDate);
        $data = $db->fetchAll($srch->getResultSet());
        $jsonArr = [];
        $groupClassIds = [];
        foreach ($data as $data) {
            $slesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $data['slesson_date'] . ' ' . $data['slesson_start_time'], true, $userTimezone);
            $slesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $data['slesson_end_date'] . ' ' . $data['slesson_end_time'], true, $userTimezone);
            $jsonArr[] = [
                // "title" => $data['teacherTeachLanguageName'],
                "title" => "",
                "start" => $slesson_start_time,
                "end" => $slesson_end_time,
                "className" => "sch_data booked-slot",
                "classType" => "0",
            ];
            if ($data['slesson_grpcls_id'] > 0) {
                $groupClassIds[] = $data['slesson_grpcls_id'];
            }
        }
        echo FatUtility::convertToJson($jsonArr);
    }

    public function getTeacherWeeklyScheduleJsonData($userId)
    {
        $post = FatApp::getPostedData();
        if (false === $post) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        if ($userId < 1) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $userTimezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $startDate = MyDate::changeDateTimezone($post['start'], $userTimezone, $systemTimeZone);
        $endDate = MyDate::changeDateTimezone($post['end'], $userTimezone, $systemTimeZone);
        $weeklySchRows = TeacherWeeklySchedule::getWeeklyScheduleJsonArr($userId, $startDate, $endDate);
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $teacherBookingBefore = null;
        if (isset($_POST['bookingBefore'])) {
            $teacherBookingBefore = FatUtility::int(FatApp::getPostedData('bookingBefore'));
        }
        $jsonArr = [];
        $validStartDateTime = strtotime("+ " . $teacherBookingBefore . " hours");
        if (!empty($weeklySchRows)) {
            /* code added on 15-07-2019 */
            foreach ($weeklySchRows as $row) {
                $endDateTime = $row['twsch_end_date'] . ' ' . $row['twsch_end_time'];
                $startDateTime = $row['twsch_date'] . ' ' . $row['twsch_start_time'];
                if ($validStartDateTime > strtotime($startDateTime) && strtotime($endDateTime) > $validStartDateTime) {
                    $startDateTime = date('Y-m-d H:i:s', $validStartDateTime);
                }
                $twsch_end_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $endDateTime, true, $userTimezone);
                $twsch_start_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $startDateTime, true, $userTimezone);
                $twsch_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $startDateTime, true, $userTimezone);
                $midPoint = (strtotime($startDateTime) + strtotime($endDateTime)) / 2;
                $dateTime = new dateTime(date('Y-m-d H:i:s', $midPoint));
                $weekRange = MyDate::getWeekStartAndEndDate($dateTime);
                $twschWeekYear = date('W-Y', strtotime($weekRange['weekStart']));
                $jsonArr[] = [
                    "title" => "",
                    "date" => $twsch_date,
                    "start" => $twsch_start_time,
                    "end" => $twsch_end_time,
                    "weekyear" => $twschWeekYear,
                    '_id' => $row['twsch_id'],
                    'classType' => $row['twsch_is_available'],
                    'className' => $cssClassNamesArr[$row['twsch_is_available']],
                    'action' => 'fromWeeklySchedule',
                ];
            }
        }
        $midPoint = (strtotime($startDate) + strtotime($endDate)) / 2;
        $dateTime = new dateTime(date('Y-m-d H:i:s', $midPoint));
        $weekRange = MyDate::getWeekStartAndEndDate($dateTime);
        $twsch_weekyear = date('W-Y', strtotime($weekRange['weekStart']));
        if ((empty($jsonArr) || end($jsonArr)['weekyear'] != $twsch_weekyear)) {
            $weekData = ['WeekStart' => $weekRange['weekStart'], 'WeekEnd' => $weekRange['weekEnd']];
            $jsonArr2 = TeacherGeneralAvailability::getGenaralAvailabilityJsonArr($userId, $weekData, $teacherBookingBefore);
            $jsonArr = array_merge($jsonArr, $jsonArr2);
        }
        echo FatUtility::convertToJson($jsonArr);
    }


    public function view($grpcls_slug)
    {
        $grpcls_slug  = CommonHelper::htmlEntitiesDecode($grpcls_slug);
        $userId = UserAuthentication::isUserLogged() ? UserAuthentication::getLoggedUserId() : 0;
        $srch = TeacherKidsClassesSearch::getSearchObj($this->siteLangId);
        $srch->joinTable(Country::DB_TBL, 'LEFT JOIN', 'ut.user_country_id = country.country_id', 'country');
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'country.country_id = countryLang.countrylang_country_id and countryLang.countrylang_lang_id = ' . $this->siteLangId, 'countryLang');
        $srch->joinTable('tbl_teacher_stats', 'LEFT JOIN', 'testat.testat_user_id = ut.user_id', 'testat');
        $srch->addMultipleFields(['IFNULL(country_name, country_code) as country_name', 'testat_reviewes', 'testat_ratings']);
        $srch->addCondition('grpcls_slug', '=', $grpcls_slug);
        $srch->setPageSize(1);
        $rawData = FatApp::getDb()->fetchAll($srch->getResultSet());
        $classData = $srch->formatTeacherSearchData($rawData, $userId);
        if (empty($classData)) {
            FatUtility::exitWithErrorCode(404);
        }
        $teacher_id = $classData[0]['grpcls_teacher_id'];
        $srch2 = TeacherKidsClassesSearch::getSearchObj($this->siteLangId);
        $srch2->joinTable(Country::DB_TBL, 'LEFT JOIN', 'ut.user_country_id = country.country_id', 'country');
        $srch2->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'country.country_id = countryLang.countrylang_country_id and countryLang.countrylang_lang_id = ' . $this->siteLangId, 'countryLang');
        $srch2->joinTable('tbl_teacher_stats', 'LEFT JOIN', 'testat.testat_user_id = ut.user_id', 'testat');
        $srch2->addMultipleFields(['IFNULL(country_name, country_code) as country_name', 'testat_reviewes', 'testat_ratings']);
        $srch2->addCondition('grpcls_teacher_id', '=', $teacher_id);
        $teacherClasses = FatApp::getDb()->fetchAll($srch2->getResultSet());
        $this->set('class', $classData[0]);
        $this->set('teacherClasses', $teacherClasses);
        $min_booking_time = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $this->set('min_booking_time', $min_booking_time);
        $this->set('setMonthAndWeekName', true);
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/enscroll-0.6.2.min.js');
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addSelectBox('', 'language', TeacherKidsClassesSearch::getTeachLangs($this->siteLangId), '', array(), Label::getLabel('LBL_All_Language'));
        $frm->addHiddenField('', 'teachLangId');
        $frm->addSelectBox('', 'ages', TeacherKidsClassesSearch::getTeachLangs($this->siteLangId), '', array(), Label::getLabel('LBL_All_Language'));
        $frm->addTextBox('', 'teach_availability', '', ['placeholder' => Label::getLabel('LBL_Select_date_time')]);
        $frm->addTextBox('', 'keyword', '', array('placeholder' => Label::getLabel('LBL_Search_Class')));
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btnSrchSubmit', '');
        return $frm;
    }

    private function getTeacherSearchForm($teachLangSlug = '')
    {
        $teachLangId = 0;
        $teachLangName = '';
        if (!empty($teachLangSlug)) {
            $srch = new TeachingLanguageSearch($this->siteLangId);
            $srch->addCondition('tlanguage_slug', '=', $teachLangSlug);
            $srch->addMultipleFields(['tlanguage_id', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name']);
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            $rs = $srch->getResultSet();
            $languages = FatApp::getDb()->fetch($rs);
            if (!empty($languages['tlanguage_id'])) {
                $teachLangId = $languages['tlanguage_id'];
                $teachLangName = $languages['tlanguage_name'];
            }
        }
        $frm = new Form('frmTeacherSrch');
        $frm->addTextBox('', 'teach_language_name', $teachLangName, ['placeholder' => Label::getLabel('LBL_Language')]);
        $frm->addHiddenField('', 'teachLangId', $teachLangId);
        $frm->addTextBox('', 'teach_availability', '', ['placeholder' => Label::getLabel('LBL_Select_date_time')]);
        $keyword = $frm->addTextBox('', 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Teacher_Name')]);
        $keyword->requirements()->setLength(0, 15);
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btnTeacherSrchSubmit', '');
        return $frm;
    }

    private function searchTeachers(&$srch)
    {
        $teachLangId = FatApp::getPostedData('teachLangId', FatUtility::VAR_INT, 0);
        $postedData = FatApp::getPostedData();
        $srch = new UserSearch(false);
        $srch->setTeacherDefinedCriteria(false, false);
        $tlangSrch = $srch->getMyTeachLangQry(true, $this->siteLangId, $teachLangId);
        $tlangSrch->addCondition('utl.utl_booking_slot', 'IN', CommonHelper::getPaidLessonDurations());
        $srch->joinTable("(" . $tlangSrch->getQuery() . ")", 'INNER JOIN', 'user_id = utl_user_id', 'utls');
        $srch->joinUserSpokenLanguages($this->siteLangId);
        $srch->joinUserCountry($this->siteLangId);
        $srch->joinUserAvailibility();
        if (UserAuthentication::isUserLogged()) {
            $srch->joinFavouriteTeachers(UserAuthentication::getLoggedUserId());
            $srch->addFld('uft_id');
        } else {
            $srch->addFld('0 as uft_id');
        }
        /* [ */
        $spokenLanguage = FatApp::getPostedData('spokenLanguage', FatUtility::VAR_STRING, NULL);
        if (!empty($spokenLanguage)) {
            $srch->addDirectCondition('spoken_language_ids IN (' . $spokenLanguage . ')');
        }
        /* ] */
        /* [ */
        $preferenceFilter = FatApp::getPostedData('preferenceFilter', FatUtility::VAR_STRING, NULL);
        if (!empty($preferenceFilter)) {
            if (is_numeric($preferenceFilter)) {
                $srch->addCondition('utpref_preference_id', '=', $preferenceFilter);
            } else {
                $preferenceFilterArr = explode(",", $preferenceFilter);
                $srch->addCondition('utpref_preference_id', 'IN', $preferenceFilterArr);
                $srch->addHaving('mysql_func_COUNT(DISTINCT utpref_preference_id)', '=', count($preferenceFilterArr), 'AND', true);
            }
        }
        /* ] */
        /* from country[ */
        $fromCountry = FatApp::getPostedData('fromCountry', FatUtility::VAR_STRING, NULL);
        if (!empty($fromCountry)) {
            if (is_numeric($fromCountry)) {
                $srch->addCondition('user_country_id', '=', $fromCountry);
            } else {
                $fromCountryArr = explode(",", $fromCountry);
                if (!empty($fromCountryArr)) {
                    $fromCountryArr = FatUtility::int($fromCountryArr);
                    $srch->addCondition('user_country_id', 'IN', $fromCountryArr);
                }
            }
        }
        /* ] */
        /* Language Teach [ */
        $langTeach = FatApp::getPostedData('teach_language_id', FatUtility::VAR_STRING, NULL);
        if ($langTeach > 0) {
            if (is_numeric($langTeach)) {
                //$srch->addCondition( 'us.us_teach_slanguage_id', '=', $langTeach );
                $srch->addDirectCondition('FIND_IN_SET(' . $langTeach . ', utl_tlanguage_ids)');
            }
        }
        /* ] */
        /* Week Day [ */
        $weekDays = FatApp::getPostedData('filterWeekDays', FatUtility::VAR_STRING, array());
        $timeSlots = FatApp::getPostedData('filterTimeSlots', FatUtility::VAR_STRING, array());
        $timeSlotArr = [];
        if (!empty($timeSlots)) {
            $timeSlotArr = CommonHelper::formatTimeSlotArr($timeSlots);
        }
        if (is_array($weekDays) && !empty($weekDays)) {
            $weekDates = MyDate::changeWeekDaysToDate($weekDays, $timeSlotArr);
            $condition = ' ( ';
            foreach ($weekDates as $weekDayKey => $date) {
                $condition .= ($weekDayKey == 0) ? '' : ' OR ';
                $condition .= ' ( CONCAT(`tgavl_date`," ",`tgavl_start_time`) < "' . $date['endDate'] . '" and CONCAT(`tgavl_end_date`," ",`tgavl_end_time`) > "' . $date['startDate'] . '" ) ';
            }
            $condition .= ' ) ';
            $srch->addDirectCondition($condition);
        }
        /* ] */
        /* Time Slot [ */
        if (empty($weekDays) && !empty($timeSlotArr)) {
            $systemTimeZone = MyDate::getTimeZone();
            $user_timezone = MyDate::getUserTimeZone();
            $condition = ' ( ';
            foreach ($timeSlotArr as $key => $formatedVal) {
                $condition .= ($key == 0) ? '' : ' OR ';
                $startTime = date('Y-m-d') . ' ' . $formatedVal['startTime'];
                $endTime = date('Y-m-d') . ' ' . $formatedVal['endTime'];
                $startTime = date('H:i:s', strtotime(MyDate::changeDateTimezone($startTime, $user_timezone, $systemTimeZone)));
                $endTime = date('H:i:s', strtotime(MyDate::changeDateTimezone($endTime, $user_timezone, $systemTimeZone)));
                $condition .= ' ( CONCAT(`tgavl_date`," ",`tgavl_start_time`) <  CONCAT(`tgavl_end_date`," ","' . $endTime . '") and CONCAT(`tgavl_end_date`," ",`tgavl_end_time`) >  CONCAT(`tgavl_date`," ","' . $startTime . '") ) ';
            }
            $condition .= ' ) ';
            $srch->addDirectCondition($condition);
        }
        /* ] */
        /* [ */
        $gender = FatApp::getPostedData('gender', FatUtility::VAR_STRING, NULL);
        if (!empty($gender)) {
            if (is_numeric($gender)) {
                $srch->addCondition('user_gender', '=', $gender);
            } else {
                $genderArr = explode(",", $gender);
                if (!empty($genderArr)) {
                    $genderArr = FatUtility::int($genderArr);
                    $srch->addCondition('user_gender', 'IN', $genderArr);
                }
            }
        }
        /* ] */
        /* price Range[ */
        $minPriceRange = FatApp::getPostedData('minPriceRange', FatUtility::VAR_FLOAT, 0);
        $maxPriceRange = FatApp::getPostedData('maxPriceRange', FatUtility::VAR_FLOAT, 0);
        if (!empty($minPriceRange) && !empty($maxPriceRange)) {
            $minPriceRangeInDefaultCurrency = CommonHelper::getDefaultCurrencyValue($minPriceRange, false, false);
            $maxPriceRangeInDefaultCurrency = CommonHelper::getDefaultCurrencyValue($maxPriceRange, false, false);
            $condition = $srch->addCondition('minPrice', '<=', $maxPriceRangeInDefaultCurrency);
            $condition->attachCondition('maxPrice', '>=', $minPriceRangeInDefaultCurrency, 'AND');
        }
        /* ] */
        /* [ */
        $filterSortBy = FatApp::getPostedData('sortBy', FatUtility::VAR_STRING, 'popularity_desc');
        $filterSortBy = explode('_', $filterSortBy);
        $sortBy = $filterSortBy[0];
        $sortOrder = $filterSortBy[1];
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        if (!empty($sortBy)) {
            $sortByArr = explode("_", $sortBy);
            $sortBy = isset($sortByArr[0]) ? $sortByArr[0] : $sortBy;
            $sortOrder = isset($sortByArr[1]) ? $sortByArr[1] : $sortOrder;
            switch ($sortBy) {
                case 'price':
                    $srch->addOrder('minPrice', $sortOrder);
                    break;
                case 'popularity':
                    $srch->addOrder('studentIdsCnt', $sortOrder);
                    $srch->addOrder('teacherTotLessons', $sortOrder);
                    $srch->addOrder('totReviews', $sortOrder);
                    $srch->addOrder('teacher_rating', $sortOrder);
                    break;
            }
        }
        /* ] */
        if (isset($postedData['keyword']) && !empty($postedData['keyword'])) {
            $cond = $srch->addCondition('user_first_name', 'LIKE', '%' . $postedData['keyword'] . '%');
            $cond->attachCondition('user_last_name', 'LIKE', '%' . $postedData['keyword'] . '%');
            $cond->attachCondition('mysql_func_CONCAT(user_first_name, " ", user_last_name)', 'LIKE', '%' . $postedData['keyword'] . '%', 'OR', true);
        }
        $srch->addOrder('user_id', 'DESC');
        $srch->addGroupBy('user_id');
        $srch->addMultipleFields([
            'user_id',
            'user_url_name',
            'user_first_name',
            'user_last_name',
            'user_country_id',
            'country_name as user_country_name',
            'user_profile_info',
            'uqualification_user_id',
            'utls.teacherTeachLanguageName',
            'utl_ids',
        ]);
    }
}
