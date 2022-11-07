<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('get-a-quote', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<style>
    /* .field_services select { height:60px !important; } */
    .field_cover.field_services input[type="file"] { padding: 15px !important; }
    input[type=email], input[type=file], input[type=number], input[type=password], input[type=phone], input[type=search], input[type=text], select, .switch-group{
        height:2.8rem !important;}
    /* .section.contact-form { width: 80% !important; margin: 0 auto !important; } */
    /* @media (min-width: 320px) and (max-width: 767px) {
        .section.contact-form { width: 100% !important; margin: 0 auto !important; }
    }
    @media (min-width: 768px) and (max-width: 1023px) {
        .section.contact-form { width: 70% !important; margin: 0 auto !important; }
    } */
</style>
<section class="section section--contect">
<div class="banner__media -hide-mobile"><img src="<?php //echo CommonHelper::generateUrl('Image', 'quote', [$siteLangId]); ?>" alt=""></div>
    <div class="container container--fixed container--narrow">
        <?php echo FatUtility::decodeHtmlEntities($quoteBanner); ?>
        <div class="section contact-form">
        <div class="banner__media -hide-mobile container" style="margin-bottom:50px;">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'quote', [$siteLangId]); ?>" alt="">
        </div>
            <div class="container container--narrow">
                <div class="row">
                    <?php //echo FatUtility::decodeHtmlEntities($contactLeftSection); ?>
                    <!-- <div class="col-md-7 col-lg-6 offset-lg-2"> -->
                    <div class="col-md-8" style="margin:0 auto !important;">
                        <div class="contact-form">
                            <?php echo $contactFrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?></label>
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
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('lname'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Address', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('address'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Street_Address', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('streetaddress'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_City', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('city'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_State_/_Province_/_Region', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('state'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_ZIP_Code_/_Postal_Code', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('zipcode'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Country', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover field_services">
                                                <?php echo $contactFrm->getFieldHTML('country'); ?>
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
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Field_Services', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover field_services">
                                                <?php echo $contactFrm->getFieldHTML('services'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Source_Language', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('sourcelanguage'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Target_Language', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('targetlanguage'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Needed_Date', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $contactFrm->getFieldHTML('neededdate'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_File_Upload', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover field_services">
                                                <?php echo $contactFrm->getFieldHTML('fileupload'); ?>
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