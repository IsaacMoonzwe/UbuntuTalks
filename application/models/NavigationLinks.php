<?php

class NavigationLinks extends MyAppModel
{

    const DB_TBL = 'tbl_navigation_links';
    const DB_TBL_PREFIX = 'nlink_';
    const DB_TBL_LANG = 'tbl_navigation_links_lang';
    const DB_TBL_LANG_PREFIX = 'nlinkslang_';
    const NAVLINK_TYPE_CMS = 0;
    //const NAVLINK_TYPE_CUSTOM_HTML = 1;
    const NAVLINK_TYPE_EXTERNAL_PAGE = 2;
    const NAVLINK_TYPE_CATEGORY_PAGE = 3;
    const NAVLINK_TARGET_CURRENT_WINDOW = "_self";
    const NAVLINK_TARGET_BLANK_WINDOW = "_blank";
    const NAVLINK_LOGIN_BOTH = 0;
    const NAVLINK_LOGIN_YES = 1;
    const NAVLINK_LOGIN_NO = 2;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObject($langId = 0, $isDeleted = false)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 'link');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'link_l.nlinkslang_nlink_id = link.' . static::tblFld('id') . ' AND link_l.nlinkslang_lang_id = ' . $langId, 'link_l');
        }
        if ($isDeleted == false) {
            $srch->addCondition('link.' . static::DB_TBL_PREFIX . 'deleted', '=', applicationConstants::NO);
        }
        return $srch;
    }

    public static function getLinkTypeArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        return [
            static::NAVLINK_TYPE_CMS => Label::getLabel('LBL_CMS_Page', $langId),
            static::NAVLINK_TYPE_EXTERNAL_PAGE => Label::getLabel('LBL_External_Page', $langId)
        ];
    }

    public static function getLinkTargetArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        return [
            static::NAVLINK_TARGET_CURRENT_WINDOW => Label::getLabel('LBL_Current_Window', $langId),
            static::NAVLINK_TARGET_BLANK_WINDOW => Label::getLabel('LBL_Blank_Window', $langId),
        ];
    }

    public static function getLinkLoginTypeArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        return [
            static::NAVLINK_LOGIN_BOTH => Label::getLabel('LBL_Both', $langId),
            static::NAVLINK_LOGIN_YES => Label::getLabel('LBL_Yes', $langId),
            static::NAVLINK_LOGIN_NO => Label::getLabel('LBL_No', $langId),
        ];
    }

}
