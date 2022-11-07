<?php
session_start();
defined('SYSTEM_INIT') or die('Invalid Usage.');
$userTimezone = MyDate::getUserTimeZone();
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $userTimezone);
$curDateTimeUnix = strtotime($curDateTime);
?>
<style>
    .custom-box-timer{ display:none !important;}
    .custom-box-block-timer { display :block !important; }
    .btn--bordered{border:none !important}
    .blog-section { padding: 0px 0px !important; }

</style>
<div class="group-cover">
    <!-- <div class="sorting__head">
        <div class="sorting__title">
            <?php if (!empty($classes)) { ?>
                <h4><b><?php echo $pagingArr['recordCount']; ?> </b><?php echo Label::getLabel('LBL_GROUP_CLASSES_FOR_YOU.'); ?></h4>
            <?php } ?>
        </div>
    </div> -->
    <?php if (!empty($classes)) { ?>
        <!-- <div class="group__list">
            <div class="row"> -->
                <?php 
                 
                
                $classArray=array();
                 foreach ($groupList as $group) {
                    foreach($classes as $class){
                        if($group['grpcls_ages']==$class['grpcls_ages']){
                            $age=explode("-",$class['grpcls_ages']);
                            $class['sort']= (int)($age[0]);
                            $class['groupCount']=$group['groupCount'];
                            array_push($classArray,$class);
                        }
                    }
                }
                // array_multisort(array_column($classArray,'sort'),SORT_ASC,$classArray);
                usort($classArray, function ($a, $b) {
                    return ($a['sort'] < $b['sort']) ? -1 : 1;
                  });
                ?>
            <section class="blog-section">
        	    <div class="container">
        		    <div class="row">
        			    <div class="col-12">
                <?php
                $myDate = new myDate();
                $myDate->setMonthAndweekName($siteLangId);
                $i=0;
                function sortFunction( $a, $b ) {
                    return strtotime($a) - strtotime($b);
                }
                $teacherArray=array();
                $language=array();    
                foreach ($classArray as $class) {
                    if($cnt!=$class['grpcls_ages']){
                        $teacherArray=array(); 
                        $language=array();
                    }
                    $searchItem = array_search($class['user_full_name'], $teacherArray);
                    
                    if (in_array($class['user_full_name'], $teacherArray) && $searchItem>=0){
                        //   return ;  
                        // echo "Hi";
                    }
                    else {
                        array_push($teacherArray,$class['user_full_name']);
                        array_push($language,$class['teacher_language']);
                        if($cnt!=$class['grpcls_ages'] ){
                            // $teacherArray=array();    
                            $cnt=$class['grpcls_ages'];
                            $title=array_pop(explode(" " ,$class['grpcls_title']));
                            $age=str_replace("-"," to ",$class['grpcls_ages']);
                            echo '<div class="ubuntu_talks_ages">
                            <p><a href="#check_0">Ages 6-9</a><a href="#check_1">Ages 10-14</a><a href="#check_2">Ages 15-17</a></p>
                            </div>';
                            echo "<div class='ages-title'>Ubuntu Talks ".ucwords($title) ." - ".$age." years"."</div>";
                        }
                        $i++;
                    
                   

                    // New Codde
                    if($class['grpcls_start_datetime']){
                        $week_dates=array();
                        $week_days=explode(',',$class['grpcls_weeks']);
                       
                        foreach($week_days as $weeks){
                            // if($class['grpcls_start_datetime']<date('Y-m-d H:i:s')){
                                $next_date=date('Y-m-d',strtotime('next '.$weeks));
                            array_push($week_dates,$next_date); 
                        }
                    
                           usort($week_dates, "sortFunction");
                         
                        if(!empty($week_dates)){
                         $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s',$week_dates[0], true, $userTimezone);
                         }
                      
                    }
                    else{
                    $startDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $class['grpcls_start_datetime'], true, $userTimezone);
                    }
                    $endDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $class['grpcls_end_datetime'], true, $userTimezone);
                    $startDateTimeUnix = strtotime($startDateTime);
                    $endDateTimeUnix = strtotime($endDateTime);
                    $timeDiff = ($endDateTimeUnix - $startDateTimeUnix) / 60;
                    if ($class['is_in_class'] || ($class['grpcls_max_learner'] > 0 && $class['total_learners'] >= $class['grpcls_max_learner']) || ($class['grpcls_start_datetime'] < date('Y-m-d H:i:s', strtotime('+' . $min_booking_time . ' minutes'))) || (UserAuthentication::isUserLogged() && $class['grpcls_teacher_id'] == UserAuthentication::getLoggedUserId()) || ($class['grpcls_status'] != TeacherKidsClasses::STATUS_ACTIVE) ){
                        $divclass = 'custom-box-timer';
                    }
                    else{
                        $divclass = '';
                    }
                    ?>
                        <div class="blog-section-inner">
        					<div href="<?php echo CommonHelper::generateUrl('Kids', 'view', [CommonHelper::htmlEntitiesDecode($class['grpcls_slug'])]); ?>" class="blog-box">
        						<div class="blog-box-images">
                                        <?php
                                        
                                            $file_row = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_KIDS_PLAN_IMAGE, $class['	grpcls_id'], 0);
                                            if (!empty($file_row)) {
                                                echo CommonHelper::displayNotApplicable('');
                                            } else {
                                                ?>
                                                <a href="<?php echo CommonHelper::generateUrl('Kids', 'view', [CommonHelper::htmlEntitiesDecode($class['grpcls_slug'])]); ?>">
                                                    <img src="<?php echo CommonHelper::generateFullUrl('Image', 'KidsPlanImage', array($class['grpcls_id'], 'THUMB')) . '?' . time(); ?>" />
                                                </a>
                                            <?php } ?>
                                        
        						</div>
        						<div class="blog-box-detail">
                                    <div class="blog-box-left">
                                        <div class="top-tag-like">
                                            <h6>Ages <?php echo $class['grpcls_ages']; ?></h6>
                                            <!-- <span>
                                            <a href="javascript:void(0)" onclick="toggleTeacherFavorite(<?php echo $class['user_id']; ?>, this)" class="btn btn--bordered color-black <?php echo ($class['uft_id']) ? 'is--active' : ''; ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84.02L256 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 .0003 232.4 .0003 190.9L0 190.9z"/>
                                                </svg>
                                                </a>
                                            </span> -->
                                        </div>
                                        <h3>
                                            <a href="<?php echo CommonHelper::generateUrl('Kids', 'view', [CommonHelper::htmlEntitiesDecode($class['grpcls_slug'])]); ?>">
                                            <?php echo $class['grpcls_title']; ?>
                                                </a>
                                            
                                        </h3>
                                        <p><?php echo $class['grpcls_description']; ?></p>
                                        <div class="author-rating">
                                            <div class="author-image">
                                            <img src="<?php echo FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'userFull', array($class['grpcls_teacher_id'], 'SMALL')), CONF_IMG_CACHE_TIME, '.jpg'); ?>" alt="">
                                            </div>
                                            <div class="author-details">
                                                <p><?php echo $class['user_full_name'] ?></p>
                                                <!-- <div>
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star fa-w-18 css-sc01zf" style="cursor: default;"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star fa-w-18 css-sc01zf" style="cursor: default;"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star fa-w-18 css-sc01zf" style="cursor: default;"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star fa-w-18 css-sc01zf" style="cursor: default;"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-star fa-w-18 css-sc01zf" style="cursor: default;"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                                                    <span>(309)</span>
                                                </div> -->
                                            </div>
                                        </div>
                                        <div class="blog-box-time">
                                            <div class="blog-box-time-left">
                                                <p>
                                                    <!-- <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 css-0"><path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg>
                                                    <?php 
                                                        $over_week =explode(',',$class['grpcls_weeks']);
                                                        $countwk = count($over_week);
                                                        $total_lesson = $class['grpcls_total_lesson'] / $countwk;
                                                        echo "$countwk times per week over ".$total_lesson. " weeks";
                                                    ?> -->
                                                    Felixible Group or One on One lessons
                                                </p>
                                                <!-- <p><?php echo $myDate->convertToLocal(date('d, M Y, h:i A', $startDateTimeUnix)) . ' - ' . $myDate->convertToLocal(date('h:i A', $endDateTimeUnix)); ?></p> -->
                                            </div>
                                            <div class="blog-box-time-right">
                                                <!-- <h6>
                                                <?php echo CommonHelper::displayMoneyFormat($class['grpcls_entry_fee']); ?>
                                                    <p>per course</p>
                                                </h6> -->
                                                <a href="<?php echo CommonHelper::generateUrl('Kids', 'view', [CommonHelper::htmlEntitiesDecode($class['grpcls_slug'])]); ?>" class="more-info-btn">More Info</a>
                                            </div>
                                        </div>
                                    </div>
        							<img class="blog-box-flex" src="<?php echo FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'showLanguageFlagImage', array($class['grpcls_tlanguage_id'], 'SMALL')), CONF_IMG_CACHE_TIME, '.jpg'); ?>" alt="">
        						</div>
                            </div>
        				</div>
                <?php }}?>
            <!-- </div>
        </div> -->
            </div>
        </div>
    </div>
