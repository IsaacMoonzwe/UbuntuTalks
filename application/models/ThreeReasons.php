<?php

class ThreeReasons extends MyAppModel
{

    const DB_TBL = 'tbl_three_reasons';
    const DB_TBL_PREFIX = 'three_reasons_';
    const DB_TBL_LANG = 'tbl_three_reasons_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_testimonial_id = t.three_reasons_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.three_reasons_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.three_reasons_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.three_reasons_active', 'DESC');
        $srch->addOrder('t.three_reasons_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('three_reasons_deleted', '=', applicationConstants::NO);
        $srch->addCondition('three_reasons_id', '=', $testimonialId);
        $srch->addFld('three_reasons_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['three_reasons_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
