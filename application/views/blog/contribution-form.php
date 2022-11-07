<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
/* $frm->setFormTagAttribute('onsubmit','setupContribution(this);return false;'); */
$frm->setFormTagAttribute('action', CommonHelper::generateUrl('Blog', 'setupContribution'));
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = '12';
$bcontributions_author_first_name = $frm->getField('bcontributions_author_first_name');
$bcontributions_author_first_name->developerTags['col'] = 6;
$bcontributions_author_last_name = $frm->getField('bcontributions_author_last_name');
$bcontributions_author_last_name->developerTags['col'] = 6;
$bcontributions_author_email = $frm->getField('bcontributions_author_email');
$bcontributions_author_email->developerTags['col'] = 6;
$bcontributions_author_phone = $frm->getField('bcontributions_author_phone');
$bcontributions_author_phone->developerTags['col'] = 6;
$btn_submit = $frm->getField('btn_submit');
$btn_submit->setFieldTagAttribute('class', 'btn btn--primary btn--block btn--large');
$btn_submit->developerTags['col'] = 12;
$fileFld = $frm->getField('file');
$preferredDimensionsStr = '<small class="text--small">' . Label::getLabel('MSG_Allowed_Extensions', $siteLangId) . '</small>';
$fileFld->htmlAfterField = $preferredDimensionsStr;
if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
    $captchaFld = $frm->getField('htmlNote');
    $captchaFld->htmlBeforeField = '<div class="field-set">
   		   <div class="caption-wraper"><label class="field_label"></label></div>
   		   <div class="field-wraper">
   			   <div class="field_cover">';
    $captchaFld->htmlAfterField = '</div></div></div>';
}
$isUserLogged = UserAuthentication::isUserLogged();
if ($isUserLogged) {
    $nameFld = $frm->getField(BlogContribution::DB_TBL_PREFIX . 'author_first_name');
    $nameFld->setFieldTagAttribute('readonly', 'readonly');
}
?>
<section class="banner banner--main">
    <div class="banner__media"><img src="/public/images/2000x600.jpg" alt=""></div>
    <div class="banner__content banner__content--centered">
        <h1><?php echo Label::getLabel('Lbl_Write_For_Us'); ?></h1>
        <p><?php echo Label::getLabel('Lbl_We_are_constantly_looking_for_writers_and_contributors_to_help_us_create_great_content_for_our_blog_visitors._If_you_can_curate_content_that_you_and_our_visitors_would_love_to_read_and_share,_this_place_is_for_you.'); ?></p>
    </div>
</section>
<section class="section section--upper">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="box -padding-40 -skin">
                   <!--h4><?php echo Label::getLabel('Lbl_Blog_Contribution', $siteLangId); ?></h4>
                      <a href="<?php echo CommonHelper::generateUrl('Blog'); ?>" class="btn btn--primary btn--sm"><?php echo Label::getLabel('Lbl_Back', $siteLangId); ?></a> </div-->
                    <?php echo $frm->getFormHtml(); ?> 
                </div>
            </div>
        </div>
    </div>
</section>
<script src='https://www.google.com/recaptcha/api.js'></script>