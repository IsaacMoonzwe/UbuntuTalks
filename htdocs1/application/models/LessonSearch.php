<?php

/**
 * Description of LessonSearch
 */
class LessonSearch extends SearchBase
{

    public function __construct(int $langId, int $userType = 1,int $kids=0)
    {
        parent::__construct(ScheduledLesson::DB_TBL, 'slesson');
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_slesson_id = slesson.slesson_id', 'sldetail');
        $this->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = sldetail.sldetail_order_id AND orders.order_type = ' . Order::TYPE_LESSON_BOOKING, 'orders');
        $this->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'op.op_order_id = orders.order_id', 'op');
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ut.user_id = slesson.slesson_teacher_id', 'ut');
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sldetail.sldetail_learner_id', 'ul');
        if($kids==0){
        $this->joinTable(TeacherGroupClasses::DB_TBL, 'LEFT JOIN', 'grpcls.grpcls_id = slesson.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherGroupClasses::DB_TBL_LANG, 'LEFT JOIN', 'gclang.grpclslang_grpcls_id = grpcls.grpcls_id and gclang.grpclslang_lang_id=' . $langId, 'gclang');
        }
        else {
            $this->joinTable(TeacherKidsClasses::DB_TBL, 'LEFT JOIN', 'grpcls.grpcls_id = slesson.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherKidsClasses::DB_TBL_LANG, 'LEFT JOIN', 'gclang.grpclslang_grpcls_id = grpcls.grpcls_id and gclang.grpclslang_lang_id=' . $langId, 'gclang');
        
        }
        $this->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'tclang.countrylang_country_id = ut.user_country_id AND tclang.countrylang_lang_id = ' . $langId, 'tclang');
        $this->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'lclang.countrylang_country_id = ul.user_country_id AND lclang.countrylang_lang_id = ' . $langId, 'lclang');
        $this->joinTable(LessonRescheduleLog::DB_TBL, 'LEFT JOIN', 'lesreschlog.lesreschlog_slesson_id=slesson.slesson_id', 'lesreschlog');
        if (ReportedIssue::USER_TYPE_TEACHER == $userType) {
            $this->joinTable(ReportedIssue::DB_TBL, 'LEFT JOIN', 'repiss.repiss_slesson_id = slesson.slesson_id', 'repiss');
        } else {
            $this->joinTable(ReportedIssue::DB_TBL, 'LEFT JOIN', 'repiss.repiss_sldetail_id = sldetail.sldetail_id', 'repiss');
        }
    }

    public function joinTeacherLessonPlans()
    {
        $this->joinTable('tbl_scheduled_lessons_to_teachers_lessons_plan', 'LEFT JOIN', 'ltp.ltp_slessonid = slesson.slesson_id', 'ltp');
        $this->joinTable(LessonPlan::DB_TBL, 'LEFT JOIN', 'tlpn.tlpn_id = ltp.ltp_tlpn_id', 'tlpn');
    }

    public function addAdditionalFields($fields = [])
    {
        foreach ($fields as $field => $alias) {
            $this->addFld($field . ' AS ' . $alias);
        }
    }

    public function addSearchDetailFields()
    {
        $fields = static::getSearchDetailFields();
        foreach ($fields as $field => $alias) {
            $this->addFld($field . ' AS ' . $alias);
        }
    }

    public function addSearchListingFields()
    {
        $fields = static::getSearchListingFields();
        foreach ($fields as $field => $alias) {
            $this->addFld($field . ' AS ' . $alias);
        }
    }

    public function applyPrimaryConditions()
    {
        
    }

    public function applySearchConditions(array $post)
    {
        if (!empty($post['keyword'] ?? '')) {
            $keywordsArr = array_unique(array_filter(explode(' ', $post['keyword'])));
            foreach ($keywordsArr as $keyword) {
                $cnd = $this->addCondition('ut.user_first_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ut.user_last_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ul.user_first_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ul.user_last_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('sldetail.sldetail_order_id', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('grpcls.grpcls_title', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('gclang.grpclslang_grpcls_title', 'like', '%' . $keyword . '%');
            }
        }
        switch ($post['class_type'] ?? '') {
            case ApplicationConstants::CLASS_TYPE_GROUP :
                $this->addCondition('slesson_grpcls_id', '>', 0);
                break;
            case ApplicationConstants::CLASS_TYPE_1_TO_1 :
                $this->addCondition('slesson_grpcls_id', '=', 0);
                break;
        }
        if (!empty($post['status'] ?? '')) {
            switch ($post['status']) {
                case ScheduledLesson::STATUS_ISSUE_REPORTED:
                    $this->addCondition('repiss.repiss_id', '>', 0);
                    break;
                case ScheduledLesson::STATUS_UPCOMING:
                    $this->addCondition('mysql_func_CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time )', '>=', date('Y-m-d H:i:s'), 'AND', true);
                    $this->addCondition('slesson.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                    $this->addCondition('sldetail.sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                    break;
                case ScheduledLesson::STATUS_RESCHEDULED:
                    $this->addCondition('slesson.slesson_status', 'IN', [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_NEED_SCHEDULING]);
                default :
                    $this->addCondition('slesson.slesson_status', '=', $post['status']);
            }
        }
        if (!empty($post['grpcls_id'] ?? 0)) {
            $this->addCondition('slesson.slesson_grpcls_id', '=', FatUtility::int($post['grpcls_id']));
        }
        if (!empty($post['slesson_id'] ?? 0)) {
            $this->addCondition('slesson.slesson_id', '=', FatUtility::int($post['slesson_id']));
        }
        if (!empty($post['sldetail_id'] ?? 0)) {
            $this->addCondition('sldetail.sldetail_id', '=', FatUtility::int($post['sldetail_id']));
        }
        if (!empty($post['sldetail_learner_id'] ?? 0)) {
            $this->addCondition('sldetail.sldetail_learner_id', '=', FatUtility::int($post['sldetail_learner_id']));
        }
        if (!empty($post['slesson_teacher_id'] ?? 0)) {
            $this->addCondition('slesson.slesson_teacher_id', ' = ', FatUtility::int($post['slesson_teacher_id']));
        }
        if (!empty($post['grpcls_teacher_id'] ?? 0)) {
            $this->addCondition('grpcls.grpcls_teacher_id', ' = ', FatUtility::int($post['grpcls_teacher_id']));
        }
    }

    public function applyOrderBy($sortOrder = '')
    {
        $this->addOrder('slesson.slesson_status', 'ASC');
        $this->addOrder('upcomingLessonOrder', 'DESC');
        $this->addOrder('passedLessonsOrder', 'DESC');
        $this->addOrder('startDateTime', 'ASC');
        $this->addOrder('slesson.slesson_id', 'DESC');
    }

    /**
     * Fetch records and Formatting 
     * And attach other required data
     */
    public function fetchAll($keyField = '')
    {
        $rs = $this->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        foreach ($rows as $key => $row) {
            $rows[$key] = $row;
        }
        return $rows;
    }

    public static function getSearchDetailFields()
    {
        return [
            'slesson.slesson_id' => 'slesson_id',
			'slesson.slesson_kids_class'=>'slesson_kids_class',
            'slesson.slesson_grpcls_id' => 'slesson_grpcls_id',
            'slesson.slesson_teacher_id' => 'slesson_teacher_id',
            'slesson.slesson_slanguage_id' => 'slesson_slanguage_id',
            'slesson.slesson_has_issue' => 'slesson_has_issue',
            'slesson.slesson_status' => 'slesson_status',
            'slesson.slesson_date' => 'slesson_date',
            'slesson.slesson_end_date' => 'slesson_end_date',
            'slesson.slesson_start_time' => 'slesson_start_time',
            'slesson.slesson_end_time' => 'slesson_end_time',
			'slesson.slesson_learner_code'=>'slesson_learner_code',
			'slesson.slesson_learner_code_status'=>'slesson_learner_code_status',
            'sldetail.sldetail_is_teacher_paid' => 'sldetail_is_teacher_paid',
            'slesson.slesson_teacher_join_time' => 'slesson_teacher_join_time',
            'slesson.slesson_teacher_end_time' => 'slesson_teacher_end_time',
            'sldetail.sldetail_id' => 'sldetail_id',
            'sldetail.sldetail_order_id' => 'sldetail_order_id',
            'sldetail.sldetail_learner_id' => 'sldetail_learner_id',
            'sldetail.sldetail_learner_status' => 'sldetail_learner_status',
            'sldetail.sldetail_learner_end_time' => 'sldetail_learner_end_time',
            'sldetail.sldetail_learner_join_time' => 'sldetail_learner_join_time',
            'op.op_lesson_duration' => 'op_lesson_duration',
            'op.op_lpackage_is_free_trial' => 'op_lpackage_is_free_trial',
            'orders.order_is_paid' => 'order_is_paid',
            'IFNULL(grpclslang_grpcls_title, grpcls_title)' => 'grpcls_title',
            'ul.user_first_name' => 'learnerFname',
            'ul.user_last_name' => 'learnerLname',
            'ul.user_url_name' => 'learnerUrlName',
            'ut.user_first_name' => 'teacherFname',
            'ut.user_last_name' => 'teacherLname',
            'ut.user_url_name' => 'teacherUrlName',
            'tclang.country_name' => 'country_name',
            'lclang.country_name' => 'learnerCountryName',
            'IFNULL(repiss.repiss_id, 0)' => 'repiss_id',
            'IFNULL(repiss.repiss_status, 0)' => 'repiss_status',
            'lesreschlog.lesreschlog_id' => 'lesreschlog_id',
            'CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time)' => 'startDateTime',
            '(CASE when CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) < NOW() then 0 ELSE 1 END )' => 'upcomingLessonOrder',
            '(CASE when CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) < NOW() then CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) ELSE NOW() END )' => 'passedLessonsOrder',
            'sldetail_learner_end_time'
        ];
    }

    public static function getSearchListingFields()
    {
        return [
            'slesson.slesson_id' => 'slesson_id',
            'slesson.slesson_grpcls_id' => 'slesson_grpcls_id',
			'slesson.slesson_kids_class'=>'slesson_kids_class',
            'slesson.slesson_slanguage_id' => 'slesson_slanguage_id',
            'slesson.slesson_has_issue' => 'slesson_has_issue',
            'slesson.slesson_date' => 'slesson_date',
            'slesson.slesson_start_time' => 'slesson_start_time',
            'slesson.slesson_end_date' => 'slesson_end_date',
            'slesson.slesson_end_time' => 'slesson_end_time',
            'slesson.slesson_status' => 'slesson_status',
            'sldetail.sldetail_is_teacher_paid' => 'sldetail_is_teacher_paid',
			'slesson.slesson_learner_code'=>'slesson_learner_code',
			'slesson.slesson_learner_code_status'=>'slesson_learner_code_status',
            'slesson.slesson_teacher_id' => 'teacherId',
            'slesson.slesson_teacher_join_time' => 'slesson_teacher_join_time',
            'sldetail.sldetail_id' => 'sldetail_id',
            'sldetail.sldetail_order_id' => 'sldetail_order_id',
            'sldetail.sldetail_learner_status' => 'sldetail_learner_status',
            'sldetail.sldetail_learner_id' => 'learnerId',
            'op.op_lpackage_is_free_trial' => 'is_trial',
            'op.op_lesson_duration' => 'op_lesson_duration',
            'orders.order_is_paid' => 'order_is_paid',
            'IFNULL(grpclslang_grpcls_title, grpcls_title)' => 'grpcls_title',
            'ul.user_first_name' => 'learnerFname',
            'ul.user_last_name' => 'learnerLname',
            'ut.user_first_name' => 'teacherFname',
            'ut.user_last_name' => 'teacherLname',
            'ut.user_url_name' => 'user_url_name',
            'tclang.country_name' => 'country_name',
            'lclang.country_name' => 'learnerCountryName',
            'lesreschlog.lesreschlog_id' => 'lesreschlog_id',
            'IFNULL(repiss.repiss_id, 0)' => 'repiss_id',
            'IFNULL(repiss.repiss_status, 0)' => 'repiss_status',
            'CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time)' => 'startDateTime',
            '(CASE when CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) < NOW() then 0 ELSE 1 END )' => 'upcomingLessonOrder',
            '(CASE when CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) < NOW() then CONCAT(slesson.slesson_date, " ", slesson.slesson_start_time) ELSE NOW() END )' => 'passedLessonsOrder',
        ];
    }

}
