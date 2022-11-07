<?php

class SentEmail extends MyAppModel
{

    const DB_TBL = 'tbl_email_archives';
    const DB_TBL_PREFIX = 'emailarchive_';

    const DB_TBL_CR = 'tbl_user_credentials';
    const DB_TBL_CR_PREFIX = 'credential_';

    public function __construct($adminId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $adminId);
        $this->objMainTableRecord->setSensitiveFields(['']);
    }

    public function getEmailSearchObj($attr = null, $joinUserCredentials = false, $keyword,$user_verified)
    {
        $srch = static::getSearchObject(true,$keyword ,$user_verified);
        $srch->addOrder('m.emailarchive_sent_on', 'ASC');
        $srch->joinTable(static::DB_TBL_CR, 'LEFT OUTER JOIN', 'uc.' . static::DB_TBL_CR_PREFIX . 'email = m.emailarchive_to_email', 'uc');
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addField($attr);
            }
        } else {
            $srch->addMultipleFields([
                'm.emailarchive_id',
                'm.emailarchive_to_email',
                'uc.emailarchive_to_email',
                'uc.credential_email',
                'uc.credential_verified',
                'uc.credential_active'
            ]);
        }
        return $srch;
    }

    public function getSearchObject($joinUserCredentials = false,$request,$user_verified=-1)
    {
        $srch = new SearchBase(static::DB_TBL, 'm'); 
       
        $srch->addOrder('m.emailarchive_sent_on', 'desc');
        $srch->joinTable(static::DB_TBL_CR, 'LEFT OUTER JOIN', 'uc.' . static::DB_TBL_CR_PREFIX . 'email = m.emailarchive_to_email', 'uc');
        if ($joinUserCredentials) {
            $srch->joinTable(static::DB_TBL_CR, 'LEFT OUTER JOIN', 'uc.' . static::DB_TBL_CR_PREFIX . 'email = m.emailarchive_to_email', 'uc');
             if($request!='')
             {
                $srch->addCondition('uc.' . static::DB_TBL_CR_PREFIX . 'email', 'LIKE',  '%' . $request . '%');
            }
            if($user_verified>-1){
            
                if( $user_verified >=0){
                    
                    $srch->addCondition('uc.' . static::DB_TBL_CR_PREFIX . 'active', 'LIKE',  '%' . $user_verified . '%');
                }
               
            }
        }  
        $srch->addOrder('m.emailarchive_sent_on', 'DESC');
        return $srch;
    }

}
