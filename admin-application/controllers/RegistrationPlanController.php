<?php
class RegistrationPlanController extends AdminBaseController
{
    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewTestimonial();
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
    }
    public function index()
    {
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addCss('css/jquery.datetimepicker.css');
        $this->_template->render();
    }
    public function search()
    {
        $srch = ThreeReasons::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['t.*', 't_l.three_reasons_title', 't_l.three_reasons_text']);
        $srch->addOrder('three_reasons_active', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        foreach ($records as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $value['three_reasons_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $records[$key] = $value;
        }
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }
    public function agendaForm($testimonialId)
    {
        $srch_event_listing = new SearchBase('tbl_agenda');
        $srch_event_listing->addCondition('agenda_deleted', '=', 0);
        $srch_event_listing->addCondition('agenda_active', '=', 0);
        $srch_event_listing->addOrder('agenda_start_time');
        $srch_event_listing->addCondition('event_id', '=', $testimonialId);
        $events_listing_categories = $srch_event_listing->getResultSet();
        $EventListingCategoriesList = FatApp::getDb()->fetchAll($events_listing_categories);
        $this->set('EventListingCategoriesList', $EventListingCategoriesList);
        $this->_template->render(false, false);
    }
    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = ThreeReasons::getAttributesById($testimonialId, [
                'three_reasons_id',
                'three_reasons_identifier',
                'registration_plan_title',
                'registration_plan_price',
                'registration_plan_zk_price',
                'registration_plan_description',
                'registration_starting_days',
                'registration_starting_date',
                'registration_ending_date',
                'registration_plan_note',
                'registration_plan_combo_events'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('three_reasons_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
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
        $newPost = FatApp::getPostedData();
        $user_food_department  = implode(',', $newPost['registration_plan_combo_events']);
        if (isset($newPost['registration_plan_combo_events']) && !empty($newPost['registration_plan_combo_events']) && $user_food_department[0] != '') {
            $user_food_department  = implode(',', $newPost['registration_plan_combo_events']);
            $post['registration_plan_combo_events'] = $user_food_department;
        } else {
            $post['registration_plan_combo_events'] = "";
        }
        $testimonialId = $post['three_reasons_id'];
        unset($post['three_reasons_id']);
        if ($testimonialId == 0) {
            $post['three_reasons_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new ThreeReasons($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                // if (!$row = ThreeReasons::getAttributesByLangId($langId, $testimonialId)) {
                //     $newTabLangId = $langId;
                //     break;
                // }
            }
        } else {
            $testimonialId = $record->getMainTableRecordId();
            // $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($testimonialId)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
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
        $langData = ThreeReasons::getAttributesByLangId($lang_id, $testimonialId);
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
        $testimonialId = $post['three_reasons_id'];
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
        unset($post['three_reasons_id']);
        unset($post['lang_id']);
        $data = [
            'testimoniallang_lang_id' => $lang_id,
            'testimoniallang_testimonial_id' => $testimonialId,
            'three_reasons_title' => $post['three_reasons_title'],
            'three_reasons_text' => $post['three_reasons_text']
        ];
        $obj = new ThreeReasons($testimonialId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = ThreeReasons::getAttributesByLangId($langId, $testimonialId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($testimonialId)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('testimonialId', $testimonialId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }
    public function updateOrder()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $pMethodObj = new ThreeReasons();
            if (!$pMethodObj->updateOrder($post['paymentMethod'])) {
                Message::addErrorMessage($pMethodObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }
    public function changeStatus()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        // $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = ThreeReasons::getAttributesById($testimonialId, ['three_reasons_id', 'three_reasons_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['three_reasons_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $obj = new ThreeReasons($testimonialId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', $this->str_update_record);
        FatUtility::dieJsonSuccess($this->str_update_record);
    }
    public function deleteRecord()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonial_id = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        if ($testimonial_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj = new ThreeReasons($testimonial_id);
        if (!$testimonialObj->canRecordMarkDelete($testimonial_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj->assignValues([ThreeReasons::tblFld('deleted') => 1]);
        if (!$testimonialObj->save()) {
            Message::addErrorMessage($testimonialObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }
    public function media($testimonialId = 0)
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatUtility::int($testimonialId);
        $testimonialMediaFrm = $this->getMediaForm($testimonialId);
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $testimonialId, 0, -1);
        $this->set('languages', Language::getAllNames());
        $this->set('testimonialId', $testimonialId);
        $this->set('testimonialMediaFrm', $testimonialMediaFrm);
        $this->set('testimonialImages', $testimonialImages);
        $this->_template->render(false, false);
    }
    public function getMediaForm($testimonialId)
    {
        $frm = new Form('frmTestimonialMedia');
        $frm->addButton(Label::getLabel('Lbl_Image', $this->adminLangId), 'testimonial_image', Label::getLabel('LBL_Upload_Image', $this->adminLangId), [
            'class' => 'uploadFile-Js',
            'id' => 'testimonial_image',
            'data-file_type' => AttachedFile::FILETYPE_EVENT_PLAN_IMAGE,
            'data-testimonial_id' => $testimonialId
        ]);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        return $frm;
    }
    public function uploadTestimonialMedia()
    {
        $this->objPrivilege->canEditTestimonial();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        $lang_id = FatApp::getPostedData('lang_id', FatUtility::VAR_INT, 0);
        if (!$testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile($fileHandlerObj::FILETYPE_EVENT_PLAN_IMAGE, $testimonialId, 0, 0, $lang_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $fileHandlerObj::FILETYPE_EVENT_PLAN_IMAGE, $testimonialId, 0, $_FILES['file']['name'], -1, $unique_record = false, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('testimonialId', $testimonialId);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    public function removeTestimonialImage($testimonialId = 0, $lang_id = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $lang_id = FatUtility::int($lang_id);
        if (!$testimonialId) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $testimonialId, 0, 0, $lang_id)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }
    private function getForm($testimonialId = 0)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = new Form('frmTestimonial');
        $frm->addHiddenField('', 'three_reasons_id', $testimonialId);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        $frm->addRequiredField(Label::getLabel('LBL_Registration_Plan_Title', $this->adminLangId), 'registration_plan_title');
        $frm->addRequiredField(Label::getLabel('LBL_Registration_Starting_Days_(Date)'), 'registration_starting_date', '', ['id' => 'registration_starting_date', 'autocomplete' => 'off']);
        $frm->addRequiredField(Label::getLabel('LBL_Registration_Ending_Days_(Date)'), 'registration_ending_date', '', ['id' => 'registration_ending_date', 'autocomplete' => 'off']);
        // $frm->addRequiredField(Label::getLabel('LBL_Registration_Starting_Days_(Title)', $this->adminLangId), 'registration_starting_days');
        // $fld =  $frm->addRequiredField(Label::getLabel('LBL_Number_Of_Days', $this->adminLangId), 'registration_number_of_days');
        //$fld->requirements()->setRequired(false);
        $frm->addRequiredField(Label::getLabel('LBL_Registration_Plan_Price_USD($)', $this->adminLangId), 'registration_plan_price');
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Registration_Plan_Price_ZMW(ZK)', $this->adminLangId), 'registration_plan_zk_price');
        $fld->requirements()->setRequired(false);
        $frm->addTextarea(Label::getLabel('LBL_Description', $this->adminLangId), 'registration_plan_description')->requirements()->setRequired();
        $frm->addTextarea(Label::getLabel('LBL_Note', $this->adminLangId), 'registration_plan_note');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $diets_data = EventUser::getFoodDepartmentArr();
        $select_plan_listing = new SearchBase('tbl_three_reasons');
        $select_plan_listing->addCondition('three_reasons_deleted', '=', 0);
        $select_plan_listing->addCondition('three_reasons_active', '=', 1);
        if ($testimonialId > 0) {
            $select_plan_listing->addCondition('three_reasons_id', '=', $testimonialId);
        }
        $select_events_listings = $select_plan_listing->getResultSet();
        $SelectEventListingsList = FatApp::getDb()->fetch($select_events_listings);
        $srch_plan_listing = new SearchBase('tbl_three_reasons');
        $srch_plan_listing->addCondition('three_reasons_deleted', '=', 0);
        $srch_plan_listing->addCondition('three_reasons_active', '=', 1);
        if ($testimonialId > 0) {
            $srch_plan_listing->addCondition('three_reasons_id', '!=', $testimonialId);
        }
        $events_listings = $srch_plan_listing->getResultSet();
        $EventListingsList = FatApp::getDb()->fetchAll($events_listings);
        foreach ($EventListingsList as $key => $value) {
            $week =  $value['registration_plan_title'];
            $id =  $value['three_reasons_id'];
            // $speekLangField = $frm->addCheckBox($week, 'registration_plan_combo_events[' . $key . ']', $id, ['class' => 'diet-boxes'], false, 0);
            $speekLangField = $frm->addCheckBox($week, 'registration_plan_combo_events[]', $id, ['class' => 'diet-boxes'], false, 0);
            if ($testimonialId > 0) {
                $select = explode(",", $SelectEventListingsList['registration_plan_combo_events']);
                if (in_array($id, $select)) {
                    $speekLangField->checked = true;
                }
                $speekLangField->value = $id;
            }
            $speekLangField->value = $id;
        }
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'three_reasons_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }
    private function getLangForm($testimonialId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTestimonialLang');
        $frm->addHiddenField('', 'three_reasons_id', $testimonialId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Testimonial_Title', $this->adminLangId), 'three_reasons_title');
        $frm->addTextarea(Label::getLabel('LBL_Testimonial_Text', $this->adminLangId), 'three_reasons_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }
    private function isMediaUploaded($testimonialId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_EVENT_PLAN_IMAGE, $testimonialId, 0)) {
            return true;
        }
        return false;
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
            default:
                $h = 260;
                $w = 260;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
        }
    }
}
