<?php

class UserQualificationSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true, $doNotLimitRecords = true)
    {
        parent::__construct(UserQualification::DB_TBL, 'uq');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if (true === $doNotLimitRecords) {
            $this->doNotLimitRecords();
        }
    }

    public function getUserQualification(int $userId)
    {
        $this->addCondition('uqualification_user_id', '=', $userId);
        $this->addMultiplefields([
            'uqualification_id',
            'uqualification_title',
            'uqualification_experience_type',
            'uqualification_start_year',
            'uqualification_end_year',
            'uqualification_institute_address',
            'uqualification_institute_name',
        ]);
        $rs = $this->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        return $rows;
    }

}
