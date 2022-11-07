<?php
session_start();
class GuestUserController extends MyAppController
{

    public function loginForm()
    {
        if (UserAuthentication::isUserLogged()) {
            FatApp::redirectUser(User::getPreferedDashbordRedirectUrl());
        }
        $frm = $this->getLoginForm();
        $this->set('frm', $frm);
        $this->set('userType', User::USER_TYPE_LEANER);
        $this->_template->render();
    }

    public function logInFormPopUp()
    {
        if (UserAuthentication::isUserLogged()) {
            Message::addErrorMessage(Label::getLabel('MSG_Already_Logged_in,_Please_try_after_reloading_the_page'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLoginForm();
        $frm->setFormTagAttribute('name', 'frmLoginPopUp');
        $frm->setFormTagAttribute('id', 'frmLoginPopUp');
        $this->set('frm', $frm);
        $this->set('userType', User::USER_TYPE_LEANER);
        $this->_template->render(false, false);
    }

    public function setUpLogin($checked=0)
    {
        $authentication = new UserAuthentication();
        $userName = FatApp::getPostedData('username', FatUtility::VAR_STRING, '');
        $password = FatApp::getPostedData('password', FatUtility::VAR_STRING, '');

        // if($checked==0){
        if (true !== $authentication->login($userName, $password, CommonHelper::getClientIp())) {
            FatUtility::dieWithError(Label::getLabel($authentication->getError()));
        }
      
        $userId = UserAuthentication::getLoggedUserId();
        $rememberme = FatApp::getPostedData('remember_me', FatUtility::VAR_INT, 0);
        if ($rememberme == applicationConstants::YES) {
            if (true !== $this->setUserLoginCookie($userId)) {
                //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
            }
        }
        FatUtility::dieJsonSuccess(['redirectUrl' => User::getPreferedDashbordRedirectUrl(), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }

    public function registrationForm()
    {
        $frm = $this->getSignUpForm();
        $this->set('frm', $frm);
        /* [ */
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $this->_template->render(true, true, 'guest-user/registration-form.php');
    }

    public function signUpFormPopUp()
    {
        $json = [];
        $json['status'] = true;
        $json['msg'] = Label::getLabel('LBL_Request_Processing..');
        $post = FatApp::getPostedData();
        $userType = User::USER_TYPE_LEANER;
        if (UserAuthentication::isUserLogged()) {
            if ($post['signUpType'] == "teacher") {
                $user_preferred_dashboard = User::USER_TEACHER_DASHBOARD;
            } else {
                $userRow = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_preferred_dashboard']);
                $user_preferred_dashboard = $userRow['user_preferred_dashboard'];
            }
            $json['redirectUrl'] = User::getPreferedDashbordRedirectUrl($user_preferred_dashboard, false);
            FatUtility::dieJsonSuccess($json);
        }
        $user_preferred_dashboard = User::USER_LEARNER_DASHBOARD;
        if ($post['signUpType'] == "teacher") {
            $user_preferred_dashboard = User::USER_TEACHER_DASHBOARD;
            $userType = User::USER_TYPE_TEACHER;
        }
        $frm = $this->getSignUpForm();
        $frm->setFormTagAttribute('name', 'frmRegisterPopUp');
        $frm->setFormTagAttribute('id', 'frmRegisterPopUp');
        $frm->fill(['user_preferred_dashboard' => $user_preferred_dashboard]);
        $this->set('frm', $frm);
        /* [ */
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('userType', $userType);
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $json['html'] = $this->_template->render(false, false, 'guest-user/sign-up-form-pop-up.php', true, false);
        FatUtility::dieJsonSuccess($json);
    }

    public function setUpSignUp()
    {
        $frm = $this->getSignUpForm();
        $post = FatApp::getPostedData();
        if (!isset($post['user_first_name'])) {
            $post['user_first_name'] = strstr($post['user_email'], '@', true);
        }
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'registrationForm'));
        }
        if (true !== CommonHelper::validatePassword($post['user_password'])) {
            Message::addErrorMessage(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $user = new User();
        /* saving user data[ */
        $post['user_is_learner'] = 1;
        $user_preferred_dashboard = User::USER_LEARNER_DASHBOARD;
        $user_registered_initially_for = User::USER_TYPE_LEANER;
        $posted_user_preferred_dashboard = FatApp::getPostedData('user_preferred_dashboard', FatUtility::VAR_INT, 0);
        if ($posted_user_preferred_dashboard == User::USER_TEACHER_DASHBOARD) {
            $user_preferred_dashboard = User::USER_TEACHER_DASHBOARD;
            $user_registered_initially_for = User::USER_TYPE_TEACHER;
        }
        $post['user_timezone'] = $_COOKIE['user_timezone'] ?? MyDate::getTimeZone();;
        $post['user_preferred_dashboard'] = $user_preferred_dashboard;
        $post['user_registered_initially_for'] = $user_registered_initially_for;
        $user->assignValues($post);
        if (true !== $user->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $user->getError());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        $active = FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1) ? 0 : 1;
        $verify = FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1) ? 0 : 1;
        if (true !== $user->setLoginCredentials($post['user_email'], $post['user_email'], $post['user_password'], $active, $verify)) {
            Message::addErrorMessage(Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET") . $user->getError());
            $db->rollbackTransaction();
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        /* ] */
        $db->commitTransaction();
        $redirectUrl = CommonHelper::redirectUserReferer(true);
        if ($user->getMainTableRecordId() and $user_registered_initially_for == User::USER_TYPE_TEACHER) {
            $_SESSION[UserAuthentication::SESSION_GUEST_USER_ELEMENT_NAME] = $user->getMainTableRecordId();
            $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
        } else {
            unset($_SESSION[UserAuthentication::SESSION_GUEST_USER_ELEMENT_NAME]);
        }
        if (1 == FatApp::getConfig('CONF_NOTIFY_ADMIN_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $user->notifyAdminRegistration($post, $this->siteLangId)) {
                Message::addErrorMessage(Label::getLabel("MSG_NOTIFICATION_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
        }
        if (1 == FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $this->sendEmailVerificationLink($user, $post)) {
                Message::addErrorMessage(Label::getLabel("MSG_VERIFICATION_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
            Message::addMessage(Label::getLabel('MSG_VERIFICATION_EMAIL_SENT'));
            $this->set('msg', Label::getLabel('MSG_VERIFICATION_EMAIL_SENT'));
            $this->set('redirectUrl', $redirectUrl);
            $this->_template->render(false, false, 'json-success.php');
        }
        if (1 == FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $this->sendSignUpWelcomeEmail($user, $post)) {
                Message::addErrorMessage(Label::getLabel("MSG_WELCOME_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
        }
        $confAutoLoginRegisteration = FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION', FatUtility::VAR_INT, 1);
        if (1 === $confAutoLoginRegisteration && (0 === FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1))) {
            $authentication = new UserAuthentication();
            if (true != $authentication->login(FatApp::getPostedData('user_email'), FatApp::getPostedData('user_password'), $_SERVER['REMOTE_ADDR'])) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
            Message::addMessage(Label::getLabel('LBL_Registeration_Successfull'));
            $redirectUrl = User::getPreferedDashbordRedirectUrl();
            $this->set('redirectUrl', $redirectUrl);
            $this->set('msg', Label::getLabel('LBL_Registeration_Successfull'));
            $this->_template->render(false, false, 'json-success.php');
        }
        Message::addMessage(Label::getLabel('LBL_Registeration_Successfull'));
        $this->set('msg', Label::getLabel('LBL_Registeration_Successfull'));
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function registrationSuccess()
    {
        if (1 === FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1)) {
            $this->set('registrationMsg', Label::getLabel("MSG_SUCCESS_USER_SIGNUP_EMAIL_VERIFICATION_PENDING"));
        } else {
            $this->set('registrationMsg', Label::getLabel("MSG_SUCCESS_USER_SIGNUP_ADMIN_APPROVAL_PENDING"));
        }
        $this->_template->render();
    }

    public function userCheckEmailVerification($code)
    {
        $code = FatUtility::convertToType($code, FatUtility::VAR_STRING, '');
        if (strlen($code) < 1) {
            Message::addMessage(Label::getLabel("MSG_PLEASE_CHECK_YOUR_EMAIL_IN_ORDER_TO_VERIFY"));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $codeArr = explode('_', $code, 2);
        $userId = FatUtility::int($codeArr[0]);
        if ($userId < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $userObj = new User($userId);
        $userData = User::getAttributesById($userId, ['user_id',]);
        if (!$userData || $userData['user_id'] != $userId) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $srch = new SearchBase('tbl_user_credentials');
        $srch->addCondition('credential_user_id', '=', $userId);
        $rs = $srch->getResultSet();
        $userCredentialRow = $db->fetch($rs);
        if (applicationConstants::ACTIVE === $userCredentialRow['credential_verified']) {
            Message::addErrorMessage(Label::getLabel('MSG_Your_Account_Is_Already_Verified'));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        if (applicationConstants::ACTIVE !== $userCredentialRow['credential_active']) {
            $active = FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1) ? 0 : 1;
            if (0 === $userObj->activateAccount($active)) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
            }
        }
        if (true !== $userObj->verifyAccount()) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $db->commitTransaction();
        $userdata = $userObj->getUserInfo([
            'credential_email',
            'credential_password',
            'user_first_name',
            'user_last_name',
            'credential_active'
        ], false);
        if (1 === FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1)) {
            $data['user_email'] = $userdata['credential_email'];
            $data['user_first_name'] = $userdata['user_first_name'];
            $data['user_last_name'] = $userdata['user_last_name'];
            if (true !== $this->sendSignUpWelcomeEmail($userObj, $data)) {
                Message::addErrorMessage(Label::getLabel("MSG_WELCOME_EMAIL_COULD_NOT_BE_SENT"));
                $db->rollbackTransaction();
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
            }
        }
        Message::addMessage(Label::getLabel("MSG_EMAIL_VERIFIED_SUCCESFULLY"));
        FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
    }

    public function logout()
    {
        unset($_SESSION[UserAuthentication::SESSION_ELEMENT_NAME]);
        unset($_SESSION['referer_page_url']);
        UserAuthentication::clearLoggedUserLoginCookie();
        FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
    }

    private function setUserLoginCookie(int $userId)
    {
        $expiryDays = FatApp::getConfig('CONF_USER_REMEMBER_ME_DAYS', FatUtility::VAR_INT);
        $token = md5(uniqid('t', true));
        $expiry = strtotime("+" . $expiryDays . " DAYS");
        $values = [
            'uauth_user_id' => $userId,
            'uauth_token' => $token,
            'uauth_expiry' => date('Y-m-d H:i:s', $expiry),
            'uauth_browser' => CommonHelper::userAgent(),
            'uauth_last_access' => date('Y-m-d H:i:s'),
            'uauth_last_ip' => CommonHelper::getClientIp()
        ];
        if (UserAuthentication::saveLoginToken($values)) {
            $cookieName = UserAuthentication::YOCOACHUSER_COOKIE_NAME;
            CommonHelper::setCookie($cookieName, $token, $expiry, CONF_WEBROOT_FRONTEND, '', true);
            return true;
        }
        return false;
    }

    private function getSignUpForm()
    {
        $frm = new Form('frmRegister');
        $frm->addHiddenField('', 'user_id', 0);
        $fld = $frm->addTextBox(Label::getLabel('LBL_First_Name'), 'user_first_name');
        $fld->requirements()->setRequired();
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $fld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_First_Name'), '{A-Z,a-z and 0-9}'));
        $fld = $frm->addTextBox(Label::getLabel('LBL_Last_Name'), 'user_last_name');
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $fld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_Last_Name'), '{A-Z,a-z and 0-9}'));
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email_ID'), 'user_email', '', ['autocomplete="off"']);
        $fld->setUnique('tbl_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'user_password');
        $fld->requirements()->setRequired();
        $conNewPwd = $frm->addPasswordField(Label::getLabel('LBL_Show_Password', $this->adminLangId), 'conf_new_password', '', ['id' => 'conf_new_password']);
        $conNewPwdReq = $conNewPwd->requirements();
        // $conNewPwdReq->setRequired();
        $conNewPwdReq->setCompareWith('user_password', 'eq');
        $conNewPwdReq->setCustomErrorMessage(Label::getLabel('LBL_Confirm_Password_Not_Matched!', $this->adminLangId));
        $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_8_Digit_AlphaNumeric_Password'));
        $termsConditionLabel = Label::getLabel('LBL_I_accept_to_the');
        $fld = $frm->addCheckBox($termsConditionLabel, 'agree', 1);
        $fld->requirements()->setRequired();
        $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));
        $frm->addHiddenField('', 'user_preferred_dashboard', User::USER_LEARNER_DASHBOARD);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Register'));
        return $frm;
    }

    private function getLoginForm()
    {
        $userName = '';
        $pass = '';
        if (CommonHelper::demoUrl()) {
            if ((FatApp::getQueryStringData('type') == 'teacher')) {
                $userName = 'grace@dummyid.com';
                $pass = 'grace@123';
            } else {
                $userName = 'jason@dummyid.com';
                $pass = 'jason@123';
            }
        }
        $frm = new Form('frmLogin');
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Email'), 'username', $userName, ['placeholder' => Label::getLabel('LBL_EMAIL_ADDRESS')]);
        $pwd = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'password', $pass, ['placeholder' => Label::getLabel('LBL_PASSWORD')]);
        $pwd->requirements()->setRequired();
        $frm->addCheckbox(Label::getLabel('LBL_Remember_Me'), 'remember_me', 1, [], '', 0);
        $frm->addHtml('', 'forgot', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_LOGIN'));
        return $frm;
    }

    private function sendEmailVerificationLink($userObj, $data)
    {
        $verificationCode = $userObj->prepareUserVerificationCode();
        $link = CommonHelper::generateFullUrl('GuestUser', 'userCheckEmailVerification', ['verify' => $verificationCode]);
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $email = new EmailHandler();
        if (true !== $email->sendEmailVerificationLink($this->siteLangId, $data)) {
            return false;
        }
        return true;
    }

    private function sendSignUpWelcomeEmail($userObj, $data)
    {
        $link = CommonHelper::generateFullUrl('GuestUser', 'loginForm');
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link
        ];
        $email = new EmailHandler();
        if (true !== $email->sendWelcomeEmail($this->siteLangId, $data)) {
            Message::addMessage(Label::getLabel("MSG_ERROR_IN_SENDING_WELCOME_EMAIL"));
            return false;
        }
        return true;
    }

    public function socialMediaLogin($oauthProvider, $userType = User::USER_TYPE_LEANER)
    {
        if (isset($oauthProvider)) {
            if ($oauthProvider == 'googleplus') {
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginGoogleplus', [$userType]));
            } elseif ($oauthProvider == 'google') {
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginGoogle', [$userType]));
            } elseif ($oauthProvider == 'facebook') {
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginFacebook'));
            } else {
                Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_REQUEST'));
            }
        }
        CommonHelper::redirectUserReferer();
    }

    public function loginFacebook()
    {
        $post = FatApp::getPostedData();
        $facebookEmail = isset($post['email']) ? $post['email'] : NULL;
        $accessToken = FatApp::getPostedData('accessToken', FatUtility::VAR_STRING, '');
        if (empty($post['id']) || empty($accessToken)) {
            FatUtility::dieJsonError(Label::getLabel("MSG_THERE_WAS_SOME_PROBLEM_IN_AUTHENTICATING_YOUR_ACCOUNT_WITH_FACEBOOK,_PLEASE_TRY_WITH_DIFFERENT_LOGIN_OPTIONS", $this->siteLangId));
        }
        $userFacebookId = $post['id'];
        $error = '';
        if (!$this->verifyFacebookUserAccessToken($accessToken, $userFacebookId, $error)) {
            FatUtility::dieJsonError($error);
        }
        $userFirstName = $post['first_name'];
        $userLastName = $post['last_name'];
        $user_type = FatApp::getPostedData('type', FatUtility::VAR_INT, User::USER_TYPE_LEANER);
        $preferredDashboard = User::USER_LEARNER_DASHBOARD;
        if ($user_type == User::USER_TYPE_TEACHER) {
            $preferredDashboard = User::USER_TEACHER_DASHBOARD;
        }
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_code']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_access_token']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_user_id']);
        $facebookName = $userFirstName . ' ' . $userLastName;
        $db = FatApp::getDb();
        $userObj = new User();
        $srch = $userObj->getUserSearchObj(['user_id', 'user_facebook_id', 'user_preferred_dashboard', 'credential_email', 'credential_verified', 'credential_active', 'user_deleted'], false, false);
        if (!empty($facebookEmail)) {
            $srch->addCondition('credential_email', '=', $facebookEmail);
        } else {
            $srch->addCondition('user_facebook_id', '=', $userFacebookId);
        }
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if ($row) {
            if ($row['credential_active'] != applicationConstants::ACTIVE) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("ERR_YOUR_ACCOUNT_HAS_BEEN_DEACTIVATED")]);
            }
            if ($row['user_deleted'] == applicationConstants::YES) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("ERR_USER_INACTIVE_OR_DELETED")]);
            }
            $userObj->setMainTableRecordId($row['user_id']);
            $arr = ['user_facebook_id' => $userFacebookId];
            if (!$userObj->setUserInfo($arr)) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("LBL_ERROR_TO_UPDATE_USER_DATA")]);
            }
            $row['credential_verified'] = FatUtility::int($row['credential_verified']);
            if ($row['credential_verified'] != applicationConstants::YES && !empty($facebookEmail)) {
                if (!$userObj->verifyAccount(applicationConstants::YES)) {
                    FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("LBL_ERROR_TO_UPDATE_USER_DATA")]);
                }
            }
            if ($row['user_preferred_dashboard'] == User::USER_TEACHER_DASHBOARD) {
                $user_type = User::USER_TYPE_TEACHER;
            }
        } else {
            $userNameArr = explode(" ", $facebookName);
            $user_first_name = (!empty($userNameArr[0])) ? $userNameArr[0] : '';
            $user_last_name = (!empty($userNameArr[1])) ? $userNameArr[1] : '';
            $db->startTransaction();
            $userData = [
                'user_first_name' => $user_first_name,
                'user_last_name' => $user_last_name,
                'user_is_learner' => 1,
                'user_facebook_id' => $userFacebookId,
                'user_preferred_dashboard' => $preferredDashboard,
                'user_registered_initially_for' => $user_type,
            ];
            $userObj->assignValues($userData);
            if (!$userObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_USER_COULD_NOT_BE_SET")]);
            }
            $username = str_replace(" ", "", $facebookName) . $userFacebookId;
            if (!$userObj->setLoginCredentials($username, $facebookEmail, uniqid(), 1, 1)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET")]);
            }
            $db->commitTransaction();
            $userId = $userObj->getMainTableRecordId();
            $userObj = new User($userId);
            $userData['user_username'] = $username;
            $userData['user_email'] = $facebookEmail;
            if (FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1) && $facebookEmail) {
                $data['user_email'] = $facebookEmail;
                $data['user_first_name'] = $user_first_name;
                $data['user_last_name'] = $user_last_name;
                $this->userWelcomeEmailRegistration($userObj, $data);
            }
        }
        $userInfo = $userObj->getUserInfo(['user_facebook_id', 'user_is_teacher', 'credential_username', 'credential_password', 'credential_email',]);
        if (!$userInfo || ($userInfo && $userInfo['user_facebook_id'] != $userFacebookId)) {
            FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_USER_COULD_NOT_BE_SET")]);
        }
        $authentication = new UserAuthentication();
        if (!$authentication->login($userInfo['credential_username'], $userInfo['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
            FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => $authentication->getError()]);
        }
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_code']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_access_token']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_user_id']);
        $redirectUrl = User::getPreferedDashbordRedirectUrl();
        $isUserTeacher = FatUtility::int($userInfo['user_is_teacher']);
        if ($user_type == User::USER_TYPE_TEACHER && $isUserTeacher != applicationConstants::YES) {
            $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
        }
        $message = Label::getLabel('MSG_LoggedIn_SUCCESSFULLY', $this->siteLangId);
        if (empty($userInfo['credential_email'])) {
            $message = Label::getLabel('MSG_PLEASE_CONFIGURE_YOUR_EMAIL', $this->siteLangId);
            $redirectUrl = CommonHelper::generateUrl('GuestUser', 'configureEmail');
        }
        FatUtility::dieJsonSuccess(['url' => $redirectUrl, 'msg' => $message]);
    }

    public function configureEmail()
    {
        UserAuthentication::checkLogin();
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_email', 'user_first_name', 'user_last_name']);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        if ($data === false || !empty($data['credential_email'])) {
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $frm = $this->getConfigureEmailForm();
        $this->set('frm', $frm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    private function getConfigureEmailForm()
    {
        $frm = new Form('changeEmailFrm');
        $frm->addHiddenField('', 'user_id', UserAuthentication::getLoggedUserId());
        $newEmail = $frm->addEmailField(Label::getLabel('LBL_NEW_EMAIL'), 'new_email');
        $newEmail->setUnique('tbl_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        $newEmail->requirements()->setRequired();
        $conNewEmail = $frm->addEmailField(Label::getLabel('LBL_CONFIRM_NEW_EMAIL'), 'conf_new_email');
        $conNewEmailReq = $conNewEmail->requirements();
        $conNewEmailReq->setRequired();
        $conNewEmailReq->setCompareWith('new_email', 'eq');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));
        return $frm;
    }

    public function updateEmail()
    {
        $emailFrm = $this->getConfigureEmailForm(false);
        $post = $emailFrm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            $message = current($emailFrm->getValidationErrors());
            FatUtility::dieJsonError($message);
        }
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_email', 'credential_password', 'user_first_name', 'user_last_name']);
        $rs = $srch->getResultSet();
        if (!$rs) {
            $message = Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId);
            FatUtility::dieJsonError($message);
        }
        $data = FatApp::getDb()->fetch($rs);
        if ($data === false || !empty($data['credential_email'])) {
            $message = Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId);
            FatUtility::dieJsonError($message);
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $msg = Label::getLabel('LBL_EMAIL_UPDATE_SUCCESSFULL');
        $redirectUrl = "";
        $emailChangeReqObj = new UserEmailChangeRequest();
        $emailChangeReqObj->deleteOldLinkforUser(UserAuthentication::getLoggedUserId());
        $emailVerification = FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1);
        if (applicationConstants::YES == $emailVerification) {
            $_token = $userObj->prepareUserVerificationCode();
            $postData = [
                'uecreq_user_id' => UserAuthentication::getLoggedUserId(),
                'uecreq_email' => $post['new_email'],
                'uecreq_token' => $_token,
                'uecreq_status' => 0,
                'uecreq_created' => date('Y-m-d H:i:s'),
                'uecreq_updated' => date('Y-m-d H:i:s'),
                'uecreq_expire' => date('Y-m-d H:i:s', strtotime('+ 24 hours', strtotime(date('Y-m-d H:i:s'))))
            ];
            $emailChangeReqObj->assignValues($postData);
            if (!$emailChangeReqObj->save()) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_Unable_to_process_your_requset') . $emailChangeReqObj->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            $userData = [
                'user_email' => $post['new_email'],
                'user_first_name' => $data['user_first_name'],
                'user_last_name' => $data['user_last_name']
            ];
            if (!$this->sendEmailChangeVerificationLink($_token, $userData)) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_Unable_to_process_your_requset') . $emailChangeReqObj->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            $msg = Label::getLabel('MSG_UPDATE_EMAIL_REQUEST_SENT_SUCCESSFULLY._YOU_NEED_TO_VERIFY_YOUR_NEW_EMAIL_ADDRESS_BEFORE_ACCESSING_OTHER_MODULES');
        } else {
            if (!$userObj->changeEmail($post['new_email'])) {
                Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set'));
                FatUtility::dieWithError(Message::getHtml());
            }
        }
        $db->commitTransaction();
        $confAutoLoginRegisteration = FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION', FatUtility::VAR_INT, 1);
        if (applicationConstants::NO == $emailVerification) {
            $authentication = new UserAuthentication();
            if (!$authentication->login($post['new_email'], $data['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                FatUtility::dieWithError(Message::getHtml());
            }
            $redirectUrl = User::getPreferedDashbordRedirectUrl();
        }
        $returnJson = ['msg' => $msg];
        if (!empty($redirectUrl)) {
            $returnJson['redirectUrl'] = $redirectUrl;
        }
        FatUtility::dieJsonSuccess($returnJson);
    }

    private function sendEmailChangeVerificationLink($_token, $data)
    {
        $link = CommonHelper::generateFullUrl('GuestUser', 'verifyEmail', [$_token]);
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $email = new EmailHandler();
        if (true !== $email->sendEmailChangeVerificationLink($this->siteLangId, $data)) {
            return false;
        }
        return true;
    }

    public function loginGoogle($userType = User::USER_TYPE_LEANER)
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/GoogleAPI/vendor/autoload.php'; // include the required calss files for google login
        $client = new Google_Client();
        $client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId)); // Set your applicatio name
        $client->setScopes(['email', 'profile', 'https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.events']); // set scope during user login
        $client->setClientId(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_ID")); // paste the client id which you get from google API Console
        $client->setClientSecret(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_SECRET")); // set the client secret
        $currentPageUri = CommonHelper::generateFullUrl('GuestUser', 'loginGoogle', [$userType], CONF_WEBROOT_FRONT_URL, NULL, false, false);
        $client->setRedirectUri($currentPageUri);
        $client->setAccessType("offline");
        $client->setApprovalPrompt("force");
        $client->setDeveloperKey(FatApp::getConfig("CONF_GOOGLEPLUS_DEVELOPER_KEY")); // Developer key
        $oauth2 = new Google_Service_Oauth2($client); // Call the OAuth2 class for get email address
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']); // Authenticate
            $_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
            FatApp::redirectUser($currentPageUri);
        }
        if (isset($_SESSION['access_token'])) {
            $client->setAccessToken($_SESSION['access_token']);
        }
        if (!$client->getAccessToken()) {
            $authUrl = $client->createAuthUrl();
            FatApp::redirectUser($authUrl);
        }
        $user = $oauth2->userinfo->get();
        $_SESSION['access_token'] = $client->getAccessToken();
        $userGoogleEmail = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
        $userGoogleId = $user['id'];
        $userGoogleName = $user['name'];
        if (!empty($userGoogleEmail)) {
            if ($userType == User::USER_TYPE_TEACHER) {
                $preferredDashboard = User::USER_TEACHER_DASHBOARD;
                $userType = User::USER_TYPE_TEACHER;
            } else {
                $preferredDashboard = User::USER_LEARNER_DASHBOARD;
                $userType = User::USER_TYPE_LEANER;
            }
            $db = FatApp::getDb();
            $userObj = new User();
            $srch = $userObj->getUserSearchObj(['user_id', 'user_preferred_dashboard', 'credential_verified', 'credential_email', 'credential_active']);
            $srch->addCondition('credential_email', '=', $userGoogleEmail);
            $rs = $srch->getResultSet();
            $row = $db->fetch($rs);
            if ($row) {
                if ($row['credential_active'] != applicationConstants::ACTIVE) {
                    Message::addErrorMessage(Label::getLabel("ERR_YOUR_ACCOUNT_HAS_BEEN_DEACTIVATED"));
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $userObj->setMainTableRecordId($row['user_id']);
                $arr = ['user_googleplus_id' => $userGoogleId,];
                if (!$userObj->setUserInfo($arr)) {
                    Message::addErrorMessage(Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA'));
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $row['credential_verified'] = FatUtility::int($row['credential_verified']);
                if ($row['credential_verified'] != applicationConstants::YES) {
                    if (!$userObj->verifyAccount(applicationConstants::YES)) {
                        Message::addErrorMessage(Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA'));
                        FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                    }
                }
                if ($row['user_preferred_dashboard'] == User::USER_TEACHER_DASHBOARD) {
                    $userType = User::USER_TYPE_TEACHER;
                }
            } else {
                $db->startTransaction();
                $userNameArr = explode(" ", $userGoogleName);
                $user_first_name = (!empty($userNameArr[0])) ? $userNameArr[0] : '';
                $user_last_name = (!empty($userNameArr[1])) ? $userNameArr[1] : '';
                $userData = [
                    'user_first_name' => $user_first_name,
                    'user_last_name' => $user_last_name,
                    'user_is_learner' => 1,
                    'user_googleplus_id' => $userGoogleId,
                    'user_preferred_dashboard' => $preferredDashboard,
                    'user_registered_initially_for' => $userType,
                ];
                $userObj->assignValues($userData);
                if (!$userObj->save()) {
                    Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $userObj->getError());
                    $db->rollbackTransaction();
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $username = str_replace(" ", "", $userGoogleName) . $userGoogleId;
                if (!$userObj->setLoginCredentials($username, $userGoogleEmail, uniqid(), 1, 1)) {
                    Message::addErrorMessage(Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET") . $userObj->getError());
                    $db->rollbackTransaction();
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $db->commitTransaction();
                $userId = $userObj->getMainTableRecordId();
                $userObj = new User($userId);
                $userData['user_username'] = $username;
                $userData['user_email'] = $userGoogleEmail;
                if (FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1) && $userGoogleEmail) {
                    $data['user_email'] = $userGoogleEmail;
                    $data['user_first_name'] = $user_first_name;
                    $data['user_last_name'] = $user_last_name;
                    $this->userWelcomeEmailRegistration($userObj, $data);
                }
            }
            $usrStngObj = new UserSetting($userObj->getMainTableRecordId());
            $usrStngObj->saveData(['us_google_access_token' => $client->getRefreshToken()]);
            $userInfo = $userObj->getUserInfo(['user_googleplus_id', 'user_is_teacher', 'credential_username', 'credential_password']);
            if (!$userInfo || ($userInfo && $userInfo['user_googleplus_id'] != $userGoogleId)) {
                Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET"));
                FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
            }
            $authentication = new UserAuthentication();
            if (!$authentication->login($userInfo['credential_username'], $userInfo['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
            }
            unset($_SESSION['access_token']);
            $redirectUrl = CommonHelper::generateUrl('Teachers', 'languages');
            $isUserTeacher = FatUtility::int($userInfo['user_is_teacher']);
            if ($userType == User::USER_TYPE_TEACHER && $isUserTeacher != applicationConstants::YES) {
                $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
            }
            FatApp::redirectUser($redirectUrl);
        }
        Message::addErrorMessage(Label::getLabel("MSG_UNABLE_To_FETCH_YOUR_EMAIL_ID"));
        FatApp::redirectUser(CommonHelper::generateUrl());
    }

    public function forgotPasswordForm()
    {
        $frm = $this->getForgotForm();
        $this->set('frm', $frm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    public function forgotPassword()
    {
        $frm = $this->getForgotForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
        }
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
            if (!CommonHelper::verifyCaptcha()) {
                Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect'));
                FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
            }
        }
        $userAuthObj = new UserAuthentication();
        $row = $userAuthObj->getUserByEmailOrUserName($post['user_email_username'], '', false);
        // echo "<pre>";
        // print_r($row);
        // echo "</pre>";
        // die;
        if (!$row || false === $row) {
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
        }
        if ($row['credential_verified'] != applicationConstants::YES) {
            Message::addErrorMessage(str_replace("{clickhere}", '<a href="javascript:void(0)" onclick="resendEmailVerificationLink(' . "'" . $row['credential_email'] . "'" . ')">' . Label::getLabel('LBL_Click_Here', $this->siteLangId) . '</a>', Label::getLabel('MSG_Your_Account_verification_is_pending_{clickhere}', $this->siteLangId)));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
            return false;
        }
        if ($userAuthObj->checkUserPwdResetRequest($row['user_id'])) {
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
        }
        $token = UserAuthentication::encryptPassword(FatUtility::getRandomString(20));
        $row['token'] = $token;
        $userAuthObj->deleteOldPasswordResetRequest();
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$userAuthObj->addPasswordResetRequest($row)) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
        }
        $row['link'] = CommonHelper::generateFullUrl('GuestUser', 'resetPassword', [$row['user_id'], $token, $row['credential_email']]);
        $email = new EmailHandler();
        if (!$email->sendForgotPasswordLinkEmail($this->siteLangId, $row)) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel("MSG_ERROR_IN_SENDING_PASSWORD_RESET_LINK_EMAIL"));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'forgotPasswordForm'));
        }
        $db->commitTransaction();
        Message::addMessage(Label::getLabel("MSG_YOUR_PASSWORD_RESET_INSTRUCTIONS_TO_YOUR_EMAIL"));
        FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
    }

    public function resetPassword($userId = 0, $token = '', $credential_email = "")
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1 || strlen(trim($token)) < 20) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_RESET_PASSWORD_REQUEST'));
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $userAuthObj = new UserAuthentication();
        if (!$userAuthObj->checkResetLink($userId, trim($token),$credential_email, 'form')) {
            Message::addErrorMessage($userAuthObj->getError());
            FatApp::redirectUser(CommonHelper::generateUrl('GuestUser', 'loginForm'));
        }
        $frm = $this->getResetPwdForm($userId, trim($token),$credential_email);
        $this->set('frm', $frm);
        $this->_template->render();
    }

    private function getForgotForm()
    {
        $siteLangId = $this->siteLangId;
        $frm = new Form('frmPwdForgot');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Email', $siteLangId), 'user_email_username')->requirements()->setRequired();
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
            $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $siteLangId));
        return $frm;
    }

    private function getResetPwdForm($uId, $token,$credential_email)
    {
        $siteLangId = $this->siteLangId;
        $frm = new Form('frmResetPwd');
        $_SESSION['email']=$credential_email;
        $fld_np = $frm->addPasswordField(Label::getLabel('LBL_NEW_PASSWORD', $siteLangId), 'new_pwd');
        $fld_np->requirements()->setRequired();
        $fld_np->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
        $fld_np->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_Valid_password', $siteLangId));
        $fld_cp = $frm->addPasswordField(Label::getLabel('LBL_CONFIRM_NEW_PASSWORD', $siteLangId), 'confirm_pwd');
        $fld_cp->requirements()->setRequired();
        $fld_cp->requirements()->setCompareWith('new_pwd', 'eq', '');
        $frm->addHiddenField('', 'user_id', $uId, ['id' => 'user_id']);
        $frm->addHiddenField('', 'token', $token, ['id' => 'token']);
        $frm->addHiddenField('', 'credential_email', $credential_email, ['name'=>'credential_email','id' => 'credential_email']);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_RESET_PASSWORD', $siteLangId));
        return $frm;
    }

    public function resetPasswordSetup()
    {
        
        $newPwd = FatApp::getPostedData('new_pwd');
        $confirmPwd = FatApp::getPostedData('confirm_pwd');
        $userId = FatApp::getPostedData('user_id', FatUtility::VAR_INT);
        $token = FatApp::getPostedData('token', FatUtility::VAR_STRING);
        $credential_email = FatApp::getPostedData('credential_email', FatUtility::VAR_STRING);
        if ($userId < 1 && strlen(trim($token)) < 20) {;
            Message::addErrorMessage(Label::getLabel('MSG_REQUEST_IS_INVALID_OR_EXPIRED'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $frm = $this->getResetPwdForm($userId, $token,$credential_email);
        $frm->addTextBox(Label::getLabel('LBL_Email', $credential_email), 'keyword', '', ['id' => 'keyword', 'autocomplete' => 'off']);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (true !== CommonHelper::validatePassword($post['new_pwd'])) {
            Message::addErrorMessage(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $userAuthObj = new UserAuthentication();
        if (!$userAuthObj->checkResetLink($userId, trim($token),$credential_email, 'submit')) {
            Message::addErrorMessage($userAuthObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $pwd = UserAuthentication::encryptPassword($newPwd);
        if (!$userAuthObj->resetUserPassword($userId, $pwd)) {
            Message::addErrorMessage($userAuthObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $email = new EmailHandler();
        $userObj = new User($userId);
        $row = $userObj->getUserInfo(['user_first_name', 'user_last_name', 'credential_email'], '', false);
        $email->sendResetPasswordConfirmationEmail($this->siteLangId, $row);
        $this->set('msg', Label::getLabel('MSG_PASSWORD_CHANGED_SUCCESSFULLY'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function resendEmailVerificationLink($username = "")
    {
        if (empty($username)) {
            FatUtility::dieWithError(Label::getLabel('MSG_ERROR_INVALID_REQUEST'));
        }
        $userAuthObj = new UserAuthentication();
        if (!$row = $userAuthObj->getUserByEmailOrUserName($username, false, false)) {
            FatUtility::dieWithError(Label::getLabel($userAuthObj->getError()));
        }
        if ($row['credential_verified'] == 1) {
            FatUtility::dieWithError(Label::getLabel("MSG_You_are_already_verified_please_login."));
        }
        $row['user_email'] = $row['credential_email'];
        $userObj = new User($row['user_id']);
        if (!$this->sendEmailVerificationLink($userObj, $row)) {
            FatUtility::dieWithError(Label::getLabel("MSG_VERIFICATION_EMAIL_COULD_NOT_BE_SENT"));
        }
        $this->set('msg', Label::getLabel('MSG_VERIFICATION_EMAIL_HAS_BEEN_SENT_AGAIN'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function checkAjaxUserLoggedIn()
    {
        $json = [];
        $json['isUserLogged'] = FatUtility::int(UserAuthentication::isUserLogged());
        die(json_encode($json));
    }
    public function checkAjaxAdminLoggedIn()
    {
        $json = [];
        $json['isUserLogged'] = FatUtility::int(AdminAuthentication::getLoggedAdminId());
        die(json_encode($json));
    }

    private function userWelcomeEmailRegistration($userObj, $data)
    {
        $email = new EmailHandler();
        if (!$email->sendWelcomeEmail($this->siteLangId, $data)) {
            Message::addMessage(Label::getLabel("MSG_ERROR_IN_SENDING_WELCOME_EMAIL", $this->siteLangId));
            return false;
        }
        return true;
    }

    public function verifyEmail($_token)
    {
        $emailChangeReqObj = new UserEmailChangeRequest();
        $userRequest = $emailChangeReqObj->checkUserRequest($_token);
        if (empty($userRequest)) {
            Message::addErrorMessage(Label::getLabel("MSG_INVAILD_VERIFICATION_LINK", $this->siteLangId));
            $this->logout();
        }
        $userObj = new User($userRequest['uecreq_user_id']);
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_password']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs, 'user_id');
        if (false == $userRow) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST'));
            $this->logout();
        }
        if (!$userObj->changeEmail($userRequest['uecreq_email'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set') . $userObj->getError());
            $this->logout();
        }
        $userRequest['status'] = 1;
        $emailCheReqObj = new UserEmailChangeRequest($userRequest['uecreq_id']);
        if (!$emailCheReqObj->updateUserRequestStatus()) {
            //Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set'). $userObj->getError());
        }
        Message::addMessage(Label::getLabel('MSG_Email_Updated._Please_Login_again_in_your_profile_with_new_email'));
        $this->logout();
    }

    private static function verifyFacebookUserAccessToken($facebookUserAccessToken, $userFacebookId, &$error = '')
    {
        $facebookUserAccessToken = filter_var($facebookUserAccessToken, FILTER_SANITIZE_STRING);
        $myFacebookAppId = FatApp::getConfig('CONF_FACEBOOK_APP_ID', FatUtility::VAR_STRING, '');
        $facebookAppSecret = FatApp::getConfig('CONF_FACEBOOK_APP_SECRET', FatUtility::VAR_STRING, '');
        $facebook_application = 'REPLACE';
        $curl = curl_init();
        $url = "https://graph.facebook.com/oauth/access_token?client_id=" . $myFacebookAppId . "&client_secret=" . $facebookAppSecret . "&grant_type=client_credentials";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $outputData = curl_exec($curl);
        curl_close($curl);
        $outputData = json_decode($outputData, true);
        $facebook_access_token = $outputData['access_token'];
        $curl = curl_init();
        $url = "https://graph.facebook.com/debug_token?input_token=" . $facebookUserAccessToken . "&access_token=" . $facebook_access_token;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $outputData = curl_exec($curl);
        curl_close($curl);
        $tokenData = json_decode($outputData, true);
        // && $tokenData['data']['application'] == $facebook_application 
        $error = Label::getLabel('LBL_ERROR_TO_VERIFY_FACEBOOK_TOKEN');
        if (!empty($tokenData['data']['error']) || $tokenData['data']['is_valid'] == false || empty($tokenData['data']['user_id'] || empty($tokenData['data']['app_id']))) {
            $error = (!empty($tokenData['data']['error']['message'])) ? $tokenData['data']['error']['message'] : $error;
            return false;
        }
        if ($myFacebookAppId == $tokenData['data']['app_id'] && $userFacebookId == $tokenData['data']['user_id'] && $tokenData['data']['is_valid'] == true) {
            return true;
        }
        return false;
    }
}
