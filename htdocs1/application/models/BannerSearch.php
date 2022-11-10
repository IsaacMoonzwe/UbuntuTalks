<?php

class BannerSearch extends SearchBase
{

    private $langId;

    public function __construct($langId = 0, $isActive = true)
    {
        $this->langId = FatUtility::int($langId);
        parent::__construct(Banner::DB_TBL, 'b');
        if ($langId > 0) {
            $this->joinTable(Banner::DB_LANG_TBL, 'LEFT OUTER JOIN', 'b_l.bannerlang_banner_id = b.banner_id AND b_l.bannerlang_lang_id = ' . $langId, 'b_l');
        }
        if ($isActive) {
            $this->addCondition('b.banner_active', '=', applicationConstants::ACTIVE);
        }
    }

    public function joinLocations($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if ($this->langId) {
            $langId = $this->langId;
        }
        $this->joinTable(Banner::DB_TBL_LOCATIONS, 'LEFT OUTER JOIN', 'bl.blocation_id = b.banner_blocation_id', 'bl');
        if ($langId > 0) {
            $this->joinTable(Banner::DB_LANG_TBL_LOCATIONS, 'LEFT OUTER JOIN', 'bl_l.blocationlang_blocation_id = bl.blocation_id AND bl_l.blocationlang_lang_id = ' . $langId, 'bl_l');
        }
    }

    public function joinAttachedFile()
    {
        $this->joinTable(AttachedFile::DB_TBL, 'INNER JOIN', 'af.afile_record_id = b.banner_id and afile_type =' . AttachedFile::FILETYPE_BANNER, 'af');
        $this->addGroupBy('banner_id');
    }

}
