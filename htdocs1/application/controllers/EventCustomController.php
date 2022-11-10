<?php

use function GuzzleHttp\json_encode;

class CustomController extends MyAppController
{

    public function paymentFailed()
    {
        $textMessage = Label::getLabel('MSG_learner_failure_order_{contact-us-page-url}');
        $contactUsPageUrl = CommonHelper::generateUrl('contact');
        $textMessage = str_replace('{contact-us-page-url}', $contactUsPageUrl, $textMessage);
        $this->set('textMessage', $textMessage);
        $this->_template->render();
    }

    public function paymentSuccess($orderId = null)
    {

        $textMessage = stripslashes(Label::getLabel('MSG_learner_success_order_{dashboard-url}_{contact-us-page-url}'));
        $fromKids=$_SESSION['fromKids'];
        $teacherId=$_SESSION['teacherId'];
        $userTutorID = UserAuthentication::getLoggedUserId();
        $user_Data=User::getAttributesById($userTutorID);
        $srch_lesson = new SearchBase('tbl_scheduled_lessons');
        $rs_lesson = $srch_lesson->getResultSet();
        $langData = FatApp::getDb()->fetchAll($rs_lesson);
        $lastRecord = end($langData);
        $user_Notification = new UserNotifications($teacherId);
        $user_first = $user_Data['user_first_name'];
        $user_last = $user_Data['user_last_name'];
        $userFullName=$user_first." ". $user_last;
        $user_Notification->createLessonNotification($lastRecord['slesson_id'], $teacherId,$userFullName, USER::USER_TYPE_TEACHER, 'Testing');
 
        $arrReplace = [
            '{dashboard-url}' => CommonHelper::generateUrl('Learner', '', [], CONF_WEBROOT_DASHBOARD),
            '{contact-us-page-url}' => CommonHelper::generateUrl('Contact'),
        ];
        $textMessage = str_replace(array_keys($arrReplace), $arrReplace, $textMessage);
        if ($orderId) {
            $orderObj = new Order();
            $order = $orderObj->getOrderById($orderId);
            if (isset($order['order_type'])) {
                $this->set('orderType', $order['order_type']);
                if ($order['order_type'] == Order::TYPE_WALLET_RECHARGE) {
                    $this->set('heading', Label::getLabel('HEADING_Money_added_to_wallet'));
                    $textMessage = Label::getLabel('MSG_Money_added_to_wallet');
                }
            }
            $orderObj = $orderObj->getLessonsByOrderId($orderId);
            $orderObj->addFld(['slesson_grpcls_id', 'op_qty','sldetail_id','slesson_status']);
            $orderObj->doNotCalculateRecords(true);
            $orderObj->doNotLimitRecords(true);
            $lessonInfo = FatApp::getDb()->fetch($orderObj->getResultSet());
        
            $this->set('lessonInfo', $lessonInfo);
        }
        $this->set('setMonthAndWeekName', true);
        $this->set('textMessage', $textMessage);
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->render();
    }

    public function updateUserCookies()
    {
        if (UserAuthentication::isUserLogged()) {
            $UserCookieConsent = new UserCookieConsent(UserAuthentication::getLoggedUserId());
            $UserCookieConsent->saveOrUpdateSetting([], false);
        }
        CommonHelper::setCookieConsent();
        return true;
    }

    public function paymentCancel()
    {
        FatApp::redirectUser(CommonHelper::generateFullUrl('Custom', 'paymentFailed'));
    }

    public function cookieForm()
    {
        $cookieForm = $this->getCookieForm();
        if (UserAuthentication::isUserLogged()) {
            $userCookieConsent = new UserCookieConsent(UserAuthentication::getLoggedUserId());
            $cookieSetting = $userCookieConsent->getCookieSettings();
            $cookieSetting = \json_decode($cookieSetting, true);
            $cookieForm->fill($cookieSetting);
        }
        $this->set('cookieForm', $cookieForm);
        $this->_template->render(false, false);
    }

    protected function getCookieForm()
    {
        $form = new Form('cookieForm');
        $checkboxValue = applicationConstants::YES;
        $form->addCheckBox(Label::getLabel('LBL_Necessary'), UserCookieConsent::COOKIE_NECESSARY_FIELD, $checkboxValue, [], true, 1);
        $form->addCheckBox(Label::getLabel('LBL_Preferences'), UserCookieConsent::COOKIE_PREFERENCES_FIELD, $checkboxValue, [], true, 0);
        $form->addCheckBox(Label::getLabel('LBL_Statistics'), UserCookieConsent::COOKIE_STATISTICS_FIELD, $checkboxValue, [], true, 0);
        $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        return $form;
    }

    public function saveCookieSetting()
    {
        $cookieForm = $this->getCookieForm();
        $data = $cookieForm->getFormDataFromArray(FatApp::getPostedData());
        if ($data == false) {
            Message::addErrorMessage(current($cookieForm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        unset($data['btn_submit']);
        unset($data['necessary']);
        if (UserAuthentication::isUserLogged()) {
            $userCookieConsent = new UserCookieConsent(UserAuthentication::getLoggedUserId());
            $userCookieConsent->saveOrUpdateSetting($data, false);
        }
        $data = \json_encode($data);
        CommonHelper::setCookieConsent($data);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Cookie_settings_update_successfully'));
    }

    public function trialBookedSuccess($orderId = null)
    {
        $textMessage = Label::getLabel('MSG_learner_success_trial_{dashboard-url}_{contact-us-page-url}');
        $arrReplace = [
            '{dashboard-url}' => CommonHelper::generateUrl('learner', '', [], CONF_WEBROOT_DASHBOARD),
            '{contact-us-page-url}' => CommonHelper::generateUrl('contact'),
        ];
        $textMessage = str_replace(array_keys($arrReplace), $arrReplace, $textMessage);
        if ($orderId) {
            $orderObj = new Order();
            $order = $orderObj->getOrderById($orderId);
            if (isset($order['order_type'])) {
                $this->set('orderType', $order['order_type']);
            }
            $orderObj = $orderObj->getLessonsByOrderId($orderId);
            $orderObj->addFld('slesson_grpcls_id');
            $orderObj->doNotCalculateRecords(true);
            $orderObj->doNotLimitRecords(true);
            $lessonInfo = FatApp::getDb()->fetch($orderObj->getResultSet());
            $this->set('lessonInfo', $lessonInfo);
        }
        $this->set('textMessage', $textMessage);
        $this->set('heading', Label::getLabel('MSG_Success'));
        $this->_template->render(true, true, 'custom/payment-success.php');
    }

    public function sitemap()
    {
        $this->_template->render();
    }

}
