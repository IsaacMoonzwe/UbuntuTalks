<?php

class TeacherGroupClasses extends MyAppModel
{

    const DB_TBL = 'tbl_group_classes';
    const DB_TBL_PREFIX = 'grpcls_';
    const DB_TBL_LANG = 'tbl_group_classes_lang';
    const DB_TBL_LANG_PREFIX = 'gclang_';
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELLED = 3;
    const FILTER_UPCOMING = 'upcoming';
    const FILTER_ONGOING = 'ongoing';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getCustomFilterAr()
    {
        return [
            static::FILTER_UPCOMING => Label::getLabel('LBL_Upcoming'),
            static::FILTER_ONGOING => Label::getLabel('LBL_OnGoing')
        ];
    }

    public function deleteClass()
    {
        $this->setFldValue('grpcls_deleted', ApplicationConstants::YES);
        $this->setFldValue('grpcls_status', self::STATUS_CANCELLED);
        return parent::save();
    }

    public function cancelClass()
    {
        $this->setFldValue('grpcls_status', self::STATUS_CANCELLED);
        return parent::save();
    }

    public static function getStatusArr($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($langId < 1) {
            $langId = CommonHelper::getLangId();
        }
        return [
            static::STATUS_PENDING => Label::getLabel('LBL_Pending', $langId),
            static::STATUS_ACTIVE => Label::getLabel('LBL_Active', $langId),
            static::STATUS_COMPLETED => Label::getLabel('LBL_Completed', $langId),
            static::STATUS_CANCELLED => Label::getLabel('LBL_Cancelled', $langId)
        ];
    }

}
