<?php
    session_start();
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
    $about=$class['user_profile_info'];
    if(!isset($_SESSION['booked'])){
        $_SESSION['booked']=0;
    }
?>
<style>
    a.btn.btn--primary.btn--large.color-white.kids-bookbtn {border-radius: 8px;margin-top: 40px;}
</style>
<title><?php echo Label::getLabel('LBL_Kids_Class') . $class['grpcls_title'] . " " . Label::getLabel('LBL_on') . " " . $websiteName; ?></title>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
<section class="blog-detail-section">
    <div class="container container--narrow">
    <div class="breadcrumb-list kids_breadcrumb">
            <ul>
                <li><a href="<?php echo CommonHelper::generateUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                <li><a href="<?php echo CommonHelper::generateUrl('Kids'); ?>"><?php echo Label::getLabel('LBL_Kids_Classes'); ?></a></li>
                <li><?php echo $class['grpcls_title']; ?></li>
            </ul>
        </div>
        <div class="group-primary__head kids_page_agestitle">
            <h3><?php echo $class['page_title']; ?></h3>         
        </div>
        <div class="group-primary__head kids_page_maintitle">
            <h3><?php echo $class['grpcls_kids_title']; ?></h3>         
        </div>
        <div class="blog-detail-list">
            <div class="row">
                <div class="col-md-7">
                    <div class="blog-desc">
                        <p><?php echo $class['grpcls_description']; ?></p>
                    </div>
                    <div class="blog-profile p-0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="profile-wrp">
                                    <span><?php echo $class['user_first_name'] . ' ' . $class['user_last_name']; ?></span>
                                    <p>Lives in <?php echo $class['country_name']; ?></p>
                                    <div class="profile-wrp-img">
                                        <?php if (true == User::isProfilePicUploaded($class['user_id'])) { ?>
                                        <img src="<?php echo CommonHelper::generateUrl('Image', 'User', array($class['user_id'], 'ORIGINAL')) ?>" alt="">
                                        <?php } ?>
                                    </div>
                                    <button type="button" class="btn btn-primary video-btn profile-video" data-toggle="modal" data-src="<?php echo $class['us_video_link'] ?>" data-target="#myModal"><?php echo Label::getLabel('Lbl_Profile_Video'); ?></button>

                                    <a href="javascript:void(0);" onclick="generateThread(<?php echo $class['user_id']; ?>);" class="btn btn--bordered color-primary btn--block">
                                    <svg class="icon icon--envelope">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#envelope'; ?>"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_Contact', $siteLangId); ?>
                                </a>
                                </div>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="profile-desc">
                                    <h3><?php echo Label::getLabel('Lbl_About_Me'); ?></h3>
                                    <p><?php echo $class['user_profile_info']; ?></p>
                                </div>
                            </div>
                        </div>
            </div>
                </div>
                <div class="col-md-5">
                    <div class="blog-img">
                        <?php if($class['grpcls_kids_youtube_link'] == " "){ ?>
                            <button type="button" class="btn btn-primary video-btn" data-toggle="modal" data-src="<?php echo $class['grpcls_kids_youtube_link'] ?>" data-target="#myModal">
                                <img src="/image/editor-image/play-icon.svg">
                            </button>
                            <?php   
                                $file_row = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_KIDS_PLAN_IMAGE, $class['   grpcls_id'], 0);
                                if (!empty($file_row)) {
                                    echo CommonHelper::displayNotApplicable('');
                                }
                                else {
                            ?>
                            <a href="<?php echo CommonHelper::generateUrl('Kids', 'view', [CommonHelper::htmlEntitiesDecode($class['grpcls_slug'])]); ?>">
                                <img src="<?php echo CommonHelper::generateFullUrl('Image', 'KidsPlanImage', array($class['grpcls_id'], 'THUMB')) . '?' . time(); ?>" />
                            </a>
                            <?php }}else{ ?>
                                <iframe width="503" height="283" src="<?php echo $class['grpcls_kids_youtube_link'] ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen poster="https://media.geeksforgeeks.org/wp-content/cdn-uploads/20190710102234/download3.png"></iframe>
                            <?php } ?>
                    </div>
                </div>
            </div>
            <div class="blog-profile p-1">
                <div class="row">
                    <div class="col-md-3">
                        <div class="profile-wrp">
                            <span><?php echo $class['user_first_name'] . ' ' . $class['user_last_name']; ?></span>
                            <p>Lives in <?php echo $class['country_name']; ?></p>
                            <div class="profile-wrp-img">
                                <?php if (true == User::isProfilePicUploaded($class['user_id'])) { ?>
                                <img src="<?php echo CommonHelper::generateUrl('Image', 'User', array($class['user_id'], 'ORIGINAL')) ?>" alt="">
                                <?php } ?>
                            </div>
                            <button type="button" class="btn btn-primary video-btn profile-video" data-toggle="modal" data-src="<?php echo $class['us_video_link'] ?>" data-target="#myModal"><?php echo Label::getLabel('Lbl_Profile_Video'); ?></button>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="profile-desc">
                            <h3><?php echo Label::getLabel('Lbl_About_Me'); ?></h3>
                            <p><?php echo $class['user_profile_info']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="multi-course-section">
    <svg class="css-1y4k0rq"><pattern id="curve-ca58cd5a-8c97-44e8-b0db-1009edeb28fe" x="0" y="0" width="800" height="100%" patternUnits="userSpaceOnUse"><path fill="currentColor" d="M 0 40 L 0 20 Q 200 0, 400 20 Q 600 40, 800 20 L 800 40"></path><animate attributeName="x" values="0;800" dur="10s" repeatCount="indefinite"></animate></pattern><rect x="0" y="0" width="100%" height="100%" fill="url(#curve-ca58cd5a-8c97-44e8-b0db-1009edeb28fe)"></rect></svg>
    <div class="container">
        <div class="multi-course-wrp">
            <div class="row group-lessons">
                <div class="col-md-8">
                    <div class="multi-course-time-wrp">
                        <button class="accodian-btn-wrp">
                            <span><?php echo Label::getLabel('Lbl_Group_Lesson_Course'); ?></span>
                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-question-circle fa-w-16 css-1xeqnh3"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z"></path></svg>
                        </button>
                        <div class="multi-time-zone">
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 fa-fw css-s3qwly"><path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg>
                                        <strong><span><?php echo $class['grpcls_total_lesson'];?>&nbsp;Lessons</span></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-clock fa-w-16 fa-fw css-s3qwly"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z"></path></svg>
                                        <strong><?php echo $class['grpcls_duration']; ?><span><b>minutes</b> per class</span></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                    <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 64 64"><circle cx="9" cy="53" r="3"/><path d="M17,61a4,4,0,0,0-4-4H5a4,4,0,0,0-4,4v2H17Z"/><circle cx="32" cy="53" r="3"/><path d="M36,57H28a4,4,0,0,0-4,4v2H40V61A4,4,0,0,0,36,57Z"/><circle cx="55" cy="53" r="3"/><path d="M59 57H51a4 4 0 0 0-4 4v2H63V61A4 4 0 0 0 59 57zM60 1H4V23.08A8 8 0 0 1 8 22h2.11a7 7 0 1 1 9.78 0H22a8 8 0 0 1 8 8v1h1v3.5l-3.6 2.7a1 1 0 1 0 1.2 1.6L31 37v2a1 1 0 0 0 2 0V37l2.4 1.8a1 1 0 0 0 .6.2 1 1 0 0 0 .8-.4 1 1 0 0 0-.2-1.4L33 34.5V31H60zM25 6H39a1 1 0 0 1 0 2H25a1 1 0 0 1 0-2zm0 5H46a1 1 0 0 1 0 2H25a1 1 0 0 1 0-2zm30 7H25a1 1 0 0 1 0-2H55a1 1 0 0 1 0 2z"/><circle cx="15" cy="17" r="5"/><path d="M28,30a6,6,0,0,0-6-6H8a6,6,0,0,0-6,6v2H28Z"/></svg>
                                        <?php 
                                            // $over_week =explode(',',$class['grpcls_weeks']);
                                            // $countwk = count($over_week);
                                            // $total_lesson = $class['grpcls_total_lesson'] / $countwk;
                                            // echo "<strong>$countwk times per week over ".$total_lesson. " weeks</strong>";
                                        ?>
                                        <strong> <h4><?php echo Label::getLabel("LBL_Group_Lessons_Flexible_Schedule") ?></h4></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="birthday-cake" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-birthday-cake fa-w-14 fa-fw css-s3qwly"><path fill="currentColor" d="M192 64c0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40-32-14.25-32-32zm160 32c17.75 0 32-13.5 32-40S364 0 352 0c0 41-32 33-32 64 0 17.75 14.25 32 32 32zm96 176v240H0V272c0-26.5 21.5-48 48-48h24V112h48v112h80V112h48v112h80V112h48v112h24c26.5 0 48 21.5 48 48zm-400 6v56.831c8.352 7 15.27 13.169 26.75 13.169 25.378 0 30.13-32 74.75-32 43.974 0 49.754 32 74.5 32 25.588 0 30.061-32 74.75-32 44.473 0 49.329 32 74.75 32 11.258 0 18.135-6.18 26.5-13.187v-56.805a6 6 0 0 0-6-6L54 272a6 6 0 0 0-6 6zm352 186v-80.87c-7.001 2.914-15.54 4.87-26.5 4.87-44.544 0-49.389-32-74.75-32-25.144 0-30.329 32-74.75 32-43.974 0-49.755-32-74.5-32-25.587 0-30.062 32-74.75 32-11.084 0-19.698-1.974-26.75-4.911V464h352zM96 96c17.75 0 32-13.5 32-40S108 0 96 0c0 41-32 33-32 64 0 17.75 14.25 32 32 32z"></path></svg>
                                        <strong><?php echo $class['grpcls_ages']; ?><span> <h4><?php echo Label::getLabel("LBL_year_olds") ?></h4></span></strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-users fa-w-20 fa-fw css-s3qwly"><path fill="currentColor" d="M544 224c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80zm0-112c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32zM96 224c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80zm0-112c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32zm396.4 210.9c-27.5-40.8-80.7-56-127.8-41.7-14.2 4.3-29.1 6.7-44.7 6.7s-30.5-2.4-44.7-6.7c-47.1-14.3-100.3.8-127.8 41.7-12.4 18.4-19.6 40.5-19.6 64.3V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-44.8c.2-23.8-7-45.9-19.4-64.3zM464 432H176v-44.8c0-36.4 29.2-66.2 65.4-67.2 25.5 10.6 51.9 16 78.6 16 26.7 0 53.1-5.4 78.6-16 36.2 1 65.4 30.7 65.4 67.2V432zm92-176h-24c-17.3 0-33.4 5.3-46.8 14.3 13.4 10.1 25.2 22.2 34.4 36.2 3.9-1.4 8-2.5 12.3-2.5h24c19.8 0 36 16.2 36 36 0 13.2 10.8 24 24 24s24-10.8 24-24c.1-46.3-37.6-84-83.9-84zm-236 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm0-176c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zM154.8 270.3c-13.4-9-29.5-14.3-46.8-14.3H84c-46.3 0-84 37.7-84 84 0 13.2 10.8 24 24 24s24-10.8 24-24c0-19.8 16.2-36 36-36h24c4.4 0 8.5 1.1 12.3 2.5 9.3-14 21.1-26.1 34.5-36.2z"></path></svg>
                                        <strong><span>Up to&nbsp;</span><?php echo $class['grpcls_max_learner']; ?><span> <h4><?php echo Label::getLabel("LBL_learners_per_class") ?></h4></span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 justify-content-center align-items-center">
                    <div class="multi-course-price-wrp">
                        <?php
                            $over_week =explode(',',$class['grpcls_weeks']);
                            $countwk = count($over_week);
                            $price = $class['grpcls_entry_fee'];
                            $TotalLesson = $class['grpcls_total_lesson'];
                            $totalAmt = $price;
                            $classPrice=$price/$TotalLesson;
                        ?>
                        <span><?php echo "$".$totalAmt; ?></span>
                        <p>Per course per learner - <?php echo "$".$classPrice; ?> per lesson</p>
                    </div>
                    <div class="book-now-button">
                        <!-- <a href="#" class="enroll-btn">Book Now</a> -->
                        <?php if ($class['is_in_class']) :?>
                                <a href="javascript:void(0);"   class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['total_learners'] >= $class['grpcls_max_learner']) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_FULL') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['grpcls_teacher_id'] == $loggedUserId) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Can_not_join_own_classes') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['grpcls_status'] != TeacherKidsClasses::STATUS_ACTIVE) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Class_Not_active') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php else : ?>
                            <a href="javascript:void(0);" onclick="cart.proceedToStep({oneOnOne:0,fromKids:1,teacherId:<?php echo $class['grpcls_teacher_id']; ?>, grpclsId:<?php echo $class['grpcls_id'] ?>, languageId: <?php echo $class['grpcls_tlanguage_id'] ?>}, 'kidsBookingForm');" class="btn btn--primary btn--large color-white kids-bookbtn NR"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row oneonone-lessons">                
                <div class="col-md-8">
                    <div class="multi-course-time-wrp">
                        <button class="accodian-btn-wrp">
                            <span><?php echo Label::getLabel('Lbl_One_On_One_Lesson'); ?></span>
                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="question-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-question-circle fa-w-16 css-1xeqnh3"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm107.244-255.2c0 67.052-72.421 68.084-72.421 92.863V300c0 6.627-5.373 12-12 12h-45.647c-6.627 0-12-5.373-12-12v-8.659c0-35.745 27.1-50.034 47.579-61.516 17.561-9.845 28.324-16.541 28.324-29.579 0-17.246-21.999-28.693-39.784-28.693-23.189 0-33.894 10.977-48.942 29.969-4.057 5.12-11.46 6.071-16.666 2.124l-27.824-21.098c-5.107-3.872-6.251-11.066-2.644-16.363C184.846 131.491 214.94 112 261.794 112c49.071 0 101.45 38.304 101.45 88.8zM298 368c0 23.159-18.841 42-42 42s-42-18.841-42-42 18.841-42 42-42 42 18.841 42 42z"></path></svg>
                        </button>
                        <div class="multi-time-zone">
                            <div class="row">
                            <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                    <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 fa-fw css-s3qwly"><path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg>
                                    <strong><span><?php echo $class['grpcls_total_lesson'];?>&nbsp;Lessons</span></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-clock fa-w-16 fa-fw css-s3qwly"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z"></path></svg>
                                        <strong><?php echo $class['grpcls_duration']; ?><span><b>minutes</b> per class</span></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="calendar-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-calendar-alt fa-w-14 fa-fw css-s3qwly"><path fill="currentColor" d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path></svg>
                                        <?php 
                                            // $over_week =explode(',',$class['grpcls_weeks']);
                                            // $countwk = count($over_week);
                                            // $total_lesson = $class['grpcls_total_lesson'] / $countwk;
                                            // echo "<strong>$countwk times per week over ".$total_lesson. " weeks</strong>";
                                        ?>
                                        <strong> <h4><?php echo Label::getLabel("LBL_One_on_One_Flexible_Schedule") ?></h4></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="birthday-cake" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-birthday-cake fa-w-14 fa-fw css-s3qwly"><path fill="currentColor" d="M192 64c0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40-32-14.25-32-32zm160 32c17.75 0 32-13.5 32-40S364 0 352 0c0 41-32 33-32 64 0 17.75 14.25 32 32 32zm96 176v240H0V272c0-26.5 21.5-48 48-48h24V112h48v112h80V112h48v112h80V112h48v112h24c26.5 0 48 21.5 48 48zm-400 6v56.831c8.352 7 15.27 13.169 26.75 13.169 25.378 0 30.13-32 74.75-32 43.974 0 49.754 32 74.5 32 25.588 0 30.061-32 74.75-32 44.473 0 49.329 32 74.75 32 11.258 0 18.135-6.18 26.5-13.187v-56.805a6 6 0 0 0-6-6L54 272a6 6 0 0 0-6 6zm352 186v-80.87c-7.001 2.914-15.54 4.87-26.5 4.87-44.544 0-49.389-32-74.75-32-25.144 0-30.329 32-74.75 32-43.974 0-49.755-32-74.5-32-25.587 0-30.062 32-74.75 32-11.084 0-19.698-1.974-26.75-4.911V464h352zM96 96c17.75 0 32-13.5 32-40S108 0 96 0c0 41-32 33-32 64 0 17.75 14.25 32 32 32z"></path></svg>
                                        <strong><?php echo $class['grpcls_ages']; ?><span><?php echo Label::getLabel("LBL_year_olds") ?></span></strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="multi-time-zone-list">
                                        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="svg-inline--fa fa-users fa-w-20 fa-fw css-s3qwly"><path fill="currentColor" d="M544 224c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80zm0-112c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32zM96 224c44.2 0 80-35.8 80-80s-35.8-80-80-80-80 35.8-80 80 35.8 80 80 80zm0-112c17.6 0 32 14.4 32 32s-14.4 32-32 32-32-14.4-32-32 14.4-32 32-32zm396.4 210.9c-27.5-40.8-80.7-56-127.8-41.7-14.2 4.3-29.1 6.7-44.7 6.7s-30.5-2.4-44.7-6.7c-47.1-14.3-100.3.8-127.8 41.7-12.4 18.4-19.6 40.5-19.6 64.3V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-44.8c.2-23.8-7-45.9-19.4-64.3zM464 432H176v-44.8c0-36.4 29.2-66.2 65.4-67.2 25.5 10.6 51.9 16 78.6 16 26.7 0 53.1-5.4 78.6-16 36.2 1 65.4 30.7 65.4 67.2V432zm92-176h-24c-17.3 0-33.4 5.3-46.8 14.3 13.4 10.1 25.2 22.2 34.4 36.2 3.9-1.4 8-2.5 12.3-2.5h24c19.8 0 36 16.2 36 36 0 13.2 10.8 24 24 24s24-10.8 24-24c.1-46.3-37.6-84-83.9-84zm-236 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm0-176c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zM154.8 270.3c-13.4-9-29.5-14.3-46.8-14.3H84c-46.3 0-84 37.7-84 84 0 13.2 10.8 24 24 24s24-10.8 24-24c0-19.8 16.2-36 36-36h24c4.4 0 8.5 1.1 12.3 2.5 9.3-14 21.1-26.1 34.5-36.2z"></path></svg>
                                        <strong><span>Up to&nbsp;</span><?php echo $class['grpcls_max_one_on_one_learner']; ?><span><?php echo Label::getLabel("LBL_learner_per_class") ?></span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 justify-content-center align-items-center">
                    <div class="multi-course-price-wrp">
                        <?php
                            $over_week =explode(',',$class['grpcls_weeks']);
                            $countwk = count($over_week);
                            $price = $class['grpcls_one_on_one_entry_fee'];
                            $TotalLesson =$class['grpcls_total_lesson'];
                            $totalAmt = $price;
                            $classPrice=$price/$TotalLesson;
                        ?>
                        <span><?php echo "$".$totalAmt; ?></span>
                        <p>Per course per learner - <?php echo "$".$classPrice; ?> per lesson</p>
                    </div>
                    <div class="book-now-button">
                        <!-- <a href="#" class="enroll-btn">Book Now</a> -->
                        <?php if ($class['is_in_class']) :?>
                                <a href="javascript:void(0);"   class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['total_learners'] >= $class['grpcls_max_learner']) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_FULL') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['grpcls_teacher_id'] == $loggedUserId) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Can_not_join_own_classes') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php elseif ($class['grpcls_status'] != TeacherKidsClasses::STATUS_ACTIVE) : ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Class_Not_active') ?>" class="btn btn--primary btn--large color-white btn--disabled kids-bookbtn"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php else : ?>
                            <a href="javascript:void(0);" onclick="cart.proceedToStep({oneOnOne:1,fromKids:1,teacherId:<?php echo $class['grpcls_teacher_id']; ?>, grpclsId:<?php echo $class['grpcls_id'] ?>, languageId: <?php echo $class['grpcls_tlanguage_id'] ?>,lessonQty:<?php echo $class['grpcls_total_lesson'];?>}, 'getPaymentSummary');" class="btn btn--primary btn--large color-white kids-bookbtn NR"><?php echo Label::getLabel("LBL_Book_Now") ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>
