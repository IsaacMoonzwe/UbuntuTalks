<?php

class BannerLocation extends MyAppModel
{

    const DB_TBL = 'tbl_banner_locations';
    const DB_TBL_PREFIX = 'blocation_';
    const DB_LANG_TBL = 'tbl_banner_locations_lang';
    const BLOCK_FIRST_AFTER_HOMESLIDER = 1;
    const BLOCK_SECOND_AFTER_HOMESLIDER = 2;
    const BLOCK_HOW_IT_WORKS = 3;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'bl');
        if ($langId > 0) {
            $srch->joinTable(static::DB_LANG_TBL, 'LEFT OUTER JOIN', 'blocationlang_blocation_id = blocation_id AND blocationlang_lang_id = ' . $langId, 'bl_l');
        }
        if ($isActive) {
            $srch->addCondition('blocation_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

}
