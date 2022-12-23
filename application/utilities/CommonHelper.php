<?php

class CommonHelper extends FatUtility
{

    private static $_ip;
    private static $_user_agent;
    private static $_lang_id;
    private static $_layout_direction;
    private static $_currency_id;
    private static $_currency_symbol_left;
    private static $_currency_symbol_right;
    private static $_currency_code;
    private static $_currency_value;
    private static $_system_currency_id;
    private static $_system_currency_data;

    public static function initCommonVariables($isAdmin = false)
    {
        self::$_ip = self::getClientIp();
        self::$_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        self::$_lang_id = FatApp::getConfig('CONF_DEFAULT_SITE_LANG', FatUtility::VAR_INT, 1);
        self::$_currency_id = FatApp::getConfig('CONF_CURRENCY', FatUtility::VAR_INT, 1);
        if (false === $isAdmin) {
            self::setSiteDefaultLang();
            if (isset($_COOKIE['defaultSiteCurrency'])) {
                $currencies = Currency::getCurrencyAssoc(self::$_lang_id);
                if (array_key_exists($_COOKIE['defaultSiteCurrency'], $currencies)) {
                    self::$_currency_id = FatUtility::int(trim($_COOKIE['defaultSiteCurrency']));
                }
            }
        }
        if (true === $isAdmin) {
            if (isset($_COOKIE['defaultAdminSiteLang'])) {
                $languages = Language::getAllNames();
                if (array_key_exists($_COOKIE['defaultAdminSiteLang'], $languages)) {
                    self::$_lang_id = FatUtility::int(trim($_COOKIE['defaultAdminSiteLang']));
                }
            } else {
                self::$_lang_id = FatApp::getConfig('CONF_ADMIN_DEFAULT_LANG', FatUtility::VAR_INT, 1);
            }
        }
        $currencyData = Currency::getAttributesById(self::$_currency_id, ['currency_code', 'currency_symbol_left', 'currency_symbol_right', 'currency_value']);
        self::$_currency_symbol_left = $currencyData['currency_symbol_left'];
        self::$_currency_symbol_right = $currencyData['currency_symbol_right'];
        self::$_currency_code = $currencyData['currency_code'];
        self::$_currency_value = $currencyData['currency_value'];
        self::$_layout_direction = Language::getLayoutDirection(self::$_lang_id);
        self::$_system_currency_data = Currency::getSystemCurrencyData();
        if (!empty(self::$_system_currency_data)) {
            self::$_system_currency_id = self::$_system_currency_data['currency_id'];
        }
    }

    public static function setSiteDefaultLang()
    {
        $specificUrlConf = FatApp::getConfig('CONF_LANG_SPECIFIC_URL', FatUtility::VAR_INT, 0);
        self::$_lang_id = FatApp::getConfig('CONF_DEFAULT_SITE_LANG', FatUtility::VAR_INT, 1);
        if (!defined('SYSTEM_LANG_ID') && isset($_COOKIE['defaultSiteLang'])) {
            if (array_key_exists($_COOKIE['defaultSiteLang'], LANG_CODES_ARR)) {
                self::$_lang_id = FatUtility::int($_COOKIE['defaultSiteLang']);
                return self::$_lang_id;
            }
        }
        if (defined('SYSTEM_LANG_ID') && SYSTEM_LANG_ID > 0) {
            self::$_lang_id = SYSTEM_LANG_ID;
            self::setDefaultSiteLangCookie(self::$_lang_id);
            return self::$_lang_id;
        }
        $headerLang = strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        $langId = array_search($headerLang, LANG_CODES_ARR);
        if (false !== $langId) {
            self::$_lang_id = FatUtility::int($langId);
        }
        if (UserAuthentication::isUserLogged()) {
            $userSetting = new UserSetting(UserAuthentication::getLoggedUserId(true));
            $userSiteLangData = $userSetting->getUserSiteLang();
            if (!empty($userSiteLangData['language_id'])) {
                self::$_lang_id = FatUtility::int($userSiteLangData['language_id']);
            }
        }
        self::setDefaultSiteLangCookie(self::$_lang_id);
        return self::$_lang_id;
    }

    public static function setDefaultSiteLangCookie(int $langId)
    {
        $cookieConsent = CommonHelper::getCookieConsent();
        $isActivePreferencesCookie = (!empty($cookieConsent[UserCookieConsent::COOKIE_PREFERENCES_FIELD]));
        if (!$isActivePreferencesCookie) {
            return true;
        }
        self::setCookie('defaultSiteLang', $langId, UserCookieConsent::getCookieExpireTime(), CONF_WEBROOT_FRONTEND, '', true);
    }

    public static function getLangId()
    {
        return self::$_lang_id;
    }

    public static function getLayoutDirection()
    {
        return self::$_layout_direction;
    }

    public static function getSystemCurrencyId()
    {
        return self::$_system_currency_id;
    }

    public static function getSystemCurrencyData(): array
    {
        return self::$_system_currency_data;
    }

    public static function getSystemCurrencySymbol(): string
    {
        return ('' != self::$_system_currency_data['currency_symbol_left']) ? self::$_system_currency_data['currency_symbol_left'] : self::$_system_currency_data['currency_symbol_right'];
    }

    public static function getSystemCurCode(): string
    {
        return static::$_system_currency_data['currency_code'];
    }

    public static function getCurrencyId()
    {
        return self::$_currency_id;
    }

    public static function getCurrencySymbolLeft()
    {
        return self::$_currency_symbol_left;
    }

    public static function getCurrencySymbolRight()
    {
        return self::$_currency_symbol_right;
    }

    public static function getCurrencyCode()
    {
        return self::$_currency_code;
    }

    public static function getCurrencyValue()
    {
        return self::$_currency_value;
    }

    public static function userIp()
    {
        return self::$_ip;
    }

    public static function userAgent()
    {
        return self::$_user_agent;
    }

