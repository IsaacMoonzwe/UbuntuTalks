<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
    /* .header__middle ul li:nth-child(6){ order: 7 !important; }
    .header__middle ul li:nth-child(7){ order: 8 !important; }
    .header__middle ul li:nth-child(8){ order: 6 !important; } */
</style>
<?php if (!empty($header_navigation)) { ?>
    <span class="overlay overlay--nav toggle--nav-js is-active"></span>
    <nav class="menu nav--primary-offset">
        <ul>
            <!-- <li class="menu__item"><a href="https://www.ubuntutalks.com/">HOME</a></li> -->
            <?php foreach ($header_navigation as $nav) { ?>
                <?php
                if ($nav['pages']) {
                    foreach ($nav['pages'] as $link) {
                        $navUrl = CommonHelper::getnavigationUrl($link['nlink_type'], $link['nlink_url'], $link['nlink_cpage_id']);
                        ?>
                        <li class="menu__item"><a target="<?php echo $link['nlink_target']; ?>" href="<?php echo $navUrl; ?>"><?php echo $link['nlink_caption']; ?></a></li>
                            <?php
                        }
                    }
                    ?>
                <?php } ?>
                <!-- <li class="menu__item"><a href="https://www.ubuntutalks.com/contact">CONTACT</a></li>
                <li class="menu__item"><a href="https://www.ubuntutalks.com/field-services">Field</a></li> -->
        </ul>
    </nav>
    <?php
}
