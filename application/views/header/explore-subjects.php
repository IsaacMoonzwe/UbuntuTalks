<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
    .close-icon {
        display: none;
    }

    .display-close-icon {
        display: block;
    }

    .block-open-icon {
        display: none;
    }

    .open-icon svg.icon.icon--menu {
        width: 32px;
        height: 32px;
    }
</style>
<div class="header-dropdown header-dropdown--explore">
    <a class="header-dropdown__trigger trigger-js" href="#explore">
        <div class="open-icon">
            <svg class="icon icon--menu">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#burger-menu'; ?>"></use>
            </svg>
        </div>

        <div class="close-icon">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 121.31 122.876" enable-background="new 0 0 121.31 122.876" xml:space="preserve">
                <g>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M90.914,5.296c6.927-7.034,18.188-7.065,25.154-0.068 c6.961,6.995,6.991,18.369,0.068,25.397L85.743,61.452l30.425,30.855c6.866,6.978,6.773,18.28-0.208,25.247 c-6.983,6.964-18.21,6.946-25.074-0.031L60.669,86.881L30.395,117.58c-6.927,7.034-18.188,7.065-25.154,0.068 c-6.961-6.995-6.992-18.369-0.068-25.397l30.393-30.827L5.142,30.568c-6.867-6.978-6.773-18.28,0.208-25.247 c6.983-6.963,18.21-6.946,25.074,0.031l30.217,30.643L90.914,5.296L90.914,5.296z" />
                </g>
            </svg>
        </div>
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