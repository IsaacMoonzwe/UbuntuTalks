<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$langFrm->setFormTagAttribute('onsubmit', 'setupLangPreference(this); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <div class="col-sm-12">
            <div class="tabs_nav_container responsive flat">
                <ul class="tabs_nav">
                    <li><a href="javascript:void(0);" onclick="editPreferenceForm(<?php echo $preferenceId ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                    <?php
                    if ($preferenceId > 0) {
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li><a class="<?php echo ($lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="editPreferenceLangForm(<?php echo $preferenceId ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
                <div class="tabs_panel_wrap">
                    <div class="tabs_panel">
                        <?php echo $langFrm->getFormHtml(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>