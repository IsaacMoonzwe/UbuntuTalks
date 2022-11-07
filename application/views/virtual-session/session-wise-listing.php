<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<section class="section section--contect">
    <div class="container container--fixed container--narrow" id="sessionwiselisting">
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
                        <!-- session-->
                        <section class="session">
                            <div class="container-width">
                                <div class="helth-info">
                                    <h1><?php echo $VirtualSessionList['virtual_main_session_title']; ?> | <?php echo $VirtualSessionList['virtual_main_session_sub_title'] ?></h1>
                                </div>
                            </div>
                        </section>

                        <!-- Health-deatil -->
                        <section class="health-deatil">
                            <div class="container-width">
                                <?php
                                foreach ($records as $value) {
                                    if (!empty($value['speaker_image'])) {
                                        foreach ($value['speaker_image'] as $testimonialImg) {
                                            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('VirtualSession', 'imageSession', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                                        }
                                    }
                                ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="health-img">
                                                <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionDetails', [CommonHelper::htmlEntitiesDecode($value['virtual_session_slug'])]); ?>">
                                                    <?php echo $htmlAfterField; ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="health-txt">
                                                <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionDetails', [CommonHelper::htmlEntitiesDecode($value['virtual_session_slug'])]); ?>">

                                                    <h2><?php echo $value['virtual_session_title_name']; ?></h2>
                                                </a>
                                                <div class="file-detail">
                                                    <div class="file-date">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                        <span>
                                                            <?php
                                                            $Createddate = explode(" ", $VirtualSessionList['virtual_main_session_added_on']);
                                                            $CreatedDate_Convert = date("d F Y", strtotime($Createddate[0]));
                                                            echo $CreatedDate_Convert;
                                                            ?>
                                                        </span>
                                                    </div>
                                                    <div class="file-date">
                                                        <i class="fa fa-file-o" aria-hidden="true"></i>
                                                        <span><?php echo $VirtualSessionList['virtual_main_session_title']; ?> | <?php echo $VirtualSessionList['virtual_main_session_sub_title'] ?></span>
                                                    </div>
                                                </div>
                                                <?php
                                                $Startdate = explode(" ", $VirtualSessionList['virtual_main_session_start_time']);
                                                $StartDate_Convert = date("d F Y, h:i:s A", strtotime($Startdate[1]));
                                                $Enddate = explode(" ", $VirtualSessionList['virtual_main_session_end_time']);
                                                $EndDate_Convert = date("g:iA", strtotime($Enddate[1]));
                                                ?>

                                                <div class="author">
                                                    <p>Authors: <?php echo $value['virtual_session_author_name']; ?></p>
                                                    <p>Live discussion with the authors: <br><?php echo $StartDate_Convert; ?> - <?php echo $EndDate_Convert; ?></p>
                                                </div>
                                                <a role="button" type="button" href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionDetails', [CommonHelper::htmlEntitiesDecode($value['virtual_session_slug'])]); ?>" class="health-btn">VIEW
                                                    MORE</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </section>
                    </main>
                </body>
            </div>
        </div>
    </div>
</section>