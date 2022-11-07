<?php

class Common
{

    public static function homePageSlides($template)
    {
        
    }

    public static function getHomePageBanners($template)
    {
        
    }

    public static function languagesWithTeachersCount($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $template->set("allLanguages", TeachingLanguage::getAllLangsWithUserCount($siteLangId));
        $template->set('siteLangId', $siteLangId);
    }

    public static function languagesWithOrdersCount($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $template->set("allLanguages", TeachingLanguage::getAllLangsWithOrderCount($siteLangId));
        $template->set("siteLangId", $siteLangId);
    }

    public static function upcomingScheduledLessons($template)
    {
        $srch = new ScheduledLessonSearch(false);
        $srch->addGroupBy('slesson_id');
        $srch->joinOrder();
        $srch->joinOrderProducts();
        $srch->joinTeacher();
        $srch->joinLearner();
        $srch->joinTeacherCountry(CommonHelper::getLangId());
        $srch->addCondition('order_is_paid', ' = ', Order::ORDER_IS_PAID);
        $srch->joinTeacherSettings();
        $srch->joinTeacherTeachLanguageView(CommonHelper::getLangId());
        $srch->addOrder('slesson_date', 'ASC');
        $srch->addOrder('slesson_status', 'ASC');
        $srch->addMultipleFields([
            'slns.slesson_id',
            'slns.slesson_slanguage_id',
            'sld.sldetail_learner_id as learnerId',
            'slns.slesson_teacher_id as teacherId',
            'ut.user_first_name as teacherFname',
            'ut.user_url_name as user_url_name',
            'ut.user_last_name as teacherLname',
            'CONCAT(ut.user_first_name, " ", ut.user_last_name) as teacherFullName',
            'IFNULL(teachercountry_lang.country_name, teachercountry.country_code) as teacherCountryName',
            'slns.slesson_date',
            'slns.slesson_start_time',
            'slns.slesson_end_time',
            'slns.slesson_status',
            'slns.slesson_is_teacher_paid',
            'op_lpackage_is_free_trial as is_trial',
            'op_lesson_duration'
        ]);
        $srch->addCondition('slesson_status', ' = ', ScheduledLesson::STATUS_SCHEDULED);
        $srch->addCondition('mysql_func_CONCAT(slns.slesson_date," ",slns.slesson_start_time )', '>=', date('Y-m-d H:i:s'), 'AND', true);
        $srch->setPageSize(10);
        $srch->setPageNumber(1);
        $rs = $srch->getResultSet();
        $lessons = FatApp::getDb()->fetchAll($rs);
        $template->set("lessons", $lessons);
    }

    public static function topRatedTeachers($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $userObj = new UserSearch();
        $topRatedTeachers = $userObj->getTopRatedTeachers();
        if ($topRatedTeachers) {
            foreach ($topRatedTeachers as $k => $topRatedTeacher) {
                if (empty($topRatedTeacher['user_id'])) {
                    unset($topRatedTeachers[$k]);
                }
            }
        }
        $template->set('siteLangId', $siteLangId);
        $template->set("topRatedTeachers", $topRatedTeachers);
    }

    public static function homePageHowItWorks($template)
    {
        $db = FatApp::getDb();
        $bannerSrch = Banner::getBannerLocationSrchObj(true, CommonHelper::getLangId());
        $rs = $bannerSrch->getResultSet();
        $bannerLocation = $db->fetchAll($rs, 'blocation_key');
        $banners = $bannerLocation;
        foreach ($bannerLocation as $val) {
            $srch = new BannerSearch(CommonHelper::getLangId(), true);
            $srch->doNotCalculateRecords();
            $srch->joinAttachedFile();
            $srch->addCondition('banner_blocation_id', '=', $val['blocation_id']);
            $rs = $srch->getResultSet();
            $bannerListing = $db->fetchAll($rs, 'banner_id');
            $banners[$val['blocation_key']]['banners'] = $bannerListing;
        }
        $template->set("banners", $banners);
        $template->set('siteLangId', CommonHelper::getLangId());
    }

