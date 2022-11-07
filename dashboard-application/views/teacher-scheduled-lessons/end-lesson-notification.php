<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box -padding-20 -align-center">
    <h4><?php echo Label::getLabel('LBL_Duration_assigned_to_this_lesson_is_completed_now_do_you_want_to_continue?'); ?></h4>
    <button class="btn btn--secondary" onclick="endLessonSetup('<?php echo $lesonId; ?>');$.facebox.close();"><?php echo Label::getLabel('LBL_End_Lesson'); ?></button>
    <button class="btn btn--primary" onclick="sessionStorage.setItem('showEndLessonNotification', 0);$.facebox.close();"><?php echo Label::getLabel('LBL_Continue'); ?></button>
</div>