<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$langFrm->setFormTagAttribute('id', 'profileLangInfoFrm');
$langLayoutClass = 'form--' . $formLayout;
$langFrm->setFormTagAttribute('class', 'form ' . $langLayoutClass);
$langFrm->setFormTagAttribute('onsubmit', 'setUpProfileLangInfo(this, false); return(false);');
$langFrm->developerTags['colClassPrefix'] = 'col-md-';
$langFrm->developerTags['fld_default_col'] = 12;
$profileInfo = $langFrm->getField('userlang_user_profile_Info');
$languagesKeys = array_keys($languages);
$langId = end($languagesKeys);
$nextButton = $langFrm->getField('btn_next');
$nextButton->addFieldTagAttribute('onclick', 'setUpProfileLangInfo(this.form, true); return(false);');
if ($langId == $lang_id) {
    $nextButton->setFieldTagAttribute('onclick', 'setUpProfileLangInfo(this.form, true); $(".teacher-lang-form-js").trigger("click"); return(false);');
}
?>
<div class="padding-6">
    <div class="max-width-80">
        <?php
        echo $langFrm->getFormTag();
        echo $langFrm->getFieldHtml('userlang_lang_id');
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label"><?php echo $profileInfo->getCaption(); ?>
                            <?php if ($profileInfo->requirement->isRequired()) { ?>
                                <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $profileInfo->getHTML(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row submit-row">
            <div class="col-sm-auto">
                <div class="field-set">
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php
                            echo $langFrm->getFieldHtml('btn_submit');
                            if ($langId != $lang_id || User::getDashboardActiveTab() == User::USER_TEACHER_DASHBOARD) {
                                echo $langFrm->getFieldHtml('btn_next');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $langFrm->getExternalJS(); ?>
    </div>
</div>
