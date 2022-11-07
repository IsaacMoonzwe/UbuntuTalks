<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Medical', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section section--contect contact-page referral-campgian">
    <div class="container">
        <div class="section contact-form">
            <div class="container">
                <div class="row align-items-center">
                    <!-- <div class="col-lg-6">
                        <div class="banner__media_content">
                            <?php //echo FatUtility::decodeHtmlEntities($ReferralCampaignBanner); ?>
                        </div>
                    </div> -->
                    <div class="col-lg-12 banner-images">
                        <div class="banner__media ">
                            <img src="<?php echo CommonHelper::generateUrl('Image', 'MedicalCampaign', [$siteLangId]); ?>" alt="">
                        </div>
                    </div>
                </div>
                <div class="row align-items-center referral-campaign-form">
                    <div class="col-lg-6">
                        <div class="banner__media_content">
                            <?php echo FatUtility::decodeHtmlEntities($MedicalCampaignLeftSectionBanner); ?>
                        </div>
                    </div>
                    <div class="col-lg-1">
                    </div>
                    <div class="col-lg-5">
                        <div class="banner__media ">
                        <div class="contact-form">
                            <?php echo $contactFrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Your_Email', $siteLangId); ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('email'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Your_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('name'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Your_Contact\'s_First_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('pfname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Your_Contact\'s_Last_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('plname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Your_Contact\'s_Corporate_Email', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('pcemail'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Company_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('cpcname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Contact\'s_Job_Function', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover field_services">
                                                <?php echo $contactFrm->getFieldHTML('pjname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Let_us_know_your_specific_needs', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover comment-section">
                                                <?php echo $contactFrm->getFieldHTML('message'); ?>
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
                <?php echo FatUtility::decodeHtmlEntities($MedicalCampaignBenifitsBanner); ?>
            </div>
        </div>
    </div>
</section>
<!-- <div class="section section--grey">
             <div class="container container--fixed">
                 <div class="row justify-content-center">
                     <div class="col-xl-6 col-lg-8 col-md-10">
                            <div class="form-container">
                                <h3 class="-align-center"><?php echo Label::getLabel('LBL_Send_us_a_message'); ?></h3>
                                <span class="-gap"></span>
<?php echo $contactFrm->getFormHtml(); ?>
                            </div>
                         </div>
                 </div>
             </div>
         </div> -->
<script src='https://www.google.com/recaptcha/api.js'></script>