<?php

class BannersController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewBanners();
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm();
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function layouts()
    {
        $this->_template->render(false, false);
    }

    public function search()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $srch = BannerLocation::getSearchObject($this->adminLangId, false);
        $srch->addMultipleFields([
            'blocation_banner_width',
            'blocation_banner_height',
            'blocation_id',
            'blocation_active',
            "IFNULL(blocation_name,blocation_identifier) as blocation_name"
        ]);
        $srch->addOrder(Banner::DB_TBL_LOCATIONS_PREFIX . 'active', 'DESC');
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditBanners($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function bannerLocation($bLocationId = 0)
    {
        $bLocationId = FatUtility::int($bLocationId);
        if (1 > $bLocationId) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $frm = $this->getLocationForm();
        $data = $this->getBannerLocationById($bLocationId);
        if (empty($data)) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->fill($data);
        $this->set('languages', Language::getAllNames());
        $this->set('frm', $frm);
        $this->set('bLocationId', $bLocationId);
        $this->set('activeInactiveArr', $activeInactiveArr);
        $this->_template->render(false, false);
    }

    public function setupLocation()
    {
        $this->objPrivilege->canEditBanners();
        $frm = $this->getLocationForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $bLocationId = $post['blocation_id'];
        if (1 > $bLocationId) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $bannerObj = new Banner();
        if (!$bannerObj->updateLocationData($post)) {
            Message::addErrorMessage($bannerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('bLocationId', $bLocationId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function listing($bLocationId)
    {
        $bLocationId = FatUtility::int($bLocationId);
        if (1 > $bLocationId) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        $frmSearch = $this->getListingSearchForm();
        $frmSearch->fill(['blocation_id' => $bLocationId]);
        $data = $this->getBannerLocationById($bLocationId);
        $this->_template->addJs('js/responsive-img.min.js');
        $this->set('data', $data);
        $this->set('bLocationId', $bLocationId);
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function listingSearch()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getListingSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $blocation_id = $post['blocation_id'];
        if (1 > $blocation_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $srch = new BannerSearch($this->adminLangId, false);
        $srch->joinLocations();
        $srch->addMultipleFields([
            'banner_id',
            'banner_url',
            'banner_target',
            'banner_active',
            'banner_blocation_id',
            'banner_title'
        ]);
        $srch->addCondition('b.banner_blocation_id', '=', $blocation_id);
        $srch->addOrder('banner_active', 'DESC');
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditBanners($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('bannerTypeArr', Banner::getBannerTypesArr($this->adminLangId));
        $this->set('linkTargetsArr', applicationConstants::getLinkTargetsArr($this->adminLangId));
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function bannerForm($blocation_id, $banner_id)
    {
        $blocation_id = FatUtility::int($blocation_id);
        if (1 > $blocation_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $frm = $this->getBannerForm();
        $data = ['banner_blocation_id' => $blocation_id, 'banner_id' => $banner_id];
        $banner_id = FatUtility::int($banner_id);
        if ($banner_id > 0) {
            $srch = Banner::getSearchObject($this->adminLangId, false);
            $srch->addCondition('banner_blocation_id', '=', $blocation_id);
            $srch->addCondition('banner_id', '=', $banner_id);
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $data = FatApp::getDb()->fetch($srch->getResultSet()) ?? [];
        }
        if ($banner_id == 0) {
            $data['banner_type'] = Banner::TYPE_BANNER;
        }
        $frm->fill($data);
        $this->set('frm', $frm);
        $this->set('languages', Language::getAllNames());
        $this->set('blocation_id', $blocation_id);
        $this->set('banner_id', $banner_id);
        $this->set('frmTax', $frm);
        $this->_template->render(false, false);
    }

    public function setupBanner()
    {
        $this->objPrivilege->canEditBanners();
        $frm = $this->getBannerForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $banner_id = $post['banner_id'];
        $record = new Banner($banner_id);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($banner_id > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Banner::getAttributesByLangId($langId, $banner_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $banner_id = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($banner_id)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('banner_id', $banner_id);
        $this->set('langId', $newTabLangId);
        $this->set('blocation_id', $post['banner_blocation_id']);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function bannerLangForm($blocation_id, $banner_id = 0, $lang_id = 0)
    {
        $blocation_id = FatUtility::int($blocation_id);
        $banner_id = FatUtility::int($banner_id);
        if (1 > $blocation_id || 1 > $banner_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $lang_id = FatUtility::int($lang_id);
        if ($banner_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $bannerLangFrm = $this->getBannerLangForm($blocation_id, $banner_id, $lang_id);
        $langData = Banner::getAttributesByLangId($lang_id, $banner_id);
        if ($langData) {
            $bannerLangFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('blocation_id', $blocation_id);
        $this->set('banner_id', $banner_id);
        $this->set('banner_lang_id', $lang_id);
        $this->set('bannerLangFrm', $bannerLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function bannerLocLangForm($blocationId, $lang_id = 0)
    {
        $blocationId = FatUtility::int($blocationId);
        if (1 > $blocationId) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $lang_id = FatUtility::int($lang_id);
        if ($lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $bannerLocLangFrm = $this->getBannerLocLangForm($blocationId, $lang_id);
        $langData = BannerLocation::getAttributesByLangId($lang_id, $blocationId);
        if ($langData) {
            $bannerLocLangFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('blocationId', $blocationId);
        $this->set('bannerLocaLangId', $lang_id);
        $this->set('bannerLocLangFrm', $bannerLocLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditBanners();
        $post = FatApp::getPostedData();
        $blocation_id = $post['blocation_id'];
        $banner_id = $post['banner_id'];
        $lang_id = $post['lang_id'];
        if ($banner_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getBannerLangForm($blocation_id, $banner_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $data = [
            'bannerlang_banner_id' => $banner_id,
            'bannerlang_lang_id' => $lang_id,
            'banner_title' => $post['banner_title'],
            'banner_description' => $post['banner_description'],
            'banner_btn_caption' => $post['banner_btn_caption'],
            'banner_btn_url' => $post['banner_btn_url'],
            'banner_video_caption' => $post['banner_video_caption'],
            'banner_video_url' => $post['banner_video_url']
        ];
        $bannerObj = new Banner($banner_id);
        if (!$bannerObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($bannerObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Banner::getAttributesByLangId($langId, $banner_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($banner_id)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('blocationId', $blocation_id);
        $this->set('bannerId', $banner_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langSetupLocation()
    {
        $this->objPrivilege->canEditBanners();
        $post = FatApp::getPostedData();
        $blocation_id = $post['blocation_id'];
        $lang_id = $post['lang_id'];
        if ($lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getBannerLocLangForm($blocation_id, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $data = [
            'blocationlang_blocation_id' => $blocation_id,
            'blocationlang_lang_id' => $lang_id,
            'blocation_name' => $post['blocation_name']
        ];
        $bannerObj = new BannerLocation($blocation_id);
        if (!$bannerObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($bannerObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = BannerLocation::getAttributesByLangId($langId, $blocation_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('blocationId', $blocation_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function mediaForm($blocation_id, $banner_id)
    {
        $blocation_id = FatUtility::int($blocation_id);
        $banner_id = FatUtility::int($banner_id);
        if (1 > $blocation_id || 1 > $banner_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $bannerDetail = Banner::getAttributesById($banner_id);
        if (!false == $bannerDetail && ($bannerDetail['banner_active'] != applicationConstants::ACTIVE)) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_request_Or_Inactive_Record', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        $mediaFrm = $this->getMediaForm($blocation_id, $banner_id);
        if (!false == $bannerDetail) {
            $bannerImgArr = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_BANNER, $banner_id, 0, -1);
            $this->set('bannerImgArr', $bannerImgArr);
        }
        $blocationData = $this->getBannerLocationById($blocation_id);
        $bannerWidth = FatUtility::convertToType($blocationData['blocation_banner_width'], FatUtility::VAR_FLOAT);
        $bannerHeight = FatUtility::convertToType($blocationData['blocation_banner_height'], FatUtility::VAR_FLOAT);
        $this->set('bannerWidth', $bannerWidth);
        $this->set('bannerHeight', $bannerHeight);
        $this->set('mediaFrm', $mediaFrm);
        $this->set('languages', Language::getAllNames());
        $this->set('bannerTypeArr', $this->bannerTypeArr());
        $this->set('screenTypeArr', $this->getDisplayScreenName());
        $this->set('blocation_id', $blocation_id);
        $this->set('banner_id', $banner_id);
        $this->_template->render(false, false);
    }

    public function images($blocation_id, $banner_id, $lang_id = 0, $screen = 0, $secondary = false)
    {
        $blocation_id = FatUtility::int($blocation_id);
        $banner_id = FatUtility::int($banner_id);
        if (1 > $blocation_id || 1 > $banner_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $bannerDetail = Banner::getAttributesById($banner_id);
        if (!false == $bannerDetail && ($bannerDetail['banner_active'] != applicationConstants::ACTIVE)) {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_request_Or_Inactive_Record', $this->adminLangId));
            FatUtility::dieWithError(Message::getHtml());
        }
        if ($secondary) {
            $imgType = AttachedFile::FILETYPE_BANNER_SECOND_IMAGE;
        } else {
            $imgType = AttachedFile::FILETYPE_BANNER;
        }
        if (!false == $bannerDetail) {
            $bannerImgArr = AttachedFile::getMultipleAttachments($imgType, $banner_id, 0, $lang_id, false, $screen);
            $this->set('images', $bannerImgArr);
        }
        $admin_id = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditBanners($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set('languages', Language::getAllNames());
        $this->set('screenTypeArr', $this->getDisplayScreenName());
        $this->set('blocation_id', $blocation_id);
        $this->set('banner_id', $banner_id);
        $this->set('secondary', $secondary);
        $this->_template->render(false, false);
    }

    public function upload($banner_id)
    {
        $this->objPrivilege->canEditBanners();
        $banner_id = FatUtility::int($banner_id);
        if (1 > $banner_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
        }
        $blocation_id = FatUtility::int($post['blocation_id']);
        $lang_id = FatUtility::int($post['lang_id']);
        $banner_screen = FatUtility::int($post['banner_screen']);
        if (1 > $blocation_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
        }
        if ($post['banner_image'] == 'banner_image_secondary') {
            $imgType = AttachedFile::FILETYPE_BANNER_SECOND_IMAGE;
        } else {
            $imgType = AttachedFile::FILETYPE_BANNER;
        }
        $fileHandlerObj = new AttachedFile();
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], $imgType, $banner_id, 0, $_FILES['file']['name'], -1, true, $lang_id, $banner_screen)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError($fileHandlerObj->getError());
        }
        $this->set('bannerId', $banner_id);
        $this->set('blocationId', $blocation_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeBanner($banner_id, $lang_id, $screen, $secondary = false)
    {
        $banner_id = FatUtility::int($banner_id);
        $lang_id = FatUtility::int($lang_id);
        if (1 > $banner_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if ($secondary) {
            $imgType = AttachedFile::FILETYPE_BANNER_SECOND_IMAGE;
        } else {
            $imgType = AttachedFile::FILETYPE_BANNER;
        }
        if (!$fileHandlerObj->deleteFile($imgType, $banner_id, 0, 0, $lang_id, $screen)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditBanners();
        $banner_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($banner_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $bannerObj = new Banner($banner_id);
        if (!$bannerObj->deleteRecord(true)) {
            Message::addErrorMessage($bannerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    private function isMediaUploaded($bannerId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_BANNER, $bannerId, 0)) {
            return true;
        }
        return false;
    }

    private function getBannerLocationById($bLocationId)
    {
        $bLocationId = FatUtility::int($bLocationId);
        $srch = Banner::getBannerLocationSrchObj(false);
        $srch->addCondition('blocation_id', '=', $bLocationId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        return FatApp::getDb()->fetch($srch->getResultSet()) ?? [];
    }

    private function getSearchForm()
    {
        $frm = new Form('frmBannerSearch');
        return $frm;
    }

    private function getLocationForm()
    {
        $frm = new Form('frmBannerLocation');
        $frm->addHiddenField('', 'blocation_id');
        $frm->addRequiredField(Label::getLabel('LBL_Banner_Location_Identifier', $this->adminLangId), 'blocation_identifier');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'blocation_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getListingSearchForm()
    {
        $frm = new Form('frmListingSearch');
        $frm->addTextBox('', 'keyword');
        $frm->addTextBox('', 'blocation_id');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        return $frm;
    }

    private function getBannerForm()
    {
        $frm = new Form('frmBanner');
        $frm->addHiddenField('', 'banner_blocation_id');
        $frm->addHiddenField('', 'banner_id');
        $frm->addHiddenField('', 'banner_type');
        $linkTargetsArr = applicationConstants::getLinkTargetsArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Open_In', $this->adminLangId), 'banner_target', $linkTargetsArr, '', [], '');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'banner_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    public function getBannerLangForm($blocation_id, $banner_id, $lang_id)
    {
        $frm = new Form('frmBannerLang');
        $frm->addHiddenField('', 'banner_id', $banner_id);
        $frm->addHiddenField('', 'blocation_id', $blocation_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Banner_Title', $this->adminLangId), 'banner_title');
        $frm->addTextArea(Label::getLabel('LBL_Banner_Description', $this->adminLangId), 'banner_description');
        $frm->addTextBox(Label::getLabel('LBL_Banner_Button_Caption', $this->adminLangId), 'banner_btn_caption');
        $frm->addTextBox(Label::getLabel('LBL_Banner_Button_Link', $this->adminLangId), 'banner_btn_url');
        $frm->addTextBox(Label::getLabel('LBL_Banner_Video_Caption', $this->adminLangId), 'banner_video_caption');
        $frm->addTextBox(Label::getLabel('LBL_Banner_Video_Link', $this->adminLangId), 'banner_video_url');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

    public function getBannerLocLangForm($blocation_id, $lang_id)
    {
        $frm = new Form('frmBannerLocLang');
        $frm->addHiddenField('', 'blocation_id', $blocation_id);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Banner_Location_Title', $this->adminLangId), 'blocation_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

    public function changeStatusBannerLocation()
    {
        $this->objPrivilege->canEditBanners();
        $blocationId = FatApp::getPostedData('blocationId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $blocationId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = BannerLocation::getAttributesById($blocationId, ['blocation_id', 'blocation_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new BannerLocation($blocationId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditBanners();
        $bannerId = FatApp::getPostedData('bannerId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $bannerId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Banner::getAttributesById($bannerId, ['banner_id', 'banner_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new Banner($bannerId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    private function getMediaForm($blocation_id, $banner_id = 0)
    {
        $frm = new Form('frmBannerMedia');
        $frm->addHiddenField('', 'banner_id', $banner_id);
        $frm->addHiddenField('', 'blocation_id', $blocation_id);
        $screenArr = applicationConstants::getDisplaysArr($this->adminLangId);
        $fld = $frm->addButton(Label::getLabel('LBL_Banner_Image', $this->adminLangId), 'banner_image', Label::getLabel('LBL_Upload_File', $this->adminLangId), [
            'class' => 'bannerFile-Js',
            'id' => 'banner_image',
            'data-banner_id' => $banner_id,
            'data-blocation_id' => $blocation_id
        ]);
        if ($blocation_id == BannerLocation::BLOCK_FIRST_AFTER_HOMESLIDER) {
            $fld = $frm->addButton(Label::getLabel('LBL_Banner_Image_(Small)', $this->adminLangId), 'banner_image_secondary', Label::getLabel('LBL_Upload_File', $this->adminLangId), [
                'class' => 'bannerFile-Js',
                'id' => 'banner_image_secondary',
                'data-banner_id' => $banner_id,
                'data-blocation_id' => $blocation_id
            ]);
        }
        return $frm;
    }

    private function bannerTypeArr()
    {
        return applicationConstants::bannerTypeArr();
    }

    private function getDisplayScreenName()
    {
        $screenTypesArr = applicationConstants::getDisplaysArr($this->adminLangId);
        return [0 => ''] + $screenTypesArr;
    }

    public function Thumb($bannerId, $langId = 0, $screen = 0, $secondary = false)
    {
        $this->showBanner($bannerId, $langId, 100, 100, $screen, $secondary);
    }

    public function showBanner($bannerId, $langId, $w = '200', $h = '200', $screen = 0, $secondary = false)
    {
        $bannerId = FatUtility::int($bannerId);
        $langId = FatUtility::int($langId);
        if ($secondary) {
            $imgType = AttachedFile::FILETYPE_BANNER_SECOND_IMAGE;
        } else {
            $imgType = AttachedFile::FILETYPE_BANNER;
        }
        $fileRow = AttachedFile::getAttachment($imgType, $bannerId, 0, $langId, true, $screen);
        $image_name = isset($fileRow['afile_physical_path']) ? $fileRow['afile_physical_path'] : '';
        AttachedFile::displayImage($image_name, $w, $h, '', '', ImageResize::IMG_RESIZE_EXTRA_ADDSPACE, false, true);
    }

}
