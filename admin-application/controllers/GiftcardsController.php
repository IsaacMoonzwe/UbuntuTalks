<?php

class GiftcardsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewGiftcards($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditGiftcards($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->objPrivilege->canViewGiftcards();
        $frmSearch = $this->getGiftcardSearchForm($this->adminLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post['keyword'] = $post['id'];
            unset($post['id']);
            $frmSearch->fill($post);
        }
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewGiftcards();
        $frmSearch = $this->getGiftcardSearchForm($this->adminLangId);
        $data = FatApp::getPostedData();
        $post = $frmSearch->getFormDataFromArray($data);
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : FatUtility::int($data['page']);
        $pageSize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = new OrderSearch();
        $srch->joinOrderPaymentMethod($this->adminLangId);
        $srch->joinGiftCardBuyer();
        $srch->addOrder('order_date_added', 'DESC');
        $srch->addCondition('order_type', '=', Order::TYPE_GIFTCARD);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $srch->addMultipleFields(['order_id', 'order_date_added', 'order_is_paid',
            'GROUP_CONCAT(DISTINCT(gcbuyer.gcbuyer_name)) as buyer_user_name',
            'GROUP_CONCAT(DISTINCT(gcbuyer.gcbuyer_email)) as buyer_email',
            'order_net_amount', 'order_wallet_amount_charge', 'order_pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'order_is_wallet_selected'
        ]);
        $keyword = FatApp::getPostedData('keyword', null, '');
        if (!empty($keyword)) {
            $srch->addCondition('order_id', 'like', '%' . $keyword . '%');
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
        $rs = $srch->getResultSet();
        $ordersList = FatApp::getDb()->fetchAll($rs);
        $this->set("ordersList", $ordersList);
        $this->set('pageCount', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('postedData', $post);
        $this->set('recordCount', $srch->recordCount());
        $this->set('canViewGiftcards', $this->objPrivilege->canViewGiftcards($this->admin_id, true));
        $this->_template->render(false, false);
    }

    public function view($orderId)
    {
        $this->objPrivilege->canViewGiftcards();
        $srch = new OrderSearch();
        $srch->joinOrderPaymentMethod($this->adminLangId);
        $srch->joinGiftCardBuyer();
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->joinOrderBuyerUser();
        $srch->addMultipleFields([
            'order_id', 'order_user_id', 'order_date_added', 'order_is_paid',
            'case when buyer.user_first_name IS NULL THEN gcbuyer.gcbuyer_name else buyer.user_first_name end as buyer_user_name',
            'case when buyer_cred.credential_email IS NULL THEN gcbuyer.gcbuyer_email else buyer_cred.credential_email end as buyer_email',
            'case when buyer.user_phone IS NULL THEN gcbuyer.gcbuyer_phone else buyer.user_phone end as buyer_phone', 'order_net_amount',
            'order_pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name']);
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addCondition('order_type', '=', Order::TYPE_GIFTCARD);
        $rs = $srch->getResultSet();
        $order = FatApp::getDb()->fetch($rs);
        if (empty($order)) {
            FatApp::redirectUser(FatUtility::generateUrl("Orders"));
        }
        $opSrch = new OrderProductSearch($this->adminLangId, false, true);
        $opSrch->joinGiftcards();
        $opSrch->joinRecipientUser();
        $opSrch->joinGiftCardBuyer();
        $opSrch->doNotCalculateRecords();
        $opSrch->doNotLimitRecords();
        $opSrch->addCondition('op.op_order_id', '=', $order['order_id']);
        $opSrch->addMultipleFields([
            'gift.giftcard_code', 'gcrecipient.gcrecipient_email as recipient_email',
            'gcrecipient.gcrecipient_name as recipient_name', 'op_qty', 'gift.giftcard_status',
            'gift.giftcard_used_date', 'gift.giftcard_expiry_date', 'op_id', 'op_invoice_number',
            'op_unit_price', 'gcbuyer.gcbuyer_name', 'gcbuyer.gcbuyer_email']);
        $order['products'] = FatApp::getDb()->fetchAll($opSrch->getResultSet(), 'op_id');
        $orderObj = new Order($order['order_id']);
        $order['payments'] = $orderObj->getOrderPayments(["order_id" => $order['order_id']]);
        $frm = $this->getPaymentForm($this->adminLangId, $order['order_id']);
        $this->set('frm', $frm);
        $this->set('yesNoArr', applicationConstants::getYesNoArr($this->adminLangId));
        $this->set('order', $order);
        $this->_template->render();
    }

    public function updatePayment()
    {
        $this->objPrivilege->canEditGiftcards();
        $frm = $this->getPaymentForm($this->adminLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $orderId = $post['opayment_order_id'];
        if ($orderId == '' || $orderId == null) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->adminLangId);
        if (!$orderPaymentObj->addOrderPayment($post["opayment_method"], $post['opayment_gateway_txn_id'], $post["opayment_amount"], $post["opayment_comments"])) {
            Message::addErrorMessage($orderPaymentObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $giftcardObj = new Giftcard();
        if (!$giftcardObj->addGiftcardDetails($orderId)) {
            Message::addErrorMessage($giftcardObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('LBL_Payment_Details_Added_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getPaymentForm($langId, $orderId = '')
    {
        $frm = new Form('frmPayment');
        $frm->addHiddenField('', 'opayment_order_id', $orderId);
        $frm->addTextArea(Label::getLabel('LBL_Comments', $this->adminLangId), 'opayment_comments', '')->requirements()->setRequired();
        $frm->addRequiredField(Label::getLabel('LBL_Payment_Method', $this->adminLangId), 'opayment_method');
        $frm->addRequiredField(Label::getLabel('LBL_Txn_ID', $this->adminLangId), 'opayment_gateway_txn_id');
        $frm->addRequiredField(Label::getLabel('LBL_Amount', $this->adminLangId), 'opayment_amount')->requirements()->setFloatPositive(true);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getGiftcardSearchForm($langId)
    {
        $currencyData = CommonHelper::getSystemCurrencyData();
        $currencySymbol = ($currencyData['currency_symbol_left'] != '') ? $currencyData['currency_symbol_left'] : $currencyData['currency_symbol_right'];
        $frm = new Form('frmOrderSearch');
        $keyword = $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '', ['id' => 'keyword', 'autocomplete' => 'off']);
        $frm->addSelectBox(Label::getLabel('LBL_Payment_Status', $this->adminLangId), 'order_is_paid', Order::getPaymentStatusArr($langId), '', [], Label::getLabel('LBL_Select_Payment_Status', $this->adminLangId));
        $frm->addDateField('', 'date_from', '', ['placeholder' => 'Date From', 'readonly' => 'true']);
        $frm->addDateField('', 'date_to', '', ['placeholder' => 'Date To', 'readonly' => 'true']);
        $frm->addTextBox('', 'price_from', '', ['placeholder' => 'Order From' . ' [' . $currencySymbol . ']']);
        $frm->addTextBox('', 'price_to', '', ['placeholder' => 'Order To [' . $currencySymbol . ']']);
        $frm->addHiddenField('', 'page');
        $frm->addHiddenField('', 'user_id');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
