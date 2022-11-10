<?php

class GdprReqSearch extends SearchBase
{

    public function __construct()
    {
        parent::__construct(GdprRequest::DB_TBL, 'gdpr');
    }

    public function joinUser($joinCredentials = false)
    {
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'gdpr.gdprdatareq_user_id = u.user_id', 'u');
        $joinCredentials && $this->joinTable(User::DB_TBL_LANG, 'LEFT OUTER JOIN', 'uc.credential_user_id = u.user_id', 'uc');
    }

}
