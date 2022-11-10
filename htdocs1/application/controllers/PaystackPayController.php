<?php

class PaystackPayController extends PaymentController
{

    protected $keyName = "Paystack";
    private $error = false;
    private $paymentSettings = false;

    private const INITIALIZE_URL = "https://api.paystack.co/transaction/initialize/";
    private const VERIFY_URL = "https://api.paystack.co/transaction/verify/";

    protected function allowedCurrenciesArr()
    {
        return ['USD', 'NGN', 'GHS', 'ZAR'];
    }

    public function charge($orderId)
    {
        if (empty(trim($orderId))) {
            FatUtility::exitWIthErrorCode(404);
        }
        $this->paymentSettings = $this->getPaymentSettings();
        if (!isset($this->paymentSettings['secret_key']) && !isset($this->paymentSettings['public'])) {
            Message::addErrorMessage(Label::getLabel('ERR_INVALID_PAYMENT_GATEWAY_SETUP_ERROR', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $paymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $payableAmount = $this->formatPayableAmount($paymentAmount);
        $orderInfo = $orderPaymentObj->getOrderById($orderId);
        if (!$orderInfo['order_id']) {
            FatUtility::exitWithErrorCode(404);
        } elseif ($orderInfo && $orderInfo["order_is_paid"] == Order::ORDER_IS_PENDING) {
            $response = $this->doPayment($payableAmount, $orderId);
            $frm = $this->getPaymentForm($orderId);
            $this->set('frm', $frm);
            if (!$response) {
                Message::addErrorMessage($this->error);
                CommonHelper::redirectUserReferer();
            }
            $this->set('response', $response);
        } else {
            $message = Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED', $this->siteLangId);
            $this->error = $message;
        }
        $this->set('paymentAmount', $paymentAmount);
        $this->set('orderInfo', $orderInfo);
        if ($this->error) {
            $this->set('error', $this->error);
        }
        $cancelBtnUrl = CommonHelper::getPaymentCancelPageUrl();
        if ($orderInfo['order_type'] == Order::TYPE_WALLET_RECHARGE) {
            $cancelBtnUrl = CommonHelper::getPaymentFailurePageUrl();
        }
        $this->set('cancelBtnUrl', $cancelBtnUrl);
        $this->set('orderInfo', $orderInfo);
        $this->set('paymentAmount', $paymentAmount);
        $this->set('exculdeMainHeaderDiv', true);
        $this->_template->render(true, false);
    }

    private function formatPayableAmount($amount = null)
    {
        if ($amount == null) {
            return false;
        }
        $amount = number_format($amount, 2, '.', '');
        return $amount * 100;
    }

    private function getPaymentSettings()
    {
        $pmObj = new PaymentSettings($this->keyName);
        return $pmObj->getPaymentSettings();
    }

    private function getPaymentForm(): Form
    {
        $frm = new Form('frmPaymentForm');
        $frm->addHiddenField('', 'orderId');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_CONFIRM', $this->siteLangId));
        return $frm;
    }

    private function doPayment($payment_amount, $orderId)
    {
        $this->paymentSettings = $this->getPaymentSettings();
        $orderSrch = new OrderSearch();
        $orderSrch->joinUser();
        $orderSrch->joinUserCredentials();
        $orderSrch->addCondition('order_id', '=', $orderId);
        $orderSrch->addMultipleFields([
            'order_id',
            'order_language_id',
            'order_currency_code',
            'u.user_first_name as user_first_name',
            'cred.credential_email as customer_email',
            'order_language_code',
            '"FATbit_SP" as paypal_bn'
        ]);
        $orderRs = $orderSrch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($orderRs);
        if ($payment_amount == null || !$this->paymentSettings || $orderInfo['order_id'] == null) {
            return false;
        }
        try {
            $systemCurrencyCode = CommonHelper::getSystemCurrencyData()['currency_code'];
            $callbackUrl = CommonHelper::generateFullUrl($this->keyName . 'Pay', "callback", [$orderId]);
            $requestBody = [
                'email' => $orderInfo['customer_email'],
                'amount' => $payment_amount,
                'currency' => $systemCurrencyCode,
                'metadata' => ['order_id' => $orderId],
                'callback_url' => $callbackUrl,
                'webhook_url' => CommonHelper::generateFullUrl($this->keyName . 'Pay', 'webhook')
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::INITIALIZE_URL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $headers = [
                "Authorization: Bearer " . $this->paymentSettings['secret_key'],
                "Content-Type: application/json",
                "cache-control: no-cache"
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            if (!$response = curl_exec($ch)) {
                throw new Exception(curl_error($ch));
            }
            $payment_response = json_decode($response, true);
            if (false === $payment_response['status']) {
                throw new Exception($payment_response['message']);
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return $payment_response;
    }

    /**
     * callback
     *
     * @param  string $orderId
     * @return void
     */
    public function callback(string $orderId)
    {
        $this->paymentSettings = $this->getPaymentSettings();
        $orderPaymentObj = new OrderPayment($orderId);
        $referenceId = $_REQUEST['reference'];
        try {
            $this->updatePaymentStatus($referenceId);
        } catch (Exception $e) {
            $orderPaymentObj->addOrderPaymentComments($e->getMessage());
            Message::addErrorMessage($e->getMessage());
            FatApp::redirectUser(CommonHelper::generateUrl('custom', 'paymentFailed'));
        }
        FatApp::redirectUser(CommonHelper::generateUrl('custom', 'paymentSuccess', [$orderId]));
    }

    private function validatePaymentResponse($referenceId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::VERIFY_URL . rawurlencode($referenceId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            "accept: application/json",
            "authorization: Bearer " . $this->paymentSettings['secret_key'],
            "cache-control: no-cache"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (!$response = curl_exec($ch)) {
            throw new Exception(curl_error($ch));
        }
        return $response;
    }

    /**
     * webhook - Not in use curretly, it will be used when customer don't get redirected after transaction, i.e for backend process
     *
     * @return void
     */
    public function webhook()
    {
        $this->paymentSettings = $this->getPaymentSettings();
        $payload = file_get_contents("php://input");
        $signature = (isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) ? $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] : '');
        /* It is a good idea to log all events received. Add code *
         * here to log the signature and body to db or file       */
        if (!$signature) {
            // only a post with paystack signature header gets our attention
            exit();
        }
        // confirm the event's signature
        if ($signature !== hash_hmac('sha512', $payload, $this->paymentSettings['secret_key'])) {
            // silently forget this ever happened
            exit();
        }
        $webhook_response = json_decode($payload, true);
        if ('charge.success' != $webhook_response['event']) {
            exit;
        }
        try {
            $orderId = $this->updatePaymentStatus($webhook_response['data']['reference']);
        } catch (Exception $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }
        http_response_code(200);
        exit();
    }

    private function updatePaymentStatus($referenceId)
    {
        $response = $this->validatePaymentResponse($referenceId);
        $paymentResponse = json_decode($response, true);
        if (1 != $paymentResponse['status']) {
            throw new Exception($paymentResponse['message']);
        }
        $orderId = $paymentResponse['data']['metadata']['order_id'];
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if ($orderInfo["order_is_paid"] != Order::ORDER_IS_PENDING) {
            return $orderId;
        }
        $paymentGatewayCharge = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $payableAmount = $this->formatPayableAmount($paymentGatewayCharge);
        $payment_comments = '';
        $totalPaidMatch = $paymentResponse['data']['amount'] == $payableAmount;
        if (!empty($paymentResponse['data']['amount'])) {
            $payment_comments .= "Paystack_PAYMENT :: Status is: " . $paymentResponse['status'] . "\n\n";
        }
        if (!$totalPaidMatch) {
            $payment_comments .= "Paystack_PAYMENT :: TOTAL PAID MISMATCH! " . strtolower($paymentResponse['data']['amount']) . "\n\n";
        }
        if (!empty($paymentResponse['data']['amount']) && $totalPaidMatch) {
            $orderPaymentObj->addOrderPayment($this->paymentSettings["pmethod_code"], $paymentResponse['data']['id'], $paymentGatewayCharge, 'Received Payment', serialize($paymentResponse));
        } else {
            throw new Exception($payment_comments);
        }
        return $orderId;
    }

}
