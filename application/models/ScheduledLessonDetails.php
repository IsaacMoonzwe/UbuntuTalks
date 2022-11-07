<?php

class ScheduledLessonDetails extends MyAppModel
{

    const DB_TBL = 'tbl_scheduled_lesson_details';
    const DB_TBL_PREFIX = 'sldetail_';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getSearchObj()
    {
        $srch = new ScheduledLessonDetailsSearch();
        return $srch;
    }

    public static function tblFld($key)
    {
        return static::DB_TBL_PREFIX . $key;
    }

    public function save()
    {
        if ($this->getMainTableRecordId() == 0) {
            $this->setFldValue('sldetail_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    public function changeStatus($status = 0)
    {
        $status = FatUtility::int($status);
        $this->setFldValue('sldetail_learner_status', $status);
        return parent::save();
    }

    public static function getDetailIdByOrderId($orderId)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::tblFld('order_id'), '=', $orderId);
        $srch->addFld(static::tblFld('id'));
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row))
            return 0;
        return $row[static::tblFld('id')];
    }

    public static function getScheduledSearchObj($recordId, $attr = null)
    {
        $recordId = FatUtility::convertToType($recordId, FatUtility::VAR_INT);
        $srch = self::getSearchObj();
        $srch->addCondition(static::tblFld('slesson_id'), '=', $recordId);
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        return $srch;
    }

    public static function getLessonDetailSearchObj(): SearchBase
    {
        $srch = self::getSearchObj();
        $srch->joinScheduledLesson();
        $srch->joinTeacher();
        $srch->joinOrder();
        $srch->joinLearner();
        $srch->joinTeacherCredentials();
        $srch->joinLearnerCredentials();
        $srch->joinLessonLanguage();
        $srch->addMultipleFields([
            'sldetail_id',
            'slesson_date',
            'slesson_end_date',
            'slesson_start_time',
            'slesson_end_time',
            'slesson_status',
            'slesson_id',
            'sldetail_learner_status',
            'ul.user_id as learnerId',
            'ut.user_id as teacherId',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'IFNULL(t_sl_l.tlanguage_name, tlang.tlanguage_identifier) as teacherTeachLanguageName',
        ]);
        return $srch;
    }

    public static function getScheduledRecordsByLessionId($recordId, $attr = null)
    {
        $db = FatApp::getDb();
        $srch = self::getScheduledSearchObj($recordId, $attr = null);
        $srch->joinScheduledLesson();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinOrder();
        $srch->joinOrderProduct();
        $srch->joinLearnerCredentials();
        $srch->joinLessonLanguage();
        $srch->addMultipleFields([
            'sldetail_id',
            'slesson_id',
            'slesson_date',
            'slesson_start_time',
            'slesson_end_time',
            'slesson_status',
            'op.op_lpackage_is_free_trial',
            'sldetail_learner_status',
            'ul.user_id as learnerId',
            'ut.user_id as teacherId',
            'tcred.credential_email as teacherEmailId',
            'ul.user_first_name as learnerFname',
            'ul.user_last_name as learnerLname',
            'lcred.credential_email as learnerEmailId',
            'ul.user_timezone as learnerTz',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'IFNULL(t_sl_l.tlanguage_name, tlang.tlanguage_identifier) as teacherTeachLanguageName',
        ]);
        $cnd = $srch->addCondition(static::tblFld('learner_status'), '=', ScheduledLesson::STATUS_SCHEDULED);
        $cnd->attachCondition(static::tblFld('learner_status'), '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        return $db->fetchAll($srch->getResultSet());
    }

    public static function getAttributesByLessionId($recordId, $attr = null)
    {
        $db = FatApp::getDb();
        $srch = self::getScheduledSearchObj($recordId, $attr = null);
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public static function getAttributesByLessonAndLearnerId($recordId, $learnerId, $attr = null)
    {
        $db = FatApp::getDb();
        $srch = self::getScheduledSearchObj($recordId, $attr = null);
        $srch->addCondition(static::tblFld('learner_id'), '=', $learnerId);
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public function getCancelledGroupClassByDetailId()
    {
        $sldetail_id = $this->getMainTableRecordId();
        if ($sldetail_id < 1) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $srch = new ScheduledLessonSearch(false);
        $srch->joinGroupClass($this->siteLangId);
        $srch->joinScheduledLessionDetails();
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->doNotCalculateRecords();
        $srch->addCondition('sld.sldetail_id', '=', $sldetail_id);
        $srch->addCondition('grpcls.grpcls_status', '=', TeacherGroupClasses::STATUS_CANCELLED,'OR');
        $srch->addCondition('grpcls.grpcls_status', '=', TeacherKidsClasses::STATUS_CANCELLED,'OR');
        $srch->addFld(['sldetail_order_id', 'order_net_amount']);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!$row) {
            $this->error = Label::getLabel('LBL_Invalid_Request');
            return false;
        }
        $order_id = $row['sldetail_order_id'];
        $orderObj = new Order();
        $order = $orderObj->getOrderById($order_id);
        if ($order == false) {
            $this->error = Label::getLabel('LBL_Error:_Please_perform_this_action_on_valid_record.');
            return false;
        }
        if ($order["order_is_paid"] == Order::ORDER_IS_CANCELLED) {
            $this->error = Label::getLabel('LBL_Already_Refunded');
            return false;
        }
        return $row;
    }

    public function refundToLearner($learner = false, $addCancelledLessonCount = false)
    {
        $db = FatApp::getDb();
        $srch = new ScheduledLessonSearch();
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->addCondition('sld.sldetail_id', ' = ', $this->getMainTableRecordId());
        $srch->addCondition('sld.sldetail_is_teacher_paid', '=', 0);
        $srch->addCondition('op.op_lpackage_is_free_trial', '=', 0);
        $data = $db->fetch($srch->getResultSet());
        if (empty($data)) {
            return true;
        }
        $utxn_comments = sprintf(Label::getLabel('LBL_LessonId:_%s_Refund_Payment', CommonHelper::getLangId()), $data['slesson_id']);
        $transactionType = Transaction::TYPE_LOADED_MONEY_TO_WALLET;
        //coupon order case
        $isDiscountApply = false;
        $data['order_discount_total'] = FatUtility::float($data['order_discount_total']);
        $data['order_net_amount'] = FatUtility::float($data['order_net_amount']);
        if ($learner && $data['order_discount_total'] > 0) {
            $orderObj = new Order;
            $orderSearch = $orderObj->getLessonsByOrderId($data['sldetail_order_id']);
            $orderSearch->addMultipleFields([
                'count(sld.sldetail_id) as totalLessons',
                'order_user_id',
                'order_id',
                'order_net_amount',
                'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ' THEN 1 ELSE 0 END) needToscheduledLessonsCount',
                'SUM(CASE WHEN sld.sldetail_learner_status = ' . ScheduledLesson::STATUS_CANCELLED . ' THEN 1 ELSE 0 END) canceledLessonsCount',
            ]);
            $orderSearch->addGroupBy('sld.sldetail_order_id');
            $resultSet = $orderSearch->getResultSet();
            $orderInfo = $db->fetch($resultSet);
            if (empty($orderInfo)) {
                $this->error = Label::getLabel('LBL_Invalid_Request');
                return false;
            }
            // plus 1 beacuse 1 lesson alredy marked cancelled in learner scheduled lesson controller
            $totalCanceledAndNeedToScheduledCount = $orderInfo['needToscheduledLessonsCount'];
            if ($addCancelledLessonCount) {
                $totalCanceledAndNeedToScheduledCount += $orderInfo['canceledLessonsCount'];
            }
            //if user buy order with discount and user scheduled his 1 or more Lessons he has not able to cancel the lesson
            if ($orderInfo['totalLessons'] != $totalCanceledAndNeedToScheduledCount) {
                $this->error = Label::getLabel('LBL_You_are_not_cancelled_the_lesson_becuase_you_purchase_the_lesson_with_coupon');
                return false;
            }
            $orderAssignValues = ['order_is_paid' => Order::ORDER_IS_CANCELLED];
            if (!$db->updateFromArray(Order::DB_TBL, $orderAssignValues, ['smt' => 'order_id = ?', 'vals' => [$orderInfo['order_id']]])) {
                $this->error = $db->getError();
                return false;
            }
            $coustomQuery = "UPDATE " . ScheduledLessonDetails::DB_TBL . " as sld INNER JOIN " . ScheduledLesson::DB_TBL . " as sl ON ( sl.slesson_id = sld.sldetail_slesson_id ) ";
            $coustomQuery .= " SET  sld.sldetail_learner_status = " . ScheduledLesson::STATUS_CANCELLED . " , sl.slesson_status = " . ScheduledLesson::STATUS_CANCELLED;
            $coustomQuery .= " where sldetail_order_id = '" . $orderInfo['order_id'] . "'";
            if (!$db->query($coustomQuery)) {
                $this->error = $db->getError();
                return false;
            }
            $formattedOrderId = "#" . $orderInfo["order_id"];
            $utxn_comments = Label::getLabel('LBL_Order_Refund:_{order-id}');
            $utxn_comments = str_replace("{order-id}", $formattedOrderId, $utxn_comments);
            $transactionType = Transaction::TYPE_ORDER_CANCELLED_REFUND;
            $isDiscountApply = true;
        }
        $diff = MyDate::hoursDiff($data['slesson_date'] . ' ' . $data['slesson_start_time']);
        $perUnitAmount = $data['op_unit_price'];
        if (!$learner && $data['order_discount_total'] > 0) {
            $perUnitAmount = round(($data['order_net_amount'] / $data['op_qty']), 2);
        }
        if ($learner && !$isDiscountApply && ($data['slesson_date'] != "0000-00-00") && ($diff < FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24))) {
            if ($data['slesson_grpcls_id'] > 0) {
                $perUnitAmount = (FatApp::getConfig('CONF_LEARNER_CLASS_REFUND_PERCENTAGE', FatUtility::VAR_INT, 10) * $perUnitAmount) / 100;
            } else {
                $perUnitAmount = (FatApp::getConfig('CONF_LEARNER_REFUND_PERCENTAGE', FatUtility::VAR_INT, 10) * $perUnitAmount) / 100;
            }
        }
        if ($learner && $data['order_discount_total'] > 0) {
            //  refund only need to scheduled lesson  ammount
            $perUnitAmount = round(($data['order_net_amount'] / $data['op_qty']), 2);
            if ($addCancelledLessonCount) {
                $orderInfo['needToscheduledLessonsCount'] += 1;
            }
            $perUnitAmount = round(($perUnitAmount * $orderInfo['needToscheduledLessonsCount']), 2);
        }
        $opObj = new OrderProduct($data['op_id']);
        $opObj->refund(1, $perUnitAmount);
        $tObj = new Transaction($data['sldetail_learner_id']);
        $data = [
            'utxn_user_id' => $data['sldetail_learner_id'],
            'utxn_date' => date('Y-m-d H:i:s'),
            'utxn_comments' => $utxn_comments,
            'utxn_status' => Transaction::STATUS_COMPLETED,
            'utxn_type' => $transactionType,
            'utxn_credit' => $perUnitAmount
        ];
        if (!$tObj->addTransaction($data)) {
            trigger_error($tObj->getError(), E_USER_ERROR);
            return false;
        }
        return true;
    }

    public function markLearnerJoinTime()
    {
        $this->assignValues(['sldetail_learner_join_time' => date('Y-m-d H:i:s')]);
        return $this->save();
    }

}
