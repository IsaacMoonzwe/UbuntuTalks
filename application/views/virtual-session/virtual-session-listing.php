<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

<div id="navigation">
    <?php
    $this->includeTemplate('virtual-session/navigation-menu.php', ['siteLangId' => $siteLangId]);
    ?>
</div>

<main>
    <!-- Education  -->
    <section class="education">
        <div class="education-box">
            <div class="container-width">
                <h1><?php echo Label::getLabel('LBL_Pushing_higher_education_forward_through_research.', $adminLangId); ?></h1>
            </div>
        </div>
    </section>

    <!-- Session Titles -->
    <section class="edu-session">
        <div class="container-width">
            <div class="edu-session-txt">
                <p>Click the <b>Session Titles</b> below to view and comment on the research posters.
                    Join the Discussion via Zoom on May 10th, 2022 (4:30pm – 7pm PT).
                    <a href="https://www.zoom.us/" target="_blank"><span class="actives">(Zoom Help)</span></a></h2>
            </div>
            <div class="row">
                <?php foreach ($records as $value) {
                    if (!empty($value['speaker_image'])) {
                        foreach ($value['speaker_image'] as $testimonialImg) {
                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('VirtualSession', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                        }
                    }
                ?>
                    <div class="col-md-4">
                        <div class="student-img">
                            <?php echo $htmlAfterField; ?>
                            <?php
                            $Startdate = explode(" ", $value['virtual_main_session_start_time']);
                            $StartDate_Convert = date("g:iA", strtotime($Startdate[1]));
                            $Enddate = explode(" ", $value['virtual_main_session_end_time']);
                            $EndDate_Convert = date("g:iA", strtotime($Enddate[1]));
                            ?>
                            <div class="session-detail">
                                <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionWiseListing', [CommonHelper::htmlEntitiesDecode($value['virtual_main_session_slug'])]); ?>">

                                    <?php echo $value['virtual_main_session_title']; ?> |
                                    <?php echo $StartDate_Convert; ?> -
                                    <?php echo $EndDate_Convert; ?> |
                                    <?php echo $value['virtual_main_session_sub_title']; ?>
                                </a>

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</main>