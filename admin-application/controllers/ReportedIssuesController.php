<?php

class ReportedIssuesController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewIssuesReported();
    }

    public function index()
    {
        $this->set('frm', $this->getSearchForm());
        $this->_template->render();
    }

    public function escalated()
    {
        $frm = $this->getSearchForm();
        $frm->fill(['repiss_status' => ReportedIssue::STATUS_ESCLATED]);
        $this->set('frm', $frm);
        $this->_template->addJs('reported-issues/page-js/index.js');
        $this->_template->render();
    }

    public function search()
    {
        $frm = $this->getSearchForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $srch = ReportedIssue::getSearchObject();
        $srch->addMultipleFields([
            'repiss.repiss_id', 'repiss.repiss_title', 'repiss.repiss_sldetail_id',
            'repiss.repiss_reported_on', 'repiss.repiss_reported_by', 'repiss.repiss_status',
            'repiss.repiss_comment', 'repiss.repiss_updated_on', 'sldetail.sldetail_order_id',
            'CONCAT(user.user_first_name, " ", user.user_last_name) AS reporter_username'
        ]);
        if ($post['repiss_status'] > 0) {
            $srch->addCondition('repiss.repiss_status', '=', $post['repiss_status']);
        }
        if (!empty($post['sldetail_order_id'])) {
            $srch->addCondition('sldetail.sldetail_order_id', 'LIKE', '%' . $post['sldetail_order_id'] . '%');
        }
        if ($post['repiss_sldetail_id'] != '') {
            $srch->addCondition('repiss.repiss_sldetail_id', '=', $post['repiss_sldetail_id']);
        }
        if ($post['sldetail_learner_id'] > 0) {
            $srch->addCondition('sldetail.sldetail_learner_id', '=', $post['sldetail_learner_id']);
        }
        if ($post['slesson_teacher_id'] > 0) {
            $srch->addCondition('slesson.slesson_teacher_id', '=', $post['slesson_teacher_id']);
        }
        $srch->setPageNumber($post['page']);
        $srch->setPageSize($post['pageSize']);
        $srch->addGroupBy('repiss.repiss_id');
        $srch->addOrder('repiss.repiss_id', 'DESC');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $this->set('post', $post);
        $this->set("records", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false, null, false, false);
    }

    public function view($issueId)
    {
        $issue = ReportedIssue::getIssueById($issueId);
        if (empty($issue)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $this->set("issue", $issue);
        $this->set('actionArr', ReportedIssue::getActionsArr());
        $this->set('logs', ReportedIssue::getIssueLogsById($issueId));
        $this->_template->render(false, false);
    }

    public function actionForm($issueId)
    {
        $issue = ReportedIssue::getIssueById($issueId);
        if (empty($issue)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $logs = ReportedIssue::getIssueLogsById($issueId);
        $lastLog = end($logs);
        $isGroupClass = (FatUtility::int($issue['slesson_grpcls_id']) > 0);
        $frm = $this->getActionForm($isGroupClass);
        $frm->fill([
            'reislo_repiss_id' => $issue['repiss_id'],
            'reislo_action' => $lastLog['reislo_action'] ?? ''
        ]);
        $this->set('frm', $frm);
        $this->set('logs', $logs);
        $this->set("issue", $issue);
        $this->set('statusArr', ReportedIssue::getStatusArr());
        $this->set('actionArr', ReportedIssue::getActionsArr());
        $this->_template->render(false, false);
    }

    public function setupAction()
    {
        $langid = CommonHelper::getLangId();
        $frm = $this->getActionForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        if (ReportedIssue::ACTION_ESCLATE_TO_ADMIN == $post['reislo_action']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PLEASE_SELECT_DIFFERENT_ACTION'));
        }
        $issue = ReportedIssue::getIssueById($post['reislo_repiss_id']);


        $srch = new ScheduledLessonDetailsSearch();
        $srch->joinScheduledLesson();
        $srch->joinTeacher();
        $srch->joinTeacherCredentials();
        $srch->joinLearner();
        $srch->joinLearnerCredentials();
        $srch->joinLessonLanguage();
        $srch->addCondition('sldetail_id','=',$issue['repiss_sldetail_id']);
        $srch->addMultipleFields(['ul.user_id as learnerId,tcred.credential_email as teacher_email','lcred.credential_email as learner_email','concat(ul.user_first_name," ",ul.user_last_name) as learner_name','concat(ut.user_first_name," ",ut.user_last_name) as teacher_name','IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name','slesson_date','slesson_start_time','slesson_end_time']);
        $data = FatApp::getDb()->fetch($srch->getResultSet());

        if (empty($issue)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($issue['slesson_grpcls_id'] > 0 && $post['reislo_action'] == ReportedIssue::ACTION_RESET_AND_UNSCHEDULED) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $reportedIssue = new ReportedIssue($post['reislo_repiss_id'], $this->admin_id, ReportedIssue::USER_TYPE_SUPPORT);
        if (!$reportedIssue->setupIssueAction($post['reislo_action'], $post['reislo_comment'], true)) {
            FatUtility::dieJsonError($reportedIssue->getError());
        }

        $userTimeZone = MyDate::getUserTimeZone($data['learnerId']);
        $sysTimeZone = MyDate::getTimeZone();

        $emailVars = [
            'issue_type'=>$issue['repiss_title'],
            'teacher_email'=>$data['teacher_email'],
            'learner_email'=>$data['learner_email'],
            'learner_name'=>$data['learner_name'],
            'teacher_name'=>$data['teacher_name'],
            'tlanguage_name'=>$data['tlanguage_name'],
            'slesson_date'=>MyDate::changeDateTimezone($data['slesson_date'],$sysTimeZone,$userTimeZone),
            'slesson_start_time'=>MyDate::changeDateTimezone($data['slesson_start_time'],$sysTimeZone,$userTimeZone),
            'slesson_end_time'=>MyDate::changeDateTimezone($data['slesson_end_time'],$sysTimeZone,$userTimeZone),
            'resolution_action_admin'=>ReportedIssue::getActionsArr($post['reislo_action']),
        ];
       
        $email = New EmailHandler();
        $email->sendEmailOfIssueResolutionToLearnerFromAdmin($data['learner_email'],$langid,$emailVars);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $frm->addTextBox(Label::getLabel('LBL_Teacher', $this->adminLangId), 'teacher');
        $frm->addTextBox(Label::getLabel('LBL_Learner', $this->adminLangId), 'learner');
        $frm->addSelectBox(Label::getLabel('LBL_Status', $this->adminLangId), 'repiss_status', ReportedIssue::getStatusArr());
        $frm->addTextBox(Label::getLabel('LBL_Order_Id', $this->adminLangId), 'sldetail_order_id');
        $frm->addTextBox(Label::getLabel('LBL_Lesson_Id', $this->adminLangId), 'repiss_sldetail_id');
        $frm->addHiddenField('', 'pageSize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'));
        $frm->addHiddenField('', 'page', 1);
        $frm->addHiddenField('', 'slesson_teacher_id', 0);
        $frm->addHiddenField('', 'sldetail_learner_id', 0);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $this->adminLangId));
        $fld_cancel = $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $this->adminLangId));
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function getActionForm(bool $isGroupClass = false)
    {
        $frm = new Form('actionFrm');
        $repissId = $frm->addHiddenField('', 'reislo_repiss_id');
        $repissId->requirements()->setRequired();
        $repissId->requirements()->setIntPositive();
        $options = ReportedIssue::getActionsArr();
        unset($options[ReportedIssue::ACTION_ESCLATE_TO_ADMIN]);
        if ($isGroupClass) {
            unset($options[ReportedIssue::ACTION_RESET_AND_UNSCHEDULED]);
        }
        $frm->addSelectBox(Label::getLabel('LBL_TAKE_ACTION', $this->adminLangId), 'reislo_action', $options)->requirements()->setRequired();
        $frm->addTextArea(Label::getLabel('LBL_ADMIN_COMMENT'), 'reislo_comment', '')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

}
