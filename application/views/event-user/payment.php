<?php
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
    <style>
        .selection-tabs__title {
            cursor: pointer;
            width: 100%;
            height: auto;
        }

        .hide-section {
            display: none !important;
        }

        .wallet-credits {
            width: 100%;
        }

        label.selection-tabs__label.selection--wallet.wallet-section {
            width: 100%;
        }

        label.selection-tabs__label.payment-method-js {
            width: 100%;
        }

        .selection-tabs__title svg {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php
    if (!empty($planResult['plan_image'])) {
        foreach ($planResult['plan_image'] as $testimonialImg) {
            $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('EventUser', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
        }
    }
    ?>
    <section class="payment-details" id="paymentData">
        <div class="container">
            <div class="row">
                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="personal-details">
                                <div class="payment_block_title-item">
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

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1">
                                        <label class="form-check-label" for="exampleRadios1">Personal Details</label>
                                        <a href="JavaScript:Void(0);" class="edit-btn hide-btn">Show</a>
                                    </div>
                                </div>
                                <div class="payment-details-block personal-details-block">
                                    <form action="#">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="First Name" value="<?php echo $EventUserListingDetails['user_first_name']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="Last Name" value="<?php echo $EventUserListingDetails['user_last_name']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="Address" value="<?php echo $EventUserListingDetails['user_address1']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="City" value="<?php echo $EventUserListingDetails['user_address2']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="Country" value="<?php echo $EventUserListingDetails['user_billing_country']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" value="<?php echo $EventUserListingDetails['user_phone']; ?>" placeholder="Phone Number" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="Zip Code" value="<?php echo $EventUserListingDetails['user_zip']; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <input type="" class="form-control" id="" aria-describedby="" placeholder="Email Id" value="<?php echo $userCrendentialData['credential_email']; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="button-box">
                                            <a href="JavaScript:Void(0);" class="btn-green continue-btn">Continue</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="payment-method" id="cart_payment_data">
                                <div class="payment_block_title-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option2">
                                        <label class="form-check-label" for="exampleRadios1">
                                            Choose Payment Method
                                        </label>
                                    </div>
                                </div>
                                <div class="payment-details-block hide-block">
                                    <form action="#">
                                        <ul class="nav nav-tabs" id="paymenttab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="fas fa-credit-card"></i>
                                                    <span>Card</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="paymenttabContent">
                                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                                <div class="row">
                                                    <div class="col-md-12 col-xl-12">
                                                        <div class="payment-wrapper">
                                                            <?php if ($userWalletBalance >= 0) { ?>
                                                                <label class="selection-tabs__label selection--wallet wallet-section">
                                                                    <input type="checkbox" class="selection-tabs__input" onChange="eventWalletSelection(this,<?php echo $userWalletBalance; ?>,'event_payment');" <?php echo ($cartData["cartWalletSelected"]) ? 'checked="checked"' : ''; ?> name="pay_from_wallet">
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
                                                            <div class="extra_pay_method" id="extra_pay_method">
                                                                <?php $counter = 0; ?>
                                                                <?php
                                                                $amount = ($cartData['orderNetAmount'] - $walletDeduction);
                                                                if ($amount > 5) { ?>

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
                                                                                    <img id="paypal-payment" style="display: inline-block;max-width:70px;" src="../../../public/images/PayPal-Logo.png" alt="Paypal" />
                                                                                <?php } else if ($value['pmethod_name'] == "Stripe") { ?>
                                                                                    <img style="display: inline-block;max-width:140px;" src="../../../public/images/stripe.svg" alt="Paypal" />
                                                                                <?php } else if ($value['pmethod_name'] == "Google Pay") { ?>
                                                                                    <img id="google-pay" style="display: inline-block;max-width:70px;" src="../../../public/images/GPay_Acceptance_Mark_800.png" alt="GooglePay" />
                                                                                <?php } else if ($value['pmethod_name'] == "Airtel") { ?>
                                                                                    <img style="display: inline-block;max-width:70px;" src="../../../public/images/airtel.jpg" alt="Airtel" />
                                                                                <?php } else {
                                                                                    echo $value['pmethod_name'];
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </label>
                                                                        <?php $counter++; ?>
                                                                <?php }
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php //} 
                                                    ?>
                                                    <div class="col-md-6 <?php echo ($cartData['orderNetAmount'] > 0) ? ' col-xl-5 offset-xl-1' : 'col-xl-12'; ?>">
                                                        <div class="plan-image">
                                                            <?php //echo $htmlAfterField; 
                                                            ?>
                                                        </div>

                                                    </div>
                                                    <span style="display:none;" class="symbol">$</span>
                                                    <a href="javascript:void(0);" onclick="eventCart.confirmOrder(0, this);" class="btn btn--primary btn--large btn--block color-white"><?php echo Label::getLabel('LBL_CONFIRM_PAYMENT'); ?></a>

                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="mobile_method_selection_box">
                                                            <ul>
                                                                <?php
                                                                foreach ($paymentMethods as $key => $value) {
                                                                ?>
                                                                    <li>
                                                                        <?php if ($value['pmethod_name'] == "Google Pay") { ?>
                                                                            <a href="#">
                                                                                <img src="../../../public/images/GPay_Acceptance_Mark_800.png" alt="">
                                                                            </a>
                                                                    </li>
                                                                <?php } ?>
                                                            <?php } ?>

                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <div class="terms-and-pay">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                                                <label class="form-check-label" for="defaultCheck1">
                                                                    I agree to DPO’s <a href="#">terms and
                                                                        conditions</a>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="button-box">
                                                            <a href="#" class="btn-green">Pay via Mobile</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="church-accordian">
                                <div id="accordion">
                                    <div class="card">
                                        <div class="card-header" id="church-accordian">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    <p class="location-title">
                                                        <span>Mulungushi International Conference Centre. <br>Lusaka, Zambia</span>
                                                        <i class="fas fa-chevron-down"></i>
                                                    </p>
                                                </button>
                                            </h5>
                                            <div class="location-title venue">
                                                <span>WEBSITE:&nbsp;&nbsp;</span><a href="#">https://www.micc.co.zm</a>
                                            </div>
                                        </div>

                                        <div id="collapseOne" class="collapse show" aria-labelledby="church-accordian" data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="sitename">
                                                    <h5>Ubuntu Talks</h5>
                                                </div>
                                                <p>Events Ticketing - <?php echo $cartData['itemName']; ?></p>
                                                <div>
                                                    <span>WEBSITE:</span><a href="#">https://ubuntutalks.com</a>
                                                </div>
                                                <div>
                                                    <span>E-MAIL:</span><a href="#">admin@ubuntutalks.com</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bottom-payment-box">
                                <div>
                                    <span>SERVICE DATE</span>
                                    <p><?php echo $today = date("D M j G:i:s T Y");  ?></p>
                                </div>
                            </div>


                            <div class="total-box" id="pay_total_box">
                                <div class="payment-total">
                                    <h3>Total</h3>
                                    <div class="input-group">
                                        <select class="form-control" id="exampleFormControlSelect1">
                                            <option><?php echo $cartData['currency']; ?></option>
                                        </select>
                                        <?php $amount = ($cartData['orderNetAmount'] - $walletDeduction); ?>
                                        <input type="text" class="form-control" id="total_cart" value="<?php echo number_format((float)$amount, 2, '.', ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        jQuery(document).on('click', '.continue-btn', function() {
            $(".payment-details-block").removeClass("hide-block");
            $(".personal-details-block").addClass("hide-block");
            $(".edit-btn").removeClass("hide-btn");
        });

        jQuery(document).on('click', '.edit-btn', function() {
            $(".payment-details-block").addClass("hide-block");
            $(".personal-details-block").removeClass("hide-block");
            $(".edit-btn").addClass("hide-btn");
        });
        $(document).ready(function() {
            eventCart.props.currency = '<?php echo $cartData['currency']; ?>';
            eventCart.props.currencyCode = '<?php echo $cartData['currencyCode']; ?>';
        });
        var data = '<?php echo $cartData['currency']; ?>';
        $.loader.show();
        if (data == 'ZMW') {
            console.log("data", data);
            $('#paypal-payment').parents('label').addClass('hide-section');
            $('#google-pay').parents('label').addClass('hide-section');
            $('.wallet-section').addClass('hide-section');
        } else {
            $('#paypal-payment').parents('label').removeClass('hide-section');
            $('#google-pay').parents('label').removeClass('hide-section');
            $('.wallet-section').removeClass('hide-section');
        }
        $.loader.hide();
    </script>
</body>

</html>