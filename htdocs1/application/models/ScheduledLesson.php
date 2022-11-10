<?php

class ScheduledLesson extends MyAppModel
{

    const DB_TBL = 'tbl_scheduled_lessons';
    const DB_TBL_PREFIX = 'slesson_';
    const STATUS_SCHEDULED = 1;
    const STATUS_NEED_SCHEDULING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_UPCOMING = 6;
    const STATUS_ISSUE_REPORTED = 7;
    const STATUS_RESCHEDULED = 8;
	const KIDS_CLASS = 1;
	const DEFAULT_KIDS_CLASS=0;

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getStatusArr()
    {
        return [
            static::STATUS_UPCOMING => Label::getLabel('LBL_Upcoming'),
            static::STATUS_SCHEDULED => Label::getLabel('LBL_Scheduled'),
            static::STATUS_RESCHEDULED => Label::getLabel('LBL_Rescheduled'),
            static::STATUS_NEED_SCHEDULING => Label::getLabel('LBL_Need_to_be_scheduled'),
            static::STATUS_COMPLETED => Label::getLabel('LBL_Completed'),
            static::STATUS_CANCELLED => Label::getLabel('LBL_Cancelled'),
            static::STATUS_ISSUE_REPORTED => Label::getLabel('LBL_Issue_Reported_Status'),
        ];
    }

