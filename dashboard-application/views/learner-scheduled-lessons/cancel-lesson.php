<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'bankInfoFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'cancelLessonSetup(this); return(false);');
$cancelBtn = $frm->getField('reset');
$cancelBtn->setFieldTagAttribute('onclick', 'closeCancelLessonPopup(this); return(false);');
if ($lessonRow['slesson_grpcls_id'] > 0 && $frm->getField('note_text')) {
    $frm->getField('note_text')->value = '<spam class="-color-primary">' . sprintf(Label::getLabel('LBL_Note:_Refund_Would_Be_%s_Percent.', $siteLangId), FatApp::getConfig('CONF_LEARNER_CLASS_REFUND_PERCENTAGE', FatUtility::VAR_INT, 10)) . '</spam>';
}
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_Cancel_Lesson'); ?></h4>
    <?php echo $frm->getFormHtml(); ?>
</div>
