<?php

class BenefitConcert extends MyAppModel
{

    const DB_TBL = 'tbl_benefit_concert';
    const DB_TBL_PREFIX = 'benefit_concert_';
    const DB_TBL_LANG = 'tbl_benefit_concert_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_testimonial_id = t.benefit_concert_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.benefit_concert_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.benefit_concert_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.benefit_concert_active', 'DESC');
        $srch->addOrder('t.benefit_concert_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('benefit_concert_deleted', '=', applicationConstants::NO);
        $srch->addCondition('benefit_concert_id', '=', $testimonialId);
        $srch->addFld('benefit_concert_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['benefit_concert_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
