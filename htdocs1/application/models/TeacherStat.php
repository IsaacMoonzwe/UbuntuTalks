<?php

class TeacherStat extends FatModel
{

    const DB_TBL = 'tbl_teacher_stats';

    private $userId;

    function __construct(int $userId)
    {
        parent::__construct();
        $this->userId = $userId;
    }

    /**
     * testat_ratings
     * testat_reviewes
     */
    public function setRatingReviewCount()
    {
        $srch = new SearchBase('tbl_teacher_lesson_reviews', 'tlreview');
        $srch->joinTable('tbl_teacher_lesson_rating', 'LEFT JOIN', 'tlrating.tlrating_tlreview_id = tlreview.tlreview_id', 'tlrating');
        $srch->addMultipleFields(['ROUND(AVG(tlrating.tlrating_rating), 2) as ratings', 'COUNT(DISTINCT tlreview.tlreview_id) as reviews']);
        $srch->addCondition('tlreview.tlreview_status', '=', TeacherLessonReview::STATUS_APPROVED);
        $srch->addCondition('tlreview.tlreview_teacher_user_id', '=', $this->userId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        $data = ['testat_ratings' => 0, 'testat_reviewes' => 0];
        if (!empty($row)) {
            $data = ['testat_ratings' => FatUtility::float($row['ratings']), 'testat_reviewes' => $row['reviews']];
        }
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_students
     * testat_lessions
     */
    public function setStudentLessionCount()
    {
        $srch = new SearchBase('tbl_scheduled_lessons', 'slesson');
        $srch->joinTable('tbl_scheduled_lesson_details', 'LEFT JOIN', 'sldetail.sldetail_slesson_id = slesson.slesson_id', 'sldetail');
        $srch->addMultipleFields(['COUNT(DISTINCT sldetail_learner_id) as students', 'COUNT(slesson.slesson_id) as lessons']);
        $srch->addCondition('slesson.slesson_teacher_id', '=', $this->userId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        $data = ['testat_students' => $row['students'], 'testat_lessions' => $row['lessons']];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_teachlang
     * testat_minprice
     * testat_maxprice
     */
    public function setTeachLangPrices()
    {
        $srch = new SearchBase(UserTeachLanguage::DB_TBL, 'utl');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'tlanguage.tlanguage_id = utl.utl_tlanguage_id', 'tlanguage');
        $srch->joinTable(TeachLangPrice::DB_TBL, 'LEFT JOIN', 'ustelgpr.ustelgpr_utl_id = utl.utl_id', 'ustelgpr');
        $srch->addCondition('utl.utl_user_id', '=', $this->userId);
        $srch->addFld('MIN(ustelgpr_price) AS minPrice');
        $srch->addFld('MAX(ustelgpr_price) AS maxPrice');
        $srch->addFld('tlanguage_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        $data = [
            'testat_teachlang' => empty($row) ? 0 : 1,
            'testat_minprice' => FatUtility::float($row['minPrice'] ?? 0),
            'testat_maxprice' => FatUtility::float($row['maxPrice'] ?? 0)
        ];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_speaklang
     */
    public function setSpeakLang()
    {
        $srch = new SearchBase('tbl_user_to_spoken_languages');
        $srch->addCondition('utsl_user_id', '=', $this->userId);
        $srch->setPageSize(1);
        $srch->getResultSet();
        $speaklang = $srch->recordCount() > 0 ? 1 : 0;
        $data = ['testat_speaklang' => $speaklang];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_qualification
     */
    public function setQualification()
    {
        $srch = new SearchBase('tbl_user_qualifications');
        $srch->addCondition('uqualification_user_id', '=', $this->userId);
        $srch->setPageSize(1);
        $srch->getResultSet();
        $qualification = $srch->recordCount() > 0 ? 1 : 0;
        $data = ['testat_qualification' => $qualification];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_preference
     */
    public function setPreference(int $preference)
    {
        $data = ['testat_preference' => $preference];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * testat_availability
     */
    public function setAvailability(int $available)
    {
        $data = ['testat_availability' => $available];
        $record = new TableRecord('tbl_teacher_stats');
        $record->setFldValue('testat_user_id', $this->userId);
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public function setTeachLangPricesBulk()
    {
        FatApp::getDb()->query("UPDATE `tbl_teacher_stats` LEFT JOIN (SELECT IF(COUNT(userTeachLang.`utl_id`) > 0, 1, 0) AS teachLangCount, userTeachLang.`utl_user_id` AS teachLangUserId, MIN(langPrice.`ustelgpr_price`) AS minPrice, MAX(langPrice.`ustelgpr_price`) AS maxPrice FROM `tbl_user_teach_languages` AS userTeachLang INNER JOIN `tbl_teaching_languages` AS teachLang ON teachLang.tlanguage_id = userTeachLang.utl_tlanguage_id LEFT JOIN `tbl_user_teach_lang_prices` AS langPrice ON langPrice.ustelgpr_utl_id = userTeachLang.utl_id GROUP BY userTeachLang.`utl_user_id`) utl ON  utl.teachLangUserId = `testat_user_id` SET `testat_teachlang` = IFNULL(utl.teachLangCount, 0), `testat_minprice` = IFNULL(utl.minPrice, 0), `testat_maxprice` = IFNULL(utl.maxPrice, 0)");
    }

    public function setPreferenceBulk()
    {
        FatApp::getDb()->query("UPDATE `tbl_teacher_stats` LEFT JOIN (SELECT userPre.`utpref_user_id` AS tPreUserId,  IF(COUNT(userPre.`utpref_user_id`) > 0, 1, 0) AS userPreCount FROM `tbl_user_to_preference` AS userPre INNER JOIN `tbl_preferences` AS prefer ON prefer.preference_id = userPre.utpref_preference_id GROUP BY userPre.`utpref_user_id`) teacherPre ON teacherPre.tPreUserId = `testat_user_id` SET `testat_preference` = IFNULL(teacherPre.userPreCount,0)");
    }

    public function setSpeakLangBulk()
    {
        FatApp::getDb()->query("UPDATE `tbl_teacher_stats` LEFT JOIN (SELECT userSpokenLang.`utsl_user_id` AS spokenUserId,  IF(COUNT(userSpokenLang.`utsl_user_id`) > 0, 1, 0) AS userSpokenCount FROM `tbl_user_to_spoken_languages` AS userSpokenLang INNER JOIN `tbl_spoken_languages` AS slanguage ON slanguage.slanguage_id = userSpokenLang.utsl_slanguage_id GROUP BY userSpokenLang.`utsl_user_id`) teacherSpoken ON teacherSpoken.spokenUserId = `testat_user_id` SET `testat_speaklang` = IFNULL(teacherSpoken.userSpokenCount,0)");
    }

}