<section class="kids-teachers-calender">
    <div class="container container--narrow kids-page-calender-section">
        <div id="availbility" class="calendar-wrapper__body"></div>
    </div>
</section>
<section class="faq-section">
    <div class="container">
        <div class="title">
            <h2><?php echo Label::getLabel("LBL_Dont_see_a_time_that_works_for_you?") ?></h2>
        </div>
        <div class="contactus">
            <a href="/contact" class="button-contact-us" target="_blank"><?php echo Label::getLabel('Lbl_Contact_Us'); ?></a>
        </div>
        <div class="accordian-wrp">
            <h3><?php echo Label::getLabel('Lbl_Description'); ?></h3>
            <strong><?php echo Label::getLabel('Lbl_Class_Experience'); ?></strong>
            <p><?php echo $class['grpcls_class_experience']; ?></p>
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><?php echo Label::getLabel('Lbl_Schedule'); ?></button>
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-down fa-w-14 small-text css-0"><path fill="#2ba7ce" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
                        </div>
                    
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <?php echo $class['grpcls_schedule']; ?>
                        </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"><?php echo Label::getLabel('Lbl_Learning_Goals'); ?></button>
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-down fa-w-14 small-text css-0"><path fill="#2ba7ce" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                        <div class="card-body">
                            <?php echo $class['grpcls_learning_goals'] ?>
                        </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingFour">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour"><?php echo Label::getLabel('Lbl_Supply_List'); ?></button>
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-down fa-w-14 small-text css-0"><path fill="#2ba7ce" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                            <div class="card-body">
                            <?php echo $class['grpcls_supply_list']; ?>
                            </div>
                        </div>
                        </div>
                    </div>
                <div class="card">
                    <div class="card-header" id="headingSeven">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">Parental Guidance</button>
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-down fa-w-14 small-text css-0"><path fill="#2ba7ce" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
                </div>
                <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                    <div class="card-body">
                    <?php echo $class['grpcls_parental_guidance']; ?>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade video-modal-wrp" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $class['grpcls_kids_title']; ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="" id="video"  allowscriptaccess="always" allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>
