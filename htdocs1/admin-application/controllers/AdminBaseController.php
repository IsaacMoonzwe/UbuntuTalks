<?php

class AdminBaseController extends FatController
{

    protected $objPrivilege;
    protected $unAuthorizeAccess;
    protected $admin_id;
    protected $str_add_record;
    protected $str_update_record;
    protected $str_export_successfull;
    protected $str_no_record;
    protected $str_invalid_request;
    protected $str_invalid_request_id;
    protected $str_delete_record;
    protected $str_invalid_Action;
    protected $str_setup_successful;
    protected $adminLangId;

    public function __construct($action)
    {
        parent::__construct($action);
        $controllerName = get_class($this);
        $arr = explode('-', FatUtility::camel2dashed($controllerName));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $controllerName = ucfirst(FatUtility::dashed2Camel($urlController));
        if ($controllerName != 'AdminGuest') {
            $_SESSION['admin_referer_page_url'] = CommonHelper::getCurrUrl();
        }
        CommonHelper::initCommonVariables(true);
        if (!AdminAuthentication::isAdminLogged()) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieWithError(Label::getLabel('LBL_YOUR_SESSION_SEEMS_TO_BE_EXPIRED', CommonHelper::getLangId()));
            }
            FatApp::redirectUser(CommonHelper::generateUrl('AdminGuest', 'loginForm'));
        }
        $this->objPrivilege = AdminPrivilege::getInstance();
        $this->admin_id = AdminAuthentication::getLoggedAdminId();
        if (!FatUtility::isAjaxCall()) {
            $session_element_name = AdminAuthentication::SESSION_ELEMENT_NAME;
            $cookie_name = $session_element_name . 'layout';
            //@todo-ask::: Confirm about the usage of $_COOKIE.
            $selected_admin_dashboard_layout = FatUtility::int($_COOKIE[$cookie_name] ?? 0);
            $this->set('selected_admin_dashboard_layout', $selected_admin_dashboard_layout);
            $admin_dashboard_layouts = Admin::$admin_dashboard_layouts;
            $this->set('admin_dashboard_layouts', $admin_dashboard_layouts);
        }
        $this->set("bodyClass", '');
        $this->setCommonValues();
    }

    private function setCommonValues()
    {
        $this->adminLangId = CommonHelper::getLangId();
        $this->layoutDirection = CommonHelper::getLayoutDirection();
        $this->unAuthorizeAccess = Label::getLabel('LBL_Unauthorized_Access', $this->adminLangId);
        $this->str_add_record = Label::getLabel('LBL_Record_Added_Successfully', $this->adminLangId);
        $this->str_update_record = Label::getLabel('LBL_Record_Updated_Successfully', $this->adminLangId);
        $this->str_no_record = Label::getLabel('LBL_No_Record_Found', $this->adminLangId);
        $this->str_invalid_request_id = Label::getLabel('LBL_Invalid_Request_Id', $this->adminLangId);
        $this->str_invalid_request = Label::getLabel('LBL_Invalid_Request', $this->adminLangId);
        $this->str_delete_record = Label::getLabel('LBL_Record_Deleted_Successfully', $this->adminLangId);
        $this->str_invalid_Action = Label::getLabel('LBL_Invalid_Action', $this->adminLangId);
        $this->str_setup_successful = Label::getLabel('LBL_Setup_Successful', $this->adminLangId);
        $this->str_export_successfull = Label::getLabel('LBL_Export_Successful', $this->adminLangId);
        $this->str_add_update_record = $this->str_update_record;
        $jsVariables = [
            'confirmRemove' => Label::getLabel('LBL_Do_you_want_to_remove', $this->adminLangId),
            'confirmRemoveOption' => Label::getLabel('LBL_Do_you_want_to_remove_this_option', $this->adminLangId),
            'confirmRemoveShop' => Label::getLabel('LBL_Do_you_want_to_remove_this_shop', $this->adminLangId),
            'confirmRemoveProduct' => Label::getLabel('LBL_Do_you_want_to_remove_this_product', $this->adminLangId),
            'confirmRemoveCategory' => Label::getLabel('LBL_Do_you_want_to_remove_this_category', $this->adminLangId),
            'confirmReset' => Label::getLabel('LBL_Do_you_want_to_reset_settings', $this->adminLangId),
            'confirmActivate' => Label::getLabel('LBL_Do_you_want_to_activate_status', $this->adminLangId),
            'confirmUpdate' => Label::getLabel('LBL_Do_you_want_to_update', $this->adminLangId),
            'confirmUpdateStatus' => Label::getLabel('LBL_Do_you_want_to_update', $this->adminLangId),
            'confirmDelete' => Label::getLabel('LBL_Do_you_want_to_delete', $this->adminLangId),
            'confirmDeleteImage' => Label::getLabel('LBL_Do_you_want_to_delete_image', $this->adminLangId),
            'confirmDeleteBackgroundImage' => Label::getLabel('LBL_Do_you_want_to_delete_background_image', $this->adminLangId),
            'confirmDeleteLogo' => Label::getLabel('LBL_Do_you_want_to_delete_logo', $this->adminLangId),
            'confirmDeleteBanner' => Label::getLabel('LBL_Do_you_want_to_delete_banner', $this->adminLangId),
            'confirmDeleteIcon' => Label::getLabel('LBL_Do_you_want_to_delete_icon', $this->adminLangId),
            'confirmDefault' => Label::getLabel('LBL_Do_you_want_to_set_default', $this->adminLangId),
            'setMainProduct' => Label::getLabel('LBL_Set_as_main_product', $this->adminLangId),
            'layoutDirection' => CommonHelper::getLayoutDirection(),
            'selectPlan' => Label::getLabel('LBL_Please_Select_any_Plan', $this->adminLangId),
            'alreadyHaveThisPlan' => Label::getLabel('LBL_You_have_already_Bought_this_plan', $this->adminLangId),
            'invalidRequest' => Label::getLabel('LBL_Invalid_Request!', $this->adminLangId),
            'pleaseWait' => Label::getLabel('LBL_Please_Wait...', $this->adminLangId),
            'DoYouWantTo' => Label::getLabel('LBL_Do_you_really_want_to', $this->adminLangId),
            'theRequest' => Label::getLabel('LBL_the_request', $this->adminLangId),
            'confirmCancelOrder' => Label::getLabel('LBL_Are_you_sure_to_cancel_this_order', $this->adminLangId),
            'confirmReplaceCurrentToDefault' => Label::getLabel('LBL_Do_you_want_to_replace_current_content_to_default_content', $this->adminLangId),
            'processing' => Label::getLabel('LBL_Processing...', $this->adminLangId),
            'preferredDimensions' => Label::getLabel('LBL_Preferred_Dimensions_%s', $this->adminLangId),
            'confirmRestore' => Label::getLabel('LBL_Do_you_want_to_restore', $this->adminLangId),
            'thanksForSharing' => Label::getLabel('LBL_Msg_Thanks_for_sharing', $this->adminLangId),
            'isMandatory' => Label::getLabel('VLBL_is_mandatory', $this->adminLangId),
            'pleaseEnterValidEmailId' => Label::getLabel('VLBL_Please_enter_valid_email_ID_for', $this->adminLangId),
            'charactersSupportedFor' => Label::getLabel('VLBL_Only_characters_are_supported_for', $this->adminLangId),
            'pleaseEnterIntegerValue' => Label::getLabel('VLBL_Please_enter_integer_value_for', $this->adminLangId),
            'pleaseEnterNumericValue' => Label::getLabel('VLBL_Please_enter_numeric_value_for', $this->adminLangId),
            'startWithLetterOnlyAlphanumeric' => Label::getLabel('VLBL_startWithLetterOnlyAlphanumeric', $this->adminLangId),
            'mustBeBetweenCharacters' => Label::getLabel('VLBL_Length_Must_be_between_6_to_20_characters', $this->adminLangId),
            'invalidValues' => Label::getLabel('VLBL_Length_Invalid_value_for', $this->adminLangId),
            'shouldNotBeSameAs' => Label::getLabel('VLBL_should_not_be_same_as', $this->adminLangId),
            'mustBeSameAs' => Label::getLabel('VLBL_must_be_same_as', $this->adminLangId),
            'mustBeGreaterOrEqual' => Label::getLabel('VLBL_must_be_greater_than_or_equal_to', $this->adminLangId),
            'mustBeGreaterThan' => Label::getLabel('VLBL_must_be_greater_than', $this->adminLangId),
            'mustBeLessOrEqual' => Label::getLabel('VLBL_must_be_less_than_or_equal_to', $this->adminLangId),
            'mustBeLessThan' => Label::getLabel('VLBL_must_be_less_than', $this->adminLangId),
            'lengthOf' => Label::getLabel('VLBL_Length_of', $this->adminLangId),
            'valueOf' => Label::getLabel('VLBL_Value_of', $this->adminLangId),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between', $this->adminLangId),
            'mustBeBetween' => Label::getLabel('VLBL_must_be_between', $this->adminLangId),
            'and' => Label::getLabel('VLBL_and', $this->adminLangId),
            'pleaseSelect' => Label::getLabel('VLBL_Please_select', $this->adminLangId),
            'to' => Label::getLabel('VLBL_to', $this->adminLangId),
            'options' => Label::getLabel('VLBL_options', $this->adminLangId),
            'confirmRestoreBackup' => Label::getLabel('LBL_Do_you_want_to_restore_database_to_this_record', $this->adminLangId),
            'confirmCancel' => Label::getLabel('LBL_Do_you_want_to_cancel'),
            'today' => Label::getLabel('LBL_Today'),
            'prev' => Label::getLabel('LBL_Prev'),
            'next' => Label::getLabel('LBL_Next'),
            'done' => Label::getLabel('Done'),
            'teachingLangUpdNotice' => Label::getLabel('LBL_Teaching_LANGUAGE_UPDATE_NOTICE'),
        ];
        $languages = Language::getAllNames(false);
        foreach ($languages as $val) {
            $jsVariables['language' . $val['language_id']] = $val['language_layout_direction'];
        }
        $this->siteDefaultCurrencyCode = CommonHelper::getCurrencyCode();
        $this->set('adminLangId', $this->adminLangId);
        $this->set('siteDefaultCurrencyCode', $this->siteDefaultCurrencyCode);
        $this->set('jsVariables', $jsVariables);
        $this->set('languages', $languages);
        $this->set('layoutDirection', $this->layoutDirection);
        if ($this->layoutDirection == 'rtl') {
            $this->_template->addCss('css/style--arabic.css');
        }
    }

    public function getNavigationBreadcrumbArr($action)
    {
        switch ($action) {
            case 'shops':
            case 'shops':
            case 'shops':
                $link = Label::getLabel('MSG_Catalog', $this->adminLangId);
                break;
        }
        return $link;
    }

    public function getBreadcrumbNodes($action)
    {
        $nodes = [];
        $className = get_class($this);
        $arr = explode('-', FatUtility::camel2dashed($className));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $className = ucwords(implode(' ', $arr));
        if ($action == 'index') {
            $nodes[] = ['title' => $className];
        } else {
            $arr = explode('-', FatUtility::camel2dashed($action));
            $action = ucwords(implode(' ', $arr));
            $nodes[] = ['title' => $className, 'href' => CommonHelper::generateUrl($urlController)];
            $nodes[] = ['title' => $action];
        }
        return $nodes;
    }

    protected function getUserSearchForm()
    {
        $frm = new Form('frmUserSearch');
        $frm->addTextBox(Label::getLabel('LBL_Name_Or_Email', $this->adminLangId), 'keyword', '', ['id' => 'keyword', 'autocomplete' => 'off']);
        $arr_options = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getActiveInactiveArr($this->adminLangId);
        $arr_options1 = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getYesNoArr($this->adminLangId);
        $arr_options2 = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + User::getUserTypesArr($this->adminLangId);
        $arr_options2 = $arr_options2 + [User::USER_TYPE_LEARNER_TEACHER => Label::getLabel('LBL_Learner', $this->adminLangId) . '+' . Label::getLabel('LBL_Teacher', $this->adminLangId)];
        $frm->addSelectBox(Label::getLabel('LBL_Active_Users', $this->adminLangId), 'user_active', $arr_options, -1, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_Email_Verified', $this->adminLangId), 'user_verified', $arr_options1, -1, [], '');
        $frm->addSelectBox(Label::getLabel('LBL_User_Type', $this->adminLangId), 'type', $arr_options2, -1, [], '');
        $frm->addDateField(Label::getLabel('LBL_Reg._Date_From', $this->adminLangId), 'user_regdate_from', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_Reg._Date_To', $this->adminLangId), 'user_regdate_to', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'user_id', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    protected function renderJsonError($msg = '')
    {
        $this->set('msg', $msg);
        $this->_template->render(false, false, 'json-error.php', false, false);
    }

    protected function renderJsonSuccess($msg = '')
    {
        $this->set('msg', $msg);
        $this->_template->render(false, false, 'json-success.php', false, false);
    }

    public function includeDateTimeFiles()
    {
        $this->_template->addCss(['css/1jquery-ui-timepicker-addon.css'], false);
        $this->_template->addJs(['js/1jquery-ui-timepicker-addon.js'], false);
    }

}
