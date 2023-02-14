<?php
$checkoutCart = $_SESSION['checkoutCart'];
$currencyCode = $checkoutCart['currencyCode'];
$loggedIn = false;
if (isset($userData)) {
    $loggedIn = true;
}
$cartData = $_SESSION['cart'];
?>
<style>
    .hide-form {
        display: none;
    }

    .show-form {
        display: block;
    }

    label.error.fail-alert {
        border: 2px solid red;
        border-radius: 4px;
        line-height: 1;
        padding: 2px 0 6px 6px;
        background: #ffe6eb;
    }

    input.valid.success-alert {
        border: 2px solid #4CAF50;
        color: green;
    }

    @media (max-width: 768px) {

        body table.shop_table.checkout-review-order-table.checkout tr,
        th,
        td {
            width: 173px !important;
        }
    }
</style>
<!-- icon -->
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
<section class="checkout-details section">

    <div class="container container--narrow">
        <div class="row">
            <div class="col-lg-8 ticket-information">
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
                                <p><a href="<?php echo CommonHelper::generateUrl('EventUser', 'logout'); ?>"><?php echo Label::getLabel('LBL_Logout'); ?></a></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-lg-6 info_form_div">
                        <div class="billing-details-box">
                            <form id="billing" name="billing" action="#">
                                <h3 class="form_head"><?php echo Label::getLabel('LBL_Billing_Detail'); ?></h3>
                                <div class="row Registration-Form">
                                    <?php if (isset($loggedIn) && $loggedIn == false) { ?>
                                        <div class="login-registration">
                                            <h6>
                                                <div class="login-design">Already Have Account ? <span class="click-btn">Login</span></div>
                                            </h6>
                                        </div>
                                    <?php } ?>
                                    <div class="col-lg-6" style="display: none;">
                                        <div class="form-group">
                                            <label for="first_Name"><?php echo Label::getLabel('LBL_First_Name'); ?><span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" name="firstName" id="firstName" aria-describedby="" placeholder="" value="<?php if (isset($loggedIn)) echo $userData['user_first_name'];  ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="lastName"><?php echo Label::getLabel('LBL_First_Name'); ?> <span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" name="middleName" id="middleName" aria-describedby="" placeholder="" value="<?php if (isset($loggedIn)) echo $userData['user_first_name']; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="lastName"><?php echo Label::getLabel('LBL_Last_Name'); ?> <span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" name="lastName" id="lastName" aria-describedby="" placeholder="" value="<?php if (isset($loggedIn)) echo $userData['user_last_name']; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="companyName"><?php echo Label::getLabel('LBL_Company_Name'); ?>(optional)</label>
                                            <input type="text" class="form-control" name="companyName" id="companyName" aria-describedby="" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group address">
                                            <label for="citeyTown"><?php echo Label::getLabel('LBL_Town_/_City'); ?><span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" name="cityTown1" id="citeyTown" aria-describedby="" placeholder="House number and street name" value="<?php if (isset($loggedIn))  echo $userData['user_address1']; ?>">
                                            <input type="text" class="form-control" name="cityTown2" id="citeyTown" aria-describedby="" placeholder="Apartment, suite, unit, etc. (optional) " value="<?php if (isset($loggedIn)) echo $userData['user_address2']; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="stateCounty"><?php echo Label::getLabel('LBL_Country'); ?> <span style="color:red;">*</span></label>
                                            <select class="form-control" name="stateCounty" id="stateCounty">
                                                <?php foreach ($CountryListing as $value) { ?>
                                                    <option id="<?php echo $value['countrylang_country_id']; ?>" value="<?php echo $value['country_name']; ?>"><?php echo $value['country_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="postcode"><?php echo Label::getLabel('LBL_Postcode_/_Zipcode'); ?> <span style="color:red;">*</span></label>
                                            <input type="text" class="form-control" name="postcode" id="postcode" aria-describedby="" placeholder="" value="<?php if (isset($loggedIn)) echo $userData['user_zip']; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="phone"><?php echo Label::getLabel('LBL_Phone'); ?> <span style="color:red;">*</span></label>
                                            <input type="phone" class="form-control" name="phone" id="phone" aria-describedby="" placeholder="" value="<?php if (isset($loggedIn)) echo $userData['user_phone']; ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email-address"><?php echo Label::getLabel('LBL_Email_Address'); ?> <span style="color:red;">*</span></label>
                                            <input type="email" class="form-control" name="email-address" id="email-address" aria-describedby="" <?php if ($loggedIn == true) echo "readOnly ";  ?>placeholder="" value="<?php if (isset($loggedIn)) echo $userCrendentialData['credential_email']; ?>">
                                        </div>
                                    </div>
                                    <?php if (!isset($_SESSION['Event_userId'])) {
                                    ?>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="email-address"><?php echo Label::getLabel('LBL_Create_Password'); ?><span style="color:red;">*</span></label>
                                                <input type="password" class="form-control" name="password" id="password" aria-describedby="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="email-address"><?php echo Label::getLabel('LBL_Confirm_Password'); ?> <span style="color:red;">*</span></label>
                                                <input type="password" class="form-control" name="conf_new_password" id="conf_new_password" aria-describedby="" placeholder="">
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>

                                <div class="row Login-Form hide-form">
                                    <div class="col-lg-12">
                                        <input type="hidden" value="reg" name="login" id="login" />
                                        <div class="form-group">
                                            <label for="email-address"><?php echo Label::getLabel('LBL_Email_/_Username'); ?> <span style="color:red;">*</span></label>
                                            <input type="email" class="form-control" name="user_email" id="user_email" aria-describedby="" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email-address"><?php echo Label::getLabel('LBL_Password'); ?><span style="color:red;">*</span></label>
                                            <input type="password" class="form-control" name="user_password" id="user_password" aria-describedby="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="box-bottom">
                                            <button type="button" onclick="submitForm();" class="btn-green submit-order"><?php echo Label::getLabel('LBL_Submit'); ?></button>

                                        </div>
                                    </div>
                                    <?php if (isset($loggedIn) && $loggedIn == false) { ?>
                                        <div class="login-registration">
                                            <h6>
                                                <div class="registration-details"><?php echo Label::getLabel('LBL_Create_a_new_account'); ?> <span class="reg-btn"><?php echo Label::getLabel('LBL_Register'); ?></span></div>
                                            </h6>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 additional_info_div">
                        <div class="additional-information">
                            <form id="checkout" name="checkout" action="" method="post">
                                <?php
                                $i = 1;
                                $attendeeName = 1;
                                $attendeeEmail = 1;
                                $phoneNumber = 1;
                                $gender = 1;
                                $church = 1;
                                $Food = 1;
                                $ticket = 1;
                                for ($i = 0; $i < $checkoutCart['ticketQty']; $i++) {
                                ?>
                                    <div class="Ticket-Form">
                                        <h3>Ticket <?php echo $ticket; ?>: <br><?php echo $EventsList['registration_plan_title']; ?></h3>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="attendeeName"><?php echo Label::getLabel('LBL_Attendee_Full_Name'); ?> <span style="color:red;">*</span></label>
                                                    <input type="text" class="form-control" id="attendeeName[]" name="attendeeName[]" aria-describedby="" placeholder="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="attendeeEmail"><?php echo Label::getLabel('LBL_Attendee_Email'); ?><span style="color:red;">*</span></label>
                                                    <input type="email" class="form-control" id="attendeeEmail[]]" name="attendeeEmail[]" aria-describedby="" required placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="phoneNumber"><?php echo Label::getLabel('LBL_Phone_Number'); ?><span style="color:red;">*</span></label>
                                                    <input type="phone" class="form-control" id="phoneNumber[]" name="phoneNumber[]" required aria-describedby="" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="phoneNumber"><?php echo Label::getLabel('LBL_Gender'); ?><span style="color:red;">*</span></label>
                                                    <select required class="form-control" id="gender[]" name="gender[]">
                                                        <option><?php echo Label::getLabel('LBL_Male'); ?></option>
                                                        <option><?php echo Label::getLabel('LBL_Female'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="church"><?php echo Label::getLabel('LBL_Organization'); ?><span style="color:red;">*</span></label>
                                                    <input required type="text" class="form-control" id="church[]" name="church[]" aria-describedby="" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="church"><?php echo Label::getLabel('LBL_Food_Option'); ?><span style="color:red;">*</span></label>
                                                    <select required class="form-control" id="Food[]" name="Food[]">
                                                        <option><?php echo Label::getLabel('LBL_Chiken'); ?></option>
                                                        <option><?php echo Label::getLabel('LBL_Beef'); ?></option>
                                                        <option><?php echo Label::getLabel('LBL_Vegetarian'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $attendeeName++;
                                    $attendeeEmail++;
                                    $phoneNumber++;
                                    $gender++;
                                    $church++;
                                    $Food++;
                                    $ticket++;
                                }
                                ?>
                        </div>
                    </div>
                </div>
                <div class="order_review_heading">
                    <h3><?php echo Label::getLabel('LBL_Your_Order'); ?></h3>
                </div>
                <div class="order_review">
                    <table class="shop_table checkout-review-order-table checkout">
                        <thead>
                            <tr>
                                <th class="product-name"><?php echo Label::getLabel('LBL_Product'); ?></th>
                                <th class="product-total"><?php echo Label::getLabel('LBL_Subtotal'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="cart_item">
                                <td class="product-name">
                                    <?php echo $EventsList['registration_plan_title']; ?>&nbsp; <strong class="product-quantity">×&nbsp;<?php echo $checkoutCart['ticketQty']; ?></strong>
                                    <dl class="variation">
                                        <dt class="variation-Event"><?php echo Label::getLabel('LBL_Event'); ?>:</dt>
                                        <dd class="variation-Event">
                                            <p><?php echo $EventsList['registration_plan_title']; ?></p>
                                        </dd>
                                    </dl>
                                </td>
                                <td class="product-total">
                                    <span class="Price-amount amount"><bdi><span class="Price-currencySymbol_count">ZK</span><?php echo $EventsList['itemNetPrice']; ?></bdi></span>
                                </td>
                            </tr>

                            <?php if (!empty($cartData['cartDiscounts'])) { ?>
                                <tr>
                                    <td>
                                        <div class="payment__row">
                                            <div>
                                                <b class="ccode"><?php echo Label::getLabel('LBL_COUPON_DISCOUNT'); ?></b>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <b><?php echo '-' .  $cartData['currencyCode'] . ' ' . $cartData['cartDiscounts']['coupon_discount_total']; ?></b>
                                        </div>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="cart-subtotal">
                                <th><?php echo Label::getLabel('LBL_Subtotal'); ?></th>
                                <td><span class="Price-amount amount"><bdi><span class="Price-currencySymbol_sub">ZK</span><?php echo $cartData['cartTotal']; ?></bdi></span>
                                </td>
                            </tr>
                            <tr class="order-total">
                                <th><?php echo Label::getLabel('LBL_Total'); ?></th>
                                <td><strong><span class="Price-amount amount"><bdi><span class="Price-currencySymbol_total">ZK</span><?php echo  $cartData['orderNetAmount']; ?></bdi></span></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="checkout-payment">
                    <div class="box-bottom">
                        <p>Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our <a href="#">privacy policy</a>.</p>
                        <a href="javascript:void(0)" onClick="formData();" class="btn-green submit-order">Place order</a>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</section>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        var userCountry = '<?php echo $userData['user_country_id']; ?>';
        if (userCountry != null) {
            $('#stateCounty option[id=' + userCountry + ']').attr("selected", "selected");
        }
    });
    jQuery(document).on('click', '.click-btn', function() {
        $(".Registration-Form").addClass("hide-form");
        $(".Login-Form").removeClass("hide-form");
        $('.form_head').html('Login Details');
        $(".additional_info_div").addClass("hide-form");
        $('#login').val('login');
    });
    jQuery(document).on('click', '.reg-btn', function() {
        $(".Registration-Form").removeClass("hide-form");
        $(".additional_info_div").removeClass("hide-form");
        $(".Login-Form").addClass("hide-form");
        $('.form_head').html('Billing Details');
        $('#login').val('reg');
    });

    function submitForm() {
        ValidateLogin();
    }
    var fullName = '<?php echo $userDetails['user_full_name']; ?>';
    if (fullName != null) {
        document.getElementsByName('attendeeName[]')[0].value = fullName;
        document.getElementsByName('attendeeEmail[]')[0].value = '<?php echo $userCrendentialData['credential_email']; ?>';
        document.getElementsByName('phoneNumber[]')[0].value = '<?php echo $userDetails['user_phone']; ?>';
        document.getElementsByName('gender[]')[0].selected = '<?php echo $userDetails['user_gender']; ?>';

    }
</script>
<script>
    function formData() {
        var $form = $('#checkout'),
            validator = $form.validate({
                rules: {
                    attendeeName: "required",
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    name: "Please specify your name",
                    email: {
                        required: "We need your email address to contact you",
                        email: "Your email address must be in the format of name@domain.com"
                    }
                }
            });

        //validate the form
        validator.form();

        //check if the form is valid 
        if ($form.valid()) {
            //form is valid
            AddAttendeeDetails('valid');
        } else {
            AddAttendeeDetails('inValid');
        }

    }
    $('.Price-currencySymbol_count').text('<?php echo $currencyCode; ?>');
    $('.Price-currencySymbol_sub').text('<?php echo $currencyCode; ?>');
    $('.Price-currencySymbol_total').text('<?php echo $currencyCode; ?>');
</script>