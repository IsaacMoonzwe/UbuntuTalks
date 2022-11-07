<?php

class Configurations extends FatModel
{

    const DB_TBL = 'tbl_configurations';
    const DB_TBL_PREFIX = 'conf_';
    const FORM_GENERAL = 1;
    const FORM_LOCAL = 2;
    const FORM_SEO = 3;
    const FORM_OPTIONS = 4;
    const FORM_LIVE_CHAT = 5;
    const FORM_THIRD_PARTY_API = 6;
    const FORM_EMAIL = 7;
    const FORM_MEDIA = 8;
    const FORM_SERVER = 9;
    const FORM_REVIEWS = 10;
    const FORM_SECURITY_SETTINGS = 11;
    const FORM_VIDEO_SLIDE = 12;
    
    const MODERATE = 0;
    const High = 1;
    
    public function __construct()
    {
        parent::__construct();
    }

    public static function getLangTypeFormArr()
    {
        return [
            Configurations::FORM_GENERAL,
            Configurations::FORM_EMAIL,
            Configurations::FORM_MEDIA,
        ];
    }

    public static function getSecuritySettings()
    {
        $adminLangId = CommonHelper::getLangId();
        return [
            Configurations::MODERATE => Label::getLabel('LBL_MODERATE', $adminLangId),
            Configurations::High => Label::getLabel('LBL_High', $adminLangId)
        ];
    }

    public static function getTabsArr()
    {
        $adminLangId = CommonHelper::getLangId();
        $configurationArr = [
            Configurations::FORM_GENERAL => Label::getLabel('MSG_General', $adminLangId),
            Configurations::FORM_LOCAL => Label::getLabel('MSG_Local', $adminLangId),
            Configurations::FORM_SEO => Label::getLabel('MSG_Seo', $adminLangId),
            Configurations::FORM_OPTIONS => Label::getLabel('MSG_Options', $adminLangId),
            Configurations::FORM_LIVE_CHAT => Label::getLabel('MSG_Live_Chat', $adminLangId),
            Configurations::FORM_THIRD_PARTY_API => Label::getLabel('MSG_Third_Party_API', $adminLangId),
            Configurations::FORM_EMAIL => Label::getLabel('MSG_Email', $adminLangId),
            Configurations::FORM_MEDIA => Label::getLabel('MSG_Media', $adminLangId),
            Configurations::FORM_REVIEWS => Label::getLabel('MSG_Reviews', $adminLangId),
            Configurations::FORM_SERVER => Label::getLabel('MSG_Server', $adminLangId),
            Configurations::FORM_SECURITY_SETTINGS => Label::getLabel('MSG_SECURITY_SETTINGS', $adminLangId),
            Configurations::FORM_VIDEO_SLIDE => Label::getLabel('MSG_HOME_VIDEO_SLIDE', $adminLangId)];
        return $configurationArr;
    }

    public static function dateFormatPhpArr()
    {
        return ['Y-m-d' => 'Y-m-d', 'd/m/Y' => 'd/m/Y', 'm-d-Y' => 'm-d-Y', 'M d, Y' => 'M d, Y'];
    }

    public static function dateFormatMysqlArr()
    {
        return ['%Y-%m-%d', '%d/%m/%Y', '%m-%d-%Y', '%b %d, %Y'];
    }

    public static function dateTimeZoneArr()
    {
        $arr = DateTimeZone::listIdentifiers();
        $arr = array_combine($arr, $arr);
        return $arr;
    }

    public static function getConfigurations(array $configs = []): array
    {
        $srch = new SearchBase(static::DB_TBL, 'conf');
        $configs && $srch->addCondition('conf_name', 'IN', $configs);
        $srch->addMultipleFields(['UPPER(conf_name) conf_name', 'conf_val']);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($rs);
    }

    public function update($data)
    {
        foreach ($data as $key => $val) {
            $assignValues = ['conf_name' => $key, 'conf_val' => $val];
            FatApp::getDb()->insertFromArray(static::DB_TBL, $assignValues, false, [], $assignValues);
        }
        return true;
    }

}
