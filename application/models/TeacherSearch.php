<?php

/**
 * Description of TeacherSearch
 */
class TeacherSearch extends SearchBase
{

    private $langId;

    /**
     * Initialize Teacher Search
     * 
     * @param type $langId
     */
    public function __construct(int $langId)
    {
        $this->langId = $langId;
        parent::__construct('tbl_users', 'teacher');
        $this->joinTable('tbl_user_credentials', 'INNER JOIN', 'cred.credential_user_id = teacher.user_id', 'cred');
        $this->joinTable('tbl_teacher_stats', 'INNER JOIN', 'testat.testat_user_id = teacher.user_id', 'testat');
        $this->doNotCalculateRecords();
    }

    /**
     * Add Search Listing Fields
     * 
     * @return void
     */
    public function addSearchListingFields(): void
    {
        $fields = static::getSearchListingFields();
        foreach ($fields as $key => $value) {
            $this->addFld($key . ' AS ' . $value);
        }
    }

    public static function getSearchListingFields(): array
    {
        return [
            'teacher.user_id' => 'user_id',
            'teacher.user_url_name' => 'user_url_name',
            'teacher.user_first_name' => 'user_first_name',
            'teacher.user_last_name' => 'user_last_name',
            'teacher.user_country_id' => 'user_country_id',
            'testat.testat_students' => 'studentIdsCnt',
            'testat.testat_lessions' => 'teacherTotLessons',
            'testat.testat_ratings' => 'teacher_rating',
            'testat.testat_reviewes' => 'totReviews',
            'testat.testat_minprice' => 'minPrice',
            'testat.testat_maxprice' => 'maxPrice',
        ];
    }

    /**
     * Apply Primary Conditions
     * 
     * @return void
     */
    public function applyPrimaryConditions(int $userId = 0): void
    {
        $this->addCondition('teacher.user_deleted', '=', 0);
        $this->addCondition('teacher.user_id', '!=', $userId);
        $this->addCondition('teacher.user_is_teacher', '=', 1);
        $this->addCondition('teacher.user_country_id', '>', 0);
        $this->addCondition('teacher.user_url_name', '!=', "");
        $this->addCondition('cred.credential_active', '=', 1);
        $this->addCondition('cred.credential_verified', '=', 1);
        $this->addCondition('testat.testat_preference', '=', 1);
        $this->addCondition('testat.testat_qualification', '=', 1);
        $this->addCondition('testat.testat_teachlang', '=', 1);
        $this->addCondition('testat.testat_speaklang', '=', 1);
        $this->addCondition('testat.testat_availability', '=', 1);
    }

