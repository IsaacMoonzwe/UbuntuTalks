<?php

class Timezone extends MyAppModel
{

    const DB_TBL = 'tbl_timezone';
    const DB_TBL_LANG = 'tbl_timezone_lang';
    const DB_TBL_PREFIX = 'timezone_';

    public function __construct($id = "")
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObject($langId, $active = true)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 'tz');
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'tz_l.timezonelang_timezone_id = tz.timezone_id AND timezonelang_lang_id = ' . $langId, 'tz_l');
        if ($active === true) {
            $srch->addCondition('tz.timezone_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

    public static function getAssocByLang($langId)
    {
        $srch = self::getSearchObject($langId);
        $srch->addMultipleFields(['timezone_id', 'IFNULL(timezonelang_text, timezone_name)']);
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    public static function getAllByLang($langId)
    {
        $srch = self::getSearchObject($langId);
        $srch->addMultipleFields([
            'timezone_id',
            'timezone_offset',
            'timezone_identifier',
            'IFNULL(timezonelang_text, timezone_name) as timezone_name'
        ]);
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'timezone_identifier');
    }

}
