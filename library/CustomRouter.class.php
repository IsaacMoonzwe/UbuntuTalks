<?php

class CustomRouter
{
    static function setRoute(&$controller, &$action, &$queryString)
    {

        if (
            !defined('SYSTEM_FRONT') || FatUtility::isAjaxCall() ||
            UrlHelper::isStaticContentProvider($controller, $action)
        ) {
            return true;
        }
        /**
         * get the $uriSegment and query sting from url 
         */

        $urlComponents = parse_url($_SERVER['REQUEST_URI']);
        $httpQueryData = (!empty($urlComponents['query'])) ? '?' . $urlComponents['query'] : '';
        $uriSegments =  array_filter(explode("/", urldecode($urlComponents['path'])), 'strlen');
        $uriSegments =  array_values($uriSegments);

        /**
         * get the Configuration and set the lang id
         */
        $langSpecificUrl = FatApp::getConfig('CONF_LANG_SPECIFIC_URL');
        $defaultSiteLangId  = FatApp::getConfig('CONF_DEFAULT_SITE_LANG');
        $cookieLangId = $_COOKIE['defaultSiteLang'] ?? '';
        /**
         * checked the URI first segment has lang id and if has and langSpecificUrl active then removed the lang id from the URL segments  
         */

        $urlLangId = array_search(strtoupper($uriSegments[0] ?? ''), LANG_CODES_ARR);
        if ($langSpecificUrl) {
            $langId = $defaultSiteLangId;
            if ($urlLangId !== false) {
                $langId = $urlLangId;
                array_shift($uriSegments);
            }
            if (in_array($action, ['login-google', 'google-calendar-authorize']) && !empty(LANG_CODES_ARR[$cookieLangId])) {
                $langId = $cookieLangId;
            }
        }


        $slice = 2;
        $firstSegment = $uriSegments[0] ?? '';
        $controller = empty($firstSegment) ? 'Home' : $firstSegment;
        $action = empty($uriSegments[1]) ? 'index' : $uriSegments[1];
        if (strtoupper($firstSegment) == 'DASHBOARD') {
            $controller =  empty($uriSegments[1]) ? 'Home' : $uriSegments[1];
            $action = empty($uriSegments[2]) ? 'index' : $uriSegments[2];
            $slice = 3;
        }

        $queryString = [];
        $urlQueryString = '';
        $uriParamsSegment = $uriSegments;
        if (count($uriSegments) > 2) {
            $replaceArray = array_fill($slice - 1, count($uriSegments) - $slice, 'urlparameter');
            $uriParamsSegment = array_merge(array_slice($uriSegments, 0, $slice), $replaceArray);
            $queryString = array_slice($uriSegments, $slice);
            $urlQueryString = '/' . implode('/', $queryString);
        }
        if (UrlHelper::isStaticContentProvider($controller, $action)) {
            return true;
        }

        $senitizedUrl = urldecode(implode('/', $uriSegments));
        $urlwithParameter = urldecode(implode('/', $uriParamsSegment));
        $baseUrl = CommonHelper::getRootUrl() . '/';
        $senitizedUrl = empty($senitizedUrl) ? '/' : $senitizedUrl;
        if (empty($senitizedUrl) && $langSpecificUrl) {
            if ($urlLangId && $defaultSiteLangId == $langId) {
                header("Location:" . $baseUrl . $httpQueryData, true, 302);
                header("Connection: close");
            }
            define('SYSTEM_LANG_ID', $langId);
            return;
        }

        if (!$langSpecificUrl) {
            $langId = CommonHelper::setSiteDefaultLang();
        }

        $row = UrlRewrite::checkOriginalUrl($senitizedUrl, $urlwithParameter, $langId);
        if (!empty($row)) {
            $customUrl = explode('/urlparameter', $row['urlrewrite_custom']);
            $customUrl = rtrim($customUrl[0] ?? '', '/');
            if (strpos($row['urlrewrite_custom'], '/urlparameter')) {
                $customUrl =  $customUrl . '/'.urlencode(ltrim($urlQueryString,'/'));
            }
            if ($langSpecificUrl && $defaultSiteLangId != $langId) {
                $customUrl = strtolower(LANG_CODES_ARR[$langId]) . '/' . $customUrl;
            }
            header("Location:" . $baseUrl . $customUrl . $httpQueryData, true, $row['urlrewrite_http_resp_code']);
            header("Connection: close");
            return;
        } else {
            $row = UrlRewrite::checkCustomUrl($senitizedUrl, $urlwithParameter);
            if (!empty($row)) {
                if ($langSpecificUrl && $urlLangId && $langId != $row['urlrewrite_lang_id']) {
                    return;
                }
                $originalUrl = explode('/', $row['urlrewrite_original']);
                if (!strpos($row['urlrewrite_custom'], '/urlparameter')) {
                    $queryString = array_slice($originalUrl, 2);
                }
                $controller = empty($originalUrl[0]) ? 'Home' : $originalUrl[0];
                $action = empty($originalUrl[1]) ? 'Index' : $originalUrl[1];
                if ($langSpecificUrl && !$urlLangId && $defaultSiteLangId != $row['urlrewrite_lang_id']) {
                    $url =  $baseUrl . strtolower(LANG_CODES_ARR[$row['urlrewrite_lang_id']]) . '/' . $senitizedUrl;
                    $url = $url . $httpQueryData;
                    header("Location:" . $url, true, $row['urlrewrite_http_resp_code']);
                    header("Connection: close");
                }
                define('SYSTEM_LANG_ID', $row['urlrewrite_lang_id']);
                return;
            }
        }
        if ($langSpecificUrl) {
            if ($urlLangId && $defaultSiteLangId == $langId) {
                $urlAction = '/' . $action;
                if (empty($urlQueryString) && $action == 'index') {
                    $urlAction = '';
                }
                $url = $baseUrl . $controller . $urlAction . $urlQueryString;
                $url = $url . $httpQueryData;
                header("Location:" . $url, true, $row['urlrewrite_http_resp_code']);
                header("Connection: close");
            }
        }
        define('SYSTEM_LANG_ID', $langId);
    }
}
