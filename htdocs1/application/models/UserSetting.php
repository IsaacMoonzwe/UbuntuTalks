<?php

class UserSetting extends MyAppModel
{

    const DB_TBL = 'tbl_user_settings';
    const DB_TBL_PREFIX = 'us_';

    public function __construct(int $userId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'user_id', $userId);
    }

    public function saveData($data = [])
    {
        if (($this->getMainTableRecordId() < 1)) {
            $this->error = Label::getLabel('ERR_INVALID_REQUEST_USER_NOT_INITIALIZED', $this->commonLangId);
            return false;
        }
        $db = FatApp::getDb();
        $data['us_user_id'] = $this->getMainTableRecordId();
        if (!$db->insertFromArray(static::DB_TBL, $data, false, [], $data)) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function getUserSiteLang(): array
    {
        $userSetting = new UserSettingSearch();
        $userSetting->doNotCalculateRecords();
        $userSetting->setPagesize(1);
        $userSetting->getUserSiteLang($this->mainTableRecordId);
        $resultSet = $userSetting->getResultSet();
        $data = FatApp::getDb()->fetch($resultSet);
        if (empty($data)) {
            return [];
        }
        return $data;
    }

    public static function getUserSettings(int $userId)
    {
        $userId = FatUtility::int($userId);
        if ($userId < 1) {
            trigger_error("User Id is not passed", E_USER_ERROR);
        }
        $srch = new SearchBase(UserSetting::DB_TBL, 'us');
        $srch->addMultipleFields(['us_is_trial_lesson_enabled',
            'us_notice_number',
            'us_video_link',
            'us_booking_before', //== code added on 23-08-2019
            'us_teach_slanguage_id',
            'us_google_access_token',
            'us_google_access_token_expiry',
            'us_site_lang'
        ]);

        $srch->addCondition('us_user_id', '=', $userId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    public static function truncateUserSettingsDataByUserId($userId)
    {
        $db = FatApp::getDb();
        $tbl_user_settings_data = [
            'us_video_link' => '',
            'us_google_access_token' => '',
        ];
        if ($db->updateFromArray(static::DB_TBL, $tbl_user_settings_data, array(
                    'smt' => 'us_user_id=?',
                    'vals' => array(
                        $userId
                    )
                ))) {
            return true;
        }
    }

}
