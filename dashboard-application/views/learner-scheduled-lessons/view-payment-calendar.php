<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$lesson_duration = $lessonRow['op_lesson_duration'];
$nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
?>
<style type="text/css">
    .slot_available{
        background: lightgreen;
    }
</style>
<div id="loaderCalendar" class="calendar-loader" style="display: none;"><div class="loader"></div></div>
<div class="calendar-view payment_calendar_wrap">
    <div class="calendar-view__head">    
        <div class="row">
            <div class="col-sm-5">
                <h4><?php echo $userRow['user_first_name'] . " " . $userRow['user_last_name'] . " " . Label::getLabel('Lbl_Calendar'); ?></h4>
            </div>
            <div class="col-sm-7">
              <!--   <div class="cal-status">
                    <span class="ml-0 box-hint disabled-box">&nbsp;</span>
                    <p><?php // echo Label::getLabel('LBL_Not_Available'); ?></p>
                </div> -->
                <div class="cal-status">
                    <span class="box-hint available-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('Lbl_Available'); ?></p>
                </div>
                <div class="cal-status">
                    <span class="box-hint booked-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('Lbl_Booked'); ?></p>
                </div>
            </div>
        </div>
    </div>
<!-- (<span id="currentTime"> </span>) -->
    <?php if ('free_trial' != $action): ?>
        <div class="note note--secondary mb-5"> 
            <svg class="icon icon--explanation"><use xlink:href="/images/sprite.yo-coach.svg#explanation"></use></svg>
            <p>
                <b><?php echo Label::getLabel('LBL_SCHEDULE_Calender_Note', $siteLangId) ?></b>
                <?php //echo Label::getLabel('To book this Instructor select an available slot(s).', $siteLangId); ?>
            </p>
        </div>
    <?php endif; ?>
    <!-- <div id='calendar-container'>
        <div id='d_payment_calendar<?php// echo ($action === 'free_trial') ? 'free_trial' : ''; ?>'></div>
    </div> -->
</div>

<div class="calendar-view scheduled-lesson-popup">
    <!-- New code NR -->
<div class="calendar-view__head">
    <div id='calendar-container'>
        <div id='d_payment_calendar'></div>
    </div>
</div>
<!-- Over NR -->
    <?php if ($isRescheduleRequest) { ?>
        <div class="box">
            <h4><?php echo Label::getLabel('Lbl_Reschedule_Reason'); ?><span class="spn_must_field">*</span></h4>
            <?php
            $commentField = $rescheduleRequestfrm->getField('reschedule_lesson_msg');
            $commentField->addFieldTagAttribute('placeholder', Label::getLabel('Lbl_Reschedule_Reason_*'));
            $commentField->addFieldTagAttribute('id', 'reschedule-reason-js');
            echo $commentField->getHTML();
            ?>
	</div>
	<?php } ?>
<?php if ('free_trial' === $action) {  ?>
    <div class="tooltipevent-wrapper-js d-none">
        <div class="tooltipevent" style="position:absolute;z-index:10001;">
            <div class="booking-view">
                <div class="booking__head">
                    <h3 class="-display-inline"><?php echo $teacher_name; ?></h3>
                    <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($teacher_country_id, 'DEFAULT')); ?>" alt=""></span>
                </div>
                <div class="booking__body">
                    <div class="inline-list">
                        <div class="inline-list__value highlight tooltipevent-time-js">
                            <div>
                                <strong><?php echo Label::getLabel("LBL_Date") . ' : '; ?></strong>
                                <span>{{displayEventDate}}</span>
                            </div>
                            <div>
                                <strong><?php echo Label::getLabel("LBL_Time") . ' : '; ?></strong>
                                <span>{{displayEventTime}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="-gap-10"></div>
                    <div class="-align-left">
                        <a href="javascript:void(0);" onClick="cart.addFreeTrial(<?php echo $teacher_id; ?>, '{{selectedStartDateTime}}', '{{selectedEndDateTime}}', '<?php echo $languageId; ?>');" class="btn btn--secondary btn--small btn--wide"><?php echo Label::getLabel('LBL_Book_Lesson!'); ?></a>
                    </div>
                    <a onclick="$('body > .tooltipevent').remove();" href="javascript:;" class="-link-close"></a>
                </div>
            </div>
        </div>
    </div>
<?php }
else{
    ?>
    <div class="tooltipevent-wrapper-js d-none">
        <div class="tooltipevent" style="position:absolute;z-index:10001;">
            <div class="booking-view">
                <div class="booking__head">
                <h3 class="-display-inline"><?php echo $userRow['user_first_name']; ?></h3>
            <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($userRow['user_country_id'], 'DEFAULT'), CONF_WEBROOT_FRONTEND); ?>" alt=""></span>
                </div>
                <div class="booking__body">
                    <div class="inline-list">
                        <div class="inline-list__value highlight tooltipevent-time-js">
                            <div>
                                <strong><?php echo Label::getLabel("LBL_Date") . ' : '; ?></strong>
                                <span>{{displayEventDate}}</span>
                            </div>
                            <div>
                                <strong><?php echo Label::getLabel("LBL_Time") . ' : '; ?></strong>
                                <span>{{displayEventTime}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="-gap-10"></div>
                    <div class="-align-left">
                        <a href="javascript:void(0);" onClick="setUpLessonSchedule(<?php echo $teacher_id; ?>, <?php echo $lDetailId; ?>, '{{selectedStartDateTime}}', '{{selectedEndDateTime}}');" class="btn btn--secondary btn--small btn--wide"><?php echo Label::getLabel('LBL_Book_Lesson!'); ?></a>
                    </div>
                    <a onclick="$('body > .tooltipevent').remove();" href="javascript:;" class="-link-close"></a>
                </div>
            </div>
        </div>
    </div>
    <?php
} ?>

<script>
    var isRescheduleRequest = <?php echo (!empty($isRescheduleRequest)) ? 1 : 0; ?>;
    var checkSlotAvailabiltAjaxRun = false;
    var fecal = new FatEventCalendar(<?php echo $teacher_id; ?>);
    fecal.WeeklyBookingCalendar('<?php echo date('Y-m-d H:i:s', strtotime($nowDate)); ?>', '<?php echo gmdate("H:i", $lesson_duration * 60); ?>', <?php echo $teacherBookingBefore; ?>);
</script>
