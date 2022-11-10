<?php

class PaymentController extends MyAppController
{

    protected $systemCurrencyCode;
    protected $systemCurrencyId;
    public $settings = [];

    public function __construct($action)
    {
        parent::__construct($action);
        $currency = CommonHelper::getSystemCurrencyData();
        $this->systemCurrencyId = CommonHelper::getSystemCurrencyId();
        $this->systemCurrencyCode = strtoupper($currency['currency_code']);
        $this->set('systemCurrencyCode', $this->systemCurrencyCode);
        $this->loadPaymenMethod();
    }

    private function loadPaymenMethod(): void
    {
        $pmObj = new PaymentSettings($this->keyName);
        if (!$this->settings = $pmObj->getPaymentSettings()) {
            $this->setErrorAndRedirect($pmObj->getError());
        }
    }

    protected function setErrorAndRedirect(string $msg, $redirect = true)
    {
        if (FatUtility::isAjaxCall()) {
            FatUtility::dieJsonError($msg);
        }
        Message::addErrorMessage($msg);
        if ($redirect) {
            CommonHelper::redirectUserReferer();
        }
    }
}
