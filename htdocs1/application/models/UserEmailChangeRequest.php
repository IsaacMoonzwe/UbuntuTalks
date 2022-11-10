<?php

class UserEmailChangeRequest extends MyAppModel
{

    const DB_TBL = 'tbl_user_email_change_request';
    const DB_TBL_PREFIX = 'uecreq_';

    public function __construct($requestID = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $requestID);
    }

    public function save()
    {
        return parent::save();
    }

    public function deleteOldLinkforUser($linkid)
    {
        $db = FatApp::getDb();
        if (!$result = $db->deleteRecords(static::DB_TBL, ['smt' => static::DB_TBL_PREFIX . 'user_id = ?', 'vals' => [$linkid]])) {
            return false;
        }
        return true;
    }

    public function checkUserRequest($_token)
    {
        $db = FatApp::getDb();
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'token', '=', $_token);
        $srch->addCondition(static::DB_TBL_PREFIX . 'expire', '>=', date('Y-m-d h:i:s'));
        $srch->addCondition(static::DB_TBL_PREFIX . 'status', '=', 0);
        return $db->fetch($srch->getResultSet());
    }

    public function checkPendingRequestForUser($userid = 0)
    {
        if ($userid < 1) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $db = FatApp::getDb();
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'user_id', '=', $userid);
        $srch->addCondition(static::DB_TBL_PREFIX . 'expire', '>=', date('Y-m-d h:i:s'));
        $srch->addCondition(static::DB_TBL_PREFIX . 'status', '=', 0);
        return $db->fetch($srch->getResultSet());
    }

    public function updateUserRequestStatus()
    {
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $record = new TableRecord(static::DB_TBL);
        $arrFlds = [
            static::DB_TBL_PREFIX . 'status' => 1,
            static::DB_TBL_PREFIX . 'updated' => date('Y-m-d H:i:s')
        ];
        $record->setFldValue(static::DB_TBL_PREFIX . 'id', $this->getMainTableRecordId());
        $record->assignValues($arrFlds);
        if (!$record->addNew([], $arrFlds)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public static function deleteUserEmailChangeRequestDataByUserId($userId)
    {
        FatApp::getDb()->deleteRecords(static::DB_TBL, array('smt' => 'uecreq_user_id = ?', 'vals' => array($userId)));
        return true;
    }
}
