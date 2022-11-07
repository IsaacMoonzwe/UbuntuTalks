<div class="page__footer align-center">
    <div class="footer-nav">
        <ul>
            <li><a href="<?php echo CommonHelper::generateUrl('', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_HOME'); ?></a></li>
            <li><a href="<?php echo CommonHelper::generateUrl('Teachers', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?></a></li>
            <?php if (User::getDashboardActiveTab() == User::USER_LEARNER_DASHBOARD) { ?>
                <li><a href="<?php echo CommonHelper::generateUrl('TeacherRequest', '', [], CONF_WEBROOT_FRONT_URL); ?>"><?php echo Label::getLabel('LBL_APPLY_TO_TEACH'); ?></a></li>
            <?php } ?>
        </ul>
    </div>
    <p class="small">
        <?php
        if (CommonHelper::demoUrl()) {
            echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT', CommonHelper::getLangId()), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
        } else {
            echo Label::getLabel('LBL_COPYRIGHT', CommonHelper::getLangId()) . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . CommonHelper::getLangId(), FatUtility::VAR_STRING);
        }
        ?>
    </p>
</div>
</div>
</main>
<!-- ] -->
</div>
<?php if (FatApp::getConfig('CONF_ENABLE_COOKIES', FatUtility::VAR_INT, 1) && !CommonHelper::getUserCookiesEnabled()) { ?>
    <div class="cc-window cc-banner cc-type-info cc-theme-block cc-bottom cookie-alert no-print">
        <?php if (FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')) { ?>
            <div class="box-cookies">
                <span id="cookieconsent:desc" class="cc-message">
                    <?php echo FatUtility::decodeHtmlEntities(FatApp::getConfig('CONF_COOKIES_TEXT_' . $siteLangId, FatUtility::VAR_STRING, '')); ?>
                    <a href="<?php echo CommonHelper::generateUrl('cms', 'view', array(FatApp::getConfig('CONF_COOKIES_BUTTON_LINK', FatUtility::VAR_INT))); ?>"><?php echo Label::getLabel('LBL_Read_More', $siteLangId); ?></a></span>
                </span>
                <span class="cc-close cc-cookie-accept-js"><?php echo Label::getLabel('LBL_Accept_Cookies', $siteLangId); ?></span>
                <a href="javascript:void(0)" class="cc-close" onClick="getCookieConsentForm(true);"><?php echo Label::getLabel('LBL_Choose_Cookies', $siteLangId); ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<!-- Custom Loader -->
<div class="loading-wrapper" style="display: none;">
    <div class="loading">
        <div class="inner rotate-one"></div>
        <div class="inner rotate-two"></div>
        <div class="inner rotate-three"></div>
    </div>
</div>
<?php
$errorClass = '';
if (Message::getMessageCount() > 0) {
    $errorClass = " alert--success";
}
if (Message::getErrorCount() > 0) {
    $errorClass = " alert--danger";
}
if (Message::getDialogCount() > 0) {
    $errorClass = " alert--info";
}
if (Message::getInfoCount() > 0) {
    $errorClass = " alert--warning";
}
?>
<?php if (!empty($errorClass)) { ?>
    <div id="mbsmessage" class="alert--positioned-top-full alert <?php echo $errorClass; ?>">
        <div class="close" src="<?php echo CONF_WEBROOT_URL . 'img/mbsmessage/close.gif'; ?>"></div>
        <div>
            <div class="content">
                <?php echo html_entity_decode(Message::getHtml()); ?>
            </div>
        </div>
    </div>
    <script>
        $.mbsmessage.settings.initialized = true;
        $("document").ready(function () {
            if (CONF_AUTO_CLOSE_SYSTEM_MESSAGES == 1) {
                var time = CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES * 1000;
                setTimeout(function () {
                    $(document).trigger('close.mbsmessage');
                }, time);
            }
            $("#mbsmessage .close").click(function () {
                $(document).trigger('close.mbsmessage');
            });
        });
    </script>
<?php } ?>
<script>
    $(".expand-js").click(function () {
        $(".expand-target-js").slideToggle();
    });
    $(".slide-toggle-js").click(function () {
        $(".slide-target-js").slideToggle();
    });
    /******** TABS SCROLL FUNCTION  ****************/
    moveToTargetDiv('.tabs-scrollable-js li.is-active', '.tabs-scrollable-js ul');
    $('.tabs-scrollable-js li').click(function () {
        $('.tabs-scrollable-js li').removeClass('is-active');
        $(this).addClass('is-active');
        moveToTargetDiv('.tabs-scrollable-js li.is-active', '.tabs-scrollable-js ul');
    });
    function moveToTargetDiv(target, outer) {
        var out = $(outer);
        var tar = $(target);
        var x = out.width();
        var y = tar.outerWidth(true);
        var z = tar.index();
        var q = 0;
        var m = out.find('li');
        for (var i = 0; i < z; i++) {
            q += $(m[i]).outerWidth(true);
        }
        out.animate({
            scrollLeft: Math.max(0, q)
        }, 800);
        return false;
    }
    $('.list-inline li').click(function () {
        $('.list-inline li').removeClass('is-active');
        $(this).addClass('is-active');
    });
    $(document).ready(function () {
        /* SIDE BAR SCROLL DYNAMIC HEIGHT */
        $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
        $(window).resize(function () {
            $('.sidebar__body').css('height', 'calc(100% - ' + $('.sidebar__head').innerHeight() + 'px');
        });
        /* COMMON TOGGLES */
        var _body = $('html');
        var _toggle = $('.trigger-js');
        _toggle.each(function () {
            var _this = $(this),
                    _target = $(_this.attr('href'));
            _this.on('click', function (e) {
                e.preventDefault();
                _target.toggleClass('is-visible');
                _this.toggleClass('is-active');
                _body.toggleClass('is-toggle');
            });
        });
        /* FOR FULL SCREEN TOGGLE */
        var _body = $('html');
        var _toggle = $('.fullview-js');
        _toggle.each(function () {
            var _this = $(this),
                    _target = $(_this.attr('href'));
            _this.on('click', function (e) {
                e.preventDefault();
                _target.toggleClass('is-visible');
                _this.toggleClass('is-active');
                _body.toggleClass('is-fullview');
            });
        });
    });
</script>
<?php
if (FatApp::getConfig('CONF_ENABLE_LIVECHAT', FatUtility::VAR_STRING, '')) {
    echo FatApp::getConfig('CONF_LIVE_CHAT_CODE', FatUtility::VAR_STRING, '');
}
if (FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '') && !empty($cookieConsent[UserCookieConsent::COOKIE_STATISTICS_FIELD])) {
    echo FatApp::getConfig('CONF_SITE_TRACKER_CODE', FatUtility::VAR_STRING, '');
}
/* $autoRestartOn =  FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1);
  if($autoRestartOn == applicationConstants::YES && CommonHelper::demoUrl()) {
  $this->includeTemplate( 'restore-system/page-content.php');
  } */
?>
</body>
</html>