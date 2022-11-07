<?php

class MetaTag extends MyAppModel
{

    const DB_TBL = 'tbl_meta_tags';
    const DB_TBL_PREFIX = 'meta_';
    const DB_LANG_TBL = 'tbl_meta_tags_lang';
    const DB_LANG_TBL_PREFIX = 'metalang_';
    const META_GROUP_DEFAULT = -1;
    const META_GROUP_OTHER = 0;
    const META_GROUP_TEACHER = 1;
    const META_GROUP_GRP_CLASS = 2;
    const META_GROUP_CMS_PAGE = 3;
    const META_GROUP_BLOG_CATEGORY = 4;
    const META_GROUP_BLOG_POST = 5;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getTabsArr($langId): array
    {
        $metaGroups = [
            static::META_GROUP_DEFAULT => [
                'name' => Label::getLabel('METALBL_Default', $langId),
                'controller' => 'Default',
                'action' => 'Default',
            ],
            static::META_GROUP_OTHER => [
                'name' => Label::getLabel('METALBL_Others', $langId),
                'controller' => '',
                'action' => '',
            ],
            static::META_GROUP_TEACHER => [
                'name' => Label::getLabel('METALBL_Teachers', $langId),
                'controller' => 'Teachers',
                'action' => 'view',
            ],
            static::META_GROUP_GRP_CLASS => [
                'name' => Label::getLabel('METALBL_Group_Classes', $langId),
                'controller' => 'GroupClasses',
                'action' => 'view',
            ],
            static::META_GROUP_CMS_PAGE => [
                'name' => Label::getLabel('METALBL_CMS_Page', $langId),
                'controller' => 'Cms',
                'action' => 'view',
            ],
            static::META_GROUP_BLOG_CATEGORY => [
                'name' => Label::getLabel('METALBL_Blog_Categories', $langId),
                'controller' => 'Blog',
                'action' => 'category',
            ],
            static::META_GROUP_BLOG_POST => [
                'name' => Label::getLabel('METALBL_Blog_Posts', $langId),
                'controller' => 'Blog',
                'action' => 'postDetail',
            ],
        ];
        return $metaGroups;
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL, 'mt');
        return $srch;
    }

    public static function getMetaTagsAdvancedGrpClasses($langId)
    {
        $metaTagSrch = new SearchBase(TeacherGroupClasses::DB_TBL, 'gcls');
        $metaTagSrch->joinTable(static::DB_TBL, 'LEFT OUTER JOIN', "mt.meta_record_id = gcls.grpcls_id and mt.meta_controller = 'GroupClasses' and mt.meta_action = 'view' ", 'mt');
        $metaTagSrch->joinTable(static::DB_LANG_TBL, 'LEFT OUTER JOIN', "mt_l.metalang_meta_id = mt.meta_id AND mt_l.metalang_lang_id = " . $langId, 'mt_l');
        $metaTagSrch->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', "gcls.grpcls_teacher_id= u.user_id", 'u');
        $metaTagSrch->addMultipleFields(['meta_id', 'meta_record_id', 'meta_identifier', 'meta_title', 'grpcls_title', 'grpcls_id', 'concat(u.user_first_name," ",u.user_last_name) as teacher_name']);
        $metaTagSrch->addOrder('grpcls_start_datetime', 'DESC');
        return $metaTagSrch;
    }

    public static function getOrignialUrlFromComponents($row)
    {
        if (empty($row) || $row['meta_controller'] == '') {
            return false;
        }
        $url = '';
        foreach ([$row['meta_controller'], $row['meta_action'], $row['meta_record_id'], $row['meta_subrecord_id']] as $value) {
            if ($value != '0' && $value != '') {
                $url .= $value . '/';
            }
        }
        return rtrim($url, '/');
    }

}
