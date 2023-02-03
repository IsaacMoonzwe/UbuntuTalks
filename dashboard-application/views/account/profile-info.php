<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$activeMettingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
$currentActiveTab = User::getDashboardActiveTab();
$isTeacherDashboardTabActive = (User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD);
?>
<script>
    var userIsTeacher = <?php echo $userIsTeacher ?: 0; ?>;
    var currentActiveTab = <?php echo $currentActiveTab ?: 0; ?>;
    var isTeacherDashboardTabActive = <?php echo $isTeacherDashboardTabActive ?: 0; ?>;
    var isCometChatMeetingToolActive = '<?php echo $activeMettingTool == ApplicationConstants::MEETING_COMET_CHAT ?>';
    var useMouseScroll = "<?php echo Label::getLabel('LBL_USE_MOUSE_SCROLL_TO_ADJUST_IMAGE'); ?>";
</script>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="container container--fixed">
    <div class="page__head">
        <h1><?php echo Label::getLabel('LBL_Account_Settings'); ?></h1>
    </div>
    <div class="page__body">
        <?php if ($isTeacherDashboardTabActive && $userIsTeacher) { ?>
            <!-- [ INFO BAR ========= -->
            <div class="infobar">
                <div class="row justify-content-between align-items-start">
                    <div class="col-lg-8 col-sm-8">
                        <div class="d-flex">
                            <div class="infobar__media margin-right-5">
                                <div class="infobar__media-icon infobar__media-icon--alert is-profile-complete-js">!</div>
                            </div>
                            <div class="infobar__content">
                                <h6 class="margin-bottom-1"><?php echo Label::getLabel('Lbl_Complete_Your_profile'); ?></h6>
                                <p class="margin-0"> <?php echo Label::getLabel('LBL_PROFILE_INFO_HEADING'); ?>
                                    <a href="javascript:void(0)" class="color-secondary underline padding-top-3 padding-bottom-3 expand-js"><?php echo Label::getLabel('LBL_Learn_More'); ?></a>
                                </p>
                                <div class="infobar__content-more margin-top-3 expand-target-js" style="display: none;">
                                    <?php echo ExtraPage::getBlockContent(ExtraPage::BLOCK_PROFILE_INFO_BAR, $siteLangId); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-4">
                        <div class="profile-progress margin-top-2">
                            <div class="profile-progress__meta margin-bottom-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div><span class="small"> <?php echo Label::getLabel('LBL_Profile_progress'); ?></span></div>
                                    <div><span class="small bold-700 progress-count-js"></span></div>
                                </div>
                            </div>
                            <div class="profile-progress__bar">
                                <div class="progress progress--small progress--round">
                                    <!-- <div class="progress__bar bg-green" role="progressbar" style="width:60%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div> -->
                                    <div class="progress-bar">
                                        <div class="progress__step profile-Info-progress-js"></div>
                                        <div class="progress__step teacher-lang-progress-js"></div>
                                        <div class="progress__step teacher-lang-price-progress-js"></div>
                                        <div class="progress__step teacher-qualification-progress-js"></div>
                                        <div class="progress__step teacher-preferences-progress-js"></div>
                                        <div class="progress__step general-availability-progress-js"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ] -->
        <?php } ?>
        <!-- [ PAGE PANEL ========= -->
        <div class="page-panel page-panel--flex min-height-500">
            <div class="page-panel__small">
                <nav class="menu menu--vertical menu--steps tabs-scrollable-js">
                    <ul>
                        <li class="menu__item <?php echo ($isTeacherDashboardTabActive && $userIsTeacher) ? 'profile--progress--menu' : ''; ?> is-active">
                            <a href="javascript:void(0);" class="profile-Info-js" onClick="profileInfoForm();">
                                <?php echo Label::getLabel('LBL_Personal_Info'); ?>
                                <span class="menu__icon"></span>
                            </a>
                        </li>
                        <?php if ($isTeacherDashboardTabActive && $userIsTeacher) { ?>
                            <li class="menu__item profile--progress--menu">
                                <a href="javascript:void(0);" class="teacher-lang-form-js" onClick="teacherLanguagesForm()">
                                    <?php echo Label::getLabel('LBL_Languages'); ?>
                                    <span class="menu__icon"></span>
                                </a>
                            </li>
                            <li class="menu__item profile--progress--menu">
                                <a href="javascript:void(0);" class="teacher-tech-lang-price-js" id="teacher-tech-lang-price-js" onClick="teacherSettingsForm()">
                                    <?php echo Label::getLabel('LBL_Price'); ?>
                                    <span class="menu__icon"></span>
                                </a>
                            </li>
                            <li class="menu__item profile--progress--menu">
                                <a href="javascript:void(0);" class="teacher-qualification-js" onClick="teacherQualification()">
                                    <?php echo Label::getLabel('LBL_Experience'); ?>
                                    <span class="menu__icon"></span>
                                </a>
                            </li>
                            <li class="menu__item profile--progress--menu">
                                <a href="javascript:void(0);" class="teacher-preferences-js" onClick="teacherPreferencesForm()">
                                    <?php echo Label::getLabel('LBL_Skills'); ?>
                                    <span class="menu__icon"></span>
                                </a>
                            </li>
                            <?php if ($isPayoutMethodActive) { ?>
                                <li class="menu__item">
                                    <a href="javascript:void(0);" class="teacher-bankinfo-js" onClick="<?php echo $paymentFormAction; ?>">
                                        <?php echo Label::getLabel('LBL_Payments'); ?>
                                        <span class="menu__icon"></span>
                                    </a>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        <li class="menu__item">
                            <a href="javascript:void(0);" onClick="changePasswordForm()">
                                <?php echo Label::getLabel('LBL_Password_/_Email'); ?>
                                <span class="menu__icon"></span>
                            </a>
                        </li>
                        <li class="menu__item">
                            <a href="javascript:void(0);" onClick="getCookieConsentForm(false)">
                                <?php echo Label::getLabel('LBL_cookie_consent'); ?>
                                <span class="menu__icon"></span>
                            </a>
                        </li>
                       <!--  <li class="menu__item">
                            <a href="javascript::void(0)" onclick="DeleteAccountForm();">
                                <?php echo Label::getLabel('LBL_Delete_My_Account'); ?>
                                <span class="menu__icon"></span>
                            </a>
                        </li> -->
                        <!-- <li class="menu__item">
                            <a href="#">
                        <?php echo Label::getLabel('LBL_Deactivate_Account'); ?>
                                <span class="menu__icon"></span>
                            </a>
                        </li> -->
                    </ul>
                </nav>
            </div>
            <div class="page-panel__large">
                <div class="content-panel" id="formBlock-js">
                    <?php echo Label::getLabel('LBL_Loading..'); ?>
                </div>
            </div>
        </div>
        <!-- ] -->
    </div>
    <!-- ] -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />