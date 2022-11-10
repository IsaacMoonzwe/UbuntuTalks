<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="header-controls">
    <div class="header-controls__item">
        <a href="<?php echo CommonHelper::generateUrl('', '', [], CONF_WEBROOT_FRONTEND); ?>" class="header-controls__action">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16.076" viewBox="0 0 18 16.076">
                <path d="M15.727,17.428H4.273a.818.818,0,0,1-.818-.818V9.246H1L9.449,1.565a.818.818,0,0,1,1.1,0L19,9.246H16.545v7.364A.818.818,0,0,1,15.727,17.428Zm-4.909-1.636h4.091V7.738L10,3.275,5.091,7.738v8.053H9.182V10.882h1.636Z" transform="translate(-1 -1.352)" />
            </svg>
        </a>
    </div>
    <div class="header-controls__item header-dropdown header-dropdown--arrow">
        <a class="header-controls__action header-dropdown__trigger trigger-js" href="#languages-nav">
            <svg class="icon icon--globe">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#globe'; ?>"></use>
            </svg>
            <span class="lang"><?php echo $languages[$siteLangId]['language_code'] . ' - ' . $currencies[$siteCurrencyId]; ?></span>
            <svg class="icon icon--arrow">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#arrow-black'; ?>"></use>
            </svg>
        </a>
        <div id="languages-nav" class="header-dropdown__target">
            <div class="dropdown__cover">
                <div class="settings-group">
                    <?php if ($languages) { ?>
                        <div class="settings toggle-group">
                            <div class="dropdaown__title"><?php echo Label::getLabel('LBL_SITE_LANGUAGES', $siteLangId) ?></div>
                            <a class="btn btn--bordered color-black btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $languages[$siteLangId]['language_name']; ?></a>
                            <div class="settings__target settings__target-js" style="display: none;">
                                <ul>
                                    <?php foreach ($languages as $langId => $language) { ?>
                                        <li <?php echo ($siteLangId == $langId) ? 'class="is--active"' : ''; ?>><a onClick="setSiteDefaultLang(<?php echo $langId; ?>)" href="javascript:void(0)"><?php echo $language['language_name']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($currencies) { ?>
                        <div class="settings toggle-group">
                            <div class="dropdaown__title"><?php echo Label::getLabel('LBL_Currency', $siteLangId); ?></div>
                            <a class="btn btn--bordered color-black btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $currencies[$siteCurrencyId]; ?></a>
                            <div class="settings__target settings__target-js" style="display: none;">
                                <ul>
                                    <?php foreach ($currencies as $currId => $currName) { ?>
                                        <li <?php echo ($siteCurrencyId == $currId) ? 'class="is--active"' : ''; ?>><a onClick="setSiteDefaultCurrency(<?php echo $currId; ?>)" href="javascript:void(0)"><?php echo $currName; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (UserAuthentication::isUserLogged()) {
        $msgCnt = CommonHelper::getUnreadMsgCount();
        $unreadNotificationsCnt = CommonHelper::getUnreadNotifications();
        ?>
        <div class="header-controls__item header--notification">
            <a href="<?php echo CommonHelper::generateUrl('Notifications', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="header-controls__action">
                <?php if (count($unreadNotificationsCnt) > 0) { ?>
                    <span class="head-count"><?php echo count($unreadNotificationsCnt); ?></span>
                <?php } ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.8" viewBox="0 0 16 16.8">
                    <path d="M16.4,14H18v1.6H2V14H3.6V8.4a6.4,6.4,0,0,1,12.8,0Zm-1.6,0V8.4a4.8,4.8,0,0,0-9.6,0V14ZM7.6,17.2h4.8v1.6H7.6Z" transform="translate(-2 -2)" />
                </svg>
            </a>
        </div>
        <div class="header-controls__item header--message">
            <a href="<?php echo CommonHelper::generateUrl('Messages', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="header-controls__action">
                <?php if ($msgCnt > 0) { ?>
                    <span class="head-count"><?php echo $msgCnt; ?></span>
                <?php } ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="14.4" viewBox="0 0 16 14.4">
                    <path d="M2.8,3H17.2a.8.8,0,0,1,.8.8V16.6a.8.8,0,0,1-.8.8H2.8a.8.8,0,0,1-.8-.8V3.8A.8.8,0,0,1,2.8,3ZM16.4,6.39l-6.342,5.68L3.6,6.373V15.8H16.4ZM4.009,4.6l6.04,5.33L16,4.6Z" transform="translate(-2 -3)" />
                </svg>
            </a>
        </div>
        <div class="header-dropdown header-dropwown--profile">
            <a class="header-dropdown__trigger trigger-js" href="#profile-nav">
                <div class="teacher-profile">
                    <div class="teacher__media">
                        <div class="avtar avtar--xsmall" data-title="<?php echo CommonHelper::getFirstChar(UserAuthentication::getLoggedUserAttribute('user_first_name')); ?>">
                            <?php if (true == User::isProfilePicUploaded()) { ?>
                                <img src=" <?php echo CommonHelper::generateUrl('Image', 'user', array(UserAuthentication::getLoggedUserId(), 'EXTRASMALL')) . '?t=' . time() ?>" alt="">
                                <?php } ?>
                        </div>
                    </div>
                    <div class="teacher__name"><?php echo $userName; ?></div>
                    <svg class="icon icon--arrow">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#arrow-black' ?>"></use>
                    </svg>
                </div>
            </a>
            <div id="profile-nav" class="header-dropdown__target">
                <div class="dropdown__cover">
                    <nav class="menu--inline">
                        <ul>
                            <?php if (User::canViewTeacherTab() && User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD) { ?>
                                <li class="menu__item <?php echo ("Teacher" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Teacher', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                                <li class="menu__item <?php echo ("TeacherStudents" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('TeacherStudents', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Students'); ?></a></li>
                                <li class="menu__item <?php echo ("TeacherScheduledLessons" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                                <?php
                            }
                            if (User::canViewLearnerTab() && User::getDashboardActiveTab() == User::USER_LEARNER_DASHBOARD) {
                                ?>
                                <li class="menu__item <?php echo ("Learner" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Learner', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                                <li class="menu__item <?php echo ("LearnerTeachers" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('LearnerTeachers', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Teachers'); ?></a></li>
                                <li class="menu__item <?php echo ("LearnerScheduledLessons" == $controllerName) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('learnerScheduledLessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                            <?php } ?>
                            <li class="menu__item <?php echo ("Account" == $controllerName && "profileInfo" == $action) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Settings'); ?></a></li>
                            <li class="menu__item"><a href="<?php echo CommonHelper::generateUrl('GuestUser', 'logout'); ?>"><?php echo Label::getLabel('LBL_Logout'); ?></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="header-controls__item header-action">
            <div class="header__action">
                <a href="javascript:void(0)" onClick="logInFormPopUp();" class="header-controls__action btn btn--bordered color-primary user-click"><?php echo Label::getLabel('LBL_Login'); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18.375" viewBox="0 0 18.331 15.331" id="enter"><path d="M17.692 0h-11.5a.639.639 0 00-.639.639V5.11a.639.639 0 101.278 0V1.278h10.222v12.775H6.833v-3.832a.639.639 0 00-1.278 0v4.472a.639.639 0 00.639.639h11.5a.639.639 0 00.639-.639V.639A.639.639 0 0017.692 0z"></path><path d="M9.936 9.769a.639.639 0 00.9.9l2.555-2.555q.022-.022.042-.046l.017-.023.02-.027.017-.028.015-.026.014-.029.013-.028c0-.009.007-.019.01-.029l.011-.03c.004-.01.005-.019.007-.029l.008-.032c.002-.011 0-.023.005-.034s0-.018 0-.028a.643.643 0 000-.126v-.028c0-.009 0-.023-.005-.034s-.005-.021-.008-.032 0-.019-.007-.029-.007-.02-.011-.03l-.01-.029c-.003-.01-.009-.018-.013-.028l-.014-.029-.015-.026-.017-.028-.02-.027-.017-.023q-.02-.024-.042-.046L10.84 4.659a.639.639 0 00-.9.9l1.46 1.468H.639A.639.639 0 000 7.666a.639.639 0 00.639.639H11.4z"></path></svg>
                </a>
                <a href="javascript:void(0)" onClick="signUpFormPopUp();" class="btn btn--primary color-white user-click">sign up
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18.375" viewBox="0 0 14 18.375">
                        <path d="M18,19.375H16.25v-1.75A2.625,2.625,0,0,0,13.625,15H8.375A2.625,2.625,0,0,0,5.75,17.625v1.75H4v-1.75A4.375,4.375,0,0,1,8.375,13.25h5.25A4.375,4.375,0,0,1,18,17.625ZM11,11.5a5.25,5.25,0,1,1,5.25-5.25A5.25,5.25,0,0,1,11,11.5Zm0-1.75a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,11,9.75Z" transform="translate(-4 -1)" />
                    </svg>
                </a>
            </div>
        </div>
    <?php } ?>
</div>