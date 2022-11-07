<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$teacherLanguage = key($teacher['teachLanguages']);
$langId = CommonHelper::getLangId();
$websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $langId, FatUtility::VAR_STRING, '');
$teacherLangPrices = [];
$bookingDuration = '';
foreach ($userTeachLangs as $key => $value) {
    if (!array_key_exists($value['utl_tlanguage_id'], $teacherLangPrices)) {
        $teacherLangPrices[$value['utl_tlanguage_id']] = [];
    }
    $slotSlabKey = $value['ustelgpr_min_slab'] . '-' . $value['ustelgpr_max_slab'];
    if (!array_key_exists($slotSlabKey, $teacherLangPrices[$value['utl_tlanguage_id']])) {
        $teacherLangPrices[$value['utl_tlanguage_id']][$slotSlabKey] = [
            'title' => sprintf(Label::getLabel('LBL_[%s_-_%s]_Lessons'), $value['ustelgpr_min_slab'], $value['ustelgpr_max_slab']),
            'lang_name' => $value['teachLangName'],
            'langPrices' => []
        ];
    }
    $price = FatUtility::float($value['ustelgpr_price']);
    $percentage = CommonHelper::getPercentValue($value['top_percentage'], $price);
    $price = $price - $percentage;
    $teacherLangPrices[$value['utl_tlanguage_id']][$slotSlabKey]['langPrices'][] = [
        'teachLangName' => $value['teachLangName'],
        'ustelgpr_slot' => $value['ustelgpr_slot'],
        'ustelgpr_max_slab' => $value['ustelgpr_max_slab'],
        'ustelgpr_min_slab' => $value['ustelgpr_min_slab'],
        'teachLangName' => $value['teachLangName'],
        'utl_tlanguage_id' => $value['utl_tlanguage_id'],
        'ustelgpr_price' => $price,
        'top_percentage' => $value['top_percentage'],
    ];
}
$disabledClass = '';
$bookNowOnClickClick = 'onclick="cart.proceedToStep({teacherId: ' . $teacher['user_id'] . '}, \'getUserTeachLangues\');"';
$contactClick = 'onclick="generateThread(' . $teacher['user_id'] . ');"';
if ($loggedUserId == $teacher['user_id']) {
    $disabledClass = 'disabled';
    $bookNowOnClickClick = '';
    $contactClick = '';
}
?>
<section class="section section--profile">
    <div class="container container--fixed">
        <div class="profile-cover">
            <div class="profile-head">
                <div class="detail-wrapper">
                    <div class="profile__media">
                        <div class="avtar avtar--xlarge" data-title="<?php echo CommonHelper::getFirstChar($teacher['user_first_name']); ?>">
                            <?php if (true == User::isProfilePicUploaded($teacher['user_id'])) { ?>
                                <img src="<?php echo FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'User', array($teacher['user_id'], 'MEDIUM')), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="profile-detail">
                        <div class="profile-detail__head">
                            <div href="#" class="tutor-name">
                                <h1><?php echo $teacher['user_full_name']; ?></h1>
                                <div class="flag">
                                    <img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($teacher['user_country_id'], 'DEFAULT')); ?>" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="profile-detail__body">
                            <div class="info-wrapper">
                                <div class="info-tag location">
                                    <svg class="icon icon--location">
                                        <use xlink:href=" <?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#location' ?>"></use>
                                    </svg>
                                    <span class="lacation__name"><?php echo $teacher['user_country_name']; ?></span>
                                </div>
                                <div class="info-tag ratings">
                                    <svg class="icon icon--rating">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#rating' ?>"></use>
                                    </svg>
                                    <span class="value"><?php echo FatUtility::convertToType($reviews['prod_rating'], FatUtility::VAR_FLOAT); ?></span>
                                    <span class="count"><?php echo '(' . FatUtility::int($reviews['totReviews']) . ')'; ?></span>
                                </div>
                                <div class="info-tag list-count">
                                    <div class="total-count"><span class="value"><?php echo $teacher['studentIdsCnt']; ?></span><?php echo Label::getLabel('LBL_Students', $siteLangId) ?></div>
                                    <div class="total-count"><span class="value"><?php echo $teacher['teacherTotLessons']; ?></span><?php echo Label::getLabel('LBL_Lessons', $siteLangId); ?></div>
                                </div>
                            </div>
                            <div class="har-rate"><?php echo Label::getLabel('LBL_Hourly_Rate'); ?><b><?php echo CommonHelper::displayMoneyFormat($teacher['minPrice']); ?> - <?php echo CommonHelper::displayMoneyFormat($teacher['maxPrice']); ?></b></div>
                            <div class="tutor-lang"><b><?php echo Label::getLabel('LBL_Teaches:'); ?></b> <?php echo implode(', ', $teacher['teachLanguages']); ?></div>
                        </div>
                    </div>
                    <div class="detail-actions">
                        <a href="javascript:void(0)" onclick="toggleTeacherFavorite(<?php echo $teacher['user_id']; ?>, this)" class="btn btn--bordered color-black <?php echo ($teacher['uft_id']) ? 'is--active' : ''; ?>">
                            <svg class="icon icon--heart">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#heart'; ?>"></use>
                            </svg>
                            <?php echo Label::getLabel('LBL_Favorite'); ?>
                        </a>
                        <div class="toggle-dropdown">
                            <a href="#" class="btn btn--bordered color-black toggle-dropdown__link-js">
                                <svg class="icon icon--share">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#share'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_Share', $siteLangId); ?>
                            </a>
                            <div class="toggle-dropdown__target toggle-dropdown__target-js">
                                <h6><?php echo Label::getLabel('LBL_Share_On', $siteLangId); ?></h6>
                                <ul class="social--share clearfix">
                                    <li class="social--fb"><span class='st_facebook_large' displayText='Facebook'><img alt="Facebook icon" src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg"></span></li>
                                    <li class="social--tw"><span class='st_twitter_large' displayText='Tweet'><img alt="Twitter icon" src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg"></span></li>
                                    <li class="social--pt"><span class='st-custom-button' data-network="pinterest"><img alt="pinterest icon" src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg"></span></li>
                                    <li class="social--mail"><span class='st_email_large' displayText='Email'><img alt="Send Massage" src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg"></span></li>
                                </ul>
                            </div>
                        </div>
                        <a href="#lessons-prices" class="color-primary btn--link scroll"><?php Label::getLabel('LBL_View_Lessons_Packages', $siteLangId); ?></a>
                    </div>
                </div>
            </div>
            <div class="profile-primary">
                <div class="panel-cover">
                    <div class="is-active panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_About', $siteLangId); ?> <?php echo $teacher['user_full_name']; ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js" style="display:block;">
                        <div class="content__row">
                            <p><?php echo nl2br($teacher['user_profile_info']); ?></p>
                        </div>
                        <div class="content__row">
                            <h4><?php echo Label::getLabel('LBL_Speaks', $siteLangId); ?></h4>
                            <p><?php $this->includeTemplate('teachers/_partial/spokenLanguages.php', $teacher, false); ?></p>
                        </div>
                    </div>
                </div>
                <div class="panel-cover" id="lessons-prices">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_Lessons_Prices', $siteLangId); ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js">
                        <div class="panel-head__right">
                            <label><?php echo Label::getLabel('LBL_Select_Language'); ?></label>
                            <div class="select--box">
                                <select name="teachLanguages" id="teachLang">
                                    <?php foreach ($teacher['teachLanguages'] as $langId => $langName) { ?>
                                        <option value="<?php echo $langId; ?>"><?php echo $langName; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php $i = 1; ?>
                        <?php foreach ($teacherLangPrices as $teachLangId => $teachLangPriceSlabs) { ?>
                            <div <?php echo (($i != 1) ? "style='display:none'" : "") ?> data-lang-id="<?php echo $teachLangId; ?>" class="slider slider--onethird slider--prices slider-onethird-js">
                                <?php foreach ($teachLangPriceSlabs as $slab => $slabDetails) { 
                                    $title = $slabDetails['title'];
                                    foreach ($slabDetails['langPrices'] as $priceDetails) {
                                        if($priceDetails['ustelgpr_slot'] == 180){
                                            $title = Label::getLabel('LBL_Crash_Course');
                                        }
                                    }
                                ?>
                                    <div>
                                        <div class="slider__item">
                                            <div class="card">
                                                <div class="card__head">
                                                    <div class="card__title">
                                                        <h4 class="color-primary"><?php echo $title; ?></h4>
                                                    </div>
                                                </div>
                                                <div class="card__body">
                                                    <div class="lesson-slot-info">
                                                        <ul>
                                                            <?php
                                                            foreach ($slabDetails['langPrices'] as $priceDetails) {
                                                                $onclick = '';
                                                                if ($loggedUserId != $teacher['user_id']) {
                                                                    $onclick = "cart.proceedToStep({teacherId: " . $teacher['user_id'] . ",languageId: " . $priceDetails['utl_tlanguage_id'] . ",lessonDuration: " . $priceDetails['ustelgpr_slot'] . ", lessonQty: " . $priceDetails['ustelgpr_min_slab'] . "},'getTeacherPriceSlabs')";
                                                                }
                                                            ?>
                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="<?php echo $onclick; ?>">
                                                                        <div class="lesson lesson--time"><?php echo $priceDetails['ustelgpr_slot'] . ' ' . Label::getLabel('LBL_Mins', $siteLangId); ?></div>
                                                                        <div class="space"></div>
                                                                        <div class="lesson lesson--price"><?php echo CommonHelper::displayMoneyFormat($priceDetails['ustelgpr_price']); ?></div>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php
                            $i++;
                        }
                        ?>
                    </div>
                </div>
                <div class="panel-cover panel--calendar">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js calendar--trigger-js">
                        <h3><?php echo Label::getLabel('LBL_Schedule', $siteLangId) ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js">
                        <div class="calendar-wrapper">
                            <div id="availbility" class="calendar-wrapper__body">
                            </div>
                        </div>
                        <div class="-gap"></div>
                        <div class="note note--blank note--vertical-border">
                            <svg class="icon icon--sound">
                                <use xlink:href="/images/sprite.yo-coach.svg#sound"></use>
                            </svg>
                            <p><b><?php echo Label::getLabel('LBL_Note:') ?></b><?php echo Label::getLabel('LBL_Not_finding_your_ideal_time?'); ?><a class="bold-600" href="javascript:void(0)" <?php echo $contactClick; ?>><?php echo Label::getLabel('LBL_Contact'); ?></a> <?php echo Label::getLabel('LBL_this_teacher_to_request_a_slot_outside_of_their_current_schedule'); ?></p>
                        </div>
                    </div>
                </div>
                <?php if (count($groupClasses) > 0) { ?>
                    <div class="panel-cover">
                        <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                            <h3><?php echo Label::getLabel('LBL_Group_Classes', $siteLangId); ?></h3>
                        </div>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="slider slider--onethird silder--group-class slider-onethird-js">
                                <?php
                                $disabledClass = 'disabled';
                                $bookNowClick = '';
                                $myDate = new myDate();
                                $myDate->setMonthAndweekName($siteLangId);
                                $user_timezone = MyDate::getUserTimeZone();
                                foreach ($groupClasses as $key => $classDetails) {
                                    $startTime = MyDate::convertTimeFromSystemToUserTimezone('M-d-Y H:i:s', $classDetails['grpcls_start_datetime'], true, $user_timezone);
                                    $curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
                                    $startUnixTime = strtotime($startTime);
                                    $currentUnixTime = strtotime($curDateTime);
                                    if ($loggedUserId != $teacher['user_id']) {
                                        $disabledClass = '';
                                        $bookNowClick = 'onclick="cart.proceedToStep({teacherId:' . $classDetails['grpcls_teacher_id'] . ', grpclsId:' . $classDetails['grpcls_id'] . ', languageId:' . $classDetails['grpcls_tlanguage_id'] . '}, \'getPaymentSummary\');"';
                                    }
                                ?>
                                    <div>
                                        <div class="slider__item">
                                            <div class="card card--bg color-primary">
                                                <div class="card__head">
                                                    <h3><?php echo $classDetails['grpcls_title']; ?></h3>
                                                </div>
                                                <div class="card__body">
                                                    <div class="card__row">
                                                        <?php
                                                        $date_by_user_timezone = MyDate::convertTimeFromSystemToUserTimezone('M d Y, h:i A', $classDetails['grpcls_start_datetime'], true, $user_timezone);
                                                        $to_time_by_user_timezone = MyDate::convertTimeFromSystemToUserTimezone('h:i A', $classDetails['grpcls_end_datetime'], true, $user_timezone);
                                                        ?>
                                                        <span><?php echo Label::getLabel('LBL_Date_&_Time', $siteLangId) ?></span>
                                                        <p><?php echo $myDate->convertToLocal($date_by_user_timezone) . ' - ' . $myDate->convertToLocal($to_time_by_user_timezone); ?></p>
                                                    </div>
                                                    <div class="card__row">
                                                        <span><?php echo Label::getLabel('LBL_Tutor', $siteLangId); ?></span>
                                                        <p><?php echo $classDetails['user_full_name']; ?></p>
                                                    </div>
                                                    <div class="card__row">
                                                        <span><?php echo Label::getLabel("LBL_Price", $siteLangId) ?></span>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <p class="class-price"><?php echo CommonHelper::displayMoneyFormat($classDetails['grpcls_entry_fee']) ?></p>
                                                            <div class="timer">
                                                                <?php if ($startUnixTime > $currentUnixTime) { ?>
                                                                    <div class="timer__media">
                                                                        <span> <svg class="icon icon--clock">
                                                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#clock' ?>"></use>
                                                                            </svg></span>
                                                                    </div>
                                                                    <div class="timer__controls countdowntimer timer-js" id="grup-class_<?php echo $key; ?>" data-startTime="<?php echo $curDateTime; ?>" data-endTime="<?php echo date('Y/m/d H:i:s', $startUnixTime); ?>"></div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card__row--action">
                                                        <a href="<?php echo CommonHelper::generateUrl('GroupClasses', 'view', [CommonHelper::htmlEntitiesDecode($classDetails['grpcls_slug'])]); ?>" class="btn btn--bordered color-primary"><?php echo Label::getLabel('LBL_View_Details', commonHelper::getLangId()); ?></a>
                                                        <a href="javascript:void(0);" <?php echo $bookNowClick; ?> class="btn btn--primary <?php echo $disabledClass; ?>"><?php echo Label::getLabel("LBL_Book_Now"); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="panel-cover">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_Teaching_Expertise', $siteLangId) ?></h3>
                    </div>
                    <?php
                    foreach ($preferencesTypeArr as $key => $preferencesType) {
                        if (empty($teacher['preferences'][$key])) {
                            continue;
                        }
                    ?>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="content-wrapper content--tick">
                                <div class="content__head">
                                    <h4><?php echo $preferencesType; ?></h4>
                                </div>
                                <div class="content__body">
                                    <div class="tick-listing tick-listing--onethird">
                                        <ul>
                                            <?php foreach ($teacher['preferences'][$key] as $preference) { ?>
                                                <li><?php echo $preference; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="panel-cover">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_Teaching_Qualifications', $siteLangId); ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js" id="qualificationsList">
                    </div>
                </div>
                <?php if ($reviews['prod_rating'] > 0) { ?>
                    <div class="panel-cover">
                        <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                            <h3><?php echo Label::getLabel('LBL_Reviews', $siteLangId); ?></h3>
                        </div>
                        <?php echo $frmReviewSearch->getFormHtml(); ?>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="rating-details">
                                <div class="rating__count">
                                    <h1><?php echo FatUtility::convertToType($reviews['prod_rating'], FatUtility::VAR_FLOAT); ?></h1>
                                </div>
                                <div class="rating__info">
                                    <b><?php echo Label::getLabel('LBL_Overall_Ratings', $siteLangId); ?></b>
                                </div>
                            </div>
                            <div class="reviews-wrapper">
                                <div class="reviews-wrapper__head">
                                    <p id="recordToDisplay"></p>
                                    <div class="review__shorting">
                                        <select name="orderBy" id="sort">
                                            <?php foreach ($sortArr as $sortKey => $sortValue) { ?>
                                                <option value="<?php echo $sortKey; ?>"><?php echo $sortValue; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="itemRatings" class="reviews-wrapper__body">
                                </div>
                                <div class="reviews-wrapper__foot">
                                    <div class="show-more">
                                        <a href="javascript:void(0);" class="btn btn--show"><?php echo Label::getLabel('Lbl_SHOW_MORE'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="profile-secondary">
                <div class="right-panel">
                    <?php if (!empty(CommonHelper::validateIntroVideoLink($teacher['us_video_link']))) { ?>
                        <div class="dummy-video">
                            <div class="video-media ratio ratio--16by9">
                                <iframe width="100%" height="100%" src="<?php echo CommonHelper::validateIntroVideoLink($teacher['us_video_link']); ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="-gap"></div>
                    <?php
                    }
                    ?>
                    <div class="box box--book">
                        <div class="book__actions">
                            <a href="javascript:void(0);" class="btn btn--primary btn--xlarge btn--block color-white <?php echo $disabledClass; ?>" <?php echo $bookNowOnClickClick; ?>><?php echo Label::getLabel('LBL_Book_Now', $siteLangId); ?></a>
                            <?php
                            if ($teacher['isFreeTrialEnabled']) {
                                $onclick = "";
                                $btnClass = "btn-secondary";
                                $disabledText = "disabled";
                                $btnText = "LBL_You_already_have_availed_the_Trial";
                                if (!$teacher['isAlreadyPurchasedFreeTrial'] && !empty($teacherLanguage)) {
                                    $disabledText = "";
                                    $onclick = "onclick=\"viewCalendar(" . $teacher['user_id'] . "," . $teacherLanguage . ",'free_trial')\"";
                                    $btnClass = 'btn-primary';
                                    $btnText = "LBL_Book_Free_Trial";
                                }
                                if ($loggedUserId == $teacher['user_id']) {
                                    $onclick = "";
                                    $disabledText = "disabled";
                                }
                            ?>

                                <a href="javascript:void(0);" <?php echo $onclick; ?> class="btn btn--secondary btn--trial btn--block color-white <?php echo $btnClass . ' ' . $disabledText; ?> " <?php echo $disabledText; ?>>
                                    <span><?php echo Label::getLabel($btnText, $siteLangId); ?></span>
                                </a>
                                <div class="-gap"></div>
                            <div class="-gap"></div>
                                 <a href="javascript:void(0);" <?php echo $contactClick; ?> class="btn btn--bordered btn--xlarge btn--block btn--contact color-primary <?php echo $disabledClass; ?>">
                                <svg class="icon icon--envelope">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#envelope'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_Contact', $siteLangId); ?>
                            </a>
                            <a href="#availbility" class="color-primary btn--link scroll"><?php echo Label::getLabel('LBL_View_Full_Availbility', $siteLangId); ?></a>
                                <p><?php echo Label::getLabel('LBL_Trial_Lesson_One_Time', $siteLangId); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        searchQualifications(<?php echo $teacher['user_id']; ?>);
        viewCalendar(<?php echo $teacher['user_id'] . ',' . $teacherLanguage . ', "paid"'; ?>);
        $('body').removeClass('calendar-facebox');
        $('.panel__head-trigger-js').click(function() {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.panel__body-target-js').slideUp();
                return false;
            }
            $('.panel__head-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.panel__body-target-js').slideUp();
            $(this).siblings('.panel__body-target-js').slideDown();
            $('.slider-onethird-js').slick('reinit');
            if ($(this).hasClass('calendar--trigger-js')) {
                window.viewOnlyCal.render();
            }
        });
    });
</script>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>