    public static function homePageSlidesAboveFooter($template)
    {
        $srch = Testimonial::getSearchObject(CommonHelper::getLangId(), true);
        $srch->addMultipleFields(['t.*', 't_l.testimonial_title', 't_l.testimonial_text']);
        $srch->addCondition('testimoniallang_testimonial_id', 'is not', 'mysql_func_null', 'and', true);
        $srch->addOrder('testimonial_added_on', 'desc');
        $records = FatApp::getDb()->fetchAll($srch->getResultSet());
        $template->set("testimonials", $records);
    }

    public static function headerLanguageArea($template)
    {
        $template->set('siteLangId', CommonHelper::getLangId());
        $template->set('siteCurrencyId', CommonHelper::getCurrencyId());
        $template->set('languages', Language::getAllNames(false));
        $template->set('currencies', Currency::getCurrencyAssoc(CommonHelper::getLangId()));
    }

    public static function headerUserLoginArea($template)
    {
        $template->set('userName', UserAuthentication::getLoggedUserAttribute('user_first_name', true));
        $controllerName = FatApp::getController();
        $arr = explode('-', FatUtility::camel2dashed($controllerName));
        array_pop($arr);
        $urlController = implode('-', $arr);
        $controllerName = ucfirst(FatUtility::dashed2Camel($urlController));
        $action = FatApp::getAction();
        $template->set('controllerName', $controllerName);
        $template->set('action', $action);
        $template->set('siteLangId', CommonHelper::getLangId());
        $template->set('siteCurrencyId', CommonHelper::getCurrencyId());
        $template->set('languages', Language::getAllNames(false));
        $template->set('currencies', Currency::getCurrencyAssoc(CommonHelper::getLangId()));
    }

    public static function languageCurrencySection($template)
    {
        $template->set('siteLangId', CommonHelper::getLangId());
        $template->set('languages', Language::getAllNames(false));
        $template->set('siteCurrencyId', CommonHelper::getCurrencyId());
        $template->set('currencies', Currency::getCurrencyAssoc(CommonHelper::getLangId()));
    }

