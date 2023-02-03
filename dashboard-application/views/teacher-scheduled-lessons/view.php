<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$activeMettingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
$isCometChatMeetingToolActive = $activeMettingTool == ApplicationConstants::MEETING_COMET_CHAT;
$isZoomMettingToolActive = $activeMettingTool == ApplicationConstants::MEETING_ZOOM;
$isLessonSpaceMeetingToolActive = $activeMettingTool == ApplicationConstants::MEETING_LESSON_SPACE;
if ($isZoomMettingToolActive) {
    ?>
    <script src="<?php echo CONF_WEBROOT_FRONTEND ?>js/zoom_tool.js"></script>
<?php } ?>
<script>
    lessonId = '<?php echo $lessonId; ?>';
    var canStartAlertLabel = '<?php echo Label::getLabel('LBL_Cannot_Start_The_lesson_Now!'); ?>';
    var is_grpcls = '<?php echo $lessonRow['slesson_grpcls_id'] > 0 ?>';
    const STATUS_SCHEDULED = <?php echo ScheduledLesson::STATUS_SCHEDULED; ?>;
    const STATUS_COMPLETED = <?php echo ScheduledLesson::STATUS_COMPLETED; ?>;
    const STATUS_ISSUE_REPORTED = <?php echo ScheduledLesson::STATUS_ISSUE_REPORTED; ?>;
    var isCometChatMeetingToolActive = '<?php echo $isCometChatMeetingToolActive ?>';
    var isZoomMettingToolActive = '<?php echo $isZoomMettingToolActive ?>';
    var isLessonSpaceMeetingToolActive = '<?php echo $isLessonSpaceMeetingToolActive ?>';
    var testTool = window.testTool;
    const ZOOM_API_KEY = '<?php echo FatApp::getConfig('CONF_ZOOM_API_KEY') ?>';
</script>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="session" id="listItems">
    <!--id="listItems" -->
</div>
</main>
<!-- ] -->