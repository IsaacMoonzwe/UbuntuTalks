<div class="menu-group">
    <h6 class="heading-6"><?php echo label::getLabel('LBL_Profile'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ( $controllerName == "EventLearner" && $action == "index" ) ? 'is-active' : ''; ?> ">
                <a href="<?php echo CommonHelper::generateUrl('EventLearner'); ?>">
                    <svg class="icon icon--dashboard margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#dashboard'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_Dashboard'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ($controllerName == "Account") ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('Account', 'ProfileInfo'); ?>">
                    <svg class="icon icon--settings margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#settings'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_Account_Settings'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<div class="menu-group">
    <h6 class="heading-6">Course</h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item book_lesson <?php echo ( $controllerName == "LearnerScheduledLessons" && $action == 'index') ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('Teachers/#teachers', '', [], CONF_WEBROOT_FRONT_URL); ?>">
                    <svg class="icon icon--lesson margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#stats_1'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_Book_Lesson'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ( $controllerName == "LearnerScheduledLessons" && $action == 'index') ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons'); ?>">
                    <svg class="icon icon--lesson margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#lessons'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_LESSONS'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ($controllerName == "LearnerTeachers") ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('LearnerTeachers'); ?>">
                    <svg class="icon icon--students margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#students'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_Teachers'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ( $controllerName == "LearnerScheduledLessons" && $action=='help') ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons', 'help'); ?>">
                    <svg class="icon icon--lesson margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#lessons'; ?>"></use></svg>
                    <span>Help Desk</span>
                </a>
            </li>
            <li class="menu__item <?php echo ( $controllerName == "LearnerScheduledLessons" && $action=='report') ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons', 'report'); ?>">
                    <svg class="icon icon--lesson margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#issue'; ?>"></use></svg>
                    <span>Report an Issue</span>
                </a>
            </li>
            <li class="menu__item">
                <a href="/learner-faq">
                    <svg class="icon icon--students margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#students'; ?>"></use></svg>
                    <span>FAQ</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('Lbl_History'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ($controllerName == "Learner" && $action == "orders") ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('EventLearner', 'orders'); ?>">
                    <svg class="icon icon--orders margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#orders'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('Lbl_Orders'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ($controllerName == "Wallet") ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('Wallet'); ?>">
                    <svg class="icon icon--wallet margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#wallet'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('Lbl_Wallet'); ?></span>
                    <!-- Wallet <span>($250.00) -->
                </a>
            </li>
        </ul>
    </nav>
</div>
<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('Lbl_Others'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <?php if (FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true)) { ?>	
                <li class="menu__item <?php echo ($controllerName == "FlashCards") ? 'is-active' : ''; ?>">
                    <a href="<?php echo CommonHelper::generateUrl('FlashCards'); ?>">
                        <svg class="icon icon--flash-cards margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#flashcards'; ?>"></use></svg>
                        <span><?php echo Label::getLabel('LBL_Flash_Cards'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <li class="menu__item <?php echo ($controllerName == "Giftcard") ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('Giftcard'); ?>">
                    <svg class="icon icon--gifts-cards margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#giftcards'; ?>"></use></svg>
                    <span><?php echo Label::getLabel('LBL_Gift_Cards'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
