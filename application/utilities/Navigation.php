<?php

class Navigation
{

    public static function headerTopNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $headerTopNavigationCache = FatCache::get('headerTopNavigation_' . $siteLangId, CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($headerTopNavigationCache) {
            $headerTopNavigation = unserialize($headerTopNavigationCache);
        } else {
            $headerTopNavigation = self::getNavigation(Navigations::NAVTYPE_TOP_HEADER);
            FatCache::set('headerTopNavigationCache_' . $siteLangId, serialize($headerTopNavigation), '.txt');
        }
        $template->set('top_header_navigation', $headerTopNavigation);
    }

    public static function headerNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $headerNavigationCache = FatCache::get('headerNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($headerNavigationCache) {
            $headerNavigation = unserialize($headerNavigationCache);
        } else {
            $headerNavigation = self::getNavigation(Navigations::NAVTYPE_HEADER);
            FatCache::set('headerNavigationCache', serialize($headerNavigation), '.txt');
        }
        $template->set('header_navigation', $headerNavigation);
    }

    public static function getNavigation($type = 0, $includeChildCategories = false)
    {
        $siteLangId = CommonHelper::getLangId();
        $srch = new NavigationLinkSearch($siteLangId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->joinNavigation();
        $srch->joinContentPages();
        $srch->addCondition('nav_type', '=', $type);
        $srch->addCondition('nlink_deleted', '=', applicationConstants::NO);
        $srch->addCondition('nav_active', '=', applicationConstants::ACTIVE);
        $srch->addMultipleFields([
            'nav_id', 'IFNULL( nav_name, nav_identifier ) as nav_name',
            'IFNULL( nlink_caption, nlink_identifier ) as nlink_caption', 'nlink_type',
            'nlink_cpage_id', 'IFNULL( cpage_deleted, ' . applicationConstants::NO . ' ) as filtered_cpage_deleted',
            'nlink_target', 'nlink_url', 'nlink_login_protected'
        ]);
        $isUserLogged = UserAuthentication::isUserLogged();
        if ($isUserLogged) {
            $cnd = $srch->addCondition('nlink_login_protected', '=', NavigationLinks::NAVLINK_LOGIN_BOTH);
            $cnd->attachCondition('nlink_login_protected', '=', NavigationLinks::NAVLINK_LOGIN_YES, 'OR');
        }
        if (!$isUserLogged) {
            $cnd = $srch->addCondition('nlink_login_protected', '=', NavigationLinks::NAVLINK_LOGIN_BOTH);
            $cnd->attachCondition('nlink_login_protected', '=', NavigationLinks::NAVLINK_LOGIN_NO, 'OR');
        }
        $srch->addOrder('nlink_display_order', 'ASC');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        $navigation = [];
        $previous_nav_id = 0;
        if ($rows) {
            foreach ($rows as $key => $row) {
                if ($key == 0 || $previous_nav_id != $row['nav_id']) {
                    $previous_nav_id = $row['nav_id'];
                }
                $navigation[$previous_nav_id]['parent'] = $row['nav_name'];
                $navigation[$previous_nav_id]['pages'][$key] = $row;
            }
        }
        return $navigation;
    }

    public static function footerNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $footerNavigationCache = FatCache::get('footerNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($footerNavigationCache) {
            $footerNavigation = unserialize($footerNavigationCache);
        } else {
            $footerNavigation = self::getNavigation(Navigations::NAVTYPE_FOOTER);
            FatCache::set('footerNavigationCache', serialize($footerNavigation), '.txt');
        }
        $template->set('footer_navigation', $footerNavigation);
    }

    public static function footerRightNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $footerRightNavigationCache = FatCache::get('footerRightNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($footerRightNavigationCache) {
            $footerRightNavigation = unserialize($footerRightNavigationCache);
        } else {
            $footerRightNavigation = self::getNavigation(Navigations::NAVTYPE_FOOTER_RIGHT);
            FatCache::set('footerRightNavigationCache', serialize($footerRightNavigation), '.txt');
        }
        $template->set('footer_right_navigation', $footerRightNavigation);
    }

    public static function footerBottomNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $footerBottomNavigationCache = FatCache::get('footerBottomNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($footerBottomNavigationCache) {
            $footerBottomNavigation = unserialize($footerBottomNavigationCache);
        } else {
            $footerBottomNavigation = self::getNavigation(Navigations::NAVTYPE_FOOTER_BOTTOM);
            FatCache::set('footerBottomNavigationCache', serialize($footerBottomNavigation), '.txt');
        }
        $template->set('footer_bottom_navigation', $footerBottomNavigation);
    }

    public static function footerSignUpNavigation($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $footerSignUpNavigationCache = FatCache::get('footerSignUPNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($footerSignUpNavigationCache) {
            $footerSignUpNavigation = unserialize($footerSignUpNavigationCache);
        } else {
            $footerSignUpNavigation = self::getNavigation(Navigations::NAVTYPE_FOOTER_SIGNUP);
            FatCache::set('footerSignUPNavigation', serialize($footerSignUpNavigation), '.txt');
        }
        $template->set('footerSignUPNavigation', $footerSignUpNavigation);
        $template->set('siteLangId', $siteLangId);
    }

    public static function headerMoreNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $headerNavigationMoreCache = FatCache::get('headerNavigationMore', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($headerNavigationMoreCache) {
            $headerNavigationMore = unserialize($headerNavigationMoreCache);
        } else {
            $headerNavigationMore = self::getNavigation(Navigations::NAVTYPE_HEADER_MORE);
            FatCache::set('headerNavigationMoreCache', serialize($headerNavigationMore), '.txt');
        }
        $template->set('header_navigation_more', $headerNavigationMore);
    }

    public static function dashboardNavigation($template)
    {
        $controllerName = FatApp::getController();
        $arr = explode('-', FatUtility::camel2dashed($controllerName));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $controllerName = ucfirst(FatUtility::dashed2Camel($urlController));
        $action = FatApp::getAction();
        $template->set('controllerName', $controllerName);
        $template->set('action', $action);
    }

    public static function dashboardRightNavigation($template)
    {
        $controllerName = FatApp::getController();
        $arr = explode('-', FatUtility::camel2dashed($controllerName));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $controllerName = ucfirst(FatUtility::dashed2Camel($urlController));
        $action = FatApp::getAction();
        $userObj = new User(UserAuthentication::getLoggedUserId());
        $userDetails = $userObj->getDashboardData(CommonHelper::getLangId());
        $template->set('userDetails', $userDetails);
        $template->set('controllerName', $controllerName);
        $template->set('action', $action);
    }

    public static function tutorListNavigation($template)
    {
        $db = FatApp::getDb();
        $siteLangId = CommonHelper::getLangId();
        $tutorListNavigationCache = FatCache::get('tutorListNavigation', CONF_HOME_PAGE_CACHE_TIME, '.txt');
        if ($tutorListNavigationCache) {
            $tutorListNavigation = unserialize($tutorListNavigationCache);
        } else {
            $tutorListNavigation = self::getTutorListByTeachLanguage();
            FatCache::set('tutorListNavigationCache', serialize($tutorListNavigation), '.txt');
        }
        $template->set('tutorListNavigation', $tutorListNavigation);
    }

    public static function getTutorList()
    {
        $pageSize = 5;
        $srch = new SpokenLanguageSearch(CommonHelper::getLangId());
        $srch->doNotCalculateRecords();
        $srch->setPageSize($pageSize);
        $srch->addMultipleFields(['slanguage_id', 'IFNULL(slanguage_name, slanguage_identifier) as slanguage_name']);
        $srch->addCondition('slanguage_active', '=', applicationConstants::ACTIVE);
        $rs = $srch->getResultSet();
        $languages = FatApp::getDb()->fetchAll($rs, 'slanguage_id');
        $json = [];
        foreach ($languages as $key => $language) {
            $json[] = ['id' => $key, 'name' => $language['slanguage_name'],];
        }
        return $json;
    }

    public static function getTutorListByTeachLanguage()
    {
        $pageSize = 5;
        $srch = new TeachingLanguageSearch(CommonHelper::getLangId());
        $srch->doNotCalculateRecords();
        $srch->setPageSize($pageSize);
        $srch->addMultipleFields(['tlanguage_id', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_slug']);
        $srch->addCondition('tlanguage_active', '=', applicationConstants::ACTIVE);
        $rs = $srch->getResultSet();
        $languages = FatApp::getDb()->fetchAll($rs, 'tlanguage_id');
        $json = [];
        foreach ($languages as $key => $language) {
            $json[] = ['id' => $key, 'name' => $language['tlanguage_name'], 'slug' => $language['tlanguage_slug']];
        }
        return $json;
    }

}
