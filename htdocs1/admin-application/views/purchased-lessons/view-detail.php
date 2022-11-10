<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$adminTimezone = Admin::getAdminTimeZone();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_View_Lesson_Detail', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="details-title">
                    <?php echo Label::getLabel('LBL_Class_Detail'); ?>
                </div>
                <div class="tabs_panel">
                    <?php //if ($lessonRow['grpcls_title']): ?>
                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Class_Title', $adminLangId); ?>
                                    </label>
                                    : <?php echo CommonHelper::displayNotApplicable($lessonRow['grpcls_title']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Class_Description', $adminLangId); ?>
                                    </label>
                                    : <?php echo CommonHelper::displayNotApplicable($lessonRow['grpcls_description']); ?>
                                </div>
                            </div>
                        </div>
                        <?php //endif; ?>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Lesson_Duration', $adminLangId); ?>
                                    </label>
                                    : <?php echo $lessonRow['op_lesson_duration']; ?><?php echo " "; ?><?php echo Label::getLabel('LBL_mins'); ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                    

                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Language', $adminLangId); ?>
                                    </label>
                                    : <?php echo $lessonRow['teacherTeachLanguageName']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Session_Date', $adminLangId); ?>
                                    </label> :
                                    
                                        <?php echo ($lessonRow['slesson_date'] == "0000-00-00") ? Label::getLabel('LBL_N/A') : MyDate::format($lessonRow['slesson_date'] . " " . $lessonRow['slesson_start_time'], false, true, $adminTimezone); ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Scheduled_Start_Time', $adminLangId); ?>
                                    </label>
                                    :
                                        <?php echo ($lessonRow['slesson_start_time'] == "00:00:00" && $lessonRow['slesson_date'] == "0000-00-00") ? Label::getLabel('LBL_N/A') : MyDate::convertTimeFromSystemToUserTimezone('H:i:s A', $lessonRow['slesson_date'] . " " . $lessonRow['slesson_start_time'], true, $adminTimezone); ?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Session_End_Time', $adminLangId); ?>
                                    </label> :
                                        <?php echo ($lessonRow['slesson_end_time'] == "00:00:00" && $lessonRow['slesson_date'] == "0000-00-00") ? Label::getLabel('LBL_N/A') : MyDate::convertTimeFromSystemToUserTimezone('H:i:s A', $lessonRow['slesson_date'] . " " . $lessonRow['slesson_end_time'], true, $adminTimezone); ?>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Lesson_Status', $adminLangId); ?>
                                    </label>
                                    : <?php echo $statusArr[$lessonRow['slesson_status']]; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Free_Trail', $adminLangId); ?>
                                    </label>
                                    :<?php 
                                        if($lessonRow['is_trial']=='0'){
                                             echo " Not Enable"; 
                                        }
                                        else
                                        {
                                            echo " Enable"; 
                                        }
                                        
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="instructor-details">
                        <?php echo Label::getLabel('LBL_Instuctor_&_Learner_Detail'); ?>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Learner_Name', $adminLangId); ?>
                                    </label>
                                    : <?php echo $lessonRow['learnerFullName']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Instructor_Name', $adminLangId); ?>
                                    </label>
                                    : <?php echo $lessonRow['instructorFullName']; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
