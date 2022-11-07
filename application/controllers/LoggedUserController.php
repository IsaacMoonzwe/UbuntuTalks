<?php

class LoggedUserController extends MyAppController
{

    protected $userDetails = [];
    protected $loggedUserId = 0;

    public function __construct($action)
    {
        parent::__construct($action);
        UserAuthentication::checkLogin();
        $this->userDetails = $this->verifyLoggedUser();
        $this->loggedUserId = FatUtility::int($this->userDetails['user_id']);
        $this->set('userDetails', $this->userDetails);
    }

    private function verifyLoggedUser()
    {
        $srch = new UserSearch();
        $srch->joinCredentials(false, false);
        $srch->addCondition('u.user_id', '=', UserAuthentication::getLoggedUserId());
        $srch->setPageSize(1);
        $srch->addMultipleFields(['user_first_name', 'user_id', 'user_is_teacher', 'user_last_name', 'user_country_id', 'user_preferred_dashboard', 'credential_email', 'credential_active', 'credential_verified']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs);
        if (empty($userRow) || $userRow['credential_active'] != 1) {
            if (FatUtility::isAjaxCall()) {
                Message::addErrorMessage(Label::getLabel('ERR_YOUR_ACCOUNT_HAS_BEEN_DEACTIVATED_OR_NOT_ACTIVE'));
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'logout'));
        }
        if (false === User::isAdminLogged() && 1 != $userRow['credential_verified']) {
            if (FatUtility::isAjaxCall()) {
                Message::addErrorMessage(Label::getLabel('MSG_Your_Account_verification_is_pending_,_Please_try_after_reloading_the_page'));
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'logout'));
        }
        if (UserAuthentication::getLoggedUserId() < 1) {
            if (FatUtility::isAjaxCall()) {
                Message::addErrorMessage(Label::getLabel('MSG_Session_seems_to_be_expired,_Please_try_after_reloading_the_page'));
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'logout'));
        }
        if (empty($userRow['credential_email'])) {
            if (FatUtility::isAjaxCall()) {
                Message::addErrorMessage(Label::getLabel('MSG_Please_Configure_Your_Email,_try_after_reloading_the_page'));
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'configureEmail'));
        }
        return $userRow;
    }

    protected function getLessonFlashCardSearchForm()
    {
        $frm = new Form('frmFlashCardSrch');
        $frm->addTextBox('', 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_Flash_Cards...')]);
        $fld = $frm->addHiddenField('', 'lesson_id');
        $fld->requirements()->setIntPositive();
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', '');
        return $frm;
    }

    protected function addToCartForm(): Form
    {
        $form = new Form('addToCart');
        $teacherIdField = $form->addHiddenField(Label::getLabel('LBL_Teacher_Id'), 'teacherId');
        $teacherIdField->requirements()->setRequired(true);
        $teacherIdField->requirements()->setIntPositive();
        $teacherIdField->requirements()->setRange(1, 99999999999);
        $getMinAndMaxSlab = PriceSlab::getMinAndMaxSlab();
        $min = $max = 0;
        if (!empty($getMinAndMaxSlab)) {
            $min = $getMinAndMaxSlab['minSlab'];
            $max = $getMinAndMaxSlab['maxSlab'];
        }
        $groupClassField = $form->addIntegerField(Label::getLabel('LBL_Group_Class'), 'grpclsId');
        $groupClassField->requirements()->setRequired(false);
        $groupClassField->requirements()->setRange(1, 9999999);
        $slabIdField = $form->addIntegerField(Label::getLabel('LBL_lesson_qty'), 'lessonQty');
        $slabIdField->requirements()->setRequired(false);
        $slabIdField->requirements()->setRange($min, $max);
        $slabIdField->requirements()->setRequired(true);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'gt', 'lessonQty', $slabIdField->requirements());
        $slabIdField->requirements()->setRequired(false);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'le', 'lessonQty', $slabIdField->requirements());
        $languageIdField = $form->addIntegerField(Label::getLabel('LBL_language_Id'), 'languageId');
        $languageIdField->requirements()->setRequired(false);
        $languageIdField->requirements()->setRange(1, 9999);
        $languageIdField->requirements()->setRequired(true);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'gt', 'languageId', $languageIdField->requirements());
        $languageIdField->requirements()->setRequired(false);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'le', 'languageId', $languageIdField->requirements());
        $bookingSlot = applicationConstants::getBookingSlots();
        $lessonDurationField = $form->addSelectBox(Label::getLabel('LBL_lesson_duration'), 'lessonDuration', array_flip($bookingSlot));
        $slabIdField->requirements()->setIntPositive();
        $lessonDurationField->requirements()->setRequired(true);
        $lessonDurationField->requirements()->setRequired(true);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'gt', 'lessonDuration', $lessonDurationField->requirements());
        $lessonDurationField->requirements()->setRequired(false);
        $groupClassField->requirements()->addOnChangerequirementUpdate(0, 'le', 'lessonDuration', $lessonDurationField->requirements());
        $freeTrialField = $form->addCheckBox(Label::getLabel('LBL_Free_trial'), 'isFreeTrial', applicationConstants::YES, [], false, applicationConstants::NO);
        $freeTrialField->requirements()->setRequired(false);
        $startDateTimeField = $form->addTextBox(Label::getLabel('LBL_Start_Date_Time'), 'startDateTime');
        $startDateTimeField->requirements()->setRequired(false);
        /* startDateTime requirements */
        $startDateTimeField->requirements()->setRequired(true);
        $freeTrialField->requirements()->addOnChangerequirementUpdate(applicationConstants::YES, 'eq', 'startDateTime', $startDateTimeField->requirements());
        $startDateTimeField->requirements()->setRequired(false);
        $freeTrialField->requirements()->addOnChangerequirementUpdate(applicationConstants::YES, 'ne', 'startDateTime', $startDateTimeField->requirements());
        /* ] */
        $endDateTimeField = $form->addTextBox(Label::getLabel('LBL_End_Date_Time'), 'endDateTime');
        $endDateTimeField->requirements()->setRequired(false);
        // $startDateTimeField->requirements()->setCompareWith('endDateTime', 'lt', Label::getLabel('LBL_End_Date_Time'));
        // $endDateTimeField->requirements()->setCompareWith('startDateTime', 'gt', Label::getLabel('LBL_Start_Date_Time'));
        /* endDateTime requirements */
        $endDateTimeField->requirements()->setRequired(true);
        $freeTrialField->requirements()->addOnChangerequirementUpdate(applicationConstants::YES, 'eq', 'endDateTime', $endDateTimeField->requirements());
        $endDateTimeField->requirements()->setRequired(false);
        $freeTrialField->requirements()->addOnChangerequirementUpdate(applicationConstants::YES, 'ne', 'endDateTime', $endDateTimeField->requirements());
        /* ] */
        $weekStartField = $form->addTextBox(Label::getLabel('LBL_week_Start'), 'weekStart');
        $weekStartField->requirements()->setRequired(false);
        return $form;
    }

}
