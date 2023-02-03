<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
MyDate::setUserTimeZone();
$user_timezone = MyDate::getUserTimeZone();
$date = new DateTime("now", new DateTimeZone($user_timezone));
$curDate = $date->format('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 days', strtotime($curDate)));
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
$myDate = new Mydate();
$myDate->setMonthAndweekName($siteLangId);
?>
<style>
    .actions-group {
        gap: 0px;
    }

    p.actions-title {
        margin: 0;
        font-size: 12px;
        color: #000000bf;
    }

    .actions-box {
        text-align: center;
        width: 65px;
    }
</style>
<div class="results">
    <?php foreach ($lessonArr as $key => $lessons) { ?>
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
                $lessonsStatus = $statusArr[$lesson['slesson_status']];
                $endDateTimeYMD = $lesson['slesson_end_date'] . " " . $lesson['slesson_end_time'];
                $canEdit = ($lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) || (($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) && (strtotime($endDateTimeYMD) > strtotime($date->format('Y-m-d H:i:s')))) || (strtotime($endDateTimeYMD) < strtotime($date->format('Y-m-d H:i:s')));
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
                $teachLang = Label::getLabel('LBL_Trial');
                if ($lesson['is_trial'] == applicationConstants::NO) {
                    $teachLang = empty($teachLanguages[$lesson['slesson_slanguage_id']]) ? '' : $teachLanguages[$lesson['slesson_slanguage_id']];
                }
            ?>
                <!-- [ LESSON CARD ========= -->
                <div class="card-landscape">
                    <div class="card-landscape__colum card-landscape__colum--first">
                        <?php
                        $lessonsStartTime = $lesson['slesson_date'] . " " . $lesson['slesson_start_time'];
                        $startTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $lessonsStartTime, true, $user_timezone);
                        $startUnixTime = strtotime($startTime);
                        if ($lesson['slesson_date'] != '0000-00-00') {
                        ?>
                            <div class="card-landscape__head">
                                <?php
                                $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $endDateTimeYMD, true, $user_timezone);
                                ?>
                                <time class="card-landscape__time"><?php echo $myDate->convertToLocal(date('h:i A', $startUnixTime)); ?></time>
                                <date class="card-landscape__date"><?php echo $myDate->convertToLocal(date('l, F d, Y', $startUnixTime)); ?></date>
                            </div>
                            <?php if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) { ?>
                                <div class="timer">
                                    <div class="timer__media"><span><svg class="icon icon--clock icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock'; ?>"></use>
                                            </svg></span></div>
                                    <div class="timer__content">
                                        <?php if ($startUnixTime > strtotime($curDateTime)) { ?>
                                            <div class="timer__controls countdowntimer timer-js" id="countdowntimer-<?php echo $lesson['slesson_id'] ?>" data-startTime="<?php echo $curDateTime; ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startUnixTime); ?>">
                                            </div>
                                        <?php
                                        } else {
                                            $lessonInfoLblKey = 'LBL_Lesson_time_has_passed';
                                            if (strtotime($endDateTime) > strtotime($curDateTime)) {
                                                $lessonInfoLblKey = 'LBL_Lesson_ongoing';
                                            }
                                            echo '<span class="color-red">' . Label::getLabel($lessonInfoLblKey) . '</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
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
                            <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo $lessonsStatus; ?></span>
                            <?php if ($lesson['slesson_grpcls_id'] > 0 && $lesson['slesson_kids_class'] <= 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_GROUP_CLASS'); ?></span>
                            <?php } ?>
                            <?php if ($lesson['slesson_grpcls_id'] > 0 && $lesson['slesson_kids_class'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_KIDS_CLASS'); ?></span>
                            <?php } ?>

                            <?php if ($lesson['repiss_id'] > 0) { ?>
                                <span class="card-landscape__status badge color-primary badge--curve badge--small margin-left-0"><?php echo Label::getLabel('LBL_Issue_Reported'); ?></span>
                            <?php } ?>
                        </div>
                        <?php
                        if ($lesson['slesson_status'] != ScheduledLesson::STATUS_CANCELLED) {
                            if ($lesson['tlpn_id'] > 0) {
                        ?>
                                <div class="card-landscape__docs">
                                    <div class="d-flex align-items-center">
                                        <a href="javascript:void(0);" onclick="viewAssignedLessonPlan('<?php echo $lesson['slesson_id']; ?>')" class="attachment-file">
                                            <svg class="icon icon--issue icon--attachement icon--xsmall color-black">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#attach'; ?>"></use>
                                            </svg>
                                            <?php echo $lesson['tlpn_title'] ?>
                                        </a>
                                        <a href="javascript:void(0);" onclick="changeLessonPlan('<?php echo $lesson['slesson_id']; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_Change'); ?></a>
                                        <a href="javascript:void(0);" onclick="removeAssignedLessonPlan('<?php echo $lesson['slesson_id']; ?>');" class="underline color-black  btn btn--transparent btn--small"><?php echo Label::getLabel('LBL_Remove'); ?></a>
                                    </div>
                                </div>
                            <?php } else if ($canEdit) { ?>
                                <div class="card-landscape__docs">
                                    <a href="javascript:void(0);" onclick="listLessonPlans('<?php echo $lesson['slesson_id']; ?>');" class="btn btn--transparent btn--addition color-black btn--small"><?php echo Label::getLabel('LBL_Add_Lesson_Plan'); ?></a>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <div class="card-landscape__colum card-landscape__colum--third">
                        <div class="card-landscape__actions">
                            <div class="profile-meta">
                                <?php if ($lesson['slesson_grpcls_id'] <= 0) { ?>
                                    <div class="profile-meta__media">
                                        <span class="avtar" data-title="<?php echo CommonHelper::getFirstChar($lesson['learnerFname']); ?>">
                                            <?php
                                            if (true == User::isProfilePicUploaded($lesson['learnerId'])) {
                                                $img = CommonHelper::generateUrl('Image', 'user', array($lesson['learnerId']), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                                echo '<img src="' . $img . '" />';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="profile-meta__details">
                                        <p class="bold-600 color-black"><?php echo $lesson['learnerFname']; ?></p>
                                        <p class="small"><?php echo $lesson['learnerCountryName']; ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="actions-group">
                                <?php if ($lesson['order_is_paid'] != Order::ORDER_IS_CANCELLED) { ?>
                                    <div class="actions-box">
                                        <p class="actions-title">Class</p>
                                        <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons', 'view', [$lesson['slesson_id']]); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--enter icon--18">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#enter'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Enter_Classroom'); ?></div>
                                        </a>
                                    </div>
                                    <?php if (($lesson['slesson_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING || $lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED) && strtotime($curDateTime) < $startUnixTime) { ?>
                                        <div class="actions-box">
                                            <p class="actions-title">Cancle</p>
                                            <a href="javascript:void(0);" onclick="cancelLesson('<?php echo $lesson['slesson_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                <svg class="icon icon--cancel icon--small">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#cancel'; ?>"></use>
                                                </svg>
                                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Cancel'); ?></div>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if ($lesson['slesson_status'] == ScheduledLesson::STATUS_SCHEDULED && strtotime($curDateTime) < $startUnixTime && (MyDate::hoursDiff($lessonsStartTime) >= FatApp::getConfig('LESSON_STATUS_UPDATE_WINDOW', FatUtility::VAR_FLOAT, 24))) { ?>
                                        <div class="actions-box">
                                            <p class="actions-title">Reschedule</p>
                                            <a href="javascript:void(0);" onclick="requestReschedule('<?php echo $lesson['slesson_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                <svg class="icon icon--reschedule icon--small">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#reschedule'; ?>"></use>
                                                </svg>
                                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Reschedule'); ?></div>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if ($lesson['repiss_id'] > 0 && $lesson['slesson_grpcls_id'] > 0) { ?>
                                        <a href="<?php echo CommonHelper::generateUrl('TeacherIssueReported', 'index', [$lesson['slesson_grpcls_id']]) ?>" target="_blank" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--reschedule icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view-report'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Issue_Details'); ?></div>
                                        </a>
                                    <?php } else if ($lesson['repiss_id'] > 0) { ?>
                                        <a href="javascript:void(0);" onclick="issueDetails('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                            <svg class="icon icon--reschedule icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#view-report'; ?>"></use>
                                            </svg>
                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Issue_Details'); ?></div>
                                        </a>
                                        <?php if ($lesson['repiss_status'] == ReportedIssue::STATUS_PROGRESS) { ?>
                                            <a href="javascript:void(0);" onclick="resolveForm('<?php echo $lesson['repiss_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                <svg class="icon icon--reschedule icon--small">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#resolve-issue'; ?>"></use>
                                                </svg>
                                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Resolve_Issue'); ?></div>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
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
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    echo FatUtility::createHiddenFormFromData($postedData, ['name' => 'frmSLnsSearchPaging']);
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
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
                timeUp: reloadmMyfin,
            });

            function reloadmMyfin() {
                window.location = window.location;
                $.loader.hide();
                searchLessons(document.frmSrch);

            }

            function myfin() {
                window.location = window.location;
                tim_id = $(this).attr('id');
                myArrfay = tim_id.split("-");
                // myArrfay[1]=508;    
                var getLocalVal = localStorage.getItem('schecule_id')
                if (getLocalVal != myArrfay[1]) {
                    var formData = new FormData();
                    formData.append('myArrfay', parseInt(myArrfay[1]))
                    localStorage.setItem('schecule_id', myArrfay[1]);
                    $.ajax({
                        url: fcom.makeUrl('TeacherScheduledLessons', 'joinnow'),
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        mimeType: "multipart/form-data",
                        contentType: false,
                        processData: false,
                        success: function(data, textStatus, jqXHR) {
                            window.location = window.location;
                            $.loader.hide();
                            searchLessons(document.frmSrch);
                            setTimeout(function() {
                                $.systemMessage.close();
                            }, 2000);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $.loader.hide();
                            $.systemMessage(jqXHR.msg, true);
                        }
                    });
                }
            }
        });
    });
</script>