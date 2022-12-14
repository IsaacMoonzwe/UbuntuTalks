<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setUpTeacherRequestStatus(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$fld = $frm->getField('utrequest_status');
$fld->setFieldTagAttribute('onChange', 'showHideCommentBox(this.value)');
$fldBl = $frm->getField('utrequest_comments');
$fldBl->htmlBeforeField = '<span id="div_comments_box" class="hide">' . Label::getLabel('LBL_Reason_for_Cancellation', $adminLangId);
$fldBl->htmlAfterField = '</span>';
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Update_Status', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">      
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>						
        </div>
    </div>						
</section>
