<?php

class TeacherGeneralAvailabilitySearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true)
    {
        $this->db = FatApp::getDb();
        parent::__construct(TeacherGeneralAvailability::DB_TBL, 'tga');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function joinUser()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'tga.tgavl_user_id = u.user_id', 'u');
    }

}
