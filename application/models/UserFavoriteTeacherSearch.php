<?php

class UserFavoriteTeacherSearch extends SearchBase
{

    private $langId;
    private $isTeacherSettingsJoined;

    public function __construct($langId = 0, $alias = 'uft')
    {
        parent::__construct(User::DB_TBL_TEACHER_FAVORITE, 'uft');
        $this->langId = FatUtility::int($langId);
        $this->productsJoined = false;
        $this->commonLangId = CommonHelper::getLangId();
    }

    public function joinTeachers()
    {
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'uft_teacher_id = ut.user_id', 'ut');
        $this->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'credential_user_id = ut.user_id', 'utc');
        $this->addCondition('ut.user_is_teacher', '=', applicationConstants::YES);
        $this->addCondition('credential_active', '=', applicationConstants::ACTIVE);
        $this->addCondition('credential_verified', '=', applicationConstants::YES);
    }

    public function joinTeacherSettings()
    {
        if (true === $this->isTeacherSettingsJoined) {
            return;
        }
        $this->joinTable(UserSetting::DB_TBL, 'INNER JOIN', 'ts.us_user_id = ut.user_id', 'ts');
        $this->isTeacherSettingsJoined = true;
    }

    public function joinLearnerOfferPrice($learnerId)
    {
        $learnerId = FatUtility::int($learnerId);
        if ($learnerId < 1) {
            trigger_error("Invalid Request", E_USER_ERROR);
        }
        $this->joinTable(TeacherOfferPrice::DB_TBL, 'LEFT JOIN', 'ut.user_id = top_teacher_id AND top_learner_id = ' . $learnerId, 'top');
    }

    public function joinTeacherTeachLanguage($langId = 0)
    {
        if (false === $this->isTeacherSettingsJoined) {
            trigger_error("First use 'joinTeacherSettings' before joining 'joinTeacherTeachLanguage'", E_USER_ERROR);
        }
        $langId = FatUtility::int($langId);
        $this->joinTable(SpokenLanguage::DB_TBL, 'INNER JOIN', 't_sl.slanguage_id = ts.us_teach_slanguage_id', 't_sl');
        if ($langId > 0) {
            $this->joinTable(SpokenLanguage::DB_TBL_LANG, 'LEFT JOIN', 't_sl.slanguage_id = t_sl_l.slanguagelang_slanguage_id AND slanguagelang_lang_id = ' . $langId, 't_sl_l');
        }
    }

    public function joinUserTeachLanguages($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        $this->joinTable(UserTeachLanguage::DB_TBL, 'LEFT  JOIN', 'ut.user_id = utsl.utl_user_id', 'utsl');
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT JOIN', 'tlanguage_id = utsl.utl_tlanguage_id');
        $this->joinTable(TeachingLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'tlanguagelang_tlanguage_id = utsl.utl_tlanguage_id AND tlanguagelang_lang_id = ' . $langId, 'sl_lang');
        $this->addMultipleFields(['utsl.utl_user_id', 'GROUP_CONCAT( DISTINCT IFNULL(tlanguage_name, tlanguage_identifier) ) as teacherTeachLanguageName']);
    }

}
