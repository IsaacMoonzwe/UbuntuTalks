<?php

class MetaTagSearch extends SearchBase
{

    public function __construct(int $langId = 0, int $metaType = MetaTag::META_GROUP_DEFAULT)
    {
        parent::__construct(MetaTag::DB_TBL, 'mt');
        if ($langId > 0) {
            $this->joinTable(MetaTag::DB_LANG_TBL, 'LEFT OUTER JOIN', 'mt_l.metalang_meta_id = mt.meta_id AND mt_l.metalang_lang_id = ' . $langId, 'mt_l');
        }
    }

    public function joinTeachers(int $metaType = MetaTag::META_GROUP_DEFAULT)
    {
        $this->joinTable(User::DB_TBL, 'RIGHT OUTER JOIN', 'mt.meta_record_id = u.user_url_name AND u.user_is_teacher=1 and u.user_deleted = 0 and mt.meta_type=' . $metaType, 'u');
        $this->joinTable(User::DB_TBL_CRED, 'LEFT OUTER JOIN', 'u.user_id = uc.credential_user_id and uc.credential_active = ' . applicationConstants::YES . ' and uc.credential_verified=' . applicationConstants::YES, 'uc');
    }

    public function joinGrpClasses(int $metaType = MetaTag::META_GROUP_DEFAULT)
    {
        $this->joinTable(TeacherGroupClasses::DB_TBL, 'RIGHT OUTER JOIN', 'mt.meta_record_id = gcls.grpcls_id AND mt.meta_type=' . $metaType, 'gcls');
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'gcls.grpcls_teacher_id = u.user_id and u.user_deleted = 0', 'u');
        $this->addCondition('gcls.grpcls_status', '=', TeacherGroupClasses::STATUS_ACTIVE);
        $this->addCondition('gcls.grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
    }

    public function joinCmsPage(int $metaType = MetaTag::META_GROUP_DEFAULT, int $langId)
    {
        $this->joinTable(ContentPage::DB_TBL, 'RIGHT OUTER JOIN', 'mt.meta_record_id = cp.cpage_id AND mt.meta_type=' . $metaType, 'cp');
        $this->joinTable(ContentPage::DB_TBL_LANG, 'LEFT OUTER JOIN', 'cp_l.cpagelang_cpage_id = cp.cpage_id and cp_l.cpagelang_lang_id=' . $langId, 'cp_l');
    }

    public function joinBlogCategories(int $metaType = MetaTag::META_GROUP_DEFAULT, int $langId)
    {
        $this->joinTable(BlogPostCategory::DB_TBL, 'RIGHT OUTER JOIN', 'mt.meta_record_id = bpc.bpcategory_id AND mt.meta_type=' . $metaType, 'bpc');
        $this->joinTable(BlogPostCategory::DB_TBL_LANG, 'LEFT OUTER JOIN', 'bpc.bpcategory_id = bpcl.bpcategorylang_bpcategory_id and bpcl.bpcategorylang_lang_id=' . $langId, 'bpcl');
    }

    public function joinBlogPosts(int $metaType = MetaTag::META_GROUP_DEFAULT, int $langId)
    {
        $this->joinTable(BlogPost::DB_TBL, 'RIGHT OUTER JOIN', 'mt.meta_record_id = bp.post_id AND mt.meta_type=' . $metaType, 'bp');
        $this->joinTable(BlogPost::DB_LANG_TBL, 'LEFT OUTER JOIN', 'bpl.postlang_post_id = bp.post_id and bpl.postlang_lang_id=' . $langId, 'bpl');
    }

    public function searchByCriteria($criteria, int $langId)
    {
        $metaType = $criteria['metaType']['val'];
        if (isset($criteria['keyword']['val']) && $criteria['keyword']['val']) {
            $condition = $this->addCondition('mt.meta_identifier', 'like', '%' . $criteria['keyword']['val'] . '%');
            $condition->attachCondition('mt_l.meta_title', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
        }
        switch ($metaType) {
            case MetaTag::META_GROUP_CMS_PAGE:
                $this->joinCmsPage($criteria['metaType']['val'], $langId);
                $this->addCondition('cpage_deleted', '=', 0);
                if (isset($condition) && $condition) {
                    $condition->attachCondition('cp.cpage_identifier', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                }
                break;
            case MetaTag::META_GROUP_TEACHER:
                $this->joinTeachers($metaType);
                $this->addCondition('u.user_is_teacher', '=', 1, 'AND');
                $this->addCondition('u.user_deleted', '=', 0, 'AND');
                $this->addCondition('u.user_url_name', 'is not', 'mysql_func_null', 'and', true);
                if (isset($condition) && $condition) {
                    $condition->attachCondition('u.user_first_name', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                    $condition->attachCondition('u.user_last_name', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                    $condition->attachCondition('mysql_func_concat(u.user_first_name," ",u.user_last_name)', ' like', '%' . $criteria['keyword']['val'] . '%', 'OR', true);
                }
                break;
            case MetaTag::META_GROUP_GRP_CLASS:
                $this->joinGrpClasses($metaType);
                if (isset($condition) && $condition) {
                    $condition->attachCondition('gcls.grpcls_title', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                }
                break;
            case MetaTag::META_GROUP_BLOG_POST:
                $this->joinBlogPosts($metaType, $langId);
                $this->addCondition('post_deleted', '=', 0);
                if (isset($condition) && $condition) {
                    $condition->attachCondition('bp.post_identifier', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                    $condition->attachCondition('bp.post_identifier', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                }
                break;
            case MetaTag::META_GROUP_BLOG_CATEGORY:
                $this->joinBlogCategories($metaType, $langId);
                $this->addCondition('bpcategory_deleted', '=', 0);
                if (isset($condition) && $condition) {
                    $condition->attachCondition('bpc.bpcategory_identifier', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                    $condition->attachCondition('bpcl.bpcategory_name', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                }
                break;
            default:
                if (!empty($post['keyword'])) {
                    $condition = $this->addCondition('mt.meta_identifier', 'like', '%' . $criteria['keyword']['val'] . '%');
                    $condition->attachCondition('mt_l.meta_title', 'like', '%' . $criteria['keyword']['val'] . '%', 'OR');
                }
                $this->addCondition('mt.meta_type', '=', $metaType);
                break;
        }
        if (isset($criteria['hasTagsAssociated'])) {
            if ($criteria['hasTagsAssociated']['val'] == applicationConstants::YES) {
                $this->addCondition('mt.meta_id', 'is not', 'mysql_func_NULL', 'AND', true);
            } else {
                $this->addCondition('mt.meta_id', 'is', 'mysql_func_NULL', 'AND', true);
            }
        }
    }

}
