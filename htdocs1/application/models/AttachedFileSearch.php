<?php

class AttachedFileSearch extends SearchBase
{

    private $langId;

    public function __construct($langId = 0)
    {
        $this->langId = FatUtility::int($langId);
        parent::__construct(AttachedFile::DB_TBL, 'af');
    }

    public function joinBanners($autoAddFields = false)
    {
        $this->joinTable(Banner::DB_TBL, 'LEFT OUTER JOIN', 'banner_id = afile_record_id', 'banner');
        $this->joinTable(Banner::DB_LANG_TBL, 'LEFT OUTER JOIN', Banner::DB_LANG_TBL_PREFIX . 'banner_id = banner.banner_id and bannerlang_lang_id=' . $this->langId, 'banner_l');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_BANNER);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'banner_id as record_id', 'afile_lang_id', 'banner_title as record_name', 'afile_type']);
        }
    }

    public function joinHomePageBanner($autoAddFields = false)
    {
        $this->joinTable(Slides::DB_TBL, 'LEFT OUTER JOIN', 'slide_id = afile_record_id', 'slide');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_HOME_PAGE_BANNER);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'slide_id as record_id', 'afile_lang_id', 'slide_identifier as record_name', 'afile_type']);
        }
    }

    public function joinContentBackgroudImage($autoAddFields = false)
    {
        $this->joinTable(ContentPage::DB_TBL, 'LEFT OUTER JOIN', 'cpage_id = afile_record_id', 'cp');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE);
        $this->addCondition('cpage_deleted', '=', applicationConstants::NO);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'cpage_id as record_id', 'afile_lang_id', 'cpage_identifier as record_name', 'afile_type']);
        }
    }

    public function joinTeachingLanguage($autoAddFields = false)
    {
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT OUTER JOIN', 'tlanguage_id = afile_record_id', 'tl');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_TEACHING_LANGUAGES);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'tlanguage_id as record_id', 'afile_lang_id', 'tlanguage_identifier as record_name', 'afile_type']);
        }
    }

    public function joinFlagTeachingLangugage($autoAddFields = false)
    {
        $this->joinTable(TeachingLanguage::DB_TBL, 'LEFT OUTER JOIN', 'tlanguage_id = afile_record_id', 'tl');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_FLAG_TEACHING_LANGUAGES);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'tlanguage_id as record_id', 'afile_lang_id', 'tlanguage_identifier as record_name', 'afile_type']);
        }
    }

    public function joinBlogPostImage($autoAddFields = false)
    {
        $this->joinTable(BlogPost::DB_TBL, 'LEFT OUTER JOIN', 'post_id = afile_record_id', 'bp');
        $this->addCondition('afile_type', '=', AttachedFile::FILETYPE_BLOG_POST_IMAGE);
        if ($autoAddFields) {
            $this->addMultipleFields(['afile_id', 'post_id as record_id', 'afile_lang_id', 'post_identifier as record_name', 'afile_type']);
        }
    }

}
