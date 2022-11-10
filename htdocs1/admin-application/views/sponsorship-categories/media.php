<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$testimonialMediaFrm->setFormTagAttribute('class', 'web_form form_horizontal');
$testimonialMediaFrm->developerTags['colClassPrefix'] = 'col-md-';
$testimonialMediaFrm->developerTags['fld_default_col'] = 12;
$fld2 = $testimonialMediaFrm->getField('sponsorshipcategories_image');
$fld2->addFieldTagAttribute('class', 'btn btn--primary btn--sm');
$preferredDimensionsStr = '<small class="text--small">' . sprintf(Label::getLabel('LBL_Preferred_Dimensions_%s', $adminLangId), '275 × 275') . '</small>';
$htmlAfterField = $preferredDimensionsStr;
if (!empty($testimonialImages)) {
    $htmlAfterField .= '<ul class="image-listing grids--onethird">';
    foreach ($testimonialImages as $testimonialImg) {
        $htmlAfterField .= '<li><div class="uploaded--image"><img src="' . CommonHelper::generateFullUrl('Testimonials', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'THUMB')) . '?' . time() . '"> <a href="javascript:void(0);" onClick="removeTestimonialImage(' . $testimonialImg['afile_record_id'] . ',' . $testimonialImg['afile_lang_id'] . ')" class="remove--img"><i class="ion-close-round"></i></a></div>';
    }
    $htmlAfterField .= '</li></ul>';
}
$fld2->htmlAfterField = $htmlAfterField;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Testimonial_Media_setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0)" onclick="editTestimonialForm(<?php echo $testimonialId ?>);"><?php echo Label::getLabel('LBL_General', $adminLangId); ?></a></li>
                        <?php
                        $inactive = ( $testimonialId == 0 ) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($testimonialId > 0) { ?> onclick="editTestimonialLangForm(<?php echo $testimonialId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li><a class="active" href="javascript:void(0)" onclick="testimonialMediaForm(<?php echo $testimonialId ?>);"><?php echo Label::getLabel('LBL_Media', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $testimonialMediaFrm->getFormHtml(); ?>			
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>