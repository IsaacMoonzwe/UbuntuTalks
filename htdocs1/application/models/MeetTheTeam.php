<?php

class MeetTheTeam extends MyAppModel
{

    const DB_TBL = 'tbl_meet_the_team';
    const DB_TBL_PREFIX = 'meet_the_team_';
    const DB_TBL_LANG = 'tbl_meet_the_team_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_meet_the_team_id = t.meet_the_team_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.meet_the_team_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.meet_the_team_deleted', '=', applicationConstants::NO);
        $srch->addOrder('t.meet_the_team_active', 'DESC');
        $srch->addOrder('t.meet_the_team_display_order', 'ASC');
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('meet_the_team_deleted', '=', applicationConstants::NO);
        $srch->addCondition('meet_the_team_id', '=', $testimonialId);
        $srch->addFld('meet_the_team_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['meet_the_team_id'] == $testimonialId) {
            return true;
        }
        return false;
    }
}
