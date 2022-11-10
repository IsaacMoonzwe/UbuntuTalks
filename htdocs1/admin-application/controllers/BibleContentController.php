<?php

class BibleContentController extends AdminBaseController
{

    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewBibleContent($this->admin_id);
        $this->objPrivilege->canViewBibleContent($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditBibleContent($this->admin_id, true);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->set('srchFrm', $this->getSearchForm());
        $this->_template->render();
    }

    private function getSearchForm()
    {
        $frm = new Form('frmPagesSearch');
        $f1 = $frm->addTextBox('Content Heading', 'keyword', '');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox('Status', 'biblecontent_active', $activeInactiveArr, '', []);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
        $fld_cancel = $frm->addButton("", "btn_clear", "Clear Search", ['onClick' => 'clearSearch()']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    public function setup()
    {
        $this->objPrivilege->canEditBibleContent();
        $frm = $this->getForm();
        $postedData = FatApp::getPostedData();
        $postedData['image'] = 1;
        $post = $frm->getFormDataFromArray($postedData);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $bibleContentId = $post['biblecontent_id'];
        $bibleContent = new BibleContent($bibleContentId);
        $bibleContent->assignValues($post);
        if (!$bibleContent->save()) {
            Message::addErrorMessage($bibleContent->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $bibleContentId = $bibleContent->getMainTableRecordId();
        FatUtility::dieJsonSuccess('Setup Successful.');
    }

    public function search()
    {
        $this->objPrivilege->canViewBibleContent();
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = BibleContent::getSearchObject($this->adminLangId);
        if (!empty($post['keyword'])) {
            $srch->addCondition('biblecontent_title', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['biblecontent_type']) && intval($post['biblecontent_type'] > 0)) {
            $srch->addCondition('biblecontent_type', '=', $post['biblecontent_type']);
        }
        if ($post['biblecontent_active'] != '') {
            $srch->addCondition('biblecontent_active', '=', intval($post['biblecontent_active']));
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $srch->addOrder(BibleContent::DB_TBL_PREFIX . 'active', 'DESC');
        $srch->addOrder(BibleContent::DB_TBL_PREFIX . 'display_order', 'ASC');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form($contentId = 0)
    {
        $this->objPrivilege->canViewBibleContent();
        $contentId = FatUtility::int($contentId);
        $frm = $this->getForm($contentId);
        if ($contentId > 0) {
            $data = BibleContent::getBibleContentById($contentId);
            if (empty($data)) {
                FatUtility::dieWithError("Invalid Request");
            }
            $videoUrl = $data['biblecontent_url'];
            $videoData = CommonHelper::getVideoDetail($videoUrl);
            if ($videoData['video_thumb']) {
                $frm->getField('biblecontent_url')->attachField($frm->addHtml('', 'video_display', '<img id="displayVideo" alt="" width="100" height="100" src=' . $videoData['video_thumb'] . '>'));
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('contentId', $contentId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    private function getForm()
    {
        $langId = CommonHelper::getLangId();
        $frm = new Form('frmBlock');
        $frm->addHiddenField('', 'biblecontent_id', '');
        $frm->addRequiredField('Content Heading', 'biblecontent_title');
        $videoFld = $frm->addRequiredField('Video Url', 'biblecontent_url', '', []);
        $videoReq = new FormFieldRequirement('biblecontent_url', 'Video Url');
        $videoReq->setRequired(true);
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($langId);
        $frm->addSelectBox('Status', 'biblecontent_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', 'Save Changes');
        return $frm;
    }

    public function langForm($biblecontent_id, $lang_id = 0)
    {
        $biblecontent_id = FatUtility::int($biblecontent_id);
        if (1 > $biblecontent_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $lang_id = FatUtility::int($lang_id);
        if ($lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $bibleLangFrm = $this->getLangForm($biblecontent_id, $lang_id);
        $langData = BibleContent::getAttributesByLangId($lang_id, $biblecontent_id);
        if ($langData) {
            $bibleLangFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('biblecontent_id', $biblecontent_id);
        $this->set('bible_lang_id', $lang_id);
        $this->set('bibleLangFrm', $bibleLangFrm);
        $this->_template->render(false, false);
    }

    private function getLangForm($biblecontent_id, $lang_id)
    {
        $frm = new Form('frmBibleLang');
        $frm->addHiddenField('', 'biblecontent_id', $biblecontent_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Bible_Title', $this->adminLangId), 'biblecontentlang_biblecontent_title');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditBibleContent();
        $post = FatApp::getPostedData();
        $biblecontent_id = $post['biblecontent_id'];
        $lang_id = $post['lang_id'];
        if ($biblecontent_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($biblecontent_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $data = [
            'biblecontentlang_biblecontent_id' => $biblecontent_id,
            'biblecontentlang_lang_id' => $lang_id,
            'biblecontentlang_biblecontent_title' => $post['biblecontentlang_biblecontent_title']
        ];
        $bibleObj = new BibleContent($biblecontent_id);
        if (!$bibleObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($bibleObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = BibleContent::getAttributesByLangId($langId, $biblecontent_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('biblecontent_id', $biblecontent_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditBibleContent();
        $biblecontent_id = FatApp::getPostedData('biblecontent_id', FatUtility::VAR_INT, 0);
        if (0 >= $biblecontent_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $biblecontent_active = FatApp::getPostedData('biblecontent_active', FatUtility::VAR_INT, -1);
        if (0 !== $biblecontent_active && 1 !== $biblecontent_active) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = BibleContent::getAttributesById($biblecontent_id, ['biblecontent_id', 'biblecontent_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $BibleContent = new BibleContent($biblecontent_id);
        if (!$BibleContent->changeStatus($biblecontent_active)) {
            Message::addErrorMessage($BibleContent->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditBibleContent();
        $biblecontent_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($biblecontent_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $db = FatApp::getDb();
        $db->deleteRecords(BibleContent::DB_TBL, ['smt' => 'biblecontent_id = ?', 'vals' => [$biblecontent_id]]);
        if ($db->getError()) {
            Message::addErrorMessage($db->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $db->deleteRecords(BibleContent::DB_TBL_LANG, ['smt' => 'biblecontentlang_biblecontent_id = ?', 'vals' => [$biblecontent_id]]);
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function updateOrder()
    {
        $this->objPrivilege->canEditBibleContent();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $BibleContent = new BibleContent();
            if (!$BibleContent->updateOrder($post['bibleList'])) {
                Message::addErrorMessage($BibleContent->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
        }
    }

}
