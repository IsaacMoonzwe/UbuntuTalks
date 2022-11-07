<?php

class ScheduledLessonDetailsSearch extends SearchBase
{

    public function __construct($doNotCalculateRecords = true)
    {
        parent::__construct(ScheduledLessonDetails::DB_TBL, 'sld');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function joinScheduledLesson()
    {
        $this->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sld.sldetail_slesson_id=sl.slesson_id', 'sl');
    }

    public function joinTeacher()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ut.user_id = sl.slesson_teacher_id', 'ut');
    }

    public function joinLearner()
    {
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sld.sldetail_learner_id', 'ul');
    }

    public function joinLearnerCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'lcred.credential_user_id = sld.sldetail_learner_id', 'lcred');
    }

    public function joinTeacherCredentials()
    {
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'tcred.credential_user_id = sl.slesson_teacher_id', 'tcred');
    }

    public function joinOrder()
    {
        $this->joinTable(Order::DB_TBL, 'INNER JOIN', 'sld.sldetail_order_id = o.order_id and order_type = ' . Order::TYPE_LESSON_BOOKING, 'o');
    }

    public function joinOrderProduct()
    {
        $this->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'op.op_order_id = o.order_id', 'op');
    }

    public function joinLessonLanguage()
    {
        $langId = CommonHelper::getLangId();
        $this->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'sl.slesson_slanguage_id = tlang.tlanguage_id', 'tlang');
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_sl_l.tlanguagelang_tlanguage_id = tlang.tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 't_sl_l');
        }
    }

    public function getDetailsById(int $sDetailsID): array
    {
        $this->joinScheduledLesson();
        $this->addCondition('sldetail_id', '=', $sDetailsID);
        $rs = $this->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        return $data;
    }

    public function getRecordsByLessonId($lessonId)
    {
        $this->addCondition('sldetail_slesson_id', '=', $lessonId);
        return FatApp::getDb()->fetchAll($this->getResultSet());
    }

}
