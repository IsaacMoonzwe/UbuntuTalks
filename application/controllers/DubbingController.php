<?php

class DubbingController extends MyAppController
{

    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }

        $B2bBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_DUBBING, $this->siteLangId);
        $this->set('ReferralCampaignBenifitsBanner', $ReferralCampaignBenifitsBanner);
        $this->set('ReferralCampaignBanner', $ReferralCampaignBanner);
        $this->set('ReferralCampaignLeftSectionBanner', $ReferralCampaignLeftSectionBanner);
        $this->set('B2bBanner', $B2bBanner);
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
            FatApp::redirectUser(CommonHelper::generateUrl('referral'));
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
            if (!$email->sendReferralFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('referral'));
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $person_job_function = [
            'C-Level' => 'C-Level', 
            'VP-Level' => 'VP-Level', 
            'Management' => 'Management',
            'HR Professional' => 'HR Professional',
            'L&D Professional' => 'L&D Professional',
            'Office Management' => 'Office Management',
            'Student / Intern' => 'Student / Intern',
            'Other' => 'Other'
        ];
        $frm->addRequiredField(Label::getLabel('LBL_Your_Name', $langId), 'name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Contact\'s_First_Name', $langId), 'pfname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Your_Contact\'s_Last_Name', $langId), 'plname', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Contact\'s_Corporate_Email', $langId), 'pcemail', '');
        $frm->addRequiredField(Label::getLabel('LBL_Company_Name', $langId), 'cpcname', '');
        $frm->addSelectBox(Label::getLabel('LBL_Contact\'s_Job_Function', $langId), 'pjname', $person_job_function, -1, [], '');
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

}
