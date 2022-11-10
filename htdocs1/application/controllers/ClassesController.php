<?php
class ClassesController extends MyAppController
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
		$codeFrm=$this->codeForm($this->siteLangId);
		$this->set('codeFrm', $codeFrm);
        $this->set('contactBanner', $contactBanner);
        $this->set('contactLeftSection', $contactLeftSection);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        // $this->_template->addJs('js/learnerLessonCommon.js');
        // $this->_template->addJs('js/moment.min.js');
        // $this->_template->addJs('js/fullcalendar-luxon.min.js');
        // $this->_template->addJs('js/fullcalendar.min.js');
        // $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        // $this->_template->addJs('js/fateventcalendar.js');
        // $this->_template->addJs(['js/jquery.barrating.min.js']);
        // $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->render();
    }
 public function addFirstName(){
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $fName = FatApp::getPostedData('fName', FatUtility::VAR_STRING, '');
        $data = [
            'msg' => '',
            'fName' => $fName,
            // 'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }
    public function addLastName(){
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $languageId = FatApp::getPostedData('languageId', FatUtility::VAR_INT, 0);
        $lessonDuration = FatApp::getPostedData('lessonDuration', FatUtility::VAR_INT, 0);
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $lName = FatApp::getPostedData('lName', FatUtility::VAR_STRING, '');
        $data = [
            'msg' => '',
            'lName' => $lName,
            // 'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }
    public function search()
    {
        // $frmSrch = $this->getSearchForm();
		
        $post = FatApp::getPostedData();
		if($post){
		$_SESSION['lesson_id']=$post['lesson_id'];
		}
        // $referer = CommonHelper::redirectUserReferer(true);
        // if (false === $post) {
        //     FatUtility::dieWithError($frmSrch->getValidationErrors());
        // }
        $sortOrder = '';
        $page =1;
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE');
        // $userId = UserAuthentication::getLoggedUserId();
        // $post = array_merge($post, ['sldetail_learner_id' => $userId]);
        $srch = new LessonSearch($this->siteLangId);
        $srch->addCondition('slesson_id',"=",$post['lesson_id']);
        $srch->joinTeacherLessonPlans();
        $srch->addSearchListingFields();
        $srch->applyPrimaryConditions();
        $srch->addFld('tlpn.tlpn_title');
        $srch->addFld('tlpn.tlpn_id');
        $srch->applyOrderBy($sortOrder);
        $srch->addGroupBy('sldetail.sldetail_id');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $srch->addCondition('slesson_id', '=', $post['lesson_id']);
        
        $resultSet = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetch($resultSet);
        // $lessons = $srch->fetchAll();
    	$lessonArr = [];
        $user_timezone = MyDate::getUserTimeZone();
        
        // echo "<pre>";
        // print_r($lessons);
        // foreach ($lessons as $lesson) {
            $key = $lessons['slesson_date'];
            if ('0000-00-00' != $lessons['slesson_date']) {
                $key = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lessons['slesson_date'] . ' ' . $lessons['slesson_start_time'], true, $user_timezone);
            }
            $lessonArr[$key][] = $lessons;
        // }

        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }

        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        $this->set('referer', $referer);
        $this->set('codeStatus', $lessons);		
        $this->set('lessonArr', $lessonArr);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->set('reportHours', FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION'));
        $this->_template->render(false, false);
    }

    protected function getLessonFlashCardSearchForm()
    {
        $frm = new Form('frmFlashCardSrch');
        $frm->addTextBox('', 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_Flash_Cards...')]);
        $fld = $frm->addHiddenField('', 'lesson_id');
        $fld->requirements()->setIntPositive();
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', '');
        return $frm;
    }

    public function view($lessonId=null)
    {
        $lDetailId = FatUtility::int($lessonId);
        $lessonDetailRow = ScheduledLessonDetails::getAttributesById(
            $lDetailId,
            ['sldetail_id', 'sldetail_slesson_id', 'sldetail_learner_id']
        );
        if (empty($lessonDetailRow)) {
            FatUtility::exitWithErrorCode(404);
        }
        $lessonId = $lessonDetailRow['sldetail_slesson_id'];
     
        $lessonRow = ScheduledLesson::getAttributesById($lessonId, ['slesson_id', 'slesson_teacher_id', 'slesson_grpcls_id']);
        if (!$lessonRow || $lessonRow['slesson_id'] != $lessonId) {
            FatUtility::exitWithErrorCode(404);
        }
        // if ($lessonDetailRow['sldetail_learner_id'] != UserAuthentication::getLoggedUserId()) {
        //     echo "Hello";
        //     Message::addErrorMessage(Label::getLabel('LBL_Access_Denied'));
        //     FatApp::redirectUser(CommonHelper::generateUrl('Classes'));
        // }
        $lessonRow['learnerId'] = $lessonDetailRow['sldetail_learner_id'];
        $lessonRow['teacherId'] = $lessonRow['slesson_teacher_id'];
        $srch=new SearchBase('tbl_schedule_lesson_code_join');
        
        $srch->addCondition('schedule_lesson_id','=',$lDetailId);
        $srch->addOrder('schedule_lesson_code_join', 'DESC');
      
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $latest_record=end($rows);
        $currentUserId=$latest_record['schedule_lesson_code_join'];
        $_SESSION['currentUserId']=$currentUserId;
        // $flashCardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true);
       // if ($flashCardEnabled) {
            // $frmSrchFlashCard = $this->getLessonFlashCardSearchForm();
            // $frmSrchFlashCard->fill(['lesson_id' => $lessonRow['slesson_id']]);
            // $this->set('frmSrchFlashCard', $frmSrchFlashCard);
        //}
        $this->set('user', $latest_record);
        $this->set('lessonRow', $lessonRow);
        $this->set('lessonId', $lessonRow['slesson_id']);
        $this->set('lDetailId', $lDetailId);
        // $this->set('showFlashCard', true);
        $this->set('setMonthAndWeekName', true);
        // $this->_template->addJs('js/learnerLessonCommon.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->addJs(['js/jquery.barrating.min.js']);
        $this->_template->render();
    }

    public function viewLessonDetail($ldetailId)
    {
        
        $post = [
        
            'sldetail_id' => FatUtility::int($ldetailId),
        ];
        $srch = new LessonSearch($this->siteLangId);
        $srch->joinTeacherLessonPlans();
        $srch->addSearchDetailFields();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->addFld('tlpn.tlpn_title');
        $srch->addFld('tlpn.tlpn_id');
        $srch->addFld('ut.user_deleted');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $lesson = current($srch->fetchAll());
        if (empty($lesson)) {
            FatUtility::exitWithErrorCode(404);
        }
        // $flashCardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true);
        // if ($flashCardEnabled) {
        //     $frmSrchFlashCard = $this->getLessonFlashCardSearchForm();
        //     $frmSrchFlashCard->fill(['lesson_id' => $lesson['slesson_id']]);
        //     $this->set('frmSrchFlashCard', $frmSrchFlashCard);
        // }
        $coupon_user=new SearchBase('tbl_schedule_lesson_code_join');
        $coupon_user->addCondition('schedule_lesson_id','=',$ldetailId);
        $coupon_user->addOrder('schedule_lesson_code_join', 'DESC');
      
        $rs = $coupon_user->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $latest_record=end($rows);
        $currentUserId=$latest_record['schedule_lesson_code_join'];
        $_SESSION['currentUserId']=$currentUserId;
        $this->set('userData', $latest_record);
        $this->set('lesson', $lesson);
        // $this->set('flashCardEnabled', $flashCardEnabled);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('reportHours', FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION'));
        $this->_template->render(false, false);
    }

    public function searchFlashCards()
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frmSrch = $this->getLessonFlashCardSearchForm();
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $lessonId = $post['lesson_id'];
        $srch = new FlashCardSearch(false);
        $srch->joinSharedFlashCard();
        $srch->joinLesson();
        $srch->joinWordLanguage();
        $srch->joinWordDefinitionLanguage();
        if ($lessonId) {
            $srch->addCondition('sflashcard_slesson_id', '=', $lessonId);
        }
        // $srch->addCondition('sflashcard_learner_id', '=',$_SESSION['currentUserId']);
        $srch->addOrder('flashcard_id', 'DESC');
        $srch->addMultipleFields([
            'flashcard_id',
            'flashcard_created_by_user_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
            'sflashcard_slesson_id',
        ]);
        if (!empty($post['keyword'])) {
            $srch->addCondition('flashcard_title', 'like', '%' . $post['keyword'] . '%');
        }
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('flashCards', $rows);
        $this->set('postedData', $post);
        $this->set('teacherId', $teacherId);
        $this->_template->render(false, false, 'teacher-scheduled-lessons/search-flash-cards.php');
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
        $srch->joinGroupClass($this->siteLangId);
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
      //  $srch->addCondition('sld.sldetail_learner_id', ' = ',$_SESSION['currentUserId']);
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

    public function cancelLesson($lDetailId)
    {
        $lDetailId = FatUtility::int($lDetailId);
        if (1 > $lDetailId) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $scheduledLessonObj = new ScheduledLessonSearch();
        $scheduledLessonObj->joinLearner();
        $scheduledLessonObj->joinOrder();
        $scheduledLessonObj->joinOrderProducts();
        $scheduledLessonObj->addMultipleFields(['slesson_grpcls_id', 'sldetail_learner_id', 'slesson_date', 'sldetail_order_id', 'slesson_start_time', 'op_lpackage_is_free_trial', 'sldetail_learner_status', 'order_net_amount']);
        $scheduledLessonObj->addCondition('sldetail_id', '=', $lDetailId);
        $scheduledLessonObj->addCondition('sldetail_learner_status', 'IN', [ScheduledLesson::STATUS_NEED_SCHEDULING, ScheduledLesson::STATUS_SCHEDULED]);
        $scheduledLessonObj->addCondition('sldetail_learner_id', '=',$_SESSION['currentUserId']);
        $scheduledLessonObj->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $resultSet = $scheduledLessonObj->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($resultSet);
        if (empty($lessonRow)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $lessonsStartTime = strtotime($lessonRow['slesson_date'] . " " . $lessonRow['slesson_start_time']);
        if ($lessonRow['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED && time() >= $lessonsStartTime) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $lessonRow['order_net_amount'] = FatUtility::float($lessonRow['order_net_amount']);
        $orderObj = new Order();
        $orderSearch = $orderObj->getLessonsByOrderId($lessonRow['sldetail_order_id']);
        $orderSearch->addMultipleFields([
            'slesson_grpcls_id',
            'count(sldetail_order_id) as totalLessons',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ' THEN 1 ELSE 0 END) needToscheduledLessonsCount',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_CANCELLED . ' THEN 1 ELSE 0 END) canceledLessonsCount',
        ]);
        $orderSearch->addGroupBy('sldetail_order_id');
        $resultSet = $orderSearch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($resultSet);
        if (empty($orderInfo)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $orderInfo['order_discount_total'] = FatUtility::float($orderInfo['order_discount_total']);
        $totalCanceledAndNeedToScheduledCount = $orderInfo['needToscheduledLessonsCount'] + $orderInfo['canceledLessonsCount'];
        if (!empty($orderInfo['order_discount_total']) && 0 == $orderInfo['slesson_grpcls_id'] && $orderInfo['totalLessons'] != $totalCanceledAndNeedToScheduledCount) {
            Message::addErrorMessage(Label::getLabel('LBL_YOU_ARE_NOT_CANCELLED_THE_LESSON_BECUASE_YOU_PURCHASE_THE_LESSON_WITH_COUPON'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $showCouponRefundNote = ($orderInfo['order_discount_total'] > 0) ? true : false;
        $diff = MyDate::hoursDiff($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time'], date('Y-m-d H:i:s'));
        $deductionNote = false;
        if ((ScheduledLesson::STATUS_SCHEDULED == $lessonRow['sldetail_learner_status']) && ($diff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) && ($lessonRow['order_net_amount'] > 0)) {
            $deductionNote = (applicationConstants::YES == $lessonRow['op_lpackage_is_free_trial']) ? false : true;
        }
        $frm = $this->getCancelLessonFrm($deductionNote, $showCouponRefundNote, $lessonRow['sldetail_order_id']);
        $frm->fill(['sldetail_id' => $lDetailId]);
        $this->set('frm', $frm);
        $this->set('lessonRow', $lessonRow);
        $this->set('showCouponRefundAlert', $showCouponRefundNote);
        $this->_template->render(false, false);
    }

    public function cancelLessonSetup()
    {
        $frm = $this->getCancelLessonFrm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $lDetailId = $post['sldetail_id'];
        $sLessonDetailObj = new ScheduledLessonDetails($lDetailId);
        $lessonStsLog = new LessonStatusLog($lDetailId);
        // [
        $srch = $this->searchLessons();
        $srch->joinLessonLanguage($this->siteLangId);
        $srch->joinTeacherCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('sldetail_id', '=', $lDetailId);
        $srch->addCondition('sldetail_learner_id', '=',$_SESSION['currentUserId']);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $srch->addCondition('sldetail_learner_status', 'IN', [ScheduledLesson::STATUS_NEED_SCHEDULING, ScheduledLesson::STATUS_SCHEDULED]);
        $srch->addFld([
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'ut.user_timezone as teacherTz',
            'IFNULL(tlanguage_name, tlang.tlanguage_identifier) as teacherTeachLanguageName',
            'tcred.credential_email as teacherEmailId',
            'sldetail_learner_status',
            'sldetail_order_id',
            'slesson_date',
            'order_discount_total',
            'slesson_start_time',
        ]);
        $db = FatApp::getDb();
        $db->startTransaction();
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $sessionStartTime = strtotime($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        if (ScheduledLesson::STATUS_SCHEDULED == $lessonRow['sldetail_learner_status'] && time() >= $sessionStartTime) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $orderObj = new Order();
        $orderSearch = $orderObj->getLessonsByOrderId($lessonRow['sldetail_order_id']);
        $orderSearch->addMultipleFields([
            'slesson_grpcls_id',
            'count(sldetail_order_id) as totalLessons',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ' THEN 1 ELSE 0 END) needToscheduledLessonsCount',
            'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_CANCELLED . ' THEN 1 ELSE 0 END) canceledLessonsCount',
        ]);
        $orderSearch->addGroupBy('sldetail_order_id');
        $resultSet = $orderSearch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($resultSet);
        if (empty($orderInfo)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $orderInfo['order_discount_total'] = FatUtility::float($orderInfo['order_discount_total']);
        $totalCanceledAndNeedToScheduledCount = $orderInfo['needToscheduledLessonsCount'] + $orderInfo['canceledLessonsCount'];
        if (!empty($orderInfo['order_discount_total']) && 0 == $orderInfo['slesson_grpcls_id'] && $orderInfo['totalLessons'] != $totalCanceledAndNeedToScheduledCount) {
            FatUtility::dieJsonError(Label::getLabel('LBL_You_are_not_cancelled_the_lesson_becuase_you_purchase_the_lesson_with_coupon'));
        }
        // ]
        // update lesson status[
        $sLessonDetailObj = new ScheduledLessonDetails($lessonRow['sldetail_id']);
        $sLessonDetailObj->assignValues(['sldetail_learner_status' => ScheduledLesson::STATUS_CANCELLED]);
        if (!$sLessonDetailObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonDetailObj->getError());
        }
        // remove from student google calendar
        $learnerSettings = UserSetting::getUserSettings(UserAuthentication::getLoggedUserId());
        if (!empty($learnerSettings['us_google_access_token'])) {
            $sLessonDetailObj->loadFromDb();
            $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
            if ($oldCalId) {
                SocialMedia::deleteEventOnGoogleCalendar($learnerSettings['us_google_access_token'], $oldCalId);
            }
            $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', '');
            $sLessonDetailObj->save();
        }
        // ]
        // Also update lesson status for 1 to 1[
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        if ($lessonRow['slesson_grpcls_id'] <= 0) {
            $sLessonObj->assignValues(['slesson_status' => ScheduledLesson::STATUS_CANCELLED]);
            if (!$sLessonObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
            // remove from teacher google calendar
            $settings = UserSetting::getUserSettings($lessonRow['teacherId']);
            if (!empty($settings['us_google_access_token'])) {
                $sLessonObj->loadFromDb();
                $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
                if ($oldCalId) {
                    SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
                }
                $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', '');
                $sLessonObj->save();
            }
        }
        // ]
        if (!$sLessonDetailObj->refundToLearner(true, true)) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonDetailObj->getError());
        }
        // start: saving log in new table i.e. tbl_lesson_status_log
        $lessonStsLog->addLog(ScheduledLesson::STATUS_CANCELLED, User::USER_TYPE_LEANER,$_SESSION['currentUserId'], $post['cancel_lesson_msg']);
        // End: saving log in new table i.e. tbl_lesson_status_log
        $db->commitTransaction();
        // send email to teacher[
        $start_date = $lessonRow['slesson_date'];
        $start_time = $lessonRow['slesson_start_time'];
        $end_time = $lessonRow['slesson_end_time'];
        $user_timezone = $lessonRow['teacherTz'];
        if ($start_time) {
            $start_time = $start_date . ' ' . $start_time;
            $end_time = $start_date . ' ' . $end_time;
            $start_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $start_time, true, $user_timezone);
            $start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $start_time, true, $user_timezone);
            $end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $end_time, true, $user_timezone);
        }
        $learnerId =$_SESSION['currentUserId'];
        $userNotification = new UserNotifications($lessonRow['teacherId']);
        $userNotification->cancelLessonNotification($lessonRow['slesson_id'], $learnerId, $lessonRow['learnerFullName'], USER::USER_TYPE_TEACHER, $post['cancel_lesson_msg']);
        $vars = [
            '{learner_name}' => $lessonRow['learnerFullName'],
            '{teacher_name}' => $lessonRow['teacherFullName'],
            '{lesson_name}' => (applicationConstants::NO == $lessonRow['is_trial']) ? $lessonRow['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial', $this->siteLangId),
            '{learner_comment}' => $post['cancel_lesson_msg'],
            '{lesson_date}' => FatDate::format($start_date),
            '{lesson_start_time}' => $start_time,
            '{lesson_end_time}' => $end_time,
            '{action}' => Label::getLabel('VERB_Canceled', $this->siteLangId),
        ];
        EmailHandler::sendMailTpl($lessonRow['teacherEmailId'], 'learner_cancelled_email', $this->siteLangId, $vars);
        // ]
        $isGroupClass = ($lessonRow['slesson_grpcls_id'] > 0) ? applicationConstants::YES : applicationConstants::NO;
        $returnData = ['msg' => Label::getLabel('LBL_Lesson_Cancelled_Successfully!'), 'isGroupClass' => $isGroupClass];
        if ($lessonRow['order_discount_total'] > 0) {
            $returnData['redirectUrl'] = CommonHelper::generateUrl('Classes');
        }
        FatUtility::dieJsonSuccess($returnData);
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
        $getLessonDetailObj->addCondition('slesson_grpcls_id', '=', 0);
        $getLessonDetailObj->addCondition('sldetail_learner_id', '=',$_SESSION['currentUserId']);
        $getLessonDetailObj->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $getLessonDetailObj->addMultipleFields([
            'uts.us_booking_before as teacherBookingBefore',
            'ut.user_country_id as teacherCountryId',
            'ut.user_first_name as teacherFirstName',
            'op_lpackage_is_free_trial',
            'op_lesson_duration',
        ]);
        $getLessonDetailObj->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $getResultSet = $getLessonDetailObj->getResultSet();
        $data = $db->fetch($getResultSet);
        if (empty($data)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $hoursDiff = MyDate::hoursDiff($data['slesson_date'] . ' ' . $data['slesson_start_time']);
        if ($hoursDiff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
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
        $this->_template->render(false, false, 'learner-scheduled-lessons/view-booking-calendar.php');
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
        $srch->joinTeacherSettings();
        $srch->doNotCalculateRecords();
        $srch->addCondition('sldetail_id', '=', $lDetailId);
        $srch->addCondition('slesson_grpcls_id', '=', 0);
        $srch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        $srch->addFld(['us_booking_before as teacherBookingBefore', 'ut.user_country_id as teacherCountryId']);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        // if (!$lessonRow || $lessonRow['learnerId'] != UserAuthentication::getLoggedUserId()) {
        //     Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
        //     FatUtility::dieWithError(Message::getHtml());
        // }
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
        $getLessonDetailObj->addCondition('slesson_grpcls_id', '=', 0);
        $getLessonDetailObj->addCondition('sldetail_learner_id', '=',$_SESSION['currentUserId']);
        $getLessonDetailObj->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $getLessonDetailObj->addCondition('sldetail_learner_status', 'IN', $lessonsStatus);
        $getLessonDetailObj->addMultipleFields([
            'uts.us_booking_before as teacherBookingBefore',
            'ut.user_country_id as teacherCountryId',
            'ut.user_first_name as teacherFirstName',
            'op_lpackage_is_free_trial',
            'tcred.credential_email as teacherEmailId',
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
        if ((ScheduledLesson::STATUS_SCHEDULED == $lessonDetail['sldetail_learner_status'])) {
            if ((empty($isRescheduleRequest) || empty($rescheduleReason))) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $hoursDiff = MyDate::hoursDiff($lessonDetail['slesson_date'] . ' ' . $lessonDetail['slesson_start_time']);
            if ($hoursDiff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
        }
        
       
        $teacher_id = $lessonDetail['teacherId'];
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $startTime = MyDate::changeDateTimezone($post['startTime'], $user_timezone, $systemTimeZone);
        $endTime = MyDate::changeDateTimezone($post['endTime'], $user_timezone, $systemTimeZone);
        $teacherBookingBefore = FatUtility::int($lessonDetail['teacherBookingBefore']);
        $validDate = date('Y-m-d H:i:s', strtotime('+' . $teacherBookingBefore . 'hours', strtotime(date('Y-m-d H:i:s'))));
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

        $userIds = [$teacher_id];
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
        $action = Label::getLabel('VERB_Scheduled', $this->siteLangId);
        if (ScheduledLesson::STATUS_SCHEDULED == $lessonDetail['sldetail_learner_status'] && $rescheduleReason) {
            $lessonResLogArr = [
                'lesreschlog_slesson_id' => $lessonDetail['slesson_id'],
                'lesreschlog_reschedule_by' =>$_SESSION['currentUserId'],
                'lesreschlog_user_type' => User::USER_TYPE_LEANER,
                'lesreschlog_comment' => $rescheduleReason,
            ];
            $lessonResLogObj = new LessonRescheduleLog();
            $lessonResLogObj->assignValues($lessonResLogArr);
            if (!$lessonResLogObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($lessonResLogObj->getError());
            }
            $lessonStsLog->addLog(ScheduledLesson::STATUS_SCHEDULED, User::USER_TYPE_LEANER,$_SESSION['currentUserId'], $rescheduleReason);
            $action = Label::getLabel('VERB_Rescheduled', $this->siteLangId);
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
        EmailHandler::sendMailTpl($lessonDetail['teacherEmailId'], 'learner_schedule_email', $this->siteLangId, $vars);
        // share on student google calendar
        $settings = UserSetting::getUserSettings(UserAuthentication::getLoggedUserId());
        if (!empty($settings['us_google_access_token'])) {
            $sLessonDetailObj->loadFromDb();
            $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
            if ($oldCalId) {
                SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
            }
            $view_url = CommonHelper::generateFullUrl('Classes', 'view', [$lessonDetail['sldetail_id']]);
            $title = sprintf(Label::getLabel('LBL_%1$s_LESSON_%2$s_with_%3$s'), (applicationConstants::NO == $lessonDetail['op_lpackage_is_free_trial'] ? $lessonDetail['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial', $this->siteLangId)), $action, $lessonDetail['teacherFullName']);
            $google_cal_data = [
                'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId),
                'summary' => $title,
                'description' => sprintf(Label::getLabel('LBL_Click_here_to_join_the_lesson:_%s'), $view_url),
                'url' => $view_url,
                'start_time' => date('c', $SelectedDateTimeStamp),
                'end_time' => date('c', $endDateTimeStamp),
                'timezone' => MyDate::getTimeZone(),
            ];
            // CommonHelper::printArray($google_cal_data);die;
            $calId = SocialMedia::addEventOnGoogleCalendar($settings['us_google_access_token'], $google_cal_data);
            if ($calId) {
                $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', $calId);
                $sLessonDetailObj->save();
            }
        }
        // share on teacher google calendar
        $settings = UserSetting::getUserSettings($lessonDetail['teacherId']);
        if (!empty($settings['us_google_access_token'])) {
            $sLessonObj->loadFromDb();
            $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
            if ($oldCalId) {
                SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
            }
            $view_url = CommonHelper::generateFullUrl('TeacherScheduledLessons', 'view', [$lessonDetail['slesson_id']]);
            $title = sprintf(Label::getLabel('LBL_%1$s_LESSON_Scheduled_by_%2$s'), (applicationConstants::NO == $lessonDetail['op_lpackage_is_free_trial'] ? $lessonDetail['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial', $this->siteLangId)), $lessonDetail['learnerFullName']);
            $google_cal_data = [
                'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId),
                'summary' => $title,
                'description' => sprintf(Label::getLabel('LBL_Click_here_to_deliver_the_lesson:_%s'), $view_url),
                'url' => $view_url,
                'start_time' => date('c', $SelectedDateTimeStamp),
                'end_time' => date('c', $endDateTimeStamp),
                'timezone' => MyDate::getTimeZone(),
            ];
            $calId = SocialMedia::addEventOnGoogleCalendar($settings['us_google_access_token'], $google_cal_data);
            if ($calId) {
                $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', $calId);
                $sLessonObj->save();
            }
        }
        $userNotification = new UserNotifications($lessonDetail['teacherId']);
        $userNotification->sendSchLessonByLearnerNotification($lessonDetail['slesson_id']);
        $msg = 'LBL_LESSON_SCHEDULED_SUCCESSFULLY';
        if ($isRescheduleRequest) {
            $msg = 'LBL_LESSON_RESCHEDULED_SUCCESSFULLY';
        }
        FatUtility::dieJsonSuccess(Label::getLabel($msg));
    }

    public function viewAssignedLessonPlan($lDetailId)
    {
        $lDetailId = FatUtility::int($lDetailId);
        if (1 > $lDetailId) {
            FatUtility::exitWithErrorCode(404);
        }
        $lessonDetailRow = ScheduledLessonDetails::getAttributesById($lDetailId, ['sldetail_id', 'sldetail_slesson_id', 'sldetail_learner_id']);
        if (!$lessonDetailRow || $lessonDetailRow['sldetail_id'] != $lDetailId) {
            FatUtility::exitWithErrorCode(404);
        }
        $lessonId = $lessonDetailRow['sldetail_slesson_id'];
        $lessonRow = ScheduledLesson::getAttributesById($lessonId, ['slesson_id', 'slesson_teacher_id']);
        if (!$lessonRow || $lessonRow['slesson_id'] != $lessonId) {
            FatUtility::exitWithErrorCode(404);
        }
        $lessonId = FatUtility::int($lessonId);
        $srch = new LessonPlanSearch(false);
        $srch->addMultipleFields([
            'tlpn_id',
            'tlpn_title',
            'tlpn_level',
            'tlpn_user_id',
            'tlpn_description',
        ]);
        $srch->joinTable('tbl_scheduled_lessons_to_teachers_lessons_plan', 'inner join', 'tlpn_id = ltp_tlpn_id');
        $srch->addCondition('ltp_slessonid', '=', $lessonId);
        $rows = FatApp::getDb()->fetch($srch->getResultSet());
        $this->set('statusArr', LessonPlan::getDifficultyArr());
        $this->set('data', $rows);
        $this->_template->render(false, false);
    }

    public function lessonFeedback($lDetailId)
    {
        $lDetailId = FatUtility::int($lDetailId);
        if (1 > $lDetailId) {
            Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS', $this->siteLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $error = '';
        if (!$this->getLessonToFeedback($lDetailId, UserAuthentication::getLoggedUserId(), $error)) {
            Message::addErrorMessage($error);
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('frm', $this->getLessonFeedbackForm($lDetailId));
        $this->_template->render(false, false);
    }

    public function setupLessonFeedback()
    {
        $lDetailId = FatApp::getPostedData('sldetail_id', FatUtility::VAR_INT, 0);
        if (1 > $lDetailId) {
            FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $frm = $this->getLessonFeedbackForm($lDetailId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $userId = UserAuthentication::getLoggedUserId();
        $error = '';
        if (!$lessonData = $this->getLessonToFeedback($lDetailId, $userId, $error)) {
            FatUtility::dieJsonError($error);
        }
        $lessonId = $lessonData['slesson_id'];
        $post['tlreview_teacher_user_id'] = $lessonData['teacherId'];
        $post['tlreview_lesson_id'] = $lessonId;
        $post['tlreview_postedby_user_id'] = $userId;
        $post['tlreview_posted_on'] = date('Y-m-d H:i:s');
        $post['tlreview_lang_id'] = $this->siteLangId;
        $post['tlreview_status'] = FatApp::getConfig('CONF_DEFAULT_REVIEW_STATUS', FatUtility::VAR_INT, 0);
        $selProdReview = new TeacherLessonReview();
        $selProdReview->assignValues($post);
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$selProdReview->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($selProdReview->getError());
        }
        $spreviewId = $selProdReview->getMainTableRecordId();
        $ratingsPosted = FatApp::getPostedData('review_rating');
        $ratingAspects = TeacherLessonRating::getRatingAspectsArr($this->siteLangId);
        foreach ($ratingsPosted as $ratingAspect => $ratingValue) {
            if (isset($ratingAspects[$ratingAspect])) {
                $teachLessonRating = new TeacherLessonRating();
                $ratingRow = [
                    'tlrating_tlreview_id' => $spreviewId,
                    'tlrating_rating_type' => $ratingAspect,
                    'tlrating_rating' => $ratingValue,
                ];
                $teachLessonRating->assignValues($ratingRow);
                if (!$teachLessonRating->save()) {
                    FatUtility::dieJsonError($teachLessonRating->getError());
                    $db->rollbackTransaction();
                }
            }
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_FEEDBACK_SUBMITTED_SUCCESSFULLY'));
    }

    public function viewFlashCard($flashcardId)
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $flashcardId = FatUtility::int($flashcardId);
        $srch = new FlashCardSearch();
        $srch->joinSharedFlashCard();
        $srch->joinLesson();
        $srch->joinWordLanguage();
        $srch->joinWordDefinitionLanguage();
        $srch->addCondition('sflashcard_flashcard_id', '=', $flashcardId);
        $srch->addCondition('sflashcard_learner_id', '=',$_SESSION['currentUserId']);
        $srch->addOrder('flashcard_id', 'DESC');
        $srch->addMultipleFields([
            'flashcard_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
            'flashcard_notes',
            'flashcard_added_on',
        ]);
        $srch->setPageSize(1);
        $rsFlashcard = $srch->getResultSet();
        $flashCard = FatApp::getDb()->fetch($rsFlashcard);
        if (empty($flashCard)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('flashCardData', $flashCard);
        $this->_template->render(false, false);
    }

    public function removeFlashcard($flashCardId)
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $flashCardId = FatUtility::int($flashCardId);
        // validation [
        $srch = new FlashCardSearch();
        $srch->joinSharedFlashCard();
        $srch->joinLesson();
        $srch->addCondition('sflashcard_learner_id', '=',$_SESSION['currentUserId']);
        $srch->addCondition('flashcard_id', '=', $flashCardId);
        $srch->addMultipleFields([
            'flashcard_id',
            'sflashcard_learner_id',
            'sflashcard_teacher_id',
        ]);
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        // ]
        $db = FatApp::getDb();
        if (!$db->deleteRecords(FlashCard::DB_TBL_SHARED, ['smt' => 'sflashcard_flashcard_id = ?', 'vals' => [$row['flashcard_id']]])) {
            FatUtility::dieJsonError($db->getError());
        }
        if (!$db->deleteRecords(FlashCard::DB_TBL, ['smt' => 'flashcard_id = ? ', 'vals' => [$row['flashcard_id']]])) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Deleted_Successfully!'));
    }

    public function flashCardForm()
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $flashCardId = FatApp::getPostedData('flashcardId', FatUtility::VAR_INT, 0);
        $lessonId = $post['lessonId'];
        if (1 > $lessonId) {
            FatUtility::exitWithErrorCode(404);
        }
        $lessonRow = ScheduledLesson::getAttributesById($lessonId, ['slesson_id']);
        if (!$lessonRow || $lessonRow['slesson_id'] != $lessonId) {
            FatUtility::exitWithErrorCode(404);
        }
        $frm = $this->getFlashcardFrm();
        $frmData['slesson_id'] = $lessonId;
        if ($flashCardId > 0) {
            $frmData = $frmData + (array) FlashCard::getAttributesById($flashCardId);
        }
        $frm->fill($frmData);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setupFlashCard()
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $frm = $this->getFlashcardFrm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $lessonId = $post['slesson_id'];
        $lessonRow = ScheduledLesson::getAttributesById($lessonId, ['slesson_id', 'slesson_teacher_id']);
        if (!$lessonRow || $lessonRow['slesson_id'] != $lessonId) {
            FatUtility::exitWithErrorCode(404);
        }
        $flashCardId = $post['flashcard_id'];
        $post['flashcard_user_id'] =$_SESSION['currentUserId'];
        $post['flashcard_created_by_user_id'] =$_SESSION['currentUserId'];
        $flashCardObj = new FlashCard($flashCardId);
        $flashCardObj->assignValues($post);
        if (!$flashCardObj->save($post)) {
            FatUtility::dieJsonError($flashCardObj->getError());
        }
        if (0 == $flashCardId) {
            $db = FatApp::getDb();
            $flashcardId = $flashCardObj->getMainTableRecordId();
            $db->insertFromArray(FlashCard::DB_TBL_SHARED, [
                'sflashcard_flashcard_id' => $flashcardId,
                'sflashcard_learner_id' =>$_SESSION['currentUserId'],
                'sflashcard_teacher_id' => $lessonRow['slesson_teacher_id'],
                'sflashcard_slesson_id' => $lessonId,
            ]);
            if ($db->getError()) {
                FatUtility::dieJsonError($db->getError());
            }
        }
        $this->set('msg', Label::getLabel('LBL_Flashcard_Saved_Successfully!'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function endLessonSetup()
    {
        $lDetailId = FatApp::getPostedData('lDetailId', FatUtility::VAR_INT, 0);
        if ($lDetailId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $srch = $this->searchLessons();
        $srch->joinTeacherCredentials();
        $srch->doNotCalculateRecords();
        $srch->addCondition('sldetail_id', '=', $lDetailId);
        $srch->addCondition('sldetail_learner_status', 'IN', [ScheduledLesson::STATUS_COMPLETED, ScheduledLesson::STATUS_SCHEDULED]);
        $srch->addCondition('mysql_func_CONCAT(slesson_date," ", slesson_start_time)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('slesson_teacher_join_time', '>=', 'mysql_func_CONCAT(slesson_date," ", slesson_start_time)', 'AND', true);
        $srch->addCondition('sldetail_learner_join_time', '>=', 'mysql_func_CONCAT(slesson_date," ", slesson_start_time)', 'AND', true);
        $srch->addMultipleFields(['slesson_teacher_end_time', 'slesson_ended_by', 'sldetail_learner_end_time']);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if (ScheduledLesson::STATUS_COMPLETED == $lessonRow['sldetail_learner_status']) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_ALREADY_ENDED_BY_YOU/TEACHER!'));
        }
        if (ScheduledLesson::STATUS_SCHEDULED != $lessonRow['sldetail_learner_status']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        if (0 == $lessonRow['slesson_grpcls_id']) {
            $dataUpdateArr = [
                'slesson_status' => ScheduledLesson::STATUS_COMPLETED,
                'slesson_ended_by' => User::USER_TYPE_LEANER,
                'slesson_ended_on' => date('Y-m-d H:i:s'),
                'slesson_teacher_end_time' => date('Y-m-d H:i:s'),
            ];
            $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
            $sLessonObj->assignValues($dataUpdateArr);
            if (!$sLessonObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
        }
        $lessionDetailUpdateArr = [
            'sldetail_learner_status' => ScheduledLesson::STATUS_COMPLETED,
            'sldetail_learner_end_time' => date('Y-m-d H:i:s'),
        ];
        $sLessonDetailObj = new ScheduledLessonDetails($lessonRow['sldetail_id']);
        $sLessonDetailObj->assignValues($lessionDetailUpdateArr);
        if (!$sLessonDetailObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonDetailObj->getError());
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_ENDED_SUCCESSFULLY!'));
    }

    public function checkEveryMinuteStatus($lDetailId)
    {
        $srch = new ScheduledLessonSearch();
        $srch->addMultipleFields([
            'IF(slns.slesson_teacher_join_time>0, 1, 0) as has_teacher_joined',
            'IF(sld.sldetail_learner_join_time>0, 1, 0) as has_learner_joined',
            'sld.sldetail_learner_status',
            'slns.slesson_status',
        ]);
        $srch->addCondition('sld.sldetail_learner_id', '=',$_SESSION['currentUserId']);
        $srch->addCondition('sld.sldetail_id', '=', $lDetailId);
        $srch->addCondition('slns.slesson_date', '<=', date('Y-m-d'));
        $srch->addCondition('slns.slesson_start_time', '<=', date('H:i:s'));
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        echo FatUtility::convertToJson($data);
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
        if (empty($startTime) || empty($endTime) || empty($teacherId) || empty($date)) {
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

    public function startLesson()
    {
        $lDetailId = FatApp::getPostedData('lDetailId', FatUtility::VAR_INT, 0);
        if ($lDetailId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        // validate lesson, if it can be started
        $lessonData = $this->getStartedLessonDetails($lDetailId);
       
        
        if (empty($lessonData)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        // check if teacher has joined
        if ($lessonData['slesson_teacher_join_time'] == '0000-00-00 00:00:00') {
            FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_WAIT._LET_TEACHER_JOIN'));
        }
        // get meeting details
        $lessonData['fromClasses']=1;
        try {
            $lesMettings = new LessonMeetings();
            $meetingData = $lesMettings->getMeetingData($lessonData);
        } catch (Exception $e) {
            CommonHelper::dieJsonError($e->getMessage());
        }
        // update learner join time
        if ($lessonData['sldetail_learner_join_time'] == '0000-00-00 00:00:00') {
            $schLessonDetails = new ScheduledLessonDetails($lDetailId);
            if (!$schLessonDetails->markLearnerJoinTime()) {
                CommonHelper::dieJsonError($schLessonDetails->getError());
            }
        }
        CommonHelper::dieJsonSuccess(['data' => $meetingData, 'msg' => Label::getLabel('LBL_Joining._Please_Wait...')]);
    }

    private function searchLessons($post = [], $getCancelledOrder = false, $addLessonDateOrder = true)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinGroupClass($this->siteLangId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
      //  $srch->joinLearner();
        $srch->joinTeacherCountry($this->siteLangId);
        $orderIsPaidCondition = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        if ($getCancelledOrder) {
            $orderIsPaidCondition->attachCondition('order_is_paid', '=', Order::ORDER_IS_CANCELLED, 'OR');
        }
        // $srch->addCondition('sldetail_learner_id', '=',$_SESSION['currentUserId']);
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

    private function getCancelLessonFrm($deductionNote = false, $showCouponRefundNote = false, $orderId = '')
    {
        $frm = new Form('cancelFrm');
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'cancel_lesson_msg', '');
        if ($deductionNote && !$showCouponRefundNote) {
            $frm->addHtml('', 'note_text', '<spam class="-color-primary">' . sprintf(Label::getLabel('LBL_Note:_Refund_Would_Be_%s_Percent.', $this->siteLangId), FatApp::getConfig('CONF_LEARNER_REFUND_PERCENTAGE', FatUtility::VAR_INT, 10)) . '</spam>');
        }
        if ($showCouponRefundNote) {
            $label = Label::getLabel('LBL_Note:_If_you_cancelled_this_lesson_you_total_order_will_cancelled_becuase_you_puchase_this_order_with_discount.', $this->siteLangId);
            $label .= '<br> ' . sprintf(Label::getLabel('LBL_Order_Id:_%s'), $orderId);
            $frm->addHtml('', 'coupon_refund_note_text', '<spam class="-color-primary">' . $label . '</spam>');
        }
        $fld->requirement->setRequired(true);
        $fld = $frm->addHiddenField('', 'sldetail_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive();
        $submitBtn = $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Approve'));
        $cancelBtn = $frm->addResetButton('', 'reset', Label::getLabel('LBL_Cancel'));
        $submitBtn->attachField($cancelBtn);
        return $frm;
    }

    private function getLessonFeedbackForm($sldetailId)
    {
        $frm = new Form('frmLessonFeedback');
        $ratingAspects = TeacherLessonRating::getRatingAspectsArr();
        foreach ($ratingAspects as $aspectVal => $aspectLabel) {
            $fld = $frm->addSelectBox($aspectLabel, "review_rating[{$aspectVal}]", ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'], '', ['class' => 'star-rating'], Label::getLabel('L_Rate'));
            $fld->requirements()->setRequired(true);
            $fld->setWrapperAttribute('class', 'rating-f');
        }
        $frm->addRequiredField(Label::getLabel('LBL_Title'), 'tlreview_title');
        $frm->addTextArea(Label::getLabel('LBL_Description'), 'tlreview_description')->requirements()->setRequired();
        // $frm->addHiddenField('', 'tlreview_lesson_id', $lessonId);
        $frm->addHiddenField('', 'sldetail_id', $sldetailId);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Send_Review'));
        return $frm;
    }

    private function getFlashcardFrm()
    {
        $frm = new Form('flashcardFrm');
        $frm->addRequiredField(Label::getLabel('LBL_Title'), 'flashcard_title');
        $langArr = SpokenLanguage::getAllLangs();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Title_Language'), 'flashcard_slanguage_id', $langArr);
        $fld->requirements()->setRequired(true);
        $frm->addRequiredField(Label::getLabel('LBL_Defination'), 'flashcard_defination');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Defination_Language'), 'flashcard_defination_slanguage_id', $langArr);
        $fld->requirements()->setRequired(true);
        $frm->addTextBox(Label::getLabel('LBL_Pronunciation'), 'flashcard_pronunciation');
        $fld = $frm->addHiddenField('', 'flashcard_id', 0);
        $fld->requirements()->setInt();
        $fld = $frm->addHiddenField('', 'slesson_id', 0);
        $fld->requirements()->setInt();
        $frm->addTextArea(Label::getLabel('LBL_Notes'), 'flashcard_notes');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

    private function getStartedLessonDetails($lDetailId)
    {
        $srch = $this->searchLessons();
       // $srch->joinLearnerCredentials();
        $srch->addMultipleFields([
            'slns.slesson_teacher_join_time',
            'sld.sldetail_learner_join_time',
            // 'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            // 'lcred.credential_email as learnerEmail',
        ]);
       $srch->addCondition('sld.sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch->addCondition('mysql_func_CONCAT(slesson_date," ", slesson_start_time)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('mysql_func_CONCAT(slesson_end_date," ", slesson_end_time)', '>', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('sld.sldetail_id', '=', $lDetailId);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    private function getLessonToFeedback(int $lDetailId, int $userId, &$error = '')
    {
        $srch = $this->searchLessons();
        $srch->joinTeacherCredentials();
        $srch->doNotCalculateRecords();
        $srch->addCondition('sldetail_id', '=', $lDetailId);
        $srch->addCondition('sldetail_learner_id', '=', $userId);
        $srch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_COMPLETED);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (empty($lessonRow)) {
            $error = Label::getLabel('MSG_ERROR_INVALID_ACCESS', $this->siteLangId);
            return false;
        }
        $lessonId = $lessonRow['slesson_id'];
        $lFeedbackSrch = new TeacherLessonReviewSearch();
        $lFeedbackSrch->doNotCalculateRecords();
        $lFeedbackSrch->doNotLimitRecords();
        $lFeedbackSrch->addCondition('tlreview_postedby_user_id', '=', $userId);
        $lFeedbackSrch->addCondition('tlreview_lesson_id', '=', $lessonId);
        $lFeedbackRs = $lFeedbackSrch->getResultSet();
        if (FatApp::getDb()->fetch($lFeedbackRs)) {
            $error = Label::getLabel('MSG_ALREADY_SUBMITTED_LESSON_FEEDBACK', $this->siteLangId);
            return false;
        }
        return $lessonRow;
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        FatApp::redirectUser(CommonHelper::generateUrl('classes'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_Access_code', $langId), 'code', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

	public function codeSubmit()
    {
        
        $frm = $this->codeForm($this->siteLangId);
        $post =FatApp::getPostedData();

		$srch=new SearchBase('tbl_scheduled_lessons');
		$post['lesson_id']=$_SESSION['lesson_id'];
		$srch->addCondition('slesson_learner_code','=',$post['code']);
		$srch->addCondition('slesson_id','=',$post['lesson_id']);
		$rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetch($rs);
      
        if(empty($rows) || $rows['slesson_id']<=0){
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access_code', $this->siteLangId));
            // CommonHelper::redirectUserReferer();   
            FatUtility::dieWithError(Message::getHtml());
            // $this->set('redirectUrl', CommonHelper::generateUrl('Classes/?lesson_id=', [$lessonId]));
            // $this->set('msg', Label::getLabel("MSG_Invalid_Access_code", $this->siteLangId));
        }

		if($rows['slesson_learner_code_status']>0){
            $sLessonDetailSrch = new ScheduledLessonDetailsSearch();
            $srchDetails=new SearchBase('tbl_scheduled_lesson_details');
            $srchDetails->addCondition('sldetail_slesson_id','=',$post['lesson_id']);
            $rsDetails = $srchDetails->getResultSet();
            $rowsDetails = FatApp::getDb()->fetch($rsDetails);
            $available_code=$rows['slesson_learner_code_status']-1;
			$db = FatApp::getDb();
        	$db->startTransaction();
		 	$sLessonSrch = new ScheduledLesson($rows['slesson_id']);
			$sLessonSrch->assignValues(array('slesson_learner_code_status' => $available_code));
			$sLessonSrch->save();
		    $db->commitTransaction();
            $lessonId=$rowsDetails['sldetail_id'];
             $db = FatApp::getDb();
            $db->startTransaction();
           
            $data['employee_first_name']=$post['fName'];
            $data['employee_last_name']=$post['lName'];
            $data['schedule_lesson_id']=$rowsDetails['sldetail_id'];
             $record = new TableRecord('tbl_schedule_lesson_code_join');
            $record->assignValues($data);
             if (!$record->addNew([], ['employee_last_name' => $post['lastname']])) {
                Message::addErrorMessage($record->getError());
            }
            $db->commitTransaction();
            // Message::addSuccessMessage(Label::getLabel('MSG_Access_code_Verified', $this->siteLangId));
            // CommonHelper::redirectUserReferer();   
        
          
            $this->set('redirectUrl', CommonHelper::generateUrl('Classes', 'view', [$lessonId]));
            $this->set('msg', Label::getLabel("MSG_Access_Code_Verified", $this->siteLangId));
            // FatUtility::dieWithError(Message::getHtml());
            $this->_template->render(false, false, 'json-success.php');
		}
        else if($rows['slesson_learner_code_status']<=0){
            Message::addErrorMessage(Label::getLabel('MSG_Access_Code_No_Longer_available', $this->siteLangId)); 
            FatUtility::dieWithError(Message::getHtml());
            // CommonHelper::redirectUserReferer();
            $this->set('msg', Label::getLabel("MSG_Access_Code_No_Longer_available", $this->siteLangId));
        }
        // $this->set('redirectUrl', CommonHelper::generateUrl('Classes', 'view', [$lessonId]));
        
        //  $this->_template->render(false, false, 'json-success.php');
    }

	 private function codeForm(int $langId)
    {
        $frm = new Form('frmCode');
		$frm->addRequiredField(Label::getLabel('LBL_First_Name', $langId), 'firstname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name', $langId), 'lastname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Access_code', $langId), 'code', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_Apply_Code', $langId));
        return $frm;
		
    }
	 public function employeeAcccessCode()
    {
        $this->set('codeFrm', $this->codeForm($this->siteLangId));
    //    $this->_template->render(false, false);
        $this->_template->render(false, false, 'classes/employee-access-code.php');
	}
	   
	   
     

}
