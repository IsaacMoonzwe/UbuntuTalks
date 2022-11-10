<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupCoupon(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$minOrder_fld = $frm->getField('coupon_min_order_value');
$minOrder_fld->setWrapperAttribute('id', 'coupon_minorder_div');
$coupon_max_discount_value_fld = $frm->getField('coupon_max_discount_value');
$coupon_max_discount_value_fld->setWrapperAttribute('id', 'coupon_max_discount_value_div');
$coupon_discount_in_percent_fld = $frm->getField('coupon_discount_in_percent');
$coupon_discount_in_percent_fld->addFieldTagAttribute('onChange', 'callCouponDiscountIn(this.value); ');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Coupon_Setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="addCouponForm(<?php echo $coupon_id ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ($coupon_id == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($coupon_id > 0) { ?> onclick="addCouponLangForm(<?php echo $coupon_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div></div></section>
<script>
    $(document).ready(function () {
        callCouponDiscountIn(<?php echo $couponDiscountIn; ?>);
    });
</script>