<?php

class OrderProduct extends MyAppModel
{

    const DB_TBL = 'tbl_order_products';
    const DB_TBL_PREFIX = 'op_';
    const ORDER_PRODUCT_TYPE_LESSON = 0;
    const ORDER_PRODUCT_TYPE_GIFTCARD = 1;

    public function __construct($orderProductId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $orderProductId);
    }

    public function refund($qty, $amount)
    {
        $this->loadFromDb();
        $this->setFldValue('op_refund_qty', $this->getFldValue('op_refund_qty') + $qty);
        $this->setFldValue('op_total_refund_amount', $this->getFldValue('op_total_refund_amount') + $amount, true);
        return $this->save();
    }

    public static function isAlreadyPurchasedFreeTrial($learnerId, $teacherId)
    {
        $learnerId = FatUtility::int($learnerId);
        $teacherId = FatUtility::int($teacherId);
        if ($learnerId < 1 || $teacherId < 1) {
            trigger_error("Invalid Request", E_USER_ERROR);
        }
        $orderProductSearch = new OrderProductSearch(0, true, false);
        $orderProductSearch->joinOrders();
        $orderProductSearch->joinScheduleLessonDetails();
        $orderProductSearch->joinScheduleLesson(false);
        $orderProductSearch->setPageSize(1);
        $orderProductSearch->addCondition('order_user_id', '=', $learnerId);
        $orderProductSearch->addCondition('op_teacher_id', '=', $teacherId);
        $orderProductSearch->addCondition('op_lpackage_is_free_trial', '=', applicationConstants::YES);
        $orderProductSearch->addCondition('slesson_status', '!=', ScheduledLesson::STATUS_CANCELLED);
        $orderProductSearch->addMultipleFields(['op_id']);
        if (empty(FatApp::getDb()->fetch($orderProductSearch->getResultSet()))) {
            return false;
        }
        return true;
    }

}
