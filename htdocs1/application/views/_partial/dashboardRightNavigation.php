<div class="page-panel__inner-r">
    <div class="box-group">
        <div class="box -align-center" style="margin-bottom: 30px;">
            <div class="-padding-20">
                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($userDetails['user_first_name']); ?>">
                    <?php
                    if (true == User::isProfilePicUploaded($userDetails['user_id'])) {
                        $img = CommonHelper::generateUrl('Image', 'user', array($userDetails['user_id'], 'MEDIUM')) . '?' . time();
                        echo '<img src="' . $img . '" alt="" />';
                    }
                    ?>
                        <!--<span class="tag-online"></span>-->
                </div>
                <span class="-gap"></span>
                <h3 class="-display-inline">
                    <h3 class="-display-inline"><?php echo $userDetails['user_first_name']; ?></h3>
                </h3>
                <?php if ($userDetails['user_country_id'] > 0) { ?>
                    <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($userDetails['user_country_id'], 'DEFAULT')); ?>" alt=""></span>
                <?php } ?>
                <p class="-no-margin-bottom"><?php
                    echo $userDetails['countryName'] . "<br>";
                    if ($userDetails['user_timezone'] != '') {
                        //echo CommonHelper::getDateOrTimeByTimeZone($userDetails['user_timezone'],' h:i:s A (P)');
                        echo CommonHelper::getDateOrTimeByTimeZone($userDetails['user_timezone'], 'h:i A');
                        echo " (" . Label::getLabel('LBL_TIMEZONE_STRING') . " " . CommonHelper::getDateOrTimeByTimeZone($userDetails['user_timezone'], ' P') . ")";
                    }
                    ?></p>
            </div>
            <div class="tabled">
                <div class="tabled__cell">
                    <h3 class="-color-secondary"><?php echo $userDetails['learnerSchLessonsExcPast']; ?></h3> <?php echo Label::getLabel('LBL_Scheduled'); ?>
                </div>
                <div class="tabled__cell">
                    <h3 class="-color-primary"><?php echo $userDetails['learnerTotLessons']; ?></h3> <?php echo Label::getLabel('LBL_Lesson(s)'); ?>
                </div>
            </div>
        </div>
        <?php
        if ($userDetails['teacherIds']) {
            $teacherIds = explode(',', $userDetails['teacherIds']);
            ?>
            <div class="box -padding-20 -align-center" style="margin-bottom: 30px;">
                <h4><?php echo Label::getLabel('LBL_My_Teachers'); ?></h4>
                <div class="avtars-list">
                    <ul>
                        <?php foreach ($teacherIds as $teacherId) { ?>
                            <li>
                                <a href="javascript:void(0)">
                                    <figure class="avtar avtar--small" data-title="A">
                                        <img src="<?php echo CommonHelper::generateUrl('image', 'user', array($teacherId)) ?>" alt="">
                                    </figure>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <a href="<?php echo CommonHelper::generateUrl('LearnerTeachers') ?>" class="-link-underline link-color"><?php echo Label::getLabel('LBL_See_All_Teachers'); ?></a>
            </div>
        <?php } ?>
    </div>
</div>