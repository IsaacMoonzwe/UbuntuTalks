<?php

class AdminUsersController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewAdminUsers();
    }

    public function index()
    {
        $this->_template->render();
    }

    public function search()
    {
        $srch = AdminUsers::getSearchObject(false);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditAdminUsers($this->admin_id, true);
        $canViewAdminPermissions = $this->objPrivilege->canViewAdminPermissions($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("canViewAdminPermissions", $canViewAdminPermissions);
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->set("arr_listing", $records);
        $this->set('recordCount', $srch->recordCount());
        $this->set('adminLoggedInId', $this->admin_id);
        $this->_template->render(false, false);
    }

    public function form($adminId = 0)
    {
        $adminId = FatUtility::int($adminId);
        $frm = $this->getForm($adminId);
        if (0 < $adminId) {
            $data = AdminUsers::getAttributesById($adminId);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            $frm->fill($data);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('admin_id', $adminId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditAdminUsers();
        $post = FatApp::getPostedData();
        $adminId = FatUtility::int($post['admin_id']);
        $frm = $this->getForm($adminId);
        $post = $frm->getFormDataFromArray($post);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $record = new AdminUsers($adminId);
        if ($adminId == 0) {
            $password = $post['password'];
            $encryptedPassword = UserAuthentication::encryptPassword($password);
            $post['admin_password'] = $encryptedPassword;
        }
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('adminId', $adminId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changePassword($adminId = 0)
    {
        $adminId = FatUtility::int($adminId);
        $frm = $this->getChangePasswordForm($adminId);
        if (0 >= $adminId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = AdminUsers::getAttributesById($adminId);
        $this->set('admin_id', $adminId);
        $this->set('adminProfile', $data);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setupChangePassword()
    {
        $this->objPrivilege->canEditAdminUsers();
        $post = FatApp::getPostedData();
        $adminId = FatUtility::int($post['admin_id']);
        unset($post['admin_id']);
        if (0 >= $adminId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getChangePasswordForm($adminId);
        $post = $frm->getFormDataFromArray($post);
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $record = new AdminUsers($adminId);
        $password = $post['password'];
        $encryptedPassword = UserAuthentication::encryptPassword($password);
        $post['admin_password'] = $encryptedPassword;
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Password_Changed_Successfully', $this->adminLangId));
        $this->set('adminId', $adminId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditAdminUsers();
        $adminId = FatApp::getPostedData('adminId', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        if (1 >= $adminId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = AdminUsers::getAttributesById($adminId, ['admin_id', 'admin_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $adminObj = new AdminUsers($adminId);
        if (!$adminObj->changeStatus($status)) {
            Message::addErrorMessage($adminObj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_update_record);
    }

    public function permissions($adminId = 0)
    {
        $this->objPrivilege->canViewAdminPermissions();
        $adminId = FatUtility::int($adminId);
        if (1 > $adminId || $adminId == 1 || $adminId == $this->admin_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatApp::redirectUser(CommonHelper::generateUrl('adminUsers'));
        }
        $frm = $this->searchForm();
        $allAccessfrm = $this->getAllAccessForm();
        $data = AdminUsers::getAttributesById($adminId);
        $frm->fill(['admin_id' => $adminId]);
        $this->set('frm', $frm);
        $this->set('admin_id', $adminId);
        $this->set('allAccessfrm', $allAccessfrm);
        $this->set('data', $data);
        $this->_template->render();
    }

    public function roles()
    {
        $this->objPrivilege->canViewAdminPermissions();
        $frmSearch = $this->searchForm();
        $post = $frmSearch->getFormDataFromArray(FatApp::getPostedData());
        $adminId = FatUtility::int($post['admin_id']);
        $userData = [];
        if ($adminId > 0) {
            $userData = AdminUsers::getUserPermissions($adminId);
        }
        $permissionModules = AdminPrivilege::getPermissionModulesArr();
        $this->set('arr_listing', $permissionModules);
        $this->set('userData', $userData);
        $this->set('canViewAdminPermissions', $this->objPrivilege->canViewAdminPermissions($this->admin_id, true));
        $this->_template->render(false, false);
    }

    public function updatePermission($moduleId, $permission)
    {
        $this->objPrivilege->canEditAdminPermissions();
        $moduleId = FatUtility::int($moduleId);
        $permission = FatUtility::int($permission);
        $frmSearch = $this->searchForm();
        $post = $frmSearch->getFormDataFromArray(FatApp::getPostedData());
        $adminId = FatUtility::int($post['admin_id']);
        if (2 > $adminId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $data = ['admperm_admin_id' => $adminId, 'admperm_section_id' => $moduleId, 'admperm_value' => $permission];
        $obj = new AdminUsers();
        if ($moduleId == 0) {
            if (!$obj->updatePermissions($data, true)) {
                Message::addErrorMessage($obj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
        } else {
            $permissionModules = AdminPrivilege::getPermissionModulesArr();
            $permissionArr = AdminPrivilege::getPermissionArr();
            if (!array_key_exists($moduleId, $permissionModules) || !array_key_exists($permission, $permissionArr)) {
                Message::addErrorMessage($this->str_invalid_request);
                FatUtility::dieJsonError(Message::getHtml());
            }
            if (!$obj->updatePermissions($data)) {
                Message::addErrorMessage($obj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
        }
        $this->set('moduleId', $moduleId);
        $this->set('msg', Label::getLabel('MSG_Updated_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    private function searchForm()
    {
        $frm = new Form('frmAdminSrchFrm');
        $frm->addHiddenField('', 'admin_id');
        return $frm;
    }

    private function getForm($adminId = 0)
    {
        $adminId = FatUtility::int($adminId);
        $frm = new Form('frmAdminUser');
        $frm->addHiddenField('', 'admin_id', $adminId, ['id' => 'admin_id']);
        $frm->addRequiredField(Label::getLabel('LBL_Full_Name', $this->adminLangId), 'admin_name');
        $fld = $frm->addTextBox(Label::getLabel('LBL_Username', $this->adminLangId), 'admin_username', '');
        $fld->setUnique(AdminUsers::DB_TBL, 'admin_username', 'admin_id', 'admin_id', 'admin_id');
        $fld->requirements()->setRequired();
        $fld->requirements()->setUsername();
        $emailFld = $frm->addEmailField(Label::getLabel('LBL_Email', $this->adminLangId), 'admin_email', '');
        $emailFld->setUnique(AdminUsers::DB_TBL, 'admin_email', 'admin_id', 'admin_id', 'admin_id');
        if ($adminId == 0) {
            $fld = $frm->addPasswordField(Label::getLabel('LBL_Password', $this->adminLangId), 'password');
            $fld->requirements()->setRequired();
            $fld->requirements()->setPassword();
            $fld = $frm->addPasswordField(Label::getLabel('LBL_Confirm_Password', $this->adminLangId), 'confirm_password');
            $fld->requirements()->setRequired();
            $fld->requirements()->setCompareWith('password', 'eq', '');
        }
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'admin_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getAllAccessForm()
    {
        $permissionArr = AdminPrivilege::getPermissionArr();
        $frm = new Form('frmAllAccess');
        $frm->setFormTagAttribute('class', 'web_form form_horizontal');
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Select_permission_for_all_modules', $this->adminLangId), 'permissionForAll', $permissionArr, '', ['class' => 'permissionForAll'], Label::getLabel('LBL_Select', $this->adminLangId));
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Apply_to_All', $this->adminLangId), ['onclick' => 'updatePermission(0);return false;']);
        return $frm;
    }

    private function getChangePasswordForm($adminId)
    {
        $frm = new Form('frmAdminUserChangePassword');
        $frm->addHiddenField('', 'admin_id', $adminId);
        $fld = $frm->addPasswordField(Label::getLabel('LBL_New_Password', $this->adminLangId), 'password');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setPassword();
        $fld = $frm->addPasswordField(Label::getLabel('LBL_Confirm_Password', $this->adminLangId), 'confirm_password');
        $fld->requirements()->setRequired();
        $fld->requirements()->setCompareWith('password', 'eq', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
