<?php

class UserNotifications extends FatModel
{

    const DB_TBL = "tbl_notifications";
    const DB_TBL_PREFIX = "notification_";
    const NOTIFICATION_NOT_READ = 0;
    const NOTIFICATION_READ = 1;
    const NOTICATION_FOR_TEACHER_APPROVAL = 1;
    const NOTICATION_FOR_SCHEDULED_LESSON_BY_LEARNER = 2;
    const NOTICATION_FOR_SCHEDULED_LESSON_BY_TEACHER = 3;
    const NOTICATION_FOR_WALLET_CREDIT_ON_LESSON_COMPLETE = 4;
    const NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_TEACHER = 7;
    const NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_LEARNER = 8;
    const NOTICATION_FOR_CANCEL_LESSON_BY_TEACHER = 9;
    const NOTICATION_FOR_CANCEL_LESSON_BY_LEARNER = 10;
    const NOTICATION_FOR_ISSUE_REFUNDED = 11;
    const NOTICATION_FOR_ISSUE_REPORTED = 12;
    const NOTICATION_FOR_ISSUE_RESOLVED = 13;
    const NOTICATION_FOR_ISSUE_ESCLATED = 14;
    const NOTICATION_FOR_ISSUE_CLOSED = 15;
    const NOTICATION_FOR_CHANGE_PASSWORD = 16;

    private $userId = 0;
    private $recordId = 0;
    private $subRecordId = 0;
    private $type = 0;
    private $userInfo = [];

    public function __construct($userId)
    {
        $this->userId = $userId;
        $user = new User($this->userId);
        if (!$user->loadFromDb()) {
            return false;
        }
        $this->userInfo = $user->getFlds();
    }

    public function sendNotifcationMetaData($type, $recordId, $subRecordId = 0)
    {
        $this->type = $type;
        $this->recordId = $recordId;
        $this->subRecordId = $subRecordId;
    }

