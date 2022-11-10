<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
// $contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('teachers', 'contactSubmit'));
$contactFrm->setFormTagAttribute('onSubmit', 'contactSubmit(this); return false;');
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';  
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<style type="text/css">
    .slot_available{ background: lightgreen;}
</style>
<div class="section contact-form request-booking-form hide-show-booking-form">
    <div class="request-title">
        <?php echo Label::getLabel('LBL_Request_Booking_Form') ?>
        <span aria-hidden="true" class="closebutton">×</span>
    </div>
        <div class="container container--narrow">
            <div class="row">
                <div class="col-md-12">
                    <div class="contact-form">
                        <?php echo $contactFrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('fname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('lname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Email', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('email'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Language', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('langauge'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Ages', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('ages'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <?php if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '') { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="field-wraper">
                                                <div class="g-recaptcha" data-sitekey="<?php echo FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, ''); ?>"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('btn_submit'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo $contactFrm->getExternalJS(); ?>
                            </form>
                    </div>
                </div>
            </div>
        </div>
</div>
<div id="loaderCalendar" class="calendar-loader" style="display: none;"><div class="loader"></div></div>
<div class="calendar-view">
    <div class="calendar-view__head">    
        <div class="request-another-time"> 
            <p>
                <b><?php echo Label::getLabel('LBL_REQUSET_ANOTHER_TIME', $siteLangId) ?></b>
            </p>
        </div>
        <div class="time-booking-form">
            <a href="javascript:void(0);" onclick="" class="btn btn--primary btn--large color-white kids-bookbtn" tabindex="0">Request Another Time</a>
        </div>
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
                <b><?php echo Label::getLabel('LBL_TEACHER_Calender_Note', $siteLangId) ?></b>
                <?php //echo Label::getLabel('This_calendar_is_to_only_check_availability', $siteLangId); ?>
            </p>
        </div>
    <?php endif; ?>
    <div id='calendar-container'>
        <div class="view_availability" id='d_calendar<?php echo ($action === 'free_trial') ? 'free_trial' : ''; ?>'></div>
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
    $(document).ready(function(){
        $(".time-booking-form .kids-bookbtn").click(function(){
            $(".section.contact-form.request-booking-form").removeClass("hide-show-booking-form");
            $(".calendar-view").addClass("hide-show-calender");
        });

        $("span.closebutton").click(function(){
            $(".calendar-view").removeClass("hide-show-calender");
            $(".section.contact-form.request-booking-form").addClass("hide-show-booking-form");
            
        });
    });
</script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
    var fecal = new FatEventCalendar(<?php echo $teacher_id ?>);
    fecal.AvailaibilityCalendar('<?php echo date('Y-m-d H:i:s', strtotime($nowDate)); ?>', '<?php echo $bookingSnapDuration; ?>', '<?php echo $teacherBookingBefore; ?>', <?php echo 'free_trial' === $action; ?>);
</script>
