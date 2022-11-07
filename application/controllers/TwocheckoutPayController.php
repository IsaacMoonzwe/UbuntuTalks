<?php

class TwocheckoutPayController extends PaymentController
{

    protected $keyName = "Twocheckout";
    private $paymentType = "API"; //holds two values HOSTED or API

    public function __construct($action)
    {
        parent::__construct($action);
        if (!is_array($this->allowedCurrenciesArr())) {
            $this->setErrorAndRedirect(Label::getLabel('MSG_INVALID_CURRENCY_FORMAT', $this->siteLangId));
        }
        if (!in_array($this->systemCurrencyCode, $this->allowedCurrenciesArr())) {
            $msg = Label::getLabel('MSG_INVALID_ORDER_CURRENCY_({CURRENCY})_PASSED_TO_GATEWAY', $this->siteLangId);
            $msg = CommonHelper::replaceStringData($msg, ['{CURRENCY}' => $this->systemCurrencyCode]);
            $this->setErrorAndRedirect($msg);
        }
        if (!$this->validateSettings()) {
            $msg = Label::getLabel('MSG_PLESAE_UPDATE_2CHECKOUT_PAYMENT_SETTINGS', $this->siteLangId);
            $this->setErrorAndRedirect($msg);
        }
    }

    protected function allowedCurrenciesArr()
    {
        return ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'RON', 'CZK', 'HUF', 'TRY', 'ZAR', 'EGP', 'MXN', 'PEN'];
    }

    public function charge($orderId)
    {
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $paymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if (!$orderInfo['order_id']) {
            FatUtility::exitWIthErrorCode(404);
        } elseif ($orderInfo["order_is_paid"] == Order::ORDER_IS_PENDING) {
            $frm = $this->getPaymentForm($orderId);
            $frm->getField('country')->value = $orderInfo['user_country_id'];
            $frm->getField('cc_owner')->value = $orderInfo['user_name'];
            $this->set('frm', $frm);
            if ($this->paymentType == 'API') {
                $this->set('sellerId', $this->settings['sellerId']);
                $this->set('publishableKey', $this->settings['publishableKey']);
                $this->set('transaction_mode', 'production');
            }
        } else {
            $this->set('error', Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED', $this->siteLangId));
        }
        $cancelBtnUrl = CommonHelper::getPaymentCancelPageUrl();
        if ($orderInfo['order_type'] == Order::TYPE_WALLET_RECHARGE) {
            $cancelBtnUrl = CommonHelper::getPaymentFailurePageUrl();
        }
        $this->set('cancelBtnUrl', $cancelBtnUrl);
        $this->set('paymentAmount', $paymentAmount);
        $this->set('paymentType', $this->paymentType);
        $this->set('orderInfo', $orderInfo);
        $this->set('exculdeMainHeaderDiv', true);
        if (FatUtility::isAjaxCall()) {
            $json['html'] = $this->_template->render(false, false, 'twocheckout-pay/charge-ajax.php', true, false);
            FatUtility::dieJsonSuccess($json);
        }
        $this->_template->render(true, false);
    }

    /**
     * Description: This function will be called in case of Payment type is API CHECKOUT i.e. $paymentType = API.
     */
    public function send($orderId)
    {
        $frm = $this->getPaymentForm($orderId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieWithError(current($frm->getValidationErrors()));
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        /* Retrieve Payment to charge corresponding to your order */
        $orderPaymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        if ($orderPaymentAmount > 0) {
            /* Retrieve Primary Info corresponding to your order */
            $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
            $order_actual_paid = number_format(round($orderPaymentAmount, 2), 2, ".", "");
            $params = [
                'sellerId' => $this->settings['sellerId'],
                'privateKey' => $this->settings['privateKey'],
                'merchantOrderId' => $orderId,
                'token' => $post['token'],
                'currency' => $orderInfo["order_currency_code"],
                'total' => $order_actual_paid,
                'billingAddr' => [
                    'name' => FatUtility::decodeHtmlEntities($post['cc_owner'], ENT_QUOTES, 'UTF-8'),
                    "addrLine1" => $post['addrLine1'],
                    "city" => $post['city'],
                    "state" => $post['state'],
                    "zipCode" => $post['zipCode'],
                    'country' => Country::getAttributesById($post['country'], 'country_code'),
                    'email' => $orderInfo['user_email'],
                    'phoneNumber' => $orderInfo['user_phone'],
                ]
            ];
            if (FatApp::getConfig('CONF_TRANSACTION_MODE', FatUtility::VAR_BOOLEAN, false) == false) {
                $params['demo'] = true;
            }
            $url = 'https://www.2checkout.com/checkout/api/1/' . $this->settings['sellerId'] . '/rs/authService';
            $curl = curl_init($url);
            $params = json_encode($params);
            $header = ["content-type:application/json", "content-length:" . strlen($params)];
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_USERAGENT, "2Checkout PHP/0.1.0%s");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            $result = curl_exec($curl);
            $json = [];
            $json['redirect'] = CommonHelper::generateUrl('custom', 'paymentFailed');
            if (curl_error($curl)) {
                $json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
            } elseif ($result) {
                $object = json_decode($result, true);
                $result_array = [];
                foreach ($object as $member => $data) {
                    $result_array[$member] = $data;
                }
                $exception = $result_array['exception']; //must be null in case of successful orders
                $response = $result_array['response'];
                $message = '';
                if (!is_null($response)) {
                    $errors = $response['errors'];
                    $validationErrors = !empty($response['validationErrors']) ? $response['validationErrors'] : ''; // '' or null
                    if (is_null($errors)) {
                        $responseCode = $response['responseCode']; //APPROVED : Code indicating the result of the authorization attempt.
                        $responseMsg = $response['responseMsg']; //Message indicating the result of the authorization attempt.
                        $orderNumber = $response['orderNumber']; //2Checkout Order Number
                        $merchantOrderId = $response['merchantOrderId']; //must be equal to order id sent
                        $transactionId = $response['transactionId']; //2Checkout Invoice ID
                        $message .= 'Response Code: ' . $responseCode . "\n";
                        $message .= 'Order Number: ' . $orderNumber . "\n";
                        $message .= 'Merchant Order Id: ' . $merchantOrderId . "\n";
                        $message .= 'Transaction Id: ' . $transactionId . "\n";
                        $message .= 'Payment Method: 2Checkout API' . "\n";
                        $message .= 'Response Message: ' . $responseMsg . "\n";
                        if ($responseCode == 'APPROVED') {
                            $orderPaymentObj->addOrderPayment($this->settings["pmethod_code"], $transactionId, $orderPaymentAmount, Label::getLabel("LBL_Received_Payment", $this->siteLangId), $message);
                            $json['redirect'] = CommonHelper::generateUrl('custom', 'paymentSuccess', [$orderId]);
                        }
                    } else {
                        $json['error'] = $errors;
                    }
                } else {
                    $json['error'] = $exception['errorMsg'];
                }
            } else {
                $json['error'] = Label::getLabel('MSG_EMPTY_GATEWAY_RESPONSE', $this->siteLangId);
            }
        } else {
            $json['error'] = Label::getLabel('MSG_Invalid_Request', $this->siteLangId);
        }
        curl_close($curl);
        echo json_encode($json);
    }

    private function getPaymentForm($orderId)
    {
        return $this->getAPICheckoutForm($orderId);
    }

    private function getAPICheckoutForm($orderId)
    {
        $frm = new Form('frmTwoCheckout', ['id' => 'frmTwoCheckout', 'action' => CommonHelper::generateUrl('TwocheckoutPay', 'send', [$orderId]), 'class' => "form form--normal"]);
        $frm->addRequiredField(Label::getLabel('LBL_ENTER_CREDIT_CARD_NUMBER', $this->siteLangId), 'ccNo')->requirements()->setRegularExpressionToValidate(applicationConstants::CREDIT_CARD_NO_REGEX);
        $frm->addRequiredField(Label::getLabel('LBL_CARD_HOLDER_NAME', $this->siteLangId), 'cc_owner');
        $frm->addHiddenField('', 'token', '');
        $data['months'] = applicationConstants::getMonthsArr($this->siteLangId);
        $today = getdate();
        $data['year_expire'] = [];
        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $data['year_expire'][strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
        }
        $frm->addSelectBox(Label::getLabel('LBL_EXPIRY_MONTH', $this->siteLangId), 'expMonth', $data['months'], '', [], '');
        $frm->addSelectBox(Label::getLabel('LBL_EXPIRY_YEAR', $this->siteLangId), 'expYear', $data['year_expire'], '', [], '');
        $fld = $frm->addPasswordField(Label::getLabel('LBL_CVV_SECURITY_CODE', $this->siteLangId), 'cvv');
        $fld->requirements()->setRequired(true);
        $fld->requirements()->setRegularExpressionToValidate(applicationConstants::CVV_NO_REGEX);
        $frm->addRequiredField(Label::getLabel('LBL_Address'), 'addrLine1');
        $frm->addRequiredField(Label::getLabel('LBL_City'), 'city');
        $frm->addRequiredField(Label::getLabel('LBL_State'), 'state');
        $frm->addRequiredField(Label::getLabel('LBL_Zip'), 'zipCode');
        $country = new Country();
        $countriesArr = $country->getCountriesArr($this->siteLangId);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Country'), 'country', $countriesArr, FatApp::getConfig('CONF_COUNTRY', FatUtility::VAR_INT, 0), [], Label::getLabel('LBL_Select'));
        $fld->requirement->setRequired(true);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Pay_Now', $this->siteLangId));
        return $frm;
    }

    public function getExternalLibraries()
    {
        $json['libraries'] = ["https://www.2checkout.com/checkout/api/2co.min.js",];
        FatUtility::dieJsonSuccess($json);
    }

    public function validateSettings(): bool
    {
        if (
                empty($this->settings['sellerId']) || empty($this->settings['publishableKey']) ||
                empty($this->settings['privateKey']) || empty($this->settings['hashSecretWord'])
        ) {
            return false;
        }
        return true;
    }

}
