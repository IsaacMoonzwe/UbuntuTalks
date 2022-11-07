<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$mediaFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$mediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$mediaFrm->developerTags['fld_default_col'] = 12;
$fld1 = $mediaFrm->getField('slanguage_image');
$fld1->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$preferredDimensionsStr = '<span class="uploadimage--info" >' . Label::getLabel('LBL_Preferred_Dimensions_are', $adminLangId) . ' Width : 350px & Height : 263px .</span>';
$htmlAfterField = $preferredDimensionsStr;
$htmlAfterField .= '<div id="image-listing"></div>';
$fld1->htmlAfterField = $htmlAfterField;
$fld1 = $mediaFrm->getField('slanguage_flag_image');
if ($fld1) {
    $fld1->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
    $preferredDimensionsStr = '<span class="uploadimage--info" >' . Label::getLabel('LBL_Preferred_Dimensions_are', $adminLangId) . ' Width : 150px & Height : 150px .</span>';
    $htmlAfterField = $preferredDimensionsStr;
    $htmlAfterField .= '<div id="flag-image-listing"></div>';
    $fld1->htmlAfterField = $htmlAfterField;
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Language_Image', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="editSpokenLanguageForm(<?php echo $sLangId; ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ($sLangId == 0) ? 'fat-inactive' : '';
                        if ($sLangId > 0) {
                            foreach ($languages as $langId => $langName) {
                                ?>
                                <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($sLangId > 0) { ?> onclick="editSpokenLanguageLangForm(<?php echo $sLangId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                                <?php
                            }
                        }
                        ?>
                        <li><a class="active" href="javascript:void(0)" onclick="mediaForm(<?php echo $sLangId ?>);"><?php echo Label::getLabel('LBL_Media', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $mediaFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>	
            </div>
        </div>
    </div>
</section>