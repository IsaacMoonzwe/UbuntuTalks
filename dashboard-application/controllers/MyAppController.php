<?php

class MyAppController extends FatController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->action = $action;
        CommonHelper::initCommonVariables();
        $this->initCommonVariables();
        if (
                FatApp::getConfig("CONF_MAINTENANCE", FatUtility::VAR_INT, 0) && ($this->_controllerName != "MaintenanceController") && ($this->_controllerName != 'Home' && $action != 'setSiteDefaultLang' && $this->_controllerName != 'Custom' && $action != 'updateUserCookies' && $action != 'cookieForm' && $action != 'saveCookieSetting')
        ) {
            UserAuthentication::logout();
            FatUtility::isAjaxCall() && FatUtility::dieWithError(Label::getLabel('MSG_Maintenance_Mode_Text'));
            FatApp::redirectUser(CommonHelper::generateUrl('maintenance', '', [], CONF_WEBROOT_FRONTEND));
        }
    }

    public function pwaManifest()
    {
        $pwaSettings = FatApp::getConfig('CONF_PWA_SETTINGS');
        $pwaManifest = [];
        if (!empty($pwaSettings)) {
            $pwaManifest = json_decode(FatApp::getConfig('CONF_PWA_SETTINGS'), true);
        }
        $pwaManifest['icons'] = [
            [
                "src" => CommonHelper::generateUrl('Image', 'pwaIcon', ['144'], CONF_WEBROOT_FRONTEND),
                "sizes" => "144x144",
                "type" => "image/png"
            ],
            [
                "src" => CommonHelper::generateUrl('Image', 'pwaSplashIcon', ['512']),
                "sizes" => "512x512",
                "type" => "image/png"
            ]
        ];
        unset($pwaManifest['offline_page']);
        die(stripslashes(json_encode($pwaManifest)));
    }

    public function test()
    {
        echo 1;
        CommonHelper::printArray($_GET);
        CommonHelper::printArray(apache_request_headers());
    }

    public function initCommonVariables()
    {
        $this->siteLangId = CommonHelper::getLangId();
        $this->siteCurrencyId = CommonHelper::getCurrencyId();
        /* [ */
        $arr = explode('-', FatUtility::camel2dashed($this->_controllerName));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $controllerName = ucfirst(FatUtility::dashed2Camel($urlController));
        /* ] */
        $cookieConsent = CommonHelper::getCookieConsent();
        $this->cookieConsent = $cookieConsent;
        $jsVariables = [
            'confirmUnLockPrice' => Label::getLabel('LBL_Are_you_sure_to_unlock_this_price!'),
            'confirmRemove' => Label::getLabel('LBL_Do_you_want_to_remove'),
            'confirmCancel' => Label::getLabel('LBL_Do_you_want_to_cancel'),
            'languageUpdateAlert' => Label::getLabel('LBL_On_Submit_Price_Needs_To_Be_Set'),
            'layoutDirection' => CommonHelper::getLayoutDirection(),
            'processing' => Label::getLabel('LBL_Processing...'),
            'requestProcessing' => Label::getLabel('LBL_Request_Processing...'),
            'isMandatory' => Label::getLabel('LBL_is_mandatory'),
            'pleaseEnterValidEmailId' => Label::getLabel('LBL_Please_enter_valid_email_ID_for'),
            'charactersSupportedFor' => Label::getLabel('VLBL_Only_characters_are_supported_for'),
            'pleaseEnterIntegerValue' => Label::getLabel('VLBL_Please_enter_integer_value_for'),
            'pleaseEnterNumericValue' => Label::getLabel('VLBL_Please_enter_numeric_value_for'),
            'startWithLetterOnlyAlphanumeric' => Label::getLabel('VLBL_startWithLetterOnlyAlphanumeric'),
            'mustBeBetweenCharacters' => Label::getLabel('VLBL_Length_Must_be_between_6_to_20_characters'),
            'invalidValues' => Label::getLabel('VLBL_Length_Invalid_value_for'),
            'shouldNotBeSameAs' => Label::getLabel('VLBL_should_not_be_same_as'),
            'mustBeSameAs' => Label::getLabel('VLBL_must_be_same_as'),
            'mustBeGreaterOrEqual' => Label::getLabel('VLBL_must_be_greater_than_or_equal_to'),
            'mustBeGreaterThan' => Label::getLabel('VLBL_must_be_greater_than'),
            'mustBeLessOrEqual' => Label::getLabel('VLBL_must_be_less_than_or_equal_to'),
            'mustBeLessThan' => Label::getLabel('VLBL_must_be_less_than'),
            'lengthOf' => Label::getLabel('VLBL_Length_of'),
            'valueOf' => Label::getLabel('VLBL_Value_of'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between'),
            'and' => Label::getLabel('VLBL_and'),
            'Quit' => Label::getLabel('LBL_Quit'),
            'Reschedule' => Label::getLabel('LBL_Reschedule'),
            'chargelearner' => Label::getLabel('LBL_Charge_Learner'),
            'bookedSlotAlert' => Label::getLabel('VLBL_You_have_already_booked_this_slot._Do_you_want_to_continue?'),
            'endLessonAlert' => Label::getLabel('VLBL_Are_you_sure_to_end_this_Lesson?'),
            'Proceed' => Label::getLabel('LBL_Proceed'),
            'Confirm' => Label::getLabel('LBL_Confirm'),
            'pleaseSelect' => Label::getLabel('VLBL_Please_select'),
            'confirmCancelessonText' => Label::getLabel('LBL_Are_you_sure_want_to_cancel_this_lesson'),
            'deleteImageCnfMsg' => Label::getLabel('LBL_Delete_Image_Confirm_Message'),
            'teacherProfileIncompleteMsg' => Label::getLabel('LBL_Please_Complete_Profile_to_be_visible_on_teachers_listing_page'),
            'requriedRescheduleMesssage' => Label::getLabel('Lbl_Reschedule_Reason_Is_Requried'),
            'completeProfile' => Label::getLabel('Lbl_Your_profile_is_completed'),
            'incompleteProfile' => Label::getLabel('Lbl_Complete_Your_profile'),
            'language' => Label::getLabel('Lbl_Language'),
            'myTimeZoneLabel' => Label::getLabel('Lbl_My_Current_Time'),
            'timezoneString' => Label::getLabel('LBL_TIMEZONE_STRING'),
            'lessonMints' => Label::getLabel('LBL_%s_Mins/Lesson'),
            'confirmDeleteLessonPlanText' => Label::getLabel('LBL_DELETE_LESSON_PLAN_CONFIRM_TEXT'),
            'gdprDeleteAccDesc' => Label::getLabel('LBL_Gdpr_Delete_Account_Request_Description'),
            'disableSlot' => Label::getLabel('LBL_Do_you_want_to_disable_the_slot'),
            'enableSlot' => Label::getLabel('LBL_Do_you_want_to_enable_the_slot_again'),
            'today' => Label::getLabel('LBL_Today'),
            'prev' => Label::getLabel('LBL_Prev'),
            'next' => Label::getLabel('LBL_Next'),
            'done' => Label::getLabel('Done'),
            'fileSize' => Label::getLabel('LBL_YOU_{name}_FILE_SIZE_IS_MORE_THEN_{size}_MB')
        ];
        $languages = Language::getAllNames(false);
        foreach ($languages as $val) {
            $jsVariables['language' . $val['language_id']] = $val['language_layout_direction'];
        }
        if (CommonHelper::getLayoutDirection() == 'rtl') {
            $this->_template->addCss(['css/common-rtl.css', 'css/dashboard-rtl.css']);
        } else {
            $this->_template->addCss(['css/common-ltr.css', 'css/dashboard-ltr.css']);
        }
        $currencyData = Currency::getCurrencyAssoc($this->siteLangId);
        $this->set('cookieConsent', $cookieConsent);
        $this->set('websiteLangues', $languages);
        $this->set('currencySymbolLeft', CommonHelper::getCurrencySymbolLeft());
        $this->set('currencySymbolRight', CommonHelper::getCurrencySymbolRight());
        $this->set('siteLangId', $this->siteLangId);
        $this->set('siteCurrencyId', $this->siteCurrencyId);
        $this->set('currencyData', $currencyData);
        $this->set('jsVariables', $jsVariables);
        $this->set('controllerName', $controllerName);
        $this->set('action', $this->_actionName);
        $this->set('canonicalUrl', Common::getCanonicalUrl());
    }

    protected function getChangeEmailForm($passwordField = true)
    {
        $frm = new Form('changeEmailFrm');
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['credential_email']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs);
        $user_email = $userRow['credential_email'];
        $frm->addHiddenField('', 'user_id', UserAuthentication::getLoggedUserId());
        $curEmail = $frm->addEmailField(Label::getLabel('LBL_CURRENT_EMAIL'), 'user_email', $user_email);
        $curEmail->requirements()->setRequired();
        $curEmail->addFieldTagAttribute('readonly', 'true');
        $newEmail = $frm->addEmailField(Label::getLabel('LBL_NEW_EMAIL'), 'new_email');
        $newEmail->setUnique('tbl_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        $newEmail->requirements()->setRequired();
        if ($passwordField) {
            $curPwd = $frm->addPasswordField(Label::getLabel('LBL_CURRENT_PASSWORD'), 'current_password');
            $curPwd->requirements()->setRequired();
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    protected function getTeacherQualificationForm($isCertiRequried = false)
    {
        $frm = new Form('frmQualification');
        $fld = $frm->addHiddenField('', 'uqualification_id', 0);
        $fld->requirements()->setInt();
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Experience_Type'), 'uqualification_experience_type', UserQualification::getExperienceTypeArr(), '', [], Label::getLabel('LBL_Select'));
        $fld->requirements()->setRequired();
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Title'), 'uqualification_title', '', ['placeholder' => Label::getLabel('LBL_Eg:_B.A._English')]);
        $fld->requirements()->setLength(1, 100);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Institution'), 'uqualification_institute_name', '', ['placeholder' => Label::getLabel('LBL_Eg:_Oxford_University')]);
        $fld->requirements()->setLength(1, 100);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Location'), 'uqualification_institute_address', '', ['placeholder' => Label::getLabel('LBL_Eg:_London')]);
        $fld->requirements()->setLength(1, 100);
        $fld = $frm->addTextArea(Label::getLabel('LBL_Description'), 'uqualification_description', '', ['placeholder' => Label::getLabel('LBL_Eg._Focus_in_Humanist_Literature')]);
        $fld->requirements()->setLength(1, 500);
        $yearArr = range(date('Y'), 1970);
        $fld1 = $frm->addSelectBox(Label::getLabel('LBL_Start_Year'), 'uqualification_start_year', array_combine($yearArr, $yearArr), '', [], '');
        $fld1->requirements()->setRequired();
        $fld2 = $frm->addSelectBox(Label::getLabel('LBL_End_Year'), 'uqualification_end_year', array_combine($yearArr, $yearArr), '', [], '');
        $fld2->requirements()->setRequired();
        $fld2->requirements()->setCompareWith('uqualification_start_year', 'ge');
        $fld = $frm->addFileUpload(Label::getLabel('LBL_Upload_Certificate'), 'certificate');
        $fld->htmlAfterField = "<small>" . Label::getLabel('LBL_NOTE:_Allowed_Certificate_Extentions!') . "</small>";
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $frm;
    }

    /* public function fatActionCatchAll($action)
      {
      $this->_template->render(false, false, 'error-pages/404.php');
      } */

    public function includeDateTimeFiles()
    {
        $this->_template->addCss(['css/jquery-ui-timepicker-addon.css'], false);
        $this->_template->addJs(['js/jquery-ui-timepicker-addon.js'], false);
    }

    public function setUpNewsLetter()
    {
        $post = FatApp::getPostedData();
        $frm = Common::getNewsLetterForm(CommonHelper::getLangId());
        $post = $frm->getFormDataFromArray($post);
        if ($post === false) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatUtility::dieWithError(Message::getHtml());
        }
        $siteLangId = CommonHelper::getLangId();
        $api_key = FatApp::getConfig("CONF_MAILCHIMP_KEY");
        $list_id = FatApp::getConfig("CONF_MAILCHIMP_LIST_ID");
        if ($api_key == '' || $list_id == '') {
            Message::addErrorMessage(Label::getLabel("LBL_Newsletter_is_not_configured_yet,_Please_contact_admin", $siteLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        require_once(CONF_INSTALLATION_PATH . 'library/Mailchimp.php');
        $MailchimpObj = new Mailchimp($api_key);
        $Mailchimp_ListsObj = new Mailchimp_Lists($MailchimpObj);
        try {
            $subscriber = $Mailchimp_ListsObj->subscribe($list_id, ['email' => htmlentities($post['email'])]);
            if (empty($subscriber['leid'])) {
                Message::addErrorMessage(Label::getLabel('MSG_Newsletter_subscription_valid_email', $siteLangId));
                FatUtility::dieWithError(Message::getHtml());
            }
        } catch (Mailchimp_Error $e) {
            Message::addErrorMessage(Label::getLabel('MSG_Error_while_subscribing_to_newsletter', $siteLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Successfully_subscribed', $siteLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

}
