<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (!empty($stripe['secret_key']) && !empty($stripe['publishable_key'])) {
?>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('<?php echo $stripe['publishable_key']; ?>');
    </script>
<?php } ?>
<section class="section section--grey section--page -pattern">
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <div class="page__panel-narrow">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="box -padding-30 -skin">
                            <div class="box__data">
                                <div class="loader"></div>
                                <div class="-align-center">
                                    <h1 class="-color-secondary">We're redirecting you!!</h1>
                                    <h4>Please wait...</h4>
                                </div>
                            </div>
                            <div class="message-display">
                                 <form id="payment-form" action="" method="POST" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="payment_details" id="payment-details">
                                    <input type="hidden" name="provider" id="provider" value="stripe">
                                    <p>I want to pay</p>
                                    <div class="input-group mb-4">
                                        <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="button">$</button>
                                        </div>
                                        <input name="amount" id="amount" type="number" min="10" max="" step="1" class="form-control"  aria-label="amount" aria-describedby="basic-addon1" required>
                                        <span id="amount-error" class="invalid-feedback d-block" role="alert"></span>
                                    </div>
                                    <ul class="nav nav-tabs mb-4 d-none" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="credit-card-tab" data-toggle="tab" href="#credit-card" role="tab" aria-controls="credit-card" aria-selected="true">Pay with Credit Card</a>
                                        </li>
                                         <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="paypal-tab" data-toggle="tab" href="#paypal" role="tab" aria-controls="paypal" aria-selected="false">Pay with Paypal</a>
                                        </li> 
                                    </ul>
                                    <div class="tab-content d-none" id="myTabContent" >
                                        <div class="tab-pane fade show active" id="credit-card" role="tabpanel" aria-labelledby="credit-card-tab">
                                            <p>Credit Card details</p>
                                            <small class="text-muted mt-2 mb-4 d-block">We accept <img src="{{ asset('images/misc/cc.jpeg') }}" class="ml-4" alt="Accepted Credit Cards" width="100"></small>
                                            <input type="text" name="card_holder" id="card-holder" class="form-control mb-3" placeholder="Name on card*" required>
                                            <div id="stripe-card"></div>
                                            <span id="card-error" class="d-block invalid-feedback pl-1" role="alert"></span>

                                            <div class="form-footer mt-4">
                                                <button id="pay-btn" type="submit" class="btn btn-primary w-100">Confirm and pay</button>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                                             <div id="paypal-button-container"></div> 
                                        </div>
                                    </div>
                                </form>
                                <p class=""><?php echo Label::getLabel('LBL_Payable_Amount', $siteLangId); ?> : <strong><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?></strong> </p>
                                <p class="">
                                    <?php echo Label::getLabel('LBL_Order_Invoice', $siteLangId); ?>: <strong><?php echo $orderInfo["order_id"]; /* displayNotApplicable($orderInfo["order_id"]) */ ?></strong>
                                </p>
                                <?php if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) { ?>
                                    <p class="-color-secondary"><?php echo CommonHelper::currencyDisclaimer($siteLangId, $paymentAmount); ?></p>
                                <?php } ?>
                            </div>
                            <div class="-align-center">
                                <div class="payment-from">
                                    <?php if (isset($error)) : ?>
                                        <div class="alert alert--danger">
                                            <?php echo $error ?>
                                            <div>
                                            <?php endif; ?>
                                            </div>
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
<script>
    // $(function() {
    //     stripe.redirectToCheckout({
    //         sessionId: '<?php echo $stripeSessionId ?>'
    //     });
    // });
        
</script>