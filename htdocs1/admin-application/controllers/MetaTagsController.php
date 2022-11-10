<?php

class MetaTagsController extends AdminBaseController
{

    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewMetaTags();
        $this->canEdit = $this->objPrivilege->canEditMetaTags($this->admin_id, true);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $tabsArr = MetaTag::getTabsArr($this->adminLangId);
        $this->set('tabsArr', $tabsArr);
        $this->set('activeTab', MetaTag::META_GROUP_DEFAULT);
        $this->_template->render();
    }

    public function listMetaTags()
    {
        $metaType = FatApp::getPostedData('metaType', FatUtility::VAR_INT, MetaTag::META_GROUP_DEFAULT);
        $searchForm = $this->getSearchForm($metaType);
        $canAdd = false;
        $showFilters = true;
        if (in_array($metaType, [MetaTag::META_GROUP_DEFAULT])) {
            $showFilters = false;
        }
        if (in_array($metaType, [MetaTag::META_GROUP_OTHER])) {
            $canAdd = true;
        }
        $this->set('metaTypeDefault', MetaTag::META_GROUP_DEFAULT);
        $this->set('showFilters', $showFilters);
        $this->set('canAdd', $canAdd);
        $this->set('metaType', $metaType);
        $this->set('frmSearch', $searchForm);
        $this->_template->render(false, false);
    }

    public function search()
    {
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $searchForm = $this->getSearchForm($data['metaType']);
        $post = $searchForm->getFormDataFromArray($data);
        $metaType = FatUtility::convertToType($post['metaType'], FatUtility::VAR_INT, MetaTag::META_GROUP_DEFAULT);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $metaSrch = new MetaTagSearch($this->adminLangId);
        $criteria['metaType'] = ['val' => $metaType];
        if (!empty($post['keyword'])) {
            $criteria['keyword'] = ['val' => $post['keyword']];
        }
        if (isset($post['hasTagsAssociated']) && $post['hasTagsAssociated'] != '') {
            $criteria['hasTagsAssociated'] = ['val' => $post['hasTagsAssociated']];
        }
        $metaSrch->searchByCriteria($criteria, $this->adminLangId);
        $metaSrch->addMultipleFields($this->getDbColumns($metaType));
        $metaSrch->setPageNumber($page);
        $metaSrch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($metaSrch->getResultSet());
        $this->set("meta_record_id", $this->getMetaRecordcolumn($metaType));
        $this->set("columnsArr", $this->getColumns($metaType));
        $this->set("arr_listing", $records);
        $this->set('pageCount', $metaSrch->pages());
        $this->set('recordCount', $metaSrch->recordCount());
        $this->set('page', $page);
        $this->set('metaType', $metaType);
        $this->set('pageSize', $pagesize);
        $this->set('metaType', $metaType);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function form()
    {
        $metaId = FatApp::getPostedData('metaId', FatUtility::VAR_INT, 0);
        $metaType = FatApp::getPostedData('metaType', FatUtility::VAR_INT, MetaTag::META_GROUP_DEFAULT);
        $recordId = FatApp::getPostedData('recordId', FatUtility::VAR_INT, 0);
        $frm = $this->getForm($metaId, $metaType, $recordId);
        if (0 < $metaId) {
            $data = MetaTag::getAttributesById($metaId);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            if ($metaType == MetaTag::META_GROUP_OTHER) {
                $data['meta_slug'] = MetaTag::getOrignialUrlFromComponents($data);
            }
            $frm->fill($data);
        }
        $this->set('frm', $frm);
        $this->set('recordId', $recordId);
        $this->set('adminLangId', $this->adminLangId);
        $this->set('metaId', $metaId);
        $this->set('metaType', $metaType);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    public function setup()
    {
        if (!$this->canEdit) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $meta_record_id = FatApp::getPostedData('meta_record_id');
        $metaId = FatApp::getPostedData('meta_id');
        $tabsArr = MetaTag::getTabsArr($this->adminLangId);
        $metaType = FatApp::getPostedData('meta_type', FatUtility::VAR_INT);
        $frm = $this->getForm($metaId, $metaType, $meta_record_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!isset($tabsArr[$metaType])) {
            Message::addErrorMessage($this->str_invalid_access);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->setUrlComponents($metaType, $post);
        $metaTag = new MetaTag($metaId);
        $metaTag->assignValues($post);
        if (!$metaTag->save()) {
            Message::addErrorMessage($metaTag->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($metaId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = MetaTag::getAttributesByLangId($langId, $metaId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $metaId = $metaTag->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('metaId', $metaId);
        $this->set('metaType', $metaType);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($metaId = 0, $langId = 0, int $metaType = MetaTag::META_GROUP_DEFAULT)
    {
        $metaId = FatUtility::int($metaId);
        $langId = FatUtility::int($langId);
        if (!$data = MetaTag::getAttributesById($metaId)) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($metaId, $langId);
        $recordId = FatUtility::int($data['meta_record_id']);
        $langData = MetaTag::getAttributesByLangId($langId, $metaId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('metaId', $metaId);
        $this->set('metaType', $metaType);
        $this->set('recordId', $recordId);
        $this->set('langId', $langId);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($langId));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        if (!$this->canEdit) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $data = FatApp::getPostedData();
        $metaId = $data['meta_id'];
        $langId = $data['lang_id'];
        if ($metaId == 0 || $langId == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!$data['meta_other_meta_tags'] == '' && $data['meta_other_meta_tags'] == strip_tags($data['meta_other_meta_tags'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Other_Meta_Tag', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($metaId, $langId);
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $data) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        unset($data['meta_id']);
        unset($data['lang_id']);
        unset($data['btn_submit']);
        unset($data['open_graph_image']);
        $data['metalang_lang_id'] = $langId;
        $data['metalang_meta_id'] = $metaId;
        $metaTag = new MetaTag($metaId);
        if (!$metaTag->updateLangData($langId, $data)) {
            Message::addErrorMessage($metaTag->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = MetaTag::getAttributesByLangId($langId, $metaId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('metaId', $metaId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteRecord()
    {
        if (!$this->canEdit) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $metaId = FatApp::getPostedData('metaId', FatUtility::VAR_INT, 0);
        $metaTag = new MetaTag($metaId);
        if (!$metaTag->loadFromDb()) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!$metaTag->deleteRecord(true)) {
            Message::addErrorMessage($metaTag->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function setUpOgImage($metaId)
    {
        if (!$this->canEdit) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        $metaId = FatUtility::int($metaId);
        if (1 > $metaId) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $langId = FatUtility::int($post['lang_id']);
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], AttachedFile::FILETYPE_OPENGRAPH_IMAGE, $metaId, 0, $_FILES['file']['name'], -1, true, $langId, 0, true)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('metaId', $metaId);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function images($metaId, $langId = 0)
    {
        if (!$this->canEdit) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $metaId = FatUtility::int($metaId);
        $metaDetail = MetaTag::getAttributesById($metaId);
        if (false == $metaDetail) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!false == $metaDetail) {
            $ogImages = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_OPENGRAPH_IMAGE, $metaId, 0, $langId, false);
            $this->set('images', $ogImages);
        }
        $this->set('metaId', $metaId);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    public function viewImage($metaId, $langId)
    {
        $metaId = FatUtility::int($metaId);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_OPENGRAPH_IMAGE, $metaId, 0, $langId);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $w = 200;
        $h = 100;
        AttachedFile::displayImage($image_name, $w, $h);
    }

    public function removeImage()
    {
        $post = FatApp::getPostedData();
        $metaId = FatUtility::int($post['metaId']);
        $langId = FatUtility::int($post['langId']);
        if (1 > $metaId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_OPENGRAPH_IMAGE, $metaId, 0, 0, $langId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getSearchForm(int $metaType): Form
    {
        $frm = new Form('frmSearch');
        $frm->addHiddenField(Label::getLabel('LBL_Type', $this->adminLangId), 'metaType', $metaType);
        switch ($metaType) {
            case MetaTag::META_GROUP_DEFAULT:
                return $frm;
                break;
            case MetaTag::META_GROUP_OTHER:
                $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword');
                break;
            default:
                $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword');
                $frm->addSelectBox(Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId), 'hasTagsAssociated', applicationConstants::getYesNoArr($this->adminLangId), false, [], Label::getLabel('LBL_Doesn\'t_Matter', $this->adminLangId));
                break;
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        return $frm;
    }

    private function getForm(int $metaId = 0, $metaType = MetaTag::META_GROUP_DEFAULT, $recordId = 0)
    {
        $frm = new Form('frmMetaTag');
        $frm->addHiddenField('', 'meta_id', $metaId);
        $frm->addHiddenField('', 'meta_type', $metaType);
        if ($metaType == MetaTag::META_GROUP_OTHER) {
            $fld = $frm->addRequiredField(Label::getLabel('LBL_Slug', $this->adminLangId), 'meta_slug');
        } else {
            $frm->addHiddenField(Label::getLabel('LBL_Entity_Id', $this->adminLangId), 'meta_record_id', $recordId);
        }
        $frm->addRequiredField(Label::getLabel('LBL_Identifier', $this->adminLangId), 'meta_identifier');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm(int $metaId = 0, int $langId = 0)
    {
        $frm = new Form('frmMetaTagLang');
        $frm->addHiddenField('', 'meta_id', $metaId);
        $frm->addHiddenField('', 'lang_id', $langId);
        $frm->addTextBox(Label::getLabel('LBL_Meta_Title', $this->adminLangId), 'meta_title');
        $frm->addTextarea(Label::getLabel('LBL_Meta_Keywords', $this->adminLangId), 'meta_keywords');
        $frm->addTextarea(Label::getLabel('LBL_Meta_Description', $this->adminLangId), 'meta_description');
        $frm->addTextarea(Label::getLabel('LBL_Other_Meta_Tags', $this->adminLangId), 'meta_other_meta_tags');
        $frm->addTextBox(Label::getLabel('LBL_Open_Graph_Title', $this->adminLangId), 'meta_og_title');
        $frm->addTextBox(Label::getLabel('LBL_Open_Graph_Url', $this->adminLangId), 'meta_og_url');
        $frm->addTextarea(Label::getLabel('LBL_Open_Graph_Description', $this->adminLangId), 'meta_og_description');
        $fld = $frm->addButton(Label::getLabel("LBL_Open_Graph_Image", $this->adminLangId), 'open_graph_image', Label::getLabel("LBL_Upload_File", $this->adminLangId), array(
            'class' => 'meta-tag',
            'id' => 'open_graph_image',
            'meta_id' => $metaId
        ));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function setUrlComponents($metaType, &$post)
    {
        $metaId = FatUtility::int($post['meta_id']);
        $tabsArr = MetaTag::getTabsArr($this->adminLangId);
        switch ($metaType) {
            case MetaTag::META_GROUP_TEACHER:
                $userDetail = User::getAttributesById($post['meta_record_id']);
                if (!$userDetail) {
                    return false;
                }
                $post['meta_controller'] = $tabsArr[$metaType]['controller'];
                $post['meta_action'] = $tabsArr[$metaType]['action'];
                $post['meta_record_id'] = CommonHelper::htmlEntitiesDecode($userDetail['user_url_name']);
                if ($metaId == 0) {
                    $post['meta_subrecord_id'] = 0;
                }
                break;
            case MetaTag::META_GROUP_OTHER:
                $post['meta_controller'] = $post['meta_action'] = '';
                $post['meta_record_id'] = $post['meta_subrecord_id'] = 0;
                $componentsNameArray = ['meta_controller', 'meta_action', 'meta_record_id', 'meta_subrecord_id'];
                $urlComponents = explode("/", $post['meta_slug']);
                $urlComponents = array_combine(array_slice($componentsNameArray, 0, count($urlComponents)), $urlComponents);
                $post = array_merge($post, $urlComponents);
                $post['meta_controller'] = FatUtility::dashed2Camel($post['meta_controller'], true);
                $post['meta_action'] = FatUtility::dashed2Camel($post['meta_action']);
                break;
            default:
                $post['meta_controller'] = $tabsArr[$metaType]['controller'];
                $post['meta_action'] = $tabsArr[$metaType]['action'];
                if ($metaId == 0) {
                    $post['meta_subrecord_id'] = 0;
                }
                break;
        }
        return true;
    }

    private function getColumns(int $metaType)
    {
        $columnsArr = [];
        switch ($metaType) {
            case MetaTag::META_GROUP_DEFAULT:
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'meta_identifier' => Label::getLabel('LBL_Identifier', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_OTHER:
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'url' => Label::getLabel('LBL_Slug', $this->adminLangId),
                    'meta_identifier' => Label::getLabel('LBL_Identifier', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_CMS_PAGE:
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'cpage_title' => Label::getLabel('LBL_CMS_Page', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'has_tag_associated' => Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_TEACHER:
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'teacher_name' => Label::getLabel('LBL_Teacher_Name', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'has_tag_associated' => Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_GRP_CLASS:
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'grpcls_title' => Label::getLabel('LBL_Group_Class', $this->adminLangId),
                    'teacher_name' => Label::getLabel('LBL_Teacher_Name', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'has_tag_associated' => Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_BLOG_CATEGORY;
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'bpcategory_identifier' => Label::getLabel('LBL_Blog_Categories', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'has_tag_associated' => Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
            case MetaTag::META_GROUP_BLOG_POST;
                $columnsArr = [
                    'listserial' => Label::getLabel('LBL_Sr._No', $this->adminLangId),
                    'post_identifier' => Label::getLabel('LBL_Post_Title', $this->adminLangId),
                    'meta_title' => Label::getLabel('LBL_Meta_Title', $this->adminLangId),
                    'has_tag_associated' => Label::getLabel('LBL_Has_Tags_Associated', $this->adminLangId),
                    'action' => Label::getLabel('LBL_Action', $this->adminLangId),
                ];
                break;
        }
        return $columnsArr;
    }

    private function getDbColumns(int $metaType)
    {
        $dbcolumnsArr = ['meta_id', 'meta_record_id', 'meta_identifier', 'meta_title'];
        switch ($metaType) {
            case MetaTag::META_GROUP_OTHER:
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['meta_controller', 'meta_action', 'meta_record_id', 'meta_subrecord_id']);
                break;
            case MetaTag::META_GROUP_CMS_PAGE:
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['cpage_id', 'IFNULL(cpage_title,cpage_identifier) as cpage_title']);
                break;
            case MetaTag::META_GROUP_TEACHER:
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['CONCAT(user_first_name, " ", user_last_name) as teacher_name', 'u.user_id']);
                break;
            case MetaTag::META_GROUP_GRP_CLASS:
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['grpcls_title', 'grpcls_id', 'concat(u.user_first_name," ",u.user_last_name) as teacher_name']);
                break;
            case MetaTag::META_GROUP_BLOG_CATEGORY;
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['IFNULL(bpcategory_name,bpcategory_identifier) as bpcategory_identifier', 'bpcategory_id']);
                break;
            case MetaTag::META_GROUP_BLOG_POST;
                $dbcolumnsArr = array_merge($dbcolumnsArr, ['IFNULL(post_title,post_identifier) as post_identifier ', 'post_id']);
                break;
        }
        return $dbcolumnsArr;
    }

    private function getMetaRecordcolumn(int $metaType)
    {
        $metaRecordColumns = [
            MetaTag::META_GROUP_DEFAULT => 'meta_record_id',
            MetaTag::META_GROUP_OTHER => 'meta_record_id',
            MetaTag::META_GROUP_CMS_PAGE => 'cpage_id',
            MetaTag::META_GROUP_TEACHER => 'user_id',
            MetaTag::META_GROUP_BLOG_CATEGORY => 'bpcategory_id',
            MetaTag::META_GROUP_BLOG_POST => 'post_id',
            MetaTag::META_GROUP_GRP_CLASS => 'grpcls_id'
        ];
        return $metaRecordColumns[$metaType];
    }

}
