<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--grey section--details">
    <div class="container container--narrow">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-10">
                <div class="breadcrumb">
                    <ul>
                        <li><a href="<?php echo CommonHelper::generateUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                        <li><a href="<?php echo CommonHelper::generateUrl('teachers'); ?>"><?php echo Label::getLabel('LBL_Tutors'); ?></a></li>
                        <li><?php echo $teacher['user_full_name']; ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="box -padding-30 box--preview">
                    <div class="box__profile-head">
                        <div class="row align-items-center">
                            <!-- Image[ -->
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 -align-center">
                                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($teacher['user_first_name']); ?>">
                                    <?php
                                    if (true == User::isProfilePicUploaded($teacher['user_id'])) {
                                        $img = CommonHelper::generateUrl('Image', 'User', array($teacher['user_id']));
                                        echo '<img src="' . $img . '" />';
                                    }
                                    ?>
                                </div> 
                            </div>
                            <!-- ] -->
                            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-8">
                                <h3 class="-display-inline"><a href="<?php echo CommonHelper::generateUrl('teachers', 'profile') . '/' . $teacher['user_url_name']; ?>"><?php echo $teacher['user_full_name']; ?></a></h3>
                                <?php if ($teacher['user_country_id'] > 0) { ?>
                                    <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($teacher['user_country_id'], 'DEFAULT')); ?>" alt=""></span>
                                <?php } ?>
                                <!-- User Location[ -->
                                <div class="location">
                                    <span class="svg-icon"><svg xmlns="http://www.w3.org/2000/svg" width="11.625" height="15.969" viewBox="0 0 11.625 15.969">
                                            <path d="M462.04,225.016a5.805,5.805,0,0,0-5.81,5.788c0,3.96,5.2,9.774,5.421,10.02a0.524,0.524,0,0,0,.778,0c0.221-.246,5.42-6.06,5.42-10.02A5.8,5.8,0,0,0,462.04,225.016Zm0,8.7a2.912,2.912,0,1,1,2.923-2.912A2.921,2.921,0,0,1,462.04,233.716Z" transform="translate(-456.219 -225.031)"/>
                                        </svg></span>
                                    <?php echo $teacher['user_country_name']; ?> 
                                    <?php
                                    echo ", " . CommonHelper::getDateOrTimeByTimeZone($teacher['user_timezone'], 'h:i A');
                                    echo " (" . Label::getLabel('LBL_TIMEZONE_STRING') . " " . CommonHelper::getDateOrTimeByTimeZone($teacher['user_timezone'], ' P') . ")";
                                    ?>
                                </div>
                                <span class="-gap"></span>
                                <!-- ] -->
                                <!-- Reviews[ -->
                                <div class="ratings -display-inline">
                                    <span class="ratings__star -display-inline">
                                        <?php
                                        if (round($teacher['teacher_rating'])) {
                                            for ($i = 0; $i < round($teacher['teacher_rating']); $i++) {
                                                ?>
                                                <img src="<?php echo CONF_WEBROOT_URL; ?>images/star-filled.svg" alt="">
                                                    <?php
                                                }
                                                for ($i = 0; $i < 5 - round($teacher['teacher_rating']); $i++) {
                                                    ?>
                                                    <img src="<?php echo CONF_WEBROOT_URL; ?>images/star-empty.svg" alt="">
                                                        <?php
                                                    }
                                                }
                                                ?>    
                                                </span>
                                                <?php if ($teacher['totReviews']) { ?>  								  
                                                    <span class="ratings__count -display-inline"><?php echo $teacher['totReviews'] . ' ' . Label::getLabel('Lbl_Reviews'); ?></span>
                                                <?php } ?>								  
                                                </div>
                                                <span class="-gap"></span>
                                                <!-- ]-->
                                                <!-- Favorite[ -->
                                                <a href="javascript:void(0)" onClick="toggleTeacherFavorite(<?php echo $teacher['user_id']; ?>, this)" class="btn btn--small btn--bordered btn--fav <?php echo($teacher['uft_id']) ? 'is-active' : ''; ?>">
                                                    <span class="svg-icon"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="12" viewBox="0 0 14 12">
                                                            <path d="M1313.03,714.438l-4.53,4.367-4.54-4.375a4.123,4.123,0,0,1-1.46-2.774,3.455,3.455,0,0,1,.17-1.117,2.067,2.067,0,0,1,.43-0.769,1.972,1.972,0,0,1,.63-0.465,2.818,2.818,0,0,1,.74-0.242,4.378,4.378,0,0,1,.76-0.063,2.222,2.222,0,0,1,.88.2,3.987,3.987,0,0,1,.86.5,8.351,8.351,0,0,1,.68.563,6.435,6.435,0,0,1,.47.48,0.506,0.506,0,0,0,.76,0,6.435,6.435,0,0,1,.47-0.48,8.351,8.351,0,0,1,.68-0.563,3.987,3.987,0,0,1,.86-0.5,2.222,2.222,0,0,1,.88-0.2,4.378,4.378,0,0,1,.76.063,2.818,2.818,0,0,1,.74.242,1.972,1.972,0,0,1,.63.465,2.067,2.067,0,0,1,.43.769,3.455,3.455,0,0,1,.17,1.117,4.126,4.126,0,0,1-1.47,2.782h0Zm1.48-5.469a3.76,3.76,0,0,0-2.74-.969,3.064,3.064,0,0,0-.99.168,3.963,3.963,0,0,0-.94.453q-0.435.285-.75,0.535c-0.2.167-.4,0.344-0.59,0.532-0.19-.188-0.39-0.365-0.59-0.532s-0.46-.345-0.75-0.535a3.963,3.963,0,0,0-.94-0.453,3.064,3.064,0,0,0-.99-0.168,3.76,3.76,0,0,0-2.74.969,3.586,3.586,0,0,0-.99,2.687,3.479,3.479,0,0,0,.18,1.078,5.7,5.7,0,0,0,.42.946,7.129,7.129,0,0,0,.53.761c0.2,0.248.35,0.418,0.44,0.512a2.683,2.683,0,0,0,.21.2l4.88,4.7a0.48,0.48,0,0,0,.68,0l4.87-4.687a5.122,5.122,0,0,0,1.79-3.516A3.586,3.586,0,0,0,1314.51,708.969Z" transform="translate(-1301.5 -708)"/>
                                                        </svg></span>
                                                    <?php echo Label::getLabel('LBL_Favorite'); ?>
                                                </a>
                                                <!-- ] -->
                                                </div>
                                                <!-- [-->
                                                <div class="col-xl-3 col-lg-3 col-md-8 col-sm-12 offset-min-12">
                                                    <div class="box-highlighted box-language">
                                                        <div class="row d-block justify-content-between">
                                                            <div class="col"><strong><?php echo Label::getLabel('LBL_Teaches:'); ?></strong></div>
                                                            <div class="col"><?php echo CommonHelper::getTeachLangs($teacher['utl_tlanguage_ids'], '', 1); ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="box-highlighted">
                                                        <div class="row justify-content-between">
                                                            <div class="col"><strong><?php echo Label::getLabel('LBL_Students:'); ?></strong></div>
                                                            <div class="col -align-right"><?php echo $teacher['studentIdsCnt']; ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="box-highlighted">
                                                        <div class="row justify-content-between">
                                                            <div class="col"><strong><?php echo Label::getLabel('LBL_Lessons:'); ?></strong></div>
                                                            <div class="col -align-right"><?php echo $teacher['teacherTotLessons']; ?></div>
                                                        </div>
                                                    </div>
                                                    <a href="<?php echo CommonHelper::generateUrl('teachers', 'profile') . '/' . $teacher['user_url_name']; ?>" class="btn btn--secondary btn--large btn--block"><?php echo Label::getLabel('LBL_View_Profile'); ?></a>
                                                </div>
                                                <!-- ] -->
                                                </div>
                                                </div>
                                                <hr>
                                                    <div class="box__profile-body">
                                                        <?php
                                                        /* Spoken Languages[ */
                                                        $this->includeTemplate('teachers/_partial/spokenLanguages.php', $teacher, false);
                                                        /* ] */
                                                        ?>
                                                        <div class="content-inline">
                                                            <p class="-small-title"><strong><?php echo Label::getLabel('LBL_About_Me'); ?></strong></p>
                                                            <p><?php echo $teacher['user_profile_info']; ?></p>
                                                            <!--<a href="javascript:void(0)" class="btn btn--small btn--bordered btn--wide btn--arrow">Show More</a>-->
                                                        </div>
                                                    </div>
                                                    </div>	
                                                    <span class="-gap"></span>
                                                    <div class="box">
                                                        <div class="tabs-inline tabs-js">
                                                            <ul>
                                                                <li class="is-active"><a href="javascript:void(0)" onClick="searchLessons(<?php echo $teacher['user_id']; ?>)"><?php echo Label::getLabel('LBL_My_Lessons'); ?></a></li>
                                                                <?php if (FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true)) { ?>
                                                                    <li><a href="javascript:void(0)" onClick="searchFlashCards(<?php echo $teacher['user_id']; ?>)" ><?php echo Label::getLabel('LBL_Flashcards'); ?></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>	
                                                    <div id="gt-data" class="tab-data-container tab-data-container-preview">
                                                        111111111111
                                                    </div>
                                                    </div>			
                                                    </div>
                                                    </div>
                                                    </section>
                                                    <script >
                                                        $(document).ready(function () {
                                                            searchLessons(<?php echo $teacher['user_id']; ?>);
                                                        });
                                                    </script>