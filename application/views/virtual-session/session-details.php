<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<!-- Header -->
<section class="section section--contect">
    <div class="container container--fixed container--narrow">
        <div class="section contact-form">
            <div class="container container--narrow">
                <div class="row">
                    <img src="<?php echo CommonHelper::generateUrl('Image', 'VirtualSessionCampaign', [$siteLangId]); ?>" alt="">
                </div>
                <div id="navigation">
                    <?php
                    $this->includeTemplate('virtual-session/navigation-menu.php', ['siteLangId' => $siteLangId]);
                    ?>
                </div>

                <body>
                    <main>
                        <!-- visibility -->
                        <section class="visibility">
                            <div class="container-width">
                                <div class="w-100 w-md-100">
                                    <div class="visibility-txt">
                                        <h1><?php echo $SessionWiseListing['virtual_session_title_name']; ?></h1>
                                    </div>
                                    <div class="file-date">
                                        <i class="fa fa-file-o" aria-hidden="true"></i>
                                        <span><?php echo $VirtualSessionList['virtual_main_session_title']; ?> | <?php echo $VirtualSessionList['virtual_main_session_sub_title']; ?></span>
                                    </div>
                                    <div class="author">
                                        <p>Authors: <?php echo $SessionWiseListing['virtual_session_author_name']; ?></p>
                                        <?php
                                        $Startdate = explode(" ", $VirtualSessionList['virtual_main_session_start_time']);
                                        $StartDate_Convert = date("d F Y, h:i:s A", strtotime($Startdate[1]));
                                        $Enddate = explode(" ", $VirtualSessionList['virtual_main_session_end_time']);
                                        $EndDate_Convert = date("g:iA", strtotime($Enddate[1]));
                                        ?>
                                        <p><?php echo Label::getLabel('LBL_Live_discussion_with_the_authors:', $adminLangId); ?><?php echo $StartDate_Convert; ?> - <?php echo $EndDate_Convert; ?></p>
                                    </div>
                                    <div class="visibility-img">
                                        <?php

                                        if (!empty($SessionWiseListing['speaker_image'])) {
                                            foreach ($SessionWiseListing['speaker_image'] as $testimonialImg) {
                                                $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('VirtualSession', 'imageSession', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                                            }
                                        }
                                        ?>
                                        <?php echo $htmlAfterField; ?>
                                        <?php if ($SessionWiseListing['virtual_session_video_link'] != '') { ?>
                                            <div>
                                                <iframe src="<?php echo $SessionWiseListing['virtual_session_video_link']; ?>" allowfullscreen="" width="900" height="400" frameborder="0"></iframe>
                                            </div>
                                        <?php } ?>

                                    </div>
                                    <div>
                                        <p><?php echo Label::getLabel('LBL_Click_on_the_links_below_to_view_the_supporting_research_documentation:', $adminLangId); ?></p>

                                        <ul class="code-list">
                                            <li><a href="<?php echo $SessionWiseListing['virtual_session_research_poster']; ?>" class="actives"><?php echo Label::getLabel('LBL_Full_size_research_poster', $adminLangId); ?></a></li>
                                            <li><a href="<?php echo $SessionWiseListing['virtual_session_research_brief']; ?>" class="actives"><?php echo Label::getLabel('LBL_Research_brief', $adminLangId); ?></a></li>
                                            <li><a href="<?php echo $SessionWiseListing['virtual_session_code_list']; ?>" class="actives"><?php echo Label::getLabel('LBL_Code_List', $adminLangId); ?></a></li>
                                        </ul>
                                    </div>
                                    <div class="visibility-btn">
                                        <a role="button" href="<?php echo $SessionWiseListing['virtual_session_twitter_link']; ?>" class="tweet-btn"><i class="fa fa-twitter" aria-hidden="true"></i>
                                            </i><?php echo Label::getLabel('LBL_Tweet', $adminLangId); ?></a>
                                        <a role="button" href="<?php echo $SessionWiseListing['virtual_session_facebook_link']; ?>" class="fb-btn"><i class="fa fa-facebook" aria-hidden="true"></i>
                                            </i><?php echo Label::getLabel('LBL_Share', $adminLangId); ?></a>
                                        <a role="button" href="<?php echo $SessionWiseListing['virtual_session_linkedin_link']; ?>" class="linkdin-btn"><i class="fa fa-linkedin" aria-hidden="true"></i>
                                            </i><?php echo Label::getLabel('LBL_Research_Share', $adminLangId); ?></a>
                                        <a role="button" href="<?php echo $SessionWiseListing['virtual_session_email_link']; ?>" class="mail-btn"><i class="fa fa-envelope" aria-hidden="true"></i>
                                            </i><?php echo Label::getLabel('LBL_Email', $adminLangId); ?></a>
                                    </div>
                                    <div class="more-explore">
                                        <h2><?php echo Label::getLabel('LBL_More_To_Explore', $adminLangId); ?></h2>
                                        <hr>
                                        <div class="row">
                                            <?php
                                            foreach ($ExploreSessionWiseListing as $value) {
                                                if (!empty($value['speaker_image'])) {
                                                    foreach ($value['speaker_image'] as $testimonialImg) {
                                                        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('VirtualSession', 'imageSession', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                                                    }
                                                }
                                            ?>
                                                <div class="col-md-3">
                                                    <div class="explore-img">
                                                        <a href="<?php echo $value['virtual_session_slug']; ?>">
                                                            <?php echo $htmlAfterField; ?>
                                                        </a>
                                                        <div class="explore-txt">
                                                            <a href="<?php echo $value['virtual_session_slug']; ?>"><?php echo $value['virtual_session_title_name']; ?></a>
                                                            <p>Authors: <?php echo $value['virtual_session_author_name']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }  ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </section>
                    </main>
                </body>
            </div>
        </div>
    </div>
</section>