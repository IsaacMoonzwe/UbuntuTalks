<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$layoutDirection = CommonHelper::getLayoutDirection();
$lessonDuration = $cartData['lessonDuration'];
if ($cartData['grpclsId'] > 0 && !empty($cartData['startDateTime']) && !empty($cartData['endDateTime'])) {
    $lessonDuration = Mydate::timeDiffInMints($cartData['startDateTime'], $cartData['endDateTime']);
}
if ($cartData['isFreeTrial'] == applicationConstants::NO && $cartData['lessonQty'] > 0) {
    $teachLanguages = array_column($userTeachLanguages, 'teachLangName', 'utl_tlanguage_id');
    $bookingDurations = array_column($userTeachLanguages, 'ustelgpr_slot', 'ustelgpr_slot');
    ?>
    <script>
        var teachLanguages = <?php echo FatUtility::convertToJson($teachLanguages); ?>
    </script>
<?php } ?>
<section class="section section--grey section--page">
    <div class="container container--narrow">
        <div class="section__head">
            <h2><?php echo Label::getLabel('LBL_Checkout'); ?></h2>
        </div>
        <div class="section__body">
            <div class="row d-block -clearfix">
                <div id="lessonDetails" class="col-xl-4 col-lg-4 col-md-12 -clear-right">
                    <div class="box -align-center" style="margin-bottom: 30px;">
                        <div class="-padding-30">
                            <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($cartData['user_first_name']); ?>">
                                <?php
                                if (true == User::isProfilePicUploaded($cartData['user_id'])) {
                                    $img = FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'User', array($cartData['user_id'], 'MEDIUM')), CONF_DEF_CACHE_TIME, '.jpg');
                                    echo '<img src="' . $img . '" />';
                                }
                                ?>
                            </div>
                            <span class="-gap"></span>
                            <h3 class="-display-inline"><?php echo $cartData['user_first_name']; ?></h3>
                            <?php if ($cartData['user_country_id'] > 0) { ?>
                                <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($cartData['user_country_id'], 'DEFAULT')); ?>" alt=""></span>
                            <?php } ?>
                        </div>
                        <div class="tabled">
                            <?php if ($cartData['isFreeTrial'] == applicationConstants::NO && $cartData['lessonQty'] > 0) { ?>
                                <div class="tabled__cell">
                                    <span class="-color-light"><?php echo Label::getLabel('LBL_Language'); ?></span><br>
                                    <span class="cart-lang-id-js">
                                        <?php
                                        if (isset($teachLanguages[$cartData['languageId']])) {
                                            echo $teachLanguages[$cartData['languageId']];
                                        }
                                        ?>
                                    </span>
                                </div>
                            <?php } ?>
                            <div class="tabled__cell">
                                <span class="-color-light"><?php echo Label::getLabel('LBL_Duration'); ?></span><br>
                                <span class="cart-lesson-duration"> <?php echo sprintf(Label::getLabel('LBL_%s_Mins/Lesson'), $lessonDuration); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($cartData['isFreeTrial'] == applicationConstants::NO && $cartData['lessonQty'] > 0) { ?>
                    <div class="col-xl-8 col-lg-8 col-md-12">
                        <div class="box -padding-20" style="margin-bottom:30px;">
                            <h3><?php echo Label::getLabel('LBL_Languages_Offered'); ?></h3>
                            <p><?php echo Label::getLabel('LBL_Choose_Among_Languages_Offered!'); ?></p>
                            <div class="selection-list">
                                <ul>
                                    <?php foreach ($teachLanguages as $key => $teachLanguage) { ?>
                                        <li class="<?php echo ($cartData['languageId'] == $key) ? 'is-active' : ''; ?>">
                                            <label class="selection">
                                                <span class="radio">
                                                    <input onchange="updateCart(<?php echo $cartData['teacherId']; ?>);" type="radio" name="language" value="<?php echo $key; ?>" <?php echo ($cartData['languageId'] == $key) ? 'checked="checked"' : ''; ?>><i class="input-helper"></i>
                                                </span>
                                                <span class="selection__item">
                                                    <?php echo $teachLanguage; ?> <small class="-float-right"> </small>
                                                </span>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-12" id="booking-durations-js">
                        <div class="box -padding-20" style="margin-bottom:30px;">
                            <h3><?php echo Label::getLabel('LBL_Slot_Duration'); ?></h3>
                            <p><?php echo Label::getLabel('LBL_Choose_Duration_for_lesson'); ?></p>
                            <div class="selection-list">
                                <ul>
                                    <?php foreach ($bookingDurations as $lessonDuration) { ?>
                                        <li class="<?php echo ($cartData['lessonDuration'] == $lessonDuration) ? 'is-active' : ''; ?>">
                                            <label class="selection">
                                                <span class="radio">
                                                    <input onchange="updateCart(<?php echo $cartData['teacherId']; ?>);" type="radio" name="lessonDuration" value="<?php echo $lessonDuration; ?>" <?php echo ($cartData['lessonDuration'] == $lessonDuration) ? 'checked="checked"' : ''; ?>><i class="input-helper"></i>
                                                </span>
                                                <span class="selection__item">
                                                    <?php echo sprintf(Label::getLabel('LBL_%s_Mins/Lesson'), $lessonDuration); ?> <small class="-float-right"> </small>
                                                </span>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-12" id="price-slabs">
                    </div>
                <?php } ?>
                <div class="col-xl-4 col-lg-4 col-md-12 -clear-right">
                    <div class="box" style="margin-bottom: 30px;" id="financialSummaryListing">
                    </div>
                    <p class="-color-secondary">
                        <?php
                        $labelstr = Label::getLabel('LBL_*_All_Purchases_are_in_{default-currency-code}._Foreign_transaction_fees_might_apply,_according_to_your_bank\'s_policies');
                        echo str_replace("{default-currency-code}", CommonHelper::getSystemCurrencyData()['currency_code'], $labelstr);
                        ?> </p> <?php if (AttachedFile::getAttachment(AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE, 0, 0, CommonHelper::getLangId())) { ?> <div class="">
                            <img src="<?php echo FatUtility::generateFullUrl('Image', 'allowedPaymentGatewayImage', array(CommonHelper::getLangId())); ?>">
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="box" style="margin-bottom: 30px;" id="checkout-left-side">
                        <div id="paymentDiv">
                            <?php
                            $cartData['orderNetAmount'] = FatUtility::float($cartData['orderNetAmount']);
                            if (0 >= $cartData['orderNetAmount']) {
                                $this->includeTemplate('checkout/_partial/processWithNoPayment.php', $this->variables);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<script>
<?php
if ($cartData['orderNetAmount'] > 0) {
    echo 'loadPaymentSummary();';
}
if ($cartData['isFreeTrial'] == applicationConstants::NO && $cartData['lessonQty'] > 0) {
    ?>
        getTeacherPriceSlabs(<?php echo $cartData['languageId'] . ',' . $cartData['lessonDuration']; ?>);
<?php } ?>
</script>