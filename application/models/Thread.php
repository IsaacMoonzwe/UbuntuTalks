<?php

class Thread extends MyAppModel
{

    const DB_TBL = 'tbl_threads';
    const DB_TBL_PREFIX = 'thread_';
    const DB_TBL_THREAD_MESSAGES = 'tbl_thread_messages';
    const DB_TBL_THREAD_USERS = 'tbl_thread_users';
    const MESSAGE_IS_READ = 0;
    const MESSAGE_IS_UNREAD = 1;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL, 't');
        return $srch;
    }

    public function addThreadMessages($data)
    {
        if (empty($data)) {
            return false;
        }
        if (!FatApp::getDb()->insertFromArray(Thread::DB_TBL_THREAD_MESSAGES, $data)) {
            $this->error = FatApp::getDb()->getError();
        }
        return FatApp::getDb()->getInsertId();
    }

    public function markUserMessageRead($threadId, $userId)
    {
        if (FatApp::getDb()->updateFromArray('tbl_thread_messages', ['message_is_unread' => self::MESSAGE_IS_READ], ['smt' => '`message_thread_id`=? AND `message_to`=? ', 'vals' => [$threadId, $userId]])) {
            return true;
        }
        $this->error = FatApp::getDb()->getError();
        return false;
    }

    public function getThreadId($userArr)
    {
        $srch = new SearchBase(static::DB_TBL_THREAD_USERS);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('threaduser_id', 'IN', $userArr);
        $srch->addGroupBy('threaduser_thread_id');
        $srch->addHaving('mysql_func_count(distinct threaduser_id)', '>', 1, 'AND', true);
        $srch->addMultipleFields(['threaduser_thread_id']);
        $res = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($res['threaduser_thread_id'])) {
            return self::isThreadExist($res['threaduser_thread_id']);
        }
        return $this->createThread($userArr);
    }

    public function createThread($data)
    {
        if (empty($data)) {
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $threadObj = new Thread();
        $threadDataToSave = ['thread_start_date' => date('Y-m-d H:i:s')];
        $threadObj->assignValues($threadDataToSave);
        if (!$threadObj->save()) {
            $this->error = $threadObj->getError();
            return false;
        }
        foreach ($data as $id) {
            $threadUserArr = [];
            $threadUserArr['threaduser_id'] = $id;
            $threadUserArr['threaduser_thread_id'] = $threadObj->mainTableRecordId;
            if (!$db->insertFromArray(Thread::DB_TBL_THREAD_USERS, $threadUserArr)) {
                $this->error = $db->getError();
                return false;
            }
        }
        $db->commitTransaction();
        return $threadObj->mainTableRecordId;
    }

    public static function isThreadExist($threadId)
    {
        $srch = static::getSearchObject();
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('thread_id', '=', $threadId);
        $res = FatApp::getDb()->fetch($srch->getResultSet());
        return $res['thread_id'] ?? 0;
    }

    public static function getThreads($userId)
    {
        $srch = new MessageSearch();
        $srch->joinLatestThreadMessage();
        $srch->joinMessagePostedFromUser();
        $srch->joinMessagePostedToUser();
        $srch->addMultipleFields(['tth.*', 'ttm.message_id', 'ttm.message_text', 'ttm.message_date',
            '(CASE WHEN tfr.user_id = ' . $userId . ' THEN 0 ELSE ttm.message_is_unread END) AS message_is_unread', 'ttm.message_to',]);
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $cnd = $srch->addCondition('ttm.message_from', '=', $userId);
        $cnd->attachCondition('ttm.message_to', '=', $userId, 'OR');
        $srch->addOrder('ttm.message_date', 'Desc');
        return $srch;
    }

    public static function getThreadUsers($threadId)
    {
        $srch = new SearchBase(static::DB_TBL_THREAD_USERS);
        $srch->addCondition('threaduser_thread_id', '=', $threadId);
        $srch->addMultipleFields(['threaduser_id']);
        $res = FatApp::getDb()->fetchAll($srch->getResultSet(), 'threaduser_id');
        return array_keys($res);
    }

}
