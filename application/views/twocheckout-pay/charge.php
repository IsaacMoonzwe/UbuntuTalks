<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<script>
    if (performance.navigation.type == 2) {
        location.reload(true);
    }
</script>
<div class="payment-page">
    <div class="cc-payment">
        <div class="logo-payment">
            <?php if (CommonHelper::demoUrl()) { ?>
                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
            <?php } else { ?>
                <img src="<?php echo CommonHelper::generateFullUrl('Image', 'paymentPageLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL); ?>" alt="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" title="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" />
            <?php } ?>
        </div>
        <div class="reff row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Payable_Amount', $siteLangId); ?> : <strong><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?></strong> </p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Order_Invoice', $siteLangId); ?>: <strong><?php echo $orderInfo["invoice"]; ?></strong></p>
            </div>
        </div>
        <div id="body" class="body">
            <?php if ($paymentType == 'HOSTED') { /* Hosted Checkout */ ?>
                <div class="payment-from">
                    <?php if (!isset($error)) : ?>
                        <p><?php echo Label::getLabel('LBL_We_are_redirecting_payment_page', $siteLangId) ?>:</p>
                        <?php echo $frm->getFormHtml(); ?>
                    <?php else : ?>
                        <div class="alert alert--danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
                <script type="text/javascript">
                    $(function () {
                        setTimeout(function () {
                            $('form[name="twocheckout"]').submit()
                        }, 5000);
                    });
                </script>
            <?php } else { /* API Checkout */ ?>
                <div class="payment-from">
                    <div class="loader" style="display: none;"></div>
                    <div class="col-md-12 alert--info alert" id="lbl-txn-inproress" style="display: none;"><?php echo Label::getLabel('LBL_Please_hold_while_your_transaction_is_being_processed') ?></div>
                    <?php
                    if (!isset($error)) :
                        // $frm->setFormTagAttribute('onsubmit', 'sendPayment(this);return false;');
                        $frm->setFormTagAttribute('id', 'twocheckout');
                        $frm->getField('ccNo')->setFieldTagAttribute('class', 'p-cards');
                        $frm->getField('ccNo')->setFieldTagAttribute('id', 'ccNo');
                        $frm->getField('cvv')->addFieldTagAttribute('id', 'cvv');
                        $frm->getField('expMonth')->addFieldTagAttribute('id', 'expMonth');
                        $frm->getField('expYear')->addFieldTagAttribute('id', 'expYear');
                        echo $frm->getFormTag();
                        ?>
                        <?php echo $frm->getFieldHtml('token'); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_ENTER_CREDIT_CARD_NUMBER', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('ccNo'); ?> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_CARD_HOLDER_NAME', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('cc_owner'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="caption-wraper">
                                    <label class="field_label"> <?php echo Label::getLabel('LBL_CREDIT_CARD_EXPIRY', $siteLangId); ?> </label>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="field-set">
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php
                                                    $fld = $frm->getField('expMonth');
                                                    echo $fld->getHtml();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="field-set">
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php
                                                    $fld = $frm->getField('expYear');
                                                    echo $fld->getHtml();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_CVV', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('cvv'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_Address', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('addrLine1'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_Country', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('country'); ?> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_State', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('state'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_City', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('city'); ?> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_Zip', $siteLangId); ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover"> <?php echo $frm->getFieldHtml('zipCode'); ?> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="total-pay"><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?>
                            <small>(<?php echo Label::getLabel('LBL_Total_Payable', $siteLangId); ?>)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php
                                            $btn = $frm->getField('btn_submit');
                                            $btn->addFieldTagAttribute('class', 'btn btn-primary');
                                            echo $frm->getFieldHtml('btn_submit');
                                            ?>
                                            <a href="<?php echo $cancelBtnUrl; ?>" class="btn btn--medium"><?php echo Label::getLabel('LBL_Cancel', $siteLangId); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <?php echo $frm->getExternalJs(); ?>
                        <?php else : ?>
                            <div class="alert alert--danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <div id="ajax_message" class="col-md-12"></div>
                    </div>
                    <?php if (!FatUtility::isAjaxCall()) { ?>
                        <script type="text/javascript" src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
                    <?php } ?>
                    <script type="text/javascript">
                    $("#ccNo").keydown(function () {
                        var obj = $(this);
                        var cc = obj.val();
                        obj.attr('class', 'p-cards');
                        if (cc != '') {
                            var data = "cc=" + cc;
                            fcom.ajax(fcom.makeUrl('AuthorizeAimPay', 'checkCardType'), data, function (t) {
                                var ans = $.parseJSON(t);
                                var card_type = ans.card_type.toLowerCase();
                                obj.addClass('type-bg p-cards ' + card_type);
                            });
                        }
                    });
                    var frmApiCheckout = '';
                    // Called when token created successfully.
                    var successCallback = function (cdata) {
                        var myForm = document.getElementById('twocheckout');
                        // Set the token as the value for the token input
                        myForm.token.value = cdata.response.token.token;
                        // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
                        var data = fcom.frmData(myForm);
                        data += '&outmode=json&is_ajax_request=yes';
                        var action = $(myForm).attr('action');
                        fcom.ajax(action, data, function (response) {
                            try {
                                var json = $.parseJSON(response);
                                if (json['error']) {
                                    $('#ajax_message').html('<div class="alert alert--danger">' + json['error'] + '</div>');
                                }
                                if (json['redirect']) {
                                    $(location).attr("href", json['redirect']);
                                }
                            } catch (exc) {
                                console.log(t);
                            }
                        });
                    };
                    // Called when token creation fails.
                    var errorCallback = function (data) {
                        // Retry the token request if ajax call fails
                        if (data.errorCode === 200) {
                            // This error code indicates that the ajax call failed. We recommend that you retry the token request.
                            tokenRequest();
                        } else {
                            $("#twocheckout").data('requestRunning', false);
                            $('#ajax_message').html('<div class="alert alert--danger">' + data.errorMsg + '</div>');
                            $("#twocheckout").show();
                            $('div.payment-from .loader').hide();
                            $('#lbl-txn-inproress').hide();
                        }
                    };
                    var tokenRequest = function () {
                        // Setup token request arguments
                        var args = {
                            sellerId: "<?php echo $sellerId ?? ''; ?>",
                            publishableKey: "<?php echo $publishableKey ?? ''; ?>",
                            ccNo: $("#ccNo").val(),
                            cvv: $("#cvv").val(),
                            expMonth: $("#expMonth").val(),
                            expYear: $("#expYear").val(),
                            demo: true
                        };
                        // Make the token request
                        TCO.requestToken(successCallback, errorCallback, args);
                    };
                    // Pull in the public encryption key for our environment
                    /* TCO.loadPubKey("<?php echo $transaction_mode; ?>"); */
                    $(document).on("submit", "#twocheckout", function (event) {
                        event.preventDefault();
                        if (!$('#twocheckout').validate()) {
                            return;
                        }
                        var token = $("input[name='token']").val();
                        if ('' != token && 'undefined' != typeof token) {
                            return;
                        }
                        $(this).hide();
                        $('div.payment-from .loader').show();
                        $('#lbl-txn-inproress').show();
                        $(this).data('requestRunning', false);
                        TCO.loadPubKey("<?php echo $transaction_mode; ?>", tokenRequest);
                        /* tokenRequest(); */
                    });
                    </script>
                <?php } ?>
            </div>
        </div>
    </div>