</section>
        <?php
        echo FatUtility::createHiddenFormFromData($postedData, array(
            'name' => 'frmSearchPaging'
        ));
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
    } else {
        ?>
        <div class="message-display">
            <div class="message-display__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.571 373.649">
                <defs>
                <style>
                    .a {
                        fill: #ccd0d9;
                    }
                    .b,
                    .e,
                    .f {
                        fill: none;
                    }
                    .b,
                    .d {
                        stroke: #b1b5c4;
                        stroke-width: 4px;
                    }
                    .b,
                    .d,
                    .e {
                        stroke-miterlimit: 10;
                    }
                    .c {
                        fill: #b1b5c4;
                    }
                    .d {
                        fill: #fff;
                    }
                    .e {
                        stroke: #fff;
                    }
                </style>
                </defs>
                <g transform="translate(-700 -2490)">
                <path class="a" d="M343.893,118.759v22.694l-232.8-1.631s-.7,1.347-1.691-13.153v-42s-2.234-11.96,11.822-11.96h72.411l17.676,28.748,119.086,1.386s13.5,2.26,13.5,15.914" transform="translate(688.518 2438.915)" />
                <path class="b" d="M326.87,101.459H211.341L194.991,74.33a3.434,3.434,0,0,0-2.878-1.619H124.469c-5.39,0-9.523,1.619-12.038,4.761a13.312,13.312,0,0,0-2.7,10.152V257.415c0,6.2,1.709,10.78,5.3,13.925a15.764,15.764,0,0,0,10.421,3.593,10.868,10.868,0,0,0,1.889-.09h211.92a3.375,3.375,0,0,0,3.328-3.328V119.875C342.681,107.477,332.349,102.446,326.87,101.459Z" transform="translate(688.486 2438.915)" />
                <path class="c" d="M394.855,358.35a1.151,1.151,0,0,0-1.627,1.627l3.446,3.447a1.123,1.123,0,0,0,.814.335.985.985,0,0,0,.813-.335,1.187,1.187,0,0,0,0-1.628Z" transform="translate(660.013 2410.24)" />
                <path class="c" d="M405.619,354.513l-3.78-3.78a1.262,1.262,0,1,0-1.785,1.785l3.78,3.78a1.227,1.227,0,0,0,.892.368,1.154,1.154,0,0,0,.892-.368,1.3,1.3,0,0,0,0-1.785" transform="translate(659.33 2411.009)" />
                <path class="d" d="M305.525,163.257A100.833,100.833,0,1,0,406.358,264.09,100.787,100.787,0,0,0,305.525,163.257Z" transform="translate(678.928 2429.814)" />
                <path class="d" d="M307.49,182.811a83.244,83.244,0,1,0,83.244,83.244A83.207,83.207,0,0,0,307.49,182.811Z" transform="translate(676.963 2427.849)" />
                <path class="c" d="M343.526,146.837a1.349,1.349,0,0,0-1.329-1.016H110.231a1.34,1.34,0,0,0-1.333,1.346c0,.033,0,.067,0,.1a1.387,1.387,0,0,0,1.446,1.25H342.237a1.367,1.367,0,0,0,1.094-.547,1.314,1.314,0,0,0,.2-1.133" transform="translate(688.556 2431.567)" />
                <path class="e" d="M300.089,309.212s2.159-13.043,17.36-13.043c0,0,17.36,0,17.36,13.043" transform="translate(669.34 2416.456)" />
                <g transform="translate(942.292 2665.979)">
                <path class="c" d="M279.5,251.373l4.009-4.009a1.781,1.781,0,0,0-2.492-2.544l-.026.026-4.008,4.008-4.009-4.008a1.781,1.781,0,0,0-2.544,2.492l.026.025,4.008,4.009-4.008,4.008a1.833,1.833,0,0,0,0,2.518,1.672,1.672,0,0,0,1.233.515,1.592,1.592,0,0,0,1.233-.515l4.009-4.008,4.008,4.008a1.672,1.672,0,0,0,1.233.515,1.592,1.592,0,0,0,1.233-.515,1.833,1.833,0,0,0,0-2.518Z" transform="translate(-269.92 -244.312)" />
                <path class="c" d="M357.146,251.373l4.009-4.009a1.781,1.781,0,0,0-2.492-2.544l-.026.026-4.008,4.008-4.009-4.008a1.781,1.781,0,0,0-2.544,2.492l.026.025,4.008,4.009-4.008,4.008a1.833,1.833,0,0,0,0,2.518,1.674,1.674,0,0,0,1.233.515,1.592,1.592,0,0,0,1.233-.515l4.009-4.008,4.008,4.008a1.674,1.674,0,0,0,1.233.515,1.592,1.592,0,0,0,1.233-.515,1.833,1.833,0,0,0,0-2.518Z" transform="translate(-277.724 -244.312)" />
                <path class="c" d="M330.513,304.263c-1.517-7.948-8.372-9.1-12.316-9.1-9.465,0-12.074,6.067-12.8,8.677a1.994,1.994,0,0,0,.425,1.82,1.973,1.973,0,0,0,1.638.729h.121a2.143,2.143,0,0,0,1.82-1.456c.607-2.063,2.488-5.521,8.8-5.521.789,0,7.584.182,8.312,5.521a2,2,0,0,0,2.063,1.759,2.263,2.263,0,0,0,1.578-.729,2.1,2.1,0,0,0,.363-1.7" transform="translate(-273.479 -249.422)" />
                </g>
                <path class="d" d="M464.146,422.193h0a17.644,17.644,0,0,1-24.953.053l-.053-.053-37.059-37.059a17.682,17.682,0,1,1,24.839-25.173l.167.167,37.059,37.059a17.644,17.644,0,0,1,.053,24.953l-.053.053" transform="translate(659.618 2410.556)" />
                <g transform="translate(723.249 2495.397)">
                <path class="a" d="M454.055,204.269a5.667,5.667,0,1,0,5.667,5.667,5.667,5.667,0,0,0-5.667-5.667m2.768,5.667a2.768,2.768,0,1,1-2.768-2.768h0a2.786,2.786,0,0,1,2.768,2.768" transform="translate(-68.814 -69.705)" />
                <path class="a" d="M239.2,54.669a4.408,4.408,0,1,0,4.408,4.408h0a4.419,4.419,0,0,0-4.408-4.408m2.152,4.408a2.152,2.152,0,1,1-2.152-2.152,2.153,2.153,0,0,1,2.152,2.152" transform="translate(-47.346 -54.669)" />
                <path class="a" d="M74.523,124.658H69.851v-4.717A1.924,1.924,0,1,0,66,119.859c0,.027,0,.055,0,.083v4.718H61.236a1.924,1.924,0,0,0-.084,3.847h4.8v4.672a1.924,1.924,0,0,0,3.847.084v-4.71h4.626a1.906,1.906,0,0,0,1.924-1.888v-.036a1.817,1.817,0,0,0-1.658-1.964c-.058,0-.116-.007-.175-.006" transform="translate(-29.705 -61.032)" />
                <path class="a" d="M417.46,101.665H412.82V97.052a1.116,1.116,0,0,0-2.228,0v4.614h-4.613a1.116,1.116,0,0,0,0,2.228h4.613v4.637a1.122,1.122,0,0,0,1.115,1.116,1.1,1.1,0,0,0,1.115-1.093v-4.636h4.641a1.127,1.127,0,1,0,0-2.253" transform="translate(-64.445 -58.823)" />
                <path class="a" d="M170.939,359.05H164.88v-6.027a1.454,1.454,0,0,0-2.908,0v6.027h-6.027a1.454,1.454,0,0,0,0,2.908h6.027v6.059a1.463,1.463,0,0,0,1.454,1.454,1.44,1.44,0,0,0,1.454-1.424v-6.057h6.059a1.472,1.472,0,0,0,0-2.943" transform="translate(-39.279 -84.513)" />
                <path class="a" d="M84.971,313.962,67.142,281.482a1.6,1.6,0,0,0-1.037-.908l-11.67-3.242a1.529,1.529,0,0,0-1.3.065L29.151,290.235a5.214,5.214,0,0,0-2.528,2.982,5.394,5.394,0,0,0,.324,3.89l19.514,37.407a4.833,4.833,0,0,0,3.047,2.528,5.88,5.88,0,0,0,1.491.2,8.278,8.278,0,0,0,2.2-.389l.259-.065L82.9,320.965a4.865,4.865,0,0,0,2.528-3.112,5.126,5.126,0,0,0-.454-3.89M58.908,285.178l-1.88-3.112,4.8,1.362ZM58.2,289.2h.13a1.2,1.2,0,0,0,.778-.324l6.159-3.7,16.6,30.276a1.657,1.657,0,0,1,.13,1.362,2,2,0,0,1-.842,1.037L51.712,333.671a1.511,1.511,0,0,1-1.3.13,2.162,2.162,0,0,1-1.1-.908L29.928,295.486a1.962,1.962,0,0,1,.713-2.464l21.978-11.735,4.279,7.131a1.917,1.917,0,0,0,.972.713c.065.065.26.065.325.065" transform="translate(-26.401 -77.037)" />
                </g>
                <path class="c" d="M39.272,304.441a1.153,1.153,0,0,1-1.037-.649,1.11,1.11,0,0,1,.428-1.51.939.939,0,0,1,.09-.045l15.755-8.04a1.167,1.167,0,0,1,1.556.519,1.111,1.111,0,0,1-.43,1.511c-.029.016-.058.031-.089.045l-15.689,8.039a1.882,1.882,0,0,1-.584.13" transform="translate(695.672 2416.666)" />
                <path class="c" d="M47.993,320.528a1.155,1.155,0,0,1-1.037-.648,1.112,1.112,0,0,1,.43-1.512c.029-.016.058-.031.089-.044l19.255-9.855a1.16,1.16,0,0,1,1.037,2.075L48.512,320.4a1.222,1.222,0,0,1-.519.13" transform="translate(694.796 2415.231)" />
                <path class="c" d="M52.1,328.261a1.157,1.157,0,0,1-1.038-.649,1.112,1.112,0,0,1,.43-1.512c.029-.016.058-.031.089-.044l20.163-10.308a1.162,1.162,0,0,1,1.555.518,1.11,1.11,0,0,1-.428,1.511c-.03.016-.059.031-.09.045L52.62,328.131a1.544,1.544,0,0,1-.518.13" transform="translate(694.383 2414.5)" />
                <path class="c" d="M43.092,312.384a1.153,1.153,0,0,1-1.037-.648,1.111,1.111,0,0,1,.428-1.511c.03-.016.059-.031.09-.045l19.255-9.854a1.16,1.16,0,0,1,1.037,2.074l-19.255,9.855a.7.7,0,0,1-.519.129" transform="translate(695.288 2416.05)" />
                <rect class="f" width="460.571" height="373.649" transform="translate(700 2490)" />
                </g>
                </svg>
            </div>
            <h5> <?php echo (empty($msgHeading)) ? Label::getLabel('LBL_No_Result_Found!!') : $msgHeading; ?></h5>
        <?php } ?>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        $(".ages-title").each(function(key,elem){
            $(elem).attr('id', 'check_' + key)
        })
            var countText =  $(".countdowntimer-js:nth-child(1)").html()
            if(countText!="" && countText!=="00:00:00:00"){

        $('.countdowntimer-js').each(function (i) {
            
            $(this).countdowntimer({
                startDate: $(this).attr('data-startTime'),
                dateAndTime: $(this).attr('data-endTime'),
                size: "sm",
                timeUp : myfin,
            });
            function myfin() {
            window.location = window.location;
            tim_id = $(this).attr('id');
            myArrfay=tim_id.split("-");
            $(this).attr('data-endTime',null);
            var getLocalVal=localStorage.getItem('grpcls_id')
            if(getLocalVal!=myArrfay[1]){
            console.log(getLocalVal);
            var formData = new FormData();
            formData.append('myArrfay',myArrfay[1])
            localStorage.setItem('grpcls_id',myArrfay[1]);
            $.ajax({
                url: fcom.makeUrl('KidsClasses', 'setup'),
                type: 'POST',
                dataType: 'json',
                data:formData,
                mimeType: "multipart/form-data",
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    localStorage.setItem('grpcls_id',myArrfay[1]);
                    var data = JSON.parse(data);
                    $.loader.hide();
                    if (data.status == 0) {
                        $.mbsmessage(data.msg, true, 'alert alert--danger');
                        return false;
                    } else {
                        searchGroupClasses(document.frmSrch);
                        $.mbsmessage(data.msg, true, 'alert alert--success');
                        if (data.lang_id > 0) {
                            editGroupClassLangForm(data.grpcls_id, data.lang_id);
                        }
                    }
                    setTimeout(function () {
                        $.systemMessage.close();
                    }, 2000);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $.loader.hide();
                    $.systemMessage(jqXHR.msg, true);
                }
        });
    }
        }
        });
    }
    });
</script>