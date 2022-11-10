<?php

class PaymentMethodTransactionFee extends MyAppModel
{

    const DB_TBL = 'tbl_payment_method_transaction_fee';
    const DB_TBL_PREFIX = 'pmtfee_';
    const FEE_TYPE_PERCENTAGE = 1; // save the fee as percentage e.g 3%
    const FEE_TYPE_FLAT = 2; //  save the fee as amount $3

    private $currancyId;
    private $pMethodId;
    public $feeType;

    public function __construct(int $pMethodId, int $currancyId)
    {
        $this->currancyId = $currancyId;
        $this->pMethodId = $pMethodId;
        $this->feeType = 0;
    }

    public static function getSearchObject(bool $joinPaymentMethod = true, bool $joinCurrancy = true): object
    {
        $srch = new SearchBase(static::DB_TBL, 'pmtfee');
        if ($joinPaymentMethod) {
            $srch->joinTable(PaymentMethods::DB_TBL, 'INNER JOIN', 'pmtfee_pmethod_id = pmethod_id', 'pm');
        }
        if ($joinCurrancy) {
            $srch->joinTable(Currency::DB_TBL, 'INNER JOIN', 'pmtfee_currency_id = currency_id', 'curr');
        }
        return $srch;
    }

    public static function feeTypeArray(int $langId = 0): array
    {
        $langId = ($langId > 0) ? $langId : CommonHelper::getLangId();
        return [
            self::FEE_TYPE_PERCENTAGE => Label::getLabel('LBL_percentage', $langId),
            self::FEE_TYPE_FLAT => Label::getLabel('LBL_FLAT', $langId),
        ];
    }

    public function getGatewayFee(): float
    {
        $srch = static::getSearchObject();
        $srch->addCondition('pmtfee_pmethod_id', '=', $this->pMethodId);
        $srch->addCondition('pmtfee_currency_id', '=', $this->currancyId);
        $srch->addMultipleFields(['pmtfee.*']);
        $resultSet = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($resultSet);
        $this->feeType = (empty($data['pmtfee_type'])) ? self::FEE_TYPE_PERCENTAGE : $data['pmtfee_type'];
        if (empty($data['pmtfee_fee'])) {
            return 0.00;
        }
        return FatUtility::float($data['pmtfee_fee']);
    }

    public function setupFee(float $fee, int $type = self::FEE_TYPE_PERCENTAGE): bool
    {
        $tableRecordObj = new TableRecord(self::DB_TBL);
        $fields = [
            'pmtfee_pmethod_id' => $this->pMethodId,
            'pmtfee_currency_id' => $this->currancyId,
            'pmtfee_fee' => $fee,
            'pmtfee_type' => $type,
        ];
        $tableRecordObj->setFlds($fields);
        if ($tableRecordObj->addNew([], $fields) === false) {
            $this->error = $tableRecordObj->getError();
            return false;
        }
        return true;
    }

}
