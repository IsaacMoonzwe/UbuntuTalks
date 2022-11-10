<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$sign = '';
if ($paymentAmount < 0) {
    $val = abs($val);
    $sign = '-';
}
$currencySymbolLeft = CommonHelper::getCurrencySymbolLeft();
$currencySymbolRight = CommonHelper::getCurrencySymbolRight();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<style>
    header.header.nav-up {
        z-index: 9999;
    }

    .footer .section-copyright .copyright {
        justify-content: center !important;
    }

    .footer .section-copyright .copyright p {
        color: #000;
        font-size: 16px;
    }

    .message-display {
        min-height: 10rem !important;
        margin-top: 0px !important;
    }

    p.paypal-description {
        font-size: 18px;
        color: #000;
    }

    .header__logo {
        text-align: center !important;
    }
</style>
<header class="header">
    <div class="header-primary">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="header__left">
                    <a href="javascript:void(0)" class="toggle toggle--nav toggle--nav-js">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 515.555 515.555">
                            <path d="m303.347 18.875c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                            <path d="m303.347 212.209c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                            <path d="m303.347 405.541c25.167 25.167 25.167 65.971 0 91.138s-65.971 25.167-91.138 0-25.167-65.971 0-91.138c25.166-25.167 65.97-25.167 91.138 0" />
                        </svg>
                    </a>
                    <div class="header__logo">
                        <a href="<?php echo CommonHelper::generateUrl(); ?>">
                            <?php if (CommonHelper::demoUrl()) { ?>
                                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                            <?php } else { ?>
                                <img src="<?php echo CommonHelper::generateFullUrl('Image', 'siteLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL); ?>" alt="Ubuntu Talks logo" />
                            <?php } ?>
                        </a>
                    </div>
                    <?php $this->includeTemplate('header/navigation.php'); ?>

                </div>
                <div class="header__middle">
                    <?php $this->includeTemplate('header/explore-subjects.php'); ?>
                </div>
                <div class="header__right">
                    <?php $this->includeTemplate('header/right-section.php'); ?>
                </div>
            </div>
        </div>
    </div>
</header>
<section class="section section--grey section--page -pattern">
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <div class="page__panel-narrow">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-8 col-md-10">
                        <div class="box -padding-30 -skin">
                            <div class="header__logo">
                                <a href="/">
                                    <img src="https://ubuntutalks.com/image/site-logo/1" alt="Ubuntu Talks logo">
                                </a>
                            </div>
                            <div class="message-display">
                                <p class="paypal-description">
                                    <?php echo Label::getLabel('LBL_Order_Invoice', $siteLangId); ?> : <strong><?php echo $orderInfo["order_id"]; /* displayNotApplicable($orderInfo["order_id"]) */ ?></strong>
                                </p>
                                <p class="paypal-description"><?php echo Label::getLabel('LBL_Payable_Amount', $siteLangId); ?> : <strong><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?></strong> </p>
                                <?php if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) { ?>
                                    <p class="-color-secondary"><?php echo CommonHelper::currencyDisclaimer($siteLangId, $paymentAmount); ?></p>
                                <?php } ?>
                            </div>
                            <div class="-align-center">
                                <div class="payment-from">
                                    <div id="smart-button-container">
                                        <div style="text-align: center;">
                                            <div id="paypal-button-container">
                                                <?php
                                                // echo "<pre>";
                                                // print_r($orderInfo);
                                                // echo "<pre>";
                                                // print_r($paymentAmount);
                                                if (!isset($error)) : ?>
                                                    <?php echo $frm->getFormHtml() ?>
                                                <?php else : ?>
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
            </div>
        </div>
    </div>
</section>
<footer class="footer footernewcls">
    <div class="section-copyright">
        <div class="container container--narrow">
            <div class="copyright">
                <div class="footer__logo">
                    <a href="<?php echo CommonHelper::generateUrl(); ?>">
                        <?php if (CommonHelper::demoUrl()) { ?>
                            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                        <?php } else { ?>
                            <img src="<?php echo CommonHelper::generateFullUrl('Image', 'siteLogo', array(CommonHelper::getLangId()), CONF_WEBROOT_FRONT_URL); ?>" alt="" />
                        <?php } ?>
                    </a>
                </div>
                <p><?php echo Label::getLabel('LBL_Footer_Section_Label'); ?>
                    <?php
                    if (CommonHelper::demoUrl()) {
                        echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT', CommonHelper::getLangId()), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
                    } else {
                        echo Label::getLabel('LBL_COPYRIGHT', CommonHelper::getLangId()) . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . CommonHelper::getLangId(), FatUtility::VAR_STRING);
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- <script>
    $(function() {
        setTimeout(function() {
            $('form[name="frmPayPalStandard"]').submit()
        }, 5000);
    });
</script> -->
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paymentSettings['paypal_standard_client_id']; ?>&enable-funding=venmo&currency=USD" data-sdk-integration-source="button-factory"></script>
<script>
    function initPayPalButton() {
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'vertical',
                label: 'checkout',

            },

            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        "amount": {
                            "currency_code": "USD",
                            "value": <?php echo $paymentAmount; ?>
                        }
                    }]
                });
            },

            onApprove: function(data, actions) {
                return actions.order.capture().then(function(orderData) {

                    // Full available details
                    //  console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                    localStorage.setItem('orderData', JSON.stringify(orderData, null, 2));

                    // Show a success message within this page, e.g.
                    const element = document.getElementById('paypal-button-container');
                    element.innerHTML = '';
                    element.innerHTML = '<h3>Thank you for your payment!</h3>';

                    callback(orderData);
                    // Or go to another URL:  actions.redirect('thank_you.html');

                });
            },

            onError: function(err) {
                console.log(err);
            }
        }).render('#paypal-button-container');
    }
    initPayPalButton();

    function callback(orderData) {
        $.ajax({
            type: "post",
            url: '<?php echo $notify_url; ?>',
            data: orderData,
            dataType: "json",

            success: function(data) {
                //  console.log('hii', data);

                setTimeout(() => {
                    window.location.href = "<?php echo $return; ?>";
                }, 500);
            }
        }, {
            fOutMode: 'json'
        });

    }
</script>