<?php

class TeacherKidsClassesController extends TeacherBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $this->_template->addJs('js/teacherLessonCommon.js');
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $frmSrch = $this->getSearchForm();
        $this->set('serachForm', $frmSrch);
        $this->set('setMonthAndWeekName', true);
        $this->_template->render();
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
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
        $teacher_id = UserAuthentication::getLoggedUserId();

        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $srch->joinTable(TeacherKidsClasses::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = grpclsl.grpclslang_grpcls_id AND grpclsl.grpclslang_lang_id=' . $this->siteLangId, 'grpclsl');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'LEFT JOIN', 'slesson.slesson_grpcls_id = grpcls.grpcls_id', 'slesson');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT JOIN', 'sldetail.sldetail_slesson_id = slesson.slesson_id', 'sldetail');
        $srch->joinTable(ReportedIssue::DB_TBL, 'LEFT JOIN', 'sldetail.sldetail_id = repiss.repiss_sldetail_id', 'repiss');
        $srch->addCondition('grpcls.grpcls_deleted', '=', applicationConstants::NO);
        $srch->addCondition('grpcls.grpcls_teacher_id', '=', $teacher_id);
        $srch->addFld('slesson.slesson_teacher_join_time > 0');
        $srch->addMultipleFields([
            'grpcls.grpcls_id  as grpcls_id',
            'grpcls.grpcls_status as grpcls_status',
            'grpcls.grpcls_entry_fee as grpcls_entry_fee',
            'grpcls.grpcls_start_datetime as grpcls_start_datetime',
            'grpcls.grpcls_end_datetime as grpcls_end_datetime',
            'repiss.repiss_id as repiss_id',
            'IFNULL(grpclslang_grpcls_title, grpcls_title) as grpcls_title',
            'slesson.slesson_id as slesson_id',
            'slesson.slesson_teacher_join_time as is_joined',
            '(' . $srch2->getQuery() . ') total_learners',
           // '(' . $srch3->getQuery() . ') is_joined',
        ]);
        if (!empty($post['status'])) {
            $srch->addCondition('grpcls.grpcls_status', '=', $post['status']);
        }
        if (!empty($post['keyword'])) {
            $keywordCondition = $srch->addCondition('grpcls.grpcls_title', 'like', '%' . $post['keyword'] . '%');
            $keywordCondition->attachCondition('grpclsl.grpclslang_grpcls_title', 'like', '%' . $post['keyword'] . '%');
        }
        $srch->addGroupBy('grpcls.grpcls_id');
        $srch->addOrder('grpcls.grpcls_id', 'desc');
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
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
        $this->set('totalRecords', $totalRecords);
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('classes', $classes);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('referer', CommonHelper::redirectUserReferer(true));
        $this->set('classStatusArr', TeacherKidsClasses::getStatusArr());
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->_template->render(false, false);
    }

    public function form($grpclsId = 0)
    {
        $classId = FatUtility::int($grpclsId);
        $teacher_id = UserAuthentication::getLoggedUserId();
        $frm = $this->getFrm();
        if ($classId > 0) {
            $user_timezone = MyDate::getUserTimeZone();
            $systemTimeZone = MyDate::getTimeZone();
            $data = TeacherKidsClasses::getAttributesById($grpclsId);
            $week_names = ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday','Thursday' => 'thursday','Friday' => 'Friday','Saturday' => 'Saturday','Sunday' => 'Sunday'];
            $weekname_list = $data['grpcls_weeks'];
            $fld = $frm->getField('grpcls_weeks');
            $select=explode(",",$weekname_list);
            $frm = $this->getFrm($select);
            $isSlotBooked = ScheduledLessonSearch::isSlotBooked($teacher_id, $data['grpcls_start_datetime'], $data['grpcls_end_datetime']);
            if ($isSlotBooked) {
                $fld = $frm->getField('grpcls_start_datetime');
                $fld->setFieldTagAttribute('disabled', 'disabled');
                $fld->setFieldTagAttribute('title', Label::getLabel("LBL_Start_Time_can_not_change_for_Booked_Class"));
                $fld->requirements()->setRequired(false);
                $fld = $frm->getField('grpcls_entry_fee');
                $fld->setFieldTagAttribute('disabled', 'disabled');
                $fld = $frm->getField('grpcls_tlanguage_id');
                $fld->setFieldTagAttribute('disabled', 'disabled');
                $fld->setFieldTagAttribute('title', Label::getLabel("LBL_Price_can_not_change_for_Booked_Class"));
                $fld->requirements()->setRequired(false);      
            }

            $data['grpcls_start_datetime'] = MyDate::changeDateTimezone($data['grpcls_start_datetime'], $systemTimeZone, $user_timezone);
            $data['grpcls_end_datetime'] = MyDate::changeDateTimezone($data['grpcls_end_datetime'], $systemTimeZone, $user_timezone);
            $diff = abs(strtotime($data['grpcls_end_datetime']) - strtotime($data['grpcls_start_datetime']));
            $data['grpcls_duration'] = floor($diff / 60);
            $frm->fill($data);
        }

        $currentTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, MyDate::getUserTimeZone());

        $this->set('userId', UserAuthentication::getLoggedUserId());
        $this->set('currentTime', $currentTime);
        $this->set('grpclsId', $grpclsId);
        $this->set('grpcls_weeks',$data['grpcls_weeks']);
        $this->set('languages', Language::getAllNames(false));
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $grpcls_id = FatApp::getPostedData('grpcls_id', FatUtility::VAR_INT, 0);
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $post = FatApp::getPostedData();
        $weeks_list  = implode(',',$post['grpcls_weeks']);
        if(isset($post['grpcls_weeks']) && !empty($post['grpcls_weeks']) && $weeks_list[0]!=''){
        $weeks_list  = implode(',',$post['grpcls_weeks']);
        $post['grpcls_weeks'] = $weeks_list;
        }
        else {
            $post['grpcls_weeks'] = "";
        }
        $frm = $this->getFrm($grpcls_id);
        if ($grpcls_id > 0) {
            $class_details = TeacherKidsClassesSearch::getClassDetailsByTeacher($grpcls_id, $this->loggedUserId, $this->siteLangId);
            if (empty($class_details)) {
                FatUtility::dieJsonError(Label::getLabel("LBL_Unauthorized"));
            }
            $isSlotBooked = ScheduledLessonSearch::isSlotBooked($this->loggedUserId, $class_details['grpcls_start_datetime'], $class_details['grpcls_end_datetime']);
            if ($isSlotBooked) {
                $post['grpcls_start_datetime'] = MyDate::changeDateTimezone($class_details['grpcls_start_datetime'], $systemTimeZone, $user_timezone);
                $post['grpcls_end_datetime'] = MyDate::changeDateTimezone($class_details['grpcls_end_datetime'], $systemTimeZone, $user_timezone);
                $post['grpcls_entry_fee'] = $class_details['grpcls_entry_fee'];
                $post['grpcls_one_on_one_entry_fee'] = $class_details['grpcls_one_on_one_entry_fee'];
                $post['grpcls_duration'] = $class_details['grpcls_duration'];
                $post['grpcls_tlanguage_id'] = $class_details['grpcls_tlanguage_id'];
                $post['grpcls_ages'] = $class_details['grpcls_ages'];
                $post['grpcls_kids_youtube_link'] = $class_details['grpcls_kids_youtube_link'];
                $post['grpcls_max_one_on_one_learner'] = $class_details['grpcls_max_one_on_one_learner'];
                $post['grpcls_kids_title'] = $class_details['grpcls_kids_title']; 
                $post['grpcls_class_experience'] = $class_details['grpcls_class_experience'];
                $post['grpcls_schedule'] = $class_details['grpcls_schedule'];
                $post['grpcls_learning_goals'] = $class_details['grpcls_learning_goals'];
                $post['grpcls_supply_list'] = $class_details['grpcls_supply_list'];
                $post['grpcls_parental_guidance'] = $class_details['grpcls_parental_guidance'];
                $post['grpcls_total_lesson'] = $class_details['grpcls_total_lesson'];
            }
        }

        $post = $frm->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['grpcls_end_datetime'] = date('Y-m-d H:i', strtotime($post['grpcls_start_datetime'] . ' + ' . $post['grpcls_duration'] . ' minutes'));
        $post['grpcls_slug'] = isset($post['grpcls_slug']) ? $post['grpcls_slug'] : str_replace(" ", '-', strtolower($post['grpcls_title']));
        $post['grpcls_teacher_id'] = $this->loggedUserId;
        $post['grpcls_start_datetime'] = MyDate::changeDateTimezone($post['grpcls_start_datetime'], $user_timezone, $systemTimeZone);
        $post['grpcls_end_datetime'] = MyDate::changeDateTimezone($post['grpcls_end_datetime'], $user_timezone, $systemTimeZone);
        $post['grpcls_ages'] = isset($post['grpcls_ages'])?$post['grpcls_ages']:'';
        $post['grpcls_kids_youtube_link'] = isset($post['grpcls_kids_youtube_link'])?$post['grpcls_kids_youtube_link']:'';
        $post['grpcls_max_one_on_one_learner'] = isset($post['grpcls_max_one_on_one_learner'])?$post['grpcls_max_one_on_one_learner']:'';
        $post['grpcls_one_on_one_entry_fee'] = isset($post['grpcls_one_on_one_entry_fee'])?$post['grpcls_one_on_one_entry_fee']:'';
        $post['grpcls_kids_title'] = isset($post['grpcls_kids_title'])?$post['grpcls_kids_title']:'';
        $post['grpcls_class_experience'] = isset($post['grpcls_class_experience'])?$post['grpcls_class_experience']:'';
        $post['grpcls_schedule'] = isset($post['grpcls_schedule'])?$post['grpcls_schedule']:'';
        $post['grpcls_learning_goals'] = isset($post['grpcls_learning_goals'])?$post['grpcls_learning_goals']:'';
        $post['grpcls_supply_list'] = isset($post['grpcls_supply_list'])?$post['grpcls_supply_list']:'';
        $post['grpcls_parental_guidance'] = isset($post['grpcls_parental_guidance'])?$post['grpcls_parental_guidance']:'';
        $post['grpcls_duration'] = isset($post['grpcls_duration'])?$post['grpcls_duration']:'';
        $post['grpcls_total_lesson'] = isset($post['grpcls_total_lesson'])?$post['grpcls_total_lesson']:1;
        if(isset($post['grpcls_weeks']) && !empty($post['grpcls_weeks']) && $post['grpcls_weeks'][0]!=""){
            $post['grpcls_weeks'] = $weeks_list;
             }
             else {
                 
                 $post['grpcls_weeks'] = "";
             }
            
        // if (strtotime($post['grpcls_start_datetime']) < time()) {
        //     FatUtility::dieJsonError(Label::getLabel('LBL_Can_not_add_time_for_old_date'));
        // }

        $tGrpClsSrchObj = new TeacherKidsClassesSearch();
        if ($grpcls_id > 0) {
            $tGrpClsSrchObj->addCondition('grpcls_id', '!=', $grpcls_id);
        }
        $tGrpClsSrchObj->addCondition('grpcls_teacher_id', '=', $this->loggedUserId);
    //    $tGrpClsSrchObj->addCondition('grpcls_status', '=', TeacherKidsClasses::STATUS_ACTIVE);
    //    $cnd = $tGrpClsSrchObj->addCondition('grpcls_end_datetime', '>', $post['grpcls_start_datetime']);
    //    $cnd->attachCondition('grpcls_start_datetime', '<', $post['grpcls_end_datetime'], 'AND');
        $tGrpClsSrchObj->setPageSize(1);
        $rs = $tGrpClsSrchObj->getResultSet();
        // if (FatApp::getDb()->fetch($rs)) {
        //     FatUtility::dieJsonError(Label::getLabel('LBL_A_class_already_exist_in_selected_time'));
        // }
        $dataTime = new DateTime($post['grpcls_start_datetime']);
        $weekStartAndEndDate = MyDate::getWeekStartAndEndDate($dataTime);
        $weekStart = $weekStartAndEndDate['weekStart'];
        $isSlotBooked = ScheduledLessonSearch::isSlotBooked($this->loggedUserId, $post['grpcls_start_datetime'], $post['grpcls_end_datetime']);
        if ($isSlotBooked) {
            if ($grpcls_id <= 0 || $post['grpcls_start_datetime'] != $class_details['grpcls_start_datetime'] || $post['grpcls_end_datetime'] != $class_details['grpcls_end_datetime']) {
                FatUtility::dieJsonError(Label::getLabel('LBL_Slot_is_already_booked'));
            }
        }
        $tWSchObj = new TeacherWeeklySchedule();
        $isAvailable = $tWSchObj->checkCalendarTimeSlotAvailability($this->loggedUserId, $post['grpcls_start_datetime'], $post['grpcls_end_datetime'], $weekStart);
        if ($grpcls_id == 0) {
            $post['grpcls_status'] = TeacherKidsClasses::STATUS_ACTIVE;
        }
        $tGrpClsObj = new TeacherKidsClasses($grpcls_id);
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
      
        $lessonPlanId = $tGrpClsObj->getMainTableRecordId();
        if (!empty($_FILES['tlpn_image']['name'])) {
            $size = ($_FILES['tlpn_image']['size'] / 1048576);
            if ($_FILES['tlpn_image']['size'] > 1) {
                $label = Label::getLabel('LBL_YOU_{name}_FILE_SIZE_IS_MORE_THEN_{size}_MB');
                $label = str_replace(['{name}', 'size'], [Label::getLabel('LBL_PLAN_BANNER_IMAGE'), 1], $label);
            }
            $fileHandlerObj = new AttachedFile();
            if (!$fileHandlerObj->saveImage($_FILES['tlpn_image']['tmp_name'], AttachedFile::FILETYPE_KIDS_PLAN_IMAGE, $lessonPlanId, 0, $_FILES['tlpn_image']['name'], 0, true)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($fileHandlerObj->getError());
            }
        }
        $db->commitTransaction();
        $msg = Label::getLabel('LBL_Group_Class_Saved_Successfully!');
        if ($isAvailable) {
            $msg = Label::getLabel('LBL_Slot_is_already_added_for_1_to_1_class_whichever_first_booked_will_be_booked!');
        }
        $newTabLangId = 0;
        if ($grpcls_id > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = TeacherKidsClasses::getAttributesByLangId($langId, $grpcls_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $grpcls_id = $tGrpClsObj->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $msg);
        $this->set('grpcls_id', $grpcls_id);
        $this->set('lang_id', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function KidsPlanImage($lessonPlanId = 0, $subRecordId = 0, $sizeType = '')
    {
        $recordId = FatUtility::int($lessonPlanId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_KIDS_PLAN_IMAGE, $recordId, $subRecordId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                $w = 538;
                $h = 283;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
        }
    }

    public function langForm($grpclsId, $langId)
    {
        if ($grpclsId == 0 || $langId == 0) {
            FatUtility::dieWithError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $langFrm = $this->getLangForm($grpclsId, $langId);
        $langData = TeacherKidsClasses::getAttributesByLangId($langId, $grpclsId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('grpclsId', $grpclsId);
        $this->set('langId', $langId);
        $this->set('langFrm', $langFrm);
        $this->_template->render(false, false);
    }

    public function getLangForm($grpcls_id, $lang_id)
    {
        $frm = new Form('frmGroupClassLang');
        $frm->addHiddenField('', 'grpclslang_grpcls_id', $grpcls_id);
        $frm->addHiddenField('', 'grpclslang_lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Title', $this->siteLangId), 'grpclslang_grpcls_title');
        $frm->addTextArea(Label::getLabel('LBL_Description', $this->siteLangId), 'grpclslang_grpcls_description')->requirements()->setRequired(true);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->siteLangId));
        return $frm;
    }

    public function langSetup()
    {
        $post = FatApp::getPostedData();
        $grpclsId = $post['grpclslang_grpcls_id'];
        $lang_id = $post['grpclslang_lang_id'];
        if ($grpclsId == 0 || $lang_id == 0) {
            FatUtility::dieWithError(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $frm = $this->getLangForm($grpclsId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['btn_submit']);
        $teacherGroupClass = new TeacherKidsClasses($grpclsId);
        if (!$teacherGroupClass->updateLangData($lang_id, $post)) {
            Message::addErrorMessage($teacherGroupClass->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($grpclsId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = TeacherKidsClasses::getAttributesByLangId($langId, $grpclsId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', Label::getLabel('MSG_Group_Class_Language_Save_Succesfully', $this->siteLangId));
        $this->set('grpclsId', $grpclsId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeClass($grpclsId)
    {
        $grpclsId = FatUtility::int($grpclsId);
        if ($grpclsId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher_id = UserAuthentication::getLoggedUserId();
        $class_details = TeacherKidsClassesSearch::getClassDetailsByTeacher($grpclsId, $teacher_id, $this->siteLangId);
        if (empty($class_details)) {
            FatUtility::dieJsonError(Label::getLabel("LBL_Invalid_Request"));
        }
        $teacherGroupClassObj = new TeacherKidsClasses($grpclsId);
        $teacherGroupClassObj->deleteClass();
        if ($teacherGroupClassObj->getError()) {
            FatUtility::dieJsonError($teacherGroupClassObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_Class_Deleted_Successfully!"));
    }

    public function cancelClass($grpclsId)
    {
        $grpclsId = FatUtility::int($grpclsId);
        if ($grpclsId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacher_id = UserAuthentication::getLoggedUserId();
        $class_details = TeacherKidsClassesSearch::getClassDetailsByTeacher($grpclsId, $teacher_id, $this->siteLangId);
        if (empty($class_details)) {
            FatUtility::dieJsonError(Label::getLabel("LBL_Invalid_Request"));
        }
        
        $db = FatApp::getDb();
        $db->startTransaction();
        $teacherGroupClassObj = new TeacherKidsClasses($grpclsId);
        $teacherGroupClassObj->cancelClass();
        if ($teacherGroupClassObj->getError()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($teacherGroupClassObj->getError());
        }
        /* update all lesson status for this class[ */
        $sLessonSrchObj = new ScheduledLessonSearch();
        $lesson = $sLessonSrchObj->getLessonInfoByGrpClsid($grpclsId, ['slesson_id', 'slesson_teacher_google_calendar_id']);
        if (!empty($lesson)) {
            $sLessonObj = new ScheduledLesson($lesson['slesson_id']);
            $sLessonObj->assignValues(['slesson_status' => ScheduledLesson::STATUS_CANCELLED]);
            if (!$sLessonObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
            if (!$sLessonObj->cancelLessonByTeacher('')) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($sLessonObj->getError());
            }
        }

        /* ] */
        $db->commitTransaction();
        if (!empty($lesson['slesson_teacher_google_calendar_id'])) {
            $setting = UserSetting::getUserSettings($teacher_id);
            if (!empty($setting['us_google_access_token'])) {
                SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $lesson['slesson_teacher_google_calendar_id']);
            }
        }
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_Class_Cancelled_Successfully!"));
    }

    private function getFrm($data1=[])
    {
        $grpClassDuration = FatApp::getConfig('CONF_GROUP_CLASS_DURATION', FatUtility::VAR_STRING, '60');
        $grpClassDuration = ($grpClassDuration != '') ? $grpClassDuration : '60';
        $getGroupSlots = explode(',', $grpClassDuration);
        $getGroupSlots = array_map([$this, 'updateArray'], array_combine($getGroupSlots, $getGroupSlots));
        $teacher_id = UserAuthentication::getLoggedUserId();
        $frm = new Form('groupClassesFrm');
        $week_names = ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday','Thursday' => 'Thursday','Friday' => 'Friday','Saturday' => 'Saturday','Sunday' => 'Sunday'];
        $ages_names = ['6-9' => '6-9', '10-14' => '10-14', '15-17' => '15-17'];
        $frm->addHiddenField('', 'grpcls_id');
        $frm->addRequiredField(Label::getLabel('LBl_Title'), 'grpcls_title');
        $frm->addTextArea(Label::getLabel('LBl_DESCRIPTION'), 'grpcls_description')->requirements()->setRequired(true);
        $fld = $frm->addIntegerField(Label::getLabel('LBl_Max_No_Of_Group_Learners'), 'grpcls_max_learner', '', ['id' => 'grpcls_max_learner']);
        $fld->requirements()->setRequired(false);

        $fld = $frm->addIntegerField(Label::getLabel('LBl_Max_No_Of_One_On_One_Learners'), 'grpcls_max_one_on_one_learner', '', ['id' => 'grpcls_max_one_on_one_learner']);
        $fld->requirements()->setRequired(false);

        $fld = $frm->addIntegerField(Label::getLabel('LBl_Total_lesson'), 'grpcls_total_lesson', '', ['id' => 'grpcls_total_lesson']);
        $fld->requirements()->setRequired(false);

        $max_learners = FatApp::getConfig('CONF_GROUP_CLASS_MAX_LEARNERS', FatUtility::VAR_INT, 9999);
        $frm->addFileUpload(Label::getLabel('LBl_Kids_Banner_Image'), 'tlpn_image');
        $fld->requirements()->setRange(1, $max_learners);
        $frm->addSelectBox(Label::getLabel('LBl_Language'), 'grpcls_tlanguage_id', UserToLanguage::getTeachingAssoc($teacher_id, $this->siteLangId), '', [], Label::getLabel('LBL_Select'))->requirements()->setRequired(true);
        $fld = $frm->addFloatField(Label::getLabel('LBl_Group_Class_Entry_fee'), 'grpcls_entry_fee', '', ['id' => 'grpcls_entry_fee']);
        $fld->requirements()->setPositive(true);
        $fld->requirements()->setRange(0, 9999999);
        $fld = $frm->addFloatField(Label::getLabel('LBl_One_On_One_Class_Entry_fee'), 'grpcls_one_on_one_entry_fee', '', ['id' => 'grpcls_one_on_one_entry_fee']);
        $fld->requirements()->setPositive(true);
        $fld->requirements()->setRange(0, 9999999);
        $frm->addRequiredField(Label::getLabel('LBl_Start_Time'), 'grpcls_start_datetime', '', ['id' => 'grpcls_start_datetime', 'autocomplete' => 'off', 'readonly' => 'readonly']);
        $frm->addSelectBox(Label::getLabel('LBL_Duration'), 'grpcls_duration', $getGroupSlots, 60)->requirements()->setRequired(true);
        $frm->addSelectBox(Label::getLabel('LBL_Ages_Group'), 'grpcls_ages', $ages_names, 60)->requirements()->setRequired(true);
        $frm->addRequiredField(Label::getLabel('LBl_Kids_Youtube_Link'), 'grpcls_kids_youtube_link');
        $frm->addRequiredField(Label::getLabel('LBl_Kids_Title'), 'grpcls_kids_title');
        $frm->addTextArea(Label::getLabel('LBl_Class_Description'), 'grpcls_class_experience')->requirements()->setRequired(true);
        $frm->addTextArea(Label::getLabel('LBl_Kids_Class_Schedule'), 'grpcls_schedule')->requirements()->setRequired(true);
        $frm->addTextArea(Label::getLabel('LBl_Kids_Learning_Goals'), 'grpcls_learning_goals')->requirements()->setRequired(true);
        $frm->addTextArea(Label::getLabel('LBl_Kids_Supply_List'), 'grpcls_supply_list')->requirements()->setRequired(true);
        $frm->addTextArea(Label::getLabel('LBl_Kids_Parental_Guidance'), 'grpcls_parental_guidance')->requirements()->setRequired(true);

        foreach ($week_names as $key => $week) {
            $speekLangField = $frm->addCheckBox($week, 'grpcls_weeks[]', $week, false, 0);
            if (in_array($week, $data1)) { 
                $speekLangField->checked = true;
            }
                $speekLangField->value = $key;
        }
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

    public function updateArray($value)
    {
        return $value . ' ' . Label::getLabel('LBL_Mints');
    }

    protected function getSearchForm()
    {
        $form = new Form('frmSrch');
        $statusArray = TeacherKidsClasses::getStatusArr();
        unset($statusArray[TeacherKidsClasses::STATUS_PENDING]);
        $form->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $form->addSelectBox(Label::getLabel('LBL_Status'), 'status', $statusArray, '', [], Label::getLabel('LBL_All'))->requirements()->setInt();
        $field = $form->addHiddenField('', 'page', 1);
        $field->requirements()->setIntPositive();
        $btnSubmit = $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnReset = $form->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        $btnSubmit->attachField($btnReset);
        return $form;
    }

}
