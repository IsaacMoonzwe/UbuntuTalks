<?php

class VirtualSessionsComments extends MyAppModel
{

    const DB_TBL = 'tbl_virtual_session_comments';
    const DB_TBL_PREFIX = 'virtual_session_comments_';
    const DB_TBL_LANG = 'tbl_virtual_session_comments_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_virtual_session_comments_id = t.virtual_session_comments_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.virtual_session_comments_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.virtual_session_comments_deleted', '=', applicationConstants::NO);
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('virtual_session_comments_deleted', '=', applicationConstants::NO);
        $srch->addCondition('virtual_session_comments_id', '=', $testimonialId);
        $srch->addFld('virtual_session_comments_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['virtual_session_comments_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
