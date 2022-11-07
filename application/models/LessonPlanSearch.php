<?php

class LessonPlanSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true)
    {
        parent::__construct(LessonPlan::DB_TBL, 'tlpn');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function joinScheduledLesson()
    {
        $this->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'tlpn.tlpn_slesson_id = slns.slesson_id', 'slns');
    }

}
