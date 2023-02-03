<?php

class NotificationsController extends LoggedUserController
{

    public function index()
    {
        $this->_template->render();
    }

    public function search()
    {
        $post = FatApp::getPostedData();
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : FatUtility::int($post['page']);
        $pagesize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srchNotification = UserNotifications::getUserNotifications(UserAuthentication::getLoggedUserId());
        $srchNotification->joinTable(Order::DB_TBL, 'LEFT OUTER JOIN', 'order_id = notification_record_id');
        $srchNotification->addMultipleFields([
            'notification_record_id as noti_record_id',
            'notification_record_type as noti_type',
            'notification_title as noti_title',
            'notification_description as noti_desc',
            'notification_added_on as noti_sent_on',
            'notification_read as noti_is_read',
            'notification_id as noti_id',
            'notification_sub_record_id as noti_sub_record_id',
        ]);
        $srchNotification->setPageNumber($page);
        $srchNotification->setPageSize($pagesize);
        $rs = $srchNotification->getResultSet();
        $list = FatApp::getDb()->fetchAll($rs);
        $pages = $srchNotification->pages();
        $recordCount = $srchNotification->recordCount();
        $this->set('list', $list);
        $this->set('page', $page);
        $this->set('pageCount', $pages);
        $this->set('pagesize', $pagesize);
        $this->set('recordCount', $recordCount);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function readNotification($notificationId)
    {
        $notificationId = intval($notificationId);
        $notificationData = UserNotifications::getUserNotificationsByNotificationId(UserAuthentication::getLoggedUserId(), $notificationId);
        $notificationRedirectUrl = CommonHelper::generateUrl('Notifications');
        $notificationType = $notificationData['notification_record_type'];
        $notificationRecordId = $notificationData['notification_record_id'];
        $notificationRead = $notificationData['notification_read'];
        switch ($notificationType) {
            case UserNotifications::NOTICATION_FOR_TEACHER_APPROVAL:
                $notificationRedirectUrl = CommonHelper::generateUrl('teacher');
                break;
            case UserNotifications::NOTICATION_FOR_SCHEDULED_LESSON_BY_LEARNER:
            case UserNotifications::NOTICATION_FOR_CANCEL_LESSON_BY_LEARNER:
                $notificationRedirectUrl = CommonHelper::generateUrl('TeacherScheduledLessons', 'view', [$notificationRecordId]);
                break;
            case UserNotifications::NOTICATION_FOR_SCHEDULED_LESSON_BY_TEACHER:
            case UserNotifications::NOTICATION_FOR_CANCEL_LESSON_BY_TEACHER:
                $notificationRedirectUrl = CommonHelper::generateUrl('LearnerScheduledLessons', 'view', [$notificationRecordId]);
                break;
            case UserNotifications::NOTICATION_FOR_WALLET_CREDIT_ON_LESSON_COMPLETE:
                $notificationRedirectUrl = CommonHelper::generateUrl('Wallet');
                break;
            case UserNotifications::NOTICATION_FOR_ISSUE_REPORTED:
            case UserNotifications::NOTICATION_FOR_ISSUE_ESCLATED:
                $notificationRedirectUrl = CommonHelper::generateUrl('TeacherScheduledLessons', 'view', [$notificationRecordId]);
                break;
            case UserNotifications::NOTICATION_FOR_ISSUE_REFUNDED:
            case UserNotifications::NOTICATION_FOR_ISSUE_RESOLVED:
            case UserNotifications::NOTICATION_FOR_ISSUE_CLOSED:
                $notificationRedirectUrl = CommonHelper::generateUrl('LearnerScheduledLessons', 'view', [$notificationRecordId]);
                break;
            case UserNotifications::NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_TEACHER:
                $notificationRedirectUrl = CommonHelper::generateUrl('TeacherScheduledLessons', 'view', [$notificationRecordId]);
                break;
            case UserNotifications::NOTICATION_FOR_LESSON_STATUS_UPDATED_BY_ADMIN_LEARNER:
                $notificationRedirectUrl = CommonHelper::generateUrl('LearnerScheduledLessons', 'view', [$notificationRecordId]);
                break;
        }
        if ($notificationRead == UserNotifications::NOTIFICATION_NOT_READ) {
            $userNotification = new UserNotifications(UserAuthentication::getLoggedUserId());
            $userNotification->markRead($notificationId);
        }
        FatApp::redirectUser($notificationRedirectUrl);
    }

    public function markNotificationRead()
    {
        $notificationId = FatApp::getPostedData('noti_id', FatUtility::VAR_INT, 0);
        if ($notificationId < 1) {
            $this->invalidRequest();
        }
        $userNotification = new UserNotifications(UserAuthentication::getLoggedUserId());
        if ($userNotification->markRead($notificationId)) {
            $unreadNotificationCount = UserNotifications::getUserUnreadNotifications(UserAuthentication::getLoggedUserId());
            CommonHelper::dieJsonSuccess("Success", compact('unreadNotificationCount'));
        }
        CommonHelper::dieJsonError(Label::getLabel("ERROR_UNBALE_TO_UPDATE_THE_STATUS", $this->siteLangId));
    }

    public function deleteRecords()
    {
        $notificationIds = FatApp::getPostedData('record_ids');
        if (!UserNotifications::deleteNotifications($notificationIds)) {
            FatUtility::dieJsonError(Label::getLabel("ERROR_UNBALE_TO_DELETE", $this->siteLangId));
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Notification_Deleted_Successfully!'));
    }

    public function changeStatus()
    {
        $notificationIds = FatApp::getPostedData('record_ids');
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $markread = FatApp::getPostedData('markread', FatUtility::VAR_INT, 0);
        if (!UserNotifications::changeNotifyStatus($status, $notificationIds)) {
            FatUtility::dieJsonError(Label::getLabel("ERROR_UNBALE_TO_UPDATE_THE_STATUS", $this->siteLangId));
        }
        $msg = '';
        if ($markread != 1) {
            $msg = Label::getLabel('LBL_Status_Updated_Successfully!', $this->siteLangId);
        }
        FatUtility::dieJsonSuccess($msg);
    }

}
