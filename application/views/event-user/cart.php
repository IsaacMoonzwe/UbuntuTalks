<?php
$checkoutCart = $_SESSION['checkoutCart'];
$ticketQty = $checkoutCart['ticketQty'];
$plan = $checkoutCart['plan'];
$currency = $checkoutCart['currency'];
$currencyCode = $checkoutCart['currencyCode'];
$remainingWalletBalance = 0;
$walletCreditLabel = '';
$walletDeduction = 0;
$checkoutCart = $_SESSION['checkoutCart'];
if (isset($_SESSION['walletSummary'])) {
    $cartData = $_SESSION['walletSummary'];
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
        if (isset($_SESSION['walletSummary'])) {
            if ($remainingWalletBalance > 0) {
            }
        }
    }
}

if (isset($_SESSION['summary'])) {
    $_SESSION['cart'] = $_SESSION['summary'];
    $_SESSION['cart']['cartDiscounts'] = $_SESSION['summary']['cartDiscounts'];
    $_SESSION['checkoutCart']['cart']['cartDiscounts'] = $_SESSION['summary']['cartDiscounts'];
    $cartData = $_SESSION['cart'];
} elseif (isset($_SESSION['removeCoupon'])) {
    $cartData = $_SESSION['cart'];
}

$cartData['currency'] = $checkoutCart['currency'];
$cartData['currencyCode'] = $checkoutCart['currencyCode'];
if (!empty($planResult['plan_image'])) {
    foreach ($planResult['plan_image'] as $testimonialImg) {
        $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('EventUser', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000&display=swap" rel="stylesheet">

    <!-- icon -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

</head>
<style>
    .hide-total-block {
        display: none !important;
    }
</style>

<body>
    <section class="cart section">
        <div class="container container--narrow">
            <div class="row">
                <div class="col-lg-8 col-md-12 ticket-information">
                    <div class="cart-top-div">
                        <div class="backbtn">
                            <a href="javascript:history.go(-1)" class="btn btn--bordered color-black btn--back Cartbackbtn">
                                <svg class="icon icon--back">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_BACK'); ?>
                            </a>
                        </div>

                        <?php if (EventUserAuthentication::isUserLogged()) {
                            $fullName = $userDetails['user_full_name'];
                        ?>
                            <div class="user-information">
                                <div class="username">
                                    <p><i class='fas fa-user-alt'></i></p>
                                    <p><?php echo $fullName ?></p>
                                </div>
                                <div class="logout">
                                    <p><i class="fa fa-sign-out"></i></p>
                                    <p><a href="<?php echo CommonHelper::generateUrl('EventUser', 'logout'); ?>">Logout</a></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <form class="cart-form" method="post" name="cart_form">
                        <div class="table-box">
                            <table class="table" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="product-remove"></th>
                                        <th class="product-thumbnail"></th>
                                        <th class="product-name"><?php echo Label::getLabel('LBL_Product'); ?></th>
                                        <th class="product-price"><?php echo Label::getLabel('LBL_Price'); ?></th>
                                        <th class="product-quantity"><?php echo Label::getLabel('LBL_Quantity'); ?></th>
                                        <th class="product-subtotal"><?php echo Label::getLabel('LBL_SubTotal'); ?></th>
                                </thead>
                                </tr>
                                <tbody>
                                    <?php if (isset($EventsList)) { ?>
                                        <tr class="cart-form__cart-item cart_item">
                                            <td class="product-remove">
                                                <a href="" onclick="removeFromCart(<?php echo $EventsList['three_reasons_id']; ?>)" class="remove" aria-label="Remove this item" data-product_id="265" data-product_sku="">×</a>
                                            </td>
                                            <td class="product-thumbnail">
                                                <a href=""><img width="300" height="200" src="https://ubuntutalks.com/image/editor-image/1660102886-img1.jpg" class="" alt="" decoding="async" loading="lazy"></a>
                                            </td>
                                            <td class="product-name" data-title="Product">
                                                <dl class="variation">
                                                    <dd class="variation-Event">
                                                        <p><?php echo $EventsList['registration_plan_title']; ?></p>
                                                    </dd>
                                                </dl>
                                            </td>
                                            <td class="product-price" data-title="Price">
                                                <span class=""><bdi><span class=""></span><?php echo $currencyCode . $EventsList['itemPrice']; ?></bdi></span>
                                            </td>
                                            <td class="quantity-count" data-title="Quantity">
                                                <div class="quantity">
                                                    <input type="number" class="input-text qty text" id="cartTickets" name="cartTickets" value=<?php echo $ticketQty; ?> title="Qty" size="4" min="1" max="1500" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                                                </div>
                                            </td>
                                            <td class="subtotal-amount" data-title="Subtotal">
                                                <span class="amount"><bdi>
                                                        <p id="itemPrice"><?php echo $currencyCode . $EventsList['itemNetPrice']; ?></p>
                                                    </bdi></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="update_cart">
                                                <a onclick="UpdateCart();" class="btn-green cart-empty" id="update_cart" name="update_cart" aria-disabled="false">Update cart</a>
                                                <input type="hidden" value=""><input type="hidden" name="_wp_http_referer" value="/cart/">
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr class="cart-form__cart-item cart_item">
                                            <td colspan="6">Cart is empty<br><a href="/events" class="event-design">Return to Event</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="paymentcoupon">
                                <div id="coupon_data" class="coupon_data">
                                    <?php
                                    $amount = ($cartData['orderNetAmount'] - $walletDeduction);
                                    if ($amount > 5) { ?>
                                        <div class="selection-title">
                                            <p><?php echo Label::getLabel('LBL_Have_a_Coupon?'); ?></p>
                                            <a href="javascript:void(0);" class="color-primary btn--link slide-toggle-coupon-js"><?php echo Label::getLabel('LBL_View_Coupons'); ?></a>
                                        </div>

                                        <div class="apply-coupon">
                                            <svg class="icon icon--price-tag">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#price-tag'; ?>"></use>
                                            </svg>
                                            <input type="text" id="coupon_code" name="coupon_code" placeholder="<?php echo Label::getLabel('LBL_ENTER_COUPON_CODE'); ?>">
                                            <a href="javascript:void(0);" onclick="eventPlanApplyPromoCode(document.getElementById('coupon_code').value);" class="btn btn--secondary btn--small color-white"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($cartData['cartDiscounts']['coupon_code'])) { ?>
                                        <div class="coupon-applied">
                                            <div class="coupon-type">
                                                <span class="bold-600 coupon-code"><?php echo $cartData['cartDiscounts']['coupon_code']; ?></span>
                                                <p><?php echo Label::getLabel('LBL_COUPON_APPLIED'); ?></p>
                                            </div>
                                            <a href="javascript:void(0);" onclick="eventPlanRemovePromoCode('registrationPlan');" class="btn btn--coupon btn--small"><?php echo Label::getLabel('LBL_REMOVE'); ?></a>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($cartData['cartDiscounts'])) { ?>
                                        <div class="payment__row">
                                            <div>
                                                <b><?php echo Label::getLabel('LBL_COUPON_DISCOUNT'); ?></b>
                                            </div>
                                            <div>
                                                <b><?php echo '-' .  $cartData['currencyCode'] . ' ' . $cartData['cartDiscounts']['coupon_discount_total']; ?></b>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    if ($walletDeduction > 0) {
                                    ?>
                                        <div class="payment__row">
                                            <div>
                                                <b><?php echo Label::getLabel('LBL_WALLET_DEDUCTION'); ?></b>
                                            </div>
                                            <div>
                                                <b><?php echo '-' . $cartData['currencyCode'] . ' ' . $walletDeduction; ?></b>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="payment__row hide-total-block">
                                        <div>
                                            <b class="color-primary"><?php echo Label::getLabel('LBL_Total'); ?></b>
                                        </div>
                                        <div>
                                            <?php echo CommonHelper::displayMoneyFormat($cartData['orderNetAmount'] - $walletDeduction); ?></b> -->
                                            <b class="color-primary">
                                                <span class="symbol"><?php echo  $cartData['currencyCode']; ?></span>
                                                <?php
                                                $amount = ($cartData['orderNetAmount'] - $walletDeduction);
                                                echo number_format((float)$amount, 2, '.', '');
                                                ?>
                                            </b>
                                        </div>
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
                        </div>
                    </form>
                    <div id="cartTotal">
                        <div class="cart_totals">
                            <h2>Cart Total</h2>
                            <table cellspacing="0" class="shop_table shop_table_responsive cartMobile">
                                <tbody>
                                    <tr class="cart-subtotal">
                                        <th>Subtotal</th>
                                        <td data-title="Subtotal">
                                            <span class="Price-amount amount">
                                                <bdi><span class="Price-currencySymbol"></span>
                                                    <?php
                                                    $amount = ($cartData['orderNetAmount'] - $walletDeduction);
                                                    echo $currencyCode . number_format((float)$amount, 2, '.', '');
                                                    ?>
                                                </bdi>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="order-total">
                                        <th>Total</th>
                                        <td data-title="Total">
                                            <strong>
                                                <span class="Price-amount amount">
                                                    <bdi><span class="Price-currencySymbol"></span>
                                                        <?php
                                                        $amount = ($cartData['orderNetAmount'] - $walletDeduction);
                                                        echo $currencyCode . number_format((float)$amount, 2, '.', '');
                                                        ?>
                                                    </bdi>
                                                </span>
                                            </strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="proceed-to-checkout">
                        <?php if (isset($EventsList)) { ?>
                            <a href="<?php echo CommonHelper::generateUrl('EventUser', 'Checkout', []); ?>" class="checkout-button btn-green">
                                Proceed to checkout</a>
                        <?php } else { ?>
                            <a href="javascript:void(0);" class="checkout-button btn-green cart-empty">
                                Proceed to checkout</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script>
    eventCart.props.countOfTickets = <?php echo $ticketQty; ?>;
    eventCart.props.sponsershipPlan = <?php echo $plan; ?>;
    $("#update_cart").off('click');
    $('#cartTickets').change(function() {
        $('#update_cart').removeClass('cart-empty');
        eventCart.props.countOfTickets = this.value;
        console.log("hi==", eventCart.props.countOfTickets);
        $("#update_cart").on('click');
    });

    function updateCartData() {
        var qty = $('#cartTickets').val();
        updateCart();
    }
</script>

</html>