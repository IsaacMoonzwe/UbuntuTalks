<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($header_navigation_more)) { ?>
    <li class="nav__dropdown">
        <a href="javascript:void(0)" class="nav__dropdown-trigger nav__dropdown-trigger-js"><?php echo Label::getLabel("Label_Help", CommonHelper::getLangId()); ?> </a>
        <div class="nav__dropdown-target nav__dropdown-target-js -skin">
            <nav class="nav nav--vertical">
                <ul>
                    <?php foreach ($header_navigation_more as $nav) { ?>
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
                </ul>
            </nav>
        </div>
    </li>
<?php } ?>
