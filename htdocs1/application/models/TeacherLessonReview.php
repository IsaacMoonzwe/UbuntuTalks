<?php

class TeacherLessonReview extends MyAppModel
{

    const DB_TBL = 'tbl_teacher_lesson_reviews';
    const DB_TBL_PREFIX = 'tlreview_';
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_CANCELLED = 2;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getReviewSortArr(int $langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId == 0) {
            trigger_error(Label::getLabel('MSG_Language_Id_not_specified.', $langId), E_USER_ERROR);
        }
        $arr = [
            'most_helpful' => Label::getLabel('LBL_Most_Helpful', $langId),
            'date_posted_desc' => Label::getLabel('LBL_Sort_By_Newest', $langId),
            'date_posted_asc' => Label::getLabel('LBL_Sort_By_Oldest', $langId),
        ];
        return $arr;
    }

    public static function getReviewStatusArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId == 0) {
            trigger_error(Label::getLabel('MSG_Language_Id_not_specified.', $langId), E_USER_ERROR);
        }
        $arr = [
            static::STATUS_PENDING => Label::getLabel('LBL_Pending'),
            static::STATUS_APPROVED => Label::getLabel('LBL_Approved'),
            static::STATUS_CANCELLED => Label::getLabel('LBL_Cancelled'),
        ];
        return $arr;
    }

    public static function getTeacherTotalReviews($teachcerId, $lessonId = 0, $postedBy = null)
    {
        $teachcerId = FatUtility::int($teachcerId);
        $lessonId = FatUtility::int($lessonId);
        $postedBy = FatUtility::int($postedBy);
        $srch = new TeacherLessonReviewSearch();
        $srch->joinLearner();
        $srch->joinTeacher();
        $srch->addMultipleFields(['count(*) as numOfReviews']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('tlreview_teacher_user_id', '=', $teachcerId);
        if ($lessonId > 0) {
            $srch->addCondition('tlreview_lesson_id', '=', $lessonId);
        }
        if ($postedBy != null) {
            $srch->addCondition('tlreview_postedby_user_id', '=', $postedBy);
        }
        $srch->addGroupby('tlreview_teacher_user_id');
        $record = FatApp::getDb()->fetch($srch->getResultSet());
        if ($record == false) {
            return 0;
        }
        return $record['numOfReviews'];
    }

}
