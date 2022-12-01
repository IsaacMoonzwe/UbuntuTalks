<?php

class SponsorshipCategoriesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTestimonial();
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $srch = SponsorshipCategories::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['t.*', 't_l.sponsorshipcategories_title', 't_l.sponsorshipcategories_text']);
        $srch->addOrder('sponsorshipcategories_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = SponsorshipCategories::getAttributesById($testimonialId, [
                'sponsorshipcategories_id',
                'sponsorshipcategories_identifier',
                'sponsorshipcategories_active',
                'sponsorshipcategories_name',
                'sponsorshipcategories_plan_price',
                'sponsorshipcategories_tickets',
                'sponsorshipcategories_on_screen_advertisement',
                'sponsorshipcategories_program_advertisement_full_page',
                'sponsorshipcategories_program_advertisement_half_page',
                'sponsorshipcategories_program_advertisement_quarter_page',
                'sponsorshipcategories_attendee_list',
                'sponsorshipcategories_black_tie_speaker',
                'sponsorshipcategories_cocktail_speaker',
                'sponsorshipcategories_food_sponsor',
                'sponsorshipcategories_tea_break',
                'sponsorshipcategories_lunch',
                'sponsorshipcategories_cocktail_reception',
                'sponsorshipcategories_cultural_night',
                'sponsorshipcategories_black_tie_awards_gala',
                'sponsorshipcategories_exhitbit_booth',
                'sponsorshipcategories_additional_passes',
                'sponsorshipcategories_logo_link_blurb',
                'sponsorshipcategories_logo_footer',
                'sponsorshipcategories_banner',
                'sponsorshipcategories_logo_sponsor_signage',
                'sponsorshipcategories_attendee_email',
                'sponsorshipcategories_program_guide',
                'sponsorshipcategories_colors'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('sponsorshipcategories_id', $testimonialId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditTestimonial();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = $post['sponsorshipcategories_id'];
        unset($post['sponsorshipcategories_id']);
        if ($testimonialId == 0) {
            $post['sponsorshipcategories_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new SponsorshipCategories($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
            }
        } else {
            $testimonialId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($testimonialId = 0, $lang_id = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $lang_id = FatUtility::int($lang_id);
        if ($testimonialId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($testimonialId, $lang_id);
        $langData = SponsorshipCategories::getAttributesByLangId($lang_id, $testimonialId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('testimonialId', $testimonialId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        $testimonialId = $post['sponsorshipcategories_id'];
        $lang_id = $post['lang_id'];
        if ($testimonialId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($testimonialId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage($frm->getValidationErrors());
            FatUtility::dieWithError(Message::getHtml());
        }
        unset($post['sponsorshipcategories_id']);
        unset($post['lang_id']);
        $data = [
            'testimoniallang_lang_id' => $lang_id,
            'testimoniallang_sponsorshipcategories_id' => $testimonialId,
            'sponsorshipcategories_title' => $post['sponsorshipcategories_title'],
            'sponsorshipcategories_text' => $post['sponsorshipcategories_text']
        ];
        $obj = new SponsorshipCategories($testimonialId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = SponsorshipCategories::getAttributesByLangId($langId, $testimonialId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = SponsorshipCategories::getAttributesById($testimonialId, ['sponsorshipcategories_id', 'sponsorshipcategories_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new SponsorshipCategories($testimonialId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditTestimonial();
        $sponsorshipcategories_id = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        if ($sponsorshipcategories_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj = new SponsorshipCategories($sponsorshipcategories_id);
        if (!$testimonialObj->canRecordMarkDelete($sponsorshipcategories_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj->assignValues([SponsorshipCategories::tblFld('deleted') => 1]);
        if (!$testimonialObj->save()) {
            Message::addErrorMessage($testimonialObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function getForm($testimonialId = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = new Form('frmTestimonial');
        $options = ['Yes' => 'Yes', 'No' => 'No'];
        $program_guide_data = new SearchBase('tbl_sponsorship_program_guide');
        $program_guide_data->addCondition('program_guide_deleted', '=', '0');
        $program_guide_dropdown_value = FatApp::getDb()->fetchAll($program_guide_data->getResultSet());
        $program_guide = array();
        foreach ($program_guide_dropdown_value as $key => $value) {
            $program_guide[$value['program_guide_user_name']] = $value['program_guide_user_name'];
        }
        $frm->addHiddenField('', 'sponsorshipcategories_id', $testimonialId);
        $frm->addRequiredField(Label::getLabel('LBL_Sponsorship_Categoryname', $this->adminLangId), 'sponsorshipcategories_name');
        $frm->addRequiredField(Label::getLabel('LBL_Plan_Price', $this->adminLangId), 'sponsorshipcategories_plan_price');
        $frm->addRequiredField(Label::getLabel('LBL_Conference_Passes', $this->adminLangId), 'sponsorshipcategories_tickets');
        $frm->addRadioButtons(
            Label::getLabel('LBL_20%_Discount_On_Additional_passes', $this->adminLangId),
            'sponsorshipcategories_additional_passes',
            $options,
            '',
            ['class' => 'list-inline']
        )->requirements()->setRequired();
        $frm->addRequiredField(Label::getLabel('LBL_Exhibit_Booth', $this->adminLangId), 'sponsorshipcategories_exhitbit_booth');
        $frm->addRadioButtons(
            Label::getLabel('LBL_Logo,_Link,_and_blurb_on_the_sponsor_page', $this->adminLangId),
            'sponsorshipcategories_logo_link_blurb',
            $options,
            '',
            ['class' => 'list-inline']
        )->requirements()->setRequired();
        $frm->addRadioButtons(Label::getLabel('LBL_Logo_In_Website_Footer', $this->adminLangId), 'sponsorshipcategories_logo_footer', $options, '', ['class' => 'list-inline'])->requirements()->setRequired();
        $frm->addRadioButtons(Label::getLabel('LBL_Static_Banner_Ad_To_Rotate_on_The_Conference_Website', $this->adminLangId), 'sponsorshipcategories_banner', $options, '', ['class' => 'list-inline'])->requirements()->setRequired();
        $frm->addRadioButtons(Label::getLabel('LBL_Logo_On_Onsite_Sponsor_Signage', $this->adminLangId), 'sponsorshipcategories_logo_sponsor_signage', $options, '', ['class' => 'list-inline'])->requirements()->setRequired();
        $frm->addRadioButtons(Label::getLabel('LBL_Company_Named_In_Pre-_Conf_Attendee_Email', $this->adminLangId), 'sponsorshipcategories_attendee_email', $options, '', ['class' => 'list-inline'])->requirements()->setRequired();
        $frm->addSelectBox(Label::getLabel('LBL_Full_Color_ad_In_Printed_Program_Guide', $this->adminLangId), 'sponsorshipcategories_program_guide', $program_guide, '', [], '')->requirements()->setRequired();
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'sponsorshipcategories_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($testimonialId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTestimonialLang');
        $frm->addHiddenField('', 'sponsorshipcategories_id', $testimonialId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Job_Function_Title', $this->adminLangId), 'sponsorshipcategories_title');
        $frm->addTextarea(Label::getLabel('LBL_Job_Function_Text', $this->adminLangId), 'sponsorshipcategories_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }
}
