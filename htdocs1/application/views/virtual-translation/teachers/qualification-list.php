<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
foreach ($qualifications as $type => $qualification) {
    ?>
    <div class="row row--resume">
        <div class="col-xl-4 col-lg-4 col-sm-4">
            <h4 class="color-dark"><?php echo $qualificationTypeArr[$type]; ?></h4>
        </div>
        <div class="col-xl-8 col-lg-8 col-sm-8 offset">
            <?php foreach ($qualification as $qualificationRow) { ?>
                <div class="resume-wrapper">
                    <div class="row">
                        <div class="col-4 col-sm-4">
                            <div class="resume__primary"><b><?php echo $qualificationRow['uqualification_start_year'] ?> - <?php echo $qualificationRow['uqualification_end_year'] ?></b></div>
                        </div>
                        <div class="col-7 col-sm-7 offset-1">
                            <div class="resume__secondary">
                                <b><?php echo $qualificationRow['uqualification_title']; ?></b>
                                <p><?php echo $qualificationRow['uqualification_institute_name']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
