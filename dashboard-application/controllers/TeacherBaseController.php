<?php

class TeacherBaseController extends LoggedUserController
{

    protected $teacherProfileProgress;

    public function __construct($action)
    {
        parent::__construct($action);
        if (!User::canAccessTeacherDashboard()) {
            if (FatUtility::isAjaxCall()) {
                Message::addErrorMessage(Label::getLabel('MSG_ERROR_INVALID_ACCESS'));
                FatUtility::dieWithError(Message::getHtml());
            }
            FatApp::redirectUser(CommonHelper::generateUrl('TeacherRequest', 'form', [], CONF_WEBROOT_FRONTEND));
        }
        User::setDashboardActiveTab(User::USER_TEACHER_DASHBOARD);
    }

    protected function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addHiddenField(Label::getLabel('LBL_GROUP_CLASS'), 'grpcls_id');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $options = ScheduledLesson::getStatusArr() + [ScheduledLesson::STATUS_ISSUE_REPORTED => Label::getLabel('LBL_Issue_Reported')];
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'status', $options, '', [], Label::getLabel('LBL_All'))->requirements()->setInt();
        $frm->addHiddenField('', 'listingView', '');
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $classType = applicationConstants::getClassTypes($this->siteLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Class_Type'), 'class_type', $classType, '', [], Label::getLabel('LBL_Group/one_to_one_class'));
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        return $frm;
    }

    protected function reportSearchForm($langId)
    {
        $durationArray = Statistics::getDurationTypesArr($langId);
        $reportType = [Statistics::REPORT_EARNING => Statistics::REPORT_EARNING, Statistics::REPORT_SOLD_LESSONS => Statistics::REPORT_SOLD_LESSONS];
        $frm = new Form('reportSearchForm');
        $field = $frm->addSelectBox(Label::getLabel('LBL_Duration_type'), 'duration_type', $durationArray, Statistics::TYPE_TODAY);
        $field->requirements()->setInt();
        $field->requirements()->setRequired(true);
        // $field = $frm->addSelectBox(Label::getLabel('LBL_Lesson_Sold_Duration'), 'lesson_duration', $durationArray, Statistics::TYPE_TODAY);
        // $field->requirements()->setInt();
        // $field->requirements()->setRequired(true);
        $field = $frm->addSelectBox('', 'report_type[]', $reportType, $reportType, ['multiple' => 'multiple']);
        $field->requirements()->setRequired(true);
        $frm->addHiddenField('', 'forGraph', applicationConstants::NO);
        return $frm;
    }

}
