<?php

class LessonMeetingDetail extends MyAppModel
{

    const DB_TBL = 'tbl_lesson_meeting_details';
    const DB_TBL_PREFIX = 'lmeetdetail_';
    const KEY_LS_URL = "LESSON_URL";
    const KEY_LS_ROOM_ID = "LESSON_ROOM_ID";
    const KEY_ZOOM_ID = "ZOOM_ID";
    const KEY_ZOOM_START_URL = "ZOOM_START_URL";
    const KEY_ZOOM_JOIN_URL = "ZOOM_JOIN_URL";
    const KEY_ZOOM_RAW_DATA = "ZOOM_RAW_DATA";

    private $lessonId;
    private $userId;

    public function __construct(int $lessonId, int $userId, int $id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->lessonId = $lessonId;
        $this->userId = $userId;
    }

    public static function getSearchObject()
    {
        $searchBase = new SearchBase(static::DB_TBL, 'lmeetdetail');
        return $searchBase;
    }

    public function getUserLessonUrl(): string
    {
        $lessonMeetingDetail = self::getSearchObject();
        $lessonMeetingDetail->addCondition('lmeetdetail_user_id', '=', $this->userId);
        $lessonMeetingDetail->addCondition('lmeetdetail_slesson_id', '=', $this->lessonId);
        $lessonMeetingDetail->addCondition('lmeetdetail_key', '=', self::KEY_LS_URL);
        $lessonMeetingDetail->addOrder('lmeetdetail_id', 'desc');
        $lessonMeetingDetail->setPageSize(1);
        $resultSet = $lessonMeetingDetail->getResultSet();
        $meetingData = FatApp::getDb()->fetch($resultSet);
        if (empty($meetingData['lmeetdetail_value'])) {
            $this->error = Label::getLabel('MSG_NO_RECORD_FOUND!');
            return '';
        }
        return $meetingData['lmeetdetail_value'];
    }

    public function addDetails(string $key, string $value = ''): bool
    {
        $assigenValue = [
            'lmeetdetail_key' => $key,
            'lmeetdetail_value' => $value,
            'lmeetdetail_user_id' => $this->userId,
            'lmeetdetail_slesson_id' => $this->lessonId
        ];
        $this->assignValues($assigenValue);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    public function getMeetingDetails($key): string
    {
        $lessonMeetingDetail = self::getSearchObject();
        $lessonMeetingDetail->addCondition('lmeetdetail_slesson_id', '=', $this->lessonId);
        $lessonMeetingDetail->addCondition('lmeetdetail_user_id', '=', $this->userId);
        $lessonMeetingDetail->addCondition('lmeetdetail_key', '=', $key);
        $lessonMeetingDetail->addOrder('lmeetdetail_id', 'desc');
        $lessonMeetingDetail->doNotCalculateRecords();
        $lessonMeetingDetail->setPageSize(1);
        $resultSet = $lessonMeetingDetail->getResultSet();
        $meetingData = FatApp::getDb()->fetch($resultSet);
        if (empty($meetingData['lmeetdetail_value'])) {
            $this->error = Label::getLabel('MSG_NO_RECORD_FOUND!');
            return '';
        }
        return $meetingData['lmeetdetail_value'];
    }

}
