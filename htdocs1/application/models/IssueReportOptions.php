<?php

class IssueReportOptions extends MyAppModel
{

    const DB_TBL = 'tbl_issue_report_options';
    const DB_TBL_PREFIX = 'tissueopt_';
    const DB_TBL_LANG = 'tbl_issue_report_options_lang';
    const DB_TBL_LANG_PREFIX = 'tissueoptlang_';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public function getAllOptions($langId)
    {
        $srch = new IssueReportOptionsSearch($langId);
        $srch->addOrder(self::DB_TBL_PREFIX . 'display_order', 'asc');
        return $srch;
    }

    public static function getOptionsArray(int $langId, $userType = NULL): array
    {
        $srch = new IssueReportOptionsSearch($langId);
        $srch->addMultipleFields(['tissueopt_id', 'IFNULL(tissueoptlang_title, tissueopt_identifier)',]);
        if (!is_null($userType)) {
            $srch->findByCriteria(['tissueopt_user_type' => [$userType, 0]]);
        }
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    public function deleteOption($optId)
    {
        $db = FatApp::getDb();
        $langDelete = $db->deleteRecords(static::DB_TBL_LANG, ['smt' => 'tissueoptlang_tissueopt_id = ?', 'vals' => [$optId]]);
        if (!$db->deleteRecords(static::DB_TBL, ['smt' => 'tissueopt_id = ?', 'vals' => [$optId]]) && !$langDelete) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

}
