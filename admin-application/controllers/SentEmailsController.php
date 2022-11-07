<?php

class SentEmailsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $frm = $this->sentEmailSearchForm();
        $this->set('srchFrm', $frm);
        $this->_template->render();
    }

    public function search()
    {
        
        if (!FatUtility::isAjaxCall()) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $keyword = FatApp::getPostedData('keyword');
        $user_verified = FatApp::getPostedData('user_verified');
    

        $srchFrm = $this->sentEmailSearchForm();
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        $page = (empty($post['page']) || $post['page'] <= 0) ? 1 : intval($post['page']);
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $sentEmailObj = new SentEmail();

        $srch = $sentEmailObj->getSearchObject(true,$keyword,$user_verified);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        
        $rs = $srch->getResultSet();
        $arr_listing = FatApp::getDb()->fetchAll($rs);

        $this->set("arr_listing", $arr_listing);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    public function view($id)
    {
        $row_data = SentEmail::getAttributesById($id);
        $this->set('data', $row_data);
        $this->_template->render(false, false);
    }

    private function sentEmailSearchForm()
    {
        $frm = new Form('sentEmailSrchForm');
        $frm->addTextBox(Label::getLabel('LBL_Email', $this->adminLangId), 'keyword', '', ['id' => 'keyword', 'autocomplete' => 'off']);
        $arr_options = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getActiveInactiveArr($this->adminLangId);
        $arr_options1 = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + applicationConstants::getYesNoArr($this->adminLangId);
        $arr_options2 = ['-1' => Label::getLabel('LBL_Does_Not_Matter', $this->adminLangId)] + User::getUserTypesArr($this->adminLangId);
        $arr_options2 = $arr_options2 + [User::USER_TYPE_LEARNER_TEACHER => Label::getLabel('LBL_Learner', $this->adminLangId) . '+' . Label::getLabel('LBL_Teacher', $this->adminLangId)];
        $frm->addSelectBox(Label::getLabel('LBL_Email_Verified', $this->adminLangId), 'user_verified', $arr_options1, -1, [], '');
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'user_id', '');
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        $frm->addHiddenField('', 'page');
        return $frm;
    }

    public function autoCompleteJson()
    {
    
        $pagesize = 20;
        $post = FatApp::getPostedData();
        $skipDeletedUser = true;
        $userObj = new SentEmail();
        $keyword = FatApp::getPostedData('keyword', null, '');
        $user_verified = FatApp::getPostedData('user_verified', null, '');
        $srch = $userObj->getEmailSearchObj([ 'm.emailarchive_id',
            'm.emailarchive_to_email','emailarchive_to_email','credential_email',
            'credential_verified',
            'credential_active'
        ],true,$keyword,$user_verified);
        $srch->addOrder('credential_email', 'ASC');
        $keyword = FatApp::getPostedData('keyword');
        $user_verified = FatApp::getPostedData('user_verified');
        $cond = $srch->addCondition('uc.credential_email', 'like', '%' . $keyword . '%');
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $users = $db->fetchAll($rs, 'emailarchive_id');
        $json = [];
        foreach ($users as $key => $user) {
            
            $user_full_name = strip_tags(html_entity_decode($user['emailarchive_to_email'], ENT_QUOTES, 'UTF-8'));
            $json[] = [
                'id' => $key, 'name' => $user_full_name,
            ];
        }
        die(json_encode($json));
    }

}
