<?php

class TeacherOfferPrice extends MyAppModel
{

    const DB_TBL = 'tbl_teacher_offer_price';
    const DB_TBL_PREFIX = 'top_';

    protected $teacherId;
    protected $learnerId;

    public function __construct($teacherId = 0, $learnerId = 0, $id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->teacherId = $teacherId;
        $this->learnerId = $learnerId;
    }

    public function saveOffer(float $percentage, int $lesonDuration)
    {
        if (0 > $this->teacherId || 0 > $this->learnerId) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $data = [
            'top_percentage' => $percentage,
            'top_lesson_duration' => $lesonDuration,
            'top_teacher_id' => $this->teacherId,
            'top_learner_id' => $this->learnerId,
        ];
        $this->assignValues($data);
        return $this->addNew([], $data);
    }

    public function removeOffer(): bool
    {
        if (0 > $this->teacherId || 0 > $this->learnerId) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->deleteRecords(static::DB_TBL, [
                    "smt" => "top_learner_id = ? and top_teacher_id = ?",
                    "vals" => [$this->learnerId, $this->teacherId]
                ])) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function getOffer(int $learnerId, int $teacherId, int $lessonDuration): SearchBase
    {
        if (0 > $learnerId || 0 > $teacherId) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $srch = new SearchBase(self::DB_TBL, 'us');
        $srch->addCondition('top_learner_id', '=', $learnerId);
        $srch->addCondition('top_teacher_id', '=', $teacherId);
        $srch->addCondition('top_lesson_duration', '=', $lessonDuration);
        return $srch;
    }

    public function getTeacherOffer(int $learnerId, int $teacherId)
    {
        $srch = new SearchBase(self::DB_TBL, 'top');
        $srch->addCondition('top_learner_id', '=', $learnerId);
        $srch->addCondition('top_teacher_id', '=', $teacherId);
        $srch->addCondition('top_percentage', '>', 0);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

}
