<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('contact', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section section--contect contact-page company-classes">
<div class="banner__media -hide-mobile"><img src="<?php //echo CommonHelper::generateUrl('Image', 'contact', [$siteLangId]); ?>" alt=""></div>
    <div class="container container--fixed container--narrow">
        <div class="section contact-form">
            <div class="banner__media -hide-mobile container" style="margin-bottom:50px;">
                <h1 class="classes-title"><?php echo Label::getLabel('LBL_UBUNTU_TALKS_GROUP_LESSONS'); ?></h1>
                <div class="page-content">
                    <div class="results" id="listItemsLessons">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src='https://www.google.com/recaptcha/api.js'></script>
