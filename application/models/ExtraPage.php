<?php

class ExtraPage extends MyAppModel
{

    const DB_TBL = 'tbl_extra_pages';
    const DB_TBL_PREFIX = 'epage_';
    const DB_TBL_LANG = 'tbl_extra_pages_lang';
    const DB_TBL_LANG_PREFIX = 'epagelang_';
    const BLOCK_PROFILE_INFO_BAR = 1;
    const BLOCK_WHY_US = 2;
    const BLOCK_BROWSE_TUTOR = 3;
    const BLOCK_CONTACT_BANNER_SECTION = 4;
    const BLOCK_TEACHER_BANNER_SECTION = 14;
    const BLOCK_GROUP_BANNER_SECTION = 13;
    const BLOCK_PROCESS_PAGE_SECTION = 12;
    const BLOCK_QUOTE_BANNER = 10;
    const BLOCK_KIDS_BANNER = 11;
    const BLOCK_CONTACT_LEFT_SECTION = 5;
    const BLOCK_APPLY_TO_TEACH_BENEFITS_SECTION = 6;
    const BLOCK_APPLY_TO_TEACH_FEATURES_SECTION = 7;
    const BLOCK_APPLY_TO_TEACH_BECOME_A_TUTOR_SECTION = 8;
    const BLOCK_APPLY_TO_TEACH_STATIC_BANNER = 9;
    const BLOCK_REFERRAL_CAMPAIGN_BANNER = 15;
    const BLOCK_REFERRAL_CAMPAIGN_LEFT_SECTION_BANNER = 16;
    const BLOCK_REFERRAL_CAMPAIGN_BENEFITS_SECTION_BANNER = 17;
    const BLOCK_B2B = 18;
    const BLOCK_EDUCATION_CAMPAIGN_LEFT_SECTION_BANNER = 19;
    const BLOCK_EDUCATION_CAMPAIGN_BENEFITS_SECTION_BANNER = 20;
    const BLOCK_BUSINESS_CAMPAIGN_LEFT_SECTION_BANNER = 21;
    const BLOCK_BUSINESS_CAMPAIGN_BENEFITS_SECTION_BANNER = 22;
    const BLOCK_FAITH_GROUPS_CAMPAIGN_LEFT_SECTION_BANNER = 23;
    const BLOCK_FAITH_GROUPS_CAMPAIGN_BENEFITS_SECTION_BANNER = 24;
    const BLOCK_MEDICAL_CAMPAIGN_LEFT_SECTION_BANNER = 25;
    const BLOCK_MEDICAL_CAMPAIGN_BENEFITS_SECTION_BANNER = 26;
    const BLOCK_DUBBING = 27;
    const BLOCK_VIRTUAL_TRANSLATION = 28;

    private $pageType;

    public function __construct($epageId = 0, $pageType = '')
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $epageId);
        $this->pageType = $pageType;
    }

    public static function getSearchObject($langId = 0, $isActive = true)
    {
        $srch = new SearchBase(static::DB_TBL, 'ep');
        if ($langId > 0) {
            $srch->joinTable(
                    static::DB_TBL_LANG,
                    'LEFT OUTER JOIN',
                    'ep_l.' . static::DB_TBL_LANG_PREFIX . 'epage_id = ep.' . static::tblFld('id') . ' and
			     ep_l.' . static::DB_TBL_LANG_PREFIX . 'lang_id = ' . $langId,
                    'ep_l'
            );
        }
        if ($isActive) {
            $srch->addCondition('epage_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

    public static function getContentBlockArr($langId)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG');
        }

        return array(
            static::BLOCK_WHY_US => Labels::getLabel('LBL_WHY_US_Block', $langId),
            static::BLOCK_HOW_IT_WORK => Labels::getLabel('LBL_HOW_IT_WORK_Block', $langId),
        );
    }

    public function updatePageContent($data = array())
    {
        if (!($this->mainTableRecordId > 0)) {
            $this->error = Labels::getLabel('MSG_Invalid_Request', $this->commonLangId);
            return false;
        }
        $epage_id = FatUtility::int($data['epage_id']);
        unset($data['btn_submit']);
        unset($data['epage_id']);
        $assignValues = $data;
        if (!FatApp::getDb()->updateFromArray(
                        static::DB_TBL,
                        $assignValues,
                        array('smt' => static::DB_TBL_PREFIX . 'id = ? ', 'vals' => array((int) $epage_id))
                )) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    public static function getBlockContent(int $pageType, int $langId = 0): string
    {
        $langId = (0 > $langId) ? $langId : CommonHelper::getLangId();
        $srch = self::getSearchObject($langId);
        $srch->addCondition('ep.epage_type', '=', $pageType);
        $srch->addMultipleFields(array('epage_id', 'IFNULL(epage_label, epage_identifier) as epage_label', 'epage_type', 'IFNULL(epage_content,"") as epage_content', 'epage_default_content'));
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $resultData = FatApp::getDb()->fetch($rs);
        if (empty($resultData['epage_content'])) {
            return "";
        }
        return $resultData['epage_content'];
    }

}
