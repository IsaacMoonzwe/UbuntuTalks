<?php

class PaypalStandardSettingsController extends PaymentSettingsController
{

    private $keyName = "PaypalStandard";

    public function index()
    {
        $paymentSettings = $this->getPaymentSettings($this->keyName);
        $frm = $this->getForm();
        $frm->fill($paymentSettings);
        $this->set('frm', $frm);
        $this->set('paymentMethod', $this->keyName);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $frm = $this->getForm();
        $this->setUpPaymentSettings($frm, $this->keyName);
    }

    private function getForm()
    {
        $frm = new Form('frmPaymentMethods');
        $frm->addRequiredField(Label::getLabel('LBL_MERCHANT_EMAIL', $this->adminLangId), 'merchant_email');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE_CHANGES', $this->adminLangId));
        return $frm;
    }
}
