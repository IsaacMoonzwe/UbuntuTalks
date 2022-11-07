<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$activeMettingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
$isCometChatMeetingToolActive = $activeMettingTool == ApplicationConstants::MEETING_COMET_CHAT;
$isLessonSpaceMeetingToolActive = $activeMettingTool == ApplicationConstants::MEETING_LESSON_SPACE;
$isZoomMettingToolActive = $activeMettingTool == ApplicationConstants::MEETING_ZOOM;
if ($isZoomMettingToolActive) {
    ?>
    <script src="<?php echo CONF_WEBROOT_FRONTEND ?>js/zoom_tool.js"></script>
<?php } ?>
<script>
    var lDetailId = '<?php echo $lDetailId; ?>';
    var lessonId = '<?php echo $lessonId; ?>';
    var is_grpcls = '<?php echo $lessonRow['slesson_grpcls_id'] > 0 ?>';
    var isCometChatMeetingToolActive = '<?php echo $isCometChatMeetingToolActive ?>';
    var isZoomMettingToolActive = '<?php echo $isZoomMettingToolActive; ?>';
    var isLessonSpaceMeetingToolActive = '<?php echo $isLessonSpaceMeetingToolActive; ?>';
    var testTool = window.testTool;
    var isConfirmpopOpen = false;
    const ZOOM_API_KEY = '<?php echo FatApp::getConfig('CONF_ZOOM_API_KEY', FatUtility::VAR_STRING, '') ?>';
</script>
<style>
    .session-infobar .session-infobar__top {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}
.session-infobar .session-infobar__top h4 {
  color: var(--color-dark);
  line-height: 22px;
}
.session-infobar__top .color-primary {
  color: #ce4400 !important;
}
@media (min-width:1299px) {
  .session-infobar .session-infobar__top h4 {
    font-size: 1.4rem;
  }
}
.session-infobar .session-infobar__top h4 {
  color: var(--color-dark);
  line-height: 22px;
}
.session-infobar {
    padding: var(--padding-4) 0;
    position: relative;
}
.session {
    margin: 0 auto;
    position: relative;
    height: 100vh;
    max-width: 2000px;
    padding: var(--padding-3) var(--padding-10);
}
.session-infobar .session-infobar__top .profile-meta {
  margin-left: var(--margin-3);
}
.profile-meta {
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}
.avtar--xsmall {
  width: 32px;
  height: 32px;
  font-size: var(--font-size-small);
}
.avtar {
  position: relative;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  border-radius: var(--radius-1);
  width: 3.428rem;
  height: 3.428rem;
  font-size: var(--font-size-md);
  font-weight: 700;
  text-transform: uppercase;
  overflow: hidden;
  background: var(--color-primary);
}
.avtar:before {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  -webkit-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  pointer-events: none;
  content: attr(data-title);
  color: #fff;
}
.profile-meta__media {
  margin-right: var(--margin-3);
}
@media (min-width:1299px) {
  .session-infobar .session-infobar__top h4 {
    font-size: 1.4rem;
  }
}
.session-infobar .session-infobar__top h4 {
  color: var(--color-dark);
  line-height: 22px;
}
.bold-600 {
  font-weight: 600;
}
.session-infobar .session-infobar__bottom {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
}
.session-time p {
  color: var(--color-dark);
  margin-bottom: 0;
}
.session-time p span {
  font-weight: var(--font-weight-bold);
}
.session-time p {
  color: var(--color-dark);
  margin-bottom: 0;
}
@media (min-width:1399px) {
  .session-resource {
    margin-left: var(--margin-10);
  }
}
.session-resource {
  cursor: pointer;
  position: relative;
}
.session-resource::before {
  content: "";
  position: absolute;
  height: 13px;
  width: 1px;
  background: var(--color-dark);
  left: -17px;
  top: 50%;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
}
.btn.btn--addition {
  padding: 0;
  position: relative;
}
.btn.btn--small {
  height: 32px;
  line-height: 32px;
  font-size: .95rem;
  padding: 0 var(--padding-4);
}
.btn.btn--transparent {
  background: transparent !important;
}
.btn {
  background: #333;
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  padding: 0 var(--padding-6);
  position: relative;
  cursor: pointer;
  border: none;
  height: 2.8rem;
  line-height: 2.8rem;
  color: var(--color-white);
  font-weight: 500;
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
  white-space: nowrap;
  border: 1px solid transparent;
}
.color-primary {
  color: var(--color-primary) !important;
}
a {
  color: inherit;
  text-decoration: none;
  background-color: transparent;
  -webkit-text-decoration-skip: objects;
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
}
@media (min-width:991px) {
  .session-infobar .session-infobar__action {
    text-align: right;
  }
}
.btn--live {
  background: rgba(0, 0, 0, 0.08);
  color: var(--color-red);
  padding-left: var(--padding-12);
  position: relative;
  pointer-events: none;
  font-weight: bold;
}
.btn {
  background: #333;
  display: -webkit-inline-box;
  display: -ms-inline-flexbox;
  display: inline-flex;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  padding: 0 var(--padding-6);
  position: relative;
  cursor: pointer;
  border: none;
  height: 2.8rem;
  line-height: 2.8rem;
  color: var(--color-white);
  font-weight: 500;
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
  white-space: nowrap;
  border: 1px solid transparent;
}
.bg-red {
  color: #ffffff;
  background-color: var(--color-red) !important;
}
[type=reset],[type=submit], button, html[type=button] {
  -webkit-appearance: button;
}
.session-infobar .session-infobar__action button:last-child {
  margin-left: var(--margin-2);
}
.btn.btn--bordered {
  background: transparent;
  border: 1px solid currentColor;
}
.color-third {
  color: var(--color-third) !important;
}
button, select {
  text-transform: none;
}
button, input {
  overflow: visible;
}
button, input, optgroup, select, textarea {
  margin: 0;
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
  outline: 0;
  font-family: inherit;
  -webkit-transition: .3s all ease-in-out;
  -o-transition: .3s all ease-in-out;
  transition: .3s all ease-in-out;
}
button {
  border-radius: 0;
}
.session__body {
    position: relative;
    width: 100%;
    height: calc(100% - 110px);
}
.sesson-window {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center top;
    border-radius: var(--radius-2);
    overflow: hidden;
}
.sesson-window::before {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    content: "";
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
    background-color: rgba(0, 0, 0, 0.4);
}
.sesson-window .sesson-window__content {
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    text-align: center;
    width: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
}
.session-status {
    max-width: 452px;
    position: relative;
    color: #fff;
}
.btn.btn--secondary {
    background-color: var(--color-secondary);
}
.sesson-window .sesson-window__content .start-lesson-timer {
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
}
.timer {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    color: var(--color-third);
    min-height: 32px;
}
.sesson-window .sesson-window__content .start-lesson-timer h5 {
    font-size: large;
    font-weight: var(--font-weight-normal);
    margin-bottom: var(--margin-6);
    color: #fff;
    text-transform: uppercase;
}
.countdown-timer {
    font-size: var(--font-size-h3);
    padding: var(--padding-5) var(--padding-10);
    background-color: var(--color-secondary);
    color: var(--color-white);
    font-weight: var(--font-weight-medium);
    border-radius: var(--radius-1);
}
.row.align-items-center, .referral-campaign-form {
    padding-bottom: 0rem !important;
}
</style>
<!-- [ PAGE ========= -->
<!-- <main class="page"> -->
<div class="session" id="listItems" ><!--id="listItems" -->
</div>
</main>
<!-- ] -->