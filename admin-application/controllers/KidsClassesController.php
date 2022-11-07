<?php

class KidsClassesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewGroupClasses();
    }

    public function index()
    {
        $this->set('frmSrch', $this->getSearchForm());
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
        $this->_template->render();
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        $referer = CommonHelper::redirectUserReferer(true);
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $srch = TeacherKidsClassesSearch::getSearchObj($this->adminLangId);
        $keyword = FatApp::getPostedData('teacher', null, '');
        $user_id = FatApp::getPostedData('teacher_id', FatUtility::VAR_INT, -1);
        if ($user_id > 0) {
            $srch->addCondition('ut.user_id', '=', $user_id);
        } else {
            if (!empty($keyword)) {
                $keywordsArr = array_unique(array_filter(explode(' ', $keyword)));
                foreach ($keywordsArr as $kw) {
                    $cnd = $srch->addCondition('ut.user_first_name', 'like', '%' . $kw . '%');
                    $cnd->attachCondition('ut.user_last_name', 'like', '%' . $kw . '%');
                    $cnd->attachCondition('tcred.credential_username', 'like', '%' . $kw . '%');
                    $cnd->attachCondition('tcred.credential_email', 'like', '%' . $kw . '%');
                }
            }
        }
        if ($post['grpcls_start_datetime']) {
            $srch->addCondition('grpcls_start_datetime', '>=', $post['grpcls_start_datetime']);
        }
        if ($post['grpcls_end_datetime']) {
            $srch->addCondition('grpcls_end_datetime', '<=', $post['grpcls_end_datetime']);
        }
        if ($post['added_on']) {
            $srch->addCondition('grpcls_added_on', 'LIKE', $post['added_on'] . '%');
        }
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->addOrder('grpcls_start_datetime', 'DESC');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $user_timezone = MyDate::getUserTimeZone();
        $totalRecords = $srch->recordCount();
        $this->set('postedData', $post);
        $this->set('pageCount', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('postedData', $post);
        $this->set('recordCount', $srch->recordCount());
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $teachLanguages = TeachingLanguage::getAllLangs($this->adminLangId);
        $this->set('teachLanguages', $teachLanguages);
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        $this->set('referer', $referer);
        $this->set('classes', $classes);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('classStatusArr', TeacherKidsClasses::getStatusArr());
        $this->_template->render(false, false, null, false, false);
    }

    public function form($classId = 0)
    {
        $classId = FatUtility::int($classId);
        if ($classId < 1) {
            die('Invalid request');
        }
        $data = TeacherKidsClasses::getAttributesById($classId);
        $teacher_id = $data['grpcls_teacher_id'];
        $frm = $this->getFrm($teacher_id);
        $isSlotBooked = ScheduledLessonSearch::isSlotBooked($teacher_id, $data['grpcls_start_datetime'], $data['grpcls_end_datetime']);
        if ($isSlotBooked) {
            $fld = $frm->getField('grpcls_start_datetime');
            $fld->setFieldTagAttribute('disabled', 'disabled');
            $fld->setFieldTagAttribute('title', Label::getLabel("LBL_Start_Time_can_not_change_for_Booked_Class"));
            $fld->requirements()->setRequired(false);
            $fld = $frm->getField('grpcls_end_datetime');
            $fld->setFieldTagAttribute('disabled', 'disabled');
            $fld->setFieldTagAttribute('title', Label::getLabel("LBL_End_Time_can_not_change_for_Booked_Class"));
            $fld->requirements()->setRequired(false);
        }
        $frm->fill($data);
        $this->set('userId', $teacher_id);
        $this->set('classId', $classId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $post = FatApp::getPostedData();
        $grpcls_id = FatApp::getPostedData('grpcls_id', FatUtility::VAR_INT, 0);
        if ($grpcls_id > 0) {
            $class_details = TeacherKidsClasses::getAttributesById($grpcls_id);
            if (empty($class_details)) {
                FatUtility::dieJsonError(Label::getLabel("LBL_Unauthorized"));
            }
            $teacher_id = $class_details['grpcls_teacher_id'];
            $frm = $this->getFrm($teacher_id);
            $isSlotBooked = ScheduledLessonSearch::isSlotBooked($teacher_id, $class_details['grpcls_start_datetime'], $class_details['grpcls_end_datetime']);
            if ($isSlotBooked) {
                $post['grpcls_start_datetime'] = $class_details['grpcls_start_datetime'];
                $post['grpcls_end_datetime'] = $class_details['grpcls_end_datetime'];
            }
        } else {
            FatUtility::dieJsonError(Label::getLabel("INVALID_REQUEST"));
        }
        $post = $frm->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $post['grpcls_teacher_id'] = $teacher_id;
        if ($post['grpcls_start_datetime'] < date('Y-m-d H:i:s')) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Can_not_add_time_for_old_date'));
        }
        $weekStartDay = date('W', strtotime($post['grpcls_start_datetime']));
        $weekStart = date("Y-m-d", strtotime(date('Y') . "-W$weekStartDay+1"));
        $isSlotBooked = ScheduledLessonSearch::isSlotBooked($teacher_id, $post['grpcls_start_datetime'], $post['grpcls_end_datetime']);
        if ($isSlotBooked) {
            if ($grpcls_id <= 0 || $post['grpcls_start_datetime'] != $class_details['grpcls_start_datetime'] || $post['grpcls_end_datetime'] != $class_details['grpcls_end_datetime']) {
                FatUtility::dieJsonError(Label::getLabel('LBL_Slot_is_already_booked'));
            }
        }
        $tWSchObj = new TeacherWeeklySchedule();
        $isAvailable = $tWSchObj->checkCalendarTimeSlotAvailability($teacher_id, $post['grpcls_start_datetime'], $post['grpcls_end_datetime'], $weekStart);
        if ($grpcls_id == 0) {
            $post['grpcls_status'] = TeacherKidsClasses::STATUS_ACTIVE;
        }
        $tGrpClsObj = new TeacherKidsClasses($grpcls_id);
        $tGrpClsObj->assignValues($post);
        if (true !== $tGrpClsObj->save()) {
            FatUtility::dieJsonError($tGrpClsObj->getError());
        }
        $msg = Label::getLabel('LBL_Group_Class_Saved_Successfully!');
        if ($isAvailable) {
            $msg = Label::getLabel('LBL_Slot_is_already_added_for_1_to_1_class_whichever_first_booked_will_be_booked!');
        }
        FatUtility::dieJsonSuccess($msg);
    }

    public function removeClass($grpclsId)
    {
        $grpclsId = FatUtility::int($grpclsId);
        if ($grpclsId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $db = FatApp::getDb();
        $srch = TeacherKidsClassesSearch::getSearchObj($this->adminLangId);
        $srch->doNotCalculateRecords();
        $srch->setPagesize(1);
        $srch->addCondition('grpcls_id', '=', $grpclsId);
        $class_details = $db->fetch($srch->getResultSet());
        if (strtotime($class_details['grpcls_start_datetime']) <= time() || empty($class_details) || ($class_details['grpcls_status'] == TeacherKidsClasses::STATUS_COMPLETED)) {
            FatUtility::dieJsonError(Label::getLabel("LBL_INVALID_REQUEST"));
        }
        $db->startTransaction();
        /* update all lesson status for this class[ */
        $sLessonSrchObj = new ScheduledLessonSearch();
        $lessons = $sLessonSrchObj->getLessonsByClass($grpclsId);
        foreach ($lessons as $lesson) {
            if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) {
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
        }
        /* ] */
        $teacherGroupClassObj = new TeacherKidsClasses($grpclsId);
        $teacherGroupClassObj->deleteClass();
        if ($teacherGroupClassObj->getError()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($teacherGroupClassObj->getError());
        }
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_Class_Deleted_Successfully!"));
    }

    public function cancelClass($grpclsId)
    {
        $grpclsId = FatUtility::int($grpclsId);
        if ($grpclsId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $db = FatApp::getDb();
        $srch = TeacherKidsClassesSearch::getSearchObj($this->adminLangId);
        // $srch->addMultipleFields(['issrep_id']);
        $srch->doNotCalculateRecords();
        $srch->setPagesize(1);
        $srch->addCondition('grpcls_id', '=', $grpclsId);
        $class_details = $db->fetch($srch->getResultSet());
        if (empty($class_details)) {
            FatUtility::dieJsonError(Label::getLabel("LBL_Invalid_Request"));
        }
        if (strtotime($class_details['grpcls_start_datetime']) <= time() || $class_details['grpcls_status'] == TeacherKidsClasses::STATUS_COMPLETED) {
            FatUtility::dieJsonError(Label::getLabel("LBL_INVALID_REQUEST"));
        }
        $db->startTransaction();
        $teacherGroupClassObj = new TeacherKidsClasses($grpclsId);
        $teacherGroupClassObj->cancelClass();
        if ($teacherGroupClassObj->getError()) {
            $db->rollbackTransaction();
            FatUtility::dieJsonError($teacherGroupClassObj->getError());
        }
        /* update all lesson status for this class[ */
        $sLessonSrchObj = new ScheduledLessonSearch();
        $lessons = $sLessonSrchObj->getLessonsByClass($grpclsId);
        foreach ($lessons as $lesson) {
            if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) {
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
        }
        /* ] */
        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel("LBL_Class_Cancelled_Successfully!"));
    }

    public function viewJoinedLearners($grpclsId)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinGroupClass($this->adminLangId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinLearnerCountry($this->adminLangId);
        $srch->addCondition('grpcls.grpcls_id', '=', $grpclsId);
        $srch->joinTeacherSettings();
        $srch->addOrder('slesson_date', 'ASC');
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'sld.sldetail_learner_id as learnerId',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'IFNULL(learnercountry_lang.country_name, learnercountry.country_code) as learnerCountryName',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'sld.sldetail_order_id',
            'sld.sldetail_learner_status',
            'sld.sldetail_added_on',
        ]);
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        $this->set('lessons', $lessons);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->_template->render(false, false);
    }

    protected function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addHiddenField('', 'teacher_id');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $start_time_fld = $frm->addTextBox(Label::getLabel('LBl_Start_Time'), 'grpcls_start_datetime', '', ['id' => 'grpcls_start_datetime', 'autocomplete' => 'off']);
        $end_time_fld = $frm->addTextBox(Label::getLabel('LBl_End_Time'), 'grpcls_end_datetime', '', ['id' => 'grpcls_end_datetime', 'autocomplete' => 'off']);
        $frm->addTextBox(Label::getLabel('LBl_Teacher'), 'teacher');
        $frm->addSelectBox(Label::getLabel('LBl_Status'), 'status', TeacherKidsClasses::getStatusArr());
        $frm->addDateField(Label::getLabel('LBl_Added_On'), 'added_on');
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnReset = $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        $btnSubmit->attachField($btnReset);
        return $frm;
    }

    private function getFrm($teacher_id)
    {
        $frm = new Form('groupClassesFrm');
        $frm->addHiddenField('', 'grpcls_id');
        $frm->addRequiredField(Label::getLabel('LBl_Title'), 'grpcls_title');
        $frm->addTextArea(Label::getLabel('LBl_DESCRIPTION'), 'grpcls_description')->requirements()->setRequired(true);
        $fld = $frm->addIntegerField(Label::getLabel('LBl_Max_No._Of_Learners'), 'grpcls_max_learner', '', ['id' => 'grpcls_max_learner']);
        $fld->requirements()->setRange(1, 9999);
        $frm->addSelectBox(Label::getLabel('LBl_Language'), 'grpcls_tlanguage_id', UserToLanguage::getTeachingAssoc($teacher_id, $this->adminLangId))->requirements()->setRequired(true);
        $fld = $frm->addFloatField(Label::getLabel('LBl_Entry_fee'), 'grpcls_entry_fee', '', ['id' => 'grpcls_entry_fee']);
        $fld->requirements()->setIntPositive(true);
        $start_time_fld = $frm->addRequiredField(Label::getLabel('LBl_Start_Time'), 'grpcls_start_datetime', '', ['id' => 'grpcls_start_datetime', 'autocomplete' => 'off']);
        $end_time_fld = $frm->addRequiredField(Label::getLabel('LBl_End_Time'), 'grpcls_end_datetime', '', ['id' => 'grpcls_end_datetime', 'autocomplete' => 'off']);
        $end_time_fld->requirements()->setCompareWith('grpcls_start_datetime', 'gt', Label::getLabel('LBl_Start_Time'));
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

}
