<?php

class UrlRewriteSearch extends SearchBase
{

    public function __construct()
    {
        parent::__construct(UrlRewrite::DB_TBL, 'ur');
    }

}
