<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $layoutDirection = CommonHelper::getLayoutDirection(); ?>
<section class="section section--slideshow">
    <div class="slideshow slideshow-js">
        <?php foreach ($slides as $slide) {
            $desktop_url = '';
            $tablet_url = '';
            $mobile_url = '';
            $out = '';
            $haveUrl = ($slide['slide_url'] != '');
            $defaultUrl = '';
            $slideArr = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_HOME_PAGE_BANNER, $slide['slide_id'], 0, $siteLangId);
            if (!$slideArr) {
                continue;
            }
            foreach ($slideArr as $slideScreen) {

                switch ($slideScreen['afile_screen']) {
                    case applicationConstants::SCREEN_MOBILE:
                        $mobile_url = FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'slide', array($slide['slide_id'], applicationConstants::SCREEN_MOBILE, $siteLangId, 'MOBILE')), CONF_IMG_CACHE_TIME, '.jpg');
                        break;
                    case applicationConstants::SCREEN_IPAD:
                        $tablet_url = FatCache::getCachedUrl(
                            CommonHelper::generateUrl('Image', 'slide', array($slide['slide_id'], applicationConstants::SCREEN_IPAD, $siteLangId, 'TABLET')),
                            CONF_IMG_CACHE_TIME,
                            '.jpg'
                        );
                        break;
                    case applicationConstants::SCREEN_DESKTOP:
                    default:
                        $defaultUrl = FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'slide', array($slide['slide_id'], applicationConstants::SCREEN_DESKTOP, $siteLangId, 'DESKTOP')), CONF_IMG_CACHE_TIME, '.jpg');
                        $desktop_url = $defaultUrl;
                        break;
                }
            }

            if ($slide['slide_url']) {
                $out .= '<a target="' . $slide['slide_target'] . '" href="' . CommonHelper::processUrlString($slide['slide_url']) . '">';
            }

            $out .= '<div>
                 <div class="slideshow__item">
                 <picture class="hero-img">
                    <source data-aspect-ratio="4:3" srcset="' . $mobile_url . '" media="(max-width: 767px)">
                    <source data-aspect-ratio="4:3" srcset="' . $tablet_url . '" media="(max-width: 1024px)">
                    <source data-aspect-ratio="10:3" srcset="' . $desktop_url . '">
                    <img data-aspect-ratio="10:3" srcset="' . $desktop_url . '" alt="">
                </picture>
            </div></div>';
            if ($slide['slide_url']) {
                $out .= '</a>';
            }
            echo $out;
            
        } ?>
    </div>
    <div class="slideshow-content">
        <h1><?php echo Label::getLabel('LBL_Slider_Title_Text',$siteLangId); ?></h1>
        <p><?php echo Label::getLabel('LBL_Slider_Description_Text',$siteLangId); ?></p>
        <div class="slideshow__form">
        <form method="POST" class="form" action="<?php echo CommonHelper::generateFullUrl('Teachers','languages'); ?>" name="homeSearchForm" id="homeSearchForm" >
                <div class="slideshow-input">
                    <svg class="icon icon--search">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL.'images/sprite.yo-coach.svg#search'; ?>"></use>
                    </svg>
                    <input type="text" name="language" placeholder="<?php echo Label::getLabel('LBL_I_am_learning...',$siteLangId); ?>">
                    <input type="hidden" name="teachLangId">
                    <input type="hidden" name="teachLangSlug">
                </div>
                <button class="btn btn--secondary btn--large btn--block"><?php echo Label::getLabel('LBL_Search_for_teachers',$siteLangId); ?></button>
            </form>
        </div>
        <div class="tags-inline">
            <b><?php echo Label::getLabel("LBL_Popular:",$siteLangId) ?></b>
            <ul>
                <?php
                $lastElment = end($allLanguages);
                foreach ($allLanguages as $langId => $langDetails) {
                    if ($lastElment['tlanguage_id'] != $langDetails['tlanguage_id']) {
                        $langDetails['tlanguage_name'] = $langDetails['tlanguage_name'] . ", ";
                    }
                ?>
                    <li class="tags-inline__item"><a href="<?php echo CommonHelper::generateUrl('teachers', 'languages', [$langDetails['tlanguage_slug']]); ?>"><?php echo $langDetails['tlanguage_name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</section>