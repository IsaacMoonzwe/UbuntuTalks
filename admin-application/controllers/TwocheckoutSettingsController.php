<?php

class TwocheckoutSettingsController extends PaymentSettingsController
{

    private $keyName = "Twocheckout";

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
        $frm->addHiddenField('', 'payment_type', 'API');
        $frm->addRequiredField(Label::getLabel('LBL_Seller_ID', $this->adminLangId), 'sellerId');
        $frm->addRequiredField(Label::getLabel('LBL_Publishable_Key', $this->adminLangId), 'publishableKey');
        $frm->addRequiredField(Label::getLabel('LBL_Private_Key', $this->adminLangId), 'privateKey');
        $frm->addRequiredField(Label::getLabel('LBL_Secret_Word', $this->adminLangId), 'hashSecretWord');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
