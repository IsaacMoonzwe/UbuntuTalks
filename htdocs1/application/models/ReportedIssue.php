<?php

class ReportedIssue extends MyAppModel
{

    const DB_TBL = 'tbl_reported_issues';
    const DB_TBL_PREFIX = 'repiss_';
    const DB_TBL_LOG = 'tbl_reported_issues_log';
    const DB_TBL_LOG_PREFIX = 'reislo_';
    /* Issue Status */
    const STATUS_PROGRESS = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_ESCLATED = 3;
    const STATUS_CLOSED = 4;
    /* Issue Actions */
    const ACTION_RESET_AND_UNSCHEDULED = 1;
    const ACTION_COMPLETE_ZERO_REFUND = 2;
    const ACTION_COMPLETE_HALF_REFUND = 3;
    const ACTION_COMPLETE_FULL_REFUND = 4;
    const ACTION_ESCLATE_TO_ADMIN = 5;
    /* Action User Types */
    const USER_TYPE_LEARNER = 1;
    const USER_TYPE_TEACHER = 2;
    const USER_TYPE_SUPPORT = 3;
    const ISSUE_REPORTED_NOTIFICATION = 1;
    const ISSUE_RESOLVE_NOTIFICATION = 2;

    private $userId;
    private $userType;

    public function __construct($id = 0, $userId = 0, $userType = 0)
    {
        $this->userId = FatUtility::int($userId);
        $this->userType = FatUtility::int($userType);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(['repiss_status']);
    }

