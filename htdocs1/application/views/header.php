<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
/** Filter Session Destroy * */
$__controller = FatApp::getController();
$__method = FatApp::getAction();
if ($__controller != 'TeachersController' && isset($_SESSION['search_filters']))
{
    //unset($_SESSION['search_filters']);
}
/* * ******** */
/* commonHead1[ */
$commonHead1DataArr = array(
    'siteLangId' => $siteLangId,
    'jsVariables' => $jsVariables,
    'controllerName' => $controllerName,
    'canonicalUrl' => isset($canonicalUrl) ? $canonicalUrl : '',
    'currencySymbolLeft' => $currencySymbolLeft,
    'currencySymbolRight' => $currencySymbolRight,
    'layoutDirection' => CommonHelper::getLayoutDirection(),
    'cookieConsent' => $cookieConsent,
    'setMonthAndWeekName' => (isset($setMonthAndWeekName)) ? $setMonthAndWeekName : false
);
$this->includeTemplate('header/commonHead1.php', $commonHead1DataArr, false);
/* ] */
echo $this->writeMetaTags();
/* * ******** */
echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
/* commonHead2[ */
$commonHead2DataArr = array(
    'siteLangId' => $siteLangId,
    'controllerName' => $controllerName,
);
if (isset($includeEditor) && $includeEditor == true)
{
    $commonHead2DataArr['includeEditor'] = $includeEditor;
}
$htmlBodyClassesArr = array();
switch ($controllerName)
{
    case 'Blog':
        array_push($htmlBodyClassesArr, 'is--blog');
        break;
    case 'Home':
        array_push($htmlBodyClassesArr, 'is-landing');
        break;
    case 'Teach':
        array_push($htmlBodyClassesArr, 'is-landing');
        break;
    case 'TeacherRequest':
        if ($__method == 'index')
        {
            array_push($htmlBodyClassesArr, 'is-landing');
        }
        else
        {
            array_push($htmlBodyClassesArr, 'is-landing is-registration');
        }
        break;
}
$htmlBodyClassesString = implode(" ", $htmlBodyClassesArr);
$commonHead2DataArr['htmlBodyClassesString'] = $htmlBodyClassesString;
$this->includeTemplate('header/commonHead2.php', $commonHead2DataArr);
/* ] */
if (!isset($exculdeMainHeaderDiv))
{
    $this->includeTemplate('header/top.php', array('siteLangId' => $siteLangId, 'languages' => $languages), false);
}
?>

<!-- Live Chat Script -->
<!--Start of Tawk.to Script-->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {},
        Tawk_LoadStart = new Date();
    (function() {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/625660c27b967b11798a79ea/1g0go72f7';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>
<!--End of Tawk.to Script-->