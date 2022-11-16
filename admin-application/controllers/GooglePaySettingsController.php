<?php

class GooglePaySettingsController extends PaymentSettingsController
{

    private $keyName = "GooglePay";

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
        $frm->addRequiredField(Label::getLabel('LBL_Merchant_Id', $this->adminLangId), 'merchant_Id');
        $frm->addRequiredField(Label::getLabel('LBL_Merchant_Name', $this->adminLangId), 'merchant_Name');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
