<?php

class TeacherWeeklyScheduleSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true, $doNotLimitRecords = true)
    {
        parent::__construct(TeacherWeeklySchedule::DB_TBL, 'p');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if (true === $doNotLimitRecords) {
            $this->doNotLimitRecords();
        }
    }

}
