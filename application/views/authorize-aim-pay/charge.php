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
<div class="payment-page">
    <div class="cc-payment">
        <div class="logo-payment">
            <?php if (CommonHelper::demoUrl()) { ?>
                <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
            <?php } else { ?>
                <img src="<?php echo CommonHelper::generateFullUrl('Image', 'paymentPageLogo', array($siteLangId), CONF_WEBROOT_FRONT_URL, null, false, false); ?>" alt="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" title="<?php echo FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId) ?>" />
            <?php } ?>
        </div>
        <div class="reff row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Payable_Amount', $siteLangId); ?> : <strong><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?></strong> </p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <p class=""><?php echo Label::getLabel('LBL_Order_Invoice', $siteLangId); ?>: <strong><?php echo $orderInfo["order_id"]; ?></strong></p>
            </div>
        </div>
        <div id="body" class="body">
            <div class="payment-from">
                <?php
                if (!isset($error)) :
                    $frm->setFormTagAttribute('onsubmit', 'sendPayment(this); return(false);');
                    $frm->getField('cc_number')->addFieldTagAttribute('class', 'p-cards');
                    $frm->getField('cc_number')->addFieldTagAttribute('id', 'cc_number');
                    ?>
                    <?php echo $frm->getFormTag(); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label"><?php echo Label::getLabel('LBL_ENTER_CREDIT_CARD_NUMBER', $siteLangId); ?></label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover"> <?php echo $frm->getFieldHtml('cc_number'); ?> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
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
                        <div class="col-md-6">
                            <div class="caption-wraper">
                                <label class="field_label"> <?php echo Label::getLabel('LBL_CREDIT_CARD_EXPIRY', $siteLangId); ?> </label>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php
                                                $fld = $frm->getField('cc_expire_date_month');
                                                $fld->addFieldTagAttribute('id', 'ccExpMonth');
                                                $fld->addFieldTagAttribute('class', 'ccExpMonth  combobox required');
                                                echo $fld->getHtml();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php
                                                $fld = $frm->getField('cc_expire_date_year');
                                                $fld->addFieldTagAttribute('id', 'ccExpYear');
                                                $fld->addFieldTagAttribute('class', 'ccExpYear  combobox required');
                                                echo $fld->getHtml();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label"><?php echo Label::getLabel('LBL_CVV_SECURITY_CODE', $siteLangId); ?></label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover"> <?php echo $frm->getFieldHtml('cc_cvv'); ?> </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php /* <div class="row">
                      <div class="col-md-12">
                      <div class="field-set">
                      <div class="caption-wraper">
                      <label class="field_label"></label>
                      </div>
                      <div class="field-wraper">
                      <div class="field_cover">
                      <label class="checkbox">
                      <?php
                      $fld = $frm->getField('cc_save_card');
                      $fld->addFieldTagAttribute('onclick','alert("|SAVE THIS CARD| Not Functional!");return false;');
                      $fldHtml = $fld->getHTML();
                      $fldHtml = str_replace("<label >","",$fldHtml);
                      $fldHtml = str_replace("</label>","",$fldHtml);
                      echo $fldHtml;
                      ?>
                      <i class="input-helper"></i> </label>
                      </div>
                      </div>
                      </div>
                      </div>
                      </div> */ ?>
                    <div class="total-pay"><?php echo CommonHelper::displayMoneyFormat($paymentAmount) ?> <small>(<?php echo Label::getLabel('LBL_Total_Payable', $siteLangId); ?>)</small> </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label"></label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover"> <?php echo $frm->getFieldHtml('btn_submit'); ?> <a href="<?php echo $cancelBtnUrl; ?>" class="btn btn--medium"><?php echo Label::getLabel('LBL_Cancel', $siteLangId); ?></a> </div>
                                </div>
                                <span class="-gap -hide-mobile"></span>
                                <?php if (CommonHelper::getCurrencyId() != CommonHelper::getSystemCurrencyId()) { ?>
                                    <p class="-color-secondary"><?php echo CommonHelper::currencyDisclaimer($siteLangId, $paymentAmount); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php echo $frm->getExternalJs(); ?>
                <?php else : ?>
                    <div class="alert alert--danger">
                        <h5><?php echo $error ?></h5>
                    </div>
                <?php endif; ?>
                <div id="ajax_message"></div>
            </div>
        </div>
    </div>
</div>
<div class="loading-wrapper" style="display: none;">
    <div class="loading">
        <div class="inner rotate-one"></div>
        <div class="inner rotate-two"></div>
        <div class="inner rotate-three"></div>
    </div>
</div>