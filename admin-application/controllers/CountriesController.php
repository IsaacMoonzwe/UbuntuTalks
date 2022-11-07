<?php

class CountriesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCountries();
    }

    public function index()
    {
        $search = $this->getSearchForm();
        $this->set("canEdit", $this->objPrivilege->canEditCountries(AdminAuthentication::getLoggedAdminId(), true));
        $this->set("search", $search);
        $this->_template->addJs('js/import-export.js');
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = Country::getSearchObject(false, $this->adminLangId);
        $srch->addFld('c.* , c_l.country_name');
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('c.country_code', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('c_l.country_name', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditCountries($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form($countryId)
    {
        $this->objPrivilege->canEditCountries();
        $countryId = FatUtility::int($countryId);
        $frm = $this->getForm($countryId);
        if (0 < $countryId) {
            $data = Country::getAttributesById($countryId, [
                        'country_id',
                        'country_code',
                        'country_active',
                        'country_currency_id',
                        'country_language_id'
            ]);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('country_id', $countryId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditCountries();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $countryId = $post['country_id'];
        unset($post['country_id']);
        $record = new Country($countryId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($countryId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Country::getAttributesByLangId($langId, $countryId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $countryId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', Label::getLabel('LBL_Updated_Successfully', $this->adminLangId));
        $this->set('countryId', $countryId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($countryId = 0, $lang_id = 0)
    {
        $countryId = FatUtility::int($countryId);
        $lang_id = FatUtility::int($lang_id);
        if ($countryId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($countryId, $lang_id);
        $langData = Country::getAttributesByLangId($lang_id, $countryId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('countryId', $countryId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditCountries();
        $post = FatApp::getPostedData();
        $countryId = $post['country_id'];
        $lang_id = $post['lang_id'];
        if ($countryId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($countryId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['country_id']);
        unset($post['lang_id']);
        $data = [
            'countrylang_lang_id' => $lang_id,
            'countrylang_country_id' => $countryId,
            'country_name' => $post['country_name']
        ];
        $countryObj = new Country($countryId);
        if (!$countryObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($countryObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Country::getAttributesByLangId($langId, $countryId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('countryId', $countryId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getForm($countryId = 0)
    {
        $countryId = FatUtility::int($countryId);
        $frm = new Form('frmCountry');
        $frm->addHiddenField('', 'country_id', $countryId);
        $frm->addRequiredField(Label::getLabel('LBL_Country_code', $this->adminLangId), 'country_code');
        $currencyArr = Currency::getCurrencyNameWithCode($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Currency', $this->adminLangId), 'country_currency_id', $currencyArr);
        $languageArr = Language::getAllNames();
        $frm->addSelectBox(Label::getLabel('LBL_Language', $this->adminLangId), 'country_language_id', [0 => 'Site Default'] + $languageArr, '', [], '');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'country_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($countryId = 0, $lang_id = 0)
    {
        $frm = new Form('frmCountryLang');
        $frm->addHiddenField('', 'country_id', $countryId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Country_Name', $this->adminLangId), 'country_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditCountries();
        $countryId = FatApp::getPostedData('countryId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $countryId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Country::getAttributesById($countryId, ['country_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $countryObj = new Country($countryId);
        if (!$countryObj->changeStatus($status)) {
            Message::addErrorMessage($countryObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function media($country_id = 0)
    {
        $this->objPrivilege->canEditCountries();
        $country_id = FatUtility::int($country_id);
        $countryMediaFrm = $this->getMediaForm($country_id);
        $this->set('languages', Language::getAllNames());
        $this->set('country_id', $country_id);
        $this->set('countryMediaFrm', $countryMediaFrm);
        $this->_template->render(false, false);
    }

    public function images($country_id)
    {
        $country_id = FatUtility::int($country_id);
        $countryImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_COUNTRY_FLAG, $country_id, 0, 0, false);
        $this->set('images', $countryImages);
        $this->set("canEdit", $this->objPrivilege->canEditCountries(AdminAuthentication::getLoggedAdminId(), true));
        $this->_template->render(false, false);
    }

    public function setUpFlagImage()
    {
        $this->objPrivilege->canEditCountries();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
        }
        $country_id = FatApp::getPostedData('country_id', FatUtility::VAR_INT, 0);
        if (!$country_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError($this->str_invalid_request_id);
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieWithError(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
        }
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile($fileHandlerObj::FILETYPE_COUNTRY_FLAG, $country_id, 0, 0, 0);
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $fileHandlerObj::FILETYPE_COUNTRY_FLAG, $country_id, 0, $_FILES['file']['name'], -1, $unique_record = true, 0)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieWithError($fileHandlerObj->getError());
        }
        $this->set('countryId', $country_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteFlagImage($country_id = 0)
    {
        $country_id = FatUtility::int($country_id);
        if (!$country_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_COUNTRY_FLAG, $country_id, 0, 0, 0)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getMediaForm($country_id)
    {
        $frm = new Form('frmCountryMedia');
        $frm->addHTML('', 'country_flag_heading', '');
        $frm->addHiddenField('', 'country_id', $country_id);
        $frm->addButton(Label::getLabel('Lbl_Flag', $this->adminLangId), 'flag', Label::getLabel('LBL_Upload_Flag', $this->adminLangId), [
            'class' => 'uploadFile-Js', 'id' => 'flag', 'data-file_type' => AttachedFile::FILETYPE_COUNTRY_FLAG, 'data-country_id' => $country_id
        ]);
        $frm->addHtml('', 'country_flag_display_div', '');
        return $frm;
    }

}
