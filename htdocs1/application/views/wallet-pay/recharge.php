<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--grey section--page">
    <div class="container container--narrow">
        <div class="section__head">
            <h2><?php echo Label::getLabel('LBL_Checkout'); ?></h2>
        </div>
        <div class="section__body">
            <div class="row d-block -clearfix">
                <div class="col-xl-4 col-lg-4 col-md-12 -clear-right">
                    <div class="box -align-center" style="margin-bottom: 30px;">
                        <div class="-padding-30">
                            <div class="avtar avtar--centered avtar--xlarge margin-auto" data-title="<?php echo CommonHelper::getFirstChar($userDetails['user_first_name']); ?>">
                                <?php
                                if (true == User::isProfilePicUploaded($userDetails['user_id'])) {
                                    $img = CommonHelper::generateUrl('Image', 'User', array($userDetails['user_id']));
                                    echo '<img src="' . $img . '" />';
                                }
                                ?>
                            </div>
                            <span class="-gap"></span>
                            <h3 class="-display-inline"><?php echo $userDetails['user_first_name']; ?></h3>
                            <?php if ($userDetails['user_country_id'] > 0) { ?>
                                <span class="flag -display-inline"><img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($userDetails['user_country_id'], 'DEFAULT')); ?>" alt=""></span>
                            <?php } ?>
                            <br></p>
                        </div>
                    </div>
                    <p class="-color-secondary">
                        <?php
                        $labelstr = Label::getLabel('LBL_*_All_Purchases_are_in_{default-currency-code}._Foreign_transaction_fees_might_apply,_according_to_your_bank\'s_policies');
                        echo str_replace("{default-currency-code}", CommonHelper::getSystemCurrencyData()['currency_code'], $labelstr);
                        ?>
                    </p>
                    <?php if (AttachedFile::getAttachment(AttachedFile::FILETYPE_ALLOWED_PAYMENT_GATEWAYS_IMAGE, 0, 0, CommonHelper::getLangId())) { ?>
                        <div class="">
                            <img src="<?php echo FatUtility::generateFullUrl('Image', 'allowedPaymentGatewayImage', array(CommonHelper::getLangId())); ?>">
                        </div>
                    <?php } ?>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="box" style="margin-bottom: 30px;">
                        <div class="-padding-20">
                            <h3><?php echo Label::getLabel('LBL_Payment'); ?></h3>
                            <p class="-no-margin-bottom"><?php echo Label::getLabel('LBL_Pick_a_payment_method.'); ?></p>
                        </div>
                        <div class="payments-container payments-container-js">
                            <div class="-padding-20">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4">
                                        <?php if ($paymentMethods) { ?>
                                            <div class="tabs-gray">
                                                <ul id="payment_methods_tab">
                                                    <?php foreach ($paymentMethods as $k => $paymentMethod) { ?>
                                                        <li class="<?php
                                                        if ($k == 0) {
                                                            echo 'is-active';
                                                        }
                                                        ?>"><a href="<?php echo CommonHelper::generateUrl('Checkout', 'paymentTab', array($paymentMethod['pmethod_id'], $orderId)); ?>"><?php echo $paymentMethod['pmethod_name']; ?></a></li>
                                                        <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xl-8 col-lg-8 col-md-8" id="tabs-container">
                                    </div>
                                </div>
                            </div>
                            <span class="-gap"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>