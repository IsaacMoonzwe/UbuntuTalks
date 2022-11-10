<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupStep1(this); return(false);');
$usrFirstName = $frm->getField('utrequest_first_name');
$usrLastName = $frm->getField('utrequest_last_name');
$usrGender = $frm->getField('utrequest_gender');
$usrPhoneCode = $frm->getField('utrequest_phone_code');
$usrPhoneCode->setFieldTagAttribute('id', 'utrequest_phone_code');
$usrPhone = $frm->getField('utrequest_phone_number');
$usrPhone->setFieldTagAttribute('id', 'utrequest_phone_number');
$usrPhotoId = $frm->getField('user_photo_id');
$usrPhone->value = $usrPhoneCode->value . $usrPhone->value;
?>
<?php $this->includeTemplate('teacher-request/_partial/leftPanel.php', ['siteLangId' => $siteLangId, 'step' => 1]); ?>
<div class="page-block__right">
    <div class="page-block__head">
        <div class="head__title">
            <h4><?php echo Label::getLabel('LBL_Tutor_registration', $siteLangId); ?></h4>
        </div>
    </div> 
    <div class="page-block__body">
        <?php echo $frm->getFormTag() ?>
        <div class="row justify-content-center no-gutters">
            <div class="col-md-12 col-lg-10 col-xl-8">
                <div class="block-content">
                    <div class="block-content__head">
                        <div class="info__content">
                            <h5><?php echo Label::getLabel('LBL_Personal_Information', $siteLangId); ?></h5>
                            <p><?php echo Label::getLabel('LBL_tutor_reg_description', $siteLangId) ?></p>
                        </div>
                    </div>
                    <div class="block-content__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo $usrFirstName->getCaption(); ?>
                                            <?php if ($usrFirstName->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $usrFirstName->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo $usrLastName->getCaption(); ?>
                                            <?php if ($usrLastName->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $usrLastName->getHTML(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo $usrGender->getCaption(); ?>
                                            <?php if ($usrGender->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <div class="row">
                                                <?php foreach ($usrGender->options as $id => $name) { ?>
                                                    <div class="col-6 col-md-6">
                                                        <div class="list-inline">
                                                            <label><span class="radio"><input <?php echo ($usrGender->value == $id) ? 'checked="checked"' : ''; ?> type="radio" name="<?php echo $usrGender->getName(); ?>" value="<?php echo $id; ?>"><i class="input-helper"></i></span><?php echo $name; ?></label>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo $usrPhone->getCaption(); ?>
                                            <?php if ($usrPhone->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper phone--number">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-md-12">
                                                <div class="field_cover">
                                                    <?php echo $usrPhoneCode->getHtml(); ?>
                                                    <?php echo $usrPhone->getHtml(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo $usrPhotoId->getCaption(); ?> <span><?php echo Label::getLabel('LBL_Allowed_Extension', $siteLangId); ?></span>
                                            <?php if ($usrPhone->requirement->isRequired()) { ?>
                                                <span class="spn_must_field">*</span>
                                            <?php } ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $usrPhotoId->getHtml(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="block-content__foot">
                        <div class="form__actions">
                            <div class="d-flex align-items-center justify-content-between">
                                <div></div>
                                <div>
                                    <input type="submit" name="save" value="<?php echo Label::getLabel('LBL_SAVE', $siteLangId); ?>" />
                                    <input type="button" name="next" onclick="setupStep1(document.frmFormStep1, true)" value="<?php echo Label::getLabel('LBL_NEXT', $siteLangId); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $frm->getFieldHtml('resubmit'); ?>
        </form>
        <?php echo $frm->getExternalJs(); ?>
    </div>          
</div>
<script>
    var statusActive = '<?php echo Label::getLabel('LBL_Active'); ?>';
    var statusInActive = '<?php echo Label::getLabel('LBL_In-active'); ?>';
    var countryData = window.intlTelInputGlobals.getCountryData();
    for (var i = 0; i < countryData.length; i++) {
        var country = countryData[i];
        country.name = country.name.replace(/ *\([^)]*\) */g, "");
    }

    var input = document.querySelector("#utrequest_phone_number");
    $("#utrequest_phone_number").inputmask();
    input.addEventListener("countrychange", function () {
        var dial_code = $.trim($('.iti__selected-dial-code').text());
        setPhoneNumberMask();
        $('#utrequest_phone_code').val(dial_code);
    });

    var telInput = window.intlTelInput(input, {
        separateDialCode: true,
        initialCountry: "us",
        utilsScript: siteConstants.webroot + "js/utils.js",
    });

    setPhoneNumberMask = function () {
        let placeholder = $("#utrequest_phone_number").attr("placeholder");
        if (placeholder) {
            placeholderlength = placeholder.length;
            placeholder = placeholder.replace(/[0-9.]/g, '9');
            $("#utrequest_phone_number").inputmask({"mask": placeholder});
        }
    };
    $(document).ready(function () {
        var dial_code = $.trim($('.iti__selected-dial-code').text());
        $('#utrequest_phone_code').val(dial_code);
        setTimeout(() => {
            setPhoneNumberMask();
        }, 100);
    });
</script>