    public static function getClientIp()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    public static function generateUrl($controller = '', $action = '', $queryData = [], $use_root_url = '', $url_rewriting = null, $encodeUrl = false, $useLangCode = true): string
    {
        $langId = self::$_lang_id;
        if (empty($use_root_url)) {
            $use_root_url = CONF_WEBROOT_URL;
        }
        if ($useLangCode == true && FatApp::getConfig('CONF_LANG_SPECIFIC_URL') && count(LANG_CODES_ARR) > 1 && $langId != FatApp::getConfig('CONF_DEFAULT_SITE_LANG')) {
            $use_root_url = '/' . strtolower(LANG_CODES_ARR[$langId]) . '/' . ltrim($use_root_url, '/');
        }
        $url = FatUtility::generateUrl($controller, $action, $queryData, $use_root_url, $url_rewriting);
        if (UrlHelper::isStaticContentProvider($controller, $action)) {
            return $url;
        }
        if (!$use_root_url) {
            $use_root_url = CONF_WEBROOT_URL;
        }
        $originalUrl = strtolower(FatUtility::camel2dashed($controller)) . '/' . strtolower(FatUtility::camel2dashed($action));
        $urlWithParma = $originalUrl;
        if (!empty($queryData)) {
            $originalUrl = $originalUrl . '/' . rtrim(implode('/', $queryData));
            $replaceArray = array_fill(0, count($queryData), 'urlparameter');
            $urlWithParma = $urlWithParma . '/' . rtrim(implode('/', $replaceArray), '/');
        }
        $srch = new UrlRewriteSearch();
        $srch->doNotCalculateRecords();
        $srch->setPagesize(1);
        $cond = $srch->addCondition('urlrewrite_original', 'LIKE', $originalUrl);
        $cond->attachCondition('urlrewrite_original', 'LIKE', $urlWithParma);
        $srch->addCondition('urlrewrite_lang_id', '=', $langId);
        $rs = $srch->getResultSet();
        if ($row = FatApp::getDb()->fetch($rs)) {
            if (false !== strpos($row['urlrewrite_custom'], 'urlparameter')) {
                $row['urlrewrite_custom'] = implode('/', array_slice(explode('/', $row['urlrewrite_custom']), 0, 2)) . '/' . implode('/', $queryData);
            }
            if ($encodeUrl) {
                $url = $use_root_url . urlencode($row['urlrewrite_custom']);
            } else {
                $url = $use_root_url . $row['urlrewrite_custom'];
            }
        }
        return $url;
    }

    public static function generateFullUrl($controller = '', $action = '', $queryData = [], $use_root_url = '', $url_rewriting = null, $encodeUrl = false, $useLangCode = true)
    {
        $url = static::generateUrl($controller, $action, $queryData, $use_root_url, $url_rewriting, $encodeUrl, $useLangCode);
        $protocol = (1 == FatApp::getConfig('CONF_USE_SSL')) ? 'https://' : 'http://';
        if ($encodeUrl) {
            $url = urlencode($url);
        }
        return $protocol . $_SERVER['SERVER_NAME'] .":444". $url;
    }

    public static function getRootUrl()
    {
        $protocol = (1 == FatApp::getConfig('CONF_USE_SSL')) ? 'https://' : 'http://';
        return $protocol . $_SERVER['SERVER_NAME'];
    }

    public static function generateNoAuthUrl($model = '', $action = '', $queryData = [], $use_root_url = '')
    {
        $url = CommonHelper::generateUrl($model, $action, $queryData, $use_root_url, false);
        $url = str_replace('index.php?', 'index_noauth.php?', $url);
        $protocol = (1 == FatApp::getConfig('CONF_USE_SSL')) ? 'https://' : 'http://';
        return $protocol . $_SERVER['SERVER_NAME'] . $url;
    }

    public static function underMyDevelopment($sessionId = false)
    {
        if ($sessionId && $sessionId != session_id()) {
            return false;
        }
        return true;
    }

    public static function printArray($attr, $exit = false, $sessionId = false)
    {
        if ($sessionId && $sessionId != session_id()) {
            return;
        }
        echo 'IN PRINT Function: <pre>';
        print_r($attr);
        echo '</pre>';
        if ($exit) {
            exit;
        }
    }

    public static function combinationOfElementsOfArr($arr = [], $useKey = '')
    {
        $tempArr = [];
        $loopCount = count($arr);
        for ($i = 0; $i < $loopCount; ++$i) {
            $count = 0;
            foreach ($arr as $key => $val) {
                if ($count != $i) {
                    continue;
                }
                asort($val[$useKey]);
                if (!empty($tempArr)) {
                    foreach ($tempArr as $tempKey => $tempVal) {
                        foreach ($val[$useKey] as $k => $v) {
                            $tempArr[$tempKey . '|' . $k] = $tempVal . '|' . $v;
                            unset($tempArr[$tempKey]);
                        }
                    }
                } else {
                    foreach ($val[$useKey] as $k => $v) {
                        $tempArr[$k] = $v;
                    }
                }
            }
            ++$count;
        }
        return $tempArr;
    }

    public static function renderHtml($content = '', $stripJs = false)
    {
        $str = html_entity_decode($content);
        $str = (true == $stripJs) ? static::strip_javascript($str) : $str;
        return $str;
    }

    public static function displayTaxFormat($isPercent, $val, $position = 'R')
    {
        if (!$isPercent) {
            return self::displayMoneyFormat($val);
        }
        if ('L' == $position) {
            return '% ' . $val;
        }
        return $val . ' %';
    }

    public static function getDefaultCurrencyValue($val, $format = true, $displaySymbol = true)
    {
        $currencyValue = self::getCurrencyValue();
        $defaultCurrencyValue = $val / $currencyValue;
        return static::displayMoneyFormat($defaultCurrencyValue, $format, true, $displaySymbol);
    }

    public static function getCurrencySymbol($showDefaultSiteCurrenySymbol = false)
    {
        if ($showDefaultSiteCurrenySymbol) {
            $currencyData = CommonHelper::getSystemCurrencyData();
            $currencySymbolLeft = $currencyData['currency_symbol_left'];
            $currencySymbolRight = $currencyData['currency_symbol_right'];
        } else {
            $currencySymbolLeft = self::getCurrencySymbolLeft();
            $currencySymbolRight = self::getCurrencySymbolRight();
        }
        return $currencySymbolLeft . $currencySymbolRight;
    }

    public static function numberStringFormat($number)
    {
        $prefixes = 'KMGTPEZY';
        if ($number >= 1000) {
            for ($i = -1; $number >= 1000; ++$i) {
                $number = $number / 1000;
            }
            return floor($number) . $prefixes[$i];
        }
        return $number;
    }

    public static function convertExistingToOtherCurrency($currCurrencyId, $val, $otherCurrencyId, $numberFormat = true)
    {
        $currencyData = Currency::getAttributesById($currCurrencyId, ['currency_value']);
        $currencyValue = $currencyData['currency_value'];
        $val = $val / $currencyValue;
        $currencyData = Currency::getAttributesById($otherCurrencyId, ['currency_value']);
        $currencyValue = $currencyData['currency_value'];
        $val = $val * $currencyValue;
        if ($numberFormat) {
            $val = number_format($val, 2);
        }
        return $val;
    }

