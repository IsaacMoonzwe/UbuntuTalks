<?php

class FlashCardSearch extends SearchBase
{

    private $isSharedFlashCardJoined;

    public function __construct($doNotCalculateRecords = true)
    {
        $this->isSharedFlashCardJoined = false;
        parent::__construct(FlashCard::DB_TBL, 'tlpn');
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
    }

    public function joinSharedFlashCard()
    {
        $this->joinTable(FlashCard::DB_TBL_SHARED, 'INNER JOIN', 'sflashcard_flashcard_id = flashcard_id');
        $this->isSharedFlashCardJoined = true;
    }

    public function joinWordLanguage()
    {
        $this->joinTable(SpokenLanguage::DB_TBL, 'INNER JOIN', 'flashcard_slanguage_id = wordLang.slanguage_id', 'wordLang');
    }

    public function joinWordDefinitionLanguage()
    {
        $this->joinTable(SpokenLanguage::DB_TBL, 'INNER JOIN', 'flashcard_defination_slanguage_id = wordDefLang.slanguage_id', 'wordDefLang');
    }

    public function joinLesson()
    {
        if (false === $this->isSharedFlashCardJoined) {
            trigger_error("Please Join Shared FlashCard First to Join with Lesson", E_USER_ERROR);
        }
        if (true === $this->isSharedFlashCardJoined) {
            return;
        }
        $this->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sl.slesson_id = sflashcard_slesson_id', 'sl');
    }

}
