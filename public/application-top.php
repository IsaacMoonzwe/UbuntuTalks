<?php

ob_end_clean();
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
{
    ob_start("ob_gzhandler");
}
else
{
    ob_start();
}

ini_set('display_errors', CONF_DEVELOPMENT_MODE);
error_reporting(CONF_DEVELOPMENT_MODE ? E_ALL : E_ALL & ~E_NOTICE & ~E_WARNING);
require_once CONF_INSTALLATION_PATH . 'library/autoloader.php';
require_once CONF_INSTALLATION_PATH . 'vendor/autoload.php';
AttachedFile::registerS3ClientStream();
/* --- Redirect SSL --- */
$protocol = FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_INT, false) ? 'https://' : 'http://';

/* AWS */
if ((($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') != 'https') && (FatApp::getConfig('CONF_USE_SSL') == 1))
{
    // if ((($_SERVER['HTTPS'] ?? '') != 'on') && (FatApp::getConfig('CONF_USE_SSL') == 1)) {
    $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //FatApp::redirectUser($redirect);
}
/* --- Redirect SSL --- */
/* We must set it before initiating db connection. So that connection timezone is in sync with php */
$timezone = FatApp::getConfig('CONF_TIMEZONE', FatUtility::VAR_STRING, 'America/New_York');
date_default_timezone_set($timezone);
$timezonOffset = date('P');
$query = "SET NAMES utf8mb4; SET time_zone = '" . $timezonOffset . "';";
$dbCon = FatApp::getDb()->getConnectionObject();

if ($dbCon->multi_query($query))
{
    while ($dbCon->next_result())
    {
        if (!$dbCon->more_results())
            break;
    }
}
CommonHelper::setSeesionCookieParams();
session_start();
$_SESSION['WYSIWYGFileManagerRequirements'] = CONF_INSTALLATION_PATH . 'public/WYSIWYGFileManagerRequirements.php';
define('SYSTEM_INIT', true);
