<?php

class UserSettingSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true)
    {
        parent::__construct(UserSetting::DB_TBL, 'p');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function joinLanguageTable($langId = 0)
    {
        $langId = FatUtility::int($langId);
        $this->joinTable(SpokenLanguage::DB_TBL, 'LEFT JOIN', 'us_teach_slanguage_id = slanguage_id', 'sl');
        if ($langId > 0) {
            $this->joinTable(SpokenLanguage::DB_TBL_LANG, 'LEFT JOIN', 'sl.slanguage_id = sl_lang.slanguagelang_slanguage_id AND slanguagelang_lang_id = ' . $langId, 'sl_lang');
        }
    }

    public function getUserSiteLang(int $userId = 0)
    {
        $this->joinTable(Language::DB_TBL, 'INNER JOIN', 'language_id = us_site_lang', 'lang');
        $this->addCondition('us_user_id', '=', $userId);
        $this->addCondition('language_active', '=', applicationConstants::YES);
        $this->addMultipleFields(['language_id', 'language_code']);
    }

    public function getUserVideos()
    {
        
    }

}
