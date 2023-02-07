<?php
class GroupClassesController extends MyAppController
{

    public function index()
    {
        $contactFrm = $this->getGroupClassesForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $this->set('frmSrch', $this->getSearchForm());
        $siteLangId = CommonHelper::getLangId();
        $groupBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_GROUP_BANNER_SECTION, $this->siteLangId);
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);
        $this->set('groupBanner', $groupBanner);
        $this->_template->addJs('js/jquery.datetimepicker.js');
        $this->_template->addJs('js/jquery.form.js');
        $this->_template->addJs('js/jquery.inputmask.bundle.js');
        $this->_template->addJs('js/cropper.js');
        $this->_template->addCss('css/event.css');
        $this->_template->addJs('js/intlTelInput.js');
        $this->_template->addCss('css/intlTelInput.css');
        $this->set('languages', TeachingLanguage::getAllLangsWithUserCount($this->siteLangId));
        $this->_template->render();
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

    public function groupClassesForm()
    {
        $this->set('frmOnlineContact', $this->getGroupClassesForm());
        $this->_template->render(false, false);
    }

    private function getGroupClassesForm()
    {
        $groupLanguages = array();
        $languages = new SearchBase('tbl_spoken_languages');
        $allLanguages = FatApp::getDb()->fetchAll($languages->getResultSet());
        foreach ($allLanguages as $key => $value) {
            $groupLanguages[$value['slanguage_identifier']] = $value['slanguage_identifier'];
        }
        $frm = new Form('frmOnlineContact');
        $frm->addRequiredField(Label::getLabel('LBL_First_Name', $langId), 'first_name', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name', $langId), 'last_name', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email_address', '');
        $fld_phn = $frm->addRequiredField(Label::getLabel('LBL_Your_Phone', $langId), 'phone_number');
        $fld_phn->requirements()->setRegularExpressionToValidate('^[\s()+-]*([0-9][\s()+-]*){5,20}$');
        $fld_phn->requirements()->setCustomErrorMessage(Label::getLabel('VLD_ADD_VALID_PHONE_NUMBER', $langId));
        $frm->addRequiredField(Label::getLabel('LBL_Organisation_Name', $langId), 'organisation_name', '');
        $frm->addRequiredField(Label::getLabel('LBL_Organisation_Url', $langId), 'organisation_url', '');
        $countryObj = new Country();
        $countriesArr = $countryObj->getCountriesArr($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Country'), 'user_country_id[]', $countriesArr, FatApp::getConfig('CONF_COUNTRY', FatUtility::VAR_INT, 0), array(), Label::getLabel('LBL_Select'));
        // $fld->requirement->setRequired(true);
        // $fld->requirement->setMultiple(true);
        $timezonesArr = MyDate::timeZoneListing();
        $fld2 = $frm->addSelectBox(Label::getLabel('LBL_TimeZone'), 'user_timezone[]', $timezonesArr, FatApp::getConfig('CONF_COUNTRY', FatUtility::VAR_INT, 0), array(), Label::getLabel('LBL_Select'));
        // $fld2->requirement->setRequired(true);

        $start_time_fld = $frm->addTextBox(Label::getLabel('LBl_Start_Time'), 'grpcls_start_datetime', '', ['id' => 'grpcls_start_datetime', 'autocomplete' => 'off']);
        $end_time_fld = $frm->addTextBox(Label::getLabel('LBl_End_Time'), 'grpcls_end_datetime', '', ['id' => 'grpcls_end_datetime', 'autocomplete' => 'off']);
        $frm->addRequiredField(Label::getLabel('LBL_Objective', $langId), 'objective_lesson', '');
        $frm->addRequiredField(Label::getLabel('LBL_Group_Size', $langId), 'group_size', '');
        $group_type = ['Private' => 'Private', 'Corporate' => 'Corporate', 'Faith-Based' => 'Faith-Based', 'Education' => 'Education'];
        $frm->addSelectBox(Label::getLabel('LBL_Group_Type', $langId), 'group_type', $group_type, -1, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Language', $langId), 'Language', $groupLanguages, -1, [], '');
        $frm->addRequiredField(Label::getLabel('LBL_Others', $langId), 'others', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_SUBMIT', $langId));
        return $frm;
    }

    public function contactSubmit()
    {
        $frm = $this->getGroupClassesForm($this->siteLangId);
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
            $country = FatApp::getPostedData('country', FatUtility::VAR_STRING, '');
            $post['country'] = $country;
            $time = FatApp::getPostedData('timeZone', FatUtility::VAR_STRING, '');
            $post['user_timezone'] = '('.$time;
         
            if (!$email->GroupsendContactFormEmail($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        $this->set('redirectUrl', CommonHelper::generateUrl('GroupClasses'));
        $this->set('msg', Label::getLabel('MSG_Redirecting', $this->siteLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function setup()
    {
        $grpcls_id = FatApp::getPostedData('myArrfay', FatUtility::VAR_INT, 0);
        $srch = new SearchBase(TeacherGroupClasses::DB_TBL, 'grpcls');
        $user_timezone = MyDate::getUserTimeZone();
        $systemTimeZone = MyDate::getTimeZone();
        $srch2 = new SearchBase('tbl_scheduled_lesson_details');
        $srch2->joinTable('tbl_scheduled_lessons', 'INNER JOIN', 'slesson_id=sldetail_slesson_id');
        $srch2->addDirectCondition('slesson_grpcls_id=grpcls_id');
        $cnd = $srch2->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_NEED_SCHEDULING);
        $cnd->attachCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $srch2->doNotCalculateRecords();
        $srch2->doNotLimitRecords();
        $srch2->addFld('COUNT(DISTINCT sldetail_learner_id)');
        $srch3 = new SearchBase('tbl_scheduled_lessons');
        $srch3->addDirectCondition('slesson_grpcls_id=grpcls_id');
        $srch3->doNotCalculateRecords();
        $srch3->doNotLimitRecords();
        $srch3->addFld('slesson_teacher_join_time > 0');
        $srch = new SearchBase(TeacherGroupClasses::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_id', '=', $grpcls_id);
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $flag = false;
        foreach ($classes as $class) {
            $post = $class;
            $weeks_list  = $class['grpcls_weeks'];
            $check = explode(',', $weeks_list);
            if ($weeks_list !== '' && $weeks_list) {
                $dbTtime = explode(' ', $class['grpcls_start_datetime']);
                $endTime = explode(' ', $class['grpcls_end_datetime']);
                $weekNames = explode(',', $weeks_list);
                if (empty($weekNames)) {
                    array_push($weekNames, $class['grpcls_weeks']);
                }
                foreach ($weekNames as $w) {
                    if ($w != '') {
                        $next_date = date('Y-m-d', strtotime('next ' . $w, strtotime($class['grpcls_start_datetime'])));
                        $nT = $next_date . ' ' . $dbTtime[1];
                        $classEndTime = $next_date . ' ' . $endTime[1];
                        $newWeekEndTime = date($classEndTime);
                        $newStartTime = date($nT);
                        $newWeek = $w;
                        $newWeek .= ",";
                        $post['grpcls_weeks'] = $newWeek;
                        $post['grpcls_start_datetime'] = $newStartTime;
                        $post['grpcls_end_datetime'] = $newWeekEndTime;
                        $post['grpcls_slug'] = isset($class['grpcls_slug']) ? $class['grpcls_slug'] : str_replace(" ", '-', strtolower($class['grpcls_title']));
                        $tGrpClsSrchObj = new TeacherGroupClassesSearch();
                        unset($post['grpcls_id']);
                        unset($post['grpcls_slug']);
                        $tGrpClsObj = new TeacherGroupClasses();
                        $tGrpClsObj->assignValues($post);
                        $db = FatApp::getDb();
                        $db->startTransaction();
                        if (true !== $tGrpClsObj->save()) {
                            $db->rollbackTransaction();
                            FatUtility::dieJsonError($tGrpClsObj->getError());
                        }
                        $seoUrl = CommonHelper::seoUrl($post['grpcls_title'] . '-' . $tGrpClsObj->getMainTableRecordId());
                        if (!$db->updateFromArray(
                            TeacherGroupClasses::DB_TBL,
                            ['grpcls_slug' => $seoUrl],
                            ['smt' => 'grpcls_id = ?', 'vals' => [$tGrpClsObj->getMainTableRecordId()]]
                        )) {
                            $this->error = $db->getError();
                            $db->rollbackTransaction();
                            return false;
                        }
                        $db->commitTransaction();
                        $msg = Label::getLabel('LBL_Group_Class_Saved_Successfully!');
                    }
                }
            }
        }
        $srch = new SearchBase(TeacherGroupClasses::DB_TBL, 'grpcls');
        $srch->addCondition('grpcls.grpcls_id', '!=', $grpcls_id);
        $srch->addCondition('grpcls.grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
        $classes = FatApp::getDb()->fetchAll($srch->getResultSet());
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('totalRecords', $totalRecords);
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('classes', $classes);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('referer', CommonHelper::redirectUserReferer(true));
        $this->set('classStatusArr', TeacherGroupClasses::getStatusArr());
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->set('msg', $msg);
        $this->set('grpcls_id', $grpcls_id);
        $this->set('lang_id', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post === false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        if ($page < 2) {
            $page = 1;
        }
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = TeacherGroupClassesSearch::getSearchObj($this->siteLangId);
        if (isset($post['language']) && $post['language'] !== "") {
            $srch->addCondition('grpcls_tlanguage_id', '=', $post['language']);
        }
        $groupBanner = ExtraPage::getBlockContent(ExtraPage::BLOCK_GROUP_BANNER_SECTION, $this->siteLangId);
        $this->set('groupBanner', $groupBanner);
        $srch->addCondition('grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $rs = $srch->getResultSet();
        $classesList = FatApp::getDb()->fetchAll($rs);
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount(),
        ];
        $this->set('classes', $classesList);
        $min_booking_time = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $this->set('min_booking_time', $min_booking_time);
        $post['page'] = $page;
        $this->set('postedData', $post);
        $this->set('frm', $frm);
        $this->set('pagingArr', $pagingArr);
        $this->_template->render(false, false);
    }

    public function view($grpcls_slug)
    {
        $grpcls_slug  = CommonHelper::htmlEntitiesDecode($grpcls_slug);
        $srch = TeacherGroupClassesSearch::getSearchObj($this->siteLangId);
        $srch->joinTable(Country::DB_TBL, 'LEFT JOIN', 'ut.user_country_id = country.country_id', 'country');
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT JOIN', 'country.country_id = countryLang.countrylang_country_id and countryLang.countrylang_lang_id = ' . $this->siteLangId, 'countryLang');
        $srch->joinTable('tbl_teacher_stats', 'LEFT JOIN', 'testat.testat_user_id = ut.user_id', 'testat');
        $srch->addMultipleFields(['IFNULL(country_name, country_code) as country_name', 'testat_reviewes', 'testat_ratings']);
        $srch->addCondition('grpcls_slug', '=', $grpcls_slug);
        $srch->setPageSize(1);
        $classData = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($classData)) {
            FatUtility::exitWithErrorCode(404);
        }
        $this->set('class', $classData);
        $min_booking_time = FatApp::getConfig('CONF_CLASS_BOOKING_GAP', FatUtility::VAR_INT, 60);
        $this->set('min_booking_time', $min_booking_time);
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmTeacherSrch');
        $frm->addSelectBox('', 'language', TeacherGroupClassesSearch::getTeachLangs($this->siteLangId), '', array(), Label::getLabel('LBL_All_Language'));
        $frm->addTextBox('', 'keyword', '', array('placeholder' => Label::getLabel('LBL_Search_Class')));
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $frm->addSubmitButton('', 'btnSrchSubmit', '');
        return $frm;
    }
}
