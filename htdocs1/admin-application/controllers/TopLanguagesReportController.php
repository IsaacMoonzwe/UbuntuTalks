<?php

class TopLanguagesReportController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewTopLangReport($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditTopLangReport($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index($orderDate = '')
    {
        $this->objPrivilege->canViewTopLangReport();
        $frmSearch = $this->getSearchForm($orderDate);
        $this->set('frmSearch', $frmSearch);
        $this->set('orderDate', $orderDate);
        $this->_template->render();
    }

    public function viewSchedules($langId, $countryid = 0)
    {
        $this->objPrivilege->canViewTopLangReport();
        $frmSearch = $this->getScheduleSearchForm($langId, $countryid);
        $this->set('frmSearch', $frmSearch);
        $this->set('SLangId', $langId);
        $this->set('countryid', $countryid);
        $this->_template->render();
    }

    public function searchSchedule()
    {
        $this->objPrivilege->canViewTopLangReport();
        $db = FatApp::getDb();
        $langId = FatApp::getPostedData('langId');
        $countryid = FatApp::getPostedData('countryid');
        $srchFrm = $this->getScheduleSearchForm($langId, $countryid);
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = new ScheduledLessonSearch(false);
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinTeacherSettings();
        $srch->joinLessonLanguage($this->adminLangId);
        $srch->addCondition('slns.slesson_slanguage_id', ' = ', $langId);
        if ($countryid > 0) {
            $srch->addCondition('ul.user_country_id', '=', $countryid);
        }
        $srch->addMultipleFields([
            'slns.slesson_id',
            'slns.slesson_date',
            'slns.slesson_status',
            'IFNULL(sl.tlanguage_name, tlang.tlanguage_identifier) as teacherTeachLanguageName',
            'CONCAT(ul.user_first_name, " " , ul.user_last_name) AS learner_username',
            'CONCAT(ut.user_first_name, " " , ut.user_last_name) AS teacher_username',
        ]);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetchAll($rs);
        if ($data == false) {
            Message::addErrorMessage('Error: Lessons not allocated yet.');
        }
        $statusArr = ScheduledLesson::getStatusArr();
        $this->set('arr_listing', $data);
        $this->set('status_arr', $statusArr);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function search()
    {
        $this->objPrivilege->canViewTopLangReport();
        $db = FatApp::getDb();
        $orderDate = FatApp::getPostedData('orderDate');
        $srchFrm = $this->getSearchForm($orderDate);
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = AdminStatistic::LessonLanguagesObject($this->adminLangId, $post);
        $srch->addGroupBy('slesson_slanguage_id');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'ul.user_id = sld.sldetail_learner_id', 'ul');
        if (isset($post['country_id']) && $post['country_id'] > 0) {
            $srch->addCondition('ul.user_country_id', '=', $post['country_id']);
            $this->set('country_id', $post['country_id']);
            $joinQuery = ' slns INNER JOIN `tbl_scheduled_lesson_details` AS sld ON sld.sldetail_slesson_id = slns.slesson_id INNER JOIN `tbl_users` AS countUl ON countUl.user_id = sld.sldetail_learner_id  where slesson_slanguage_id = slns.slesson_slanguage_id AND countUl.user_country_id = ' . $post['country_id'];
        } else {
            $joinQuery = ' WHERE slesson_slanguage_id = slns.slesson_slanguage_id';
        }
        $joinQueryCancelled = $joinQuery;
        if (empty($orderDate)) {
            $date_from = FatApp::getPostedData('date_from', FatUtility::VAR_DATE, '');
            if (!empty($date_from)) {
                $srch->addCondition('slesson_added_on', '>=', $date_from . ' 00:00:00');
                $joinQuery .= ' AND slesson_ended_on >="' . $date_from . ' 00:00:00"';
            }
            $date_to = FatApp::getPostedData('date_to', FatUtility::VAR_DATE, '');
            if (!empty($date_to)) {
                $srch->addCondition('slesson_added_on', '<=', $date_to . ' 23:59:59');
                $joinQuery .= ' AND slesson_ended_on <="' . $date_to . ' 23:59:59"';
            }
        } else {
            $this->set('orderDate', $orderDate);
            $srch->addCondition('slesson_added_on', '>=', $orderDate . ' 00:00:00');
            $srch->addCondition('slesson_added_on', '<=', $orderDate . ' 23:59:59');
        }
        $srch->addMultipleFields([
            'IFNULL(tlanguage_name , tlanguage_Identifier) as languageName',
            'count(slesson_id) as lessonsSold',
            'slesson_slanguage_id',
            '(select COUNT(IF(slesson_status="' . ScheduledLesson::STATUS_COMPLETED . '",1,null)) from ' . ScheduledLesson::DB_TBL . $joinQuery . ' ) as completedLessons',
            '(select COUNT(IF(slesson_status="' . ScheduledLesson::STATUS_CANCELLED . '",1,null)) from ' . ScheduledLesson::DB_TBL . $joinQueryCancelled . ' ) as cancelledLessons'
        ]);
        $srch->addOrder('lessonsSold', 'desc');
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $arr_listing = $db->fetchAll($rs);
        $this->set("arr_listing", $arr_listing);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    private function getScheduleSearchForm($langId, $countryid)
    {
        $frm = new Form('frmScheduleReportSearch');
        $frm->addHiddenField('', 'page');
        $frm->addHiddenField('', 'langId', $langId);
        $frm->addHiddenField('', 'countryid', $countryid);
        return $frm;
    }

    private function getSearchForm($orderDate = '')
    {
        $frm = new Form('frmSalesReportSearch');
        $frm->addHiddenField('', 'page');
        $frm->addHiddenField('', 'orderDate', $orderDate);
        if (empty($orderDate)) {
            $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
            $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
            $srch = Country::getSearchObject(false, $this->adminLangId);
            $srch->addFld('c.* , c_l.country_name');
            $srch->addCondition('c.country_active', '=', 1);
            $rs = $srch->getResultSet();
            $countriesList = [];
            $countriesListOptions = [];
            if ($rs) {
                $countriesList = FatApp::getDb()->fetchAll($rs);
                if (!empty($countriesList)) {
                    foreach ($countriesList as $_country) {
                        $countriesListOptions[$_country['country_id']] = $_country['country_name'];
                    }
                }
            }
            $frm->addSelectBox(Label::getLabel('LBL_Country', $this->adminLangId), 'country_id', $countriesListOptions);
            $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
            $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId), ['onclick' => 'clearSearch();']);
            $fld_submit->attachField($fld_cancel);
        }
        return $frm;
    }

}
