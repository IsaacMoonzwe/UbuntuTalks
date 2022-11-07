<?php

class GdprRequestsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewGdprRequests();
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm($this->adminLangId);
        $this->set('frmSrch', $frmSrch);
        $this->_template->render();
    }

    public function search()
    {
        $canEdit = $this->objPrivilege->canEditGdprRequests($this->admin_id, true);
        $frmSrch = $this->getSearchForm($this->adminLangId);
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frmSrch->getValidationErrors());
        }
        $srch = new GdprReqSearch();
        $srch->addCondition('gdprdatareq_status', '!=', GdprRequest::STATUS_DELETED_REQUEST);
        !empty($post['keyword']) && $srch->addCondition('gdprdatareq_reason', 'LIKE', '%' . $post['keyword'] . '%');
        ($post['status'] !== "") && $srch->addCondition('gdprdatareq_status', '=', $post['status']);
        $post['added_on'] && $srch->addCondition('mysql_func_cast(gdprdatareq_added_on AS DATE)', '=', $post['added_on'], 'AND', true);

        $srch->addOrder('gdprdatareq_added_on', 'DESC');

        $page = max($post['page'], 1);
        $pageSize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);

        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $gdprRequests = FatApp::getDb()->fetchAll($srch->getResultSet());
        /* [ */
        $totalRecords = $srch->recordCount();
        $this->set('postedData', $post);
        $this->set('pageCount', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pageSize);
        $this->set('canEdit', $canEdit);
        $this->set('recordCount', $srch->recordCount());
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        /* ] */
        $this->set('gdprStatus', GdprRequest::getStatusArr($this->adminLangId));
        $this->set('gdprRequests', $gdprRequests);
        $this->_template->render(false, false, null, false, false);
    }

    public function view()
    {
        $gdprRequestid = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($gdprRequestid <= 0) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request', $this->adminLangId));
        }
        $srch = new GdprReqSearch();
        $srch->joinUser();
        $srch->addCondition('gdprdatareq_id', '=', $gdprRequestid);
        $rs = $srch->getResultSet();
        $reqDetail = FatApp::getDb()->fetch($rs);
        if (!$reqDetail) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request', $this->adminLangId));
        }
        $frm = $this->changeGdprRequestStatusForm($this->adminLangId);
        $frm->fill($reqDetail);
        $this->set("data", $reqDetail);
        $this->set("frm", $frm);
        $this->_template->render(false, false);
    }

    protected function getSearchForm(int $langId)
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword', $langId), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword', $langId)]);
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $frm->addSelectBox(Label::getLabel('LBl_Status', $langId), 'status', GdprRequest::getStatusArr($langId));
        $frm->addDateField(Label::getLabel('LBl_Added_On', $langId), 'added_on');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $langId));
        $frm->addButton('', 'btn_reset', Label::getLabel('LBL_Reset', $langId));
        return $frm;
    }

    private function changeGdprRequestStatusForm(int $langId)
    {
        $frm = new Form('changeStatusForm');
        $frm->addSelectBox(Label::getLabel('LBL_Request_Status'), 'gdprdatareq_status', GdprRequest::getStatusArr($this->adminLangId), '')->requirements()->setRequired();
        $frm->addHiddenField('', 'gdprdatareq_id', 0);
        $frm->addSubmitButton('', 'btn_submit', 'Update');
        return $frm;
    }

    public function updateStatus()
    {
        $gdprRequestId = FatApp::getPostedData('gdprdatareq_id', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('gdprdatareq_status', FatUtility::VAR_INT, 0);
        $gdpr = new GdprRequest($gdprRequestId);
        if (!$gdpr->loadFromDb()) {
            FatUtility::dieJsonError($this->str_invalid_request);
        }
        $gdprRequestDetail = $gdpr->getFlds();
        if (GdprRequest::STATUS_DELETED_DATA == $status) {
            if (!$gdpr->truncateUserPersonalData($gdprRequestDetail['gdprdatareq_user_id'])) {
                FatUtility::dieJsonError($gdpr->getError());
            }
        }
        $assignValues = ['gdprdatareq_status' => $status];
        $gdpr->assignValues($assignValues);
        if (!$gdpr->save()) {
            FatUtility::dieJsonError($gdpr->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Updated_Successfully!'));
    }

}
