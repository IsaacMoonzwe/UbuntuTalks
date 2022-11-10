<?php

class LessonStatsSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true, $doNotLimitRecords = true, $joinDetails = false)
    {
        parent::__construct(LessonStatusLog::DB_TBL, 'lsl');
        $doNotCalculateRecords && $this->doNotCalculateRecords();
        $doNotLimitRecords && $this->doNotLimitRecords();
        $joinDetails && $this->joinDetails();
    }

    public function joinDetails()
    {
        $this->joinTable(User::DB_TBL, 'LEFT JOIN', 'lsl.lesstslog_updated_by_user_id = u.user_id', 'u');
        $this->joinTable(ScheduledLesson::DB_TBL, 'LEFT JOIN', 'lsl.lesstslog_slesson_id  = sl.slesson_id', 'sl');
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT JOIN', 'sld.sldetail_id = lsl.lesstslog_sldetail_id', 'sld');
        $this->joinTable(User::DB_TBL, 'LEFT JOIN', 'sl.slesson_teacher_id = tu.user_id', 'tu');
        $this->joinTable(User::DB_TBL, 'LEFT JOIN', 'sld. sldetail_learner_id = su.user_id', 'su');
    }

}
