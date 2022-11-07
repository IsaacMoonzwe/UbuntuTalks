<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$langFrm->setFormTagAttribute('onsubmit', 'setupCouponLang(this); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;
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
                        <li><a href="javascript:void(0);" onclick="addCouponForm(<?php echo $coupon_id ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ($coupon_id == 0) ? 'fat-inactive' : '';
                        if ($coupon_id > 0) {
                            foreach ($languages as $langId => $langName) {
                                ?>
                                <li><a class="<?php echo ($coupon_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="addCouponLangForm(<?php echo $coupon_id ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $langFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>	
            </div>
        </div>
    </div>
</section>