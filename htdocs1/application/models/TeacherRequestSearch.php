<?php

class TeacherRequestSearch extends SearchBase
{

    private $isUserJoined = false;

    public function __construct($joinUsers = true)
    {
        parent::__construct(TeacherRequest::DB_TBL, 'tr');
        if (true === $joinUsers) {
            $this->joinUsers();
        }
    }

    public function joinUsers()
    {
        if (true === $this->isUserJoined) {
            return;
        }
        $this->isUserJoined = true;
        $this->joinTable(User::DB_TBL, 'INNER JOIN', 'u.user_id = tr.utrequest_user_id', 'u');
    }

    public function joinUserCredentials()
    {
        if (false === $this->isUserJoined) {
            trigger_error("User Table is not joined", E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL_CRED, 'INNER JOIN', 'u.user_id = cred.credential_user_id', 'cred');
    }

}
