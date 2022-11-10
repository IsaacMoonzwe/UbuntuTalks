<?php

class Speakers extends MyAppModel
{

    const DB_TBL = 'tbl_speakers';
    const DB_TBL_PREFIX = 'speakers_';
    const DB_TBL_LANG = 'tbl_speakers_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_speakers_id = t.speakers_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.speakers_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.speakers_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.speakers_active', 'DESC');
        $srch->addOrder('t.speakers_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('speakers_deleted', '=', applicationConstants::NO);
        $srch->addCondition('speakers_id', '=', $testimonialId);
        $srch->addFld('speakers_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['speakers_id'] == $testimonialId) {
            return true;
        }
        return false;
    }
}
