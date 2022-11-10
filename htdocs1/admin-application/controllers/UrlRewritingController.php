<?php

class UrlRewritingController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewUrlRewrites();
    }

    public function index()
    {
        $canEdit = $this->objPrivilege->canEditUrlRewrites(true);
        $srchFrm = $this->getSearchForm($this->adminLangId);
        $this->set("srchFrm", $srchFrm);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $canEdit = $this->objPrivilege->canEditUrlRewrites(true);
        $pageSize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm($this->adminLangId);
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        if (false === $post) {
            Message::addErrorMessage(current($searchForm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $srch = new UrlRewriteSearch();
        $srch->joinTable(Language::DB_TBL, 'LEFT OUTER JOIN', 'lng.language_id = ur.urlrewrite_lang_id', 'lng');
        if (!empty($post['keyword'])) {
            $condition = $srch->addCondition('ur.urlrewrite_original', 'like', '%' . $post['keyword'] . '%');
            $condition->attachCondition('ur.urlrewrite_custom', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        if ($post['lang_id'] > 0) {
            $srch->addCondition('ur.urlrewrite_lang_id', '=', $post['lang_id']);
        }
        $page = max(FatApp::getPostedData('page', FatUtility::VAR_INT, 1), 1);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $srch->addMultipleFields([
            'urlrewrite_id', 'urlrewrite_original', 'urlrewrite_custom',
            'urlrewrite_http_resp_code', 'language_code'
        ]);
        $srch->addOrder('urlrewrite_id', 'DESC');
        $srch->addOrder('urlrewrite_original', 'asc');
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set("canEdit", $canEdit);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form()
    {
        $post = FatApp::getPostedData();
        $urlRewriteId = FatUtility::int($post['UrlRewriteId']);
        $frm = $this->getForm($urlRewriteId, $this->adminLangId);
        if (!empty($post['originalUrl'])) {
            $srch = new UrlRewriteSearch();
            $srch->addCondition('ur.urlrewrite_original', '=', $post['originalUrl']);
            $rs = $srch->getResultSet();
            $data = [];
            while ($row = FatApp::getDb()->fetch($rs)) {
                $data['urlrewrite_original'] = $row['urlrewrite_original'];
                $data['urlrewrite_custom'][$row['urlrewrite_lang_id']] = $row['urlrewrite_custom'];
                $data['urlrewrite_http_resp_code'][$row['urlrewrite_lang_id']] = $row['urlrewrite_http_resp_code'];
            }
            if (empty($data)) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditUrlRewrites();
        $urlrewriteId = FatApp::getPostedData('urlrewrite_id', FatUtility::VAR_INT, 0);
        $frm = $this->getForm($urlrewriteId, $this->adminLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $row = [];
        $originalUrl = FatApp::getPostedData('urlrewrite_original', FatUtility::VAR_STRING, '');
        if (0 < $urlrewriteId) {
            $srch = new UrlRewriteSearch();
            $srch->addCondition('ur.urlrewrite_original', '=', $originalUrl);
            $row = FatApp::getDb()->fetchAll($srch->getResultSet(), 'urlrewrite_lang_id');
            if (empty($row)) {
                FatUtility::dieJsonError(Label::getLabel('MSG_INVALID_REQUEST', $this->adminLangId));
            }
        }
        $langArr = Language::getAllNames();
        foreach ($langArr as $langId => $langName) {
            $recordId = 0;
            if (array_key_exists($langId, $row)) {
                $recordId = $row[$langId]['urlrewrite_id'];
            }
            $url = $post['urlrewrite_custom'][$langId];
            if ($recordId > 0) {
                $data = [
                    'urlrewrite_http_resp_code' => $post['urlrewrite_http_resp_code'][$langId],
                    'urlrewrite_custom' => CommonHelper::seoUrl($url, "/[\s,<>\"&$%+?$@=]/")
                ];
            } else {
                $data = [
                    'urlrewrite_original' => $originalUrl,
                    'urlrewrite_lang_id' => $langId,
                    'urlrewrite_http_resp_code' => $post['urlrewrite_http_resp_code'][$langId],
                    'urlrewrite_custom' => CommonHelper::seoUrl($url, "/[\s,<>\"&$%+?$@=]/")
                ];
            }
            $record = new UrlRewrite($recordId);
            $record->assignValues($data);
            if (!$record->save()) {
                FatUtility::dieJsonError($record->getError());
            }
        }
        FatUtility::dieJsonSuccess(['urlrewrite_id' => $urlrewriteId, 'msg' => $this->str_setup_successful]);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditUrlRewrites();
        $urlRewriteId = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($urlRewriteId < 1) {
            FatUtility::dieJsonError($this->str_invalid_request_id);
        }
        $urlRewrite = new UrlRewrite($urlRewriteId);
        if (!$urlRewrite->loadFromDb()) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!$urlRewrite->deleteRecord(false)) {
            Message::addErrorMessage($urlRewrite->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function getSearchForm(int $langId)
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $langId), 'keyword');
        $langArr = Language::getAllNames();
        $defaultLangId = FatApp::getConfig('CONF_DEFAULT_SITE_LANG', FatUtility::VAR_INT, 1);
        $frm->addSelectBox(Label::getLabel('LBL_Language', $langId), 'lang_id', $langArr, $defaultLangId);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $langId));
        $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $langId));
        return $frm;
    }

    private function getForm(int $urlrewrite_id = 0, int $langId)
    {
        $frm = new Form('frmUrlRewrite');
        $frm->addHiddenField('', 'urlrewrite_id', $urlrewrite_id);
        $frm->addRequiredField(Label::getLabel('LBL_Original_URL', $langId), 'urlrewrite_original');
        $langArr = Language::getAllNames();
        foreach ($langArr as $langId => $langName) {
            $frm->addRequiredField(Label::getLabel('LBL_Custom_URL', $langId) . '(' . $langName . ')', 'urlrewrite_custom[' . $langId . ']');
            $fldhttpcode = $frm->addSelectBox(Label::getLabel('LBL_Http_Code', $langId) . '(' . $langName . ')', 'urlrewrite_http_resp_code[' . $langId . ']', UrlRewrite::getHttpCodeArr($langId));
            $fldhttpcode->requirements()->setRequired();
        }
        $frm->addHTML('', '', '<small>' . Label::getLabel('LBL_Example_Custom_URL_Example', $langId) . '</small>');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $langId));
        return $frm;
    }
}
