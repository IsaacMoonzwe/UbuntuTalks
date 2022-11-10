<?php

class User extends MyAppModel
{

    const ADMIN_SESSION_ELEMENT_NAME = 'yoCoachAdmin';
    const DB_TBL = 'tbl_users';
    const DB_TBL_PREFIX = 'user_';
    const DB_TBL_CRED = 'tbl_user_credentials';
    const DB_TBL_CRED_PREFIX = 'credential_';
    const DB_TBL_USER_EMAIL_VER = 'tbl_user_email_verification';
    const DB_TBL_UEMV_PREFIX = 'uev_';
    const DB_TBL_USR_BANK_INFO = 'tbl_user_bank_details';
    const DB_TBL_USR_BANK_INFO_PREFIX = 'ub_';
    const DB_TBL_USR_WITHDRAWAL_REQ = 'tbl_user_withdrawal_requests';
    const DB_TBL_USR_WITHDRAWAL_REQ_PREFIX = 'withdrawal_';
    const DB_TBL_LANG = 'tbl_users_lang';
    const DB_TBL_LANG_PREFIX = 'userlang_';
    const DB_TBL_USER_TO_SPOKEN_LANGUAGES = 'tbl_user_to_spoken_languages';
    const DB_TBL_TEACHER_FAVORITE = 'tbl_user_favourite_teachers';
    const USER_TYPE_LEANER = 1;
    const USER_TYPE_TEACHER = 2;
    const USER_TYPE_LEARNER_TEACHER = 3;
    const USER_LEARNER_DASHBOARD = 1;
    const USER_TEACHER_DASHBOARD = 2;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const LESSION_EMAIL_BEFORE_12_HOUR = 12;
    const LESSION_EMAIL_BEFORE_24_HOUR = 24;
    const LESSION_EMAIL_BEFORE_48_HOUR = 48;
    const USER_NOTICATION_NUMBER_12 = 12;
    const USER_NOTICATION_NUMBER_24 = 24;
    const USER_NOTICATION_NUMBER_48 = 48;
    const WITHDRAWAL_METHOD_TYPE_BANK = 1;
    const WITHDRAWAL_METHOD_TYPE_PAYPAL = 2;

