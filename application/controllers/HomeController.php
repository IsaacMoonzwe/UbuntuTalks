<?php

class HomeController extends MyAppController
{

    public function index()
    {
       // $db = FatApp::getDb();
        /* Main Slides[ */
        // $srchSlide = new SlideSearch($this->siteLangId);
        // $srchSlide->doNotCalculateRecords();
        // $srchSlide->joinAttachedFile();
        // $srchSlide->addMultipleFields([
        //     'slide_id', 'slide_record_id', 'slide_type',
        //     'IFNULL(slide_title, slide_identifier) as slide_title', 'slide_target', 'slide_url'
        // ]);
        // $srchSlide->addOrder('slide_display_order');
        // $totalSlidesPageSize = FatApp::getConfig('CONF_TOTAL_SLIDES_HOME_PAGE', FatUtility::VAR_INT, 4);
        // $ppcSlides = [];
        // $adminSlides = [];
        // $slidesSrch = new SearchBase('(' . $srchSlide->getQuery() . ') as t');
        // $slidesSrch->addMultipleFields(['slide_id', 'slide_type', 'slide_record_id', 'slide_url', 'slide_target', 'slide_title']);
        // if ($totalSlidesPageSize > count($ppcSlides)) {
        //     $totalSlidesPageSize = $totalSlidesPageSize - count($ppcSlides);
        //     $adminSlideSrch = clone $slidesSrch;
        //     $adminSlideSrch->addCondition('slide_type', '=', Slides::TYPE_SLIDE);
        //     $adminSlideSrch->setPageSize($totalSlidesPageSize);
        //     $slideRs = $adminSlideSrch->getResultSet();
        //     $adminSlides = $db->fetchAll($slideRs, 'slide_id');
        // }
        // $slide = array_merge($ppcSlides, $adminSlides);
        

        /* Get Value For Days Trials */    
        $srch = FreeDays::getSearchObject($this->siteLangId);
        $srch->addMultipleFields(['faq_id', 'faq_category', 'IFNULL(faq_title, faq_identifier) as faq_title', 'faq_description']);
        $srch->joinTable(FaqCategory::DB_TBL, 'LEFT OUTER JOIN', 'faqcat_id=faq_category');
        $srch->addOrder('faqcat_display_order');
        $srch->setPageSize(50);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetchAll($rs);
        $finaldata = [];
        foreach ($data as $val) {
            $finaldata[$val['faq_category']][] = $val;
        }
        $this->set('finaldata', $finaldata);
        $this->set('typeArr', FreeDays::getFaqCategoryArr($this->siteLangId));

        /* Contact Form Section */
        $contactFrm = $this->contactUsForm($this->siteLangId);
        $post = FatApp::getPostedData();
        if (!empty($post)) {
            $post = $contactFrm->getFormDataFromArray($post);
            $contactFrm->fill($post);
        }
        $this->set('contactFrm', $contactFrm);
        $this->set('siteLangId', $this->siteLangId);


        $frm = $this->contactUsForm($this->siteLangId);
        $srch = TeacherGroupClassesSearch::getSearchObj($this->siteLangId);
        $srch->addCondition('grpcls_end_datetime', '>', date('Y-m-d H:i:s'));
        $rs = $srch->getResultSet();
        $classesList1 = FatApp::getDb()->fetchAll($rs);
        $classesList = [];
        foreach ($classesList1 as $val) {
            $classesList[$val['grpcls_start_datetime']][] = $val;
        }
        $this->set('classesList', $classesList);
        $slide = FatApp::getConfig('CONF_YOUTUBE_VIDEO_SLIDE', FatUtility::VAR_STRING, '');
        $this->set('slide', $slide);
        $this->_template->addJs('js/slick.min.js');
        $this->set('languages', TeachingLanguage::getLanguages($this->siteLangId));
        $this->set('newsLetterForm', Common::getNewsLetterForm(CommonHelper::getLangId()));
        /* ] */
        $this->_template->render();
    }

