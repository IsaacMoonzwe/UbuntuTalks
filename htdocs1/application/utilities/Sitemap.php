<?php

class Sitemap
{

    public static function getUrls($langId)
    {
        $sitemapUrls = [];
        $db = FatApp::getDb();
        // teachers profile
        $srch = new TeacherSearch($langId);
        $srch->applyPrimaryConditions();
        $srch->addMultipleFields(['user_url_name', 'CONCAT(user_first_name, " ", user_last_name) as user_full_name']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $urls = []; 
        while ($row = $db->fetch($rs)) {
            array_push($urls, [
                'url' => CommonHelper::generateFullUrl('Teachers', 'profile', [$row['user_url_name']], CONF_WEBROOT_FRONT_URL),
                'value' => $row['user_full_name'],
                'frequency' => 'weekly'
            ]);
        }
        $sitemapUrls = array_merge($sitemapUrls, array(Label::getLabel('LBL_Teachers') => $urls));
        /* ] */
        /* Group Classes [ */
        $srch = new TeacherGroupClassesSearch();
        $srch->joinTeacher();
        $srch->setTeacherDefinedCriteria(false, false);
        $srch->addCondition('grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
        $srch->addDirectCondition('grpcls_slug IS NOT NULL');
        $srch->addCondition('grpcls_deleted', '=', applicationConstants::NO);
        $srch->addFld('DISTINCT grpcls_id, grpcls_title');
        $srch->addFld('grpcls_slug');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $urls = [];
        while ($row = $db->fetch($rs)) {
            array_push($urls, [
                'url' => CommonHelper::generateFullUrl('GroupClasses', 'view', [$row['grpcls_slug']], CONF_WEBROOT_FRONT_URL),
                'value' => $row['grpcls_title'],
                'frequency' => 'weekly'
            ]);
        }
        
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_Group_Classes') => $urls]);
        /* ] */
        /* CMS Pages [ */
        $srch = new NavigationLinkSearch($langId);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->joinNavigation();
        $srch->addCondition('nlink_deleted', '=', applicationConstants::NO);
        $srch->addCondition('nav_active', '=', applicationConstants::ACTIVE);
        $srch->addMultipleFields(['nav_id', 'nlink_type', 'nlink_cpage_id', 'nlink_url', 'nlink_identifier']);
        $srch->addOrder('nlink_display_order', 'ASC');
        $srch->addGroupBy('nlink_cpage_id');
        $srch->addGroupBy('nlink_url');
        $rs = $srch->getResultSet();
        $urls = [];
        while ($link = $db->fetch($rs)) {
            if ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_CMS && $link['nlink_cpage_id']) {
                array_push($urls, ['url' => CommonHelper::generateFullUrl('Cms', 'view', [$link['nlink_cpage_id']], CONF_WEBROOT_FRONT_URL), 'value' => $link['nlink_identifier'], 'frequency' => 'monthly']);
            } elseif ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_EXTERNAL_PAGE) {
                $url = str_replace('{SITEROOT}', CONF_WEBROOT_FRONT_URL, $link['nlink_url']);
                $url = str_replace('{siteroot}', CONF_WEBROOT_FRONT_URL, $url);
                $url = CommonHelper::processURLString($url);
                array_push($urls, ['url' => CommonHelper::getUrlScheme() . $url, 'value' => $link['nlink_identifier'], 'frequency' => 'monthly']);
            }
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_CMS_Pages') => $urls]);
        return $sitemapUrls;
    }
}
