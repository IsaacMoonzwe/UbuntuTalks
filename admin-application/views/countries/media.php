<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$countryMediaFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$countryMediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$countryMediaFrm->developerTags['fld_default_col'] = 12;

$fld2 = $countryMediaFrm->getField('flag');
$fld2->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$preferredDimensionsStr = '<small class="text--small">' . sprintf(Label::getLabel('LBL_Preferred_Dimensions_%s', $adminLangId), '192*82') . '</small>';
$htmlAfterField = $preferredDimensionsStr;
$htmlAfterField .= '<div id="image-listing"></div>';
$fld2->htmlAfterField = $htmlAfterField;
?><section class="section">
    <div class="sectionhead">

        <h4><?php echo Label::getLabel('LBL_Country_Flag_Setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0)" onclick="editCountryForm(<?php echo $country_id ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ( $country_id == 0 ) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($country_id > 0) { ?> onclick="editCountryLangForm(<?php echo $country_id ?>, <?php echo $langId; ?>);" <?php } ?>>
                                    <?php echo Label::getLabel("LBL_" . $langName, $adminLangId); ?></a></li>
                        <?php } ?>
                        <li><a class="active" href="javascript:void(0)" onclick="countryMediaForm(<?php echo $country_id ?>);"><?php echo Label::getLabel('LBL_Media', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $countryMediaFrm->getFormHtml(); ?>			
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
