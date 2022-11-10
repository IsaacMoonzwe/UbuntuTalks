<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
</div>
<!--footer start here-->
<footer id="footer">
    <p><?php
        echo FatApp::getConfig("CONF_WEBSITE_NAME_" . $adminLangId, FatUtility::VAR_STRING, 'Copyright &copy; ' . date('Y') . ' <a href="javascript:void(0);">FATbit.com');
        echo " " . FatApp::getConfig("CONF_YOCOACH_VERSION", FatUtility::VAR_STRING, 'V1.0')
        ?> </p>
</footer>
<!--footer start here-->
</div>
<?php
$haveMsg = false;
if (Message::getMessageCount() || Message::getErrorCount()) {
    $haveMsg = true;
}
?>
<div class="alert alert--positioned " <?php
if ($haveMsg) {
    echo 'style="display:block"';
}
?>>
    <div class="close"></div>
    <div class="sysmsgcontent content ">
        <?php
        if ($haveMsg) {
            echo html_entity_decode(Message::getHtml());
        }
        ?>
    </div>
</div>
<div class="loading-wrapper" style="display: none;">
    <div class="loading">
        <div class="inner rotate-one"></div>
        <div class="inner rotate-two"></div>
        <div class="inner rotate-three"></div>
    </div>
</div>
<?php if ($haveMsg) { ?>
    <script>
        $("document").ready(function () {
            if (CONF_AUTO_CLOSE_SYSTEM_MESSAGES == 1) {
                var time = CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES * 1000;
                setTimeout(function () {
                    $.systemMessage.close();
                }, time);
            }
        });
    </script>
<?php } ?>
<!--wrapper end here-->
</body>
</html>
<?php
/* $autoRestartOn =  FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1);
if($autoRestartOn == applicationConstants::YES && CommonHelper::demoUrl()) {
    $this->includeTemplate( 'restore-system/page-content.php');
} */
