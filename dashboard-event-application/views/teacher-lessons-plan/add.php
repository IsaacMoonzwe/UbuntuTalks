<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'lessonPlanFrm');
$frm->setFormTagAttribute('enctype', 'multipart/form-data');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$flanFileFld = $frm->getField('tlpn_file[]');
// $flanFileFld->addFieldTagAttribute('onchange', 'checkFileSize(this,3);');
$imgFld = $frm->getField('tlpn_image');
// $imgFld->addFieldTagAttribute('onchange', 'checkFileSize(this,1);');
if ($lessonPlanId > 0) {
    $tlpn_file_display = $frm->getField('tlpn_file_display');
    $file_rows = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_LESSON_PLAN_FILE, $lessonPlanId, 0);
    if ($file_rows) {
        $files = '<div class="field-set filelink">';
        foreach ($file_rows as $file_row) {
            $files .= '<span class="tag"><span><a target="_blank" href=' . CommonHelper::generateFullUrl('TeacherLessonsPlan', 'getFileById', array($file_row['afile_id'])) . '?' . time() . '>' . ucwords($file_row['afile_name']) . '&nbsp;</a></span><a href="javascript:void(0);" onclick="removeFile(this,' . $file_row['afile_id'] . ')">x</a></span>&nbsp;';
        }
        $files .= "</div>";
    } else {
        $files = '';
    }
    $tlpn_file_display->value = $files;
}
?>
<div class="box -padding-20">
    <?php echo $frm->getFormHtml(); ?>
</div>