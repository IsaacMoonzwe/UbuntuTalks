<?php

class PreferenceSearch extends SearchBase
{

    public function __construct($langId = 0, $doNotCalculateRecords = true)
    {
        $langId = FatUtility::int($langId);
        parent::__construct(Preference::DB_TBL, 'p');
        if ($langId > 0) {
            $this->joinTable(Preference::DB_TBL_LANG, 'LEFT OUTER JOIN', 'pl.preferencelang_preference_id = p.preference_id AND pl.preferencelang_lang_id = ' . $langId, 'pl');
        }
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

}
