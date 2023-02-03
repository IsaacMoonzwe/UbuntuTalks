<?php

class LearnerTeachersController extends LearnerBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $frmSrch = $this->getSearchForm();
        $this->set('frmSrch', $frmSrch);
        $this->_template->render();
    }

    public function search()
    {

        $page = FatApp::getPostedData('page', FatUtility::VAR_INT, 1);
        $keyword = FatApp::getPostedData('keyword', FatUtility::VAR_STRING, '');
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);
        $srch = new SearchBase(Order::DB_TBL, 'orders');
        $srch->joinTable(OrderProduct::DB_TBL, 'INNER JOIN', 'orders.order_id = op.op_order_id', 'op');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'op.op_teacher_id = teacher.user_id', 'teacher');
        $srch->joinTable(TeacherStat::DB_TBL, 'INNER JOIN', 'testat.testat_user_id = teacher.user_id', 'testat');
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        $srch->addMultipleFields([
            'teacher.user_id', 'user_url_name', 'testat_ratings', 'user_country_id',
            'CONCAT(teacher.user_first_name," ", teacher.user_last_name) as teacherFullName'
        ]);
        $srch->addCondition('order_user_id', '=', $this->loggedUserId);
        $srch->addCondition('order_type', '=', Order::TYPE_LESSON_BOOKING);
        if (!empty($keyword)) {
            $cnd = $srch->addCondition('teacher.user_first_name', 'like', '%' . $keyword . '%');
            $cnd->attachCondition('teacher.user_last_name', 'like', '%' . $keyword . '%');
        }
        $srch->addGroupBy('teacher.user_id');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $teachers = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($teachers)) {
            $teacherIds = array_column($teachers, 'user_id', 'user_id');
            $countryIds = array_column($teachers, 'user_country_id');
            $lessonCounts =    $this->getLessonCount($teacherIds);
            $teachLangs =    $this->getTeachLangs($teacherIds);
            $offerPrice =    $this->getOfferPrice($teacherIds);
            $countries = TeacherSearch::getCountryNames($this->siteLangId, $countryIds);
            foreach ($teachers as &$value) {
                $value['scheduledCount'] = $lessonCounts[$value['user_id']]['scheduledCount'] ?? 0;
                $value['unScheduledCount'] = $lessonCounts[$value['user_id']]['unScheduledCount'] ?? 0;
                $value['pastCount'] = $lessonCounts[$value['user_id']]['pastCount'] ?? 0;
                $value['offerPrice'] = $offerPrice[$value['user_id']] ?? [];
                $value['teacherTeachLanguageName'] = $teachLangs[$value['user_id']] ?? '';
                $value['user_country_name'] = $countries[$value['user_country_id']] ?? '';
            }
        }
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount()
        ];
        $this->set('teachers', $teachers);
        $this->set('postedData', FatApp::getPostedData());
        $this->set('pagingArr', $pagingArr);
        $this->_template->render(false, false);
    }

    private function getLessonCount(array $teacherIds) :array
    {
        if (empty($teacherIds)) {
            return [];
        }

        $srch = new SearchBase(ScheduledLessonDetails::DB_TBL, 'sldetail');
        $srch->doNotCalculateRecords();
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_slesson_id = slesson.slesson_id', 'slesson');
        $srch->addCondition('slesson_teacher_id', 'IN', $teacherIds);
        $srch->addCondition('sldetail_learner_id', '=', $this->loggedUserId);

        $srch->addMultipleFields([
            'slesson_teacher_id',
            'COUNT(IF(slesson.slesson_status = "' . ScheduledLesson::STATUS_SCHEDULED . '", 1, null)) as scheduledCount',
            'COUNT(IF(slesson.slesson_status = "' . ScheduledLesson::STATUS_NEED_SCHEDULING . '",1,null)) as unScheduledCount',
            'COUNT(IF(CONCAT(slesson_end_date, " ", slesson_end_time) < "' . date('Y-m-d H:i:s') . '" AND slesson.slesson_status != ' . ScheduledLesson::STATUS_CANCELLED . ' AND slesson.slesson_date != "0000-00-00", 1, null)) as pastCount',
        ]);
        $srch->addGroupBy('slesson_teacher_id');
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'slesson_teacher_id');
    }

    private function getOfferPrice(array $teacherIds) :array
    {
        if (empty($teacherIds)) {
            return [];
        }
        $srch = new SearchBase(TeacherOfferPrice::DB_TBL, 'top');
        $srch->doNotCalculateRecords();
        $srch->addCondition('top_learner_id', '=', $this->loggedUserId);
        $srch->addCondition('top_teacher_id', 'IN', $teacherIds);
        $srch->addCondition('top_percentage', '>', 0);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $offerData = [];
        while ($row = $db->fetch($rs)) {
            $offerData[$row['top_teacher_id']][$row['top_lesson_duration']] = $row['top_percentage'];
        }
        return $offerData;
    }

    private function getTeachLangs(array $teacherIds): array
    {
        if (empty($teacherIds)) {
            return [];
        }
        $srch = new SearchBase(UserTeachLanguage::DB_TBL, 'utl');
        $srch->joinTable(TeachingLanguage::DB_TBL, 'INNER JOIN', 'tlanguage.tlanguage_id = utl.utl_tlanguage_id', 'tlanguage');
        $srch->joinTable(TeachingLanguage::DB_TBL_LANG, 'LEFT JOIN', 'tlanguageLang.tlanguagelang_tlanguage_id = tlanguage.tlanguage_id and tlanguageLang.tlanguagelang_lang_id = ' . $this->siteLangId, 'tlanguageLang');
        $srch->joinTable(TeachLangPrice::DB_TBL, 'INNER JOIN', 'utl_prc.ustelgpr_utl_id = utl.utl_id', 'utl_prc');
        $srch->addMultipleFields(['utl.utl_user_id', 'GROUP_CONCAT(DISTINCT IFNULL(tlanguageLang.tlanguage_name, tlanguage_identifier)) as tlanguage_name']);
        $srch->addCondition('utl.utl_user_id', 'IN', $teacherIds);
        $srch->addGroupBy('utl.utl_user_id');
        $srch->doNotCalculateRecords();
        $result = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($result);
    }

    public function getMessageToTeacherFrm()
    {
        $frm = new Form('messageToLearnerFrm');
        $fld = $frm->addTextArea(Label::getLabel('LBL_Comment'), 'msg_to_teacher', '', ['style' => 'width:300px;']);
        $fld->requirement->setRequired(true);
        $frm->addSubmitButton('', 'submit', 'Send');
        return $frm;
    }
}
