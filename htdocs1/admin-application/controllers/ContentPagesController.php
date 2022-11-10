<?php

class ContentPagesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewContentPages();
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm();
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditContentPages($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set('includeEditor', true);
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = ContentPage::getSearchObject($this->adminLangId);
        if (!empty($post['keyword'])) {
            $srch->addCondition('p.cpage_identifier', 'like', '%' . $post['keyword'] . '%');
        }
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditContentPages($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function layouts()
    {
        $this->_template->render(false, false);
    }

    public function form($cpage_id = 0)
    {
        $cpage_id = FatUtility::int($cpage_id);
        $blockFrm = $this->getForm($cpage_id);
        if (0 < $cpage_id) {
            $data = ContentPage::getAttributesById($cpage_id, ['cpage_id', 'cpage_identifier', 'cpage_layout']);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $blockFrm->fill($data);
            $this->set('cpage_layout', $data['cpage_layout']);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('cpage_id', $cpage_id);
        $this->set('blockFrm', $blockFrm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditContentPages();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $cpage_id = $post['cpage_id'];
        unset($post['cpage_id']);
        $record = new ContentPage($cpage_id);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($cpage_id > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = ContentPage::getAttributesByLangId($langId, $cpage_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $cpage_id = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', Label::getLabel('LBL_Setup_Successful', $this->adminLangId));
        $this->set('pageId', $cpage_id);
        $this->set('langId', $newTabLangId);
        $this->set('cpage_layout', $post['cpage_layout']);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($cpage_id = 0, $lang_id = 0, $cpage_layout = 0)
    {
        $cpage_id = FatUtility::int($cpage_id);
        $lang_id = FatUtility::int($lang_id);
        if ($cpage_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $blockLangFrm = $this->getLangForm($cpage_id, $lang_id, $cpage_layout);
        $langData = ContentPage::getAttributesByLangId($lang_id, $cpage_id);
        if ($langData) {
            $srch = new searchBase(ContentPage::DB_TBL_CONTENT_PAGES_BLOCK_LANG);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $srch->addMultipleFields(["cpblocklang_text", 'cpblocklang_block_id']);
            $srch->addCondition('cpblocklang_cpage_id', '=', $cpage_id);
            $srch->addCondition('cpblocklang_lang_id', '=', $lang_id);
            $srchRs = $srch->getResultSet();
            $blockData = FatApp::getDb()->fetchAll($srchRs, 'cpblocklang_block_id');
            foreach ($blockData as $blockKey => $blockContent) {
                $langData['cpblock_content_block_' . $blockKey] = $blockContent['cpblocklang_text'];
            }
            $blockLangFrm->fill($langData);
        }
        $bgImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE, $cpage_id, 0, $lang_id);
        $bannerTypeArr = applicationConstants::bannerTypeArr();
        $this->set('bgImages', $bgImages);
        $this->set('bannerTypeArr', $bannerTypeArr);
        $this->set('languages', Language::getAllNames());
        $this->set('cpage_id', $cpage_id);
        $this->set('cpage_lang_id', $lang_id);
        $this->set('cpage_layout', $cpage_layout);
        $this->set('blockLangFrm', $blockLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditContentPages();
        $post = FatApp::getPostedData();
        $cpage_id = $post['cpage_id'];
        $lang_id = $post['lang_id'];
        $cpage_layout = $post['cpage_layout'];
        if ($cpage_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        unset($post['cpage_id']);
        unset($post['lang_id']);
        $data = [
            'cpagelang_lang_id' => $lang_id,
            'cpagelang_cpage_id' => $cpage_id,
            'cpage_title' => $post['cpage_title']
        ];
        if ($cpage_layout == ContentPage::CONTENT_PAGE_LAYOUT1_TYPE) {
            $data['cpage_image_title'] = $post['cpage_image_title'];
            $data['cpage_image_content'] = $post['cpage_image_content'];
        } else {
            $data['cpage_content'] = $post['cpage_content'];
        }
        $pageObj = new ContentPage($cpage_id);
        if (!$pageObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($pageObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $cpage_id = $pageObj->getMainTableRecordId();
        if (!$cpage_id) {
            $cpage_id = FatApp::getDb()->getInsertId();
        }
        $pageObj = new ContentPage($cpage_id);
        if ($cpage_layout == ContentPage::CONTENT_PAGE_LAYOUT1_TYPE) {
            for ($i = 1; $i <= ContentPage::CONTENT_PAGE_LAYOUT1_BLOCK_COUNT; $i++) {
                $data['cpblocklang_text'] = $post['cpblock_content_block_' . $i];
                $data['cpblocklang_block_id'] = $i;
                if (!$pageObj->addUpdateContentPageBlocks($lang_id, $cpage_id, $data)) {
                    Message::addErrorMessage($pageObj->getError());
                    FatUtility::dieWithError(Message::getHtml());
                }
            }
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = ContentPage::getAttributesByLangId($langId, $cpage_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('LBL_Setup_Successful', $this->adminLangId));
        $this->set('pageId', $cpage_id);
        $this->set('langId', $newTabLangId);
        $this->set('cpage_layout', $cpage_layout);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditContentPages();
        $cpage_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($cpage_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $obj = new ContentPage($cpage_id);
        if (!$obj->canRecordMarkDelete($cpage_id)) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $obj->assignValues([ContentPage::tblFld('deleted') => 1]);
        if (!$obj->save()) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function autoComplete()
    {
        $db = FatApp::getDb();
        $srch = ContentPage::getSearchObject($this->adminLangId);
        $post = FatApp::getPostedData();
        if (!empty($post['keyword'])) {
            $srch->addCondition('cpage_title', 'LIKE', '%' . $post['keyword'] . '%');
        }
        $srch->setPageSize(FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10));
        $srch->addMultipleFields(['cpage_id', 'IFNULL(cpage_title,cpage_identifier) as cpage_name']);
        $products = $db->fetchAll($srch->getResultSet(), 'cpage_id');
        $json = [];
        foreach ($products as $key => $product) {
            $json[] = ['id' => $key, 'name' => strip_tags(html_entity_decode($product['cpage_name'], ENT_QUOTES, 'UTF-8'))];
        }
        die(json_encode($json));
    }

    private function getSearchForm()
    {
        $frm = new Form('frmPagesSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Page_Identifier', $this->adminLangId), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function getForm($cpage_id = 0)
    {
        $cpage_id = FatUtility::int($cpage_id);
        $frm = new Form('frmBlock');
        $frm->addHiddenField('', 'cpage_id', 0);
        $frm->addRequiredField(Label::getLabel('LBL_Page_Identifier', $this->adminLangId), 'cpage_identifier');
        $frm->addSelectBox(Label::getLabel('LBL_Layout_Type', $this->adminLangId), 'cpage_layout', $this->getAvailableLayouts(), '', ['id' => 'cpage_layout'])->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getAvailableLayouts()
    {
        $collectionLayouts = [
            ContentPage::CONTENT_PAGE_LAYOUT1_TYPE => Label::getLabel('LBL_Content_Page_Layout1', $this->adminLangId),
            ContentPage::CONTENT_PAGE_LAYOUT2_TYPE => Label::getLabel('LBL_Content_Page_Layout2', $this->adminLangId)
        ];
        return $collectionLayouts;
    }

    private function getLangForm($cpage_id = 0, $lang_id = 0, $cpage_layout = 0)
    {
        $frm = new Form('frmBlockLang');
        $frm->addHiddenField('', 'cpage_id', $cpage_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addHiddenField('', 'cpage_layout', $cpage_layout);
        $frm->addRequiredField(Label::getLabel('LBL_Page_Title', $this->adminLangId), 'cpage_title');
        if ($cpage_layout == ContentPage::CONTENT_PAGE_LAYOUT1_TYPE) {
            $bannerTypeArr = applicationConstants::bannerTypeArr();
            $fld = $frm->addButton(Label::getLabel('LBL_Backgroud_Image', $this->adminLangId), 'cpage_bg_image', Label::getLabel('LBL_Upload_Image', $this->adminLangId), [
                'class' => 'bgImageFile-Js',
                'id' => 'cpage_bg_image',
                'data-file_type' => AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE,
                'data-frm' => 'frmBlock'
            ]);
            $frm->addTextBox(Label::getLabel('LBL_Background_Image_Title', $this->adminLangId), 'cpage_image_title');
            $frm->addTextarea(Label::getLabel('LBL_Background_Image_Description', $this->adminLangId), 'cpage_image_content');
            for ($i = 1; $i <= ContentPage::CONTENT_PAGE_LAYOUT1_BLOCK_COUNT; $i++) {
                $frm->addHtmlEditor(Label::getLabel('LBL_Content_Block_' . $i, $this->adminLangId), 'cpblock_content_block_' . $i);
            }
        } else {
            $frm->addHtmlEditor(Label::getLabel('LBL_Page_Content', $this->adminLangId), 'cpage_content');
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

    public function setUpBgImage()
    {
        $post = FatApp::getPostedData();
        $file_type = FatApp::getPostedData('file_type', FatUtility::VAR_INT, 0);
        $cpage_id = FatApp::getPostedData('cpage_id', FatUtility::VAR_INT, 0);
        $lang_id = FatApp::getPostedData('lang_id', FatUtility::VAR_INT, 0);
        $cpage_layout = FatApp::getPostedData('cpage_layout', FatUtility::VAR_INT, 0);
        if (!$file_type || !$cpage_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $allowedFileTypeArr = [AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE];
        if (!in_array($file_type, $allowedFileTypeArr)) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('LBL_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$res = $fileHandlerObj->saveImage($_FILES['file']['tmp_name'], $file_type, $cpage_id, 0, $_FILES['file']['name'], -1, $unique_record = true, $lang_id, $_FILES['file']['type'])) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('file', $_FILES['file']['name']);
        $this->set('cpage_id', $cpage_id);
        $this->set('cpage_layout', $cpage_layout);
        $this->set('lang_id', $lang_id);
        $this->set('msg', $_FILES['file']['name'] . ' ' . Label::getLabel('LBL_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeBgImage($cpage_id = 0, $langId = 0)
    {
        $cpage_id = FatUtility::int($cpage_id);
        $langId = FatUtility::int($langId);
        if (!$cpage_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE, $cpage_id, 0, 0, $langId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('LBL_Deleted_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function cpageBackgroundImage($cpageId, $langId = 0, $sizeType = '')
    {
        $cpageId = FatUtility::int($cpageId);
        $langId = FatUtility::int($langId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE, $cpageId, 0, $langId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            case 'COLLECTION_PAGE':
                $w = 45;
                $h = 41;
                AttachedFile::displayImage($image_name, $w, $h);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name);
                break;
        }
    }

}
