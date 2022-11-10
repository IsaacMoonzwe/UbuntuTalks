<?php

class EventsSponsorshipCategories extends MyAppModel
{

    const DB_TBL = 'tbl_events_sponsorship_categories';
    const DB_TBL_PREFIX = 'events_sponsorship_categories_';
    const DB_TBL_LANG = 'tbl_events_sponsorship_categories_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_testimonial_id = t.events_sponsorship_categories_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.events_sponsorship_categories_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.events_sponsorship_categories_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.events_sponsorship_categories_active', 'DESC');
        $srch->addOrder('t.events_sponsorship_categories_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('events_sponsorship_categories_deleted', '=', applicationConstants::NO);
        $srch->addCondition('events_sponsorship_categories_id', '=', $testimonialId);
        $srch->addFld('events_sponsorship_categories_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['events_sponsorship_categories_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
