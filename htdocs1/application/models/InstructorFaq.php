<?php

class InstructorFaq extends MyAppModel
{

    const DB_TBL = 'tbl_instructorfaq';
    const DB_TBL_PREFIX = 'faq_';
    const DB_TBL_LANG = 'tbl_instructorfaq_lang';
    const CATEGORY_GENERAL_QUERIES = 1;
    const CATEGORY_APPLICATION = 2;
    const CATEGORY_PAYMENTS = 3;
    const CATEGORY_APPLY_TO_TEACH = 4;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->db = FatApp::getDb();
    }

    public static function getFaqCategoryArr(int $langid)
    {
        $srch = FaqCategory::getSearchObject($langid);
        $srch->addCondition('faqcat_active', '=', 1);
        $srch->addOrder('faqcat_display_order', 'ASC');
        $rs = $srch->getResultSet();
        $CatList = [];
        $category_options = [];
        if ($rs) {
            $CatList = FatApp::getDb()->fetchAll($rs);
        }
        if (!empty($CatList)) {
            foreach ($CatList as $cat) {
                if (isset($cat['faqcat_name']) && $cat['faqcat_name'] != '') {
                    $name = $cat['faqcat_name'];
                } else {
                    $name = $cat['faqcat_identifier'];
                }
                $category_options[$cat['faqcat_id']] = $name;
            }
        }
        return $category_options;
    }

    public static function getSearchObject(int $langId, $active = true)
    {
        $srch = new SearchBase(static::DB_TBL, 't');
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 't_l.faqlang_faq_id = t.faq_id AND faqlang_lang_id = ' . $langId, 't_l');
        }
        if ($active == true) {
            $srch->addCondition('t.faq_active', '=', applicationConstants::ACTIVE);
        }
        return $srch;
    }

}
