<?php

class AboutusSpeakerListing extends MyAppModel
{

    const DB_TBL = 'tbl_aboutus_speaker_listing';
    const DB_TBL_PREFIX = 'aboutus_speaker_listing_';
    const DB_TBL_LANG = 'tbl_aboutus_speaker_listing_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_aboutus_speaker_listing_id = t.aboutus_speaker_listing_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.aboutus_speaker_listing_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.aboutus_speaker_listing_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.aboutus_speaker_listing_active', 'DESC');
        $srch->addOrder('t.aboutus_speaker_listing_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('aboutus_speaker_listing_deleted', '=', applicationConstants::NO);
        $srch->addCondition('aboutus_speaker_listing_id', '=', $testimonialId);
        $srch->addFld('aboutus_speaker_listing_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['aboutus_speaker_listing_id'] == $testimonialId) {
            return true;
        }
        return false;
    }
}
