<?php

class PreSymposiumDinner extends MyAppModel
{

    const DB_TBL = 'tbl_pre_symposium_dinner';
    const DB_TBL_PREFIX = 'pre_symposium_dinner_';
    const DB_TBL_LANG = 'tbl_pre_symposium_dinner_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_testimonial_id = t.pre_symposium_dinner_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.pre_symposium_dinner_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.pre_symposium_dinner_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.pre_symposium_dinner_active', 'DESC');
        $srch->addOrder('t.pre_symposium_dinner_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('pre_symposium_dinner_deleted', '=', applicationConstants::NO);
        $srch->addCondition('pre_symposium_dinner_id', '=', $testimonialId);
        $srch->addFld('pre_symposium_dinner_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['pre_symposium_dinner_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
