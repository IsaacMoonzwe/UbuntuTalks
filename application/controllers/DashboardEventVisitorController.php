<?php

class DashboardEventVisitorController extends MyEventAppController
{

    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $userObj = new EventUser($userId);
        $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());

        $EventplanData = new SearchBase('tbl_event_user_ticket_plan');
        $EventplanData->addCondition('event_user_ticket_pay_status', '=', 1);
        $EventplanData->addCondition('event_user_id', '=', $userId);
        $EventplanResult = FatApp::getDb()->fetchAll($EventplanData->getResultSet());
        foreach ($EventplanResult as $key => $value) {

            $EventsTicketsplanData = new SearchBase('tbl_three_reasons');
            $EventsTicketsplanData->addCondition('three_reasons_deleted', '=', 0);
            $EventsTicketsplanData->addCondition('three_reasons_id', '=', $value['event_user_plan_id']);
            $EventsTicketsplanResult = FatApp::getDb()->fetch($EventsTicketsplanData->getResultSet());
            $OrderProductDatas = new SearchBase('tbl_order_products');
            $OrderProductDatas->addCondition('op_grpcls_id', '=', $value['event_user_ticket_plan_id']);
            $OrderProductDatas->addCondition('op_teacher_id', '=', $userId);
            $OrderProductDatas->addOrder('op_id', 'DESC');
            $OrderProductsResults = FatApp::getDb()->fetch($OrderProductDatas->getResultSet());
            $OrderDatas = new SearchBase('tbl_orders');
            $OrderDatas->addCondition('order_id', '=', $OrderProductsResults['op_order_id']);
            $OrderDatas->addCondition('order_is_paid', '=', 1);
            $OrderResults = FatApp::getDb()->fetch($OrderDatas->getResultSet());
            $value['order_data'] = $OrderProductsResults;
            $value['coupon_code'] = $OrderResults['order_discount_coupon_code'];

            $value['plan_name'] = $EventsTicketsplanResult['registration_plan_title'];
            $value['plan_start_date'] = $EventsTicketsplanResult['registration_starting_date'];
            $value['plan_end_date'] = $EventsTicketsplanResult['registration_ending_date'];
            $EventplanResult[$key] = $value;
        }
        // echo "<pre>";
        // print_r($EventplanResult);


        $BenefitConcertplanData = new SearchBase('tbl_event_concert_ticket_plan');
        $BenefitConcertplanData->addCondition('event_user_ticket_pay_status', '=', 1);
        $BenefitConcertplanData->addCondition('event_user_id', '=', $userId);
        $BenefitConcertplanResult = FatApp::getDb()->fetchAll($BenefitConcertplanData->getResultSet());
        foreach ($BenefitConcertplanResult as $key => $value) {
            $BenefitConcertTicketsplanData = new SearchBase('tbl_benefit_concert');
            $BenefitConcertTicketsplanData->addCondition('benefit_concert_deleted', '=', 0);
            $BenefitConcertTicketsplanData->addCondition('benefit_concert_id', '=', $value['event_user_concert_id']);
            $BenefitConcertTicketsplanResult = FatApp::getDb()->fetch($BenefitConcertTicketsplanData->getResultSet());
            $value['plan_name'] = $BenefitConcertTicketsplanResult['benefit_concert_plan_title'];
            $value['plan_start_date'] = $BenefitConcertTicketsplanResult['benefit_concert_starting_date'];
            $value['plan_end_date'] = $BenefitConcertTicketsplanResult['benefit_concert_ending_date'];
            $BenefitConcertplanResult[$key] = $value;
        }

        $PreSymposiumDinnerplanData = new SearchBase('tbl_pre_symposium_dinner_ticket_plan');
        $PreSymposiumDinnerplanData->addCondition('event_user_ticket_pay_status', '=', 1);
        $PreSymposiumDinnerplanData->addCondition('event_user_id', '=', $userId);
        $PreSymposiumDinnerplanResult = FatApp::getDb()->fetchAll($PreSymposiumDinnerplanData->getResultSet());
        foreach ($PreSymposiumDinnerplanResult as $key => $value) {
            $PreSymposiumDinnerTicketsplanData = new SearchBase('tbl_pre_symposium_dinner');
            $PreSymposiumDinnerTicketsplanData->addCondition('pre_symposium_dinner_deleted', '=', 0);
            $PreSymposiumDinnerTicketsplanData->addCondition('pre_symposium_dinner_id', '=', $value['event_user_pre_symposium_dinner_id']);
            $PreSymposiumDinnerTicketsplanResult = FatApp::getDb()->fetch($PreSymposiumDinnerTicketsplanData->getResultSet());
            $value['plan_name'] = $PreSymposiumDinnerTicketsplanResult['pre_symposium_dinner_plan_title'];
            $value['plan_start_date'] = $PreSymposiumDinnerTicketsplanResult['pre_symposium_dinner_starting_date'];
            $value['plan_end_date'] = $PreSymposiumDinnerTicketsplanResult['pre_symposium_dinner_ending_date'];
            $PreSymposiumDinnerplanResult[$key] = $value;
        }



        $SponsorshipeventplanData = new SearchBase('tbl_event_user_become_sponser');
        $SponsorshipeventplanData->addCondition('event_user_payment_status', '=', 1);
        $SponsorshipeventplanData->addCondition('event_user_id', '=', $userId);
        //   $SponsorshipeventplanData->addMultipleFields(['*','COUNT(event_user_become_id) as total_data']);
        //  $SponsorshipeventplanData->addGroupBy('event_user_sponser_selected_plan');
        $SponsorshipeventplanResult = FatApp::getDb()->fetchAll($SponsorshipeventplanData->getResultSet());
        $eventList = array();
        $events = array();
        $plan = '';
        $index = 0;
        $SponserEvent = array();
        // echo "<pre>";
        // print_r($SponsorshipeventplanResult);
        foreach ($SponsorshipeventplanResult as $key => $value) {
            $plan_name = '';
            $plan_qty = '';

            $sponserId = unserialize($value['event_user_sponsrship_id']);
            $sponser_qty = unserialize($value['event_user_sponsership_qty']);
            $qty_json = json_decode($sponser_qty);
            $allValues = array_values((array)$qty_json);
            $qty_index = 0;
            $qty_plan = 0;

            $json = json_decode($sponserId);
            $allKeysOfEmployee = array_keys((array)$json);
            $total_qty = 0;

            $SponEventsSelectionData = new SearchBase('tbl_events_sponsorship_categories');
            $SponEventsSelectionData->addCondition('events_sponsorship_categories_id', '=', $value['event_user_sponser_selected_plan']);
            $SponSorshipEventsSelectionplanResult = FatApp::getDb()->fetch($SponEventsSelectionData->getResultSet());
            $events['event_name'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'];
            $events['event_ending_time'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_ending_date'];
            if (!empty($SponSorshipEventsSelectionplanResult)) {
                foreach ($allKeysOfEmployee as $tempKey) {
                    $sponserPlan = new SearchBase('tbl_sponsorshipcategories');
                    $sponserPlan->addCondition('sponsorshipcategories_id', '=', $tempKey);
                    $sponserPlanResult = FatApp::getDb()->fetch($sponserPlan->getResultSet());
                    $plan_name = $plan_name . " " . $sponserPlanResult['sponsorshipcategories_name'] . ",";
                    $plan_qty = $plan_qty . " " . $allValues[$qty_index] . ",";
                    $qty_plan = $allValues[$qty_index];
                    $total_qty = $total_qty + $qty_plan;
                    if (array_key_exists($SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'], $events)) {
                        $plans = $events['plan'];
                        unset($events['plan']);
                        $plans = $plans . ',' . $sponserPlanResult['sponsorshipcategories_name'];
                        $unique = implode(',', array_unique(str_word_count($plans, 1)));
                        $events['plan'] = $unique;
                        $total = $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] + $qty_plan;;
                        unset($events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']]);
                        $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $total;
                    } else {
                        $plan = $sponserPlanResult['sponsorshipcategories_name'];
                        $unique = implode(',', array_unique(str_word_count($plan, 1)));
                        $events['plan'] = $unique;
                        $events['index'] = $index;
                        // $events['plan'] = $plan;
                        $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $qty_plan;
                    }
                    if (array_key_exists($sponserPlanResult['sponsorshipcategories_name'], $eventList)) {
                        $total = $eventList[$sponserPlanResult['sponsorshipcategories_name']] + $qty_plan;
                        unset($eventList[$sponserPlanResult['sponsorshipcategories_name']]);
                        $eventList[$sponserPlanResult['sponsorshipcategories_name']] = $total;
                    } else {
                        $plan = $sponserPlanResult['sponsorshipcategories_name'];
                        //$events['plan']=$plan;
                        $eventList[$sponserPlanResult['sponsorshipcategories_name']] = $qty_plan;
                    }
                    $qty_index++;
                }

                $OrderProductData = new SearchBase('tbl_order_products');
                $OrderProductData->addCondition('op_grpcls_id', '=', $value['event_user_become_id']);
                $OrderProductData->addCondition('op_teacher_id', '=', $userId);
                $OrderProductData->addOrder('op_id', 'DESC');
                $OrderProductsResult = FatApp::getDb()->fetch($OrderProductData->getResultSet());
                $OrderData = new SearchBase('tbl_orders');
                $OrderData->addCondition('order_id', '=', $OrderProductsResult['op_order_id']);
                $OrderData->addCondition('order_is_paid', '=', 1);
                $OrderResult = FatApp::getDb()->fetch($OrderData->getResultSet());
                $events['order_data'] = $OrderProductsResult;
                $events['coupon_code'] = $OrderResult['order_discount_coupon_code'];
                $value['total'] = $events[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']];
                $value['event_name'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title'];
                $value['event_ending_time'] = $SponSorshipEventsSelectionplanResult['events_sponsorship_categories_ending_date'];
                $value['sponser_plan'] = $plan_name;
                $value['sponser_plan_qty'] = $plan_qty;
                $SponsorshipeventplanResult[$key] = $value;
                $SponserEvent[$SponSorshipEventsSelectionplanResult['events_sponsorship_categories_plan_title']] = $events;
            }
            $index++;
        }
        $sponserEventData = $SponserEvent;

        $SponsorshipplanData = new SearchBase('tbl_event_user_become_sponser');
        $SponsorshipplanData->addCondition('event_user_payment_status', '=', 1);
        $SponsorshipplanData->addCondition('event_user_id', '=', $userId);
        $SponsorshipplanResult = FatApp::getDb()->fetchAll($SponsorshipplanData->getResultSet());
        $sopnsersList = array();
        foreach ($SponsorshipplanResult as $key => $value) {
            $plan_name = '';
            $plan_qty = '';
            $sponserId = unserialize($value['event_user_sponsrship_id']);
            $sponser_qty = unserialize($value['event_user_sponsership_qty']);
            $qty_json = json_decode($sponser_qty);
            $allValues = array_values((array)$qty_json);
            $qty_index = 0;
            $qty_plan = 0;
            $json = json_decode($sponserId);
            $allKeysOfEmployee = array_keys((array)$json);
            foreach ($allKeysOfEmployee as $tempKey) {
                $sponserPlan = new SearchBase('tbl_sponsorshipcategories');
                $sponserPlan->addCondition('sponsorshipcategories_id', '=', $tempKey);
                $sponserPlanResult = FatApp::getDb()->fetch($sponserPlan->getResultSet());
                $plan_name = $plan_name . " " . $sponserPlanResult['sponsorshipcategories_name'] . ",";
                $plan_qty = $plan_qty . " " . $allValues[$qty_index] . ",";
                $qty_plan = $allValues[$qty_index];
                if (array_key_exists($sponserPlanResult['sponsorshipcategories_name'], $sopnsersList)) {
                    $total = $sopnsersList[$sponserPlanResult['sponsorshipcategories_name']] + $qty_plan;
                    unset($sopnsersList[$sponserPlanResult['sponsorshipcategories_name']]);
                    $sopnsersList[$sponserPlanResult['sponsorshipcategories_name']] = $total;
                } else {
                    $sopnsersList[$sponserPlanResult['sponsorshipcategories_name']] = $qty_plan;
                }
                $qty_index++;
            }
            $SponEventsSelectionData = new SearchBase('tbl_three_reasons');
            $SponEventsSelectionData->addCondition('three_reasons_id', '=', $value['event_user_sponser_selected_plan']);
            $SponSorshipEventsSelectionplanResult = FatApp::getDb()->fetch($SponEventsSelectionData->getResultSet());
            $value['event_data'] = $SponSorshipEventsSelectionplanResult;
            $value['sponser_plan'] = $plan_name;
            $value['sponser_plan_qty'] = $plan_qty;
            $SponsorshipplanResult[$key] = $value;
        }
        $PurchaseSponserShip = $sopnsersList;
        $TotalEventsTicketsplanData = new SearchBase('tbl_three_reasons');
        $TotalEventsTicketsplanData->addCondition('three_reasons_deleted', '=', 0);
        $TotalEventsTicketsplanResult = FatApp::getDb()->fetchAll($TotalEventsTicketsplanData->getResultSet());
        $DonationplanData = new SearchBase('tbl_event_user_donation');
        $DonationplanData->addCondition('event_user_donation_status', '=', 1);
        $DonationplanData->addCondition('event_user_user_id', '=', $userId);
        $DonationplanData->addOrder('event_user_donation_id', 'DESC');
        $DonationplanResult = FatApp::getDb()->fetchAll($DonationplanData->getResultSet());
        $DisclaimerSection = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_DISCLAIMER_SECTION, $this->siteLangId);
        $userFirstName = EventUserAuthentication::getLoggedUserAttribute('user_first_name');



        $BenefitConcertplanData = new SearchBase('tbl_event_concert_ticket_plan');
        $BenefitConcertplanData->addCondition('event_user_ticket_pay_status', '=', 1);
        $BenefitConcertplanData->addCondition('event_user_id', '=', $userId);

        $BenefitConcertplanResult = FatApp::getDb()->fetchAll($BenefitConcertplanData->getResultSet());
        foreach ($BenefitConcertplanResult as $key => $value) {

            $OrderProductData = new SearchBase('tbl_order_products');
            $OrderProductData->addCondition('op_grpcls_id', '=', $value['event_concert_ticket_plan_id']);
            $OrderProductData->addCondition('op_teacher_id', '=', $userId);
            $OrderProductData->addOrder('op_id', 'DESC');
            $OrderProductsResult = FatApp::getDb()->fetch($OrderProductData->getResultSet());
            $OrderData = new SearchBase('tbl_orders');
            $OrderData->addCondition('order_id', '=', $OrderProductsResult['op_order_id']);
            $OrderData->addCondition('order_is_paid', '=', 1);
            $OrderResult = FatApp::getDb()->fetch($OrderData->getResultSet());
            $value['order_data'] = $OrderProductsResult;
            $value['coupon_code'] = $OrderResult['order_discount_coupon_code'];
            $value['plan_name'] = $BenefitConcertTicketsplanResult['benefit_concert_plan_title'];
            $value['plan_start_date'] = $BenefitConcertTicketsplanResult['benefit_concert_starting_date'];
            $value['plan_end_date'] = $BenefitConcertTicketsplanResult['benefit_concert_ending_date'];
            $BenefitConcertplanResult[$key] = $value;
        }
        
        $this->set('PreSymposiumDinnerplanResult', $PreSymposiumDinnerplanResult);
        $this->set('BenefitConcertplanResult', $BenefitConcertplanResult);
        $this->set('userFirstName', $userFirstName);
        $this->set('DisclaimerSection', $DisclaimerSection);
        $this->set('sponserEventData', $sponserEventData);
        $this->set('SponsorshipeventplanResult', $SponsorshipeventplanResult);
        $this->set('DonationplanResult', $DonationplanResult);
        $this->set('TotalEventsTicketsplanResult', $TotalEventsTicketsplanResult);
        $this->set('SponsorshipplanResult', $SponsorshipplanResult);
        $this->set('PurchaseSponserShip', $PurchaseSponserShip);
        $this->set('EventplanResult', $EventplanResult);
        $this->_template->addJs('js/jquery.form.js');
        $this->_template->addJs('js/jquery.inputmask.bundle.js');
        $this->_template->addJs('js/cropper.js');
        $this->_template->addCss('css/event.css');
        $this->_template->addJs('js/intlTelInput.js');
        $this->_template->addCss('css/intlTelInput.css');
        $this->set('isProfilePicUploaded', EventUser::isProfilePicUploaded());
        $this->set('SponSorshipEventsSelectionplanResult', $SponSorshipEventsSelectionplanResult);
        $this->set('languages', Language::getAllNames(false));
        $this->set('userDetails', $userDetails);
        $this->set('contactFrm', $contactFrm);
        $this->set('userId', $userId);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    public function report()
    {
        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = $this->getAgendaForm($testimonialId);
        if (0 < $testimonialId) {
            $data = EventsReportComments::getAttributesById($testimonialId, [
                'events_report_comments_id',
                'user_id',
                'events_report_comments_information'
            ]);
            if ($data === false) {

                FatUtility::dieWithError($this->str_invalid_request);
            }
            $agendafrm->fill($data);
            $this->set('records', $data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('events_report_comments_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('agendafrm', $agendafrm);
        $this->_template->render(false, false);
    }

    private function getAgendaForm($testimonialId)
    {
        $userId = EventUserAuthentication::getLoggedUserId();
        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = new Form('frmAgendaTestimonials');
        $agendafrm->addHiddenField(Label::getLabel('LBl_Id'), 'events_report_comments_id', $testimonialId);
        $agendafrm->addHiddenField('', 'user_id', $userId);
        $agendafrm->addTextarea(Label::getLabel('LBL_Event_Location', $this->adminLangId), 'events_report_comments_information');
        $agendafrm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Submit', $this->adminLangId));
        return $agendafrm;
    }

    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = EventsReportComments::getAttributesById($testimonialId, [
                'user_id',
                'events_report_comments_information'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('events_report_comments_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function agendasetup()
    {
        $post = FatApp::getPostedData();

        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = $post['events_report_comments_id'];
        unset($post['events_report_comments_id']);
        if ($testimonialId == 0) {
            $post['events_report_comments_added_on'] = date('Y-m-d H:i:s');
            $post['events_report_comments_user'] = 'Event';
        }
        $record = new EventsReportComments($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $res = EmailHandler::sendSmtpEmail('admin@ubuntutalks.com', 'Report Issue', $post['events_report_comments_information'], '', '', $this->siteLangId, '', '');
        $this->set('msg', 'Report Issue Sent');
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }


    public function dailySchedule()
    {
        $userId = EventUserAuthentication::getLoggedUserId();
        $selectedPlan = new SearchBase('tbl_event_user_ticket_plan');
        $selectedPlan->addCondition('event_user_id', '=', $userId);
        $selectedPlan->addCondition('event_user_ticket_pay_status', '=', 1);
        $selectedPlan->addGroupBy('event_user_plan_id');
        $fetch_selectedPlan = $selectedPlan->getResultSet();
        $fetch_plan_Data = FatApp::getDb()->fetchAll($fetch_selectedPlan);
        $AgendaListData = array();
        $selectedEvents = array();
        foreach ($fetch_plan_Data as $key => $value) {
            $fetch_selected_All = new SearchBase('tbl_three_reasons');
            $fetch_selected_All->addCondition('three_reasons_deleted', '=', 0);
            $fetch_selected_All->addCondition('three_reasons_active', '=', 1);
            $fetch_selected_All->addCondition('three_reasons_id', '=', $value['event_user_plan_id']);
            $get_all_event = $fetch_selected_All->getResultSet();
            $plan_Data = FatApp::getDb()->fetchAll($get_all_event);
            $AgendaListData[$value['event_user_plan_id']] = $plan_Data;
            $selectedEvents[$value['event_user_plan_id']] = $value['event_user_plan_id'];
            foreach ($plan_Data as $plan => $data) {
                $plan = explode(',', $data['registration_plan_combo_events']);
                if (empty($plan)) {
                    array_push($plan, $data['registration_plan_combo_events']);
                }
                if (in_array($value['event_user_plan_id'], $plan)) {
                } else {
                    $fetch_agenda_All = new SearchBase('tbl_agenda');
                    $fetch_agenda_All->addCondition('agenda_active', '=', 0);
                    $fetch_agenda_All->addCondition('agenda_deleted', '=', 0);
                    $fetch_agenda_All->addCondition('event_id', '=', $data['three_reasons_id']);
                    $fetch_agenda_All->addMultipleFields(['COUNT(event_id) as available_data']);
                    $fetch_agenda_All->addGroupBy('event_id');
                    $agenda_all_event = $fetch_agenda_All->getResultSet();
                    $agenda_Data = FatApp::getDb()->fetch($agenda_all_event);
                    $data['agenda_details'] = $agenda_Data;
                    $AgendaListData[$data['three_reasons_id']] = $data;
                    $selectedEvents[$data['three_reasons_id']] = $data['three_reasons_id'];
                    $AgendaListData[$data['three_reasons_id']]['available_data'] = $agenda_Data['available_data'];
                    if ($data['registration_plan_combo_events'] != '') {
                        $plan = explode(',', $data['registration_plan_combo_events']);
                        if (empty($plan)) {
                            array_push($plan, $data['registration_plan_combo_events']);
                        }
                        foreach ($plan as $event => $combo) {
                            $fetch_agenda_combo_All = new SearchBase('tbl_agenda');
                            $fetch_agenda_combo_All->addCondition('agenda_active', '=', 0);
                            $fetch_agenda_combo_All->addCondition('agenda_deleted', '=', 0);
                            $fetch_agenda_combo_All->addCondition('event_id', '=', $combo);
                            $fetch_agenda_combo_All->addMultipleFields(['COUNT(event_id) as available_data']);
                            $fetch_agenda_combo_All->addGroupBy('event_id');
                            $fetch_agenda_data_combo_All = $fetch_agenda_All->getResultSet();
                            $agenda_combo_Data = FatApp::getDb()->fetch($fetch_agenda_data_combo_All);
                            $selectedEvents[$combo] = $combo;
                            $AgendaListData[$combo]['available_data'] = $agenda_combo_Data['available_data'];
                            $AgendaListData[$combo]['agenda_details'] = $agenda_combo_Data;
                        }
                    }
                }
            }
        }
        $fetchAll = new SearchBase('tbl_three_reasons');
        $fetchAll->addCondition('three_reasons_deleted', '=', 0);
        $fetchAll->addCondition('three_reasons_active', '=', 1);
        $fetchAll->addOrder('registration_starting_date', 'ASC');
        $fetchAll_results = $fetchAll->getResultSet();
        $eventData = FatApp::getDb()->fetchAll($fetchAll_results);
        foreach ($eventData as $event_key => $event_value) {
            $event_all_agenda = new SearchBase('tbl_agenda');

            $event_all_agenda->addCondition('event_id', '=', $event_value['three_reasons_id']);
            $event_all_agenda->addCondition('agenda_deleted', '=', 0);
            $event_all_agenda_set = $event_all_agenda->getResultSet();
            $agenda_event_data = FatApp::getDb()->fetchAll($event_all_agenda_set);

            $event_value['available_data'] = sizeOf($agenda_event_data);
            $eventData[$event_key] = $event_value;
        }
        $this->set('check_event', $check_event);
        $this->set('AgendaEventsList', $AgendaListData);
        if (!empty($AgendaListData)) {
            $srch_full_agenda_details = new SearchBase('tbl_agenda');
            $srch_full_agenda_details->addCondition('agenda_deleted', '=', 0);
            $srch_full_agenda_details->addOrder('agenda_start_time');
            $full_agenda_categories = $srch_full_agenda_details->getResultSet();
            $FullAgendaCategoriesList = FatApp::getDb()->fetchAll($full_agenda_categories);
            $this->set('FullAgendaCategoriesList', $FullAgendaCategoriesList);
        }
        $this->_template->render(false, false);
    }

    public function requirement()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $userObj = new EventUser($userId);
        $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
        $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
            'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
            'user_gender', 'user_food_department', 'user_phone', 'user_phone_code', 'user_country_id',
            'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_food_allergies', 'user_other_food_restriction', 'user_other_requirement'
        ));
        $userRow['user_phone'] = ($userRow['user_phone'] == 0) ? '' : $userRow['user_phone'];
        $food_data = explode(',', $userRow['user_food_department']);
        $profileFrms = $this->getDietProfileInfoForm($userRow['user_is_teacher'], false, $food_data);
        $userRow['user_phone'] = $userRow['user_phone_code'] . $userRow['user_phone'];
        $profileFrms->fill($userRow);
        $emailChangeReqObj = new UserEmailChangeRequest();
        $userPendingRequest = $emailChangeReqObj->checkPendingRequestForUser(EventUserAuthentication::getLoggedUserId());
        $frm = $this->getChangeEmailForm();
        $ChnagePasswordfrm = $this->getChangePasswordForm();
        $userRow['user_food_department'] = json_decode($userRow['user_food_department'], true);

        $diet_data = EventUser::getFoodDepartmentArr();

        $this->set('ChnagePasswordfrm', $ChnagePasswordfrm);
        $this->set('frm', $frm);
        $this->set('userPendingRequest', $userPendingRequest);
        $this->set('isProfilePicUploaded', EventUser::isProfilePicUploaded());
        $this->set('userRow', $userRow);
        $this->set('profileFrms', $profileFrms);
        $this->set('foodData', $food_data);
        $this->set('profileImgFrm', $profileImgFrm);
        $this->set('languages', Language::getAllNames(false));
        $this->set('userDetails', $userDetails);
        $this->set('diet_data', $diet_data);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render(false, false);
    }

    private function getDietProfileInfoForm($teacher = false, bool $setUnique = false, $data1 = [])
    {
        $frm = new Form('frmProfileInfoRequirement');
        $frm->addHTML('', 'personal_information', '');
        if ($teacher) {
            $frm->addHiddenField('', 'user_id', 'user_id');
            $fldUname = $frm->addTextBox(Label::getLabel('LBL_Username'), 'user_url_name');
            $fldUname->setUnique('tbl_users', 'user_url_name', 'user_id', 'user_id', 'user_id');
            $fldUname->requirements()->setRegularExpressionToValidate('^[A-Za-z0-9-_]{3,35}$');
            $fldUname->requirements()->setRequired();
            $fldUname->requirements()->setCustomErrorMessage(Label::getLabel('LBL_Invalid_Username', $this->siteLangId));
            // $fldUname->requirements()->setUsername();
        }
        $diets_data = EventUser::getFoodDepartmentArr();
        foreach ($diets_data as $key => $week) {
            $speekLangField = $frm->addCheckBox($week, 'user_food_department[' . $key . ']', $week, ['class' => 'diet-boxes'], false, 0);
        }

        $frm->addTextBox(Label::getLabel('LBL_Food_Allergies'), 'user_food_allergies');
        $frm->addTextArea(Label::getLabel('LBL_Other_Food_Restriction'), 'user_other_food_restriction');
        $frm->addTextBox(Label::getLabel('LBL_Other_Requirement'), 'user_other_requirement');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    public function myAccount()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $userObj = new EventUser($userId);
        $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
        $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
            'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
            'user_gender', 'user_food_department', 'user_phone', 'user_phone_code', 'user_country_id',
            'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_food_allergies', 'user_other_food_restriction', 'user_other_requirement'
        ));
        $userRow['user_phone'] = ($userRow['user_phone'] == 0) ? '' : $userRow['user_phone'];
        $profileFrm = $this->getProfileInfoForm($userRow['user_is_teacher']);
        $userRow['user_phone'] = $userRow['user_phone_code'] . $userRow['user_phone'];
        $profileFrm->fill($userRow);
        $emailChangeReqObj = new UserEmailChangeRequest();
        $userPendingRequest = $emailChangeReqObj->checkPendingRequestForUser(EventUserAuthentication::getLoggedUserId());
        $frm = $this->getChangeEmailForm();
        $ChnagePasswordfrm = $this->getChangePasswordForm();
        $profileImgFrm = $this->getProfileImageForm();
        $userFirstName = EventUserAuthentication::getLoggedUserAttribute('user_first_name');
        $isProfilePicUploaded = EventUser::isProfilePicUploaded($userId);
        $this->set('userFirstName', $userFirstName);
        $this->set('isProfilePicUploaded', $isProfilePicUploaded);
        $this->set('ChnagePasswordfrm', $ChnagePasswordfrm);
        $this->set('frm', $frm);
        $this->set('userPendingRequest', $userPendingRequest);
        $this->set('isProfilePicUploaded', EventUser::isProfilePicUploaded());
        $this->set('userRow', $userRow);
        $this->set('profileFrm', $profileFrm);
        $this->set('profileImgFrm', $profileImgFrm);
        $this->set('userId', $userId);
        $this->set('languages', Language::getAllNames(false));
        $this->set('userDetails', $userDetails);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render(false, false);
    }

    public function eventFaq()
    {
        $srch = EventFaq::getSearchObject($this->siteLangId);
        $srch->addMultipleFields(['faq_id', 'faq_category', 'IFNULL(faq_title, faq_identifier) as faq_title', 'faq_description']);
        $srch->joinTable(FaqCategory::DB_TBL, 'LEFT OUTER JOIN', 'faqcat_id=faq_category');
        $srch->addOrder('faqcat_display_order');
        $srch->setPageSize(50);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetchAll($rs);
        $finaldata = [];
        foreach ($data as $val) {
            $finaldata[$val['faq_category']][] = $val;
        }
        $this->set('finaldata', $finaldata);
        $this->set('typeArr', EventFaq::getFaqCategoryArr($this->siteLangId));
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render(false, false);
    }

    private function getProfileInfoForm($teacher = false, bool $setUnique = false)
    {
        $frm = new Form('frmProfileInfo');
        $frm->addHTML('', 'personal_information', '');
        if ($teacher) {
            $frm->addHiddenField('', 'user_id', 'user_id');
            $fldUname = $frm->addTextBox(Label::getLabel('LBL_Username'), 'user_url_name');
            $fldUname->setUnique('tbl_users', 'user_url_name', 'user_id', 'user_id', 'user_id');
            $fldUname->requirements()->setRegularExpressionToValidate('^[A-Za-z0-9-_]{3,35}$');
            $fldUname->requirements()->setRequired();
            $fldUname->requirements()->setCustomErrorMessage(Label::getLabel('LBL_Invalid_Username', $this->siteLangId));
            // $fldUname->requirements()->setUsername();
        }
        $fldFname = $frm->addRequiredField(Label::getLabel('LBL_First_Name'), 'user_first_name');
        // $fldFname->requirements()->setCharOnly();
        $fldLname = $frm->addRequiredField(Label::getLabel('LBL_Last_Name'), 'user_last_name');
        $fldRegisterPlan = $frm->addRequiredField(Label::getLabel('LBL_Registeration_Plan'), 'user_sponsorship_plan');
        $fldRegisterPlan->requirement->setRequired(false);
        $fldSponserShipPlan = $frm->addRequiredField(Label::getLabel('LBL_SponserShip_Plan'), 'user_become_sponsership_plan');
        $fldSponserShipPlan->requirement->setRequired(false);

        // $fldLname->requirements()->setCharOnly();
        $frm->addRadioButtons(Label::getLabel('LBL_Gender'), 'user_gender', EventUser::getGenderArr());

        $frm->addRadioButtons(Label::getLabel('LBL_Diet_Requirement'), 'user_food_department', EventUser::getFoodDepartmentArr());
        $frm->addTextBox(Label::getLabel('LBL_Food_Allergies'), 'user_food_allergies');
        $frm->addTextArea(Label::getLabel('LBL_Other_Food_Restriction'), 'user_other_food_restriction');

        $fldPhn = $frm->addTextBox(Label::getLabel('LBL_Phone'), 'user_phone');
        $fldPhn->requirements()->setRegularExpressionToValidate(applicationConstants::PHONE_NO_REGEX);
        $fldPhn->requirements()->setCustomErrorMessage(Label::getLabel('LBL_PHONE_NO_VALIDATION_MSG'));
        $frm->addHiddenField('', 'user_phone_code');
        if ($teacher) {
            $frm->addTextBox(Label::getLabel('M_Introduction_Video_Link'), 'us_video_link', '');
        }
        $countryObj = new Country();
        $countriesArr = $countryObj->getCountriesArr($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Country'), 'user_country_id', $countriesArr, FatApp::getConfig('CONF_COUNTRY', FatUtility::VAR_INT, 0), array(), Label::getLabel('LBL_Select'));
        $fld->requirement->setRequired(true);
        $timezonesArr = MyDate::timeZoneListing();
        $fld2 = $frm->addSelectBox(Label::getLabel('LBL_TimeZone'), 'user_timezone', $timezonesArr, FatApp::getConfig('CONF_COUNTRY', FatUtility::VAR_INT, 0), array(), Label::getLabel('LBL_Select'));
        $fld2->requirement->setRequired(true);
        if ($teacher) { //== check if user is teacher
            $bookingOptionArr = array(0 => Label::getLabel('LBL_Immediate'), 12 => Label::getLabel('LBL_12_Hours'), 24 => Label::getLabel('LBL_24_Hours'));
            $fld3 = $frm->addSelectBox(Label::getLabel('LBL_Booking_Before'), 'us_booking_before', $bookingOptionArr, 'us_booking_before', array(), Label::getLabel('LBL_Select'));
            $fld3->requirement->setRequired(true);
        }
        /* $fld = $frm->addTextArea(Label::getLabel('LBL_Biography'), 'user_profile_info');
          $fld->requirements()->setLength(1, 500); */
        $frm->addSelectBox(Label::getLabel('LBL_Site_Language'), 'us_site_lang', Language::getAllNames(), '', array(), Label::getLabel('LBL_Select'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        return $frm;
    }

    public function eventProfileInfoForm()
    {
        // $profileImgFrm = $this->getProfileImageForm();
        $userRow = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), array(
            'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
            'user_gender', 'user_food_department as user_food_department', 'user_phone', 'user_phone_code', 'user_country_id',
            'user_is_teacher', 'user_timezone', 'user_profile_info', 'user_food_allergies as user_food_allergies', 'user_other_food_restriction as user_other_food_restriction', 'user_other_requirement as user_other_requirement'
        ));
        $userRow['user_phone'] = ($userRow['user_phone'] == 0) ? '' : $userRow['user_phone'];
        $profileFrm = $this->getProfileInfoForm($userRow['user_is_teacher']);
        $userRow['user_phone'] = $userRow['user_phone_code'] . $userRow['user_phone'];
        $profileFrm->fill($userRow);
        $this->_template->addCss('css/frontend-ltr.css');
        $this->set('isProfilePicUploaded', EventUser::isProfilePicUploaded());
        $this->set('userRow', $userRow);
        $this->set('profileFrm', $profileFrm);
        // $this->set('profileImgFrm', $profileImgFrm);
        $this->set('languages', Language::getAllNames(false));
        $this->_template->render(false, false);
    }

    public function setUpProfileInfo()
    {
        $isTeacher = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), 'user_is_teacher');
        $frm = $this->getProfileInfoForm($isTeacher);
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieWithError(Message::getHtml());
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $user = new EventUser(EventUserAuthentication::getLoggedUserId());
        $user->assignValues($post);
        if (!$user->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($user->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $uaObj = new EventUserAuthentication();
        $uaObj->updateSessionData($post);
        $db->commitTransaction();
        $this->set('msg', Label::getLabel('MSG_Setup_successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function setUpProfileRequirementInfo()
    {
        $isTeacher = EventUser::getAttributesById(EventUserAuthentication::getLoggedUserId(), 'user_is_teacher');
        $frm = $this->getDietProfileInfoForm($isTeacher);
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post);

        $user_food_department  = implode(',', $post['user_food_department']);
        if (isset($post['user_food_department']) && !empty($post['user_food_department']) && $user_food_department[0] != '') {
            $user_food_department  = implode(',', $post['user_food_department']);
            $post['user_food_department'] = $user_food_department;
        } else {
            $post['user_food_department'] = "";
        }
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieWithError(Message::getHtml());
        }

        $db = FatApp::getDb();
        $db->startTransaction();
        $user = new EventUser(EventUserAuthentication::getLoggedUserId());
        $user->assignValues($post);
        if (!$user->save()) {
            $db->rollbackTransaction();
            Message::addErrorMessage($user->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $uaObj = new EventUserAuthentication();
        $uaObj->updateSessionData($post);
        $db->commitTransaction();
        $this->set('msg', Label::getLabel('MSG_Setup_successful'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('contact'));
        }
        if (!CommonHelper::verifyCaptcha()) {
            Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::generateUrl('contact'));
        }
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->sendContactFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('contact'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Name', $langId), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Your_Phone', $langId), 'phone');
        $fld_phn->requirements()->setRegularExpressionToValidate('^[\s()+-]*([0-9][\s()+-]*){5,20}$');
        $fld_phn->requirements()->setCustomErrorMessage(Label::getLabel('VLD_ADD_VALID_PHONE_NUMBER', $langId));
        $frm->addTextArea(Label::getLabel('LBL_Your_Message', $langId), 'message')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

    public function country()
    {
        $select_design = $_REQUEST['product_code'];
        $con = new Country();
        $FindObj = $con->getCountryById($select_design);
        $country_codes = strval($FindObj["country_code"]);

        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_codes);

        $timezone_offsets = array();
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }
        $timezone_list = array();
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));

            $pretty_offset = "timezone ${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }
        echo json_encode($timezone_list);
        return $timezone_list;
    }

    public function changeEmailForm()
    {
        $emailChangeReqObj = new UserEmailChangeRequest();
        $userPendingRequest = $emailChangeReqObj->checkPendingRequestForUser(EventUserAuthentication::getLoggedUserId());
        $frm = $this->getChangeEmailForm();
        $this->set('frm', $frm);
        $this->set('userPendingRequest', $userPendingRequest);
        $this->_template->render(false, false);
    }

    public function setUpEmail()
    {
        $EmailFrm = $this->getChangeEmailForm();
        $post = $EmailFrm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($EmailFrm->getValidationErrors()));
        }
        $userId = EventUserAuthentication::getLoggedUserId();
        $userObj = new EventUser($userId);
        $srch = $userObj->getUserSearchObj(['user_id', 'user_first_name', 'user_last_name', 'credential_password']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs, 'user_id');

        $userData = [
            'user_email' => $post['new_email'],
            'user_first_name' => $userRow['user_first_name'],
            'user_last_name' => $userRow['user_last_name']
        ];
        // echo "<pre>";
        // print_r($userData);
        if ($userRow['credential_password'] != EventUserAuthentication::encryptPassword($post['current_password'])) {
            FatUtility::dieJsonError(Label::getLabel('MSG_YOUR_CURRENT_PASSWORD_MIS_MATCHED'));
        }
        $_token = $userObj->prepareUserVerificationCode();
        $error = '';
        if (!$this->sendEmailChangeVerificationLink($_token, $userData, $error)) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Unable_to_process_your_requset') . ' : ' . $error);
        }
        $emailChangeReqObj = new UserEmailChangeRequest();
        $emailChangeReqObj->deleteOldLinkforUser($userId);
        $postData = [
            'uecreq_user_id' => $userId,
            'uecreq_email' => $post['new_email'],
            'uecreq_token' => $_token,
            'uecreq_status' => 0,
            'uecreq_created' => date('Y-m-d H:i:s'),
            'uecreq_updated' => date('Y-m-d H:i:s'),
            'uecreq_expire' => date('Y-m-d H:i:s', strtotime('+ 24 hours', strtotime(date('Y-m-d H:i:s'))))
        ];
        $emailChangeReqObj->assignValues($postData);
        if (!$emailChangeReqObj->save()) {
            FatUtility::dieJsonError(Label::getLabel('MSG_Unable_to_process_your_requset') . $emailChangeReqObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('MSG_Please_verify_your_email'));
    }
    private function sendEmailChangeVerificationLink($_token, $data, &$error)
    {
        $link = CommonHelper::generateFullUrl('EventUser', 'verifyEmail', [$_token], CONF_WEBROOT_FRONT_URL);
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $email = new EmailHandler();
        if (true !== $email->sendEmailChangeVerificationLink($this->siteLangId, $data)) {
            $error = $email->getError();
            return false;
        }
        return true;
    }

    private function getChangePasswordForm()
    {
        $frm = new Form('changePwdFrm');
        $curPwd = $frm->addPasswordField(Label::getLabel('LBL_CURRENT_PASSWORD'), 'current_password');
        $curPwd->requirements()->setRequired();
        $newPwd = $frm->addPasswordField(Label::getLabel('LBL_NEW_PASSWORD'), 'new_password');
        $newPwd->requirements()->setRequired();
        $newPwd->requirements()->setRegularExpressionToValidate(applicationConstants::PASSWORD_REGEX);
        $newPwd->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Valid_password'));
        $conNewPwd = $frm->addPasswordField(Label::getLabel('LBL_CONFIRM_NEW_PASSWORD'), 'conf_new_password');
        $conNewPwdReq = $conNewPwd->requirements();
        $conNewPwdReq->setRequired();
        $conNewPwdReq->setCompareWith('new_password', 'eq');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        $frm->addSubmitButton('', 'btn_next', Label::getLabel('LBL_next'));
        return $frm;
    }

    public function setUpPassword()
    {
        $pwdFrm = $this->getChangePasswordForm();
        $post = $pwdFrm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($pwdFrm->getValidationErrors());
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($post['new_password'] != $post['conf_new_password']) {
            Message::addErrorMessage(Label::getLabel('MSG_New_Password_Confirm_Password_does_not_match'));
            FatUtility::dieWithError(Message::getHtml());
        }
        if (true !== CommonHelper::validatePassword($post['new_password'])) {
            Message::addErrorMessage(
                Label::getLabel('MSG_PASSWORD_MUST_BE_EIGHT_CHARACTERS_LONG_AND_ALPHANUMERIC')
            );
            FatUtility::dieWithError(Message::getHtml());
        }
        $userObj = new EventUser(EventUserAuthentication::getLoggedUserId());
        $srch = $userObj->getUserSearchObj(['user_id', 'credential_password']);
        $rs = $srch->getResultSet();
        $userRow = FatApp::getDb()->fetch($rs, 'user_id');
        if (false == $userRow) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST'));
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($userRow['credential_password'] != EventUserAuthentication::encryptPassword($post['current_password'])) {
            Message::addErrorMessage(Label::getLabel('MSG_YOUR_CURRENT_PASSWORD_MIS_MATCHED'));
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!$userObj->setLoginPassword($post['new_password'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Password_could_not_be_set') . $userObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Password_changed_successfully'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function Sucesshelp()
    {
        $res = EmailHandler::sendSmtpEmail('admin@ubuntutalks.com', 'Support Help', $_POST['comment'], '', '', $this->siteLangId, '', '');
        $this->set('msg', Label::getLabel('MSG_Email_sent_successfully'));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function profileImageForm()
    {
        $userId = EventUserAuthentication::getLoggedUserId();
        // $isTeacher = User::getAttributesById($userId, 'user_is_teacher');
        // $userSettings = UserSetting::getUserSettings($userId);
        $isTeacherDashboardActive = (EventUser::getDashboardActiveTab() == EventUser::USER_TEACHER_DASHBOARD);
        $profileImgFrm = $this->getProfileImageForm($isTeacherDashboardActive);
        // $profileImgFrm->fill(['us_video_link' => $userSettings['us_video_link'] ?? '']);
        $userFirstName = EventUserAuthentication::getLoggedUserAttribute('user_first_name');
        $isProfilePicUploaded = EventUser::isProfilePicUploaded($userId);
        $this->set('profileImgFrm', $profileImgFrm);
        $this->set('isProfilePicUploaded', $isProfilePicUploaded);
        $this->set('userId', $userId);
        $this->set('userFirstName', $userFirstName);
        $this->_template->render(false, false);
    }

    private function getProfileImageForm($teacher = false)
    {
        $frm = new Form('frmProfile', ['id' => 'frmProfile']);
        $frm->addFileUpload(Label::getLabel('LBL_Profile_Picture'), 'user_profile_image', ['onchange' => 'popupImage(this)', 'accept' => 'image/*']);
        $frm->addHiddenField('', 'update_profile_img', Label::getLabel('LBL_Update_Profile_Picture'), ['id' => 'update_profile_img']);
        $frm->addHiddenField('', 'rotate_left', Label::getLabel('LBL_Rotate_Left'), ['id' => 'rotate_left']);
        $frm->addHiddenField('', 'rotate_right', Label::getLabel('LBL_Rotate_Right'), ['id' => 'rotate_right']);
        $frm->addHiddenField('', 'remove_profile_img', 0, ['id' => 'remove_profile_img']);
        $frm->addHiddenField('', 'action', 'avatar', ['id' => 'avatar-action']);
        $frm->addHiddenField('', 'img_data', '', ['id' => 'img_data']);
        if ($teacher) {
            $vidoLinkfield = $frm->addTextBox(Label::getLabel('M_Introduction_Video_Link'), 'us_video_link', '');
            $vidoLinkfield->requirements()->setRegularExpressionToValidate(applicationConstants::INTRODUCTION_VIDEO_LINK_REGEX);
            $vidoLinkfield->requirements()->setCustomErrorMessage(Label::getLabel('MSG_Please_Enter_Valid_Video_Link'));
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES'));
        $frm->addButton('', 'btn_next', Label::getLabel('LBL_Next'));
        return $frm;
    }

    public function setUpProfileImage()
    {

        $userId = EventUserAuthentication::getLoggedUserId();

        $isTeacherDashboardActive = (EventUser::getDashboardActiveTab() == EventUser::USER_TEACHER_DASHBOARD);
        $profileImgFrm = $this->getProfileImageForm($isTeacherDashboardActive);
        $post = FatApp::getPostedData();
        $post = $profileImgFrm->getFormDataFromArray($post);
        if (false === $post) {
            Message::addErrorMessage(current($profileImgFrm->getValidationErrors()));
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($post['action'] == "demo_avatar" && !empty($_FILES['user_profile_image']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                $msgLblKey = CommonHelper::getFileUploadErrorLblKeyFromCode($_FILES['user_profile_image']['error']);
                Message::addErrorMessage(Label::getLabel($msgLblKey));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            if (CONF_USE_FAT_CACHE) {
                FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'ORIGINAL'), CONF_WEBROOT_FRONTEND));
                FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM'), CONF_WEBROOT_FRONTEND));
                FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'SMALL'), CONF_WEBROOT_FRONTEND));
                FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId), CONF_WEBROOT_FRONTEND));
            }
            $this->set('file', CommonHelper::generateFullUrl('Image', 'user', [$userId, 'ORIGINAL', 0], CONF_WEBROOT_FRONTEND) . '?' . time());
        }
        if ($post['action'] == "avatar" && !empty($_FILES['user_profile_image']['tmp_name'])) {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                $msgLblKey = CommonHelper::getFileUploadErrorLblKeyFromCode($_FILES['user_profile_image']['error']);
                Message::addErrorMessage(Label::getLabel($msgLblKey));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_USER_PROFILE_CROPED_IMAGE, $userId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $data = json_decode(stripslashes($post['img_data']));
            CommonHelper::crop($data, CONF_UPLOADS_PATH . $res);
            $this->set('file', CommonHelper::generateFullUrl('Image', 'user', [$userId, 'MEDIUM', true], CONF_WEBROOT_FRONTEND) . '?' . time());
        }
        // if ($isTeacherDashboardActive) {
        //     $userSettings = new UserSetting($userId);
        //     if (!$userSettings->saveData(['us_video_link' => $post['us_video_link']])) {
        //         FatUtility::dieJsonError($userSettings->getError());
        //     }
        // }
        $this->set('msg', Label::getLabel('MSG_Data_uploaded_successfully'));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeProfileImage()
    {
        $userId = EventUserAuthentication::getLoggedUserId();
        if (1 > $userId) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST_ID'));
            FatUtility::dieWithError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_USER_PROFILE_CROPED_IMAGE, $userId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        if (CONF_USE_FAT_CACHE) {
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'ORIGINAL'), CONF_WEBROOT_FRONTEND));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM'), CONF_WEBROOT_FRONTEND));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'SMALL'), CONF_WEBROOT_FRONTEND));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'EXTRASMALL'), CONF_WEBROOT_FRONTEND));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId), CONF_WEBROOT_FRONTEND));
        }
        $this->set('msg', Label::getLabel('MSG_File_deleted_successfully'));
        $this->_template->render(false, false, 'json-success.php');
    }
}
