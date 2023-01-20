<?php
class EventsController extends MyEventAppController
{
    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        if (EventUserAuthentication::isUserLogged()) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $userObj = new EventUser($userId);
            $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
            $this->set('userDetails', $userDetails);
        }
        $UTLanguageContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_UT_LANGUAGE_SYMPOSIUM, $this->siteLangId);
        $UTRegistration = EventRegistration::getBlockContent(EventRegistration::BLOCK_UT_REGISTRATION, $this->siteLangId);
        $ProgramSpeakersDescription = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_PROGRAM_AND_SPEAKERS_DESCRIPTION, $this->siteLangId);
        $ProgramSpeakersFirstKeyNoteDescription = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_SYMPOSIUM_PROGRAM_FIRST_KEY_NOTE, $this->siteLangId);
        $ProgramSpeakersSecondKeyNoteDescription = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_SYMPOSIUM_PROGRAM_SECOND_KEY_NOTE, $this->siteLangId);
        $ProgramSpeakersThirdKeyNoteDescription = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_SYMPOSIUM_PROGRAM_THIRD_KEY_NOTE, $this->siteLangId);
        $ProgramSpeakersTestimonial = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_PROGRAM_AND_SPEAKERS_TESTIMONIAL, $this->siteLangId);
        $ProgramSpeakersTestimonial = ProgramAndSpeakers::getBlockContent(ProgramAndSpeakers::BLOCK_PROGRAM_AND_SPEAKERS_TESTIMONIAL, $this->siteLangId);
        $VenueSection = TravelAndAccommodations::getBlockContent(TravelAndAccommodations::BLOCK_VENUE_SECTION, $this->siteLangId);
        $AccommodationsSection = TravelAndAccommodations::getBlockContent(TravelAndAccommodations::BLOCK_ACCOMMODATIONS_SECTION, $this->siteLangId);
        $TravelSection = TravelAndAccommodations::getBlockContent(TravelAndAccommodations::BLOCK_TRAVEL_SECTION, $this->siteLangId);
        $MapSection = TravelAndAccommodations::getBlockContent(TravelAndAccommodations::BLOCK_MAP_SECTION, $this->siteLangId);
        $MapInformationSection = TravelAndAccommodations::getBlockContent(TravelAndAccommodations::BLOCK_MAP_INFORMATION_SECTION, $this->siteLangId);
        $CovidInformation = CovidInformation::getBlockContent(CovidInformation::BLOCK_COVID19_INFORMATION, $this->siteLangId);
        $PressInformation = EventPress::getBlockContent(EventPress::BLOCK_PRESS_INFORMATION, $this->siteLangId);
        $ContactInformation = EventContact::getBlockContent(EventContact::BLOCK_CONTACT_INFORMATION, $this->siteLangId);
        $PrivacyInformation = EventContact::getBlockContent(EventContact::BLOCK_PRIVACY_INFORMATION, $this->siteLangId);
        $AccommodationFirst = Accommodations::getBlockContent(Accommodations::BLOCK_PROTEA_BY_MARRIOT, $this->siteLangId);
        $AccommodationSecond = Accommodations::getBlockContent(Accommodations::BLOCK_NEELKANTH_SAROVAR_PREMIERE, $this->siteLangId);
        $AccommodationThird = Accommodations::getBlockContent(Accommodations::BLOCK_RADDISON_BLU, $this->siteLangId);
        $srch = new SearchBase('tbl_speakers');
        $srch->addCondition('speakers_deleted', '=', 0);
        $srch->addCondition('speakers_active', '=', 1);
        $srch->addOrder('speakers_display_order', 'ASC');
        $rs = $srch->getResultSet();
        $SpeakersList = FatApp::getDb()->fetchAll($rs);
        foreach ($SpeakersList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $value['speakers_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $records[$key] = $value;
        }
        $speakers_positions_listing = new SearchBase('tbl_speakers_position_listing');
        $speakers_positions_listing->addCondition('speakers_position_deleted', '=', 0);
        $speakers_positions_listing->addCondition('speakers_position_active', '=', 1);
        $positionsListing = $speakers_positions_listing->getResultSet();
        $SpeakersPositionsListing = FatApp::getDb()->fetchAll($positionsListing);
        $srch_categories = new SearchBase('tbl_sponsorshipcategories');
        $srch_categories->addCondition('sponsorshipcategories_deleted', '=', 0);
        $srch_categories->addCondition('sponsorshipcategories_active', '=', 1);
        $srch_categories->addCondition('sponsorshipcategories_type', '=', 'Regular');
        $sponsorship_categories = $srch_categories->getResultSet();
        $SponsorshipCategoriesList = FatApp::getDb()->fetchAll($sponsorship_categories);
        $srch_categories_dinner = new SearchBase('tbl_sponsorshipcategories');
        $srch_categories_dinner->addCondition('sponsorshipcategories_deleted', '=', 0);
        $srch_categories_dinner->addCondition('sponsorshipcategories_active', '=', 1);
        $srch_categories_dinner->addCondition('sponsorshipcategories_type', '=', 'Dinner');
        $sponsorship_categories_dinner = $srch_categories_dinner->getResultSet();
        $SponsorshipCategoriesDinnerList = FatApp::getDb()->fetchAll($sponsorship_categories_dinner);
        $srch_events_details = new SearchBase('tbl_event_and_agenda');
        $events_categories = $srch_events_details->getResultSet();
        $EventsList = FatApp::getDb()->fetchAll($events_categories);
        $srch_agenda_details = new SearchBase('tbl_agenda');
        $srch_agenda_details->addCondition('agenda_deleted', '=', 0);
        $srch_agenda_details->addGroupBy('DATE(agenda_start_time)');
        $agenda_categories = $srch_agenda_details->getResultSet();
        $AgendaCategoriesList = FatApp::getDb()->fetchAll($agenda_categories);

        $AgendaEventsrecods = FatApp::getDb()->fetchAll($agenda_categories);
        foreach ($AgendaCategoriesList as $key => $value) {
            $srch_agends_events_details = new SearchBase('tbl_three_reasons');
            $srch_agends_events_details->addCondition('three_reasons_deleted', '=', 0);
            $srch_agends_events_details->addCondition('three_reasons_active', '=', 1);
            $srch_agends_events_details->addOrder('registration_starting_date', 'DESC');
            $agenda_events_categories = $srch_agends_events_details->getResultSet();
            $AgendaEventsList = FatApp::getDb()->fetchAll($agenda_events_categories);
        }
        foreach ($AgendaEventsList as $key => $value) {
            $srch_agenda = new SearchBase('tbl_agenda');
            $srch_agenda->addCondition('agenda_deleted', '=', 0);
            $srch_agenda->addCondition('event_id', '=', $value['three_reasons_id']);
            $srch_agenda_set = $srch_agenda->getResultSet();
            $agenda_data = FatApp::getDb()->fetchAll($srch_agenda_set);
            $value['available_data'] = sizeOf($agenda_data);
            $AgendaEventsList[$key] = $value;
        }
        $srch_full_agenda_details = new SearchBase('tbl_agenda');
        $srch_full_agenda_details->addCondition('agenda_deleted', '=', 0);
        $srch_full_agenda_details->addOrder('agenda_start_time');
        $full_agenda_categories = $srch_full_agenda_details->getResultSet();
        $FullAgendaCategoriesList = FatApp::getDb()->fetchAll($full_agenda_categories);

        $srch_sponsorship = new SearchBase('tbl_sponsorship');
        $srch_sponsorship->addCondition('sponsorship_deleted', '=', 0);
        $srch_sponsorship->addCondition('sponsorship_active', '=', 1);
        $sponsorship = $srch_sponsorship->getResultSet();
        $sponsorshipList = FatApp::getDb()->fetchAll($sponsorship);
        foreach ($sponsorshipList as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_SPONSORSHIP_IMAGE, $value['sponsorship_id'], 0, -1);
            $value['sponsorship_image'] = $testimonialImages;
            $Sponsershiprecords[$key] = $value;
        }
        $Registration_categories = new SearchBase('tbl_three_reasons');
        $Registration_categories->addCondition('three_reasons_deleted', '=', 0);
        $Registration_categories->addCondition('three_reasons_active', '=', 1);
        $Registration_categories->addOrder('three_reasons_display_order', 'ASC');
        $RegistrationPlan_categories = $Registration_categories->getResultSet();
        $RegistrationPlanDetailsList = FatApp::getDb()->fetchAll($RegistrationPlan_categories);
        $events_tickets = new SearchBase('tbl_event_and_agenda');
        $events_tickets->addCondition('event_and_agenda_deleted', '=', 0);
        $events_tickets->addCondition('event_and_agenda_active', '=', 1);
        $event_tickets_categories = $events_tickets->getResultSet();
        $EventTicketsDetailsList = FatApp::getDb()->fetchAll($event_tickets_categories);
        $benefitConcert = new SearchBase('tbl_benefit_concert');
        $benefitConcert->addCondition('benefit_concert_deleted', '=', 0);
        $benefitConcert->addCondition('benefit_concert_active', '=', 1);
        $benefitConcert->addOrder('benefit_concert_id', 'ASC');
        $benefirConcert_categories = $benefitConcert->getResultSet();
        $BenefitConcertDetailsList = FatApp::getDb()->fetchAll($benefirConcert_categories);
        $tickets = new SearchBase('tbl_event_concert_ticket_plan');
        $tickets->addCondition('event_user_ticket_pay_status', '=', 1);
        $tickets->addMultipleFields(['SUM(event_user_ticket_count) as TotalTicket', 'event_user_concert_id']);
        $tickets->addGroupBy('event_user_concert_id');
        $tickets->addOrder('event_user_concert_id', 'ASC');
        $ticketManager = $tickets->getResultSet();
        $ticketManagerDetails = FatApp::getDb()->fetchAll($ticketManager);
        $SponsorContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_SPONSORSHIP_INFORMATION, $this->siteLangId);
        $DonationContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_DONATION_INFORMATION, $this->siteLangId);
        $CodeOfConductContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_CODE_OF_CONDUCT_INFORMATION, $this->siteLangId);
        $DisclaimerSection = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_DISCLAIMER_SECTION, $this->siteLangId);
        $AboutVenue = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_ABOUT_VENUE, $this->siteLangId);
        $BenefitConcertTicketInformation = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_BENEFIT_CONCERT_TICKET_INFORMATION, $this->siteLangId);
        $PreSymposiumDinner = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_PRE_SYMPOSIUM_DINNER, $this->siteLangId);
        $PreSymposiumDinners = new SearchBase('tbl_pre_symposium_dinner');
        $PreSymposiumDinners->addCondition('pre_symposium_dinner_deleted', '=', 0);
        $PreSymposiumDinners->addCondition('pre_symposium_dinner_active', '=', 1);
        $PreSymposiumDinners->addOrder('pre_symposium_dinner_display_order', 'ASC');
        $PreSymposiumDinners_categories = $PreSymposiumDinners->getResultSet();
        $PreSymposiumDinnersDetailsList = FatApp::getDb()->fetchAll($PreSymposiumDinners_categories);
        $BenefitConcertArtists = new SearchBase('tbl_benefit_concert_artists');
        $BenefitConcertArtists->addCondition('benefit_concert_artists_deleted', '=', 0);
        $BenefitConcertArtists->addCondition('benefit_concert_artists_active', '=', 1);
        $BenefitConcertArtists->addOrder('benefit_concert_artists_display_order', 'ASC');
        $BenefirConcertArtists_categories = $BenefitConcertArtists->getResultSet();
        $BenefitConcertArtistsDetailsList = FatApp::getDb()->fetchAll($BenefirConcertArtists_categories);
        $this->set('PreSymposiumDinner', $PreSymposiumDinner);
        $this->set('PreSymposiumDinnersDetailsList', $PreSymposiumDinnersDetailsList);
        $this->set('BenefitConcertArtistsDetailsList', $BenefitConcertArtistsDetailsList);
        $this->set('ticketManagerDetails', $ticketManagerDetails);
        $this->set('BenefitConcertDetailsList', $BenefitConcertDetailsList);
        $this->set('BenefitConcertTicketInformation', $BenefitConcertTicketInformation);
        $this->set('AboutVenue', $AboutVenue);
        $this->set('DisclaimerSection', $DisclaimerSection);
        $this->set('CodeOfConductContent', $CodeOfConductContent);
        $this->set('DonationContent', $DonationContent);
        $this->set('SponsorContent', $SponsorContent);
        $this->set('SpeakersPositionsListing', $SpeakersPositionsListing);
        $this->set('EventTicketsDetailsList', $EventTicketsDetailsList);
        $this->set('RegistrationPlanDetailsList', $RegistrationPlanDetailsList);
        $this->set('EventsList', $EventsList);
        $this->set('AgendaEventsrecods', $AgendaEventsrecods);
        $this->set('FullAgendaCategoriesList', $FullAgendaCategoriesList);
        $this->set('AgendaCategoriesList', $AgendaCategoriesList);
        $this->set('AgendaEventsList', $AgendaEventsList);
        $this->set('ThreeReasonsCategoriesList', $ThreeReasonsCategoriesList);
        $this->set('Sponsershiprecords', $Sponsershiprecords);
        $this->set('SponsorshipCategoriesList', $SponsorshipCategoriesList);
        $this->set('SponsorshipCategoriesDinnerList', $SponsorshipCategoriesDinnerList);
        $this->set('records', $records);
        $this->set('ContactInformation', $ContactInformation);
        $this->set('PrivacyInformation', $PrivacyInformation);
        $this->set('PressInformation', $PressInformation);
        $this->set('CovidInformation', $CovidInformation);
        $this->set('AccommodationThird', $AccommodationThird);
        $this->set('AccommodationSecond', $AccommodationSecond);
        $this->set('AccommodationFirst', $AccommodationFirst);
        $this->set('VenueSection', $VenueSection);
        $this->set('AccommodationsSection', $AccommodationsSection);
        $this->set('TravelSection', $TravelSection);
        $this->set('MapSection', $MapSection);
        $this->set('MapInformationSection', $MapInformationSection);
        $this->set('ProgramSpeakersTestimonial', $ProgramSpeakersTestimonial);
        $this->set('ProgramSpeakersDescription', $ProgramSpeakersDescription);
        $this->set('ProgramSpeakersFirstKeyNoteDescription', $ProgramSpeakersFirstKeyNoteDescription);
        $this->set('ProgramSpeakersSecondKeyNoteDescription', $ProgramSpeakersSecondKeyNoteDescription);
        $this->set('ProgramSpeakersThirdKeyNoteDescription', $ProgramSpeakersThirdKeyNoteDescription);
        $this->set('UTLanguageContent', $UTLanguageContent);
        $this->set('UTRegistration', $UTRegistration);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    public function contactUs()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        if (EventUserAuthentication::isUserLogged()) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $userObj = new EventUser($userId);
            $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
            $this->set('userDetails', $userDetails);
        }
        $ContactInformation = EventContact::getBlockContent(EventContact::BLOCK_CONTACT_INFORMATION, $this->siteLangId);
        $this->set('ContactInformation', $ContactInformation);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render(false, false);
    }

    public function contact()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        if (EventUserAuthentication::isUserLogged()) {
            $userId = EventUserAuthentication::getLoggedUserId();
            $userObj = new EventUser($userId);
            $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
            $this->set('userDetails', $userDetails);
        }
        $ContactInformation = EventContact::getBlockContent(EventContact::BLOCK_CONTACT_INFORMATION, $this->siteLangId);
        $this->set('ContactInformation', $ContactInformation);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
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
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_SPONSORSHIP_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_SPONSORSHIP_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
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
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function sponsorshipimage($recordId, $langId = 0, $sizeType = '', $afile_id = 0, $displayUniversalImage = true)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($recordId);
        $afile_id = FatUtility::int($afile_id);
        $langId = FatUtility::int($langId);
        if ($afile_id > 0) {
            $res = AttachedFile::getAttributesById($afile_id);
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_TESTIMONIAL_IMAGE || !false == $res && $res['afile_type'] == AttachedFile::FILETYPE_SPONSORSHIP_IMAGE) {
                $file_row = $res;
            }
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $recordId, 0, $langId, $displayUniversalImage);
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
            case 'MINMEDIUM':
                $w = 120;
                $h = 120;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'MEDIUM':
                $w = 150;
                $h = 150;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('events'));
        }
        if (!CommonHelper::verifyCaptcha()) {
            Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::generateUrl('event'));
        }
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->sendEventFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('events'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $job_function_data = new SearchBase('tbl_jobfunction');
        $job_function_data->addCondition('testimonial_deleted', '=', '0');
        $job_function_dropdown_value = FatApp::getDb()->fetchAll($job_function_data->getResultSet());
        $person_job_function = array();
        foreach ($job_function_dropdown_value as $key => $value) {
            $person_job_function[$value['testimonial_user_name']] = $value['testimonial_user_name'];
        }
        $frm->addRequiredField(Label::getLabel('LBL_Name', $langId), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Email', $langId), 'email', '');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Contact\'s_First_Name', $langId), 'subject', '');
        $frm->addTextArea(Label::getLabel('LBL_Let_us_know_your_specific_needs', $langId), 'message')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }
}
