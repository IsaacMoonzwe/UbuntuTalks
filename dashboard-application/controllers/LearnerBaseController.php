<?php

class LearnerBaseController extends LoggedUserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        if (true !== User::isLearner()) {
            if (true == User::isTeacher()) {
                FatApp::redirectUser(CommonHelper::generateUrl('Teacher'));
            }
            FatApp::redirectUser(CommonHelper::generateUrl('Account'));
        }
        User::setDashboardActiveTab(User::USER_LEARNER_DASHBOARD);
    }

    protected function getSearchForm()
    {
        $frm = new Form('frmSrch');
        $frm->addTextBox(Label::getLabel('LBL_Search_By_Keyword'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Search_By_Keyword')]);
        $frm->addSelectBox(Label::getLabel('LBL_Status'), 'status', ScheduledLesson::getStatusArr() + [ScheduledLesson::STATUS_ISSUE_REPORTED => Label::getLabel('LBL_Issue_Reported')], '', [], Label::getLabel('LBL_All'))->requirements()->setInt();
        $fld = $frm->addHiddenField('', 'page', 1);
        $fld->requirements()->setIntPositive();
        $classType = applicationConstants::getClassTypes($this->siteLangId);
        $frm->addSelectBox(Label::getLabel('LBL_Class_Type'), 'class_type', $classType, '', [], Label::getLabel('LBL_Group/one_to_one_class'));
        $btnSubmit = $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $btnReset = $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Reset'));
        return $frm;
    }

}
