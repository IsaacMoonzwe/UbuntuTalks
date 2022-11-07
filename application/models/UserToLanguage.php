<?php

class UserToLanguage extends MyAppModel
{

    const DB_TBL = 'tbl_user_to_spoken_languages';
    const DB_TBL_PREFIX = 'utsl_';
    const DB_TBL_TEACH = 'tbl_user_teach_languages';
    const DB_TBL_TEACH_PREFIX = 'utl_';

    public function __construct(int $userId = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'user_id', $userId);
    }

    public function getUserTeachlanguages(int $langId = 0): object
    {
        $searchBase = new SearchBase(static::DB_TBL_TEACH, 'utl');
        $searchBase->addCondition('utl_user_id', '=', $this->mainTableRecordId);
        $searchBase->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'tlanguage_id = utl_tlanguage_id', 'tl');
        if ($langId > 0) {
            $searchBase->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanguage_id = tlanguagelang_tlanguage_id and tlanguagelang_lang_id =' . $langId, 'tll');
        }
        return $searchBase;
    }

    public static function getTeachingAssoc($teacherId, $langId = 0, $activeOnly = true)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $TeachingLangSrch = new SearchBase(self::DB_TBL_TEACH, 'tt');
        $TeachingLangSrch->joinTable(TeachingLanguage::DB_TBL, 'LEFT OUTER JOIN', 'tl.tlanguage_id = tt.utl_tlanguage_id', 'tl');
        $TeachingLangSrch->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT OUTER JOIN', 'tl_l.tlanguagelang_tlanguage_id = tl.tlanguage_id AND tl_l.tlanguagelang_lang_id = ' . $langId, 'tl_l');
        $TeachingLangSrch->addCondition('utl_user_id', '=', $teacherId);
        $activeOnly && $TeachingLangSrch->addCondition('tlanguage_active', '=', applicationConstants::YES);

        $TeachingLangSrch->doNotCalculateRecords();
        $TeachingLangSrch->addMultiplefields(['tlanguage_id', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name']);
        $rs = $TeachingLangSrch->getResultSet();
        $teachingLanguagesArr = FatApp::getDb()->fetchAllAssoc($rs);
        return $teachingLanguagesArr;
    }

    public static function getAttributesByUserAndLangId($recordId, $langId, $attr = null)
    {
        $recordId = FatUtility::convertToType($recordId, FatUtility::VAR_INT);
        $db = FatApp::getDb();
        $srch = new SearchBase(static::DB_TBL_TEACH);
        $srch->addCondition(static::DB_TBL_TEACH_PREFIX . 'us_user_id', '=', $recordId);
        $srch->addCondition(static::DB_TBL_TEACH_PREFIX . 'slanguage_id', '=', $langId);
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $rs = $srch->getResultSet();
        $row = $db->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public function getAttributesByLangAndSlot($langId, $slot, $attr = null)
    {
        $srch = new SearchBase(static::DB_TBL_TEACH);
        $srch->addCondition(static::DB_TBL_TEACH_PREFIX . 'us_user_id', '=', $this->mainTableRecordId);
        $srch->addCondition(static::DB_TBL_TEACH_PREFIX . 'slanguage_id', '=', $langId);
        $srch->addCondition(static::DB_TBL_TEACH_PREFIX . 'booking_slot', '=', $slot);
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }
        $srch->doNotCalculateRecords();
        $srch->setPagesize(1);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!is_array($row)) {
            return false;
        }
        if (is_string($attr)) {
            return $row[$attr];
        }
        return $row;
    }

    public function getTeacherPricesForLearner($langId, $learnerId, int $slotDuration = 0): array
    {
        $srch = new SearchBase(static::DB_TBL_TEACH, 'utl');
        $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'ustelgpr.ustelgpr_utl_id = utl.utl_id', 'ustelgpr');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'tlanguage_id = utl.utl_tlanguage_id');
        $srch->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'tlanguagelang_tlanguage_id = utl.utl_tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl_lang');
        $srch->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'ustelgpr_slot = top_lesson_duration AND top_teacher_id = utl_user_id AND top_learner_id = ' . $learnerId, 'top');
        $srch->addMultipleFields([
            'tlanguage_id',
            'IFNULL(tlanguage_name,tlanguage_identifier) as tlanguage_name',
            'IFNULL(top.top_percentage, 0) as top_percentage',
            'ustelgpr_slot',
            'ustelgpr_slot',
            'ustelgpr_min_slab',
            'ustelgpr_max_slab',
        ]);
        $srch->addCondition('utl_user_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('ustelgpr_price', '>', 0);
        $srch->addCondition('utl_tlanguage_id', '>', 0);
        $srch->addCondition('tlanguage_active', '=', applicationConstants::YES);
        if (!empty($slotDuration)) {
            $slotDuration = FatUtility::convertToType($slotDuration, FatUtility::VAR_INT);
            $srch->addCondition('ustelgpr.ustelgpr_slot', '=', $slotDuration);
            $srch->addCondition('ustelgpr_slot', 'IN', CommonHelper::getPaidLessonDurations());
        }

        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    public function removeSpeakLang(array $langIds = []): bool
    {
        $db = FatApp::getDb();
        $query = 'DELETE  FROM ' . self::DB_TBL . ' WHERE  1 = 1';

        if (!empty($this->mainTableRecordId)) {
            $query .= ' and utsl_user_id = ' . $this->mainTableRecordId;
        }

        if (!empty($langIds)) {
            $langIds = implode(",", $langIds);
            $query .= ' and utsl_slanguage_id IN (' . $langIds . ')';
        }
        $db->query($query);
        if ($db->getError()) {
            $this->error = $db->getError();
            return false;
        }

        return true;
    }

}
