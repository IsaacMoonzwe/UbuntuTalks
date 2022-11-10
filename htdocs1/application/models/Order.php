<?php

class Order extends MyAppModel
{

    const DB_TBL = 'tbl_orders';
    const DB_TBL_PREFIX = 'order_';
    const DB_TBL_ORDER_PAYMENTS = 'tbl_order_payments';
    const DB_TBL_ORDER_PAYMENTS_PREFIX = 'opayment_';
    const DB_TBL_ORDER_STATUS_HISTORY = 'tbl_order_status_history';
    const DB_TBL_ORDER_STATUS_HISTORY_PREFIX = 'oshistory_';
    const TYPE_LESSON_BOOKING = 1;
    const TYPE_GIFTCARD = 2;
    const TYPE_WALLET_RECHARGE = 3;
    const ORDER_IS_CANCELLED = -1;
    const ORDER_IS_PENDING = 0;
    const ORDER_IS_PAID = 1;

    public function __construct($orderId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $orderId);
    }

    public static function getSearchObject($langId = 0)
    {
        return new SearchBase(static::DB_TBL, 'o');
    }

    public function updateOrderInfo($order_id, $data)
    {
        if (empty($data) || sizeof($data) <= 0 || !$order_id) {
            $this->error = 'Error, in updating the order, no parameters passed to update record.';
            return false;
        }
        $record = new TableRecord(static::DB_TBL);
        $record->assignValues($data);
        if (!$record->update(['smt' => 'order_id = ?', 'vals' => [$order_id]])) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public static function getPaymentStatusArr($langId = 0, $skipPending = false)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        if ($skipPending) {
            return [
                static::ORDER_IS_CANCELLED => Label::getLabel('LBL_Cancelled', $langId),
                static::ORDER_IS_PAID => Label::getLabel('LBL_Paid', $langId),
            ];
        }
        return [
            static::ORDER_IS_CANCELLED => Label::getLabel('LBL_Cancelled', $langId),
            static::ORDER_IS_PENDING => Label::getLabel('LBL_Pending', $langId),
            static::ORDER_IS_PAID => Label::getLabel('LBL_Paid', $langId),
        ];
    }

    public function addUpdate($data)
    {
        if (empty($data['order_id'])) {
            $order_id = $this->generateOrderId();
            $data['order_id'] = $order_id;
            $data['order_date_added'] = date('Y-m-d H:i:s');
        } else {
            $data['order_date_updated'] = date('Y-m-d H:i:s');
        }
        $this->order_id = $data['order_id'];
        $products = [];
        if (isset($data['products'])) {
            $products = $data['products'];
            unset($data['products']);
        }
        if (empty($data['order_discount_coupon_code'])) {
            unset($data['order_discount_coupon_code']);
            unset($data['order_discount_total']);
            unset($data['order_discount_info']);
        }
        $recordObj = new TableRecord(static::DB_TBL);
        $recordObj->assignValues($data);
        $flds_update_on_duplicate = $data;
        unset($flds_update_on_duplicate['order_id']);
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$recordObj->addNew([], $flds_update_on_duplicate)) {
            $db->rollbackTransaction();
            $this->error = $recordObj->getError();
            return false;
        }
        $_SESSION['shopping_cart']["order_id"] = $this->getOrderId();
        $db->deleteRecords(OrderProduct::DB_TBL, ['smt' => 'op_order_id = ?', 'vals' => [$this->getOrderId()]]);
        $db->deleteRecords(OrderProduct::DB_TBL . '_lang', ['smt' => 'oplang_order_id = ?', 'vals' => [$this->getOrderId()]]);
        if (!empty($products)) {
            $counter = 1;
            $opRecord = new OrderProduct();
            $opLangRecordObj = new TableRecord(OrderProduct::DB_TBL . '_lang');
            foreach ($products as $product) {
                $op_invoice_number = $this->getOrderId() . '-S' . str_pad($counter, 4, '0', STR_PAD_LEFT);
                $product['op_order_id'] = $this->getOrderId();
                $product['op_invoice_number'] = $op_invoice_number;
                $productsLangData = $product['productsLangData'];
                $opRecord->assignValues($product);
                if (!$opRecord->save()) {
                    $db->rollbackTransaction();
                    $this->error = $opRecord->getError();
                    return false;
                }
                $op_id = $opRecord->getMainTableRecordId();
                /* saving of products lang data[ */
                if (!empty($productsLangData)) {
                    foreach ($productsLangData as $productLangData) {
                        $productLangData['oplang_op_id'] = $op_id;
                        $productLangData['oplang_order_id'] = $this->getOrderId();
                        $opLangRecordObj->assignValues($productLangData);
                        if (!$opLangRecordObj->addNew()) {
                            $db->rollbackTransaction();
                            $this->error = $opLangRecordObj->getError();
                            return false;
                        }
                    }
                }
                /* ] */
                $counter++;
            }
        }
        $db->commitTransaction();
        return $this->getOrderId();
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function getOrderById($orderId)
    {
        $srch = new OrderSearch();
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addMultipleFields([
            'order_id',
            'order_user_id',
            'order_is_paid',
            'order_type',
            'order_net_amount',
            'order_is_wallet_selected',
            'order_wallet_amount_charge',
            'order_currency_code',
            'order_language_code',
            'order_language_id',
            'order_discount_coupon_code',
            'order_discount_total',
        ]);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function getOrderPaymentFinancials($orderId)
    {
        $orderInfo = $this->getOrderById($orderId);
        if(isset($_SESSION['event'])){
        $userBalance = EventUser::getUserBalance($orderInfo["order_user_id"]);     
        }
        else{
        $userBalance = User::getUserBalance($orderInfo["order_user_id"]);
        }
        $orderCreditsCharge = $orderInfo["order_wallet_amount_charge"] ? min($orderInfo["order_wallet_amount_charge"], $userBalance) : 0;
        $orderPaymentGatewayCharge = $orderInfo["order_net_amount"] - $orderInfo["order_wallet_amount_charge"];
        $orderPaymentSummary = [
            "net_payable" => $orderInfo["order_net_amount"],
            "order_user_balance" => $userBalance,
            "order_credits_charge" => $orderCreditsCharge,
            "order_payment_gateway_charge" => $orderPaymentGatewayCharge,
        ];
        return $orderPaymentSummary;
    }

    public function getOrderPaymentPaid($orderId)
    {
        $srch = new SearchBase(static::DB_TBL_ORDER_PAYMENTS, 'opayment');
        $srch->addMultipleFields(['sum(opayment_amount) as totalPaid']);
        $srch->addCondition('opayment_order_id', '=', $orderId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row)) {
            return ($row['totalPaid'] != null) ? $row['totalPaid'] : 0;
        }
        return 0;
    }

    public function addOrderPaymentHistory($orderId, $orderPaymentStatus, $comment = '', $notify = false)
    {
        $orderInfo = $this->getOrderById($orderId);
        if ($orderInfo) {
            if (!FatApp::getDb()->updateFromArray(Order::DB_TBL,
                            ['order_is_paid' => FatUtility::int($orderPaymentStatus), 'order_date_updated' => date('Y-m-d H:i:s')],
                            ['smt' => 'order_id = ? ', 'vals' => [$orderId]]
                    )) {
                $this->error = FatApp::getDb()->getError();
                return false;
            }
        }
        if (!FatApp::getDb()->insertFromArray(Order::DB_TBL_ORDER_STATUS_HISTORY,
                        [
                            'oshistory_order_id' => $orderId,
                            'oshistory_order_payment_status' => $orderPaymentStatus,
                            'oshistory_date_added' => date('Y-m-d H:i:s'),
                            'oshistory_customer_notified' => FatUtility::int($notify),
                            'oshistory_comments' => $comment
                        ]
                )) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        if (!$this->addProductOrderPayment($orderId, $orderInfo, $orderPaymentStatus, $comment, $notify)) {
            return false;
        }
        return true;
    }

    private function addProductOrderPayment($orderId, $orderInfo, $orderPaymentStatus, $comment = '', $notify = false)
    {
        $emailObj = new EmailHandler();
        // If order Payment status is 0 then becomes greater than 0 mail to Vendors and Update Child Order Status to Paid & Give Referral Reward Points
        if (!$orderInfo['order_is_paid'] && ($orderPaymentStatus > 0)) {
            $srch = new OrderProductSearch();
            $srch->addOrderIdCondition($orderId);
            $srch->addMultipleFields(['op_id']);
            $rs = $srch->getResultSet();
            $subOrders = FatApp::getDb()->fetchAll($rs);
            foreach ($subOrders as $subOrder) {
                if (!$this->addChildProductOrderHistory($subOrder["op_id"], $orderInfo['order_language_id'], FatApp::getConfig("CONF_DEFAULT_PAID_ORDER_STATUS"), '', true)) {
                    return false;
                }
            }
        }
    }

    public function addChildProductOrderHistory($op_id, $langId, $opStatusId, $comment = '', $notify = false)
    {
        $op_id = FatUtility::int($op_id);
        $langId = FatUtility::int($langId);
        $opStatusId = FatUtility::int($opStatusId);
        $db = FatApp::getDb();
        if (!$db->updateFromArray(
                        OrderProduct::DB_TBL,
                        ['op_orderstatus_id' => $opStatusId],
                        ['smt' => 'op_id = ? ', 'vals' => [$op_id]]
                )) {
            $this->error = $db->getError();
            return false;
        }
        if (!$db->insertFromArray(
                        Order::DB_TBL_ORDER_STATUS_HISTORY,
                        [
                            'oshistory_op_id' => $op_id,
                            'oshistory_orderstatus_id' => $opStatusId,
                            'oshistory_date_added' => date('Y-m-d H:i:s'),
                            'oshistory_customer_notified' => (int) $notify,
                            'oshistory_comments' => $comment,
                            'oshistory_tracking_number' => ''
                        ],
                        true
                )) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    private function generateOrderId()
    {
        $order_id = 'O';
        $order_id .= time();
        if ($this->checkUniqueOrderId($order_id)) {
            return $order_id;
        }
        $this->generateOrderId();
    }

    private function checkUniqueOrderId($order_id)
    {
        $row = Order::getAttributesById($order_id, ['order_id']);
        if ($row == false) {
            return true;
        }
        return false;
    }

    public static function getOrders($filter, $userType, $userId)
    {
        $srch = new OrderSearch(false, false);
        $srch->addGroupBy('order_id');
        $srch->joinOrderProduct();
         if(isset($_SESSION['event'])){
            $srch->addCondition('o.order_user_id', '=', $userId);
            $srch->joinTeacherLessonLanguage();
            $srch->addMultipleFields(['CONCAT(t.user_first_name," ",t.user_last_name) AS teacher_name', 'op_teacher_id']);
         
    }else  if ($userType == User::USER_TYPE_LEANER) {
            $srch->addCondition('o.order_user_id', '=', $userId);
            $srch->joinTeacherLessonLanguage();
            $srch->addMultipleFields(['CONCAT(t.user_first_name," ",t.user_last_name) AS teacher_name', 'op_teacher_id']);
        } 
    else {
            $srch->addCondition('op.op_teacher_id', '=', $userId);
            $srch->joinUser();
            $srch->addMultipleFields(['CONCAT(u.user_first_name," ",u.user_last_name) AS learner_name']);
        }
        if ($filter['status'] > -2) {
            $srch->addCondition('o.order_is_paid', '=', $filter['status']);
        }
        $page = (empty($filter['page']) || $filter['page'] <= 0) ? 1 : FatUtility::int($filter['page']);
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->addCondition('o.order_type', '=', self::TYPE_LESSON_BOOKING);
        if ($filter['keyword']) {
            $condition = $srch->addCondition('o.order_id', 'LIKE', '%' . $filter['keyword'] . '%');
            switch ($userType) {
                case User::USER_TYPE_LEANER:
                    $condition->attachCondition('CONCAT(`t`.`user_first_name`," ",`t`.`user_last_name`)', 'LIKE', '%' . $filter['keyword'] . '%', 'OR');
                    break;
                case User::USER_TYPE_TEACHER:
                    $condition->attachCondition('CONCAT(`u`.`user_first_name`," ",`u`.`user_last_name`)', 'LIKE', '%' . $filter['keyword'] . '%', 'OR');
                    break;
            }
        }
        $systemTimeZone = MyDate::getTimeZone();
        $user_timezone = MyDate::getUserTimeZone();
        $dateFrom = $filter['date_from'];
        if (!empty($dateFrom)) {
            $dateFrom = $dateFrom . ' 00:00:00';
            $dateFrom = MyDate::changeDateTimezone($dateFrom, $user_timezone, $systemTimeZone);
            $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
            $srch->addCondition('o.order_date_added', '>=', $dateFrom);
        }
        $dateTo = $filter['date_to'];
        if (!empty($dateTo)) {
            $dateTo = $dateTo . ' 23:59:59';
            $dateTo = MyDate::changeDateTimezone($dateTo, $user_timezone, $systemTimeZone);
            $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));
            $srch->addCondition('o.order_date_added', '<=', $dateTo);
        }
        $srch->addMultipleFields(['order_id',
            'order_user_id',
            'order_is_paid',
            'order_type',
            'order_net_amount',
            'order_is_wallet_selected',
            'order_wallet_amount_charge',
            'order_currency_code',
            'order_language_id',
            'order_date_added',
            'op_lpackage_is_free_trial'
        ]);
        $srch->addOrder('order_date_added', 'desc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $rs = $srch->getResultSet();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount()
        ];
        $dataArr['Orders'] = FatApp::getDb()->fetchAll($rs);
        $dataArr['pagingArr'] = $pagingArr;
        return $dataArr;
    }

    public function getOrderPayments($criteria = [])
    {
        if (count($criteria) == 0) {
            return [];
        }
        $srch = new SearchBase(static::DB_TBL_ORDER_PAYMENTS, 'opayment');
        foreach ($criteria as $key => $val) {
            if (strval($val) == '') {
                continue;
            }
            switch ($key) {
                case 'id':
                    $srch->addCondition('opayment.opayment_id', '=', intval($val));
                    break;
                case 'order_id':
                    $srch->addCondition('opayment.opayment_order_id', '=', $val);
                    break;
            }
        }
        $srch->doNotLimitRecords();
        $srch->doNotCalculateRecords(true);
        $srch->addOrder('opayment_id');
        $row = FatApp::getDb()->fetchAll($srch->getResultSet(), 'opayment_id');
        if ($row == false) {
            return [];
        }
        return $row;
    }

    public function getLessonsByOrderId($orderId)
    {
        $orderSearch = new OrderSearch();
        $orderSearch->addMultipleFields([
            'order_id',
            'order_is_paid',
            'order_user_id',
            'order_net_amount',
            'order_discount_total',
            'op_lpackage_is_free_trial'
        ]);
        $orderSearch->joinOrderProduct();
        $orderSearch->joinScheduledLessonDetail();
        $orderSearch->joinTable(ScheduledLesson::DB_TBL, 'LEFT OUTER JOIN', 'sl.slesson_id = sld.sldetail_slesson_id', 'sl');
        $orderSearch->addCondition('o.order_id', '=', $orderId);
        return $orderSearch;
    }

}
