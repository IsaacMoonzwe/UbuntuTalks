<?php

class LearnerGroupClassesController extends LearnerBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->_template->addJs('js/jquery-confirm.min.js');
    }

    public function index()
    {
        $this->set('frmSrch', $this->getSearchForm());
        $this->_template->addJs('js/learnerLessonCommon.js');
        $this->_template->addJs('js/moment.min.js');
        $this->_template->addJs('js/fullcalendar.min.js');
        $this->_template->addJs(['js/jquery.barrating.min.js']);
        $this->_template->addJs('js/jquery.countdownTimer.min.js');
        $this->_template->render();
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        $referer = CommonHelper::redirectUserReferer(true);
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $srch = ScheduledLessonSearch::getSearchLessonsObj($this->siteLangId);
        $srch->addFld([
            'IFNULL(iss.issrep_status,0) AS issrep_status',
            'IFNULL(iss.issrep_id,0) AS issrep_id',
            'IFNULL(iss.issrep_issues_resolve_type,0) AS issrep_issues_resolve_by'
        ]);
        $srch->addCondition('slesson_grpcls_id', '!=', 0);
        $srch->addCondition('sld.sldetail_learner_id', '=', UserAuthentication::getLoggedUserId());
        if (isset($post) && !empty($post['keyword'])) {
            $keywordsArr = array_unique(array_filter(explode(' ', $post['keyword'])));
            foreach ($keywordsArr as $keyword) {
                $cnd = $srch->addCondition('ut.user_first_name', 'like', '%' . $keyword . '%');
                $cnd->attachCondition('ut.user_last_name', 'like', '%' . $keyword . '%');
            }
        }
        if (isset($post) && !empty($post['status'])) {
            if ($post['status'] == ScheduledLesson::STATUS_ISSUE_REPORTED) {
                $srch->addCondition('issrep_id', '>', 0);
            } elseif ($post['status'] == ScheduledLesson::STATUS_UPCOMING) {
                $srch->addCondition('slns.slesson_date', '>=', date('Y-m-d'));
                $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                $srch->addCondition('sld.sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
                $srch->addDirectCondition('IF(grpcls.grpcls_id>0, grpcls.grpcls_status=' . TeacherGroupClasses::STATUS_ACTIVE . ', 1)');
            } elseif ($post['status'] == ScheduledLesson::STATUS_CANCELLED) {
                $cnd = $srch->addDirectCondition('IF(grpcls.grpcls_id>0, grpcls.grpcls_status=' . TeacherGroupClasses::STATUS_CANCELLED . ', 1)');
                $cnd->attachCondition('sld.sldetail_learner_status', '=', $post['status']);
            } else {
                $srch->addCondition('slns.slesson_status', '=', $post['status']);
                $srch->addCondition('sld.sldetail_learner_status', '=', $post['status']);
                $srch->addDirectCondition('IF(grpcls.grpcls_id>0, grpcls.grpcls_status=' . TeacherGroupClasses::STATUS_ACTIVE . ', 1)');
            }
        }
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        if (isset(FatApp::getPostedData()['dashboard'])) {
            $srch->addCondition('slesson_status', ' != ', ScheduledLesson::STATUS_NEED_SCHEDULING);
        }
        /* called from My-teacher detail Page. [ */
        if (isset(FatApp::getPostedData()['teacherId'])) {
            $teacherId = FatApp::getPostedData('teacherId', FatUtility::VAR_INT, 0);
            if ($teacherId > 0) {
                $srch->addCondition('slns.slesson_teacher_id', ' = ', FatApp::getPostedData()['teacherId']);
            }
        }
        /* ] */
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        $lessonArr = [];
        $user_timezone = MyDate::getUserTimeZone();
        foreach ($lessons as $lesson) {
            $key = $lesson['slesson_date'];
            if ($lesson['slesson_date'] != '0000-00-00') {
                $key = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lesson['slesson_date'] . ' ' . $lesson['slesson_start_time'], true, $user_timezone);
            }
            $lessonArr[$key][] = $lesson;
        }
        /* [ */
        $lessonPackages = LessonPackage::getPackagesWithoutTrial($this->siteLangId);
        $this->set('lessonPackages', $lessonPackages);
        /* ] */
        /* [ */
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('totalRecords', $totalRecords);
        /* ] */
        $teachLanguages = TeachingLanguage::getAllLangs($this->siteLangId);
        $this->set('teachLanguages', $teachLanguages);
        $this->set('referer', $referer);
        $this->set('lessonArr', $lessonArr);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('grpStatusesAr', TeacherGroupClasses::getStatusArr($this->siteLangId));
        $this->_template->render(false, false);
    }

    protected function getSearchForm()
    {
        $frm = parent::getSearchForm();
        $frm = new Form('frmSrch');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        return $frm;
    }

}