    /**
     * Setup Issue
     * 
     * @param int $sldetailId
     * @param int $titleId
     * @param string $comment
     * @return bool
     */
    public function setupIssue(int $sldetailId, int $titleId, string $comment): bool
    {
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
            return false;
        }
        $slessonId = ScheduledLessonDetails::getAttributesById($sldetailId, 'sldetail_slesson_id');
        $options = IssueReportOptions::getOptionsArray($this->commonLangId, User::USER_TYPE_LEANER);
        $this->setFldValue('repiss_status', static::STATUS_PROGRESS);
        $this->assignValues([
            'repiss_comment' => $comment,
            'repiss_slesson_id' => $slessonId,
            'repiss_sldetail_id' => $sldetailId,
            'repiss_reported_by' => $this->userId,
            'repiss_reported_on' => date('Y-m-d H:i:s'),
            'repiss_title' => $options[$titleId] ?? 'NA'
        ]);
        if (!$this->save()) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$this->sendUserNotification($this->getMainTableRecordId())) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
            $db->rollbackTransaction();
            return false;
        }
        return true;
    }

    /**
     * Setup Issue Action
     * 
     * Step 1. Add reported issue log 
     * Step 2. Set issue status & refund percentage
     * Step 3. Transaction settlement for refund percentage
     * Step 4. Update issue status and datetime
     * Step 5. Mark lesson detail record as paid
     * 
     * @param int $action
     * @param string $comment
     * @param bool $closed
     * @return bool
     */
    public function setupIssueAction(int $action, string $comment, bool $closed = false): bool
    {
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
            return false;
        }
        /* Add reported issue log */
        $issueId = $this->getMainTableRecordId();
        $record = new TableRecord(ReportedIssue::DB_TBL_LOG);
        $record->assignValues([
            'reislo_action' => $action,
            'reislo_comment' => $comment,
            'reislo_repiss_id' => $issueId,
            'reislo_added_by' => $this->userId,
            'reislo_added_on' => date('Y-m-d H:i:s'),
            'reislo_added_by_type' => $this->userType
        ]);
        if (!$record->addNew()) {
            $this->error = $record->getError();
            $db->rollbackTransaction();
            return false;
        }
        /* Set issue status & refund percentage */
        $status = static::STATUS_RESOLVED;
        $refund = 0;
        $isTeacherPaid = 1;
        switch ($action) {
            case static::ACTION_COMPLETE_HALF_REFUND:
                $refund = 50;
                break;
            case static::ACTION_COMPLETE_FULL_REFUND:
                $refund = 100;
                break;
            case static::ACTION_RESET_AND_UNSCHEDULED:
                $isTeacherPaid = 0;
                $comment = Label::getLabel('LBL_CLASS_UNSCHEDULED');
                break;
            case static::ACTION_ESCLATE_TO_ADMIN:
                $status = static::STATUS_ESCLATED;
                break;
        }
        if ($closed) {
            $status = static::STATUS_CLOSED;
        }
        /* Transaction settlement for refund percentage */
        if ($closed && !$this->executeLessonTransactions($issueId, $refund)) {
            $db->rollbackTransaction();
            return false;
        }
        /* Update issue status and datetime */
        $this->setFldValue('repiss_status', $status);
        $this->setFldValue('repiss_updated_on', date('Y-m-d H:i:s'));
        if (!$this->save()) {
            $db->rollbackTransaction();
            return false;
        }
        /* Mark lesson detail record as paid */
        if ($status == static::STATUS_CLOSED) {
            $sldetailId = static::getAttributesById($issueId, 'repiss_sldetail_id');
            $whr = ['smt' => 'sldetail_id = ?', 'vals' => [$sldetailId]];
            $fields = ['sldetail_is_teacher_paid' => $isTeacherPaid];
            $record = new TableRecord(ScheduledLessonDetails::DB_TBL);
            if ($action == static::ACTION_RESET_AND_UNSCHEDULED) {
                $fields = array_merge($fields, [
                    'sldetail_learner_join_time' => '0000-00-00 00:00:00',
                    'sldetail_learner_end_time' => '0000-00-00 00:00:00',
                    'sldetail_learner_status' => ScheduledLESSON::STATUS_NEED_SCHEDULING
                ]);
            }
            $record->setFlds($fields);
            if (!$record->update($whr)) {
                $this->error = $record->getError();
                $db->rollbackTransaction();
                return false;
            }
            if ($action == static::ACTION_RESET_AND_UNSCHEDULED) {
                $slessonId = ScheduledLessonDetails::getAttributesById($sldetailId, 'sldetail_slesson_id');
                $whr = ['smt' => 'slesson_id = ?', 'vals' => [$slessonId]];
                $record = new TableRecord(ScheduledLesson::DB_TBL);
                $record->setFlds([
                    'slesson_date' => '0000-00-00',
                    'slesson_end_date' => '0000-00-00',
                    'slesson_start_time' => '00:00:00',
                    'slesson_end_time' => '00:00:00',
                    'slesson_teacher_join_time' => '00:00:00',
                    'slesson_teacher_end_time' => '00:00:00',
                    'slesson_status' => ScheduledLESSON::STATUS_NEED_SCHEDULING,
                ]);
                if (!$record->update($whr)) {
                    $this->error = $record->getError();
                    $db->rollbackTransaction();
                    return false;
                }
                /* @todo add Rescheduled log */
            }
        }
        if (!$this->sendUserNotification($this->getMainTableRecordId())) {
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
            $db->rollbackTransaction();
            return false;
        }
        return true;
    }

    /**
     * Execute Lesson Transactions
     * 
     * @param int $issueId
     * @param int $percent
     * @return bool
     */
    private function executeLessonTransactions(int $issueId, int $percent): bool
    {
        if ($percent == 0) {
            return true;
        }
        /* Get Issue detail */
        $issue = static::getIssueById($issueId);
        if (empty($issue)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        /* 100% Refund to student */
        if ($percent == 100) {
            $txn = new Transaction($issue['sldetail_learner_id']);
            $data = [
                'utxn_date' => date('Y-m-d H:i:s'),
                'utxn_slesson_id' => $issue['slesson_id'],
                'utxn_credit' => $issue['op_unit_price'],
                'utxn_user_id' => $issue['sldetail_learner_id'],
                'utxn_comments' => 'Refund of Lesson ' . $issue['slesson_id'],
                'utxn_status' => Transaction::STATUS_COMPLETED,
                'utxn_type' => Transaction::TYPE_ISSUE_REFUND,
            ];
            if (!$txn->addTransaction($data)) {
                $this->error = $txn->getError();
                return false;
            }
            $oprod = new OrderProduct($issue['op_id']);
            if (!$oprod->refund(1, $issue['op_unit_price'])) {
                $this->error = $oprod->getError();
                return false;
            }
            if (!$this->sendRefundNotification($issue['sldetail_learner_id'], $issue['slesson_id'])) {
                return false;
            }
            return true;
        }
        if ($percent == 50) {
            /* 50% Refund to Student */
            $txn = new Transaction($issue['sldetail_learner_id']);
            $data = [
                'utxn_date' => date('Y-m-d H:i:s'),
                'utxn_slesson_id' => $issue['slesson_id'],
                'utxn_credit' => $issue['op_unit_price'] / 2,
                'utxn_user_id' => $issue['sldetail_learner_id'],
                'utxn_status' => Transaction::STATUS_COMPLETED,
                'utxn_type' => Transaction::TYPE_ISSUE_REFUND,
                'utxn_comments' => 'Refund of Lesson ' . $issue['slesson_id'],
            ];
            if (!$txn->addTransaction($data)) {
                $this->error = $txn->getError();
                return false;
            }
            /* 50% Payment to Teaher */
            $teacherPercent = (100 - $issue['op_commission_percentage']) / 2;
            $teacherPayment = ($teacherPercent * $issue['op_unit_price']) / 100;
            $txn = new Transaction($issue['slesson_teacher_id']);
            $data = [
                'utxn_date' => date('Y-m-d H:i:s'),
                'utxn_slesson_id' => $issue['slesson_id'],
                'utxn_credit' => $teacherPayment,
                'utxn_user_id' => $issue['slesson_teacher_id'],
                'utxn_comments' => 'Payment of Lesson ' . $issue['slesson_id'],
                'utxn_status' => Transaction::STATUS_COMPLETED,
                'utxn_type' => Transaction::TYPE_LESSON_BOOKING,
            ];
            if (!$txn->addTransaction($data)) {
                $this->error = $txn->getError();
                return false;
            }
            $oprod = new OrderProduct($issue['op_id']);
            if (!$oprod->refund(1, $issue['op_unit_price'] / 2)) {
                $this->error = $oprod->getError();
                return false;
            }
            if (!$this->sendRefundNotification($issue['sldetail_learner_id'], $issue['slesson_id'])) {
                return false;
            }
            $record = new UserNotifications($issue['slesson_teacher_id']);
            if (!$record->sendWalletCreditNotification($issue['slesson_id'])) {
                $this->error = $record->getError();
                return false;
            }
        }
        return true;
    }

    private function sendRefundNotification($userId, $lessonId)
    {
        $record = new UserNotifications($userId);
        $record->sendNotifcationMetaData(UserNotifications::NOTICATION_FOR_ISSUE_REFUNDED, $lessonId);
        $title = Label::getLabel("LBL_REFUND_GRANTED_FOR_ISSUE_TITLE");
        $description = Label::getLabel("LBL_REFUND_GRANTED_FOR_ISSUE_DESCRIPTION");
        if (!$record->addNotification($title, $description)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    private function sendUserNotification($issueId)
    {
        $srch = new SearchBase(ScheduledLessonDetails::DB_TBL, 'sldetail');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'slesson.slesson_id=sldetail.sldetail_slesson_id', 'slesson');
        $srch->joinTable(ReportedIssue::DB_TBL, 'INNER JOIN', 'repiss.repiss_sldetail_id=sldetail.sldetail_id', 'repiss');
        $srch->addMultipleFields(['repiss_status', 'slesson_id', 'slesson_teacher_id', 'sldetail_learner_id', 'sldetail_id']);
        $srch->addCondition('repiss.repiss_id', '=', $issueId);
        $srch->doNotCalculateRecords();
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($row)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $userId = 0;
        $notiType = 0;
        $title = '';
        $lessonId = $row['slesson_id'];
        $description = '';
        switch ($row['repiss_status']) {
            case static::STATUS_PROGRESS:
                $userId = $row['slesson_teacher_id'];
                $notiType = UserNotifications::NOTICATION_FOR_ISSUE_REPORTED;
                $title = Label::getLabel('LBL_ISSUE_REPORTED_NOTIFICATION_TITLE');
                $description = Label::getLabel('LBL_ISSUE_REPORTED_NOTIFICATION_DETAIL');
                break;
            case static::STATUS_RESOLVED:
                $userId = $row['sldetail_learner_id'];
                $lessonId = $row['sldetail_id'];
                $notiType = UserNotifications::NOTICATION_FOR_ISSUE_RESOLVED;
                $title = Label::getLabel('LBL_ISSUE_RESOLVED_NOTIFICATION_TITLE');
                $description = Label::getLabel('LBL_ISSUE_RESOLVED_NOTIFICATION_DETAIL');
                break;
            case static::STATUS_ESCLATED:
                $userId = $row['slesson_teacher_id'];
                $notiType = UserNotifications::NOTICATION_FOR_ISSUE_ESCLATED;
                $title = Label::getLabel('LBL_ISSUE_ESCLATED_NOTIFICATION_TITLE');
                $description = Label::getLabel('LBL_ISSUE_ESCLATED_NOTIFICATION_DETAIL');
                break;
            case static::STATUS_CLOSED:
                $userId = $row['sldetail_learner_id'];
                $lessonId = $row['sldetail_id'];
                $notiType = UserNotifications::NOTICATION_FOR_ISSUE_CLOSED;
                $title = Label::getLabel('LBL_ISSUE_CLOSED_NOTIFICATION_TITLE');
                $description = Label::getLabel('LBL_ISSUE_CLOSED_NOTIFICATION_DETAIL');
                break;
            default:
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
        }
        $record = new UserNotifications($userId);
        $record->sendNotifcationMetaData($notiType, $lessonId);
        if (!$record->addNotification($title, $description)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Issue Info By Id
     * 
     * @param int $issueId
     * @return mixed<array|null>
     */
    public static function getIssueById(int $issueId)
    {
        $adminLangId = CommonHelper::getLangId();
        $srch = ReportedIssue::getSearchObject();
        $srch->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'slesson.slesson_teacher_id = us.us_user_id', 'us');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'slesson.slesson_slanguage_id = tlang.tlanguage_id', 'tlang');
        $srch->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'sll.tlanguagelang_tlanguage_id = tlang.tlanguage_id AND sll.tlanguagelang_lang_id = ' . $adminLangId, 'sll');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_learner_id = ul.user_id', 'ul');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'slesson.slesson_teacher_id = ut.user_id', 'ut');
        $srch->addMultipleFields([
            'repiss.repiss_id', 'repiss.repiss_title', 'repiss.repiss_sldetail_id', 'repiss.repiss_reported_on',
            'repiss.repiss_reported_by', 'repiss.repiss_status', 'repiss.repiss_comment', 'repiss.repiss_updated_on', "us.*",
            'sldetail.sldetail_order_id', 'sldetail.sldetail_learner_id', 'sldetail.sldetail_learner_join_time', 'slesson.slesson_id',
            'slesson.slesson_teacher_join_time', 'sldetail.sldetail_learner_end_time', 'slesson.slesson_teacher_id', 'slesson.slesson_grpcls_id',
            'slesson.slesson_teacher_end_time', 'slesson.slesson_ended_by', 'slesson.slesson_ended_on',
            'IFNULL(sll.tlanguage_name, tlang.tlanguage_identifier) as tlanguage_name',
            'CONCAT(user.user_first_name, " ", user.user_last_name) AS reporter_username',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) AS learner_username',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) AS teacher_username',
            'orders.order_net_amount', 'orders.order_id', 'orders.order_discount_total', 'op.op_commission_percentage',
            'op.op_id', 'op.op_qty', 'op.op_lpackage_is_free_trial', 'op.op_unit_price',
        ]);
        $srch->addCondition('repiss.repiss_id', '=', FatUtility::int($issueId));
        $srch->addGroupBy('repiss.repiss_id');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Issue Logs Issue ById
     * 
     * @param int $issueId
     * @return array
     */
    public static function getIssueLogsById(int $issueId): array
    {
        $srch = new SearchBase(ReportedIssue::DB_TBL_LOG, 'reislo');
        $srch->joinTable(User::DB_TBL, 'LEFT JOIN', 'user.user_id=reislo.reislo_added_by and reislo.reislo_added_by_type IN (1,2)', 'user');
        $srch->joinTable('tbl_admin', 'LEFT JOIN', 'admin.admin_id=reislo.reislo_added_by and reislo.reislo_added_by_type IN (3)', 'admin');
        $srch->addCondition('reislo_repiss_id', '=', FatUtility::int($issueId));
        $srch->addMultipleFields([
            'reislo_repiss_id', 'reislo_action', 'reislo_comment', 'reislo_added_on', 'reislo_added_by', 'reislo_added_by_type',
            'CASE WHEN reislo_added_by_type = 3 THEN admin.admin_name ELSE CONCAT(user.user_first_name, " ", user.user_last_name) END as user_fullname'
        ]);
        $srch->addOrder('reislo.reislo_id', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL, 'repiss');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_id=repiss.repiss_sldetail_id', 'sldetail');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'slesson.slesson_id=sldetail.sldetail_slesson_id', 'slesson');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id=sldetail.sldetail_order_id', 'orders');
        $srch->joinTable('tbl_order_products', 'INNER JOIN', 'op.op_order_id=orders.order_id', 'op');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'repiss.repiss_reported_by=user.user_id', 'user');
        return $srch;
    }

    public function getLessonToReport(int $sldetailId)
    {
        $reportHours = FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION');
        if ($reportHours < 1) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $srch = new SearchBase('tbl_scheduled_lesson_details', 'sldetail');
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'slesson.slesson_id=sldetail.sldetail_slesson_id', 'slesson');
        $srch->addMultipleFields([
            'slesson_end_date',
            'slesson_end_time',
            'sldetail.sldetail_learner_status',
            'slesson_teacher_join_time',
            'sldetail_learner_join_time', 'sldetail_learner_end_time'
        ]);
        $srch->addCondition('sldetail.sldetail_learner_id', '=', $this->userId);
        $srch->addCondition('sldetail.sldetail_id', '=', $sldetailId);
        $srch->addCondition('sldetail.sldetail_learner_status', 'IN', [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_COMPLETED]);
        $srch->addCondition('mysql_func_CONCAT(slesson.slesson_date," ", slesson.slesson_start_time)', '<=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $lesson = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($lesson)) {
            $this->error = Label::getLabel('LBL_INVALID_REQUEST');
            return false;
        }
        $lessonEnddate = $lesson['slesson_end_date'] . ' ' . $lesson['slesson_end_time'];
        $lessonReportDate = strtotime($lessonEnddate . " +" . $reportHours . " hour");
        if ($lessonReportDate <= time()) {
            $this->error = Label::getLabel('LBL_REPORT_TIME_EXPIRED');
            return false;
        }
        $srch = new SearchBase('tbl_reported_issues');
        $srch->addCondition('repiss_reported_by', '=', $this->userId);
        $srch->addCondition('repiss_sldetail_id', '=', $sldetailId);
        $srch->setPageSize(1);
        $srch->getResultSet();
        if ($srch->recordCount() > 0) {
            $this->error = Label::getLabel('LBL_ALREADY_REPORTED_ISSUE');
            return false;
        }
        if (($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_COMPLETED) ||
                ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED &&
                time() > strtotime($lessonEnddate) && $lesson['slesson_teacher_join_time'] == '0000-00-00 00:00:00')
        ) {
            return $lesson;
        }
        $this->error = Label::getLabel('LBL_INVALID_REQUEST');
        return false;
    }

    /**
     * Resolved Issue Transaction Settlement cronjob
     * @return boolean
     */
    public static function resolvedIssueSettlement()
    {
        $hours = FatApp::getConfig('CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION');
        $srch = new SearchBase(ReportedIssue::DB_TBL, 'repiss');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_id=repiss.repiss_sldetail_id', 'sldetail');
        $srch->addMultipleFields(['repiss.repiss_id', 'sldetail.sldetail_id', 'sldetail.sldetail_is_teacher_paid']);
        $srch->addDirectCondition('DATE_ADD(repiss.repiss_updated_on, INTERVAL ' . $hours . ' HOUR) < NOW()', 'AND');
        $srch->addCondition('repiss.repiss_status', '=', ReportedIssue::STATUS_RESOLVED);
        $srch->addCondition('sldetail.sldetail_is_teacher_paid', '=', 0);
        $srch->addOrder('repiss.repiss_id', 'ASC');
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($issue = FatApp::getDb()->fetch($resultSet)) {
            $srch = new SearchBase('tbl_reported_issues_log');
            $srch->addCondition('reislo_repiss_id', '=', $issue['repiss_id']);
            $srch->addOrder('reislo_id', 'DESC');
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            $log = FatApp::getDb()->fetch($srch->getResultSet());
            $repIssue = new ReportedIssue($issue['repiss_id'], 1, ReportedIssue::USER_TYPE_SUPPORT);
            $comment = ($log['reislo_action'] != static::ACTION_RESET_AND_UNSCHEDULED) ? Label::getLabel('RESOLVED_ISSUE_TRANSACTION') : Label::getLabel('LBL_CLASS_UNSCHEDULED');
            if (!$repIssue->setupIssueAction($log['reislo_action'], $comment, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Completed Lesson Transaction Settlement cronjob
     * 
     * Step 1. Get completed lesson without issue
     * Step 2. Add payment to teacher's wallet
     * Step 3. Mark lesson detail record as paid
     * 
     * @return boolean
     */
    public function completedLessonSettlement()
    {
        /* Get completed lesson without issue */
        $hours = FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION');
        $srch = new SearchBase(ScheduledLesson::DB_TBL, 'slesson');
        $srch->joinTable(ScheduledLessonDetails::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_slesson_id=slesson.slesson_id', 'sldetail');
        $srch->joinTable(ReportedIssue::DB_TBL, 'LEFT JOIN', 'repiss.repiss_sldetail_id=sldetail.sldetail_id', 'repiss');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id=sldetail.sldetail_order_id', 'orders');
        $srch->joinTable('tbl_order_products', 'INNER JOIN', 'op.op_order_id=orders.order_id', 'op');
        $srch->addMultipleFields([
            'sldetail.sldetail_id', 'sldetail.sldetail_is_teacher_paid', 'orders.order_id',
            'slesson.slesson_id', 'slesson.slesson_teacher_id', 'op.op_unit_price', 'op.op_commission_percentage'
        ]);
        if ($hours > 0) {
            $srch->addDirectCondition('DATE_ADD(slesson_teacher_end_time, INTERVAL ' . $hours . ' HOUR) < NOW()', 'AND');
        } else {
            $srch->addDirectCondition('slesson_teacher_end_time < NOW()', 'AND');
        }
        $srch->addCondition('sldetail.sldetail_learner_status', '=', ScheduledLesson::STATUS_COMPLETED);
        $srch->addCondition('sldetail.sldetail_is_teacher_paid', '=', 0);
        $srch->addDirectCondition('repiss.repiss_id IS NULL', 'AND');
        $srch->addOrder('sldetail.sldetail_id', 'ASC');
        $srch->doNotCalculateRecords();
        $resultSet = $srch->getResultSet();
        while ($lesson = FatApp::getDb()->fetch($resultSet)) {
            $db = FatApp::getDb();
            if (!$db->startTransaction()) {
                $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
                return false;
            }
            /* Add payment to teacher's wallet */
            $teacherPercent = 100 - $lesson['op_commission_percentage'];
            $teacherAmount = ($teacherPercent * $lesson['op_unit_price']) / 100;
            $txn = new Transaction($lesson['slesson_teacher_id']);
            $data = [
                'utxn_date' => date('Y-m-d H:i:s'),
                'utxn_slesson_id' => $lesson['slesson_id'],
                'utxn_credit' => $teacherAmount,
                'utxn_user_id' => $lesson['slesson_teacher_id'],
                'utxn_comments' => 'Payment of Lesson ' . $lesson['slesson_id'],
                'utxn_status' => Transaction::STATUS_COMPLETED,
                'utxn_type' => Transaction::TYPE_LESSON_BOOKING,
            ];
            if (!$txn->addTransaction($data)) {
                $this->error = $txn->getError();
                $db->rollbackTransaction();
                return false;
            }
            /* Mark lesson detail record as paid */
            $whr = ['smt' => 'sldetail_id = ?', 'vals' => [$lesson['sldetail_id']]];
            $record = new TableRecord(ScheduledLessonDetails::DB_TBL);
            $record->setFldValue('sldetail_is_teacher_paid', '1');
            if (!$record->update($whr)) {
                $this->error = $record->getError();
                $db->rollbackTransaction();
                return false;
            }
            if (!$db->commitTransaction()) {
                $this->error = Label::getLabel('LBL_PLEASE_TRY_AGAIN');
                $db->rollbackTransaction();
                return false;
            }
        }
        return true;
    }

    public static function getUserTypeArr($key = null)
    {
        $arr = [
            static::USER_TYPE_LEARNER => Label::getLabel('USER_LEARNER'),
            static::USER_TYPE_TEACHER => Label::getLabel('USER_TEACHER'),
            static::USER_TYPE_SUPPORT => Label::getLabel('USER_SUPPORT')
        ];
        if ($key === null) {
            return $arr;
        }
        return $arr[$key] ?? Label::getLabel('LBL_NA');
    }

    public static function getStatusArr($key = null)
    {
        $arr = [
            static::STATUS_PROGRESS => Label::getLabel('STATUS_PROGRESS'),
            static::STATUS_RESOLVED => Label::getLabel('STATUS_RESOLVED'),
            static::STATUS_ESCLATED => Label::getLabel('STATUS_ESCLATED'),
            static::STATUS_CLOSED => Label::getLabel('STATUS_CLOSED')
        ];
        if ($key === null) {
            return $arr;
        }
        return $arr[$key] ?? Label::getLabel('LBL_NA');
    }

    public static function getActionsArr($key = null)
    {
        $arr = [
            static::ACTION_RESET_AND_UNSCHEDULED => Label::getLabel('LBL_RESET_AND_UNSCHEDULED'),
            static::ACTION_COMPLETE_ZERO_REFUND => Label::getLabel('LBL_COMPLETE_AND_ZERO_REFUND'),
            static::ACTION_COMPLETE_HALF_REFUND => Label::getLabel('LBL_COMPLETE_AND_50%_REFUND'),
            static::ACTION_COMPLETE_FULL_REFUND => Label::getLabel('LBL_COMPLETE_AND_100%_REFUND'),
            static::ACTION_ESCLATE_TO_ADMIN => Label::getLabel('LBL_ESCALATE_TO_SUPPORT_TEAM')
        ];
        if ($key === null) {
            return $arr;
        }
        return $arr[$key] ?? Label::getLabel('LBL_NA');
    }

}
