<?php

class LessonStatsController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewLessonStatsReport($this->admin_id);
    }

    public function index()
    {
        $frmSearch = $this->getSearchForm($this->adminLangId);
        $this->set('frmSearch', $frmSearch);
        $this->_template->render();
    }

    public function search()
    {
        $db = FatApp::getDb();
        $srchFrm = $this->getSearchForm($this->adminLangId);
        $post = $srchFrm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($srchFrm->getValidationErrors());
        }
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $page = max(1, $post['page']);
        $srchLessons = new ScheduledLessonSearch();
        $srchLessons->doNotLimitRecords();
        $srchLessonStats = new LessonStatsSearch();
        $srchLessonStats->addMultipleFields([
            'lesstslog_updated_by_user_id',
            'lesstslog_current_status',
            'COUNT(CASE
                WHEN lesstslog_current_status IN (' . ScheduledLesson::STATUS_SCHEDULED . ' , ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ') THEN 1
                ELSE NULL
            END) num_resched',
            'COUNT(CASE
                WHEN lesstslog_current_status = ' . ScheduledLesson::STATUS_CANCELLED . ' THEN 1
                ELSE NULL
            END) num_canceled'
        ]);
        $srchLessonStats->addGroupBy('lesstslog_updated_by_user_id');
        $srchLessonStats->addGroupBy('lesstslog_current_status');
        $srch = new UserSearch(false);
        $srch->joinCredentials();
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($page);
        if (!empty($post['reschedule_from'])) {
            $srchLessonStats->addCondition('lesstslog_added_on', '>=', $post['reschedule_from'] . ' 00:00:00');
        }
        if (!empty($post['reschedule_to'])) {
            $srchLessonStats->addCondition('lesstslog_added_on', '<=', $post['reschedule_to'] . ' 23:59:59');
        }
        $srch->joinTable('(' . $srchLessonStats->getQuery() . ')', 'INNER JOIN', 'lesstslog_updated_by_user_id = user_id', 'lsl');
        $srch->addMultipleFields(
                [
                    'user_id',
                    'user_is_learner',
                    'user_is_teacher',
                    'CONCAT(user_first_name, " ", user_last_name) as user_full_name',
                    'credential_email',
                    'SUM(CASE WHEN lesstslog_current_status = ' . ScheduledLesson::STATUS_CANCELLED . ' THEN num_canceled ELSE 0 END) cancelledLessons',
                    'SUM(CASE WHEN lesstslog_current_status IN (' . ScheduledLesson::STATUS_SCHEDULED . ' , ' . ScheduledLesson::STATUS_NEED_SCHEDULING . ') THEN num_resched ELSE 0 END) rescheduledLessons'
                ]
        );
        if ($post['user_id'] > 0) {
            $srch->addCondition('user_id', '=', $post['user_id']);
        }
        $srch->addGroupBy('user_id');
        $srch->addOrder('user_full_name');
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $teachersList = $db->fetchAll($rs);
        $this->set("arr_listing", $teachersList);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->_template->render(false, false);
    }

    private function getSearchForm($langId)
    {
        $frm = new Form('frmRescheduledReportSearch');
        $frm->addTextBox(Label::getLabel('LBL_User', $langId), 'user', '', ['id' => 'user', 'autocomplete' => 'off']);
        $frm->addDateField(Label::getLabel('LBL_Date_From', $langId), 'reschedule_from', '', ['readonly' => 'readonly']);
        $frm->addDateField(Label::getLabel('LBL_Date_To', $langId), 'reschedule_to', '', ['readonly' => 'readonly']);
        $frm->addHiddenField('', 'page', 1)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'user_id', '');
        $frm->addHiddenField('', 'report_type', '');
        $frm->addHiddenField('', 'report_user_id', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search', $langId));
        $frm->addButton("", "btn_clear", Label::getLabel('LBL_Clear_Search', $langId));
        return $frm;
    }

    public function viewReport()
    {
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm($this->adminLangId);
        $reportName = '';
        $post = $searchForm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($searchForm->getValidationErrors());
        }
        $db = FatApp::getDb();
        $lessonStatsSearch = new LessonStatsSearch();
        $lessonStatsSearch->joinDetails();
        if ($post['report_user_id'] > 0) {
            $lessonStatsSearch->addCondition('lesstslog_updated_by_user_id', '=', $post['report_user_id']);
        }
        if (!empty($post['reschedule_from'])) {
            $lessonStatsSearch->addCondition('lesstslog_added_on', '>=', $post['reschedule_from'] . ' 00:00:00');
        }
        if (!empty($post['reschedule_to'])) {
            $lessonStatsSearch->addCondition('lesstslog_added_on', '<=', $post['reschedule_to'] . ' 23:59:59');
        }
        if (isset($post['report_type']) && $post['report_type'] == LessonStatusLog::CANCELLED_REPORT) {
            $reportName = Label::getLabel('LBL_CANCELLED_REPORT');
            $lessonStatsSearch->addCondition('lesstslog_current_status', '=', ScheduledLesson::STATUS_CANCELLED);
        }
        if (isset($post['report_type']) && $post['report_type'] == LessonStatusLog::RESCHEDULED_REPORT) {
            $reportName = Label::getLabel('LBL_RESCHEDULED_REPORT');
            $lessonStatsSearch->addCondition('lesstslog_current_status', 'IN', [
                ScheduledLesson::STATUS_NEED_SCHEDULING,
                ScheduledLesson::STATUS_SCHEDULED
            ]);
        }
        $lessonStatsSearch->addMultipleFields([
            'lesstslog_updated_by_user_id',
            'slesson_status',
            'slesson_id',
            'slesson_date',
            'sldetail_order_id',
            'CONCAT(tu.user_first_name, " ", tu.user_last_name) as ExpertName',
            '(CASE
                    WHEN lesstslog_updated_by_user_id = sl.slesson_teacher_id THEN CONCAT(tu.user_first_name, " ", tu.user_last_name)
                    ELSE CONCAT(su.user_first_name, " ", su.user_last_name)
                END ) as RescheduledBy',
            'CONCAT(su.user_first_name, " ", su.user_last_name) as StudentName',
            'lesstslog_current_status',
            'CONCAT(lesstslog_prev_start_date," ",lesstslog_prev_start_time) as StartTime',
            'CONCAT(lesstslog_prev_end_date," ",lesstslog_prev_end_time) as EndTime',
            'lesstslog_comment',
            'lesstslog_added_on',
            'lesstslog_id',
            'tu.user_id tuser_id',
            'su.user_id suser_id',
        ]);
        $page = max(1, $post['page']);
        $lessonStatsSearch->setPageNumber($page);
        $lessonStatsSearch->setPageSize($pagesize);
        $rs = $lessonStatsSearch->getResultSet();
        $lessons = $db->fetchAll($rs);
        $reportNoteText = Label::getLabel('NOTE_LESSONSTATS_REPORT');
        $this->set('lessons', $lessons);
        $this->set('pageCount', $lessonStatsSearch->pages());
        $this->set('recordCount', $lessonStatsSearch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('report_type', $post['report_type']);
        $this->set('report_user_id', $post['report_user_id']);
        $this->set('postedData', $post);
        $this->set('userFullName', User::getAttributesById($post['report_user_id'], 'concat(user_first_name, " " ,user_last_name)'));
        $this->set('reportName', $reportName);
        $this->set('reportNoteText', $reportNoteText);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->_template->render(false, false);
    }

    public function export()
    {
        $searchForm = $this->getSearchForm($this->adminLangId);
        $post = $searchForm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($searchForm->getValidationErrors());
        }
        $lessonStatsSearch = new LessonStatsSearch();
        $lessonStatsSearch->joinDetails();
        $lessonStatsSearch->addMultipleFields([
            'u.user_id,
                slesson_status,
                slesson_id,
                slesson_date,
                sldetail_order_id,
                CONCAT(tu.user_first_name, " ", tu.user_last_name) as ExpertName,
                CONCAT(u.user_first_name, " ", u.user_last_name) as RescheduledBy, 
                CONCAT(su.user_first_name, " ", su.user_last_name) as StudentName,
                lesstslog_current_status,
                CONCAT(lesstslog_prev_start_date," ",lesstslog_prev_start_time) as StartTime,
                CONCAT(lesstslog_prev_end_date," ",lesstslog_prev_end_time) as EndTime,
                lesstslog_comment,
                lesstslog_added_on,
                lesstslog_id
                '
        ]);
        if ($post) {
            if ($post['report_user_id'] > 0) {
                $lessonStatsSearch->addCondition('lesstslog_updated_by_user_id', '=', $post['report_user_id']);
            }
            if (!empty($post['reschedule_from'])) {
                $lessonStatsSearch->addCondition('lesstslog_added_on', '>=', $post['reschedule_from'] . ' 00:00:00');
            }
            if (!empty($post['reschedule_to'])) {
                $lessonStatsSearch->addCondition('lesstslog_added_on', '<=', $post['reschedule_to'] . ' 23:59:59');
            }
        }
        $reportName = '';
        $extraLabelArr = [
            Label::getLabel('LBL_Date_Of_Rescheduled/Cancellation', $this->adminLangId),
            Label::getLabel('LBL_Rescheduled/Cancelled_By', $this->adminLangId),
            Label::getLabel('LBL_Reason_Of_Rescheduled/Cancellation', $this->adminLangId)
        ];
        if (!empty($post['report_type']) && ($post['report_type'] == LessonStatusLog::CANCELLED_REPORT)) {
            $reportName = 'Cancelled_';
            $extraLabelArr = [
                Label::getLabel('LBL_Date_Of_Cancellation', $this->adminLangId),
                Label::getLabel('LBL_Cancelled_By', $this->adminLangId),
                Label::getLabel('LBL_Reason_Of_Cancellation', $this->adminLangId)
            ];
            $lessonStatsSearch->addCondition('lesstslog_current_status', '=', ScheduledLesson::STATUS_CANCELLED);
        }
        if (!empty($post['report_type']) && ($post['report_type'] == LessonStatusLog::RESCHEDULED_REPORT)) {
            $reportName = 'Rescheduled_';
            $extraLabelArr = [
                Label::getLabel('LBL_Date_Of_Rescheduled', $this->adminLangId),
                Label::getLabel('LBL_Rescheduled_By', $this->adminLangId),
                Label::getLabel('LBL_Reason_Of_Reschedule', $this->adminLangId)
            ];
            $lessonStatsSearch->addCondition('lesstslog_current_status', 'IN', [
                ScheduledLesson::STATUS_NEED_SCHEDULING,
                ScheduledLesson::STATUS_SCHEDULED
            ]);
        }
        $lessonStatsSearch->doNotCalculateRecords();
        $lessonStatsSearch->doNotLimitRecords();
        $rs = $lessonStatsSearch->getResultSet();
        $csvColumns = [];
        $arr = [
            Label::getLabel('LBL_Sr_No', $this->adminLangId),
            Label::getLabel('LBL_Expert_Name', $this->adminLangId),
            Label::getLabel('LBL_Student_Name', $this->adminLangId),
            Label::getLabel('LBL_Order_ID', $this->adminLangId),
            Label::getLabel('LBL_Lesson_ID', $this->adminLangId),
            Label::getLabel('LBL_Start_Time', $this->adminLangId),
            Label::getLabel('LBL_End_Time', $this->adminLangId),
            Label::getLabel('LBL_Lesson_Status', $this->adminLangId),
            Label::getLabel('LBL_Action_Performed', $this->adminLangId)
        ];
        $arr = array_merge($arr, $extraLabelArr);
        array_push($csvColumns, $arr);
        $statusArr = ScheduledLesson::getStatusArr();
        $rowKeys = [
            'ExpertName', 'StudentName', 'sldetail_order_id', 'slesson_id',
            'StartTime', 'EndTime', 'slesson_status', 'lesstslog_current_status',
            'lesstslog_added_on', 'RescheduledBy', 'lesstslog_comment'
        ];
        $placeholders = ['slesson_status', 'lesstslog_current_status'];
        CommonHelper::exportCsv($rs, $csvColumns[0], $rowKeys, $statusArr, $placeholders, $reportName . 'Stats_' . date("d-M-Y") . '.csv');
        exit;
    }

}
