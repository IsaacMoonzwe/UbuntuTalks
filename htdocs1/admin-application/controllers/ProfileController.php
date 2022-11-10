<?php

class ProfileController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $this->_template->addJs('js/jquery.form.js');
        $this->_template->addJs('js/cropper.js');
        $this->_template->addCss('css/cropper.css');
        $this->_template->render();
    }

    public function profileInfoForm()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $imgFrm = $this->getImageForm();
        $admin_row = AdminUsers::getAttributesById($adminId);
        $frm = $this->getProfileInfoForm();
        $frm->fill($admin_row);
        $this->set('imgFrm', $imgFrm);
        $this->set('frm', $frm);
        $this->set('admin_row', $admin_row);
        $this->set('clss', 'chg_pass');
        $this->_template->render(false, false);
    }

    public function updateProfileInfo()
    {
        $frm = $this->getProfileInfoForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        unset($_SESSION[AdminAuthentication::SESSION_ELEMENT_NAME]['admin_name']);
        $_SESSION[AdminAuthentication::SESSION_ELEMENT_NAME]['admin_name'] = $post['admin_name'];
        $adminId = AdminAuthentication::getLoggedAdminId();
        $adminProfileObj = new AdminUsers($adminId);
        $adminProfileObj->assignValues($post);
        if (!$adminProfileObj->save()) {
            Message::addErrorMessage($adminProfileObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_successful', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getProfileInfoForm()
    {
        $frm = new Form('frmProfileInfo');
        $frm->addHiddenField('', ' admin_id', $this->admin_id, ['id' => 'admin_id']);
        $fld = $frm->addRequiredField(Label::getLabel('LBL_Username', $this->adminLangId), 'admin_username', '');
        $fld->setUnique('tbl_admin', 'admin_username', 'admin_id', 'admin_id', 'admin_id');
        $fld->requirements()->setUsername();
        $fld = $frm->addEmailField(Label::getLabel('LBL_Email', $this->adminLangId), 'admin_email', '');
        $fld->setUnique('tbl_admin', 'admin_email', 'admin_id', 'admin_id', 'admin_id');
        $frm->addRequiredField(Label::getLabel('LBL_Full_Name', $this->adminLangId), 'admin_name');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES', $this->adminLangId));
        return $frm;
    }

    private function getImageForm()
    {
        $frm = new Form('frmProfile', ['id' => 'frmProfile']);
        $frm->addFileUpload(Label::getLabel('LBL_Profile_Picture', $this->adminLangId), 'user_profile_image', ['id' => 'user_profile_image', 'onchange' => 'popupImage(this)', 'accept' => 'image/*']);
        $frm->addHiddenField('', 'update_profile_img', Label::getLabel('LBL_Update_Profile_Picture', $this->adminLangId), ['id' => 'update_profile_img']);
        $frm->addHiddenField('', 'rotate_left', Label::getLabel('LBL_Rotate_Left', $this->adminLangId), ['id' => 'rotate_left']);
        $frm->addHiddenField('', 'rotate_right', Label::getLabel('LBL_Rotate_Right', $this->adminLangId), ['id' => 'rotate_right']);
        $frm->addHiddenField('', 'remove_profile_img', 0, ['id' => 'remove_profile_img']);
        $frm->addHiddenField('', 'action', 'avatar', ['id' => 'avatar-action']);
        $frm->addHiddenField('', 'img_data', '', ['id' => 'img_data']);
        return $frm;
    }

    public function uploadProfileImage()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request_Or_File_not_supported', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        if ($post['action'] == "demo_avatar") {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                Message::addErrorMessage(Label::getLabel('MSG_Please_select_a_file', $this->adminLangId));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_ADMIN_PROFILE_IMAGE, $adminId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('file', CommonHelper::generateFullUrl('profile', 'profileImage', [$adminId]) . '?' . time());
        }
        if ($post['action'] == "avatar") {
            if (!is_uploaded_file($_FILES['user_profile_image']['tmp_name'])) {
                Message::addErrorMessage(Label::getLabel('MSG_Please_select_a_file', $this->adminLangId));
                FatUtility::dieJsonError(Message::getHtml());
            }
            $fileHandlerObj = new AttachedFile();
            if (!$res = $fileHandlerObj->saveImage($_FILES['user_profile_image']['tmp_name'], AttachedFile::FILETYPE_ADMIN_PROFILE_CROPED_IMAGE, $adminId, 0, $_FILES['user_profile_image']['name'], -1, $unique_record = true)) {
                Message::addErrorMessage($fileHandlerObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $data = json_decode(stripslashes($post['img_data']));
            CommonHelper::crop($data, CONF_UPLOADS_PATH . $res, $this->adminLangId);
            $this->set('file', CommonHelper::generateFullUrl('profile', 'profileImage', [$adminId, 'croped', true]) . '?' . time());
        }
        $this->set('msg', Label::getLabel('MSG_File_uploaded_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function profileImage($adminId, $sizeType = '', $cropedImage = false)
    {
        $default_image = 'user_deafult_image.jpg';
        $recordId = FatUtility::int($adminId);
        if ($cropedImage == true) {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ADMIN_PROFILE_CROPED_IMAGE, $recordId);
        } else {
            $file_row = AttachedFile::getAttachment(AttachedFile::FILETYPE_ADMIN_PROFILE_IMAGE, $recordId);
        }
        $image_name = isset($file_row['afile_physical_path']) ? $file_row['afile_physical_path'] : '';
        switch (strtoupper($sizeType)) {
            case 'THUMB':
                $w = 100;
                $h = 100;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            case 'CROPED':
                $w = 230;
                $h = 230;
                AttachedFile::displayImage($image_name, $w, $h, $default_image);
                break;
            default:
                AttachedFile::displayOriginalImage($image_name, $default_image);
                break;
        }
    }

    public function removeProfileImage()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_ADMIN_PROFILE_IMAGE, $adminId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!$fileHandlerObj->deleteFile(AttachedFile::FILETYPE_ADMIN_PROFILE_CROPED_IMAGE, $adminId)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (CONF_USE_FAT_CACHE) {
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'ORIGINAL')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'SMALL')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId, 'EXTRASMALL')));
            FatCache::delete(CommonHelper::generateUrl('Image', 'user', array($userId)));
        }
        $this->set('msg', Label::getLabel('MSG_File_deleted_successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function getBreadcrumbNodes($action)
    {
        $nodes = [];
        $className = get_class($this);
        $arr = explode('-', FatUtility::camel2dashed($className));
        array_pop($arr);
        $className = ucwords(implode(' ', $arr));
        if ($action == 'index') {
            $nodes[] = ['title' => $className];
        } else {
            $nodes[] = ['title' => $action];
        }
        return $nodes;
    }

    public function changePassword()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $pwdFrm = $this->getPwdFrm();
        $pwdFrm->setFormTagAttribute('id', 'getPwdFrm');
        $pwdFrm->setFormTagAttribute('class', 'web_form');
        $pwdFrm->setRequiredStarPosition('none');
        $pwdFrm->setValidatorJsObjectName('changeValidator');
        $pwdFrm->setFormTagAttribute("action", '');
        $pwdFrm->setFormTagAttribute('onsubmit', 'changePassword(this, changeValidator); return false;');
        $this->set('pwdFrm', $pwdFrm);
        $this->set('clss', 'chg_pass');
        $this->_template->render();
    }

    public function updatePassword()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $adminProfileObj = new AdminUsers($adminId);
        $pwdFrm = $this->getPwdFrm();
        $post = $pwdFrm->getFormDataFromArray(FatApp::getPostedData());
        if (!$pwdFrm->validate($post)) {
            FatUtility::dieJsonError($pwdFrm->getValidationErrors());
        }
        if (!$curDbPassword = AdminUsers::getAttributesById($adminId, 'admin_password')) {
            FatUtility::dieJsonError($adminProfileObj->getError());
        }
        $newPassword = UserAuthentication::encryptPassword(FatApp::getPostedData('new_password'));
        $currentPassword = UserAuthentication::encryptPassword(FatApp::getPostedData('current_password'));
        if ($curDbPassword != $currentPassword) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Your_current_Password_mis-matched!', $this->adminLangId));
        }
        $data = ['admin_password' => $newPassword];
        $adminProfileObj->assignValues($data);
        if (!$adminProfileObj->save()) {
            FatUtility::dieJsonError($adminProfileObj->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Password_Updated_Successfully', $this->adminLangId));
    }

    public function logout()
    {
        AdminAuthentication::clearLoggedAdminLoginCookie();
        session_destroy();
        Message::addMessage(Label::getLabel('LBL_You_Are_Logged_Out_Successfully', $this->adminLangId));
        FatApplication::redirectUser(CommonHelper::generateUrl('adminGuest', 'loginForm'));
    }

    public function themeSetup()
    {
        $post = FatApp::getPostedData();
        $session_element_name = AdminAuthentication::SESSION_ELEMENT_NAME;
        $cookie_name = $session_element_name . 'layout';
        if (CommonHelper::setCookie($cookie_name, $post['layout'], time() + 86400 * 30, CONF_WEBROOT_FRONTEND, '', true)) {
            Message::addMessage(Label::getLabel('LBL_Setting_Updated_Successfully', $this->adminLangId));
        } else {
            Message::addErrorMessage($this->str_invalid_request);
        }
        FatUtility::dieJsonError(Message::getHtml());
    }

    private function getPwdFrm()
    {
        $frm = new Form('getPwdFrm');
        $curPwd = $frm->addPasswordField(Label::getLabel('LBL_Current_Password', $this->adminLangId), 'current_password', '', ['id' => 'current_password']);
        $curPwd->requirements()->setRequired();
        $newPwd = $frm->addPasswordField(Label::getLabel('LBL_New_Password', $this->adminLangId), 'new_password', '', ['id' => 'new_password']);
        $newPwd->requirements()->setRequired();
        $newPwd->requirements()->setPassword();
        $conNewPwd = $frm->addPasswordField(Label::getLabel('LBL_Confirm_New_Password', $this->adminLangId), 'conf_new_password', '', ['id' => 'conf_new_password']);
        $conNewPwdReq = $conNewPwd->requirements();
        $conNewPwdReq->setRequired();
        $conNewPwdReq->setCompareWith('new_password', 'eq');
        $conNewPwdReq->setCustomErrorMessage(Label::getLabel('LBL_Confirm_Password_Not_Matched!', $this->adminLangId));
        $frm->addSubmitButton(Label::getLabel('LBL_Change', $this->adminLangId), 'btn_submit', Label::getLabel('LBL_Change', $this->adminLangId), ['id' => 'btn_submit']);
        return $frm;
    }

}
