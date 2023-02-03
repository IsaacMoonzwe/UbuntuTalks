<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$experienceFrm->setFormTagAttribute('id', 'experienceFrm');
$experienceFrm->setFormTagAttribute('class', 'form');
$experienceFrm->setFormTagAttribute('onsubmit', 'setUpTeacherQualification(this); return(false);');
$experienceFrm->developerTags['colClassPrefix'] = 'col-md-';
$experienceFrm->developerTags['fld_default_col'] = 12;
$uqualification_experience_type = $experienceFrm->getField('uqualification_experience_type');
$uqualification_experience_type->developerTags['col'] = 6;
$uqualification_institute_address = $experienceFrm->getField('uqualification_institute_address');
$uqualification_institute_address->developerTags['col'] = 6;
$uqualification_institute_name = $experienceFrm->getField('uqualification_institute_name');
$uqualification_institute_name->developerTags['col'] = 6;
$uqualification_start_year = $experienceFrm->getField('uqualification_start_year');
$uqualification_start_year->developerTags['col'] = 6;
$uqualification_end_year = $experienceFrm->getField('uqualification_end_year');
$uqualification_end_year->developerTags['col'] = 6;
$uqualification_title = $experienceFrm->getField('uqualification_title');
$uqualification_title->developerTags['col'] = 6;
$certificate = $experienceFrm->getField('certificate');
$certificate->developerTags['col'] = 6;
$btn_submit = $experienceFrm->getField('btn_submit');
$btn_submit->developerTags['col'] = 6;
?>
<div class="box -padding-20">                         
    <div class="section-head">
        <div class="d-flex justify-content-between align-items-center">
            <div><h5 class="page-heading"><?php echo Label::getLabel('LBL_Resume'); ?></h5></div>
        </div>
    </div>
    <?php echo $experienceFrm->getFormHtml(); ?>
</div>
