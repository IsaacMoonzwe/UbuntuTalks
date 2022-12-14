<?php defined('SYSTEM_INIT') or die('Invalid usage'); ?>
<section class="section">
    <div class="container container--narrow">
        <div class="contact-cta">
            <div class="contact__content">
                <h3><?php echo stripslashes(Label::getLabel('LBL_find_an_answer', $siteLangId)); ?></h3>
                <p><?php echo sprintf(Label::getLabel('LBL_call_us_message', $siteLangId), FatApp::getConfig('CONF_CONTACT_EMAIL', FatUtility::VAR_STRING, ''), FatApp::getConfig('CONF_SITE_PHONE', FatUtility::VAR_STRING, '')); ?></p>
            </div>
            <a href="<?php echo CommonHelper::generateUrl('Contact'); ?>" class="btn btn--primary color-white"><?php echo Label::getLabel('LBL_Contact_Us', $siteLangId); ?></a>
        </div>
    </div>
</section>