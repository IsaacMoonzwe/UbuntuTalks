<?php

class TeacherLessonRating extends MyAppModel
{

    const DB_TBL = 'tbl_teacher_lesson_rating';
    const DB_TBL_PREFIX = '	tlrating_';
    const TYPE_LESSON = 1;
    const TYPE_TEACHER_ACCENT = 2;
    const TYPE_TEACHER_PRSESNCE = 3;
    const TYPE_TEACHER_OVERALL = 4;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObj()
    {
        return $srch = new SearchBase(static::DB_TBL, 'sprating');
    }

    public static function getRatingAspectsArr()
    {
        return [
            static::TYPE_LESSON => Label::getLabel('LBL_Lesson'),
            static::TYPE_TEACHER_ACCENT => Label::getLabel('LBL_Accent'),
            static::TYPE_TEACHER_PRSESNCE => Label::getLabel('LBL_Presence'),
            static::TYPE_TEACHER_OVERALL => Label::getLabel('LBL_Overall'),
        ];
    }

    public static function getSellerRating($userId)
    {
        $userId = FatUtility::int($userId);
        $srch = new TeacherLessonReviewSearch();
        $srch->joinTeacher();
        $srch->addMultipleFields(['avg(tlrating_rating) as avg_rating']);
        $srch->addCondition('tlrating_rating_type', 'in', [static::TYPE_TEACHER_ACCENT, static::TYPE_TEACHER_PRSESNCE, static::TYPE_TEACHER_OVERALL]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('tlreview_seller_user_id', '=', $userId);
        $srch->addCondition('tlr.spreview_status', '=', TeacherLessonReview::STATUS_APPROVED);
        $srch->addGroupby('tlreview_seller_user_id');
        $record = FatApp::getDb()->fetch($srch->getResultSet());
        return $record['avg_rating'] ?? 0;
    }

}
