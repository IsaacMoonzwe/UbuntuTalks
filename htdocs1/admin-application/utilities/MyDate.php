<?php

class MyDate extends FatDate
{

    public static function format($date, $showTime = false, $useTimeZone = true, $timeZone = '')
    {
        if ('' == $timeZone) {
            $timeZone = static::getTimeZone();
        }
        return parent::format($date, $showTime, $useTimeZone, $timeZone);
    }

    public static function getDateAndTimeDisclaimer()
    {
        $str = Label::getLabel('LBL_All_Date_&_Times_are_showing_in_{time-zone-abbr},_Current_Date_&_Time:_{current-date-time}');
        $arr = ["{time-zone-abbr}" => date('T'), "{current-date-time}" => date('d-M-Y H:i:s A T')];
        foreach ($arr as $key => $val) {
            $str = str_replace($key, $val, $str);
        }
        echo $str;
    }

    public static function getTimeZone()
    {
        return FatApp::getConfig('CONF_TIMEZONE', FatUtility::VAR_STRING, date_default_timezone_get());
    }

    /** custom function for change according to timezone * */
    public static function changeDateTimezone($date, $fromTimezone, $toTimezone)
    {
        return parent::changeDateTimezone($date, $fromTimezone, $toTimezone);
    }

    public static function convertTimeFromSystemToUserTimezone($format, $dateTime, $showtime, $timeZone, $removeDstTime = false)
    {
        if (substr($dateTime, 0, 10) === '0000-00-00') {
            return $dateTime;
        }
        if ($timeZone == '') {
            $timeZone = self::getTimeZone();
        }
        if ($removeDstTime) {
            $dateTime = date($format, strtotime('-1 hours', strtotime($dateTime)));
        }
        $changedDate = self::format(date('Y-m-d H:i:s', strtotime($dateTime)), $showtime, true, $timeZone);
        return date($format, strtotime($changedDate));
    }

    public static function timezoneConvertedTime($format, $dateTime, $showtime, $timeZone)
    {
        return static::convertTimeFromSystemToUserTimezone($format, $dateTime, $showtime, $timeZone);
    }

    public static function getUserTimeZone($userId = 0)
    {
        $user_timezone = '';
        if ($userId > 0) {
            $userRow = User::getAttributesById($userId, ['user_timezone']);
            $user_timezone = $userRow['user_timezone'];
        } else {
            if (UserAuthentication::isUserLogged()) {
                $userRow = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_timezone']);
                $user_timezone = $userRow['user_timezone'];
            } else {
                $user_timezone = $_COOKIE['user_timezone'] ?? self::getTimeZone();
            }
        }
        if (empty($user_timezone)) {
            $user_timezone = $_COOKIE['user_timezone'] ?? self::getTimeZone();
        }
        return $user_timezone;
    }

    public static function setUserTimeZone()
    {
        if (UserAuthentication::isUserLogged()) {
            $userDataRow = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_timezone']);
            $user_timezone = $userDataRow['user_timezone'];
            $cookieConsent = CommonHelper::getCookieConsent();
            $isActivePreferencesCookie = (!empty($cookieConsent[UserCookieConsent::COOKIE_PREFERENCES_FIELD]));
            if (!empty($user_timezone) && $isActivePreferencesCookie) {
                CommonHelper::setCookie("user_timezone", $user_timezone, time() + 365 * 24 * 60 * 60, CONF_WEBROOT_FRONTEND, '', true);
            }
        }
    }

    public static function getDayNumber($date)
    {
        $number = date('N', strtotime($date));
        if (7 == $number) { //== Sunday is 0 in full-calendar
            $number = 0;
        }
        return $number;
    }

    public static function displayTimezoneString($echoTimeZone = true)
    {
        $user_timezone = self::getUserTimeZone();
        $string = sprintf(Label::getLabel("LBL_Timezone_:_UTC_%s"), CommonHelper::getDateOrTimeByTimeZone($user_timezone, ' P'));
        if ($echoTimeZone) {
            echo $string;
            return;
        }
        return $string;
    }

