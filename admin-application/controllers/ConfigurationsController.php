<?php

class ConfigurationsController extends AdminBaseController
{
    /* these variables must be only those which will store array type data and will saved as serialized array [ */

    private $serializeArrayValues = [];

    /* ] */

    public function __construct($action)
    {
        parent::__construct($action);
        $this->set("includeEditor", true);
        $this->objPrivilege->canViewGeneralSettings();
    }

    public function index()
    {
        $tabs = Configurations::getTabsArr();
        $this->set('activeTab', Configurations::FORM_GENERAL);
        $this->set('tabs', $tabs);
        $this->_template->render();
    }

    public function form($frmType)
    {
        $frmType = FatUtility::int($frmType);
        $dispLangTab = false;
        if (in_array($frmType, Configurations::getLangTypeFormArr())) {
            $dispLangTab = true;
            $this->set('languages', Language::getAllNames());
        }
        $record = Configurations::getConfigurations();
        if (($frmType == Configurations::FORM_OPTIONS)) {
            $record['CONF_PAID_LESSON_DURATION'] = explode(',', $record['CONF_PAID_LESSON_DURATION']);
            $record['CONF_GROUP_CLASS_DURATION'] = explode(',', $record['CONF_GROUP_CLASS_DURATION']);
        }
        $frm = $this->getForm($frmType);
        $frm->fill($record);
        if (($frmType == Configurations::FORM_THIRD_PARTY_API) && CommonHelper::demoUrl()) {
            CommonHelper::maskAndDisableFormFields($frm, ['CONF_ACTIVE_MEETING_TOOL']);
        }
        $this->set('frm', $frm);
        $this->set('canEdit', $this->objPrivilege->canEditGeneralSettings(AdminAuthentication::getLoggedAdminId(), true));
        $this->set('frmType', $frmType);
        $this->set('dispLangTab', $dispLangTab);
        $this->set('lang_id', 0);
        $this->set('formLayout', '');
        $this->_template->render(false, false);
    }

