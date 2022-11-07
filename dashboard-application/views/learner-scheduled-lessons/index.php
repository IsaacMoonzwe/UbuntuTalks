<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <h1><?php echo Label::getLabel('LBL_Manage_Lessons'); ?></h1>
    </div>
    <div class="page__body">
        <?php
        if (!empty($upcomingLesson)) {
            $user_timezone = MyDate::getUserTimeZone();
            $curDate = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
            $lessonStartTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $upcomingLesson['slesson_date'] . ' ' . $upcomingLesson['slesson_start_time'], true, $user_timezone);
            $startUnixTime = strtotime($lessonStartTime);
            ?>
            <!-- [ INFO BAR ========= -->
            <div class="infobar infobar--primary">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-8 col-sm-6">
                        <div class="d-flex align-items-lg-center">
                            <div class="infobar__media margin-right-5">
                                <div class="infobar__media-icon infobar__media-icon--vcamera ">
                                    <svg class="icon icon--vcamera"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#video-camera'; ?>"></use></svg>
                                </div>
                            </div>
                            <div class="infobar__content">
                                <div class="upcoming-lesson display-inline">
                                    <?php echo Label::getLabel('LBL_Next_Lesson:'); ?> <date class=" bold-600"> <?php echo date('M d, Y', $startUnixTime); ?></date> <?php echo Label::getLabel('LBL_At'); ?> <time class=". bold-600"><?php echo date('h:i A', $startUnixTime); ?></time>
                                    <?php echo Label::getLabel('LBL_with'); ?>
                                    <div class="avtar-meta display-inline"  >
                                        <span class="avtar avtar--xsmall display-inline margin-right-2" data-title="<?php echo CommonHelper::getFirstChar($upcomingLesson['teacherFname']); ?>">
                                            <?php
                                            if (true == User::isProfilePicUploaded($upcomingLesson['teacherId'])) {
                                                $img = CommonHelper::generateUrl('Image', 'user', array($upcomingLesson['teacherId'], 'normal', 1), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                                echo '<img src="' . $img . '" />';
                                            }
                                            ?>
                                        </span>
                                        <?php echo $upcomingLesson['teacherFname']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="upcoming-lesson-action d-flex align-items-center justify-content-between justify-content-sm-end">
                            <div class="timer margin-right-4">
                                <div class="timer__media"><span><svg class="icon icon--clock icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock'; ?>"></use></svg></span></div>
                                <div class="timer__content">
                                    <div class="timer__controls color-white timer-js" id="countdowntimer-upcoming" data-startTime="<?php echo $curDate; ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startUnixTime); ?>">
                                        <!-- <div class="timer__digit">00</div>
                                        <div class="timer__digit">01</div>
                                        <div class="timer__digit">24</div>
                                        <div class="timer__digit">47</div> -->
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons', 'view', [$upcomingLesson['sldetail_id']]); ?>" class="btn bg-secondary"><?php echo Label::getLabel('LBL_Enter_Classroom') ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ] -->
        <?php } ?>
        <!-- [ PAGE PANEL ========= -->
        <div class="page-filter">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-9 col-6">
                    <!-- [ FILTERS ========= -->
                    <?php
                    $frmSrch->setFormTagAttribute('onsubmit', 'searchAllStatusLessons(this); return(false);');
                    $frmSrch->setFormTagAttribute('class', 'form');
                    $frmSrch->setFormTagAttribute('id', 'frmSrch');
                    $fldStatus = $frmSrch->getField('status');
                    $fldStatus->addFieldTagAttribute('id', 'lesson-status');
                    $fldStatus->addFieldTagAttribute('onChange', 'getLessonsByStatus(this.value)');
                    $fldStatus->addFieldTagAttribute('class', 'd-none');
                    $statusOptions = $fldStatus->options;
                    $fldSubmit = $frmSrch->getField('btn_submit');
                    $btnReset = $frmSrch->getField('btn_reset');
                    $btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
                    $classType = $frmSrch->getField('class_type');
                    $classType->addFieldTagAttribute('form', $frmSrch->getFormTagAttribute('id'));
                    $classType->addFieldTagAttribute('onChange', 'searchAllStatusLessons(this.form); return(false);');
                    ?>
                    <div class="filter-responsive slide-target-js">
                        <div class="form-inline">
                            <div class="form-inline__item">
                                <select id="<?php echo $fldStatus->getFieldTagAttribute('id'); ?>" name="<?php echo $fldStatus->getName(); ?>" onChange='getLessonsByStatus(this.value);' form="<?php echo $frmSrch->getFormTagAttribute('id'); ?>">
                                    <option value=''><?php echo Label::getLabel('L_ALL'); ?></option>
                                    <?php
                                    unset($statusOptions[ScheduledLesson::STATUS_RESCHEDULED]);
                                    $statusOptions[ScheduledLesson::STATUS_SCHEDULED] = Label::getLabel('LBL_Scheduled/Rescheduled');
                                    foreach ($statusOptions as $key => $value) {
                                        ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-inline__item">
                                <?php echo $classType->getHTML('class_type'); ?>
                            </div>
                            <div class="form-inline__item">
                                <?php echo $frmSrch->getFormTag(); ?>
                                <div class="search-form">
                                    <div class="search-form__field">
                                        <?php
                                        echo $frmSrch->getFieldHTML('keyword');
                                        echo $frmSrch->getFieldHTML('page');
                                        ?>
                                    </div>
                                    <div class="search-form__action search-form__action--submit">
                                        <?php echo $frmSrch->getFieldHTML('btn_submit'); ?>
                                        <span class="btn btn--equal btn--transparent color-black">
                                            <svg class="icon icon--search icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use></svg>
                                        </span>
                                    </div>
                                    <div class="search-form__action search-form__action--reset">
                                        <?php echo $frmSrch->getFieldHTML('btn_reset'); ?>
                                        <span class="close"></span>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- ] ========= -->
                    <a href="javascript:void(0)" class="btn bg-yellow btn--filters slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#filter'; ?>"></use></svg>
                        <?php echo Label::getLabel('LBL_Filters') ?>
                    </a>
                </div>
                <div class="col-auto">
                    <div class="tab-switch tab-switch--icons">
                        <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons'); ?>" class="tab-switch__item is-active list-js">
                            <svg class="icon icon--view icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#lesson-view'; ?>"></use></svg>
                            <?php echo Label::getLabel('LBL_List'); ?>
                        </a>
                        <a href="javascript:void(0);" onclick="viewCalendar();" class="tab-switch__item calender-js">
                            <svg class="icon icon--calendar"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#calendar'; ?>"></use></svg>
                            <?php echo Label::getLabel('LBL_Calendar'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content" id="listItemsLessons">
        </div>
        <!-- ] -->
    </div>
    <?php if (!empty($upcomingLesson)) { ?>
        <script>
            jQuery(document).ready(function () {
                $("#countdowntimer-upcoming").countdowntimer({
                    startDate: $("#countdowntimer-upcoming").attr('data-startTime'),
                    dateAndTime: $("#countdowntimer-upcoming").attr('data-endTime'),
                    size: "sm",
                });
            });
        </script>
    <?php } ?>