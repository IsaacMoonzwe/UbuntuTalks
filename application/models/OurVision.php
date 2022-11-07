<?php

class OurVision extends MyAppModel
{

    const DB_TBL = 'tbl_our_vision';
    const DB_TBL_PREFIX = 'our_vision_';
    const DB_TBL_LANG = 'tbl_our_vision_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_our_vision_id = t.our_vision_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.our_vision_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.our_vision_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.our_vision_active', 'DESC');
        $srch->addOrder('t.our_vision_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('our_vision_deleted', '=', applicationConstants::NO);
        $srch->addCondition('our_vision_id', '=', $testimonialId);
        $srch->addFld('our_vision_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['our_vision_id'] == $testimonialId) {
            return true;
        }
        return false;
    }
}
