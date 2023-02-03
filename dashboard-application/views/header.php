<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($controllerName != 'GuestUser' && $controllerName != 'Error' && $controllerName != 'Teach') {
    $_SESSION['referer_page_url'] = CommonHelper::getCurrUrl();
}
$layoutDirection = CommonHelper::getLayoutDirection();
?>
<!doctype html>
<html lang="en" dir="<?php echo $layoutDirection; ?>" class="<?php echo (FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1) && CommonHelper::demoUrl()) ? 'sticky-demo-header' : '' ?>">
    <head>
        <!-- Basic Page Needs ======================== -->
        <meta charset="utf-8">
        <?php echo $this->writeMetaTags(); ?>
        <!-- MOBILE SPECIFIC METAS ===================== -->
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
        <!-- FONTS ================================================== -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
        <link rel="canonical" href="<?php echo $canonicalUrl; ?>" />
        <!-- FAVICON ================================================== -->
        <link rel="shortcut icon" href="<?php echo CommonHelper::generateUrl('Image', 'favicon', array($siteLangId), CONF_WEBROOT_FRONTEND); ?>">
        <link rel="apple-touch-icon" href="<?php echo CommonHelper::generateUrl('Image', 'appleTouchIcon', array($siteLangId), CONF_WEBROOT_FRONTEND); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/apple-touch-icon-114x114.png">
        <!-- CSS/JS ================================================== -->
        <?php
        $jsVariables = CommonHelper::htmlEntitiesDecode($jsVariables);
        $sslUsed = (FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_BOOLEAN, false)) ? 1 : 0;
        $closeSystemMessages = FatApp::getConfig("CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES", FatUtility::VAR_INT, 3);
        $closeSystemMessages = ($closeSystemMessages <= 0) ? 3 : $closeSystemMessages;
        $websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, '');
        $loggedUserFirstName = $userDetails['user_first_name'];
        $loggedUserLastName = $userDetails['user_last_name'];
        $loggedUserFullName = $loggedUserFirstName . ' ' . $loggedUserLastName;
        $loggedUserId = UserAuthentication::getLoggedUserId();
        $currentActiveTab = User::getDashboardActiveTab();
        $canViewTeacherTab = User::canViewTeacherTab();
        $isUserTeacher = User::isTeacher();
        $bodyClass = (User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD) ? 'dashboard-teacher' : 'dashboard-learner';
        $mainDashboardClass = (($controllerName == 'Teacher' || $controllerName == 'Learner') && $action == "index") ? "main-dashboard" : '';
        $msgCnt = CommonHelper::getUnreadMsgCount();
        $unreadNotifications = UserNotifications::getUserUnreadNotifications($loggedUserId);
        ?>
        <script type="text/javascript">
            var langLbl = <?php echo json_encode(CommonHelper::htmlEntitiesDecode($jsVariables)) ?>;
            var layoutDirection = '<?php echo CommonHelper::getLayoutDirection(); ?>';
            var currencySymbolLeft = '<?php echo $currencySymbolLeft; ?>';
            var currencySymbolRight = '<?php echo $currencySymbolRight; ?>';
            var SslUsed = '<?php $sslUsed; ?>';
            var cookieConsent = <?php echo json_encode($cookieConsent); ?>;
            var userTimeZone = '<?php echo MyDate::getUserTimeZone(); ?>';
            var timeZoneOffset = '<?php echo MyDate::getOffset(MyDate::getUserTimeZone()); ?>';
            const CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES = '<?php echo $closeSystemMessages; ?>';
            const CONF_AUTO_CLOSE_SYSTEM_MESSAGES = '<?php echo FatApp::getConfig("CONF_AUTO_CLOSE_SYSTEM_MESSAGES", FatUtility::VAR_INT, 0); ?>';
            const confWebRootUrl = '<?php echo CONF_WEBROOT_URL; ?>';
            const confFrontEndUrl = '<?php echo CONF_WEBROOT_FRONTEND; ?>';
            const statusUpcoming = <?php echo FatUtility::int(ScheduledLesson::STATUS_UPCOMING); ?>;
            const statusScheduled = <?php echo FatUtility::int(ScheduledLesson::STATUS_SCHEDULED); ?>;
            const statusUnscheduled = <?php echo FatUtility::int(ScheduledLesson::STATUS_NEED_SCHEDULING); ?>;
            const statusCompleted = <?php echo FatUtility::int(ScheduledLesson::STATUS_COMPLETED); ?>;
            const statusCanceled = <?php echo FatUtility::int(ScheduledLesson::STATUS_CANCELLED); ?>;
            const statusIssueReported = <?php echo FatUtility::int(ScheduledLesson::STATUS_ISSUE_REPORTED); ?>;