    private function contactUsForm(int $langId)
    {
        $frm = new Form('frmContact');
        $frm->addRequiredField(Label::getLabel('LBL_First_Name', $langId), 'name', '');
        $frm->addRequiredField(Label::getLabel('LBL_Last_Name', $langId), 'lname', '');
        $frm->addEmailField(Label::getLabel('LBL_Your_Email', $langId), 'email', '');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('BTN_Book_Free_Trial', $langId));
        return $frm;
    }

    public function contactSubmit()
    {
        $frm = $this->contactUsForm($this->siteLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $email = explode(',', FatApp::getConfig('CONF_CONTACT_EMAIL'));
        foreach ($email as $emailId) {
            $emailId = trim($emailId);
            if (false === filter_var($emailId, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $email = new EmailHandler();
            if (!$email->homepagecontactfrom($emailId, $this->siteLangId, $post)) {
                Message::addErrorMessage(Label::getLabel('MSG_email_not_sent_server_issue', $this->siteLangId));
            } else {
                Message::addMessage(Label::getLabel('MSG_your_message_sent_successfully', $this->siteLangId));
            }
        }
        FatApp::redirectUser(CommonHelper::generateUrl('online-classes'));
    }

    public function setSiteDefaultLang($langId = 0, $pathname = '')
    {
        $isActivePreferencesCookie = (!empty($this->cookieConsent[UserCookieConsent::COOKIE_PREFERENCES_FIELD]));
        if (!$isActivePreferencesCookie) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PREFRENCES_COOKIES_ARE_DISABLED', $this->siteLangId));
        }
        $langId = FatUtility::int($langId);
        if (empty(LANG_CODES_ARR[$langId])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST', $this->siteLangId));
        }

        $pathname = ltrim(FatApp::getPostedData('pathname', FatUtility::VAR_STRING, ''), '/');
        $redirectUrl = '';
        $uriComponents = explode('/', $pathname);
        if (!empty($uriComponents)) {
            if (in_array(strtoupper($uriComponents[0]), LANG_CODES_ARR)) {
                $pathname = ltrim(substr(ltrim($pathname, '/'), strlen($uriComponents[0])), '/');
            } else {
                $pathname = ltrim($pathname, CONF_WEBROOT_FRONTEND);
            }
        }
        $uriSegments = explode('/', $pathname);
        $uriSegmentCount = count($uriSegments);
        if ($uriSegmentCount > 2) {
            $urlwithoutparameter = array_slice($uriSegments, 0, 2);
            $lastParamArray = array_slice($uriSegments, (-$uriSegmentCount + 2), ($uriSegmentCount - 2), true);
            $last_param = '/' . implode('/', $lastParamArray);
            $replaceArray = array_fill(count($urlwithoutparameter) - 1, count($lastParamArray), 'urlparameter');
            $uriSegments = array_merge($urlwithoutparameter, $replaceArray);
        }
        $srch = new UrlRewriteSearch();
        $srch->joinTable(UrlRewrite::DB_TBL, 'LEFT OUTER JOIN', 'temp.urlrewrite_original = ur.urlrewrite_original and temp.urlrewrite_lang_id = ' . $langId, 'temp');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addMultipleFields(['ifnull(temp.urlrewrite_custom, ur.urlrewrite_custom) customurl', 'ifnull(temp.urlrewrite_original, ur.urlrewrite_original) originalurl']);
        $srch->addCondition('ur.' . UrlRewrite::DB_TBL_PREFIX . 'custom', '=', implode('/', $uriSegments));
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (!empty($row)) {
            $langUrlRewriteSrch = new UrlRewriteSearch();
            $langUrlRewriteSrch->doNotCalculateRecords();
            $langUrlRewriteSrch->setPageSize(1);
            $langUrlRewriteSrch->addMultipleFields(['urlrewrite_custom', 'urlrewrite_original']);
            $langUrlRewriteSrch->addCondition('ur.' . UrlRewrite::DB_TBL_PREFIX . 'custom', '=', $row['originalurl']);
            $langUrlRewriteSrch->addCondition('ur.' . UrlRewrite::DB_TBL_PREFIX . 'lang_id', '=', $langId);
            $rs = $langUrlRewriteSrch->getResultSet();
            $langUrlrewriterow = FatApp::getDb()->fetch($rs);
            if (!empty($langUrlrewriterow)) {
                $row['customurl'] = $langUrlrewriterow['urlrewrite_custom'];
            } else {
                $row['customurl'] = $row['originalurl'];
            }
            $redirectUrl = CommonHelper::generateFullUrl('', '', [], '', null, false, false, false);
            if (FatApp::getConfig('CONF_LANG_SPECIFIC_URL', FatUtility::VAR_INT, 0) && count(LANG_CODES_ARR) > 1 && $langId != FatApp::getConfig('CONF_DEFAULT_SITE_LANG', FatUtility::VAR_INT, 1)) {
                $redirectUrl .= strtolower(LANG_CODES_ARR[$langId]) . '/';
            }
            if (strpos($row['customurl'], 'urlparameter') !== false || $row['customurl'] == 'teachers/view') {
                $redirectUrl .= implode('/', array_slice(explode('/', $row['customurl']), 0, 2)) . $last_param;
            } else {
                $redirectUrl .= $row['customurl'];
            }
        }
        if (empty($redirectUrl)) {
            $redirectUrl = rtrim(CommonHelper::generateFullUrl('', '', [], '', null, false, false), '/');
            if (FatApp::getConfig('CONF_LANG_SPECIFIC_URL', FatUtility::VAR_INT, 0) && count(LANG_CODES_ARR) > 1 && $langId != FatApp::getConfig('CONF_DEFAULT_SITE_LANG', FatUtility::VAR_INT, 1)) {
                $redirectUrl .= '/' . strtolower(LANG_CODES_ARR[$langId]);
            }
            $redirectUrl .= '/' . ltrim($pathname, '/');
        }
        CommonHelper::setDefaultSiteLangCookie($langId);
        FatUtility::dieJsonSuccess(['redirectUrl' => $redirectUrl, 'msg' => Label::getLabel('LBL_SITE_LANGUAGE_UPDATED', $this->siteLangId)]);
    }

    public function setSiteDefaultCurrency($currencyId = 0)
    {
        $isActivePreferencesCookie = (!empty($this->cookieConsent[UserCookieConsent::COOKIE_PREFERENCES_FIELD]));
        if (!$isActivePreferencesCookie) {
            FatUtility::dieJsonError(Label::getLabel('LBL_PREFRENCES_COOKIES_ARE_DISABLED', $this->siteLangId));
        }
        $currencyId = FatUtility::int($currencyId);
        if (0 < $currencyId) {
            $currencies = Currency::getCurrencyAssoc($this->siteLangId);
            if (array_key_exists($currencyId, $currencies)) {
                if (isset($_SESSION['search_filters']['minPriceRange'])) {
                    unset($_SESSION['search_filters']['minPriceRange']);
                }
                if (isset($_SESSION['search_filters']['maxPriceRange'])) {
                    unset($_SESSION['search_filters']['maxPriceRange']);
                }
                CommonHelper::setCookie('defaultSiteCurrency', $currencyId, time() + 3600 * 24 * 10, CONF_WEBROOT_FRONTEND, '', true);
            }
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SITE_CURRENCY_UPDATED', $this->siteLangId));
    }
}
