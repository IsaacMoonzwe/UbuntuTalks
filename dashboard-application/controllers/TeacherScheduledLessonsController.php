<?php

class TeacherScheduledLessonsController extends TeacherBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->_template->addJs('js/jquery-confirm.min.js');
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm();
        $this->set('frmSrch', $frmSrch);
        $lessonStatuses = ScheduledLesson::getStatusArr();
        $lessonStatuses += array(ScheduledLesson::STATUS_ISSUE_REPORTED => Label::getLabel('LBL_Issue_Reported'));
        $srch = $this->searchLessons(['status' => ScheduledLesson::STATUS_UPCOMING], false, false);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addOrder('CONCAT(slns.slesson_date, " ", slns.slesson_start_time)', 'ASC');
        $upcomingLesson = FatApp::getDb()->fetch($srch->getResultSet());
        $this->set('lessonStatuses', $lessonStatuses);
        $this->set('upcomingLesson', $upcomingLesson);
        $this->set('setMonthAndWeekName', true);
        $this->_template->addJs('js/teacherLessonCommon.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->render();
    }
   
    public function joinnow(){
        $schecule_id = FatApp::getPostedData('myArrfay', FatUtility::VAR_INT, 0);
        // $schecule_id = FatApp::getPostedData('grpclsId', FatUtility::VAR_INT, 0);
        $srch_lesson = new SearchBase('tbl_scheduled_lessons');
        $srch_lesson->addCondition('slesson_id',"=",$schecule_id);
        $rs_lesson = $srch_lesson->getResultSet();
        $langData = FatApp::getDb()->fetchAll($rs_lesson);
        $lastDataIndex=sizeOf($langData);
        $schedule=end($langData);

        $newClass=new SearchBase('tbl_scheduled_lessons');
        $newClass->addCondition('slesson_grpcls_id',"=",$schedule['slesson_grpcls_id']);
        $newClass->addCondition('slesson_status',"=",2);
        $new_rs_lesson = $newClass->getResultSet();
        $new_class_Data = FatApp::getDb()->fetchAll($new_rs_lesson);
        $schedule_new=$new_class_Data[0];
        $sLessonObj = new ScheduledLesson($schedule_new['slesson_id']);
        $sLessonObj->loadFromDb();
        $sLessonObj->setFldValue('slesson_status', 1);
        $sLessonObj->save();

        $slDetailsObj = new ScheduledLessonDetails();
                      
        $slDetailsObj->addCondition('sldetail_slesson_id',"=",$schedule_new['slesson_id']);
        $new_details_lesson = $slDetailsObj->getResultSet();
        $new_detail_class_Data = FatApp::getDb()->fetchAll($new_details_lesson);
        $schedule_details_data=$new_detail_class_Data[0];


        $sLessonDetailObj = new ScheduledLessonDetails($schedule_details_data['sldetail_id']);
        $sLessonDetailObj->loadFromDb();
        $sLessonDetailObj->setFldValue('sldetail_learner_status', 1);
        $sLessonDetailObj->save();
        // $class = FatApp::getPostedData('class');
        

    }



    public function joinnow_new(){
     $defaultSiteLangId = FatApp::getConfig('conf_default_site_lang');
      
        $schecule_id = FatApp::getPostedData('myArrfay', FatUtility::VAR_INT, 0);
        // $schecule_id = FatApp::getPostedData('grpclsId', FatUtility::VAR_INT, 0);
        $srch_lesson = new SearchBase('tbl_scheduled_lessons');
        $srch_lesson->addCondition('slesson_id',"=",$schecule_id);
        $rs_lesson = $srch_lesson->getResultSet();
        $langData = FatApp::getDb()->fetchAll($rs_lesson);
        $lastDataIndex=sizeOf($langData);
        $schedule=end($langData);
        // $class = FatApp::getPostedData('class');
        $srch_data = new SearchBase('tbl_talkkids_classes');
        $srch_data->addCondition('grpcls_id',"=",$schedule['slesson_grpcls_id']);
        $rs_data = $srch_data->getResultSet();
        $kidsData = FatApp::getDb()->fetchAll($rs_data);
        $lastKidsDataIndex=sizeOf($kidsData);
        $kidsLast=end($kidsData);
    
        $totalLesson=(int)$kidsLast['grpcls_total_lesson'];
        $remianLesson=$totalLesson-$lastDataIndex;
        $week_dates=array();
      
        $week_days=explode(',',$kidsLast['grpcls_weeks']);
        // $next_date=date('Y-m-d',strtotime($kidsLast['grpcls_start_datetime']));
    //    array_push($week_dates,$next_date); 
        foreach($week_days as $weeks){
          
           $next_date=date('Y-m-d',strtotime('next '.$weeks,strtotime($schedule['slesson_date'])));
          
            array_push($week_dates,$next_date); 
       }
        function sortFunction( $a, $b ) {
            return strtotime($a) - strtotime($b);
        }
        
        // }
            foreach($week_days as $weeks){
                for($m=0;$m<$remianLesson;$m++){
          
                $next_date=date('Y-m-d',strtotime('+'.$m.$weeks,strtotime($schedule['slesson_date'])));
              
                array_push($week_dates,$next_date); 
                }
          
            }
			
            // echo "<pre>";
           array_unique($week_dates);
			$id=$schedule['slesson_id'];
			$k=0;
			$new_lessons=array();
            foreach ($week_dates as $days) {
			if($k<$remianLesson){
			if($k>0){
			$schedule['slesson_status']=ScheduledLesson::STATUS_NEED_SCHEDULING;
			}
			
            $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $days, true, $userTimezone);  
            
            unset($schedule['slesson_id']);
            
  
            $schedule['slesson_date']=$startDateTime;
            $schedule['slesson_end_date']=$startDateTime;
            $sLessonObj = new ScheduledLesson();
            $sLessonObj->assignValues($schedule);
            if (!$sLessonObj->save()) {
                $this->error = $sLessonObj->getError();
                return false;
            }
            $slesson_id = $sLessonObj->getMainTableRecordId();
            $srch_detail = new SearchBase('tbl_scheduled_lesson_details');
            $srch_detail->addCondition('sldetail_slesson_id',"=",$id);
            $rs_Detail = $srch_detail->getResultSet();
            $detailData = FatApp::getDb()->fetchAll($rs_Detail);
            $d_data=end($detailData); 
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
			
			
				$k++;
				}
       
        }
    
           
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        $referer = CommonHelper::redirectUserReferer(true);
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $sortOrder = '';
        $page = $post['page'] ?? 1;
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE');
        $userId = UserAuthentication::getLoggedUserId();
        $post = array_merge($post, ['slesson_teacher_id' => $userId]);
        $srch = new LessonSearch($this->siteLangId, ReportedIssue::USER_TYPE_TEACHER);
        $srch->joinTeacherLessonPlans();
        $srch->addSearchListingFields();
        $srch->applySearchConditions($post);
        $srch->addFld('tlpn.tlpn_title');
        $srch->addFld('tlpn.tlpn_id');
        $srch->applyOrderBy($sortOrder);
        $srch->addGroupBy('slesson_id');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $lessons = $srch->fetchAll();
        $srch_kids = new LessonSearch($this->siteLangId, ReportedIssue::USER_TYPE_TEACHER,1);
        $srch_kids->joinTeacherLessonPlans();
        $srch_kids->addSearchListingFields();
        $srch_kids->applySearchConditions($post);
        $srch_kids->addFld('tlpn.tlpn_title');
        $srch_kids->addFld('tlpn.tlpn_id');
        $srch_kids->applyOrderBy($sortOrder);
        $srch_kids->addGroupBy('slesson_id');
        $srch_kids->setPageSize($pageSize);
        $srch_kids->setPageNumber($page);
        $lessons_kids = $srch_kids->fetchAll();
        $lessonArr = [];
        $user_timezone = MyDate::getUserTimeZone();
        $k=0;
        foreach ($lessons as $lesson) {
            if($lesson['grpcls_title']==""){
                $lesson=$lessons_kids[$k];
            }
            $key = $lesson['slesson_date'];
            if ($lesson['slesson_date'] != '0000-00-00') {
                $key = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lesson['slesson_date'] . ' ' . $lesson['slesson_start_time'], true, $user_timezone);
            }
            $lessonArr[$key][] = $lesson;
            $k++;
        }
        /* [ */
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'page' => $page,
            'pageSize' => $pageSize,
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
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        $this->set('referer', $referer);
        $this->set('lessonArr', $lessonArr);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->set('reportHours', FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION'));
        $listingView = FatApp::getPostedData('listingView', FatUtility::VAR_STRING, '');
        $tplpath = '';
        if ($listingView == 'shortDetail') {
            $tplpath = 'teacher-scheduled-lessons/_partial/short-detail-lesson-listing.php';
        }
        $this->_template->render(false, false, $tplpath);
    }

    private function searchLessons($post = [], $getCancelledOrder = false, $addLessonDateOrder = true)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinGroupClass($this->siteLangId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinLearnerCountry($this->siteLangId);
        $orderIsPaidCondition = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        if ($getCancelledOrder) {
            $orderIsPaidCondition->attachCondition('order_is_paid', '=', Order::ORDER_IS_CANCELLED, 'OR');
        }
        $srch->addCondition('slns.slesson_teacher_id', '=', UserAuthentication::getLoggedUserId());
        $srch->joinTeacherSettings();
        if ($addLessonDateOrder) {
            $srch->addOrder('slesson_date', 'ASC');
        }
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'slns.slesson_id',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'slesson_grpcls_id',
			'slns.slesson_kids_class',
            'order_is_paid',
            'sld.sldetail_learner_id as learnerId',
            'slns.slesson_teacher_id as teacherId',
            'slns.slesson_slanguage_id',
            'ul.user_first_name as learnerFname',
            'ul.user_last_name as learnerLname',
            'ul.user_url_name as learnerUrlName',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'IFNULL(learnercountry_lang.country_name, learnercountry.country_code) as learnerCountryName',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'sld.sldetail_learner_status',
            'sld.sldetail_order_id',
            'sld.sldetail_is_teacher_paid',
            '"-" as teacherTeachLanguageName',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration'
        ]);
        if (isset($post) && !empty($post['keyword'])) {
            $keywordsArr = array_unique(array_filter(explode(' ', $post['keyword'])));
            foreach ($keywordsArr as $keyword) {
                $cnd = $srch->addCondition('ul.user_first_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ul.user_last_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('sldetail_order_id', 'like', '%' . $keyword . '%');
            }
        }
        if (isset($post) && !empty($post['status'])) {
            switch ($post['status']) {
                case ScheduledLesson::STATUS_ISSUE_REPORTED:
                    $srch->addCondition('issrep_id', '>', 0);
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

    public function view($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $lessonRow = ScheduledLesson::getAttributesById($lessonId, ['slesson_id', 'slesson_teacher_id', 'slesson_grpcls_id']);
        if (!$lessonRow) {
            FatUtility::exitWithErrorCode(404);
        }
        if ($lessonRow['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('LBL_Access_Denied'));
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherScheduledLessons'));
        }
        $flashCardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true);
        $flashCardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true);
        if ($flashCardEnabled) {
            /* flashCardSearch Form[ */
            $frmSrchFlashCard = $this->getLessonFlashCardSearchForm();
            $frmSrchFlashCard->fill(['lesson_id' => $lessonId]);
            $this->set('frmSrchFlashCard', $frmSrchFlashCard);
            /* ] */
        }
        $this->set('lessonRow', $lessonRow);
        $this->set('lessonId', $lessonRow['slesson_id']);
        $this->set('showFlashCard', $flashCardEnabled);
        $this->_template->addJs('js/teacherLessonCommon.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->render();
    }

    public function viewLessonDetail($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $userId = UserAuthentication::getLoggedUserId();
        $post = [
            'slesson_teacher_id' => $userId,
            'slesson_id' => FatUtility::int($lessonId)
        ];
        $srch = new LessonSearch($this->siteLangId, ReportedIssue::USER_TYPE_TEACHER);
        $srch->joinTeacherLessonPlans();
        $srch->addSearchDetailFields();
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions($post);
        $srch->addFld('tlpn.tlpn_title');
        $srch->addFld('tlpn.tlpn_id');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $lesson = current($srch->fetchAll());
        if (empty($lesson)) {
            FatUtility::exitWithErrorCode(404);
        }
        $flashCardEnabled = FatApp::getConfig('CONF_ENABLE_FLASHCARD');
        if ($flashCardEnabled) {
            $frmSrchFlashCard = $this->getLessonFlashCardSearchForm();
            $frmSrchFlashCard->fill(['lesson_id' => $lesson['slesson_id']]);
            $this->set('frmSrchFlashCard', $frmSrchFlashCard);
        }
        $countReviews = TeacherLessonReview::getTeacherTotalReviews($lesson['slesson_teacher_id'], $lesson['slesson_id'], $lesson['sldetail_learner_id']);
        $this->set('lesson', $lesson);
        $this->set('countReviews', $countReviews);
        $this->set('flashCardEnabled', $flashCardEnabled);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('learners', $this->getLessonLearners($lessonId));
        $this->_template->render(false, false);
    }

    private function getLessonLearners(int $lessonId): array
    {
        $srch = new SearchBase(ScheduledLessonDetails::DB_TBL, 'sldetail');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sldetail.sldetail_learner_id', 'ul');
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'lclang.countrylang_country_id = ul.user_country_id AND lclang.countrylang_lang_id = ' . $this->siteLangId, 'lclang');
        $srch->addMultipleFields(['ul.user_id', 'ul.user_first_name', 'ul.user_last_name', 'lclang.country_name']);
        $srch->addCondition('sldetail.sldetail_slesson_id', '=', $lessonId);
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'user_id');
    }

    public function searchFlashCards()
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frmSrch = $this->getLessonFlashCardSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        $teacherId = (isset(FatApp::getPostedData()['teacherId'])) ? FatApp::getPostedData()['teacherId'] : 0;
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $lessonId = $post['lesson_id'];
        $srch = new FlashCardSearch(false);
        $srch->joinSharedFlashCard();
        $srch->joinLesson();
        $srch->joinWordLanguage();
        $srch->joinWordDefinitionLanguage();
        $srch->addCondition('sflashcard_slesson_id', '=', $lessonId);
        $srch->addCondition('sflashcard_teacher_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addOrder('flashcard_id', 'DESC');
        $srch->addMultipleFields([
            'flashcard_id',
            'flashcard_created_by_user_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
            'sflashcard_slesson_id'
        ]);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        if (!empty($post['keyword'])) {
            $srch->addCondition('flashcard_title', 'like', '%' . $post['keyword'] . '%');
        }
        $rsFlashcard = $srch->getResultSet();
        $flashCards = FatApp::getDb()->fetchAll($rsFlashcard);
        $this->set('flashCards', $flashCards);
        /* [ */
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
        $this->set('lessonId', $lessonId);
        $this->set('totalRecords', $totalRecords);
        $this->set('teacherId', $teacherId);
        /* ] */
        $this->_template->render(false, false, 'teacher-scheduled-lessons/search-flash-cards.php');
    }

    public function viewCalendar()
    {
        MyDate::setUserTimeZone();
        $user_timezone = MyDate::getUserTimeZone();
        $nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
        $this->set('user_timezone', $user_timezone);
        $this->set('nowDate', $nowDate);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $currentLangCode = strtolower(Language::getLangCode($this->siteLangId));
        $this->set('currentLangCode', $currentLangCode);
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
            'slns.slesson_grpcls_id',
			'slns.slesson_kids_class',
            'slns.slesson_teacher_id',
            'sld.sldetail_learner_id',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'ul.user_first_name',
            'ul.user_id',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'concat(slns.slesson_date," ",slns.slesson_start_time) AS slesson_date_time'
        ]);
        $srch->addCondition('slns.slesson_teacher_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addCondition('slns.slesson_status', 'NOT IN', [ScheduledLesson::STATUS_CANCELLED, ScheduledLesson::STATUS_NEED_SCHEDULING]);
        $srch->addCondition('CONCAT(slns.`slesson_date`, " ", slns.`slesson_start_time` )', '< ', $endDate);
        $srch->addCondition('CONCAT(slns.`slesson_end_date`, " ", slns.`slesson_end_time` )', ' > ', $startDate);
        // $srch->addHaving('slesson_date_time', '>', $curDateTime);
        $srch->joinLearner();
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $jsonArr = [];
        if (!empty($rows)) {
            $user_timezone = MyDate::getUserTimeZone();
            foreach ($rows as $k => $row) {
                $slesson_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['slesson_date'], true, $user_timezone);
                $slesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['slesson_date'] . ' ' . $row['slesson_start_time'], true, $user_timezone);
                $slesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['slesson_end_date'] . ' ' . $row['slesson_end_time'], true, $user_timezone);
                $jsonArr[$k] = [
                    "is1to1" => $row['slesson_grpcls_id'] == 0,
                    "title" => $row['grpcls_title'] ? $row['grpcls_title'] : $row['user_first_name'],
                    "date" => $slesson_date,
                    "start" => $slesson_start_time,
                    "end" => $slesson_end_time,
                    'lid' => $row['sldetail_learner_id'],
                    'liFname' => substr($row['user_first_name'], 0, 1),
                    'classType' => $row['slesson_status'],
                    'className' => $cssClassNamesArr[$row['slesson_status']]
                ];
                if (true == User::isProfilePicUploaded($row['user_id'])) {
                    $img = CommonHelper::generateFullUrl('Image', 'User', [$row['user_id']]);
                    $jsonArr[$k]['imgTag'] = '<img src="' . $img . '" />';
                } else {
                    $jsonArr[$k]['imgTag'] = '';
                }
            }
        }
        echo FatUtility::convertToJson($jsonArr);
    }

    private function getCancelLessonFrm()
    {
        $frm = new Form('cancelFrm');
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'cancel_lesson_msg', '');
        $fld->requirement->setRequired(true);
        $fld = $frm->addHiddenField('', 'slesson_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Send'));
        return $frm;
    }

    public function cancelLesson($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $srch = $this->searchLessons();
        $srch->joinLearnerCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('slns.slesson_id', ' = ', $lessonId);
        $srch->addCondition('slns.slesson_status', ' IN ', [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_NEED_SCHEDULING]);
        $lessonRow = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($lessonRow)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $sessionStartTime = strtotime($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        if ($lessonRow['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED && time() > $sessionStartTime) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getCancelLessonFrm();
        $frm->fill(['slesson_id' => $lessonId]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function cancelLessonSetup()
    {
        $frm = $this->getCancelLessonFrm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $lessonId = $post['slesson_id'];
        $db = FatApp::getDb();
        /* [ */
        $srch = $this->searchLessons();
        $srch->joinLearnerCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('sldetail_id');
        $srch->addCondition('slns.slesson_id', ' = ', $lessonId);
        $srch->addCondition('slns.slesson_status', ' IN ', [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_NEED_SCHEDULING]);
        $lessonRow = $db->fetch($srch->getResultSet());
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $sessionStartTime = strtotime($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        if ($lessonRow['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED && time() >= $sessionStartTime) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* ] */
        $db->startTransaction();
        /* update lesson status[ */
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $sLessonObj->assignValues(['slesson_status' => ScheduledLesson::STATUS_CANCELLED]);
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        /* ] */
        if (!$sLessonObj->cancelLessonByTeacher($post['cancel_lesson_msg'])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        // remove from teacher google calendar
        $settings = UserSetting::getUserSettings(UserAuthentication::getLoggedUserId());
        if (!empty($settings['us_google_access_token'])) {
            $sLessonObj->loadFromDb();
            $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
            if (!empty($oldCalId)) {
                SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
            }
            $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', '');
            $sLessonObj->save();
        }
        $lessonStsLog = new LessonStatusLog($lessonRow['sldetail_id']);
        // start: saving log in new table i.e. tbl_lesson_status_log
        $lessonStsLog->addLog(ScheduledLesson::STATUS_CANCELLED, User::USER_TYPE_TEACHER, UserAuthentication::getLoggedUserId(), $post['cancel_lesson_msg']);
        // End: saving log in new table i.e. tbl_lesson_status_log
        $db->commitTransaction();
        /* ] */
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Lesson_Cancelled_Successfully!'));
    }

    private function getRescheduleLessonFrm()
    {
        $frm = new Form('rescheduleFrm');
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'reschedule_lesson_msg', '');
        $fld->requirement->setRequired(true);
        $fld = $frm->addHiddenField('', 'slesson_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Send'));
        return $frm;
    }

    public function requestReschedule($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $srch = $this->searchLessons();
        $srch->joinLearnerCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('slns.slesson_id', ' = ', $lessonId);
        $srch->addCondition('slns.slesson_status', ' = ', ScheduledLesson::STATUS_SCHEDULED);
		 $srchDetail= $srch->addCondition('slns.slesson_grpcls_id', ' = ', 0);
		$srchDetail->attachCondition('slesson_kids_class', '>',0, 'OR');
      
       
        $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>', date('Y-m-d H:i:s'), 'AND', true);
        $lessonRow = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($lessonRow)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $hoursDiff = MyDate::hoursDiff($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        if ($hoursDiff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) {
            Message::addErrorMessage(Label::getLabel('LBL_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getRescheduleLessonFrm();
        $frm->fill(['slesson_id' => $lessonId]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function requestRescheduleSetup()
    {
        $frm = $this->getRescheduleLessonFrm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $lessonId = $post['slesson_id'];
        $db = FatApp::getDb();
        /* [ */
        $srch = $this->searchLessons();
        $srch->joinLearnerCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('slns.slesson_id', ' = ', $lessonId);
        $srch->addCondition('slns.slesson_teacher_id', ' = ', UserAuthentication::getLoggedUserId());
        $srch->addCondition('slns.slesson_status', ' = ', ScheduledLesson::STATUS_SCHEDULED);
      //$srch->addCondition('slns.slesson_grpcls_id', ' = ', 0);
		 $srchDetail= $srch->addCondition('slns.slesson_grpcls_id', ' = ', 0);
		$srchDetail->attachCondition('slesson_kids_class', '>',0, 'OR');
      
        $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addFld('sldetail_id');
        $lessonRow = $db->fetch($srch->getResultSet());
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $hoursDiff = MyDate::hoursDiff($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        if ($hoursDiff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* ] */
        /* update lesson status[ */
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $dataArr = [
            'slesson_status' => ScheduledLesson::STATUS_NEED_SCHEDULING,
            'slesson_date' => '0000-00-00',
            'slesson_end_date' => '0000-00-00',
            'slesson_start_time' => '00:00:00',
            'slesson_end_time' => '00:00:00',
            'slesson_teacher_join_time' => '00:00:00',
        ];
        $sLessonObj->assignValues($dataArr);
        $db->startTransaction();
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        // remove from teacher google calendar
        $settings = UserSetting::getUserSettings(UserAuthentication::getLoggedUserId());
        if (!empty($settings['us_google_access_token'])) {
            $sLessonObj->loadFromDb();
            $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
            if ($oldCalId) {
                SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
            }
            $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', '');
            $sLessonObj->save();
        }
        $lessonResLogArr = [
            'lesreschlog_slesson_id' => $lessonRow['slesson_id'],
            'lesreschlog_reschedule_by' => UserAuthentication::getLoggedUserId(),
            'lesreschlog_user_type' => User::USER_TYPE_TEACHER,
            'lesreschlog_comment' => $post['reschedule_lesson_msg'],
        ];
        $lessonResLogObj = new LessonRescheduleLog();
        $lessonResLogObj->assignValues($lessonResLogArr);
        if (!$lessonResLogObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($lessonResLogObj->getError());
        }
        $lessonStsLog = new LessonStatusLog($lessonRow['sldetail_id']);
        // start: saving log in new table i.e. tbl_lesson_status_log
        $lessonStsLog->addLog(ScheduledLesson::STATUS_NEED_SCHEDULING, User::USER_TYPE_TEACHER, UserAuthentication::getLoggedUserId(), $post['reschedule_lesson_msg']);
        // End: saving log in new table i.e. tbl_lesson_status_log
        /* ] */
        if (!$sLessonObj->rescheduleLessonByTeacher($post['reschedule_lesson_msg'])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        $db->commitTransaction();
        $userNotification = new UserNotifications($lessonRow['learnerId']);
        $userNotification->sendSchLessonByTeacherNotification($lessonRow['sldetail_id'], true);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Lesson_Re-schedule_request_sent_successfully!'));
    }

    public function viewBookingCalendar($lessonId = 0)
    {
        $lessonId = FatUtility::int($lessonId);
        $teacher_id = UserAuthentication::getLoggedUserId();
        $cssClassNamesArr = TeacherWeeklySchedule::getWeeklySchCssClsNameArr();
        $this->set('action', FatApp::getPostedData('action'));
        $this->set('teacher_id', $teacher_id);
        $this->set('lessonId', $lessonId);
        $this->set('cssClassArr', $cssClassNamesArr);
        $this->_template->render(false, false);
    }

    public function scheduleLessonSetup()
    {
        FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        if ($lessonId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        /* [ */
        $srch = $this->searchLessons();
        $srch->joinLearnerCredentials();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addCondition('slns.slesson_id', '=', $lessonId);
        $srch->addFld([
            'lcred.credential_user_id as learnerId',
            'lcred.credential_email as learnerEmailId',
            'sldetail_id',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName'
        ]);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (!$lessonRow) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        /* ] */
        /* update lesson status[ */
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $dataArr = [
            'slesson_status' => ScheduledLesson::STATUS_SCHEDULED,
            'slesson_date' => $post['date'],
            'slesson_start_time' => $post['startTime'],
            'slesson_end_time' => $post['endTime']
        ];
        $sLessonObj->assignValues($dataArr);
        if (!$sLessonObj->save()) {
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        /* ] */
        /* send email to learner[ */
        $vars = [
            '{learner_name}' => $lessonRow['learnerFullName'],
            '{teacher_name}' => $lessonRow['teacherFullName'],
            '{lesson_name}' => $lessonRow['teacherTeachLanguageName'],
            '{lesson_date}' => FatDate::format($post['date']),
            '{lesson_start_time}' => $post['startTime'],
            '{lesson_end_time}' => $post['endTime'],
            '{action}' => Label::getLabel('VERB_Scheduled', $this->siteLangId),
        ];
        if (!EmailHandler::sendMailTpl($lessonRow['learnerEmailId'], 'teacher_scheduled_lesson_email', $this->siteLangId, $vars)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Mail_not_sent!'));
        }
        /* ] */
        $userNotification = new UserNotifications($lessonRow['learnerId']);
        $userNotification->sendSchLessonByTeacherNotification($lessonRow['sldetail_id']);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Lesson_Scheduled_Successfully!'));
    }

    public function viewAssignedLessonPlan($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        if ($lessonId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* validation[ */
        $lessonDetail = ScheduledLesson::getAttributesById($lessonId, ['slesson_teacher_id']);
        if ($lessonDetail['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('LBL_Access_Denied'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $srch = new LessonPlanSearch(false);
        $srch->joinTable('tbl_scheduled_lessons_to_teachers_lessons_plan', 'INNER JOIN', 'tlpn_id = ltp_tlpn_id');
        $srch->addMultipleFields(['tlpn_id', 'tlpn_title', 'tlpn_level', 'tlpn_user_id', 'tlpn_description','tlpn_links']);
        $srch->addCondition('tlpn_user_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addCondition('ltp_slessonid', '=', $lessonId);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        $this->set('statusArr', LessonPlan::getDifficultyArr());
        $this->set('data', $row);
        $this->_template->render(false, false);
    }

    public function assignLessonPlanToLessons()
    {
        $lessonId = FatApp::getPostedData('ltp_slessonid', FatUtility::VAR_INT, 0);
        $planId = FatApp::getPostedData('ltp_tlpn_id', FatUtility::VAR_INT, 0);
        if ($lessonId < 1 || $planId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        /* validation[ */
        $lessonDetail = ScheduledLesson::getAttributesById($lessonId);
        $canEdit = ($lessonDetail['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) ||
                (($lessonDetail['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) &&
                (strtotime($lessonDetail['slesson_end_date'] . " " . $lessonDetail['slesson_end_time']) > strtotime(date('Y-m-d H:i:s'))) || (strtotime($lessonDetail['slesson_end_date'] . " " . $lessonDetail['slesson_end_time']) < strtotime(date('Y-m-d H:i:s'))));
        if (($lessonDetail['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) || !$canEdit) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Access_Denied'));
        }
        $planDetail = LessonPlan::getAttributesById($planId, ['tlpn_user_id']);
        if ($planDetail['tlpn_user_id'] != UserAuthentication::getLoggedUserId()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Access_Denied'));
        }
        /* ] */
        $data = ["ltp_slessonid" => $lessonId, "ltp_tlpn_id" => $planId];
        if (!FatApp::getDb()->insertFromArray('tbl_scheduled_lessons_to_teachers_lessons_plan', $data, false, [], $data)) {
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Lesson_Plan_Assigned_Successfully!'));
    }

    public function removeAssignedLessonPlan()
    {
        $lessonId = FatApp::getPostedData('ltp_slessonid', FatUtility::VAR_INT, 0);
        if ($lessonId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        /* validation[ */
        $lessonDetail = ScheduledLesson::getAttributesById($lessonId, ['slesson_teacher_id']);
        if ($lessonDetail['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Access_Denied'));
        }
        /* ] */
        $data = ['smt' => 'ltp_slessonid = ?', 'vals' => [$lessonId]];
        if (!FatApp::getDb()->deleteRecords('tbl_scheduled_lessons_to_teachers_lessons_plan', $data)) {
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Lesson_Plan_Removed_Successfully!'));
    }

    public function changeLessonPlan($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        if ($lessonId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* validation[ */
        $lessonDetail = ScheduledLesson::getAttributesById($lessonId, ['slesson_teacher_id']);
        if ($lessonDetail['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('LBL_Access_Denied'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $post = FatApp::getPostedData();
        $srch = new LessonPlanSearch(false);
        $srch->addMultipleFields(['tlpn_id', 'tlpn_title', 'tlpn_level', 'tlpn_user_id', 'tlpn_description']);
        $srchRelLsnToPln = new SearchBase('tbl_scheduled_lessons_to_teachers_lessons_plan');
        $srchRelLsnToPln->addMultipleFields(['ltp_tlpn_id']);
        $srchRelLsnToPln->addCondition('ltp_slessonid', '=', $lessonId);
        $relRs = $srchRelLsnToPln->getResultSet();
        $relRows = FatApp::getDb()->fetch($relRs);
        $srch->addCondition('tlpn_id', '!=', $relRows['ltp_tlpn_id']);
        $srch->addCondition('tlpn_user_id', '=', UserAuthentication::getLoggedUserId());
        if (!empty($post['keyword'])) {
            $srch->addCondition('tlpn_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['status'])) {
            $srch->addCondition('tlpn_level', '=', $post['status']);
        }
        $rs = $srch->getResultSet();
        $count = $srch->recordCount();
        $rows = FatApp::getDb()->fetchAll($rs);
        $this->set('userId', UserAuthentication::getLoggedUserId());
        $this->set('statusArr', LessonPlan::getDifficultyArr());
        $this->set('countData', $count);
        $this->set('lessonsPlanData', $rows);
        $this->set('lessonId', $lessonId);
        $this->_template->render(false, false);
    }

    public function listLessonPlans($lessonId)
    {
        $post = FatApp::getPostedData();
        $lessonId = FatUtility::int($lessonId);
        if ($lessonId < 1) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* validation[ */
        $lessonDetail = ScheduledLesson::getAttributesById($lessonId, ['slesson_teacher_id']);
        if ($lessonDetail['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            Message::addErrorMessage(Label::getLabel('LBL_Access_Denied'));
            FatUtility::dieWithError(Message::getHtml());
        }
        /* ] */
        $srch = new LessonPlanSearch(false);
        $srch->addCondition('tlpn_user_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addMultipleFields(['tlpn_id', 'tlpn_title', 'tlpn_level', 'tlpn_user_id', 'tlpn_description']);
        if (!empty($post['keyword'])) {
            $srch->addCondition('tlpn_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['status'])) {
            $srch->addCondition('tlpn_level', '=', $post['status']);
        }
        $rs = $srch->getResultSet();
        $count = $srch->recordCount();
        $rows = FatApp::getDb()->fetchAll($rs);
        $this->set('userId', UserAuthentication::getLoggedUserId());
        $this->set('statusArr', LessonPlan::getDifficultyArr());
        $this->set('countData', $count);
        $this->set('lessonsPlanData', $rows);
        $this->set('lessonId', $lessonId);
        $this->_template->render(false, false);
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
        $srch->addCondition('sflashcard_teacher_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addOrder('flashcard_id', 'DESC');
        $srch->addMultipleFields([
            'flashcard_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
            'flashcard_notes',
            'flashcard_added_on'
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
        /* validation [ */
        $srch = new FlashCardSearch();
        $srch->joinSharedFlashCard();
        $srch->joinLesson();
        $srch->addCondition('flashcard_id', '=', $flashCardId);
        $srch->addMultipleFields(['flashcard_id', 'sflashcard_learner_id', 'sflashcard_teacher_id', 'flashcard_created_by_user_id']);
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        if ($row['flashcard_created_by_user_id'] != UserAuthentication::getLoggedUserId()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Record_cannot_be_deleted,_Access_Denied'));
        }
        /* ] */
        $db = FatApp::getDb();
        if (!$db->deleteRecords(FlashCard::DB_TBL_SHARED, ['smt' => 'sflashcard_flashcard_id = ?', 'vals' => [$row['flashcard_id']]])) {
            FatUtility::dieJsonError($db->getError());
        }
        if (!$db->deleteRecords(FlashCard::DB_TBL, ['smt' => 'flashcard_id = ?', 'vals' => [$row['flashcard_id']]])) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_Record_Deleted_Successfully!"));
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

    public function flashCardForm()
    {
        if (empty(FatApp::getConfig('CONF_ENABLE_FLASHCARD'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $flashCardId = FatApp::getPostedData('flashcardId', FatUtility::VAR_INT, 0);
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        if ($lessonId <= 0) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getFlashcardFrm();
        $frmData['slesson_id'] = $lessonId;
        if ($flashCardId > 0) {
            $frmData = $frmData + FlashCard::getAttributesById($flashCardId);
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
        $scheduledLessonObj = new ScheduledLessonSearch(false);
        $scheduledLessonObj->addMultipleFields(['slesson_teacher_id', 'sld.sldetail_learner_id']);
        $scheduledLessonObj->addCondition('slesson_id', '=', $lessonId);
        $scheduledLessonObj->setPageSize(1);
        $resultSet = $scheduledLessonObj->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($resultSet);
        if (empty($lessonRow) || $lessonRow['slesson_teacher_id'] != UserAuthentication::getLoggedUserId()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $flashCardId = $post['flashcard_id'];
        $post['flashcard_user_id'] = $lessonRow['sldetail_learner_id'];
        $post['flashcard_created_by_user_id'] = UserAuthentication::getLoggedUserId();
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
                'sflashcard_learner_id' => $lessonRow['sldetail_learner_id'],
                'sflashcard_teacher_id' => $lessonRow['slesson_teacher_id'],
                'sflashcard_slesson_id' => $lessonId,
            ]);
            if ($db->getError()) {
                FatUtility::dieJsonError($db->getError());
            }
        }
        $this->set('msg', Label::getLabel("LBL_Flashcard_Saved_Successfully!"));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getStartedLessonDetails($lessonId)
    {
        $srch = $this->searchLessons();
        $srch->joinTeacherCredentials();
        $srch->addMultipleFields([
            'slns.slesson_id', 'op_id',
            'tcred.credential_email as teacherEmail',
            'ut.user_first_name as teacherFirstName',
            'ut.user_last_name as teacherLastName',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'slesson_teacher_join_time'
        ]);
        $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch->addCondition('slns.slesson_id', '=', $lessonId);
        $srch->addCondition('mysql_func_CONCAT(slesson_date," ", slesson_start_time)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('mysql_func_CONCAT(slesson_end_date," ", slesson_end_time)', '>', date('Y-m-d H:i:s'), 'AND', true);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function startLesson($lessonId)
    {
        // validate lesson, if it can be started
        $lessonData = $this->getStartedLessonDetails($lessonId);
        if (empty($lessonData)) {
            CommonHelper::dieJsonError(Label::getLabel('MSG_Cannot_Start_The_lesson_Now'));
        }
        // get meeting details
        try {
            $lesMettings = new LessonMeetings();
            $meetingData = $lesMettings->getMeetingData($lessonData);
        } catch (Exception $e) {
            CommonHelper::dieJsonError($e->getMessage());
        }
        // update teacher join time
        if ($lessonData['slesson_teacher_join_time'] == '0000-00-00 00:00:00') {
            $schLesson = new ScheduledLesson($lessonId);
            if (!$schLesson->markTeacherJoinTime()) {
                CommonHelper::dieJsonError($schLesson->getError());
            }
        }
        CommonHelper::dieJsonSuccess(['data' => $meetingData, 'msg' => Label::getLabel('LBL_Joining._Please_Wait...')]);
    }

    public function checkEveryMinuteStatus($lessonId)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->addMultipleFields([
            'slns.slesson_status', 'slesson_teacher_end_time',
			'slesson_kids_class',
            'IF(slesson_grpcls_id=0, sld.sldetail_learner_status, 0) as sldetail_learner_status'
        ]);
        $srch->addCondition('slns.slesson_teacher_id', '=', UserAuthentication::getLoggedUserId());
        $srch->addCondition('slns.slesson_id', '=', $lessonId);
        $srch->addCondition('slns.slesson_date', '<=', date('Y-m-d'));
        $srch->addCondition('slns.slesson_start_time', '<=', date('H:i:s'));
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        echo FatUtility::convertToJson($data);
    }

    public function endLessonSetup()
    {
        $lessonId = FatApp::getPostedData('lessonId', FatUtility::VAR_INT, 0);
        if ($lessonId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        // validate user too with lesson id.
        /* [ */
        $srch = $this->searchLessons();
        $srch->doNotCalculateRecords();
        $srch->addCondition('slns.slesson_id', '=', $lessonId);
        $srch->addCondition('slns.slesson_status', 'IN', [ScheduledLesson::STATUS_COMPLETED, ScheduledLesson::STATUS_SCHEDULED]);
        $srch->addCondition('mysql_func_CONCAT(slesson_date," ", slesson_start_time)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('slesson_teacher_join_time', '>=', 'mysql_func_CONCAT(slesson_date," ", slesson_start_time)', 'AND', true);
        $rs = $srch->getResultSet();
        $lessonRow = FatApp::getDb()->fetch($rs);
        if (empty($lessonRow)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        if (ScheduledLesson::STATUS_COMPLETED == $lessonRow['slesson_status']) {
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_ALREADY_ENDED_BY_YOU/LEARNER!'));
        }
        if (ScheduledLesson::STATUS_SCHEDULED != $lessonRow['slesson_status']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        /* ] */
        $to_time = strtotime($lessonRow['slesson_date'] . ' ' . $lessonRow['slesson_start_time']);
        $from_time = strtotime(date('Y-m-d H:i:s'));
        $diff = round(abs($to_time - $from_time) / 60, 2);
        if ($lessonRow['slesson_date'] == date('Y-m-d') and $diff < FatApp::getConfig('CONF_ALLOW_TEACHER_END_LESSON', FatUtility::VAR_INT, 10)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Cannot_End_Lesson_So_Early!'));
        }
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
        }
        $dataUpdateArr = [];
        $lessonMeetingDetail = new LessonMeetingDetail($lessonId, $lessonRow['teacherId']);
        if ($meetingRow = $lessonMeetingDetail->getMeetingDetails(LessonMeetingDetail::KEY_ZOOM_RAW_DATA)) {
            $meetingRow = json_decode($meetingRow, true);
            try {
                $zoom = new Zoom();
                $endRes = $zoom->endMeeting($meetingRow['id']);
            } catch (Exception $e) {
                // exception
            }
        }
        $dataUpdateArr1 = [
            'slesson_status' => ScheduledLesson::STATUS_COMPLETED,
            'slesson_ended_by' => User::USER_TYPE_TEACHER,
            'slesson_ended_on' => date('Y-m-d H:i:s'),
            'slesson_teacher_end_time' => date('Y-m-d H:i:s'),
        ];
        $dataUpdateArr = array_merge($dataUpdateArr, $dataUpdateArr1);
        if ($lessonRow['slesson_grpcls_id'] > 0 && $lessonRow['slesson_kids_class']<=0) {
            $tGrpClsObj = new TeacherGroupClasses($lessonRow['slesson_grpcls_id']);
            $tGrpClsObj->assignValues(['grpcls_status' => TeacherGroupClasses::STATUS_COMPLETED]);
            if (!$tGrpClsObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($tGrpClsObj->getError());
            }
        }
		 if ($lessonRow['slesson_grpcls_id'] > 0 && $lessonRow['slesson_kids_class']>0) {
            $tGrpClsObj = new TeacherKidsClasses($lessonRow['slesson_grpcls_id']);
            $tGrpClsObj->assignValues(['grpcls_status' => TeacherKidsClasses::STATUS_COMPLETED]);
            if (!$tGrpClsObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($tGrpClsObj->getError());
            }
        }
        $sLessonObj = new ScheduledLesson($lessonRow['slesson_id']);
        $sLessonObj->assignValues($dataUpdateArr);
        if (!$sLessonObj->save()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($sLessonObj->getError());
        }
        $record = new TableRecord(ScheduledLessonDetails::DB_TBL);
        $record->assignValues([
            'sldetail_learner_end_time' => date('Y-m-d H:i:s'),
            'sldetail_learner_status' => ScheduledLesson::STATUS_COMPLETED
        ]);
        if (!$record->update(['smt' => 'sldetail_slesson_id = ?', 'vals' => [$lessonRow['slesson_id']]])) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($record->getError());
        }
        if (!$db->commitTransaction()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_LESSON_ENDED_SUCCESSFULLY!'));
    }

    public function endLessonNotification($lessonId)
    {
        $this->set('lesonId', $lessonId);
        $this->_template->render(false, false);
    }

}
