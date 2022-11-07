<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="-padding-20">
    <h3><?php echo Label::getLabel('LBL_Payment'); ?></h3>
    <p class="-no-margin-bottom"><?php echo Label::getLabel('LBL_Pick_a_payment_method.') ?></p>
</div>
<?php if ($userWalletBalance > 0 && $cartData['orderNetAmount'] > 0) { ?>
    <div class="label-select">
        <label class="label-trigger-js">
            <span class="checkbox">
                <input onChange="walletSelection(this)" type="checkbox" <?php echo ($cartData["cartWalletSelected"]) ? 'checked="checked"' : ''; ?> name="pay_from_wallet" id="pay_from_wallet" /><i class="input-helper"></i>
            </span>
            <h6>
                <?php
                if ($cartData["cartWalletSelected"] && $userWalletBalance >= $cartData['orderNetAmount']) {
                    echo Label::getLabel('LBL_Sufficient_balance_in_your_wallet');
                } else {
                    echo Label::getLabel('MSG_Use_My_Wallet_Credits')
                    ?>: (<?php echo CommonHelper::displayMoneyFormat($userWalletBalance) ?>)
                <?php } ?></h6>
        </label>
        <!-- section with sufficient balance-->
        <?php if ($cartData["cartWalletSelected"]) { ?>
            <div class="wallet-target-js">
                <div class="listing-cell">
                    <ul>
                        <li>
                            <div class="boxwhite">
                                <p><?php echo Label::getLabel('LBL_Payment_to_be_made'); ?></p>
                                <h5><?php echo CommonHelper::displayMoneyFormat($cartData['orderNetAmount']); ?></h5>
                            </div>
                        </li>
                        <li>
                            <div class="boxwhite">
                                <p><?php echo Label::getLabel('LBL_Amount_in_your_wallet'); ?></p>
                                <h5><?php echo CommonHelper::displayMoneyFormat($userWalletBalance); ?></h5>
                            </div>
                            <p><i><?php
                                    echo Label::getLabel('LBL_Remaining_wallet_balance');
                                    $remainingWalletBalance = ($userWalletBalance - $cartData['orderNetAmount']);
                                    $remainingWalletBalance = ($remainingWalletBalance < 0) ? 0 : $remainingWalletBalance;
                                    echo CommonHelper::displayMoneyFormat($remainingWalletBalance);
                                    ?></i>
                            </p>
                        </li>
                        <?php
                        if ($userWalletBalance >= $cartData['orderNetAmount']) { /* ?>
                          <li>
                          <?php
                          $btnSubmitFld = $WalletPaymentForm->getField('btn_submit');
                          $btnSubmitFld->addFieldTagAttribute('class', 'btn btn--secondary btn--large');
                          $WalletPaymentForm->developerTags['colClassPrefix'] = 'col-md-';
                          $WalletPaymentForm->developerTags['fld_default_col'] = 12;
                          echo $WalletPaymentForm->getFormHtml();
                          ?>
                          </li>
                          <?php */
                        } else {
                            ?>
                            <li>
                                <div class="boxwhite">
                                    <p><?php echo Label::getLabel('LBL_Select_an_option_to_pay_balance'); ?></p>
                                    <h6><?php echo CommonHelper::displayMoneyFormat($cartData['orderPaymentGatewayCharges']); ?></h6>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php if ($cartData['orderPaymentGatewayCharges'] > 0) { ?>
    <div class="payments-container payments-container-js">
        <div class="-padding-20">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4">
                    <div class="tabs-gray">
                        <ul id="payment_methods_tab">
                            <?php
                            if ($paymentMethods) {
                                $count = 1;
                                foreach ($paymentMethods as $key => $val) {
                                    ?>
                                    <li class="<?php echo ($count == 1) ? 'is-active' : ''; ?>"><a class="<?php echo ($count == 1) ? 'is-active' : ''; ?>" href="<?php echo CommonHelper::generateUrl('Checkout', 'paymentTab', array($val['pmethod_id']), '', null, false, false); ?>"><?php echo $val['pmethod_name']; ?></a></li>
                                    <?php
                                    $count++;
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8 col-md-8" id="tabs-container">
                </div>
            </div>
        </div>
        <span class="-gap"></span>
    </div>
    <script>
        var containerId = '#tabs-container';
        var tabsId = '#payment_methods_tab';
        $(document).ready(function () {
            if ($(tabsId + ' LI A.is-active').length > 0) {
                loadTab($(tabsId + ' LI A.is-active'));
            }
            $(tabsId + ' A').click(function () {
                if ($(this).hasClass('is-active')) {
                    return false;
                }
                $(tabsId + ' LI A.is-active').removeClass('is-active');
                $('li').removeClass('is-active');
                $(this).parent().addClass('is-active');
                loadTab($(this));
                return false;
            });
        });
        function loadTab(tabObj) {
            if (!tabObj || !tabObj.length) {
                return;
            }
            $(containerId).html(fcom.getLoader());
            //$(containerId).fadeOut('fast');
            fcom.ajax(tabObj.attr('href'), '', function (response) {
                $(containerId).html(response);
            });
            /* $(containerId).load( tabObj.attr('href'), function(){
             //$(containerId).fadeIn('fast');
             }); */
        }
    </script>
    <?php
} else {
    $this->includeTemplate('checkout/_partial/processWithNoPayment.php', $this->variables);
}
?>