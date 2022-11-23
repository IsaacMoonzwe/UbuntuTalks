<?php

class GoogleSettingsController extends PaymentSettingsController
{

    private $keyName = "Google";

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
        $fld1 = $frm->addRequiredField(Label::getLabel('LBL_Environment', $this->adminLangId), 'merchant_environment');
        $fld1->htmlAfterField = "<br><small>" . Label::getLabel("LBL_For_Testing_Environment_Type_TEST_&_For_Live_Environment_Type_PRODUCTION.", $this->adminLangId) . "</small>";
        $frm->addSubmitButton('&nbsp;', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

}
