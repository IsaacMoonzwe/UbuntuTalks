<?php

class SponsorshipCategories extends MyAppModel
{

    const DB_TBL = 'tbl_sponsorshipcategories';
    const DB_TBL_PREFIX = 'sponsorshipcategories_';
    const DB_TBL_LANG = 'tbl_sponsorshipcategories_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_sponsorshipcategories_id = t.sponsorshipcategories_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.sponsorshipcategories_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.sponsorshipcategories_deleted', '=', applicationConstants::NO);
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('sponsorshipcategories_deleted', '=', applicationConstants::NO);
        $srch->addCondition('sponsorshipcategories_id', '=', $testimonialId);
        $srch->addFld('sponsorshipcategories_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['sponsorshipcategories_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
