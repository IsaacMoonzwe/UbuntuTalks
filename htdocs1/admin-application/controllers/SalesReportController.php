<?php

class SalesReportController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewSalesReport($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditSalesReport($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index($orderDate = '')
    {
        $this->objPrivilege->canViewSalesReport();
        $frmSearch = $this->getSearchForm($orderDate);
        $this->set('frmSearch', $frmSearch);
        $this->set('orderDate', $orderDate);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewSalesReport();
        $db = FatApp::getDb();
        $orderDate = FatApp::getPostedData('orderDate');
        $srchFrm = $this->getSearchForm($orderDate);
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = AdminStatistic::salesReportObject();
        if (empty($orderDate)) {
            $date_from = FatApp::getPostedData('date_from', FatUtility::VAR_DATE, '');
            if (!empty($date_from)) {
                $srch->addCondition('o.order_date_added', '>=', $date_from . ' 00:00:00');
            }
            $date_to = FatApp::getPostedData('date_to', FatUtility::VAR_DATE, '');
            if (!empty($date_to)) {
                $srch->addCondition('o.order_date_added', '<=', $date_to . ' 23:59:59');
            }
            $srch->addGroupBy('DATE(o.order_date_added)');
        } else {
            $this->set('orderDate', $orderDate);
            $srch->addGroupBy('op_invoice_number');
            $srch->addCondition('o.order_date_added', '>=', $orderDate . ' 00:00:00');
            $srch->addCondition('o.order_date_added', '<=', $orderDate . ' 23:59:59');
            $srch->addFld(['op_invoice_number']);
        }
        $srch->addCondition('o.order_type', '=', Order::TYPE_LESSON_BOOKING);
        $srch->addCondition('o.order_is_paid', '=', Order::ORDER_IS_PAID);
        $srch->addOrder('order_date', 'desc');
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

    private function getSearchForm($orderDate = '')
    {
        $frm = new Form('frmSalesReportSearch');
        $frm->addHiddenField('', 'page');
        $frm->addHiddenField('', 'orderDate', $orderDate);
        if (empty($orderDate)) {
            $frm->addDateField(Label::getLabel('LBL_Date_From', $this->adminLangId), 'date_from', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
            $frm->addDateField(Label::getLabel('LBL_Date_To', $this->adminLangId), 'date_to', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
            $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
            $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId), ['onclick' => 'clearSearch();']);
            $fld_submit->attachField($fld_cancel);
        }
        return $frm;
    }

}
