<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="header-dropdown header-dropdown--explore">
    <a class="header-dropdown__trigger trigger-js" href="#explore">
        <svg class="icon icon--menu">
        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#burger-menu'; ?>"></use>
        </svg>
        <span><?php echo Label::getLabel('LBL_EXPLORE_SUBJECTS', CommonHelper::getLangId()); ?></span>
    </a>
    <div id="explore" class="header-dropdown__target">
        <div class="dropdown__cover">
            <nav class="menu--inline">
                <ul>
                    <?php foreach ($teachLangs as $teachLangId => $teachlang) { ?>
                        <li class="menu__item"><a href="<?php echo CommonHelper::generateUrl('teachers', 'languages', [$teachlang['tlanguage_slug']], CONF_WEBROOT_FRONTEND); ?>"><?php echo $teachlang['tlanguage_name']; ?></a></li>
                        <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>