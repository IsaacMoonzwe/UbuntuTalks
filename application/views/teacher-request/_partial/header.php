<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<header class="header">
    <div class="container container--narrow">
        <div class="header-primary">
            <div class="d-flex justify-content-between">
                <div class="header__left">
                    <div class="header__logo">
                        <a href="<?php echo CommonHelper::generateUrl(); ?>">
                            <?php if (CommonHelper::demoUrl()) { ?>
                                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                            <?php } else { ?>
                                <img src="<?php echo CommonHelper::generateFullUrl('Image', 'siteLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL); ?>" alt="">
                            <?php } ?>
                        </a>
                    </div>
                </div>
                <div class="header__right">
                    <div class="head__action">
                        <a class="" href="<?php echo CommonHelper::generateUrl('TeacherRequest', 'logoutGuestUser'); ?>">
                            <svg class="icon icon--logout">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#close' ?>"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>



<!-- ] -->



