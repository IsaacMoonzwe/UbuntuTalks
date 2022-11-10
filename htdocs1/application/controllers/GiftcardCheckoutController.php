<?php

class GiftcardCheckoutController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->user_id = UserAuthentication::getLoggedUserId();
    }

    public function remove()
    {
        $giftcardKey = FatApp::getPostedData('key');
        if ($this->removeGiftcardFromCart($giftcardKey, $this->user_id) === true) {
            $this->set('msg', Label::getLabel("MSG_Cart_Giftcard_Removed_Successfuly", $this->siteLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }

    public function index()
    {
        $giftcardObj = new Giftcard();
        $orderId = $giftcardObj->saveOrder();
        if (false === $orderId) {
            FatApp::redirectUser(FatUtility::generateUrl('Giftcard', '', [], CONF_WEBROOT_DASHBOARD));
        }
        $loggedUserId = UserAuthentication::getLoggedUserId();
        $orderObj = new Order();
        $srch = Order::getSearchObject();
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addCondition('order_user_id', '=', $loggedUserId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
        $srch->addCondition('order_type', '=', Order::TYPE_GIFTCARD);
        $rs = $srch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($rs);
        if (!$orderInfo) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $this->set('orderInfo', $orderInfo);
        $userObj = new User($loggedUserId);
        $userDetails = $userObj->getUserInfo();
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmSrch->addMultipleFields(['pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'pmethod_code', 'pmethod_description']);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $this->set('userDetails', $userDetails);
        $this->set('orderId', $orderId);
        $this->set('paymentMethods', $paymentMethods);
        $this->_template->render(true, true);
    }

    public function removeGiftcardFromCart($giftcardKey, $userId)
    {
        $db = FatApp::getDb();
        $srch = new SearchBase(Cart::DB_TBL);
        $srch->addCondition('usercart_user_id', '=', $userId);
        $srch->addCondition('usercart_type', '=', Cart::TYPE_GIFTCARD);
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if (!empty($row)) {
            $usercartDetails = json_decode($row['usercart_details'], true);
            if (empty($usercartDetails)) {
                $db->deleteRecords(Cart::DB_TBL, ['smt' => '`usercart_user_id`=? and usercart_type=?', 'vals' => [$userId, Cart::TYPE_GIFTCARD]]);
                return true;
                exit;
            }
            foreach ($usercartDetails as $key => $value) {
                if ($value['giftcard_price'] == $giftcardKey) {
                    unset($usercartDetails[$key]);
                }
            }
            $dataSerialized = json_encode($usercartDetails);
            $this->updateUserCart($dataSerialized);
            return true;
        }
    }

    private function updateUserCart($dataSerialized)
    {
        if (isset($this->user_id)) {
            $record = new TableRecord(Cart::DB_TBL);
            $record->assignValues(["usercart_user_id" => $this->user_id, "usercart_type" => CART::TYPE_GIFTCARD, "usercart_details" => $dataSerialized]);
            if (!$record->addNew([], ['usercart_details' => $dataSerialized])) {
                Message::addErrorMessage($record->getError());
            }
        }
    }

}