    public function __construct($userId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId);
        $this->objMainTableRecord->setSensitiveFields(['user_regdate']);
    }

    public function getUserSearchObj($attr = null, $joinUserCredentials = false, $skipDeleted = true)
    {
        $srch = static::getSearchObject($joinUserCredentials, $skipDeleted);
        $srch->joinTable(static::DB_TBL_CRED, 'LEFT OUTER JOIN', 'uc.' . static::DB_TBL_CRED_PREFIX . 'user_id = u.user_id', 'uc');
        if ($this->getMainTableRecordId() > 0) {
            $srch->addCondition('u.' . static::DB_TBL_PREFIX . 'id', '=', $this->getMainTableRecordId());
        }
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addField($attr);
            }
        } else {
            $srch->addMultipleFields([
                'u.user_id',
                'u.user_first_name',
                'u.user_last_name',
                'u.user_phone',
                'u.user_profile_info',
                'u.user_added_on',
                'u.user_country_id',
                'uc.credential_username',
                'uc.credential_email',
                'uc.credential_active',
                'uc.credential_verified'
            ]);
        }
        return $srch;
    }

    public function getUserInfo($attr = null, $isActive = true, $isVerified = true, $joinUserCredentials = false)
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $srch = $this->getUserSearchObj($attr);
        if ($isActive) {
            $srch->addCondition('uc.credential_active', '=', 1);
        }
        if ($isVerified) {
            $srch->addCondition('uc.credential_verified', '=', 1);
        }
        if ($joinUserCredentials) {
            $srch->joinTable(static::DB_TBL_CRED, 'LEFT OUTER JOIN', 'uc.credential_user_id = u.user_id', 'uc');
        }
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        if (!empty($record)) {
            return $record;
        }
        return false;
    }

    public static function getSearchObject($joinUserCredentials = false, $skipDeleted = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'u');
        if ($skipDeleted == true) {
            $srch->addCondition('user_deleted', '=', applicationConstants::NO);
        }
        if ($joinUserCredentials) {
            $srch->joinTable(static::DB_TBL_CRED, 'LEFT OUTER JOIN', 'uc.' . static::DB_TBL_CRED_PREFIX . 'user_id = u.user_id', 'uc');
        }
        return $srch;
    }

    public static function getWithdrawlMethodArray(): array
    {
        return [
            static::WITHDRAWAL_METHOD_TYPE_BANK => Label::getLabel('LBL_Bank_Payout'),
            static::WITHDRAWAL_METHOD_TYPE_PAYPAL => Label::getLabel('LBL_Paypal_Payout'),
        ];
    }

    public static function getWithdrawlMethodKey(): array
    {
        return [static::WITHDRAWAL_METHOD_TYPE_PAYPAL => 'PaypalPayout'];
    }

    public static function getUserTypesArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::USER_TYPE_LEANER => Label::getLabel('LBL_Learner', $langId),
            static::USER_TYPE_TEACHER => Label::getLabel('LBL_Teacher', $langId),
        ];
    }

    public static function getGenderArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::GENDER_MALE => Label::getLabel('LBL_Male', $langId),
            static::GENDER_FEMALE => Label::getLabel('LBL_Female', $langId),
        ];
    }

    public static function getUserDashboardArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::USER_LEARNER_DASHBOARD => Label::getLabel('LBL_Learner', $langId),
            static::USER_TEACHER_DASHBOARD => Label::getLabel('LBL_Teacher', $langId),
        ];
    }

    public static function setDashboardActiveTab($userPreferredDashboard)
    {
        $userPreferredDashboard = FatUtility::int($userPreferredDashboard);
        if ($userPreferredDashboard <= 0) {
            $userPreferredDashboard = User::USER_LEARNER_DASHBOARD;
        }
        $_SESSION[UserAuthentication::SESSION_ELEMENT_NAME]['activeTab'] = $userPreferredDashboard;
    }

    public static function getDashboardActiveTab()
    {
        if (self::isTeacher()) {
            $activeTab = User::USER_TEACHER_DASHBOARD;
        } else {
            $activeTab = User::USER_LEARNER_DASHBOARD;
        }
        if (isset($_SESSION[UserAuthentication::SESSION_ELEMENT_NAME]['activeTab']) && $_SESSION[UserAuthentication::SESSION_ELEMENT_NAME]['activeTab'] > 0) {
            $activeTab = $_SESSION[UserAuthentication::SESSION_ELEMENT_NAME]['activeTab'];
        }
        return $activeTab;
    }

    public static function isTeacher($returnNullIfNotLogged = false)
    {
        return (1 == UserAuthentication::getLoggedUserAttribute('user_is_teacher', $returnNullIfNotLogged));
    }

    public static function isLearner($returnNullIfNotLogged = false)
    {
        return (1 == UserAuthentication::getLoggedUserAttribute('user_is_learner', $returnNullIfNotLogged));
    }

    public static function canAccessTeacherDashboard()
    {
        return static::isTeacher();
    }

    public static function isLearnerProfileCompleted($userId = 0)
    {
        $userId = FatUtility::int($userId);
        if ($userId <= 0) {
            $userId = UserAuthentication::getLoggedUserId();
        }
        $srch = new UserSearch();
        $srch->joinUserSettings();
        $srch->addCondition('user_id', '=', $userId);
        $srch->setPageSize(1);
        $srch->addMultiplefields(['user_timezone']);
        $teacherRow = FatApp::getDb()->fetch($srch->getResultSet());
        return empty($teacherRow['user_timezone']) ? false : true;
    }

    public static function getTeacherProfileProgress(int $userId = 0): array
    {
        $userId = (1 > $userId) ? UserAuthentication::getLoggedUserId(true) : $userId;
        if (1 > $userId) {
            return [];
        }
        $teacherStat = new SearchBase(User::DB_TBL, 'user');
        $teacherStat->joinTable(TeacherStat::DB_TBL, 'LEFT JOIN', 'testat.testat_user_id = user.user_id', 'testat');
        $teacherStat->addCondition('user.user_id', '=', $userId);
        $teacherStat->doNotCalculateRecords();
        $teacherStat->setPageSize(1);
        $teacherStat->addMultiplefields([
            'if(user.user_country_id > 0 && user.user_timezone != "" && user.user_url_name != "",1,0) as generalProfile',
            'IFNULL(testat.testat_qualification,0) as uqualificationCount',
            'IFNULL(testat.testat_availability,0) as generalAvailabilityCount',
            'if(IFNULL(testat.testat_teachlang,0) = 1 && IFNULL(testat.testat_speaklang,0) = 1,1,0) as languagesCount',
            'IFNULL(testat.testat_preference,0) as preferenceCount',
            'if(IFNULL(testat.testat_minprice,0) > 0 && IFNULL(testat.testat_maxprice,0) > 0,1,0) as priceCount',
        ]);
        $teacherRow = FatApp::getDb()->fetch($teacherStat->getResultSet());
        if (empty($teacherRow)) {
            return [];
        }
        $teacherRowCount = count($teacherRow);
        $teacherFieldSum = array_sum($teacherRow);
        $teacherRow += [
            'totalFields' => $teacherRowCount,
            'totalFilledFields' => $teacherFieldSum,
            'percentage' => round((($teacherFieldSum * 100) / $teacherRowCount), 2),
            'isProfileCompleted' => ($teacherRowCount == $teacherFieldSum),
        ];
        return $teacherRow;
    }

    public static function isTeacherRequestApproved()
    {
        return true;
    }

    public static function canViewLearnerTab()
    {
        if (self::isLearner()) {
            return true;
        }
        return false;
    }

    public static function canViewTeacherTab()
    {
        if (self::isTeacher()) {
            return true;
        }
        $userRow = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_registered_initially_for']);
        if ($userRow['user_registered_initially_for'] == User::USER_TEACHER_DASHBOARD) {
            return true;
        }
        return false;
    }

    public static function isAdminLogged($ip = '')
    {
        if ($ip == '') {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (isset($_SESSION[static::ADMIN_SESSION_ELEMENT_NAME]) && $_SESSION[static::ADMIN_SESSION_ELEMENT_NAME]['admin_ip'] == $ip) {
            return true;
        }
        return false;
    }

    public function save()
    {
        $broken = false;
        if (!($this->getMainTableRecordId() > 0)) {
            $this->setFldValue('user_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    public function setLoginCredentials($username, $email, $password, $active = null, $verified = null)
    {
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $record = new TableRecord(static::DB_TBL_CRED);
        $arrFlds = [
            static::DB_TBL_CRED_PREFIX . 'username' => $username,
            static::DB_TBL_CRED_PREFIX . 'email' => $email,
            static::DB_TBL_CRED_PREFIX . 'password' => UserAuthentication::encryptPassword($password)
        ];
        if (null != $active) {
            $arrFlds[static::DB_TBL_CRED_PREFIX . 'active'] = $active;
        }
        if (null != $verified) {
            $arrFlds[static::DB_TBL_CRED_PREFIX . 'verified'] = $verified;
        }
        $record->setFldValue(static::DB_TBL_CRED_PREFIX . 'user_id', $this->getMainTableRecordId());
        $record->assignValues($arrFlds);
        if (!$record->addNew([], $arrFlds)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public function setUserInfo($data = [])
    {
        if (empty($data)) {
            return false;
        }
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $record = new TableRecord(static::DB_TBL);
        $record->setFldValue(static::DB_TBL_PREFIX . 'id', $this->getMainTableRecordId());
        $record->assignValues($data);
        if (!$record->addNew([], $data)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public function setLoginPassword($password)
    {
        if ($this->getMainTableRecordId() <= 0) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $record = new TableRecord(static::DB_TBL_CRED);
        $arrFlds = [static::DB_TBL_CRED_PREFIX . 'password' => UserAuthentication::encryptPassword($password)];
        $record->setFldValue(static::DB_TBL_CRED_PREFIX . 'user_id', $this->getMainTableRecordId());
        $record->assignValues($arrFlds);
        if (!$record->addNew([], $arrFlds)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public function changeEmail($email)
    {
        if (trim($email) == '') {
            return false;
        }
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $record = new TableRecord(static::DB_TBL_CRED);
        $arrFlds = [
            static::DB_TBL_CRED_PREFIX . 'email' => $email,
            static::DB_TBL_CRED_PREFIX . 'username' => $email,
        ];
        $record->setFldValue(static::DB_TBL_CRED_PREFIX . 'user_id', $this->getMainTableRecordId());
        $record->assignValues($arrFlds);
        if (!$record->addNew([], $arrFlds)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    public function verifyAccount($v = 1)
    {
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->updateFromArray(
                        static::DB_TBL_CRED,
                        [static::DB_TBL_CRED_PREFIX . 'verified' => $v],
                        [
                            'smt' => static::DB_TBL_CRED_PREFIX . 'user_id = ?', 'vals' => [$this->getMainTableRecordId()]
                        ]
                )) {
            $this->error = $db->getError();
            return false;
        }
        // You may want to send some email notification to user that his account is verified.
        return true;
    }

    public function activateAccount($v = 1)
    {
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->updateFromArray(
                        static::DB_TBL_CRED,
                        [static::DB_TBL_CRED_PREFIX . 'active' => $v],
                        [
                            'smt' => static::DB_TBL_CRED_PREFIX . 'user_id = ?', 'vals' => [$this->getMainTableRecordId()]
                        ]
                )) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function getProfileData()
    {
        if (!$this->getMainTableRecordId() > 0) {
            return false;
        }
        $srch = static::getSearchObject(true);
        $srch->addCondition('u.' . static::DB_TBL_PREFIX . 'id', '=', $this->getMainTableRecordId());
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        unset($record['credential_password']);
        $record['user_email'] = $record['credential_email'];
        return $record;
    }

    public function prepareUserVerificationCode($email = '')
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST.', $this->commonLangId);
            return false;
        }
        $verificationCode = $this->getMainTableRecordId() . '_' . FatUtility::getRandomString(15);
        $data = [
            static::DB_TBL_UEMV_PREFIX . 'user_id' => $this->getMainTableRecordId(),
            static::DB_TBL_UEMV_PREFIX . 'token' => $verificationCode,
            static::DB_TBL_UEMV_PREFIX . 'email' => trim($email),
        ];
        $tblRec = new TableRecord(static::DB_TBL_USER_EMAIL_VER);
        $tblRec->assignValues($data);
        if ($tblRec->addNew([], $data)) {
            return $verificationCode;
        } else {
            return false;
        }
    }

    public function verifyUserEmailVerificationCode($code)
    {
        $arrCode = explode('_', $code, 2);
        if (!is_numeric($arrCode[0])) {
            $this->error = Label::getLabel('ERR_INVALID_CODE', $this->commonLangId);
            return false;
        }
        $userId = FatUtility::int($arrCode[0]);
        $emvSrch = new SearchBase(static::DB_TBL_USER_EMAIL_VER);
        $emvSrch->addCondition(static::DB_TBL_UEMV_PREFIX . 'user_id', '=', $userId);
        $emvSrch->addCondition(static::DB_TBL_UEMV_PREFIX . 'token', '=', $code, 'AND');
        $emvSrch->addFld([static::DB_TBL_UEMV_PREFIX . 'user_id', static::DB_TBL_UEMV_PREFIX . 'email']);
        $rs = $emvSrch->getResultSet();
        if ($row = FatApp::getDb()->fetch($rs)) {
            $this->deleteEmailVerificationToken($userId);
            if (trim($row['uev_email']) == '') {
                return true;
            }
            return $row['uev_email'];
        } else {
            $this->error = Label::getLabel('ERR_INVALID_CODE.', $this->commonLangId);
            return false;
        }
        return false;
    }

    public function resetPassword($pwd)
    {
        if (!($this->getMainTableRecordId() > 0)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $db = FatApp::getDb();
        if (!$db->updateFromArray(
                        static::DB_TBL_CRED,
                        [static::DB_TBL_CRED_PREFIX . 'password' => $pwd],
                        [
                            'smt' => static::DB_TBL_CRED_PREFIX . 'user_id = ?', 'vals' => [$this->getMainTableRecordId()]
                        ]
                )) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function notifyAdminRegistration($data, $langId)
    {
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_email' => $data['user_email'],
        ];
        $email = new EmailHandler();
        if (!$email->sendNewRegistrationNotification($langId, $data)) {
            Message::addMessage(Label::getLabel("ERR_ERROR_IN_SENDING_NOTIFICATION_EMAIL_TO_ADMIN", $langId));
            return false;
        }
        return true;
    }

    public function userWelcomeEmailRegistration($userObj, $data, $langId)
    {
        $link = CommonHelper::generateFullUrl('GuestUser', 'loginForm');
        $data = [
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_full_name' => $data['user_first_name'] . ' ' . $data['user_last_name'],
            'user_email' => $data['user_email'],
            'link' => $link,
        ];
        $emailHandler = new EmailHandler();
        if (!$emailHandler->sendWelcomeEmail($langId, $data)) {
            Message::addMessage(Label::getLabel("ERR_ERROR_IN_SENDING_WELCOME_EMAIL", $langId));
            return false;
        }
        return true;
    }

    private function deleteEmailVerificationToken($userId)
    {
        FatApp::getDb()->deleteRecords(static::DB_TBL_USER_EMAIL_VER, ['smt' => static::DB_TBL_UEMV_PREFIX . 'user_id = ?', 'vals' => [$userId]]);
        return true;
    }

    public function getUserBankInfo()
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED');
            return false;
        }
        $srch = new SearchBase(static::DB_TBL_USR_BANK_INFO, 'tub');
        $srch->addMultipleFields(['ub_bank_name', 'ub_account_holder_name', 'ub_account_number', 'ub_ifsc_swift_code', 'ub_bank_address', 'ub_paypal_email_address']);
        $srch->addCondition(static::DB_TBL_USR_BANK_INFO_PREFIX . 'user_id', '=', $this->getMainTableRecordId());
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function getUserPaypalInfo()
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED');
            return false;
        }
        $srch = new SearchBase(static::DB_TBL_USR_BANK_INFO, 'tub');
        $srch->addMultipleFields(['ub_paypal_email_address']);
        $srch->addCondition(static::DB_TBL_USR_BANK_INFO_PREFIX . 'user_id', '=', $this->getMainTableRecordId());
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function updateBankInfo($data = [])
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $assignValues = [
            'ub_user_id' => $this->getMainTableRecordId(),
            'ub_bank_name' => $data['ub_bank_name'],
            'ub_account_holder_name' => $data['ub_account_holder_name'],
            'ub_account_number' => $data['ub_account_number'],
            'ub_ifsc_swift_code' => $data['ub_ifsc_swift_code'],
            'ub_bank_address' => $data['ub_bank_address'],
        ];
        if (!FatApp::getDb()->insertFromArray(static::DB_TBL_USR_BANK_INFO, $assignValues, false, [], $assignValues)) {
            $this->error = $this->db->getError();
            return false;
        }
        return true;
    }

    public function updatePaypalInfo($data = [])
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED');
            return false;
        }
        $assignValues = [
            'ub_user_id' => $this->getMainTableRecordId(),
            'ub_paypal_email_address' => $data['ub_paypal_email_address']
        ];
        if (!FatApp::getDb()->insertFromArray(static::DB_TBL_USR_BANK_INFO, $assignValues, false, [], $assignValues)) {
            $this->error = $this->db->getError();
            return false;
        }
        return true;
    }

    public static function getUserBalance($userId, $excludePendingWidrawReq = true)
    {
        $userId = FatUtility::int($userId);
        $srch = Transaction::getSearchObject();
        $srch->addGroupBy('utxn.utxn_user_id');
        $srch->addMultipleFields(["SUM(utxn_credit - utxn_debit) as userBalance"]);
        $srch->addCondition('utxn_user_id', '=', $userId);
        $srch->addCondition('utxn_status', '=', Transaction::STATUS_COMPLETED);
        $rs = $srch->getResultSet();
        if (!$row = FatApp::getDb()->fetch($rs)) {
            return 0;
        }
        $userBalance = $row["userBalance"];
        if ($excludePendingWidrawReq) {
            $srch = new SearchBase('tbl_user_withdrawal_requests', 'uwr');
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
            $srch->addGroupBy('uwr.withdrawal_user_id');
            $srch->addMultipleFields(["SUM(withdrawal_amount) as withdrawal_amount"]);
            $srch->addCondition('withdrawal_user_id', '=', $userId);
            $cnd = $srch->addCondition('withdrawal_status', '=', Transaction::WITHDRAWL_STATUS_PENDING);
            $cnd->attachCondition('withdrawal_status', '=', Transaction::WITHDRAWL_STATUS_PAYOUT_SENT);
            if ($res = FatApp::getDb()->fetch($srch->getResultSet())) {
                $userBalance = $userBalance - $res["withdrawal_amount"];
            }
        }
        return $userBalance;
    }

    public static function getPreferedDashbordRedirectUrl($preferredDashboard = "", $detectReferrerUrl = true)
    {
        $redirectUrl = "";
        if (isset($_SESSION['referer_page_url']) && true == $detectReferrerUrl) {
            $redirectUrl = $_SESSION['referer_page_url'];
            unset($_SESSION['referer_page_url']);
            return $redirectUrl;
        }
        if ("" == $preferredDashboard) {
            $userRow = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_preferred_dashboard']);
            $preferredDashboard = $userRow['user_preferred_dashboard'];
        }
        switch ($preferredDashboard) {
            case User::USER_LEARNER_DASHBOARD:
                if (true != User::isLearnerProfileCompleted()) {
                    $redirectUrl = CommonHelper::generateFullUrl('learner', '', [], CONF_WEBROOT_DASHBOARD);
                } else {
                    $redirectUrl = CommonHelper::generateFullUrl('teachers', '', [], CONF_WEBROOT_FRONTEND);
                }
                break;
            case User::USER_TEACHER_DASHBOARD:
                $redirectUrl = CommonHelper::generateFullUrl('teacher', '', [], CONF_WEBROOT_DASHBOARD);
                break;
        }
        if ("" == $redirectUrl) {
            $redirectUrl = CommonHelper::generateFullUrl('account', '', [], CONF_WEBROOT_DASHBOARD);
        }
        return $redirectUrl;
    }

    public static function isProfilePicUploaded($userId = 0)
    {
        $userId = FatUtility::int($userId);
        if (1 > $userId) {
            $userId = UserAuthentication::getLoggedUserId();
        }
        $fileRow = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PROFILE_IMAGE, $userId);
        if (!$fileRow) {
            return false;
        }
        return true;
    }

    public function getDashboardData($langId = 0, $isTeacherDashboard = false)
    {
        $langId = FatUtility::int($langId);
        $curDateTime = date('Y-m-d H:i:s');
        $srch = new UserSearch();
        $srch->joinCredentials();
        if (0 < $langId) {
            $srch->joinUserCountry($langId);
            $srch->addFld('IFNULL(country_name, country_code) as countryName');
        } else {
            $srch->joinUserCountry($langId);
            $srch->addFld('country_code as countryName');
        }
        if ($isTeacherDashboard) {
            $srch->joinTeacherLessonData($this->getMainTableRecordId(), false, false);
        } else {
            $srch->joinLearnerLessonData($this->getMainTableRecordId());
        }
        $srch->joinTable(Order::DB_TBL, 'LEFT JOIN', 'o.order_id = sld.sldetail_order_id and o.order_type = ' . Order::TYPE_LESSON_BOOKING . ' and order_is_paid IN (' . Order::ORDER_IS_PAID . ',' . Order::ORDER_IS_CANCELLED . ')', 'o');
        $srch->addMultipleFields([
            'user_id', 'user_url_name', 'user_first_name', 'user_last_name',
            'concat(user_first_name, " ", user_last_name) as user_full_name',
            'COUNT(sl.slesson_id) AS teacherTotLessons',
            'SUM(IF(sldetail_learner_status="' . ScheduledLesson::STATUS_SCHEDULED . '" AND concat(sl.slesson_date," ",sl.slesson_start_time) >="' . $curDateTime . '",1,0)) as learnerSchLessonsExcPast',
            'SUM(IF(slesson_status="' . ScheduledLesson::STATUS_SCHEDULED . '" AND concat(sl.slesson_date," ",sl.slesson_start_time) >="' . $curDateTime . '",1,0)) as teacherSchLessons',
            'SUM(IF(slesson_status="' . ScheduledLesson::STATUS_CANCELLED . '",1,0)) as cancelledLessons',
            'user_timezone',
            'user_country_id'
        ]);
        $srch->addCondition('user_id', '=', $this->getMainTableRecordId());
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function getUserLastWithdrawalRequest($userId)
    {
        $userId = FatUtility::int($userId);
        if (1 > $userId) {
            return false;
        }
        $srch = new SearchBase(static::DB_TBL_USR_WITHDRAWAL_REQ, 'tuwr');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('withdrawal_user_id', '=', $userId);
        $srch->addOrder('withdrawal_request_date', 'desc');
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public function addWithdrawalRequest($data, $langId)
    {
        $userId = FatUtility::int($data['ub_user_id']);
        unset($data['ub_user_id']);
        if ($userId < 1) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST.', $this->commonLangId);
            return false;
        }
        $assignFields = [
            'withdrawal_user_id' => $userId,
            'withdrawal_amount' => $data['withdrawal_amount'],
            'withdrawal_payment_method_id' => $data['withdrawal_payment_method_id'],
            'withdrawal_comments' => $data['withdrawal_comments'],
            'withdrawal_status' => 0,
            'withdrawal_request_date' => date('Y-m-d H:i:s')
        ];
        switch ($data['pmethod_code']) {
            case PaymentMethods::BANK_PAYOUT_KEY:
                $assignFields += [
                    'withdrawal_bank' => $data['ub_bank_name'],
                    'withdrawal_account_holder_name' => $data['ub_account_holder_name'],
                    'withdrawal_account_number' => $data['ub_account_number'],
                    'withdrawal_ifc_swift_code' => $data['ub_ifsc_swift_code'],
                    'withdrawal_bank_address' => $data['ub_bank_address'],
                ];
                break;
            case PaypalPayout::KEY_NAME:
                $assignFields += ['withdrawal_paypal_email_id' => $data['ub_paypal_email_address'],];
                break;
        }
        $broken = false;
        if (FatApp::getDb()->startTransaction() && FatApp::getDb()->insertFromArray(static::DB_TBL_USR_WITHDRAWAL_REQ, $assignFields)) {
            $withdrawRequestId = FatApp::getDb()->getInsertId();
            $formattedRequestValue = '#' . str_pad($withdrawRequestId, 6, '0', STR_PAD_LEFT);
            $txnArray["utxn_user_id"] = $userId;
            $txnArray["utxn_debit"] = $data["withdrawal_amount"];
            $txnArray["utxn_status"] = Transaction::STATUS_PENDING;
            $txnArray["utxn_comments"] = Label::getLabel('LBL_Funds_Withdrawn', $langId) . '. ' . Label::getLabel('LBL_Request_ID', $langId) . ' ' . $formattedRequestValue;
            $txnArray["utxn_withdrawal_id"] = $withdrawRequestId;
            $txnArray['utxn_type'] = Transaction::TYPE_MONEY_WITHDRAWN;
            $transObj = new Transaction($userId);
            if ($txnId = $transObj->addTransaction($txnArray)) {
                
            } else {
                $this->error = $transObj->getError();
                $broken = true;
            }
        }
        if ($broken === false && FatApp::getDb()->commitTransaction()) {
            return $withdrawRequestId;
        }
        FatApp::getDb()->rollbackTransaction();
        return false;
    }

    public function addUpdateUserFavoriteTeacher($teacher_id)
    {
        $user_id = FatUtility::int($this->getMainTableRecordId());
        $teacher_id = FatUtility::int($teacher_id);
        $data_to_save = ['uft_user_id' => $user_id, 'uft_teacher_id' => $teacher_id];
        $data_to_save_on_duplicate = ['uft_teacher_id' => $teacher_id];
        if (!FatApp::getDb()->insertFromArray(static::DB_TBL_TEACHER_FAVORITE, $data_to_save, false, [], $data_to_save_on_duplicate)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    public function getFavourites($filter, $langId = 0)
    {
        $srch = new UserFavoriteTeacherSearch();
        $page = (empty($filter['page']) || $filter['page'] <= 0) ? 1 : FatUtility::int($filter['page']);
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->addCondition('uft_user_id', '=', $this->getMainTableRecordId());
        $keyword = $filter['keyword'];
        if (!empty($keyword)) {
            $srch->addCondition('mysql_func_concat(`user_first_name`," ",`user_last_name`)', 'like', '%' . $keyword . '%', 'AND', true);
        }
        $srch->addGroupBy('uft_teacher_id');
        $srch->joinTeachers();
        $srch->joinTeacherSettings();
        $srch->joinUserTeachLanguages($langId);
        $srch->addMultipleFields([
            'uft_teacher_id',
            'user_url_name',
            'user_first_name',
            'user_last_name',
            'user_country_id',
        ]);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $rs = $srch->getResultSet();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount()
        ];
        $dataArr['Favourites'] = FatApp::getDb()->fetchAll($rs);
        $dataArr['pagingArr'] = $pagingArr;
        return $dataArr;
    }

    public static function canWithdraw($userId): bool
    {
        return (bool) self::getAttributesById($userId, 'user_is_teacher');
    }

    public function truncateUserData($langId = 1)
    {
        $db = FatApp::getDb();
        $tblUsersData = [
            'user_url_name' => '',
            'user_first_name' => Label::getLabel('LBL_Deleted', $langId),
            'user_last_name' => Label::getLabel('LBL_User', $langId),
            'user_phone' => '',
            'user_gender' => NULL,
            'user_dob' => NULL,
            'user_profile_info' => '',
            'user_address1' => '',
            'user_address2' => '',
            'user_facebook_id' => '',
            'user_googleplus_id' => '',
            'user_fb_access_token' => '',
            'user_zip' => '',
            'user_country_id' => NULL,
            'user_city' => '',
        ];
        return $db->updateFromArray(static::DB_TBL, $tblUsersData, ['smt' => 'user_id=?', 'vals' => [$this->mainTableRecordId]]);
    }

    public function truncateUserCredentials()
    {
        $db = FatApp::getDb();
        $tbl_user_credentials_data = ['credential_username' => NULL, 'credential_email' => NULL];
        return $db->updateFromArray(static::DB_TBL_CRED, $tbl_user_credentials_data, ['smt' => 'credential_user_id=?', 'vals' => [$this->mainTableRecordId]]);
    }

    public function truncateUsersLangData()
    {
        return FatApp::getDb()->deleteRecords(static::DB_TBL_LANG, array('smt' => 'userlang_user_id = ?', 'vals' => [$this->mainTableRecordId]));
    }

    public function deleteUserBankInfoData()
    {
        return FatApp::getDb()->deleteRecords(static::DB_TBL_USR_BANK_INFO, array('smt' => 'ub_user_id = ?', 'vals' => [$this->mainTableRecordId]));
    }

    public function deleteUserEmailVerificationData()
    {
        return FatApp::getDb()->deleteRecords(static::DB_TBL_USER_EMAIL_VER, array('smt' => 'uev_user_id = ?', 'vals' => [$this->mainTableRecordId]));
    }

    public function truncateUserWithdrawalRequestsData()
    {
        $db = FatApp::getDb();
        $tbl_user_withdrawal_requests_data = [
            'withdrawal_bank' => '',
            'withdrawal_account_holder_name' => '',
            'withdrawal_account_number' => '',
            'withdrawal_ifc_swift_code' => '',
            'withdrawal_bank_address' => '',
            'withdrawal_paypal_email_id' => ''
        ];
        return $db->updateFromArray(static::DB_TBL_USR_WITHDRAWAL_REQ, $tbl_user_withdrawal_requests_data, ['smt' => 'withdrawal_user_id=?', 'vals' => [$this->mainTableRecordId]]);
    }

    public function deleteUserSetting()
    {
        return FatApp::getDb()->deleteRecords(UserSetting::DB_TBL, array('smt' => 'us_user_id  = ?', 'vals' => [$this->mainTableRecordId]));
    }

    public function deleteUserQualifications()
    {
        return FatApp::getDb()->deleteRecords(UserQualification::DB_TBL, ['smt' => 'uqualification_user_id = ?', 'vals' => [$this->mainTableRecordId]]);
    }

    public function deleteUserEmailChangeRequests()
    {
        return FatApp::getDb()->deleteRecords(UserEmailChangeRequest::DB_TBL, ['smt' => 'uecreq_user_id = ?', 'vals' => [$this->mainTableRecordId]]);
    }

}
