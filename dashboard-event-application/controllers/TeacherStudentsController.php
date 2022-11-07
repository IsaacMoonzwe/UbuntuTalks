<?php

class TeacherStudentsController extends TeacherBaseController
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
        $frmSrch = $this->getSearchForm();
        if (!$post = $frmSrch->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieWithError($frmSrch->getValidationErrors());
        }
        $page = $post['page'];
        $pageSize = FatApp::getConfig('CONF_FRONTEND_PAGESIZE', FatUtility::VAR_INT, 10);

        $srch = new SearchBase(OrderProduct::DB_TBL, 'op');
        $srch->joinTable(Order::DB_TBL, 'INNER JOIN', 'orders.order_id = op.op_order_id', 'orders');
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', 'orders.order_user_id = learner.user_id', 'learner');
        $srch->addMultipleFields(['learner.user_id', 'CONCAT(learner.user_first_name," ", learner.user_last_name) as learnerFullName']);
        $srch->addCondition('op_teacher_id', '=', $this->loggedUserId);
        $srch->addCondition('order_type', '=', Order::TYPE_LESSON_BOOKING);
        $srch->addCondition('order_is_paid', '=', Order::ORDER_IS_PAID);
        if (!empty($post['keyword'])) {
            $cnd = $srch->addCondition('learner.user_first_name', 'like', '%' . $post['keyword'] . '%');
            $cnd->attachCondition('learner.user_last_name', 'like', '%' . $post['keyword'] . '%');
        }
        if (!empty($post['class_type'])) {
            $operator = ($post['class_type'] == applicationConstants::CLASS_TYPE_GROUP) ? '>' : '=';
            $srch->addCondition('op.op_grpcls_id', $operator, 0);
        }
        $srch->addGroupBy('learner.user_id');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);
        $learners = FatApp::getDb()->fetchAll($srch->getResultSet());
        if (!empty($learners)) {
            $learnerIds = array_column($learners, 'user_id', 'user_id');
            $lessonCounts =  $this->getLessonCount($learnerIds, $post['class_type']);
            $offerPrice =  $this->getOfferPrice($learnerIds);
            foreach ($learners as &$value) {
                $value['scheduledCount'] = $lessonCounts[$value['user_id']]['scheduledCount'] ?? 0;
                $value['unScheduledCount'] = $lessonCounts[$value['user_id']]['unScheduledCount'] ?? 0;
                $value['pastCount'] = $lessonCounts[$value['user_id']]['pastCount'] ?? 0;
                $value['offerPrice'] = $offerPrice[$value['user_id']] ?? [];
            }
        }
        $pagingArr = [
            'pageCount' => $srch->pages(),
            'page' => $page,
            'pageSize' => $pageSize,
            'recordCount' => $srch->recordCount()
        ];
        $this->set('students', $learners);
        $this->set('postedData', $post);
        $this->set('pagingArr', $pagingArr);
        $this->_template->render(false, false);
    }

    private function getLessonCount($learnerIds, string $classType = ''): array
    {
        if (empty($learnerIds)) {
            return [];
        }
        $srch = new SearchBase(ScheduledLessonDetails::DB_TBL, 'sldetail');
        $srch->doNotCalculateRecords();
        $srch->joinTable(ScheduledLesson::DB_TBL, 'INNER JOIN', 'sldetail.sldetail_slesson_id = slesson.slesson_id', 'slesson');
        $srch->addCondition('sldetail_learner_id', 'IN', $learnerIds);
        $srch->addCondition('slesson.slesson_teacher_id', '=', $this->loggedUserId);
        if (!empty($classType)) {
            $operator = ($classType == applicationConstants::CLASS_TYPE_GROUP) ? '>' : '=';
            $srch->addCondition('slesson.slesson_grpcls_id', $operator, 0);
        }
        $srch->addMultipleFields([
            'sldetail_learner_id',
            'COUNT(IF(slesson.slesson_status = "' . ScheduledLesson::STATUS_SCHEDULED . '", 1, null)) as scheduledCount',
            'COUNT(IF(slesson.slesson_status = "' . ScheduledLesson::STATUS_NEED_SCHEDULING . '",1,null)) as unScheduledCount',
            'COUNT(IF(CONCAT(slesson_end_date, " ", slesson_end_time) < "' . date('Y-m-d H:i:s') . '" AND slesson.slesson_status != ' . ScheduledLesson::STATUS_CANCELLED . ' AND slesson.slesson_date != "0000-00-00", 1, null)) as pastCount',
        ]);
        $srch->addGroupBy('sldetail_learner_id');
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'sldetail_learner_id');
    }


    private function getOfferPrice($learnerIds): array
    {
        if (empty($learnerIds)) {
            return [];
        }
        $srch = new SearchBase(TeacherOfferPrice::DB_TBL, 'top');
        $srch->doNotCalculateRecords();
        $srch->addCondition('top_learner_id', 'IN', $learnerIds);
        $srch->addCondition('top_teacher_id', '=', $this->loggedUserId);
        $srch->addCondition('top_percentage', '>', 0);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $offerData = [];
        while ($row = $db->fetch($rs)) {
            $offerData[$row['top_learner_id']][$row['top_lesson_duration']] = $row['top_percentage'];
        }
        return $offerData;
    }

    public function offerForm()
    {
        $teacherId = UserAuthentication::getLoggedUserId();
        $learnerId = FatApp::getPostedData('top_learner_id', FatUtility::VAR_INT, 0);
        $teacherOffer = new TeacherOfferPrice();
        $offerData = $teacherOffer->getTeacherOffer($learnerId, $teacherId);
        $teacherOfferData = ['top_learner_id' => $learnerId];
        $isOfferSet = (!empty($offerData));
        foreach ($offerData as $offer) {
            $teacherOfferData['top_percentage'][$offer['top_lesson_duration']] = $offer['top_percentage'];
        }
        $teachLangPrice = new TeachLangPrice();
        $userSlots = $teachLangPrice->getTeachingSlots($teacherId);
        $frm = $this->getOfferForm($userSlots);
        $frm->fill($teacherOfferData);
        $this->set('frm', $frm);
        $this->set('userSlots', $userSlots);
        $this->set('isOfferSet', $isOfferSet);
        $this->set('user_info', User::getAttributesById($learnerId, ['user_id', 'user_first_name', 'user_last_name']));
        $this->_template->render(false, false);
    }

    public function setUpOffer()
    {
        $frmSrch = $this->getOfferForm();
        $post = $frmSrch->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError($frmSrch->getValidationErrors());
        }
        $teacherId = UserAuthentication::getLoggedUserId();
        $learnerId = $post['top_learner_id'];
        foreach ($post['top_percentage'] as $lesonDuration => $offer) {
            $teacherOfferPrice = new TeacherOfferPrice($teacherId, $learnerId);
            if (!$teacherOfferPrice->saveOffer($offer, $lesonDuration)) {
                FatUtility::dieJsonError($teacherOfferPrice->getError());
            }
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Price_Locked_Successfully!'));
    }

    private function getOfferForm(array $userSlots = null)
    {
        if ($userSlots == null) {
            $teacherId = UserAuthentication::getLoggedUserId();
            $teachLangPrice = new TeachLangPrice();
            $userSlots = $teachLangPrice->getTeachingSlots($teacherId);
        }
        $frm = new Form('frmOfferPrice');
        foreach ($userSlots as $slot) {
            $label = str_replace('{slot}', $slot, Label::getLabel('LBL_{slot}_slot_Offer(%)'));
            $fld = $frm->addRequiredField($label, 'top_percentage[' . $slot . ']');
            $fld->requirements()->setFloatPositive();
            $fld->requirements()->setRange(1, 100);
        }
        $fld = $frm->addHiddenField('', 'top_learner_id');
        $fld->requirements()->setInt();
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

    public function unlockOffer()
    {
        $learnerId = FatApp::getPostedData('learnerId', FatUtility::VAR_INT, 0);
        $teacherId = UserAuthentication::getLoggedUserId();
        if ($learnerId < 1) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Invalid_Request'));
        }
        $teacherOfferPrice = new TeacherOfferPrice($teacherId, $learnerId);
        if (!$teacherOfferPrice->removeOffer()) {
            FatUtility::dieJsonError($teacherOfferPrice->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Price_Unlocked_Successfully!'));
    }

    public function getMessageToLearnerFrm()
    {
        $frm = new Form('messageToLearnerFrm');
        $fld = $frm->addTextArea('Comment', 'msg_to_learner', '', ['style' => 'width:300px;']);
        $fld->requirement->setRequired(true);
        $frm->addSubmitButton('', 'submit', 'Send');
        return $frm;
    }

    public function sendMessageToLearner($learnerId = 0)
    {
        $learnerId = FatUtility::int($learnerId);
        $frm = $this->getMessageToLearnerFrm();
        $frm->addHiddenField('', 'slesson_learner_id', $learnerId);
        $this->set('frm', $frm);
        $this->_template->render(false, false);
    }

    public function messageToLearnerSetup()
    {
        $db = FatApp::getDb();
        $post = FatApp::getPostedData();
        if (empty($post)) {
            FatUtility::dieWithError(Label::getLabel('LBL_Invalid_Request'));
        }
        $learnerId = $post['slesson_learner_id'];
        $teacherData = User::getAttributesById(UserAuthentication::getLoggedUserId(), ['user_first_name', 'user_last_name']);
        $learnerData = User::getAttributesById($learnerId, ['user_first_name', 'user_last_name']);
        $userSrch = User::getSearchObject(true);
        $userSrch->addMultipleFields(['credential_email']);
        $userSrch->addCondition('credential_user_id', '=', $learnerId);
        $userRs = $userSrch->getResultSet();
        $userData = $db->fetch($userRs);
        $tpl = 'teacher_message_to_learner_email';
        $vars = [
            '{learner_name}' => $learnerData['user_first_name'] . " " . $learnerData['user_last_name'],
            '{teacher_name}' => $teacherData['user_first_name'] . " " . $teacherData['user_last_name'],
            '{teacher_message}' => $post['msg_to_learner'],
            '{action}' => 'Message To Learner',
        ];
        if (!EmailHandler::sendMailTpl($userData['credential_email'], $tpl, $this->siteLangId, $vars)) {
            FatUtility::dieJsonError(Label::getLabel('LBL_Mail_not_sent!'));
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_Message_Sent_Successfully!'));
    }

    protected function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $classType = applicationConstants::getClassTypes($this->siteLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Class_Type'), 'class_type', $classType, '', [], Label::getLabel('LBL_Group/one_to_one_class'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        return $frm;
    }
}