    public function sendTeacherApprovalNotification($langId = 0)
    {
        $this->type = self::NOTICATION_FOR_TEACHER_APPROVAL;
        $this->recordId = 0;
        $title = Label::getLabel("LABEL_TEACHER_REQUEST_APPROVED", ($langId != applicationConstants::NO) ? $langId : CommonHelper::getLangId());
        $description = Label::getLabel("LABEL_TEACHER_REQUEST_APPROVED_DESCRIPTION", ($langId != applicationConstants::NO) ? $langId : CommonHelper::getLangId());
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendChangePwdNotifi($langId = 0)
    {
        $this->type = self::NOTICATION_FOR_CHANGE_PASSWORD;
        $this->recordId = 0;
        $title = Label::getLabel("LBL_Password_Changed", ($langId != applicationConstants::NO) ? $langId : CommonHelper::getLangId());
        $description = Label::getLabel("LBL_Change_Password_Description", ($langId != applicationConstants::NO) ? $langId : CommonHelper::getLangId());
        return $this->addNotification($title, $description);
    }

    public function sendWalletCreditNotification($lessonId = 0)
    {
        $this->type = self::NOTICATION_FOR_WALLET_CREDIT_ON_LESSON_COMPLETE;
        $this->recordId = $lessonId;
        if ($lessonId) {
            $title = Label::getLabel("LABEL_WALLET_CREDIT", CommonHelper::getLangId());
            $description = sprintf(Label::getLabel('LBL_LessonId:_%s_Wallet_Credit_Notification', CommonHelper::getLangId()), $lessonId);
        } else {
            $title = Label::getLabel("LABEL_WALLET_CREDIT", CommonHelper::getLangId());
            $description = Label::getLabel("LABEL_WALLET_CREDIT_DESCRIPTION", CommonHelper::getLangId());
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendSchLessonByTeacherNotification($lessonId, $reschedule = false)
    {
        $this->type = self::NOTICATION_FOR_SCHEDULED_LESSON_BY_TEACHER;
        $this->recordId = $lessonId;
        $this->subRecordId = UserAuthentication::getLoggedUserId();
        if ($reschedule) {
            $title = Label::getLabel("LABEL_LESSON_RESCHEDULE_REQUEST_BY_TEACHER", CommonHelper::getLangId());
            $description = Label::getLabel("LABEL_LESSON_RESCHEDULE_REQUEST_BY_TEACHER_DESCRIPTION", CommonHelper::getLangId());
        } else {
            $title = Label::getLabel("LABEL_LESSON_SCHEDULED_BY_TEACHER", CommonHelper::getLangId());
            $description = Label::getLabel("LABEL_LESSON_SCHEDULED_BY_TEACHER_DESCRIPTION", CommonHelper::getLangId());
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendSchLessonByLearnerNotification($lessonId, $reschedule = false)
    {
        $this->type = self::NOTICATION_FOR_SCHEDULED_LESSON_BY_LEARNER;
        $this->recordId = $lessonId;
        $this->subRecordId = UserAuthentication::getLoggedUserId();
        if ($reschedule) {
            $title = Label::getLabel("LABEL_LESSON_RESCHEDULE_REQUEST_BY_LEARNER", CommonHelper::getLangId());
            $description = Label::getLabel("LABEL_LESSON_RESCHEDULE_REQUEST_LEARNER_DESCRIPTION", CommonHelper::getLangId());
        } else {
            $title = Label::getLabel("LABEL_LESSON_SCHEDULED_BY_LEARNER", CommonHelper::getLangId());
            $description = Label::getLabel("LABEL_LESSON_SCHEDULED_BY_LEARNER_DESCRIPTION", CommonHelper::getLangId());
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public static function deleteNotifications($recordId)
    {
        $db = FatApp::getDb();
        if (!$db->query("UPDATE tbl_notifications SET notification_deleted = 1 WHERE notification_id in (" . $recordId . ")")) {
            return false;
        }
        return true;
    }

    public static function changeNotifyStatus($status, $recordId)
    {
        $db = FatApp::getDb();
        if (!$db->query("UPDATE tbl_notifications SET notification_read = " . $status . " WHERE notification_id in (" . $recordId . ")")) {
            return false;
        }
        return true;
    }

    public function sendBuyerCancelOrderNotification($orderId, $comment, $orderUserId)
    {
        $this->type = self::NOTICATION_FOR_ORDER_RECIEVED;
        $this->recordId = $orderId;
        $userCompanyName = User::getAttributesById($orderUserId, 'user_company_name');
        $title = sprintf(Label::getLabel("LABEL_ORDER_NO_%s_HAS_BEEN_CANCELLED_BY_%s", CommonHelper::getLangId()), $orderId, $userCompanyName);
        $description = sprintf("Reason: %s", $comment);
        $this->sendFCMNotificationToUsers($title, $description);
        $orderStatus = Orders::getOrderStatus($orderId, CommonHelper::getLangId());
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            //return false;
        }
        $emailHandler = new EmailHandler();
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderProductInStockNotification($sellerProductId, $productName, $sellerName)
    {
        $this->recordId = $sellerProductId;
        $title = sprintf(Label::getLabel("LABEL_INSTOCK_NOTIFICATION_TITLE_%s", CommonHelper::getLangId()), $productName);
        $description = sprintf(Label::getLabel("LABEL_INSTOCK__NOTIFICATION_DESCRIPTION_FOR_Product_Name_%s_BY_SELLER_%s", CommonHelper::getLangId()), $sellerName, $productName);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderReceiveNotification($orderId, $data = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_RECIEVED;
        $this->recordId = $orderId;
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_ORDER_NO_%s_HAS_BEEN_RECIEVED", CommonHelper::getLangId()), $orderId);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_ORDER_RECIVED_DESCRIPTION:%s", CommonHelper::getLangId()), $orderId);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->newOrderEmailToSeller($orderId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderPlacedNotification($orderId, $data = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_STATUS_UPDATE;
        $this->recordId = $orderId;
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_ORDER_NO_%s_HAS_BEEN_PLACED", CommonHelper::getLangId()), $orderId);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_ORDER_PLACED_DESCRIPTION:%s", CommonHelper::getLangId()), $orderId);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->newOrderEmailToSeller($orderId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderReviewNotificationToSeller($orderId, $data = [])
    {
        return true;
        $this->type = self::NOTICATION_FOR_REVIEW_STATUS_UPDATED_TO_SELLER;
        $this->recordId = $orderId;
        $reviewId = $data['reveiwId'];
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_NEW_REVIEW_%s", CommonHelper::getLangId()), $orderId);
        $description = Label::getLabel("LABEL_NOTIFICATION_DESC_NEW_REVIEW", CommonHelper::getLangId());
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            //return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->sendSellerReviewNotification($reviewId, CommonHelper::getLangId())) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderReviewStatusUpdateNotification($orderId, $data = [])
    {
        return true;
        $this->type = self::NOTICATION_FOR_REVIEW_STATUS_UPDATED_TO_BUYER;
        $this->recordId = $orderId;
        $statusName = $data['statusName'];
        $reviewId = $data['reveiwId'];
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_REVIEW_STATUS_CHANGE_%s", CommonHelper::getLangId()), $orderId);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_ORDER_REVIEW_STATUS_CHANGED_TO_%s", CommonHelper::getLangId()), $statusName);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            //return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->sendBuyerReviewStatusUpdatedNotification($reviewId, CommonHelper::getLangId())) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderStatusUpdateNotification($orderId, $data = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_STATUS_UPDATE;
        $this->recordId = $orderId;
        $statusName = $data['statusName'];
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_STATUS_CHANGE_%s", CommonHelper::getLangId()), $orderId);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_ORDER_STATUS_CHANGED_TO_%s", CommonHelper::getLangId()), $statusName);
        $this->sendFCMNotificationToUsers($title, $description);
        $orderStatus = Orders::getOrderStatus($orderId, CommonHelper::getLangId());
        $smsTxt = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_DATA_CHANGED_SMS_%s_%s", CommonHelper::getLangId()), $orderId, $orderStatus);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $smsTxt)) {
            //return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->orderStatusUpdateEmailToSeller($orderId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderNotUpdatedNotification($orderList = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_NOT_UPDATED;
        $this->recordId = 0;
        $ordersCount = count($orderList);
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_STATUS_NOT_CHANGED_%d", CommonHelper::getLangId()), $ordersCount);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_DESC_ORDER_STATUS_NOT_CHANGED_%d", CommonHelper::getLangId()), $ordersCount);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->orderNotUpdatedEmailToSeller($this->userId, $ordersCount)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendNotificationForUnfulfilledOrders($orderList = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_NOT_COMPLETED;
        $this->recordId = 0;
        $ordersCount = count($orderList);
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_NOT_COMPLETED_%d", CommonHelper::getLangId()), $ordersCount);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_DESC_ORDER_NOT_COMPLETED_%d", CommonHelper::getLangId()), $ordersCount);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderDataUpdateNotification($orderId, $data = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_UPDATE;
        $this->recordId = $orderId;
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_ORDER_DATA_CHANGED_%s", CommonHelper::getLangId()), $orderId);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_DESC_ORDER_DATA_CHANGED_%s", CommonHelper::getLangId()), $orderId);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->orderUpdateEmailToBuyer($orderId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendLicenseExpiryNotification()
    {
        $latestLicenseInfo = UserLicense::getUserLatestLicenseInfo($this->userId);
        $this->type = self::NOTICATION_FOR_LICENSE_EXPIRY;
        if (!$latestLicenseInfo['license_expiry_date']) {
            return false;
        }
        $expiryDate = FatDate::format($latestLicenseInfo['license_expiry_date']);
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_TITLE_YOUR_LICENSE_WILL_EXPIRE_ON_%s", CommonHelper::getLangId()), $expiryDate);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_DESC_YOUR_LICENSE_WILL_EXPIRE_ON_%s", CommonHelper::getLangId()), $expiryDate);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->sendLicenseAboutToExpireEmail($this->userId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendOrderDeliveryDateUpdateNotification($orderId, $data = [])
    {
        $this->type = self::NOTICATION_FOR_ORDER_DELIVERY_DATE_UPDATE;
        $this->recordId = $orderId;
        $deliveryDate = $data['deliveryDate'];
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_ORDER_DELIVERY_DATE_SET_%s", CommonHelper::getLangId()), $orderId);
        $smsText = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_ORDER_DELIVERY_DATE_SET_%s_%s", CommonHelper::getLangId()), $orderId, $deliveryDate);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_DESC_ORDER_DELIVERY_DATE_%s", CommonHelper::getLangId()), $orderId);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $smsText)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendLoyaltyVoucherStatusUpdatedNotification($loyaltyVoucherId)
    {
        $this->recordId = $loyaltyVoucherId;
        $langId = CommonHelper::getLangId();
        $srch = LoyaltyVoucher::getSearchObject(true);
        $srch->setPageSize(1);
        $srch->addCondition('loyaltyvoucher_id', '=', $loyaltyVoucherId);
        $srch->addMultipleFields(['loyaltyvoucher_id', 'loyaltyvoucher_valid_till', 'loyaltyvoucher_code', 'loyaltyvoucher_status', 'loyaltyvoucher_comments', 'user_name', 'credential_email']);
        $rs = $srch->getResultSet();
        $loyaltyVoucherData = FatApp::getDb()->fetch($rs);
        if (!$loyaltyVoucherData) {
            $this->error = Label::getLabel('MSG_INVALID_REQUEST', $langId);
            return false;
        }
        if ($loyaltyVoucherData['loyaltyvoucher_status'] == LoyaltyVoucher::STATUS_REJECTED) {
            $this->type = self::NOTICATION_FOR_LOYALTY_VOUCHER_STATUS_REJECTED;
        } elseif ($loyaltyVoucherData['loyaltyvoucher_status'] == LoyaltyVoucher::STATUS_APPROVED || $loyaltyVoucherData['loyaltyvoucher_status'] == LoyaltyVoucher::STATUS_REDEEMED) {
            $this->type = self::NOTICATION_FOR_LOYALTY_VOUCHER_STATUS_APPROVED_REDEEMED;
        }
        $statusArr = LoyaltyVoucher::getVoucherStatusArr($langId);
        if (!isset($statusArr[$loyaltyVoucherData['loyaltyvoucher_status']])) {
            $this->error = Label::getLabel('MSG_INVALID_LOYALTY_VOUCHER_STATUS', $langId);
            return false;
        }
        $status = $statusArr[$loyaltyVoucherData['loyaltyvoucher_status']];
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_LOYALTY_VOUCHER_CODE_%s_HAS_BEEN_%s", CommonHelper::getLangId()), $loyaltyVoucherData['loyaltyvoucher_code'], $status);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_LOYALTY_VOUCHER_STATUS_UPDATE_DESCRIPTION", CommonHelper::getLangId()), $loyaltyVoucherData['loyaltyvoucher_code'], $status);
        $this->sendFCMNotificationToUsers($title, $description);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->sendLoyaltyVoucherStatusUpdatedNotification($langId, $loyaltyVoucherId)) {
            //return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendLoyaltyPointsAwardedNotification($rewtrxId)
    {
        $this->recordId = $rewtrxId;
        $langId = CommonHelper::getLangId();
        $srch = new RewardPointSearch($langId, true);
        $srch->addMultipleFields(['rewtrx.*', 'u.user_name', 'uc.credential_email']);
        $rewtrxData = $srch->findBy(['id' => $rewtrxId], null, 1);
        if (!$rewtrxData) {
            $this->error = Label::getLabel('MSG_INVALID_REQUEST', $langId);
            return false;
        }
        if ($rewtrxData['rewtrx_type'] == LoyaltyPoint::TYPE_VOUCHER_REJECTED) {
            $this->type = self::NOTICATION_FOR_LOYALTY_POINTS_REFUNDED;
        } else {
            $this->type = self::NOTICATION_FOR_LOYALTY_POINTS_AWARDED;
        }
        $title = sprintf(Label::getLabel("LABEL_NOTIFICATION_LOYALTY_POINTS_%s_HAVE_BEEN_AWARDED", CommonHelper::getLangId()), $rewtrxData['rewtrx_amount']);
        $description = sprintf(Label::getLabel("LABEL_NOTIFICATION_YOUR_LOYALTY_POINTS_HAVE_BEEN_AWARDED_DESCRIPTION_%s", CommonHelper::getLangId()), $rewtrxData['rewtrx_desc']);
        $this->sendFCMNotificationToUsers($title, $description, true);
        $phoneNo = $this->getUserPhoneNo();
        if ($this->isSmsNotificationAllowed() && !$this->sendSmsNotification($phoneNo, $title)) {
            // return false;
        }
        $emailHandler = new EmailHandler();
        if ($this->isEmailNotificationAllowed() && !$emailHandler->sendRewardPointsNotification($langId, $rewtrxId, $rewtrxData)) {
            // return false;
        }
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function sendSurveyNotification(int $surveyId)
    {
        $this->recordId = $surveyId;
        $langId = CommonHelper::getLangId();
        if ($surveyId < 1) {
            $this->error = Label::getLabel('MSG_INVALID_REQUEST', $langId);
            return false;
        }
        $survey = new Survey($surveyId);
        $questionnaireData = $survey->getQuestionnaireData($langId, []);
        if (empty($questionnaireData)) {
            $this->error = 'Some data in questionnaire is missing in database.';
            return false;
        }
        $this->type = self::NOTICATION_FOR_SURVEY;
        $title = $questionnaireData['questionnaire_name'];
        $description = Label::getLabel("LABEL_NOTIFICATION_YOU_HAVE_SURVEY_TO_FILL_DESCRIPTION", CommonHelper::getLangId());
        $this->sendFCMNotificationToUsers($title, $description, true);
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public static function getUserNotifications($userId)
    {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . "user_id", '=', $userId);
        $srch->addOrder(self::DB_TBL_PREFIX . "id", 'DESC');
        $srch->addCondition('notification_deleted', '=', 0);
        return $srch;
    }

    public static function getUserNotificationsByNotificationId($userId, $notiId)
    {
        $srch = self::getUserNotifications($userId);
        $srch->addCondition(self::DB_TBL_PREFIX . "id", '=', $notiId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public static function getUserUnreadNotifications(int $userId)
    {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . "user_id", '=', $userId);
        $srch->addCondition(self::DB_TBL_PREFIX . "read", '=', self::NOTIFICATION_NOT_READ);
        $srch->getResultSet();
        return $srch->recordCount();
    }

    private function getUserFcmIdList()
    {
        $srch = new SearchBase(UserAuthentication::DB_TBL_USER_AUTH);
        $srch->addCondition('uauth_user_id', '=', $this->userId);
        $srch->addCondition('uauth_type', '=', UserAuthentication::TYPE_INTERNAL_API);
        $srch->addDirectCondition('uauth_expiry > NOW()');
        $srch->addMultipleFields(['uauth_token', 'uauth_fcm_id']);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($rs);
    }

    public function addNotification($title, $description)
    {
        $saveData = [
            'notification_user_id' => $this->userId,
            'notification_title' => $title,
            'notification_description' => $description,
            'notification_record_id' => $this->recordId,
            'notification_record_type' => $this->type,
            'notification_sub_record_id' => $this->subRecordId,
        ];
        $tableRecord = new TableRecord(self::DB_TBL);
        $tableRecord->assignValues($saveData);
        if (!$tableRecord->addNew()) {
            $this->error = $tableRecord->getError();
            return false;
        }
        return true;
    }

    public function markRead($notificationId)
    {
        $saveData = ['notification_read' => UserNotifications::NOTIFICATION_READ,];
        $tableRecord = new TableRecord(self::DB_TBL);
        $tableRecord->assignValues($saveData);
        $where = ['smt' => 'notification_user_id = ? and notification_id = ?', 'vals' => [$this->userId, intval($notificationId)]];
        if ($tableRecord->update($where)) {
            return true;
        }
        return false;
    }

    private function isPushNotificationAllowed()
    {
        return ($this->userInfo['user_notification_inapp'] == 1 ? true : false);
    }

    private function isSmsNotificationAllowed()
    {
        return ($this->userInfo['user_notification_sms'] == 1 ? true : false);
    }

    private function getUserPhoneNo()
    {
        $userPhoneNo = $this->userInfo['user_phone'];
        if (strlen($userPhoneNo) < 10) {
            $userPhoneNo = "0" . $userPhoneNo;
        }
        return FatApp::getConfig("CONF_SMS_PHONE_CODE") . $userPhoneNo;
    }

    private function isEmailNotificationAllowed()
    {
        return ($this->userInfo['user_notification_email'] == 1 ? true : false);
    }

    public function getUnreadOrderNotificationCount()
    {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'user_id', '=', $this->userId);
        $srch->addCondition(self::DB_TBL_PREFIX . 'read', '=', self::NOTIFICATION_NOT_READ);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', '=', self::NOTICATION_FOR_ORDER_RECIEVED);
        $srch->getResultSet();
        return $srch->recordCount();
    }

    /**
     * @param bool $forcePush
     *
     * Whether to push the notification forcefully irrespective of the flag set in database.
     *
     * Added by Prinka : To implement the pop-up notification even when push notif is disabled for the user
     *
     */
    public function sendFCMNotificationToUsers($title, $description, $forcePush = false)
    {
        $fcmList = $this->getUserFcmIdList();
        foreach ($fcmList as $fcmId) {
            if (empty($fcmId)) {
                continue;
            }
            $fcmNotification = new FcmNotification($fcmId);
            $fcmNotification->setNotificationData($title, $description, $this->type, $this->recordId, $this->isPushNotificationAllowed());
            if (($forcePush === true || $this->isPushNotificationAllowed()) && !$fcmNotification->sendNotification()) {
                //return false;
            }
        }
        return true;
    }

    public function sendSmsNotification($phoneNo, $title)
    {
        $smsModel = new Sms();
        if (!$smsModel->sendSms($phoneNo, $title)) {
            return false;
        }
        SmsHandler::archiveSms($phoneNo, '', $title);
        return true;
    }

    public function sendSchLessonUpdateNotificationByAdmin($lessonId, $userId, $status, $updateFor)
    {
        $lessonStatusArray = ScheduledLesson::getStatusArr();
        if ($updateFor == USER::USER_TYPE_LEANER) {
            $this->type = self::NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_LEARNER;
        } else {
            $this->type = self::NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_TEACHER;
        }
        $this->recordId = $lessonId;
        $this->subRecordId = $userId;
        $title = Label::getLabel("LABEL_LESSON_STATUS_UPDATED", CommonHelper::getLangId());
        $description = sprintf(Label::getLabel("LABEL_LESSON_STATUS_UPDATED_TO_%s", CommonHelper::getLangId()), $lessonStatusArray[$status]);
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function cancelLessonNotification(int $lessonId, int $userId, string $userFullName, int $updateFor, string $comment = '')
    {
        if ($updateFor == USER::USER_TYPE_LEANER) {
            $this->type = self::NOTICATION_FOR_CANCEL_LESSON_BY_TEACHER;
        } else {
            $this->type = self::NOTICATION_FOR_CANCEL_LESSON_BY_LEARNER;
        }
        $this->recordId = $lessonId;
        $this->subRecordId = $userId;
        $title = Label::getLabel("LABEL_LESSON_CANCELED", CommonHelper::getLangId());
        $label = Label::getLabel("LBL_LESSON_{lesson-id}_CANCELED_BY_{user-full-name}_Comment:{comment}", CommonHelper::getLangId());
        $description = str_replace(['{lesson-id}', '{user-full-name}', '{comment}'], [$lessonId, $userFullName, $comment], $label);
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

    public function createLessonNotification(int $lessonId, int $userId, string $userFullName, int $updateFor, string $comment = '')
    {
        if ($updateFor == USER::USER_TYPE_LEANER) {
            $this->type = self::NOTICATION_FOR_CANCEL_LESSON_BY_TEACHER;
        } else {
            $this->type = self::NOTICATION_FOR_CANCEL_LESSON_BY_LEARNER;
        }
     
   

        $this->recordId = $lessonId;
        $this->subRecordId = $userId;
        $title = Label::getLabel("LABEL_LESSON_CREATED", CommonHelper::getLangId());
        $label = Label::getLabel("LBL_LESSON_{lesson-id}_LESSON_CREATED_BY_{user-full-name}", CommonHelper::getLangId());
        $description = str_replace(['{lesson-id}', '{user-full-name}', '{comment}'], [$lessonId, $userFullName, $comment], $label);
        if (!$this->addNotification($title, $description)) {
            return false;
        }
        return true;
    }

}
