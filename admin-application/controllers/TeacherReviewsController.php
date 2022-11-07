<?php

class TeacherReviewsController extends AdminBaseController
{

    private $canView;
    private $canEdit;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewTeacherReviews($this->admin_id, true);
        $this->canEdit = $this->objPrivilege->canEditTeacherReviews($this->admin_id, true);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index($sellerId = 0)
    {
        $sellerId = FatUtility::int($sellerId);
        $this->objPrivilege->canViewTeacherReviews();
        $srchFrm = $this->getSearchForm();
        $srchFrm->fill(['seller_id' => $sellerId]);
        $this->set("frmSearch", $srchFrm);
        $this->_template->render();
    }

    public function search()
    {
        $this->objPrivilege->canViewTeacherReviews();
        $pagesize = FatApp::getConfig('CONF_ADMIN_PAGESIZE', FatUtility::VAR_INT, 10);
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $page = (empty($data['page']) || $data['page'] <= 0) ? 1 : $data['page'];
        $post = $searchForm->getFormDataFromArray($data);
        $page = (empty($page) || $page <= 0) ? 1 : $page;
        $page = FatUtility::int($page);
        $srch = new TeacherLessonReviewSearch($this->adminLangId);
        $srch->joinLearner();
        $srch->joinTeacher($this->adminLangId);
        $srch->joinTeacherLessonRating();
        $srch->joinScheduledLesson();
        $srch->joinScheduleLessonDetails();
        $srch->addMultipleFields([
            'tlreview_lesson_id',
            'ul.user_id as learner_user_id', 'ul.user_first_name as learner_name', 'ul.user_phone as learner_phone', 'uc.credential_email as learner_email_id',
            'ut.user_id as teacher_user_id', 'ut.user_first_name as teacher_name', 'ut.user_phone as teacher_phone', 'usc.credential_email as teacher_email_id',
            'tlreview_id', 'tlreview_posted_on', 'tlreview_status', 'tlrating_rating', 'sld.sldetail_order_id as tlreview_order_id'
        ]);
        $srch->addOrder('tlreview_posted_on', 'DESC');
        $srch->addGroupBy('tlreview_id');
        if (!empty($post['tlreview_order_id'])) {
            $srch->addCondition('sld.sldetail_order_id', '=', $post['tlreview_order_id']);
        }
        if ($post['reviewed_by_id'] > 0) {
            $srch->addCondition('tlreview_postedby_user_id', '=', $post['reviewed_by_id']);
        }
        if ($post['teacher_id'] > 0) {
            $srch->addCondition('tlreview_teacher_user_id', '=', $post['teacher_id']);
        }
        $tlreview_status = FatApp::getPostedData('tlreview_status', FatUtility::VAR_INT, -1);
        if ($tlreview_status > -1) {
            $srch->addCondition('tlreview_status', '=', $tlreview_status);
        }
        $date_from = FatApp::getPostedData('date_from', FatUtility::VAR_DATE, '');
        if (!empty($date_from)) {
            $srch->addCondition('tlreview_posted_on', '>=', $date_from . ' 00:00:00');
        }
        $date_to = FatApp::getPostedData('date_to', FatUtility::VAR_DATE, '');
        if (!empty($date_to)) {
            $srch->addCondition('tlreview_posted_on', '<=', $date_to . ' 23:59:59');
        }
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        if ($records) {
            foreach ($records as $k => $record) {
                $avgRatingSrch = TeacherLessonRating::getSearchObj();
                $avgRatingSrch->addCondition('tlrating_tlreview_id', '=', $record['tlreview_id']);
                $avgRatingSrch->addMultipleFields(['AVG(tlrating_rating) as average_rating']);
                $avgRatingSrch->doNotCalculateRecords();
                $avgRatingSrch->doNotLimitRecords();
                $avgRatingRs = $avgRatingSrch->getResultSet();
                $avgRatingData = FatApp::getDb()->fetch($avgRatingRs);
                $records[$k]['average_rating'] = $avgRatingData['average_rating'];
            }
        }
        $this->set("arr_listing", $records);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('postedData', $post);
        $this->set('reviewStatus', TeacherLessonReview::getReviewStatusArr($this->adminLangId));
        $this->_template->render(false, false);
    }

