<?php
$checkoutCart = $_SESSION['checkoutCart'];
$ticketQty = $checkoutCart['ticketQty'];
$plan = $checkoutCart['plan'];

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

<body>
    <section class="cart section">
        <div class="container container--narrow">
            <div class="row">
                <div class="col-lg-8 col-md-12 ticket-information">
                    <form class="cart-form" method="post" name="cart_form">
                        <div class="table-box">

                            <table class="table" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="product-remove">
                                            <!-- <span class="screen-reader-text">Remove item</span> -->
                                        </th>
                                        <th class="product-thumbnail">
                                            <!-- <span class="screen-reader-text">Thumbnail image</span> -->
                                        </th>
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
                                                    <!-- <dt class="variation-Event"></dt> -->
                                                    <dd class="variation-Event">
                                                        <!-- <p>EQUIP 2023</p> -->
                                                        <p><b>Event :</b><?php echo $EventsList['registration_plan_title']; ?></p>
                                                    </dd>
                                                </dl>
                                            </td>
                                            <td class="product-price" data-title="Price">
                                                <!-- <span class=""><bdi><span class="">ZK</span>50.00</bdi></span> -->
                                                <span class=""><bdi><span class=""></span><?php echo "USD" . $EventsList['registration_plan_price']; ?></bdi></span>
                                            </td>
                                            <td class="quantity-count" data-title="Quantity">
                                                <div class="quantity">
                                                    <input type="number" class="input-text qty text" id="cartTickets" name="cartTickets" value=<?php echo $ticketQty; ?> title="Qty" size="4" min="1" max="1500" step="1" placeholder="" inputmode="numeric" autocomplete="off">
                                                </div>
                                            </td>
                                            <td class="subtotal-amount" data-title="Subtotal">
                                                <!-- <span class="amount"><bdi><span class="">ZK</span>50.00</bdi></span> -->
                                                <span class="amount"><bdi>
                                                        <p id="itemPrice"><?php echo "USD" . $EventsList['itemNetPrice']; ?></p>
                                                    </bdi></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="update_cart">
                                                <a disabled=true onclick="UpdateCart();" class="btn-green cart-empty" id="update_cart" name="update_cart" aria-disabled="false">Update cart</a>
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

                        </div>
                    </form>


                    <div class="cart_totals ">
                        <h2>Cart Total</h2>
                        <table cellspacing="0" class="shop_table shop_table_responsive">
                            <tbody>
                                <tr class="cart-subtotal">
                                    <th>Subtotal</th>
                                    <td data-title="Subtotal">
                                        <span class="Price-amount amount">
                                            <bdi><span class="Price-currencySymbol"></span>
                                                <?php if (isset($EventsList)) { ?>
                                                    <p id="subtotal-itemPrice"><?php echo "USD" . $EventsList['itemNetPrice']; ?></p>
                                                <?php } else { ?>
                                                    <p id="subtotal-itemPrice"><?php echo "USD 0"; ?></p>
                                                <?php } ?>
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
                                                    <?php if (isset($EventsList)) { ?>
                                                        <p id="total-itemPrice"><?php echo "USD" . $EventsList['itemNetPrice']; ?></p>
                                                    <?php } else { ?>
                                                        <p id="total-itemPrice"><?php echo "USD 0"; ?></p>
                                                    <?php } ?>
                                                </bdi>
                                            </span>
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

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
    console.log("ticket==", eventCart.props.countOfTickets);


    $('#cartTickets').change(function() {
        $('#update_cart').removeClass('cart-empty');
        eventCart.props.countOfTickets = this.value;
        console.log("hi==", eventCart.props.countOfTickets);
    });

    function updateCartData() {
        var qty = $('#cartTickets').val();
        console.log("ticket==", qty);

        updateCart();
        // window.location.href = fcom.makeUrl('EventUser', 'Cart', [qty, eventCart.props.sponsershipPlan]);
    }
</script>

</html>