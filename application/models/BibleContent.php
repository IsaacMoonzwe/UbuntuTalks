<?php

class BibleContent extends MyAppModel
{

    const DB_TBL = "tbl_bible_content";
    const DB_TBL_PREFIX = "biblecontent_";
    const DB_TBL_LANG = "tbl_bible_content_lang";
    const DB_TBL_LANG_PREFIX = "biblecontentlang_";

    public function __construct($id = 0)
    {
        parent::__construct(self::DB_TBL, self::DB_TBL_PREFIX . "id", $id);
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function getList($langId = 0)
    {
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . "active", '=', 1);
        if ($langId) {
            $srch->joinTable(self::DB_TBL_LANG, 'LEFT OUTER JOIN', 'biblecontent_id = biblecontentlang_biblecontent_id AND biblecontentlang_lang_id=' . $langId);
        }
        return $srch;
    }

    public static function getBibleContentById($id)
    {
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . "id", '=', $id);
        $rs = $srch->getResultSet();
        if ($srch->recordCount() < 1) {
            return [];
        }
        return FatApp::getDb()->fetch($rs);
    }

}
