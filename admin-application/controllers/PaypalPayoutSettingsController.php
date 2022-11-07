<?php

class PaypalPayoutSettingsController extends PaymentSettingsController
{

    public function index()
    {
        $paymentSettings = $this->getPaymentSettings(PaypalPayout::KEY_NAME);
        $frm = $this->getSettingsForm();
        $frm->fill($paymentSettings);
        $this->set('frm', $frm);
        $this->set('paymentMethod', PaypalPayout::KEY_NAME);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $frm = $this->getSettingsForm();
        $this->setUpPaymentSettings($frm, PaypalPayout::KEY_NAME);
    }

    private function getSettingsForm()
    {
        $frm = new Form('frmPaymentMethods');
        $frm->addRequiredField(Label::getLabel('LBL_Client_Id', $this->adminLangId), 'paypal_client_id');
        $frm->addRequiredField(Label::getLabel('LBL_Client_Secret', $this->adminLangId), 'paypal_client_secret');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
