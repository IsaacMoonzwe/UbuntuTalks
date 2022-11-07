<?php

class PaypalPayoutController extends MyAppController
{

    public function callback()
    {
        $webhookData = file_get_contents('php://input');
        $webhookData = json_decode($webhookData, true);
        $event_type = $webhookData['event_type'];
        switch ($event_type) {
            case "PAYMENT.PAYOUTS-ITEM.SUCCEEDED":
                $withdrawStatus = Transaction::WITHDRAWL_STATUS_COMPLETED;
                $trxnStatus = Transaction::STATUS_COMPLETED;
                if (!$this->updatePayoutWithdrawRequest($webhookData, $withdrawStatus, $trxnStatus)) {
                    Message::addErrorMessage('Error');
                    FatUtility::dieJsonError(Message::getHtml());
                }
                break;
            case "PAYMENT.PAYOUTS-ITEM.CANCELED":
                $withdrawStatus = Transaction::WITHDRAWL_STATUS_DECLINED;
                $trxnStatus = Transaction::STATUS_DECLINED;
                if (!$this->updatePayoutWithdrawRequest($webhookData, $withdrawStatus, $trxnStatus)) {
                    Message::addErrorMessage('Error');
                    FatUtility::dieJsonError(Message::getHtml());
                }
                break;
            case "PAYMENT.PAYOUTS-ITEM.DENIED":
                $withdrawStatus = Transaction::WITHDRAWL_STATUS_DECLINED;
                $trxnStatus = Transaction::STATUS_DECLINED;
                if (!$this->updatePayoutWithdrawRequest($webhookData, $withdrawStatus, $trxnStatus)) {
                    Message::addErrorMessage('Error');
                    FatUtility::dieJsonError(Message::getHtml());
                }
                break;
            case "PAYMENT.PAYOUTS-ITEM.FAILED":
                $withdrawStatus = Transaction::WITHDRAWL_STATUS_PAYOUT_FAILED;
                $trxnStatus = Transaction::STATUS_DECLINED;
                if (!$this->updatePayoutWithdrawRequest($webhookData, $withdrawStatus, $trxnStatus)) {
                    Message::addErrorMessage('Error');
                    FatUtility::dieJsonError(Message::getHtml());
                }
                break;
        }
    }

    public function updatePayoutWithdrawRequest($requestData, $status, $trxnStatus)
    {
        if (empty($requestData)) {
            return false;
        }
        $transaction_id = $requestData['resource']['transaction_id'];
        $payout_item_id = $requestData['resource']['payout_item_id'];
        $payout_batch_id = $requestData['resource']['payout_batch_id'];
        $sender_batch_id = $requestData['resource']['sender_batch_id'];
        $arryId = explode('_', $sender_batch_id);
        $withdrawalId = end($arryId);
        $withdrawalId = FatUtility::int($withdrawalId);
        $assignFields = ['withdrawal_status' => $status, 'withdrawal_response' => json_encode($requestData)];
        if (!FatApp::getDb()->updateFromArray(User::DB_TBL_USR_WITHDRAWAL_REQ, $assignFields, ['smt' => 'withdrawal_id=?', 'vals' => [$withdrawalId]])) {
            return false;
        }
        FatApp::getDb()->updateFromArray(Transaction::DB_TBL, ["utxn_status" => $trxnStatus], ['smt' => 'utxn_withdrawal_id=?', 'vals' => [$withdrawalId]]);
        return true;
    }

}
