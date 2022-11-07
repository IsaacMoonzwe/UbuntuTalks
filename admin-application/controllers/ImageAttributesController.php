<?php

class ImageAttributesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewImageAttributes($this->admin_id);
    }

    public function index()
    {
        $searchForm = $this->getSearchForm(AttachedFile::FILETYPE_BANNER, $this->adminLangId);
        $this->set('activeTab', AttachedFile::FILETYPE_BANNER);
        $this->set("tabsArr", AttachedFile::getFileTypesArrForImgAttrs($this->adminLangId));
        $this->set('frmSearch', $searchForm);
        $this->_template->render();
    }

    public function search()
    {
        $langArr = Language::getAllCodesAssoc(true);
        $canEdit = $this->objPrivilege->canEditImageAttributes($this->admin_id, true);
        $pageSize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $data = FatApp::getPostedData();
        $searchForm = $this->getSearchForm($data['imageAttributeType'], $this->adminLangId);
        $post = $searchForm->getFormDataFromArray($data);
        if (false === $post) {
            Message::addErrorMessage(current($searchForm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $srch = new AttachedFileSearch($this->adminLangId);
        switch ($post['imageAttributeType']) {
            case AttachedFile::FILETYPE_BANNER:
                $srch->joinBanners(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('banner_title', 'like', '%' . $post['keyword'] . '%');
                }
                break;
            case AttachedFile::FILETYPE_HOME_PAGE_BANNER:
                $srch->joinHomePageBanner(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('slide_identifier', 'like', '%' . $post['keyword'] . '%');
                }
                break;
            case AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE:
                $srch->joinContentBackgroudImage(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('cpage_identifier', 'like', '%' . $post['keyword'] . '%');
                }
                break;
            case AttachedFile::FILETYPE_TEACHING_LANGUAGES:
                $srch->joinTeachingLanguage(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('tlanguage_identifier', 'like', '%' . $post['keyword'] . '%');
                }
                break;
            case AttachedFile::FILETYPE_FLAG_TEACHING_LANGUAGES:
                $srch->joinFlagTeachingLangugage(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('tlanguage_identifier', 'like', '%' . $post['keyword'] . '%');
                }
                break;
            case AttachedFile::FILETYPE_BLOG_POST_IMAGE:
                $srch->joinBlogPostImage(true);
                if (!empty($post['keyword'])) {
                    $srch->addCondition('post_identifier', 'like', '%' . $post['keyword'] . '%');
                }
                break;
        }
        $srch->addOrder('afile_id', 'DESC');
        $page = max(FatApp::getPostedData('page', FatUtility::VAR_INT, 1), 1);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('imageAttributeType', $post['imageAttributeType']);
        $this->set('pageCount', $srch->pages());
        $this->set("canEdit", $canEdit);
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('postedData', $post);
        $this->set('langArr', $langArr);
        $this->_template->render(false, false);
    }

    public function form()
    {
        $recordId = FatApp::getPostedData('recordId', FatUtility::VAR_INT, 0);
        $type = FatApp::getPostedData('type', FatUtility::VAR_INT, MetaTag::META_GROUP_DEFAULT);
        $fileId = FatApp::getPostedData('afileId', FatUtility::VAR_INT, 0);
        if (!$fileId || !$type || !$recordId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $frm = $this->getForm($recordId, $type, $fileId, $this->adminLangId);
        if (0 < $fileId) {
            $srch = AttachedFile::getSearchObject();
            $srch->addCondition('afile_id', '=', $fileId);
            $rs = $srch->getResultSet();
            $record = FatApp::getDb()->fetch($rs);
            $frm->fill($record);
        }
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditImageAttributes();
        $post = FatApp::getPostedData();
        $recordId = FatUtility::int($post['recordId']);
        $moduleType = FatUtility::int($post['type']);
        $fileId = FatUtility::int($post['fileId']);
        if (!$recordId || !$moduleType || !$fileId) {
            FatUtility::dieJsonError($this->str_invalid_request_id);
        }
        $frm = $this->getForm($recordId, $moduleType, $fileId, $this->adminLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $db = FatApp::getDb();
        $where = ['smt' => 'afile_record_id = ? and afile_id = ?', 'vals' => [$recordId, $fileId]];
        if (!$db->updateFromArray(AttachedFile::DB_TBL, ['afile_attribute_title' => $post['afile_attribute_title'], 'afile_attribute_alt' => $post['afile_attribute_alt']], $where)) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess($this->str_setup_successful);
        ;
    }

    private function getSearchForm(int $imageAttributeType, int $langId)
    {
        $frm = new Form('frmSearch');
        $frm->addHiddenField(Label::getLabel('LBL_Type', $langId), 'imageAttributeType', $imageAttributeType);
        $frm->addTextBox(Label::getLabel('LBL_Keyword', $langId), 'keyword');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $langId));
        $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $langId));
        return $frm;
    }

    private function getForm(int $recordId, int $type, int $fileId, int $langId)
    {
        $frm = new Form('frmImageAttributes');
        $frm->addHiddenField('', 'recordId', $recordId);
        $frm->addHiddenField('', 'type', $type);
        $frm->addHiddenField('', 'fileId', $fileId);
        $frm->addRequiredField(Label::getLabel('LBL_Image_Title', $langId), 'afile_attribute_title');
        $frm->addRequiredField(Label::getLabel('LBL_Image_Alt', $langId), 'afile_attribute_alt');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $langId));
        return $frm;
    }

}
