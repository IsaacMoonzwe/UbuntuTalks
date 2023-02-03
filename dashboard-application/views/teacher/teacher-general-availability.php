<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$user_timezone = MyDate::getUserTimeZone();
$nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
?>
<div class="page-panel__head">
    <div class="row align-items-center justify-content-between">
        <div class="col-6">
            <div class="tab-switch">
                <a href="javascript:void(0);" class="tab-switch__item is-active"><?php echo Label::getLabel('LBL_General'); ?></a>
                <a href="javascript:void(0);" class="tab-switch__item" onclick="teacherWeeklySchedule()"><?php echo Label::getLabel('LBL_Weekly'); ?></a>
            </div>
        </div>
        <div class="col-lg-auto col-auto">
            <input type="button"  onclick="saveGeneralAvailability();" value="<?php echo Label::getLabel('LBL_Save'); ?>" class="btn bg-primary">
        </div>
    </div>
</div>
<div class="page-panel__body availability-setting-calendar" id='calendar-container'>
    <div id='ga_calendar' class="calendar-view availability-calendar general-calendar"></div>
</div>
<script>
    var fecal = new FatEventCalendar(<?php echo $userId; ?>);
    var calendar = fecal.TeacherGeneralAvailaibility('<?php echo date('Y-m-d H:i:s', strtotime($nowDate)); ?>');
</script>
