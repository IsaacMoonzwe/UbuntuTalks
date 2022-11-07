<?php

class Currency extends MyAppModel
{

    const DB_TBL = 'tbl_currencies';
    const DB_TBL_PREFIX = 'currency_';
    const DB_TBL_LANG = 'tbl_currencies_lang';
    const DB_TBL_LANG_PREFIX = 'currencylang_';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
        $this->objMainTableRecord->setSensitiveFields(['currency_is_default']);
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 'curr');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'curr_l.' . static::DB_TBL_LANG_PREFIX . 'currency_id = curr.' . static::tblFld('id') . ' and curr_l.' . static::DB_TBL_LANG_PREFIX . 'lang_id = ' . $langId, 'curr_l');
        }
        if ($isActive) {
            $srch->addCondition('curr.currency_active', '=', 1);
        }
        return $srch;
    }

    public static function getDefaultCurrencyData(int $langId = 0): array
    {
        $row = Currency::getAttributesById(FatApp::getConfig('CONF_CURRENCY'));
        if (empty($row)) {
            trigger_error(Label::getLabel('ERR_Default_currency_not_specified.', CommonHelper::getLangId()), E_USER_ERROR);
        }
        return $row;
    }

    public static function getSystemCurrencyData(int $langId = 0): array
    {
        $searchObject = self::getSearchObject($langId);
        $searchObject->addCondition('currency_is_default', '=', applicationConstants::YES);
        $searchObject->addCondition('currency_value', '=', 1);
        $searchObject->addMultipleFields(['" " as currency_name', 'currency_id', 'currency_code', 'currency_value', 'currency_symbol_right', 'currency_symbol_left']);
        if ($langId > 0) {
            $searchObject->addMultipleFields(['currency_name']);
        }
        $resultSet = $searchObject->getResultSet();
        $systemCurrency = FatApp::getDb()->fetch($resultSet);
        if (empty($systemCurrency)) {
            trigger_error(Label::getLabel('ERR_System_currency_not_specified.', CommonHelper::getLangId()), E_USER_ERROR);
        }
        return $systemCurrency;
    }

    public static function getListingObj($langId, $attr = null)
    {
        $srch = self::getSearchObject($langId);
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $srch->addMultipleFields(['IFNULL(curr_l.currency_name,curr.currency_code) as currency_name']);
        return $srch;
    }

    public static function getCurrencyAssoc($langId)
    {
        $langId = FatUtility::int($langId);
        $srch = self::getListingObj($langId, ['currency_id', 'currency_code']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetchAllAssoc($rs);
        if (!is_array($row)) {
            return false;
        }
        return $row;
    }

    public static function getCurrencyNameWithCode($langId)
    {
        $langId = FatUtility::int($langId);
        $srch = self::getSearchObject($langId);
        $srch->addMultipleFields(['currency_id', 'CONCAT(IFNULL(curr_l.currency_name,curr.currency_code)," (",currency_code ,")") as currency_name_code']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $row = FatApp::getDb()->fetchAllAssoc($srch->getResultSet(), 'currency_id');
        if (!is_array($row)) {
            return false;
        }
        return $row;
    }

}
