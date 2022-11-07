<?php

class HomeController extends AdminBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->canView = $this->objPrivilege->canViewAdminDashboard($this->admin_id, true);
        $this->set("canView", $this->canView);
        require_once(CONF_INSTALLATION_PATH . 'library/third-party/phpfastcache.php');
    }

    public function index()
    {
        $accountId = false;
        $this->set('configuredAnalytics', false);
        $this->set('objPrivilege', $this->objPrivilege);
        phpFastCache::setup("storage", "files");
        phpFastCache::setup("path", CONF_INSTALLATION_PATH . "public/caching");
        $cache = phpFastCache();
        $this->clearCache();
        $dashboardInfo = $cache->get("dashboardInfo" . $this->adminLangId);
        if ($dashboardInfo == null) {
            $dashboardInfo = [];
            include_once CONF_INSTALLATION_PATH . 'library/third-party/analytics/AnalyticsAPI.php';
            try {
                $analytics = new AnalyticsAPI();
                $token = $analytics->getRefreshToken(FatApp::getConfig("CONF_ANALYTICS_ACCESS_TOKEN"));
                $analytics->setAccessToken((isset($token['accessToken'])) ? $token['accessToken'] : '');
                $accountId = $analytics->setAccountId(FatApp::getConfig("CONF_ANALYTICS_ID"));
                if (!$accountId) {
                    Message::addErrorMessage(Label::getLabel('LBL_Analytic_Id_does_not_exist_with_Configured_Account', $this->adminLangId));
                } else {
                    $this->set('configuredAnalytics', true);
                }
                if ($accountId) {
                    $statsInfo = $analytics->getVisitsByDate();
                    $visitCount = $statsInfo['result'];
                    foreach ($statsInfo['result'] as $key => $val) {
                        $visitCount[$key] = $val['totalsForAllResults'];
                    }
                    $dashboardInfo['visitsCount'] = (isset($visitCount)) ? $visitCount : '';
                }
            } catch (exception $e) {
                
            }
            $statsObj = new AdminStatistic();
            $dashboardInfo["stats"] = [];
            $dashboardInfo["stats"]["totalUsers"] = $statsObj->getStats('total_members');
            $dashboardInfo["stats"]["totalLessons"] = $statsObj->getStats('total_lessons');
            $dashboardInfo["stats"]["totalCompletedLessons"] = $statsObj->getStats('total_lessons', ScheduledLesson::STATUS_COMPLETED);
            $dashboardInfo["stats"]["totalCancelledLessons"] = $statsObj->getStats('total_lessons', ScheduledLesson::STATUS_CANCELLED);
            $dashboardInfo["stats"]["totalNeedtoScheduleLessons"] = $statsObj->getStats('total_lessons', ScheduledLesson::STATUS_NEED_SCHEDULING);
            $dashboardInfo["stats"]["totalSales"] = $statsObj->getStats('total_sales');
            $dashboardInfo["stats"]["totalEarnings"] = $statsObj->getStats('total_earnings');
            $dashboardInfo['topLessonLanguage'] = $statsObj->getTopLessonLanguages('YEARLY');
            $salesData = $statsObj->getDashboardLast12MonthsSummary($this->adminLangId, 'sales', [], 6);
            $salesChartData = [];
            foreach ($salesData as $key => $val) {
                $salesChartData[$val["duration"]] = $val["value"];
            }
            $signupsData = $statsObj->getDashboardLast12MonthsSummary($this->adminLangId, 'signups', ['user_is_learner' => 1, 'user_is_teacher' => 1], 6);
            $signupsChartData = [];
            foreach ($signupsData as $key => $val) {
                $signupsChartData[$val["duration"]] = $val["value"];
            }
            $earningsData = $statsObj->getDashboardLast12MonthsSummary($this->adminLangId, 'earnings', ['user_is_learner' => 1, 'user_is_teacher' => 1], 6);
            $earningsChartData = [];
            foreach ($earningsData as $key => $val) {
                $earningsChartData[$val["duration"]] = $val["value"];
            }
            $dashboardInfo['salesChartData'] = array_reverse($salesChartData);
            $dashboardInfo['signupsChartData'] = array_reverse($signupsChartData);
            $dashboardInfo['earningsChartData'] = array_reverse($earningsChartData);
            $cache->set("dashboardInfo" . $this->adminLangId, $dashboardInfo, 24 * 60 * 60);
        }
        $this->_template->addJs(['js/chartist.min.js', 'js/jquery.counterup.js', 'js/slick.min.js', 'js/enscroll-0.6.2.min.js']);
        $this->_template->addCss(['css/chartist.css']);
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) {
            $this->_template->addCss('css/ie.css');
        }
        $this->set('dashboardInfo', $dashboardInfo);
        $this->_template->render();
    }

    public function dashboardStats()
    {
        $post = FatApp::getPostedData();
        $type = $post['rtype'];
        $interval = isset($post['interval']) ? $post['interval'] : '';
        include_once CONF_INSTALLATION_PATH . 'library/third-party/analytics/AnalyticsAPI.php';
        phpFastCache::setup("storage", "files");
        phpFastCache::setup("path", CONF_INSTALLATION_PATH . "public/caching");
        $cache = phpFastCache();
        $result = $cache->get("dashboardInfo_" . $type . '_' . $interval);
        if ($result == null) {
            if (strtoupper($type) == 'TOP_LESSON_LANGUAGES') {
                $statsObj = new AdminStatistic();
                $result = $statsObj->getTopLessonLanguages($interval, $this->adminLangId, 10);
            } else {
                try {
                    $analytics = new AnalyticsAPI();
                    $token = $analytics->getRefreshToken(FatApp::getConfig("CONF_ANALYTICS_ACCESS_TOKEN"));
                    if (isset($token['accessToken'])) {
                        $analytics->setAccessToken($token['accessToken']);
                    }
                    $accountId = $analytics->setAccountId(FatApp::getConfig("CONF_ANALYTICS_ID"));
                    switch (strtoupper($type)) {
                        case 'TOP_COUNTRIES':
                            $result = $analytics->getTopCountries($interval, 9);
                            break;
                        case 'TOP_REFERRERS':
                            $result = $analytics->getTopReferrers($interval, 9);
                            break;
                        case 'TRAFFIC_SOURCE':
                            $result = $analytics->getTrafficSource($interval);
                            break;
                        case 'VISITORS_STATS':
                            $result = $analytics->getVisitsByDate();
                            break;
                    }
                } catch (exception $e) {
                    echo $e->getMessage();
                }
            }
            $cache->set("dashboardInfo_" . $type . '_' . $interval, $result, 24 * 60 * 60);
        }
        $this->set('stats_type', strtoupper($type));
        $this->set('stats_info', $result);
        $this->_template->render(false, false);
    }

    public function clearCache()
    {
        FatCache::clearAll();
        phpFastCache::setup("path", CONF_INSTALLATION_PATH . "public/caching");
        $cache = phpFastCache();
        $dashboardStatsType = [
            'TOP_LESSON_LANGUAGES',
            'TOP_COUNTRIES',
            'TOP_REFERRERS',
            'TRAFFIC_SOURCE',
            'VISITORS_STATS'
        ];
        $intervalType = [
            'today',
            'Weekly',
            'Monthly',
            'Yearly',
        ];
        $cache->set("dashboardInfo" . $this->adminLangId, '', -1);
        foreach ($dashboardStatsType as $key => $statsType) {
            foreach ($intervalType as $key => $interval) {
                $cache->set("dashboardInfo_" . $statsType . '_' . $interval, '', -1);
            }
        }
        Message::addMessage(Label::getLabel('LBL_Cache_has_been_cleared', $this->adminLangId));
    }

    public function setLanguage($langId = 0)
    {
        $langId = FatUtility::int($langId);
        if (0 < $langId) {
            $languages = Language::getAllNames();
            if (array_key_exists($langId, $languages)) {
                CommonHelper::setCookie('defaultAdminSiteLang', $langId, time() + 3600 * 24 * 10, CONF_WEBROOT_FRONTEND, '', true);
            }
            $this->set('msg', Label::getLabel('Msg_Please_Wait_We_are_redirecting_you...', $this->adminLangId));
            $this->_template->render(false, false, 'json-success.php');
        }
        Message::addErrorMessage(Label::getLabel('MSG_Please_select_any_language', $this->adminLangId));
        FatUtility::dieWithError(Message::getHtml());
    }

}
