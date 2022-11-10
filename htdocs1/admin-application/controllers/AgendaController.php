<?php

class AgendaController extends AdminBaseController
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

    public function event_days()
    {
        $select_design = $_REQUEST['product_code'];
        $job_days_function_data = new SearchBase('tbl_three_reasons');
        $job_days_function_data->addCondition('three_reasons_deleted', '=', '0');
        $job_days_function_data->addCondition('three_reasons_active', '=', '1');
        $job_days_function_data->addCondition('three_reasons_id', '=', $select_design);
        $job_days_function_dropdown_value = FatApp::getDb()->fetchAll($job_days_function_data->getResultSet());
        $timezone_list = array();
        foreach ($job_days_function_dropdown_value as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));
            $pretty_offset = "timezone ${offset_prefix}${offset_formatted}";
            $timezone_list[$offset['three_reasons_id']] = $offset['registration_starting_days'];
        }

        echo json_encode($timezone_list);
        return $timezone_list;
    }


    private function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
        $statusArr = ['-1' => Label::getLabel('LBL_All', $this->adminLangId)] + TeacherRequest::getStatusArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'status', $statusArr, '', [], '');
        $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'field--calender']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }
    public function search()
    {
        $srchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $srchForm->getFormDataFromArray($data);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $srch = Agenda::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields(['t.*', 't_l.agenda_title', 't_l.agenda_text']);
        $srch->addOrder('agenda_active', 'desc');
        $srch->addOrder('agenda_start_time', 'asc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        foreach ($records as $key => $value) {
            $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $value['agenda_id'], 0, -1);
            $value['speaker_image'] = $testimonialImages;
            $event_srch = new SearchBase('tbl_three_reasons', 't_e');
            $event_srch->addCondition('t_e.three_reasons_id', '=', $value['event_id']);
            $eventrecords = FatApp::getDb()->fetch($event_srch->getResultSet());
            $value['registration_plan_title'] = $eventrecords['registration_plan_title'];
            $value['registration_starting_days'] = explode(',',$eventrecords['registration_starting_days'])[0];
            $records[$key] = $value;
        }
        $canEdit = $this->objPrivilege->canEditTestimonial($this->admin_id, true);
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $pagingArr = [
            'page' => $page,
            'pageCount' => $srch->pages(),
            'recordCount' => $srch->recordCount(),
            'adminLangId' => $this->adminLangId
        ];
        $this->set('pagingArr', $pagingArr);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    private function getAgendaForm($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = new Form('frmAgendaTestimonials');
        $agendafrm->addHiddenField(Label::getLabel('LBl_Id'), 'agenda_id', $testimonialId);
        $agendafrm->addHiddenField('', 'event_id', $testimonialId);
        $job_function_data = new SearchBase('tbl_three_reasons');
        $job_function_data->addCondition('three_reasons_deleted', '=', '0');
        $job_function_data->addCondition('three_reasons_active', '=', '1');
        $job_function_dropdown_value = FatApp::getDb()->fetchAll($job_function_data->getResultSet());
        $person_job_function = array();
        $Event_Starting_Data_function = array();
        foreach ($job_function_dropdown_value as $key => $value) {
            $person_job_function[$value['three_reasons_id']] = $value['registration_plan_title'];
            $Event_Starting_Data_function[$value['three_reasons_id']] = $value['registration_starting_days'];
        }
        $agendafrm->addSelectBox(Label::getLabel('LBL_Event_Listing', $langId), 'event_listing', $person_job_function, -1, [], '');
        $agendafrm->addSelectBox(Label::getLabel('LBL_Event_Starting_Days', $langId), 'event_starting_days', $Event_Starting_Data_function, -1, [], '');
        $agendafrm->addRequiredField(Label::getLabel('LBl_Agenda_Start_Time'), 'agenda_start_time', '', ['id' => 'agenda_start_time', 'autocomplete' => 'off']);
        $agendafrm->addRequiredField(Label::getLabel('LBl_Agenda_End_Time'), 'agenda_end_time', '', ['id' => 'agenda_end_time', 'autocomplete' => 'off']);
        $agendafrm->addRequiredField(Label::getLabel('LBL_Agenda_Schedule', $this->adminLangId), 'agenda_schedule');
        $agendafrm->addRequiredField(Label::getLabel('LBL_Event_Location', $this->adminLangId), 'agenda_event_location');
        $agendafrm->addRequiredField(Label::getLabel('LBL_Information', $this->adminLangId), 'agenda_information');
        $agendafrm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $agendafrm;
    }
    public function agendaForm($testimonialId)
    {

        $testimonialId = FatUtility::int($testimonialId);
        $agendafrm = $this->getAgendaForm($testimonialId);

        if (0 < $testimonialId) {
            $data = Agenda::getAttributesById($testimonialId, [
                'agenda_id',
                'event_id as event_listing',
                'agenda_start_time',
                'agenda_end_time',
                'agenda_schedule',
                'agenda_event_location',
                'agenda_information'
            ]);
            if ($data === false) {

                FatUtility::dieWithError($this->str_invalid_request);
            }
            $agendafrm->fill($data);
            $this->set('records', $data);
        }

        $this->set('languages', Language::getAllNames());
        $this->set('agenda_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);

        $this->set('agendafrm', $agendafrm);
        $this->_template->render(false, false);
    }
    public function form($testimonialId)
    {
        $testimonialId = FatUtility::int($testimonialId);
        $frm = $this->getForm($testimonialId);
        if (0 < $testimonialId) {
            $data = Agenda::getAttributesById($testimonialId, [
                'event_id',
                'event_name',
                'event_description',
                'event_ticket_url',
                'event_start_time',
                'event_end_time',
                'agenda_start_time',
                'agenda_end_time',
                'agenda_schedule',
                'agenda_event_location',
                'agenda_information'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('agenda_id', $testimonialId);
        $this->set('testimonial_id', $testimonialId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }
    public function agendasetup()
    {
        $this->objPrivilege->canEditTestimonial();
        // $post =FatApp::getPostedData();
        // echo "<pre>";
        // print_r($post);

        $post = FatApp::getPostedData();
        // $post['agenda_start_time']=$post['start_time'];
        // $post['agenda_end_time']=$post['end_time'];

        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post['event_id'] = $post['event_listing'];
        $testimonialId = $post['agenda_id'];
        unset($post['agenda_id']);
        if ($testimonialId == 0) {
            $post['agenda_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new Agenda($testimonialId);

        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                // if (!$row = Agenda::getAttributesByLangId($langId, $testimonialId)) {
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
    public function setup()
    {
        $this->objPrivilege->canEditTestimonial();
        $frm = $this->getForm();

        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialId = $post['agenda_id'];
        unset($post['agenda_id']);
        if ($testimonialId == 0) {
            $post['agenda_added_on'] = date('Y-m-d H:i:s');
        }
        $record = new Agenda($testimonialId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        // $agendaFrm=$this->getAgendaForm();
        // $newPost=$agendaFrm->getFormDataFromArray(FatApp::getPostedData());
        // $agendaTestimonialId = $newPost['agenda_id'];
        // unset($newPost['agenda_id']);
        // if ($agendaTestimonialId == 0) {
        //     $newPost['agenda_added_on'] = date('Y-m-d H:i:s');
        // }
        // $agendaRecord = new Agenda($agendaTestimonialId);
        // $agendaRecord->assignValues($newPost);
        // if (!$agendaRecord->save()) {
        //     Message::addErrorMessage($agendaRecord->getError());
        //     FatUtility::dieJsonError(Message::getHtml());
        // }
        $newTabLangId = 0;
        if ($testimonialId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                // if (!$row = Agenda::getAttributesByLangId($langId, $testimonialId)) {
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
        $langData = Agenda::getAttributesByLangId($lang_id, $testimonialId);
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
        $testimonialId = $post['agenda_id'];
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
        unset($post['agenda_id']);
        unset($post['lang_id']);
        $data = [
            'testimoniallang_lang_id' => $lang_id,
            'testimoniallang_agenda_id' => $testimonialId,
            'agenda_title' => $post['agenda_title'],
            'agenda_text' => $post['agenda_text']
        ];
        $obj = new Agenda($testimonialId);
        if (!$obj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Agenda::getAttributesByLangId($langId, $testimonialId)) {
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


    public function changeStatus()
    {
        $this->objPrivilege->canEditTestimonial();
        $testimonialId = FatApp::getPostedData('testimonialId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $testimonialId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Agenda::getAttributesById($testimonialId, ['agenda_id', 'agenda_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new Agenda($testimonialId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
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
        $testimonialObj = new Agenda($testimonial_id);
        if (!$testimonialObj->canRecordMarkDelete($testimonial_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $testimonialObj->assignValues([Agenda::tblFld('deleted') => 1]);
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
        $testimonialImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $testimonialId, 0, -1);
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
            'data-file_type' => AttachedFile::FILETYPE_TESTIMONIAL_IMAGE,
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
        $fileHandlerObj->deleteFile($fileHandlerObj::FILETYPE_TESTIMONIAL_IMAGE, $testimonialId, 0, 0, $lang_id);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $fileHandlerObj::FILETYPE_TESTIMONIAL_IMAGE, $testimonialId, 0, $_FILES['file']['name'], -1, $unique_record = false, $lang_id)) {
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
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $testimonialId, 0, 0, $lang_id)) {
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
        $frm->addHiddenField('', 'agenda_id', $testimonialId);
        $frm->addHtml('', 'testimonial_image_display_div', '');
        $frm->addRequiredField(Label::getLabel('LBL_Event_Id', $this->adminLangId), 'event_id');
        $fld = $frm->getField('event_id');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        $frm->addRequiredField(Label::getLabel('LBL_Event_Name', $this->adminLangId), 'event_name');
        $fld = $frm->getField('event_name');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        $frm->addTextarea(Label::getLabel('LBL_Description', $this->adminLangId), 'event_description')->requirements()->setRequired();
        $fld = $frm->getField('event_description');
        $fld->setFieldTagAttribute('disabled', 'disabled');

        $frm->addTextarea(Label::getLabel('LBL_Information', $this->adminLangId), 'agenda_information')->requirements()->setRequired();
        $fld = $frm->getField('agenda_information');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        


        $frm->addRequiredField(Label::getLabel('LBL_Register_URL', $this->adminLangId), 'event_ticket_url');
        $fld = $frm->getField('event_ticket_url');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        $frm->addRequiredField(Label::getLabel('LBL_Event_Start_Time', $this->adminLangId), 'event_start_time');
        $fld = $frm->getField('event_start_time');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        $frm->addRequiredField(Label::getLabel('LBL_Event_End_Time', $this->adminLangId), 'event_end_time');
        $fld = $frm->getField('event_end_time');
        $fld->setFieldTagAttribute('disabled', 'disabled');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'agenda_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($testimonialId = 0, $lang_id = 0)
    {
        $frm = new Form('frmTestimonialLang');
        $frm->addHiddenField('', 'agenda_id', $testimonialId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Testimonial_Title', $this->adminLangId), 'agenda_title');
        $frm->addTextarea(Label::getLabel('LBL_Testimonial_Text', $this->adminLangId), 'agenda_text');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function isMediaUploaded($testimonialId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, $testimonialId, 0)) {
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
            if (!false == $res && $res['afile_type'] == AttachedFile::FILETYPE_TESTIMONIAL_IMAGE) {
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
