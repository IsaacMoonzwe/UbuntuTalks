<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$userTz = MyDate::getUserTimeZone();
$format = 'Y/m/d H:i:s';
$curDate = MyDate::timezoneConvertedTime($format, date('Y-m-d H:i:s'), true, $userTz);
$curDateTimeunix = strtotime($curDate);
$startTime = MyDate::timezoneConvertedTime($format, $lesson['slesson_date'] . ' ' . $lesson['slesson_start_time'], true, $userTz);
$startDateTimeUnixtime = strtotime($startTime);
$endTime = MyDate::timezoneConvertedTime($format, date($lesson['slesson_end_date'] . ' ' . $lesson['slesson_end_time']), true, $userTz);
$endDateTimeUnixtime = strtotime($endTime);
$endTime = date($format, $endDateTimeUnixtime);
$chatId = UserAuthentication::getLoggedUserId();
$teacherImageTag = '';
$teacherImage = '';
$studentImageTag = '';
$studentImage = '';
$baseSeoUrl = CommonHelper::generateUrl('Teachers', 'profile') . '/';
if (true == User::isProfilePicUploaded($lesson['sldetail_learner_id'])) {
    $studentImage = CommonHelper::generateFullUrl('Image', 'user', array($lesson['sldetail_learner_id']), CONF_WEBROOT_FRONT_URL) . '?' . time();
    $studentImageTag = '<img src="' . $studentImage . '" />';
}
if (true == User::isProfilePicUploaded($lesson['slesson_teacher_id'])) {
    $teacherImage = CommonHelper::generateFullUrl('Image', 'user', array($lesson['slesson_teacher_id']), CONF_WEBROOT_FRONT_URL) . '?' . time();
    $teacherImageTag = '<img src="' . $teacherImage . '" />';
}
$teachLang = Label::getLabel('LBL_Trial');
if ($lesson['op_lpackage_is_free_trial'] == applicationConstants::NO) {
    $teachLang = TeachingLanguage::getLangById($lesson['slesson_slanguage_id']);
}
$chat_group_id = '';
$lessonTitle = $lesson['grpcls_title'];
if ($lesson['slesson_grpcls_id'] <= 0) {
    $chat_group_id = "LESSON-" . $lesson['slesson_id'];
    $lessonTitleLabel = Label::getLabel('LBL_{teach-lang},{n}_minutes_of_Lesson');
    $lessonTitle = str_replace(['{teach-lang}', '{n}'], [$teachLang, $lesson['op_lesson_duration']], $lessonTitleLabel);
}
$canEnd = ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) && ($startDateTimeUnixtime < $curDateTimeunix);
$lessonsStatus = $statusArr[$lesson['slesson_status']];
$lesson['lesreschlog_id'] = FatUtility::int($lesson['lesreschlog_id']);
if (
        $lesson['lesreschlog_id'] > 0 &&
        ($lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING ||
        $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED)
) {
    $lessonsStatus = Label::getLabel('LBL_Rescheduled');
    if ($lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) {
        $lessonsStatus = Label::getLabel('LBL_Pending_for_Reschedule');
    }
}
$isScheduled = $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED;
$isJoined = $lesson['slesson_teacher_join_time'] > 0;
$activeMettingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
$canEdit = ($lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) ||
        (($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) && ($endDateTimeUnixtime > $curDateTimeunix));
