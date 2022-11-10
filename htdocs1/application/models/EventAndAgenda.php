<?php

class EventAndAgenda extends MyAppModel
{

    const DB_TBL = 'tbl_event_and_agenda';
    const DB_TBL_PREFIX = 'event_and_agenda_';
    const DB_TBL_LANG = 'tbl_event_and_agenda_lang';

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
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.testimoniallang_event_and_agenda_id = t.event_and_agenda_id AND testimoniallang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.event_and_agenda_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addCondition('t.event_and_agenda_deleted', '=', applicationConstants::NO);
        return $srch;
    }

    public function canRecordMarkDelete($testimonialId)
    {
        $srch = static::getSearchObject(0, false);
        $srch->addCondition('event_and_agenda_deleted', '=', applicationConstants::NO);
        $srch->addCondition('event_and_agenda_id', '=', $testimonialId);
        $srch->addFld('event_and_agenda_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['event_and_agenda_id'] == $testimonialId) {
            return true;
        }
        return false;
    }

}
