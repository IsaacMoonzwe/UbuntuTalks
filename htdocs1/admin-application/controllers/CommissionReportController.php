<?php

class CommissionReportController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCommissionReport();
    }

    public function index()
    {
        $frm = $this->searchForm();
        $this->set('frm', $frm);
        $this->_template->render();
    }

    public function search()
    {
        $frm = $this->searchForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $orderSearch = new OrderSearch();
        $orderSearch->joinOrderProduct();
        $orderSearch->joinScheduledLessonDetail();
        $orderSearch->joinScheduledLesson();
        $orderSearch->addCondition('slesson_status', '=', ScheduledLesson::STATUS_COMPLETED);
        $orderSearch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $orderSearch->addCondition('order_net_amount', '>', 0);
        $orderSearch->addCondition('order_type', '=', Order::TYPE_LESSON_BOOKING);
        $orderSearch->addCondition('op_lpackage_is_free_trial', '=', applicationConstants::NO);
        $orderSearch->addMultipleFields([
            'count(sl.slesson_id) as totalLesson', 'order_net_amount', 'op_teacher_id',
            '(((order_net_amount / op_qty ) - op_commission_charged) * count(sl.slesson_id)) as totalAdminCommission ',
        ]);
        $dateTo = FatApp::getPostedData('dateto', FatUtility::VAR_DATE, '');
        if (!empty($dateTo)) {
            $orderSearch->addDirectCondition("DATE(order_date_added) <= '" . $post['dateto'] . "'");
        }
        $dateFrom = FatApp::getPostedData('datefrom', FatUtility::VAR_DATE, '');
        if ($dateFrom) {
            $orderSearch->addDirectCondition("DATE(order_date_added) >= '" . $post['datefrom'] . "'");
        }
        $orderSearch->addHaving('totalAdminCommission', '>', 0);
        $orderSearch->addGroupBy('order_id');
        $user = new User();
        $attrArray = ['credential_email', 'CONCAT(`user_first_name`," ",`user_last_name`) as fullName'];
        $userSearch = $user->getUserSearchObj($attrArray, true);
        $userSearch->addMultipleFields([
            'SUM(ord.totalLesson) as totalQuantity',
            'SUM(ord.totalAdminCommission) totalCommision',
            'SUM(ord.order_net_amount) as saleOrderValue'
        ]);
        $userSearch->joinTable('(' . $orderSearch->getQuery() . ')', 'INNER JOIN', 'u.user_id = ord.op_teacher_id', 'ord');
        $userSearch->addCondition('user_is_teacher', '=', applicationConstants::YES);
        if ($post['search']) {
            $cond = $userSearch->addCondition('user_first_name', 'like', '%' . $post['search'] . '%');
            $cond->attachCondition('user_last_name', 'like', '%' . $post['search'] . '%', 'OR');
            $cond->attachCondition('credential_email', 'like', '%' . $post['search'] . '%');
        }
        $userSearch->addGroupBy('u.user_id');
        $userSearch->addOrder('totalCommision', 'DESC');
        $userSearch->setPageNumber($page);
        $userSearch->setPageSize($pagesize);
        $resultSet = $userSearch->getResultSet();
        $resultData = FatApp::getdb()->fetchAll($resultSet);
        $this->set('data', $resultData);
        $this->set('adminLangId', $this->adminLangId);
        $this->set('postedData', $post);
        $this->set('pageCount', $userSearch->pages());
        $this->set('recordCount', $userSearch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->_template->render(false, false);
    }

    private function searchForm()
    {
        $frm = new Form('comssionReportForm');
        $frm->addDateField(Label::getLabel('LBL_DATE_FROM', $this->adminLangId), 'datefrom', '', []);
        $frm->addDateField(Label::getLabel('LBL_DATE_TO', $this->adminLangId), 'dateto', '', []);
        $frm->addTextBox(Label::getLabel('LBL_SEARCH_FOR_TEACHERS', $this->adminLangId), 'search', '', []);
        $frm->addHiddenField('', 'page', 1);
        $fldSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fldCancel = $frm->addButton('', 'btn_clear', Label::getLabel('LBL_CLEAR_SEARCH', $this->adminLangId), ['onClick' => 'clear_search()']);
        $fldSubmit->attachField($fldCancel);
        return $frm;
    }

}
