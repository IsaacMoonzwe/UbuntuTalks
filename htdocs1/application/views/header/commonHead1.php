<!DOCTYPE html>
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($controllerName != 'GuestUser' && $controllerName != 'Error' && $controllerName != 'Teach')
{
    $_SESSION['referer_page_url'] = CommonHelper::getCurrUrl();
}
?>

<html prefix="og: http://ogp.me/ns#" class="<?php echo (FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1) && CommonHelper::demoUrl()) ? 'sticky-demo-header' : '' ?>" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="author" content="">
    <meta name="image" property="og:image" content="https://ubuntutalks.com/image/editor-image/new-ubuntutalks-logo.jpg">
    <meta property="og:image"  itemprop="image" content="https://ubuntutalks.com/image/editor-image/new-ubuntutalks-logo.jpg">
    <meta property="og:image" content="https://ubuntutalks.com/image/editor-image/1649412701-NelsonMandela.jpg"/>
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no, maximum-scale=1.0,user-scalable=0" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SSEQHZZC6Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-SSEQHZZC6Q');
    </script>
    <link hreflang="en" rel="shortcut icon" href="<?php echo CommonHelper::generateUrl('Image', 'favicon', [$siteLangId], CONF_WEBROOT_FRONTEND); ?>">
    <link hreflang="en" rel="apple-touch-icon" href="<?php echo CommonHelper::generateUrl('Image', 'appleTouchIcon', [$siteLangId], CONF_WEBROOT_FRONTEND); ?>">
    
    <?php if (!empty($canonicalUrl))
    {?>
        <link rel="canonical" hreflang="en" href="<?php echo str_replace('http://www.', 'https://www.', $canonicalUrl); ?>" />
    <?php } ?>
    <link hreflang="en" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php
    $jsVariables = CommonHelper::htmlEntitiesDecode($jsVariables);
    $SslUsed = (FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_BOOLEAN, false)) ? 1 : 0;
    $str = '<script type="text/javascript">
		var langLbl = ' . json_encode(
        CommonHelper::htmlEntitiesDecode($jsVariables)
    ) . ';
		var timeZoneOffset = "' . MyDate::getOffset(MyDate::getUserTimeZone()) . '";
		var CONF_AUTO_CLOSE_SYSTEM_MESSAGES = ' . FatApp::getConfig("CONF_AUTO_CLOSE_SYSTEM_MESSAGES", FatUtility::VAR_INT, 0) . ';
		var CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES = ' . FatApp::getConfig("CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES", FatUtility::VAR_INT, 3) . ';
		var layoutDirection ="' . $layoutDirection . '";
		var currencySymbolLeft = "' . $currencySymbolLeft . '";
		var currencySymbolRight = "' . $currencySymbolRight . '";
		const confWebRootUrl = "' . CONF_WEBROOT_URL . '";
		const confFrontEndUrl = "' . CONF_WEBROOT_URL . '";
		const confWebDashUrl = "' . CONF_WEBROOT_DASHBOARD . '";
		var SslUsed = ' . $SslUsed . ';
		var cookieConsent = ' . json_encode($cookieConsent) . ';
		if( CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES <= 0  ){
			CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES = 3;
		}';
    /**
     * var monthNames, weekDayNames used in the fullcalendar-luxon.min.js file  
     */
    if (isset($setMonthAndWeekName) && $setMonthAndWeekName)
    {
        $str .= ' var monthNames =  ' . json_encode(CommonHelper::htmlEntitiesDecode(MyDate::getAllMonthName(false, $siteLangId))) . ';
            var weekDayNames =  ' . json_encode(CommonHelper::htmlEntitiesDecode(MyDate::dayNames(false, $siteLangId))) . ';
            var meridiems =  ' . json_encode(CommonHelper::htmlEntitiesDecode(MyDate::meridiems(false, $siteLangId))) . ';';
    }
    $str .= '</script>' . "\r\n";
    echo $str;
