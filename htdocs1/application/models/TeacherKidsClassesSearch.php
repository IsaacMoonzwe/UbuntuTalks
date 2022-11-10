<?php

class TeacherKidsClassesSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true, $skipDeleted = true)
    {
        parent::__construct(TeacherKidsClasses::DB_TBL, 'grpcls');
        if (true === $doNotCalculateRecords) {
            // $this->addCondition('grpcls.grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
            $this->doNotCalculateRecords();
        }
        if ($skipDeleted == true) {
            // $this->addCondition('grpcls.grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
      
            $this->addCondition('grpcls_deleted', '=', applicationConstants::NO);
        }
    }
   public function addSearchListingFields(): void
    {
        $fields = static::getSearchListingFields();
        foreach ($fields as $key => $value) {
                $this->addFld($key . ' AS ' . $value);
        }
    }
    public static function getSearchListingFields(): array
    {
        // $this->joinTable('tbl_user_credentials', 'INNER JOIN', 'cred.credential_user_id = grpcls.grpcls_teacher_id', 'cred');
        return [
            'count(grpcls.grpcls_id)'=>'groupCount',
            'grpcls.grpcls_id' => 'grpcls_id',
            'grpcls.grpcls_title' => 'grpcls_title',
            'grpcls.grpcls_weeks' => 'grpcls_weeks',
            'grpcls.grpcls_max_learner' => 'grpcls_max_learner',
            'grpcls.grpcls_description' => 'grpcls_description',
            'grpcls.grpcls_entry_fee' => 'grpcls_entry_fee',
            'grpcls.grpcls_start_datetime	' => 'grpcls_start_datetime	',
            'grpcls.grpcls_end_datetime' => 'grpcls_end_datetime',
            'grpcls.grpcls_added_on' => 'grpcls_added_on',
            'grpcls.grpcls_status' => 'grpcls_status',
            'grpcls.grpcls_ages' => 'grpcls_ages',
          
           
        ];
    }

    public function applyPrimaryConditions(int $userId = 0): void
    {

        $this->addCondition('grpcls.grpcls_deleted', '=', 0);
        $this->addCondition('grpcls.grpcls_id', '!=', $userId);
        // $this->addCondition('grpcls.user_is_teacher', '=', 1);
        // $this->addCondition('grpcls.user_country_id', '>', 0);
        // $this->addCondition('grpcls.user_url_name', '!=', "");
     }

     public function applySearchConditions(array $post): void
     {
         /* Keyword */
        //  $this->addCondition('grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
         $keyword = trim($post['keyword'] ?? '');
         if (!empty($keyword)) {
            
             $cond=  $this->joinTable('tbl_user_credentials', 'INNER JOIN', 'cred.credential_user_id = grpcls.grpcls_teacher_id', 'cred');
            //   $this->addCondition('cred.credential_username', 'LIKE', '%' . $keyword . '%');
             $cond->addCondition('cred.credential_username', 'LIKE', '%' . $keyword . '%');
            //  $fullNameField = 'mysql_func_CONCAT(cred.user_first_name, " ", cred.user_last_name)';
            //  $cond->attachCondition($fullNameField, 'LIKE', '%' . $keyword . '%', 'OR', true);
         }

         /* Teach Language */
         $teachLangId = FatUtility::int($post['language'] ?? 0);
         if ($teachLangId > 0) {
         
            $srch->addCondition('grpcls_tlanguage_id', '=', $post['language']);
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
             $srch = new SearchBase('tbl_talkkids_classes');
             $srch->addFld('DISTINCT grpcls_weeks as grpcls_weeks');
             if (is_array($weekDays) && !empty($weekDays)) {
                 $weekDates = MyDate::changeWeekDaysToDate($weekDays, $timeSlotArr);
                //  $condition = ' ( ';
                 foreach ($weekDates as $weekDayKey => $date) {
                     
                //      $condition .= ($weekDayKey == 0) ? '' : ' OR ';
                //      $condition .= ' ( CONCAT(`grpcls_start_datetime`,"") < "' . $date['endDate'] . '" and CONCAT(`grpcls_end_datetime`,"") > "' . $date['startDate'] . '" ) ';
                 }
                //  $condition .= ' ) ';
                //  $srch->addDirectCondition($condition);
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
                     $condition .= ' ( CONCAT(`grpcls_start_datetime`,"") <  CONCAT("' . $endTime . '","") and CONCAT(`grpcls_end_datetime`,"") >  CONCAT("' . $startTime . '","") ) ';
                    }
                 $condition .= ' ) ';
                 $srch->addDirectCondition($condition);
             }
             $srch->doNotCalculateRecords();
             $srch->doNotLimitRecords();
             $subTable = '(' . $srch->getQuery() . ')';
             $this->joinTable($subTable, 'INNER JOIN', 'cred.credential_user_id = grpcls.grpcls_teacher_id', 'tgavl');
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
        
             $this->addCondition('grpcls.grpcls_entry_fee', '<=', $maxPrice);
             $this->addCondition('grpcls.grpcls_entry_fee', '>=', $minPrice);
         }
        //  $this->addCondition('grpcls.grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
        //  /* Preferences Filter (Teacher’s accent, Teaches level, Subjects, Test preparations, Lesson includes, Learner’s age group) */
        //  $preferences = explode(",", $post['preferenceFilter'] ?? '');
        //  $preferences = array_filter(FatUtility::int($preferences));
        //  if (count($preferences) > 0) {
        //      $srch = new SearchBase('tbl_user_to_preference');
        //      $srch->addFld('DISTINCT utpref_user_id as utpref_user_id');
        //      $srch->addCondition('utpref_preference_id', 'IN', $preferences);
        //      $srch->doNotCalculateRecords();
        //      $srch->doNotLimitRecords();
        //      $subTable = '(' . $srch->getQuery() . ')';
        //      $this->joinTable($subTable, 'INNER JOIN', 'utpref.utpref_user_id = teacher.user_id', 'utpref');
        //  }
         /* Tutor Gender */
      
     }
 

     public function applyOrderBy(string $sortOrder): void
     {
         switch ($sortOrder) {
             case 'ages_asc':
                 $this->addOrder('grpcls.grpcls_ages', 'ASC');
                 break;
             case 'ages_desc':
                 $this->addOrder('grpcls.grpcls_ages', 'DESC');
                 break;
             default:
                 $this->addOrder('grpcls.grpcls_ages', 'DESC');
                 break;
         }
         $this->addOrder('grpcls.grpcls_ages', 'DESC');
         $this->addOrder('grpcls.grpcls_id', 'ASC');
     }
 


    public static function getSearchObj($langId, bool $addFlds = true)
    {
        $postedData = FatApp::getPostedData();
        $srch = new self(false);
        $srch->joinGroupClassLang($langId);
        $srch->joinTeacher();
        $srch->joinScheduledLesson();
        $srch->joinClassLang($langId);
        $srch2 = ScheduledLessonDetails::getSearchObj();
        $srch2->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'slesson_id=sldetail_slesson_id');
        $srch2->addFld('COUNT(DISTINCT sldetail_learner_id)');
        $srch2->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch2->addDirectCondition('slesson_grpcls_id=grpcls_id');
        $srch2->doNotLimitRecords(true);
        $srch2->doNotCalculateRecords(true);
        $addFlds && $srch->addMultipleFields(['user_id',
                    'user_first_name',
                    'user_last_name',
                    'CONCAT(user_first_name," ", user_last_name) as user_full_name',
                    'user_url_name',
                    'user_timezone as teacher_timezone',
                    'grpcls_id',
                    'grpcls_tlanguage_id',
                    'grpcls_teacher_id',
                    'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
                    'IFNULL(grpclslang_grpcls_description,grpcls_description) as grpcls_description',
                    'grpcls_entry_fee',
                    'grpcls_weeks',
                    'grpcls_start_datetime',
                    'grpcls_end_datetime',
                    'grpcls_max_learner',
                    'grpcls_status',
                    'grpcls_ages',
                    'grpcls_kids_youtube_link',
                    'grpcls_one_on_one_entry_fee',
                    'grpcls_max_one_on_one_learner',
                    'grpcls_class_experience',
                    'grpcls_schedule',
                    'grpcls_learning_goals',
                    'grpcls_kids_title',
                    'grpcls_supply_list',
                    'grpcls_total_lesson',
                    'grpcls_duration',
                    'grpcls_total_lesson',
                    'grpcls_parental_guidance',
                    'grpcls_slug',
                    'grpcls_added_on',
                    'max(grpcls_entry_fee) AS maxPrice',
                    'min(grpcls_entry_fee) AS minPrice',
                    'IFNULL(tlanguage_name, tlanguage_identifier) as teacher_language',
                    '(' . $srch2->getQuery() . ') as total_learners',
        ]);
        if (UserAuthentication::isUserLogged()) {
            $user_id = UserAuthentication::getLoggedUserId();
            $addFlds && $srch->addFld('(SELECT IF(sldetail_id>0, 1, 0) FROM `tbl_scheduled_lesson_details` INNER JOIN `tbl_scheduled_lessons` ON slesson_id=sldetail_slesson_id  WHERE slesson_grpcls_id=grpcls_id AND sldetail_learner_status=' . ScheduledLesson::STATUS_SCHEDULED . ' AND sldetail_learner_id=' . $user_id . ' LIMIT 1) is_in_class');
        } else {
            $addFlds && $srch->addFld('0 as is_in_class');
        }
        if (isset($postedData['keyword']) && !empty($postedData['keyword'])) {
            $condition = $srch->addCondition('grpcls_title', 'LIKE', '%' . $postedData['keyword'] . '%');
            $condition->attachCondition('grpclslang_grpcls_title', 'LIKE', '%' . $postedData['keyword'] . '%');
            $condition->attachCondition('user_first_name', 'LIKE', '%' .  $postedData['keyword'] . '%');
            $condition->attachCondition('user_first_name', 'LIKE', '%' .  $postedData['keyword'] . '%');
            $fullNameField = 'mysql_func_CONCAT(user_first_name, " ", user_last_name)';
            $condition->attachCondition($fullNameField, 'LIKE', '%' .  $postedData['keyword'] . '%', 'OR', true);
         
        }
        if (isset($postedData['status']) && $postedData['status'] !== "") {
            $srch->addCondition('grpcls_status', '=', $postedData['status']);
        } else {
            $srch->addCondition('grpcls_status', '!=', TeacherKidsClasses::STATUS_CANCELLED);
        }
        $teachLangId = FatUtility::int($postedData['language'] ?? 0);
        if ($teachLangId > 0) {
            
           $srch->addCondition('grpcls_tlanguage_id', '=', $postedData['language']);
           }
        /* Speak Language */
        $speakLangs = explode(",", $postedData['spokenLanguage'] ?? '');
        $speakLangIds = array_filter(FatUtility::int($speakLangs));
        if (count($speakLangIds) > 0) {
        
            $srch1 = new SearchBase('tbl_user_to_spoken_languages');
            $srch1->addFld('DISTINCT utsl_user_id as utsl_user_id');
            $srch1->addCondition('utsl_slanguage_id', 'IN', $speakLangIds);
            $src1h->doNotCalculateRecords();
            $srch1->doNotLimitRecords();
            $subTable = '(' . $srch->getQuery() . ')';
            $srch->joinTable($subTable, 'INNER JOIN', 'utsl.utsl_user_id = teacher.user_id', 'utsl');
        }
        /* Week Day and Time Slot */
        $weekDays = (array) ($postedData['filterWeekDays'] ?? []);
        $timeSlots = (array) ($postedData['filterTimeSlots'] ?? []);
        if (count($weekDays) > 0 || count($timeSlots) > 0) {
          
            $timeSlotArr = [];
            if (!empty($timeSlots)) {
                $timeSlotArr = CommonHelper::formatTimeSlotArr($timeSlots);
            }
            if (is_array($weekDays) && !empty($weekDays)) {
                foreach ($weekDays as $weekDayKey => $date) {
                    $srch->addCondition('grpcls.grpcls_weeks',  'LIKE', '%' . $date . '%');
                }
            }
            if (empty($weekDays) && !empty($timeSlotArr)) {
                $systemTimezone = MyDate::getTimeZone();
                $userTimezone = MyDate::getUserTimeZone();
                $condition = '( ';
                foreach ($timeSlotArr as $key => $formatedVal) {
                    $condition .= ($key == 0) ? '' : 'OR';
                    $startTime = date('Y-m-d') . ' ' . $formatedVal['startTime'];
                    $endTime = date('Y-m-d') . ' ' . $formatedVal['endTime'];
                    // $startTime=date('Y-m-d H:i:s',strtotime($startTime));
                    // $endTime=date('Y-m-d H:i:s',strtotime($endTime));
                    $startTime = date('Y-m-d H:i:s', strtotime(MyDate::changeDateTimezone($startTime, $userTimezone, $systemTimezone)));
                    $endTime = date('Y-m-d H:i:s', strtotime(MyDate::changeDateTimezone($endTime, $userTimezone, $systemTimezone)));
                    $condition .= ' ( CONCAT(`grpcls_start_datetime`,"") <  CONCAT("' . $endTime . '","") AND CONCAT(`grpcls_end_datetime`,"") >  CONCAT("' . $startTime . '","") ) ';
                   }
                $condition .= ' ) ';
                $srch->addDirectCondition($condition);
            }
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
        }
        /* From Country */
        $fromCountries = explode(",", $postedData['fromCountry'] ?? '');
        $fromCountries = array_filter(FatUtility::int($fromCountries));
        if (count($fromCountries)) {
       
            $srch->addCondition('teacher.user_country_id', 'IN', $fromCountries);
        }
        /* Min & Max Price */
        $minPrice = FatUtility::float($postedData['minPriceRange'] ?? 0);
        $maxPrice = FatUtility::float($postedData['maxPriceRange'] ?? 0);
        if (!empty($minPrice) && !empty($maxPrice)) {
            $srch->addCondition('grpcls_entry_fee', '<=', $maxPrice);
            $srch->addCondition('grpcls_entry_fee', '>=', $minPrice);
        }
        if (isset($postedData['techAges']) && !empty($postedData['techAges'])) 
        {
            if($postedData['techAges']!=='All Ages'){
            $srch->addCondition('grpcls_ages', '=', $postedData['techAges']);
            $srch->addGroupBy('grpcls_teacher_id');
            }
            else{
                $srch->addGroupBy('grpcls_id');    
            }
        }
        else {

            $srch->addGroupBy('grpcls_id');
        }
        $srch->setTeacherDefinedCriteria(false, false);
        $srch->addOrder('grpcls_start_datetime', 'ASC');
       // $srch->addGroupBy('grpcls_teacher_id');
        
        return $srch;
    }
    public function getRecordCount(): int
    {
        
        $db = FatApp::getDb();
        
        $order = $this->order;
        // echo $db;
        $page = $this->page;
        $pageSize = $this->pageSize;
        // $this->limitRecords = false;
        // $this->order = [];
        // $qry = $this->getQuery() . ' LIMIT ' . SEARCH_MAX_COUNT . ', 1';
        // if ($db->totalRecords($db->query($qry)) > 0) {
        //     $recordCount = SEARCH_MAX_COUNT;
        // } else {
        //     if (empty($this->groupby) && empty($this->havings)) {
        //         $this->addFld('COUNT(grpcls_id) AS total');
        //         $rs = $db->query($this->getQuery());
        //     } else {
        //         $this->addFld('grpcls_id as grpcls_id');
                $rs = $db->query('SELECT COUNT(grpcls_id) AS total FROM (' . $this->getQuery() . ') t');
        //     }
            $recordCount = FatUtility::int($db->fetch($rs)['total'] ?? 0);
        // }
        
        $this->order = $order;
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->limitRecords = true;
        return $recordCount;
    }

    public function joinGroupClassLang(int $langId)
    {
        $this->joinTable(TeacherKidsClasses::DB_TBL_LANG, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpcls_l.grpclslang_lang_id=' . $langId, 'grpcls_l');
    }

    public function joinTeacher()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ut.user_id = grpcls.grpcls_teacher_id', 'ut');
    }

    public function joinTeacherCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'tcred.credential_user_id = grpcls.grpcls_teacher_id', 'tcred');
    }

    public function joinLearner()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sld.sldetail_learner_id', 'ul');
    }

    public function joinLearnerCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'lcred.credential_user_id = sld.sldetail_learner_id', 'lcred');
    }

    public function joinScheduledLesson()
    {
        $this->joinTable(ScheduledLesson::DB_TBL, 'LEFT OUTER JOIN', 'sl.slesson_grpcls_id = grpcls.grpcls_id', 'sl');
    }

    public function joinScheduledLessonDetails()
    {
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT OUTER JOIN', 'sld.sldetail_slesson_id = sl.slesson_id', 'sld');
    }

    public function joinClassLang($langId = 0)
    {
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'grpcls.grpcls_tlanguage_id = tlanguage_id', 'teachl');
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'teachl.tlanguage_id = teachl_lang.tlanguagelang_tlanguage_id AND teachl_lang.tlanguagelang_lang_id = ' . $langId, 'teachl_lang');
        }
    }

    public function joinTeacherSpokenLang($langId = 0)
    {
        $this->joinTable(SpokenLanguage::DB_TBL, 'LEFT JOIN', 'sl.slesson_slanguage_id = slanguage_id', 'teachl');
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $this->joinTable(SpokenLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'teachl.slanguage_id = teachl_lang.slanguagelang_slanguage_id AND teachl_lang.slanguagelang_lang_id = ' . $langId, 'teachl_lang');
        }
    }

    public static function totalSeatsBooked($grpclsId)
    {
        $db = FatApp::getDb();
        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'LEFT OUTER JOIN', 'sl.slesson_grpcls_id = grpcls.grpcls_id', 'sl');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT OUTER JOIN', 'sld.sldetail_slesson_id = sl.slesson_id', 'sld');
        $srch->addFld('count(DISTINCT sldetail_learner_id) as total');
        $srch->addCondition('grpcls_id', '=', $grpclsId);
        $srch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        return $row['total'];
    }

    public static function isClassBookedByUser($grpclsId, $userId)
    {
        $db = FatApp::getDb();
        $srch = new SearchBase(TeacherKidsClasses::DB_TBL, 'grpcls');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'LEFT OUTER JOIN', 'sl.slesson_grpcls_id = grpcls.grpcls_id', 'sl');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'LEFT OUTER JOIN', 'sld.sldetail_slesson_id = sl.slesson_id', 'sld');
        $srch->addFld('count(DISTINCT sldetail_learner_id) as total');
        $srch->addCondition('grpcls_id', '=', $grpclsId);
        $srch->addCondition('sldetail_learner_id', '=', $userId);
        $srch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        return $row['total'] > 0;
    }

    public function getClassBasicDetails($grpclsId, $userId, $langId = 0)
    {
        $db = FatApp::getDb();
        $this->joinScheduledLesson();
        $this->joinGroupClassLang($langId);
        $this->joinScheduledLessonDetails();
        $this->joinTeacher();
        $this->joinTeacherCredentials();
        $this->joinLearner();
        $this->joinLearnerCredentials();
        $this->addMultipleFields(['CONCAT(ut.user_first_name," ", ut.user_last_name) as teacher_full_name',
            'ut.user_timezone as teacherTimeZone',
            'tcred.credential_email as teacherEmailId',
            'CONCAT(ul.user_first_name," ", ul.user_last_name) as learner_full_name',
            'ul.user_timezone as learnerTimeZone',
            'lcred.credential_email as learnerEmailId',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'grpcls_start_datetime',
            'grpcls_end_datetime'
        ]);
        $this->addCondition('grpcls_id', '=', $grpclsId);
        $this->addCondition('sldetail_learner_id', '=', $userId);
        $this->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $rs = $this->getResultSet();
        return $db->fetch($rs);
    }

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

    public static function formatTeacherSearchData(array $records, int $userId=0): array
    {
   
        $langId = CommonHelper::getLangId();
    
        $teacherIds = array_column($records, 'user_id');
        // $countryIds = array_column($records, 'user_country_id');
        // $countries = static::getCountryNames($langId, $countryIds);
        // $favorites = static::getFavoriteTeachers($userId, $teacherIds);
        $langData = static::getTeachersLangData($langId, $teacherIds);
        // $teachLangs = static::getTeachLangs($langId, $teacherIds);
        // $speakLangs = static::getSpeakLangs($langId, $teacherIds);
        // $timeslots = static::getTimeslots($userId, $teacherIds);
        $videos = static::getYouTubeVideos($teacherIds);
        foreach ($records as $key => $record) {
            $tittle=array_pop(explode(" ",$record['grpcls_title']));
            $age=str_replace("-"," to ",$record['grpcls_ages']);
            $record['page_title']="Ubuntu Talks ".ucwords($tittle) ." - ".$age." years";
            // $record['grpcls_id'] = $favorites[$record['grpcls_id']] ?? 0;
            $record['user_profile_info'] = $langData[$record['user_id']] ?? '';
            // $record['user_country_name'] = $countries[$record['user_country_id']] ?? '';
            // $record['teacherTeachLanguageName'] = $teachLangs[$record['grpcls_id']] ?? '';
            // $record['spoken_language_names'] = $speakLangs[$record['user_id']]['slanguage_name'] ?? '';
            // $record['spoken_languages_proficiency'] = $speakLangs[$record['user_id']]['utsl_proficiency'] ?? '';
            // $record['testat_timeslots'] = $timeslots[$record['grpcls_id']] ?? CommonHelper::getEmptyDaySlots();
            $record['us_video_link'] = CommonHelper::validateIntroVideoLink($videos[$record['user_id']]);
            $records[$key] = $record;
        }
        return $records;
    }

    public static function getClassDetailsByTeacher($grpcls_id, $teacher_id, $langId)
    {
        $srch = new TeacherKidsClassesSearch();
        $srch->joinGroupClassLang($langId);
        $srch->addMultipleFields(['grpcls_id',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'grpcls_max_learner',
            'grpcls_entry_fee',
            'grpcls_start_datetime',
            'grpcls_end_datetime',
            'grpcls_status',
            'grpcls_tlanguage_id'
        ]);
        $srch->doNotCalculateRecords();
        $srch->addCondition('grpcls_teacher_id', '=', $teacher_id);
        $srch->addCondition('grpcls_id', '=', $grpcls_id);
      
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public function setTeacherDefinedCriteria($langCheck = true, $addUserSettingJoin = true)
    {
        $this->joinCredentials();
        $this->addCondition('user_is_teacher', '=', 1);
        $this->addCondition('user_country_id', '>', 0);
        $this->addCondition('credential_active', '=', 1);
        $this->addCondition('credential_verified', '=', 1);
        /* additional conditions[ */
        if ($addUserSettingJoin) {
            $this->joinUserSettings();
        }
        /* teachLanguage[ */
        if ($langCheck) {
            // $tlangSrch = $this->getMyTeachLangQry();
            // $this->joinTable("(" . $tlangSrch->getQuery() . ")", 'INNER JOIN', 'user_id = utl_user_id', 'utls');
        }
        /* ] */
        /* qualification/experience[ */
        $qSrch = new UserQualificationSearch();
        $qSrch->addMultipleFields(['uqualification_user_id']);
        $qSrch->addCondition('uqualification_active', '=', 1);
        $qSrch->addGroupBy('uqualification_user_id');
        $this->joinTable("(" . $qSrch->getQuery() . ")", 'INNER JOIN', 'user_id = uqualification_user_id', 'utqual');
        /* ] */
        /* user preferences/skills[ */
        $skillSrch = new UserToPreferenceSearch();
        $skillSrch->addMultipleFields(['utpref_user_id', 'GROUP_CONCAT(utpref_preference_id) as utpref_preference_ids']);
        $skillSrch->addGroupBy('utpref_user_id');
        $this->joinTable("(" . $skillSrch->getQuery() . ")", 'INNER JOIN', 'user_id = utpref_user_id', 'utpref');
        /* ] */
    }

    public function joinCredentials($isActive = true, $isEmailVerified = true)
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'ut.user_id = cred.credential_user_id', 'cred');
        if (true === $isActive) {
            $this->addCondition('cred.credential_active', '=', 1);
        }
        if (true === $isEmailVerified) {
            $this->addCondition('cred.credential_verified', '=', 1);
        }
    }

    public function joinUserSettings()
    {
        $this->joinTable(UserSetting::DB_TBL, 'LEFT JOIN', 'u.user_id = us_user_id', 'us');
    }

    public static function getTeacherClassByTime($teacherId, $startDateTime, $endDateTime)
    {
        $groupClassTiming = self::checkGroupClassTiming([$teacherId], $startDateTime, $endDateTime);
        $groupClassTiming->addCondition('grpcls_status', '=', TeacherKidsClasses::STATUS_ACTIVE);
        $rs = $groupClassTiming->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function checkGroupClassTiming(array $userIds, $startDateTime, $endDateTime): object
    {
        $searchBase = new self(false);
        $searchBase->addMultipleFields(['grpcls_id']);
        $searchBase->addCondition('grpcls_teacher_id', 'IN', $userIds);
        // $searchBase->addCondition('grpcls_start_datetime', '<', $endDateTime);
        // $searchBase->addCondition('grpcls_end_datetime', '>', $startDateTime);
        return $searchBase;
    }

    public static function getTeachLangs(int $langId)
    {
        $srch = static::getSearchObj($langId, false);
        $srch->joinClassLang($langId);
        $srch->doNotCalculateRecords();
        $srch->addMultipleFields(
                array(
                    'tlanguage_id',
                    'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name'
                )
        );
        // $srch->addCondition('grpcls_end_datetime', '>=', date('Y-m-d H:i:s'));
        $srch->addOrder('tlanguage_display_order');
        $rs = $srch->getResultSet();
        $teachingLanguagesArr = FatApp::getDb()->fetchAllAssoc($rs);
        return $teachingLanguagesArr;
    }

}
