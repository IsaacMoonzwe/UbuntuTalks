<?php

class FreePayController extends MyAppController
{

    public function charge($orderId = '')
    {
        $isAjaxCall = FatUtility::isAjaxCall();
        if (!$orderId || ((isset($_SESSION['shopping_cart']["order_id"]) && $orderId != $_SESSION['shopping_cart']["order_id"]))) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        if (!UserAuthentication::isUserLogged()) {
            Message::addErrorMessage(Label::getLabel('MSG_Your_Session_seems_to_be_expired.', $this->siteLangId));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        $userId = UserAuthentication::getLoggedUserId();
        $srch = new OrderSearch();
        $srch->joinOrderProduct();
        $srch->addCondition('order_id', '=', $orderId);
        $srch->addCondition('order_user_id', '=', $userId);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'op.op_teacher_id = tu.user_id', 'tu');
        $srch->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'tu.user_id = cred.credential_user_id', 'cred');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'op.op_tlanguage_id = tlang.tlanguage_id', 'tlang');
        $srch->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'ttlang.tlanguagelang_tlanguage_id = tlang.tlanguage_id AND tlanguagelang_lang_id = ' . $this->siteLangId, 'ttlang');
        $srch->addMultipleFields([
            'order_id',
            'order_user_id',
            'order_net_amount',
            'order_wallet_amount_charge',
            'op_teacher_id',
            'op_tlanguage_id',
            'cred.credential_email',
            'CONCAT(tu.user_first_name, " ", tu.user_last_name) as teacherFullName',
            'IFNULL(tlanguage_name, tlanguage_identifier) AS teacherTeachLanguageName',
            'tu.user_timezone'
        ]);
        $rs = $srch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($rs);
        if (!$orderInfo) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        if ($orderInfo['order_net_amount'] > 0) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access'));
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        $orderObj = new Order();
        $orderPaymentFinancials = $orderObj->getOrderPaymentFinancials($orderId);
        if ($orderPaymentFinancials['order_payment_gateway_charge'] > 0) {
            FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentFailure', [$orderId]));
        }
        $cartObj = new Cart();
        $cartData = $cartObj->getCart($this->siteLangId);
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $isFreeTrial = ($cartData['isFreeTrial'] == applicationConstants::YES);
        if (!$orderPaymentObj->chargeFreeOrder(0, $isFreeTrial)) {
            Message::addErrorMessage($orderPaymentObj->getError());
            if ($isAjaxCall) {
                FatUtility::dieWithError(Message::getHtml());
            }
            CommonHelper::redirectUserReferer();
        }
        /* add schedulaed lessons[ */
        if ($cartData['isFreeTrial']) { //== only for free trial
            $sLessonArr = [
                'slesson_teacher_id' => $orderInfo['op_teacher_id'],
                'slesson_slanguage_id' => $orderInfo['op_tlanguage_id'],
                'slesson_date' => date('Y-m-d', strtotime($cartData['startDateTime'])),
                'slesson_end_date' => date('Y-m-d', strtotime($cartData['endDateTime'])),
                'slesson_start_time' => date('H:i:s', strtotime($cartData['startDateTime'])),
                'slesson_end_time' => date('H:i:s', strtotime($cartData['endDateTime'])),
                'slesson_status' => ScheduledLesson::STATUS_SCHEDULED
            ];
            $getlearnerFullName = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['CONCAT(user_first_name," ",user_last_name) as learnerFullName']);
            $db = FatApp::getDb();
            $db->startTransaction();
            $sLessonObj = new ScheduledLesson();
            $sLessonObj->assignValues($sLessonArr);
            if (!$sLessonObj->save()) {
                $db->rollbackTransaction();
                Message::addErrorMessage($sLessonObj->getError());
                if ($isAjaxCall) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                CommonHelper::redirectUserReferer();
            }
            $lessonId = $sLessonObj->getMainTableRecordId();
            $sLessonDetailAr = [
                'sldetail_slesson_id' => $lessonId,
                'sldetail_order_id' => $orderId,
                'sldetail_learner_id' => $orderInfo['order_user_id'],
                'sldetail_learner_status' => ScheduledLesson::STATUS_SCHEDULED
            ];
            $slDetailsObj = new ScheduledLessonDetails();
            $slDetailsObj->assignValues($sLessonDetailAr);
            if (!$slDetailsObj->save()) {
                $db->rollbackTransaction();
                Message::addErrorMessage($slDetailsObj->getError());
                if ($isAjaxCall) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                CommonHelper::redirectUserReferer();
            }
            $sldetailId = $slDetailsObj->getMainTableRecordId();
            // share on student google calendar
            $setting = UserSetting::getUserSettings($orderInfo['order_user_id']);
            if (!empty($setting['us_google_access_token'])) {
                $view_url = CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$sldetailId]);
                $title = sprintf(Label::getLabel('LBL_%1$s_LESSON_Scheduled_with_%2$s'), Label::getLabel('LBL_Trial', $this->siteLangId), $orderInfo['teacherFullName']);
                $google_cal_data = [
                    'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId),
                    'summary' => $title,
                    'description' => sprintf(Label::getLabel("LBL_Click_here_to_join_the_lesson:_%s"), $view_url),
                    'url' => $view_url,
                    'start_time' => date('c', strtotime($cartData['startDateTime'])),
                    'end_time' => date('c', strtotime($cartData['endDateTime'])),
                    'timezone' => MyDate::getTimeZone(),
                ];
                $calId = SocialMedia::addEventOnGoogleCalendar($setting['us_google_access_token'], $google_cal_data);
                if (!empty($calId)) {
                    $slDetailsObj->setFldValue('sldetail_learner_google_calendar_id', $calId);
                    $slDetailsObj->save();
                }
            }
            // share on teacher google calendar
            $settings = UserSetting::getUserSettings($orderInfo['op_teacher_id']);
            if (!empty($settings['us_google_access_token'])) {
                $sLessonObj->loadFromDb();
                $oldCalId = $sLessonObj->getFldValue('slesson_teacher_google_calendar_id');
                if (!empty($oldCalId)) {
                    SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
                }
                $view_url = CommonHelper::generateFullUrl('TeacherScheduledLessons', 'view', [$lessonId]);
                $title = sprintf(Label::getLabel('LBL_%1$s_LESSON_Scheduled_by_%2$s'), Label::getLabel('LBL_Trial', $this->siteLangId), $getlearnerFullName['learnerFullName']);
                $google_cal_data = [
                    'title' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId),
                    'summary' => $title,
                    'description' => sprintf(Label::getLabel("LBL_Click_here_to_deliver_the_lesson:_%s"), $view_url),
                    'url' => $view_url,
                    'start_time' => date('c', strtotime($cartData['startDateTime'])),
                    'end_time' => date('c', strtotime($cartData['endDateTime'])),
                    'timezone' => MyDate::getTimeZone(),
                ];
                $calId = SocialMedia::addEventOnGoogleCalendar($settings['us_google_access_token'], $google_cal_data);
                if (!empty($calId)) {
                    $sLessonObj->setFldValue('slesson_teacher_google_calendar_id', $calId);
                    $sLessonObj->save();
                }
            }
            if ($cls = TeacherGroupClassesSearch::getTeacherClassByTime($orderInfo['op_teacher_id'], date('Y-m-d H:i:s', strtotime($cartData['startDateTime'])), date('Y-m-d H:i:s', strtotime($cartData['endDateTime'])))) {
                $grpclsId = $cls['grpcls_id'];
                $grpclsObj = new TeacherGroupClasses($grpclsId);
                $grpclsObj->cancelClass();
            }
            $db->commitTransaction();
            $emailData = [];
            $emailData = [
                'teacherFullName' => $orderInfo['teacherFullName'],
                'startDate' => MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $cartData['startDateTime'], false, $orderInfo['user_timezone']),
                'startTime' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $cartData['startDateTime'], true, $orderInfo['user_timezone']),
                'endTime' => MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $cartData['endDateTime'], true, $orderInfo['user_timezone']),
                'teacherTeachLanguageName' => Label::getLabel('LBL_Trial', $this->siteLangId),
                'learnerFullName' => $getlearnerFullName['learnerFullName'],
            ];
            EmailHandler::sendlearnerScheduleEmail($orderInfo['credential_email'], $emailData, $this->siteLangId);
            $userNotification = new UserNotifications($orderInfo['op_teacher_id']);
            $userNotification->sendSchLessonByLearnerNotification($lessonId);
        }
        $cartObj->clear();
        $cartObj->updateUserCart();
        /* ] */
        if ($isAjaxCall) {
            $this->set('redirectUrl', CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
            $this->set('msg', Label::getLabel("MSG_Payment_from_wallet_made_successfully", $this->siteLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
        if ($cartData['lpackage_is_free_trial']) {
            FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'trialBookedSuccess', [$orderId]));
        }
        FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
    }

}
