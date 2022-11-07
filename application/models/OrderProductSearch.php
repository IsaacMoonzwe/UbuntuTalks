<?php

class OrderProductSearch extends SearchBase
{

    public function __construct($langId = 0, $doNotCalculateRecords = true, $doNotLimitRecords = true)
    {
        parent::__construct(OrderProduct::DB_TBL, 'op');
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $this->joinTable(OrderProduct::DB_TBL . '_lang', 'LEFT OUTER JOIN', 'op.op_id = opl.oplang_op_id AND opl.oplang_lang_id = ' . $langId, 'opl');
        }
        if (true === $doNotCalculateRecords) {
            $this->doNotCalculateRecords();
        }
        if (true === $doNotLimitRecords) {
            $this->doNotLimitRecords();
        }
    }

    public function joinOrders()
    {
        $this->joinTable(Order::DB_TBL, 'INNER JOIN', 'op.op_order_id = o.order_id', 'o');
    }

    public function joinScheduleLessonDetails()
    {
        $this->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'o.order_id = sld.sldetail_order_id', 'sld');
    }

    public function joinScheduleLesson($addTeacherPaidCondition = true)
    {
        $onCondition = 'sld.sldetail_slesson_id = sl.slesson_id';
        if ($addTeacherPaidCondition) {
            $onCondition .= ' AND sld.sldetail_is_teacher_paid = ' . applicationConstants::YES;
        }
        $this->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', $onCondition, 'sl');
    }

    public function addOrderIdCondition($orderId)
    {
        $this->addCondition('op_order_id', '=', $orderId);
    }

    public function joinGiftcards()
    {
        $this->joinTable(Giftcard::DB_TBL, 'LEFT OUTER JOIN', 'gift.giftcard_op_id = op.op_id', 'gift');
    }

    public function joinRecipientUser()
    {
        $this->joinTable(Giftcard::DB_TBL_GIFTCARD_RECIPIENT, 'LEFT OUTER JOIN', 'gcrecipient.gcrecipient_op_id = op.op_id', 'gcrecipient');
    }

    public function joinGiftCardBuyer()
    {
        $this->joinTable(Giftcard::DB_TBL_GIFTCARD_BUYER, 'LEFT OUTER JOIN', 'gcbuyer.gcbuyer_op_id = op.op_id', 'gcbuyer');
    }

    public function addCountsOfOrderedProducts()
    {
        $srch = new SearchBase(OrderProduct::DB_TBL, 'temp_op');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addGroupBy('temp_op.op_order_id');
        $srch->addMultipleFields(['temp_op.op_order_id', "count(temp_op.op_order_id) as totCombinedOrders"]);
        $qryCombinedOrders = $srch->getQuery();
        $this->joinTable('(' . $qryCombinedOrders . ')', 'LEFT OUTER JOIN', 'op.op_order_id = co.op_order_id', 'co');
    }

}
