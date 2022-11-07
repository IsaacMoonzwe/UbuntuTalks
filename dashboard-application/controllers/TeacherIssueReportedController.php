<?php

class TeacherIssueReportedController extends TeacherBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index($gclassId = 0)
    {
        $this->_template->addJs('js/teacherLessonCommon.js');
        $frmSrch = $this->getSearchForm();
        $frmSrch->fill(['grpcls_id' => $gclassId]);
        $this->set('frmSrch', $frmSrch);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->_template->render();
    }

    public function search()
    {
        $frmSrch = $this->getSearchForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $sortOrder = '';
        $page = $post['page'] ?? 1;
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE');
        $userId = UserAuthentication::getLoggedUserId();
        $post = array_merge($post, ['slesson_teacher_id' => $userId,
            'status' => ScheduledLesson::STATUS_ISSUE_REPORTED]);
        $srch = new LessonSearch($this->siteLangId);
        $srch->addSearchListingFields();
        $srch->applySearchConditions($post);
        $srch->applyOrderBy($sortOrder);
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $lessons = $srch->fetchAll();
        $totalRecords = $srch->recordCount();
        $pagingArr = [
            'page' => $page,
            'pageSize' => $pageSize,
            'pageCount' => $srch->pages(),
            'recordCount' => $totalRecords,
        ];
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $startRecord = ($page - 1) * $pageSize + 1;
        $endRecord = $page * $pageSize;
        if ($totalRecords < $endRecord) {
            $endRecord = $totalRecords;
        }
        $this->set('totalRecords', $totalRecords);
        $this->set('startRecord', $startRecord);
        $this->set('endRecord', $endRecord);
        $this->set('lessons', $lessons);
        $this->set('statusArr', ScheduledLesson::getStatusArr());
        $this->set('referer', CommonHelper::redirectUserReferer(true));
        $this->set('teachLanguages', TeachingLanguage::getAllLangs($this->siteLangId));
        $this->_template->render(false, false);
    }

}
