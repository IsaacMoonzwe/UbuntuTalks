<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$reportSearchForm->addFormTagAttribute('onsubmit', "getStatisticalData(this); return (false);");
$durationType = $reportSearchForm->getField('duration_type');
$durationType->addFieldTagAttribute('onChange', 'getStatisticalData(this.form); return (false);');
$reportType = $reportSearchForm->getField('report_type[]');
$reportType->addFieldTagAttribute('class', 'd-none');
$frmSrch->setFormTagAttribute('onsubmit', 'searchLessons(this); return(false);');
$frmSrch->setFormTagAttribute('class', 'd-none');
$nowDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $userTimezone);
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="dashboard">
    <div class="dashboard__primary">
        <div class="page__head">
            <h1><?php echo Label::getLabel('LBL_Dashboard') ?></h1>
        </div>
        <div class="page__body">
            <?php if (false == $teacherProfileProgress['isProfileCompleted']) { ?>
                <!-- [ INFO BAR ========= -->
                <div class="infobar infobar--primary">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-sm-8 col-lg-6 col-xl-8">
                            <div class="d-flex">
                                <div class="infobar__content">
                                    <h6 class="margin-bottom-1"><?php echo str_replace('{user-first-name}', $userDetails['user_first_name'], Label::getLabel('LBL_TEACHER_DASHBOARD_HEADING_{user-first-name}')); ?></h6>
                                    <p class="margin-0"><?php echo Label::getLabel('LBL_TEACHER_DASHBOARD_INFO_TEXT'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-3  col-xl-4">
                            <div class="-align-right">
                                <a href="<?php echo CommonHelper::generateUrl('Account', 'ProfileInfo'); ?>" class="btn bg-secondary"><?php echo Label::getLabel('Lbl_Complete_Profile') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] -->
            <?php } ?>
            <div class="stats-row margin-bottom-6 align-item-stretch">
                <div class="row align-items-center">
                    <div class="col-lg-4 col-md-6 col-sm-6 d-none">
                        <div class="stat">
                            <div class="stat__amount">
                                <span><?php echo Label::getLabel('Lbl_Earnings'); ?></span>
                                <h5><?php echo CommonHelper::displayMoneyFormat($earningData['earning']); ?></h5>
                            </div>
                            <div class="stat__media bg-yellow">
                                <svg class="icon icon--money icon--40 color-white"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#stats'; ?>"></use></svg>
                            </div>
                            <a href="#" class="stat__action"></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="stat">
                            <div class="stat__amount">
                                <span><?php echo Label::getLabel('LBL_Scheduled'); ?></span>
                                <h5><?php echo $userDetails['teacherSchLessons']; ?></h5>
                            </div>
                            <div class="stat__media bg-secondary">
                                <svg class="icon icon--money icon--40 color-white"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#stats_1'; ?>"></use></svg>
                            </div>
                            <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons'); ?>" class="stat__action"></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="stat">
                            <div class="stat__amount">
                                <span> <?php echo Label::getLabel('LBL_Wallet'); ?></span>
                                <h5><?php echo CommonHelper::displayMoneyFormat($userTotalWalletBalance); ?></h5>
                            </div>
                            <div class="stat__media bg-primary">
                                <svg class="icon icon--money icon--40 color-white"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#stats_2'; ?>"></use></svg>
                            </div>
                            <a href="<?php echo CommonHelper::generateUrl('Wallet'); ?>" class="stat__action"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-panel">
                <?php
                echo $reportSearchForm->getFormTag();
                echo $reportType->getHTML();
                echo $reportSearchForm->getFieldHtml('forGraph');
                ?>
                <div class="page-panel__head border-bottom-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h4><?php echo Label::getLabel('Lbl_Sale_Statistics'); ?></h4>
                        </div>
                        <div class="col-md-6">
                            <div class="sale-stat__select">
                                <div class="form-inline__item">
                                    <?php echo $durationType->getHTML(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-panel__body">
                    <div class="row margin-bottom-6">
                        <div class="col-lg-6 col-md-6 col-sm-6 d-none">
                            <div class="sale-stat sale-stat--primary sale-stat--yellow">
                                <div class="sale-stat__count">
                                    <span><?php echo Label::getLabel('Lbl_Sales'); ?></span>
                                    <h5 class="earing-amount-js"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="sale-stat sale-stat--secondary sale-stat--sky">
                                <div class="sale-stat__count">
                                    <span><?php echo Label::getLabel('LBL_Lessons_sold'); ?></span>
                                    <h5 class="lessons-sold-count-js"></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $reportSearchForm->getExternalJS(); ?>
                    <div class="graph-media" id="chart_div">
                    </div>
                </div>
            </div>
            </form>
        </div>
        <!-- <div class="page__footer align-center">
                <p class="small">Copyright © 2021 Yo!Coach Developed by <a href="#" class="underline color-primary">FATbit Technologies</a> . </p>
        </div> -->
    </div>
    <div class="dashboard__secondary">
        <div class="status-bar">
            <div class="status-bar__head">
                <div class="status-title">
                    <h5><?php echo Label::getLabel('LBL_Upcoming_Lessons'); ?></h5>
                    <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons') . "#" . ScheduledLesson::STATUS_UPCOMING; ?>" class="color-secondary underline padding-top-3 padding-bottom-3"><?php echo Label::getLabel('LBL_View_All'); ?></a>
                </div>
                <div class="calendar">
                    <div id='d_calendar' class="dashboard-calendar calendar-view"></div>
                </div>
            </div>
            <div class="status-bar__body">
                <?php echo $frmSrch->getFormHtml(); ?>
                <div class="listing-window" id="listItemsLessons">
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<!-- ] -->
<script>
    moreLinkTextLabel = '<?php echo Label::getLabel('LBL_VIEW_MORE'); ?>';
    var fecal = new FatEventCalendar(0, '<?php echo MyDate::displayTimezoneString(); ?>');
    fecal.TeacherMonthlyCalendar('<?php echo date('Y-m-d H:i:s', strtotime($nowDate)); ?>', 1);
</script>