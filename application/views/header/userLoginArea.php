<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (UserAuthentication::isUserLogged()) { ?>
    <li class="nav__dropdown nav__dropdown--account">
        <a href="javascript:void(0)" class="nav__dropdown-trigger nav__dropdown-trigger-js">
            <div class="avtar avtar--xsmall -display-inline" data-title="<?php echo CommonHelper::getFirstChar(UserAuthentication::getLoggedUserAttribute('user_first_name')); ?>">
                <?php
                if (true == User::isProfilePicUploaded()) {
                    echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array(UserAuthentication::getLoggedUserId())) . '?' . time() . '" alt="" />';
                }
                ?>
            </div>
            <span class="-display-inline"><?php echo $userName; ?></span></a>
        <div class="nav__dropdown-target nav__dropdown-target-js -skin">
            <a href="javascript:void(0)" class="-link-close nav__dropdown-trigger-js -hide-desktop -show-mobile"></a>
            <!--desktop account nav start here-->
            <nav class="nav nav--vertical -hide-responsive">
                <ul>
                    <?php if (User::canViewTeacherTab() && User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD) { ?>
                        <li class="<?php echo ( "Teacher" == $controllerName ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Teacher', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                        <li class="<?php echo ( "TeacherStudents" == $controllerName ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('TeacherStudents', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Students'); ?></a></li>
                        <li class="<?php echo ( "TeacherScheduledLessons" == $controllerName ) ? 'is-active' : ''; ?>" ><a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                        <?php
                    }
                    if (User::canViewLearnerTab() && User::getDashboardActiveTab() == User::USER_LEARNER_DASHBOARD) {
                        ?>
                        <li class="<?php echo ( "Learner" == $controllerName ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Learner', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Dashboard'); ?></a></li>
                        <li class="<?php echo ( "LearnerTeachers" == $controllerName ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('LearnerTeachers', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_My_Teachers'); ?></a></li>
                        <li class="<?php echo ( "LearnerScheduledLessons" == $controllerName ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('learnerScheduledLessons', '', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Lessons'); ?></a></li>
                    <?php } ?>
                    <li class="<?php echo ( "Account" == $controllerName && "profileInfo" == $action ) ? 'is-active' : ''; ?>"><a href="<?php echo CommonHelper::generateUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD); ?>"><?php echo Label::getLabel('LBL_Settings'); ?></a></li>
                    <li><a href="<?php echo CommonHelper::generateUrl('GuestUser', 'logout'); ?>"><?php echo Label::getLabel('LBL_Logout'); ?></a></li>
                </ul>
            </nav>
            <!--desktop account nav end here-->
        </div>
        <span class="overlay overlay-dasboard"></span>
    </li>
<?php } else { ?>
    <li class="-hide-mobile">
        <a href="javascript:void(0)" onClick="signUpFormPopUp();"><?php echo Label::getLabel('LBL_Sign_Up'); ?><img src="<?php echo CONF_WEBROOT_URL; ?>images/user.svg" alt="<?php echo Label::getLabel('LBL_Sign_Up'); ?>" class="-hide-desktop -show-mobile"></a>
    </li>
    <li class="user-click">
        <a href="javascript:void(0)" onClick="logInFormPopUp();" class="btn btn--primary"><?php echo Label::getLabel('LBL_Login'); ?><span class="svg-icon user-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="38" viewBox="0 0 40 38">
                <path id="Forma_1" data-name="Forma 1" class="cls-1" d="M19.934,21.326A10.663,10.663,0,1,0,9.228,10.647,10.695,10.695,0,0,0,19.934,21.326Zm0-18.541a7.862,7.862,0,1,1-7.882,7.862A7.9,7.9,0,0,1,19.934,2.784ZM1.412,38H38.588A1.4,1.4,0,0,0,40,36.591a13.431,13.431,0,0,0-13.432-13.4H13.432A13.431,13.431,0,0,0,0,36.591,1.4,1.4,0,0,0,1.412,38Zm12.02-11.99H26.568a10.592,10.592,0,0,1,10.509,9.172H2.923A10.62,10.62,0,0,1,13.432,26.01Z" />
                </svg>
            </span></a>
    </li>
<?php } ?>
