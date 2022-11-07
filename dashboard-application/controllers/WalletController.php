<?php

class WalletController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $userId = UserAuthentication::getLoggedUserId();
        $frmSrch = $this->getSearchForm();
        $this->set('frmSrch', $frmSrch);
        $this->set('userTotalWalletBalance', User::getUserBalance($userId, false));
        $this->set('can_withdraw', User::canWithdraw($userId));
        $this->set('setMonthAndWeekName', true);
        $this->_template->render();
    }

    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $userId = UserAuthentication::getLoggedUserId();
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        /* [ */
        $balSrch = Transaction::getSearchObject();
        $balSrch->doNotCalculateRecords();
        $balSrch->doNotLimitRecords();
        $balSrch->addMultipleFields(['utxn.*', "utxn_credit - utxn_debit as bal"]);
        $balSrch->addCondition('utxn_user_id', '=', $userId);
        $balSrch->addCondition('utxn_status', '=', applicationConstants::ACTIVE);
        $qryUserPointsBalance = $balSrch->getQuery();
        /* ] */
        $srch = Transaction::getSearchObject(false, false);
        $srch->joinTable('(' . $qryUserPointsBalance . ')', 'LEFT JOIN', 'tqupb.utxn_id <= utxn.utxn_id', 'tqupb');
        $srch->addMultipleFields(['utxn.*', "SUM(tqupb.bal) balance"]);
        $srch->addCondition('utxn.utxn_user_id', '=', $userId);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $srch->addGroupBy('utxn.utxn_id');
        $srch->addOrder('utxn.utxn_date', 'DESC');
        $srch->addOrder('utxn_id', 'DESC');
        $keyword = FatApp::getPostedData('keyword', null, '');
        if (!empty($keyword)) {
            $cond = $srch->addCondition('utxn.utxn_order_id', 'like', '%' . $keyword . '%');
            $cond->attachCondition('utxn.utxn_op_id', 'like', '%' . $keyword . '%', 'OR');
            $cond->attachCondition('utxn.utxn_comments', 'like', '%' . $keyword . '%', 'OR');
            $cond->attachCondition('concat("TN-" ,lpad( utxn.`utxn_id`,7,0))', 'like', '%' . $keyword . '%', 'OR', true);
        }
        $systemTimeZone = MyDate::getTimeZone();
        $user_timezone = MyDate::getUserTimeZone();
        $fromDate = FatApp::getPostedData('date_from', FatUtility::VAR_DATE, '');
        if (!empty($fromDate)) {
            $fromDate = MyDate::changeDateTimezone($fromDate, $user_timezone, $systemTimeZone);
            $cond = $srch->addCondition('utxn.utxn_date', '>=', $fromDate);
        }
        $toDate = FatApp::getPostedData('date_to', FatUtility::VAR_DATE, '');
        if (!empty($toDate)) {
            $toDate = MyDate::changeDateTimezone($toDate, $user_timezone, $systemTimeZone);
            $cond = $srch->addCondition('cast( utxn.`utxn_date` as date)', '<=', $toDate, 'and', true);
        }
        $debit_credit_type = FatApp::getPostedData('debit_credit_type', FatUtility::VAR_INT, -1);
        if ($debit_credit_type > 0) {
            switch ($debit_credit_type) {
                case Transaction::CREDIT_TYPE:
                    $srch->addCondition('utxn.utxn_credit', '>', '0');
                    $srch->addCondition('utxn.utxn_debit', '=', '0');
                    break;
                case Transaction::DEBIT_TYPE:
                    $srch->addCondition('utxn.utxn_debit', '>', '0');
                    $srch->addCondition('utxn.utxn_credit', '=', '0');
                    break;
            }
        }
        $records = [];
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs, 'utxn_id');
        $this->set('arrListing', $records);
        $this->set('page', $page);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('postedData', $post);
        $this->set('statusArr', Transaction::getStatusArr($this->siteLangId));
        $this->_template->render(false, false);
    }

    private function getSearchForm()
    {
        $frm = new Form('frmCreditSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '');
        $frm->addSelectBox(Label::getLabel('LBL_Type'), 'debit_credit_type', [-1 => Label::getLabel('LBL_Both-Debit/Credit', $this->siteLangId)] + Transaction::getCreditDebitTypeArr($this->siteLangId), -1, [], '');
        $frm->addDateField(Label::getLabel('LBL_From'), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_To'), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $fldSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->siteLangId));
        $btnReset = $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        $fldSubmit->attachField($btnReset);
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        return $frm;
    }

    public function rechargeWalletForm()
    {
        $this->set('frmRechargeWallet', $this->getRechargeWalletForm());
        $this->_template->render(false, false);
    }

    private function getRechargeWalletForm()
    {
        $frm = new Form('frmRechargeWallet');
        $str = Label::getLabel('LBL_ENTER_AMOUNT_TO_BE_ADDED_[{currency-code}]');
        $str = str_replace("{currency-code}", CommonHelper::getSystemCurCode(), $str);
        $fld = $frm->addFloatField($str, 'amount', '', ['placeholder' => CommonHelper::getPlaceholderForAmtField()]);
        $fld->requirements()->setFloatPositive();
        $fld->requirements()->setRange(1, 999999);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Add_Money_to_account', $this->siteLangId));
        return $frm;
    }

    /*     * ****User Wallet Gift Card Redemption module start***** */

    public function reedemGiftcard()
    {
        $userId = UserAuthentication::getLoggedUserId();
        if (1 > $userId) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $frm = $this->getGiftcardRedeemForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (!$post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(FatUtility::generateUrl('Wallet'));
            return;
        }
        $giftcardObj = new Giftcard();
        if (false === $giftcardObj->checkGiftCardAvailablity($post['giftcard_code'], $userId)) {
            Message::addErrorMessage(Label::getLabel("MSG_Invalid_Giftcard_code", $this->siteLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $txnDataArr = $giftcardObj->checkGiftCardAvailablity($post['giftcard_code'], $userId);
        $txnDataArr['utxn_user_id'] = $userId;
        $txnDataArr['utxn_status'] = Transaction::STATUS_COMPLETED;
        $txnDataArr['utxn_type'] = Transaction::TYPE_GIFTCARD_REDEEM_TO_WALLET;
        $txnDataArr['utxn_comments'] = Label::getLabel('LBL_Giftcard_Redeem_To_Wallet', $this->siteLangId);
        $transObj = new Transaction($userId);
        if (!$txnId = $transObj->addTransaction($txnDataArr)) {
            $this->error = $transObj->getError();
            Message::addErrorMessage($transObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (true != $giftcardObj->updateGiftcardStatus($post['giftcard_code'], $userId, $txnId)) {
            Message::addErrorMessage($giftcardObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $emailObj = new EmailHandler();
        $emailObj->giftcardRedeenNotificationAdmin($post['giftcard_code'], $this->siteLangId, $txnDataArr['order_currency_id']);
        $this->set('msg', Label::getLabel("MSG_Giftcard_Redeem_successfully", $this->siteLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function giftcardRedeemForm()
    {
        $frm = $this->getGiftcardRedeemForm();
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    private function getGiftcardRedeemForm()
    {
        $frm = new Form('giftCardReeedem');
        $frm->addFormTagAttribute('class', 'form');
        $frm->addFormTagAttribute('onsubmit', 'giftcardRedeem(this); return(false);');
        $giftcard = $frm->addTextBox(Label::getLabel('LBL_GiftCard_Code'), 'giftcard_code', '', ['placeholder' => Label::getLabel('LBL_Enter_Gift_Card_Code', $this->siteLangId)]);
        $giftcard->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Redeem', $this->siteLangId), ['class' => 'btn btn--primary block-on-mobile']);
        return $frm;
    }

    public function setUpWalletRecharge()
    {
        $minimumRechargeAmount = 1;
        $frm = $this->getRechargeWalletForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $loggedUserId = UserAuthentication::getLoggedUserId();
        $order_net_amount = $post['amount'];
        if ($order_net_amount < $minimumRechargeAmount) {
            $str = Label::getLabel("LBL_Recharge_amount_must_be_greater_than_{minimumrechargeamount}", $this->siteLangId);
            $str = str_replace("{minimumrechargeamount}", CommonHelper::displayMoneyFormat($minimumRechargeAmount, true, true), $str);
            Message::addErrorMessage($str);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $orderData = [];
        $order_id = isset($_SESSION['wallet_recharge_cart']["order_id"]) ? $_SESSION['wallet_recharge_cart']["order_id"] : false;
        $orderData['order_type'] = Order::TYPE_WALLET_RECHARGE;
        $orderData['order_id'] = $order_id;
        $orderData['order_user_id'] = $loggedUserId;
        $orderData['order_is_paid'] = Order::ORDER_IS_PENDING;
        $orderData['order_date_added'] = date('Y-m-d H:i:s');
        $languageRow = Language::getAttributesById($this->siteLangId);
        $orderData['order_language_id'] = $languageRow['language_id'];
        $orderData['order_language_code'] = $languageRow['language_code'];
        $currencyRow = Currency::getAttributesById($this->siteCurrencyId);
        $orderData['order_currency_id'] = $currencyRow['currency_id'];
        $orderData['order_currency_code'] = $currencyRow['currency_code'];
        $orderData['order_currency_value'] = $currencyRow['currency_value'];
        $orderData['order_net_amount'] = $order_net_amount;
        $orderData['order_wallet_amount_charge'] = 0;
        $orderObj = new Order();
        if ($orderObj->addUpdate($orderData, $this->siteLangId)) {
            $order_id = $orderObj->getOrderId();
        } else {
            Message::addErrorMessage($orderObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('redirectUrl', CommonHelper::generateUrl('WalletPay', 'Recharge', [$order_id], CONF_WEBROOT_FRONTEND));
        $this->set('msg', Label::getLabel('MSG_Redirecting', $this->siteLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function requestWithdrawal()
    {
        $payoutMethodId = FatApp::getPostedData('methodId', FatUtility::VAR_INT, 0);
        $frm = $this->getWithdrawalForm($payoutMethodId, $this->siteLangId);
        $userId = UserAuthentication::getLoggedUserId();
        if (!User::canWithdraw($userId)) {
            Message::addErrorMessage(Label::getLabel('MSG_Unauthorized'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $balance = User::getUserBalance($userId);
        $days = FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS", FatUtility::VAR_INT, 0);
        $lastWithdrawal = User::getUserLastWithdrawalRequest($userId);
        if ($lastWithdrawal && (strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . $days . " days") - time()) > 0) {
            $nextWithdrawalDate = date('Y-m-d', strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS", FatUtility::VAR_INT, 0) . " days"));
            Message::addErrorMessage(sprintf(Label::getLabel('MSG_Last_Withdrawal_Request_Date_is_%s_You_can_Place_next_request_on_%s', $this->siteLangId), FatDate::format($lastWithdrawal["withdrawal_request_date"]), FatDate::format($nextWithdrawalDate)));
            FatUtility::dieWithError(Message::getHtml());
        }
        $minimumWithdrawLimit = FatApp::getConfig("CONF_MIN_WITHDRAW_LIMIT", FatUtility::VAR_INT, 0);
        if ($balance < $minimumWithdrawLimit) {
            Message::addErrorMessage(sprintf(Label::getLabel('MSG_Withdrawal_Request_Minimum_Balance_Less_%s', $this->siteLangId), CommonHelper::displayMoneyFormat($minimumWithdrawLimit, true, true)));
            FatUtility::dieWithError(Message::getHtml());
        }
        $userObj = new User($userId);
        $data = $userObj->getUserBankInfo();
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    private function getWithdrawalForm(int $payoutMethodId, int $langId)
    {
        $withdrawlMethodArray = PaymentMethods::getPayoutMethods(true, $langId);
        if (empty($withdrawlMethodArray)) {
            Message::addErrorMessage(Label::getLabel('MSG_No_Payment_Method_Active_Yet'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $paymentMethod = array_column($withdrawlMethodArray, 'pmethod_code', 'pmethod_id');
        if (!empty($payoutMethodId) && !array_key_exists($payoutMethodId, $paymentMethod)) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $payoutMethod = current($withdrawlMethodArray);
        if (!empty($payoutMethodId)) {
            $pmethodCode = $paymentMethod[$payoutMethodId];
            $payoutMethod = $withdrawlMethodArray[$pmethodCode];
        }
        $payoutMethodId = $payoutMethod['pmethod_id'];
        $payoutMethodCode = $payoutMethod['pmethod_code'];
        $frm = new Form('frmWithdrawal');
        $fld = $frm->addRadioButtons(Label::getLabel('LBL_Payout_Type'), 'withdrawal_payment_method_id', $paymentMethod, $payoutMethodId);
        $defaultCurLbl = Label::getLabel('LBL_ENTER_AMOUNT_TO_BE_ADDED_[{currency-code}]');
        $defaultCurLbl = str_replace('{currency-code}', CommonHelper::getSystemCurCode(), $defaultCurLbl);
        $withdrawalAmountFld = $frm->addRequiredField($defaultCurLbl, 'withdrawal_amount');
        $withdrawalAmountFld->requirement->setFloat(true);
        $walletBalance = User::getUserBalance(UserAuthentication::getLoggedUserId());
        $withdrawalAmountAfterHTML = "<small>" . Label::getLabel("LBL_Current_Wallet_Balance", $langId) . ' ' . CommonHelper::displayMoneyFormat($walletBalance, true, true) . "</small>";
        switch ($payoutMethodCode) {
            case PaymentMethods::BANK_PAYOUT_KEY:
                $frm->addRequiredField(Label::getLabel('LBL_Bank_Name', $langId), 'ub_bank_name');
                $frm->addRequiredField(Label::getLabel('LBL_Account_Holder_Name', $langId), 'ub_account_holder_name');
                $frm->addRequiredField(Label::getLabel('LBL_Account_Number', $langId), 'ub_account_number');
                $frm->addRequiredField(Label::getLabel('LBL_IFSC_Swift_Code', $langId), 'ub_ifsc_swift_code');
                $frm->addTextArea(Label::getLabel('LBL_Bank_Address', $langId), 'ub_bank_address');
                break;
            case PaypalPayout::KEY_NAME:
                $frm->addRequiredField(Label::getLabel('LBL_Paypal_Email', $langId), 'ub_paypal_email_address');
                break;
        }
        $paymentMethodTransactionFee = new PaymentMethodTransactionFee($payoutMethodId, CommonHelper::getSystemCurrencyId());
        $payoutGatewayFee = $paymentMethodTransactionFee->getGatewayFee();
        switch ($paymentMethodTransactionFee->feeType) {
            case PaymentMethodTransactionFee::FEE_TYPE_FLAT:
                $payoutFee = CommonHelper::displayMoneyFormat($payoutGatewayFee, true, true);
                break;
            default:
                $payoutFee = $payoutGatewayFee . '%';
                break;
        }
        if ($payoutGatewayFee > 0) {
            $withdrawalAmountAfterHTML .= "<small class='-color-secondary transaction-fee'>" . Label::getLabel("LBL_Transaction_Fee", $langId) . ' ' . $payoutFee . "</small>";
        }
        $withdrawalAmountFld->htmlAfterField = $withdrawalAmountAfterHTML;
        $frm->addTextArea(Label::getLabel('LBL_Other_Info_Instructions', $langId), 'withdrawal_comments');
        $frm->addHiddenField('', 'pmethod_code', $payoutMethodCode);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Send_Request', $langId));
        $frm->addButton("", "btn_cancel", Label::getLabel("LBL_Cancel", $langId));
        return $frm;
    }

    public function setupRequestWithdrawal()
    {
        $userId = UserAuthentication::getLoggedUserId();
        $balance = User::getUserBalance($userId);
        $lastWithdrawal = User::getUserLastWithdrawalRequest($userId);
        if (!User::canWithdraw($userId)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Unauthorized'));
        }
        if ($lastWithdrawal && (strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS") . " days") - time()) > 0) {
            $nextWithdrawalDate = date('Y-d-m', strtotime($lastWithdrawal["withdrawal_request_date"] . "+" . FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS") . " days"));
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_Withdrawal_Request_Date', $this->siteLangId), MyDate::format($lastWithdrawal["withdrawal_request_date"]), MyDate::format($nextWithdrawalDate), FatApp::getConfig("CONF_MIN_INTERVAL_WITHDRAW_REQUESTS")));
        }
        $minimumWithdrawLimit = FatApp::getConfig("CONF_MIN_WITHDRAW_LIMIT");
        if ($balance < $minimumWithdrawLimit) {
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_Withdrawal_Request_Minimum_Balance_Less_%s', $this->siteLangId), CommonHelper::displayMoneyFormat($minimumWithdrawLimit, true, true)));
        }
        $payoutMethodId = FatApp::getPostedData('withdrawal_payment_method_id', FatUtility::VAR_INT, 0);
        $frm = $this->getWithdrawalForm($payoutMethodId, $this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $pmethodCodeFld = $frm->getField('pmethod_code');
        $post['pmethod_code'] = $pmethodCode = $pmethodCodeFld->value;
        if (($minimumWithdrawLimit > $post["withdrawal_amount"])) {
            FatUtility::dieJsonError(sprintf(Label::getLabel('MSG_Withdrawal_Request_Less_%s', $this->siteLangId), CommonHelper::displayMoneyFormat($minimumWithdrawLimit, true, true)));
        }
        if (($post["withdrawal_amount"] > $balance)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Withdrawal_Request_Greater', $this->siteLangId));
        }
        $userObj = new User($userId);
        $saveInfoFunction = ($pmethodCode == PaymentMethods::BANK_PAYOUT_KEY) ? 'updateBankInfo' : 'updatePaypalInfo';
        if (!$userObj->$saveInfoFunction($post)) {
            Label::getLabel($userObj->getError());
        }
        if (!$withdrawRequestId = $userObj->addWithdrawalRequest(array_merge($post, ["ub_user_id" => $userId]), $this->siteLangId)) {
            FatUtility::dieJsonError($userObj->getError());
        }
        $withdrawRequestData = $post;
        $withdrawRequestData['txn_id'] = $withdrawRequestId;
        $withdrawRequestData['user_first_name'] = UserAuthentication::getLoggedUserAttribute('user_first_name', true);
        $withdrawRequestData['user_last_name'] = UserAuthentication::getLoggedUserAttribute('user_last_name', true);
        $withdrawRequestData['user_email'] = UserAuthentication::getLoggedUserAttribute('user_email', true);
        $withdrawRequestData['payout_type'] = ($pmethodCode == PaymentMethods::BANK_PAYOUT_KEY) ? Label::getLabel('Lbl_Bank_Payout') : Label::getLabel('Lbl_Paypal_Payout');
        $fatTemplate = new FatTemplate(' ', ' ');
        $fatTemplate->set('data', $withdrawRequestData);
        $fatTemplate->set('pmethodCode', $pmethodCode);
        $withdrawRequestData['other_details'] = $fatTemplate->render(false, false, '_partial/new-withdrawal-request-mail.php', true);
        $emailNotificationObj = new EmailHandler($this->siteLangId);
        $emailNotificationObj->sendWithdrawRequestNotification($withdrawRequestData, "A");
        $emailNotificationObj->sendWithdrawRequestNotification($withdrawRequestData, "U");
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_Withdraw_request_placed_successfully', $this->siteLangId));
    }

    public function testGiftCard($orderId)
    {
        $giftcard = new OrderPayment($orderId);
        $giftcard->addOrderPayment("Paypal", 'sdfsd76s7sf', 150);
    }

}
