<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-md-6 col-lg-3 foot-block2">
    <div class="footer-group toggle-group">
        <div class="footer__group-title toggle-trigger-js">
            <h5 class="">Subscribe<?php //echo Label::getLabel('LBL_Support', $siteLangId) ?></h5>
        </div>
        <div class="footer__group-content toggle-target-js" >
            <?php $this->includeTemplate('footer/footerNewsLetter.php', ['siteLangId' => $siteLangId]); ?>
            <div class="bullet-list">
                <ul class="footer_contact_details">
                    <li>
                        <!--<svg class="icon icon--phone"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#phone'; ?>"></use></svg>-->
                        <span><a href="tel:<?php echo FatApp::getConfig('CONF_SITE_PHONE', null, ''); ?>"><b><?php echo Label::getLabel('LBL_Call_Us', $siteLangId) . ':</b> ' . FatApp::getConfig('CONF_SITE_PHONE', null, ''); ?></a></span>
                    </li>
                    <li>
                        <!--<svg class="icon icon--email"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#email'; ?>"></use></svg>-->
                        <span><a href="https://www.ubuntutalks.com/contact"><b>Email Us:</b> admin@ubuntutalks.com</a></span>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
</div>
