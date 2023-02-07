<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$colorClass = [
    1 => 'cell-green-40',
    2 => 'cell-green-60',
    3 => 'cell-green-80',
    4 => 'cell-green-100',
    5 => 'cell-lightgreen-50',
    6 => 'cell-lightgreen-50',
    7 => 'cell-lightgreen-50',
    8 => 'cell-lightgreen-50',
    9 => 'cell-lightgreen-50',
];
$hourStringLabel = Label::getLabel('LBL_{hourstring}_HRS');
?>
<?php
$class = '';
if (!isset($langname)) {
    $class = 'd-none';
}
?>
<style>
    .swal-text {
        font-weight: bold;
        font-size: 18px;
    }

    .swal-modal {
        background-color: #fbebcd;
    }

    button.swal-button.swal-button--confirm {
        background-color: #006313;
    }

    button.swal-button.swal-button--confirm:hover {
        background-color: #ce4400;
    }
</style>
<div class="container container--narrow <?php echo $class; ?>">
    <div class="row m-10">
        <div class="col-md-10 col-sm-9">
            <h2>About <?php echo $langname; ?></h2>
            <p id="aboutdesc"><?php echo $aboutdesc['aboutdesc']; ?></p>
        </div>
        <div class="col-md-2 col-sm-3" style="text-align: center;">

            <img src="https://ubuntutalks.com/teachers/thumb/<?php echo $image['afile_record_id'] . "/" . $image['afile_type'] . "/0/0/?" . time(); ?>" style="display: inline-block;">
        </div>
    </div>

