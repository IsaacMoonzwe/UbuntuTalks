<?php
    defined('SYSTEM_INIT') or die('Invalid Usage.');
    $frm->setFormTagAttribute('class', 'web_form form_horizontal');
    $frm->setFormTagAttribute('onsubmit', 'setupTestimonial(this); return(false);');
    $frm->developerTags['colClassPrefix'] = 'col-md-';
    $frm->developerTags['fld_default_col'] = 12;
    $dateformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
    $timeformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
   // $frm->getField('agenda_start_time')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);

?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Testimonial_Setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="editTestimonialForm(<?php echo $testimonial_id ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
