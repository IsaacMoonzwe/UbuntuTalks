<?php

class Slide extends MyAppModel
{

    const DB_TBL = 'tbl_slides';
    const DB_TBL_PREFIX = 'slide_';
    const DB_LANG_TBL = 'tbl_slides_lang';
    const TYPE_SLIDE = 1;
    const TYPE_PPC = 2;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSlideTypesArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        return [
            static::TYPE_SLIDE => Label::getLabel('LBL_Slide', $langId),
            static::TYPE_PPC => Label::getLabel('LBL_Promotion', $langId),
        ];
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'sl');
        if ($langId > 0) {
            $srch->joinTable(static::DB_LANG_TBL, 'LEFT OUTER JOIN', 'slidelang_slide_id = slide_id AND slidelang_lang_id = ' . $langId);
        }
        if ($isActive) {
            $srch->addCondition('slide_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

}
