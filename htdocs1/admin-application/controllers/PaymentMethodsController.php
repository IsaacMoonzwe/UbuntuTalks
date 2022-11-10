<?php

class PaymentMethodsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewPaymentMethods();
    }

    public function index()
    {
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditPaymentMethods($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->_template->render();
    }

    public function search()
    {
        $srch = PaymentMethods::getSearchObject($this->adminLangId, false);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $adminId = AdminAuthentication::getLoggedAdminId();
        $canEdit = $this->objPrivilege->canEditPaymentMethods($adminId, true);
        $this->set("canEdit", $canEdit);
        $this->set("arr_listing", $records);
        $this->set('activeInactiveArr', applicationConstants::getActiveInactiveArr($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function form($pMethodId)
    {
        $pMethodId = FatUtility::int($pMethodId);
        $frm = $this->getForm($pMethodId);
        if (1 > $pMethodId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $data = PaymentMethods::getAttributesById($pMethodId, ['pmethod_id', 'pmethod_identifier', 'pmethod_active']);
        if ($data === false) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $frm->fill($data);
        $this->set('languages', Language::getAllNames());
        $this->set('pmethod_id', $pMethodId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditPaymentMethods();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $pMethodId = $post['pmethod_id'];
        unset($post['pmethod_id']);
        $data = PaymentMethods::getAttributesById($pMethodId, ['pmethod_id']);
        if ($data === false) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $record = new PaymentMethods($pMethodId);
        $record->assignValues($post);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        if ($pMethodId > 0) {
            $languages = Language::getAllNames();
            foreach ($languages as $langId => $langName) {
                if (!$row = PaymentMethods::getAttributesByLangId($langId, $pMethodId)) {
                    $newTabLangId = $langId;
                    break;
                }
            }
        } else {
            $pMethodId = $record->getMainTableRecordId();
            $newTabLangId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('pMethodId', $pMethodId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function langForm($pMethodId = 0, $lang_id = 0)
    {
        $pMethodId = FatUtility::int($pMethodId);
        $lang_id = FatUtility::int($lang_id);
        if ($pMethodId == 0 || $lang_id == 0) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $langFrm = $this->getLangForm($pMethodId, $lang_id);
        $langData = PaymentMethods::getAttributesByLangId($lang_id, $pMethodId);
        if ($langData) {
            $langFrm->fill($langData);
        }
        $this->set('languages', Language::getAllNames());
        $this->set('pMethodId', $pMethodId);
        $this->set('lang_id', $lang_id);
        $this->set('langFrm', $langFrm);
        $this->set('formLayout', Language::getLayoutDirection($lang_id));
        $this->_template->render(false, false);
    }

    public function langSetup()
    {
        $this->objPrivilege->canEditPaymentMethods();
        $post = FatApp::getPostedData();
        $pMethodId = $post['pmethod_id'];
        $lang_id = $post['lang_id'];
        if ($pMethodId == 0 || $lang_id == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $frm = $this->getLangForm($pMethodId, $lang_id);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        unset($post['pmethod_id']);
        unset($post['lang_id']);
        $data = [
            'pmethodlang_lang_id' => $lang_id,
            'pmethodlang_pmethod_id' => $pMethodId,
            'pmethod_name' => $post['pmethod_name'],
            'pmethod_description' => $post['pmethod_description'],
        ];
        $pMethodObj = new PaymentMethods($pMethodId);
        if (!$pMethodObj->updateLangData($lang_id, $data)) {
            Message::addErrorMessage($pMethodObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $newTabLangId = 0;
        $languages = Language::getAllNames();
        foreach ($languages as $langId => $langName) {
            if (!$row = PaymentMethods::getAttributesByLangId($langId, $pMethodId)) {
                $newTabLangId = $langId;
                break;
            }
        }
        $this->set('msg', $this->str_setup_successful);
        $this->set('pMethodId', $pMethodId);
        $this->set('langId', $newTabLangId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function uploadIcon($pmethod_id)
    {
        $this->objPrivilege->canEditPaymentMethods();
        $pmethod_id = FatUtility::int($pmethod_id);
        if (1 > $pmethod_id) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieJsonError(Message::getHtml());
        }
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            Message::addErrorMessage(Label::getLabel('MSG_Please_select_a_file', $this->adminLangId));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $fileHandlerObj = new AttachedFile();
        if (!$fileHandlerObj->saveAttachment($_FILES['file']['tmp_name'], AttachedFile::FILETYPE_PAYMENT_METHOD, $pmethod_id, 0, $_FILES['file']['name'], -1, true)) {
            Message::addErrorMessage($fileHandlerObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('pmethodId', $pmethod_id);
        $this->set('file', $_FILES['file']['name']);
        $this->set('msg', $_FILES['file']['name'] . ' ' . Label::getLabel('LBL_File_Uploaded_Successfully', $this->adminLangId));
        $this->_template->render(false, false, 'json-success.php');
    }

    public function updateOrder()
    {
        $this->objPrivilege->canEditPaymentMethods();
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $pMethodObj = new PaymentMethods();
            if (!$pMethodObj->updateOrder($post['paymentMethod'])) {
                Message::addErrorMessage($pMethodObj->getError());
                FatUtility::dieJsonError(Message::getHtml());
            }
            $this->set('msg', Label::getLabel('LBL_Order_Updated_Successfully', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
    }

    public function changeStatus()
    {
        $this->objPrivilege->canEditPaymentMethods();
        $pmethodId = FatApp::getPostedData('pmethodId', FatUtility::VAR_INT, 0);
        if (0 >= $pmethodId) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieWithError(Message::getHtml());
        }
        $data = PaymentMethods::getAttributesById($pmethodId, ['pmethod_id', 'pmethod_active']);
        if ($data == false) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $status = ($data['pmethod_active'] == applicationConstants::ACTIVE) ? applicationConstants::INACTIVE : applicationConstants::ACTIVE;
        $obj = new PaymentMethods($pmethodId);
        if (!$obj->changeStatus($status)) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieWithError(Message::getHtml());
        }
        $this->set('msg', $this->str_update_record);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function paymentMethodFeeList(int $pMethodId)
    {
        $getPMethodDetail = PaymentMethods::getAttributesById($pMethodId, ['pmethod_id']);
        if ($getPMethodDetail === false) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $srch = PaymentMethodTransactionFee::getSearchObject();
        $srch->joinTable(Currency::DB_TBL_LANG, 'LEFT JOIN', 'curr_l.' . Currency::DB_TBL_LANG_PREFIX . 'currency_id = curr.' . Currency::tblFld('id') . ' and curr_l.' . Currency::DB_TBL_LANG_PREFIX . 'lang_id = ' . $this->adminLangId, 'curr_l');
        $srch->addMultipleFields(['currency_name', 'currency_code', 'pmtfee.*']);
        $srch->addCondition('pmethod_id', '=', $pMethodId);
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $canEdit = $this->objPrivilege->canEditPaymentMethods($this->admin_id, true);
        $this->set("canEdit", $canEdit);
        $this->set("frm", $this->getPaymentMethodFeeForm($pMethodId));
        $this->set("arr_listing", $records);
        $this->_template->render(false, false);
    }

    public function setupPaymentMethodFee()
    {
        $this->objPrivilege->canEditPaymentMethods();
        $pMethodId = FatApp::getPostedData('pmtfee_pmethod_id', FatUtility::VAR_INT, 0);
        $getPMethodDetail = PaymentMethods::getAttributesById($pMethodId, ['pmethod_id']);
        if ($getPMethodDetail === false) {
            Message::addErrorMessage(Label::getLabel('LBL_Invalid_Request'));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $frm = $this->getPaymentMethodFeeForm($pMethodId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $paymentMethodFeeObj = new PaymentMethodTransactionFee($pMethodId, $post['pmtfee_currency_id']);
        if ($paymentMethodFeeObj->setupFee($post['pmtfee_fee'], $post['pmtfee_type']) === false) {
            Message::addErrorMessage($paymentMethodFeeObj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Fee_Setup_Successfully'));
    }

    private function getPaymentMethodFeeForm(int $pMethodId)
    {
        $frm = new Form('frmGatewayFee');
        $frm->addHiddenField('', 'pmtfee_pmethod_id', $pMethodId);
        $fld = $frm->addFloatField(Label::getLabel('LBL_Method_Fee', $this->adminLangId), 'pmtfee_fee');
        $fld->requirements()->setFloatPositive();
        $currancyList = Currency::getCurrencyAssoc($this->adminLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Currency', $this->adminLangId), 'pmtfee_currency_id', $currancyList, '', [], '');
        $fld->requirements()->setRequired();
        $feeTypeArray = PaymentMethodTransactionFee::feeTypeArray($this->adminLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Method_Fee_Type', $this->adminLangId), 'pmtfee_type', $feeTypeArray, PaymentMethodTransactionFee::FEE_TYPE_PERCENTAGE, [], '');
        $fld->requirements()->setRequired();
        $submitFild = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        $resetBtn = $frm->addResetButton('', 'btn_clear', Label::getLabel('LBL_Reset', $this->adminLangId));
        $submitFild->attachField($resetBtn);
        return $frm;
    }

    private function getForm($pMethodId = 0)
    {
        $pMethodId = FatUtility::int($pMethodId);
        $frm = new Form('frmGateway');
        $frm->addHiddenField('', 'pmethod_id', $pMethodId);
        $frm->addRequiredField(Label::getLabel('LBL_Gateway_Identifier', $this->adminLangId), 'pmethod_identifier');
        $activeInactiveArr = applicationConstants::getActiveInactiveArr($this->adminLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'pmethod_active', $activeInactiveArr, '', [], '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getLangForm($pMethodId = 0, $lang_id = 0)
    {
        $frm = new Form('frmGatewayLang');
        $frm->addHiddenField('', 'pmethod_id', $pMethodId);
        $frm->addHiddenField('', 'lang_id', $lang_id);
        $frm->addRequiredField(Label::getLabel('LBL_Gateway_Name', $this->adminLangId), 'pmethod_name');
        $frm->addTextarea(Label::getLabel('LBL_Details', $this->adminLangId), 'pmethod_description');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
