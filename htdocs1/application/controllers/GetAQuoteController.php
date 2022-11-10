<?php

class GetAQuoteController extends MyAppController
{

    public function index()
    {
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $quoteBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_QUOTE_BANNER, $this->siteLangId);
        $this->set('quoteBanner', $quoteBanner);
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
            FatApp::redirectUser(CommonHelper::generateUrl('get-a-quote'));
        }
        if (!CommonHelper::verifyCaptcha()) {
            Message::addErrorMessage(Label::getLabel('MSG_That_captcha_was_incorrect', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::generateUrl('get-a-quote'));
        }
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->sendQuoteContactFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('get-a-quote'));
    }

    private function contactUsForm(int $langId)
    {
        $job_function_data=new SearchBase('tbl_fieldservices');
        $job_function_data->addCondition('testimonial_deleted', '=', '0');
        $job_function_dropdown_value = FatApp::getDb()->fetchAll($job_function_data->getResultSet());
        $arr_options=array();
        foreach ($job_function_dropdown_value as $key => $value) {
            $arr_options[$value['testimonial_user_name']] = $value['testimonial_user_name'];
        }

        // Get all conuntry
        $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.first.org/data/v1/countries',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $listing = json_decode($response);
        $country_options=array();
        foreach($listing as $country_option){
            foreach ($country_option as $key => $value) {
                $country_options[$value->country]=$value->country;
            }
        }

        
        // Get all Cities
        $curl_state = curl_init();
        curl_setopt_array($curl_state, array(
            CURLOPT_URL => 'https://countriesnow.space/api/v0.1/countries/population/cities',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $state_response = curl_exec($curl_state);
        curl_close($curl_state);
        $state_listing = json_decode($state_response);
        $citys = array();
        foreach($state_listing as $city) {
            foreach($city as $key => $value) {
                $citys[$value->city]=$value->city;
            }
        }


        $curl_new = curl_init();
        curl_setopt_array($curl_new, array(
        CURLOPT_URL => "https://countriesnow.space/api/v0.1/countries/states",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json"
        ),
        ));

        $response_new = curl_exec($curl_new);
        $err = curl_error($curl_new);
        curl_close($curl_new);
        $states = array();
        $state_new = json_decode($response_new);
        foreach ($state_new as $state) {
            foreach ($state as $key => $value) {
                foreach($value->states as $key=>$state_data){
                    $states[$state_data->name]=$state_data->name;
                }
            } 
        }

        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_First_Name', $langId), 'name', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name', $langId), 'lname', '');
        $frm->addRequiredField(Label::getLabel('LBL_Address', $langId), 'address', '');
        $frm->addRequiredField(Label::getLabel('LBL_Street_Address', $langId), 'streetaddress', '');
        $frm->addSelectBox(Label::getLabel('LBL_City', $langId), 'city', $citys, -1, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_State_/_Province_/_Region', $langId), 'state', $states, -1, [], '');
        $frm->addRequiredField(Label::getLabel('LBL_ZIP_Code_/_Postal_Code', $langId), 'zipcode', '');
        $frm->addSelectBox(Label::getLabel('LBL_Country', $langId), 'country', $country_options, -1, [], '');
        $frm->addRequiredField(Label::getLabel('LBL_Source_Language', $langId), 'sourcelanguage', '');
        $frm->addRequiredField(Label::getLabel('LBL_Target_Language', $langId), 'targetlanguage', '');
        $frm->addDateField(Label::getLabel('LBL_Needed_Date', $this->adminLangId), 'neededdate', '', ['readonly' => 'readonly']);
        $frm->addFileUpload(Label::getLabel('LBL_File_Upload'), 'fileupload');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $frm->addSelectBox(Label::getLabel('LBL_Active_Users', $langId), 'services', $arr_options, -1, [], '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Your_Phone', $langId), 'phone');
        $fld_phn->requirements()->setRegularExpressionToValidate('^(\+\d{1,2}\s?)?1?\-?\.?\s?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$');
        $fld_phn->requirements()->setCustomErrorMessage(Label::getLabel('VLD_ADD_VALID_PHONE_NUMBER', $langId));
        $frm->addTextArea(Label::getLabel('LBL_Your_Message', $langId), 'message')->requirements()->setRequired();
        $frm->addHtml('', 'htmlNote', '<div class="g-recaptcha" data-sitekey="' . FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') . '"></div>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

}
