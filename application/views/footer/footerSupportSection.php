<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-md-3">
    <div class="footer-group toggle-group">
        <div class="footer__group-title toggle-trigger-js">
            <h5 class=""><?php echo Label::getLabel('LBL_Support') ?></h5>
        </div>
        <div class="footer__group-content toggle-target-js" >
            <div class="bullet-list">
                <ul class="footer_contact_details">
                    <li>
                        <svg class="icon icon--email"><use xlink:href="images/sprite.yo-coach.svg#email"></use></svg>
                        <span><?php echo FatApp::getConfig('CONF_CONTACT_EMAIL', null, ''); ?></span>
                    </li>
                    <li>
                        <svg class="icon icon--phone"><use xlink:href="images/sprite.yo-coach.svg#phone"></use></svg>
                        <span><?php echo Label::getLabel('LBL_Call_Us') . ':' . FatApp::getConfig('CONF_SITE_PHONE', null, ''); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
