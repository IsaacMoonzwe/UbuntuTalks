<?php

class SpokenLanguageSearch extends SearchBase
{

    public function __construct($langId = 0)
    {
        parent::__construct(SpokenLanguage::DB_TBL, 'sl');
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $this->joinTable(SpokenLanguage::DB_TBL . '_lang', 'LEFT OUTER JOIN', 'sl_l.slanguagelang_slanguage_id = sl.slanguage_id AND sl_l.slanguagelang_lang_id = ' . $langId, 'sl_l');
        }
    }

    public function addChecks()
    {
        $this->addCondition('slanguage_active', '=', applicationConstants::ACTIVE);
        $this->doNotCalculateRecords();
        $this->addMultipleFields(['slanguage_id', 'IFNULL(slanguage_name, slanguage_identifier) as slanguage_name']);
    }

    public function joinActiveTeachers()
    {
        $this->joinTable(UserToLanguage::DB_TBL_TEACH, 'INNER JOIN', 'sl.slanguage_id = utl.utl_slanguage_id', 'utl');
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = utl.utl_user_id', 'u');
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
    }

}