    public function view($tlreview_id = 0)
    {
        $tlreview_id = FatUtility::int($tlreview_id);
        if (1 > $tlreview_id) {
            FatUtility::dieWithError($this->str_invalid_request);
        }
        $srch = new TeacherLessonReviewSearch($this->adminLangId);
        $srch->joinLearner();
        $srch->joinScheduledLesson();
        $srch->joinScheduleLessonDetails();
        $srch->addMultipleFields([
            'sld.sldetail_order_id as tlreview_order_id',
            'ul.user_first_name as reviewed_by', 'tlreview_id', 'tlreview_posted_on',
            'tlreview_status', 'tlreview_title', 'tlreview_description'
        ]);
        $srch->addOrder('tlreview_posted_on', 'DESC');
        $srch->addCondition('tlreview_id', '=', $tlreview_id);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        $avgRatingSrch = TeacherLessonRating::getSearchObj();
        $avgRatingSrch->addCondition('tlrating_tlreview_id', '=', $tlreview_id);
        $avgRatingSrch->addMultipleFields(['AVG(tlrating_rating) as average_rating']);
        $avgRatingSrch->doNotCalculateRecords();
        $avgRatingSrch->doNotLimitRecords();
        $avgRatingRs = $avgRatingSrch->getResultSet();
        $avgRatingData = FatApp::getDb()->fetch($avgRatingRs);
        $ratingSrch = TeacherLessonRating::getSearchObj();
        $ratingSrch->addCondition('tlrating_tlreview_id', '=', $tlreview_id);
        $ratingSrch->addMultipleFields(['tlrating_tlreview_id', 'tlrating_rating_type', 'tlrating_rating']);
        $ratingSrch->doNotCalculateRecords();
        $ratingSrch->doNotLimitRecords();
        $ratingRs = $ratingSrch->getResultSet();
        $ratingData = FatApp::getDb()->fetchAll($ratingRs);
        $frm = $this->reviewRequestForm();
        $frm->fill($records);
        $this->set("data", $records);
        $this->set("ratingData", $ratingData);
        $this->set("avgRatingData", $avgRatingData);
        $this->set("ratingTypeArr", TeacherLessonRating::getRatingAspectsArr($this->adminLangId));
        $this->set("frm", $frm);
        $this->_template->render(false, false);
    }

    public function updateStatus($tlreview_id = 0)
    {
        $tlreview_id = FatApp::getPostedData('tlreview_id', FatUtility::VAR_INT, 0);
        $status = FatApp::getPostedData('tlreview_status', FatUtility::VAR_INT, 0);
        if (1 > $tlreview_id) {
            Message::addErrorMessage($this->str_invalid_request_id);
            FatUtility::dieJsonError(Message::getHtml());
        }
        $data = TeacherLessonReview::getAttributesById($tlreview_id, [
                    'tlreview_id', 'tlreview_status', 'tlreview_teacher_user_id',
                    'tlreview_lang_id', 'tlreview_postedby_user_id'
        ]);
        if (false == $data) {
            Message::addErrorMessage($this->str_invalid_request);
            FatUtility::dieWithError(Message::getHtml());
        }
        $assignValues = ['tlreview_status' => $status];
        $record = new TeacherLessonReview($tlreview_id);
        $record->assignValues($assignValues);
        if (!$record->save()) {
            Message::addErrorMessage($record->getError());
            FatUtility::dieJsonError(Message::getHtml());
        }
        /* Upudate Ratings and Reviews counts */
        $teacherId = FatUtility::int($data['tlreview_teacher_user_id']);
        (new TeacherStat($teacherId))->setRatingReviewCount();
        $this->set('msg', 'Updated Successfully.');
        $this->set('tlreviewId', $tlreview_id);
        $this->_template->render(false, false, 'json-success.php');
    }

    private function getSearchForm()
    {
        $frm = new Form('frmSearch');
        $frm->addHiddenField('', 'reviewed_by_id');
        $frm->addHiddenField('', 'teacher_id', 0);
        $frm->addTextBox('Order Id', 'tlreview_order_id');
        $frm->addTextBox('Reviewed To', 'reviewed_to');
        $frm->addTextBox('Reviewed By', 'reviewed_by');
        $statusArr = TeacherLessonReview::getReviewStatusArr($this->adminLangId);
        $frm->addSelectBox('Status', 'tlreview_status', [-1 => 'Does not Matter'] + $statusArr, '', [], '');
        $frm->addDateField('Date From', 'date_from', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
        $frm->addDateField('Date To', 'date_to', '', ['readonly' => 'readonly', 'class' => 'small dateTimeFld field--calender']);
        $fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
        $fld_cancel = $frm->addButton("", "btn_clear", "Clear Search", ['onclick' => 'clearSearch();']);
        $fld_submit->attachField($fld_cancel);
        return $frm;
    }

    private function reviewRequestForm()
    {
        $frm = new Form('reviewRequestForm');
        $statusArr = TeacherLessonReview::getReviewStatusArr($this->adminLangId);
        $frm->addSelectBox('Status', 'tlreview_status', $statusArr, '')->requirements()->setRequired();
        $frm->addHiddenField('', 'tlreview_id', 0);
        $frm->addSubmitButton('', 'btn_submit', 'Update');
        return $frm;
    }

}
