<?php

class SpokenLanguage extends MyAppModel
{

    const DB_TBL = 'tbl_spoken_languages';
    const DB_TBL_LANG = 'tbl_spoken_languages_lang';
    const DB_TBL_PREFIX = 'slanguage_';
    const PROFICIENCY_TOTAL_BEGINNER = 1;
    const PROFICIENCY_BEGINNER = 2;
    const PROFICIENCY_UPPER_BEGINNER = 3;
    const PROFICIENCY_INTERMEDIATE = 4;
    const PROFICIENCY_UPPER_INTERMEDIATE = 5;
    const PROFICIENCY_ADVANCED = 6;
    const PROFICIENCY_UPPER_ADVANCED = 7;
    const PROFICIENCY_NATIVE = 8;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObject($langId = 0, $active = true)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 't');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.slanguagelang_slanguage_id = t.slanguage_id AND slanguagelang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.slanguage_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

    public static function getProficiencyArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::PROFICIENCY_TOTAL_BEGINNER => Label::getLabel('LBL_Total_Beginner', $langId),
            static::PROFICIENCY_BEGINNER => Label::getLabel('LBL_Beginner', $langId),
            static::PROFICIENCY_UPPER_BEGINNER => Label::getLabel('LBL_Upper_Beginner', $langId),
            static::PROFICIENCY_INTERMEDIATE => Label::getLabel('LBL_Intermediate', $langId),
            static::PROFICIENCY_UPPER_INTERMEDIATE => Label::getLabel('LBL_Upper_Intermediate', $langId),
            static::PROFICIENCY_ADVANCED => Label::getLabel('LBL_Advanced', $langId),
            static::PROFICIENCY_UPPER_ADVANCED => Label::getLabel('LBL_Upper_Advanced', $langId),
            static::PROFICIENCY_NATIVE => Label::getLabel('LBL_Native', $langId),
        ];
    }

    public static function getAllLangs($langId = 0, $active = false)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $spokenLangSrch = new SpokenLanguageSearch($langId);
        if ($active) {
            $spokenLangSrch->addChecks();
        }
        $spokenLangSrch->doNotCalculateRecords();
        $spokenLangSrch->addMultiplefields(['slanguage_id', 'IFNULL(slanguage_name, slanguage_identifier) as slanguage_name']);
        $spokenLangSrch->addOrder('slanguage_display_order');
        $rs = $spokenLangSrch->getResultSet();
        $spokenLanguagesArr = FatApp::getDb()->fetchAllAssoc($rs);
        return $spokenLanguagesArr;
    }

}
