<?php if ($lessons) { ?>
    <section class="section section--black section--lessons">
        <div class="container container--smnarrow">
            <div class="section-title">
                <h2><?php echo Label::getLabel('LBL_Upcoming_Scheduled_Lessons'); ?></h2>
            </div>
            <div class="<?php if (count($lessons) > 4) : ?> vert-carousel <?php endif; ?>">

                <?php foreach ($lessons as $lesson) {
                    $teacherUrl = CommonHelper::generateUrl('Teachers', 'view', [CommonHelper::htmlEntitiesDecode($lesson['user_url_name'])]);
                ?>
                    <div class="repeat-listing -border">
                        <div class="row justify-content-between">
                            <div class="col-xl-2 col-md-2 date-format">
                                <span class="date"><?php echo date('d', strtotime($lesson['slesson_date'])); ?></span>
                                <div class="date-info">
                                    <span class="day"><?php echo date('D', strtotime($lesson['slesson_date'])); ?></span>
                                    <span class="month"><?php echo date('M', strtotime($lesson['slesson_date'])); ?></span>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-8 col-md-5 leason-consult align-center">
                                <h3><?php echo $lesson['teacherFname'] . ' ' . $lesson['teacherLname']; ?></h3>
                                <span class="location"><?php echo $lesson['teacherCountryName']; ?></span>
                                <p><?php echo date('M d,Y', strtotime($lesson['slesson_date'])) . ', ' . $lesson['slesson_start_time'] . ' - ' . $lesson['slesson_end_time']; ?></p>
                            </div>
                            <div class="col-auto selector-pic">
                                <a href="<?php echo $teacherUrl; ?>" target="_blank">
                                    <figure><img src="<?php echo CommonHelper::generateUrl('Image', 'User', array($lesson['teacherId'], 'normal', 1)); ?>" alt=""></figure>
                                </a>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <section class="section section-cta">
        <div class="container container--fixed">
            <div class="cta-wrap">
                <h6><?php echo Label::getLabel('LBL_Start_now_and_turn_Text'); ?></h6>
                <a href="#" class="btn"><?php echo Label::getLabel('LBL_Get_started_now!'); ?></a>
            </div>
        </div>
    </section>
<?php } ?>