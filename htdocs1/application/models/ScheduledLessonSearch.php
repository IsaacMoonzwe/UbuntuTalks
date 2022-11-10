<?php

class ScheduledLessonSearch extends SearchBase
{

    private $isTeacherSettingsJoined;
    private $isOrderJoined;
    private $isScheduledLessonDetailJoined;

    public function __construct($doNotCalculateRecords = true, $joinDetails = true)
    {
        parent::__construct(ScheduledLesson::DB_TBL, 'slns');
        $this->isTeacherSettingsJoined = false;
        $this->isOrderJoined = false;
        $this->isScheduledLessonDetailJoined = false;
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if ($joinDetails === true) {
            $this->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'sld.sldetail_slesson_id = slns.slesson_id', 'sld');
            $this->isScheduledLessonDetailJoined = true;
        }
    }

    public static function getSearchLessonsObj($langId)
    {
        $srch = new self(false);
        $srch->joinGroupClass($langId);
        $srch->joinKidsClass($langId);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinTeacherCountry($langId);
        $cnd = $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $cnd->attachCondition('order_is_paid', '=', Order::ORDER_IS_CANCELLED);
        $srch->joinTeacherSettings();
        $srch->addOrder('slesson_date', 'ASC');
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'slns.slesson_id',
            'slns.slesson_grpcls_id',
			'slns.slesson_kids_class',
            'IFNULL(grpclslang_grpcls_title,grpcls_title) as grpcls_title',
            'IFNULL(grpclslang_grpcls_description,grpcls_description) as grpcls_description',
            'grpcls_tlanguage_id',
            'grpcls.grpcls_status',
            'sld.sldetail_id',
            'sld.sldetail_order_id',
            'slns.slesson_slanguage_id',
            'sld.sldetail_learner_id as learnerId',
            'slns.slesson_teacher_id as teacherId',
            'ut.user_first_name as teacherFname',
            'ut.user_last_name as teacherLname',
            'ut.user_url_name',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'IFNULL(teachercountry_lang.country_name, teachercountry.country_code) as teacherCountryName',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'sld.sldetail_learner_status',
            'sld.sldetail_is_teacher_paid',
            '"-" as teacherTeachLanguageName',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration',
            'order_is_paid'
        ]);
        return $srch;
    }

    public function joinUserLessonData()
    {
        $scheduledLessonDetailsSrch = new ScheduledLessonDetailsSearch();
        $scheduledLessonDetailsSrch->addGroupBy('sld.sldetail_slesson_id');
        $this->joinTable(LessonStatusLog::DB_TBL, 'LEFT JOIN', 'lsl.lesstslog_slesson_id = slns.slesson_id', 'lsl');
        $this->joinTable(User::DB_TBL, 'LEFT JOIN', 'lsl.lesstslog_updated_by_user_id = u.user_id', 'u');
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
        $this->addGroupBy('lesstslog_updated_by_user_id');
    }

    public function joinGroupClass($langId)
    {
        $this->joinTable(TeacherGroupClasses::DB_TBL, 'LEFT JOIN', 'grpcls.grpcls_id = slns.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherGroupClasses::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpclslang_lang_id=' . $langId, 'grpcls_l');
    }

    public function joinKidsClass($langId)
    {
        $this->joinTable(TeacherKidsClasses::DB_TBL, 'LEFT JOIN', 'grpcls.grpcls_id = slns.slesson_grpcls_id', 'grpcls');
        $this->joinTable(TeacherKidsClasses::DB_TBL_LANG, 'LEFT JOIN', 'grpcls.grpcls_id = grpcls_l.grpclslang_grpcls_id and grpclslang_lang_id=' . $langId, 'grpcls_l');
    }

    public function joinTeacher()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ut.user_id = slns.slesson_teacher_id', 'ut');
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'ut.user_id = credut.credential_user_id', 'credut');
    }

    public function joinLearner()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sld.sldetail_learner_id', 'ul');
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'ul.user_id = cred.credential_user_id', 'cred');
    }

    public function joinTeacherSettings()
    {
        if (true === $this->isTeacherSettingsJoined) {
            return;
        }
        $this->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'ts.us_user_id = slns.slesson_teacher_id', 'ts');
        $this->isTeacherSettingsJoined = true;
    }

    public function joinTeacherCountry($langId = 0)
    {
        $langId = FatUtility::int($langId);
        $this->joinTable(Country::DB_TBL, 'LEFT JOIN', 'ut.user_country_id = teachercountry.country_id', 'teachercountry');
        if ($langId > 0) {
            $this->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'teachercountry.country_id = teachercountry_lang.countrylang_country_id AND teachercountry_lang.countrylang_lang_id = ' . $langId, 'teachercountry_lang');
        }
    }

    public function joinLearnerCountry($langId = 0)
    {
        $langId = FatUtility::int($langId);
        $this->joinTable(Country::DB_TBL, 'LEFT JOIN', 'ul.user_country_id = learnercountry.country_id', 'learnercountry');
        if ($langId > 0) {
            $this->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'learnercountry.country_id = learnercountry_lang.countrylang_country_id AND learnercountry_lang.countrylang_lang_id = ' . $langId, 'learnercountry_lang');
        }
    }

    public function joinTeacherTeachLanguageView($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'tlanguage_id = slns.slesson_slanguage_id');
        $this->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'tlanguagelang_tlanguage_id = slns.slesson_slanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl_lang');
        $this->addMultipleFields(['GROUP_CONCAT( DISTINCT IFNULL(tlanguage_name, tlanguage_identifier) ) as teacherTeachLanguageName']);
    }

    public function joinTeacherTeachLanguage($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $this->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 't_t_lang.tlanguage_id = slns.slesson_slanguage_id', 't_t_lang');
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tl_l.tlanguagelang_tlanguage_id = t_t_lang.tlanguage_id AND tl_l.tlanguagelang_lang_id = ' . $langId, 'tl_l');
        }
    }

    public function joinLessonLanguage($langId = 0)
    {
        $langId = FatUtility::int($langId);
        $this->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'slns.slesson_slanguage_id = tlang.tlanguage_id', 'tlang');
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'sl.tlanguagelang_tlanguage_id = tlang.tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl');
        }
    }

    public function joinOrder()
    {
        if (true === $this->isOrderJoined) {
            return;
        }
        $this->joinTable(Order::DB_TBL, 'INNER JOIN', 'o.order_id = sld.sldetail_order_id AND o.order_type = ' . Order::TYPE_LESSON_BOOKING, 'o');
        $this->isOrderJoined = true;
    }

    public function joinOrderProducts()
    {
        if (false === $this->isOrderJoined) {
            trigger_error("First Use Join Order, to Join OrderProducts", E_USER_ERROR);
        }
        $this->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'o.order_id = op.op_order_id', 'op');
    }

    public function joinTeacherCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'tcred.credential_user_id = slns.slesson_teacher_id', 'tcred');
    }

    public function joinLearnerCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'lcred.credential_user_id = sld.sldetail_learner_id', 'lcred');
    }

    public function joinLessonRescheduleLog()
    {
        $getLessonRescheduleLogObj = LessonRescheduleLog::getLatestLessonRescheduleLog();
        $getLessonRescheduleLogObj->addMultipleFields(['lreschlog.*']);
        $getLessonRescheduleLogObj->doNotCalculateRecords();
        $getLessonRescheduleLogObj->doNotLimitRecords();
        $this->joinTable("(" . $getLessonRescheduleLogObj->getQuery() . ")", 'LEFT JOIN', 'lrsl.lesreschlog_slesson_id = slns.slesson_id', 'lrsl');
    }

    public function joinTeacherOfferPrice($teacherId)
    {
        $teacherId = FatUtility::int($teacherId);
        if ($teacherId < 1) {
            trigger_error("Invalid Request", E_USER_ERROR);
        }
        $this->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'sldetail_learner_id = top_learner_id AND top_teacher_id = ' . $teacherId, 'top');
    }

    public function joinLearnerOfferPrice($learnerId)
    {
        $learnerId = FatUtility::int($learnerId);
        if ($learnerId < 1) {
            trigger_error("Invalid Request", E_USER_ERROR);
        }
        $this->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'slesson_teacher_id = top_teacher_id AND top_learner_id = ' . $learnerId, 'top');
    }

    public static function countPlansRelation($lessonId)
    {
        $lessonId = FatUtility::int($lessonId);
        $srchRelLsnToPln = new SearchBase('tbl_scheduled_lessons_to_teachers_lessons_plan');
        $srchRelLsnToPln->addMultipleFields(['ltp_tlpn_id', 'ltp_slessonid']);
        $srchRelLsnToPln->addCondition('ltp_slessonid', '=', $lessonId);
        $relRs = $srchRelLsnToPln->getResultSet();
        $count = $srchRelLsnToPln->recordCount();
        return $count;
    }

    public function joinRatingReview()
    {
        $this->joinTable(TeacherLessonReview::DB_TBL, 'LEFT OUTER JOIN', 'ut.user_id = tlr.tlreview_teacher_user_id AND tlr.tlreview_status = ' . TeacherLessonReview::STATUS_APPROVED, 'tlr');
        $this->joinTable(TeacherLessonRating::DB_TBL, 'LEFT OUTER JOIN', 'tlrating.tlrating_tlreview_id = tlr.tlreview_id', 'tlrating');
        $this->addMultipleFields(["ROUND(AVG(tlrating_rating),2) as teacher_rating", "count(DISTINCT tlreview_id) as totReviews"]);
    }

    public function joinLessonPLan()
    {
        $this->joinTable('tbl_scheduled_lessons_to_teachers_lessons_plan', 'LEFT JOIN', 'tlp.ltp_slessonid = slns.slesson_id', 'tlp');
        $this->joinTable(LessonPlan::DB_TBL, 'LEFT JOIN', 'lp.tlpn_id = tlp.ltp_tlpn_id', 'lp');
    }

    public function joinUserTeachLanguages($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $this->joinTable(UserTeachLanguage::DB_TBL, 'LEFT JOIN', 'ut.user_id = utsl.utl_user_id', 'utsl');
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'tlanguage_id = utsl.utl_tlanguage_id');
        $this->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'tlanguagelang_tlanguage_id = utsl.utl_tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl_lang');
        $this->addMultipleFields(['utsl.utl_user_id', 'GROUP_CONCAT(DISTINCT IFNULL(tlanguage_name, tlanguage_identifier) ORDER BY IFNULL(tlanguage_name, tlanguage_identifier) ASC SEPARATOR ", ") AS teacherTeachLanguageName']);
    }

    public static function isSlotBooked(int $teacherId, $startDateTime, $endDateTime)
    {
        $srch = new self(false);
        $srch->addMultipleFields(['slns.slesson_id']);
        $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch->addCondition('slns.slesson_teacher_id', '=', $teacherId);
        $srch->addCondition('slns.slesson_date', '=', date('Y-m-d', strtotime($startDateTime)));
        $cnd = $srch->addCondition('slns.slesson_start_time', '=', date('H:i:s', strtotime($startDateTime)), 'AND');
        $cnd->attachCondition('slns.slesson_start_time', '<=', date('H:i:s', strtotime($endDateTime)), 'AND');
        $cnd1 = $cnd->attachCondition('slns.slesson_end_time', '>', date('H:i:s', strtotime($startDateTime)), 'OR');
        $cnd1->attachCondition('slns.slesson_end_time', '<=', date('H:i:s', strtotime($endDateTime)), 'AND');
        $srch->getResultSet();
        return $srch->recordCount() > 0;
    }

    public static function getLessonInfoByGrpClsid($grpclsId, $attr = null)
    {
        $grpclsId = FatUtility::int($grpclsId);
        $srch = new self();
        $srch->addCondition('slesson_grpcls_id', '=', $grpclsId);
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public function getLessonsByClass($grpclsId)
    {
        $db = FatApp::getDb();
        $this->addMultipleFields(['slesson_id', 'slesson_status']);
        $this->addCondition('slesson_grpcls_id', '=', $grpclsId);
        $cnd = $this->addCondition('slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $cnd->attachCondition('slesson_status', '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        $rs = $this->getResultSet();
        return $db->fetchAll($rs);
    }

    public function checkUserLessonBooking(array $userIds, string $startDateTime, string $endDateTime): SearchBase
    {
        if (empty($userIds)) {
            trigger_error(Label::getLabel('LBL_User_id_Requried'), E_USER_ERROR);
        }
        $userFieldCnd = $this->addCondition('slns.slesson_teacher_id', ' IN ', $userIds);
        $userFieldCnd->attachCondition('sld.sldetail_learner_id', ' IN ', $userIds, ' OR ');
        $directStr = " ( CONCAT(slns.`slesson_date`, ' ', slns.`slesson_start_time` ) < '" . $endDateTime . "' AND CONCAT(slns.`slesson_end_date`, ' ', slns.`slesson_end_time` ) > '" . $startDateTime . "' ) ";
        $this->addDirectCondition($directStr);
        // $this->addCondition('slns.slesson_status', ' IN ', [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_COMPLETED]);
        $this->addCondition('slns.slesson_status', ' = ', ScheduledLesson::STATUS_SCHEDULED);
        $this->addMultipleFields(['slns.slesson_date', 'slns.slesson_start_time', 'slns.slesson_end_time', 'slns.slesson_id', 'sld.sldetail_order_id', 'sld.sldetail_learner_id']);
        return $this;
    }

}
