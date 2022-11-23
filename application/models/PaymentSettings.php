<?php

class PaymentSettings
{

    const DB_PAYMENT_METHODS_TBL = 'tbl_payment_methods';
    const DB_PAYMENT_METHODS_TBL_PREFIX = 'pmethod_';
    const DB_PAYMENT_METHOD_SETTINGS_TBL = 'tbl_payment_method_settings';
    const DB_PAYMENT_METHOD_SETTINGS_TBL_PREFIX = 'paysetting_';

    private $db;
    private $error;
    private $paymentMethodKey = null;
    private $commonLangId;

    public function __construct($methodIdentifier)
    {
        $this->db = FatApp::getDb();
        $this->error = '';
        $this->paymentMethodKey = $methodIdentifier;
        $this->commonLangId = CommonHelper::getLangId();
    }

    public function getError()
    {
        return $this->error;
    }

    public function saveSettings($arr)
    {
        if (empty($arr)) {
            $this->error = Label::getLabel('ERR_Error:_Please_provide_data_to_save_settings.', $this->commonLangId);
            return false;
        }
        $paymentMethod = $this->getPaymentMethodByCode($this->paymentMethodKey);
        if (!$paymentMethod) {
            $this->error = Label::getLabel('ERR_Error:_Payment_method_with_defined_payment_key_does_not_exist.', $this->commonLangId);
            return false;
        }
        $pmethod_id = $paymentMethod["pmethod_id"];
        if (!$this->db->deleteRecords(static::DB_PAYMENT_METHOD_SETTINGS_TBL, ['smt' => static::DB_PAYMENT_METHOD_SETTINGS_TBL_PREFIX . 'pmethod_id = ?', 'vals' => [$pmethod_id]])) {
            $this->error = $this->db->getError();
            return false;
        }
        foreach ($arr as $key => $val) {
            if ($key == "btn_submit") {
                continue;
            }
            $data = ['paysetting_pmethod_id' => $pmethod_id, 'paysetting_key' => $key];
            if (!is_array($val)) {
                $data['paysetting_value'] = $val;
            } else {
                $data['paysetting_value'] = serialize($val);
            }
            if (!$this->db->insertFromArray(static::DB_PAYMENT_METHOD_SETTINGS_TBL, $data, false, ['IGNORE'])) {
                $this->error = $this->db->getError();
                return false;
            }
        }
        return true;
    }

    public function getPaymentSettings()
    {
        if (!isset($this->paymentMethodKey)) {
            $this->error = Label::getLabel('ERR_Error:_Please_create_an_object_with_Payment_Method_Key.', $this->commonLangId);
            return false;
        }
        $paymentMethod = $this->getPaymentMethodByCode($this->paymentMethodKey);
        if (!$paymentMethod) {
            $this->error = Label::getLabel('ERR_Error:_Payment_method_with_this_payment_key_does_not_exist.', $this->commonLangId);
            return false;
        }
        $paymentMethodSettings = $this->getPaymentMethodFieldsById($paymentMethod["pmethod_id"]);
        $paymentSettings = [];
        $paymentSettings['pmethod_id'] = $paymentMethod["pmethod_id"];
        foreach ($paymentMethodSettings as $pkey => $pval) {
            $paymentSettings[$pval["paysetting_key"]] = $pval["paysetting_value"];
        }
        $paymentSettings['pmethod_name'] = $paymentMethod['pmethod_identifier'];
        return array_merge($paymentSettings, $paymentMethod);
    }

    private function getPaymentMethodByCode($code)
    {
        if (empty($code)) {
            return false;
        }
        $srch = new SearchBase(static::DB_PAYMENT_METHODS_TBL, 'tpm');
        $srch->addCondition('tpm.' . static::DB_PAYMENT_METHODS_TBL_PREFIX . 'code', '=', $code);
        $rs = $srch->getResultSet();
        $payment_method = $this->db->fetch($rs);
        return $payment_method;
    }

    private function getPaymentMethodFieldsById($pmethod_id)
    {
        $srch = new SearchBase(static::DB_PAYMENT_METHOD_SETTINGS_TBL, 'tpms');
        $srch->addCondition('tpms.paysetting_pmethod_id', '=', (int) $pmethod_id);
        $rs = $srch->getResultSet();
        $paymentMethodSettings = $this->db->fetchAll($rs);
        return $paymentMethodSettings;
    }
}
