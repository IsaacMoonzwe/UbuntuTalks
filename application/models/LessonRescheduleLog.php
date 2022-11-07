<?php

class LessonRescheduleLog extends MyAppModel
{

    const DB_TBL = 'tbl_lesson_reschedule_log';
    const DB_TBL_PREFIX = 'lesreschlog_';

    private $scheduledLessonId;

    public function __construct(int $id = 0, int $scheduledLessonId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->scheduledLessonId = FatUtility::int($scheduledLessonId);
    }

    public static function getSearchObject(string $alias = 'lsl'): SearchBase
    {
        $srch = new SearchBase(static::DB_TBL, $alias);
        return $srch;
    }

    public function save()
    {
        if ($this->getMainTableRecordId() == 0) {
            $this->setFldValue('lesreschlog_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    public static function getLatestLessonRescheduleLog(): SearchBase
    {
        $searchObj = self::getSearchObject('lreschlog');
        $latestLogSearchObj = self::getSearchObject('latestReschLessonLog');
        $latestLogSearchObj->doNotCalculateRecords();
        $latestLogSearchObj->doNotLimitRecords();
        $latestLogSearchObj->addMultipleFields(['max(latestReschLessonLog.lesreschlog_id) as lessonLatestLogId']);
        $latestLogSearchObj->addGroupBy('latestReschLessonLog.lesreschlog_slesson_id');
        $searchObj->joinTable("(" . $latestLogSearchObj->getQuery() . ")", 'INNER JOIN', 'latestLesReschLog.lessonLatestLogId = lreschlog.lesreschlog_id', 'latestLesReschLog');
        return $searchObj;
    }

}