    public static function footerSocialMedia($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $srch = SocialPlatform::getSearchObject($siteLangId);
        $srch->addMultipleFields(['splatform_url', 'splatform_id', 'IFNULL(splatform_title, splatform_identifier) as splatform_title']);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('splatform_user_id', '=', 0);
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet());
        $template->set('rows', $rows);
        $template->set('siteLangId', $siteLangId);
    }

    public static function learnerSocialMediaSignUp($template)
    {
        
    }
    public static function kidsSearchFilters($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $teacherSrchObj = new  TeacherKidsClassesSearch($siteLangId);
        $priceSrch = clone $teacherSrchObj;
        $priceRs = $priceSrch->getResultSet();
        $priceArr = FatApp::getDb()->fetchAll($priceRs);
        if ($priceArr) {
            $newArr = [];
            $newArr['minPrice'] = min(array_column($priceArr, 'minPrice'));
            $newArr['maxPrice'] = max(array_column($priceArr, 'maxPrice'));
            $priceArr = $newArr;
        }
        if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) {
            $priceArr['minPrice'] = CommonHelper::displayMoneyFormat(($priceArr['minPrice']) ?? 0, false, false, false);
            $priceArr['maxPrice'] = CommonHelper::displayMoneyFormat(($priceArr['maxPrice']) ?? 0, false, false, false);
        }
        $filterDefaultMinValue = ($priceArr['minPrice']) ?? 0;
        $filterDefaultMaxValue = ($priceArr['maxPrice']) ?? 0;
        $template->set('filterDefaultMinValue', $filterDefaultMinValue);
        $template->set('filterDefaultMaxValue', $filterDefaultMaxValue);
        $template->set('priceArr', $priceArr);
        $template->set('currencySymbolLeft', CommonHelper::getCurrencySymbolLeft());
        $template->set('currencySymbolRight', CommonHelper::getCurrencySymbolRight());
       
    }
    public static function teacherLeftFilters($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $frmFilters = new Form('teacherFilters');
        $filterSortBy = [
            'popularity_desc' => Label::getLabel('LBL_By_Popularity'),
            'price_asc' => Label::getLabel('LBL_By_Price_Low_to_High'),
            'price_desc' => Label::getLabel('LBL_By_Price_High_to_Low'),
        ];
        $frmFilters->addSelectBox('', 'filterSortBy', $filterSortBy, '', [], Label::getLabel('LBL_Sort_by'));
        $teacherSrchObj = new UserSearch();
        $teacherSrchObj->setTeacherDefinedCriteria(true);
        $teacherSrchObj->doNotLimitRecords();
        /* preferences/skills[ */
        $prefSrch = new UserToPreferenceSearch();
        $prefSrch->joinToPreference($siteLangId, 'INNER JOIN');
        $prefSrch->addOrder('preference_display_order', 'asc');
        $prefSrch->addMultipleFields([
            'DISTINCT preference_id',
            'preference_type',
            'IFNULL(preference_title, preference_identifier) as preference_titles'
        ]);
        $prefRs = $prefSrch->getResultSet();
        $prefSrch->doNotCalculateRecords();
        $preferences = FatApp::getDb()->fetchAll($prefRs);
        $allPreferences = [];
        foreach ($preferences as $value) {
            $allPreferences[$value['preference_type']][$value['preference_id']] = $value;
        }
        $template->set('allPreferences', $allPreferences);
        $template->set('preferenceTypeArr', Preference::getPreferenceTypeArr(CommonHelper::getLangId()));
        $spokenLangSrch = clone $teacherSrchObj;
        $spokenLangSrch->joinTable(UserToLanguage::DB_TBL, 'INNER JOIN', 'u.user_id = utsl2.utsl_user_id', 'utsl2');
        $spokenLangSrch->joinTable(SpokenLanguage::DB_TBL, 'INNER JOIN', 'utsl_slanguage_id = slanguage_id AND slanguage_active = 1');
        $spokenLangSrch->joinTable(SpokenLanguage::DB_TBL . '_lang', 'LEFT JOIN', 'slanguagelang_slanguage_id = utsl_slanguage_id AND slanguagelang_lang_id = ' . CommonHelper::getLangId(), 'sl_lang');
        $spokenLangSrch->addGroupBy('utsl_slanguage_id');
        $spokenLangSrch->addMultipleFields(['slanguage_id', 'IFNULL(slanguage_name, slanguage_identifier) as slanguage_name']);
        $spokenLangSrch->addOrder('slanguage_display_order');
        $spokenLangRs = $spokenLangSrch->getResultSet();
        $spokenLangsArr = FatApp::getDb()->fetchAllAssoc($spokenLangRs);
        $template->set('spokenLangsArr', $spokenLangsArr);
        /* ] */
        /* [ */
        $priceSrch = clone $teacherSrchObj;
        $priceRs = $priceSrch->getResultSet();
        $priceArr = FatApp::getDb()->fetchAll($priceRs);
        if ($priceArr) {
            $newArr = [];
            $newArr['minPrice'] = min(array_column($priceArr, 'minPrice'));
            $newArr['maxPrice'] = max(array_column($priceArr, 'maxPrice'));
            $priceArr = $newArr;
        }
        if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) {
            $priceArr['minPrice'] = CommonHelper::displayMoneyFormat(($priceArr['minPrice']) ?? 0, false, false, false);
            $priceArr['maxPrice'] = CommonHelper::displayMoneyFormat(($priceArr['maxPrice']) ?? 0, false, false, false);
        }
        $filterDefaultMinValue = ($priceArr['minPrice']) ?? 0;
        $filterDefaultMaxValue = ($priceArr['maxPrice']) ?? 0;
        $template->set('filterDefaultMinValue', $filterDefaultMinValue);
        $template->set('filterDefaultMaxValue', $filterDefaultMaxValue);
        $template->set('priceArr', $priceArr);
        $template->set('currencySymbolLeft', CommonHelper::getCurrencySymbolLeft());
        $template->set('currencySymbolRight', CommonHelper::getCurrencySymbolRight());
        /* ] */
        /* from countries[ */
        $fromSrch = clone $teacherSrchObj;
        $fromSrch->joinUserCountry(CommonHelper::getLangId());
        $fromSrch->addMultipleFields(['user_country_id', 'IFNULL(country_name, country_code) as country_name']);
        $fromSrch->addGroupBy('user_country_id');
        $fromRs = $fromSrch->getResultSet();
        $fromArr = FatApp::getDb()->fetchAll($fromRs);
        $teachLangs = TeachingLanguage::getAllLangs($siteLangId);
        $template->set('teachLangs', $teachLangs);
        $template->set('fromArr', $fromArr);
        $template->set('genderArr', User::getGenderArr());
        $template->set('siteLangId', $siteLangId);
        $template->set('frmFilters', $frmFilters);
    }

    public static function blogSidePanelArea($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $blogSrchFrm = static::getBlogSearchForm();
        $blogSrchFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Blog'));
        /* to fill the posted data into form[ */
        $postedData = FatApp::getPostedData();
        $blogSrchFrm->fill($postedData);
        /* ] */
        /* Right Side Categories Data[ */
        $categoriesArr = BlogPostCategory::getBlogPostCatParentChildWiseArr($siteLangId);
        $template->set('categoriesArr', $categoriesArr);
        /* ] */
        $template->set('blogSrchFrm', $blogSrchFrm);
        $template->set('siteLangId', $siteLangId);
    }

    public static function footerNewsLetter($template)
    {
        $form = static::getNewsLetterForm();
        $template->set('form', $form);
    }

    public static function blogTopFeaturedCategories($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $bpCatObj = new BlogPostCategory();
        $arrCategories = $bpCatObj->getFeaturedCategories($siteLangId);
        $categories = $bpCatObj->makeAssociativeArray($arrCategories);
        $template->set('featuredBlogCategories', $categories);
        $template->set('siteLangId', $siteLangId);
    }

    public static function getBlogSearchForm()
    {
        $frm = new Form('frmBlogSearch');
        $frm->setFormTagAttribute('autocomplete', 'off');
        $frm->addTextBox('', 'keyword', '', ['placeholder' => Label::getLabel('Lbl_Search', CommonHelper::getLangId())]);
        $frm->addHiddenField('', 'page', 1);
        $frm->addSubmitButton('', 'btn_submit', '');
        return $frm;
    }

    public static function getNewsLetterForm()
    {
        $frm = new Form('frmNewsLetter');
        $fld1 = $frm->addEmailField('', 'email');
        $fld1->requirements()->setRequired();
        $fld1->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_WITH_NONE);
        $frm->addSubmitButton('', 'btnSubmit', Label::getLabel('LBL_SUBSCRIBE'));
        return $frm;
    }

    public static function doesStringStartWith(string $string, string $piece): bool
    {
        return substr($string, 0, strlen($piece)) == $piece;
    }

    public static function getUriFromPath($path)
    {
        return self::doesStringStartWith($path, CONF_WEBROOT_URL) ? rtrim(substr($path, strlen(CONF_WEBROOT_URL)), '/') : ltrim($path, '/');
    }

    public static function getCanonicalUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url_components = parse_url($url);
        $path = $url_components['path'];
        $uri = Common::getUriFromPath($path);
        $row = UrlRewrite::getDataByOriginalUrl($uri);
        $rootUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
        if (!empty($_SERVER['HTTP_HOST'])) {
            $rootUrl .= $_SERVER['HTTP_HOST'];
        } else {
            $rootUrl .= $_SERVER['SERVER_NAME'];
        }
        return $rootUrl . CONF_WEBROOT_URL . ($row['urlrewrite_custom'] ?? $uri);
    }

    public static function getBrowseTutorSection($template)
    {
        $browseTutorPage = ExtraPage::getBlockContent(ExtraPage::BLOCK_BROWSE_TUTOR, commonHelper::getLangId());
        $template->set('browseTutorPage', $browseTutorPage);
    }

    public static function whyUsTemplateContent($template)
    {
        $epageDetail = ExtraPage::getBlockContent(ExtraPage::BLOCK_WHY_US, CommonHelper::getLangId());
        $template->set('epage', $epageDetail);
    }

    public static function getTeachLanguages($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $teachingLangSrch = new TeachingLanguageSearch($siteLangId);
        $teachingLangSrch->doNotCalculateRecords();
        $teachingLangSrch->addMultiplefields(['tlanguage_id', 'IFNULL(tlanguage_name, tlanguage_identifier) as tlanguage_name', 'tlanguage_slug']);
        $teachingLangSrch->addOrder('tlanguage_display_order');
        $teachingLangSrch->addCondition('tlanguage_active', '=', applicationConstants::YES);
        $teachingLanguagesArr = FatApp::getDb()->fetchAll($teachingLangSrch->getResultSet());
        $template->set('teachLangs', $teachingLanguagesArr);
        $template->set('siteLangId', $siteLangId);
    }

    public static function upcomingGroupClass($template)
    {
        $siteLangId = commonHelper::getLangId();
        $pageSize = 3;
        $srch = TeacherGroupClassesSearch::getSearchObj($siteLangId);
        $srch->addCondition('grpcls_status', '=', TeacherGroupClasses::STATUS_ACTIVE);
        $srch->addCondition('grpcls_start_datetime', '>', date('Y-m-d H:i:s'));
        $srch->setPageSize($pageSize);
        $srch->addOrder('grpcls_start_datetime', 'Asc');
        $rs = $srch->getResultSet();
        $classesList = FatApp::getDb()->fetchAll($rs);
        $template->set('siteLangId', $siteLangId);
        $template->set('classes', $classesList);
    }

    public static function getBlogs($template)
    {
        $siteLangId = CommonHelper::getLangId();
        $blogSrch = BlogPost::getSearchObject($siteLangId, true);
    }

    public static function getTestmonials($template)
    {
        $pageSize = 4;
        $siteLangId = CommonHelper::getLangId();
        $testmonialSrch = Testimonial::getSearchObject($siteLangId, true);
        $testmonialSrch->joinTable(AttachedFile::DB_TBL, 'LEFT  JOIN', 'af.afile_record_id = t.testimonial_id and afile_type =' . AttachedFile::FILETYPE_TESTIMONIAL_IMAGE, 'af');
        $testmonialSrch->setPageSize($pageSize);
        $testmonialList = FatApp::getDb()->fetchAll($testmonialSrch->getResultSet());
        $template->set('testmonialList', $testmonialList);
        $template->set('siteLangId', $siteLangId);
    }

    public static function getBlogsForGrids($template)
    {
        $pageSize = 4;
        $siteLangId = CommonHelper::getLangId();
        $blogPostSrch = BlogPost::getSearchObject($siteLangId, true, true);
        $blogPostSrch->joinTable(AttachedFile::DB_TBL, 'INNER  JOIN', 'af.afile_record_id = bp.post_id and afile_type =' . AttachedFile::FILETYPE_BLOG_POST_IMAGE, 'af');
        $blogPostSrch->setPageSize($pageSize);
        $blogPostsList = FatApp::getDb()->fetchAll($blogPostSrch->getResultSet());
        $template->set('blogPostsList', $blogPostsList);
        $template->set('siteLangId', $siteLangId);
    }

    public static function footerSignUpNavigation($template)
    {
        
    }

}
