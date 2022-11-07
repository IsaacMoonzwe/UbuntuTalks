<?php

class UserQualification extends MyAppModel
{

    const DB_TBL = 'tbl_user_qualifications';
    const DB_TBL_PREFIX = 'uqualification_';
    const EXPERIENCE_EDUCATION = 1;
    const EXPERIENCE_CERTIFICATION = 2;
    const EXPERIENCE_WORK = 3;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getExperienceTypeArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::EXPERIENCE_EDUCATION => Label::getLabel('LBL_Education', $langId),
            static::EXPERIENCE_CERTIFICATION => Label::getLabel('LBL_Certification', $langId),
            static::EXPERIENCE_WORK => Label::getLabel('LBL_Work_Experience', $langId),
        ];
    }

    public static function deleteUserQualificationsDataByUserId($userId)
    {
        FatApp::getDb()->deleteRecords(static::DB_TBL, array('smt' => 'uqualification_user_id = ?', 'vals' => array($userId)));
        return true;
    }

}
