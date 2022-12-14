<div class="container container--fixed">
    <div class="page__head">
        <h1><?php echo Label::getLabel('Lbl_Manage_Calendar'); ?></h1>
    </div>
    <div class="page__body">
        <!-- [ INFO BAR ========= -->
        <div class="infobar">
            <div class="row justify-content-between align-items-start">
                <div class="col-lg-8 col-sm-8">
                    <div class="d-flex">
                        <div class="infobar__media margin-right-5">
                            <div class="infobar__media-icon infobar__media-icon--alert is-profile-complete-js">!</div>
                        </div>
                        <div class="infobar__content">
                            <h6 class="margin-bottom-1"><?php echo Label::getLabel('Lbl_Complete_Your_profile'); ?></h6>
                            <p class="margin-0"> <?php echo Label::getLabel('LBL_PROFILE_INFO_HEADING'); ?>
                                <a href="javascript:void(0)" class="color-secondary underline padding-top-3 padding-bottom-3 expand-js"><?php echo Label::getLabel('LBL_Learn_More'); ?></a>
                            </p>
                            <div class="infobar__content-more margin-top-3 expand-target-js" style="display: none;">
                                <?php echo ExtraPage::getBlockContent(ExtraPage::BLOCK_PROFILE_INFO_BAR, $siteLangId); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-4">
                    <div class="profile-progress margin-top-2">
                        <div class="profile-progress__meta margin-bottom-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div><span class="small"> <?php echo Label::getLabel('LBL_Profile_progress'); ?></span></div>
                                <div><span class="small bold-700 progress-count-js"></span></div>
                            </div>
                        </div>
                        <div class="profile-progress__bar">
                            <div class="progress progress--small progress--round">
                                <!-- <div class="progress__bar bg-green" role="progressbar" style="width:60%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div> -->
                                <div class="progress-bar">
                                    <div class="progress__step profile-Info-progress-js"></div>
                                    <div class="progress__step teacher-lang-progress-js"></div>
                                    <div class="progress__step teacher-lang-price-progress-js"></div>
                                    <div class="progress__step teacher-qualification-progress-js"></div>
                                    <div class="progress__step teacher-preferences-progress-js"></div>
                                    <div class="progress__step general-availability-progress-js"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ] -->
        <!-- [ PAGE PANEL ========= -->
        <div class="page-panel teacher_available_cal" style="min-height: 700px;" id="availability-calendar-js">
        </div>
        <!-- ] -->
    </div>