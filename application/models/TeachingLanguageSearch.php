<?php

class TeachingLanguageSearch extends SearchBase
{

    public function __construct($langId = 0)
    {
        parent::__construct(TeachingLanguage::DB_TBL, 'tl');
        $langId = FatUtility::int($langId);
        if ($langId > 0) {
            $this->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT OUTER JOIN', 'tl_l.tlanguagelang_tlanguage_id = tl.tlanguage_id AND tl_l.tlanguagelang_lang_id = ' . $langId, 'tl_l');
        }
    }

    public function addChecks()
    {
        $this->addCondition('tlanguage_active', '=', applicationConstants::ACTIVE);
        $this->doNotCalculateRecords();
        $this->addMultipleFields(['tlanguage_id', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name']);
    }

    public function joinActiveTeachers()
    {
        $this->joinTable(UserTeachLanguage::DB_TBL, 'INNER JOIN', 'tl.tlanguage_id = utl.utl_tlanguage_id', 'utl');
        $this->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utl_id = utl.utl_id', 'ustelgpr');
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = utl.utl_user_id', 'u');
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
    }

    public function joinOrderProduct()
    {
        $this->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'tl.tlanguage_id = op.op_tlanguage_id', 'op');
        $this->joinTable(Order::DB_TBL, 'INNER JOIN', 'op.op_order_id = o.order_id', 'o');
    }


}
