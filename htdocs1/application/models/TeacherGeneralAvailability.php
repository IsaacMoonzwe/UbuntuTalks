<?php

class TeacherGeneralAvailability extends MyAppModel
{

    const DB_TBL = 'tbl_teachers_general_availability';
    const DB_TBL_PREFIX = 'teacher_avl_';
    const DB_WEEK_STARTDATE = '2018-01-07 00:00:00';
    const DB_WEEK_ENDDATE = '2018-01-14 00:00:00';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getGenaralAvailabilityJsonArr($userId, $post = [], int $teacherBookingBefore = NULL)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            trigger_error(Label::getLabel('LBL_Invalid_Request'));
        }
        $srch = new TeacherGeneralAvailabilitySearch();
        $srch->joinUser();
        $srch->addMultipleFields(['tga.*', 'user_timezone']);
        $srch->addCondition('tgavl_user_id', '=', $userId);
        $srch->addOrder('tgavl_date', 'ASC');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $jsonArr = [];
        $userTimezone = MyDate::getUserTimeZone();
        if (!empty($post)) {
            $weekStartDate = $post['WeekStart'];
        } else {
            $weekStartAndEndDate = MyDate::getWeekStartAndEndDate(new DateTime());
            $weekStartDate = $weekStartAndEndDate['weekStart'];
        }
        if (!empty($rows)) {
            $weekStartDateDB = static::DB_WEEK_STARTDATE;
            $weekDiff = MyDate::week_between_two_dates($weekStartDateDB, $weekStartDate);
            $bookingBefore = $teacherBookingBefore ?? 0;
            $validStartDateTime = strtotime("+ " . $bookingBefore . " hours");
            $teacherTimeZone = (empty($rows[0]['user_timezone'])) ? MyDate::getTimeZone() : $rows[0]['user_timezone'];
            foreach ($rows as $row) {
                $dateUnixTime = strtotime($row['tgavl_date'] . ' ' . $row['tgavl_start_time']);
                $endDateUnixTime = strtotime($row['tgavl_end_date'] . ' ' . $row['tgavl_end_time']);
                $date = date('Y-m-d H:i:s', strtotime('+ ' . $weekDiff . ' weeks', $dateUnixTime));
                $endDate = date('Y-m-d H:i:s', strtotime('+ ' . $weekDiff . ' weeks', $endDateUnixTime));
                $dateUnixTime = strtotime($date);
                $endDateUnixTime = strtotime($endDate);
                if (!is_null($teacherBookingBefore)) {
                    if ($validStartDateTime > $endDateUnixTime) {
                        continue;
                    }
                    if ($validStartDateTime > $dateUnixTime) {
                        $date = date('Y-m-d H:i:s', $validStartDateTime);
                    }
                }
                $removedstTimeFromStartTime = MyDate::isDateWithDST($date, $teacherTimeZone);
                $removedstTimeFromEndTime = MyDate::isDateWithDST($endDate, $teacherTimeZone);
                $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $date, true, $userTimezone, $removedstTimeFromStartTime);
                $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $endDate, true, $userTimezone, $removedstTimeFromEndTime);
                $jsonArr[] = [
                    "title" => "",
                    "start" => $startDateTime,
                    "end" => $endDateTime,
                    '_id' => $row['tgavl_id'],
                    "classType" => 1,
                    'action' => 'fromGeneralAvailability',
                    "day" => MyDate::getDayNumber($startDateTime),
                    'className' => "slot_available"
                ];
            }
        }
        return $jsonArr;
    }

    public function deleteTeacherGeneralAvailability($tgavl_id, $userId)
    {
        $userId = FatUtility::int($userId);
        $tgavl_id = FatUtility::int($tgavl_id);
        if ($userId < 1 || $tgavl_id < 1) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $db = FatApp::getDb();
        $deleteRecords = $db->deleteRecords(TeacherGeneralAvailability::DB_TBL, ['smt' => 'tgavl_user_id = ? and tgavl_id = ?', 'vals' => [$userId, $tgavl_id]]);
        if (!$deleteRecords) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function addTeacherGeneralAvailability($post, $userId)
    {
        if (false === $post) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $postJson = json_decode($post['data'], true);
        $db = FatApp::getDb();
        $deleteRecords = $db->deleteRecords(TeacherGeneralAvailability::DB_TBL, ['smt' => 'tgavl_user_id = ?', 'vals' => [$userId]]);
        if (empty($postJson)) {
            return true;
        }
        if ($deleteRecords) {
            /* code added  on 12-07-2019 */
            $userTimezone = MyDate::getUserTimeZone();
            $systemTimeZone = MyDate::getTimeZone();
            $dbWeekStartDateUnix = strtotime(static::DB_WEEK_STARTDATE);
            $dbWeekEndDateUnix = strtotime(static::DB_WEEK_ENDDATE);
            foreach ($postJson as $val) {
                if (empty($val['startDateTime']) || empty($val['endDateTime'])) {
                    continue;
                }
                $startDateTimeUnix = strtotime($val['startDateTime']);
                $endDateTimeUnix = strtotime($val['endDateTime']);
                if ($dbWeekStartDateUnix >= $endDateTimeUnix || $startDateTimeUnix >= $dbWeekEndDateUnix) {
                    continue;
                }
                if ($startDateTimeUnix >= $endDateTimeUnix) {
                    continue;
                }
                /* code added  on 12-07-2019 */
                $startDateTime = MyDate::changeDateTimezone($val['startDateTime'], $userTimezone, $systemTimeZone);
                $endDateTime = MyDate::changeDateTimezone($val['endDateTime'], $userTimezone, $systemTimeZone);
                $isStartDateWithDST = MyDate::isDateWithDST($startDateTime, $userTimezone);
                $isEndDateWithDST = MyDate::isDateWithDST($endDateTime, $userTimezone);
                $startDateTimeUnix = strtotime($startDateTime);
                $endDateTimeUnix = strtotime($endDateTime);
                if ($isStartDateWithDST) {
                    $startDateTimeUnix = strtotime('+1 Hours', $startDateTimeUnix);
                }
                if ($isEndDateWithDST) {
                    $endDateTimeUnix = strtotime('+1 Hours', $endDateTimeUnix);
                }
                $day = MyDate::getDayNumber($startDateTime);
                $insertArr = [
                    'tgavl_day' => $day,
                    'tgavl_user_id' => $userId,
                    'tgavl_start_time' => date('H:i', $startDateTimeUnix),
                    'tgavl_end_time' => date('H:i', $endDateTimeUnix),
                    'tgavl_date' => date('Y-m-d', $startDateTimeUnix),
                    'tgavl_end_date' => date('Y-m-d', $endDateTimeUnix),
                ];
                if (!$db->insertFromArray(TeacherGeneralAvailability::DB_TBL, $insertArr)) {
                    $this->error = $db->getError();
                    return false;
                }
            }
        }
        return true;
    }

    public static function timeSlotArr()
    {
        return [
            0 => '00 - 04',
            1 => '04 - 08',
            2 => '08 - 12',
            3 => '12 - 16',
            4 => '16 - 20',
            5 => '20 - 24',
        ];
    }


    public static function twelHrsFrmtTimeSlotArr()
    {
        return [
            0 => '00 - 04 AM',
            1 => '04 - 08 AM',
            2 => '08 - 12 AM',
            3 => '12 - 04 PM',
            4 => '04 - 08 PM',
            5 => '08 - 00 PM',
        ];

        // return [
        //     0 => '06:00 - 12:00',
        //     1 => '12:00 - 18:00',
        //     2 => '18:00 - 24:00',
        //     3 => '00:00 - 06:00',
        // ];
    }
    public static function twelNewHrsFrmtTimeSlotArr()
    {
        // return [
        //     0 => '00 - 06 AM',
        //     1 => '04 - 08 AM',
        //     2 => '08 - 12 AM',
        //     3 => '12 - 04 PM',
        //     4 => '04 - 08 PM',
        //     5 => '08 - 00 PM',
        // ];

        return [
            0 => '00:00 - 06:00',
            1 => '06:00 - 12:00',
            2 => '12:00 - 18:00',
            3 => '18:00 - 24:00',
          
        ];
    }

    public static function twelHrsFrmtDayTimeSlotArr()
    {
        // return [
        //     0 => 'Morning',
        //     1 => 'Afternoon',
        //     2 => 'Evening',
        //     3 => 'Night',
        // ];
    }

    public static function timeSlots()
    {
        return [
            0 => '00:00 - 04:00',
            1 => '04:00 - 08:00',
            2 => '08:00 - 12:00',
            3 => '12:00 - 16:00',
            4 => '16:00 - 20:00',
            5 => '20:00 - 24:00',
        ];
    }

    private static function mergeDates(array $datesArray = []): array
    {
        foreach ($datesArray as $key => &$date) {
            foreach ($datesArray as $index => $value) {
                $mergeDates = false;
                if ($date['startDateTime'] == $value['endDateTime']) {
                    $mergeDates = true;
                    $date['startDateTime'] = $value['startDateTime'];
                    $date['startTime'] = $value['startTime'];
                    $date['day'] = $value['day'];
                }
                if ($date['endDateTime'] == $value['startDateTime']) {
                    $mergeDates = true;
                    $date['endDateTime'] = $value['endDateTime'];
                    $date['endTime'] = $value['endTime'];
                }
                if ($mergeDates) {
                    unset($datesArray[$index]);
                    $datesArray = self::mergeDates($datesArray);
                }
            }
        }
        return $datesArray;
    }

}
