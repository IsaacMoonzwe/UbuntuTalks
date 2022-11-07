<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <a href="<?php echo CommonHelper::generateUrl('EventUser', 'EventLoginForm'); ?>" class="-link-underline -color-secondary -style-bold"><?php echo Label::getLabel('LBL_Back_to_login'); ?></a>
        <span class="-gap"></span>
        <div class="row justify-content-center">
            <div class="col-sm-9 col-lg-5 col-xl-5">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></h4>
                        <p><?php echo Label::getLabel('LBL_Please_enter_the_email_address_registered_on_your_account.'); ?></p>
                    </div>
                    <div class="box__body -padding-40">
                        <?php
                        $frm->setFormTagAttribute('class', 'form');
                        $fld = $frm->getField('btn_submit');
                        $frm->developerTags['colClassPrefix'] = 'col-md-';
                        $frm->developerTags['fld_default_col'] = 12;
                        $frm->setFormTagAttribute('id', 'frmPwdForgot');
                        $frm->setFormTagAttribute('autocomplete', 'off');
                        $frm->setValidatorJsObjectName('forgotValObj');
                        $frm->setFormTagAttribute('action', CommonHelper::generateUrl('EventUser', 'forgotPassword'));
                        $frmFld = $frm->getField('user_email');
                        /* $frmFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_EMAIL_ADDRESS')); */
                        if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '' && FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, '') != '') {
                            $captchaFld = $frm->getField('htmlNote');
                            $captchaFld->htmlBeforeField = '<div class="field-set">
                                                                            <div class="caption-wraper"><label class="field_label"></label></div>
                                                                            <div class="field-wraper">
                                                                            <div class="field_cover">';
                            $captchaFld->htmlAfterField = '</div></div></div>';
                        }
                        echo $frm->getFormHtml();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src='https://www.google.com/recaptcha/api.js'></script>