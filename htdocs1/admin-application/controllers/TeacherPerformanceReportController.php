<?php

class TeacherPerformanceReportController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewTeacherPerformanceReport($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditTeacherPerformanceReport($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index($orderDate = '')
    {
        $this->objPrivilege->canViewTeacherPerformanceReport();
        $frmSearch = $this->getSearchForm($orderDate);
        $this->set('frmSearch', $frmSearch);
        $this->set('orderDate', $orderDate);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewTeacherPerformanceReport();
        $db = FatApp::getDb();
        $orderDate = FatApp::getPostedData('orderDate');
        $srchFrm = $this->getSearchForm($orderDate);
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = new UserSearch(false);
        $srch->addCondition('user_is_teacher', '=', 1);
        $srch->joinTeacherLessonData();
        $srch->joinRatingReview();
        $srch->addMultipleFields(['CONCAT(user_first_name, " ", user_last_name) as user_name']);
        if (isset($post['country_id']) && $post['country_id'] > 0) {
            $srch->addCondition('user_country_id', '=', $post['country_id']);
        }
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($page);
        $srch->addOrder('teacher_rating', 'DESC');
        $srch->addOrder('teacherSchLessons', 'DESC');
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $teachersList = $db->fetchAll($rs);
        $totalRecords = $srch->recordCount();
        $this->set("arr_listing", $teachersList);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    private function getSearchForm($orderDate = '')
    {
        $frm = new Form('frmSalesReportSearch');
        $frm->addHiddenField('', 'page');
        $srch = Country::getSearchObject(false, $this->adminLangId);
        $srch->addFld('c.* , c_l.country_name');
        $srch->addCondition('c.country_active', '=', 1);
        $countriesList = [];
        $countriesListOptions = [];
        $countriesList = FatApp::getDb()->fetchAll($srch->getResultSet());
        foreach ($countriesList as $_country) {
            $countriesListOptions[$_country['country_id']] = $_country['country_name'];
        }
        $frm->addSelectBox(Label::getLabel('LBL_Country', $this->adminLangId), 'country_id', $countriesListOptions);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId), ['onclick' => 'clearSearch();']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
