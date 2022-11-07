<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$LessonPackageMediaFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$LessonPackageMediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$LessonPackageMediaFrm->developerTags['fld_default_col'] = 12;
$fld2 = $LessonPackageMediaFrm->getField('lpackage_image');
$fld2->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$preferredDimensionsStr = '<small class="text--small">' . sprintf(Label::getLabel('LBL_Preferred_Dimensions', $adminLangId), '80*80') . '</small>';
$htmlAfterField = $preferredDimensionsStr;
if (!empty($LessonPackageImages)) {
    $htmlAfterField .= '<ul class="image-listing grids--onethird">';
    foreach ($LessonPackageImages as $LessonPackageImg) {
        $htmlAfterField .= '<li><div class="uploaded--image"><img src="' . CommonHelper::generateFullUrl('LessonPackages', 'image', array($LessonPackageImg['afile_record_id'], $LessonPackageImg['afile_lang_id'], 'THUMB')) . '?' . time() . '"> <a href="javascript:void(0);" onClick="removeLessonPackageImage(' . $LessonPackageImg['afile_record_id'] . ',' . $LessonPackageImg['afile_lang_id'] . ')" class="remove--img"><i class="ion-close-round"></i></a></div>';
    }
    $htmlAfterField .= '</li></ul>';
}
$fld2->htmlAfterField = $htmlAfterField;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Lesson_Package_Media_setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0)" onclick="editLessonPackageForm(<?php echo $lPackageId ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ( $lPackageId == 0 ) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($lPackageId > 0) { ?> onclick="editLessonPackageLangForm(<?php echo $lPackageId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li><a class="active" href="javascript:void(0)" onclick="LessonPackageMediaForm(<?php echo $lPackageId ?>);"><?php echo Label::getLabel('LBL_Media', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $LessonPackageMediaFrm->getFormHtml(); ?>			
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>