<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupStep3(this); return(false);');
$teachLangField = $frm->getField('utrequest_teach_slanguage_id');
$speakLangField = $frm->getField('utrequest_language_speak[]');
$proficiencyField = $frm->getField('utrequest_language_speak_proficiency[]');
?>
<?php $this->includeTemplate('teacher-request/_partial/leftPanel.php', ['siteLangId' => $siteLangId, 'step' => 3]); ?>
<div class="page-block__right">
    <div class="page-block__head">
        <div class="head__title">
            <h4><?php echo Label::getLabel('LBL_Tutor_registration', $siteLangId); ?></h4>
        </div>
    </div>
    <div class="page-block__body">
        <?php echo $frm->getFormTag() ?>
        <div class="row justify-content-center no-gutters">
            <div class="col-md-12 col-lg-12 col-xl-11">
                <div class="block-content">
                    <div class="block-content__head">
                        <div class="info__content">
                            <h5><?php echo Label::getLabel('LBL_Languages_section_Title', $siteLangId); ?></h5>
                            <p><?php echo Label::getLabel('LBL_Languages_section_Desc', $siteLangId); ?></p>
                        </div>
                    </div>
                    <div class="block-content__body">
                        <div class="form__body">
                            <div class="colum-layout">
                                <div class="colum-layout__cell">
                                    <div class="colum-layout__head">
                                        <span class="bold-600"><?php echo $teachLangField->getCaption(); ?></span>
                                    </div>
                                    <div class="colum-layout__body">
                                        <div class="colum-layout__scroll scrollbar scrollbar-js" tabindex="0">
                                            <?php foreach ($teachLangField->options as $key => $value) { ?>
                                                <div class="selection">
                                                    <label class="selection__trigger">
                                                        <input name="<?php echo $teachLangField->getName(); ?>[]" value="<?php echo $key; ?>" <?php echo in_array($key, $teachLangField->value) ? 'checked' : ''; ?> class="selection__trigger-input" type="checkbox">
                                                        <span class="selection__trigger-action">
                                                            <span class="selection__trigger-label"><span class="flag-icon flag-icon--s"><img src="<?php echo FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'showLanguageFlagImage', array($key, 'SMALL'), CONF_WEBROOT_FRONT_URL), CONF_IMG_CACHE_TIME, '.jpg'); ?>" alt=""></span><?php echo $value; ?></span>
                                                            <span class="selection__trigger-icon"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="colum-layout__cell">
                                    <div class="colum-layout__head">
                                        <span class="bold-600"><?php echo Label::getLabel('LBL_Language_I_Speak', $siteLangId); ?></span>
                                    </div>
                                    <div class="colum-layout__body">

                                        <div class="colum-layout__scroll scrollbar scrollbar-js">
                                            <?php
                                            foreach ($spokenLangs as $key => $value) {
                                                $speakLangField = $frm->getField('utrequest_language_speak[' . $key . ']');
                                                $proficiencyField = $frm->getField('utrequest_language_speak_proficiency[' . $key . ']');
                                                $proficiencyField->addFieldTagAttribute('onchange', 'changeProficiency(this,' . $key . ');');
                                                $proficiencyField->addFieldTagAttribute('data-lang-id', $key);
                                                $isLangSpeak = false;
                                                $proficiencyKey = array_search($key, $request['utrequest_language_speak']);
                                                if ($proficiencyKey !== false) {
                                                    $proficiencyField->value = $request['utrequest_language_speak_proficiency'][$proficiencyKey];
                                                    $isLangSpeak = true;
                                                }
                                            ?>
                                                <div class="selection selection--select slanguage-<?php echo $key; ?> <?php echo ($isLangSpeak) ? 'is-selected' : ''; ?>">
                                                    <label class="selection__trigger ">
                                                        <input type="checkbox" value="<?php echo $key; ?>" class="slanguage-checkbox-js slanguage-checkbox-<?php echo $key; ?>" onchange="changeSpeakLang(this, <?php echo $key; ?>);" name="<?php echo $speakLangField->getName(); ?>" <?php echo ($isLangSpeak) ? 'checked' : ''; ?>>
                                                        <span class="selection__trigger-action">
                                                            <span class="selection__trigger-label">
                                                                <span class="flag-icon flag-icon--s">
                                                                    <?php
                                                                    $languageFlagImage = FatCache::getCachedUrl(CommonHelper::generateUrl('Image', 'showSpokenLangFlagImage', array($key, 'SMALL'), CONF_WEBROOT_FRONT_URL), CONF_IMG_CACHE_TIME, '.jpg');
                                                                    echo '<img src="' . $languageFlagImage . '" alt="' . $value . '">';
                                                                    ?>
                                                                </span> <?php echo $value; ?>


                                                            </span>
                                                            <span class="selection__trigger-icon"></span>
                                                        </span>
                                                    </label>
                                                    <div class="selection__target">
                                                        <?php echo $proficiencyField->getHTML(); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>
                                <div id="errorDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content__foot">
                        <div class="form__actions">
                            <div class="d-flex align-items-center justify-content-between">
                                <div><input type="button" name="back" onclick="getform(2);" value="<?php echo Label::getLabel('LBL_Back', $siteLangId); ?>"></div>
                                <div>
                                    <input type="submit" name="save" value="<?php echo Label::getLabel('LBL_SAVE', $siteLangId); ?>" />
                                    <input type="button" name="next" onclick="setupStep3(document.frmFormStep3, true)" value="<?php echo Label::getLabel('LBL_NEXT', $siteLangId); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJs(); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        if ($(window).width() > 1199) {
            $('.scrollbar-js').enscroll({
                verticalTrackClass: 'scrollbar-track',
                verticalHandleClass: 'scrollbar-handle'
            });
        }
    });
</script>