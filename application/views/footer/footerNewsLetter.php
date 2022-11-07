<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$form->developerTags['colClassPrefix'] = 'col-sm-';
$form->developerTags['fld_default_col'] = 12;
$form->setFormTagAttribute('onsubmit', 'submitNewsletterForm(this); return false;');
$emailFld = $form->getField('email');
$emailFld->developerTags['noCaptionTag'] = true;
$emailFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_ENTER_EMAIL'));
$submitBtn = $form->getField('btnSubmit');
$submitBtn->developerTags['noCaptionTag'] = true;
$submitBtn->addFieldTagAttribute('class', 'btn btn--secondary col-12 no-gutter');
?>
<?php if (FatApp::getConfig('CONF_ENABLE_NEWSLETTER_SUBSCRIPTION', FatUtility::VAR_INT, 1)) { ?>
    
        <div class="footer-group toggle-group">
            <div class="footer__group-content toggle-target-js">
                
                <?php echo $form->getFormHtml(); ?>
                <div class="email-field">
                </div>
            </div>
        </div>
<?php } ?>