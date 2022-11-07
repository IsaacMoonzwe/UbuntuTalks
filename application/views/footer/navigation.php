<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($footer_navigation)) { ?>
    <?php foreach ($footer_navigation as $nav) { ?>
        <?php
        if ($nav['pages']) {
            foreach ($nav['pages'] as $link) {
                $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                ?>
                <li><a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>"><?php echo $link['nlink_caption']; ?></a></li>	
                <?php
            }
        }
        ?>
    <?php } ?>
<?php } ?>