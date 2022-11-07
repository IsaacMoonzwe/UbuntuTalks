<?php

class EducationController extends MyAppController
{

    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $ReferralCampaignBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_REFERRAL_CAMPAIGN_BANNER, $this->siteLangId);
        $EducationCampaignLeftSectionBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_EDUCATION_CAMPAIGN_LEFT_SECTION_BANNER, $this->siteLangId);
        $EducationCampaignBenifitsBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_EDUCATION_CAMPAIGN_BENEFITS_SECTION_BANNER, 
        $this->siteLangId);
        $this->set('EducationCampaignBenifitsBanner', $EducationCampaignBenifitsBanner);
        $this->set('ReferralCampaignBanner', $ReferralCampaignBanner);
        $this->set('EducationCampaignLeftSectionBanner', $EducationCampaignLeftSectionBanner);
        $this->set('contactLeftSection', $contactLeftSection);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        $this->_template->render();
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatApp::redirectUser(CommonHelper::generateUrl('education'));
        }
        if (!CommonHelper::verifyCaptcha()) {
            Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::generateUrl('referral'));
        }
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->sendEducationFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('education'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $job_function_data=new SearchBase('tbl_jobfunction');
        $job_function_data->addCondition('testimonial_deleted', '=', '0');
        $job_function_dropdown_value = FatApp::getDb()->fetchAll($job_function_data->getResultSet());
        $person_job_function=array();
        foreach ($job_function_dropdown_value as $key => $value) {
            $person_job_function[$value['testimonial_user_name']] = $value['testimonial_user_name'];
        }
        $frm->addRequiredField(Label::getLabel('LBL_Your_Name', $langId), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Contact\'s_First_Name', $langId), 'pfname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Contact\'s_Last_Name', $langId), 'plname', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Contact\'s_Corporate_Email', $langId), 'pcemail', '');
        $frm->addRequiredField(Label::getLabel('LBL_Company_Name', $langId), 'cpcname', '');
        $frm->addSelectBox(Label::getLabel('LBL_Contact\'s_Job_Function', $langId), 'pjname', $person_job_function, -1, [], '');
        $frm->addTextArea(Label::getLabel('LBL_Let_us_know_your_specific_needs', $langId), 'message')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

}
