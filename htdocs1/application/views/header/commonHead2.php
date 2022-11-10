<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (isset($includeEditor) && $includeEditor)
{
?>
    <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
    <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js"></script>
<?php } ?>
<?php if (FatApp::getConfig('CONF_ENABLE_PWA', FatUtility::VAR_BOOLEAN, false))
{ ?>
    <link rel="manifest" href="<?php echo CommonHelper::generateUrl('MyApp', 'PwaManifest'); ?>">
    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("<?php echo CONF_WEBROOT_FRONTEND; ?>sw.js");
        }
    </script>
<?php } ?>
<meta property="og:title" content="Language Learning Online Courses | Ubuntu Talks">
<meta property="og:site_name" content="Language Learning Online Courses | Ubuntu Talks">
<meta property="og:url" content="https://ubuntutalks.com/">
<meta property="og:description" content="Ubuntu Talks is the best online platform to learn sub-Saharan African languages. We have customized courses for all ages and language learning levels.">
<meta property="og:type" content="book">
<meta property="og:image" content="https://ubuntutalks.com/image/editor-image/Android-Image.png">
<meta name="facebook-domain-verification" content="obzyd9slo15h4yxrhy917g1d49dfck" />
<link rel="canonical" href="https://ubuntutalks.com/" />
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '570002834475953');
fbq('track', 'PageView');
fbq('track', 'kids');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=570002834475953&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->



</head>
<?php
$layoutDirection = CommonHelper::getLayoutDirection();
?>
<body class="<?php echo $htmlBodyClassesString; ?>" <?php echo (strtolower($layoutDirection) == 'rtl') ? 'dir="rtl"' : ""; ?>>

    <?php
    $autoRestartOn = FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1);
    if ($autoRestartOn == applicationConstants::YES && CommonHelper::demoUrl())
    {
        $this->includeTemplate('restore-system/header-bar.php');
    }
