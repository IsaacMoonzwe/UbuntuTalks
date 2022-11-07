<?php

class EventRegistration extends MyAppModel
{

    const DB_TBL = 'tbl_event_registration';
    const DB_TBL_PREFIX = 'epage_';
    const DB_TBL_LANG = 'tbl_event_registration_lang';
    const DB_TBL_LANG_PREFIX = 'epagelang_';
    const BLOCK_UT_REGISTRATION = 26;

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
            $srch->addCondition('event_registration_active', '=', applicationConstants::ACTIVE);
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
        $srch->addCondition('ep.event_registration_type', '=', $pageType);
        $srch->addMultipleFields(array('epage_id', 'IFNULL(event_registration_label, event_registration_identifier) as event_registration_label', 'event_registration_type', 'IFNULL(event_registration_content,"") as event_registration_content', 'event_registration_default_content'));
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $resultData = FatApp::getDb()->fetch($rs);
        if (empty($resultData['event_registration_content'])) {
            return "";
        }
        return $resultData['event_registration_content'];
    }

}
