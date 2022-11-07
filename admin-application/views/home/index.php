<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); /* CommonHelper::printArray(json_encode( array_values($dashboardInfo['signupsChartData']) ));die; */ ?>
<script>
    $SalesChartKey = <?php echo json_encode(array_keys($dashboardInfo['salesChartData'])); ?>;
    $SalesChartVal = <?php echo json_encode(array_values($dashboardInfo['salesChartData'])); ?>;
    $signupsKey = <?php echo json_encode(array_keys($dashboardInfo['signupsChartData'])); ?>;
    $signupsVal = <?php echo json_encode(array_values($dashboardInfo['signupsChartData'])); ?>;
    $earningsKey = <?php echo json_encode(array_keys($dashboardInfo['earningsChartData'])); ?>;
    $earningsVal = <?php echo json_encode(array_values($dashboardInfo['earningsChartData'])); ?>;
</script>
<script src="https://www.google.com/jsapi"></script>
<?php if ($canView) { ?>
    <!--main panel start here-->
    <div class="page">
        <div class="container container-fluid">
            <div class="gap"></div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_1.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Total_Revenue_from_lessons', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="1" data-count="<?php echo $dashboardInfo["stats"]["totalSales"][4]["totalsales"]; ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo CommonHelper::displayMoneyFormat($dashboardInfo["stats"]["totalSales"][2]["totalsales"]); ?></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('purchasedLessons'); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_2.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Total_Earnings_to_Admin', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="1" data-count="<?php echo isset($dashboardInfo["stats"]["totalEarnings"][-1]["totalEarnings"]) ? $dashboardInfo["stats"]["totalEarnings"][-1]["totalEarnings"] : 0; ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo CommonHelper::displayMoneyFormat(isset($dashboardInfo["stats"]["totalEarnings"][30]["totalEarnings"]) ? $dashboardInfo["stats"]["totalEarnings"][30]["totalEarnings"] : 0) ?></span></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('salesReport'); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_3.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Total_Users', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $dashboardInfo["stats"]["totalUsers"]['-1']; ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $dashboardInfo["stats"]["totalUsers"]['30']; ?></strong></p>
                            <?php if ($objPrivilege->canViewUsers(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('users'); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_3.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Total_Events_Visitors', $adminLangId); ?>
                            </h6>
                            <?php
                            $count = array();
                            $monthly_count = 0;
                            foreach ($EventUserListing as $value) {
                                $countUser = $value['user_added_on'];
                                $UserListing = array_push($count, $countUser);
                                $TotalEventUser = sizeof($count);
                                $date = date('Y-m-d H:i:s');
                                $currentUserMonth = date('m', strtotime($value['user_added_on']));
                                $currentUserYear = date('Y', strtotime($value['user_added_on']));   
                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                if ($currentMonth == $currentUserMonth && $currentUserYear == $currentYear) {
                                    $monthly_count++;
                                }
                            }
                            ?>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $TotalEventUser; ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $monthly_count; ?></strong></p>
                            <?php if ($objPrivilege->canViewUsers(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('EventUsers'); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gap"></div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_4.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Total_lessons', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $dashboardInfo["stats"]["totalLessons"]['-1'] ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $dashboardInfo["stats"]["totalLessons"]['30'] ?></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('purchasedLessons', 'viewSchedules'); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_4.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Completed_lessons', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $dashboardInfo["stats"]["totalCompletedLessons"]['-1'] ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $dashboardInfo["stats"]["totalCompletedLessons"]['30'] ?></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('purchasedLessons', 'viewSchedules', [ScheduledLesson::STATUS_COMPLETED]); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_4.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Cancelled_lessons', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $dashboardInfo["stats"]["totalCancelledLessons"]['-1'] ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $dashboardInfo["stats"]["totalCancelledLessons"]['30'] ?></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('purchasedLessons', 'viewSchedules', [ScheduledLesson::STATUS_CANCELLED]) ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3">
                    <div class="box box--white box--stats">
                        <div class="box__body">
                            <img src="<?php echo CONF_WEBROOT_URL ?>images/stats_icon_4.svg" alt="" class="stats__icon">
                            <h6 class="-txt-uppercase">
                                <?php echo Label::getLabel('LBL_Need_to_be_Schedule_lessons', $adminLangId); ?>
                            </h6>
                            <h3 class="counter" data-currency="0" data-count="<?php echo $dashboardInfo["stats"]["totalNeedtoScheduleLessons"]['-1'] ?>">0</h3>
                            <p>
                                <?php echo Label::getLabel('LBL_This_Month', $adminLangId); ?> <strong>
                                    <?php echo $dashboardInfo["stats"]["totalNeedtoScheduleLessons"]['30'] ?></strong></p>
                            <?php if ($objPrivilege->canViewPurchasedLessons(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                                <a href="<?php echo CommonHelper::generateUrl('purchasedLessons', 'viewSchedules', [ScheduledLesson::STATUS_NEED_SCHEDULING]); ?>" class="stats__link"></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gap"></div>
            <div class="grid grid--tabled">
                <div class="grid__left">
                    <div class="box">
                        <div class="box__head">
                            <h4>
                                <?php echo Label::getLabel('LBL_Statistics', $adminLangId); ?>
                            </h4>
                        </div>
                        <div class="box__body">
                            <div class="tabs_nav_container">
                                <ul class="nav nav--floated -clearfix theme--hovercolor statistics-nav-js">
                                    <li><a class="active" rel="tabs_1" data-chart="true" href="javascript:void(0)">
                                            <?php echo Label::getLabel('LBL_Total_Earning_From_Lessons', $adminLangId); ?></a></li>
                                    <li><a rel="tabs_2" data-chart="true" href="javascript:void(0)">
                                            <?php echo Label::getLabel('LBL_Total_Commisions_From_Lessons', $adminLangId); ?></a></li>
                                    <li><a rel="tabs_3" data-chart="true" href="javascript:void(0)">
                                            <?php echo Label::getLabel('LBL_Total_Sign_ups', $adminLangId); ?></a></li>
                                </ul>
                                <div class="tabs_panel_wrap statistics-tab-js">
                                    <!--tab1 start here-->
                                    <div id="tabs_1" class="tabs_panel" style="width:100%;height:100%">
                                        <div id="monthlysales--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                                    </div>
                                    <!--tab1 end here-->
                                    <!--tab2 start here-->
                                    <div id="tabs_2" class="tabs_panel" style="width:100%;height:100%">
                                        <div id="monthlysalesearnings--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                                    </div>
                                    <!--tab2 end here-->
                                    <!--tab3 start here-->
                                    <div id="tabs_3" class="tabs_panel" style="width:100%;height:100%">
                                        <div id="monthly-signups--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                                    </div>
                                    <!--tab3 end here-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid__right">
                    <div class="box--scroll">
                        <div class="box__head">
                            <h4>
                                <?php echo Label::getLabel('LBL_Top_Lesson_Languages', $adminLangId); ?>
                            </h4>
                            <ul class="actions right">
                                <li class="droplink">
                                    <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                    <div class="dropwrap">
                                        <ul class="linksvertical">
                                            <li><a href="javascript:void(0)" onClick="getTopLessonLanguage('today')">
                                                    <?php echo Label::getLabel('LBL_Today', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="getTopLessonLanguage('Weekly')">
                                                    <?php echo Label::getLabel('LBL_Weekly', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="getTopLessonLanguage('Monthly')">
                                                    <?php echo Label::getLabel('LBL_Monthly', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="getTopLessonLanguage('Yearly')">
                                                    <?php echo Label::getLabel('LBL_Yearly', $adminLangId); ?></a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="box__body">
                            <div class="scrollbar scrollbar-js">
                                <ul class="list list--vertical theme--txtcolor theme--hovercolor topLessonLanguage">
                                    <?php
                                    $count = 1;
                                    if (count($dashboardInfo['topLessonLanguage']) > 0) {
                                        foreach ($dashboardInfo['topLessonLanguage'] as $row) {
                                            if ($count > 11) {
                                                break;
                                            }
                                    ?>
                                            <li>
                                                <?php echo ($row['languageName'] == '') ? Label::getLabel('LBL_Blank_Search', $adminLangId) : $row['languageName']; ?> <span>
                                                    <?php echo $row['lessonsSold']; ?></span></li>
                                    <?php
                                            $count++;
                                        }
                                    } else {
                                        echo Label::getLabel('LBL_No_Record_Found', $adminLangId);
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gap"></div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="box box--white box--height">
                        <div class="box__head">
                            <h4>
                                <?php echo Label::getLabel('LBL_Visitors_Statistics', $adminLangId); ?>
                            </h4>
                        </div>
                        <div class="box__body space">
                            <div class="graph-container">
                                <div id="visitsGraph" class="ct-chart ct-perfect-fourth graph--visitor"></div>
                            </div>
                            <?php if (isset($dashboardInfo['visitsCount'])) { ?>
                                <ul class="horizontal_grids">
                                    <li>
                                        <?php echo $dashboardInfo['visitsCount']['today'] ?> <span>
                                            <?php echo Label::getLabel('LBL_Today', $adminLangId); ?></span></li>
                                    <li>
                                        <?php echo $dashboardInfo['visitsCount']['weekly'] ?> <span>
                                            <?php echo Label::getLabel('LBL_Weekly', $adminLangId); ?></span></li>
                                    <li>
                                        <?php echo $dashboardInfo['visitsCount']['lastMonth'] ?><span>
                                            <?php echo Label::getLabel('LBL_last_Month', $adminLangId); ?></span></li>
                                    <li>
                                        <?php echo $dashboardInfo['visitsCount']['last3Month'] ?><span>
                                            <?php echo Label::getLabel('LBL_Last_3_Months', $adminLangId); ?></span></li>
                                </ul>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="box box--white box--height">
                        <div class="box__head">
                            <h4>
                                <?php echo Label::getLabel('LBL_Traffic', $adminLangId); ?>
                            </h4>
                            <ul class="actions right">
                                <li class="droplink">
                                    <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                    <div class="dropwrap">
                                        <ul class="linksvertical">
                                            <li><a href="javascript:void(0)" onClick="traficSource('today')">
                                                    <?php echo Label::getLabel('LBL_Today', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="traficSource('Weekly')">
                                                    <?php echo Label::getLabel('LBL_Weekly', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="traficSource('Monthly')">
                                                    <?php echo Label::getLabel('LBL_Monthly', $adminLangId); ?></a></li>
                                            <li><a href="javascript:void(0)" onClick="traficSource('Yearly')">
                                                    <?php echo Label::getLabel('LBL_Yearly', $adminLangId); ?></a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="box__body space">
                            <!--<div class="graph-container"><img src="images/traffic_graph.jpg" style="margin:auto;" alt=""></div>-->
                            <div class="graph-container">
                                <div id="piechart" class="ct-chart ct-perfect-fourth graph--traffic"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="page">
        <div class="container container-fluid">
            <div class="row"></div>
        </div>
    </div>
<?php } ?>
<script>
    var dataCurrencyLeft = '<?php echo CommonHelper::getCurrencySymbolLeft(); ?>';
    var dataCurrencyRight = '<?php echo CommonHelper::getCurrencySymbolRight(); ?>';
    var w = $('.tabs_panel_wrap').width();
    google.load('visualization', '1', {
        'packages': ['corechart', 'bar']
    });
</script>