<?php

class FlashCard extends MyAppModel
{

    const DB_TBL = 'tbl_flashcards';
    const DB_TBL_PREFIX = 'flashcard_';
    const DB_TBL_SHARED = 'tbl_shared_flashcards';
    const ACCURACY_LEVEL_CORRECT = 1;
    const ACCURACY_LEVEL_ALMOST = 2;
    const ACCURACY_LEVEL_WRONG = 3;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getAccuracyArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId <= 0) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::ACCURACY_LEVEL_CORRECT => Label::getLabel('LBL_Correct', $langId),
            static::ACCURACY_LEVEL_ALMOST => Label::getLabel('LBL_Upper_Almost', $langId),
            static::ACCURACY_LEVEL_WRONG => Label::getLabel('LBL_Wrong', $langId),
        ];
    }

    public function save()
    {
        if ($this->mainTableRecordId == 0) {
            $this->setFldValue('flashcard_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

}
