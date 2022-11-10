<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
MyDate::setUserTimeZone();
$user_timezone = MyDate::getUserTimeZone();
$date = new DateTime("now", new DateTimeZone($user_timezone));
$curDate = $date->format('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 days', strtotime($curDate)));
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
$currentUnixTime = strtotime($curDateTime);
$referer = preg_replace("(^https?://)", "", $referer);
$myDate = new MyDate();
$myDate->setMonthAndweekName($siteLangId);

?>
<style>
    p.bold-600.color-black {
        margin: 0px !important;
    }

    p.small {
        margin: 0px !important;
    }
</style>
<div class="results">
    <?php
    $remaining = $codeStatus['slesson_learner_code_status']; ?>
    <h1><?php //echo($codeStatus['slesson_learner_code_status']); 
        ?> <?php echo Label::getLabel('LBL_Left_to_join_private_lessons'); ?></h1>

    <?php foreach ($lessonArr as $key => $lessons) {

    ?>
        <div class="lessons-group margin-top-10">
            <?php if ($key != '0000-00-00') { ?>
                <date class="date uppercase small bold-600">
                    <?php
                    if (strtotime($curDate) == strtotime($key)) {
                        echo Label::getLabel('LBL_Today');
                    } elseif (strtotime($nextDate) == strtotime($key)) {
                        echo Label::getLabel('LBL_Tommorrow');
                    } else {
                        echo $myDate->convertToLocal(date('l, F d, Y', strtotime($key)));
                    }
                    ?>
                </date>
            <?php
            }
            foreach ($lessons as $lesson) {
                $lessonsStatus = $statusArr[$lesson['sldetail_learner_status']];
                $lesson['lesreschlog_id'] = FatUtility::int($lesson['lesreschlog_id']);
                if (
                    $lesson['lesreschlog_id'] > 0 &&
                    ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING ||
                        $lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED)
                ) {
                    $lessonsStatus = Label::getLabel('LBL_Rescheduled');
                    if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) {
                        $lessonsStatus = Label::getLabel('LBL_Pending_for_Reschedule');
                    }
                }
                if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_ISSUE_REPORTED) {
                    $lessonsStatus = Label::getLabel('L_Issue_Reported');
                }
                $teachLang = Label::getLabel('LBL_Trial');
                $action = 'free_trial';
                if ($lesson['is_trial'] == applicationConstants::NO) {
                    $action = '';
                    $teachLang = empty($teachLanguages[$lesson['slesson_slanguage_id']]) ? '' : $teachLanguages[$lesson['slesson_slanguage_id']];
                }
                $countReviews = TeacherLessonReview::getTeacherTotalReviews($lesson['teacherId'], $lesson['slesson_id']);
            ?>
                <!-- [ LESSON CARD ========= -->
                <div class="card-landscape">
                    <div class="card-landscape__colum card-landscape__colum--first">
                        <?php
                        $lessonsStartTime = $lesson['slesson_date'] . " " . $lesson['slesson_start_time'];
                        $startTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $lessonsStartTime, true, $user_timezone);
                        $startUnixTime = strtotime($startTime);
                        $endDateTime = $lesson['slesson_end_date'] . " " . $lesson['slesson_end_time'];
                        $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $endDateTime, true, $user_timezone);
                        $endUnixTime = strtotime($endDateTime);
                        if ($lesson['slesson_date'] != '0000-00-00') {
                        ?>
                            <div class="card-landscape__head">
                                <time class="card-landscape__time"><?php echo $myDate->convertToLocal(date('h:i A', $startUnixTime)); ?></time>
                                <date class="card-landscape__date"><?php echo $myDate->convertToLocal(date('l, F d, Y', $startUnixTime)); ?></date>
                            </div>
                            <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED) { ?>
                                <div class="timer company-classes">
                                    <div class="timer__media"><span><svg class="icon icon--clock icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock'; ?>"></use>
                                            </svg></span></div>
                                    <div class="timer__content">
                                        <?php if ($startUnixTime > $currentUnixTime) { ?>
                                            <div class="timer__controls countdowntimer timer-js" id="countdowntimer-<?php echo $lesson['slesson_id'] ?>" data-startTime="<?php echo $curDateTime; ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startUnixTime); ?>"></div>
                                        <?php
                                        } else {
                                            $lessonInfoLblKey = 'LBL_Lesson_time_has_passed';
                                            if ($endUnixTime > $currentUnixTime) {
                                                $lessonInfoLblKey = 'LBL_Lesson_ongoing';
                                            }
                                            echo '<span class="color-red">' . Label::getLabel($lessonInfoLblKey) . '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--second">
                        <div class="card-landscape__head">
                            <?php if ($lesson['slesson_grpcls_id'] > 0) { ?>
                                <span class="card-landscape__title"><?php echo $lesson['grpcls_title']; ?></span>
                            <?php } else { ?>
                                <span class="card-landscape__title">
                                    <?php
                                    $str = Label::getLabel('LBL_{teach-lang},{n}_minutes_of_Lesson');
                                    echo str_replace(['{teach-lang}', '{n}'], [$teachLang, $lesson['op_lesson_duration']], $str);
                                    ?>
                                </span>
                            <?php } ?>
                            <span class="card-landscape__status badge color-secondary badge--curve badge--small margin-left-0"><?php echo $lessonsStatus; ?></span>
                            <?php if ($lesson['slesson_grpcls_id'] > 0) { ?>
                                <span class="card-landscape__status badge color-secondary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_GROUP_CLASS'); ?></span>
                            <?php } ?>
                            <?php if ($lesson['repiss_id'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_Issue_Reported'); ?></span>
                            <?php } ?>
                        </div>
                        <?php if ($lesson['slesson_status'] != ScheduledLesson::STATUS_CANCELLED && $lesson['tlpn_id'] > 0) { ?>
                            <div class="card-landscape__docs">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" onclick="viewAssignedLessonPlan('<?php echo $lesson['sldetail_id']; ?>')" class="attachment-file">
                                        <svg class="icon icon--issue icon--attachement icon--xsmall color-black">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#attach'; ?>"></use>
                                        </svg>
                                        <?php echo $lesson['tlpn_title'] ?>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--third">
                        <div class="card-landscape__actions">
                            <div class="profile-meta">
                                <div class="profile-meta__media">
                                    <span class="avtar" data-title="<?php echo CommonHelper::getFirstChar($lesson['teacherFname']); ?>">
                                        <?php
                                        if (true == User::isProfilePicUploaded($lesson['teacherId'])) {
                                            $img = CommonHelper::generateUrl('Image', 'user', array($lesson['teacherId'], 'normal', 1), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                            echo '<img src="' . $img . '" />';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black">
                                        <?php echo $lesson['teacherFname']; ?><?php echo " "; ?><?php echo $lesson['teacherLname']; ?>
                                    </p>
                                    <p class="small"><?php echo $lesson['country_name']; ?></p>
                                </div>
                            </div>
                            <div class="main-join-button">
                                <a href="javascript:void(0);" onclick="employeeAcccessCode();" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover openFrom join-button">
                                    <svg class="icon icon--enter icon--18">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#enter'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Enter_Classroom'); ?></div>
                                    <p>Join now</p>
                                </a>
                            </div>
                            <div class="actions-group" style="display:none;">

                                <a href="<?php echo CommonHelper::generateUrl('Classes', 'view', [$lesson['sldetail_id']]); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--enter icon--18">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#enter'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Enter_Classroom'); ?></div>
                                </a>
                                <?php
                                if (
                                    $lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING ||
                                    ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED && $currentUnixTime < $startUnixTime)
                                ) {
                                ?>
                                    <a href="javascript:void(0);" onclick="cancelLesson('<?php echo $lesson['sldetail_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--cancel icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#cancel'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Cancel'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php if ($lesson['slesson_grpcls_id'] <= 0 && $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED && (MyDate::hoursDiff($lessonsStartTime) >= FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24))) { ?>
                                    <a href="javascript:void(0);" onclick="requestReschedule('<?php echo $lesson['sldetail_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#reschedule'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Reschedule'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php if ($lesson['slesson_grpcls_id'] <= 0 && $lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) { ?>
                                    <a href="javascript:void(0);" onclick="viewBookingCalendar('<?php echo $lesson['sldetail_id']; ?>', '<?php echo $action; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#reschedule'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Schedule_Lesson'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php
                                $lessonEnddate = $lesson['slesson_end_date'] . ' ' . $lesson['slesson_end_time'];
                                $lessonReportDate = strtotime($lessonEnddate . " +" . $reportHours . " hour");
                                if (FatUtility::int($lesson['repiss_id']) > 0) {
                                ?>
                                    <a href="javascript:void(0);" onclick="issueDetails('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue-details icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view-report'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Issue_Details'); ?></div>
                                    </a>
                                <?php
                                } else if (
                                    $reportHours > 0 &&
                                    (($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_COMPLETED ||
                                        ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED &&
                                            $currentUnixTime > $endUnixTime && $lesson['slesson_teacher_join_time'] == '0000-00-00 00:00:00')) &&
                                        $lesson['repiss_id'] < 1 && $lessonReportDate > strtotime(date('Y-m-d H:i:s')))
                                ) {
                                ?>
                                    <a href="javascript:void(0);" onclick="issueReported('<?php echo $lesson['sldetail_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--issue-reported icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#report-issue'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Report_Issue'); ?></div>
                                    </a>
                                <?php } ?>
                                <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_COMPLETED && $countReviews == 0) { ?>
                                    <a href="javascript:void(0);" onclick="lessonFeedback('<?php echo $lesson['sldetail_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--reschedule icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#lesson-view'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Rate_Lesson'); ?></div>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] ========= -->
            <?php } ?>
        </div>
    <?php } ?>

</div>
<?php
if (empty($lessons)) {
    $variables['msgHeading'] = Label::getLabel('LBL_No_Upcoming_lessons!!');
    $variables['btn'] = '<a href="' . CommonHelper::generateFullUrl('Classes') . '" class="btn bg-primary">' . Label::getLabel('LBL_View_All_Lessons') . '</a>';
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
} else {
    echo FatUtility::createHiddenFormFromData($postedData, array(
        'name' => 'frmSLnsSearchPaging'
    ));
    if ($referer == preg_replace("(^https?://)", "", CommonHelper::generateFullUrl('Classes'))) {
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
    }
}
?>
<script>
    jQuery(document).ready(function() {
        $('.countdowntimer').each(function(i) {
            var countdowntimerid = $(this).attr('id');
            $("#" + countdowntimerid).countdowntimer({
                startDate: $(this).attr('data-startTime'),
                dateAndTime: $(this).attr('data-endTime'),
                size: "sm",
            });
        });
    });
</script>