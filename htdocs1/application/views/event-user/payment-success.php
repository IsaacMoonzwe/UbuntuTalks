<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--grey section--page">
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <div class="page__panel-narrow">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="box -padding-30 -skin">
                            <div class="message-display">
                                <div class="message-display__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
                                        <path fill="#000" d="M105.823,52.206a4.4,4.4,0,0,0-4.4,4.393v4.425a43.258,43.258,0,0,1-43.3,43.188H58.092a43.213,43.213,0,1,1,.024-86.426h0.026a43.111,43.111,0,0,1,17.6,3.741A4.395,4.395,0,1,0,79.325,13.5,51.871,51.871,0,0,0,58.147,9H58.116a52,52,0,1,0-.029,104h0.031a52.054,52.054,0,0,0,52.108-51.973V56.6A4.4,4.4,0,0,0,105.823,52.206Z" transform="translate(-0.516 -1)" />
                                        <path class="-color-fill" d="M113.706,15.075a4.409,4.409,0,0,0-6.226,0L58.117,64.335,46.918,53.16a4.4,4.4,0,0,0-6.226,6.213L55,73.655a4.409,4.409,0,0,0,6.226,0l52.476-52.367A4.386,4.386,0,0,0,113.706,15.075Z" transform="translate(-0.516 -1)" />
                                    </svg>
                                </div>
                                <span class="-gap"></span>
                                <h1 class="-color-secondary"><?php echo $title_message ?? Label::getLabel('MSG_Thankyou_for_Purchase'); ?></h1>
                                <div class="-color-secondary">
                                    <h5><?php echo $message; ?></h5>
                                </div>
                                <div class="payment-success">
                                    <?php //echo FatUtility::decodeHtmlEntities(($textMessage)) ?? Label::getLabel('MSG_Your_Order_has_been_successfully_Placed'); 
                                    ?>
                                </div>

                                <!-- <?php if (isset($orderType) and $orderType == Order::TYPE_WALLET_RECHARGE) { ?>
                                    <a href="<?php echo CommonHelper::generateUrl('wallet', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_Go_to_Wallet'); ?></a>
                                <?php } elseif (isset($orderType) and $orderType == Order::TYPE_GIFTCARD) { ?>
                                    <a href="<?php echo CommonHelper::generateUrl('giftcard', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_Go_to_Giftcards_Purchased'); ?></a>
                                <?php } elseif (!empty($lessonInfo) && $lessonInfo['slesson_grpcls_id'] > 0 && $_SESSION['fromKids'] <= 0) { ?>
                                    <h5><?php echo Label::getLabel('MSG_You_can_view_classes_here'); ?></h5>
                                    <a href="<?php echo CommonHelper::generateUrl('learnerScheduledLessons', 'index', [applicationConstants::CLASS_TYPE_GROUP], CONF_WEBROOT_DASHBOARD) . '#' . ScheduledLesson::STATUS_SCHEDULED; ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_Navigate_to_classes'); ?></a>
                                <?php } elseif (!empty($lessonInfo) && ($lessonInfo['op_lpackage_is_free_trial'] == 1)) { ?>
                                    <a href="<?php echo CommonHelper::generateUrl('learnerScheduledLessons', '', [], CONF_WEBROOT_DASHBOARD) . '#' . ScheduledLesson::STATUS_SCHEDULED; ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_Go_to_my_Lessons'); ?></a>
                                <?php } elseif (!empty($lessonInfo) && $lessonInfo['op_qty'] > 0) { ?>
                                    <h5><?php echo Label::getLabel('MSG_You_can_view_classes_here'); ?></h5>
                                    <a href="<?php echo CommonHelper::generateUrl('learnerScheduledLessons', 'index', [], CONF_WEBROOT_DASHBOARD) . '#' . ScheduledLesson::STATUS_NEED_SCHEDULING; ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_Go_to_my_Lessons'); ?></a>
                                    <?php if ($lessonInfo['slesson_status'] != ScheduledLesson::STATUS_SCHEDULED && UserAuthentication::checkLogin(false)) { ?>
                                        <div class="-gap"></div>
                                        <a href="javascript:void(0);" onclick="viewBookingCalendar('<?php echo $lessonInfo['sldetail_id']; ?>','');" class="btn btn--secondary"><?php echo Label::getLabel('MSG_You_Can_Schdule_Lesson'); ?></a>
                                    <?php } ?>
                                <?php } else { ?>
                                    <h5><?php echo Label::getLabel('MSG_VIEW_YOUR_ORDER'); ?></h5>
                                    <a href="<?php echo CommonHelper::generateUrl('Learner', 'orders', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary"><?php echo Label::getLabel('MSG_GO_TO_MY_ORDERS'); ?></a>
                                <?php } ?> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>