    public static function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        // Declare an empty array
        $array = [];
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }

    public static function week_between_two_dates($date1, $date2)
    {
        $first = new DateTime($date1);
        $second = new DateTime($date2);
        if ($date1 > $date2) {
            return self::week_between_two_dates($date2, $date1);
        }
        return floor($first->diff($second)->days / 7);
    }

    public static function timeDiff($date1, $date2)
    {
        $first = new DateTime($date1);
        $second = new DateTime($date2);
        return $first->diff($second);
    }

    public static function getOffset(string $timeZone = 'UTC'): string
    {
        $dateTimeZone = new DateTimeZone($timeZone);
        $dateTime = new DateTime("now", $dateTimeZone);
        return $dateTime->format('P');
    }

    public static function timeZoneListing(): array
    {
        $timeZoneList = Timezone::getAllByLang(CommonHelper::getLangId());
        $finalArray = [];
        foreach ($timeZoneList as $key => $timezone) {
            $finalArray[$key] = sprintf(Label::getLabel('LBL_(TIMEZONE_%s)_%s'), $timezone['timezone_offset'], $timezone['timezone_name']);
        }
        return $finalArray;
    }

    public static function getIdentifiers()
    {
        return Timezone::getAssocByLang(CommonHelper::getLangId());
    }

    public static function getWeekStartAndEndDate(DateTime $dateTime, string $format = 'Y-m-d', bool $sundayMidNight = false): array
    {
        $dateTime = $dateTime->modify('last saturday')->modify('+1 day');
        return [
            'weekStart' => $dateTime->format($format),
            'weekEnd' => ($sundayMidNight) ?  $dateTime->modify('next saturday +1 day')->format($format) : $dateTime->modify('next saturday')->format($format),
        ];
    }

    public static function changeWeekDaysToDate(array $weekDays, array $timeSlotArr = []): array
    {
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $newWeekDayArray = [];
        foreach ($weekDays as $key => $day) {
            $dateTime = new DateTime();
            $dateTime->setISODate(2018, 2, $day);
            $day = $dateTime->format('d');
            $date = "2018-01-" . $day;
            if (!empty($timeSlotArr)) {
                foreach ($timeSlotArr as $timeKey => $timeSlot) {
                    $startDateTime = $date . ' ' . $timeSlot['startTime'];
                    $endDateTime = $date . ' ' . $timeSlot['endTime'];
                    $startDateTime = MyDate::changeDateTimezone($startDateTime, $user_timezone, $systemTimeZone);
                    $endDateTime = MyDate::changeDateTimezone($endDateTime, $user_timezone, $systemTimeZone);
                    $newWeekDayArray[] = [
                        'startDate' => $startDateTime,
                        'endDate' => $endDateTime
                    ];
                }
            } else {
                $dateStart = $date . " 00:00:00";
                $date = date('Y-m-d', strtotime($date . " +1 day"));
                $dateEnd = $date . " 00:00:00";
                $dateStart = MyDate::changeDateTimezone($dateStart, $user_timezone, $systemTimeZone);
                $dateEnd = MyDate::changeDateTimezone($dateEnd, $user_timezone, $systemTimeZone);
                $newWeekDayArray[] = [
                    'startDate' => $dateStart,
                    'endDate' => $dateEnd,
                ];
            }
        }
        return $newWeekDayArray;
    }

    public static function hoursDiff(string $toDate, string $fromDate = '', int $roundUpTo = 2): float
    {
        $fromDate = $fromDate ?: date('Y-m-d H:i:s');
        return round((strtotime($toDate) - strtotime($fromDate)) / 3600, $roundUpTo);
    }

    public static function getMonthStartAndEndDate(DateTime $dateTime): array
    {
        return [
            'monthStart' => $dateTime->modify('first day of this month')->format('Y-m-d'),
            'monthEnd' => $dateTime->modify('last day of this month')->format('Y-m-d'),
        ];
    }

    /**
     * Time Difference In Hours
     * @param string $date1
     * @param string $date2
     * @return int $hours
     */
    public static function timeDiffInHours($date1, $date2): int
    {
        $hours = round((strtotime($date2) - strtotime($date1)) / 3600, 1);
        if ($hours < 1) {
            return 0;
        }
        return $hours;
    }

    public static function timeDiffInMints($date1, $date2): int
    {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        $difference = $date1->diff($date2);
        $minutes = $difference->days * 24 * 60;
        $minutes += $difference->h * 60;
        $minutes += $difference->i;
        return $minutes;
    }

    public static function isDateWithDST(string $dateTime = '', string $timeZone = '')
    {
        $dateTime = (empty($dateTime)) ? date('Y-m-d H:i:s') : $dateTime;
        $timeZone = (empty($timeZone)) ? self::getUserTimeZone() : $timeZone;
        $tz = new DateTimeZone($timeZone);
        $theTime = strtotime($dateTime);
        $transition = $tz->getTransitions($theTime, $theTime);
        $transition = current($transition);
        return $transition['isdst'];
    }

    public static function changeSystemDateWithDST($date, $timeZone)
    {
        $tz = new DateTimeZone($timeZone);
        $theTime = strtotime($date);
        $transition = $tz->getTransitions($theTime, $theTime);
        $transition = current($transition);
        if (!$transition['isdst']) {
            return date('Y-m-d H:i:s', strtotime($date));
        }
        return date('Y-m-d H:i:s', strtotime('+1 hours', strtotime($date)));
    }

    /**
     * Get hours and minuties formatted string.
     *
     * @param integer $seconds
     * @param string  $format
     *
     * @return string
     */
    public static function getHoursMinutes(int $seconds, string $format = '%02d:%02d'): string
    {
        if (empty($seconds) || !is_numeric($seconds)) {
            return false;
        }
        $minutes = round($seconds / 60);
        $hours = floor($minutes / 60);
        $remainMinutes = ($minutes % 60);
        return sprintf($format, $hours, $remainMinutes);
    }

    public function setMonthAndweekName(int $langId)
    {
        $monthName = MyDate::getAllMonthName(true, $langId);
        $monthName = array_merge($monthName['longName'], $monthName['shortName']);
        $dateName = MyDate::dayNames(true, $langId);
        $dateName = array_merge($dateName['longName'], $dateName['shortName']);
        return $this->monthDateName = array_merge($monthName, $dateName, MyDate::meridiems(true, $langId));
    }

    public function convertToLocal($dateTime, int $langId = 0)
    {
        $monthDateName = (empty($this->monthDateName)) ? $this->setMonthAndweekName($langId) : $this->monthDateName;
        return str_replace(array_keys($monthDateName), $monthDateName, $dateTime);
    }

    public static function getAllMonthName(bool $getWithKeys = false, int $langId = 0): array
    {
        $langId = (empty($langId)) ? CommonHelper::getLangId() : $langId;
        $monthName = [
            'longName' => [
                'January' => Label::getLabel('LBL_January', $langId),
                'February' => Label::getLabel('LBL_February', $langId),
                'March' => Label::getLabel('LBL_March', $langId),
                'April' => Label::getLabel('LBL_April', $langId),
                'May' => Label::getLabel('LBL_May', $langId),
                'June' => Label::getLabel('LBL_June', $langId),
                'July' => Label::getLabel('LBL_July', $langId),
                'August' => Label::getLabel('LBL_August', $langId),
                'September' => Label::getLabel('LBL_September', $langId),
                'October' => Label::getLabel('LBL_October', $langId),
                'November' => Label::getLabel('LBL_November', $langId),
                'December' => Label::getLabel('LBL_December', $langId)
            ],
            'shortName' => [
                'Jan' => Label::getLabel('LBL_Jan', $langId),
                'Feb' => Label::getLabel('LBL_Feb', $langId),
                'Mar' => Label::getLabel('LBL_Mar', $langId),
                'Apr' => Label::getLabel('LBL_Apr', $langId),
                'May' => Label::getLabel('LBL_May', $langId),
                'Jun' => Label::getLabel('LBL_Jun', $langId),
                'Jul' => Label::getLabel('LBL_Jul', $langId),
                'Aug' => Label::getLabel('LBL_Aug', $langId),
                'Sep' => Label::getLabel('LBL_Sep', $langId),
                'Oct' => Label::getLabel('LBL_Oct', $langId),
                'Nov' => Label::getLabel('LBL_Nov', $langId),
                'Dec' => Label::getLabel('LBL_Dec', $langId)
            ]
        ];
        if (!$getWithKeys) {
            return [
                'longName' => array_values($monthName['longName']),
                'shortName' => array_values($monthName['shortName']),
            ];
        }
        return $monthName;
    }

    /**
     * dayNames function
     *
     * @return array
     * 
     * Note : Plesae not change the index of days 
     */
    public static function dayNames(bool $getWithKeys = false, int $langId = 0): array
    {
        $langId = (empty($langId)) ? CommonHelper::getLangId() : $langId;
        $dayNames = [
            'longName' => [
                'Monday' => Label::getLabel('LBL_Monday', $langId),
                'Tuesday' => Label::getLabel('LBL_Tuesday', $langId),
                'Wednesday' => Label::getLabel('LBL_Wednesday', $langId),
                'Thursday' => Label::getLabel('LBL_Thursday', $langId),
                'Friday' => Label::getLabel('LBL_Friday', $langId),
                'Saturday' => Label::getLabel('LBL_Saturday', $langId),
                'Sunday' => Label::getLabel('LBL_Sunday', $langId),
            ],
            'shortName' => [
                'Mon' => Label::getLabel('LBL_Mon', $langId),
                'Tue' => Label::getLabel('LBL_Tue', $langId),
                'Wed' => Label::getLabel('LBL_Wed', $langId),
                'Thu' => Label::getLabel('LBL_Thu', $langId),
                'Fri' => Label::getLabel('LBL_Fri', $langId),
                'Sat' => Label::getLabel('LBL_Sat', $langId),
                'Sun' => Label::getLabel('LBL_Sun', $langId),
            ],
        ];
        if (!$getWithKeys) {
            return [
                'longName' => array_values($dayNames['longName']),
                'shortName' => array_values($dayNames['shortName']),
            ];
        }
        return $dayNames;
    }

    public static function meridiems(bool $getWithKeys = false, int $langId = 0)
    {
        $langId = (empty($langId)) ? CommonHelper::getLangId() : $langId;
        $meridiems = [
            'AM' => Label::getLabel('LBL_AM', $langId),
            'PM' => Label::getLabel('LBL_PM', $langId),
        ];
        if (!$getWithKeys) {
            return array_values($meridiems);
        }
        return $meridiems;
    }

}
