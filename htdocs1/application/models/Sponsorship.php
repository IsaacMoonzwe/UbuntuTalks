<?php

class Sponsorship extends MyAppModel
{

    const DB_TBL = 'tbl_sponsorship';
    const DB_TBL_PREFIX = 'sponsorship_';
    const DB_TBL_LANG = 'tbl_sponsorship_lang';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
    }

    public static function getSearchObject($langId = 0, $active = true)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 't');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_sponsorship_id = t.sponsorship_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.sponsorship_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.sponsorship_deleted', '=', applicationConstants::NO);
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('sponsorship_deleted', '=', applicationConstants::NO);
        $srch->addCondition('sponsorship_id', '=', $testimonialId);
        $srch->addFld('sponsorship_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['sponsorship_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
