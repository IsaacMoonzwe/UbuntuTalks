<?php

class FreeDaysController extends MyAppController
{

    public function index()
    {
        $srch = FreeDays::getSearchObject($this->siteLangId);
        $srch->addMultipleFields(['faq_id', 'faq_category', 'IFNULL(faq_title, faq_identifier) as faq_title', 'faq_description']);
        $srch->joinTable(FaqCategory::DB_TBL, 'LEFT OUTER JOIN', 'faqcat_id=faq_category');
        $srch->addOrder('faqcat_display_order');
        $srch->setPageSize(50);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetchAll($rs);
        $finaldata = [];
        foreach ($data as $val) {
            $finaldata[$val['faq_category']][] = $val;
        }
        $this->set('finaldata', $finaldata);
        $this->set('typeArr', FreeDays::getFaqCategoryArr($this->siteLangId));
        $this->_template->render();
    }

}
