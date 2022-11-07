<?php

class Admin extends MyAppModel
{

    public static $admin_dashboard_layouts = [0 => 'default', 1 => 'switch_layout'];

    const DB_TBL = 'tbl_admin';
    const DB_TBL_PREFIX = 'admin_';

    public function __construct($userId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId);
        $this->objMainTableRecord->setSensitiveFields([]);
    }

    public static function getAdminTimeZone()
    {
        return FatApp::getConfig('CONF_ADMIN_TIMEZONE', FatUtility::VAR_STRING, date_default_timezone_get());
    }

}
