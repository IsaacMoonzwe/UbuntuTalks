<?php

class LoggedUserController extends MyAppController
{

    protected $userDetails = [];
    protected $loggedUserId = 0;

    public function __construct($action)
    {
        parent::__construct($action);
        EventUserAuthentication::checkLogin();
        $this->userDetails = $this->verifyLoggedUser();
        $this->loggedUserId = FatUtility::int($this->userDetails['user_id']);
        $this->set('userDetails', $this->userDetails);
        if (!FatUtility::isAjaxCall() && $this->userDetails['user_is_teacher'] == applicationConstants::YES) {
            $this->teacherProfileProgress = User::getTeacherProfileProgress();
            $this->set('teacherProfileProgress', $this->teacherProfileProgress);
        }
    }

    private function verifyLoggedUser()
    {
        $srch = new UserSearch();
        $srch->joinCredentials(false, false);
        $srch->addCondition('u.user_id', '=', EventUserAuthentication::getLoggedUserId());
        $srch->setPageSize(1);
        $srch->addMultipleFields(['user_first_name', 'user_id', 'user_url_name', 'user_is_teacher', 'user_last_name', 'user_country_id', 'user_preferred_dashboard', 'credential_email', 'credential_active', 'credential_verified']);
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
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'configureEmail', [], CONF_WEBROOT_FRONT_URL));
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

}