</div> 
        <!-- Video Modal End-->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
    $(document).ready(
      $('.show-meeting').click(function() {
        $(this).parents('.available-time-box').toggleClass('active');
        })
     );
    $(document).ready(
        $('.available-time-box').click(function() {
        $('.available-time-box').toggleClass('box-active');
        })
    );
        
</script>
<script>
        $(document).ready(function() {
      //  searchQualifications(<?php echo $teacher['user_id']; ?>);
        viewCalendar(<?php echo $class['user_id'] . ',' . $class['grpcls_tlanguage_id'] . ', "paid"'; ?>);
        $('body').removeClass('calendar-facebox');
        $('.panel__head-trigger-js').click(function() {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.panel__body-target-js').slideUp();
                return false;
            }
            $('.panel__head-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.panel__body-target-js').slideUp();
            $(this).siblings('.panel__body-target-js').slideDown();
            $('.slider-onethird-js').slick('reinit');
            if ($(this).hasClass('calendar--trigger-js')) {
                window.viewOnlyCal.render();
            }
        });
    });
    $(document).ready(function () {
        var sideNavMenu = $(".blog-desc p").height();
        if(sideNavMenu > 190){
            $(".p-0").css("display", "none");
            $(".p-1").css("display", "block");
        }
            else{
            $(".p-0").css("display", "block"); 
            $(".p-1").css("display", "none"); 
            }
        });
    $(document).ready(function() {
        
        var $videoSrc;  
        $('.video-btn').click(function() {
            $videoSrc = $(this).data( "src" );
        });
        console.log($videoSrc);
        $('#myModal').on('shown.bs.modal', function (e) {
        $("#video").attr('src',$videoSrc + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0" ); 
        })
        $('#myModal').on('hide.bs.modal', function (e) {
            // a poor man's stop video
            $("#video").attr('src',$videoSrc); 
        }) 
        });
</script>
<script>
    $('.available-time-box-slider').slick({
    dots: false,
    arrows: true,
    infinite: true,
    autoplay: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
        {
        breakpoint: 1024,
        settings: {
            slidesToShow: 3,
            slidesToScroll: 1
        }
        },
        {
        breakpoint: 600,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1
        }
        },
        {
        breakpoint: 480,
        settings: {
            slidesToShow: 1,
            slidesToScroll: 1
        }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
    ]
    });
</script>