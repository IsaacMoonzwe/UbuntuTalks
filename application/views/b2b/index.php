<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Referral', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="container container--narrow">
        <div class="main__title">
            <h1><?php echo Label::getLabel('LBL_Field_Services', $siteLangId) ?></h1>
        </div>
        <div class="who-we__content">
            <?php echo FatUtility::decodeHtmlEntities($B2bBanner); ?>     
        </div>
    </div>
</section>