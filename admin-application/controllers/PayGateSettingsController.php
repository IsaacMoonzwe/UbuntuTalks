<?php

class PayGateSettingsController extends PaymentSettingsController
{

    private $keyName = "PayGate";

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
        $frm->addRequiredField(Label::getLabel('LBL_PayGate_Id', $this->adminLangId), 'paygateId');
        $frm->addRequiredField(Label::getLabel('LBL_Encryption_Key', $this->adminLangId), 'encryptionKey');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
