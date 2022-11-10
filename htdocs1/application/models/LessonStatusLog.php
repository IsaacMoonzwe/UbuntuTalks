<?php

class LessonStatusLog extends MyAppModel
{

    const DB_TBL = 'tbl_lesson_status_log';
    const DB_TBL_PREFIX = 'lesstslog_';
    const RESCHEDULED_REPORT = 1;
    const CANCELLED_REPORT = 2;
    const UPCOMING_REPORT = 3;
    const COMPLETED_REPORT = 4;
    const BOTH_REPORT = 3;

    private $lessonDetailId;
    private $lessonData;

    public function __construct(int $lessonDetailId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $lessonDetailId);
        if ($lessonDetailId > 0) {
            $this->lessonDetailId = $lessonDetailId;
            $scheduledLessonDetailsSearch = new ScheduledLessonDetailsSearch();
            $this->lessonData = $scheduledLessonDetailsSearch->getDetailsById($this->lessonDetailId);
        }
    }

    public static function getSearchObject(string $alias = 'lsl'): object
    {
        $srch = new SearchBase(static::DB_TBL, $alias);
        return $srch;
    }

    public function save()
    {
        if ($this->getMainTableRecordId() == 0) {
            $this->setFldValue('lesstslog_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    public function addLog(int $status, int $userType, int $userId, string $comment): bool
    {
        $lessonStatusLogArr = [
            'lesstslog_slesson_id' => $this->lessonData['slesson_id'],
            'lesstslog_sldetail_id' => $this->lessonData['sldetail_id'],
            'lesstslog_prev_status' => $this->lessonData['slesson_status'],
            'lesstslog_current_status' => $status,
            'lesstslog_prev_start_date' => $this->lessonData['slesson_date'],
            'lesstslog_prev_end_time' => $this->lessonData['slesson_end_time'],
            'lesstslog_prev_start_time' => $this->lessonData['slesson_start_time'],
            'lesstslog_prev_end_date' => $this->lessonData['slesson_end_date'],
            'lesstslog_updated_by_user_id' => $userId,
            'lesstslog_updated_by_user_type' => $userType,
            'lesstslog_comment' => $comment
        ];
        $lessonStatusLog = new LessonStatusLog();
        $lessonStatusLog->assignValues($lessonStatusLogArr);
        return (bool) $lessonStatusLog->save();
    }

}
