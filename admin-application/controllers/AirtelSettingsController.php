<?php

class AirtelSettingsController extends PaymentSettingsController
{

    private $keyName = "Airtel";

    public function index()
    {
        $paymentSettings = $this->getPaymentSettings($this->keyName);
        $frm = $this->getSettingsForm();
        $frm->fill($paymentSettings);
        $this->set('frm', $frm);
        $this->set('paymentMethod', $this->keyName);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $frm = $this->getSettingsForm();
        $this->setUpPaymentSettings($frm, $this->keyName);
    }

    private function getSettingsForm()
    {
        $frm = new Form('frmPaymentMethods');
        $frm->addRequiredField(Label::getLabel('LBL_Airetl_Merchant_Id', $this->adminLangId), 'airtel_merchant_Id');
        $frm->addRequiredField(Label::getLabel('LBL_Airtel_Public_Key', $this->adminLangId), 'airtel_public_key');
        $fld1 = $frm->addRequiredField(Label::getLabel('LBL_Encryption_key', $this->adminLangId), 'airtel_encryption_key');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
