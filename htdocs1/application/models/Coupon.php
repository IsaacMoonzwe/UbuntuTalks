<?php

class Coupon extends MyAppModel
{

    const DB_TBL = 'tbl_coupons';
    const DB_TBL_PREFIX = 'coupon_';
    const DB_TBL_LANG = 'tbl_coupons_lang';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, 'coupon_id', $id);
    }

    public static function getByCode(string $code)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('coupon_code', '=', $code);
        $srch->addCondition('coupon_uses_count', '>', 0);
        $srch->addCondition('coupon_start_date', '<=', date('Y-m-d H:i:s'));
        $srch->addCondition('coupon_end_date', '>=', date('Y-m-d H:i:s'));
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

}