<?php
/**
 * var monthNames, weekDayNames used in the fullcalendar-luxon.min.js file  
 */
if (isset($setMonthAndWeekName) && $setMonthAndWeekName) {
    ?>
                var monthNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::getAllMonthName(false, $siteLangId))); ?>;
                var weekDayNames = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::dayNames(false, $siteLangId))); ?>;
                var meridiems = <?php echo json_encode(CommonHelper::htmlEntitiesDecode(MyDate::meridiems(false, $siteLangId))); ?>;
<?php } ?>
        </script>
        <?php
        echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
        if (isset($includeEditor) && $includeEditor) {
            ?>
            <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
            <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js"></script>
            <?php
        }
        if (FatApp::getConfig('CONF_ENABLE_PWA', FatUtility::VAR_BOOLEAN, false)) {
            ?>
            <link rel="manifest" href="<?php echo CommonHelper::generateUrl('MyApp', 'PwaManifest'); ?>">
            <script>
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.register("<?php echo CONF_WEBROOT_FRONTEND; ?>sw.js");
            }
            </script>
        <?php } ?>
    </head>
    <body class="<?php echo $bodyClass . ' ' . strtolower($controllerName) . ' ' . strtolower($action) . ' ' . $mainDashboardClass; ?>">
        <?php
        $autoRestartOn = FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1);
        if ($autoRestartOn == applicationConstants::YES && CommonHelper::demoUrl()) {
            $this->includeTemplate('restore-system/header-bar.php');
        }
        ?>
        <div class="site">
            <!-- [ SIDE BAR ========= -->
            <aside class="sidebar">
                <!-- [ SIDE BAR SECONDARY ========= -->
                <div class="sidebar__secondary">
                    <nav class="menu menu--secondary">
                        <ul>
                            <li class="menu__item menu__item-toggle">
                                <a href="#primary-nav" class="menu__item-trigger trigger-js for-responsive" title="<?php echo Label::getLabel('LBL_Menu'); ?>">
                                    <span class="icon icon--menu">
                                        <span class="toggle"><span></span></span>
                                    </span>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Menu'); ?></span>
                                </a>
                                <a href="#sidebar__primary" class="menu__item-trigger fullview-js for-desktop" title="<?php echo Label::getLabel('LBL_Menu'); ?>">
                                    <span class="icon icon--menu">
                                        <span class="toggle"><span></span></span>
                                    </span>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Menu'); ?></span>
                                </a>
                            </li>
                            <li class="menu__item menu__item-home">
                                <a href="<?php echo CommonHelper::generateUrl('Account'); ?>" class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_Home'); ?>">
                                    <svg class="icon icon--home">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#home'; ?>"></use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Home'); ?></span>
                                </a>
                            </li>
                            <li class="menu__item menu__item-messaging  <?php echo ($controllerName == 'Messages') ? 'is-active' : ''; ?>">
                                <a href="<?php echo CommonHelper::generateUrl('Messages'); ?>" class="menu__item-trigger" <?php echo ($msgCnt > 0) ? 'data-count="' . $msgCnt . '"' : ""; ?> title="<?php echo Label::getLabel('LBL_Messaging'); ?>">
                                    <!-- add  data-count="{count}" if any unread message -->
                                    <svg class="icon icon--messaging">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#message'; ?>"></use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Messaging'); ?></span>
                                </a>
                            </li>
                            <li class="menu__item menu__item-notifications <?php echo ($controllerName == 'Notifications') ? 'is-active' : ''; ?> ">
                                <a href="<?php echo CommonHelper::generateUrl('Notifications'); ?>" class="menu__item-trigger" <?php echo ($unreadNotifications > 0) ? 'data-count="' . $unreadNotifications . '"' : ""; ?> title="<?php echo Label::getLabel('LBL_Notificatons'); ?>">
                                    <!-- add  data-count="{count}" if any unread Notificatons -->
                                    <svg class="icon icon--notificatons">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#notification'; ?>"></use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Notificatons'); ?></span>
                                </a>
                            </li>
                            <?php if (!empty($websiteLangues) || !empty($currencyDat)) { ?>
                                <li class="menu__item menu__item-languages">
                                    <a href="#languages-nav" class="menu__item-trigger trigger-js" title="<?php echo Label::getLabel('LBL_Languages/Currencies'); ?>">
                                        <svg class="icon icon--lang">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#language'; ?>"></use>
                                        </svg>
                                        <span class="sr-only"><?php echo Label::getLabel('LBL_Languages/Currencies'); ?></span>
                                    </a>
                                    <div id="languages-nav" class="menu__dropdown">
                                        <div class="menu__dropdown-head">
                                            <span class="uppercase small bold-600"><?php echo Label::getLabel('LBL_Change_Languages'); ?></span>
                                        </div>
                                        <div class="menu__dropdown-body">
                                            <nav class="menu menu--inline">
                                                <ul>
                                                    <?php foreach ($websiteLangues as $language) { ?>
                                                        <li class="menu__item <?php echo ($siteLangId == $language['language_id']) ? 'is-active' : ''; ?>"><a href="javascript:void(0)" onClick="setSiteDefaultLang(<?php echo $language['language_id']; ?>)"><?php echo $language['language_name']; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                                <hr>
                                                <ul>
                                                    <?php foreach ($currencyData as $key => $currency) { ?>
                                                        <li class="menu__item <?php echo ($siteCurrencyId == $key) ? 'is-active' : ''; ?>"><a onClick="setSiteDefaultCurrency(<?php echo $key; ?>)" href="javascript:void(0);"><?php echo $currency; ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            if ($currentActiveTab == User::USER_LEARNER_DASHBOARD) {
                                ?>
                                <li class="menu__item menu__item-favorites <?php echo ($controllerName == 'Learner' && $action == 'favourites') ? 'is-active' : ''; ?>">
                                    <a href="<?php echo CommonHelper::generateUrl('Learner', 'favourites'); ?>" class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_Favorites'); ?>">
                                        <svg class="icon icon--favorites">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#favorite'; ?>"></use>
                                        </svg>
                                        <span class="sr-only"><?php echo Label::getLabel('LBL_Favorites'); ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="menu__item menu__item-logout">
                                <a href="<?php echo CommonHelper::generateUrl('GuestUser', 'logout', [], CONF_WEBROOT_FRONT_URL); ?>" class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_Logout'); ?>">
                                    <svg class="icon icon--logout">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#logout'; ?>"></use>
                                    </svg>
                                    <span class="sr-only"><?php echo Label::getLabel('LBL_Logout'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!-- ] -->
                <!-- [ SIDE BAR PRIMARY ========= -->
                <div id="sidebar__primary" class="sidebar__primary">
                    <div class="sidebar__head">
                        <figure class="logo">
                            <a href="<?php echo CommonHelper::generateUrl('', '', [], CONF_WEBROOT_FRONT_URL); ?>">
                                <?php if (CommonHelper::demoUrl()) { ?>
                                    <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                                <?php } else { ?>
                                    <img src="<?php echo CommonHelper::generateFullUrl('Image', 'siteLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL); ?>" alt="<?php echo $websiteName; ?>">
                                <?php } ?>
                            </a>
                        </figure>
                        <!-- [ PROFILE ========= -->
                        <div class="profile">
                            <a href="#profile-target" class="trigger-js profile__trigger">
                                <div class="profile__meta d-flex align-items-center">
                                    <div class="profile__media margin-right-4">
                                        <div class="avtar" data-title="<?php echo CommonHelper::getFirstChar($loggedUserFirstName); ?>">
                                            <?php
                                            if (true == User::isProfilePicUploaded()) {
                                                echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array(UserAuthentication::getLoggedUserId()), CONF_WEBROOT_FRONT_URL) . '?' . time() . '" alt="' . $loggedUserFirstName . '" />';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="profile__details">
                                        <h6 class="profile__title"><?php echo $loggedUserFullName; ?></h6>
                                        <?php
                                        $loggedAs = ($currentActiveTab == User::USER_TEACHER_DASHBOARD) ? 'LBL_Logged_in_as_a_teacher' : 'LBL_Logged_in_as_a_learner';
                                        ?>
                                        <small class="color-black"><?php echo label::getLabel($loggedAs); ?></small>
                                    </div>
                                </div>
                            </a>
                            <div id="profile-target" class="profile__target">
                                <div class="profile__target-details">
                                    <table>
                                        <?php
                                        $countryDetails = Country::getCountryById($userDetails['user_country_id']);
                                        if (!empty($countryDetails['country_name'])) {
                                            ?>
                                            <tr>
                                                <th><?php echo label::getLabel('LBL_Location'); ?></th>
                                                <td>
                                                    <?php echo $countryDetails['country_name']; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr>
                                            <th><?php echo label::getLabel('LBL_Time_Zone'); ?></th>
                                            <td>
                                                <?php
                                                $userTimeZone = MyDate::getUserTimeZone($loggedUserId);
                                                $timezoneStr = CommonHelper::getDateOrTimeByTimeZone($userTimeZone, 'h:i A');
                                                echo $timezoneStr . " (" . Label::getLabel('LBL_TIMEZONE_STRING') . " " . CommonHelper::getDateOrTimeByTimeZone($userTimeZone, 'P') . ")";
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <span class="-gap-10"></span>
                                    <div class="btns-group">
                                        <?php if ($isUserTeacher && $currentActiveTab == User::USER_TEACHER_DASHBOARD && !empty($teacherProfileProgress['isProfileCompleted'])) { ?>
                                            <a href="<?php echo CommonHelper::generateFullUrl('teachers', 'view', [CommonHelper::htmlEntitiesDecode($userDetails['user_url_name'])], CONF_WEBROOT_FRONTEND); ?>" class="btn btn--bordered color-third btn--block margin-top-2"><?php echo label::getLabel('LBL_View_Public_Profile'); ?></a>
                                            <?php
                                        }
                                        if ($currentActiveTab == User::USER_LEARNER_DASHBOARD && $canViewTeacherTab) {
                                            ?>
                                            <a href="<?php echo CommonHelper::generateUrl('Teacher'); ?>" class="btn bg-third btn--block margin-top-4"><?php echo label::getLabel('LBL_Switch_to_Teacher_Profile'); ?></a>
                                        <?php } elseif ($currentActiveTab == User::USER_TEACHER_DASHBOARD) { ?>
                                            <a href="<?php echo CommonHelper::generateUrl('Learner'); ?>" class="btn bg-third btn--block margin-top-4"><?php echo label::getLabel('LBL_Switch_to_Learner_Profile'); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                    </div>
                    <div class="sidebar__body">
                        <div class="sidebar__scroll">
                            <div id="primary-nav" class="menu-offset">
                                <!-- Display flashcard list on left sidebar in lesson view page  -->
                                <?php
                                $templateVariable = ['controllerName' => $controllerName, 'action' => $action];
                                $sidebarMenuLayout = 'learner/_partial/learnerDashboardNavigation.php';
                                if (User::canViewTeacherTab() && User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD) {
                                    $templateVariable['tpp'] = $teacherProfileProgress;
                                    $sidebarMenuLayout = 'teacher/_partial/teacherDashboardNavigation.php';
                                }
                                if (isset($showFlashCard) && $showFlashCard) {
                                    $templateVariable['frmSrchFlashCard'] = $frmSrchFlashCard;
                                    $templateVariable['lessonRow'] = $lessonRow;
                                    $sidebarMenuLayout = 'learner/_partial/flashCardSidebarView.php';
                                    if ($currentActiveTab == User::USER_TEACHER_DASHBOARD) {
                                        $sidebarMenuLayout = 'teacher/_partial/flashCardSidebarView.php';
                                    }
                                }
                                $this->includeTemplate($sidebarMenuLayout, $templateVariable);
                                ?>
                                <div class="sidebar__links">
                                    <a href="<?php echo CommonHelper::generateUrl('Teachers', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?></a>
                                    <?php if (User::getDashboardActiveTab() == User::USER_LEARNER_DASHBOARD && !User::isTeacher()) { ?>
                                        <a href="<?php echo CommonHelper::generateUrl('TeacherRequest', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_APPLY_TO_TEACH'); ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] -->
            </aside>
            <!-- ] -->
            <main class="page">