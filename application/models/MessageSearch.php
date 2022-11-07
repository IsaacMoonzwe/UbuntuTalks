<?php

class MessageSearch extends SearchBase
{

    private $langId;
    private $joinThreadMessage = false;
    private $joinOrderProducts = false;

    public function __construct()
    {
        parent::__construct(Thread::DB_TBL, 'tth');
    }

    public function joinThreadMessage()
    {
        $this->joinThreadMessage = true;
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'tth.thread_id = ttm.message_thread_id', 'ttm');
    }

    public function joinLatestThreadMessage()
    {
        $this->joinThreadMessage = true;
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'tth.thread_id = ttm.message_thread_id', 'ttm');
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'ttm_temp.message_id > ttm.message_id AND ttm_temp.message_thread_id = ttm.message_thread_id', 'ttm_temp');
        $this->addDirectCondition('ttm_temp.message_id IS NULL');
    }

    public function joinMessagePostedFromUser()
    {
        if (!$this->joinThreadMessage) {
            trigger_error('You have not joined joinThreadMessage.', E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'ttm.message_from = tfr.user_id', 'tfr');
        $this->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'tfr_c.credential_user_id = tfr.user_id', 'tfr_c');
        $this->addMultipleFields(['tfr.user_id as message_from_user_id', 'tfr.user_first_name as message_from_name', 'tfr_c.credential_email as message_from_email', 'tfr_c.credential_username as message_from_username']);
    }

    public function joinMessagePostedToUser()
    {
        if (!$this->joinThreadMessage) {
            trigger_error('You have not joined joinThreadMessage.', E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'ttm.message_to = tfto.user_id', 'tfto');
        $this->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'tfto_c.credential_user_id = tfto.user_id', 'tfto_c');
        $this->addMultipleFields(['tfto.user_id as message_to_user_id', 'tfto.user_first_name as message_to_name', 'tfto_c.credential_email as message_to_email', 'tfto_c.credential_username as message_to_username']);
    }

}
