<?php

require_once CONF_INSTALLATION_PATH . 'library/payment-plugins/stripe/init.php';

class StripePayController extends PaymentController
{

    protected $keyName = "Stripe";
    private $error = false;
    private $paymentSettings = false;

    protected function allowedCurrenciesArr()
    {
        return [
            'USD', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BIF', 'BMD', 'BND', 'BOB',
            'BRL', 'BSD', 'BWP', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB',
            'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'ISK',
            'JMD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KRW', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK',
            'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PAB', 'PEN', 'PGK', 'PHP',
            'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SZL',
            'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'UGX', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW'
        ];
    }

    private function zeroDecimalCurrencies()
    {
        return ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
    }

    public function charge($orderId, $sponsers = 0, $currency = 'USD', $currencyCode = '$')
    {

        if (empty(trim($orderId))) {
            FatUtility::exitWIthErrorCode(404);
        }
        $this->paymentSettings = $this->getPaymentSettings();
        if (!isset($this->paymentSettings['privateKey']) && !isset($this->paymentSettings['publishableKey'])) {
            Message::addErrorMessage(Label::getLabel('STRIPE_INVALID_PAYMENT_GATEWAY_SETUP_ERROR', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        // $stripe = [
        //     'secret_key' => 'sk_test_51JwGHMEBydRe3lMmCC8oizzTfitqi9q9oi9f6QXrRN6x7cRVQKt9BkckGaTOOpUiMZT6e8OFYHvBO87mgss8aqWD00o4PT4Rd9',
        //     'publishable_key' => 'pk_test_51JwGHMEBydRe3lMmSMnKBfxpsc6QoqlBI7vQMsj53qfdPSNNq97yVUHEpUaoeckkrFIx2aFVTH8YZdYpxQSrGcya00je6gTKLD',
        // ];
        $stripe = ['secret_key' => $this->paymentSettings['privateKey'], 'publishable_key' => $this->paymentSettings['publishableKey']];

        $this->set('stripe', $stripe);
        if (strlen(trim($this->paymentSettings['privateKey'])) > 0 && strlen(trim($this->paymentSettings['publishableKey'])) > 0) {
            \Stripe\Stripe::setApiKey($stripe['secret_key']);
        } else {
            $this->error = Label::getLabel('STRIPE_INVALID_PAYMENT_GATEWAY_SETUP_ERROR', $this->siteLangId);
        }
        $systemCurrencyCode = CommonHelper::getSystemCurrencyData()['currency_code'];
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $paymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $payableAmount = $this->formatPayableAmount($paymentAmount);
        $currency_amount = $this->currencyConverter($paymentAmount, $currency);

        $orderSrch = new OrderSearch();
        if ($sponsers > 0) {
            $_SESSION['sponser'] = $sponsers;
            $orderSrch->joinEventUser();
            $orderSrch->joinEventUserCredentials();
        } else {
            $orderSrch->joinUser();
            $orderSrch->joinUserCredentials();
        }
        $orderSrch->addCondition('order_id', '=', $orderId);
        $orderSrch->addMultipleFields([
            'order_id',
            'order_language_id',
            'order_currency_code',
            'u.user_first_name as user_first_name',
            'cred.credential_email as customer_email',
            'order_is_paid',
            'order_language_code'
        ]);
        $orderRs = $orderSrch->getResultSet();
        $orderInfo = FatApp::getDb()->fetch($orderRs);
        $customer_email = strtolower(trim($orderInfo['customer_email']));
        if (!$orderInfo['order_id']) {

            FatUtility::exitWithErrorCode(404);
        } elseif ($orderInfo && $orderInfo["order_is_paid"] == Order::ORDER_IS_PENDING) {
            try {
                $session = \Stripe\Checkout\Session::create([
                    'customer_email' => $customer_email,
                    'payment_method_types' => ['card'],
                    'metadata' => [
                        'order_id' => $orderId
                    ],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => $currency ?? $systemCurrencyCode,
                            'product_data' => [
                                'name' => Label::getLabel('LBL_Buy_Lessons'),
                            ],
                            'unit_amount' => $payableAmount
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => CommonHelper::generateFullUrl('StripePay', 'callback') . "?session_id={CHECKOUT_SESSION_ID}",
                    'cancel_url' => CommonHelper::getPaymentCancelPageUrl(),
                ]);
                $this->set('stripeSessionId', $session->id);
            } catch (exception $e) {
                $this->set('error', $e->getMessage());
            }
        } else {
            $message = Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED', $this->siteLangId);
            $this->error = $message;
        }
        unset($_SESSION['cart']);
        $this->set('orderId', $orderId);
        $this->set('session_id', $session->id);
        $this->set('paymentAmount', $paymentAmount);
        $this->set('currency_amount', $currency_amount);
        $this->set('orderInfo', $orderInfo);
        $this->set('currency', $currency);
        if ($this->error) {
            $this->set('error', $this->error);
        }
        //$this->set('exculdeMainHeaderDiv', true);
        $this->_template->render();
    }

    private function formatPayableAmount($amount = null, $currency = 'USD')
    {
        if ($amount == null) {
            return false;
        }
        $systemCurrencyCode = Currency::getAttributesById(FatApp::getConfig('CONF_CURRENCY', FatUtility::VAR_INT, 1), 'currency_code');
        $amount = number_format($amount, 2, '.', '');
        if (in_array($currency, $this->zeroDecimalCurrencies())) {
            return round($amount);
        }
        return $amount * 100;
    }

    /*currency convert code*/
    private function currencyConverter($amount = 0, $toCurrency = 'USD', $fromCurrency = 'USD')
    {
        $fromCurrency = urlencode($fromCurrency);
        $toCurrency = urlencode($toCurrency);
        $url  = "https://www.google.com/search?q=" . $fromCurrency . "+to+" . $toCurrency;

        // $url='http://api.fixer.io/latest?symbols='.$fromCurrency.','.$toCurrency.'';
        $get = file_get_contents($url);

        $data = preg_split('/\D\s(.*?)\s=\s/', $get);
        $exhangeRate = (float) substr($data[1], 0, 7);

        $convertedAmount = $amount * $exhangeRate;

        $data = array(
            'exhangeRate' => $exhangeRate, 'convertedAmount' => $convertedAmount,
            'fromCurrency' => strtoupper($fromCurrency), 'toCurrency' => strtoupper($toCurrency)
        );

        if ($convertedAmount > 0 && $toCurrency !== $fromCurrency)
            return $convertedAmount;
        return $amount;
    }

    private function getPaymentSettings()
    {
        $pmObj = new PaymentSettings($this->keyName);
        return $pmObj->getPaymentSettings();
    }

    public function callback()
    {
        $get = FatApp::getQueryStringData();
        $sessionId = $get['session_id'];
        $this->updatePaymentStatus($sessionId);
    }

    public function create($currency = 'USD')
    {
        //  $stripe = [
        //     'secret_key' => 'sk_test_51JwGHMEBydRe3lMmCC8oizzTfitqi9q9oi9f6QXrRN6x7cRVQKt9BkckGaTOOpUiMZT6e8OFYHvBO87mgss8aqWD00o4PT4Rd9',
        //     'publishable_key' => 'pk_test_51JwGHMEBydRe3lMmSMnKBfxpsc6QoqlBI7vQMsj53qfdPSNNq97yVUHEpUaoeckkrFIx2aFVTH8YZdYpxQSrGcya00je6gTKLD',
        // ];
        $this->paymentSettings = $this->getPaymentSettings();
        $stripe = ['secret_key' => $this->paymentSettings['privateKey'], 'publishable_key' => $this->paymentSettings['publishableKey']];

        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        function calculateOrderAmount(array $items): int
        {
            return (int)($items[0]->id) * 100;
        }
        header('Content-Type: application/json');
        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);

            // Create a PaymentIntent with amount and currency
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => calculateOrderAmount($jsonObj->items),
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            echo json_encode($output);
        } catch (Error $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function payment()
    {
        $this->paymentSettings = $this->getPaymentSettings();
        $stripe = ['secret_key' => $this->paymentSettings['privateKey'], 'publishable_key' => $this->paymentSettings['publishableKey']];
        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
        $orderId = $_GET['orderId'];
        if (empty($orderId)) {
            Message::addErrorMessage(Label::getLabel('STRIPE_INVALID_OrderId', $this->siteLangId));
            FatApp::redirectUser($session->cancel_url);
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if ($orderInfo["order_is_paid"] == Order::ORDER_IS_PAID) {
            if (isset($_SESSION['sponser'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId]));
            } else {
                FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
            }
        }
        $paymentGatewayCharge = $orderPaymentObj->getOrderPaymentGatewayAmount();

        $payableAmount = $this->formatPayableAmount($paymentGatewayCharge);
        $payment_comments = '';
        $totalPaidMatch = $session->amount_total == $payableAmount;

        if (strtolower($session->payment_status) != 'paid') {
            $payment_comments .= "STRIPE_PAYMENT :: Status is: " . strtolower($session->payment_status) . "\n\n";
        }
        if (!$totalPaidMatch) {
            $payment_comments .= "STRIPE_PAYMENT :: TOTAL PAID MISMATCH! " . strtolower($session->amount_total) . "\n\n";
        }
        $session->payment_status = 'paid';
        if (strtolower($session->payment_status) == 'paid' && $totalPaidMatch) {
            //echo $paymentGatewayCharge;exit;
            $orderPaymentObj->addOrderPayment($this->paymentSettings["pmethod_code"], $_GET['session_id'], $paymentGatewayCharge, 'Received Payment', serialize($session));
        } else {
            $orderPaymentObj->addOrderPaymentComments($payment_comments);
            FatApp::redirectUser($session->cancel_url);
        }
        if (isset($_SESSION['sponser'])) {
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId]));
        } else {
            FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
        }
    }

