<?php

class LessonReminder extends MyAppModel
{

    const DAILY = 1;
    const HOURLY = 2;
    const TEACHER = 1;
    const LEARNER = 2;

    public function __construct()
    {
        CommonHelper::initCommonVariables();
    }

    public function sendLessonReminder($type)
    {
        $type = FatUtility::int($type);
        $langId = CommonHelper::getLangId();
        if (0 > $type) {
            return Label::getLabel('MSG_Invalid_Request', $langId);
        }
        $srch = $this->getLessonsData();
        if ($type == self::DAILY) {
            $srch->addCondition('slns.slesson_date', '=', date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))));
            $srch->addCondition('slns.slesson_reminder_one', '=', 0);
        } else {
            $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '>', date('Y-m-d H:i:s'), 'AND', true);
            $srch->addCondition('mysql_func_CONCAT(slns.slesson_date, " ", slns.slesson_start_time )', '<=', date('Y-m-d H:i:s', strtotime('+ 30 minutes', strtotime(date('Y-m-d H:i:s')))), 'AND', true);
            $srch->addCondition('slns.slesson_reminder_two', '=', 0);
        }
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        if (empty($lessons)) {
            return Label::getLabel('MSG_No_Record_Found', $langId);
        }
        if ($this->prepareDataForReminder($lessons, $type)) {
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
        $teacherStatus = $this->sendEmailToUsers($teacherLessonArr, self::TEACHER);
        $learnerStatus = $this->sendEmailToUsers($lernerLessonArr, self::LEARNER);
        if ($cronType == self::DAILY) {
            $filed_to_update = 'slesson_reminder_one';
        } else {
            $filed_to_update = 'slesson_reminder_two';
        }
        if (!empty($teacherStatus) || !empty($learnerStatus)) {
            $lessonIds = array_merge($teacherStatus, $learnerStatus);
            $lessonIds = array_unique($lessonIds);
            $LessonIdsString = '(' . implode(', ', $lessonIds) . ')';
            $db = FatApp::getDb();
            if ($db->query('UPDATE `' . ScheduledLesson::DB_TBL . '` SET `' . $filed_to_update . '` = 1 WHERE `slesson_id` IN ' . $LessonIdsString)) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function sendEmailToUsers($LessonArr, $userType)
    {
        $template = 'coming_up_lesson_reminder';
        if ($userType == self::TEACHER) {
            $user = Label::getLabel('Lbl_Learner', CommonHelper::getLangId());
        } else {
          
            $user = Label::getLabel('Lbl_Teacher', CommonHelper::getLangId());
        }
        $lessonIds = [];
        $emailNotificationObj = new EmailHandler();
        $langId = CommonHelper::getLangId();
        foreach ($LessonArr as $lessons) {
            $lessonsData = '';
            $data = [];
            if ($userType == self::TEACHER) {
                $tommorowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lessons[0]['slesson_date'] . ' ' . $lessons[0]['slesson_start_time'], true, $lessons[0]['teacherTimezone']);
            } else {
                $tommorowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $lessons[0]['slesson_date'] . ' ' . $lessons[0]['slesson_start_time'], true, $lessons[0]['LearnerTimezone']);
            }
            $lessonsData = ' <table style="border:1px solid #ddd; border-collapse:collapse;" cellspacing="0" cellpadding="0" border="0" >
				<thead>
                                    <tr><th colspan="4" style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" >' . $tommorowDate . '</th></tr>
				</thead>
				<tbody>
                                    <tr>
                                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . $user . ' </th>
                                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . Label::getLabel('Lbl_Start', CommonHelper::getLangId()) . ' </th>
                                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> ' . Label::getLabel('Lbl_End', CommonHelper::getLangId()) . '  </th>
                                        <th style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333; font-weight:bold;" width="153"> </th>
                                    </tr>';
            foreach ($lessons as $lesson) {
                $lessonIds[] = $lesson['slesson_id'];
                $lessonLink = CommonHelper::generateFullUrl('TeacherScheduledLessons', 'view', [$lesson['slesson_id']], CONF_WEBROOT_DASHBOARD);
                if ($userType == self::TEACHER) {
                    $lesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_date'] . '  ' . $lesson['slesson_start_time'], true, $lesson['teacherTimezone']);
                    $lesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_end_date'] . '  ' . $lesson['slesson_end_time'], true, $lesson['teacherTimezone']);
                    $lessonsData .= '<tr>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson['learnerFullName'] . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson_start_time . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson_end_time . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153"><a href="' . $lessonLink . '" style="background:#e84c3d; color:#fff; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">View</a></td>
                                    </tr>';
                } else {
                    $lesson_start_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_date'] . '  ' . $lesson['slesson_start_time'], true, $lesson['LearnerTimezone']);
                    $lesson_end_time = MyDate::convertTimeFromSystemToUserTimezone('H:i', $lesson['slesson_end_date'] . '  ' . $lesson['slesson_end_time'], true, $lesson['LearnerTimezone']);
                    $lessonLink = CommonHelper::generateFullUrl('LearnerScheduledLessons', 'view', [$lesson['sldetail_id']], CONF_WEBROOT_DASHBOARD);
                    $lessonsData .= '<tr>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson['teacherFullName'] . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson_start_time . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153">' . $lesson_end_time . '</td>
                                        <td style="padding:10px;font-size:13px;border:1px solid #ddd; color:#333;" width="153"><a href="' . $lessonLink . '" style="background:#e84c3d; color:#fff; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">View</a></td>
                                 </tr>';
                }
            }
            $lessonsData .= '</tbody></table><br />';
            if ($userType == self::TEACHER) {
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
        return $lessonIds;
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
            'sld.sldetail_id',
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
        ]);
        $srch->addCondition('slns.slesson_status', '=', ScheduledLesson::STATUS_SCHEDULED);
        return $srch;
    }
}
