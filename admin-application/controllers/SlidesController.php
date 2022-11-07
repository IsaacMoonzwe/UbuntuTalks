<?php

class SlidesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewSlides();
        FatApp::redirectUser(FatUtility::generateUrl('Configurations'));
    }

    public function index()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditSlides($adminId, true);
        $frmSearch = $this->getSearchForm();
        $this->set("canEdit", $canEdit);
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditSlides($adminId, true);
        $post = FatApp::getPostedData();
        $searchForm = $this->getSearchForm();
        $post = $searchForm->getFormDataFromArray($post);
        $srch = Slide::getSearchObject($this->adminLangId, false);
        $srch->addCondition('slide_type', '=', Slide::TYPE_SLIDE);
        $srch->addOrder(Slide::DB_TBL_PREFIX . 'active', 'DESC');
        $srch->addOrder('slide_display_order', 'ASC');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $arrListing = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("arrListing", $arrListing);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('postedData', $post);
        $this->set("canEdit", $canEdit);
        $this->_template->render(false, false);
    }

    public function form($slide_id = 0)
    {
        $slide_id = FatUtility::int($slide_id);
        $slideFrm = $this->getForm();
        if (0 < $slide_id) {
            $data = Slide::getAttributesById($slide_id);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $slideFrm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('slide_id', $slide_id);
        $this->set('slideFrm', $slideFrm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditSlides();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $slide_id = $post['slide_id'];
        unset($post['slide_id']);
        $recordObj = new Slide($slide_id);
        $recordObj->assignValues($post);
        if (!$recordObj->save()) {
            Message::addErrorMessage($recordObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($slide_id > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = Slide::getAttributesByLangId($langId, $slide_id)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $slide_id = $recordObj->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($slide_id)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('slideId', $slide_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($slide_id = 0, $lang_id = 0)
    {
        $slide_id = FatUtility::int($slide_id);
        $lang_id = FatUtility::int($lang_id);
        if ($slide_id == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $slideLangFrm = $this->getLangForm($lang_id);
        $langData = Slide::getAttributesByLangId($lang_id, $slide_id);
        $langData['slide_id'] = $slide_id;
        $slideLangFrm->fill($langData);
        $slideBanner = AttachedFile::getAttachment(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, $lang_id);
        $this->set('slideBanner', $slideBanner);
        $this->set('languages', Language::getAllNames());
        $this->set('slide_id', $slide_id);
        $this->set('slide_lang_id', $lang_id);
        $this->set('slideLangFrm', $slideLangFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditSlides();
        $post = FatApp::getPostedData();
        $slide_id = $post['slide_id'];
        $lang_id = $post['lang_id'];
        if ($slide_id == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['slide_id']);
        unset($post['lang_id']);
        $data = [
            'slidelang_slide_id' => $slide_id,
            'slidelang_lang_id' => $lang_id,
            'slide_title' => $post['slide_title']
        ];
        $slideObj = new Slide($slide_id);
        if (!$slideObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($slideObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = Slide::getAttributesByLangId($langId, $slide_id)) {
                $newTabLangId = $langId;
                break;
            }
        }
        if ($newTabLangId == 0 && !$this->isMediaUploaded($slide_id)) {
            $this->set('openMediaForm', true);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('slideId', $slide_id);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function mediaForm($slide_id)
    {
        $slide_id = FatUtility::int($slide_id);
        $slideMediaFrm = $this->getMediaForm($slide_id);
        $this->set('slide_id', $slide_id);
        $this->set('slideMediaFrm', $slideMediaFrm);
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    public function images($slide_id, $slide_screen = 0, $lang_id = 0)
    {
        $slide_id = FatUtility::int($slide_id);
        $slideDetail = Slide::getAttributesById($slide_id);
        if (false == $slideDetail) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        if (!false == $slideDetail) {
            $slideBanner = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, $lang_id, false, $slide_screen);
            $this->set('images', $slideBanner);
        }
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditSlides($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set('slide_id', $slide_id);
        $this->set('bannerTypeArr', $this->bannerTypeArr());
        $this->set('screenTypeArr', $this->getDisplayScreenName());
        $this->set('languages', Language::getAllNames());
        $this->_template->render(false, false);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditSlides();
        $slide_id = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($slide_id < 1) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $obj = new Slide($slide_id);
        if (!$obj->deleteRecord(true)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        $fileHandlerObj->deleteFile(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id);
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function setUpImage($slide_id)
    {
        $this->objPrivilege->canEditSlides();
        $slide_id = FatUtility::int($slide_id);
        if (1 > $slide_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $post = FatApp::getPostedData();
        $lang_id = FatUtility::int($post['lang_id']);
        $slide_screen = FatUtility::int($post['slide_screen']);
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_Select_A_File', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$res = $fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, $_FILES['file']['name'], -1, true, $lang_id, $slide_screen)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (CONF_USE_FAT_CACHE) {
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $slide_screen, $lang_id, 'MOBILE')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $slide_screen, $lang_id, 'TABLET')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $slide_screen, $lang_id, 'DESKTOP')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $slide_screen, $lang_id, 'THUMB')));
        }

        $this->set('slideId', $slide_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function removeImage($slide_id, $lang_id, $screen)
    {
        $slide_id = FatUtility::int($slide_id);
        $lang_id = FatUtility::int($lang_id);
        if (1 > $slide_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, 0, $lang_id, $screen)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (CONF_USE_FAT_CACHE) {
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $screen, $lang_id, 'MOBILE')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $screen, $lang_id, 'TABLET')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $screen, $lang_id, 'DESKTOP')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'slide', array($slide_id, $screen, $lang_id, 'THUMB')));
        }
        $this->set('msg', Label::getLabel('MSG_Deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updateOrder()
    {
        $this->objPrivilege->canEditSlides();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $slideObj = new Slide();
            if (!$slideObj->updateOrder($post['slideList'])) {
                Message::addErrorMessage($slideObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            FatUtility::dieJsonSuccess(Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
        }
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditSlides();
        $slideId = FatApp::getPostedData('slideId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (0 >= $slideId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = Slide::getAttributesById($slideId, ['slide_id', 'slide_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $obj = new Slide($slideId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    private function isMediaUploaded($slideId)
    {
        if ($attachment = AttachedFile::getAttachment(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slideId, 0)) {
            return true;
        }
        return false;
    }

    private function getForm()
    {
        $frm = new Form('frmSlide');
        $frm->addHiddenField('', 'slide_id');
        $frm->addHiddenField('', 'slide_type', Slide::TYPE_SLIDE);
        $frm->addRequiredField(Label::getLabel('LBL_Slide_Identifier', $this->adminLangId), 'slide_identifier');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Slide_URL', $this->adminLangId), 'slide_url');
        $fld->setFieldTagAttribute('placeholder', 'http://');
        $linkTargetsArr = applicationConstants::getLinkTargetsArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Open_In', $this->adminLangId), 'slide_target', $linkTargetsArr, '', [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'slide_active', applicationConstants::getActiveInactiveArr($this->adminLangId), applicationConstants::ACTIVE, [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($lang_id = 0)
    {
        $frm = new Form('frmSlideLang');
        $frm->addHiddenField('', 'slide_id');
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Slide_Title', $this->adminLangId), 'slide_title');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Update', $this->adminLangId));
        return $frm;
    }

    private function getMediaForm($slide_id = 0)
    {
        $frm = new Form('frmSlideMedia');
        $frm->addHiddenField('', 'slide_id', $slide_id);
        $bannerTypeArr = $this->bannerTypeArr();
        $frm->addSelectBox(Label::getLabel('LBL_Language', $this->adminLangId), 'lang_id', $bannerTypeArr, '', [], '');
        $screenArr = applicationConstants::getDisplaysArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel("LBL_Display_For", $this->adminLangId), 'slide_screen', $screenArr, '', [], '');
        $frm->addButton(Label::getLabel("LBL_Slide_Banner_Image", $this->adminLangId), 'slide_image', Label::getLabel("LBL_Upload_File", $this->adminLangId), ['class' => 'slideFile-Js', 'id' => 'slide_image', 'data-slide_id' => $slide_id]);
        return $frm;
    }

    private function getSearchForm()
    {
        return new Form('frmSlideSearch', ['id' => 'frmSlideSearch']);
    }

    private function bannerTypeArr()
    {
        return applicationConstants::bannerTypeArr();
    }

    private function getDisplayScreenName()
    {
        $screenTypesArr = applicationConstants::getDisplaysArr($this->adminLangId);
        return array(0 => '') + $screenTypesArr;
    }

    public function slide($slide_id, $screen = 0, $lang_id, $sizeType = '', $displayUniversalImage = true)
    {
        $default_image = 'brand_deafult_image.jpg';
        $slide_id = FatUtility::int($slide_id);
        $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide_id, 0, $lang_id, $displayUniversalImage, $screen);
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        $cacheKey = $_SERVER['REQUEST_URI'];
        $str = FatCache::get($cacheKey, null, '.jpg');
        if (false == $str && !CONF_USE_FAT_CACHE) {
            $cacheKey = false;
        }
        if ($sizeType) {
            switch (strtoupper($sizeType)) {
                case 'THUMB':
                    $w = 200;
                    $h = 100;
                    AttachedFile::displayImage($image_name, $w, $h, $default_image);
                    break;
                default:
                    $w = 2000;
                    $h = 360;
                    AttachedFile::displayImage($image_name, $w, $h, $default_image);
                    break;
            }
        } else {
            AttachedFile::displayOriginalImage($image_name, $default_image);
        }
    }

}