    public function webhook()
    {
        $payload = file_get_contents('php://input');
        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }
        $sessionId = $event->data->id;
        $this->updatePaymentStatus($sessionId);
    }

    private function updatePaymentStatus($sessionId)
    {
        $this->paymentSettings = $this->getPaymentSettings();
        $stripe = [
            'secret_key' => $this->paymentSettings['privateKey'],
            'publishable_key' => $this->paymentSettings['publishableKey']
        ];

        \Stripe\Stripe::setApiKey($stripe['secret_key']);
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        $orderId = $session->metadata->order_id;
        if (empty($orderId)) {
            Message::addErrorMessage(Label::getLabel('STRIPE_INVALID_OrderId', $this->siteLangId));
            FatApp::redirectUser($session->cancel_url);
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if ($orderInfo["order_is_paid"] == Order::ORDER_IS_PAID) {
            if (isset($_SESSION['sponser'])) {
                FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId]));
            } else {
                FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
            }
            // FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
        }
        $paymentGatewayCharge = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $payableAmount = $this->formatPayableAmount($paymentGatewayCharge);
        $payment_comments = '';
        $totalPaidMatch = $session->amount_total == $payableAmount;
        if (strtolower($session->payment_status) != 'paid') {
            $payment_comments .= "STRIPE_PAYMENT :: Status is: " . strtolower($session->payment_status) . "\n\n";
        }
        if (!$totalPaidMatch) {
            $payment_comments .= "STRIPE_PAYMENT :: TOTAL PAID MISMATCH! " . strtolower($session->amount_total) . "\n\n";
        }
        if (strtolower($session->payment_status) == 'paid' && $totalPaidMatch) {
            $orderPaymentObj->addOrderPayment($this->paymentSettings["pmethod_code"], $sessionId, $paymentGatewayCharge, 'Received Payment', serialize($session));
        } else {
            $orderPaymentObj->addOrderPaymentComments($payment_comments);
            FatApp::redirectUser($session->cancel_url);
        }
        if (isset($_SESSION['sponser'])) {
            FatApp::redirectUser(CommonHelper::generateUrl('EventUser', 'paymentSuccess', [$orderId]));
        } else {
            FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
        }
        // FatApp::redirectUser(CommonHelper::generateUrl('Custom', 'paymentSuccess', [$orderId]));
    }
}
