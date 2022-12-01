<?php
class EventUserController extends MyEventAppController
{
    public function EventLoginForm()
    {
        if (EventUserAuthentication::isUserLogged()) {
            //FatApp::redirectUser(EventUser::getPreferedDashbordRedirectUrl());
            FatApp::redirectUser(CommonHelper::generateUrl('Events'));
        }
        $frm = $this->getLoginForm();
        $this->set('frm', $frm);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->_template->render();
    }
    public function EventTicketSuccess($orderId)
    {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data) {
            // $ticketUrl=$post['ticketUrl'];
            $_SESSION['ticketUrl'] = $data->ticketUrl;
            //   $ticket_download=$post['ticket_download'];
            $_SESSION['ticketDownloadUrl'] = $data->download_ticketUrl;
            // FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND));
            $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
            $this->set('redirectUrl', $redirectUrl);
        }
        // if ($rememberme == applicationConstants::YES) {
        //     if (true !== $this->setUserLoginCookie($userId)) {
        //         //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
        //     }
        // }
        FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function EventTicket($orderId = null, $planSelected = '')
    {
        $orderData = new SearchBase('tbl_order_products');
        $orderData->addCondition('op_order_id', '=', $orderId);
        $orderResult = FatApp::getDb()->fetch($orderData->getResultSet());
        $ticketPlanData = new SearchBase('tbl_event_user_ticket_plan');
        $ticketPlanData->addCondition('event_user_ticket_plan_id', '=', $orderResult['op_grpcls_id']);
        $ticketPlanResult = FatApp::getDb()->fetch($ticketPlanData->getResultSet());
        $planData = new SearchBase('tbl_three_reasons');
        $planData->addCondition('three_reasons_id', '=', $ticketPlanResult['event_user_plan_id']);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $this->set('planSelected', $planResult['registration_plan_title']);
        $this->set('planPrice', $planResult['registration_plan_price']);
        $this->set('tickets', $_SESSION['ticket_count']);
        $this->set('orderId', $orderId);
        $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
        $order_type = FatApp::getPostedData('ticketUrl', FatUtility::VAR_STRING, '');
        $post = FatApp::getPostedData();
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false);
    }
    //Concert Ticket
    public function ConcertTicketSuccess($orderId)
    {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if ($data) {
            // $ticketUrl=$post['ticketUrl'];
            $_SESSION['concertUrl'] = $data->ticketUrl;
            //   $ticket_download=$post['ticket_download'];
            $_SESSION['concertDownloadUrl'] = $data->download_ticketUrl;
            // FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND));
            $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
            $this->set('redirectUrl', $redirectUrl);
        }
        // if ($rememberme == applicationConstants::YES) {
        //     if (true !== $this->setUserLoginCookie($userId)) {
        //         //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
        //     }
        // }
        FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function ConcertTicket($orderId = null, $planSelected = '')
    {
        $orderData = new SearchBase('tbl_order_products');
        $orderData->addCondition('op_order_id', '=', $orderId);
        $orderResult = FatApp::getDb()->fetch($orderData->getResultSet());
        $ticketPlanData = new SearchBase('tbl_event_concert_ticket_plan');
        $ticketPlanData->addCondition('event_concert_ticket_plan_id', '=', $orderResult['op_grpcls_id']);
        $ticketPlanResult = FatApp::getDb()->fetch($ticketPlanData->getResultSet());
        $planData = new SearchBase('tbl_benefit_concert');
        $planData->addCondition('benefit_concert_id', '=', $ticketPlanResult['event_user_concert_id']);

        $planResult = FatApp::getDb()->fetch($planData->getResultSet());

        $this->set('planSelected', $planResult['benefit_concert_plan_title']);
        $this->set('planPrice', $planResult['benefit_concert_plan_price']);
        $this->set('tickets', $_SESSION['concert_ticket']);
        $this->set('orderId', $orderId);
        $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
        $order_type = FatApp::getPostedData('ticketUrl', FatUtility::VAR_STRING, '');
        $post = FatApp::getPostedData();
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false);
    }

    //symposium Ticket
    public function SymposiumTicketSuccess($orderId)
    {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);

        if ($data) {
            // $ticketUrl=$post['ticketUrl'];
            $_SESSION['symposiumUrl'] = $data->ticketUrl;
            //   $ticket_download=$post['ticket_download'];
            $_SESSION['symposiumDownloadUrl'] = $data->download_ticketUrl;
            // FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND));
            $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
            $this->set('redirectUrl', $redirectUrl);
        }
        // if ($rememberme == applicationConstants::YES) {
        //     if (true !== $this->setUserLoginCookie($userId)) {
        //         //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
        //     }
        // }
        FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function SymposiumTicket($orderId = null, $planSelected = '')
    {
        $orderData = new SearchBase('tbl_order_products');
        $orderData->addCondition('op_order_id', '=', $orderId);
        $orderResult = FatApp::getDb()->fetch($orderData->getResultSet());

        $ticketPlanData = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $ticketPlanData->addCondition('pre_symposium_dinner_ticket_plan_id', '=', $orderResult['op_grpcls_id']);
        $ticketPlanResult = FatApp::getDb()->fetch($ticketPlanData->getResultSet());
        $planData = new SearchBase('tbl_pre_symposium_dinner');
        $planData->addCondition('pre_symposium_dinner_id', '=', $ticketPlanResult['event_user_pre_symposium_dinner_id']);

        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $this->set('planSelected', $planResult['pre_symposium_dinner_plan_title']);
        $this->set('planPrice', $planResult['pre_symposium_dinner_plan_price']);
        $this->set('tickets', $_SESSION['symposium_ticket']);
        $this->set('orderId', $orderId);
        $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
        $order_type = FatApp::getPostedData('ticketUrl', FatUtility::VAR_STRING, '');
        $post = FatApp::getPostedData();
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false);
    }
    public function DonationSuccess($orderId)
    {
        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body);
        if ($data) {
            // $ticketUrl=$post['ticketUrl'];
            $_SESSION['donationUrl'] = $data->ticketUrl;
            //   $ticket_download=$post['ticket_download'];
            $_SESSION['donationDownloadUrl'] = $data->download_ticketUrl;
            // FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND));
            $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
            $this->set('redirectUrl', $redirectUrl);
        }
        // if ($rememberme == applicationConstants::YES) {
        //     if (true !== $this->setUserLoginCookie($userId)) {
        //         //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
        //     }
        // }
        FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function DonationReceipt($orderId = null, $planSelected = '')
    {
        $orderData = new SearchBase('tbl_order_products');
        $orderData->addCondition('op_order_id', '=', $orderId);
        $orderResult = FatApp::getDb()->fetch($orderData->getResultSet());
        $ticketPlanData = new SearchBase('tbl_event_user_donation');
        $ticketPlanData->addCondition('event_user_donation_id', '=', $orderResult['op_grpcls_id']);
        $ticketPlanResult = FatApp::getDb()->fetch($ticketPlanData->getResultSet());
        // $planData = new SearchBase('tbl_three_reasons');
        // $planData->addCondition('three_reasons_id', '=', $ticketPlanResult['event_user_plan_id']);
        // $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        // $this->set('planSelected', $planResult['registration_plan_title']);
        // $this->set('planPrice', $planResult['registration_plan_price']);
        $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
            'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
            'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
            'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
        ));
        $this->set('userData', $userRow);
        $this->set('tickets', $ticketPlanResult['event_user_donation_amount']);
        $this->set('orderId', $orderId);
        $redirectUrl = CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId, 1], CONF_WEBROOT_FRONTEND);
        $order_type = FatApp::getPostedData('ticketUrl', FatUtility::VAR_STRING, '');
        $post = FatApp::getPostedData();
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false);
    }
    public function paymentSuccess($orderId = null, $ticket_generate_url = 0)
    {
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $paymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $orderData = new SearchBase('tbl_order_products');
        $orderData->addCondition('op_order_id', '=', $orderId);
        $orderResult = FatApp::getDb()->fetch($orderData->getResultSet());
        $userId = $_SESSION['Event_userId'];
        $userObj = new EventUser($userId);
        $cartData = $_SESSION['cart'];

        if (isset($_SESSION['summary'])) {
            // print_r($cartData);
            $cartData = $_SESSION['summary'];
            $_SESSION['cart'] = $cartData;
            $couponCode = $cartData['cartDiscounts'];
            if ($couponCode != '') {
                $coupon_info = $couponCode['coupon_code'];
                $pendingOrderHoldSrch = new SearchBase('tbl_coupons');
                $pendingOrderHoldSrch->addCondition('coupon_code', '=', $coupon_info);
                // $pendingOrderHoldSrch->addCondition('coupon_end_date', '>=',date('Y-m-d'));
                $rs = $pendingOrderHoldSrch->getResultSet();
                $couponInfo = FatApp::getDb()->fetch($rs, 'coupon_id');
                $updateCount = ($couponInfo['coupon_uses_count']) + 1;
                $record = new TableRecord('tbl_coupons');
                $record->assignValues(['coupon_uses_count' => $updateCount]);
                if (!$record->update(['smt' => 'coupon_id = ?', 'vals' => [$couponInfo['coupon_id']]])) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
                }
            }
        }
        if (isset($_SESSION['reg_sponser'])) {
            $userObj->setFldValue('user_sponsership_charge', $paymentAmount);
        } elseif (isset($_SESSION['become_sponser'])) {
            # code... selected_sponsershipPlan_id
            $record = new TableRecord('tbl_event_user_become_sponser');
            $record->assignValues(['event_user_payment_status' => EventUser::EVENT_DONATION_SUCCESS]);
            if (!$record->update(['smt' => 'event_user_become_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
            $method_json = json_decode($_SESSION['become_sponser']);
            $qty = $_SESSION['plan_Qty'];
            $qty_json = json_decode($qty);
            $allValues = array_values((array)$qty_json);
            $qty_index = 0;
            $allKeysOfEmployee = array_keys((array)$method_json);
            $planTitle = '';
            foreach ($allKeysOfEmployee as $tempKey) {
                $planData = new SearchBase('tbl_sponsorshipcategories');
                $planData->addCondition('sponsorshipcategories_deleted', '=', 0);
                $planData->addCondition('sponsorshipcategories_id', '=', $tempKey);
                $planResult = FatApp::getDb()->fetch($planData->getResultSet());
                $planTitle = $planTitle . " " . $planResult['sponsorshipcategories_name'] . " - " . $allValues[$qty_index] . ",";
                $qty_index++;
            }
            $planTitle = substr($planTitle, 0, -1);
            $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $srch = $userObj->getUserSearchObj();
            $rs = $srch->getResultSet();
            $usersRow = FatApp::getDb()->fetch($rs);
            $userRow['user_email'] = $usersRow['credential_email'];
            $data = [
                'user_first_name' => $userRow['user_first_name'],
                'user_last_name' => $userRow['user_last_name'],
                'user_email' => $userRow['user_email'],
                'plan' => $planTitle,
            ];
            $email = new EmailHandler();
            if (true !== $email->sendSponsorshipplanEmail($this->siteLangId, $data)) {
                return false;
            }
            $title_message = Label::getLabel('LBL_Thank_You_For_Purchase_Sponsorship_Plan');
            unset($_SESSION['selected_sponsershipPlan_id']);
            unset($_SESSION['plan_Qty']);
        } elseif (isset($_SESSION['donation'])) {
            $record = new TableRecord('tbl_event_user_donation');
            $record->assignValues(['event_user_donation_status' => EventUser::EVENT_DONATION_SUCCESS]);
            if (!$record->update(['smt' => 'event_user_donation_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
            if (isset($_SESSION['donationUrl']) && isset($_SESSION['donationDownloadUrl'])) {
                $ticket_generate_url = $_SESSION['donationUrl'];
                $ticket_download = $_SESSION['donationDownloadUrl'];
            }
            $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            if (!isset($_SESSION['donationUrl'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'DonationReceipt', [$orderId]));
            } elseif ($ticket_download != '' && $ticket_generate_url != '') {
                $record = new TableRecord('tbl_event_user_donation');
                $record->assignValues(['event_user_receipt_url' => $ticket_generate_url, 'event_user_receipt_download_url' => $ticket_download]);
                if (!$record->update(['smt' => 'event_user_donation_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
                }
                $srch = $userObj->getUserSearchObj();
                $rs = $srch->getResultSet();
                $usersRow = FatApp::getDb()->fetch($rs);
                $userRow['user_email'] = $usersRow['credential_email'];
                $data = [
                    'user_first_name' => $userRow['user_first_name'],
                    'user_last_name' => $userRow['user_last_name'],
                    'user_email' => $userRow['user_email'],
                    'plan' => $_SESSION['become_sponser'],
                    'file_upload' => $_SESSION['donationUrl'],
                ];
                $email = new EmailHandler();
                if (true !== $email->sendDonationEmail($this->siteLangId, $data)) {
                    return false;
                }
                unset($_SESSION['donation']);
                unset($_SESSION['event']);
                unset($_SESSION['reg_sponser']);
                unset($_SESSION['become_sponser']);
                unset($_SESSION['sponsor']);
                unset($_SESSION['Event_userId']);
                unset($_SESSION['ticketDownloadUrl']);
                unset($_SESSION['ticketUrl']);
                unset($_SESSION['donationUrl']);
                unset($_SESSION['donationDownloadUrl']);
                unset($_SESSION['ticket_count']);
                unset($_SESSION['event_ticket_id']);
                unset($_SESSION['planSelected']);
            }
            $title_message = Label::getLabel('LBL_Thank_You_For_Donation');
        } elseif (isset($_SESSION['ticket_count'])) {
            $title_message = Label::getLabel('LBL_Thank_You_For_Purchase_Ticket');
            $message =  Label::getLabel('LBL_Ticket_Has_Been_Genrated_Please_Check_In_Your_Email!');
            $record = new TableRecord('tbl_event_user_ticket_plan');
            $record->assignValues(['event_user_id' => EventUserAuthentication::getLoggedUserId(), 'event_user_ticket_count' => $_SESSION['ticket_count'], 'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_SUCCESS]);
            if (isset($_SESSION['ticketUrl']) && isset($_SESSION['ticketDownloadUrl'])) {
                $ticket_generate_url = $_SESSION['ticketUrl'];
                $ticket_download = $_SESSION['ticketDownloadUrl'];
            }
            if (!$record->update(['smt' => 'event_user_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
            $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            if (!isset($_SESSION['ticketUrl'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventTicket', [$orderId, $_SESSION['planSelected']]));
            } elseif ($ticket_download != '' && $ticket_generate_url != '') {
            
                $record = new TableRecord('tbl_event_user_ticket_plan');
                $record->assignValues(['event_user_ticket_url' => $ticket_generate_url, 'event_user_ticket_download_url' => $ticket_download]);
                if (!$record->update(['smt' => 'event_user_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
                }
                $srch = $userObj->getUserSearchObj();
                $rs = $srch->getResultSet();
                $usersRow = FatApp::getDb()->fetch($rs);
                $userRow['user_email'] = $usersRow['credential_email'];
                $data = [
                    'user_first_name' => $userRow['user_first_name'],
                    'user_last_name' => $userRow['user_last_name'],
                    'user_email' => $userRow['user_email'],
                    'plan' => $_SESSION['planSelected'],
                    'file_upload' => $_SESSION['ticketUrl'],
                ];
                $email = new EmailHandler();
                if (true !== $email->sendRegisterplanEmail($this->siteLangId, $data)) {
                    return false;
                }
                unset($_SESSION['donation']);
                unset($_SESSION['event']);
                unset($_SESSION['reg_sponser']);
                unset($_SESSION['become_sponser']);
                unset($_SESSION['sponsor']);
                unset($_SESSION['Event_userId']);
                unset($_SESSION['ticketDownloadUrl']);
                unset($_SESSION['ticketUrl']);
                unset($_SESSION['ticket_count']);
                unset($_SESSION['event_ticket_id']);
                unset($_SESSION['planSelected']);
            }
        } elseif (isset($_SESSION['concert_ticket'])) {


            $title_message = Label::getLabel('LBL_Thank_You_For_Purchase_Ticket_For_Concert');
            $message =  Label::getLabel('LBL_Ticket_Has_Been_Genrated_Please_Check_In_Your_Email!');
            $ticket = (int)$_SESSION['concert_ticket'];

            $record = new TableRecord('tbl_event_concert_ticket_plan');
            $record->assignValues(['event_user_id' => EventUserAuthentication::getLoggedUserId(), 'event_user_ticket_count' => $_SESSION['concert_ticket'], 'ticket_count' => 10, 'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_SUCCESS]);
            if (isset($_SESSION['concertUrl']) && isset($_SESSION['concertDownloadUrl'])) {
                $ticket_generate_url = $_SESSION['concertUrl'];
                $ticket_download = $_SESSION['concertDownloadUrl'];
            }
            if (!$record->update(['smt' => 'event_concert_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
            $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            if (!isset($_SESSION['concertUrl'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'ConcertTicket', [$orderId, $_SESSION['concertPlan']]));
            } elseif ($ticket_download != '' && $ticket_generate_url != '') {
                $record = new TableRecord('tbl_event_concert_ticket_plan');
                $record->assignValues(['event_user_ticket_url' => $ticket_generate_url, 'event_user_ticket_download_url' => $ticket_download]);
                if (!$record->update(['smt' => 'event_concert_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
                }

                $srch = $userObj->getUserSearchObj();
                $rs = $srch->getResultSet();
                $usersRow = FatApp::getDb()->fetch($rs);
                $userRow['user_email'] = $usersRow['credential_email'];
                $data = [
                    'user_first_name' => $userRow['user_first_name'],
                    'user_last_name' => $userRow['user_last_name'],
                    'user_email' => $userRow['user_email'],
                    'plan' => $_SESSION['concertPlan'],
                    'file_upload' => $_SESSION['concertUrl'],
                ];
                $email = new EmailHandler();
                if (true !== $email->sendConcertplanEmail($this->siteLangId, $data)) {
                    return false;
                }
                unset($_SESSION['donation']);
                unset($_SESSION['event']);
                unset($_SESSION['reg_sponser']);
                unset($_SESSION['become_sponser']);
                unset($_SESSION['sponsor']);
                unset($_SESSION['Event_userId']);
                unset($_SESSION['ticketDownloadUrl']);
                unset($_SESSION['ticketUrl']);
                unset($_SESSION['concertDownloadUrl']);
                unset($_SESSION['concertUrl']);
                unset($_SESSION['ticket_count']);
                unset($_SESSION['event_ticket_id']);
                unset($_SESSION['planSelected']);
                unset($_SESSION['concert_ticket']);
                unset($_SESSION['concertPlan']);
            }
        } elseif (isset($_SESSION['symposium_ticket'])) {
            $title_message = Label::getLabel('LBL_Thank_You_For_Purchase_Ticket');
            $message =  Label::getLabel('LBL_Ticket_Has_Been_Genrated_Please_Check_In_Your_Email!');
            $record = new TableRecord('tbl_pre_symposium_dinner_ticket_plan');
            $record->assignValues(['event_user_id' => EventUserAuthentication::getLoggedUserId(), 'event_user_ticket_count' => $_SESSION['symposium_ticket'], 'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_SUCCESS]);
            if (isset($_SESSION['symposiumUrl']) && isset($_SESSION['symposiumDownloadUrl'])) {
                $ticket_generate_url = $_SESSION['symposiumUrl'];
                $ticket_download = $_SESSION['symposiumDownloadUrl'];
            }
            if (!$record->update(['smt' => 'pre_symposium_dinner_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
            }
            $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            if (!isset($_SESSION['symposiumUrl'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'SymposiumTicket', [$orderId, $_SESSION['symposiumPlan']]));
            } elseif ($ticket_download != '' && $ticket_generate_url != '') {
                $record = new TableRecord('tbl_pre_symposium_dinner_ticket_plan');
                $record->assignValues(['event_user_ticket_url' => $ticket_generate_url, 'event_user_ticket_download_url' => $ticket_download]);
                if (!$record->update(['smt' => 'pre_symposium_dinner_ticket_plan_id = ?', 'vals' => [$orderResult['op_grpcls_id']]])) {
                    FatUtility::dieJsonError(Label::getLabel('LBL_SOMETHING_WENT_WRONG_PLEASE_TRY_AGAIN'));
                }
                $srch = $userObj->getUserSearchObj();
                $rs = $srch->getResultSet();
                $usersRow = FatApp::getDb()->fetch($rs);
                $userRow['user_email'] = $usersRow['credential_email'];
                $data = [
                    'user_first_name' => $userRow['user_first_name'],
                    'user_last_name' => $userRow['user_last_name'],
                    'user_email' => $userRow['user_email'],
                    'plan' => $_SESSION['symposiumPlan'],
                    'file_upload' => $_SESSION['symposiumUrl'],
                ];
                $email = new EmailHandler();
                if (true !== $email->sendSymposiumplanEmail($this->siteLangId, $data)) {
                    return false;
                }
                unset($_SESSION['donation']);
                unset($_SESSION['event']);
                unset($_SESSION['reg_sponser']);
                unset($_SESSION['become_sponser']);
                unset($_SESSION['sponsor']);
                unset($_SESSION['Event_userId']);
                unset($_SESSION['ticketDownloadUrl']);
                unset($_SESSION['ticketUrl']);
                unset($_SESSION['ticket_count']);
                unset($_SESSION['symposiumDownloadUrl']);
                unset($_SESSION['symposiumUrl']);
                unset($_SESSION['symposium_ticket']);
                unset($_SESSION['symposiumPlan']);
                unset($_SESSION['event_ticket_id']);
                unset($_SESSION['planSelected']);
            }
        }
        $userObj->save();
        $userTutorID = $_SESSION['Event_userId'];
        // $textMessage = 'Success';
        unset($_SESSION['donation']);
        unset($_SESSION['event']);
        unset($_SESSION['reg_sponser']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['Event_userId']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);
        unset($_SESSION['cart']);
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);
        unset($_SESSION['walletSummary']);
        unset($_SESSION['summary']);
        unset($_SESSION['concert_ticket']);
        unset($_SESSION['concertPlan']);
        $this->set('setMonthAndWeekName', true);
        $this->set('textMessage', $textMessage);
        $this->set('title_message', $title_message);
        $this->set('message', $message);
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar-luxon.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs('js/fullcalendar-luxon-global.min.js');
        $this->_template->addJs('js/fateventcalendar.js');
        $this->_template->addJs('/common-js/fat-common.js');
        $this->_template->render();
    }
    public function confirmOrder()
    {
        $_SESSION['event'] = 'start';
        $user_id = $_SESSION['Event_userId'];
        $cartData = $_SESSION['cart'];
        if (isset($_SESSION['summary'])) {
            // echo "<pre>" ;
            // print_r($cartData);
            $cartData = $_SESSION['summary'];
            $_SESSION['cart'] = $cartData;
            // print_r($cartData);
        }
        if ($_SESSION['walletSummary']) {
            $cartData = $_SESSION['walletSummary'];
            $_SESSION['cart'] = $cartData;
        }

        $order_type = FatApp::getPostedData('order_type', FatUtility::VAR_INT, 0);
        $pmethodId = FatApp::getPostedData('pmethod_id', FatUtility::VAR_INT, 0);
        $order_id = FatApp::getPostedData('order_id', FatUtility::VAR_STRING, '');
        $referralName = FatApp::getPostedData('referralName', FatUtility::VAR_STRING, '');
        $currency = FatApp::getPostedData('currency', FatUtility::VAR_STRING, 'USD');
        $currencyCode = FatApp::getPostedData('currencyCode', FatUtility::VAR_STRING, '$');
        if ($pmethodId > 0) {
            $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
            $pmSrch->doNotCalculateRecords();
            $pmSrch->doNotLimitRecords();
            $pmSrch->addMultipleFields(['pmethod_id', 'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name', 'pmethod_code', 'pmethod_description']);
            $pmSrch->addCondition('pmethod_id', '=', $pmethodId);
            $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
            $pmRs = $pmSrch->getResultSet();
            $paymentMethod = FatApp::getDb()->fetch($pmRs);
            if (!$paymentMethod) {
                Message::addErrorMessage(Label::getLabel('MSG_Selected_Payment_method_not_found!'));
                FatUtility::dieWithError(Message::getHtml());
            }
        }
        // ]
        // Loading Money to wallet[
        if (Order::TYPE_WALLET_RECHARGE == $order_type || Order::TYPE_GIFTCARD == $order_type) {

            $criteria = ['isEventUserLogged' => true];
            if (!$this->isEligibleForNextStep($criteria)) {
                if (Message::getErrorCount()) {
                    $errMsg = Message::getHtml();
                } else {
                    Message::addErrorMessage(Label::getLabel('MSG_Something_went_wrong,_please_try_after_some_time.'));
                    $errMsg = Message::getHtml();
                }
                FatUtility::dieWithError($errMsg);
            }
            $user_id = $_SESSION['Event_userId'];
            // if ('' == $order_id) {
            //     Message::addErrorMessage(Label::getLabel('MSG_INVALID_Request'));
            //     FatUtility::dieWithError(Message::getHtml());
            // }
            $orderObj = new Order();
            $srch = Order::getSearchObject();
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $srch->addCondition('order_id', '=', $order_id);
            $srch->addCondition('order_user_id', '=', $user_id);
            $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PENDING);
            $rs = $srch->getResultSet();
            $orderInfo = FatApp::getDb()->fetch($rs);
            if (!$orderInfo) {
                Message::addErrorMessage(Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED'));
                FatUtility::dieWithError(Message::getHtml());
            }
            $orderObj->updateOrderInfo($order_id, ['order_pmethod_id' => $pmethodId]);
            // $controller = $paymentMethod['pmethod_code'] . 'Pay';
            $controller = 'EventWalletPay';
            $redirectUrl = CommonHelper::generateUrl($controller, 'charge', [$order_id, 1]);
            $this->set('msg', Label::getLabel('LBL_Processing...'));
            // $this->set('redirectUrl', $redirectUrl);
            $this->_template->render(false, false, 'json-success.php');
        }
        $fromKids = FatApp::getPostedData('fromKids', FatUtility::VAR_INT, 0);
        // $cartData = $this->cartObj->getCart($this->siteLangId,$fromKids);
        $cartData = $_SESSION['cart'];
        if (isset($_SESSION['summary'])) {
            // echo "<pre>" ;
            // print_r($cartData);
            $cartData = $_SESSION['summary'];
            $_SESSION['cart'] = $cartData;
            // print_r($cartData);
        }
        $criteria = ['isEventUserLogged' => true, 'hasItems' => true];
        if (0 == $cartData['orderPaymentGatewayCharges'] && $pmethodId) {
            Message::addErrorMessage(Label::getLabel('MSG_Amount_for_payment_gateway_must_be_greater_than_zero.'));
            FatUtility::dieWithError(Message::getHtml());
        }
        // addOrder[
        // $order_id = isset($_SESSION['shopping_cart']['order_id']) ? $_SESSION['shopping_cart']['order_id'] : false;
        $orderNetAmount = $cartData['orderNetAmount'];
        $walletAmountCharge = $cartData['walletAmountCharge'];
        $orderNetAmount = $cartData["orderNetAmount"];
        $walletAmountCharge = $cartData["walletAmountCharge"];
        $coupon_discount_total = FatUtility::float($cartData['cartDiscounts']['coupon_discount_total'] ?? 0);
        $orderData = [
            'order_type' => Order::TYPE_LESSON_BOOKING,
            'order_user_id' => $_SESSION['Event_userId'],
            'order_is_paid' => Order::ORDER_IS_PENDING,
            'order_net_amount' => $orderNetAmount,
            'order_is_wallet_selected' => $cartData['cartWalletSelected'],
            'order_wallet_amount_charge' => $walletAmountCharge,
            'order_currency_id' => CommonHelper::getCurrencyId(),
            'order_currency_code' => CommonHelper::getCurrencyCode(),
            'order_currency_value' => CommonHelper::getCurrencyValue(),
            'order_pmethod_id' => $pmethodId,
            'order_discount_coupon_code' => $cartData['cartDiscounts']['coupon_code'] ?? '',
            'order_discount_total' => $coupon_discount_total,
            'order_discount_info' => $cartData['cartDiscounts']['coupon_info'] ?? '',
            'order_referral' => $referralName,
        ];
        $languageRow = Language::getAttributesById($this->siteLangId);
        $orderData['order_language_id'] = $languageRow['language_id'];
        $orderData['order_language_code'] = $languageRow['language_code'];
        // [
        $op_lesson_duration = $cartData['lessonDuration']; //FatApp::getConfig('conf_paid_lesson_duration', FatUtility::VAR_INT, 60);
        $cartData['op_commission_charged'] = 0;
        $cartData['op_commission_percentage'] = 0;
        if ($cartData['isFreeTrial']) {
            $op_lesson_duration = FatApp::getConfig('conf_trial_lesson_duration', FatUtility::VAR_INT, 30);
        } else {
            $commissionDetails = Commission::getTeacherCommission($cartData['teacherId'], $cartData['grpclsId']);
            if ($commissionDetails) {
                $cartData['op_commission_percentage'] = $commissionDetails['commsetting_fees'];
                $teacherCommission = ((100 - $commissionDetails['commsetting_fees']) * $cartData['itemPrice']) / 100;
            } else {
                $teacherCommission = $cartData['itemPrice'];
            }
            $teacherCommission = $teacherCommission;
            $cartData['op_commission_charged'] = $teacherCommission;
        }
        $products = [
            'op_grpcls_id' => $cartData['grpclsId'],
            'op_lpackage_is_free_trial' => $cartData['isFreeTrial'],
            'op_lesson_duration' => $op_lesson_duration,
            'op_teacher_id' => $cartData['user_id'],
            'op_qty' => 0 == $cartData['grpclsId'] ? $cartData['lessonQty'] : 1,
            'op_commission_charged' => $cartData['op_commission_charged'],
            'op_commission_percentage' => $cartData['op_commission_percentage'],
            'op_unit_price' => $cartData['itemPrice'],
            'op_tlanguage_id' => $cartData['languageId'],
        ];
        if (isset($_SESSION)) {
            $products['op_qty'] = $cartData['lessonQty'];
        }
        $productsLangData = [];
        $products['productsLangData'] = $productsLangData;
        $orderData['products'][] = $products;
        $order = new Order();

        if (!$order->addUpdate($orderData)) {
            Message::addErrorMessage($order->getError());
            FatUtility::dieWithError(Message::getHtml());
        }

        // ]
        $redirectUrl = '';
        $_SESSION['fromKids'] = $fromKids;
        $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
        $_SESSION['teacherId'] = $teacherId;
        $msg = Label::getLabel('LBL_Processing...', $this->siteLangId);
        if (0 >= $orderNetAmount) {
            $redirectUrl = CommonHelper::generateUrl('FreePay', 'Charge', [$order->getOrderId()], CONF_WEBROOT_FRONT_URL);
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        $userId = $_SESSION['Event_userId'];
        $userData = EventUser::getAttributesById($userId);
        $userWalletBalance = EventUser::getUserBalance($userId);
        if ($orderNetAmount > 0 && $cartData['cartWalletSelected'] && ($userWalletBalance >= $orderNetAmount) && !$pmethodId) {
            $redirectUrl = CommonHelper::generateUrl('EventWalletPay', 'Charge', [$order->getOrderId()], CONF_WEBROOT_FRONTEND);
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        if ($pmethodId > 0) {
            $controller = $paymentMethod['pmethod_code'] . 'Pay';
            $redirectUrl = CommonHelper::generateUrl($controller, 'charge', [$order->getOrderId(), 1, $currency, $currencyCode], CONF_WEBROOT_FRONTEND);
            FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => $msg]);
        }
        Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
        FatUtility::dieWithError(Message::getHtml());
    }
    //Donation plan paymentsummary
    public function RegisterForEvents($fromEventType = '', $checkLogged = 1, $userStatus = '')
    {
        // $userId = EventUserAuthentication::getLoggedUserId();
        $userId = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $userId = $_SESSION['Event_userId'];
        }
        if ($userStatus == 'Registration' && $checkLogged <= 0) {
            if ($checkLogged == 0) {
                $post = FatApp::getPostedData();
                if (!isset($post['user_first_name'])) {
                    $post['user_first_name'] = strstr($post['user_email'], '@', true);
                }
                $sponserShip = $post['sponsership'];
                if ($post == false) {
                    Message::addErrorMessage(Label::getLabel('MSG_ERROR'));
                    $this->set('msg', Label::getLabel('MSG_ERROR'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'registrationForm'));
                }
                if (true !== CommonHelper::validateUsername($post['user_first_name'])) {
                    $this->set('msg', Label::getLabel('MSG_USER_NAME_MUST_BE_THREE_CHARATERS_LONG'));
                    Message::addErrorMessage(Label::getLabel('MSG_USER_NAME_MUST_BE_THREE_CHARATERS_LONG'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                if (!CommonHelper::isValidEmail($post['user_email'])) {
                    $this->set('msg', Label::getLabel('MSG_EMAIL_MUST_BE_IN_VALID_FORMAT'));
                    Message::addErrorMessage(Label::getLabel('MSG_EMAIL_MUST_BE_IN_VALID_FORMAT'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                if (true !== CommonHelper::validatePassword($post['user_password'])) {
                    $this->set('msg', Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
                    Message::addErrorMessage(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                if ($post['user_password'] !== $post['conf_new_password']) {
                    $this->set('msg', Label::getLabel('MSG_CONFIRM_PASSWORD_MUST_BE_SAME_AS_PASSWORD'));
                    Message::addErrorMessage(Label::getLabel('MSG_CONFIRM_PASSWORD_MUST_BE_SAME_AS_PASSWORD'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                if (!isset($post['agree'])) {
                    $this->set('msg', Label::getLabel('MSG_MUST_ACCEPT_TERMS_AND_CONDITIONS'));
                    Message::addErrorMessage(Label::getLabel('MSG_MUST_ACCEPT_TERMS_AND_CONDITIONS'));
                    if (FatUtility::isAjaxCall()) {
                        FatUtility::dieWithError(Message::getHtml());
                    }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                $db = FatApp::getDb();
                $db->startTransaction();
                $user = new EventUser();
                /* saving user data[ */
                $post['user_is_learner'] = 1;
                $user_preferred_dashboard = EventUser::USER_LEARNER_DASHBOARD;
                $user_registered_initially_for = EventUser::USER_TYPE_LEANER;
                $posted_user_preferred_dashboard = FatApp::getPostedData('user_preferred_dashboard', FatUtility::VAR_INT, 0);
                if ($posted_user_preferred_dashboard == EventUser::USER_TEACHER_DASHBOARD) {
                    $user_preferred_dashboard = EventUser::USER_TEACHER_DASHBOARD;
                    $user_registered_initially_for = EventUser::USER_TYPE_TEACHER;
                }
                $post['user_sponsorship_plan'] = '';
                $post['user_timezone'] = $_COOKIE['user_timezone'] ?? MyDate::getTimeZone();;
                $post['user_preferred_dashboard'] = $user_preferred_dashboard;
                $post['user_registered_initially_for'] = $user_registered_initially_for;
                $post['credential_verified'] = applicationConstants::YES;
                $user->assignValues($post);
                $user->setUserInfo($post);
                if (true !== $user->save()) {
                    $db->rollbackTransaction();
                    $this->set('msg', Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $user->getError());
                    Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $user->getError());
                    // if (FatUtility::isAjaxCall()) {
                    //     FatUtility::dieWithError(Message::getHtml());
                    // }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                $record = new TableRecord('tbl_event_users');
                $arrFlds = [
                    'user_first_name' => $post['user_first_name'],
                    'user_email' => $post['user_email'],
                    'user_sponsorship_plan' => $sponserShip
                ];
                $record->assignValues($arrFlds);
                if (!$record->addNew([], $arrFlds)) {
                }
                $active = FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1) ? 1 : 1;
                $verify = FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1) ? 1 : 1;
                if (true !== $user->setLoginCredentials($post['user_email'], $post['user_email'], $post['user_password'], $active, $verify)) {
                    // Message::addErrorMessage(Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET") . $user->getError());
                    $this->set('msg', Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $user->getError());
                    $db->rollbackTransaction();
                    // if (FatUtility::isAjaxCall()) {
                    //     FatUtility::dieWithError(Message::getHtml());
                    // }
                    $this->RegisterDonationEventUserData($donation, 0, $checkLogged);
                    return;
                }
                /* ] */
                $db->commitTransaction();
                $_SESSION['Event_userId'] = $user->getMainTableRecordId();
                $authentication = new EventUserAuthentication();
                $userName = FatApp::getPostedData('user_email', FatUtility::VAR_STRING, '');
                $password = FatApp::getPostedData('user_password', FatUtility::VAR_STRING, '');
                if (true !== $authentication->login($userName, $password, CommonHelper::getClientIp())) {
                    FatUtility::dieWithError(Label::getLabel($authentication->getError()));
                }
                $userId = EventUserAuthentication::getLoggedUserId();
            }
        } else if ($userStatus = 'Login' && $checkLogged <= 0) {
            $authentication = new EventUserAuthentication();
            $userName = FatApp::getPostedData('username', FatUtility::VAR_STRING, '');
            $password = FatApp::getPostedData('password', FatUtility::VAR_STRING, '');
            if (true !== $authentication->login($userName, $password, CommonHelper::getClientIp())) {
                FatUtility::dieWithError(Label::getLabel($authentication->getError()));
            }
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
        }
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->set('msg', Label::getLabel("MSG_RECORD_UPDATED_SUCCESSFULLY..."));
        $this->set('fromEventType', $fromEventType);
        $this->_template->render(false, false, 'json-success.php');
        // $this->_template->render(false, false);
    }
    public function GetEventDonationPaymentSummary($donation = '', $checkLogged = 1)
    {
        $userId = EventUserAuthentication::getLoggedUserId();
        unset($_SESSION['reg_sponser']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);

        unset($_SESSION['concert_ticket']);
        unset($_SESSION['concertPlan']);
        //code for donation
        $_SESSION['donation'] = 'event_donation';
        $donation_record = new TableRecord('tbl_event_user_donation');
        $donation_record->assignValues([
            'event_user_user_id' => $userId,
            'event_user_donation_amount' => $donation,
            'event_user_donation_status' => EventUser::EVENT_DONATION_FAILURE
        ]);
        if (!$donation_record->addNew([], [])) {
            Message::addErrorMessage($donation_record->getError());
            throw new Exception('');
        }
        $donation_data = new SearchBase('tbl_event_user_donation');
        $donation_data->addCondition('event_user_user_id', '=', $userId);
        $donation_set = $donation_data->getResultSet();
        $donation_result = FatApp::getDb()->fetchAll($donation_set);
        $userLastDonationRecord = end($donation_result);
        $userObj = new EventUser();
        if ($checkLogged > 0) {
            $userRow = EventUser::getAttributesById($userId);
            $this->set('userData', $userRow);
        }
        $grpclsId = $userLastDonationRecord['event_user_donation_id'];
        $key = $userId . '_' . $grpclsId;
        $cartTotal = $donation;
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = 'Event Donation';
        $cartData['itemPrice'] = $cartTotal;
        $cartData['cartTotal'] = $cartTotal;
        $cartData['orderPaymentGatewayCharges'] = 10;
        $cartData['orderNetAmount'] = $cartTotal;
        $cartData['total'] = $cartTotal;
        $_SESSION['cart'] = $cartData;
        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->set('userWalletBalance', $userWalletBalance);

        $this->_template->render(false, false);
        // $this->_template->render(false, false);
    }

    // event ticket payment summary
    public function GetEventTicketsPaymentSummary($method = '', $ticketCount = 1, $checkLogged = 1, $currency = 'USD')
    {
        $_SESSION['Event_userId'] = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = EventUserAuthentication::getLoggedUserId();
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        //event plan ticket Summary
        $_SESSION['ticket_count'] = $ticketCount;
        $userObj = new EventUser();
        if ($userId > 0) {
            $userRow = EventUser::getAttributesById($userId);
            $this->set('userData', $userRow);
        }
        $planData = new SearchBase('tbl_three_reasons');
        $planData->addCondition('registration_plan_title', '=', $method);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());

        //  foreach ($sponsorshipList as $key => $value) {
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $planResult['three_reasons_id'], 0, -1);
        $planResult['plan_image'] = $testimonialImages;
        //       $Sponsershiprecords[$key] = $value;
        //}

        $grpclsId = $_SESSION['event_ticket_id'];
        $key = $userId . '_' . $grpclsId;
        // echo "<pre>";
        // print_r($planResult);
        $cartTotal = $planResult['registration_plan_price'] * $ticketCount;
        if ($currency == 'ZMW' || $currency == 'zmw') {
            $cartTotal = $planResult['registration_plan_zk_price'] * $ticketCount;
            $planPrice = $planResult['registration_plan_zk_price'];
        }
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = $method;
        $cartData['itemId'] = $planResult['three_reasons_id'];
        $cartData['itemPrice'] = $planPrice;
        $cartData['cartTotal'] = $cartTotal;
        $cartData['orderPaymentGatewayCharges'] = 1;
        $cartData['orderNetAmount'] = $cartTotal;
        $cartData['total'] = $cartTotal;
        $_SESSION['cart'] = $cartData;

        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $EventTicketsCouponCodeListing = new SearchBase('tbl_coupons');
        $EventTicketsCouponCodeListing->addCondition('coupon_identifier', '=', 'EventRegistration');
        $EventTicketsCCListing = $EventTicketsCouponCodeListing->getResultSet();
        $EventTicketsCouponCodeFinalListing = FatApp::getDb()->fetchAll($EventTicketsCCListing);
        $currencySwitcherData = new SearchBase('tbl_currencies_switcher');
        $currencySwitcherData->addCondition('currencies_switcher_active', '=', '1');
        $currencySwitcherData->addOrder('currencies_switcher_display_order', 'ASC');
        $currencySwitcherResultData = FatApp::getDb()->fetchall($currencySwitcherData->getResultSet());
        $selectedPlan = $cartData['itemId'];
        $registrationPlanData = new SearchBase('tbl_three_reasons');
        $registrationPlanData->addCondition('three_reasons_id', '=', $selectedPlan);
        $registrationPlanData->addCondition('three_reasons_active', '=', '1');
        $registrationPlanData->addCondition('three_reasons_deleted', '=', '0');
        $registrationPlanResultData = FatApp::getDb()->fetch($registrationPlanData->getResultSet());
        $this->set('registrationPlanResultData', $registrationPlanResultData);
        $this->set('currencySwitcherResultData', $currencySwitcherResultData);
        $this->set('EventTicketsCouponCodeFinalListing', $EventTicketsCouponCodeFinalListing);
        $this->set('planResult', $planResult);
        $this->set('tickets', $_SESSION['ticket_count']);
        $this->set('planSelected', $method);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->_template->render(false, false);
    }
    public function image($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_EVENT_PLAN_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'MINITHUMB':
                $w = 40;
                $h = 40;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'THUMB':
                $w = 50;
                $h = 50;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'HIGH':
                $w = 360;
                $h = 200;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }
    //BecomeSponser plan paymentsummary
    public function GetEventBecomeSponserPaymentSummary($method = null, $qty = null, $selectedPlan = null, $checkLogged = 1)
    {
        $userId = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
        }
        //become sponser
        $post = FatApp::getPostedData();
        $_SESSION['Event_userId'] = $userId;
        // $plan_json = json_decode($selectedPlan);
        // $allKeysOfPlan = array_keys((array)$plan_json);
        // $selectedEvent='';
        // foreach ($allKeysOfPlan as $select => $plan) {
        $sponser_record = new TableRecord('tbl_event_user_become_sponser');
        $sponser_record->assignValues([
            'event_user_id' => $userId,
            'event_user_sponsrship_id' => serialize($method),
            'event_user_sponsership_qty' => serialize($qty),
            'event_user_sponser_selected_plan' => $selectedPlan,
            'event_user_payment_status' => EventUser::EVENT_DONATION_FAILURE
        ]);
        if (!$sponser_record->addNew([], [])) {
            Message::addErrorMessage($donation_record->getError());
            throw new Exception('');
        }

        $eventData = new SearchBase('tbl_events_sponsorship_categories');
        $eventData->addCondition('events_sponsorship_categories_id', '=', $selectedPlan);
        $eventResult = FatApp::getDb()->fetch($eventData->getResultSet());
        $selectedEvent = $eventResult['events_sponsorship_categories_plan_title'];

        // }
        $use_plan_srch = new SearchBase('tbl_event_user_become_sponser');
        $use_plan_srch->addCondition('event_user_payment_status', '=', EventUser::EVENT_DONATION_FAILURE);
        $use_plan_srch->addCondition('event_user_id', '=', $userId);
        $userplanData = FatApp::getDb()->fetchAll($use_plan_srch->getResultSet());
        $lastPlanRecord = end($userplanData);
        $_SESSION['selected_sponsershipPlan_id'] = $lastPlanRecord['event_user_become_id'];
        $_SESSION['become_sponser'] = $method;
        $allPlan = new SearchBase('tbl_sponsorshipcategories');
        $allPlan->addCondition('sponsorshipcategories_deleted', '=', 0);
        $allPlanResult = FatApp::getDb()->fetchAll($allPlan->getResultSet());
        $userObj = new EventUser();
        $userRow = EventUser::getAttributesById($userId);
        $cartTotal = 0;
        $method_json = json_decode($method);
        $allKeysOfEmployee = array_keys((array)$method_json);
        $_SESSION['plan_Qty'] = $qty;
        $qty_json = json_decode($qty);
        $allValues = array_values((array)$qty_json);
        $qty_index = 0;
        $planTitle = array();
        $planPrice = array();
        $totalQty = 0;
        $planQty = array();
        foreach ($allKeysOfEmployee as $tempKey) {
            $planData = new SearchBase('tbl_sponsorshipcategories');
            $planData->addCondition('sponsorshipcategories_deleted', '=', 0);
            $planData->addCondition('sponsorshipcategories_id', '=', $tempKey);
            $planResult = FatApp::getDb()->fetch($planData->getResultSet());
            $cartTotal  = $cartTotal + ($planResult['sponsorshipcategories_plan_price'] * $allValues[$qty_index]);
            $totalQty = $totalQty + $allValues[$qty_index];
            array_push($planTitle, $planResult['sponsorshipcategories_name']);
            array_push($planQty, $allValues[$qty_index]);
            array_push($planPrice, ($planResult['sponsorshipcategories_plan_price']));
            $qty_index++;
        }
        $grpclsId = $lastPlanRecord['event_user_become_id'];
        $key = $userId . '_' . $grpclsId;
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
            'planTitle' => $planTitle,
            'planQty' => $planQty,
            'planPrice' => $planPrice,
            'selectedEvent' => $selectedEvent,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = $method;
        $cartData['itemPrice'] = $cartTotal;
        $cartData['cartTotal'] = $cartTotal;
        $cartData['total'] = $cartTotal;
        $cartData['selectedEvent'] = $selectedEvent;
        $cartData['orderPaymentGatewayCharges'] = 10;
        $cartData['orderNetAmount'] = $cartTotal;
        $cartData['planTitle'] = $planTitle;
        $cartData['planQty'] = $planQty;
        $cartData['planPrice'] = $planPrice;
        $_SESSION['cart'] = $cartData;
        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $WalletPaymentForm = $this->getWalletPaymentForm();
        $SponsorshipCouponCodeListing = new SearchBase('tbl_coupons');
        $SponsorshipCouponCodeListing->addCondition('coupon_identifier', '=', 'EventSponserShip');
        $SponsorshipCCListing = $SponsorshipCouponCodeListing->getResultSet();
        $SponsorshipCouponCodeFinalListing = FatApp::getDb()->fetchAll($SponsorshipCCListing);
        $this->set('SponsorshipCouponCodeFinalListing', $SponsorshipCouponCodeFinalListing);

        $this->set('become_plan', $method);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->set('userData', $userRow);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('WalletPaymentForm', $WalletPaymentForm);

        $this->_template->render(false, false);
    }
    //registration plan paymentsummary
    public function GetEventPaymentSummary($method = '')
    {
        // $userId = EventUserAuthentication::getLoggedUserId();
        $_SESSION['reg_sponser'] = 'tbl_three_reasons';
        $record = new TableRecord('tbl_event_users');
        $record->setFldValue('user_sponsorship_plan', $method);
        $_SESSION['Event_userId'] = EventUserAuthentication::getLoggedUserId();
        $userId = $_SESSION['Event_userId'];
        $userObj = new EventUser($userId);
        $userObj->setFldValue('user_sponsorship_plan', $method);
        $userObj->save();
        $userObj = new EventUser();
        $userRow = EventUser::getAttributesById($userId);
        $planData = new SearchBase('tbl_three_reasons');
        $planData->addCondition('registration_plan_title', '=', $method);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $grpclsId = $planResult['three_reasons_id'];
        $key = $userId . '_' . $grpclsId;
        $cartTotal = 300;
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = $method;
        $cartData['itemPrice'] = $planResult['registration_plan_price'];
        $cartData['cartTotal'] = $planResult['registration_plan_price'];
        $cartData['orderPaymentGatewayCharges'] = 10;
        $cartData['orderNetAmount'] = $planResult['registration_plan_price'];
        $_SESSION['cart'] = $cartData;
        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->set('userData', $userRow);
        $this->_template->render(false, false);
    }
    public function EventLogInFormPopUp($data = '')
    {
        if (EventUserAuthentication::isUserLogged()) {
            Message::addErrorMessage(Label::getLabel('MSG_Already_Logged_in,_Please_try_after_reloading_the_page'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLoginForm($data);
        $frm->setFormTagAttribute('name', 'frmLoginPopUp');
        $frm->setFormTagAttribute('id', 'frmLoginPopUp');
        $this->set('frm', $frm);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->_template->render(false, false);
    }
    public function EventSetUpLogin()
    {
        $authentication = new EventUserAuthentication();
        $userName = FatApp::getPostedData('username', FatUtility::VAR_STRING, '');
        $user_email = FatApp::getPostedData('user_email', FatUtility::VAR_STRING, '');
        $password = FatApp::getPostedData('password', FatUtility::VAR_STRING, '');
        if ($username == '') {
            $username = $user_email;
        }
        if (true !== $authentication->login($userName, $password, CommonHelper::getClientIp())) {
            FatUtility::dieWithError(Label::getLabel($authentication->getError()));
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $rememberme = FatApp::getPostedData('remember_me', FatUtility::VAR_INT, 0);
        $from_location = FatApp::getPostedData('from_location', FatUtility::VAR_STRING, '');
        if ($rememberme == applicationConstants::YES) {
            if (true !== $this->setUserLoginCookie($userId)) {
                //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
            }
        }
        if ($from_location != '') {
            FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('Events', ''), 'userId' => $userId, 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
        } else {
            FatUtility::dieJsonSuccess(['redirectUrl' => CommonHelper::generateUrl('DashboardEventVisitor', ''), 'userId' => $userId, 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
        }
        // FatUtility::dieJsonSuccess(['redirectUrl' => EventUser::getPreferedDashbordRedirectUrl(), 'userId' => $userId, 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function setUpLogin()
    {
        $authentication = new EventUserAuthentication();
        $userName = FatApp::getPostedData('username', FatUtility::VAR_STRING, '');
        $password = FatApp::getPostedData('password', FatUtility::VAR_STRING, '');
        if (true !== $authentication->login($userName, $password, CommonHelper::getClientIp())) {
            FatUtility::dieWithError(Label::getLabel($authentication->getError()));
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $rememberme = FatApp::getPostedData('remember_me', FatUtility::VAR_INT, 0);
        if ($rememberme == applicationConstants::YES) {
            if (true !== $this->setUserLoginCookie($userId)) {
                //Message::addErrorMessage(Label::getLabel('MSG_Problem_in_configuring_remember_me'));
            }
        }
        FatUtility::dieJsonSuccess(['redirectUrl' => EventUser::getPreferedDashbordRedirectUrl(), 'msg' => Label::getLabel("MSG_LOGIN_SUCCESSFULL")]);
    }
    public function registrationForm()
    {
        $frm = $this->getSignUpForm();
        $this->set('frm', $frm);
        /* [ */
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $this->_template->render(true, true, 'event-user/registration-form.php');
    }
    public function EventSignUpFormPopUp()
    {
        $json = [];
        $json['status'] = true;
        $json['msg'] = Label::getLabel('LBL_Request_Processing..');
        $post = FatApp::getPostedData();
        $userType = EventUser::USER_TYPE_LEANER;
        if (EventUserAuthentication::isUserLogged()) {
            if ($post['signUpType'] == "teacher") {
                $user_preferred_dashboard = EventUser::USER_TEACHER_DASHBOARD;
            } else {
                $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), ['user_preferred_dashboard']);
                $user_preferred_dashboard = $userRow['user_preferred_dashboard'];
            }
            $json['redirectUrl'] = EventUser::getPreferedDashbordRedirectUrl($user_preferred_dashboard, false);
            FatUtility::dieJsonSuccess($json);
        }
        $user_preferred_dashboard = EventUser::USER_LEARNER_DASHBOARD;
        if ($post['signUpType'] == "teacher") {
            $user_preferred_dashboard = EventUser::USER_TEACHER_DASHBOARD;
            $userType = EventUser::USER_TYPE_TEACHER;
        }
        $frm = $this->getSignUpForm();
        $frm->setFormTagAttribute('name', 'frmRegisterPopUp');
        $frm->setFormTagAttribute('id', 'frmRegisterPopUp');
        $frm->fill(['user_preferred_dashboard' => $user_preferred_dashboard]);
        $this->set('frm', $frm);
        /* [ */
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('userType', $userType);
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $json['html'] = $this->_template->render(false, false, 'event-user/event-sign-up-form-pop-up.php', true, false);
        FatUtility::dieJsonSuccess($json);
    }
    public function EventSetUpSignUp()
    {
        $frm = $this->getSignUpForm();
        $post = FatApp::getPostedData();
        if (!isset($post['user_first_name'])) {
            $post['user_first_name'] = strstr($post['user_email'], '@', true);
        }
        $sponserShip = $post['sponsership'];
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'registrationForm'));
        }
        if (true !== CommonHelper::validatePassword($post['user_password'])) {
            Message::addErrorMessage(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $user = new EventUser();
        /* saving user data[ */
        $post['user_is_learner'] = 1;
        $user_preferred_dashboard = EventUser::USER_LEARNER_DASHBOARD;
        $user_registered_initially_for = EventUser::USER_TYPE_LEANER;
        $posted_user_preferred_dashboard = FatApp::getPostedData('user_preferred_dashboard', FatUtility::VAR_INT, 0);
        if ($posted_user_preferred_dashboard == EventUser::USER_TEACHER_DASHBOARD) {
            $user_preferred_dashboard = EventUser::USER_TEACHER_DASHBOARD;
            $user_registered_initially_for = EventUser::USER_TYPE_TEACHER;
        }
        $post['user_sponsorship_plan'] = '';
        $post['user_timezone'] = $_COOKIE['user_timezone'] ?? MyDate::getTimeZone();;
        $post['user_preferred_dashboard'] = $user_preferred_dashboard;
        $post['user_registered_initially_for'] = $user_registered_initially_for;
        $post['credential_verified'] = applicationConstants::YES;
        $user->assignValues($post);
        $user->setUserInfo($post);
        if (true !== $user->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $user->getError());
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        $record = new TableRecord('tbl_event_users');
        $arrFlds = [
            'user_first_name' => $post['user_first_name'],
            'user_email' => $post['user_email'],
            'user_sponsorship_plan' => $sponserShip
        ];
        $record->assignValues($arrFlds);
        if (!$record->addNew([], $arrFlds)) {
        }
        $active = FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1) ? 1 : 1;
        $verify = FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1) ? 1 : 1;
        if (true !== $user->setLoginCredentials($post['user_email'], $post['user_email'], $post['user_password'], $active, $verify)) {
            Message::addErrorMessage(Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET") . $user->getError());
            $db->rollbackTransaction();
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Message::getHtml());
            }
            $this->registrationForm();
            return;
        }
        /* ] */
        $db->commitTransaction();
        $_SESSION['Event_userId'] = $user->getMainTableRecordId();
        $redirectUrl = CommonHelper::redirectUserReferer(true);
        if ($user->getMainTableRecordId() and $user_registered_initially_for == EventUser::USER_TYPE_TEACHER) {
            $_SESSION[EventUserAuthentication::SESSION_GUEST_USER_ELEMENT_NAME] = $user->getMainTableRecordId();
            $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
        } else {
            unset($_SESSION[EventUserAuthentication::SESSION_GUEST_USER_ELEMENT_NAME]);
        }
        if (1 == FatApp::getConfig('CONF_NOTIFY_ADMIN_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $user->notifyAdminRegistration($post, $this->siteLangId)) {
                Message::addErrorMessage(Label::getLabel("MSG_NOTIFICATION_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
        }
        if (1 == FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $this->sendEmailVerificationLink($user, $post)) {
                Message::addErrorMessage(Label::getLabel("MSG_VERIFICATION_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
            Message::addMessage(Label::getLabel('MSG_VERIFICATION_EMAIL_SENT'));
            $this->set('msg', Label::getLabel('MSG_VERIFICATION_EMAIL_SENT'));
            $this->set('redirectUrl', $redirectUrl);
            $this->_template->render(false, false, 'json-success.php');
        }
        if (1 == FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1)) {
            if (true !== $this->sendSignUpWelcomeEmail($user, $post)) {
                Message::addErrorMessage(Label::getLabel("MSG_WELCOME_EMAIL_COULD_NOT_BE_SENT"));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
        }
        $confAutoLoginRegisteration = FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION', FatUtility::VAR_INT, 1);
        if (1 === $confAutoLoginRegisteration && (0 === FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1))) {
            $authentication = new EventUserAuthentication();
            if (true != $authentication->login(FatApp::getPostedData('user_email'), FatApp::getPostedData('user_password'), $_SERVER['REMOTE_ADDR'])) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                if (FatUtility::isAjaxCall()) {
                    FatUtility::dieWithError(Message::getHtml());
                }
                $this->registrationForm();
                return;
            }
            Message::addMessage(Label::getLabel('LBL_Registeration_Successfull'));
            $redirectUrl = EventUser::getPreferedDashbordRedirectUrl();
            $this->set('redirectUrl', $redirectUrl);
            $this->set('msg', Label::getLabel('LBL_Registeration_Successfull'));
            $this->_template->render(false, false, 'json-success.php');
        }
        Message::addMessage(Label::getLabel('LBL_Registeration_Successfull'));
        $this->set('msg', Label::getLabel('LBL_Registeration_Successfull'));
        $this->set('redirectUrl', $redirectUrl);
        $this->_template->render(false, false, 'json-success.php');
    }
    public function registrationSuccess()
    {
        if (1 === FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1)) {
            $this->set('registrationMsg', Label::getLabel("MSG_SUCCESS_USER_SIGNUP_EMAIL_VERIFICATION_PENDING"));
        } else {
            $this->set('registrationMsg', Label::getLabel("MSG_SUCCESS_USER_SIGNUP_ADMIN_APPROVAL_PENDING"));
        }
        $this->_template->render();
    }
    public function userCheckEmailVerification($code)
    {
        $code = FatUtility::convertToType($code, FatUtility::VAR_STRING, '');
        if (strlen($code) < 1) {
            Message::addMessage(Label::getLabel("MSG_PLEASE_CHECK_YOUR_EMAIL_IN_ORDER_TO_VERIFY"));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $codeArr = explode('_', $code, 2);
        $userId = FatUtility::int($codeArr[0]);
        if ($userId < 1) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $userObj = new EventUser($userId);
        $userData = EventUser::getAttributesById($userId, ['user_id',]);
        if (!$userData || $userData['user_id'] != $userId) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $srch = new SearchBase('tbl_user_credentials');
        $srch->addCondition('credential_user_id', '=', $userId);
        $rs = $srch->getResultSet();
        $userCredentialRow = $db->fetch($rs);
        if (applicationConstants::ACTIVE === $userCredentialRow['credential_verified']) {
            Message::addErrorMessage(Label::getLabel('MSG_Your_Account_Is_Already_Verified'));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        if (applicationConstants::ACTIVE !== $userCredentialRow['credential_active']) {
            $active = FatApp::getConfig('CONF_ADMIN_APPROVAL_REGISTRATION', FatUtility::VAR_INT, 1) ? 0 : 1;
            if (0 === $userObj->activateAccount($active)) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
            }
        }
        if (true !== $userObj->verifyAccount()) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_CODE'));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $db->commitTransaction();
        $userdata = $userObj->getUserInfo([
            'credential_email',
            'credential_password',
            'user_first_name',
            'user_last_name',
            'credential_active'
        ], false);
        if (1 === FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1)) {
            $data['user_email'] = $userdata['credential_email'];
            $data['user_first_name'] = $userdata['user_first_name'];
            $data['user_last_name'] = $userdata['user_last_name'];
            if (true !== $this->sendSignUpWelcomeEmail($userObj, $data)) {
                Message::addErrorMessage(Label::getLabel("MSG_WELCOME_EMAIL_COULD_NOT_BE_SENT"));
                $db->rollbackTransaction();
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'loginForm'));
            }
        }
        Message::addMessage(Label::getLabel("MSG_EMAIL_VERIFIED_SUCCESFULLY"));
        FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
    }
    public function logout()
    {
        unset($_SESSION[EventUserAuthentication::SESSION_ELEMENT_NAME]);
        unset($_SESSION['referer_page_url']);
        EventUserAuthentication::clearLoggedUserLoginCookie();
        FatApp::redirectUser(CommonHelper::generateUrl('Events', ''));
    }
    private function setUserLoginCookie(int $userId)
    {
        $expiryDays = FatApp::getConfig('CONF_USER_REMEMBER_ME_DAYS', FatUtility::VAR_INT);
        $token = md5(uniqid('t', true));
        $expiry = strtotime("+" . $expiryDays . " DAYS");
        $values = [
            'uauth_user_id' => $userId,
            'uauth_token' => $token,
            'uauth_expiry' => date('Y-m-d H:i:s', $expiry),
            'uauth_browser' => CommonHelper::userAgent(),
            'uauth_last_access' => date('Y-m-d H:i:s'),
            'uauth_last_ip' => CommonHelper::getClientIp()
        ];
        if (EventUserAuthentication::saveLoginToken($values)) {
            $cookieName = EventUserAuthentication::YOCOACHUSER_COOKIE_NAME;
            CommonHelper::setCookie($cookieName, $token, $expiry, CONF_WEBROOT_FRONTEND, '', true);
            return true;
        }
        return false;
    }
    private function getSignUpForm($id = -1)
    {
        $frm = new Form('frmRegister');
        $frm->addHiddenField('', 'user_id', 0);
        $fld = $frm->addTextBox(Label::getLabel('LBL_First_Name'), 'user_first_name');
        $fld->requirements()->setRequired();
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $fld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_First_Name'), '{A-Z,a-z and 0-9}'));
        $fld = $frm->addTextBox(Label::getLabel('LBL_Last_Name'), 'user_last_name');
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
        $fld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Enter_Valid_%S_%S'), Label::getLabel('LBL_Last_Name'), '{A-Z,a-z and 0-9}'));
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email_ID'), 'user_email', '', ['autocomplete="off"']);
        $fld->setUnique('tbl_event_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        if ($id <= 0) {
            $fld = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'user_password');
            $fld->requirements()->setRequired();
            $conNewPwd = $frm->addPasswordField(Label::getLabel('LBL_Show_Password', $this->adminLangId), 'conf_new_password', '', ['id' => 'conf_new_password']);
            $conNewPwdReq = $conNewPwd->requirements();
            $conNewPwdReq->setRequired();
            $conNewPwdReq->setCompareWith('user_password', 'eq');
            $conNewPwdReq->setCustomErrorMessage(Label::getLabel('LBL_Confirm_Password_Not_Matched!', $this->adminLangId));
            $fld->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
            $fld->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
            $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_8_Digit_AlphaNumeric_Password'));
            // $Registration_Plan_data=new SearchBase('tbl_three_reasons');
            // $Registration_Plan_data->addCondition('three_reasons_deleted', '=', '0');
            // $Registration_Plan_data->addCondition('three_reasons_active', '=', '1');
            // $Registration_dropdown_value = FatApp::getDb()->fetchAll($Registration_Plan_data->getResultSet());
            // $Registration_function=array();
            // foreach ($Registration_dropdown_value as $key => $value) {
            //     $Registration_function[$value['registration_plan_title']] = $value['registration_plan_title'];
            // }
            // $fld = $frm->addSelectBox(Label::getLabel('LBL_Sponsorship_Plan', $langId), 'pjname', $Registration_function, -1, [], '');
            // $fld->requirements()->setRequired();
            //$fld->requirements()->setRegularExpressionToValidate(applicationConstants::NAME_REGEX);
            // $fld->requirements()->setCustomErrorMessage(sprintf(Label::getLabel('MSG_Please_Select_Plan'), Label::getLabel('LBL_Sponsorship_Plan')));
            $termsConditionLabel = Label::getLabel('LBL_I_accept_to_the');
            $fld = $frm->addCheckBox($termsConditionLabel, 'agree', 1);
            $fld->requirements()->setRequired();
            $fld->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Terms_and_Condition_and_Privacy_Policy_are_mandatory.'));
        }
        $frm->addHiddenField('', 'user_preferred_dashboard', EventUser::USER_LEARNER_DASHBOARD);
        if ($id < 0) {
            $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_NEXT'));
        }
        return $frm;
    }
    public function GetEventBecomeSponserFillData()
    {
    }
    public function getSponserQtyPrice()
    {
        $lessonQty = FatApp::getPostedData('lessonQty', FatUtility::VAR_INT, 0);
        $plan_id = FatApp::getPostedData('planId', FatUtility::VAR_INT, 0);
        $planData = new SearchBase('tbl_sponsorshipcategories');
        $planData->addCondition('sponsorshipcategories_deleted', '=', 0);
        $planData->addCondition('sponsorshipcategories_id', '=', $plan_id);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $price = FatUtility::float($planResult['sponsorshipcategories_plan_price']);
        $price = ($price) * $lessonQty;
        $data = [
            'msg' => '',
            'price' => $price,
            'priceLabel' => sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($price)),
        ];
        FatUtility::dieJsonSuccess($data);
    }

    //Become Select Events sponser sponsership list
    public function GetSelectEventBecomeSponserPlan($checked = 1)
    {
        $userId = 0;
        unset($_SESSION['donation']);
        unset($_SESSION['summary']);
        unset($_SESSION['walletSummary']);
        unset($_SESSION['removeCoupon']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['cart']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);

        unset($_SESSION['concert_ticket']);
        unset($_SESSION['concertPlan']);
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
        }
        $_SESSION['Event_userId'] = $userId;
        $planData = new SearchBase('tbl_events_sponsorship_categories');
        $planData->addCondition('events_sponsorship_categories_deleted', '=', 0);
        $planData->addCondition('events_sponsorship_categories_active', '=', 1);
        $planData->addOrder('events_sponsorship_categories_display_order', 'ASC');
        $planResult = FatApp::getDb()->fetchAll($planData->getResultSet());
        if (empty($planResult)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_PLAN_AVAIABLE'));
        }
        if (isset($_SESSION['become_sponser'])) {
            $plan = $_SESSION['become_sponser'];
            $this->set('method', $_SESSION['become_sponser']);
        } else {
            unset($_SESSION['become_sponser']);
            $this->set('method', $planResult[0]['events_sponsorship_categories_plan_title']);
        }
        $this->set('slotDurations', $planResult);
        $this->_template->render(false, false);
    }


    //Become sponser sponsership list
    public function GetEventBecomeSponserPlan($selectSponserEventPlan = '', $checked = 1)
    {
        $userId = 0;
        unset($_SESSION['donation']);
        unset($_SESSION['summary']);
        unset($_SESSION['removeCoupon']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['cart']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);

        unset($_SESSION['concert_ticket']);
        unset($_SESSION['concertPlan']);
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
        }
        $planDataCat = new SearchBase('tbl_events_sponsorship_categories');
        $planDataCat->addCondition('events_sponsorship_categories_deleted', '=', 0);
        $planDataCat->addCondition('events_sponsorship_categories_active', '=', 1);
        $planDataCat->addCondition('events_sponsorship_categories_id', '=', $selectSponserEventPlan);
        $planResultCat = FatApp::getDb()->fetch($planDataCat->getResultSet());

        $_SESSION['Event_userId'] = $userId;
        $planData = new SearchBase('tbl_sponsorshipcategories');
        $planData->addCondition('sponsorshipcategories_deleted', '=', 0);
        if (!empty($planResultCat) && $planResultCat['events_sponsorship_categories_plan_title'] == 'Pre Symposium Dinner') {

            $planData->addCondition('sponsorshipcategories_type', '=', 'Dinner');
        } else {
            $planData->addCondition('sponsorshipcategories_type', '=', 'Regular');
        }
        $planResult = FatApp::getDb()->fetchAll($planData->getResultSet());
        if (empty($planResult)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_PLAN_AVAIABLE'));
        }
        if (isset($_SESSION['become_sponser'])) {
            $plan = $_SESSION['become_sponser'];
            $this->set('method', $_SESSION['become_sponser']);
        } else {
            unset($_SESSION['become_sponser']);
            $this->set('method', $planResult[0]['sponsorshipcategories_name']);
        }
        $this->set('slotDurations', $planResult);
        $this->_template->render(false, false);
    }
    public function GetEventDonation($fromPayment = 0, $checkLogged = 1, $donationAmount = 1)
    {
        $userId = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
        }
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);
        unset($_SESSION['walletSummary']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);

        unset($_SESSION['concert_ticket']);
        unset($_SESSION['concertPlan']);
        $_SESSION['Event_userId'] = $userId;
        if ($fromPayment == 1) {
            $donation_data = new SearchBase('tbl_event_user_donation');
            $donation_data->addCondition('event_user_user_id', '=', $userId);
            $donation_set = $donation_data->getResultSet();
            $donation_result = FatApp::getDb()->fetchAll($donation_set);
            if (empty($donation_result)) {
                $userLastDonationRecord['event_user_donation_amount'] = $donationAmount;
            } else {
                $userLastDonationRecord = end($donation_result);
            }
        } else {
            $userLastDonationRecord['event_user_donation_amount'] = 1;
        }
        $this->set('donationAmount', $donationAmount);
        $this->set('donation', $userLastDonationRecord);
        $this->_template->render(false, false);
    }
    public function RegisterPlanEventUserData($fromEvent = '', $fromBack = 0, $checked = 0)
    {
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
            // $userId=$_SESSION['Event_userId'];
        }
        $frm = $this->getSignUpForm($userId);
        if (0 < $userId) {
            $data = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $usersrch = new SearchBase('tbl_event_user_credentials');
            $usersrch->addCondition('credential_user_id', '=', $data['user_id']);
            $usersrchData = FatApp::getDb()->fetch($usersrch->getResultSet());
            $data['user_email'] = $usersrchData['credential_email'];
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $loginFrm = $this->getLoginForm('', 1);
        $this->set('loginFrm', $loginFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('userId', $userId);
        $this->set('method', $fromEvent);
        $this->set('ticket', $fromBack);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }
    public function walletSelection()
    {
        $payFromWallet = FatApp::getPostedData('payFromWallet', FatUtility::VAR_INT, 0);
        // $this->cartObj->updateCartWalletOption($payFromWallet, $fromKids);
        $cart = $_SESSION['cart'];
        if (isset($_SESSION['summary'])) {
            $cart = $_SESSION['summary'];
        }
        $cart['Pay_from_wallet'] = $payFromWallet;
        $userWalletBalance = EventUser::getUserBalance($cart['user_id']);
        if ($payFromWallet > 0 && $userWalletBalance > 0) {

            $cartTotal = $cart['total'];
            $cartTaxTotal = 0;
            $totalSiteCommission = 0;
            $cartDiscounts = $cart['cartDiscounts'] ?? [];
            $totalDiscountAmount = $cartDiscounts['coupon_discount_total'] ?? 0;
            $orderNetAmount = ($cartTotal + $cartTaxTotal) - $totalDiscountAmount;
            $walletAmountCharge = min($orderNetAmount, $userWalletBalance);
            $orderPaymentGatewayCharges = $orderNetAmount - $walletAmountCharge;

            $summaryArr = [
                'cartTotal' => $cartTotal,
                'cartTaxTotal' => $cartTaxTotal,
                'cartWalletSelected' => $payFromWallet,
                'siteCommission' => $totalSiteCommission,
                'orderNetAmount' => $orderNetAmount,
                'walletAmountCharge' => $walletAmountCharge,
                'orderPaymentGatewayCharges' => $orderPaymentGatewayCharges,
            ];
            $newData = array_merge($cart, $summaryArr);

            $_SESSION['walletSummary'] = $newData;
            $_SESSION['cart'] = $cart;
        } else {
            unset($_SESSION['walletSummary']);
            $_SESSION['cart'] = $cart;
        }

        FatUtility::dieJsonSuccess('');
    }
    public function eventApplyPromoCode()
    {
        unset($_SESSION['summary']);

        $couponCode = FatApp::getPostedData('coupon_code', FatUtility::VAR_STRING, '');
        $fromSelector = FatApp::getPostedData('fromSelector', FatUtility::VAR_STRING, '');
        $loggedUserId = EventUserAuthentication::getLoggedUserId();
        if (empty($couponCode) || empty($loggedUserId)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request', $this->siteLangId));
        }
        $pendingOrderHoldSrch = new SearchBase('tbl_coupons');
        $pendingOrderHoldSrch->addCondition('coupon_code', '=', $couponCode);
        $pendingOrderHoldSrch->addCondition('coupon_start_date', '<=', date('Y-m-d'));
        $pendingOrderHoldSrch->addCondition('coupon_end_date', '>=', date('Y-m-d'));
        if ($fromSelector == 'fromSponser') {
            $pendingOrderHoldSrch->addCondition('coupon_identifier', '=', 'EventSponserShip');
        } else if ($fromSelector == 'registrationPlan') {
            $pendingOrderHoldSrch->addCondition('coupon_identifier', '=', 'EventRegistration');
        } else if ($fromSelector == 'benefitConcertPlan') {
            $pendingOrderHoldSrch->addCondition('coupon_identifier', '=', 'BenefitConcert');
        } else if ($fromSelector == 'SymposiumDinnerPlan') {
            $pendingOrderHoldSrch->addCondition('coupon_identifier', '=', 'SymposiumDinnerPlan');
        } else {
            $pendingOrderHoldSrch->addCondition('coupon_identifier', '=', 'Events');
        }
        $rs = $pendingOrderHoldSrch->getResultSet();
        $couponInfo = FatApp::getDb()->fetch($rs, 'coupon_id');
        // $couponInfo = DiscountCoupons::getValidCoupons($loggedUserId, $this->siteLangId, $couponCode);
        if ($couponInfo == false) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Coupon_Code', $this->siteLangId));
        }
        $cartData = $_SESSION['cart'];
        if (isset($_SESSION['walletSummary'])) {
            $cartData = $_SESSION['walletSummary'];
        }
        $holdCouponData = [
            'couponhold_coupon_id' => $couponInfo['coupon_id'],
            'couponhold_user_id' => EventUserAuthentication::getLoggedUserId(),
            'couponhold_added_on' => date('Y-m-d H:i:s'),
        ];
        $cartSubTotal = $cartData['total'];
        $couponData = [];
        if ($couponInfo['coupon_uses_coustomer'] <= $couponInfo['coupon_uses_count']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Coupon_Code', $this->siteLangId));
        }
        if ($couponInfo) {
            $labelArr = [
                'coupon_label' => $couponInfo["coupon_title"],
                'coupon_id' => $couponInfo["coupon_id"],
                'coupon_discount_in_percent' => $couponInfo["coupon_discount_in_percent"],
                'max_discount_value' => $couponInfo["coupon_max_discount_value"]
            ];
            if ($couponInfo['coupon_discount_in_percent'] == applicationConstants::PERCENTAGE) {
                $couponDiscountValue = $cartSubTotal * $couponInfo['coupon_discount_value'] / 100;
            } elseif ($couponInfo['coupon_discount_in_percent'] == applicationConstants::FLAT) {
                $couponDiscountValue = $couponInfo["coupon_discount_value"];
            }
            if ($cartSubTotal < $couponDiscountValue) {
                $couponDiscountValue = $cartSubTotal;
            }
            if ($couponInfo['coupon_discount_in_percent'] == applicationConstants::PERCENTAGE && $couponDiscountValue > $couponInfo["coupon_max_discount_value"]) {
                $couponDiscountValue = $couponInfo["coupon_max_discount_value"];
            }
            $couponData = [
                'coupon_discount_type' => $couponInfo["coupon_type"],
                'coupon_code' => $couponInfo["coupon_code"],
                'coupon_discount_value' => $couponInfo["coupon_discount_value"],
                'coupon_discount_total' => $couponDiscountValue,
                'coupon_info' => json_encode($labelArr),
            ];
        }
        if (empty($couponData)) {
            return false;
        }
        $totalSiteCommission = 0;
        $cartTaxTotal = 0;
        $cartTotal = $cartSubTotal;
        $totalDiscountAmount = $couponData['coupon_discount_total'] ?? 0;
        $orderNetAmount = ($cartTotal + $cartTaxTotal) - $totalDiscountAmount;
        $walletAmountCharge = 0;
        $orderPaymentGatewayCharges = $orderNetAmount - $walletAmountCharge;
        $summaryArr = [
            'cartTotal' => $cartTotal,
            'cartTaxTotal' => $cartTaxTotal,
            'cartDiscounts' => $couponData,
            'siteCommission' => $totalSiteCommission,
            'orderNetAmount' => $orderNetAmount,
            'walletAmountCharge' => $walletAmountCharge,
            'orderPaymentGatewayCharges' => $orderPaymentGatewayCharges,
        ];
        $newData = array_merge($cartData, $summaryArr);
        $_SESSION['summary'] = $newData;
        $_SESSION['cart'] = $newData;
        FatUtility::dieJsonSuccess(Label::getLabel("MSG_cart_discount_coupon_applied", $this->siteLangId));
    }
    public function eventremovePromoCode()
    {
        $cartObj = $_SESSION['cart'];
        $couponCode = array_key_exists('discount_coupon', $cartObj) ? $cartObj['discount_coupon'] : '';
        unset($cartObj['discount_coupon']);
        /* Removing from temp hold[ */
        if ($couponCode != '') {
            $loggedUserId = EventUserAuthentication::getLoggedUserId();
            $srch = new SearchBase('tbl_coupons');
            $srch->addCondition('coupon_code', '=', $couponCode);
            $srch->setPageSize(1);
            $srch->addMultipleFields(['coupon_id']);
            $rs = $srch->getResultSet();
            $couponRow = FatApp::getDb()->fetch($rs);
            if ($couponRow && $loggedUserId) {
                FatApp::getDb()->deleteRecords('tbl_coupons_hold', ['smt' => 'couponhold_coupon_id = ? AND couponhold_user_id = ?', 'vals' => [$couponRow['coupon_id'], $loggedUserId]]);
            }
        }
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        if ($orderId != '') {
            FatApp::getDb()->deleteRecords('tbl_coupons_hold_pending_order', ['smt' => 'ochold_order_id = ?', 'vals' => [$orderId]]);
        }
        // if (!$cartObj->removeCartDiscountCoupon()) {
        //     FatUtility::dieJsonSuccess(Label::getLabel("LBL_Action_Trying_Perform_Not_Valid", $this->siteLangId));
        // }
        if (isset($cartObj) && array_key_exists('reward_points', $cartObj)) {
            unset($cartObj['reward_points']);
            $_SESSION['cart'] = $cartObj;
            // $this->updateUserCart(); 
        }
        unset($_SESSION['summary']);
        $_SESSION['removeCoupon'] = $_SESSION['cart'];
        FatUtility::dieJsonSuccess(Label::getLabel("MSG_cart_discount_coupon_removed", $this->siteLangId));
    }
    public function RegisterEventUserData($fromEvent = '', $fromBack = 0, $checked = 1)
    {
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
            // $userId=$_SESSION['Event_userId'];
        }
        $frm = $this->getSignUpForm($userId);
        if (0 < $userId) {
            $data = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $usersrch = new SearchBase('tbl_event_user_credentials');
            $usersrch->addCondition('credential_user_id', '=', $userId);
            $usersrchData = FatApp::getDb()->fetch($usersrch->getResultSet());
            $data['user_email'] = $usersrchData['credential_email'];
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $loginFrm = $this->getLoginForm('', 1);
        $this->set('loginFrm', $loginFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('userId', $userId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }
    public function RegisterDonationEventUserData($fromEvent = '', $fromBack = 0, $checked = 1)
    {
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
            // $userId=$_SESSION['Event_userId'];
        }
        $frm = $this->getSignUpForm($userId);
        if (0 < $userId) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $data = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $usersrch = new SearchBase('tbl_event_user_credentials');
            $usersrch->addCondition('credential_user_id', '=', $userId);
            $usersrchData = FatApp::getDb()->fetch($usersrch->getResultSet());
            $data['user_email'] = $usersrchData['credential_email'];
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $loginFrm = $this->getLoginForm('', 1);
        $this->set('loginFrm', $loginFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('userId', $userId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    //Get Symposium Plans

    public function GetSymposiumPlan($fromBack = 0)
    {
        unset($_SESSION['donation']);
        unset($_SESSION['cart']);
        unset($_SESSION['reg_sponser']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['summary']);
        unset($_SESSION['removeCoupon']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['walletSummary']);
        unset($_SESSION['event_ticket_id']);
        $planData = new SearchBase('tbl_pre_symposium_dinner');
        $planData->addCondition('pre_symposium_dinner_deleted', '=', 0);
        $planData->addCondition('pre_symposium_dinner_active', '=', 1);
        $planData->addCondition('pre_symposium_dinner_ending_date', '>', date('Y-m-d h:i'));
        $planData->addOrder('pre_symposium_dinner_id', 'ASC');
        $planResult = FatApp::getDb()->fetchAll($planData->getResultSet());
        if (empty($planResult)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_PLAN_AVAIABLE'));
        }
        if ($fromBack <= 0) {
            unset($_SESSION['symposiumPlan']);
        }
        if (isset($_SESSION['symposiumPlan'])) {
            $this->set('planSelected', $_SESSION['symposiumPlan']);
        }
        $tickets = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $tickets->addCondition('event_user_ticket_pay_status', '=', 1);
        $tickets->addMultipleFields(['SUM(event_user_ticket_count) as TotalTicket', 'event_user_pre_symposium_dinner_id']);
        $tickets->addGroupBy('event_user_pre_symposium_dinner_id');
        $tickets->addOrder('event_user_pre_symposium_dinner_id', 'ASC');
        $ticketManage = FatApp::getDb()->fetchAll($tickets->getResultSet());
        $this->set('ticketManage', $ticketManage);
        $this->set('slotDurations', $planResult);
        $this->_template->render(false, false);
    }
    //Concert Tickets
    public function GetSymposiumTickets($planSelected = '', $checked = 1, $fromPlan = 0, $ticketCount = 1)
    {
        $_SESSION['symposiumPlan'] = $planSelected;
        $this->set('planSelected', $planSelected);
        if ($fromPlan <= 0) {
            unset($_SESSION['symposium_ticket']);
        }
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
        }
        $planData = new SearchBase('tbl_pre_symposium_dinner');
        $planData->addCondition('pre_symposium_dinner_plan_title', '=', $planSelected);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $donation_record = new TableRecord('tbl_pre_symposium_dinner_ticket_plan');
        $donation_record->assignValues([
            'event_user_id' => $userId,
            'event_user_pre_symposium_dinner_id' => $planResult['pre_symposium_dinner_id'],
            'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_FAILURE
        ]);
        if (!$donation_record->addNew([], [])) {
            Message::addErrorMessage($donation_record->getError());
            throw new Exception('');
        }
        $tickets = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $tickets->addCondition('event_user_ticket_pay_status', '=', 1);
        $tickets->addCondition('event_user_pre_symposium_dinner_id', '=', $planResult['pre_symposium_dinner_id']);
        $tickets->addMultipleFields(['SUM(event_user_ticket_count) as TotalTicket', 'event_user_pre_symposium_dinner_id']);
        $tickets->addGroupBy('event_user_pre_symposium_dinner_id');

        $ticketManager = $tickets->getResultSet();
        $ticketManagerDetails = FatApp::getDb()->fetch($ticketManager);

        $ticketSrch = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $ticketSrch->addCondition('event_user_pre_symposium_dinner_id', '=', $planResult['pre_symposium_dinner_id']);
        if ($checked > 0) {
            $ticketSrch->addCondition('event_user_id', '=', $userId);
        }
        $ticketData = FatApp::getDb()->fetchAll($ticketSrch->getResultSet());
        $lastRecord = end($ticketData);

        $_SESSION['pre_symposium_dinner_ticket_plan_id'] = $lastRecord['pre_symposium_dinner_ticket_plan_id'];
        if (isset($_SESSION['symposium_ticket'])) {
            $this->set('tickets', $_SESSION['symposium_ticket']);
        } else {
            $this->set('tickets', $ticketCount);
        }
        $this->set('planResult', $planResult);
        $this->set('ticketManagerDetails', $ticketManagerDetails);
        $this->_template->render(false, false);
    }
    public function RegisterSymposiumUserData($fromEvent = '', $fromBack = 0, $checked = 0)
    {
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
            // $userId=$_SESSION['Event_userId'];
        }
        $frm = $this->getSignUpForm($userId);
        if (0 < $userId) {
            $data = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $usersrch = new SearchBase('tbl_event_user_credentials');
            $usersrch->addCondition('credential_user_id', '=', $data['user_id']);
            $usersrchData = FatApp::getDb()->fetch($usersrch->getResultSet());
            $data['user_email'] = $usersrchData['credential_email'];
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $loginFrm = $this->getLoginForm('', 1);
        $this->set('loginFrm', $loginFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('userId', $userId);
        $this->set('method', $fromEvent);
        $this->set('ticket', $fromBack);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }
    public function GetSymposiumTicketsPaymentSummary($method = '', $ticketCount = 1, $checkLogged = 1,$currency = 'USD')
    {
        $_SESSION['Event_userId'] = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = EventUserAuthentication::getLoggedUserId();
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        //event plan ticket Summary
        $_SESSION['symposium_ticket'] = $ticketCount;
        $userObj = new EventUser();
        if ($userId > 0) {
            $userRow = EventUser::getAttributesById($userId);
            $this->set('userData', $userRow);
        }
        $planData = new SearchBase('tbl_pre_symposium_dinner');
        $planData->addCondition('pre_symposium_dinner_plan_title', '=', $method);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        //  foreach ($sponsorshipList as $key => $value) {
        // $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $planResult['benefit_concert_id'], 0, -1);
        // $planResult['plan_image'] = $testimonialImages;
        //       $Sponsershiprecords[$key] = $value;
        //}
        // echo "<pre>";
        // print_r($planResult);
        $grpclsId = $_SESSION['pre_symposium_dinner_ticket_plan_id'];
        $key = $userId . '_' . $grpclsId;
        $cartTotal = $planResult['pre_symposium_dinner_plan_price'] * $ticketCount;
        if ($currency == 'ZMW' || $currency == 'zmw') {
            $cartTotal = $planResult['pre_symposium_dinner_plan_zk_price'] * $ticketCount;
            $planPrice = $planResult['pre_symposium_dinner_plan_zk_price'];
        }
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = $method;
        $cartData['itemId'] = $planResult['pre_symposium_dinner_id'];
        $cartData['itemPrice'] = $planResult['pre_symposium_dinner_plan_price'];
        $cartData['cartTotal'] = $cartTotal;
        $cartData['orderPaymentGatewayCharges'] = 1;
        $cartData['orderNetAmount'] = $cartTotal;
        $cartData['total'] = $cartTotal;
        $_SESSION['cart'] = $cartData;
        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';

        $BenefitConcertCouponCodeListing = new SearchBase('tbl_coupons');
        $BenefitConcertCouponCodeListing->addCondition('coupon_identifier', '=', 'BenefitConcert');
        $BenefirConcertCCListing = $BenefitConcertCouponCodeListing->getResultSet();
        $BenefitConcertCouponCodeFinalListing = FatApp::getDb()->fetchAll($BenefirConcertCCListing);

        $currencySwitcherData = new SearchBase('tbl_currencies_switcher');
        $currencySwitcherData->addCondition('currencies_switcher_active', '=', '1');
        $currencySwitcherData->addOrder('currencies_switcher_display_order', 'ASC');
        $currencySwitcherResultData = FatApp::getDb()->fetchall($currencySwitcherData->getResultSet());
        $selectedPlan = $cartData['itemId'];

        $registrationPlanData = new SearchBase('tbl_pre_symposium_dinner');
        $registrationPlanData->addCondition('pre_symposium_dinner_id', '=', $selectedPlan);
        $registrationPlanData->addCondition('pre_symposium_dinner_active', '=', '1');
        $registrationPlanData->addCondition('pre_symposium_dinner_deleted', '=', '0');
        $registrationPlanResultData = FatApp::getDb()->fetch($registrationPlanData->getResultSet());
        $this->set('registrationPlanResultData', $registrationPlanResultData);
        $this->set('currencySwitcherResultData', $currencySwitcherResultData);

        $this->set('BenefitConcertCouponCodeFinalListing', $BenefitConcertCouponCodeFinalListing);
        $this->set('planResult', $planResult);
        $this->set('tickets', $_SESSION['symposium_ticket']);
        $this->set('planSelected', $method);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->_template->render(false, false);
    }





    //exist
    //Get Concert Plans

    public function GetConcertPlan($fromBack = 0)
    {
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);
        unset($_SESSION['walletSummary']);
        unset($_SESSION['donation']);
        unset($_SESSION['cart']);
        unset($_SESSION['reg_sponser']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['summary']);
        unset($_SESSION['removeCoupon']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        unset($_SESSION['planSelected']);
        unset($_SESSION['event_ticket_id']);
        $planData = new SearchBase('tbl_benefit_concert');
        $planData->addCondition('benefit_concert_deleted', '=', 0);
        $planData->addCondition('benefit_concert_active', '=', 1);
        $planData->addCondition('benefit_concert_ending_date', '>', date('Y-m-d h:i'));
        $planData->addOrder('benefit_concert_id', 'ASC');
        $planResult = FatApp::getDb()->fetchAll($planData->getResultSet());
        if (empty($planResult)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_PLAN_AVAIABLE'));
        }
        if ($fromBack <= 0) {
            unset($_SESSION['concertPlan']);
        }
        if (isset($_SESSION['concertPlan'])) {
            $this->set('planSelected', $_SESSION['concertPlan']);
        }
        $tickets = new SearchBase('tbl_event_concert_ticket_plan');
        $tickets->addCondition('event_user_ticket_pay_status', '=', 1);
        $tickets->addMultipleFields(['SUM(event_user_ticket_count) as TotalTicket', 'event_user_concert_id']);
        $tickets->addGroupBy('event_user_concert_id');
        $tickets->addOrder('event_user_concert_id', 'ASC');
        $ticketManage = FatApp::getDb()->fetchAll($tickets->getResultSet());
        $this->set('ticketManage', $ticketManage);
        $this->set('slotDurations', $planResult);
        $this->_template->render(false, false);
    }
    //Concert Tickets
    public function GetConcertTickets($planSelected = '', $checked = 1, $fromPlan = 0, $ticketCount = 1)
    {
        $_SESSION['concertPlan'] = $planSelected;
        $this->set('planSelected', $planSelected);
        if ($fromPlan <= 0) {
            unset($_SESSION['concert_ticket']);
        }
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
        }
        $planData = new SearchBase('tbl_benefit_concert');
        $planData->addCondition('benefit_concert_plan_title', '=', $planSelected);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $donation_record = new TableRecord('tbl_event_concert_ticket_plan');
        $donation_record->assignValues([
            'event_user_id' => $userId,
            'event_user_concert_id' => $planResult['benefit_concert_id'],
            'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_FAILURE
        ]);
        if (!$donation_record->addNew([], [])) {
            Message::addErrorMessage($donation_record->getError());
            throw new Exception('');
        }
        $tickets = new SearchBase('tbl_event_concert_ticket_plan');
        $tickets->addCondition('event_user_ticket_pay_status', '=', 1);
        $tickets->addCondition('event_user_concert_id', '=', $planResult['benefit_concert_id']);
        $tickets->addMultipleFields(['SUM(event_user_ticket_count) as TotalTicket', 'event_user_concert_id']);
        $tickets->addGroupBy('event_user_concert_id');

        $ticketManager = $tickets->getResultSet();
        $ticketManagerDetails = FatApp::getDb()->fetch($ticketManager);

        $ticketSrch = new SearchBase('tbl_event_concert_ticket_plan');
        $ticketSrch->addCondition('event_user_concert_id', '=', $planResult['benefit_concert_id']);
        if ($checked > 0) {
            $ticketSrch->addCondition('event_user_id', '=', $userId);
        }
        $ticketData = FatApp::getDb()->fetchAll($ticketSrch->getResultSet());
        $lastRecord = end($ticketData);

        $_SESSION['event_corcert_ticket_id'] = $lastRecord['event_concert_ticket_plan_id'];
        if (isset($_SESSION['concert_ticket'])) {
            $this->set('tickets', $_SESSION['concert_ticket']);
        } else {
            $this->set('tickets', $ticketCount);
        }
        $this->set('planResult', $planResult);
        $this->set('ticketManagerDetails', $ticketManagerDetails);
        $this->_template->render(false, false);
    }
    public function RegisterConcertUserData($fromEvent = '', $fromBack = 0, $checked = 0)
    {
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
            // $userId=$_SESSION['Event_userId'];
        }
        $frm = $this->getSignUpForm($userId);
        if (0 < $userId) {
            $data = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
                'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
                'user_gender', 'user_phone', 'user_phone_code', 'user_country_id',
                'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_sponsorship_plan', 'user_become_sponsership_plan',
            ));
            $usersrch = new SearchBase('tbl_event_user_credentials');
            $usersrch->addCondition('credential_user_id', '=', $data['user_id']);
            $usersrchData = FatApp::getDb()->fetch($usersrch->getResultSet());
            $data['user_email'] = $usersrchData['credential_email'];
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $cPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0));
        $cpage = FatApp::getDb()->fetch($cPageSrch->getResultSet());
        if (!empty($cpage) && is_array($cpage)) {
            $termsAndConditionsLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpage['cpage_id']]);
        } else {
            $termsAndConditionsLinkHref = 'javascript:void(0)';
        }
        $this->set('termsAndConditionsLinkHref', $termsAndConditionsLinkHref);
        /* ] */
        /* [ */
        $cPPageSrch = ContentPage::getSearchObject($this->siteLangId);
        $cPPageSrch->addCondition('cpage_id', '=', FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0));
        $cpppage = FatApp::getDb()->fetch($cPPageSrch->getResultSet());
        if (!empty($cpppage) && is_array($cpppage)) {
            $privacyPolicyLinkHref = CommonHelper::generateUrl('Cms', 'view', [$cpppage['cpage_id']]);
        } else {
            $privacyPolicyLinkHref = 'javascript:void(0)';
        }
        $this->set('privacyPolicyLinkHref', $privacyPolicyLinkHref);
        /* ] */
        $loginFrm = $this->getLoginForm('', 1);
        $this->set('loginFrm', $loginFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('userId', $userId);
        $this->set('method', $fromEvent);
        $this->set('ticket', $fromBack);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }
    public function GetConcertTicketsPaymentSummary($method = '', $ticketCount = 1, $checkLogged = 1,$currency = 'USD')
    {
        $_SESSION['Event_userId'] = 0;
        if ($checkLogged > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = EventUserAuthentication::getLoggedUserId();
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        //event plan ticket Summary
        $_SESSION['concert_ticket'] = $ticketCount;
        $userObj = new EventUser();
        if ($userId > 0) {
            $userRow = EventUser::getAttributesById($userId);
            $this->set('userData', $userRow);
        }
        $planData = new SearchBase('tbl_benefit_concert');
        $planData->addCondition('benefit_concert_plan_title', '=', $method);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        //  foreach ($sponsorshipList as $key => $value) {
        // $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $planResult['benefit_concert_id'], 0, -1);
        // $planResult['plan_image'] = $testimonialImages;
        //       $Sponsershiprecords[$key] = $value;
        //}
        // echo "<pre>";
        // print_r($planResult);
        $grpclsId = $_SESSION['event_corcert_ticket_id'];
        $key = $userId . '_' . $grpclsId;
        $cartTotal = $planResult['benefit_concert_plan_price'] * $ticketCount;
        if ($currency == 'ZMW' || $currency == 'zmw') {
            $cartTotal = $planResult['benefit_concert_plan_zk_price'] * $ticketCount;
            $planPrice = $planResult['benefit_concert_plan_zk_price'];
        }
        $cart['cart'][$key] = [
            'teacherId' => $userId,
            'grpclsId' => $grpclsId,
            'startDateTime' => date('Y-m-d H:i:s'),
            'endDateTime' => date('Y-m-d H:i:s'),
            'isFreeTrial' => applicationConstants::NO,
            'lessonQty' => 1,
        ];
        $record = new TableRecord('tbl_user_cart');
        $cart_arr = $cart['cart'];
        $cart_arr = serialize($cart_arr);
        $record->assignValues([
            "usercart_user_id" => $userId,
            "usercart_type" => 4,
            "usercart_details" => $cart_arr,
            "usercart_added_date" => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew([], ['usercart_details' => $cart_arr, "usercart_added_date" => date('Y-m-d H:i:s')])) {
            Message::addErrorMessage($record->getError());
            throw new Exception('');
        }
        $cartData = [];
        $cartData['key'] = $key;
        $cartData['grpclsId'] = $grpclsId;
        $cartData['teacherId'] = $userId;
        $cartData['user_id'] = $userId;
        $cartData['isFreeTrial'] = applicationConstants::NO;
        $cartData['lessonQty'] = 1;
        $cartData['languageId'] = $this->siteLangId;
        $cartData['lessonDuration'] = 60;
        $cartData['lpackage_is_free_trial'] = applicationConstants::NO;
        $cartData['lpackage_lessons'] = 1;
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['startDateTime'] = date('Y-m-d H:i:s');
        $cartData['endDateTime'] = date('Y-m-d H:i:s');
        $cartData['itemName'] = $method;
        $cartData['itemId'] = $planResult['benefit_concert_id'];
        $cartData['itemPrice'] = $planResult['benefit_concert_plan_price'];
        $cartData['cartTotal'] = $cartTotal;
        $cartData['orderPaymentGatewayCharges'] = 1;
        $cartData['orderNetAmount'] = $cartTotal;
        $cartData['total'] = $cartTotal;
        $_SESSION['cart'] = $cartData;
        $userWalletBalance = EventUser::getUserBalance($userId);
        $paymentMethods = [];
        /* Payment Methods[ */
        $pmSrch = PaymentMethods::getSearchObject($this->siteLangId);
        $pmSrch->doNotCalculateRecords();
        $pmSrch->doNotLimitRecords();
        $pmSrch->addMultipleFields([
            'pmethod_id',
            'IFNULL(pmethod_name, pmethod_identifier) as pmethod_name',
            'pmethod_code',
            'pmethod_description'
        ]);
        $pmSrch->addCondition('pmethod_type', '=', PaymentMethods::TYPE_PAYMENT_METHOD);
        $pmRs = $pmSrch->getResultSet();
        $paymentMethods = FatApp::getDb()->fetchAll($pmRs);
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '';
        $WalletPaymentForm = $this->getWalletPaymentForm();
        $BenefitConcertCouponCodeListing = new SearchBase('tbl_coupons');
        $BenefitConcertCouponCodeListing->addCondition('coupon_identifier', '=', 'BenefitConcert');
        $BenefirConcertCCListing = $BenefitConcertCouponCodeListing->getResultSet();
        $BenefitConcertCouponCodeFinalListing = FatApp::getDb()->fetchAll($BenefirConcertCCListing);

        $currencySwitcherData = new SearchBase('tbl_currencies_switcher');
        $currencySwitcherData->addCondition('currencies_switcher_active', '=', '1');
        $currencySwitcherData->addOrder('currencies_switcher_display_order', 'ASC');
        $currencySwitcherResultData = FatApp::getDb()->fetchall($currencySwitcherData->getResultSet());
        $selectedPlan = $cartData['itemId'];
    
        $registrationPlanData = new SearchBase('tbl_benefit_concert');
        $registrationPlanData->addCondition('benefit_concert_id', '=', $selectedPlan);
        $registrationPlanData->addCondition('benefit_concert_active', '=', '1');
        $registrationPlanData->addCondition('benefit_concert_deleted', '=', '0');
        $registrationPlanResultData = FatApp::getDb()->fetch($registrationPlanData->getResultSet());
        // echo "<pre>";
        // print_r($registrationPlanResultData);
        $this->set('registrationPlanResultData', $registrationPlanResultData);


        $this->set('BenefitConcertCouponCodeFinalListing', $BenefitConcertCouponCodeFinalListing);
        $this->set('planResult', $planResult);
        $this->set('currencySwitcherResultData', $currencySwitcherResultData);
        $this->set('tickets', $_SESSION['concert_ticket']);
        $this->set('planSelected', $method);
        $this->set('paymentMethods', $paymentMethods);
        $this->set('cartData', $cartData);
        $this->set('userWalletBalance', $userWalletBalance);
        $this->set('WalletPaymentForm', $WalletPaymentForm);
        $this->set('userType', EventUser::USER_TYPE_LEANER);
        $this->set('userId', $userId);
        $this->_template->render(false, false);
    }

    private function getWalletPaymentForm()
    {
        return new Form('frmWalletPayment');
    }

    //REgistration sponser sponsership list
    public function GetEventPlan($fromBack = 0)
    {
        unset($_SESSION['walletSummary']);
        unset($_SESSION['symposiumPlan']);
        unset($_SESSION['symposium_ticket']);
        unset($_SESSION['donation']);
        unset($_SESSION['cart']);
        unset($_SESSION['reg_sponser']);
        unset($_SESSION['become_sponser']);
        unset($_SESSION['sponsor']);
        unset($_SESSION['summary']);
        unset($_SESSION['removeCoupon']);
        unset($_SESSION['ticketDownloadUrl']);
        unset($_SESSION['ticketUrl']);
        unset($_SESSION['ticket_count']);
        unset($_SESSION['event_ticket_id']);
        $planData = new SearchBase('tbl_three_reasons');
        $planData->addCondition('three_reasons_deleted', '=', 0);
        $planData->addCondition('three_reasons_active', '=', 1);
        $planData->addCondition('registration_ending_date', '>', date('Y-m-d h:i'));
        $planData->addOrder('three_reasons_display_order', 'ASC');
        $planResult = FatApp::getDb()->fetchAll($planData->getResultSet());
        if (empty($planResult)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_NO_PLAN_AVAIABLE'));
        }
        if ($fromBack <= 0) {
            unset($_SESSION['planSelected']);
        }
        if (isset($_SESSION['planSelected'])) {
            $this->set('planSelected', $_SESSION['planSelected']);
        }
        $this->set('slotDurations', $planResult);
        $this->_template->render(false, false);
    }
    //Event Ticcket sponser sponsership list
    public function GetEventTickets($planSelected = '', $checked = 1, $fromPlan = 0, $ticketCount = 1)
    {
        $_SESSION['planSelected'] = $planSelected;
        $this->set('planSelected', $planSelected);
        if ($fromPlan <= 0) {
            unset($_SESSION['ticket_count']);
        }
        $userId = 0;
        if ($checked > 0) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $_SESSION['Event_userId'] = $userId;
        }
        $planData = new SearchBase('tbl_three_reasons');
        $planData->addCondition('three_reasons_deleted', '=', 0);
        $planData->addCondition('registration_plan_title', '=', $planSelected);
        $planResult = FatApp::getDb()->fetch($planData->getResultSet());
        $donation_record = new TableRecord('tbl_event_user_ticket_plan');
        $donation_record->assignValues([
            'event_user_id' => $userId,
            'event_user_plan_id' => $planResult['three_reasons_id'],
            'event_user_ticket_pay_status' => EventUser::EVENT_DONATION_FAILURE
        ]);
        if (!$donation_record->addNew([], [])) {
            Message::addErrorMessage($donation_record->getError());
            throw new Exception('');
        }
        $ticketSrch = new SearchBase('tbl_event_user_ticket_plan');
        $ticketSrch->addCondition('event_user_plan_id', '=', $planResult['three_reasons_id']);
        if ($checked > 0) {
            $ticketSrch->addCondition('event_user_id', '=', $userId);
        }
        $ticketData = FatApp::getDb()->fetchAll($ticketSrch->getResultSet());
        $lastRecord = end($ticketData);
        $_SESSION['event_ticket_id'] = $lastRecord['event_user_ticket_plan_id'];
        if (isset($_SESSION['ticket_count'])) {
            $this->set('tickets', $_SESSION['ticket_count']);
        } else {
            $this->set('tickets', $ticketCount);
        }
        $this->_template->render(false, false);
    }
    private function getLoginForm($data = '', $userData = 0)
    {
        $userName = '';
        $pass = '';
        if (CommonHelper::demoUrl()) {
            if ((FatApp::getQueryStringData('type') == 'teacher')) {
                $userName = 'grace@dummyid.com';
                $pass = 'grace@123';
            } else {
                $userName = 'jason@dummyid.com';
                $pass = 'jason@123';
            }
        }
        $frm = new Form('frmLogin');
        if ($data != '') {
            $frm->addHiddenField('', 'from_location', $data);
        }
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Email'), 'username', $userName, ['placeholder' => Label::getLabel('LBL_EMAIL_ADDRESS')]);
        $pwd = $frm->addPasswordField(Label::getLabel('LBL_Password'), 'password', $pass, ['placeholder' => Label::getLabel('LBL_PASSWORD')]);
        $pwd->requirements()->setRequired();
        if ($userData == 0) {
            $frm->addCheckbox(Label::getLabel('LBL_Remember_Me'), 'remember_me', 1, [], '', 0);
            $frm->addHtml('', 'forgot', '');
            $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_LOGIN'));
        }
        return $frm;
    }
    private function sendEmailVerificationLink($userObj, $data)
    {
        $verificationCode = $userObj->prepareUserVerificationCode();
        $link = CommonHelper::generateFullUrl('EventUser', 'userCheckEmailVerification', ['verify' => $verificationCode]);
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $email = new EmailHandler();
        if (true !== $email->sendEmailVerificationLink($this->siteLangId, $data)) {
            return false;
        }
        return true;
    }
    private function sendSignUpWelcomeEmail($userObj, $data)
    {
        $link = CommonHelper::generateFullUrl('EventUser', 'loginForm');
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link
        ];
        $email = new EmailHandler();
        if (true !== $email->sendWelcomeEmail($this->siteLangId, $data)) {
            Message::addMessage(Label::getLabel("MSG_ERROR_IN_SENDING_WELCOME_EMAIL"));
            return false;
        }
        return true;
    }
    public function socialMediaLogin($oauthProvider, $userType = EventUser::USER_TYPE_LEANER)
    {
        if (isset($oauthProvider)) {
            if ($oauthProvider == 'googleplus') {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'loginGoogleplus', [$userType]));
            } elseif ($oauthProvider == 'google') {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'loginGoogle', [$userType]));
            } elseif ($oauthProvider == 'facebook') {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'loginFacebook'));
            } else {
                Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_REQUEST'));
            }
        }
        CommonHelper::redirectUserReferer();
    }
    public function loginFacebook()
    {
        $post = FatApp::getPostedData();
        $facebookEmail = isset($post['email']) ? $post['email'] : NULL;
        $accessToken = FatApp::getPostedData('accessToken', FatUtility::VAR_STRING, '');
        if (empty($post['id']) || empty($accessToken)) {
            FatUtility::dieJsonError(Label::getLabel("MSG_THERE_WAS_SOME_PROBLEM_IN_AUTHENTICATING_YOUR_ACCOUNT_WITH_FACEBOOK,_PLEASE_TRY_WITH_DIFFERENT_LOGIN_OPTIONS", $this->siteLangId));
        }
        $userFacebookId = $post['id'];
        $error = '';
        if (!$this->verifyFacebookUserAccessToken($accessToken, $userFacebookId, $error)) {
            FatUtility::dieJsonError($error);
        }
        $userFirstName = $post['first_name'];
        $userLastName = $post['last_name'];
        $user_type = FatApp::getPostedData('type', FatUtility::VAR_INT, EventUser::USER_TYPE_LEANER);
        $preferredDashboard = EventUser::USER_LEARNER_DASHBOARD;
        if ($user_type == EventUser::USER_TYPE_TEACHER) {
            $preferredDashboard = EventUser::USER_TEACHER_DASHBOARD;
        }
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_code']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_access_token']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_user_id']);
        $facebookName = $userFirstName . ' ' . $userLastName;
        $db = FatApp::getDb();
        $userObj = new EventUser();
        $srch = $userObj->getUserSearchObj(['user_id', 'user_facebook_id', 'user_preferred_dashboard', 'credential_email', 'credential_verified', 'credential_active', 'user_deleted'], false, false);
        if (!empty($facebookEmail)) {
            $srch->addCondition('credential_email', '=', $facebookEmail);
        } else {
            $srch->addCondition('user_facebook_id', '=', $userFacebookId);
        }
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if ($row) {
            if ($row['credential_active'] != applicationConstants::ACTIVE) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("ERR_YOUR_ACCOUNT_HAS_BEEN_DEACTIVATED")]);
            }
            if ($row['user_deleted'] == applicationConstants::YES) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("ERR_USER_INACTIVE_OR_DELETED")]);
            }
            $userObj->setMainTableRecordId($row['user_id']);
            $arr = ['user_facebook_id' => $userFacebookId];
            if (!$userObj->setUserInfo($arr)) {
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("LBL_ERROR_TO_UPDATE_USER_DATA")]);
            }
            $row['credential_verified'] = FatUtility::int($row['credential_verified']);
            if ($row['credential_verified'] != applicationConstants::YES && !empty($facebookEmail)) {
                if (!$userObj->verifyAccount(applicationConstants::YES)) {
                    FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("LBL_ERROR_TO_UPDATE_USER_DATA")]);
                }
            }
            if ($row['user_preferred_dashboard'] == EventUser::USER_TEACHER_DASHBOARD) {
                $user_type = EventUser::USER_TYPE_TEACHER;
            }
        } else {
            $userNameArr = explode(" ", $facebookName);
            $user_first_name = (!empty($userNameArr[0])) ? $userNameArr[0] : '';
            $user_last_name = (!empty($userNameArr[1])) ? $userNameArr[1] : '';
            $db->startTransaction();
            $userData = [
                'user_first_name' => $user_first_name,
                'user_last_name' => $user_last_name,
                'user_is_learner' => 1,
                'user_facebook_id' => $userFacebookId,
                'user_preferred_dashboard' => $preferredDashboard,
                'user_registered_initially_for' => $user_type,
            ];
            $userObj->assignValues($userData);
            if (!$userObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_USER_COULD_NOT_BE_SET")]);
            }
            $username = str_replace(" ", "", $facebookName) . $userFacebookId;
            if (!$userObj->setLoginCredentials($username, $facebookEmail, uniqid(), 1, 1)) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET")]);
            }
            $db->commitTransaction();
            $userId = $userObj->getMainTableRecordId();
            $userObj = new EventUser($userId);
            $userData['user_username'] = $username;
            $userData['user_email'] = $facebookEmail;
            if (FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1) && $facebookEmail) {
                $data['user_email'] = $facebookEmail;
                $data['user_first_name'] = $user_first_name;
                $data['user_last_name'] = $user_last_name;
                $this->userWelcomeEmailRegistration($userObj, $data);
            }
        }
        $userInfo = $userObj->getUserInfo(['user_facebook_id', 'user_is_teacher', 'credential_username', 'credential_password', 'credential_email',]);
        if (!$userInfo || ($userInfo && $userInfo['user_facebook_id'] != $userFacebookId)) {
            FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => Label::getLabel("MSG_USER_COULD_NOT_BE_SET")]);
        }
        $authentication = new EventUserAuthentication();
        if (!$authentication->login($userInfo['credential_username'], $userInfo['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
            FatUtility::dieJsonError(['url' => CommonHelper::redirectUserReferer(true), 'msg' => $authentication->getError()]);
        }
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_code']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_access_token']);
        unset($_SESSION['fb_' . FatApp::getConfig("CONF_FACEBOOK_APP_ID") . '_user_id']);
        $redirectUrl = EventUser::getPreferedDashbordRedirectUrl();
        $isUserTeacher = FatUtility::int($userInfo['user_is_teacher']);
        if ($user_type == EventUser::USER_TYPE_TEACHER && $isUserTeacher != applicationConstants::YES) {
            $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
        }
        $message = Label::getLabel('MSG_LoggedIn_SUCCESSFULLY', $this->siteLangId);
        if (empty($userInfo['credential_email'])) {
            $message = Label::getLabel('MSG_PLEASE_CONFIGURE_YOUR_EMAIL', $this->siteLangId);
            $redirectUrl = CommonHelper::generateUrl('EventUser', 'configureEmail');
        }
        FatUtility::dieJsonSuccess(['url' => $redirectUrl, 'msg' => $message]);
    }
    public function configureEmail()
    {
        EventUserAuthentication::checkLogin();
        $userObj = new EventUser(EventUserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_email', 'user_first_name', 'user_last_name']);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        if ($data === false || !empty($data['credential_email'])) {
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'loginForm'));
        }
        $frm = $this->getConfigureEmailForm();
        $this->set('frm', $frm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }
    private function getConfigureEmailForm()
    {
        $frm = new Form('changeEmailFrm');
        $frm->addHiddenField('', 'user_id', EventUserAuthentication::getLoggedUserId());
        $newEmail = $frm->addEmailField(Label::getLabel('LBL_NEW_EMAIL'), 'new_email');
        $newEmail->setUnique('tbl_user_credentials', 'credential_email', 'credential_user_id', 'user_id', 'user_id');
        $newEmail->requirements()->setRequired();
        $conNewEmail = $frm->addEmailField(Label::getLabel('LBL_CONFIRM_NEW_EMAIL'), 'conf_new_email');
        $conNewEmailReq = $conNewEmail->requirements();
        $conNewEmailReq->setRequired();
        $conNewEmailReq->setCompareWith('new_email', 'eq');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));
        return $frm;
    }
    public function updateEmail()
    {
        $emailFrm = $this->getConfigureEmailForm(false);
        $post = $emailFrm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            $message = current($emailFrm->getValidationErrors());
            FatUtility::dieJsonError($message);
        }
        $userObj = new EventUser(EventUserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_email', 'credential_password', 'user_first_name', 'user_last_name']);
        $rs = $srch->getResultSet();
        if (!$rs) {
            $message = Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId);
            FatUtility::dieJsonError($message);
        }
        $data = FatApp::getDb()->fetch($rs);
        if ($data === false || !empty($data['credential_email'])) {
            $message = Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId);
            FatUtility::dieJsonError($message);
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $msg = Label::getLabel('LBL_EMAIL_UPDATE_SUCCESSFULL');
        $redirectUrl = "";
        $emailChangeReqObj = new UserEmailChangeRequest();
        $emailChangeReqObj->deleteOldLinkforUser(EventUserAuthentication::getLoggedUserId());
        $emailVerification = FatApp::getConfig('CONF_EMAIL_VERIFICATION_REGISTRATION', FatUtility::VAR_INT, 1);
        if (applicationConstants::YES == $emailVerification) {
            $_token = $userObj->prepareUserVerificationCode();
            $postData = [
                'uecreq_user_id' => EventUserAuthentication::getLoggedUserId(),
                'uecreq_email' => $post['new_email'],
                'uecreq_token' => $_token,
                'uecreq_status' => 0,
                'uecreq_created' => date('Y-m-d H:i:s'),
                'uecreq_updated' => date('Y-m-d H:i:s'),
                'uecreq_expire' => date('Y-m-d H:i:s', strtotime('+ 24 hours', strtotime(date('Y-m-d H:i:s'))))
            ];
            $emailChangeReqObj->assignValues($postData);
            if (!$emailChangeReqObj->save()) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_Unable_to_process_your_requset') . $emailChangeReqObj->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            $userData = [
                'user_email' => $post['new_email'],
                'user_first_name' => $data['user_first_name'],
                'user_last_name' => $data['user_last_name']
            ];
            if (!$this->sendEmailChangeVerificationLink($_token, $userData)) {
                $db->rollbackTransaction();
                Message::addErrorMessage(Label::getLabel('MSG_Unable_to_process_your_requset') . $emailChangeReqObj->getError());
                FatUtility::dieWithError(Message::getHtml());
            }
            $msg = Label::getLabel('MSG_UPDATE_EMAIL_REQUEST_SENT_SUCCESSFULLY._YOU_NEED_TO_VERIFY_YOUR_NEW_EMAIL_ADDRESS_BEFORE_ACCESSING_OTHER_MODULES');
        } else {
            if (!$userObj->changeEmail($post['new_email'])) {
                Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set'));
                FatUtility::dieWithError(Message::getHtml());
            }
        }
        $db->commitTransaction();
        $confAutoLoginRegisteration = FatApp::getConfig('CONF_AUTO_LOGIN_REGISTRATION', FatUtility::VAR_INT, 1);
        if (applicationConstants::NO == $emailVerification) {
            $authentication = new EventUserAuthentication();
            if (!$authentication->login($post['new_email'], $data['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                FatUtility::dieWithError(Message::getHtml());
            }
            $redirectUrl = EventUser::getPreferedDashbordRedirectUrl();
        }
        $returnJson = ['msg' => $msg];
        if (!empty($redirectUrl)) {
            $returnJson['redirectUrl'] = $redirectUrl;
        }
        FatUtility::dieJsonSuccess($returnJson);
    }
    private function sendEmailChangeVerificationLink($_token, $data)
    {
        $link = CommonHelper::generateFullUrl('EventUser', 'verifyEmail', [$_token]);
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $email = new EmailHandler();
        if (true !== $email->sendEmailChangeVerificationLink($this->siteLangId, $data)) {
            return false;
        }
        return true;
    }
    public function loginGoogle($userType = EventUser::USER_TYPE_LEANER)
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/GoogleAPI/vendor/autoload.php'; // include the required calss files for google login
        $client = new Google_Client();
        $client->setApplicationName(FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->siteLangId)); // Set your applicatio name
        $client->setScopes(['email', 'profile', 'https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.events']); // set scope during user login
        $client->setClientId(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_ID")); // paste the client id which you get from google API Console
        $client->setClientSecret(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_SECRET")); // set the client secret
        $currentPageUri = CommonHelper::generateFullUrl('EventUser', 'loginGoogle', [$userType], CONF_WEBROOT_FRONT_URL, NULL, false, false);
        $client->setRedirectUri($currentPageUri);
        $client->setAccessType("offline");
        $client->setApprovalPrompt("force");
        $client->setDeveloperKey(FatApp::getConfig("CONF_GOOGLEPLUS_DEVELOPER_KEY")); // Developer key
        $oauth2 = new Google_Service_Oauth2($client); // Call the OAuth2 class for get email address
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']); // Authenticate
            $_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
            FatApp::redirectUser($currentPageUri);
        }
        if (isset($_SESSION['access_token'])) {
            $client->setAccessToken($_SESSION['access_token']);
        }
        if (!$client->getAccessToken()) {
            $authUrl = $client->createAuthUrl();
            FatApp::redirectUser($authUrl);
        }
        $user = $oauth2->userinfo->get();
        $_SESSION['access_token'] = $client->getAccessToken();
        $userGoogleEmail = filter_var($user['email'], FILTER_SANITIZE_EMAIL);
        $userGoogleId = $user['id'];
        $userGoogleName = $user['name'];
        if (!empty($userGoogleEmail)) {
            if ($userType == EventUser::USER_TYPE_TEACHER) {
                $preferredDashboard = EventUser::USER_TEACHER_DASHBOARD;
                $userType = EventUser::USER_TYPE_TEACHER;
            } else {
                $preferredDashboard = EventUser::USER_LEARNER_DASHBOARD;
                $userType = EventUser::USER_TYPE_LEANER;
            }
            $db = FatApp::getDb();
            $userObj = new EventUser();
            $srch = $userObj->getUserSearchObj(['user_id', 'user_preferred_dashboard', 'credential_verified', 'credential_email', 'credential_active']);
            $srch->addCondition('credential_email', '=', $userGoogleEmail);
            $rs = $srch->getResultSet();
            $row = $db->fetch($rs);
            if ($row) {
                if ($row['credential_active'] != applicationConstants::ACTIVE) {
                    Message::addErrorMessage(Label::getLabel("ERR_YOUR_ACCOUNT_HAS_BEEN_DEACTIVATED"));
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $userObj->setMainTableRecordId($row['user_id']);
                $arr = ['user_googleplus_id' => $userGoogleId,];
                if (!$userObj->setUserInfo($arr)) {
                    Message::addErrorMessage(Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA'));
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $row['credential_verified'] = FatUtility::int($row['credential_verified']);
                if ($row['credential_verified'] != applicationConstants::YES) {
                    if (!$userObj->verifyAccount(applicationConstants::YES)) {
                        Message::addErrorMessage(Label::getLabel('LBL_ERROR_TO_UPDATE_USER_DATA'));
                        FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                    }
                }
                if ($row['user_preferred_dashboard'] == EventUser::USER_TEACHER_DASHBOARD) {
                    $userType = EventUser::USER_TYPE_TEACHER;
                }
            } else {
                $db->startTransaction();
                $userNameArr = explode(" ", $userGoogleName);
                $user_first_name = (!empty($userNameArr[0])) ? $userNameArr[0] : '';
                $user_last_name = (!empty($userNameArr[1])) ? $userNameArr[1] : '';
                $userData = [
                    'user_first_name' => $user_first_name,
                    'user_last_name' => $user_last_name,
                    'user_is_learner' => 1,
                    'user_googleplus_id' => $userGoogleId,
                    'user_preferred_dashboard' => $preferredDashboard,
                    'user_registered_initially_for' => $userType,
                ];
                $userObj->assignValues($userData);
                if (!$userObj->save()) {
                    Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET") . $userObj->getError());
                    $db->rollbackTransaction();
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $username = str_replace(" ", "", $userGoogleName) . $userGoogleId;
                if (!$userObj->setLoginCredentials($username, $userGoogleEmail, uniqid(), 1, 1)) {
                    Message::addErrorMessage(Label::getLabel("MSG_LOGIN_CREDENTIALS_COULD_NOT_BE_SET") . $userObj->getError());
                    $db->rollbackTransaction();
                    FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
                }
                $db->commitTransaction();
                $userId = $userObj->getMainTableRecordId();
                $userObj = new EventUser($userId);
                $userData['user_username'] = $username;
                $userData['user_email'] = $userGoogleEmail;
                if (FatApp::getConfig('CONF_WELCOME_EMAIL_REGISTRATION', FatUtility::VAR_INT, 1) && $userGoogleEmail) {
                    $data['user_email'] = $userGoogleEmail;
                    $data['user_first_name'] = $user_first_name;
                    $data['user_last_name'] = $user_last_name;
                    $this->userWelcomeEmailRegistration($userObj, $data);
                }
            }
            $usrStngObj = new UserSetting($userObj->getMainTableRecordId());
            $usrStngObj->saveData(['us_google_access_token' => $client->getRefreshToken()]);
            $userInfo = $userObj->getUserInfo(['user_googleplus_id', 'user_is_teacher', 'credential_username', 'credential_password']);
            if (!$userInfo || ($userInfo && $userInfo['user_googleplus_id'] != $userGoogleId)) {
                Message::addErrorMessage(Label::getLabel("MSG_USER_COULD_NOT_BE_SET"));
                FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
            }
            $authentication = new EventUserAuthentication();
            if (!$authentication->login($userInfo['credential_username'], $userInfo['credential_password'], $_SERVER['REMOTE_ADDR'], false)) {
                Message::addErrorMessage(Label::getLabel($authentication->getError()));
                FatApp::redirectUser(CommonHelper::generateUrl('Teachers', 'languages'));
            }
            unset($_SESSION['access_token']);
            $redirectUrl = CommonHelper::generateUrl('Teachers', 'languages');
            $isUserTeacher = FatUtility::int($userInfo['user_is_teacher']);
            if ($userType == EventUser::USER_TYPE_TEACHER && $isUserTeacher != applicationConstants::YES) {
                $redirectUrl = CommonHelper::generateUrl('TeacherRequest', 'form');
            }
            FatApp::redirectUser($redirectUrl);
        }
        Message::addErrorMessage(Label::getLabel("MSG_UNABLE_To_FETCH_YOUR_EMAIL_ID"));
        FatApp::redirectUser(CommonHelper::generateUrl());
    }
    public function forgotPasswordForm()
    {
        $frm = $this->getForgotForm();
        $this->set('frm', $frm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }
    public function forgotPassword()
    {
        $frm = $this->getForgotForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
        }
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
            if (!CommonHelper::verifyCaptcha()) {
                Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect'));
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
            }
        }
        $userAuthObj = new EventUserAuthentication();
        $row = $userAuthObj->getUserByEmailOrUserName($post['user_email_username'], '', false);
        if (!$row || false === $row) {
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
        }
        if ($row['credential_verified'] != applicationConstants::YES) {
            Message::addErrorMessage(str_replace("{clickhere}", '<a href="javascript:void(0)" onclick="resendEmailVerificationLink(' . "'" . $row['credential_email'] . "'" . ')">' . Label::getLabel('LBL_Click_Here', $this->siteLangId) . '</a>', Label::getLabel('MSG_Your_Account_verification_is_pending_{clickhere}', $this->siteLangId)));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
            return false;
        }
        if ($userAuthObj->checkUserPwdResetRequest($row['user_id'])) {
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
        }
        $token = EventUserAuthentication::encryptPassword(FatUtility::getRandomString(20));
        $row['token'] = $token;
        $userAuthObj->deleteOldPasswordResetRequest();
        $db = FatApp::getDb();
        $db->startTransaction();
        if (!$userAuthObj->addPasswordResetRequest($row)) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel($userAuthObj->getError()));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
        }
        $row['link'] = CommonHelper::generateFullUrl('EventUser', 'resetPassword', [$row['user_id'], $token]);
        $email = new EmailHandler();
        if (!$email->sendForgotPasswordLinkEmail($this->siteLangId, $row)) {
            $db->rollbackTransaction();
            Message::addErrorMessage(Label::getLabel("MSG_ERROR_IN_SENDING_PASSWORD_RESET_LINK_EMAIL"));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'forgotPasswordForm'));
        }
        $db->commitTransaction();
        Message::addMessage(Label::getLabel("MSG_YOUR_PASSWORD_RESET_INSTRUCTIONS_TO_YOUR_EMAIL"));
        FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
    }
    public function resetPassword($userId = 0, $token = '')
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1 || strlen(trim($token)) < 20) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_RESET_PASSWORD_REQUEST'));
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $userAuthObj = new EventUserAuthentication();
        if (!$userAuthObj->checkResetLink($userId, trim($token), 'form')) {
            Message::addErrorMessage($userAuthObj->getError());
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'EventLoginForm'));
        }
        $frm = $this->getResetPwdForm($userId, trim($token));
        $this->set('frm', $frm);
        $this->_template->render();
    }
    private function getForgotForm()
    {
        $siteLangId = $this->siteLangId;
        $frm = new Form('frmPwdForgot');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Email', $siteLangId), 'user_email_username')->requirements()->setRequired();
        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
            $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $siteLangId));
        return $frm;
    }
    private function getResetPwdForm($uId, $token)
    {
        $siteLangId = $this->siteLangId;
        $frm = new Form('frmResetPwd');
        $fld_np = $frm->addPasswordField(Label::getLabel('LBL_NEW_PASSWORD', $siteLangId), 'new_pwd');
        $fld_np->requirements()->setRequired();
        $fld_np->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
        $fld_np->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_Valid_password', $siteLangId));
        $fld_cp = $frm->addPasswordField(Label::getLabel('LBL_CONFIRM_NEW_PASSWORD', $siteLangId), 'confirm_pwd');
        $fld_cp->requirements()->setRequired();
        $fld_cp->requirements()->setCompareWith('new_pwd', 'eq', '');
        $frm->addHiddenField('', 'user_id', $uId, ['id' => 'user_id']);
        $frm->addHiddenField('', 'token', $token, ['id' => 'token']);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_RESET_PASSWORD', $siteLangId));
        return $frm;
    }
    public function resetPasswordSetup()
    {
        $newPwd = FatApp::getPostedData('new_pwd');
        $confirmPwd = FatApp::getPostedData('confirm_pwd');
        $userId = FatApp::getPostedData('user_id', FatUtility::VAR_INT);
        $token = FatApp::getPostedData('token', FatUtility::VAR_STRING);
        if ($userId < 1 && strlen(trim($token)) < 20) {
            Message::addErrorMessage(Label::getLabel('MSG_REQUEST_IS_INVALID_OR_EXPIRED'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $frm = $this->getResetPwdForm($userId, $token);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (true !== CommonHelper::validatePassword($post['new_pwd'])) {
            Message::addErrorMessage(Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $userAuthObj = new EventUserAuthentication();
        if (!$userAuthObj->checkResetLink($userId, trim($token), 'submit')) {
            Message::addErrorMessage($userAuthObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $pwd = EventUserAuthentication::encryptPassword($newPwd);
        if (!$userAuthObj->resetUserPassword($userId, $pwd)) {
            Message::addErrorMessage($userAuthObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $email = new EmailHandler();
        $userObj = new EventUser($userId);
        $row = $userObj->getUserInfo(['user_first_name', 'user_last_name', 'credential_email'], '', false);
        $email->sendResetPasswordConfirmationEmail($this->siteLangId, $row);
        $this->set('msg', Label::getLabel('MSG_PASSWORD_CHANGED_SUCCESSFULLY'));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function resendEmailVerificationLink($username = "")
    {
        if (empty($username)) {
            FatUtility::dieWithError(Label::getLabel('MSG_ERROR_INVALID_REQUEST'));
        }
        $userAuthObj = new EventUserAuthentication();
        if (!$row = $userAuthObj->getUserByEmailOrUserName($username, false, false)) {
            FatUtility::dieWithError(Label::getLabel($userAuthObj->getError()));
        }
        if ($row['credential_verified'] == 1) {
            FatUtility::dieWithError(Label::getLabel("MSG_You_are_already_verified_please_login."));
        }
        $row['user_email'] = $row['credential_email'];
        $userObj = new EventUser($row['user_id']);
        if (!$this->sendEmailVerificationLink($userObj, $row)) {
            FatUtility::dieWithError(Label::getLabel("MSG_VERIFICATION_EMAIL_COULD_NOT_BE_SENT"));
        }
        $this->set('msg', Label::getLabel('MSG_VERIFICATION_EMAIL_HAS_BEEN_SENT_AGAIN'));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function checkAjaxUserLoggedIn()
    {
        $json = [];
        $json['isUserLogged'] = FatUtility::int(EventUserAuthentication::isUserLogged());
        die(json_encode($json));
    }
    private function userWelcomeEmailRegistration($userObj, $data)
    {
        $email = new EmailHandler();
        if (!$email->sendWelcomeEmail($this->siteLangId, $data)) {
            Message::addMessage(Label::getLabel("MSG_ERROR_IN_SENDING_WELCOME_EMAIL", $this->siteLangId));
            return false;
        }
        return true;
    }
    public function verifyEmail($_token)
    {
        $emailChangeReqObj = new UserEmailChangeRequest();
        $userRequest = $emailChangeReqObj->checkUserRequest($_token);
        if (empty($userRequest)) {
            Message::addErrorMessage(Label::getLabel("MSG_INVAILD_VERIFICATION_LINK", $this->siteLangId));
            $this->logout();
        }
        $userObj = new EventUser($userRequest['uecreq_user_id']);
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_password']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs, 'user_id');
        if (false == $userRow) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST'));
            $this->logout();
        }
        if (!$userObj->changeEmail($userRequest['uecreq_email'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set') . $userObj->getError());
            $this->logout();
        }
        $userRequest['status'] = 1;
        $emailCheReqObj = new UserEmailChangeRequest($userRequest['uecreq_id']);
        if (!$emailCheReqObj->updateUserRequestStatus()) {
            //Message::addErrorMessage(Label::getLabel('MSG_Email_could_not_be_set'). $userObj->getError());
        }
        Message::addMessage(Label::getLabel('MSG_Email_Updated._Please_Login_again_in_your_profile_with_new_email'));
        $this->logout();
    }
    private static function verifyFacebookUserAccessToken($facebookUserAccessToken, $userFacebookId, &$error = '')
    {
        $facebookUserAccessToken = filter_var($facebookUserAccessToken, FILTER_SANITIZE_STRING);
        $myFacebookAppId = FatApp::getConfig('CONF_FACEBOOK_APP_ID', FatUtility::VAR_STRING, '');
        $facebookAppSecret = FatApp::getConfig('CONF_FACEBOOK_APP_SECRET', FatUtility::VAR_STRING, '');
        $facebook_application = 'REPLACE';
        $curl = curl_init();
        $url = "https://graph.facebook.com/oauth/access_token?client_id=" . $myFacebookAppId . "&client_secret=" . $facebookAppSecret . "&grant_type=client_credentials";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $outputData = curl_exec($curl);
        curl_close($curl);
        $outputData = json_decode($outputData, true);
        $facebook_access_token = $outputData['access_token'];
        $curl = curl_init();
        $url = "https://graph.facebook.com/debug_token?input_token=" . $facebookUserAccessToken . "&access_token=" . $facebook_access_token;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $outputData = curl_exec($curl);
        curl_close($curl);
        $tokenData = json_decode($outputData, true);
        // && $tokenData['data']['application'] == $facebook_application
        $error = Label::getLabel('LBL_ERROR_TO_VERIFY_FACEBOOK_TOKEN');
        if (!empty($tokenData['data']['error']) || $tokenData['data']['is_valid'] == false || empty($tokenData['data']['user_id'] || empty($tokenData['data']['app_id']))) {
            $error = (!empty($tokenData['data']['error']['message'])) ? $tokenData['data']['error']['message'] : $error;
            return false;
        }
        if ($myFacebookAppId == $tokenData['data']['app_id'] && $userFacebookId == $tokenData['data']['user_id'] && $tokenData['data']['is_valid'] == true) {
            return true;
        }
        return false;
    }
}
