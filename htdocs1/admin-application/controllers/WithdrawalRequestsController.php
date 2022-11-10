<?php

class WithdrawalRequestsController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewWithdrawRequests($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditWithdrawRequests($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->objPrivilege->canViewWithdrawRequests();
        $data = FatApp::getPostedData();
        $frmSearch = $this->getSearchForm($this->adminLangId);
        if ($data) {
            $data['withdrawal_id'] = $data['id'];
            unset($data['id']);
            $frmSearch->fill($data);
        }
        $this->set("frmSearch", $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewWithdrawRequests();
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm($this->adminLangId);
        $data = FatApp::getPostedData();
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        if ($page < 2) {
            $page = 1;
        }
        $post = $searchForm->getFormDataFromArray($data);
        $srch = new WithdrawalRequestsSearch();
        $srch->joinUsers(true);
        $srch->joinForUserBalance();
        $srch->joinPayoutMethodJoin();
        $srch->joinPayoutMethodFee();
        $srch->addOrder('withdrawal_id', 'DESC');
        $srch->addMultipleFields([
            'tuwr.*', 'user_first_name', 'user_last_name', 'user_is_learner', 'user_is_teacher',
            'credential_email as user_email', 'credential_username as user_username',
            'user_balance', 'pmethod_code', 'IFNULL(pmtfee_fee,0) AS pmtfee', 'IFNULL(pmtfee_type,0) AS pmttype',
        ]);
        if ($post['keyword']) {
            $cond = $srch->addCondition('credential_username', 'like', '%' . $post['keyword'] . '%');
            $cond->attachCondition('user_first_name', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cond->attachCondition('user_last_name', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cond->attachCondition('credential_email', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cond->attachCondition('withdrawal_id', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        if ($post['minprice'] > 0) {
            $srch->addCondition('tuwr.withdrawal_amount', '>=', $post['minprice']);
        }
        if ($post['withdrawal_id'] > 0) {
            $srch->addCondition('tuwr.withdrawal_id', '=', $post['withdrawal_id']);
        }
        if ($post['maxprice'] > 0) {
            $srch->addCondition('tuwr.withdrawal_amount', '<=', $post['maxprice']);
        }
        if ($post['status'] >= 0) {
            $srch->addCondition('tuwr.withdrawal_status', '=', $post['status']);
        }
        if ($post['date_from']) {
            $srch->addCondition('tuwr.withdrawal_request_date', '>=', $post['date_from'] . ' 00:00:00');
        }
        if ($post['date_to']) {
            $srch->addCondition('tuwr.withdrawal_request_date', '<=', $post['date_to'] . ' 00:00:00');
        }
        $type = FatApp::getPostedData('type', FatUtility::VAR_INT, 0);
        if ($type > 0) {
            if ($type == User::USER_TYPE_LEANER) {
                $srch->addCondition('user_is_learner', '=', applicationConstants::YES);
                $srch->addCondition('user_is_teacher', '=', applicationConstants::NO);
            }
            if ($type == User::USER_TYPE_TEACHER) {
                $srch->addCondition('user_is_teacher', '=', applicationConstants::YES);
            }
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('statusArr', Transaction::getWithdrawlStatusArr($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function updateStatus()
    {
        $this->objPrivilege->canEditWithdrawRequests();
        $post = FatApp::getPostedData();
        if ($post == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $withdrawalId = FatUtility::int($post['id']);
        $status = FatUtility::int($post['status']);
        $allowedStatusUpdateArr = [Transaction::WITHDRAWL_STATUS_APPROVED, Transaction::WITHDRAWL_STATUS_DECLINED];
        $srch = new WithdrawalRequestsSearch();
        $srch->joinPayoutMethodJoin();
        $srch->joinPayoutMethodFee();
        $srch->addCondition('withdrawal_id', '=', $withdrawalId);
        $srch->addMultipleFields([
            'tuwr.*',
            'pmethod_id',
            'pmethod_code',
            'IFNULL(pmtfee_fee,0) AS pmtfee',
            'IFNULL(pmtfee_type,0) AS pmtType',
        ]);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetch($rs);
        if (empty($records)) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (1 > $withdrawalId || !in_array($status, $allowedStatusUpdateArr) || $records['withdrawal_status'] != Transaction::WITHDRAWL_STATUS_PENDING) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $gatewayFee = $records['pmtfee'];
        $gatewayFee = FatUtility::float($gatewayFee);
        if ($gatewayFee > 0 && $records['pmtType'] == PaymentMethodTransactionFee::FEE_TYPE_PERCENTAGE) {
            $gatewayFee = ($gatewayFee / 100) * $records['withdrawal_amount'];
        }
        $amount = $records['withdrawal_amount'] - $gatewayFee;
        if (0 >= $amount) {
            Message::addErrorMessage(Label::getLabel('MSG_Withdrawal_amount_is_zero_after_adding_gateway_fee'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $records['gatewayFee'] = $gatewayFee;
        $records['amount'] = round($amount, 2);
        if ($records['pmethod_code'] == PaypalPayout::KEY_NAME && $status == Transaction::WITHDRAWL_STATUS_APPROVED) {
            $payoutObj = new PaypalPayout();
            if ($payoutObj->releasePayout($records) == false) {
                Message::addErrorMessage($payoutObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Status_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $assignFields = ['withdrawal_status' => $status, 'withdrawal_transaction_fee' => $gatewayFee];
        if (!$db->updateFromArray(User::DB_TBL_USR_WITHDRAWAL_REQ, $assignFields, ['smt' => 'withdrawal_id = ?', 'vals' => [$withdrawalId]])) {
            $db->rollbackTransaction();
            Message::addErrorMessage($db->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!$db->updateFromArray(Transaction::DB_TBL, ["utxn_status" => Transaction::STATUS_COMPLETED], ['smt' => 'utxn_withdrawal_id=?', 'vals' => [$withdrawalId]])) {
            $db->rollbackTransaction();
            Message::addErrorMessage($db->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if ($status == Transaction::WITHDRAWL_STATUS_APPROVED && $gatewayFee > 0) {
            Transaction::updateTransactionFeeMessage($withdrawalId, $gatewayFee);
        }
        if ($status == Transaction::WITHDRAWL_STATUS_DECLINED) {
            $transObj = new Transaction($records['withdrawal_user_id']);
            $txnDetail = $transObj->getAttributesBywithdrawlId($withdrawalId);
            $formattedRequestValue = '#' . str_pad($withdrawalId, 6, '0', STR_PAD_LEFT);
            $txnArray["utxn_user_id"] = $txnDetail["utxn_user_id"];
            $txnArray["utxn_credit"] = $txnDetail["utxn_debit"];
            $txnArray["utxn_status"] = Transaction::STATUS_COMPLETED;
            $txnArray["utxn_withdrawal_id"] = $txnDetail["utxn_withdrawal_id"];
            $txnArray["utxn_type"] = Transaction::TYPE_MONEY_WITHDRAWN;
            $txnArray["utxn_comments"] = Label::getLabel('MSG_Withdrawal_Request_Declined_Amount_Refunded', $this->adminLangId) . ' ' . Label::getLabel('LBL_Request_ID') . ' ' . $formattedRequestValue;
            $txnId = $transObj->addTransaction($txnArray);
            if ($txnId == false) {
                $db->rollbackTransaction();
                Message::addErrorMessage($transObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $db->commitTransaction();
            $emailNotificationObj = new EmailHandler();
            $emailNotificationObj->sendTxnNotification($txnId, $this->adminLangId);
        }
        $db->commitTransaction();
        $this->set('msg', Label::getLabel('LBL_Status_Updated_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getSearchForm($langId)
    {
        $frm = new Form('frmReqSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword');
        $frm->addTextBox(Label::getLabel('LBL_From', $this->adminLangId) . ' [' . $this->siteDefaultCurrencyCode . ']', 'minprice')->requirements()->setFloatPositive(true);
        $frm->addTextBox(Label::getLabel('LBL_To', $this->adminLangId) . ' [' . $this->siteDefaultCurrencyCode . ']', 'maxprice')->requirements()->setFloatPositive(true);
        $statusArr = Transaction::getWithdrawlStatusArr($langId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'status', ['-1' => 'Does not matter'] + $statusArr, '', [], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $arr_options2 = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + User::getUserTypesArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_User_Type', $this->adminLangId), 'type', $arr_options2, -1, [], '');
        $frm->addHiddenField('', 'withdrawal_id', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId), ['onclick' => 'clearTagSearch();']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
