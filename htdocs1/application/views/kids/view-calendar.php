<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
?>
<div id="loaderCalendar" class="calendar-loader" style="display: none;"><div class="loader"></div></div>
<div class="calendar-view">
    <div class="calendar-view__head">    
        <div class="row">
            <div class="col-sm-5">
                <h4><?php echo $userRow['user_first_name'] . " " . $userRow['user_last_name'] . " " . Label::getLabel('Lbl_Calendar'); ?></h4>
            </div>
            <div class="col-sm-7">
                <div class="cal-status">
                    <span class="ml-0 box-hint disabled-box">&nbsp;</span>
                    <p><?php echo Label::getLabel('LBL_Not_Available'); ?></p>
                </div>
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
        <div class="note note--secondary mb-5"> <svg class="icon icon--explanation"><use xlink:href="/images/sprite.yo-coach.svg#explanation"></use></svg><p><b><?php echo Label::getLabel('LBL_Note:', $siteLangId) ?></b><?php echo Label::getLabel('This_calendar_is_to_only_check_availability', $siteLangId); ?></p></div>
    <?php endif; ?>
    <div id='calendar-container'>
        <div id='d_calendar<?php echo ($action === 'free_trial') ? 'free_trial' : ''; ?>'></div>
    </div>
</div>
<?php if ('free_trial' === $action) { ?>
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
<?php } ?>
<script>
    var fecal = new FatEventCalendar(<?php echo $teacher_id ?>);
    fecal.AvailaibilityCalendar('<?php echo date('Y-m-d H:i:s', strtotime($nowDate)); ?>', '<?php echo $bookingSnapDuration; ?>', '<?php echo $teacherBookingBefore; ?>', <?php echo 'free_trial' === $action; ?>);
</script>
