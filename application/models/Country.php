<?php

class Country extends MyAppModel
{

    const DB_TBL = 'tbl_countries';
    const DB_TBL_PREFIX = 'country_';
    const DB_TBL_LANG = 'tbl_countries_lang';
    const DB_TBL_LANG_PREFIX = 'countrylang_';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
    }

    public static function getSearchObject($isActive = true, $langId = 0)
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 'c');
        if ($isActive == true) {
            $srch->addCondition('c.' . static::DB_TBL_PREFIX . 'active', '=', applicationConstants::ACTIVE);
        }
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'c_l.' . static::DB_TBL_LANG_PREFIX . 'country_id = c.' . static::tblFld('id') . ' and c_l.' . static::DB_TBL_LANG_PREFIX . 'lang_id = ' . $langId, 'c_l');
        }
        return $srch;
    }

    public function getCountriesArr($langId, $isActive = true)
    {
        $langId = FatUtility::int($langId);
        $srch = static::getSearchObject($isActive, $langId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addOrder('country_name', 'ASC');
        $srch->addMultipleFields(['country_id', 'if(country_name is null,country_code,country_name)as country_name']);
        $row = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        if (!is_array($row)) {
            return false;
        }
        return $row;
    }

    public static function getCountryByCode($countryCode, $attr = null)
    {
        if (!$countryCode) {
            return false;
        }
        $srch = static::getSearchObject();
        $srch->addCondition('country_code', '=', strtoupper($countryCode));
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public static function getCountryById(int $countryId): array
    {
        $langId = CommonHelper::getLangId();
        $srch = new SearchBase(static::DB_TBL, 'c');
        $srch->addMultipleFields(['country_id','country_code', 'IFNULL(country_name, "") as country_name']);
        $srch->joinTable(Country::DB_TBL_LANG, 'LEFT join', 'country_id = countrylang_country_id and countrylang_lang_id = ' . $langId);
        $srch->addCondition('country_id', '=', $countryId);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row)) {
            return [];
        }
        return $row;
    }

}
