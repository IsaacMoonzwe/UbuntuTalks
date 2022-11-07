<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($rows) { ?>
    <div class="col-md-6 col-lg-3 foot-block3">
        <div class="footer-group toggle-group">
            <div class="footer__group-title toggle-trigger-js">
                <h5><?php echo Label::getLabel('LBL_Social', $siteLangId); ?></h5>
            </div>
            <div class="footer__group-content toggle-target-js">
                <div class="bullet-list">
                    <ul class="footer_social-links">
                        <?php foreach ($rows as $row) { ?>
                            <li>
                                <a title="<?php echo $row['splatform_title']; ?>" <?php if ($row['splatform_url'] != '') { ?>target="_blank" <?php } ?> href="<?php echo ($row['splatform_url'] != '') ? $row['splatform_url'] : 'javascript:void(0)'; ?>">
                                    <img src = "<?php echo CommonHelper::generateUrl('Image', 'SocialPlatform', array($row['splatform_id'])); ?>" alt=""/>
                                    <span><?php echo $row['splatform_title']; ?></span>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php //$this->includeTemplate('footer/moreLinksNavigation.php', ['siteLangId' => $siteLangId]); ?>
            </div>
        </div>
    </div>

<?php } ?>