</div>
<?php if ($teachers) { ?>
    <div class="sorting__head">
        <div class="sorting__title">
            <div style="font-size: 18px;text-align: center;font-weight: 400;color: #333;">
                To request a personalized group class, <a href="javascript:void(0);" onclick="groupClassesForm();" class="groupcontact">
                    Click here and fill out a form. </a>
            </div>
            <h1><?php echo sprintf(Label::getLabel('LBL_Found_the_best_%s_teachers_for_you', $siteLangId), $recordCount) ?></h1>
        </div>
        <div class="sorting__box">
            <!-- <b>Sort By:</b> -->
            <select name="filterSortBy" id="sort">
                <?php $sortBy = CommonHelper::getSortbyArr(); ?>
                <?php foreach ($sortBy as $filterVal => $filterLabel) { ?>
                    <option <?php echo ($postedData['sortOrder'] == $filterVal) ? "selected='selected'" : ''; ?> value="<?php echo $filterVal; ?>"><?php echo $filterLabel; ?></option>
                <?php } ?>
            </select>
            <div class="btn--filter">
                <a href="javascript:void(0)" class="btn btn--primary btn--filters-js">
                    <span class="svg-icon"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 402.577 402.577" style="enable-background:new 0 0 402.577 402.577;" xml:space="preserve">
                            <g>
                                <path d="M400.858,11.427c-3.241-7.421-8.85-11.132-16.854-11.136H18.564c-7.993,0-13.61,3.715-16.846,11.136
                                      c-3.234,7.801-1.903,14.467,3.999,19.985l140.757,140.753v138.755c0,4.955,1.809,9.232,5.424,12.854l73.085,73.083
                                      c3.429,3.614,7.71,5.428,12.851,5.428c2.282,0,4.66-0.479,7.135-1.43c7.426-3.238,11.14-8.851,11.14-16.845V172.166L396.861,31.413
                                      C402.765,25.895,404.093,19.231,400.858,11.427z"></path>
                            </g>
                        </svg></span>
                    <?php echo Label::getLabel('LBL_Filters', $siteLangId) ?></a></a>
            </div>
        </div>
    </div>
    <div class="listing__body" id="teachers">
        <div class="box-wrapper" id="teachersListingContainer">
            <?php foreach ($teachers as $teacher) { ?>
                <div class="box box-list ">
                    <div class="box__primary">
                        <div class="list__head">
                            <div class="list__media ">
                                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($teacher['user_first_name']); ?>">
                                    <?php if (User::isProfilePicUploaded($teacher['user_id'])) { ?>
                                        <a href="<?php echo CommonHelper::generateUrl('teachers', 'view', [CommonHelper::htmlEntitiesDecode($teacher['user_url_name'])]) ?>"><img src="<?php echo FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'User', array($teacher['user_id'], 'MEDIUM')), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt=""></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="list__price">
                                <p><?php echo CommonHelper::displayMoneyFormat($teacher['minPrice']); ?></p>
                            </div>
                        </div>
                        <div class="list__body">
                            <div class="profile-detail">
                                <div class="profile-detail__head">
                                    <a href="<?php echo CommonHelper::generateUrl('teachers', 'view', [CommonHelper::htmlEntitiesDecode($teacher['user_url_name'])]) ?>" class="tutor-name">
                                        <h4><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h4>
                                        <div class="flag">
                                            <img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($teacher['user_country_id'], 'DEFAULT')); ?>" alt="">
                                        </div>
                                    </a>
                                    <div class="follow ">
                                        <a class="<?php echo ($teacher['uft_id']) ? 'is--active' : ''; ?>" onClick="toggleTeacherFavorite(<?php echo $teacher['user_id']; ?>, this)" href="javascript:void(0)">
                                            <svg class="icon icon--heart">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#heart'; ?>"></use>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="profile-detail__body">
                                    <div class="info-wrapper">
                                        <div class="info-tag location">
                                            <svg class="icon icon--location">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#location'; ?>"></use>
                                            </svg>
                                            <span class="lacation__name"><?php echo $teacher['user_country_name']; ?></span>
                                        </div>
                                        <div class="info-tag ratings">
                                            <svg class="icon icon--rating">
                                                <!-- <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#rating'; ?>"></use> -->
                                            </svg>
                                            <span class="value"><?php //echo FatUtility::convertToType($teacher['teacher_rating'], FatUtility::VAR_FLOAT); 
                                                                ?></span>
                                            <span class="count"><?php //echo '(' . $teacher['totReviews'] . ')'; 
                                                                ?></span>
                                        </div>
                                        <div class="info-tag list-count">
                                            <div class="total-count"><span class="value"><?php //echo $teacher['studentIdsCnt']; 
                                                                                            ?></span><?php //echo Label::getLabel('LBL_Students', $siteLangId); 
                                                                                                        ?></div>
                                            <div class="total-count"><span class="value"><?php //echo $teacher['teacherTotLessons']; 
                                                                                            ?></span><?php //echo Label::getLabel('LBL_Lessons', $siteLangId); 
                                                                                                        ?></div>
                                        </div>
                                    </div>
                                    <div class="tutor-info">
                                        <div class="tutor-info__inner">
                                            <div class="info__title">
                                                <h6><?php Label::getLabel('LBL_Teaches', $siteLangId); ?>Teaches</h6>
                                            </div>
                                            <div class="info__language">
                                                <?php echo $teacher['teacherTeachLanguageName']; ?>
                                            </div>
                                        </div>
                                        <div class="tutor-info__inner">
                                            <div class="info__title">
                                                <h6><?php echo Label::getLabel('LBL_Speaks', $siteLangId); ?></h6>
                                            </div>
                                            <div class="info__language">
                                                <?php echo $teacher['spoken_language_names']; ?>
                                            </div>
                                        </div>
                                        <div class="tutor-info__inner info--about">
                                            <div class="info__title">
                                                <h6><?php echo LABEL::getLabel('LBL_About', $siteLangId); ?></h6>
                                            </div>
                                            <div class="about__detail">
                                                <p><?php echo $teacher['user_profile_info'] ?></p>
                                                <a href="<?php echo CommonHelper::generateUrl('teachers', 'view', [CommonHelper::htmlEntitiesDecode($teacher['user_url_name'])]) ?>"><?php echo Label::getLabel('LBL_View_Profile', $siteLangId) ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list__action">
                            <div class="list__action-btn">
                                <?php
                                $bookclick = "";
                                if (UserAuthentication::isUserLogged()) {
                                    if (UserAuthentication::getLoggedUserAttribute('user_first_name') && UserAuthentication::getLoggedUserAttribute('user_last_name') && UserAuthentication::getLoggedUserAttribute('user_gender') && UserAuthentication::getLoggedUserAttribute('user_phone') && UserAuthentication::getLoggedUserAttribute('user_country_id') && UserAuthentication::getLoggedUserAttribute('user_timezone')) {
                                ?>
                                        <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>}, 'getUserTeachLangues');" class="btn btn--primary color-white btn--block"><?php echo Label::getLabel('LBL_Book_Now', $siteLangId); ?></a>
                                    <?php
                                    } else {
                                    ?>
                                        <a href="javascript:void(0);" onclick="swal({title: 'Ubuntu Talks Says',text: 'Please Complete Your Account Profile..!',}).then(function() {window.location.href = '/dashboard/account/profile-info'});" class="btn btn--primary color-white btn--block"><?php echo Label::getLabel('LBL_Book_Now', $siteLangId); ?></a>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId: <?php echo $teacher['user_id']; ?>}, 'getUserTeachLangues');" class="btn btn--primary color-white btn--block"><?php echo Label::getLabel('LBL_Book_Now', $siteLangId); ?></a>
                                <?php
                                }
                                ?>



                                <!-- <a href="javascript:void(0);" onclick="cart.proceedToStep({teacherId: <?php //echo $teacher['user_id']; 
                                                                                                            ?>}, 'getUserTeachLangues');" class="btn btn--primary color-white btn--block"><?php //echo Label::getLabel('LBL_Book_Now', $siteLangId); 
                                                                                                                                                                                            ?></a> -->
                                <?php
                                $teacherLanguage = key($teacher['teachLanguages']);
                                if ($teacher['isFreeTrialEnabled']) {
                                    $onclick = "";
                                    $btnClass = "btn-secondary";
                                    $disabledText = "disabled";
                                    $btnText = "LBL_You_already_have_availed_the_Trial";
                                    if (!$teacher['isAlreadyPurchasedFreeTrial'] && !empty($teacherLanguage)) {
                                        $disabledText = "";
                                        $onclick = "onclick=\"viewCalendar_teacherlist(" . $teacher['user_id'] . "," . $teacherLanguage . ",'free_trial')\"";
                                        $btnClass = 'btn-primary';
                                        $btnText = "LBL_Book_Free_Trial";
                                    }
                                    if ($loggedUserId == $teacher['user_id']) {
                                        $onclick = "";
                                        $disabledText = "disabled";
                                    }
                                ?>
                                    <a href="javascript:void(0);" <?php echo $onclick; ?> class="btn btn--secondary btn--trial btn--block color-white <?php echo $btnClass . ' ' . $disabledText; ?> " <?php echo $disabledText; ?>>
                                        <span><?php echo Label::getLabel($btnText, $siteLangId); ?></span>
                                    </a>
                                <?php } ?>
                                <a href="javascript:void(0);" onclick="generateThread(<?php echo $teacher['user_id']; ?>);" class="btn btn--bordered color-primary btn--block">
                                    <svg class="icon icon--envelope">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#envelope'; ?>"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_Contact', $siteLangId); ?>
                                </a>
                            </div>
                            <a href="javascript:void(0);" onclick="viewCalendar(<?php echo $teacher['user_id']; ?>, 'paid')" class="link-detail"><?php echo Label::getLabel('LBL_View_Full_availability'); ?></a>
                        </div>
                    </div>
                    <div class="box__secondary">
                        <div class="panel-box">
                            <div class="panel-box__head">
                                <ul>

                                    <?php if (!empty($teacher['us_video_link'])) { ?>
                                        <li class="is--active">
                                            <a class="panel-action" content="video" href="javascript:void(0)"><?php echo Label::getLabel('LBL_Introduction', $siteLangId); ?></a>
                                        </li>
                                    <?php } ?>
                                    <li <?php if (empty($teacher['us_video_link'])) { ?>class="is--active" <?php } ?>>
                                        <a class="panel-action" content="calender" href="javascript:void(0)"><?php echo Label::getLabel('LBL_Availability', $siteLangId); ?></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="panel-box__body">
                                <?php if (!empty($teacher['us_video_link'])) { ?>
                                    <div class="panel-content video" src="<?php echo $teacher['us_video_link']; ?>">
                                        <!--<iframe width="100%" height="100%"  frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>-->
                                        <iframe width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen="" src="<?php echo $teacher['us_video_link']; ?>"></iframe>
                                    </div>
                                <?php } ?>
                                <div class="panel-content calender" <?php if (!empty($teacher['us_video_link'])) { ?>style="display:none;" <?php } ?>>
                                    <div class="custom-calendar widget_calendr">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <?php
                                                    /* echo "Today is " . date("Y/m/d") . "<br>";
                                                        $ddate = date("Y/m/d");
                                                        $date = new DateTime($ddate);
                                                        $week = $date->format("W");
                                                        echo "Weeknummer: $week"."<br>";
                                                        $firstday = date('l - d/m/Y', strtotime("this week"));  
                                                        echo "First day of this week: ", $firstday; */
                                                    $today = time();
                                                    $wday = date('w', $today);
                                                    /*$datemon = date('m-d-Y', $today - ($wday - 1)*86400);
                                                        $datetue = date('m-d-Y', $today - ($wday - 2)*86400);
                                                        $datewed = date('m-d-Y', $today - ($wday - 3)*86400);
                                                        $datethu = date('m-d-Y', $today - ($wday - 4)*86400);
                                                        $datefri = date('m-d-Y', $today - ($wday - 5)*86400); 
                                                        $datesat = date('m-d-Y', $today - ($wday - 6)*86400); 
                                                        $datesun = date('m-d-Y', strtotime('last Sunday'));*/
                                                    $datemon = date('d', $today - ($wday - 1) * 86400);
                                                    $datetue = date('d', $today - ($wday - 2) * 86400);
                                                    $datewed = date('d', $today - ($wday - 3) * 86400);
                                                    $datethu = date('d', $today - ($wday - 4) * 86400);
                                                    $datefri = date('d', $today - ($wday - 5) * 86400);
                                                    $datesat = date('d', $today - ($wday - 6) * 86400);
                                                    $datesun = date('d', strtotime('last Sunday'));
                                                    //echo "<br><br>"; 
                                                    /* echo "sun - ".$datesun."<br>";
                                                        echo "mon - ".$datemon."<br>";
                                                        echo "tue - ".$datetue."<br>";
                                                        echo "wed - ".$datewed."<br>";
                                                        echo "thu - ".$datethu."<br>";
                                                        echo "fri - ".$datefri."<br>";
                                                        echo "sat - ".$datesat;*/
                                                    /*echo "<br><br>";
                                                        echo "last sunday".date('m/d/Y', strtotime('last Sunday'));*/
                                                    ?>
                                                    <th>&nbsp;</th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Sun', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datesun ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Mon', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datemon ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Tue', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datetue ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Wed', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datewed ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Thu', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datethu ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Fri', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datefri ?></p>
                                                    </th>
                                                    <th>
                                                        <?php echo Label::getLabel('LBL_Sat', $siteLangId); ?>
                                                        <p class="get_custom_date"><?php echo $datesat ?></p>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $timeslots = $teacher['testat_timeslots'] ?? CommonHelper::getEmptyDaySlots(); ?>
                                                <?php  //
                                                //   echo '<pre>';print_r($timeslots); 
                                                ?>
                                                <?php $show = true;
                                                foreach ($slots as $index => $slot) {

                                                ?>
                                                    <tr>
                                                        <td class="td_slot">
                                                            <div class="cal-cell">
                                                                <p class="days-schedule"><?php echo $Daysslots[$index]; ?></p>
                                                                <p class="days-time"><?php echo $slot; ?></p>
                                                            </div>
                                                        </td>
                                                        <?php
                                                        $d = date('m/d/Y', $today);
                                                        $tD = date("d");
                                                        $tMonth = date('m');
                                                        $dIndex = 1;
                                                        ?>
                                                        <?php foreach ($timeslots as $day => $hours) { ?>
                                                            <?php
                                                            if (!empty($hours[$index])) {
                                                                // echo '<pre>';print_r($hours);
                                                                // $d=mktime($hours[$index]);
                                                                // echo '<pre>';print_r($d);
                                                                // echo strtotime($date);
                                                                // $date = strtotime($hours[$index]);
                                                                // echo "hii" ;
                                                                // echo date('H', $date);

                                                                $hourString = Mydate::getHoursMinutes($hours[$index]);
                                                                $hour = str_replace(":", '.', $hourString);
                                                                // echo  str_replace(":", '.', $hourString);
                                                                $hour = (ceil(FatUtility::float($hour)));
                                                                $hour = ($hour == 0) ? 1 : $hour;
                                                                $hourString = str_replace('{hourstring}', $hourString, $hourStringLabel);
                                                            }
                                                            ?>
                                                            <td class="is-hover">
                                                                <?php

                                                                if (!empty($hours[$index])) {
                                                                    $dif = date('d', $today - ($wday - $dIndex) * 86400);
                                                                    $dfMonth = date('m', $today - ($wday - $dIndex) * 86400);
                                                                    // echo $dfMonth;
                                                                    $nDate = date('d-m-Y', $today - ($wday - $dIndex) * 86400);
                                                                    $curr = date('d-m-Y', $today);
                                                                    $datetime1 = date_create($nDate);

                                                                    $datetime2 = date_create($curr);

                                                                    // Calculates the difference between DateTime objects
                                                                    $interval = date_diff($datetime1, $datetime2);

                                                                    // Printing result in years & months format
                                                                    $date_difference = $interval->format('%R%d');
                                                                    $month_difference = $interval->format('%R%m');
                                                                    $year_difference = $interval->format('%R%y ');
                                                                    $diffMonth = $to - $nDate;
                                                                    $diff = $tD - $dif;
                                                                    // echo $date_difference;
                                                                    if ($date_difference >= -1) {
                                                                        if ($date_difference == -1) {
                                                                            $date_difference = 0;
                                                                        }
                                                                        $hour = ((int)($date_difference)) + 5;
                                                                    }


                                                                    if ($year_difference <= 0 && $month_difference >= 0 && $date_difference <= -2) {
                                                                        // echo "Yes";
                                                                ?>
                                                                        <div class="cal-cell <?php echo $colorClass[$hour]; ?>"></div>
                                                                    <?php
                                                                    } else { ?>
                                                                        <div class="cal-cell  <?php echo $colorClass[$hour]; ?>"></div>
                                                                    <?php }
                                                                    ?>
                                                                    <div class="tooltip tooltip--top bg-black"><?php echo $hourString; ?></div>
                                                                <?php } else { ?>
                                                                    <div class="cal-cell"></div>
                                                                <?php } ?>
                                                            </td>
                                                        <?php $dIndex++;
                                                        }  ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                        <a href="javascript:void(0);" onclick="viewCalendar(<?php echo $teacher['user_id']; ?>, 'paid')" class="link-detail"><?php echo Label::getLabel('LBL_View_Full_availability'); ?></a>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php
    echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmTeacherSearchPaging'));
    $pagingArr = ['page' => $page, 'pageCount' => $pageCount, 'recordCount' => $recordCount];
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
} else {
?>
    <div class="box -padding-30" style="margin-bottom: 30px;">
        <div class="message-display">
            <div class="message-display__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                    <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
                </svg>
            </div>
            <h5><?php echo Label::getLabel('LBL_No_Result_found!!', $siteLangId); ?></h5>
        </div>
    </div>
<?php
}
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    $(window).scroll(function() {
        //set scroll position in session storage
        sessionStorage.scrollPosTeachers = $(window).scrollTop();
    });
    $(document).ready(function() {
        if ($('.teacher__name').text() !== '') {
            $('html, body').animate({
                scrollTop: sessionStorage.scrollPosTeachers
            }, 1000);
        } else {
            sessionStorage.scrollPosTeachers = 0
        }
    });
    $(document).ready(function() {
        if ($(location).attr('hash') !== '') {
            setTimeout(() => {
                document
                    .querySelector($(location).attr('hash'))
                    .scrollIntoView({
                        behavior: "smooth"
                    });
            }, 100);
        }
        const element = document.getElementsByClassName("tutor-info__inner");
        for (let j = 0; j < element.length; j++) {
            let text = element[j].children.item(1).innerHTML;
            let nText = text.replaceAll(",", ", ");
            if (text.includes(";")) {
                nText = text.replaceAll(";", ", ");
            }
            element[j].children.item(1).innerHTML = nText;
        }
    });

    function viewCalendar_teacherlist(teacherId, languageId, action = '') {
        var dv = $('#availbility');
        if (action == 'free_trial') {
            if (isUserLogged() == 0) {
                $.loader.hide();
                logInFormPopUp();
                return false;
            }
        }
        fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar', [teacherId, languageId]), 'action=' + action, function(t) {
            if (action == 'free_trial') {
                $.facebox(t, 'facebox-large');
                $('body').addClass('calendar-facebox');
            } else {
                $(dv).html(t);
            }
        });
    }
</script>