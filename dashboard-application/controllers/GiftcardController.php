<?php

class GiftcardController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->user_id = UserAuthentication::getLoggedUserId();
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm();
        $frm = $this->addForm();
        $frm->setFormTagAttribute('action', FatUtility::generateUrl('Giftcard', 'addtoCart'));
        $this->set('formData', $frm);
        $this->set('frmSrch', $frmSrch);
        $this->_template->render(true, true);
    }

    private function getSearchForm()
    {
        $frm = new Form('frmGiftcardSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '');
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'giftcard_status', [-1 => Label::getLabel('LBL_Does_Not_Matter', $this->siteLangId)] + Giftcard::getStatusArr($this->siteLangId), -1, [], '');
        $fldSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->siteLangId));
        $fldCancel = $frm->addResetButton("", "btn_clear", Label::getLabel("LBL_Clear", $this->siteLangId), ['onclick' => 'clearSearch();']);
        $fldSubmit->attachField($fldCancel);
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        return $frm;
    }

    public function remove()
    {
        $giftcardKey = FatApp::getPostedData('key');
        if ($this->removeGiftcardFromCart($giftcardKey, $this->user_id) === true) {
            $this->set('msg', Label::getLabel("MSG_Cart_Giftcard_Removed_Successfuly", $this->siteLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }

    public function listing()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $giftCardStatus = Giftcard::getStatusArr($this->siteLangId);
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = new OrderSearch(false, false);
        $srch->joinOrderPaymentMethod($this->siteLangId);
        $srch->joinOrderProduct($this->siteLangId);
        $srch->joinGiftcards();
        $srch->joinGiftCardBuyer();
        $srch->joinGiftcardRecipient();
        $srch->addOrder('order_date_added', 'DESC');
        $srch->addCondition('order_type', '=', Order::TYPE_GIFTCARD);
        $srch->addCondition('order_user_id', '=', $this->user_id);
        $srch->addMultipleFields([
            'order_id', 'order_is_paid', 'order_net_amount', 'order_wallet_amount_charge', 'gift.giftcard_code',
            'gift.giftcard_amount', 'gcrecipient.gcrecipient_email as recipient_email', 'gcrecipient.gcrecipient_name as recipient_name',
            'gift.giftcard_status', 'gift.giftcard_used_date', 'gift.giftcard_expiry_date', 'op_id', 'op_invoice_number', 'op_unit_price']);
        $keyword = FatApp::getPostedData('keyword', null, '');
        if (!empty($keyword)) {
            $cond = $srch->addCondition('giftcard_code', 'like', '%' . $keyword . '%');
            $cond->attachCondition('order_id', 'like', '%' . $keyword . '%', 'OR');
        }
        $status = FatApp::getPostedData('giftcard_status', '', -1);
        if ($status > -1) {
            $srch->addCondition('giftcard_status', '=', $status);
        }
        if (isset($post['order_is_paid']) && $post['order_is_paid'] != '') {
            $orderIsPaid = FatUtility::int($post['order_is_paid']);
            $srch->addCondition('order_is_paid', '=', $orderIsPaid);
        }
        $dateFrom = FatApp::getPostedData('date_from', null, '');
        if (!empty($dateFrom)) {
            $srch->addDateFromCondition($dateFrom);
        }
        $dateTo = FatApp::getPostedData('date_to', null, '');
        if (!empty($dateTo)) {
            $srch->addDateToCondition($dateTo);
        }
        $priceFrom = FatApp::getPostedData('price_from', null, '');
        if (!empty($priceFrom)) {
            $srch->addMinPriceCondition($priceFrom);
        }
        $priceTo = FatApp::getPostedData('price_to', null, '');
        if (!empty($priceTo)) {
            $srch->addMaxPriceCondition($priceTo);
        }
        $srch->addGroupBy('gcbuyer.gcbuyer_order_id');
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $rs = $srch->getResultSet();
        $giftcardList = FatApp::getDb()->fetchAll($rs);
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set("giftCardStatus", $giftCardStatus);
        $this->set("giftcardList", $giftcardList);
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $this->_template->render(false, false);
    }

    private function addForm()
    {
        $frm = new Form('giftcardForm');
        $frm->setFormTagAttribute('class', 'form');
        $str = Label::getLabel('LBL_ENTER_AMOUNT_TO_BE_ADDED_[{currency-code}]');
        $str = str_replace("{currency-code}", CommonHelper::getSystemCurCode(), $str);
        $giftcardPrice = $frm->addFloatField($str, 'giftcard_price', '', ['placeholder' => CommonHelper::getPlaceholderForAmtField(), "onkeyup" => "cardUpdate(this)", 'id' => "giftcard_price"]);
        $giftcardPrice->requirements()->setRequired();
        $giftcardPrice->requirements()->setLength(1, 6);
        $giftcardPrice->requirements()->setRegularExpressionToValidate("^\s*(?=.*[1-9])\d*(?:\.\d{1,2})?\s*$");
        $userObj = new User($this->user_id);
        $userInfo = $userObj->getUserInfo($this->user_id);
        $loggedInUserName = UserAuthentication::isUserLogged() === true ? $userInfo['user_last_name'] . ' ' . $userInfo['user_first_name'] : '';
        $loggedInUserEmail = UserAuthentication::isUserLogged() === true ? $userInfo['credential_email'] : '';
        $loggedInUserPhone = UserAuthentication::isUserLogged() === true ? $userInfo['user_phone'] : '';
        $buyerName = $frm->addRequiredField(Label::getLabel('LBL_Buyer_Name'), 'gcbuyer_name', $loggedInUserName, [
            'title' => Label::getLabel('LBL_Buyer_Name'),
            'placeholder' => Label::getLabel('LBL_Buyer_Name'),
            'readonly' => 'true'
        ]);
        $buyerName->requirements()->setRequired();
        $buyerEmail = $frm->addEmailField(Label::getLabel('LBL_Buyer_Email'), 'gcbuyer_email', $loggedInUserEmail, [
            'title' => Label::getLabel('LBL_Buyer_Email'),
            'placeholder' => Label::getLabel('LBL_Buyer_Email'),
            'readonly' => 'true'
        ]);
        $buyerPhone = $frm->addRequiredField(Label::getLabel('LBL_Buyer_Phone'), 'gcbuyer_phone', $loggedInUserPhone, [
            'title' => Label::getLabel('LBL_Buyer_Phone'),
            'placeholder' => Label::getLabel('LBL_Buyer_Phone')
        ]);
        $buyerPhone->requirements()->setRequired();
        $buyerPhone->requirements()->setRegularExpressionToValidate(applicationConstants::PHONE_NO_REGEX);
        $frm->addRequiredField(Label::getLabel('LBL_Recipient_Name'), 'gcrecipient_name', '', [
            'placeholder' => Label::getLabel('LBL_Recipient_Name')
        ]);
        $frm->addEmailField(Label::getLabel('LBL_Recipient_Email'), 'gcrecipient_email', '', [
            'placeholder' => Label::getLabel('LBL_Recipient_Email')
        ]);
        $save = $frm->addSubmitButton('', 'save', Label::getLabel('LBL_Send_Gift_Card'), [
            'class' => 'btn btn--primary',
            'onclick' => 'validateCustomFields()'
        ]);
        $clear = $frm->addButton('', 'clear', 'Clear', [
            'class' => 'btn btn--third',
            'onclick' => 'cleardata()'
        ]);

        return $frm;
    }

    public function addtoCart()
    {
        $frm = $this->addForm();
        $formData = FatApp::getPostedData();
        $formData = $frm->getFormDataFromArray($formData);
        $result = $this->addGiftcardToCart($formData);
        if (false === $result) {
            FatApp::redirectUser(FatUtility::generateUrl('Giftcard'));
        }
        FatApp::redirectUser(FatUtility::generateUrl('GiftcardCheckout', '', [], CONF_WEBROOT_FRONTEND));
    }

    public function addGiftcardToCart($giftcardData)
    {
        $db = FatApp::getDb();
        $db->deleteRecords(Cart ::DB_TBL, [
            'smt' => '`usercart_user_id`=? and usercart_type=?',
            'vals' => [$this->user_id, Cart::TYPE_GIFTCARD]]);
        $srch = new SearchBase(Cart::DB_TBL);
        $srch->addCondition('usercart_user_id', '=', $this->user_id);
        $srch->addCondition('usercart_type', '=', Cart::TYPE_GIFTCARD);
        $rs = $srch->getResultSet();
        if ($row = $db->fetch($rs)) {
            if (!empty($row["usercart_details"])) {
                $data = json_decode($row["usercart_details"], true);
                $dataSerialized = json_encode(array_merge($data, $giftcardData));
                $this->updateUserCart($dataSerialized);
            }
        } else {
            $dataCart['usercart_details'] = json_encode($giftcardData);
            $dataCart['usercart_user_id'] = $this->user_id;
            $dataCart['usercart_type'] = CART::TYPE_GIFTCARD;
            $dataCart = $db->insertFromArray(Cart::DB_TBL, $dataCart);
            if (false === $dataCart) {
                return false;
            }
        }
        return true;
    }

    public function checkout()
    {
        $giftcardObj = new Giftcard();
        $orderId = $giftcardObj->saveOrder();
        if (false === $orderId) {
            FatApp::redirectUser(FatUtility::generateUrl('Giftcard'));
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
