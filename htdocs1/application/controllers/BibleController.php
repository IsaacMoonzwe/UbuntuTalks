<?php

class BibleController extends MyAppController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $this->_template->render();
    }

    public function search()
    {
        $json = [];
        $post = FatApp::getPostedData();
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        if ($page < 2) {
            $page = 1;
        }
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $json['status'] = true;
        $json['msg'] = '';
        $bibleObj = new BibleContent();
        $srch = $bibleObj->getList($this->siteLangId);
        $srch->addOrder(BibleContent::DB_TBL_PREFIX . 'display_order', 'ASC');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $bibleList = $db->fetchAll($rs);
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set('bibles', $bibleList);
        $post['page'] = $page;
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $json['msg'] = Label::getLabel('LBL_Processing...', $this->siteLangId);
        $json['html'] = $this->_template->render(false, false, 'bible/search.php', true, false);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $json['startRecord'] = $startRecord;
        $json['endRecord'] = $endRecord;
        $json['totalRecords'] = $totalRecords;
        FatUtility::dieJsonSuccess($json);
    }

}