$myDate = new MyDate();
$myDate->setMonthAndweekName($siteLangId);
?>
<div class="session__head">
    <div class="session-infobar">
        <div class="row justify-content-between align-items-center">
            <div class="col-xl-8 col-lg-8 col-sm-12">
                <div class="session-infobar__top">
                    <h4>
                        <?php
                        echo $lessonTitle;
                        if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) {
                            ?>
                            <span class="color-primary"><?php echo $lessonsStatus; ?></span>
                            <?php
                        }
                        echo ' ' . Label::getLabel('LBL_with');
                        ?>
                    </h4>
                    <div class="profile-meta">
                        <span class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($lesson['learnerFname']); ?>"><?php echo $studentImageTag; ?></span>
                        <div class="profile-meta__media">
                        </div>
                        <div class="profile-meta__details">
                            <h4 class="bold-600"><?php echo $lesson['learnerFname'] ?> </h4>
                        </div>
                    </div>
                    <?php if (count($learners) > 1) { ?>
                        <div class="more-dropdown margin-left-2">
                            <a class="menu__item-trigger trigger-js color-secondary"><?php echo (count($learners) - 1) . ' ' . Label::getLabel('LBL_More'); ?></a>
                            <ul class="menu__dropdown more--dropdown" id="more-stud">
                                <?php
                                foreach ($learners as $learner) {
                                    if ($learner['user_id'] == $lesson['sldetail_learner_id']) {
                                        continue;
                                    }
                                    ?>
                                    <li>
                                        <div class="profile-meta">
                                            <div class="profile-meta__media">
                                                <span class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar($learner['user_first_name']); ?>">
                                                    <?php
                                                    if (true == User::isProfilePicUploaded($learner['user_id'])) {
                                                        $lessonStudentImage = CommonHelper::generateFullUrl('Image', 'user', array($learner['user_id']), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                                        echo '<img src="' . $lessonStudentImage . '" />';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="profile-meta__details">
                                                <h4 class="bold-600"><?php echo $learner['user_first_name'] . ' ' . $learner['user_last_name']; ?></h4>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
                <div class="session-infobar__bottom">
                    <?php if ($lesson['slesson_date'] != '0000-00-00') { ?>
                        <div class="session-time">
                            <p>
                                <span><?php echo $myDate->convertToLocal(date('h:i A', $startDateTimeUnixtime) . ' - ' . date('h:i A', $endDateTimeUnixtime)); ?>,</span>
                                <?php echo $myDate->convertToLocal(date('l, F d, Y', $startDateTimeUnixtime)); ?>
                            </p>
                        </div>
                    <?php } ?>
                    <?php if ($lesson['slesson_status'] != ScheduledLesson::STATUS_CANCELLED) { ?>
                        <?php if ($lesson['tlpn_id'] > 0) { ?>
                            <div class="session-resource">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" onclick="viewAssignedLessonPlan('<?php echo $lesson['slesson_id']; ?>');" class="attachment-file">
                                        <svg class="icon icon--issue icon--attachement icon--xsmall color-black">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#attach'; ?>"></use>
                                        </svg>
                                        <?php echo $lesson['tlpn_title']; ?>
                                    </a>
                                    <a href="javascript:void(0);" onclick="changeLessonPlan('<?php echo $lesson['slesson_id']; ?>');" class="underline color-primary  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_Change'); ?></a>
                                    <a href="javascript:void(0);" onclick="removeAssignedLessonPlan('<?php echo $lesson['slesson_id']; ?>');" class="underline color-primary  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_Remove'); ?></a>
                                </div>
                            </div>
                        <?php } else if ($canEdit) { ?>
                            <div class="session-resource">
                                <a a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['slesson_id']; ?>');" class="btn btn--transparent btn--addition color-primary btn--small"><?php echo Label::getLabel('LBL_Add_Lesson_Plan'); ?></a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-sm-12">
                <div class="session-infobar__action">
                    <span class="btn btn--live" id="end_lesson_timer" style="display:none;"> </span>
                    <button class="btn bg-red" <?php echo!$canEnd || !$isJoined ? 'style="display:none;"' : '' ?> id="endL" onclick="endLesson(<?php echo $lesson['slesson_id']; ?>);">
                        <?php echo Label::getLabel('LBL_End_Lesson'); ?>
                    </button>
                    <?php if ($lesson['repiss_id'] > 0) { ?>
                        <?php if ($lesson['slesson_grpcls_id'] > 0) { ?>
                            <a href="<?php echo CommonHelper::generateUrl('TeacherIssueReported', 'index', [$lesson['slesson_grpcls_id']]) ?>" target="_blank" class="btn btn--bordered color-third"><?php echo Label::getLabel('LBL_Issue_Details'); ?></a>
                        <?php } else { ?>
                            <button onclick="issueDetails('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered color-third"><?php echo Label::getLabel('LBL_Issue_Details'); ?></button>
                            <?php if ($lesson['repiss_status'] == ReportedIssue::STATUS_PROGRESS) { ?>
                                <button onclick="resolveForm('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered color-third"><?php echo Label::getLabel('LBL_Resolve_Issue'); ?></button>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?php
                    if (
                            ($lesson['slesson_grpcls_id'] == 0) &&
                            $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED &&
                            $curDateTimeunix < $startDateTimeUnixtime &&
                            MyDate::hoursDiff($lesson['slesson_date'] . ' ' . $lesson['slesson_start_time']) >= FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24)
                    ) {
                        ?>
                        <button class="btn btn--third reschedule-lesson--js" onclick="requestReschedule('<?php echo $lesson['slesson_id']; ?>');"><?php echo Label::getLabel('LBL_Reschedule'); ?></button>
                    <?php } ?>
                    <?php
                    if (
                            $lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING ||
                            ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED && $curDateTimeunix < $startDateTimeUnixtime)
                    ) {
                        ?>
                        <button onclick="cancelLesson('<?php echo $lesson['slesson_id']; ?>');" class="btn btn--bordered color-third cancel-lesson--js"><?php echo Label::getLabel('LBL_Cancel'); ?></button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="session__body">
    <div class="sesson-window" style="background-image: url(<?php echo CommonHelper::generateUrl('Image', 'lesson', array($siteLangId), CONF_WEBROOT_FRONTEND) ?>);">
        <div class="sesson-window__content lessonBox" id="lessonBox">
            <!-- session-window__frame -->
            <div class="session-status">
                <?php
                $showGoToDashboardBtn = true;
                $statusInfoLabel = '';
                if ($curDateTimeunix > $endDateTimeUnixtime && $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) {
                    $statusInfoLabel = Label::getLabel('LBL_Note_End_time_for_this_lesson_is_passed._Schedule_more_lessons.');
                    ?>
                    <div class="status_media">
                        <svg class="icon">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock'; ?>"></use>
                        </svg>
                    </div>
                    <?php
                }
                switch ($lesson['slesson_status']) {
                    case ScheduledLesson::STATUS_NEED_SCHEDULING:
                        $statusInfoLabel = Label::getLabel('LBL_Note_This_lesson_is_Unscheduled._Encourage_your_student_to_schedule_it.');
                        break;
                    case ScheduledLesson::STATUS_COMPLETED:
                        $statusInfoLabel = Label::getLabel('LBL_Note_This_lesson_is_completed');
                        if ($countReviews <= 0) {
                            $statusInfoLabel = Label::getLabel('LBL_Note_This_lesson_is_completed._Encourage_your_learner_to_rate_it.');
                        }
                        break;
                    case ScheduledLesson::STATUS_CANCELLED:
                        $statusInfoLabel = Label::getLabel('LBL_Note_This_Lesson_has_been_cancelled._Schedule_more_lessons.');
                        break;
                    case ScheduledLesson::STATUS_SCHEDULED:
                        $showGoToDashboardBtn = false;
                        if ($curDateTimeunix > $endDateTimeUnixtime) {
                            $showGoToDashboardBtn = true;
                        }
                        break;
                }
                if (!empty($statusInfoLabel)) {
                    echo "<p>" . $statusInfoLabel . "</p>";
                }
                if ($showGoToDashboardBtn) {
                    ?>
                    <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons'); ?>" class="btn bg-primary"><?php echo Label::getLabel('LBL_Go_to_Dashboard.'); ?></a>
                <?php } ?>
                <?php ?>
                <?php if ($lesson['slesson_teacher_join_time'] == '0000-00-00 00:00:00' || $activeMettingTool == ApplicationConstants::MEETING_ZOOM) { ?>
                    <div class="join-btns join_lesson_now" id="joinL" <?php echo ($startDateTimeUnixtime > $curDateTimeunix || $curDateTimeunix > $endDateTimeUnixtime || !$isScheduled ? 'style="display:none;"' : '') ?>>
                        <?php if ($activeMettingTool == ApplicationConstants::MEETING_ZOOM) { ?>
                            <!-- <a href="javascript:void(0);" class="btn btn--primary" onclick="joinLesson(CometJsonData, CometJsonFriendData);"><?php echo Label::getLabel('LBL_Join_Lesson_From_Browser'); ?></a> -->
                            <a href="javascript:void(0);" class="btn btn--secondary" onclick="joinLessonFromApp(CometJsonData, CometJsonFriendData);"><?php echo Label::getLabel('LBL_Join_Lesson_From_App'); ?></a>
                        <?php } else { ?>
                            <a href="javascript:void(0);" class="btn btn--secondary btn--large" id="joinL" onclick="joinLesson(CometJsonData, CometJsonFriendData);"><?php echo Label::getLabel('LBL_Join_Lesson'); ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div class="start-lesson-timer timer" style="display:none;">
                <h5 class="timer-title"><?php echo Label::getLabel('LBL_Starts_In'); ?></h5>
                <div class="countdown-timer size_lg" id="start_lesson_timer">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var flashCardEnabled = '<?php echo $flashCardEnabled ?: 0 ?>';
    var curDate = "<?php echo $curDate; ?>";
    var startTime = "<?php echo $startTime; ?>";
    var endTime = "<?php echo $endTime; ?>";
    langLbl.chargelearner = "<?php echo ($lesson['op_lpackage_is_free_trial']) ? Label::getLabel('LBL_End_Lesson') : Label::getLabel('LBL_Charge_Learner'); ?>";
    var is_time_up = '<?php echo ($endTime > 0) && ($endDateTimeUnixtime < $curDateTimeunix) ?>';
    var lesson_joined = '<?php echo $isJoined ?>';
    var lesson_completed = '<?php echo $lesson['slesson_teacher_end_time'] > 0 ?>';
    var lesson_status_completed = '<?php echo $lesson['slesson_status'] == ScheduledLesson::STATUS_COMPLETED ?>';
    var slesson_id = '<?php echo $lesson['slesson_id'] ?>';
    var lessonStatus = <?php echo $lesson['slesson_status']; ?>;
    var groupClassId = <?php echo $lesson['slesson_grpcls_id']; ?>;
    var isGroupClass = (groupClassId > 0) ? true : false;
    var chat_appid = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_APP_ID'); ?>';
    var chat_auth = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_AUTH'); ?>';
    var chat_id = '<?php echo $chatId; ?>';
    var chat_group_id = '<?php echo $chat_group_id ?>';
    var chat_name = "<?php echo $lesson['teacherFname']; ?>";
    var chat_api_key = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_API_KEY'); ?>';
    var chat_avatar = "<?php echo $teacherImage; ?>";
    var chat_friends = "<?php echo $lesson['sldetail_learner_id']; ?>";
    var activeMeetingTool = '<?php echo FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_INT, 2); ?>';
    var cometChatMeetingTool = '<?php echo FatApp::getConfig('CONF_MEETING_TOOL_COMET_CHAT', FatUtility::VAR_INT, 1); ?>';
    var lessonspaceMeetingTool = '<?php echo FatApp::getConfig('CONF_MEETING_TOOL_LESSONSPACE', FatUtility::VAR_INT, 2); ?>';
    var canEnd = '<?php echo $canEnd ?>';
    var CometJsonTeacherData = [{
            "userId": "<?php echo $chatId; ?>",
            "fname": "<?php echo $lesson['teacherFname']; ?>",
            "avatarURL": "<?php echo $teacherImage; ?>",
            "profileURL": "<?php echo $baseSeoUrl . $lesson['teacherUrlName']; ?>",
            "role": "<?php echo User::getUserTypesArr()[User::USER_TYPE_TEACHER]; ?>"
        }];
    var CometJsonLearnerData = [{
            "userId": "<?php echo $lesson['sldetail_learner_id']; ?>",
            "fname": "<?php echo $lesson['learnerFname']; ?>",
            "avatarURL": "<?php echo $studentImage; ?>",
            "profileURL": "<?php echo $baseSeoUrl . $lesson['learnerUrlName']; ?>",
            "role": "<?php echo User::getUserTypesArr()[User::USER_TYPE_LEANER]; ?>"
        }];
    var CometJsonData = CometJsonTeacherData.concat(CometJsonLearnerData);
    var CometJsonFriendData = {
        "lessonId": "<?php echo $lesson['slesson_id'] ?>",
        "userId": "<?php echo $chatId; ?>",
        "friendId": "<?php echo $lesson['sldetail_learner_id']; ?>"
    };
    var checkEveryMinuteStatusVar = null;
    var checkNewFlashCardsVar = null;
    if (activeMeetingTool == cometChatMeetingTool) {
        createUserCometChatApi(CometJsonData, CometJsonFriendData);
    }
    if (!isZoomMettingToolActive && !is_time_up && lesson_joined && !lesson_status_completed && lessonStatus != '<?php echo ScheduledLesson::STATUS_CANCELLED ?>') {
        joinLesson(CometJsonData, CometJsonFriendData);
    }
    var worker = new Worker(siteConstants.webroot + 'js/worker-time-interval.js');
    function joinLessonButtonAction() {
        $("#joinL, .reschedule-lesson--js, .cancel-lesson--js").hide();
        $("#endL").show();
        $('.screen-chat-js').show();
        $('.lessonBox').removeClass('sesson-window__content').addClass('session-window__frame').show();
        checkEveryMinuteStatus();
        searchFlashCards(document.frmFlashCardSrch);
        checkNewFlashCards();
    }
    function endLessonButtonAction() {
        $("#joinL, .reschedule-lesson--js, .cancel-lesson--js").hide();
        $("#end_lesson_time_div, #endL, .screen-chat-js").hide();
        $('.lessonBox').removeClass('session-window__frame').addClass('sesson-window__content').hide();
        searchFlashCards(document.frmFlashCardSrch);
        if (checkEveryMinuteStatusVar) {
            clearInterval(checkEveryMinuteStatusVar);
        }
        if (checkNewFlashCardsVar) {
            clearInterval(checkNewFlashCardsVar);
        }
        worker.terminate();
    }
    function checkEveryMinuteStatus() {
        checkEveryMinuteStatusVar = setInterval(function () {
            fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'checkEveryMinuteStatus', [slesson_id]), '', function (t) {
                var t = JSON.parse(t);
                if (t.slesson_status == '<?php echo ScheduledLesson::STATUS_CANCELLED ?>') {
                    location.reload();
                }
                if (!isGroupClass && lessonStatus == STATUS_SCHEDULED && (t.sldetail_learner_status == STATUS_COMPLETED || t.sldetail_learner_status == STATUS_ISSUE_REPORTED)) {
                    $.alert({
                        title: '<?php echo Label::getLabel('LBL_End_Lesson'); ?>',
                        content: '<?php echo Label::getLabel('LBL_Learner_Ends_The_Lesson!'); ?>',
                        useBootstrap: false,
                        boxWidth: '20%',
                        escapeKey: false,
                        onClose: function () {
                            $.mbsmessage.close();
                            location.reload();
                        },
                    });
                    setTimeout(function () {
                        $.mbsmessage.close();
                        location.reload();
                    }, 1500);
                }
            });
        }, 60000);
    }
    function checkNewFlashCards() {
        if ((typeof flashCardEnabled !== typeof undefined) && !flashCardEnabled) {
            return;
        }
        checkNewFlashCardsVar = setInterval(function () {
            searchFlashCards(document.frmFlashCardSrch);
        }, 30000)
    }
    function countDownTimer(start, end, func) {
        var countDownDate = new Date(end).getTime();
        var now = new Date(start).getTime();
        if (countDownDate > now) {
            worker.postMessage({
                'start': start,
                end: end
            });
        }
        worker.onmessage = function (e) {
            func(e.data);
        };
    }
    function endLessonCountDownTimer(curDate, endTime) {
        countDownTimer(curDate, endTime, function (w_res_data) {
            if (w_res_data) {
                $('#end_lesson_timer').show();
                if (lesson_joined) {
                    $('#endL').show();
                }
            } else {
                $('#end_lesson_timer').hide();
            }
            $('#end_lesson_timer').html(w_res_data);
        });
    }
    function endLessonConfirm() {
        $.confirm({
            closeIcon: true,
            title: langLbl.Confirm,
            content: '<?php echo Label::getLabel('LBL_Duration_assigned_to_this_lesson_is_completed_now_do_you_want_to_continue?'); ?>',
            autoClose: langLbl.Quit + '|8000',
            buttons: {
                Proceed: {
                    text: '<?php echo Label::getLabel('LBL_End_Lesson'); ?>',
                    btnClass: 'btn btn--primary',
                    keys: ['enter', 'shift'],
                    action: function () {
                        endLessonSetup('<?php echo $lesson['slesson_id']; ?>');
                    }
                },
                Quit: {
                    text: '<?php echo Label::getLabel('LBL_Continue'); ?>',
                    btnClass: 'btn btn--secondary',
                    keys: ['enter', 'shift'],
                    action: function () {}
                }
            }
        });
    }
    $(function () {
<?php if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) { ?>
            if (curDate < startTime) {
                countDownTimer(curDate, startTime, function (w_res_data) {
                    $('#start_lesson_timer').html(w_res_data);
                    if (w_res_data) {
                        $('.timer.start-lesson-timer').show();
                    } else {
                        $('.timer.start-lesson-timer,.reschedule-lesson--js,.cancel-lesson--js').hide();
                        $(".join_lesson_now").show();
                        endLessonCountDownTimer(startTime, endTime);
                        checkEveryMinuteStatus();
                    }
                });
            } else if (endTime && curDate < endTime && canEnd && !lesson_completed) {
                endLessonCountDownTimer(curDate, endTime)
            }
<?php } ?>
        if (is_time_up == '1' && canEnd && lesson_joined && !lesson_completed) {
            endLessonConfirm();
        }
        $('body').addClass('is-screen-on');
        $(".tabs-content-js").hide();
        $(".tabs-js li:first").addClass("is-active").show();
        $(".tabs-content-js:first").show();
        $(".tabs-js li").click(function () {
            $(".tabs-js li").removeClass("is-active");
            $(this).addClass("is-active");
            $(".tabs-content-js").hide();
            var activeTab = $(this).find("a").attr("href");
            $(activeTab).fadeIn();
            return false;
        });
        /* checkEveryMinuteStatus(); */
    });
</script>