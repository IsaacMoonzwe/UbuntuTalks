<?php

class PaypalPayout
{

    const ACCESS_TOKEN_URL_TEST = 'https://api.sandbox.paypal.com/v1/oauth2/token';
    const ACCESS_TOKEN_URL_LIVE = 'https://api.paypal.com/v1/oauth2/token';
    const PAYOUT_URL_TEST = 'https://api.sandbox.paypal.com/v1/payments/payouts';
    const PAYOUT_URL_LIVE = 'https://api.paypal.com/v1/payments/payouts';
    const KEY_NAME = 'PaypalPayout';

    private $error;
    private $isError;
    private $commonLangId;
    private $currenciesAccepted = [
        'Australian Dollar' => 'AUD',
        'Brazilian Real' => 'BRL',
        'Canadian Dollar' => 'CAD',
        'Czech Koruna' => 'CZK',
        'Danish Krone' => 'DKK',
        'Euro' => 'EUR',
        'Hong Kong Dollar' => 'HKD',
        'Hungarian Forint' => 'HUF',
        'Israeli New Sheqel' => 'ILS',
        'Malaysian Ringgit' => 'MYR',
        'Mexican Peso' => 'MXN',
        'Norwegian Krone' => 'NOK',
        'New Zealand Dollar' => 'NZD',
        'Philippine Peso' => 'PHP',
        'Polish Zloty' => 'PLN',
        'Pound Sterling' => 'GBP',
        'Russian Ruble' => 'RUB',
        'Singapore Dollar' => 'SGD',
        'Swedish Krona' => 'SEK',
        'Swiss Franc' => 'CHF',
        'Taiwan New Dollar' => 'TWD',
        'Thai Baht' => 'THB',
        'U.S. Dollar' => 'USD',
    ];

    public function __construct()
    {
        $this->error = '';
        $this->isError = false;
        $this->commonLangId = CommonHelper::getLangId();
    }

    function getError()
    {
        return $this->error;
    }

    function isError()
    {
        return $this->isError;
    }

    public static function isMethodActive()
    {
        $paymentSettings = new PaymentSettings(static::KEY_NAME);
        $data = $paymentSettings->getPaymentSettings();
        return (bool) ($data['pmethod_active'] ?? false);
    }

    public function accessToken(string $clientid, string $clientsecret): array
    {
        if (empty($clientid) || empty($clientsecret)) {
            $this->isError = true;
            $this->error = Label::getLabel('MSG_Paypal_Client_id_And_Secret_is_required_for_payout');
            return [];
        }
        $actionUrl = (FatApp::getConfig('CONF_TRANSACTION_MODE', FatUtility::VAR_BOOLEAN, false) == true) ? self::ACCESS_TOKEN_URL_LIVE : self::ACCESS_TOKEN_URL_TEST;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $actionUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, $clientid . ':' . $clientsecret);
        $headers = [];
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->isError = true;
            $this->error = 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        $accessTokenResponse = json_decode($result, true);
        if (!array_key_exists('access_token', $accessTokenResponse)) {
            $this->isError = true;
            $this->error = $accessTokenResponse['error'] . ' : ' . $accessTokenResponse['error_description'];
            return [];
        }
        return $accessTokenResponse;
    }

    //releasePayout
    public function sendRequest(string $token, array $requestData): array
    {
        $requestData = json_encode($requestData);
        $actionUrl = (FatApp::getConfig('CONF_TRANSACTION_MODE', FatUtility::VAR_BOOLEAN, false) == true) ? self::PAYOUT_URL_LIVE : self::PAYOUT_URL_TEST;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $actionUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->isError = true;
            $this->error = 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        return json_decode($result, true);
    }

    public function getSettings(): array
    {
        $pmObj = new PaymentSettings(self::KEY_NAME);
        $paymentSettings = $pmObj->getPaymentSettings();
        if (empty($paymentSettings['paypal_client_id']) || empty($paymentSettings['paypal_client_secret'])) {
            $this->isError = true;
            $this->error = Label::getLabel('MSG_Paypal_Client_id_And_Secret_is_required_for_payout');
            return [];
        }
        return $paymentSettings;
    }

    public function releasePayout(array $recordData): bool
    {
        $settings = $this->getSettings();
        if ($this->isError()) {
            return false;
        }
        $accessTokenResponse = $this->accessToken($settings['paypal_client_id'], $settings['paypal_client_secret']);
        if ($this->isError()) {
            return false;
        }
        $access_token = $accessTokenResponse['access_token'];
        $currencyData = Currency::getSystemCurrencyData();
        if (!in_array($currencyData["currency_code"], $this->currenciesAccepted)) {
            $this->isError = true;
            $this->error = Label::getLabel('MSG_INVALID_ORDER_CURRENCY_PASSED_TO_GATEWAY');
            return false;
        }
        $gatewayFee = $recordData['gatewayFee'];
        $sender_batch_id = "Payout_" . time() . '_' . $recordData['withdrawal_id'];
        $note = Label::getLabel('MSG_Transaction_Fee_Charged_:') . ' ' . CommonHelper::displayMoneyFormat($gatewayFee, true, true);
        $amount = round($recordData['amount'], 2);
        $requestData = [
            "sender_batch_header" => [
                "sender_batch_id" => $sender_batch_id,
                "email_subject" => Label::getLabel('MSG_You_have_a_payout!!'),
                "email_message" => Label::getLabel('MSG_You_have_a_Received_a_payout')
            ],
            "items" => [
                [
                    "recipient_type" => "EMAIL",
                    "amount" => [
                        "value" => (string) $recordData['amount'],
                        "currency" => $currencyData['currency_code']
                    ],
                    "note" => $note,
                    "sender_item_id" => time() . '_' . $recordData['withdrawal_id'],
                    "receiver" => $recordData['withdrawal_paypal_email_id'],
                ]]
        ];
        $response = $this->sendRequest($access_token, $requestData);
        if ($this->isError()) {
            $this->isError = true;
            $this->error = $this->getError();
            return false;
        }
        if (!array_key_exists('batch_header', $response)) {
            if (array_key_exists('message', $response)) {
                $message = $response['name'] . ' : ' . $response['message'];
            } else {
                $message = $response['details'][0]['issue'];
            }
            $this->isError = true;
            $this->error = $message;
            return false;
        }
        $db = FatApp::getDb();
        $assignFields = [
            'withdrawal_status' => Transaction::WITHDRAWL_STATUS_PAYOUT_SENT,
            'withdrawal_transaction_fee' => $gatewayFee,
            'withdrawal_response' => json_encode($response)
        ];
        if (!$db->updateFromArray(User::DB_TBL_USR_WITHDRAWAL_REQ, $assignFields, ['smt' => 'withdrawal_id=?', 'vals' => [$recordData['withdrawal_id']]])) {
            $this->isError = true;
            $this->error = $db->getError();
            return false;
        }
        if ($gatewayFee > 0) {
            Transaction::updateTransactionFeeMessage($recordData['withdrawal_id'], $gatewayFee);
        }
        return true;
    }

}
