<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($footer_navigation)) { ?>
    <div class="col-md-6 col-lg-3">
        <div class="footer-group toggle-group">
            <div class="footer__group-title toggle-trigger-js">
                <h5 class=""><?php echo current($footer_navigation)['parent']; ?></h5>
            </div>
            <div class="footer__group-content toggle-target-js">
                <div class="bullet-list">
                    <ul>
                        <?php
                        foreach ($footer_navigation as $nav) {
                            if ($nav['pages']) {
                                foreach ($nav['pages'] as $link) {
                                    $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                        ?>
                                    <li>
                                        <a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>" class="bullet-list__action"><?php echo $link['nlink_caption']; ?></a>
                                    </li>
                        <?php
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php } ?>