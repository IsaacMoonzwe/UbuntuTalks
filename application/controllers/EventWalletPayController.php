<?php

class EventWalletPayController extends EventLoggedUserController
{

    public function charge($orderId = '')
    {
        $isAjaxCall = FatUtility::isAjaxCall();
        if (!$orderId || ((isset($_SESSION['shopping_cart']["order_id"]) && $orderId != $_SESSION['shopping_cart']["order_id"]))) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $srch = new OrderSearch();
        $srch->joinOrderProduct();
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addCondition('order_user_id', '=', $userId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
        $srch->addMultipleFields(['order_id', 'order_user_id', 'order_net_amount', 'order_wallet_amount_charge']);
        $rs = $srch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($rs);
        if (!$orderInfo) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        $orderObj = new Order();
        $orderPaymentFinancials = $orderObj->getOrderPaymentFinancials($orderId);
        if ($orderPaymentFinancials["order_credits_charge"] > 0) {
            $orderPaymentObj = new OrderPayment($orderId);
            $orderPaymentObj->chargeUserWallet($orderPaymentFinancials["order_credits_charge"]);
        }
        $cartObj = new EventCart();
        $cartObj->clear();
        $fromKids=$_SESSION['fromKids'];
        if($fromKids==0){
        $cartObj->updateEventUserCart();
        }
       
        // $learnerId = UserAuthentication::getLoggedUserId();
        // $userData=User::getAttributesById($userId);
        // $srch = new SearchBase('tbl_scheduled_lessons');
        // $rs = $srch->getResultSet();
        // $langData = FatApp::getDb()->fetchAll($rs);
        // $lastRecord = end($langData);
        // $userNotification = new UserNotifications($teacherId);
        // $user_first = $userData['user_first_name'];
        // $user_last = $userData['user_last_name'];
        // $userNotification->createLessonNotification($lastRecord['slesson_id'], $teacherId,$user_first.$user_last, USER::USER_TYPE_TEACHER, 'Testing');
    
        if ($isAjaxCall) {
            $this->set('redirectUrl', CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
            $this->set('msg', Label::getLabel("MSG_Payment_from_wallet_made_successfully", $this->siteLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
        FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
    }

    public function recharge($orderId)
    {
        if ($orderId == '' || ((isset($_SESSION['wallet_recharge_cart']) && $orderId != $_SESSION['wallet_recharge_cart']["order_id"]))) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        $srch = Order::getSearchObject();
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addCondition('order_user_id', '=', $loggedUserId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
        $srch->addCondition('order_type', '=', Order::TYPE_WALLET_RECHARGE);
        $rs = $srch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($rs);
        if (!$orderInfo) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $this->set('orderInfo', $orderInfo);
        $userObj = new EventUser($loggedUserId);
        $userDetails = $userObj->getUserInfo();
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmSrch->addMultipleFields(['pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'pmethod_code', 'pmethod_description']);
        $paymentMethods = FatApp::getDb()->fetchAll($pmSrch->getResultSet());
        $this->set('userDetails', $userDetails);
        $this->set('orderId', $orderId);
        $this->set('paymentMethods', $paymentMethods);
        $this->_template->render(true, true);
    }

}
