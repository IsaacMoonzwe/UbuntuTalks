<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupCurrency(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
if ($defaultCurrency) {
    $fld = $frm->getField('currencies_switcher_value');
    $fld->setFieldTagAttribute('disabled', true);
    $fld->htmlAfterField = '<small>' . Label::getLabel('LBL_This_is_your_default_currency', $adminLangId) . '</small>';
    $frm->getField('currencies_switcher_code')->setFieldTagAttribute('disabled', true);
    $frm->getField('currencies_switcher_active')->setFieldTagAttribute('disabled', true);
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_currencies_switcher_Setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">      
        <div class="tabs_nav_container responsive flat">
            <ul class="tabs_nav">
                <li><a class="active" href="javascript:void(0)" onclick="currencyForm(<?php echo $currencies_switcher_id ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                <!-- <?php
                $inactive = ($currencies_switcher_id == 0) ? 'fat-inactive' : '';
                foreach ($languages as $langId => $langName) {
                    ?>
                    <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($currencies_switcher_id > 0) { ?> onclick="editCurrencyLangForm(<?php echo $currencies_switcher_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                <?php } ?> -->
            </ul>
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>						
        </div>
    </div>						
</section>