<?php

class EmailTemplatesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewEmailTemplates();
        $this->set("includeEditor", true);
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm();
        $adminId = AdminAuthentication::getLoggedAdminId();
        $this->canEdit = $this->objPrivilege->canEditEmailTemplates($adminId, true);
        $this->set("canEdit", $this->canEdit);
        $this->set("frmSearch", $frmSearch);
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmEtplsSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
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
        $srch = EmailTemplates::getSearchObject($this->adminLangId);
        $srch->addOrder(EmailTemplates::DB_TBL_PREFIX . 'lang_id', 'ASC');
        $srch->addGroupBy(EmailTemplates::DB_TBL_PREFIX . 'code');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        if (!empty($post['keyword'])) {
            $cond = $srch->addCondition('etpl_code', 'like', '%' . $post['keyword'] . '%', 'AND');
            $cond->attachCondition('etpl_name', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cond->attachCondition('etpl_subject', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $adminId = AdminAuthentication::getLoggedAdminId();
        $this->canEdit = $this->objPrivilege->canEditEmailTemplates($adminId, true);
        $this->set("canEdit", $this->canEdit);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('langId', $this->adminLangId);
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditEmailTemplates();
        $data = FatApp::getPostedData();
        $lang_id = $data['lang_id'];
        $frm = $this->getLangForm($data['etpl_code'], $lang_id);
        $post = $frm->getFormDataFromArray($data);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $etplCode = $post['etpl_code'];
        $etplObj = new EmailTemplates($etplCode);
        $record = $etplObj->getEtpl($etplCode, $lang_id);
        $data = [
            'etpl_lang_id' => $lang_id,
            'etpl_code' => $etplCode,
            'etpl_name' => $post['etpl_name'],
            'etpl_subject' => $post['etpl_subject'],
            'etpl_body' => $post['etpl_body'],
        ];
        if (!$etplObj->addUpdateData($data)) {
            Message::addErrorMessage($etplObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', $this->str_setup_successful);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getLangForm($etplCode = '', $lang_id = 0)
    {
        $frm = new Form('frmEtplLang');
        $frm->addHiddenField('', 'etpl_code', $etplCode);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Name', $this->adminLangId), 'etpl_name');
        $frm->addRequiredField(Label::getLabel('LBL_Subject', $this->adminLangId), 'etpl_subject');
        $fld = $frm->addHtmlEditor(Label::getLabel('LBL_Body', $this->adminLangId), 'etpl_body');
        $fld->requirements()->setRequired(true);
        $frm->addHtml(Label::getLabel('LBL_Replacement_Caption', $this->adminLangId), 'replacement_caption', '<h3>' . Label::getLabel('LBL_Replacement_Vars', $this->adminLangId) . '</h3>');
        $frm->addHtml(Label::getLabel('LBL_Replacement_Vars', $this->adminLangId), 'etpl_replacements', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function langForm($etplCode = '', $lang_id = 0)
    {
        $lang_id = FatUtility::int($lang_id);
        if ($etplCode == '' || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($etplCode, $lang_id);
        $etplObj = new EmailTemplates($etplCode);
        $langData = $etplObj->getEtpl($etplCode, $lang_id);
        if ($langData) {
            $langFrm->fill($langData);
        }
        if (empty($langData['etpl_replacements'])) {
            $etplData = $etplObj->getEtpl($etplCode);
            $langFrm->getField('etpl_replacements')->value = $etplData['etpl_replacements'];
        }
        $this->set('languages', Language::getAllNames());
        $this->set('etplCode', $etplCode);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditEmailTemplates();
        $etplCode = FatApp::getPostedData('etplCode', FatUtility::VAR_STRING, '');
        $status = FatApp::getPostedData('status', FatUtility::VAR_STRING, '');
        if ($etplCode == '') {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $etplObj = new EmailTemplates($etplCode);
        $records = $etplObj->getEtpl($etplCode);
        if ($records == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!$etplObj->activateEmailTemplate($status, $etplCode)) {
            Message::addErrorMessage($etplObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', $this->str_update_record);
        $this->_template->render(false, false, 'json-success.php');
    }

}
