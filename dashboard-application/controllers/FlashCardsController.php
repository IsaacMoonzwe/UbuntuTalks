<?php

class FlashCardsController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        if (!FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true)) {
            FatUtility::exitWithErrorCode(404);
        }
    }

    public function setUp()
    {
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frm->getValidationErrors());
        }
        $flashCardId = FatApp::getPostedData('flashcard_id', FatUtility::VAR_INT, 0);
        if ($flashCardId > 0) {
            $row = FlashCard::getAttributesById($flashCardId, ['flashcard_user_id']);
            if ($row['flashcard_user_id'] != UserAuthentication::getLoggedUserId()) {
                FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
            }
        }
        $post['flashcard_user_id'] = UserAuthentication::getLoggedUserId();
        $post['flashcard_created_by_user_id'] = UserAuthentication::getLoggedUserId();
        $flashCardObj = new FlashCard($flashCardId);
        $flashCardObj->assignValues($post);
        if (!$flashCardObj->save($post)) {
            FatUtility::dieJsonError($flashCardObj->getError());
        }
        $this->set('msg', Label::getLabel('LBL_Flashcard_Saved_Successfully!'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm();
        $this->set('frmSrch', $frmSrch);
        $this->_template->addJs('js/jquery.flip.min.js');
        $this->_template->addJs('js/jquery-confirm.min.js');
        // $this->_template->addCss('css/jquery-confirm.min.css');
        $this->_template->render();
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $srch = new FlashCardSearch(false);
        $srch->joinWordLanguage();
        $srch->joinWordDefinitionLanguage();
        $srch->addCondition('flashcard_user_id', ' = ', UserAuthentication::getLoggedUserId());
        $srch->addOrder('flashcard_added_on', 'DESC');
        $srch->addOrder('flashcard_title', 'DESC');
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $srch->addMultipleFields([
            'flashcard_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
        ]);
        if (!empty($post['keyword'])) {
            $srch->addCondition('flashcard_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['slanguage_id'])) {
            $srch->addCondition('flashcard_slanguage_id', '=', $post['slanguage_id']);
        }
        $rs = $srch->getResultSet();
        $flashCards = FatApp::getDb()->fetchAll($rs);
        // [
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        // ]
        $this->set('flashCards', $flashCards);
        $this->_template->render(false, false);
    }

    public function form($flashCardId = 0)
    {
        $flashCardId = FatUtility::int($flashCardId);
        $frm = $this->getForm();
        if ($flashCardId > 0) {
            $row = FlashCard::getAttributesById($flashCardId);
            $frm->fill($row);
        }
        $this->set('flashCardId', $flashCardId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function remove($flashCardId)
    {
        $flashCardId = FatUtility::int($flashCardId);
        $row = FlashCard::getAttributesById($flashCardId, ['flashcard_user_id']);
        if ($row['flashcard_user_id'] != UserAuthentication::getLoggedUserId()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $flashCardObj = new FlashCard($flashCardId);
        if (!$flashCardObj->deleteRecord()) {
            FatUtility::dieJsonError($flashCardObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Record_Deleted_Successfully!'));
    }

    public function viewFlashCardReviewSection()
    {
        $srch = new FlashCardSearch();
        $srch->addCondition('flashcard_user_id', ' = ', UserAuthentication::getLoggedUserId());
        // [
        $todayReviewedCountSrch = clone $srch;
        $todayReviewedCountSrch->doNotLimitRecords();
        $todayReviewedCountSrch->addCondition('mysql_func_DATE(flashcard_accuracy_added_on)', '=', date('Y-m-d'), 'AND', true);
        $todayReviewedCountSrch->addMultipleFields(['COUNT(flashcard_id) as todayReviewedCounts']);
        $rs = $todayReviewedCountSrch->getResultSet();
        $todayReviewedCountsRow = FatApp::getDb()->fetch($rs);
        // ]
        // [
        $allFCardSrch = clone $srch;
        $allFCardSrch->doNotLimitRecords();
        $allFCardSrch->addMultipleFields(['COUNT(flashcard_id) as allFCardCounts']);
        $rs = $allFCardSrch->getResultSet();
        $allFCardCountsRow = FatApp::getDb()->fetch($rs);
        // ]
        // [
        $lastReviewedOnSrch = clone $srch;
        $lastReviewedOnSrch->addCondition('flashcard_accuracy', '!=', 0);
        $lastReviewedOnSrch->addOrder('flashcard_accuracy_added_on', 'DESC');
        $lastReviewedOnSrch->setPageSize(1);
        $lastReviewedOnSrch->addFld('flashcard_accuracy_added_on');
        $rs = $lastReviewedOnSrch->getResultSet();
        $lastReviewedOnRow = FatApp::getDb()->fetch($rs);
        // ]
        $this->set('todayReviewedCounts', $todayReviewedCountsRow['todayReviewedCounts']);
        $this->set('allFCardCounts', $allFCardCountsRow['allFCardCounts']);
        $this->set('lastReviewedOnDate', !empty($lastReviewedOnRow) ? $lastReviewedOnRow['flashcard_accuracy_added_on'] : '');
        $this->_template->render(false, false);
    }

    public function reviewFlashCard()
    {
        $srch = new FlashCardSearch();
        $srch->addCondition('flashcard_user_id', ' = ', UserAuthentication::getLoggedUserId());
        // [
        $todayReviewedCountSrch = clone $srch;
        $todayReviewedCountSrch->doNotLimitRecords();
        $todayReviewedCountSrch->addCondition('mysql_func_DATE(flashcard_accuracy_added_on)', '=', date('Y-m-d'), 'AND', true);
        $todayReviewedCountSrch->addMultipleFields(['COUNT(flashcard_id) as todayReviewedCounts']);
        $rs = $todayReviewedCountSrch->getResultSet();
        $todayReviewedCountsRow = FatApp::getDb()->fetch($rs);
        $todayReviewedCount = $todayReviewedCountsRow['todayReviewedCounts'];
        // ]
        // [
        $allFCardSrch = clone $srch;
        $allFCardSrch->doNotLimitRecords();
        $allFCardSrch->addMultipleFields(['COUNT(flashcard_id) as allFCardCounts']);
        $rs = $allFCardSrch->getResultSet();
        $allFCardCountsRow = FatApp::getDb()->fetch($rs);
        $allFCardCount = $allFCardCountsRow['allFCardCounts'];
        // ]
        $currentReviewedCount = $todayReviewedCount + 1;
        $currentReviewedCount = min($currentReviewedCount, $allFCardCount);
        // [
        $flashCardSrch = clone $srch;
        $flashCardSrch->joinWordLanguage();
        $flashCardSrch->joinWordDefinitionLanguage();
        $flashCardSrch->addMultipleFields([
            'flashcard_id',
            'flashcard_title',
            'wordLang.slanguage_code as wordLanguageCode',
            'flashcard_pronunciation',
            'flashcard_defination',
            'wordDefLang.slanguage_code as wordDefLanguageCode',
        ]);
        $flashCardSrch->setPageSize(1);
        // ]
        if ($todayReviewedCount == $allFCardCount) {
            $page = FatApp::getPostedData('currentReviewedCount', FatUtility::VAR_INT, 1);
            $currentReviewedCount = 1;
            if ($page > 0) {
                $currentReviewedCount = $page + 1;
            }
            ++$page;
            $flashCardSrch->setPageNumber($page);
            $flashCardSrch->addOrder('flashcard_accuracy_added_on', 'DESC');
            $flashCardSrch->addOrder('flashcard_title', 'DESC');
            $flashCardSrch->addCondition('mysql_func_DATE(flashcard_accuracy_added_on)', '=', date('Y-m-d'), 'AND', true);
        } else {
            $flashCardSrch->addOrder('flashcard_added_on', 'DESC');
            $flashCardSrch->addOrder('flashcard_title', 'DESC');
            $flashCardSrch->addCondition('mysql_func_DATE(flashcard_accuracy_added_on)', '!=', date('Y-m-d'), 'AND', true);
        }
        // [
        $rs = $flashCardSrch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        // ]
        $this->set('currentReviewedCount', $currentReviewedCount);
        $this->set('allFCardCounts', $allFCardCount);
        $this->set('flashCardData', $row);
        $this->set('flashCardAccuracyArr', FlashCard::getAccuracyArr());
        $this->_template->render(false, false);
    }

    public function setUpFlashCardReview()
    {
        $flashcard_id = FatApp::getPostedData('flashcard_id', FatUtility::VAR_INT, 0);
        $flashcard_accuracy = FatApp::getPostedData('flashcard_accuracy', FatUtility::VAR_INT, 0);
        if ($flashcard_id <= 0 || $flashcard_accuracy <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $row = FlashCard::getAttributesById($flashcard_id, ['flashcard_user_id']);
        if (UserAuthentication::getLoggedUserId() != $row['flashcard_user_id']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $flashCard = new FlashCard($flashcard_id);
        $flashCard->assignValues(['flashcard_accuracy' => $flashcard_accuracy, 'flashcard_accuracy_added_on' => date('Y-m-d H:i:s')]);
        if (!$flashCard->save()) {
            FatUtility::dieJsonError($flashCard->getError());
        }
        $this->set('msg', Label::getLabel('LBL_FlashCard_Reviewed_Successfully!'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function reviewResult()
    {
        $srchBase = new FlashCardSearch(false);
        $srchBase->addMultipleFields(['flashcard_id']);
        $srchBase->addCondition('flashcard_user_id', ' = ', UserAuthentication::getLoggedUserId());
        // Counting Correct Flashcards
        $srchCorrect = clone $srchBase;
        $srchCorrect->addCondition('flashcard_accuracy', ' = ', FlashCard::ACCURACY_LEVEL_CORRECT);
        $rsCorrect = $srchCorrect->getResultSet();
        $countCorrect = $srchCorrect->recordCount();
        // Counting Correct Flashcards
        // Counting InCorrect Flashcards
        $srchInCorrect = clone $srchBase;
        $srchInCorrect->addCondition('flashcard_accuracy', ' = ', FlashCard::ACCURACY_LEVEL_WRONG);
        $rsInCorrect = $srchInCorrect->getResultSet();
        $countIncorrect = $srchInCorrect->recordCount();
        // Counting InCorrect Flashcards
        // Counting AlmostCorrect Flashcards
        $srchAlmostCorrect = clone $srchBase;
        $srchAlmostCorrect->addCondition('flashcard_accuracy', ' = ', FlashCard::ACCURACY_LEVEL_ALMOST);
        $rsAlmostCorrect = $srchAlmostCorrect->getResultSet();
        $countAlmostCorrect = $srchAlmostCorrect->recordCount();
        // Counting AlmostCorrect Flashcards
        // Fetching 1st flashcard id for restart
        $rs = $srchBase->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        // Fetching 1st flashcard id for restart
        $this->set('flashCardId', $row['flashcard_id']);
        $this->set('countAlmostCorrect', $countAlmostCorrect);
        $this->set('countIncorrect', $countIncorrect);
        $this->set('countCorrect', $countCorrect);
        $this->_template->render(false, false);
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addSelectBox(Label::getLabel('LBL_Language'), 'slanguage_id', SpokenLanguage::getAllLangs(CommonHelper::getLangId(), true), '', [], Label::getLabel('LBL_All'))->requirements()->setInt();
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnReset = $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        $btnSubmit->attachField($btnReset);
        return $frm;
    }

    private function getForm()
    {
        $frm = new Form('flashcardFrm');
        $frm->addRequiredField(Label::getLabel('LBL_Title'), 'flashcard_title');
        $langArr = SpokenLanguage::getAllLangs(CommonHelper::getLangId(), true);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Title_Language'), 'flashcard_slanguage_id', $langArr);
        $fld->requirements()->setRequired(true);
        $frm->addRequiredField(Label::getLabel('LBL_Defination'), 'flashcard_defination');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Defination_Language'), 'flashcard_defination_slanguage_id', $langArr);
        $fld->requirements()->setRequired(true);
        $frm->addTextBox(Label::getLabel('LBL_Pronunciation'), 'flashcard_pronunciation');
        $fld = $frm->addHiddenField('', 'flashcard_id', 0);
        $fld->requirements()->setInt();
        $frm->addTextArea(Label::getLabel('LBL_Notes'), 'flashcard_notes');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

}
