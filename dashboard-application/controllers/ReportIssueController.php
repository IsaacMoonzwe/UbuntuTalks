<?php

class ReportIssueController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function form($sldetailId)
    {
        $sldetailId = FatUtility::int($sldetailId);
        $userId = UserAuthentication::getLoggedUserId();
        $reportIssue = new ReportedIssue(0, $userId);
        if (!$lesson = $reportIssue->getLessonToReport($sldetailId)) {
            FatUtility::dieJsonError($reportIssue->getError());
        }
        $frm = $this->getForm();
        $frm->fill(['repiss_sldetail_id' => $sldetailId]);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function setup()
    {
        
        $frm = $this->getForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $langId = CommonHelper::getLangId();
        $userId = UserAuthentication::getLoggedUserId();
        $sldetailId = FatUtility::int($post['repiss_sldetail_id']);
        $srch = new ScheduledLessonDetailsSearch();
        $srch->joinScheduledLesson();
        $srch->joinTeacher();
        $srch->joinTeacherCredentials();
        $srch->joinLearner();
        $srch->joinLearnerCredentials();
        $srch->joinLessonLanguage();
        $srch->addCondition('sldetail_id','=',$post['repiss_sldetail_id']);
        $srch->addMultipleFields(['ut.user_id as teacherId,tcred.credential_email as teacher_email','lcred.credential_email as learner_email','concat(ul.user_first_name," ",ul.user_last_name) as learner_name','concat(ut.user_first_name," ",ut.user_last_name) as teacher_name','IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name','slesson_date','slesson_start_time','slesson_end_time']);
        $data = FatApp::getDb()->fetch($srch->getResultSet());
        $teacherTimeZone = MyDate::getUserTimeZone($data['teacherId']);
        $sysTimeZone = MyDate::getTimeZone();
        $options = IssueReportOptions::getOptionsArray($langId, User::USER_TYPE_LEANER);
        $emailVars = [
            'issue_type'=>$options[$post['repiss_title']] ?? 'NA',
            'teacher_email'=>$data['teacher_email'],
            'learner_email'=>$data['learner_email'],
            'learner_name'=>$data['learner_name'],
            'teacher_name'=>$data['teacher_name'],
            'tlanguage_name'=>$data['tlanguage_name'],
            'slesson_date'=>MyDate::changeDateTimezone($data['slesson_date'],$sysTimeZone,$teacherTimeZone),
            'slesson_start_time'=>MyDate::changeDateTimezone($data['slesson_start_time'],$sysTimeZone,$teacherTimeZone),
            'slesson_end_time'=>MyDate::changeDateTimezone($data['slesson_end_time'],$sysTimeZone,$teacherTimeZone),
            'learner_comment'=>$post['repiss_comment'],
        ];
        $reportIssue = new ReportedIssue(0, $userId);
        if (!$lesson = $reportIssue->getLessonToReport($sldetailId)) {
            FatUtility::dieJsonError($reportIssue->getError());
        }
        if (!$reportIssue->setupIssue($sldetailId, $post['repiss_title'], $post['repiss_comment'])) {
            FatUtility::dieJsonError($reportIssue->getError());
        }
        $email = new EmailHandler();
        $email->sendEmailOfIssueReportToTeacher($data['teacher_email'],$langId,$emailVars);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    public function detail($issueId)
    {
        $issue = ReportedIssue::getIssueById(FatUtility::int($issueId));
        if (empty($issue)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $userId = UserAuthentication::getLoggedUserId();
        $logs = ReportedIssue::getIssueLogsById($issueId);
        $log = end($logs);
        $esclateHours = FatApp::getConfig('CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION');
        $esclateDate = strtotime($issue['repiss_updated_on'] . " +" . $esclateHours . " hour");
        $canEsclate = false;
        if (($esclateDate > strtotime(date('Y-m-d H:i:s')) && ($issue['repiss_status'] == ReportedIssue::STATUS_RESOLVED) &&
                (($log['reislo_added_by_type'] ?? 0) == ReportedIssue::USER_TYPE_TEACHER) && (($log['reislo_added_by'] ?? 0) != $userId))) {
            $canEsclate = true;
        }
        $this->set('logs', $logs);
        $this->set('issue', $issue);
        $this->set('canEsclate', $canEsclate);
        $this->set('userTimezone', MyDate::getUserTimeZone());
        $this->set('actionArr', ReportedIssue::getActionsArr());
        $this->_template->render(false, false);
    }

    private function getForm()
    {
        $frm = new Form('reportIssueFrm');
        $options = IssueReportOptions::getOptionsArray($this->siteLangId, ReportedIssue::USER_TYPE_LEARNER);
        $fld = $frm->addSelectBox(Label::getLabel('LBL_Subject'), 'repiss_title', $options);
        $fld->requirements()->setRequired(true);
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'repiss_comment', '');
        $fld->requirement->setRequired(true);
        $fld = $frm->addHiddenField(Label::getLabel('LBL_slesson_id'), 'repiss_sldetail_id');
        $fld->requirements()->setRequired(true);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_SUBMIT'));
        return $frm;
    }

    public function resolveForm($issueId)
    {
        $issue = ReportedIssue::getIssueById($issueId);
        $userId = UserAuthentication::getLoggedUserId();
        if (empty($issue) || $userId != $issue['slesson_teacher_id']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($issue['repiss_status'] > ReportedIssue::STATUS_PROGRESS) {
            FatUtility::dieJsonError(Label::getLabel('LBL_RESOLUTION_PROVIDED_ALREADY'));
        }
        $isGroupClass = (FatUtility::int($issue['slesson_grpcls_id']) > 0);
        $frm = $this->getResolveForm($isGroupClass);
        $frm->fill(['reislo_repiss_id' => $issue['repiss_id']]);
        $this->set('frm', $frm);
        $this->set("issue", $issue);
        $this->set('statusArr', ReportedIssue::getStatusArr());
        $this->_template->render(false, false);
    }

    public function resolveSetup()
    {
        $frm = $this->getResolveForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }

        $userId = UserAuthentication::getLoggedUserId();
        $issue = ReportedIssue::getIssueById($post['reislo_repiss_id']);
        $langId = CommonHelper::getLangId();
        if (empty($issue) || $userId != $issue['slesson_teacher_id']) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        if ($issue['repiss_status'] > ReportedIssue::STATUS_PROGRESS) {
            FatUtility::dieJsonError(Label::getLabel('LBL_RESOLUTION_PROVIDED_ALREADY'));
        }
        if ($issue['slesson_grpcls_id'] > 0 && $post['reislo_action'] == ReportedIssue::ACTION_RESET_AND_UNSCHEDULED) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
       
        $reportedIssue = new ReportedIssue($post['reislo_repiss_id'], $userId, ReportedIssue::USER_TYPE_TEACHER);
        if (!$reportedIssue->setupIssueAction($post['reislo_action'], $post['reislo_comment'], false)) {
            FatUtility::dieJsonError($reportedIssue->getError());
        }

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

        $userTimezone = MyDate::getUserTimeZone($data['learnerId']);
        $sysTimeZone = MyDate::getTimeZone();

        $emailVars = [
            'issue_type'=>$issue['repiss_title'],
            'teacher_email'=>$data['teacher_email'],
            'learner_email'=>$data['learner_email'],
            'learner_name'=>$data['learner_name'],
            'teacher_name'=>$data['teacher_name'],
            'tlanguage_name'=>$data['tlanguage_name'],
            'slesson_date'=>MyDate::changeDateTimezone($data['slesson_date'],$sysTimeZone,$userTimezone),
            'slesson_start_time'=>MyDate::changeDateTimezone($data['slesson_start_time'],$sysTimeZone,$userTimezone),
            'slesson_end_time'=>MyDate::changeDateTimezone($data['slesson_end_time'],$sysTimeZone,$userTimezone),
            'teacher_comment'=>$post['reislo_comment'],
            'issue_resolve_type'=>ReportedIssue::getActionsArr($post['reislo_action']),
        ];
        
        $email = new EmailHandler();
        $email->sendEmailOfIssueResolutionToLearner($data['learner_email'],$langId,$emailVars);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    private function getResolveForm(bool $isGroupClass = false)
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
        $frm->addSelectBox(Label::getLabel('LBL_TAKE_ACTION'), 'reislo_action', $options)
                ->requirements()->setRequired(true);
        $frm->addTextArea(Label::getLabel('LBL_YOUR_COMMENT'), 'reislo_comment', '')
                ->requirements()->setRequired(true);
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Submit'));
        return $frm;
    }

    public function esclateForm($issueId)
    {
        $issue = ReportedIssue::getIssueById($issueId);
        $userId = UserAuthentication::getLoggedUserId();
        if (
                empty($issue) || $userId != $issue['sldetail_learner_id'] ||
                $issue['repiss_status'] != ReportedIssue::STATUS_RESOLVED
        ) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $logs = ReportedIssue::getIssueLogsById($issue['repiss_id']);
        $log = end($logs);
        if ($log['reislo_added_by'] == $userId || $issue['repiss_status'] != ReportedIssue::STATUS_RESOLVED) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $escalateHour = FatApp::getConfig('CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION');
        $escalateDate = strtotime($issue['repiss_updated_on'] . " +" . $escalateHour . " hour");
        if ($escalateDate <= strtotime(date('Y-m-d H:i:s'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_ISSUE_ESCALATION_TIME_HAS_PASSED'));
        }
        $frm = $this->getEsclateForm();
        $frm->fill(['reislo_repiss_id' => $issue['repiss_id']]);
        $this->set('frm', $frm);
        $this->set("issue", $issue);
        $this->set('statusArr', ReportedIssue::getStatusArr());
        $this->_template->render(
                false,
                false
        );
    }

    public function esclateSetup()
    {
        $frm = $this->getEsclateForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $userId = UserAuthentication::getLoggedUserId();
        $issue = ReportedIssue::getIssueById($post['reislo_repiss_id']);
        if (empty($issue) || $userId != $issue['sldetail_learner_id'] || $issue['repiss_status'] != ReportedIssue::STATUS_RESOLVED) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $logs = ReportedIssue::getIssueLogsById($issue['repiss_id']);
        $log = end($logs);
        if ($log['reislo_added_by'] == $userId || $issue['repiss_status'] != ReportedIssue::STATUS_RESOLVED) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $escalateHour = FatApp::getConfig('CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION');
        $escalateDate = strtotime($issue['repiss_updated_on'] . " +" . $escalateHour . " hour");
        if ($escalateDate <= strtotime(date('Y-m-d H:i:s'))) {
            FatUtility::dieJsonError(Label::getLabel('LBL_ISSUE_ESCALATION_TIME_HAS_PASSED'));
        }
        $reportedIssue = new ReportedIssue($post['reislo_repiss_id'], $userId, ReportedIssue::USER_TYPE_LEARNER);
        if (!$reportedIssue->setupIssueAction(ReportedIssue::ACTION_ESCLATE_TO_ADMIN, $post['reislo_comment'], false)) {
            FatUtility::dieJsonError($reportedIssue->getError());
        }

        $AdminTimezone = FatApp::getConfig('CONF_ADMIN_TIMEZONE', FatUtility::VAR_STRING, 'UTC');
        $sysTimeZone = MyDate::getTimeZone();

        $srch = new ScheduledLessonDetailsSearch();
        $srch->joinScheduledLesson();
        $srch->joinLearner();
        $srch->joinLessonLanguage();
        $srch->addCondition('sldetail_id','=',$issue['repiss_sldetail_id']);
        $srch->addMultipleFields(['ul.user_id as learnerId','concat(ul.user_first_name," ",ul.user_last_name) as learner_name','IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name','slesson_date','slesson_start_time','slesson_end_time']);
        $data = FatApp::getDb()->fetch($srch->getResultSet());

        $emailVars = [
            'issue_type'=>$issue['repiss_title'],
            'learner_name'=>$data['learner_name'],
            'tlanguage_name'=>$data['tlanguage_name'],
            'slesson_date'=>MyDate::changeDateTimezone($data['slesson_date'],$sysTimeZone,$AdminTimezone),
            'slesson_start_time'=>MyDate::changeDateTimezone($data['slesson_start_time'],$sysTimeZone,$AdminTimezone),
            'slesson_end_time'=>MyDate::changeDateTimezone($data['slesson_end_time'],$sysTimeZone,$AdminTimezone),
            'resolution_action_teacher'=>ReportedIssue::getActionsArr($log['reislo_action']),
            'learner_comment'=>$post['reislo_comment'],
        ];

        $email = new EmailHandler();
        $email->sendEmailOfIssueResolutionToAdmin(FatApp::getConfig('CONF_SITE_OWNER_EMAIL', FatUtility::VAR_STRING, 'yocoach_admin@dummyid.com'),CommonHelper::getLangId(),$emailVars);
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    private function getEsclateForm()
    {
        $frm = new Form('actionFrm');
        $repissId = $frm->addHiddenField('', 'reislo_repiss_id');
        $repissId->requirements()->setRequired();
        $repissId->requirements()->setIntPositive();
        $frm->addTextArea(Label::getLabel('LBL_YOUR_COMMENT'), 'reislo_comment', '')->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit', Label::getLabel('LBL_Submit'));
        return $frm;
    }
}
