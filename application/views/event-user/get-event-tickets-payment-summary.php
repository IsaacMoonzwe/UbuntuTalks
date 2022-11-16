<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step4
?>
<?php
$remainingWalletBalance = 0;
$walletCreditLabel = '';
$walletDeduction = 0;

if(isset($_SESSION['walletSummary'])){
    $cartData=$_SESSION['walletSummary'];

}
if ($userWalletBalance >= 0) {
    $walletCreditLabel = sprintf(Label::getLabel('LBL_Wallet_Credits_(%s)'), CommonHelper::displayMoneyFormat($userWalletBalance));
    $remainingWalletBalance = ($userWalletBalance - $cartData['orderNetAmount']);
    $remainingWalletBalance = ($remainingWalletBalance < 0) ? 0 : $remainingWalletBalance;
    if ($cartData['cartWalletSelected'] > 0) {
        $walletDeduction = $userWalletBalance;
        if ($cartData["cartWalletSelected"] && $cartData['orderNetAmount'] < $userWalletBalance) {
            $walletDeduction = $cartData['orderNetAmount'];
        }
        if(isset($_SESSION['walletSummary'])){
            if($remainingWalletBalance>0){
                $paymentMethods=[];
            }
            
        }
    }
}
if (isset($_SESSION['summary'])) {
    $cartData = $_SESSION['summary'];
} elseif (isset($_SESSION['removeCoupon'])) {
    $cartData = $_SESSION['removeCoupon'];
}
if (!empty($planResult['plan_image'])) {
    foreach ($planResult['plan_image'] as $testimonialImg) {
        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('EventUser', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
    }
}
?>
<div class="box box--checkout">
    <div class="box__head">

        <a href="javascript:void(0);" onclick="RegisterPlanEventUser(eventCart.props.sponsershipPlan,eventCart.props.countOfTickets);" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <h4><?php echo Label::getLabel('LBL_SELECT_PAYMENT_METHOD'); ?></h4>
        <!--  <?php if (1 > $cartData['grpclsId']) { ?>
            <div class="step-nav">
                <ul>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a> <span class="step-icon"></span></li>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a><span class="step-icon"></span></li>
                    <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
                </ul>
            </div>
            <?php } else { ?> -->
            <div class="step-nav">
                <ul>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                    <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a><span class="step-icon"></span></li>
                    <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>

                </ul>
            </div>
            <!-- <?php } ?> -->
        </div>
        <div class="box__body">
            <div class="selection-tabs selection--checkout selection--payment">
                <div class="row">

                    <?php if ($cartData['orderNetAmount'] > 0) {  ?>
                        <div class="col-md-6 col-xl-6">
                            <div class="selection-title">
                                <p><?php echo Label::getLabel('LBL_SELECT_A_PAYMENT_METHOD'); ?></p>
                            </div>
                            <div class="payment-wrapper">
                                <?php if ($userWalletBalance >= 0) { ?>
                                    <label class="selection-tabs__label selection--wallet">
                                        <input type="checkbox" class="selection-tabs__input" onChange="eventWalletSelection(this,<?php echo $userWalletBalance;?>,'registrationPlan');" <?php echo ($cartData["cartWalletSelected"]) ? 'checked="checked"' : ''; ?> name="pay_from_wallet">
                                        <div class="selection-tabs__title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                <g>
                                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                                </g>
                                            </svg>
                                            <div class="payment-type">
                                                <p><?php echo $walletCreditLabel; ?></p>
                                                <p class="is-selected">
                                                    <?php
                                                    if ($cartData["cartWalletSelected"] && $userWalletBalance >= $cartData['orderNetAmount']) {
                                                        echo Label::getLabel('LBL_Sufficient_balance_in_your_wallet');
                                                    } else {
                                                        echo sprintf(Label::getLabel('LBL_Wallet_Credits_(%s)'), CommonHelper::displayMoneyFormat($userWalletBalance));
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                            <div class="balance-payment">
                                                <ul>
                                                    <li>
                                                        <p><?php echo Label::getLabel('LBL_Payment_To_Be_Made'); ?></p>
                                                        <div class="space"></div>
                                                        <b><?php echo CommonHelper::displayMoneyFormat($cartData['orderNetAmount']); ?></b>
                                                    </li>
                                                    <li>
                                                        <p><?php echo Label::getLabel('LBL_Amount_In_Your_Wallet'); ?></p>
                                                        <div class="space"></div>
                                                        <b><?php echo CommonHelper::displayMoneyFormat($userWalletBalance); ?></b>
                                                    </li>
                                                    <li>
                                                        <p><?php echo Label::getLabel('LBL_Remaining_Wallet_Balance'); ?></p>
                                                        <div class="space"></div>
                                                        <b><?php echo CommonHelper::displayMoneyFormat($remainingWalletBalance); ?></b>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </label>
                                <?php } ?>
                                <?php $counter = 0; ?>
                                <?php foreach ($paymentMethods as $key => $value) { ?>
                                    <label class="selection-tabs__label payment-method-js">
                                        <input type="radio" class="selection-tabs__input" value="<?php echo $value['pmethod_id']; ?>" <?php echo empty($counter) ? 'checked' : ''; ?> name="payment_method">
                                        <div class="selection-tabs__title">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                <g>
                                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                                </g>
                                            </svg>
                                            <?php
                                            if ($value['pmethod_name'] == "PayPal Payments Standard") { ?>
                                                <img style="display: inline-block;max-width:70px;" src="../../../public/images/PayPal-Logo.png" alt="Paypal" />
                                            <?php } else if ($value['pmethod_name'] == "Stripe") { ?>
                                                <img style="display: inline-block;max-width:140px;" src="../../../public/images/stripe.svg" alt="Paypal" />
                                            <?php } else if ($value['pmethod_name'] == "Google Pay") { ?>
                                                <img style="display: inline-block;max-width:121px;height:
                                                64px" src="../../../public/images/GPay_Acceptance_Mark_800.png" alt="GooglePay" />
                                            <?php } else {
                                                echo $value['pmethod_name'];
                                            }

                                            ?>
                                        <!--<div class="payment-type">
                                                <p><?php #echo $value['pmethod_name']; 
                                                    ?></p>
                                                </div> ?> -->
                                            </div>
                                        </label>
                                        <?php $counter++; ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-6  <?php echo ($cartData['orderNetAmount'] > 0) ? ' col-xl-5 offset-xl-1' : 'col-xl-12'; ?>">
                            <div class="plan-image">
                                <?php echo $htmlAfterField; ?>
                            </div>
                            <div class="selection-title">
                                <p><?php echo Label::getLabel('LBL_Have_a_Coupon?'); ?></p>
                                <a href="javascript:void(0);" class="color-primary btn--link slide-toggle-coupon-js"><?php echo Label::getLabel('LBL_View_Coupons'); ?></a>
                            </div>
                            <div class="apply-coupon">
                                <svg class="icon icon--price-tag">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#price-tag'; ?>"></use>
                                </svg>
                                <input type="text" id="coupon_code" name="coupon_code" placeholder="<?php echo Label::getLabel('LBL_ENTER_COUPON_CODE'); ?>">
                                <a href="javascript:void(0);" onclick="eventApplyPromoCode(document.getElementById('coupon_code').value,'registrationPlan');" class="btn btn--secondary btn--small color-white"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                            </div>
                    <!-- <div class="apply-coupon">
                        <input type="text" id="referral" name="referral" placeholder="<?php //echo Label::getLabel('LBL_Enter_Referral_Name'); 
                                                                                        ?>">
                        <a href="javascript:void(0);" onclick="cart.applyPromoCode(document.getElementById('coupon_code').value);" class="btn--small color-white"></a>
                    </div> -->
                    <?php if (!empty($cartData['cartDiscounts']['coupon_code'])) { ?>
                        <div class="coupon-applied">
                            <div class="coupon-type">
                                <span class="bold-600 coupon-code"><?php echo $cartData['cartDiscounts']['coupon_code']; ?></span>
                                <p><?php echo Label::getLabel('LBL_COUPON_APPLIED'); ?></p>
                            </div>
                            <a href="javascript:void(0);" onclick="eventRemovePromoCode('registrationPlan');" class="btn btn--coupon btn--small"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                        </div>
                    <?php } ?>
                    <div class="selection-title">
                        <p><?php echo Label::getLabel('LBL_Summary'); ?></p>
                    </div>
                    <div class="payment-summary">
                        <div class="payment__row">
                            <div>
                                <b><?php echo $cartData['itemName']; ?></b>
                                <?php if (!empty($cartData['lessonQty'])) { ?>
                                    <p><?php //echo str_replace(['{lesson-qty}', '{duration}'], [$cartData['lessonQty'], $cartData['lessonDuration']], Label::getLabel('LBL_Lesson_Count:_{lesson-qty}_Lesson(s)_Duration:_{duration}_Mins/lesson')); 
                                ?></p>
                                    <p><?php //echo str_replace('{item-price}', CommonHelper::displayMoneyFormat($cartData['itemPrice']), Label::getLabel('LBL_Item_Price:_{item-price}/lesson')); 
                                ?></p>
                                <?php if (!empty($cartData['tlanguage_name'])) { ?>
                                        <p><?php //echo str_replace('{teach-language}', $cartData['tlanguage_name'], Label::getLabel('LBL_TEACH_LANGUAGE_:_{teach-language}')); 
                                    ?></p>
                                    <?php
                                }
                            }
                            if (!empty($cartData['startDateTime']) && !empty($cartData['endDateTime'])) {
                                $userTimezone = MyDate::getUserTimeZone();
                                $systemTimeZone = MyDate::getTimeZone();
                                $startDateTime = MyDate::changeDateTimezone($cartData['startDateTime'], $systemTimeZone, $userTimezone);
                                $endDateTime = MyDate::changeDateTimezone($cartData['endDateTime'], $systemTimeZone, $userTimezone);
                                echo '<p>' . date("M d, Y h:i A", strtotime($startDateTime)) . ' - ' . date("h:i A", strtotime($endDateTime)) . '</p>';
                            }
                            ?>
                        </div>
                        <div>
                            <b><?php echo CommonHelper::displayMoneyFormat($cartData['cartTotal']); ?></b>
                        </div>
                    </div>
                    <?php if (!empty($cartData['cartDiscounts'])) { ?>
                        <div class="payment__row">
                            <div>
                                <b><?php echo Label::getLabel('LBL_COUPON_DISCOUNT'); ?></b>
                            </div>
                            <div>
                                <b><?php echo '-' . CommonHelper::displayMoneyFormat($cartData['cartDiscounts']['coupon_discount_total']); ?></b>
                            </div>
                        </div>
                        <?php
                    }
                    if ($cartData['cartWalletSelected'] > 0) {
                        ?>
                        <div class="payment__row">
                            <div>
                                <b><?php echo Label::getLabel('LBL_WALLET_DEDUCTION'); ?></b>
                            </div>
                            <div>
                                <b><?php echo '-' . CommonHelper::displayMoneyFormat($walletDeduction); ?></b>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="payment__row">
                        <div>
                            <b class="color-primary"><?php echo Label::getLabel('LBL_Total'); ?></b>
                        </div>
                        <div>
                            <b class="color-primary NR"><?php echo CommonHelper::displayMoneyFormat($cartData['orderNetAmount'] - $walletDeduction); ?></b>
                        </div>
                    </div>
                </div>
                <div class="coupon-box slide-target-coupon-js">
                    <?php
                    foreach ($EventTicketsCouponCodeFinalListing as $value) {
                        if ($value['coupon_end_date'] > date('Y-m-d')) {
                            ?>
                            <div class="coupon-box__head">
                                <p><?PHP echo Label::getLabel('LBL_AVAILABLE_COUPONS'); ?></p>
                                <a href="javascript:void(0);" class="btn btn--bordered color-black btn--close">
                                    <svg class="icon icon--close">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#close'; ?>"></use>
                                    </svg>
                                </a>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="coupon-box__head">
                                <p><?PHP echo Label::getLabel('LBL_NOT_AVAILABLE_COUPONS'); ?></p>
                                <a href="javascript:void(0);" class="btn btn--bordered color-black btn--close">
                                    <svg class="icon icon--close">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#close'; ?>"></use>
                                    </svg>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="coupon-box__body">
                        <?php foreach ($couponsList as $key => $coupon) { ?>
                            <div class="coupon-list">
                                <div class="coupon-list__head">
                                    <span class="badge color-secondary"><?php echo $coupon['coupon_code']; ?></span>
                                    <a href="javascript:void(0);" onclick="cart.applyPromoCode('<?php echo $coupon['coupon_code']; ?>');" class="btn btn--coupon btn--small color-primary"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                </div>
                                <div class="coupon-list__content">
                                    <p class="bold-600"><?php echo $coupon['coupon_title']; ?></p>
                                    <?php if (!empty($coupon['coupon_description'])) { ?>
                                        <p><?php echo $coupon['coupon_description']; ?> </p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <button href="javascript:void(0);" onclick="eventCart.confirmOrder(0, this);" class="btn btn--primary btn--large btn--block color-white"><?php echo Label::getLabel('LBL_CONFIRM_PAYMENT'); ?></button>
                <p class="payment-note color-secondary">
                    <?php echo Label::getLabel('LBL_Registration_Payment_Summery');
                        // $labelstr = Label::getLabel('LBL_*_All_Purchases_are_in_{default-currency-code}._Foreign_transaction_fees_might_apply,_according_to_your_bank\'s_policies');
                        // echo str_replace("{default-currency-code}", CommonHelper::getSystemCurrencyData()['currency_code'], $labelstr);
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    $('.slide-toggle-coupon-js').click(function(e) {
        e.preventDefault();
        $(this).parent('.toggle-dropdown').toggleClass("is-active");
    });
    $(".slide-toggle-coupon-js").click(function() {
        $(".slide-target-coupon-js").slideToggle();
    });
    $('.btn--close').click(function() {
        $('.slide-target-coupon-js').slideUp("slow");
    });
    $("#referral").on("input", function() {
        var referral = ($(this).val());
        console.log(referral);
        cart.referralName = referral;
    });
</script>