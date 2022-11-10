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
<style>
    .rc-anchor-normal {
        width: 335px !important;
    }
</style>
<section class="section section--contect contact-page">
    <div class="banner__media -hide-mobile"><img src="<?php //echo CommonHelper::generateUrl('Image', 'contact', [$siteLangId]); 
                                                        ?>" alt=""></div>
    <div class="container container--fixed container--narrow">
        <?php echo FatUtility::decodeHtmlEntities($contactBanner); ?>
        <div class="section contact-form">
            <!-- <div class="banner__media -hide-mobile container" style="margin-bottom:50px;">
                <img src="<?php echo CommonHelper::generateUrl('Image', 'contact', [$siteLangId]); 
                            ?>" alt="">
            </div> -->
            <div class="container container--narrow">
                <a href="/" class="contact-logo-img">
                    <img src="https://ubuntutalks.com/image/editor-image/1659933871-450.png" alt="Ubuntu Talks logo">
                </a>
                <div class="row">
                    <?php echo FatUtility::decodeHtmlEntities($contactLeftSection); ?>
                    <div class="col-md-6 col-lg-6">
                        <div class="contact-form">
                            <?php echo $contactFrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('name'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Phone_no', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('phone'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Email', $siteLangId); ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('email'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Message', $siteLangId); ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
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