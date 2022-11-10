<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
if (isset($includeEditor) && true === $includeEditor) {
    $extendEditorJs = 'true';
} else {
    $extendEditorJs = 'false';
    $includeEditor = false;
}
$commonHeadData = array(
    'adminLangId' => $adminLangId,
    'jsVariables' => $jsVariables,
    'extendEditorJs' => $extendEditorJs,
    'includeEditor' => $includeEditor,
    'layoutDirection' => CommonHelper::getLayoutDirection()
);
$this->includeTemplate('_partial/header/common-head.php', $commonHeadData, false);
echo $this->writeMetaTags();
// echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
echo $this->getJsCssIncludeHtml(false);
$commonHeadHtmlData = array(
    'bodyClass' => $bodyClass,
    'includeEditor' => $includeEditor
);
$this->includeTemplate('_partial/header/common-header-html.php', $commonHeadHtmlData, false);
if (AdminAuthentication::isAdminLogged()) {
    include( '_partial/header/logged-user-header.php');
}
