<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php if ($classes) { ?>
    <section class="section section--upcoming-class">
        <div class="container container--narrow">
            <div class="section__head d-flex justify-content-between align-items-center">
                <h2><?php echo Label::getLabel('LBL_Upcoming_Group_Classes', $siteLangId); ?></h2>
                <a class="view-all" href="<?php echo CommonHelper::generateUrl('GroupClasses'); ?>"><?php echo Label::getLabel("LBL_View_all", $siteLangId); ?></a>
            </div>
            <?php $i = 1; ?>
            <div class="section__body">
                <div class="slider slider--onethird slider-onethird-js">
                    <?php foreach ($classes as $classesDetails) {
                        $user_timezone = MyDate::getUserTimeZone();
                        $startTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $classesDetails['grpcls_start_datetime'], true, $user_timezone);
                        $curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
                        $startUnixTime = strtotime($startTime);
                        $currentUnixTime = strtotime($curDateTime);
                    ?>
                        <div>
                            <div class="slider__item">
                                <div class="card card--bg">
                                    <div class="card__head">
                                        <h3><?php echo $classesDetails['grpcls_title']; ?></h3>
                                    </div>
                                    <div class="card__body">
                                        <div class="card__row">
                                            <span><?php echo Label::getLabel('LBL_Date_&_Time', $siteLangId) ?></span>
                                            <?php
                                            $user_timezone = MyDate::getUserTimeZone();
                                            $date_by_user_timezone = MyDate::convertTimeFromSystemToUserTimezone('M d, Y',  $classesDetails['grpcls_start_datetime'], true, $user_timezone);
                                            $from_time_by_user_timezone = MyDate::convertTimeFromSystemToUserTimezone('h:i A',  $classesDetails['grpcls_start_datetime'], true, $user_timezone);
                                            $to_time_by_user_timezone = MyDate::convertTimeFromSystemToUserTimezone('h:i A',  $classesDetails['grpcls_end_datetime'], true, $user_timezone);
                                            ?>
                                            <p><?php echo $date_by_user_timezone . ',' . $from_time_by_user_timezone . '-' . $to_time_by_user_timezone; ?></p>
                                        </div>
                                        <div class="card__row">
                                            <span><?php echo Label::getLabel('LBL_Tutor', $siteLangId); ?></span>
                                            <p><?php echo  $classesDetails['user_full_name']; ?></p>
                                        </div>
                                        <div class="card__row">
                                            <span><?php echo Label::getLabel("LBL_Price",$siteLangId) ?></span>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="class-price"><?php echo CommonHelper::displayMoneyFormat($classesDetails['grpcls_entry_fee']) ?></p>
                                                <div class="timer">

                                                    <?php if ($startUnixTime > $currentUnixTime) { ?>
                                                        <div class="timer__media">
                                                            <span> <svg class="icon icon--clock">
                                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL.'images/sprite.yo-coach.svg#clock'; ?>"></use>
                                                                </svg></span>
                                                        </div>
                                                        <div class="timer__controls countdowntimer timer-js" id="grup-class_<?php echo $i; ?>" data-startTime="<?php echo $curDateTime; ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startUnixTime); ?>"></div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="card__row--action">
                                            <a href="<?php echo CommonHelper::generateUrl('GroupClasses', 'view', [CommonHelper::htmlEntitiesDecode($classesDetails['grpcls_slug'])]); ?>" class="btn btn--bordered color-primary"><?php echo Label::getLabel('LBL_View_Details', $siteLangId); ?></a>
                                            <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId:<?php echo $classesDetails['grpcls_teacher_id']; ?>,grpclsId:<?php echo $classesDetails['grpcls_id'] ?>, languageId : <?php echo $classesDetails['grpcls_tlanguage_id'] ?>},'getPaymentSummary');" class="btn btn--primary"><?php echo Label::getLabel("LBL_Book_Now",$siteLangId); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php $i++;
                    } ?>
                </div>
            </div>
        </div>
    </section>
<?php  }
