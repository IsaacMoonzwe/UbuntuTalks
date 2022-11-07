<?php

class TeacherGroupClassesSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true, $skipDeleted = true)
    {
        parent::__construct(TeacherGroupClasses::DB_TBL, 'grpcls');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if ($skipDeleted == true) {
            $this->addCondition('grpcls_deleted', '=', applicationConstants::NO);
        }
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
                    'grpcls_classes_type',
                    'grpcls_weeks',
                    'grpcls_start_datetime',
                    'grpcls_end_datetime',
                    'grpcls_max_learner',
                    'grpcls_status',
                    'grpcls_slug',
                    'grpcls_added_on',
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
        }
        if (isset($postedData['status']) && $postedData['status'] !== "") {
            $srch->addCondition('grpcls_status', '=', $postedData['status']);
        } else {
            $srch->addCondition('grpcls_status', '!=', TeacherGroupClasses::STATUS_CANCELLED);
        }
        $srch->setTeacherDefinedCriteria(false, false);
        $srch->addOrder('grpcls_start_datetime', 'ASC');
        $srch->addGroupBy('grpcls_id');
        return $srch;
    }

    public function joinGroupClassLang(int $langId)
    {
        $this->joinTable(TeacherGroupClasses::DB_TBL_LANG, 'LEFT OUTER JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpcls_l.grpclslang_lang_id=' . $langId, 'grpcls_l');
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
        $srch = new SearchBase(TeacherGroupClasses::DB_TBL, 'grpcls');
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
        $srch = new SearchBase(TeacherGroupClasses::DB_TBL, 'grpcls');
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

    public static function getClassDetailsByTeacher($grpcls_id, $teacher_id, $langId)
    {
        $srch = new TeacherGroupClassesSearch();
        $srch->joinGroupClassLang($langId);
        $srch->addMultipleFields(['grpcls_id',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'IFNULL(grpclslang_grpcls_description,grpcls_description) as grpcls_description',
            'grpcls_max_learner',
            'grpcls_entry_fee',
            'grpcls_start_datetime',
            'grpcls_end_datetime',
            'grpcls_classes_type',
            'grpcls_weeks',
            'grpcls_teacher_id',
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
        $groupClassTiming->addCondition('grpcls_status', '=', TeacherGroupClasses::STATUS_ACTIVE);
        $rs = $groupClassTiming->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function checkGroupClassTiming(array $userIds, $startDateTime, $endDateTime): object
    {
        $searchBase = new self(false);
        $searchBase->addMultipleFields(['grpcls_id']);
        $searchBase->addCondition('grpcls_teacher_id', 'IN', $userIds);
        $searchBase->addCondition('grpcls_start_datetime', '<', $endDateTime);
        $searchBase->addCondition('grpcls_end_datetime', '>', $startDateTime);
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
        $srch->addCondition('grpcls_end_datetime', '>=', date('Y-m-d H:i:s'));
        $srch->addOrder('tlanguage_display_order');
        $rs = $srch->getResultSet();
        $teachingLanguagesArr = FatApp::getDb()->fetchAllAssoc($rs);
        return $teachingLanguagesArr;
    }

}
