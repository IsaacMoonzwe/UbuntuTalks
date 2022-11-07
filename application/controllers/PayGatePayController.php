<?php

class PayGatePayController extends PaymentController
{

    protected $keyName = "PayGate";
    private $initiateUrl = 'https://secure.paygate.co.za/payweb3/initiate.trans';
    private $processUrl = 'https://secure.paygate.co.za/payweb3/process.trans';
    private $queryUrl = 'https://secure.paygate.co.za/payweb3/query.trans';
    private $error;
    private $isError;
    private $orderInfo;
    public $settings;

    //  out of these six status only there are used 
    const STATUS_APPROVED = 1;  // in use
    const STATUS_DECLINED = 2; //  in use
    const STATUS_CANCELLED = 3;
    const STATUS_USER_CANCELLED = 4; // in used
    const STATUS_RECEIVED_BY_PAYGATE = 5;
    const STATUS_SETTLEMENT_VOIDED = 7;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->error = '';
        $this->isError = false;
    }

    public function charge(string $orderId = '')
    {
        if ($orderId == '') {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $this->settings = $this->getSettings();
        if ($this->isError()) {
            Message::addErrorMessage($this->getError());
            CommonHelper::redirectUserReferer();
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $paymentAmount = $orderPaymentObj->getOrderPaymentGatewayAmount();
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if (empty($orderInfo) || $orderInfo["order_is_paid"] != Order::ORDER_IS_PENDING) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_ORDER_PAID_CANCELLED', $this->siteLangId));
            CommonHelper::redirectUserReferer();
        }
        $orderInfo['paymentAmount'] = $paymentAmount;
        $this->orderInfo = $orderInfo;
        $initiateRequestData = $this->initiate();
        if ($this->isError()) {
            Message::addErrorMessage($this->getError());
            CommonHelper::redirectUserReferer();
        }
        $processForm = $this->getProcessForm($initiateRequestData['PAY_REQUEST_ID'], $initiateRequestData['CHECKSUM']);
        $this->set('form', $processForm);
        $this->set('paymentAmount', $paymentAmount);
        $this->set('siteLangId', $this->siteLangId);
        $this->set('exculdeMainHeaderDiv', false);
        $this->set('orderInfo', $orderInfo);
        $this->_template->render(true, false);
    }

    private function initiate(): array
    {
        $settings = $this->settings;
        $orderInfo = $this->orderInfo;
        $orderId = $orderInfo['order_id'];
        $dateTime = new DateTime();
        $requestData = [
            'PAYGATE_ID' => $settings['paygateId'],
            'REFERENCE' => $orderId,
            'AMOUNT' => $this->formatPayableAmount($orderInfo['paymentAmount']),
            'CURRENCY' => $orderInfo["order_currency_code"], // system currency code
            'RETURN_URL' => CommonHelper::generateFullUrl('PayGatePay', 'returnResult', [$orderId]),
            'TRANSACTION_DATE' => $dateTime->format('Y-m-d H:i:s'),
            'LOCALE' => strtolower($orderInfo['order_language_code']),
            'COUNTRY' => 'ZAF',
            'EMAIL' => $orderInfo['user_email'],
            'NOTIFY_URL' => CommonHelper::generateFullUrl('PayGatePay', 'callback')
        ];
        $checksum = $this->generateChecksum($requestData, $settings['encryptionKey']);
        $requestData['CHECKSUM'] = $checksum;
        $initiateRequestData = $this->doCurlPost($requestData, $this->initiateUrl);
        if ($this->isError()) {
            return [];
        }
        return $initiateRequestData;
    }

    public function returnResult(string $orderId = '')
    {
        $postData = FatApp::getPostedData();
        if (empty($postData) || $orderId == '') {
            Message::addErrorMessage(Label::getLabel('MSG_Invalid_Access', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::getPaymentCancelPageUrl());
        }
        $this->settings = $this->getSettings();
        if ($this->isError()) {
            Message::addErrorMessage($this->getError());
            FatApp::redirectUser(CommonHelper::getPaymentCancelPageUrl());
        }
        $orderPaymentObj = new OrderPayment($orderId, $this->siteLangId);
        $orderInfo = $orderPaymentObj->getOrderPrimaryinfo();
        if (empty($orderInfo)) {
            Message::addErrorMessage(Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId));
            FatApp::redirectUser(CommonHelper::getPaymentCancelPageUrl());
        }
        $payRequestId = FatApp::getPostedData('PAY_REQUEST_ID', FatUtility::VAR_STRING, '');
        $transactionStatus = FatApp::getPostedData('TRANSACTION_STATUS', FatUtility::VAR_INT, -1);
        $checksum = FatApp::getPostedData('CHECKSUM', FatUtility::VAR_STRING, '');
        $data = [
            'PAYGATE_ID' => $this->settings['paygateId'],
            'PAY_REQUEST_ID' => $payRequestId,
            'TRANSACTION_STATUS' => $transactionStatus,
            'REFERENCE' => $orderId
        ];
        $this->validateChecksum($checksum, $data, $this->settings['encryptionKey']);
        if ($this->isError()) {
            Message::addErrorMessage($this->getError());
            FatApp::redirectUser(CommonHelper::getPaymentCancelPageUrl());
        }
        if ($transactionStatus != self::STATUS_APPROVED) {
            $statusArr = $this->getTransactionStatus();
            Message::addErrorMessage(Label::getLabel('MSG_Your_Paymet_Status_' . $statusArr[$transactionStatus]));
            FatApp::redirectUser(CommonHelper::getPaymentCancelPageUrl());
        }
        if (isset($_SESSION['event'])) {
            FatApp::redirectUser(CommonHelper::generateFullUrl('EventUser', 'paymentSuccess', [$orderId]));
        } else {
            FatApp::redirectUser(CommonHelper::generateFullUrl('custom', 'paymentSuccess', [$orderId]));
        }
        exit;
    }

    public function callback()
    {
        $postData = FatApp::getPostedData();
        if (empty($postData)) {
            die(Label::getLabel('MSG_INVALID_REQUEST'));
        }
        $settings = $this->getSettings();
        if ($this->isError()) {
            die($this->getError());
        }
        $orderId = FatApp::getPostedData('REFERENCE', FatUtility::VAR_STRING, '');
        $payRequestId = FatApp::getPostedData('PAY_REQUEST_ID', FatUtility::VAR_STRING, '');
        $orderPayment = new OrderPayment($orderId, $this->siteLangId);
        $orderInfo = $orderPayment->getOrderPrimaryinfo();
        if (empty($orderInfo) || $orderInfo["order_is_paid"] != Order::ORDER_IS_PENDING) {
            die(Label::getLabel('MSG_INVALID_REQUEST', $this->siteLangId));
        }
        $paymentAmount = $orderPayment->getOrderPaymentGatewayAmount();
        $queryData = [
            'PAYGATE_ID' => $settings['paygateId'],
            'PAY_REQUEST_ID' => $payRequestId,
            'REFERENCE' => $orderId
        ];
        $checksum = $this->generateChecksum($queryData, $settings['encryptionKey']);
        $queryData['CHECKSUM'] = $checksum;
        $queryResponseData = $this->doCurlPost($queryData, $this->queryUrl);
        if ($this->isError()) {
            die($this->getError());
        }
        if (isset($queryResponseData['TRANSACTION_STATUS'])) {
            $status = $queryResponseData['TRANSACTION_STATUS'];
            switch ($queryResponseData['TRANSACTION_STATUS']) {
                case self::STATUS_APPROVED:
                    $status = Order::ORDER_IS_PAID;
                    break;
                case self::STATUS_CANCELLED:
                case self::STATUS_USER_CANCELLED:
                case self::STATUS_DECLINED:
                    $status = Order::ORDER_IS_CANCELLED;
                    break;
                default:
                    $status = Order::ORDER_IS_PENDING;
                    break;
            }
            $response = json_encode($postData);
            if ($status == Order::ORDER_IS_PAID) {
                $orderPayment->addOrderPayment($this->keyName, $queryResponseData['PAY_REQUEST_ID'], $paymentAmount, 'Received Payment', $response);
            } else {
                $orderPayment->addOrderPaymentComments($response);
            }
        }
    }

    private function getError()
    {
        return $this->error;
    }

    private function isError()
    {
        return $this->isError;
    }

    private function getTransactionStatus(): array
    {
        return [
            self::STATUS_APPROVED => Label::getLabel('LBL_Approved'), // in used
            self::STATUS_DECLINED => Label::getLabel('LBL_Declined'), // in used
            self::STATUS_CANCELLED => Label::getLabel('LBL_Cancelled'),
            self::STATUS_USER_CANCELLED => Label::getLabel('LBL_Cancelled'), // USER Cancelled // in used
            self::STATUS_RECEIVED_BY_PAYGATE => Label::getLabel('LBL_Received_By_PayGate'),
            self::STATUS_SETTLEMENT_VOIDED => Label::getLabel('LBL_Settlement_Voided')
        ];
    }

    private function doCurlPost(array $postData, string $url): array
    {
        $fields_string = http_build_query($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $curlResult = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->isError = true;
            $this->error = 'Error:' . curl_error($ch);
            return [];
        }
        curl_close($ch);
        parse_str($curlResult, $result);
        if (array_key_exists('ERROR', $result)) {
            $this->isError = true;
            $this->error = $result['ERROR'];
            return [];
        }
        return $result;
    }

    private function formatPayableAmount($amount = 0)
    {
        $amount = number_format($amount, 2, '.', '');
        return $amount * 100;
    }

    private function generateChecksum($data, $encryptionKey)
    {
        $checksum = '';
        foreach ($data as $key => $value) {
            if ($value != '') {
                $checksum .= $value;
            }
        }
        $checksum .= $encryptionKey;
        return md5($checksum);
    }

    private function validateChecksum($returnedChecksum, $data, $encryptionKey): bool
    {
        $checksum = $this->generateChecksum($data, $encryptionKey);
        if ($returnedChecksum != $checksum) {
            $this->isError = true;
            $this->error = Label::getLabel('MSG_CHECKSUM_NOT_VALID', $this->siteLangId);
            return false;
        }
        return true;
    }

    private function getSettings(): array
    {
        $paymentSetting = new PaymentSettings($this->keyName);
        $getSettings = $paymentSetting->getPaymentSettings();
        if (empty($getSettings['paygateId']) || empty($getSettings['encryptionKey'])) {
            $this->isError = true;
            $this->error = Label::getLabel('MSG_Encryption_Key_And_Paygate_Id_is_required_for_payment');
            return [];
        }
        return $getSettings;
    }

    private function getProcessForm(string $requestId, string $checksum): object
    {
        $form = new Form('payGateProcessForm', ['id' => 'payGateProcessForm', 'action' => $this->processUrl]);
        $form->addHiddenField('', 'PAY_REQUEST_ID', $requestId);
        $form->addHiddenField('', 'CHECKSUM', $checksum);
        return $form;
    }
}
