<?php

class IssueReportOptionsSearch extends SearchBase
{

    public function __construct($langId = 0, $doNotCalculateRecords = true)
    {
        $langId = FatUtility::int($langId);
        parent::__construct(IssueReportOptions::DB_TBL, 'iropt');
        if ($langId > 0) {
            $on = 'iroptLang.tissueoptlang_tissueopt_id = iropt.tissueopt_id AND iroptLang.tissueoptlang_lang_id = ' . $langId;
            $this->joinTable(IssueReportOptions::DB_TBL_LANG, 'LEFT OUTER JOIN', $on, 'iroptLang');
        }
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function findByCriteria(array $criteria = []): array
    {
        if (isset($criteria['tissueopt_user_type'])) {
            $this->addCondition('tissueopt_user_type', 'in', FatUtility::int($criteria['tissueopt_user_type']));
        }
        return FatApp::getDb()->fetchAll($this->getResultSet());
    }

}
