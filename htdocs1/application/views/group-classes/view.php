<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langId = CommonHelper::getLangId();
$websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $langId, FatUtility::VAR_STRING, '');
$userTimezone = MyDate::getUserTimeZone();
$startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $class['grpcls_start_datetime'], true, $userTimezone);
$endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $class['grpcls_end_datetime'], true, $userTimezone);
$startDateTimeUnix = strtotime($startDateTime);
$endDateTimeUnix = strtotime($endDateTime);
$seatsLeft = $class['grpcls_max_learner'] - $class['total_learners'];
$loggedUserId = UserAuthentication::getLoggedUserId(true);
$myDate = new myDate();
$myDate->setMonthAndweekName($siteLangId);
?>
<title><?php echo Label::getLabel('LBL_Group_Class') . $class['grpcls_title'] . " " . Label::getLabel('LBL_on') . " " . $websiteName; ?></title>
<!-- [ MAIN BODY ========= -->
<section class="section padding-top-0 group--detail">
    <div class="container container--fixed">
        <div class="breadcrumb-list">
            <ul>
                <li><a href="<?php echo CommonHelper::generateUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                <li><a href="<?php echo CommonHelper::generateUrl('GroupClasses'); ?>"><?php echo Label::getLabel('LBL_Group_Classes'); ?></a></li>
                <li><?php echo $class['grpcls_title']; ?></li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="group-primary">
                    <div class="group-primary__head">
                        <h1><?php echo $class['grpcls_title']; ?></h1>
                        <span class="date"><?php echo $myDate->convertToLocal(date('M, d Y', $startDateTimeUnix)); ?></span>
                    </div>
                    <div class="group-primary__body">
                        <div class="group-listing">
                            <ul>
                                <li>
                                    <svg class="icon icon--globe">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#globe'; ?>"></use>
                                    </svg>
                                    <p><b><?php echo Label::getLabel('LBL_LANGUAGE'); ?></b> - <?php echo $class['teacher_language']; ?></p>
                                </li>
                                <li>
                                    <svg class="icon icon--clock">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock'; ?>"></use>
                                    </svg>
                                    <p><b><?php echo Label::getLabel("LBL_Time") ?> </b> - <?php echo $myDate->convertToLocal(date('h:i A', $startDateTimeUnix)) . ' - ' . $myDate->convertToLocal(date('h:i A', $endDateTimeUnix)); ?></p>
                                </li>
                                <li>
                                    <svg class="icon icon--seat">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#seat'; ?>"></use>
                                    </svg>
                                    <p><b><?php echo Label::getLabel('LBL_TOTAL_SEATS'); ?> </b> - <?php echo $class['grpcls_max_learner']; ?></p>
                                </li>
                                <li>
                                    <svg class="icon icon--tag">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#tag'; ?>"></use>
                                    </svg>
                                    <p><b><?php echo Label::getLabel("LBL_Price") ?> </b> - <?php echo CommonHelper::displayMoneyFormat($class['grpcls_entry_fee']); ?></p>
                                </li>
                            </ul>
                        </div>
                        <div class="group-actions">
                            <?php if ($class['is_in_class']) : ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_ALREADY_IN_CLASS') ?>" class="btn btn--primary btn--large color-white btn--disabled"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php elseif ($class['total_learners'] >= $class['grpcls_max_learner']) : ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_FULL') ?>" class="btn btn--primary btn--large color-white btn--disabled"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php elseif ($class['grpcls_start_datetime'] < date('Y-m-d H:i:s', strtotime('+' . $min_booking_time . ' minutes'))) : ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Booking_Close_For_This_Class') ?>" class="btn btn--primary btn--large color-white btn--disabled"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php elseif ($class['grpcls_teacher_id'] == $loggedUserId) : ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Can_not_join_own_classes') ?>" class="btn btn--primary btn--large color-white btn--disabled"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php elseif ($class['grpcls_status'] != TeacherGroupClasses::STATUS_ACTIVE) : ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Class_Not_active') ?>" class="btn btn--primary btn--large color-white btn--disabled"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php else : ?>
                                <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId:<?php echo $class['grpcls_teacher_id']; ?>, grpclsId:<?php echo $class['grpcls_id'] ?>, languageId: <?php echo $class['grpcls_tlanguage_id'] ?>}, 'groupBookingForm');" class="btn btn--primary btn--large color-white"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                            <?php endif; ?>
                            <a href="javascript:void(0);" class="seat-left"><?php echo sprintf(Label::getLabel('LBL_Only_%s_Seats_Left'), $seatsLeft); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="course-details">
                    <h3><?php echo Label::getLabel('LBL_COURSE_DETAILS'); ?></h3>
                    <p><?php echo $class['grpcls_description'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="group-secondary">
                    <div class="box">
                        <div class="box__body">
                            <h3><?php echo Label::getLabel('LBL_ABOUT_THE_HOST'); ?></h3>
                            <div class="box-profile">
                                <div class="tile">
                                    <div class="tile__head">
                                        <div class="tile__media ratio ratio--1by1">
                                            <?php if (true == User::isProfilePicUploaded($class['user_id'])) { ?>
                                                <img src="<?php echo CommonHelper::generateUrl('Image', 'User', array($class['user_id'], 'ORIGINAL')) ?>" alt="">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tile__body">
                                        <a class="tile__title" href="<?php echo CommonHelper::generateUrl('Teachers', 'view', array(CommonHelper::htmlEntitiesDecode($class['user_url_name']))); ?>">
                                            <h4><?php echo $class['user_first_name'] . ' ' . $class['user_last_name']; ?></h4>
                                        </a>
                                        <div class="info-wrapper">
                                            <div class="info-tag location">
                                                <svg class="icon icon--location">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#location'; ?>"></use>
                                                </svg>
                                                <span class="lacation__name"><?php echo $class['country_name']; ?></span>
                                            </div>
                                            <?php if ($class['testat_ratings'] > 0) { ?>
                                                <div class="info-tag ratings">
                                                    <svg class="icon icon--rating">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#rating'; ?>"></use>
                                                    </svg>
                                                    <span class="value"><?php echo $class['testat_ratings']; ?></span>
                                                    <span class="count">(<?php echo $class['testat_reviewes']; ?>)</span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $contactClick = 'onclick="generateThread(' . $class['user_id'] . ');"';
                        $disabledClass = '';;
                        if ($loggedUserId == $class['user_id']) {
                            $disabledClass = 'disabled';
                            $contactClick = '';
                        }
                        ?>
                        <div class="box-actions">
                            <a href="javascript:void(0);" <?php echo $contactClick; ?> class="btn color-primary <?php echo $disabledClass; ?>">
                                <svg class="icon icon--email_1">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#email_1'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_CONTACT'); ?>
                            </a>
                            <a href="<?php echo CommonHelper::generateUrl('Teachers', 'view', array(CommonHelper::htmlEntitiesDecode($class['user_url_name']))); ?>" class="btn color-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18.375" viewBox="0 0 14 18.375">
                                    <path d="M18,19.375H16.25v-1.75A2.625,2.625,0,0,0,13.625,15H8.375A2.625,2.625,0,0,0,5.75,17.625v1.75H4v-1.75A4.375,4.375,0,0,1,8.375,13.25h5.25A4.375,4.375,0,0,1,18,17.625ZM11,11.5a5.25,5.25,0,1,1,5.25-5.25A5.25,5.25,0,0,1,11,11.5Zm0-1.75a3.5,3.5,0,1,0-3.5-3.5A3.5,3.5,0,0,0,11,9.75Z" transform="translate(-4 -1)"></path>
                                </svg>
                                <?php echo Label::getLabel('LBL_PROFILE'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>