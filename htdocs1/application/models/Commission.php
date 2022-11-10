<?php

class Commission extends MyAppModel
{

    const DB_TBL = 'tbl_commission_settings';
    const DB_TBL_PREFIX = 'commsetting_';
    const DB_TBL_HISTORY = 'tbl_commission_setting_history';

    private $db;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
        $this->objMainTableRecord->setSensitiveFields(['commsetting_is_mandatory']);
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL, 'tcs');
        $srch->addOrder('tcs.' . static::DB_TBL_PREFIX . 'is_mandatory', 'DESC');
        return $srch;
    }

    public static function getHistorySearchObject()
    {
        $srch = new SearchBase(static::DB_TBL_HISTORY, 'tcsh');
        $srch->addOrder('tcsh.csh_added_on', 'DESC');
        return $srch;
    }

    public function addUpdateData($data)
    {
        unset($data['commsetting_id']);
        $assignValues = [
            'commsetting_user_id' => $data['commsetting_user_id'],
            'commsetting_fees' => $data['commsetting_fees'],
            'commsetting_is_grpcls' => $data['commsetting_is_grpcls'],
            'commsetting_deleted' => 0,
        ];
        if ($this->mainTableRecordId > 0) {
            $assignValues['commsetting_id'] = $this->mainTableRecordId;
        }
        if ($this->db->insertFromArray(static::DB_TBL, $assignValues, false, [], $assignValues)) {
            $this->mainTableRecordId = $this->mainTableRecordId ? $this->mainTableRecordId : $this->db->getInsertId();
            return true;
        }
        $this->error = $this->db->getError();
        return false;
    }

    public function addCommissionHistory($commissionId)
    {
        $data = Commission::getAttributesById($commissionId);
        $assignValues = [
            'csh_commsetting_id' => $data['commsetting_id'],
            'csh_commsetting_user_id' => $data['commsetting_user_id'],
            'csh_commsetting_fees' => $data['commsetting_fees'],
            'csh_commsetting_is_mandatory' => $data['commsetting_is_mandatory'],
            'csh_commsetting_deleted' => $data['commsetting_deleted'],
            'csh_added_on' => date('Y-m-d H:i:s'),
        ];
        if ($this->db->insertFromArray(static::DB_TBL_HISTORY, $assignValues)) {
            return true;
        }
        $this->error = $this->db->getError();
        return false;
    }

    public static function getCommissionSettingsObj($langId, $trashed = 0)
    {
        $langId = FatUtility::int($langId);
        $srch = self::getSearchObject();
        $srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'tcs.commsetting_user_id = tu.user_id', 'tu');
        $srch->joinTable('tbl_user_credentials', 'LEFT OUTER JOIN', 'tuc.credential_user_id = tu.user_id', 'tuc');
        $srch->addCondition('tcs.commsetting_deleted', '=', FatUtility::int($trashed));
        $srch->addMultipleFields(['tcs.*', 'CONCAT(tu.user_first_name," ",tu.user_last_name) as vendor']);
        return $srch;
    }

    public static function getCommissionHistorySettingsObj($langId, $trashed = 0)
    {
        $langId = FatUtility::int($langId);
        $srch = self::getHistorySearchObject();
        $srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'tcsh.csh_commsetting_user_id = tu.user_id', 'tu');
        $srch->joinTable('tbl_user_credentials', 'LEFT OUTER JOIN', 'tuc.credential_user_id = tu.user_id', 'tuc');
        $srch->addCondition('tcsh.csh_commsetting_deleted', '=', FatUtility::int($trashed));
        $srch->addMultipleFields(['tcsh.*', 'CONCAT(tu.user_first_name," ",tu.user_last_name) as vendor']);
        return $srch;
    }

    public static function getComissionSettingIdByUser($userId = 0)
    {
        $srch = self::getSearchObject();
        $srch->addCondition('commsetting_user_id', '=', $userId);
        $srch->addFld('commsetting_id');
        $rs = $srch->getResultSet();
        if (!$row = FatApp::getDb()->fetch($rs)) {
            return false;
        }
        return $row['commsetting_id'];
    }

    public static function getTeacherCommission($userId, $grpclsId = 0)
    {
        $srch = self::getSearchObject();
        $srch->addCondition('commsetting_user_id', '=', $userId);
        $srch->addCondition('commsetting_deleted', '=', applicationConstants::NO);
        if ($grpclsId > 0) {
            $srch->addCondition('commsetting_is_grpcls', '=', applicationConstants::YES);
        }
        $srch->addMultipleFields(['commsetting_id', 'commsetting_fees']);
        $rs = $srch->getResultSet();
        if (!$row = FatApp::getDb()->fetch($rs)) {
            $srch = self::getSearchObject();
            $srch->addCondition('commsetting_user_id', '=', 0);
            $srch->addCondition('commsetting_deleted', '=', applicationConstants::NO);
            if ($grpclsId > 0) {
                $srch->addCondition('commsetting_is_grpcls', '=', applicationConstants::YES);
            }
            $srch->addMultipleFields(['commsetting_id', 'commsetting_fees']);
            $rs = $srch->getResultSet();
            if (!$row = FatApp::getDb()->fetch($rs)) {
                return false;
            }
            return $row;
        }
        return $row;
    }

}
