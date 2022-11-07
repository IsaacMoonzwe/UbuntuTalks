<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<span class="gap"></span>
<div class="apply-coupon__target apply-coupon__target-js">
    <div class="-padding-20">
        <?php
        $PromoCouponsFrm->setFormTagAttribute('class', 'form form--small');
        $PromoCouponsFrm->setFormTagAttribute('onsubmit', 'applyPromoCode(this); return false;');
        $PromoCouponsFrm->getField('onsubmit', 'applyPromoCode(this); return false;');
        $PromoCouponsFrm->setJsErrorDisplay('afterfield');
        echo $PromoCouponsFrm->getFormTag();
        echo $PromoCouponsFrm->getExternalJs();
        ?>
        <div class="d-flex">
            <div class="col-md-8">
                <?php echo $PromoCouponsFrm->getFieldHtml('coupon_code'); ?>
            </div>
            <div class="col-md-4">
                <?php echo $PromoCouponsFrm->getFieldHtml('btn_submit'); ?>
            </div>
        </div>
    </div>
</form>
<?php if (!empty($cartSummary['cartDiscounts']['coupon_code'])) { ?>
    <div class="-padding-20 -no-padding-top">
        <div class="applied-coupon">
            <p>Coupon <strong>"<?php echo $cartSummary['cartDiscounts']['coupon_code']; ?>"</strong> Applied</p>
            <a href="javascript:void(0)" onClick="removePromoCode()" class="btn btn--small btn--secondary">Remove</a>
        </div>
    </div>
<?php } ?>
<div>
    <?php if ($couponsList) { ?>
        <div class="-padding-20 -no-padding-top">
            <div class="heading3 align--center"><?php echo Label::getLabel("LBL_Available_Coupons", $siteLangId); ?></div>
            <ul class="coupon-offers">
                <?php
                $counter = 1;
                foreach ($couponsList as $coupon_id => $coupon) {
                    ?>
                    <li>
                        <a href="javascript:void(0);" class="coupon-code" onclick="triggerApplyCoupon('<?php echo $coupon['coupon_code']; ?>');" title="<?php echo Label::getLabel("LBL_Click_to_apply_coupon", $siteLangId); ?>"><?php echo $coupon['coupon_code']; ?></a>
                        <?php if ($coupon['coupon_description'] != '') { ?>
                            <p><?php echo $coupon['coupon_description']; ?> </p>
                        <?php } ?>
                    </li>
                    <?php
                    $counter++;
                }
                ?>
            </ul>
        </div>
    <?php } else { ?>
        <div class="-padding-20 -no-padding-top -align-center">
            <?php echo Label::getLabel("LBL_No_Copons_offer_is_available_now.", $siteLangId); ?>
        </div>
    <?php } ?>
</div>
</div>