    public function langForm($frmType, $langId, $tabId = null)
    {
        $frmType = FatUtility::int($frmType);
        $langId = FatUtility::int($langId);
        $frm = $this->getLangForm($frmType, $langId);
        $dispLangTab = false;
        if (in_array($frmType, Configurations::getLangTypeFormArr())) {
            $dispLangTab = true;
            $this->set('languages', Language::getAllNames());
        }
        $record = Configurations::getConfigurations();
        $frm->fill($record);
        if ($tabId) {
            $this->set('tabId', $tabId);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('frm', $frm);
        $this->set('canEdit', $this->objPrivilege->canEditGeneralSettings(AdminAuthentication::getLoggedAdminId(), true));
        $this->set('dispLangTab', $dispLangTab);
        $this->set('lang_id', $langId);
        $this->set('frmType', $frmType);
        $this->set('formLayout', Language::getLayoutDirection($langId));
        $this->_template->render(false, false, 'configurations/form.php');
    }

    public function setup()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        $frmType = FatUtility::int($post['form_type']);
        if (1 > $frmType) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        $frm = $this->getForm($frmType);
        $post = $frm->getFormDataFromArray($post);
        if (($frmType == Configurations::FORM_THIRD_PARTY_API) && CommonHelper::demoUrl()) {
            $post = ['CONF_ACTIVE_MEETING_TOOL' => FatApp::getPostedData('CONF_ACTIVE_MEETING_TOOL')];
        }
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        unset($post['form_type']);
        unset($post['btn_submit']);
        foreach ($this->serializeArrayValues as $val) {
            if (array_key_exists($val, $post)) {
                if (is_array($post[$val])) {
                    $post[$val] = serialize($post[$val]);
                }
            } else {
                if (isset($post[$val])) {
                    $post[$val] = 0;
                }
            }
        }
        $msg = '';
        $record = new Configurations();
        if (
            isset($post["CONF_SEND_SMTP_EMAIL"]) &&
            $post["CONF_SEND_EMAIL"] &&
            $post["CONF_SEND_SMTP_EMAIL"] &&
            (
                ($post["CONF_SEND_SMTP_EMAIL"] != FatApp::getConfig("CONF_SEND_SMTP_EMAIL")) ||
                ($post["CONF_SMTP_HOST"] != FatApp::getConfig("CONF_SMTP_HOST")) ||
                ($post["CONF_SMTP_PORT"] != FatApp::getConfig("CONF_SMTP_PORT")) ||
                ($post["CONF_SMTP_USERNAME"] != FatApp::getConfig("CONF_SMTP_USERNAME")) ||
                ($post["CONF_SMTP_SECURE"] != FatApp::getConfig("CONF_SMTP_SECURE")) ||
                ($post["CONF_SMTP_PASSWORD"] != FatApp::getConfig("CONF_SMTP_PASSWORD")))
        ) {
            $smtp_arr = [
                "host" => $post["CONF_SMTP_HOST"],
                "port" => $post["CONF_SMTP_PORT"],
                "username" => $post["CONF_SMTP_USERNAME"],
                "password" => $post["CONF_SMTP_PASSWORD"],
                "secure" => $post["CONF_SMTP_SECURE"]
            ];
            if (EmailHandler::sendSmtpTestEmail($this->adminLangId, $smtp_arr)) {
                $msg = Label::getLabel('LBL_We_have_sent_a_test_email_to_administrator_account' . FatApp::getConfig("CONF_SITE_OWNER_EMAIL"), $this->adminLangId);
            } else {
                FatUtility::dieJsonError(Label::getLabel("LBL_SMTP_settings_provided_is_invalid_or_unable_to_send_email_so_we_have_not_saved_SMTP_settings", $this->adminLangId));
            }
        }
        if (isset($post['CONF_USE_SSL']) && $post['CONF_USE_SSL'] == 1) {
            if (!$this->is_ssl_enabled()) {
                if ($post['CONF_USE_SSL'] != FatApp::getConfig('CONF_USE_SSL')) {
                    FatUtility::dieJsonError(Label::getLabel('MSG_SSL_NOT_INSTALLED_FOR_WEBSITE_Try_to_Save_data_without_Enabling_ssl', $this->adminLangId));
                }
                unset($post['CONF_USE_SSL']);
            }
        }
        if (array_key_exists('CONF_TIMEZONE', $post)) {
            unset($post['CONF_TIMEZONE']);
        }
        if (array_key_exists('CONF_PAID_LESSON_DURATION', $post)) {
            if (!in_array($post['CONF_DEFAULT_PAID_LESSON_DURATION'], $post['CONF_PAID_LESSON_DURATION'])) {
                FatUtility::dieJsonError(Label::getLabel('MSG_Please_select_default_duration_from_selected_durations', $this->adminLangId));
            }
        }
        $unselectedSlot = [];
        if (array_key_exists('CONF_PAID_LESSON_DURATION', $post)) {
            $bookingSlots = applicationConstants::getBookingSlots();
            $unselectedSlot = array_diff($bookingSlots, $post['CONF_PAID_LESSON_DURATION']);
            $post['CONF_PAID_LESSON_DURATION'] = implode(',', $post['CONF_PAID_LESSON_DURATION']);
        }

        if (array_key_exists('CONF_GROUP_CLASS_DURATION', $post)) {
            $post['CONF_GROUP_CLASS_DURATION'] = implode(',', $post['CONF_GROUP_CLASS_DURATION']);
        }
        if (!$record->update($post)) {
            FatUtility::dieJsonError($record->getError());
        }
        if (!empty($unselectedSlot)) {
            $teachLangPrice = new TeachLangPrice();
            $teachLangPrice->deleteTeachSlots($unselectedSlot);
            $teacherStat = new TeacherStat(0);
            $teacherStat->setTeachLangPricesBulk();
        }
        $this->set('msg', $msg ?: Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('frmType', $frmType);
        $this->set('langId', 0);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function is_ssl_enabled()
    {
        // url connection
        $url = "https://" . $_SERVER["HTTP_HOST"];
        // Initiate connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6"); // set browser/user agent
        // Set cURL and other options
        curl_setopt($ch, CURLOPT_URL, $url); // set url
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // allow https verification if true
        curl_setopt($ch, CURLOPT_NOBODY, true);
        // grab URL and pass it to the browser
        $res = curl_exec($ch);
        if (!$res) {
            return false;
        }
        return true;
    }

    public function setupLang()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        $frmType = FatUtility::int($post['form_type']);
        $langId = FatUtility::int($post['lang_id']);
        if (1 > $frmType || 1 > $langId) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $frm = $this->getLangForm($frmType, $langId);
        $post = $frm->getFormDataFromArray($post);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        unset($post['form_type']);
        unset($post['lang_id']);
        unset($post['btn_submit']);
        $record = new Configurations();
        if (!$record->update($post)) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('frmType', $frmType);
        $this->set('langId', $langId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function uploadMedia()
    {
        $this->objPrivilege->canEditGeneralSettings();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
        }
        $file_type = FatApp::getPostedData('file_type', FatUtility::VAR_INT, 0);
        $lang_id = FatApp::getPostedData('lang_id', FatUtility::VAR_INT, 0);
        if (!$file_type) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        if (CommonHelper::demoUrl()) {
            $logoTypes = [
                AttachedFile::FILETYPE_FRONT_LOGO,
                AttachedFile::FILETYPE_FRONT_WHITE_LOGO,
                AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO,
                AttachedFile::FILETYPE_MOBILE_LOGO
            ];
            if (in_array($file_type, $logoTypes)) {
                FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO', $this->adminLangId));
            }
        }
        $allowedFileTypeArr = [
            AttachedFile::FILETYPE_ADMIN_LOGO,
            AttachedFile::FILETYPE_FRONT_LOGO,
            AttachedFile::FILETYPE_FRONT_WHITE_LOGO,
            AttachedFile::FILETYPE_EMAIL_LOGO,
            AttachedFile::FILETYPE_FAVICON,
            AttachedFile::FILETYPE_SOCIAL_FEED_IMAGE,
            AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO,
            AttachedFile::FILETYPE_WATERMARK_IMAGE,
            AttachedFile::FILETYPE_APPLE_TOUCH_ICON,
            AttachedFile::FILETYPE_MOBILE_LOGO,
            AttachedFile::FILETYPE_BLOG_PAGE_IMAGE,
            AttachedFile::FILETYPE_MEDICAL_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_FAITH_GROUPS_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EDUCATION_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_BUSINESS_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_REFERRAL_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EVENT_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EVENT_SECOND_SLIDER_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EVENT_THIRD_SLIDER_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EVENT_FOURTH_SLIDER_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_EVENT_FIFTH_SLIDER_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_VIRTUAL_SESSION_CAMPAIGN_IMAGE,
            AttachedFile::FILETYPE_KIDS_PAGE_IMAGE,
            // AttachedFile::FILETYPE_SERVICE_PAGE_IMAGE,
            AttachedFile::FILETYPE_QUOTE_PAGE_IMAGE,
            AttachedFile::FILETYPE_CONTACT_PAGE_IMAGE,
            AttachedFile::FILETYPE_LESSON_PAGE_IMAGE,
            AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE,
            AttachedFile::FILETYPE_APPLY_TO_TEACH_BANNER
        ];
        if (!in_array($file_type, $allowedFileTypeArr)) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
        }
        $fileHandlerObj = new AttachedFile();
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $file_type, 0, 0, $_FILES['file']['name'], -1, $unique_record = true, $lang_id)) {
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $this->set('file', $_FILES['file']['name']);
        $this->set('frmType', Configurations::FORM_GENERAL);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function redirect()
    {
        require_once(CONF_INSTALLATION_PATH . 'library/third-party/analytics/AnalyticsAPI.php');
        $analyticArr = [
            'clientId' => FatApp::getConfig("CONF_ANALYTICS_CLIENT_ID"),
            'clientSecretKey' => FatApp::getConfig("CONF_ANALYTICS_SECRET_KEY"),
            'redirectUri' => CommonHelper::generateFullUrl('configurations', 'redirect', [], '', false),
            'googleAnalyticsID' => FatApp::getConfig("CONF_ANALYTICS_ID")
        ];

        try {
            $analytics = new AnalyticsAPI($analyticArr);
            $obj = FatApplication::getInstance();
            $get = $obj->getQueryStringVar();
        } catch (exception $e) {
            Message::addErrorMessage($e->getMessage());
        }
        if (isset($get['code']) && isset($get['code']) != '') {
            $code = $get['code'];
            $auth = $analytics->getAccessToken($code);
            if ($auth['refreshToken'] != '') {
                $arr = ['CONF_ANALYTICS_ACCESS_TOKEN' => $auth['refreshToken']];
                $record = new Configurations();
                if (!$record->update($arr)) {
                    Message::addErrorMessage($record->getError());
                } else {
                    Message::addMessage(Label::getLabel('MSG_Setting_Updated_Successfully', $this->adminLangId));
                }
            } else {
                Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access_Token', $this->adminLangId));
            }
        } else {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->adminLangId));
        }
        FatApp::redirectUser(CommonHelper::generateUrl('configurations', 'index'));
    }

    public function removeSiteAdminLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_ADMIN_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeDesktopLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        if (CommonHelper::demoUrl()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO', $this->adminLangId));
        }
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_FRONT_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeDesktopWhiteLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        if (CommonHelper::demoUrl()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO', $this->adminLangId));
        }
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_FRONT_WHITE_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeEmailLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EMAIL_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeFavicon($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_FAVICON, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeSocialFeedImage($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_SOCIAL_FEED_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removePaymentPageLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        if (CommonHelper::demoUrl()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO', $this->adminLangId));
        }
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeWatermarkImage($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_WATERMARK_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeAppleTouchIcon($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_APPLE_TOUCH_ICON, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeMobileLogo($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        if (CommonHelper::demoUrl()) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOU_CANNOT_CHANGE_LOGO_ON_DEMO', $this->adminLangId));
        }
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_MOBILE_LOGO, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeBlogImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_BLOG_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeBusinessCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_BUSINESS_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeMedicalCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_MEDICAL_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeFaithGroupsCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_FAITH_GROUPS_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeEducationCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EDUCATION_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeEventCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeEventSecondSliderCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_SECOND_SLIDER_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeEventThirdSliderCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_THIRD_SLIDER_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeEventFourthSliderCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_FOURTH_SLIDER_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeEventFifthSliderCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_FIFTH_SLIDER_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeVirtualSessionCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_VIRTUAL_SESSION_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeReferralCampaignImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_REFERRAL_CAMPAIGN_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeKidsImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_KIDS_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    // public function removeServiceImage($lang_id = 0)
    // {
    //     $lang_id = FatUtility::int($lang_id);
    //     $fileHandlerObj = new AttachedFile();
    //     if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_SERVICE_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
    //         Message::addErrorMessage($fileHandlerObj->getError());
    //         FatUtility::dieJsonError(Message::getHtml());
    //     }
    //     $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
    //     $this->_template->render(false, false, 'json-success.php');
    // }

    public function removeQuoteImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_QUOTE_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeContactImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_CONTACT_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeContactsImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_CONTACTS_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeLessonImage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_LESSON_PAGE_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeApplyToTeachBannerPage($lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_APPLY_TO_TEACH_BANNER, 0, 0, 0, $lang_id)) {
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }


    public function removeAllowedPaymentGatewayImage(int $langId = 0)
    {
        $langId = FatUtility::int($langId);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE, 0, 0, 0, $langId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeCollectionBgImage($lang_id = 0)
    {
        $this->objPrivilege->canEditGeneralSettings();
        $lang_id = FatUtility::int($lang_id);
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_CATEGORY_COLLECTION_BG_IMAGE, 0, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($type, $arrValues = [])
    {
        $frm = new Form('frmConfiguration');
        switch ($type) {
            case Configurations::FORM_GENERAL:
                $frm->addEmailField(Label::getLabel('LBL_Store_Owner_Email', $this->adminLangId), 'CONF_SITE_OWNER_EMAIL');
                $frm->addTextBox(Label::getLabel('LBL_Telephone', $this->adminLangId), 'CONF_SITE_PHONE');
                $frm->addTextBox(Label::getLabel('LBL_Fax', $this->adminLangId), 'CONF_SITE_FAX');
                $cpagesArr = ContentPage::getPagesForSelectBox($this->adminLangId);
                $frm->addSelectBox(Label::getLabel('LBL_About_Us', $this->adminLangId), 'CONF_ABOUT_US_PAGE', $cpagesArr);
                $frm->addSelectBox(Label::getLabel('LBL_Privacy_Policy_Page', $this->adminLangId), 'CONF_PRIVACY_POLICY_PAGE', $cpagesArr);
                $frm->addSelectBox(Label::getLabel('LBL_Terms_and_Conditions_Page', $this->adminLangId), 'CONF_TERMS_AND_CONDITIONS_PAGE', $cpagesArr);
                $frm->addSelectBox(Label::getLabel('LBL_Cookies_Policies_Page', $this->adminLangId), 'CONF_COOKIES_BUTTON_LINK', $cpagesArr);
                $fld1 = $frm->addCheckBox(Label::getLabel('LBL_Cookies_Policies', $this->adminLangId), 'CONF_ENABLE_COOKIES', 1, [], false, 0);
                $fld1->htmlAfterField = "<br><small>" . Label::getLabel("LBL_cookies_policies_section_will_be_shown_on_frontend", $this->adminLangId) . "</small>";
                break;
            case Configurations::FORM_LOCAL:
                $frm->addSelectBox(Label::getLabel('LBL_Default_Site_Laguage', $this->adminLangId), 'CONF_DEFAULT_SITE_LANG', Language::getAllNames(), false, [], '');
                $frm->addSelectBox(Label::getLabel('LBL_Timezone', $this->adminLangId), 'CONF_ADMIN_TIMEZONE', MyDate::timeZoneListing(), false, [], '');
                $countryObj = new Country();
                $countriesArr = $countryObj->getCountriesArr($this->adminLangId);
                $frm->addSelectBox(Label::getLabel('LBL_Country', $this->adminLangId), 'CONF_COUNTRY', $countriesArr);
                $currencyArr = Currency::getCurrencyNameWithCode($this->adminLangId);
                $frm->addSelectBox(Label::getLabel('LBL_Default_Site_Currency', $this->adminLangId), 'CONF_CURRENCY', $currencyArr, false, [], '');
                break;
            case Configurations::FORM_REVIEWS:
                $frm->addHtml('', 'Reviews', '<h3>' . Label::getLabel("LBL_Reviews", $this->adminLangId) . '</h3>');
                $reviewStatusArr = TeacherLessonReview::getReviewStatusArr($this->adminLangId);
                $fld = $frm->addSelectBox(Label::getLabel("LBL_Default_Review_Status", $this->adminLangId), 'CONF_DEFAULT_REVIEW_STATUS', $reviewStatusArr, false, [], '');
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_the_default_review_order_status_when_a_new_review_is_placed", $this->adminLangId) . "</small>";
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Allow_Reviews", $this->adminLangId), 'CONF_ALLOW_REVIEWS', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_New_Review_Alert_Email", $this->adminLangId), 'CONF_REVIEW_ALERT_EMAIL', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                break;
            case Configurations::FORM_SEO:
                $fld = $frm->addCheckBox(Label::getLabel('LBL_ENABLE_LANGUAGE_CODE_TO_SITE_URLS', $this->adminLangId), 'CONF_LANG_SPECIFIC_URL', 1, array(), false, 0);
                $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_LANGUAGE_CODE_TO_SITE_URLS_EXAMPLES", $this->adminLangId) . '</small>';
                $fld2 = $frm->addTextarea(Label::getLabel('LBL_Site_Tracker_Code', $this->adminLangId), 'CONF_SITE_TRACKER_CODE');
                $fld2->htmlAfterField = '<small>' . Label::getLabel("LBL_This_is_the_site_tracker_script,_used_to_track_and_analyze_data_about_how_people_are_getting_to_your_website._e.g.,_Google_Analytics.", $this->adminLangId) . ' http://www.google.com/analytics/</small>';
                break;
            case Configurations::FORM_OPTIONS:
                $frm->addHtml('', 'Admin', '<h3>' . Label::getLabel('LBL_Admin', $this->adminLangId) . '</h3>');
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_Default_Items_Per_Page", $this->adminLangId), "CONF_ADMIN_PAGESIZE");
                $fld3->requirements()->setRange(1, 500);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_number_of_records_shown_per_page_(Users,_orders,_etc)", $this->adminLangId) . ".</small>";
                $frm->addHtml('', 'FlashCard', '<h3>' . Label::getLabel('LBL_FlashCards', $this->adminLangId) . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_FLASHCARD", $this->adminLangId), 'CONF_ENABLE_FLASHCARD', 1, [], false, 0);

                $frm->addHtml('', 'NEWSLETTER_SUBSCRIPTION', '<h3>' . Label::getLabel('LBL_NEWSLETTER_SUBSCRIPTION', $this->adminLangId) . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_NEWSLETTER_SUBSCRIPTION", $this->adminLangId), 'CONF_ENABLE_NEWSLETTER_SUBSCRIPTION', 1, [], false, 0);
                $frm->addHtml('', 'Free_Trial', '<h3>' . Label::getLabel('LBL_Free_Trial', $this->adminLangId) . '</h3>');
                $frm->addCheckBox(Label::getLabel("CONF_ENABLE_FREE_TRIAL", $this->adminLangId), 'CONF_ENABLE_FREE_TRIAL', applicationConstants::YES, [], false, applicationConstants::NO);
                $frm->addHtml('', 'report_issue', '<h3>' . Label::getLabel('LBL_Report/Esclate_Issue_Time', $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION", $this->adminLangId), "CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION");
                $fld->requirements()->setIntPositive();
                $fld->requirements()->setRange(0, 168);
                $fld = $frm->addTextBox(Label::getLabel("CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION", $this->adminLangId), "CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION");
                $fld->requirements()->setIntPositive();
                $fld->requirements()->setRange(0, 168);
                $frm->addHtml('', 'Grpcls', '<h3>' . Label::getLabel('LBL_Group_Class', $this->adminLangId) . '</h3>');
                $fld3 = $frm->addTextBox(Label::getLabel("LBL_Class_Cancellation_Refund_PERCENTAGE", $this->adminLangId), "CONF_LEARNER_CLASS_REFUND_PERCENTAGE");
                $fld3->requirements()->setIntPositive();
                $fld3->requirements()->setRange(0, 100);
                $fld3 = $frm->addTextBox(Label::getLabel("LBL_Class_Booking_Time_Span(Minutes)", $this->adminLangId), "CONF_CLASS_BOOKING_GAP");
                $fld3->requirements()->setIntPositive();
                $fld3->requirements()->setRange(0, 1000);
                $frm->addIntegerField(Label::getLabel("LBL_Class_Max_learners", $this->adminLangId), "CONF_GROUP_CLASS_MAX_LEARNERS");
                $groupClassSlots = applicationConstants::getGroupClassSlots();
                $fld = $frm->addCheckBoxes(Label::getLabel("LBL_Lesson_Duration_For_Group_Classes", $this->adminLangId), "CONF_GROUP_CLASS_DURATION", $groupClassSlots, [], ['class' => 'list-inline']);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_GROUP_CLASS_DURATIONS_TEXT", $this->adminLangId) . ".</small>";
                $frm->addHtml('', 'Admin', '<h3>' . Label::getLabel('LBL_Teacher_Dashboard', $this->adminLangId) . '</h3>');
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_Default_Items_Per_Page", $this->adminLangId), "CONF_FRONTEND_PAGESIZE");
                $fld3->requirements()->setRange(1, 500);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_number_of_records_shown_per_page_(Lessons,_orders,_etc)", $this->adminLangId) . ".</small>";
                $bookingSlots = applicationConstants::getBookingSlots();
                $fld = $frm->addCheckBoxes(Label::getLabel("LBL_Lesson_durations", $this->adminLangId), "CONF_PAID_LESSON_DURATION", $bookingSlots, array(), array('class' => 'list-inline'));
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_LESSON_DURATIONS_TEXT", $this->adminLangId) . ".</small>";
                $frm->addRadioButtons(Label::getLabel("LBL_Default_Lesson_duration", $this->adminLangId), "CONF_DEFAULT_PAID_LESSON_DURATION", $bookingSlots, '', array('class' => 'list-inline'))->requirements()->setRequired();
                $frm->addRadioButtons(
                    Label::getLabel("LBL_Trial_Lesson_duration", $this->adminLangId),
                    "CONF_TRIAL_LESSON_DURATION",
                    $bookingSlots,
                    '',
                    array('class' => 'list-inline')
                )->requirements()->setRequired();
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_END_LESSON_DURATION", $this->adminLangId), "CONF_ALLOW_TEACHER_END_LESSON");
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Duration_After_Teacher_Can_End_Lesson_(In_Minutes)", $this->adminLangId) . ".</small>";
                $fld3 = $frm->addIntegerField(Label::getLabel("LBL_LEARNER_REFUND_PERCENTAGE", $this->adminLangId), "CONF_LEARNER_REFUND_PERCENTAGE");
                $fld3->requirements()->setRange(0, 100);
                $fld3->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Refund_to_learner_In_Less_than_24_Hours_(In_Percentage)", $this->adminLangId) . ".</small>";
                $maxAttemptFld = $frm->addIntegerField(Label::getLabel("LBL_MAX_TEACHER_REQUEST_ATTEMPT", $this->adminLangId), "CONF_MAX_TEACHER_REQUEST_ATTEMPT");
                $maxAttemptFld->requirements()->setRange(0, 10);
                $frm->addHtml('', 'Account', '<h3>' . Label::getLabel("LBL_Account", $this->adminLangId) . '</h3>');
                $fld5 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Admin_Approval_After_Registration_(Sign_Up)", $this->adminLangId), 'CONF_ADMIN_APPROVAL_REGISTRATION', 1, [], false, 0);
                $fld5->htmlAfterField = '<br><small>' . Label::getLabel("LBL_On_enabling_this_feature,_admin_need_to_approve_each_user_after_registration_(User_cannot_login_until_admin_approves)", $this->adminLangId) . '</small>';
                $fld7 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Email_Verification_After_Registration", $this->adminLangId), 'CONF_EMAIL_VERIFICATION_REGISTRATION', 1, [], false, 0);
                $fld7->htmlAfterField = "<br><small>" . Label::getLabel("LBL_user_need_to_verify_their_email_address_provided_during_registration", $this->adminLangId) . " </small>";
                $fld9 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Auto_Login_After_Registration", $this->adminLangId), 'CONF_AUTO_LOGIN_REGISTRATION', 1, [], false, 0);
                $fld9->htmlAfterField = "<br><small>" . Label::getLabel("LBL_On_enabling_this_feature,_users_will_be_automatically_logged-in_after_registration", $this->adminLangId) . "</small>";
                $fld10 = $frm->addCheckBox(Label::getLabel("LBL_Activate_Sending_Welcome_Mail_After_Registration", $this->adminLangId), 'CONF_WELCOME_EMAIL_REGISTRATION', 1, [], false, 0);
                $fld10->htmlAfterField = "<br><small>" . Label::getLabel("LBL_On_enabling_this_feature,_users_will_receive_a_welcome_mail_after_registration.", $this->adminLangId) . "</small>";
                $frm->addHtml('', 'Withdrawal', '<h3>' . Label::getLabel("LBL_Withdrawal", $this->adminLangId) . '</h3>');
                $fld = $frm->addIntegerField(Label::getLabel("LBL_Minimum_Withdrawal_Amount", $this->adminLangId) . ' [' . $this->siteDefaultCurrencyCode . ']', 'CONF_MIN_WITHDRAW_LIMIT', '');
                $fld->htmlAfterField = "<small> " . Label::getLabel("LBL_This_is_the_minimum_withdrawable_amount.", $this->adminLangId) . "</small>";
                $fld->requirements()->setRange(1, 999999);
                $fld = $frm->addIntegerField(Label::getLabel("LBL_Minimum_Interval_[Days]", $this->adminLangId), 'CONF_MIN_INTERVAL_WITHDRAW_REQUESTS', '');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_minimum_interval_in_days_between_two_withdrawal_requests.", $this->adminLangId) . "</small>";
                $fld->requirements()->setRange(0, 999999);
                $frm->addHtml('', 'Checkout', '<h3>' . Label::getLabel("LBL_Checkout", $this->adminLangId) . '</h3>');
                $fld1 = $frm->addCheckBox(Label::getLabel('LBL_Activate_Live_Payment_Transaction_Mode', $this->adminLangId), 'CONF_TRANSACTION_MODE', 1, [], false, 0);
                $fld1->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Set_Transaction_Mode_To_Live_Environment", $this->adminLangId) . "</small>";
                break;
            case Configurations::FORM_EMAIL:
                $frm->addEmailField(Label::getLabel("LBL_From_Email", $this->adminLangId), 'CONF_FROM_EMAIL');
                $frm->addEmailField(Label::getLabel("LBL_Reply_to_Email_Address", $this->adminLangId), 'CONF_REPLY_TO_EMAIL');
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Send_Email", $this->adminLangId), 'CONF_SEND_EMAIL', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                if (FatApp::getConfig('CONF_SEND_EMAIL', FatUtility::VAR_INT, 1)) {
                    $fld->htmlAfterField = '<a href="javascript:void(0)" id="testMail-js">' . Label::getLabel("LBL_Click_Here", $this->adminLangId) . '</a> to test email. ' . Label::getLabel("LBL_This_will_send_Test_Email_to_Site_Owner_Email", $this->adminLangId) . ' - ' . FatApp::getConfig("CONF_SITE_OWNER_EMAIL");
                }
                $frm->addEmailField(Label::getLabel("LBL_Contact_Email_Address", $this->adminLangId), 'CONF_CONTACT_EMAIL');
                $frm->addRadioButtons(Label::getLabel("LBL_Send_SMTP_Email", $this->adminLangId), 'CONF_SEND_SMTP_EMAIL', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Host", $this->adminLangId), 'CONF_SMTP_HOST');
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Port", $this->adminLangId), 'CONF_SMTP_PORT');
                $fld = $frm->addTextBox(Label::getLabel("LBL_SMTP_Username", $this->adminLangId), 'CONF_SMTP_USERNAME');
                $fld = $frm->addPasswordField(Label::getLabel("LBL_SMTP_Password", $this->adminLangId), 'CONF_SMTP_PASSWORD');
                $frm->addRadioButtons(Label::getLabel("LBL_SMTP_Secure", $this->adminLangId), 'CONF_SMTP_SECURE', applicationConstants::getSmtpSecureArr($this->adminLangId), '', ['class' => 'list-inline']);
                $fld = $frm->addTextarea(Label::getLabel("LBL_Additional_Alert_E-Mails", $this->adminLangId), 'CONF_ADDITIONAL_ALERT_EMAILS');
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Any_additional_emails_you_want_to_receive_the_alert_email", $this->adminLangId) . "</small>";
                break;
            case Configurations::FORM_LIVE_CHAT:
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Activate_Live_Chat", $this->adminLangId), 'CONF_ENABLE_LIVECHAT', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                $fld->htmlAfterField = "<br><small>" . Label::getLabel("LBL_Activate_3rd_Party_Live_Chat.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextarea(Label::getLabel("LBL_Live_Chat_Code", $this->adminLangId), 'CONF_LIVE_CHAT_CODE');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_live_chat_script/code_provided_by_the_3rd_party_API_for_integration.", $this->adminLangId) . "</small>";
                break;
            case Configurations::FORM_THIRD_PARTY_API:
                $fld = $frm->addTextBox(Label::getLabel("LBL_Facebook_APP_ID", $this->adminLangId), 'CONF_FACEBOOK_APP_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_ID_used_in_login_and_post.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Facebook_App_Secret", $this->adminLangId), 'CONF_FACEBOOK_APP_SECRET');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Facebook_secret_key_used_for_authentication_and_other_Facebook_related_plugins_support.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Google_Plus_Developer_Key", $this->adminLangId), 'CONF_GOOGLEPLUS_DEVELOPER_KEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_google_plus_developer_key.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Google_Plus_Client_ID", $this->adminLangId), 'CONF_GOOGLEPLUS_CLIENT_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Client_Id_used_to_Login.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Google_Plus_Client_Secret", $this->adminLangId), 'CONF_GOOGLEPLUS_CLIENT_SECRET');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Google_Plus_id_client_secret_key_used_for_authentication.", $this->adminLangId) . "</small>";
                $activeMeetingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
                $frm->addHtml('', 'Admin', '<h3>' . Label::getLabel('LBL_Meeting_TOOL', $this->adminLangId) . '</h3>');
                $toolFld = $frm->addRadioButtons(Label::getLabel("LBL_Deliver_Lesson_By", $this->adminLangId), "CONF_ACTIVE_MEETING_TOOL", ApplicationConstants::getMettingTools(), $activeMeetingTool, ['class' => 'list-inline list-inline--onehalf']);
                $frm->addHtml('', 'zoom_api_key', '<h3>' . Label::getLabel("LBL_Zoom_API_Keys", $this->adminLangId) . '</h3>');
                $frm->addTextBox(Label::getLabel("LBL_Zoom_Api_Key", $this->adminLangId), 'CONF_ZOOM_API_KEY');
                $frm->addTextBox(Label::getLabel("LBL_Zoom_Api_Secret", $this->adminLangId), 'CONF_ZOOM_API_SECRET');
                $frm->addTextBox(Label::getLabel("LBL_Zoom_JWT_Token", $this->adminLangId), 'CONF_ZOOM_JWT_TOKEN');
                $frm->addHtml('', 'comet_chat_api_keys', '<h3>' . Label::getLabel("LBL_Comet_chat_Api_Key", $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Comet_Chat_Api_Key", $this->adminLangId), 'CONF_COMET_CHAT_API_KEY');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Comet_Chat_App_ID", $this->adminLangId), 'CONF_COMET_CHAT_APP_ID');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Comet_Chat_Auth", $this->adminLangId), 'CONF_COMET_CHAT_AUTH');
                $frm->addHtml('', 'lessonspace_api_key', '<h3>' . Label::getLabel("LBL_Lessonspace_API_Key", $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Lessonspace_Api_Key", $this->adminLangId), 'CONF_LESSONSPACE_API_KEY');
                $frm->addHtml('', 'Newsletter', '<h3>' . Label::getLabel("LBL_NEWSLETTER_SUBSCRIPTION", $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Mailchimp_Key", $this->adminLangId), 'CONF_MAILCHIMP_KEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Mailchimp's_application_key_used_in_subscribe_and_send_newsletters.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Mailchimp_List_ID", $this->adminLangId), 'CONF_MAILCHIMP_LIST_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Mailchimp's_subscribers_List_ID.", $this->adminLangId) . "</small>";
                $frm->addHtml('', 'Analytics', '<h3>' . Label::getLabel("LBL_Google_Analytics", $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Client_Id", $this->adminLangId), 'CONF_ANALYTICS_CLIENT_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Client_Id_used_in_Analytics_dashboard.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Secret_Key", $this->adminLangId), 'CONF_ANALYTICS_SECRET_KEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_secret_key_used_in_Analytics_dashboard.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Analytics_Id", $this->adminLangId), 'CONF_ANALYTICS_ID');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_Google_Analytics_ID._Ex._UA-xxxxxxx-xx.", $this->adminLangId) . "</small>";
                $accessToken = FatApp::getConfig("CONF_ANALYTICS_ACCESS_TOKEN", FatUtility::VAR_STRING, '');
                require_once(CONF_INSTALLATION_PATH . 'library/third-party/analytics/AnalyticsAPI.php');
                $analyticArr = [
                    'clientId' => FatApp::getConfig("CONF_ANALYTICS_CLIENT_ID", FatUtility::VAR_STRING, ''),
                    'clientSecretKey' => FatApp::getConfig("CONF_ANALYTICS_SECRET_KEY", FatUtility::VAR_STRING, ''),
                    'redirectUri' => CommonHelper::generateFullUrl('configurations', 'redirect', [], '', false),
                    'googleAnalyticsID' => FatApp::getConfig("CONF_ANALYTICS_ID", FatUtility::VAR_STRING, '')
                ];
                try {
                    $analytics = new AnalyticsAPI($analyticArr);
                    $authUrl = $analytics->buildAuthUrl();
                } catch (exception $e) {
                    $authUrl = '';
                }
                if ($authUrl) {
                    $authenticateText = ($accessToken == '') ? 'Authenticate' : 'Re-Authenticate';
                    $fld = $frm->addHTML('', 'accessToken', 'Please save your settings & <a href="' . $authUrl . '" >click here</a> to ' . $authenticateText . ' settings.', '', 'class="medium"');
                } else {
                    $fld = $frm->addHTML('', 'accessToken', 'Please configure your settings and then authenticate them', '', 'class="medium"');
                }
                $frm->addHtml('', 'Analytics', '<h3>' . Label::getLabel("LBL_Google_Recaptcha", $this->adminLangId) . '</h3>');
                $fld = $frm->addTextBox(Label::getLabel("LBL_Site_Key", $this->adminLangId), 'CONF_RECAPTCHA_SITEKEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Site_key_used_for_Google_Recaptcha.", $this->adminLangId) . "</small>";
                $fld = $frm->addTextBox(Label::getLabel("LBL_Secret_Key", $this->adminLangId), 'CONF_RECAPTCHA_SECRETKEY');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_This_is_the_application_Secret_key_used_for_Google_Recaptcha.", $this->adminLangId) . "</small>";
                break;
            case Configurations::FORM_SERVER:
                $fld = $frm->addRadioButtons(Label::getLabel("LBL_Use_SSL", $this->adminLangId), 'CONF_USE_SSL', applicationConstants::getYesNoArr($this->adminLangId), '', ['class' => 'list-inline']);
                $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_NOTE:_To_use_SSL,_check_with_your_host", $this->adminLangId) . '.</small>';
                $fld = $frm->addSelectBox(Label::getLabel("LBL_Enable_Maintenance_Mode", $this->adminLangId), 'CONF_MAINTENANCE', applicationConstants::getYesNoArr($this->adminLangId), '', [], '');
                $fld->htmlAfterField = '<small>' . Label::getLabel("LBL_NOTE:_Enable_Maintenance_Mode_Text", $this->adminLangId) . '.</small>';
                break;
            case Configurations::FORM_SECURITY_SETTINGS:
                $fld = $frm->addIntegerField(Label::getLabel("LBL_REMEMBER_ME_DAYS_FOR_ADMIN", $this->adminLangId), 'CONF_ADMIN_REMEMBER_ME_DAYS');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_ADMIN_REMEMBER_ME_DAYS", $this->adminLangId) . "</small>";
                $fld->requirements()->setRange(1, 365);
                $fld->requirements()->setRequired();
                $fld = $frm->addIntegerField(Label::getLabel("LBL_REMEMBER_ME__DAYS_FOR_USER", $this->adminLangId), 'CONF_USER_REMEMBER_ME_DAYS');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_USER_REMEMBER_ME_DAYS", $this->adminLangId) . "</small>";
                $fld->requirements()->setRange(1, 365);
                $fld->requirements()->setRequired();
                $fld = $frm->addSelectBox(Label::getLabel("LBL_REMEMBER_ME_SECURITY_FOR_ADMIN", $this->adminLangId), 'CONF_ADMIN_REMEMBER_ME_IP_ENABLE', Configurations::getSecuritySettings(), '', [], '');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_ADMIN_REMEMBER_ME_IP_ENABLE", $this->adminLangId) . "</small>";
                $fld->requirements()->setRequired();
                $fld = $frm->addSelectBox(Label::getLabel("LBL_REMEMBER_ME_SECURITY_FOR_USER", $this->adminLangId), 'CONF_USER_REMEMBER_ME_IP_ENABLE', Configurations::getSecuritySettings(), '', [], '');
                $fld->htmlAfterField = "<small> " . Label::getLabel("htmlAfterField_USER_REMEMBER_ME_IP_ENABLE", $this->adminLangId) . "</small>";
                $fld->requirements()->setRequired();
                break;
            case Configurations::FORM_VIDEO_SLIDE:
                $fld = $frm->addRequiredField(Label::getLabel("LBL_Home_Page_Youtube_slide_link", $this->adminLangId), 'CONF_YOUTUBE_VIDEO_SLIDE');
                $fld->htmlAfterField = "<small>" . Label::getLabel("LBL_https://www.youtube.com/watch?v=Ee0Qh_nIoHw.", $this->adminLangId) . "</small>";

                break;
        }
        $frm->addHiddenField('', 'form_type', $type);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel("LBL_Save_Changes", $this->adminLangId));
        return $frm;
    }

    private function getLangForm($type, $langId)
    {
        $frm = new Form('frmConfiguration');
        switch ($type) {
            case Configurations::FORM_GENERAL:
                $frm->addTextBox(Label::getLabel("LBL_Site_Name", $this->adminLangId), 'CONF_WEBSITE_NAME_' . $langId);
                $frm->addTextBox(Label::getLabel("LBL_Site_Owner", $this->adminLangId), 'CONF_SITE_OWNER_' . $langId);
                $fld = $frm->addTextarea(Label::getLabel("LBL_ADDRESS", $this->adminLangId), 'CONF_ADDRESS_' . $langId);
                $fld->requirements()->setLength(20, 130);
                $fld = $frm->addTextarea(Label::getLabel("LBL_ADDITIONAL_ADDRESS", $this->adminLangId), 'CONF_ADDITIONAL_ADDRESS_' . $langId);
                $fld->requirements()->setLength(20, 130);
                $fld = $frm->addTextarea(Label::getLabel('LBL_Cookies_Policies_Text', $this->adminLangId), 'CONF_COOKIES_TEXT_' . $langId);
                $fld->requirements()->setLength(50, 200);
                break;
            case Configurations::FORM_EMAIL:
                $frm->addTextBox(Label::getLabel("LBL_From_Name", $this->adminLangId), 'CONF_FROM_NAME_' . $langId);
                break;
            case Configurations::FORM_MEDIA:
                $frm->addButton('Admin Logo', 'admin_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'admin_logo', 'data-file_type' => AttachedFile::FILETYPE_ADMIN_LOGO]);
                $frm->addButton('Desktop White Logo', 'front_white_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'front_white_logo', 'data-file_type' => AttachedFile::FILETYPE_FRONT_WHITE_LOGO]);
                $frm->addButton('Desktop Logo', 'front_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'front_logo', 'data-file_type' => AttachedFile::FILETYPE_FRONT_LOGO]);
                $frm->addButton('Email Template Logo', 'email_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'email_logo', 'data-file_type' => AttachedFile::FILETYPE_EMAIL_LOGO]);
                $frm->addButton('Website Favicon', 'favicon', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'favicon', 'data-file_type' => AttachedFile::FILETYPE_FAVICON]);
                $frm->addButton('Social Media Logo', 'social_feed_image', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'social_feed_image', 'data-file_type' => AttachedFile::FILETYPE_SOCIAL_FEED_IMAGE]);
                $frm->addButton('Payment Page Logo', 'payment_page_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'payment_page_logo', 'data-file_type' => AttachedFile::FILETYPE_PAYMENT_PAGE_LOGO]);
                $frm->addButton('Apple Touch Icon', 'apple_touch_icon', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'apple_touch_icon', 'data-file_type' => AttachedFile::FILETYPE_APPLE_TOUCH_ICON]);
                $frm->addButton('Mobile Logo', 'mobile_logo', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'mobile_logo', 'data-file_type' => AttachedFile::FILETYPE_MOBILE_LOGO]);
                $frm->addButton('Blog Image', 'blog_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'blog_img', 'data-file_type' => AttachedFile::FILETYPE_BLOG_PAGE_IMAGE]);
                $frm->addButton('Medical Image', 'medical_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'medical_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_MEDICAL_CAMPAIGN_IMAGE]);
                $frm->addButton('Faith Groups Image', 'faith_groups_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'faith_groups_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_FAITH_GROUPS_CAMPAIGN_IMAGE]);
                $frm->addButton('Education Image', 'education_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'education_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EDUCATION_CAMPAIGN_IMAGE]);
                $frm->addButton('Business Image', 'business_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'business_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_BUSINESS_CAMPAIGN_IMAGE]);
                $frm->addButton('Referral Image', 'referral_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'referral_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_REFERRAL_CAMPAIGN_IMAGE]);
                $frm->addButton('Event First Slider Image', 'event_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EVENT_CAMPAIGN_IMAGE]);
                $frm->addButton('Event Second Slider Image', 'event_second_slider_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_second_slider_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EVENT_SECOND_SLIDER_CAMPAIGN_IMAGE]);
                $frm->addButton('Event Third Slider Image', 'event_third_slider_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_third_slider_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EVENT_THIRD_SLIDER_CAMPAIGN_IMAGE]);
                $frm->addButton('Event Fourth Slider Image', 'event_fourth_slider_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_fourth_slider_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EVENT_FOURTH_SLIDER_CAMPAIGN_IMAGE]);

                $frm->addButton('Event Fifth Slider Image', 'event_fifth_slider_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_fifth_slider_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_EVENT_FIFTH_SLIDER_CAMPAIGN_IMAGE]);

                $frm->addButton('Virtual Session Image', 'event_virtual_session_campaign_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'event_virtual_session_campaign_img', 'data-file_type' => AttachedFile::FILETYPE_VIRTUAL_SESSION_CAMPAIGN_IMAGE]);

                $frm->addButton('Kids Image', 'kids_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'kids_img', 'data-file_type' => AttachedFile::FILETYPE_KIDS_PAGE_IMAGE]);
                // $frm->addButton('Service Image', 'service_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'service_img', 'data-file_type' => AttachedFile::FILETYPE_SERVICE_PAGE_IMAGE]);
                $frm->addButton('Quote Image', 'quote_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'quote_img', 'data-file_type' => AttachedFile::FILETYPE_QUOTE_PAGE_IMAGE]);

                $frm->addButton('Contact Image', 'contact_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'contact_img', 'data-file_type' => AttachedFile::FILETYPE_CONTACT_PAGE_IMAGE]);
                $frm->addButton('Lesson Image', 'lesson_img', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'lesson_img', 'data-file_type' => AttachedFile::FILETYPE_LESSON_PAGE_IMAGE]);
                $frm->addButton('Apply To Teach Banner Image', 'apply_to_teach_banner', 'Upload file', ['class' => 'logoFiles-Js', 'id' => 'lesson_img', 'data-file_type' => AttachedFile::FILETYPE_APPLY_TO_TEACH_BANNER]);
                $frm->addButton(label::getLabel('LBL_ALLOWED_PAYMENT_GATEWAY_IMAGE'), 'allowed_payment_gateways_img', label::getLabel('LBL_UPLOAD_FILE'), ['class' => 'logoFiles-Js', 'id' => 'allowed_payment_gateways_img', 'data-file_type' => AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE]);
                break;
            case Configurations::FORM_SERVER:
                $fld = $frm->addHtmlEditor(Label::getLabel('LBL_Maintenance_Text', $this->adminLangId), 'CONF_MAINTENANCE_TEXT_' . $langId);
                $fld->requirements()->setRequired(true);
                break;
        }
        $frm->addHiddenField('', 'lang_id', $langId);
        $frm->addHiddenField('', 'form_type', $type);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel("LBL_Save_Changes", $this->adminLangId));
        return $frm;
    }

    public function testEmail()
    {
        try {
            if (EmailHandler::sendMailTpl(FatApp::getConfig('CONF_SITE_OWNER_EMAIL'), 'test_email', $this->adminLangId)) {
                FatUtility::dieJsonSuccess("Mail sent to - " . FatApp::getConfig('CONF_SITE_OWNER_EMAIL'));
            }
        } catch (Exception $e) {
            FatUtility::dieJsonError($e->getMessage());
        }
    }
}