    public function save()
    {
        if ($this->getMainTableRecordId() == 0) {
            $this->setFldValue('slesson_added_on', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    /**
     * @todo Add cancel hours check from configuration
     */
    public function cancelLessonByAdmin($reason = '')
    {
        $lessonDetailRows = ScheduledLessonDetails::getScheduledRecordsByLessionId($this->getMainTableRecordId());
        /* update status for every learner and refund [ */
        foreach ($lessonDetailRows as $lessonDetailRow) {
            $sLessonDetailObj = new ScheduledLessonDetails($lessonDetailRow['sldetail_id']);
            if (!$sLessonDetailObj->refundToLearner()) {
                $this->error = $sLessonDetailObj->getError();
                return false;
            }
            if (!$sLessonDetailObj->changeStatus(ScheduledLesson::STATUS_CANCELLED)) {
                $this->error = $sLessonDetailObj->getError();
                return false;
            }
            // remove from learner google calendar
            $setting = UserSetting::getUserSettings($lessonDetailRow['learnerId']);
            if (!empty($setting['us_google_access_token'])) {
                $sLessonDetailObj->loadFromDb();
                $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
                if (!empty($oldCalId)) {
                    SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $oldCalId);
                }
                $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', '');
                $sLessonDetailObj->save();
            }
            $start_date = $lessonDetailRow['slesson_date'];
            $start_time = $lessonDetailRow['slesson_start_time'];
            $end_time = $lessonDetailRow['slesson_end_time'];
            $user_timezone = $lessonDetailRow['learnerTz'];
            if ($start_time) {
                $start_time = $start_date . ' ' . $start_time;
                $end_time = $start_date . ' ' . $end_time;
                $start_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $start_time, true, $user_timezone);
                $start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $start_time, true, $user_timezone);
                $end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $end_time, true, $user_timezone);
            }
            $userNotification = new UserNotifications($lessonDetailRow['learnerId']);
            $userNotification->cancelLessonNotification($lessonDetailRow['sldetail_id'], $lessonDetailRow['teacherId'], $lessonDetailRow['teacherFullName'], USER::USER_TYPE_LEANER, $reason);
            /* send an email to learner[ */
            $vars = [
                '{lesson_id}' => $lessonDetailRow['slesson_id'],
                '{learner_name}' => $lessonDetailRow['learnerFullName'],
                '{teacher_name}' => $lessonDetailRow['teacherFullName'],
                '{lesson_name}' => ($lessonDetailRow['op_lpackage_is_free_trial'] == applicationConstants::NO) ? $lessonDetailRow['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial'),
                '{teacher_comment}' => $reason,
                '{lesson_date}' => MyDate::format($start_date),
                '{lesson_start_time}' => $start_time,
                '{lesson_end_time}' => $end_time,
                '{lesson_url}' => CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lessonDetailRow['sldetail_id']]),
            ];
        
            if (!EmailHandler::sendMailTpl($lessonDetailRow['learnerEmailId'], 'admin_cancelled_email', CommonHelper::getLangId(), $vars)) {
                $this->error = Label::getLabel('LBL_Mail_not_sent!');
                return false;
            }
        }
        return true;
    }

    public function assignLessonByAdmin($lessonId,$reason = '',$new_teacher_id)
    {
        $srchlessonDetailRows=new SearchBase('tbl_scheduled_lesson_details');
        $srchlessonDetailRows->addCondition('sldetail_id','=',$lessonId);
        $lessonDetailRows = FatApp::getDb()->fetch($srchlessonDetailRows->getResultSet());

        $sLessonDetailObj = new ScheduledLessonDetails($lessonDetailRows['sldetail_id']);
        $srchLearner=new SearchBase('tbl_user_credentials');
        $srchLearner->addCondition('credential_user_id','=',$lessonDetailRows['sldetail_learner_id']);        
        $learnerData = FatApp::getDb()->fetch($srchLearner->getResultSet());
     
        $srch_learner_user_teacher=new SearchBase('tbl_users');
        $srch_learner_user_teacher->addCondition('user_id','=',$lessonDetailRows['sldetail_learner_id']);        
        $learner_user = FatApp::getDb()->fetch($srch_learner_user_teacher->getResultSet());
        $learner_fulName=$learner_user['user_first_name']." ". $learner_user['user_last_name'];

        $srchLessonRows=new SearchBase('tbl_scheduled_lessons');
        $srchLessonRows->addCondition('slesson_id','=',$lessonDetailRows['sldetail_slesson_id']);        
        $lessonRows = FatApp::getDb()->fetch($srchLessonRows->getResultSet());
        $action_schedule = $lessonRows['slesson_status'];
        if($action_schedule ==1 ){
            $action = "Schedule";
        }
        
        if($lessonRows['slesson_kids_class'] > 0){
            $srchLessonName=new SearchBase('tbl_talkkids_classes');
        }else{
            $srchLessonName=new SearchBase('tbl_group_classes');
        }
        $srchLessonName->addCondition('grpcls_id','=',$lessonRows['slesson_grpcls_id']);        
        $lessonNameRows = FatApp::getDb()->fetch($srchLessonName->getResultSet());
        $lesson_name = $lessonNameRows['grpcls_title'];
        
        
        $srchTeacher=new SearchBase('tbl_user_credentials');
        $srchTeacher->addCondition('credential_user_id','=',$lessonRows['slesson_teacher_id']);        
        $TeachersData = FatApp::getDb()->fetch($srchTeacher->getResultSet());

        $srch_old_user_teacher=new SearchBase('tbl_users');
        $srch_old_user_teacher->addCondition('user_id','=',$lessonRows['slesson_teacher_id']);        
        $old_teachers_records = FatApp::getDb()->fetch($srch_old_user_teacher->getResultSet());
        $old_teacher_fulName=$old_teachers_records['user_first_name']." ". $old_teachers_records['user_last_name'];

        $srch_new_teacher=new SearchBase('tbl_user_credentials');
        $srch_new_teacher->addCondition('credential_user_id','=',$new_teacher_id);        
        $new_teachers_records = FatApp::getDb()->fetch($srch_new_teacher->getResultSet());
       
        $srch_user_teacher=new SearchBase('tbl_users');
        $srch_user_teacher->addCondition('user_id','=',$new_teacher_id);        
        $user_teachers_records = FatApp::getDb()->fetch($srch_user_teacher->getResultSet());
        $new_teacher_fulName=$user_teachers_records['user_first_name']." ". $user_teachers_records['user_last_name'];

        //     // remove from learner google calendar
            $setting = UserSetting::getUserSettings($lessonDetailRows['sldetail_learner_id']);
            if (!empty($setting['us_google_access_token'])) {
                $sLessonDetailObj->loadFromDb();
                $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
                if (!empty($oldCalId)) {
                    SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $oldCalId);
                }
                $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', '');
                $sLessonDetailObj->save();
            }

            $start_date = $lessonRows['slesson_date'];
            $start_time = $lessonRows['slesson_start_time'];
            $end_time = $lessonRows['slesson_end_time'];
            $user_timezone = $user_teachers_records['learnerTz'];
            if ($start_time) {
                $start_time = $start_date . ' ' . $start_time;
                $end_time = $start_date . ' ' . $end_time;
                $start_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $start_time, true, $user_timezone);
                $start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $start_time, true, $user_timezone);
                $end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $end_time, true, $user_timezone);
            }
            $userNotification = new UserNotifications($lessonDetailRows['sldetail_learner_id']);
            $userNotification->cancelLessonNotification($lessonDetailRows['sldetail_id'], $lessonRows['slesson_teacher_id'], $old_teacher_fulName, USER::USER_TYPE_LEANER, $reason);
            $userNotification = new UserNotifications($new_teacher_id);
            $userNotification->sendWalletCreditNotification();

            /* Send email to old instructor */
            $vars = [
                '{lesson_id}' => $lessonRows['slesson_id'],
                '{learner_name}' => $learner_fulName,
                '{teacher_name}' => $old_teacher_fulName,
                '{lesson_name}' => $lesson_name,
                '{teacher_comment}' => $reason,
               ];
            if (!EmailHandler::sendMailTpl($TeachersData['credential_email'], 'admin_cancelled_lesson', CommonHelper::getLangId(),$vars)) {
            
                $this->error = Label::getLabel('LBL_Mail_not_sent!');
                return false;
            }

            /* Send an email to learner */
            $vars = [
                '{lesson_id}' => $lessonRows['slesson_id'],
                '{learner_name}' => $learner_fulName,
                '{teacher_name}' => $new_teacher_fulName,
                '{teacher_comment}' => $reason,
                '{lesson_name}' => $lesson_name,
                '{lesson_date}' => MyDate::format($start_date),
                '{lesson_start_time}' => $start_time,
                '{lesson_end_time}' => $end_time,
                '{action}' => $action,
                '{lesson_url}' => CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lessonDetailRows['sldetail_id']]),
            ];
            if (!EmailHandler::sendMailTpl($learnerData['credential_email'], 'admin_schedule_assign_new_lesson_email', CommonHelper::getLangId(),$vars)) {
            
                $this->error = Label::getLabel('LBL_Mail_not_sent! to learner');
                return false;
            }

            // Email for new teacher
            $vars = [
                '{lesson_id}' => $lessonRows['slesson_id'],
                '{learner_name}' => $learner_fulName,
                '{teacher_name}' => $new_teacher_fulName,
                '{teacher_comment}' => $reason,
                '{lesson_name}' => $lesson_name,
                '{lesson_date}' => MyDate::format($start_date),
                '{lesson_start_time}' => $start_time,
                '{action}' => $action,
                '{lesson_end_time}' => $end_time,
                '{lesson_url}' => CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lessonDetailRows['sldetail_id']]),
            ];
            if (!EmailHandler::sendMailTpl($new_teachers_records['credential_email'], 'admin_schedule_assign_new_lesson_email', CommonHelper::getLangId(),$vars)) {
            
                $this->error = Label::getLabel('LBL_Mail_not_sent! to new teacher');
                return false;
            }

        // }
        return true;
    }



    public function cancelLessonByTeacher($reason = '')
    {
        $lessonDetailRows = ScheduledLessonDetails::getScheduledRecordsByLessionId($this->getMainTableRecordId());
        /* update status for every learner and refund [ */
        foreach ($lessonDetailRows as $lessonDetailRow) {
            $sLessonDetailObj = new ScheduledLessonDetails($lessonDetailRow['sldetail_id']);
            if (!$sLessonDetailObj->refundToLearner()) {
                $this->error = $sLessonDetailObj->getError();
                return false;
            }
            if (!$sLessonDetailObj->changeStatus(ScheduledLesson::STATUS_CANCELLED)) {
                $this->error = $sLessonDetailObj->getError();
                return false;
            }
            // remove from learner google calendar
            $setting = UserSetting::getUserSettings($lessonDetailRow['learnerId']);
            if (!empty($setting['us_google_access_token'])) {
                $sLessonDetailObj->loadFromDb();
                $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
                if (!empty($oldCalId)) {
                    SocialMedia::deleteEventOnGoogleCalendar($setting['us_google_access_token'], $oldCalId);
                }
                $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', '');
                $sLessonDetailObj->save();
            }
            $start_date = $lessonDetailRow['slesson_date'];
            $start_time = $lessonDetailRow['slesson_start_time'];
            $end_time = $lessonDetailRow['slesson_end_time'];
            $user_timezone = $lessonDetailRow['learnerTz'];
            if ($start_time) {
                $start_time = $start_date . ' ' . $start_time;
                $end_time = $start_date . ' ' . $end_time;
                $start_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $start_time, true, $user_timezone);
                $start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $start_time, true, $user_timezone);
                $end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $end_time, true, $user_timezone);
            }
            $userNotification = new UserNotifications($lessonDetailRow['learnerId']);
            $userNotification->cancelLessonNotification($lessonDetailRow['sldetail_id'], $lessonDetailRow['teacherId'], $lessonDetailRow['teacherFullName'], USER::USER_TYPE_LEANER, $reason);
            /* send an email to learner[ */
            $vars = [
                '{lesson_id}' => $lessonDetailRow['slesson_id'],
                '{learner_name}' => $lessonDetailRow['learnerFullName'],
                '{teacher_name}' => $lessonDetailRow['teacherFullName'],
                '{lesson_name}' => ($lessonDetailRow['op_lpackage_is_free_trial'] == applicationConstants::NO) ? $lessonDetailRow['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial'),
                '{teacher_comment}' => $reason,
                '{lesson_date}' => MyDate::format($start_date),
                '{lesson_start_time}' => $start_time,
                '{lesson_end_time}' => $end_time,
                '{lesson_url}' => CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lessonDetailRow['sldetail_id']]),
            ];
            if (!EmailHandler::sendMailTpl($lessonDetailRow['learnerEmailId'], 'teacher_cancelled_email', CommonHelper::getLangId(), $vars)) {
                $this->error = Label::getLabel('LBL_Mail_not_sent!');
                return false;
            }
        }
        return true;
    }

    public function rescheduleLessonByTeacher($reason = '')
    {
        $lessonDetailRows = ScheduledLessonDetails::getScheduledRecordsByLessionId($this->getMainTableRecordId());
        /* update status for every learner [ */
        foreach ($lessonDetailRows as $lessonDetailRow) {
            $sLessonDetailObj = new ScheduledLessonDetails($lessonDetailRow['sldetail_id']);
            $sLessonDetailObj->assignValues(['sldetail_learner_status' => ScheduledLesson::STATUS_NEED_SCHEDULING, 'sldetail_learner_join_time' => '']);
            if (!$sLessonDetailObj->save()) {
                $this->error = $sLessonDetailObj->getError();
                return false;
            }
            // remove from learner google calendar
            $settings = UserSetting::getUserSettings($lessonDetailRow['learnerId']);
            if (!empty($settings['us_google_access_token'])) {
                $sLessonDetailObj->loadFromDb();
                $oldCalId = $sLessonDetailObj->getFldValue('sldetail_learner_google_calendar_id');
                if (!empty($oldCalId)) {
                    SocialMedia::deleteEventOnGoogleCalendar($settings['us_google_access_token'], $oldCalId);
                }
                $sLessonDetailObj->setFldValue('sldetail_learner_google_calendar_id', '');
                $sLessonDetailObj->save();
            }
            $start_date = $lessonDetailRow['slesson_date'];
            $start_time = $lessonDetailRow['slesson_start_time'];
            $end_time = $lessonDetailRow['slesson_end_time'];
            $user_timezone = $lessonDetailRow['learnerTz'];
            if ($start_time) {
                $start_time = $start_date . ' ' . $start_time;
                $end_time = $start_date . ' ' . $end_time;
                $start_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $start_time, true, $user_timezone);
                $start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $start_time, true, $user_timezone);
                $end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i:s', $end_time, true, $user_timezone);
            }
            /* send email to learner[ */
            $vars = [
                '{lesson_id}' => $lessonDetailRow['slesson_id'],
                '{learner_name}' => $lessonDetailRow['learnerFullName'],
                '{teacher_name}' => $lessonDetailRow['teacherFullName'],
                '{lesson_name}' => ($lessonDetailRow['op_lpackage_is_free_trial'] == applicationConstants::NO) ? $lessonDetailRow['teacherTeachLanguageName'] : Label::getLabel('LBL_Trial'),
                '{teacher_comment}' => $reason,
                '{lesson_date}' => FatDate::format($start_date),
                '{lesson_url}' => CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lessonDetailRow['sldetail_id']]),
                '{lesson_start_time}' => $start_time,
                '{lesson_end_time}' => $end_time,
                '{action}' => Label::getLabel('LBL_Rescheduled'),
            ];
            if (!EmailHandler::sendMailTpl($lessonDetailRow['learnerEmailId'], 'teacher_reschedule_email', CommonHelper::getLangId(), $vars)) {
                $this->error = Label::getLabel('LBL_Mail_not_sent!');
                return false;
            }
            /* ] */
        }
        return true;
    }

    public function markTeacherJoinTime()
    {
        $this->assignValues(['slesson_teacher_join_time' => date('Y-m-d H:i:s')]);
        return $this->save();
    }

    public function endLesson()
    {
        $lessonId = $this->getMainTableRecordId();
        $this->loadFromDb();
        $lessonRow = $this->getFlds();
        if ($lessonRow['slesson_status'] == ScheduledLesson::STATUS_COMPLETED) {
            if ($lessonRow['slesson_ended_by'] == User::USER_TYPE_TEACHER) {
                $this->error = Label::getLabel('LBL_You_already_end_lesson!');
                return false;
            }
            $this->assignValues(array('slesson_teacher_end_time' => date('Y-m-d H:i:s')));
            return $this->save();
        }
        $dataUpdateArr = array(
            'slesson_status' => ScheduledLesson::STATUS_COMPLETED,
            'slesson_ended_by' => User::USER_TYPE_TEACHER,
            'slesson_ended_on' => date('Y-m-d H:i:s'),
            'slesson_teacher_end_time' => date('Y-m-d H:i:s'),
        );
        $db = FatApp::getDb();
        $db->startTransaction();
        if ($lessonRow['slesson_is_teacher_paid'] == 0) {
            if ($this->payTeacherCommission()) {
                $userNotification = new UserNotifications($lessonRow['slesson_teacher_id']);
                $userNotification->sendWalletCreditNotification($lessonRow['slesson_id']);
                $dataUpdateArr['slesson_is_teacher_paid'] = 1;
            }
        }
        $this->assignValues($dataUpdateArr);
        if (!$this->save()) {
            $db->rollbackTransaction();
            return false;
        }
        $sLessonDetailSrch = new ScheduledLessonDetailsSearch();
        $sLessonDetailSrch->addCondition('sldetail_learner_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        $sLessonDetailSrch->addMultipleFields(array('DISTINCT sldetail_id'));
        $sLessonDetails = $sLessonDetailSrch->getRecordsByLessonId($lessonRow['slesson_id']);
        foreach ($sLessonDetails as $sLessonDetail) {
            $scheduledLessonDetailObj = new ScheduledLessonDetails($sLessonDetail['sldetail_id']);
            $scheduledLessonDetailObj->assignValues(array('sldetail_learner_status' => ScheduledLesson::STATUS_COMPLETED));
            if (!$scheduledLessonDetailObj->save()) {
                $db->rollbackTransaction();
                FatUtility::dieJsonError($scheduledLessonDetailObj->getError());
            }
        }
        $db->commitTransaction();
        return true;
    }

}