    public static function displayMoneyFormat($val, $numberFormat = true, $showInConfiguredDefaultCurrency = false, $displaySymbol = true, $stringFormat = false, $cunvertValue = true)
    {
        $currencyValue = self::getCurrencyValue();
        $currencySymbolLeft = self::getCurrencySymbolLeft();
        $currencySymbolRight = self::getCurrencySymbolRight();
        if ($showInConfiguredDefaultCurrency) {
            $currencyData = CommonHelper::getSystemCurrencyData();
            $currencyValue = $currencyData['currency_value'];
            $currencySymbolLeft = $currencyData['currency_symbol_left'];
            $currencySymbolRight = $currencyData['currency_symbol_right'];
        }
        if ($cunvertValue) {
            $val = $val * $currencyValue;
        }
        $sign = '';
        if ($val < 0) {
            $val = abs($val);
            $sign = '-';
        }
        if ($numberFormat && !$stringFormat) {
            $val = number_format($val, 2);
        }
        if ($stringFormat) {
            $val = static::numberStringFormat($val);
        }
        if ($displaySymbol) {
            $sign .= ' ';
            $val = $sign . $currencySymbolLeft . $val . $currencySymbolRight;
        } else {
            $val = $sign . $val;
        }
        return $val;
    }

    public static function displayNotApplicable($val = '', $str = '-NA-')
    {
        $str = ('' == $str) ? Label::getLabel('LBL_-NA-') : $str;
        return '' != $val ? $val : $str;
    }

