<?php

class Banner extends MyAppModel
{

    const DB_TBL = 'tbl_banners';
    const DB_TBL_PREFIX = 'banner_';
    const DB_LANG_TBL = 'tbl_banners_lang';
    const DB_LANG_TBL_PREFIX = 'bannerlang_';
    const DB_TBL_LOCATIONS = 'tbl_banner_locations';
    const DB_LANG_TBL_LOCATIONS = 'tbl_banner_locations_lang';
    const DB_TBL_LOCATIONS_PREFIX = 'blocation_';
    const TYPE_BANNER = 1;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getBannerTypesArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }
        return [];
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'b');
        if ($langId > 0) {
            $srch->joinTable(static::DB_LANG_TBL, 'LEFT OUTER JOIN', 'bannerlang_banner_id = banner_id AND bannerlang_lang_id = ' . $langId);
        }
        if ($isActive) {
            $srch->addCondition('banner_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

    public static function getBannerLocationSrchObj($isActive = true, $langId = 0)
    {
        $srch = new SearchBase(static::DB_TBL_LOCATIONS);
        if ($langId > 0) {
            $srch->joinTable(static::DB_LANG_TBL_LOCATIONS, 'LEFT OUTER JOIN', 'blocationlang_blocation_id = blocation_id AND blocationlang_lang_id = ' . $langId);
        }
        if ($isActive) {
            $srch->addCondition('blocation_active', '=', applicationConstants::ACTIVE);
        }
        $srch->addOrder('blocation_key', 'ASC');
        return $srch;
    }

    public function updateLocationData($data = [])
    {
        $db = FatApp::getDb();
        $blocationId = $data['blocation_id'];
        unset($data['blocation_id']);
        $assignValues = [
            'blocation_active' => $data['blocation_active'],
            'blocation_identifier' => $data['blocation_identifier'],
        ];
        $where = ['smt' => 'blocation_id = ?', 'vals' => [$blocationId]];
        if (!$db->updateFromArray(static::DB_TBL_LOCATIONS, $assignValues, $where)) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

}
