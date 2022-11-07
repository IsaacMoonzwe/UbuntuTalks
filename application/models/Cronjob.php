<?php

class Cronjob extends FatModel
{

    const DB_TBL_CONFIGURATION = 'tbl_configurations';

    public function __construct()
    {
        CommonHelper::initCommonVariables();
    }

    public function lessonOneDayReminder()
    {
        $langId = CommonHelper::getLangId();
        $srch = $this->getLessonsData();
        $srch->addCondition('slns.slesson_date', '=', date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))));
        $srch->addCondition('slns.slesson_reminder_one', '=', 0);
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        if (empty($lessons)) {
            return Label::getLabel('MSG_No_Record_Found', $langId);
        }
        if ($this->prepareDataForReminder($lessons, 'daily')) {
            return Label::getLabel('MSG_Success', $langId);
        } else {
            return Label::getLabel('MSG_Error', $langId);
        }
    }

    public function lessonHalfHourReminder()
    {
        $langId = CommonHelper::getLangId();
        $srch = $this->getLessonsData();
        $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>', date('Y-m-d H:i:s'), 'AND', true);
        $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '<=', date('Y-m-d H:i:s', strtotime('+ 30 minutes', strtotime(date('Y-m-d H:i:s')))), 'AND', true);
        $srch->addCondition('slns.slesson_reminder_two', '=', 0);
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        if (empty($lessons)) {
            return Label::getLabel('MSG_No_Record_Found', $langId);
        }
        if ($this->prepareDataForReminder($lessons, 'hourly')) {
            return Label::getLabel('MSG_Success', $langId);
        } else {
            return Label::getLabel('MSG_Error', $langId);
        }
    }

    private function prepareDataForReminder($lessons, $cronType)
    {
        $teacherLessonArr = [];
        $lernerLessonArr = [];
        foreach ($lessons as $lesson) {
            $key = $lesson['teacherId'];
            $key1 = $lesson['learnerId'];
            $teacherLessonArr[$key][] = $lesson;
            $lernerLessonArr[$key1][] = $lesson;
        }
        $teacherStatus = $this->sendEmailToUsers($teacherLessonArr, 'teacher', $cronType);
        $learnerStatus = $this->sendEmailToUsers($lernerLessonArr, 'learner', $cronType);
        if ($cronType == 'daily') {
            $_filed_to_update = 'slesson_reminder_one';
        } else {
            $_filed_to_update = 'slesson_reminder_two';
        }
        if (!empty($teacherStatus) || !empty($learnerStatus)) {
            $lessonIds = array_merge($teacherStatus, $learnerStatus);
            $lessonIds = array_unique($lessonIds);
            $LessonIdsString = '(' . implode(', ', $lessonIds) . ')';
            $db = FatApp::getDb();
            if ($db->query('UPDATE `' . ScheduledLesson::DB_TBL . '` SET `' . $_filed_to_update . '` = 1 WHERE `slesson_id` IN ' . $LessonIdsString)) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function sendEmailToUsers($LessonArr, $userType)
    {
        $template = 'coming_up_lesson_reminder';
        if ($userType == 'teacher') {
            $controller = 'teacherScheduledLessons';
            $user = 'Learner';
        } else {
            $controller = 'learnerScheduledLessons';
            $user = 'Teacher';
        }
        $teacherLessonIds = [];
        $emailNotificationObj = new EmailHandler();
        $langId = CommonHelper::getLangId();
        foreach ($LessonArr as $lessons) {
            $lessonsData = '';
            $data = [];
            if ($userType == 'teacher') {
                $tommorowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lessons[0]['slesson_date'] . '  ' . $lessons[0]['slesson_start_time'], true, $lessons[0]['teacherTimezone']);
            } else {
                $tommorowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lessons[0]['slesson_date'] . '  ' . $lessons[0]['slesson_start_time'], true, $lessons[0]['LearnerTimezone']);
            }
            $lessonsData = ' <table width="100%" cellspacing="0" cellpadding="5" border="1"   align="center">
				<thead>
                                    <tr><th colspan="4">' . $tommorowDate . '</th></tr>
				</thead>
				<tbody>
                                    <tr>
                                        <th> ' . $user . ' </th>
                                        <th> Start </th>
                                        <th> End  </th>
                                        <th> </th>
                                    </tr>';
            foreach ($lessons as $lesson) {
                $teacherLessonIds[] = $lesson['slesson_id'];
                if ($userType == 'teacher') {
                    $teacherLink = CommonHelper::generateFullUrl($controller, 'view', [$lesson['slesson_id']], CONF_WEBROOT_DASHBOARD);
                    $lesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_date'] . '  ' . $lesson['slesson_start_time'], true, $lesson['teacherTimezone']);
                    $lesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_end_date'] . '  ' . $lesson['slesson_end_time'], true, $lesson['teacherTimezone']);
                    $lessonsData .= '<tr>
                                        <td>' . $lesson['learnerFullName'] . '</td>
                                        <td>' . $lesson_start_time . '</td>
                                        <td>' . $lesson_end_time . '</td>
                                        <td><a href="' . $teacherLink . '" style="background:#e84c3d; color:#fff; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">View</a></td>
                                    </tr>';
                } else {
                    $lessonLink = CommonHelper::generateFullUrl($controller, 'view', [$lesson['sldetail_id']], CONF_WEBROOT_DASHBOARD);
                    $lesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_date'] . '  ' . $lesson['slesson_start_time'], true, $lesson['LearnerTimezone']);
                    $lesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_end_date'] . '  ' . $lesson['slesson_end_time'], true, $lesson['LearnerTimezone']);
                    $lessonsData .= '<tr>
                                        <td>' . $lesson['teacherFullName'] . '</td>
                                        <td>' . $lesson_start_time . '</td>
                                        <td>' . $lesson_end_time . '</td>
                                        <td><a href="' . $lessonLink . '" style="background:#e84c3d; color:#fff; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">View</a></td>
                                    </tr>';
                }
            }
            $lessonsData .= '</tbody></tabale>';
            if ($userType == 'teacher') {
                $data = [
                    'user_email' => $lessons[0]['teacherEmail'],
                    'user_first_name' => $lessons[0]['teacherFname'],
                    'user_last_name' => $lessons[0]['teacherLname'],
                    'user_full_name' => $lessons[0]['teacherFullName'],
                    'lessons_details' => $lessonsData
                ];
            } else {
                $data = [
                    'user_email' => $lessons[0]['learnerEmail'],
                    'user_first_name' => $lessons[0]['LearnerFname'],
                    'user_last_name' => $lessons[0]['LearnerLname'],
                    'user_full_name' => $lessons[0]['learnerFullName'],
                    'lessons_details' => $lessonsData
                ];
            }
            $emailNotificationObj->sendLessonReminderMail($template, $langId, $data);
        }
        return $teacherLessonIds;
    }

    private function getLessonsData()
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinTeacherCredentials();
        $srch->joinLearner();
        $srch->joinLearnerCredentials();
        $srch->addCondition('order_is_paid', ' = ', Order::ORDER_IS_PAID);
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'slns.slesson_id',
            'slns.slesson_slanguage_id',
            'sld.sldetail_learner_id as learnerId',
            'slns.slesson_teacher_id as teacherId',
            'ut.user_first_name as teacherFname',
            'ut.user_last_name as teacherLname',
            'ut.user_timezone as teacherTimezone',
            'tcred.credential_email as teacherEmail',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'ul.user_first_name as LearnerFname',
            'ul.user_last_name as LearnerLname',
            'ul.user_timezone as LearnerTimezone',
            'CONCAT(ul.user_first_name, " ", ul.user_last_name) as learnerFullName',
            'lcred.credential_email as learnerEmail',
            'slns.slesson_date',
            'slns.slesson_end_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            '"-" as teacherTeachLanguageName',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration'
        ]);
        $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        return $srch;
    }
}
