<?php

class TeacherRequest extends MyAppModel
{

    const DB_TBL = 'tbl_user_teacher_requests';
    const DB_TBL_PREFIX = 'utrequest_';
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_CANCELLED = 2;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getStatusArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::STATUS_PENDING => Label::getLabel('LBL_Pending', $langId),
            static::STATUS_APPROVED => Label::getLabel('LBL_Approved', $langId),
            static::STATUS_CANCELLED => Label::getLabel('LBL_Cancelled', $langId),
        ];
    }

    public static function isMadeMaximumAttempts($userId)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            trigger_error("User Id is not passed", E_USER_ERROR);
        }
        $srch = new TeacherRequestSearch();
        $srch->addCondition('utrequest_user_id', '=', $userId);
        $srch->addMultiplefields(['count(utrequest_id) as totalRequest']);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        $maxAttempts = FatApp::getConfig('CONF_MAX_TEACHER_REQUEST_ATTEMPT', FatUtility::VAR_INT, 3);
        if ($row && $row['totalRequest'] >= $maxAttempts) {
            return true;
        }
        return false;
    }

    public static function getData($userId)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            trigger_error("User Id is not passed", E_USER_ERROR);
        }
        $srch = new TeacherRequestSearch();
        $srch->addCondition('utrequest_user_id', '=', $userId);
        $srch->addMultiplefields(['utrequest_attempts', 'utrequest_id', 'utrequest_status']);
        $srch->addOrder('utrequest_id', 'desc');
        $rs = $srch->getResultSet();
        if ($row = FatApp::getDb()->fetch($rs)) {
            return $row;
        }
        return false;
    }

}
