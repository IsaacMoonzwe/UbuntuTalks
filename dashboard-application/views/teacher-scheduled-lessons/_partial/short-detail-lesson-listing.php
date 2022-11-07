<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
MyDate::setUserTimeZone();
$user_timezone = MyDate::getUserTimeZone();
$date = new DateTime("now", new DateTimeZone($user_timezone));
$curDate = $date->format('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 days', strtotime($curDate)));
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
?>
<!-- <div class="scrollbar scrollbar-js"> -->
<?php
$myDate = new MyDate();
$myDate->setMonthAndweekName($siteLangId);
foreach ($lessonArr as $key => $lessons) {
    ?>
    <div class="lesson-list-container">
        <?php if ($key != '0000-00-00') { ?>
            <div class="lesson-list_head">
                <div class="date">
                    <p>
                        <?php
                        if (strtotime($curDate) == strtotime($key)) {
                            echo Label::getLabel('LBL_Today');
                        } elseif (strtotime($nextDate) == strtotime($key)) {
                            echo Label::getLabel('LBL_Tommorrow');
                        } else {
                            echo $myDate->convertToLocal(date('l, F d, Y', strtotime($key)));
                        }
                        ?>
                    </p>
                </div>
                </date>
            <?php } ?>
            <?php
            foreach ($lessons as $lesson) {
                $lessonsStatus = $statusArr[$lesson['sldetail_learner_status']];
                if (FatUtility::int($lesson['lesreschlog_id']) > 0 && ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING || $lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED)) {
                    $lessonsStatus = Label::getLabel('LBL_Rescheduled');
                    if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING) {
                        $lessonsStatus = Label::getLabel('LBL_Pending_for_Reschedule');
                    }
                }
                $teachLang = Label::getLabel('LBL_Trial');
                if ($lesson['is_trial'] == applicationConstants::NO) {
                    $teachLang = empty($teachLanguages[$lesson['slesson_slanguage_id']]) ? '' : $teachLanguages[$lesson['slesson_slanguage_id']];
                }
                if ($lesson['slesson_grpcls_id'] > 0) {
                    $teachLang = $lesson['grpcls_title'];
                }
                ?>
                <div class="lesson-list short-details">
                    <div class="lesson-list__left">
                        <div class="avtar avtar--small avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($lesson['learnerFname']); ?>">
                            <?php
                            if (true == User::isProfilePicUploaded($lesson['learnerId'])) {
                                $img = CommonHelper::generateUrl('Image', 'user', array($lesson['learnerId']), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                echo '<img src="' . $img . '" alt="' . $lesson['learnerFname'] . '" />';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="lesson-list__right">
                        <p><?php echo $lesson['learnerFname']; ?></p>
                        <p class="lesson-time">
                            <?php
                            if ($lesson['slesson_date'] != '0000-00-00') {
                                $lessonsStartTime = $lesson['slesson_date'] . " " . $lesson['slesson_start_time'];
                                $startTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $lessonsStartTime, true, $user_timezone);
                                $startUnixTime = strtotime($startTime);
                                ?>
                                <span>
                                    <?php echo $myDate->convertToLocal(date('h:i A', $startUnixTime)); ?>
                                </span>
                            <?php } ?>
                            <?php
                            $str = Label::getLabel('LBL_{teach-lang},{n}_minutes_of_Lesson');
                            echo str_replace(['{teach-lang}', '{n}'], [$teachLang, $lesson['op_lesson_duration']], $str);
                            ?>
                        </p>
                    </div>
                    <?php if ($lesson['order_is_paid'] != Order::ORDER_IS_CANCELLED) { ?>
                        <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons', 'view', [$lesson['slesson_id']]); ?>" class="lesson-list__action"></a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <!-- </div>  -->
    <?php
    if (empty($lessonArr)) {
        $variables['btn'] = '<a href="' . CommonHelper::generateFullUrl('TeacherScheduledLessons') . '" class="btn bg-primary">' . Label::getLabel('LBL_View_All_Lessons') . '</a>';
        $variables['msgHeading'] = Label::getLabel('LBL_No_Upcoming_lessons!!');
        $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    }
