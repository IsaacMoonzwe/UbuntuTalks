<?php

class Transaction extends MyAppModel
{

    const DB_TBL = 'tbl_user_transactions';
    const DB_TBL_PREFIX = 'utxn_';
    const STATUS_PENDING = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_REFUND = 2;
    const STATUS_DECLINED = 3;
    const WITHDRAWL_STATUS_PENDING = 0;
    const WITHDRAWL_STATUS_COMPLETED = 1;
    const WITHDRAWL_STATUS_APPROVED = 2;
    const WITHDRAWL_STATUS_DECLINED = 3;
    const WITHDRAWL_STATUS_PAYOUT_SENT = 4;
    const WITHDRAWL_STATUS_PAYOUT_FAILED = 5;
    const TYPE_LESSON_BOOKING = 1;
    const TYPE_GIFTCARD_REDEEM_TO_WALLET = 2;
    const TYPE_LOADED_MONEY_TO_WALLET = 3;
    const TYPE_MONEY_WITHDRAWN = 4;
    const TYPE_ISSUE_REFUND = 5;
    const TYPE_ORDER_CANCELLED_REFUND = 6;
    const CREDIT_TYPE = 1;
    const DEBIT_TYPE = 2;

    protected $userId;

    public function __construct($uid, $utxnId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $utxnId);
        $uid = FatUtility::int($uid);
        $this->userId = $uid;
    }

    public static function getSearchObject($doNotCalculateRecords = true, $doNotLimitRecords = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'utxn');
        if (true === $doNotCalculateRecords) {
            $srch->doNotCalculateRecords();
        }
        if (true === $doNotLimitRecords) {
            $srch->doNotLimitRecords();
        }
        return $srch;
    }

    public static function getStatusArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::STATUS_PENDING => Label::getLabel('LBL_Pending', $langId),
            static::STATUS_COMPLETED => Label::getLabel('LBL_Completed', $langId),
            static::STATUS_REFUND => Label::getLabel('LBL_Refunded', $langId),
            static::STATUS_DECLINED => Label::getLabel('LBL_Declined', $langId)
        ];
    }

    public static function getWithdrawlStatusArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId == 0) {
            trigger_error(Label::getLabel('MSG_Language_Id_not_specified.', $langId), E_USER_ERROR);
        }
        $arr = [
            static::WITHDRAWL_STATUS_PENDING => Label::getLabel('LBL_Withdrawal_Request_Pending', $langId),
            static::WITHDRAWL_STATUS_COMPLETED => Label::getLabel('LBL_Withdrawal_Request_Completed', $langId),
            static::WITHDRAWL_STATUS_APPROVED => Label::getLabel('LBL_Withdrawal_Request_Approved', $langId),
            static::WITHDRAWL_STATUS_DECLINED => Label::getLabel('LBL_Withdrawal_Request_Declined', $langId),
            static::WITHDRAWL_STATUS_PAYOUT_SENT => Label::getLabel('LBL_Withdrawal_Payout_Sent', $langId),
            static::WITHDRAWL_STATUS_PAYOUT_FAILED => Label::getLabel('LBL_Withdrawal_Payout_Failed', $langId)
        ];
        return $arr;
    }

    public function save()
    {
        $this->setFldValue('utxn_date', date('Y-m-d H:i:s'));
        return parent::save();
    }

    public static function formatTransactionCommentByOrderId($orderId, $langId = 0)
    {
        $formattedOrderValue = " #" . $orderId;
        $langId = FatUtility::int($langId);
        $str = Label::getLabel('LBL_ORDER_PLACED_{order-id}', $langId);
        return str_replace('{order-id}', $formattedOrderValue, $str);
    }

    public static function getCreditDebitTypeArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId == 0) {
            trigger_error(Label::getLabel('MSG_Language_Id_not_specified.', $langId), E_USER_ERROR);
        }
        $arr = [
            static::CREDIT_TYPE => Label::getLabel('LBL_Credit', $langId),
            static::DEBIT_TYPE => Label::getLabel('LBL_Debit', $langId)
        ];
        return $arr;
    }

    public function addTransaction($data)
    {
        if ($this->userId < 1) {
            trigger_error(Label::getLabel('MSG_INVALID_REQUEST', $this->commonLangId), E_USER_ERROR);
            return false;
        }
        $data['utxn_date'] = date('Y-m-d H:i:s');
        $this->assignValues($data);
        if (!$this->save()) {
            return false;
        }
        return $this->getMainTableRecordId();
    }

    public function getTransactionSummary()
    {
        $srch = static::getSearchObject();
        if ($this->userId > 0) {
            $srch->addCondition('utxn.utxn_user_id', '=', $this->userId);
        }
        if ($this->mainTableRecordId > 0) {
            $srch->addCondition('utxn.utxn_id', '=', $this->mainTableRecordId);
        }
        $srch->addMultipleFields(['IFNULL(SUM(utxn.utxn_credit),0) AS total_earned', 'IFNULL(SUM(utxn.utxn_debit),0) AS total_used']);
        $srch->doNotCalculateRecords();
        $srch->doNotlimitRecords();
        $srch->addCondition('utxn_status', '=', applicationConstants::ACTIVE);
        $rs = $srch->getResultSet();
        if (!$rs) {
            trigger_error($srch->getError(), E_USER_ERROR);
        }
        return $row = FatApp::getDb()->fetch($rs);
    }

    public function getAttributesWithUserInfo($userId = 0, $attr = null)
    {
        $userId = FatUtility::int($userId);
        $srch = static::getSearchObject();
        $srch->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'u.user_id = utxn.utxn_user_id', 'u');
        $srch->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'c.credential_user_id = u.user_id', 'c');
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        if ($this->mainTableRecordId > 0) {
            $srch->addCondition('utxn.utxn_id', '=', $this->mainTableRecordId);
        }
        if ($userId > 0) {
            $srch->addCondition('utxn.utxn_user_id', '=', $userId);
        }
        $rs = $srch->getResultSet();
        if ($this->mainTableRecordId > 0) {
            $row = FatApp::getDb()->fetch($rs);
        } else {
            $row = FatApp::getDb()->fetchAll($rs, 'utxn_id');
        }
        if (!empty($row)) {
            return $row;
        }
        return [];
    }

    public static function formatTransactionNumber($txnId)
    {
        $newValue = str_pad($txnId, 7, '0', STR_PAD_LEFT);
        $newValue = "TN" . "-" . $newValue;
        return $newValue;
    }

    public static function formatTransactionComments($txnComments)
    {
        $strComments = $txnComments;
        $strComments = preg_replace('/<\/?a[^>]*>/', '', $strComments);
        return html_entity_decode($strComments);
    }

    public function getAttributesBywithdrawlId($withdrawalId, $attr = null)
    {
        $withdrawalId = FatUtility::int($withdrawalId);
        if (1 > $withdrawalId) {
            trigger_error(Label::getLabel('MSG_INVALID_REQUEST', $this->commonLangId), E_USER_ERROR);
            return false;
        }
        $srch = static::getSearchObject();
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $srch->addCondition('utxn.utxn_withdrawal_id', '=', $withdrawalId);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!empty($row)) {
            return $row;
        }
        return false;
    }

    public static function transactionDetailsWithLesson($lessonId)
    {
        $srch = self::getSearchObject();
        $srch->addCondition('utxn_slesson_id', '=', $lessonId);
        $srch->addCondition('utxn_user_id', '=', UserAuthentication::getLoggedUserId());
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'utxn_slesson_id = sld.sldetail_slesson_id', 'sld');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sld.sldetail_slesson_id = slsn.slesson_id', 'slsn');
        $srch->joinTable(Order::DB_TBL, 'LEFT JOIN', 'sld.sldetail_order_id = o.order_id', 'o');
        $srch->joinTable(OrderProduct::DB_TBL, 'LEFT JOIN', 'o.order_id = op.op_order_id', 'op');
        $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'ut.user_id = slsn.slesson_teacher_id', 'ut');
        $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'ul.user_id = sld.sldetail_learner_id', 'ul');
        $srch->joinTable(User::DB_TBL_CRED, 'LEFT JOIN', 'lcred.credential_user_id = ul.user_id', 'lcred');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'tLang.tlanguage_id = slsn.slesson_slanguage_id', 'tLang');
        $srch->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tLangLang.tlanguagelang_tlanguage_id = tLang.tlanguage_id AND tlanguagelang_lang_id = ' . CommonHelper::getLangId(), 'tLangLang');
        $srch->addFld([
            'utxn.*', 'slsn.*', 'sld.*',
            'o.order_net_amount as order_total',
            'op.op_qty as total_lessons',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'lcred.credential_email as learner_email',
            'ul.user_timezone as lerner_timezone',
            'IFNULL(tLangLang.tlanguage_name, tLang.tlanguage_identifier) as teacherTeachLanguageName'
        ]);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public function changeStatusByLessonId($lessonId, $status)
    {
        $db = FatApp::getDb();
        if (!$db->updateFromArray(self::DB_TBL, ['utxn_status' => $status], ['smt' => 'utxn_slesson_id = ?', 'vals' => [$lessonId]])) {
            return false;
        }
        return true;
    }

    public static function updateTransactionFeeMessage(int $withdrawalId, $fee)
    {
        $chargedNote = Label::getLabel('MSG_Transaction_Fee_Charged_:') . ' ' . CommonHelper::displayMoneyFormat($fee, true, true);
        $note = "<br /><small class=\"transaction-fee\">" . $chargedNote . "</small>";
        $query = "UPDATE " . Transaction::DB_TBL . " set `utxn_comments` = CONCAT(`utxn_comments`,' ', '" . $note . "') where utxn_withdrawal_id = " . $withdrawalId;
        FatApp::getDb()->query($query);
    }

}
