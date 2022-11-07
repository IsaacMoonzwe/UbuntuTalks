<?php

defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>

<style>
    .lightbox-detail-text img {
        width: 230px;
        height: 230px;
        border-radius: 100%;
        float: left;
        margin: 0px 30px 0px 0px;
    }

    h4.profile-description {
        font-size: 17px !important;
        padding-top: 30px;
    }

    @media only screen and (max-width: 600px) {
        h4.profile-description {
        padding-top: 10px;
    }
    }
</style>
<!-- About -->
<section class="about">
    <?php echo FatUtility::decodeHtmlEntities($AboutUsContent); ?>
</section>

<!-- History  -->
<section class="history">
    <?php echo FatUtility::decodeHtmlEntities($HistoryPhilosophyContent); ?>
</section>

<!-- Vision -->
<section class="vision">
    <div class="container-defalut">
        <div class="vision-info">
            <?php
            foreach ($OurVisionCategoriesList as $value) {
                if (!empty($value['our_vision_image'])) {
                    foreach ($value['our_vision_image'] as $testimonialImg) {
                        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'ourvisionimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                    }
                }
            ?>
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="vision-img">
                            <?php echo $htmlAfterField; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="vision-txt text-white">
                            <h3><?php echo $value['our_vision_user_name']; ?></h3>
                            <p><?php echo $value['our_vision_description']; ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="mission-info">
            <?php
            foreach ($OurMissionCategoriesList as $value) {
                if (!empty($value['our_mission_image'])) {
                    foreach ($value['our_mission_image'] as $testimonialImg) {
                        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'ourmissionimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                    }
                }
            ?>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="mission-txt">
                            <h3><?php echo $value['our_mission_user_name']; ?></h3>
                            <p><?php echo $value['our_mission_description']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mission-img">
                            <?php echo $htmlAfterField; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="support-info">
            <?php echo FatUtility::decodeHtmlEntities($SupportPhilanthropy); ?>
        </div>
    </div>
</section>


<section class="team">
    <div class="container-defalut">
        <div class="team-info text-center">
            <h3><?php echo Label::getLabel('LBL_Meet_The_Team', $adminLangId); ?></h3>
            <h3><?php echo Label::getLabel('LBL_Executive_Leaderships', $adminLangId); ?></h3>
        </div>
        <div class="others-speakers aboutus-speakers-listing">
            <div class="gallery-wrapper">
                <div class="row">
                    <?php
                    foreach ($ExecutiveLeadershipCategoriesList as  $value) {
                        if (!empty($value['meet_the_team_image'])) {
                            foreach ($value['meet_the_team_image'] as $testimonialImg) {
                                $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                            }
                        }
                    ?>
                        <div class="col-lg-4">
                            <div class="image-wrapper"> <a href=<?php echo "#" . $value['meet_the_team_id']; ?>>
                                    <div class="lightbox-image-box">
                                        <?php echo $htmlAfterField; ?>
                                    </div>
                                    <div class="image-title">
                                        <h3><?php echo $value['meet_the_team_user_name']; ?></h3>
                                        <p class="list-sub-title"><?php echo $value['meet_the_team_positions']; ?></p>

                                    </div>
                                </a> </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="gallery-lightboxes">
                <?php foreach ($ExecutiveLeadershipCategoriesList as  $value) {
                    if (!empty($value['meet_the_team_image'])) {
                        foreach ($value['meet_the_team_image'] as $testimonialImg) {
                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                        }
                    }    ?>
                    <div class="image-lightbox" id=<?php echo $value['meet_the_team_id']; ?>>
                        <div class="image-lightbox-wrapper"> <a href="#0" class="close"></a>
                            <div class="lightbox-detail-text">
                                <div>
                                    <?php echo $htmlAfterField; ?>
                                    <h3 class="list-sub-title break-all">
                                        <?php echo $value['meet_the_team_user_name']; ?>
                                    </h3>
                                    <h5 class="list-sub-title ">
                                        <?php echo $value['meet_the_team_positions']; ?>
                                    </h5>
                                    <h4 class="profile-description">
                                        <?php echo $value['meet_the_team_description']; ?>
                                    </h4>
                                </div>
                                <!-- <div class="lightbox-top-detail">
                                    <div class="lightbox-image-box">
                                        <?php //echo $htmlAfterField; 
                                        ?>
                                        <?php //echo $value['meet_the_team_description']; 
                                        ?>
                                    </div>
                                    <div class="image-title">
                                        <h3 class="list-sub-title break-all">
                                            <?php //echo $value['meet_the_team_user_name']; 
                                            ?></h3>
                                        <h5 class="list-sub-title "><?php //echo $value['meet_the_team_positions']; 
                                                                    ?></h5>

                                    </div>
                                </div>
                                <div class="lightbox-bottom-detail">
                                    <div class="profile-details">
                                        <h5 class="strong" style="padding-bottom:0px;"><?php echo $value['meet_the_team_user_name']; ?></h5>
                                        <h5 class="list-sub-title "><?php echo $value['meet_the_team_positions']; ?></h5>
                                        <div class="profile-description">
                                            <?php //echo $value['meet_the_team_description']; 
                                            ?>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- Senior Board Advisors -->
<section>
    <div class="container-defalut">
        <div class="team-info text-center">
            <h3><?php echo Label::getLabel('LBL_Senior_Board_Advisors', $adminLangId); ?></h3>
        </div>
        <div class="others-speakers aboutus-speakers-listing">
            <div class="gallery-wrapper">
                <div class="row">
                    <?php
                    foreach ($SeniorBoardAdvisorsCategoriesList as  $value) {
                        if (!empty($value['meet_the_team_image'])) {
                            foreach ($value['meet_the_team_image'] as $testimonialImg) {
                                $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                            }
                        }
                    ?>
                        <div class="col-lg-4">
                            <div class="image-wrapper"> <a href=<?php echo "#" . $value['meet_the_team_id']; ?>>
                                    <div class="lightbox-image-box">
                                        <?php echo $htmlAfterField; ?>
                                    </div>
                                    <div class="image-title">
                                        <h3><?php echo $value['meet_the_team_user_name']; ?></h3>
                                        <!-- <h5 class="strong" style="padding-bottom:0px;"><?php echo $value['meet_the_team_user_name']; ?></h5> -->
                                        <p class="list-sub-title"><?php echo $value['meet_the_team_positions']; ?></p>

                                    </div>
                                </a> </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="gallery-lightboxes">
                <?php foreach ($SeniorBoardAdvisorsCategoriesList as  $value) {
                    if (!empty($value['meet_the_team_image'])) {
                        foreach ($value['meet_the_team_image'] as $testimonialImg) {
                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                        }
                    }    ?>
                    <div class="image-lightbox" id=<?php echo $value['meet_the_team_id']; ?>>
                        <div class="image-lightbox-wrapper"> <a href="#0" class="close"></a>

                            <div class="lightbox-detail-text">
                                <div>
                                    <?php echo $htmlAfterField; ?>
                                    <h3 class="list-sub-title break-all">
                                        <?php echo $value['meet_the_team_user_name']; ?>
                                    </h3>
                                    <h5 class="list-sub-title ">
                                        <?php echo $value['meet_the_team_positions']; ?>
                                    </h5>
                                    <h4 class="profile-description">
                                        <?php echo $value['meet_the_team_description']; ?>
                                    </h4>
                                </div>
                                <!-- <div class="lightbox-top-detail">
                                    <div class="lightbox-image-box">
                                        <?php echo $htmlAfterField; ?> </div>
                                    <div class="image-title">
                                        <h3 class="list-sub-title break-all">
                                            <?php //echo $value['meet_the_team_user_name']; 
                                            ?></h3>
                                        <h5 class="list-sub-title "><?php //echo $value['meet_the_team_positions']; 
                                                                    ?></h5>
                                    </div>
                                </div>
                                <div class="lightbox-bottom-detail">
                                    <div class="profile-details">
                                        <h5 class="strong" style="padding-bottom:0px;"><?php echo $value['meet_the_team_user_name']; ?></h5>
                                        <h5 class="list-sub-title "><?php echo $value['meet_the_team_positions']; ?></h5>
                                        <div class="profile-description">
                                            <?php echo $value['meet_the_team_description']; ?>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- Directors & Senior Managers -->
<section>
    <div class="container-defalut">
        <div class="team-info text-center">
            <h3><?php echo Label::getLabel('LBL_Directors_&_Senior_Managers', $adminLangId); ?></h3>
        </div>
        <div class="others-speakers aboutus-speakers-listing">
            <div class="gallery-wrapper">
                <div class="row">
                    <?php
                    foreach ($DirectorsSeniorManagersCategoriesList as  $value) {
                        if (!empty($value['meet_the_team_image'])) {
                            foreach ($value['meet_the_team_image'] as $testimonialImg) {
                                $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                            }
                        }
                    ?>
                        <div class="col-lg-4">
                            <div class="image-wrapper"> <a href=<?php echo "#" . $value['meet_the_team_id']; ?>>
                                    <div class="lightbox-image-box">
                                        <?php echo $htmlAfterField; ?>
                                    </div>
                                    <div class="image-title">
                                        <h3><?php echo $value['meet_the_team_user_name']; ?></h3>
                                        <p class="list-sub-title"><?php echo $value['meet_the_team_positions']; ?></p>
                                    </div>
                                </a> </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="gallery-lightboxes">
                <?php foreach ($DirectorsSeniorManagersCategoriesList as  $value) {
                    if (!empty($value['meet_the_team_image'])) {
                        foreach ($value['meet_the_team_image'] as $testimonialImg) {
                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('AboutUs', 'aboutusimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                        }
                    }    ?>
                    <div class="image-lightbox" id=<?php echo $value['meet_the_team_id']; ?>>
                        <div class="image-lightbox-wrapper"> <a href="#0" class="close"></a>
                            <div class="lightbox-detail-text">
                                <div>
                                    <?php echo $htmlAfterField; ?>
                                    <h3 class="list-sub-title break-all">
                                        <?php echo $value['meet_the_team_user_name']; ?>
                                    </h3>
                                    <h5 class="list-sub-title ">
                                        <?php echo $value['meet_the_team_positions']; ?>
                                    </h5>
                                    <h4 class="profile-description">
                                        <?php echo $value['meet_the_team_description']; ?>
                                    </h4>
                                </div>
                                <!-- <div class="lightbox-top-detail">
                                    <div class="lightbox-image-box">
                                        <?php echo $htmlAfterField; ?> </div>
                                    <div class="image-title">
                                        <h3 class="list-sub-title break-all">
                                            <?php //echo $value['meet_the_team_user_name']; 
                                            ?></h3>
                                        <h5 class="list-sub-title "><?php //echo $value['meet_the_team_positions']; 
                                                                    ?></h5>
                                    </div>
                                </div>
                                <div class="lightbox-bottom-detail">
                                    <div class="profile-details">
                                        <h5 class="strong" style="padding-bottom:0px;"><?php echo $value['meet_the_team_user_name']; ?></h5>
                                        <h5 class="list-sub-title "><?php echo $value['meet_the_team_positions']; ?></h5>
                                        <div class="profile-description">
                                            <?php echo $value['meet_the_team_description']; ?>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>