    /**
     * Apply Search Conditions
     * 
     * @param array $post
     * @return void
     */
    public function applySearchConditions(array $post): void
    {
        /* Keyword */
        $keyword = trim($post['keyword'] ?? '');
        if (!empty($keyword)) {
            $cond = $this->addCondition('teacher.user_first_name', 'LIKE', '%' . $keyword . '%');
            $cond->attachCondition('teacher.user_last_name', 'LIKE', '%' . $keyword . '%');
            $fullNameField = 'mysql_func_CONCAT(teacher.user_first_name, " ", teacher.user_last_name)';
            $cond->attachCondition($fullNameField, 'LIKE', '%' . $keyword . '%', 'OR', true);
        }
        /* Teach Language */
        $teachLangId = FatUtility::int($post['teachLangId'] ?? 0);
        if ($teachLangId > 0) {
            $srch = new SearchBase('tbl_user_teach_languages', 'utl');
            $srch->joinTable('tbl_user_teach_lang_prices', 'INNER JOIN', 'utl_prc.ustelgpr_utl_id = utl.utl_id', 'utl_prc');
            $srch->addFld('DISTINCT utl_user_id as utl_user_id');
            $srch->addCondition('utl_tlanguage_id', '=', $teachLangId);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utl.utl_user_id = teacher.user_id', 'utl');
        }
        /* Speak Language */
        $speakLangs = explode(",", $post['spokenLanguage'] ?? '');
        $speakLangIds = array_filter(FatUtility::int($speakLangs));
        if (count($speakLangIds) > 0) {
            $srch = new SearchBase('tbl_user_to_spoken_languages');
            $srch->addFld('DISTINCT utsl_user_id as utsl_user_id');
            $srch->addCondition('utsl_slanguage_id', 'IN', $speakLangIds);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utsl.utsl_user_id = teacher.user_id', 'utsl');
        }
        /* Week Day and Time Slot */
        $weekDays = (array) ($post['filterWeekDays'] ?? []);
        $timeSlots = (array) ($post['filterTimeSlots'] ?? []);
        if (count($weekDays) > 0 || count($timeSlots) > 0) {
            $timeSlotArr = [];
            if (!empty($timeSlots)) {
                $timeSlotArr = CommonHelper::formatTimeSlotArr($timeSlots);
            }
            $srch = new SearchBase('tbl_teachers_general_availability');
            $srch->addFld('DISTINCT tgavl_user_id as tgavl_user_id');
            if (is_array($weekDays) && !empty($weekDays)) {
                $weekDates = MyDate::changeWeekDaysToDate($weekDays, $timeSlotArr);
                $condition = ' ( ';
                foreach ($weekDates as $weekDayKey => $date) {
                    $condition .= ($weekDayKey == 0) ? '' : ' OR ';
                    $condition .= ' ( CONCAT(`tgavl_date`," ",`tgavl_start_time`) < "' . $date['endDate'] . '" and CONCAT(`tgavl_end_date`," ",`tgavl_end_time`) > "' . $date['startDate'] . '" ) ';
                }
                $condition .= ' ) ';
                $srch->addDirectCondition($condition);
            }
            if (empty($weekDays) && !empty($timeSlotArr)) {
                $systemTimezone = MyDate::getTimeZone();
                $userTimezone = MyDate::getUserTimeZone();
                $condition = '( ';
                foreach ($timeSlotArr as $key => $formatedVal) {
                    $condition .= ($key == 0) ? '' : 'OR';
                    $startTime = date('Y-m-d') . ' ' . $formatedVal['startTime'];
                    $endTime = date('Y-m-d') . ' ' . $formatedVal['endTime'];
                    $startTime = date('H:i:s', strtotime(MyDate::changeDateTimezone($startTime, $userTimezone, $systemTimezone)));
                    $endTime = date('H:i:s', strtotime(MyDate::changeDateTimezone($endTime, $userTimezone, $systemTimezone)));
                    $condition .= ' ( CONCAT(`tgavl_date`," ",`tgavl_start_time`) <  CONCAT(`tgavl_end_date`," ","' . $endTime . '") and CONCAT(`tgavl_end_date`," ",`tgavl_end_time`) >  CONCAT(`tgavl_date`," ","' . $startTime . '") ) ';
                }
                $condition .= ' ) ';
                $srch->addDirectCondition($condition);
            }
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'tgavl.tgavl_user_id = teacher.user_id', 'tgavl');
        }
        /* From Country */
        $fromCountries = explode(",", $post['fromCountry'] ?? '');
        $fromCountries = array_filter(FatUtility::int($fromCountries));
        if (count($fromCountries)) {
            $this->addCondition('teacher.user_country_id', 'IN', $fromCountries);
        }
        /* Min & Max Price */
        $minPrice = FatUtility::float($post['minPriceRange'] ?? 0);
        $maxPrice = FatUtility::float($post['maxPriceRange'] ?? 0);
        $minPrice = CommonHelper::getDefaultCurrencyValue($minPrice, false, false);
        $maxPrice = CommonHelper::getDefaultCurrencyValue($maxPrice, false, false);
        if (!empty($minPrice) && !empty($maxPrice)) {
            $this->addCondition('testat.testat_minprice', '<=', $maxPrice);
            $this->addCondition('testat.testat_maxprice', '>=', $minPrice);
        }
        /* Preferences Filter (Teacher’s accent, Teaches level, Subjects, Test preparations, Lesson includes, Learner’s age group) */
        $preferences = explode(",", $post['preferenceFilter'] ?? '');
        $preferences = array_filter(FatUtility::int($preferences));
        if (count($preferences) > 0) {
            $srch = new SearchBase('tbl_user_to_preference');
            $srch->addFld('DISTINCT utpref_user_id as utpref_user_id');
            $srch->addCondition('utpref_preference_id', 'IN', $preferences);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $this->joinTable($subTable, 'INNER JOIN', 'utpref.utpref_user_id = teacher.user_id', 'utpref');
        }
        /* Tutor Gender */
        $genders = array_filter(FatUtility::int(explode(",", $post['gender'] ?? '')));
        if (count($genders) == 1) {
            $this->addCondition('teacher.user_gender', '=', current($genders));
        }
    }

    /**
     * Apply Order By
     * 
     * @param string $sortOrder
     * @return void
     */
    public function applyOrderBy(string $sortOrder): void
    {
        switch ($sortOrder) {
            case 'price_asc':
                $this->addOrder('testat.testat_minprice', 'ASC');
                break;
            case 'price_desc':
                $this->addOrder('testat.testat_minprice', 'DESC');
                break;
            case 'popularity_desc':
                $this->addOrder('testat.testat_students', 'DESC');
                $this->addOrder('testat.testat_lessions', 'DESC');
                $this->addOrder('testat.testat_reviewes', 'DESC');
                $this->addOrder('testat.testat_ratings', 'DESC');
                break;
            default:
                $this->addOrder('testat.testat_ratings', 'DESC');
                break;
        }
        $this->addOrder('teacher.user_id', 'ASC');
    }

    /**
     * Format Search Data
     * 
     * @param array $records
     * @param int $userId
     * @return array
     */
    public static function formatTeacherSearchData(array $records, int $userId): array
    {
        $langId = CommonHelper::getLangId();
        $teacherIds = array_column($records, 'user_id');
        $countryIds = array_column($records, 'user_country_id');
        $countries = static::getCountryNames($langId, $countryIds);
        $favorites = static::getFavoriteTeachers($userId, $teacherIds);
        $langData = static::getTeachersLangData($langId, $teacherIds);
        $teachLangs = static::getTeachLangs($langId, $teacherIds);
        $speakLangs = static::getSpeakLangs($langId, $teacherIds);
        $timeslots = static::getTimeslots($userId, $teacherIds);
        $videos = static::getYouTubeVideos($teacherIds);
        foreach ($records as $key => $record) {
            $record['uft_id'] = $favorites[$record['user_id']] ?? 0;
            $record['user_profile_info'] = $langData[$record['user_id']] ?? '';
            $record['user_country_name'] = $countries[$record['user_country_id']] ?? '';
            $record['teacherTeachLanguageName'] = $teachLangs[$record['user_id']] ?? '';
            $record['spoken_language_names'] = $speakLangs[$record['user_id']]['slanguage_name'] ?? '';
            $record['spoken_languages_proficiency'] = $speakLangs[$record['user_id']]['utsl_proficiency'] ?? '';
            $record['testat_timeslots'] = $timeslots[$record['user_id']] ?? CommonHelper::getEmptyDaySlots();
            $record['us_video_link'] = CommonHelper::validateIntroVideoLink($videos[$record['user_id']]);
            $records[$key] = $record;
        }
        return $records;
    }

    /**
     * Get YouTube Videos
     * 
     * @param array $teacherIds
     * @return array
     */
    public static function getYouTubeVideos(array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(UserSetting::DB_TBL);
        $srch->addCondition('us_user_id', 'In', $teacherIds);
        $srch->addMultipleFields(['us_user_id', 'us_video_link']);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
    }

    /**
     * Get Countries Names
     * 
     * @param int $langId
     * @param array $countryIds
     * @return array
     */
    public static function getCountryNames(int $langId, array $countryIds): array
    {
        if ($langId == 0 || count($countryIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_countries_lang', 'countrylang');
        $srch->addCondition('countrylang.countrylang_lang_id', '=', $langId);
        $srch->addCondition('countrylang.countrylang_country_id', 'IN', $countryIds);
        $srch->addMultipleFields(['countrylang_country_id', 'country_name']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Teachers LangData
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getTeachersLangData(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_users_lang', 'userlang');
        $srch->addCondition('userlang.userlang_lang_id', '=', $langId);
        $srch->addCondition('userlang.userlang_user_id', 'IN', $teacherIds);
        $srch->addMultipleFields(['userlang_user_id', 'userlang_user_profile_Info']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Favorite Teachers
     * 
     * @param int $userId
     * @param array $teacherIds
     * @return array
     */
    public static function getFavoriteTeachers(int $userId, array $teacherIds): array
    {
        if ($userId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_user_favourite_teachers', 'uft');
        $srch->addCondition('uft.uft_teacher_id', 'IN', $teacherIds);
        $srch->addCondition('uft.uft_user_id', '=', $userId);
        $srch->addMultipleFields(['uft_teacher_id', 'uft_id']);
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Teachers Teach Lang
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getTeachLangs(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_user_teach_languages', 'utl');
        $srch->joinTable('tbl_teaching_languages_lang', 'INNER JOIN', 'tlanguage.tlanguagelang_tlanguage_id = utl.utl_tlanguage_id', 'tlanguage');
        $srch->joinTable('tbl_user_teach_lang_prices', 'INNER JOIN', 'utl_prc.ustelgpr_utl_id = utl.utl_id', 'utl_prc');
        $srch->addMultipleFields(['utl.utl_user_id', 'GROUP_CONCAT(DISTINCT tlanguage.tlanguage_name) as tlanguage_name']);
        $srch->addCondition('tlanguage.tlanguagelang_lang_id', '=', $langId);
        $srch->addCondition('utl.utl_user_id', 'IN', $teacherIds);
        $srch->addGroupBy('utl.utl_user_id');
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    /**
     * Get Teachers Speak Lang
     * 
     * @param int $langId
     * @param array $teacherIds
     * @return array
     */
    public static function getSpeakLangs(int $langId, array $teacherIds): array
    {
        if ($langId == 0 || count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase('tbl_user_to_spoken_languages', 'utsl');
        $srch->joinTable('tbl_spoken_languages_lang', 'INNER JOIN', 'slanguage.slanguagelang_slanguage_id = utsl.utsl_slanguage_id', 'slanguage');
        $srch->addMultipleFields(['utsl.utsl_user_id', 'GROUP_CONCAT(utsl.utsl_proficiency) as utsl_proficiency', 'GROUP_CONCAT(slanguage.slanguage_name SEPARATOR ", ") as slanguage_name']);
        $srch->addCondition('slanguage.slanguagelang_lang_id', '=', $langId);
        $srch->addCondition('utsl.utsl_user_id', 'IN', $teacherIds);
        $srch->addGroupBy('utsl.utsl_user_id');
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($result, 'utsl_user_id');
    }

    /**
     * Get Availabile Timeslots
     * 
     * @param array $teacherIds
     * @return array
     */
    public static function getTimeslots(int $userId, array $teacherIds): array
    {
        if (count($teacherIds) == 0) {
            return [];
        }
        $srch = new SearchBase(TeacherGeneralAvailability::DB_TBL);
        $srch->addMultipleFields([
            'tgavl_day', 'tgavl_user_id',
            'CONCAT(tgavl_date, " ", tgavl_start_time) as startdate',
            'CONCAT(tgavl_end_date, " ", tgavl_end_time) as enddate',
            'user_timezone'
        ]);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'user.user_id = tgavl_user_id', 'user');
        $srch->addCondition('tgavl_user_id', 'IN', $teacherIds);
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet());
        $users = [];
        foreach ($rows as $row) {
            $users[$row['tgavl_user_id']][] = $row;
        }
        $userTimeslots = [];
        $currentDate = date('Y-m-d H:i:s');
        $systemTimezone = MyDate::getTimeZone();
        $userTimezone = MyDate::getUserTimeZone($userId);
        $userDate = MyDate::changeDateTimezone($currentDate, $userTimezone, $systemTimezone);
        $weekStartAndEndDate = MyDate::getWeekStartAndEndDate(new DateTime($userDate));
        $weekStartDateDB = TeacherGeneralAvailability::DB_WEEK_STARTDATE;
        $weekDiff = MyDate::week_between_two_dates($weekStartDateDB, $weekStartAndEndDate['weekStart'] . ' 00:00:00');
        $emptySlots = CommonHelper::getEmptyDaySlots();
        foreach ($users as $id => $user) {
            $records = [];
            $teacherTimeZone = (empty($user[0]['user_timezone'])) ? MyDate::getTimeZone() : $user[0]['user_timezone'];
            foreach ($user as $key => $row) {
                $row['startdate'] = date('Y-m-d H:i:s', strtotime($row['startdate'] . ' + ' . $weekDiff . ' weeks'));
                $row['enddate'] = date('Y-m-d H:i:s', strtotime($row['enddate'] . ' + ' . $weekDiff . ' weeks'));
                $removedstTimeFromStartTime = MyDate::isDateWithDST($row['startdate'], $teacherTimeZone);
                $removedstTimeFromEndTime = MyDate::isDateWithDST($row['enddate'], $teacherTimeZone);
                $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['startdate'], true, $userTimezone, $removedstTimeFromStartTime);
                $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['enddate'], true, $userTimezone, $removedstTimeFromEndTime);
                $row['tgavl_day'] = $row['tgavl_day'] % 6;
                $row['startdate'] = $startDateTime;
                $row['enddate'] = $endDateTime;
                $tmpRecords = static::breakIntoDays($row);
                foreach ($tmpRecords as $tmpRecord) {
                    array_push($records, $tmpRecord);
                }
            }
            $userTimeslots[$id] = $emptySlots;
            $timeSlots = [
                ['00:00:00', '04:00:00'], ['04:00:00', '08:00:00'],
                ['08:00:00', '12:00:00'], ['12:00:00', '16:00:00'],
                ['16:00:00', '20:00:00'], ['20:00:00', '00:00:00'],
            ];
            foreach ($records as $row) {
                $startdate = strtotime($row['startdate']);
                $enddate = strtotime($row['enddate']);
                $dateNumber = MyDate::getDayNumber($row['startdate']);
                foreach ($timeSlots as $index => $slotDates) {
                    $slotStart = strtotime(date('Y-m-d', $startdate) . ' ' . $slotDates[0]);
                    $slotEnd = strtotime(date('Y-m-d', $startdate) . ' ' . $slotDates[1]);
                    if ($slotDates[0] == '20:00:00') {
                        $dateTime = date('Y-m-d', $startdate) . ' ' . $slotDates[1];
                        $slotEnd = strtotime($dateTime . ' +1 day');
                    }
                    if ($slotEnd > $startdate && $enddate > $slotStart) {
                        $startDateTime = max($slotStart, $startdate);
                        $endDateTime = min($slotEnd, $enddate);
                        $diffInSec = ($endDateTime - $startDateTime);
                        $userTimeslots[$id][$dateNumber][$index] += $diffInSec;
                    }
                }
            }
        }
        return $userTimeslots;
    }

    private static function breakIntoDays(array $row, array $records = []): array
    {
        if (date('Y-m-d', strtotime($row['startdate'])) != date('Y-m-d', strtotime($row['enddate']))) {
            $endDateTime = date('Y-m-d', strtotime($row['startdate'] . ' +1 day')) . ' 00:00:00';
            array_push($records, [
                'tgavl_day' => MyDate::getDayNumber($row['startdate']),
                'startdate' => $row['startdate'],
                'enddate' => $endDateTime
            ]);
            $newRow = ['tgavl_day' => MyDate::getDayNumber($endDateTime), 'startdate' => $endDateTime, 'enddate' => $row['enddate']];
            return static::breakIntoDays($newRow, $records);
        } else {
            array_push($records, $row);
            return $records;
        }
    }

    /**
     * Get Record Count
     * to be updated as per requirements
     * 
     * @return int
     */
    public function getRecordCount(): int
    {
        $db = FatApp::getDb();
        $order = $this->order;
        $page = $this->page;
        $pageSize = $this->pageSize;
        $this->limitRecords = false;
        $this->order = [];
        $qry = $this->getQuery() . ' LIMIT ' . SEARCH_MAX_COUNT . ', 1';
        if ($db->totalRecords($db->query($qry)) > 0) {
            $recordCount = SEARCH_MAX_COUNT;
        } else {
            if (empty($this->groupby) && empty($this->havings)) {
                $this->addFld('COUNT(user_id) AS total');
                $rs = $db->query($this->getQuery());
            } else {
                $this->addFld('user_id as user_id');
                $rs = $db->query('SELECT COUNT(user_id) AS total FROM (' . $this->getQuery() . ') t');
            }
            $recordCount = FatUtility::int($db->fetch($rs)['total'] ?? 0);
        }
        $this->order = $order;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->limitRecords = true;
        return $recordCount;
    }

    /**
     * Remove All Conditions
     * 
     * @return void
     */
    public function removeAllConditions(): void
    {
        $this->conditions = [];
    }

    /**
     * Join setting Tabel
     * 
     * @return void
     */
    public function joinSettingTabel(): void
    {
        $this->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'us.us_user_id = teacher.user_id', 'us');
    }

}
