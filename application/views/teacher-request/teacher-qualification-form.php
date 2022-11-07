<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--narrow">
    <?php
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'setUpTeacherQualification(this); return(false);');

    $fldExpType = $frm->getField('uqualification_experience_type');
    $fldExpType->developerTags['col'] = 6;

    $fldUqualificationTitle = $frm->getField('uqualification_title');
    $fldUqualificationTitle->developerTags['col'] = 6;

    $fldUqualificationInstituteName = $frm->getField('uqualification_institute_name');
    $fldUqualificationInstituteName->developerTags['col'] = 6;

    $fldUqualificationInstituteAddress = $frm->getField('uqualification_institute_address');
    $fldUqualificationInstituteAddress->developerTags['col'] = 6;

    $fldUqualificationStartYear = $frm->getField('uqualification_start_year');
    $fldUqualificationStartYear->developerTags['col'] = 6;

    $fldUqualificationEndYear = $frm->getField('uqualification_end_year');
    $fldUqualificationEndYear->developerTags['col'] = 6;

    $fldCertificate = $frm->getField('certificate');
    $fldCertificate->developerTags['col'] = 6;

    $fldBtnSubmit = $frm->getField('btn_submit');
    $fldBtnSubmit->developerTags['col'] = 6;

    echo $frm->getFormHtml();
    ?>
</div>