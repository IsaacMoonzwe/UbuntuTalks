<?php

class CommissionController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewCommissionSettings($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditCommissionSettings($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index()
    {
        $this->objPrivilege->canViewCommissionSettings();
        $frmSearch = $this->getSearchForm();
        $this->set("frmSearch", $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewCommissionSettings();
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $srch = Commission::getCommissionSettingsObj($this->adminLangId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        if (!empty($post['keyword'])) {
            $cond = $srch->addCondition('tu.user_first_name', 'like', '%' . $post['keyword'] . '%', 'AND');
            $cond->attachCondition('tuc.credential_username', 'like', '%' . $post['keyword'] . '%', 'OR');
            $cond->attachCondition('tu.user_last_name', 'like', '%' . $post['keyword'] . '%', 'OR');
        }
        $srch->addOrder('commsetting_user_id', 'asc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set("arr_listing", $records);
        $this->_template->render(false, false);
    }

    public function form($commissionId)
    {
        $this->objPrivilege->canViewCommissionSettings();
        $commissionId = FatUtility::int($commissionId);
        $frm = $this->getForm($commissionId);
        if (0 < $commissionId) {
            $data = Commission::getAttributesById($commissionId, ['commsetting_id', 'commsetting_user_id', 'commsetting_fees', 'commsetting_is_grpcls']);
            if ($data === false) {
                FatUtility::dieWithError($this->str_invalid_request);
            }
            if ($data['commsetting_user_id'] > 0) {
                $userObj = new User($data['commsetting_user_id']);
                $res = $userObj->getUserInfo(['credential_email', 'user_last_name', 'user_first_name'], false, false);
                $data['user_name'] = isset($res['credential_email']) ? ($res['user_first_name'] . ' ' . $res['user_last_name'] . " (" . $res['credential_email'] . ")") : '';
            }
            $frm->fill($data);
        }
        $this->set('commsetting_id', $commissionId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        $this->objPrivilege->canEditCommissionSettings();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            FatUtility::dieJsonError(Message::getHtml());
        }
        $commissionId = $post['commsetting_id'];
        unset($post['commsetting_id']);
        $isMandatory = false;
        if ($data = Commission::getAttributesById($commissionId, ['commsetting_is_mandatory'])) {
            $isMandatory = $data['commsetting_is_mandatory'];
        }
        if ($isMandatory) {
            $post['commsetting_user_id'] = 0;
        }
        $record = new Commission($commissionId);
        if (!$record->addUpdateData($post)) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $insertId = $record->getMainTableRecordId();
        if (!$insertId) {
            $insertId = FatApp::getDb()->getInsertId();
        }
        if (!$record->addCommissionHistory($insertId)) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        $this->set('msg', Label::getLabel('MSG_Setup_Successful', $this->adminLangId));
        $this->set('commissionId', $commissionId);
        $this->_template->render(false, false, 'json-success.php');
    }

    public function viewHistory($commsettingId = 0)
    {
        $this->objPrivilege->canViewCommissionSettings();
        $commsettingId = FatUtility::int($commsettingId);
        if (1 > $commsettingId) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $post = FatApp::getPostedData();
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : $post['page'];
        $page = (empty($page) || $page <= 0) ? 1 : FatUtility::int($page);
        $srch = Commission::getCommissionHistorySettingsObj($this->adminLangId);
        $srch->addCondition('tcsh.csh_commsetting_id', '=', $commsettingId);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function deleteRecord()
    {
        $this->objPrivilege->canEditCommissionSettings();
        $commissionId = FatApp::getPostedData('id', FatUtility::VAR_INT, 0);
        if ($commissionId < 1) {
            FatUtility::dieJsonError($this->str_invalid_request_id);
        }
        $row = Commission::getAttributesById($commissionId, ['commsetting_id', 'commsetting_is_mandatory', 'commsetting_user_id']);
        if ($row == false || ($row != false && $row['commsetting_is_mandatory'] == 1) || $row['commsetting_user_id'] == 0) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $obj = new Commission($commissionId);
        $obj->assignValues(['commsetting_deleted' => 1]);
        if (!$obj->save()) {
            Message::addErrorMessage($obj->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        FatUtility::dieJsonSuccess($this->str_delete_record);
    }

    public function userAutoComplete()
    {
        $this->objPrivilege->canViewCommissionSettings();
        $userObj = new User();
        $srch = $userObj->getUserSearchObj(['u.user_first_name', 'u.user_id', 'u.user_last_name', 'uc.credential_email']);
        $srch->addCondition('user_is_teacher', '=', 1);
        $post = FatApp::getPostedData();
        if (!empty($post['keyword'])) {
            $srch->addCondition('u.user_first_name', 'LIKE', '%' . $post['keyword'] . '%')
                    ->attachCondition('u.user_last_name', 'LIKE', '%' . $post['keyword'] . '%')
                    ->attachCondition('uc.credential_email', 'LIKE', '%' . $post['keyword'] . '%');
        }
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $users = $db->fetchAll($rs, 'user_id');
        $json = [];
        foreach ($users as $key => $user) {
            $json[] = ['id' => $key, 'name' => strip_tags(html_entity_decode($user['user_first_name'] . ' ' . $user['user_last_name'] . " (" . $user['credential_email'] . ")", ENT_QUOTES, 'UTF-8'))];
        }
        die(json_encode($json));
    }

    public function productAutoComplete()
    {
        $this->objPrivilege->canViewCommissionSettings();
        $srch = Product::getSearchObject($this->adminLangId);
        $post = FatApp::getPostedData();
        if (!empty($post['keyword'])) {
            $srch->addCondition('product_name', 'LIKE', '%' . $post['keyword'] . '%');
        }
        $srch->setPageSize(10);
        $srch->addMultipleFields(['product_id', 'IFNULL(product_name,product_identifier) as product_name']);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $products = $db->fetchAll($rs, 'product_id');
        $json = [];
        foreach ($products as $key => $product) {
            $json[] = ['id' => $key, 'name' => strip_tags(html_entity_decode($product['product_name'], ENT_QUOTES, 'UTF-8'))];
        }
        die(json_encode($json));
    }

    private function getForm($commissionId = 0)
    {
        $this->objPrivilege->canViewCommissionSettings();
        $commissionId = FatUtility::int($commissionId);
        $isMandatory = false;
        $notGlobal = true;
        $isGrpCls = true;
        if ($data = Commission::getAttributesById($commissionId, ['commsetting_is_mandatory', 'commsetting_user_id', 'commsetting_is_grpcls'])) {
            $isMandatory = $data['commsetting_is_mandatory'];
            if ($data['commsetting_user_id'] == 0) {
                $notGlobal = false;
            }
            $isGrpCls = $data['commsetting_is_grpcls'];
        }
        $frm = new Form('frmCommission');
        $frm->addHiddenField('', 'commsetting_id', $commissionId);
        if (!$isMandatory) {
            if ($notGlobal) {
                $frm->addTextBox(Label::getLabel('LBL_Seller', $this->adminLangId), 'user_name');
            }
            $frm->addHiddenField('', 'commsetting_user_id', 0);
        }
        $fld = $frm->addFloatField(Label::getLabel('LBL_Commission_fees_[%]', $this->adminLangId), 'commsetting_fees');
        $fld->requirements()->setRange(1, 100);
        if ($notGlobal || $isGrpCls) {
            $frm->addCheckBox(Label::getLabel('LBL_Is_Group_Class', $this->adminLangId), 'commsetting_is_grpcls', 1, ($isGrpCls && !$notGlobal ? ['disabled' => 'disabled'] : []));
        }
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $this->adminLangId));
        return $frm;
    }

    private function getSearchForm()
    {
        $this->objPrivilege->canViewCommissionSettings();
        $frm = new Form('frmCommissionSearch');
        $f1 = $frm->addTextBox(Label::getLabel('LBL_Keyword', $this->adminLangId), 'keyword', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

}