    public static function editorSvg($path)
    {
        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Content-type: image/svg+xml');
            header('Cache-Control: public, must-revalidate');
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            header('Expires: ' . date('D, d M Y H:i:s', strtotime('+30 days')));
            exit;
        }
        header('Content-type: image/svg+xml');
        header('Pragma: public');
        header('Cache-Control: public, must-revalidate');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);
        header('Expires: ' . date('D, d M Y H:i:s', strtotime('+30 days')));
        readfile($path);
    }

    public static function convertToCsv($input_array, $output_file_name, $delimiter)
    {
        /** open raw memory as file, no need for temp files */
        $temp_memory = fopen('php://memory', 'w');
        // loop through array
        foreach ($input_array as $key => $line) {
            fputcsv($temp_memory, $line, $delimiter);
        }
        // rewrind the "file" with the csv lines
        fseek($temp_memory, 0);
        // modify header to be downloadable csv file
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-type: application/csv; charset=UTF-8; encoding=UTF-8');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        fpassthru($temp_memory);
    }

    public static function getPercentValue(float $percentage, float $total): float
    {
        if (!$total) {
            return 0;
        }
        if (0 > $percentage) {
            return $total;
        }
        $percentage = ($percentage / 100) * $total;
        return number_format($percentage, 2);
    }

    public static function verifyCaptcha($fld_name = 'g-recaptcha-response')
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/ReCaptcha/src/autoload.php';
        if (!empty(FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '')) && !empty(FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, ''))) {
            $recaptcha = new \ReCaptcha\ReCaptcha(FatApp::getConfig('CONF_RECAPTCHA_SECRETKEY', FatUtility::VAR_STRING, ''));
            $post = FatApp::getPostedData();
            if (isset($post[$fld_name])) {
                $resp = $recaptcha->verify($post[$fld_name], $_SERVER['REMOTE_ADDR']);
                return true == $resp->isSuccess() ? true : false;
            }
            return false;
        }
        return true;
    }

    public static function strip_javascript($content = '')
    {
        $javascript = '/<script[^>]*?>.*?<\/script>/si';
        $noscript = '';
        return preg_replace($javascript, $noscript, $content);
    }

    public static function addhttp($url)
    {
        return $url;
    }

    public static function escapeStringAndAddQuote($string)
    {
        $db = FatApp::getDb();
        if (method_exists($db, 'quoteVariable')) {
            return $db->quoteVariable($string);
        }
        return "'" . mysql_real_escape_string($string) . "'";
    }

    public static function setAppUser()
    {
        $_SESSION['app_user'] = true;
    }

    public static function isAppUser()
    {
        if (isset($_SESSION['app_user'])) {
            return true;
        }
        return false;
    }

    public static function escapeString($string)
    {
        return trim(self::escapeStringAndAddQuote($string), "'");
    }

    public static function parseYouTubeurl($url)
    {
        $pattern = '#^(?:https?://)?'; // Optional URL scheme. Either http or https.
        $pattern .= '(?:www\.)?'; // Optional www subdomain.
        $pattern .= '(?:'; // Group host alternatives:
        $pattern .= 'youtu\.be/'; // Either youtu.be,
        $pattern .= '|youtube\.com'; // or youtube.com
        $pattern .= '(?:'; // Group path alternatives:
        $pattern .= '/embed/'; // Either /embed/,
        $pattern .= '|/v/'; // or /v/,
        $pattern .= '|/watch\?v='; // or /watch?v=,
        $pattern .= '|/watch\?.+&v='; // or /watch?other_param&v=
        $pattern .= ')'; // End path alternatives.
        $pattern .= ')'; // End host alternatives.
        $pattern .= '([\w-]{11})'; // 11 characters (Length of Youtube video ids).
        $pattern .= '(?:.+)?$#x'; // Optional other ending URL parameters.
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
    }

    public static function getCurrUrl()
    {
        return self::getUrlScheme() . $_SERVER['REQUEST_URI'];
    }

    public static function getnavigationUrl($type, $nav_url = '', $nav_cpage_id = 0, $nav_category_id = 0)
    {
        if (NavigationLinks::NAVLINK_TYPE_CMS == $type) {
            $url = CommonHelper::generateUrl('cms', 'view', [$nav_cpage_id], CONF_WEBROOT_FRONTEND);
        } elseif (NavigationLinks::NAVLINK_TYPE_EXTERNAL_PAGE == $type) {
            $use_root_url = CONF_WEBROOT_FRONTEND;
            if (FatApp::getConfig('CONF_LANG_SPECIFIC_URL') && count(LANG_CODES_ARR) > 1 && self::$_lang_id != FatApp::getConfig('CONF_DEFAULT_SITE_LANG')) {
                $use_root_url = $use_root_url . strtolower(LANG_CODES_ARR[self::$_lang_id]) . '/';
            }
            $url = str_replace(['{SITEROOT}', '{siteroot}'], [$use_root_url, $use_root_url], $nav_url);
            $url = CommonHelper::processURLString($url);
        } elseif (NavigationLinks::NAVLINK_TYPE_CATEGORY_PAGE == $type) {
            $url = CommonHelper::generateUrl('category', 'view', [$nav_category_id], CONF_WEBROOT_FRONTEND);
        }
        return $url;
    }

    public static function getUrlScheme()
    {
        $pageURL = 'http';
        if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
            $pageURL .= 's';
        }
        $pageURL .= '://';
        if ('80' != $_SERVER['SERVER_PORT']) {
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        } else {
            $pageURL .= $_SERVER['SERVER_NAME'];
        }
        return $pageURL;
    }

    public static function redirectUserReferer($returnUrl = false)
    {
        if (!defined('REFERER')) {
            if (self::getCurrUrl() == $_SERVER['HTTP_REFERER'] || empty($_SERVER['HTTP_REFERER'])) {
                define('REFERER', CommonHelper::generateUrl('/'));
            } else {
                define('REFERER', $_SERVER['HTTP_REFERER']);
            }
        }
        if ($returnUrl) {
            return REFERER;
        }
        FatApp::redirectUser(REFERER);
    }

    public static function renderJsonError($tpl, $msg)
    {
        $tpl->set('msg', $msg);
        $tpl->render(false, false, 'json-error.php', false, false);
    }

    public static function renderJsonSuccess($tpl, $msg)
    {
        $tpl->set('msg', $msg);
        $tpl->render(false, false, 'json-success.php', false, false);
    }

    public static function checkMsgs()
    {
        $msgs_result['has_msgs'] = false;
        $msgs_result['msgs_html'] = '';
        if (Message::getErrorCount() > 0 || Message::getMessageCount() > 0) {
            $msgs_result['has_msgs'] = true;
            $msgs_result['msgs_html'] = Message::getHtml();
        }
        return $msgs_result;
    }

    public static function getRandomPassword($n)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = '';
        for ($i = 0; $i < $n; ++$i) {
            $pass .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return $pass;
    }

    public static function getAdminUrl($controller = '', $action = '', $queryData = [], $use_root_url = '/admin/', $url_rewriting = null)
    {
        return FatUtility::generateFullUrl($controller, $action, $queryData, $use_root_url, $url_rewriting);
    }

    public static function currentDateTime($dateFormat = null, $dateTime = false, $timeFormat = null, $timeZone = null)
    {
        if (null == $timeZone) {
            $timeZone = FatApp::getConfig('CONF_TIMEZONE', FatUtility::VAR_STRING, date_default_timezone_get());
        }
        if (null == $dateFormat) {
            $dateFormat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
        }
        if ($dateTime) {
            if (null == $timeFormat) {
                $timeFormat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
            }
        }
        $format = $dateFormat . ' ' . $timeFormat;
        return FatDate::nowInTimezone($timeZone, trim($format));
    }

    public static function validatePassword($string = '')
    {
        if (strlen($string) < 1) {
            return false;
        }
        if (!preg_match('/' . applicationConstants::PASSWORD_REGEX . '/', $string)) {
            return false;
        }
        return true;
    }

    public static function validateUsername($string = '')
    {
        if (strlen($string) < 3) {
            return false;
        }
        if (!preg_match('/^[a-zA-Z0-9]{3,30}$/', $string)) {
            return false;
        }
        return true;
    }

    public static function getLangFields($condition_id = 0, $condition_field = '', $condition_lang_field = '', $lang_flds = [], $lang_table = '')
    {
        $condition_id = FatUtility::int($condition_id);
        if (0 == $condition_id || '' == $condition_field || '' == $condition_lang_field || '' == $lang_table || empty($lang_flds)) {
            return [];
        }
        $langs = Language::getAllNames();
        $array = [];
        $srch = new SearchBase($lang_table);
        $srch->addCondition($condition_field, '=', $condition_id);
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetchAll($rs);
        foreach ($langs as $langId => $lang) {
            foreach ($record as $rec) {
                if ($rec[$condition_lang_field] == $langId) {
                    foreach ($lang_flds as $fld) {
                        $array[$fld][$langId] = $rec[$fld];
                        $array[$fld . $langId] = $rec[$fld];
                    }
                    continue;
                }
            }
        }
        return $array;
    }

    public static function arrayToAssocArray($arr)
    {
        $arr_url_params = [];
        if (!empty($arr)) {
            foreach ($arr as $key => $val) {
                $v = 0;
                if (0 == $key % 2) {
                    $k = $val;
                } else {
                    $v = $val;
                }
                $arr_url_params[$k] = $v;
            }
        }
        return $arr_url_params;
    }

    public static function crop($data, $src)
    {
        if (empty($data)) {
            return;
        }
        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height
        $src_img_w = $size_w;
        $src_img_h = $size_h;
        $degrees = $data->rotate;
        switch ($size['mime']) {
            case 'image/gif':
                $src_img = imagecreatefromgif($src);
                break;
            case 'image/jpeg':
                $src_img = imagecreatefromjpeg($src);
                break;
            case 'image/png':
                $src_img = imagecreatefrompng($src);
                break;
        }
        // Rotate the source image
        if (is_numeric($degrees) && 0 != $degrees) {
            // PHP's degrees is opposite to CSS's degrees
            $new_img = imagerotate($src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127));
            imagedestroy($src_img);
            $src_img = $new_img;
            $deg = abs($degrees) % 180;
            $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;
            $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
            $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);
            // Fix rotated image miss 1px issue when degrees < 0
            --$src_img_w;
            --$src_img_h;
        }
        $tmp_img_w = $data->width;
        $tmp_img_h = $data->height;
        $dst_img_w = 320;
        $dst_img_h = 320;
        $src_x = $data->x;
        $src_y = $data->y;
        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } elseif ($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } elseif ($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }
        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } elseif ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } elseif ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }
        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;
        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);
        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);
        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        if ($result) {
            if (!imagepng($dst_img, $src)) {
                return Label::getLabel('MSG_Failed_to_save_cropped_file');
            }
        } else {
            return Label::getLabel('MSG_Failed_to_crop_file');
        }
        imagedestroy($src_img);
        imagedestroy($dst_img);
    }

    public static function getCorrectImageOrientation($filename)
    {
        $deg = 0;
        if (function_exists('exif_read_data') && (('image/jpeg' == mime_content_type($filename)) || ('image/tiff' == mime_content_type($filename)))) {
            $exif = exif_read_data($filename);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if (1 != $orientation) {
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                }
            }
        }
        return $deg;
    }

    public static function isRenderTemplateExist($template = '')
    {
        $instance = FatApplication::getInstance();
        if ('' == $template) {
            $themeDirName = FatUtility::camel2dashed(substr($instance->getController(), 0, - (strlen('controller'))));
            $actionName = FatUtility::camel2dashed($instance->getAction()) . '.php';
            $template = $themeDirName . '/' . $actionName;
        }
        if (file_exists(CONF_THEME_PATH . $template)) {
            return true;
        }
        return false;
    }

    public static function subStringByWords($str, $maxlength)
    {
        if (strlen($str) < $maxlength) {
            return $str;
        }
        $str = substr($str, 0, $maxlength);
        $rpos = strrpos($str, ' ');
        if ($rpos > 0) {
            $str = substr($str, 0, $rpos);
        }
        return $str;
    }

    public static function getWeightInGrams($unit, $val)
    {
        $unit = FatUtility::int($unit);
        switch ($unit) {
            case applicationConstants::WEIGHT_GRAM:
                $weight = $val;
                break;
            case applicationConstants::WEIGHT_POUND:
                $weight = $val * 453.592;
                break;
            case applicationConstants::WEIGHT_KILOGRAM:
                $weight = $val * 0.001;
                break;
            default:
                trigger_error('Invalid Argument', E_USER_ERROR);
        }
        return $weight;
    }

    public static function getLengthInCentimeter($val, $unit)
    {
        $unit = FatUtility::int($unit);
        switch ($unit) {
            case applicationConstants::LENGTH_CENTIMETER:
                $length = $val;
                break;
            case applicationConstants::LENGTH_METER:
                $length = $val * 100;
                break;
            case applicationConstants::LENGTH_INCH:
                $length = $val * 2.54;
                break;
            default:
                trigger_error('Invalid Argument', E_USER_ERROR);
        }
        return $length;
    }

    public static function getVolumeInCC($unit, $val)
    {
        $unit = FatUtility::int($unit);
        return $val;
    }

    public static function is_multidim_array($arr)
    {
        if (!is_array($arr)) {
            return false;
        }
        foreach ($arr as $elm) {
            if (!is_array($elm)) {
                return false;
            }
        }
        return true;
    }

    public static function processURLString($urlString)
    {
        $strtestpos = strpos(' ' . $urlString, '.');
        if (!$strtestpos) {
            return $urlString;
        }
        $urlString = trim($urlString);
        if ($urlString) {
            $my_bool = false;
            if ('https' == substr($urlString, 0, 5)) {
                $my_bool = true;
            }
            $urlString = preg_replace('/https?:\/\//', '', $urlString);
            $urlString = trim($urlString);
            $pre_str = 'http://';
            if ($my_bool) {
                $pre_str = 'https://';
            }
            $urlString = $pre_str . $urlString;
        }
        return $urlString;
    }

    public static function currencyDisclaimer($langId, $amount = 0)
    {
        $str = Label::getLabel('LBL_*_Note_:_charged_in_currency_disclaimer_{default-currency-symbol}', $langId);
        if ($amount) {
            $str = str_replace('{default-currency-symbol}', static::displayMoneyFormat($amount, true, true), $str);
        } else {
            $str = str_replace('{default-currency-symbol}', ' $ ', $str);
        }
        return $str;
    }

    public static function truncateCharacters($string, $limit, $break = ' ', $pad = '...', $nl2br = false)
    {
        if (strlen($string) <= $limit) {
            return ($nl2br) ? nl2br($string) : $string;
        }
        $tempString = str_replace('\n', '^', $string);
        $tempString = substr($tempString, 0, $limit);
        if ('^' == substr($tempString, -1)) {
            $limit = $limit - 1;
        }
        $string = substr($string, 0, $limit);
        if (false !== ($breakpoint = strrpos($string, $break))) {
            $string = substr($string, 0, $breakpoint);
        }
        return (($nl2br) ? nl2br($string) : $string) . $pad;
    }

    public static function displayName($string)
    {
        if (!empty($string)) {
            return ucfirst($string);
        }
    }

    public static function getFirstChar($string, $capitalize = false)
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        if (!empty($string)) {
            if (true == $capitalize) {
                return strtoupper($string[0]);
            }
            return $string[0];
        }
    }

    public static function seoUrl($string, $replace = "/[\s,<>\/\"&#%+?$@=]/")
    {
        $string = trim($string);
        $string = preg_replace($replace, "-", $string);
        $string = preg_replace('/[\\s-]+/', '-', $string);
        $string = preg_replace("/[\-]+/", "-", $string);
        // $keyword = strtolower($string);
        if (file_exists(CONF_INSTALLATION_PATH . 'application/controllers/' . strtolower($string) . 'Controller' . '.php')) {
            return $string . '-' . rand(1, 100);
        }
        return trim($string, '-');
    }

    public static function recursiveDelete($str)
    {
        if (is_file($str)) {
            return @unlink($str);
        }
        if (is_dir($str)) {
            $scan = glob(rtrim($str, '/') . '/*');
            foreach ($scan as $index => $path) {
                static::recursiveDelete($path);
            }
            return @rmdir($str);
        }
    }

    public static function displayText($value = '')
    {
        return empty(trim($value)) ? '-' : $value;
    }

    public static function getPlaceholderForAmtField()
    {
        return self::$_system_currency_data['currency_symbol_left'] . '0.00' . self::$_system_currency_data['currency_symbol_right'];
    }

    public static function concatCurrencySymbolWithAmtLbl()
    {
        $currencyId = FatApp::getConfig('CONF_CURRENCY', FatUtility::VAR_INT, 1);
        $currencyData = Currency::getAttributesById($currencyId, ['currency_code', 'currency_symbol_left', 'currency_symbol_right', 'currency_value']);
        $currencySymbolLeft = $currencyData['currency_symbol_left'];
        $currencySymbolRight = $currencyData['currency_symbol_right'];
        $symbol = $currencySymbolRight ? $currencySymbolRight : $currencySymbolLeft;
        return empty($symbol) ? '' : " ({$symbol})";
    }

    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function multipleExplode($delimiters = [], $string = '')
    {
        $mainDelim = end($delimiters);
        array_pop($delimiters);
        foreach ($delimiters as $delimiter) {
            $string = str_replace($delimiter, $mainDelim, $string);
        }
        $result = explode($mainDelim, $string);
        return self::array_trim($result);
    }

    public static function array_trim($ar)
    {
        foreach ($ar as $key => $val) {
            $val = trim($val);
            if (!empty($val)) {
                $reArray[] = $val;
            }
        }
        return $reArray;
    }

    public static function referralTrackingUrl($code)
    {
        return self::generateFullUrl('Home', 'Referral', [$code]);
    }

    public static function affiliateReferralTrackingUrl($code)
    {
        return self::generateFullUrl('Home', 'AffiliateReferral', [$code]);
    }

    public static function createSlug($string)
    {
        return preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    }

    public static function isCsvValidMimes()
    {
        return [
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'text/plain',
        ];
    }

    public static function createDropDownFromArray($name = '', $arr = [], $selected = 0, $extra = ' ', $selectCaption = '')
    {
        $dropDown = '<select name="' . $name . '" ' . $extra . '>';
        if ($selectCaption) {
            $dropDown .= '<option  value="0">' . $selectCaption . '</option>';
        }
        foreach ($arr as $key => $val) {
            $selectedStr = ($key == $selected) ? 'selected=selected' : '';
            $dropDown .= '<option ' . $selectedStr . ' value="' . $key . '">' . $val . '</option>';
        }
        $dropDown .= '</select>';
        return $dropDown;
    }

    public static function validate_cc_number($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', ($cardNumber));
        $len = strlen($cardNumber);
        $result = [];
        if ($len > 16) {
            $result['card_type'] = 'Invalid';
            return $result;
        }
        switch ($cardNumber) {
            case 0:
                $result['card_type'] = '';
                break;
            case preg_match('/^4/', $cardNumber) >= 1:
                $result['card_type'] = 'VISA';
                break;
            case preg_match('/^5[1-5]/', $cardNumber) >= 1:
                $result['card_type'] = 'MASTER';
                break;
            case preg_match('/^3[47]/', $cardNumber) >= 1:
                $result['card_type'] = 'AMEX';
                break;
            case preg_match('/^3(?:0[0-5]|[68])/', $cardNumber) >= 1:
                $result['card_type'] = 'DINERS_CLUB';
                break;
            case preg_match('/^6(?:011|5)/', $cardNumber) >= 1:
                $result['card_type'] = 'DISCOVER';
                break;
            case preg_match('/^(?:2131|1800|35\d{3})/', $cardNumber) >= 1:
                $result['card_type'] = 'JCB';
                break;
            default:
                $result['card_type'] = '';
                break;
        }
        return $result;
    }

    public static function setCookie($cookieName, string $cookieValue, $cookieExpiryTime = 60 * 60 * 24 * 7, $cookiePath = CONF_WEBROOT_FRONT_URL, $cokieSubDomainName = '', $isCookieSecure = false, $isCookieHttpOnly = true, $samesite = '')
    {
        $cokieSubDomainName = ('' == $cokieSubDomainName) ? $_SERVER['HTTP_HOST'] : $cokieSubDomainName;
        $cookieOptions = [];
        $secure = FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_BOOLEAN, false);
        $isCookieSecure = ($isCookieSecure && $secure) ? true : false;
        if (empty($samesite) && $isCookieSecure) {
            $samesite = 'none';
        }
        $cookieOptions = [
            'expires' => $cookieExpiryTime,
            'path' => $cookiePath,
            'domain' => $cokieSubDomainName,
            'secure' => $secure,
            'httponly' => $isCookieHttpOnly,
            'samesite' => $samesite,
        ];
        if ('' != $samesite) {
            $cookieOptions['samesite'] = $samesite;
        }
        return setcookie($cookieName, $cookieValue, $cookieOptions);
    }

    public static function writeFile($name, $data, &$response)
    {
        $fName = CONF_UPLOADS_PATH . preg_replace('/[^a-zA-Z0-9\/\-\_\.]/', '', $name);
        $dest = dirname($fName);
        if (!file_exists($dest)) {
            mkdir($dest, 0777, true);
        }
        $file = fopen($fName, 'w');
        if (!fwrite($file, $data)) {
            $response = Label::getLabel('MSG_Could_not_save_file.');
            return false;
        }
        fclose($file);
        $response = $fName;
        return true;
    }

    public static function getPaymentCancelPageUrl()
    {
        return CommonHelper::generateFullUrl('Custom', 'paymentCancel');
    }

    public static function getPaymentFailurePageUrl()
    {
        return CommonHelper::generateFullUrl('Custom', 'paymentFailed');
    }

    public static function minify_html($input)
    {
        if ('' === trim($input)) {
            return $input;
        }
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", '', $input));
        // Minify inline CSS declaration(s)
        if (false !== strpos($input, ' style=')) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . CommonHelper::minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        if (false !== strpos($input, '</style>')) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
                return '<style' . $matches[1] . '>' . CommonHelper::minify_css($matches[2]) . '</style>';
            }, $input);
        }
        if (false !== strpos($input, '</script>')) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
                return '<script' . $matches[1] . '>' . CommonHelper::minify_js($matches[2]) . '</script>';
            }, $input);
        }
        return preg_replace(
                [
                    '#<(img|input)(>| .*?>)#s',
                    // Remove a line break and two or more white-space(s) between tag(s)
                    '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                    '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                    '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                    '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                    '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                    '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                    '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                    // Remove HTML comment(s) except IE comment(s)
                    '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s',
                ],
                ['<$1$2</$1>', '$1$2$3', '$1$2$3', '$1$2$3$4$5', '$1$2$3$4$5$6$7', '$1$2$3', '<$1$2', '$1 ', '$1', ''],
                $input
        );
    }

    public static function minify_css($input)
    {
        if ('' === trim($input)) {
            return $input;
        }
        return preg_replace(
                [
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                    // Remove unused white-space(s)
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                    // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                    '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                    // Replace `:0 0 0 0` with `:0`
                    '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                    // Replace `background-position:0` with `background-position:0 0`
                    '#(background-position):0(?=[;\}])#si',
                    // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                    '#(?<=[\s:,\-])0+\.(\d+)#s',
                    // Minify string value
                    '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                    '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                    // Minify HEX color code
                    '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                    // Replace `(border|outline):none` with `(border|outline):0`
                    '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                    // Remove empty selector(s)
                    '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
                ],
                ['$1', '$1$2$3$4$5$6$7', '$1', ':0', '$1:0 0', '.$1', '$1$3', '$1$2$4$5', '$1$2$3', '$1:0', '$1$2'],
                $input
        );
    }

    // JavaScript Minifier
    public static function minify_js($input)
    {
        if ('' === trim($input)) {
            return $input;
        }
        return preg_replace(
                [
                    '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                    // Remove white-space(s) outside the string and regex
                    '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                    // Remove the last semicolon
                    '#;+\}#',
                    // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                    '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                    // --ibid. From `foo['bar']` to `foo.bar`
                    '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i',
                ],
                ['$1', '$1$2', '}', '$1$3', '$1.$3'],
                $input
        );
    }

    public static function getUserCookiesEnabled(): bool
    {
        return !empty($_COOKIE[UserCookieConsent::COOKIE_NAME]);
    }

    public static function getDefaultCurrencySymbol()
    {
        $row = Currency::getAttributesById(FatApp::getConfig('CONF_CURRENCY'), ['currency_symbol_left', 'currency_symbol_right']);
        if (!empty($row)) {
            return ('' != $row['currency_symbol_left']) ? $row['currency_symbol_left'] : $row['currency_symbol_right'];
        }
        trigger_error(Label::getLabel('ERR_Default_currency_not_specified.', CommonHelper::getLangId()), E_USER_ERROR);
    }

    public static function getDefaultCurrencyData()
    {
        $row = Currency::getAttributesById(FatApp::getConfig('CONF_CURRENCY'));
        if (!empty($row)) {
            return $row;
        }
        trigger_error(Label::getLabel('ERR_Default_currency_not_specified.', CommonHelper::getLangId()), E_USER_ERROR);
    }

    public static function logData($str)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        //Something to write to txt log
        $log = 'User: ' . $_SERVER['REMOTE_ADDR'] . ' - ' . date('F j, Y, g:i a') . PHP_EOL .
                'data: ' . $str . PHP_EOL .
                '-------------------------' . PHP_EOL;
        $file = CONF_UPLOADS_PATH . './log_' . date('Y-m-d') . '.txt';
        //Save string to log, use FILE_APPEND to append.
        file_put_contents($file, $log, FILE_APPEND);
    }

    public static function fullCopy($source, $target, $empty_first = true)
    {
        if ($empty_first) {
            self::recursiveDelete($target);
        }
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while (false !== ($entry = $d->read())) {
                if ('.' == $entry || '..' == $entry) {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if (is_dir($Entry)) {
                    self::fullCopy($Entry, $target . '/' . $entry);
                    continue;
                }
                copy($Entry, $target . '/' . $entry);
            }
            $d->close();
        } else {
            copy($source, $target);
        }
    }

    public static function getWeekRangeByDate($date)
    {
        if ('' == $date) {
            trigger_error('Invalid date', E_USER_ERROR);
        }
        $dateTime = new DateTime($date);
        $weekStartAndEndDate = MyDate::getWeekStartAndEndDate($dateTime);
        return [
            'start' => $weekStartAndEndDate['weekStart'],
            'end' => $weekStartAndEndDate['weekEnd'],
        ];
    }

    public static function getDateOrTimeByTimeZone($timeZone = '', $returnInFormat = 'D-M-Y h:i:s A (P)')
    {
        $timeZone = ('' == $timeZone) ? date_default_timezone_get() : $timeZone;
        $dt = date('Y-m-d H:i:s');
        //$date = new DateTime( $dt, new DateTimeZone( date_default_timezone_get() ) );
        $date = new DateTime($dt, new DateTimeZone(FatApp::getConfig('CONF_TIMEZONE')));
        $date->setTimezone(new DateTimeZone($timeZone));
        return $date->format($returnInFormat);
    }

    public static function getVideoDetail($url)
    {
        $data = [];
        $data['video_id'] = '';
        $data['video_thumb'] = '';
        $data['video_type'] = '';
        if (false !== strpos($url, 'youtube')) {
            $pattern = '%^# Match any youtube URL
                        (?:https?://)?  # Optional scheme. Either http or https
                        (?:www\.)?      # Optional www subdomain
                        (?:             # Group host alternatives
                          youtu\.be/    # Either youtu.be,
                        | youtube\.com  # or youtube.com
                          (?:           # Group path alternatives
                                /embed/     # Either /embed/
                          | /v/         # or /v/
                          | .*v=        # or /watch\?v=
                          )             # End path alternatives.
                        )               # End host alternatives.
                        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                        ($|&).*         # if additional parameters are also in query string after video id.
                        $%x';
            $result = preg_match($pattern, $url, $matches);
            if (false !== $result && isset($matches[1])) {
                $data['video_type'] = 1;
                $data['video_id'] = $matches[1];
                $data['video_thumb'] = 'http://img.youtube.com/vi/' . $data['video_id'] . '/1.jpg';
            }
        }
        return $data;
    }

    public static function encryptId($string_to_encrypt)
    {
        $key = md5(ENCRYPTION_SALT);
        $append = '-';
        return urlencode(base64_encode($string_to_encrypt . $append . $key));
    }

    public static function decryptId($string_to_decrypt)
    {
        $d = base64_decode(urldecode($string_to_decrypt));
        $append = '-';
        $value = explode($append, $d);
        return $value[0];
    }

    public static function formatTimeSlotArr($arr)
    {
        $timeSlotArr = array_intersect_key(TeacherGeneralAvailability::timeSlots(), array_flip($arr));
        $formattedArr = [];
        foreach ($timeSlotArr as $k => $timeSlot) {
            $breakTimeStrng = explode('-', $timeSlot);
            $formattedArr[$k]['startTime'] = $breakTimeStrng[0];
            $formattedArr[$k]['endTime'] = $breakTimeStrng[1];
        }
        return array_values($formattedArr);
    }

    public static function getUnreadMsgCount()
    {
        $srch = Thread::getThreads(UserAuthentication::getLoggedUserId());
        $srch->addHaving('message_is_unread', '=', 1);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs, 'thread_id');
        return $srch->recordCount();
    }

    public static function demoUrl(): bool
    {
        return (strtolower($_SERVER['SERVER_NAME']) === 'teach.yo-coach.com');
    }

    public static function getUnreadNotifications($limit = false)
    {
        $srchNotification = UserNotifications::getUserNotifications(UserAuthentication::getLoggedUserId());
        $srchNotification->joinTable(Order::DB_TBL, 'LEFT OUTER JOIN', 'order_id = notification_record_id');
        $srchNotification->addCondition('notification_read', '=', 0);
        $srchNotification->addMultipleFields([
            'notification_record_id as noti_record_id',
            'notification_record_type as noti_type',
            'notification_title as noti_title',
            'notification_description as noti_desc',
            'notification_added_on as noti_sent_on',
            'notification_read as noti_is_read',
            'notification_id as noti_id',
            'notification_sub_record_id as noti_sub_record_id',
        ]);
        $srchNotification->setPageNumber(1);
        if ($limit) {
            $srchNotification->setPageSize(5);
        }
        $rs = $srchNotification->getResultSet();
        return FatApp::getDb()->fetchAll($rs);
    }

    public static function getTeachLangs($ids = null, $homePagCal = false, $singleView = false)
    {
        if (empty($ids)) {
            return '';
        }
        $allLangs = TeachingLanguage::getAllLangs(CommonHelper::getLangId(), true);
        $teachLangIds = explode(',', $ids);
        $teachLangs = [];
        $teachLangsStr = '';
        foreach ($teachLangIds as $teachLangId) {
            if (isset($allLangs[$teachLangId])) {
                $teachLangs[$teachLangId] = $allLangs[$teachLangId];
            }
        }
        if ($singleView || $homePagCal) {
            if (count($teachLangs) > 2) {
                $first_array = array_slice($teachLangs, 0, 2);
                $second_array = array_slice($teachLangs, 2, count($teachLangs));
                ?>
                <div class="language">
                    <?php foreach ($first_array as $teachLang) { ?>
                        <span class="main-language"><?php echo $teachLang; ?></span>
                    <?php } ?>
                    <ul>
                        <li><span class="plus">+</span>
                            <div class="more_listing">
                                <ul>
                                    <?php foreach ($second_array as $teachLang) { ?>
                                        <li><a><?php echo $teachLang; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
                <?php
                return;
            }
            echo '<div class="language">';
            foreach ($teachLangs as $teachLang) {
                ?>
                <span class="main-language"><?php echo $teachLang; ?></span>
                <?php
            }
            echo '</div>';
            return;
        }
        return implode(', ', $teachLangs);
    }

    public static function setCookieConsent(string $value = '')
    {
        if (empty($value)) {
            $value = json_encode(UserCookieConsent::fieldsArrayWithDefultValue());
        }
        self::setCookie(UserCookieConsent::COOKIE_NAME, $value, UserCookieConsent::getCookieExpireTime(), CONF_WEBROOT_FRONT_URL, '', true);
    }

    public static function getCookieConsent(): array
    {
        $settings = [];
        if (!empty($_COOKIE[UserCookieConsent::COOKIE_NAME])) {
            $settings = json_decode($_COOKIE[UserCookieConsent::COOKIE_NAME], true);
        }
        if (UserAuthentication::isUserLogged()) {
            $userCookieConsent = new UserCookieConsent(UserAuthentication::getLoggedUserId());
            $cookieSettings = $userCookieConsent->getCookieSettings();
            if (!empty($cookieSettings)) {
                $settings = json_decode($cookieSettings, true);
                self::setCookieConsent($cookieSettings);
            }
        }
        if (empty($settings)) {
            $settings = UserCookieConsent::fieldsArrayWithDefultValue();
        }
        return $settings;
    }

    public static function setSeesionCookieParams()
    {
        $secure = FatApp::getConfig('CONF_USE_SSL', FatUtility::VAR_BOOLEAN, false);
        $params = [
            'httponly' => true,
            'secure' => $secure,
            'path' => CONF_WEBROOT_FRONT_URL,
        ];
        if ($secure) {
            $params['samesite'] = 'none';
        }
        session_set_cookie_params($params);
    }

    public static function getFileUploadErrorLblKeyFromCode(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'ERR_FILE_SIZE_EXCEEDS_ALLOWED_SIZE_' . ini_get('upload_max_filesize') . 'B';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'ERR_The_uploaded_file_was_only_partially_uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'ERR_No_file_was_uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'ERR_Missing_a_temporary_folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'ERR_Failed_to_write_file_to_disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'ERR_File_upload_stopped_by_extension';
                break;
            default:
                $message = 'ERR_Unknown_upload_error';
                break;
        }
        return $message;
    }

    public static function replaceStringData($str, $replacements = [], $replaceTags = false)
    {
        foreach ($replacements as $key => $val) {
            if ($replaceTags) {
                $val = strip_tags($val);
            }
            $str = str_replace($key, $val, $str);
            $str = str_replace(strtolower($key), $val, $str);
            $str = str_replace(strtoupper($key), $val, $str);
        }
        return $str;
    }

    public static function htmlEntitiesDecode($var, $addSlashes = false)
    {
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $var[$key] = self::htmlEntitiesDecode($val);
            }
        } elseif (is_string($var) || is_numeric($var)) {
            $var = html_entity_decode($var, ENT_COMPAT, 'UTF-8');
            if ($addSlashes) {
                $var = addslashes($var);
            }
        }
        return $var;
    }

    public static function maskAndDisableFormFields(Form $frm, array $fieldsToSkip)
    {
        $flds = $frm->getAllFields();
        foreach ($flds as $fld) {
            if (!in_array($fld->getName(), $fieldsToSkip) && ('submit' != $fld->fldType)) {
                $fld->addFieldTagAttribute('disabled', 'disabled');
            }
            if ('text' == $fld->fldType) {
                $fld->value = '***********';
            }
        }
        $frm->addHTML(Label::getLabel('LBL_Note'), 'note', '<span class="spn_must_field">' . Label::getLabel('NOTE_SETTINGS_NOT_ALLOWED_TO_BE_MODIFIED_ON_DEMO_VERSION') . '</span>')->setWrapperAttribute('class', 'text--center');
    }

    public static function getBannerUrl(string $url): string
    {
        $formattedUrl = $url;
        if (false !== stripos($url, '{siteroot}')) {
            $formattedUrl = str_ireplace('{siteroot}', CONF_WEBROOT_URL, $url);
        }
        return $formattedUrl;
    }

    public static function exportCsv($rs, $refindedLabels, $rowKeys, $labels, $placeholders, $output_file_name)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $output_file_name . '";');
        $outstream = fopen('php://output', 'w');
        fputcsv($outstream, $refindedLabels, ',');
        $count = 1;
        while ($row = FatApp::getDb()->fetch($rs)) {
            $rec = [$count];
            foreach ($rowKeys as $rowKey) {
                if (in_array($rowKey, $placeholders)) {
                    array_push($rec, $labels[$row[$rowKey]]);
                } else {
                    array_push($rec, $row[$rowKey]);
                }
            }
            fputcsv($outstream, $rec, ',');
            ++$count;
        }
        fclose($outstream);
    }

    public static function getPaidLessonDurations(): array
    {
        $defaultPaidLessonDuration = FatApp::getConfig('CONF_DEFAULT_PAID_LESSON_DURATION', FatUtility::VAR_INT, 60);
        $confPaidLessonDuration = FatApp::getConfig('CONF_PAID_LESSON_DURATION', FatUtility::VAR_STRING, $defaultPaidLessonDuration);
        return explode(',', $confPaidLessonDuration);
    }

    public static function encrypt($string)
    {
        $key = hash('sha256', ENCRYPTION_KEY);
        $iv = substr(hash('sha256', ENCRYPTION_IV), 0, 16);
        $output = openssl_encrypt($string, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($output);
    }

    public static function decrypt($string)
    {
        $key = hash('sha256', ENCRYPTION_KEY);
        $iv = substr(hash('sha256', ENCRYPTION_IV), 0, 16);
        return openssl_decrypt(base64_decode($string), 'AES-256-CBC', $key, 0, $iv);
    }

    /**
     * Can Report Issue.
     */
    public static function canReportIssue(string $starttime): bool
    {
        if ('0000-00-00 00:00:00' == $starttime) {
            return false;
        }
        $currenttime = date('Y-m-d H:i:s');
        if (strtotime($currenttime) > strtotime($starttime)) {
            return false;
        }
        $hours = MyDate::timeDiffInHours($currenttime, $starttime);
        if ($hours >= FatApp::getConfig('CONF_REPORT_ISSUE_HOURS_AFTER_COMPLETION')) {
            return true;
        }
        return false;
    }

    /**
     * Can Esclate Issue.
     */
    public static function canEsclateIssue(string $starttime): bool
    {
        if ('0000-00-00 00:00:00' == $starttime) {
            return false;
        }
        $currenttime = date('Y-m-d H:i:s');
        if (strtotime($currenttime) > strtotime($starttime)) {
            return false;
        }
        $hours = MyDate::timeDiffInHours($currenttime, $starttime);
        if ($hours >= FatApp::getConfig('CONF_ESCLATE_ISSUE_HOURS_AFTER_RESOLUTION')) {
            return true;
        }
        return false;
    }

    public static function getEmptyDaySlots()
    {
        return [
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0],
        ];
    }

    public static function getSortbyArr()
    {
        return [
            'popularity_desc' => Label::getLabel('LBL_By_Popularity'),
            'price_asc' => Label::getLabel('LBL_By_Price_Low_to_High'),
            'price_desc' => Label::getLabel('LBL_By_Price_High_to_Low'),
        ];
    }

    public static function validateIntroVideoLink($link): string
    {
        if (empty($link)) {
            return '';
        }
        $pattern = "#" . applicationConstants::INTRODUCTION_VIDEO_LINK_REGEX . "#";
        if (!preg_match($pattern, $link, $matches)) {
            return '';
        }
        if (empty($matches[1])) {
            $link = "//" . $link;
        }
        return $link;
    }

}
