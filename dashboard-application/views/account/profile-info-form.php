<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$profileFrm->setFormTagAttribute('id', 'profileInfoFrm');
$profileFrm->setFormTagAttribute('class', 'form form--horizontal');
$profileFrm->setFormTagAttribute('onsubmit', 'setUpProfileInfo(this, false); return(false);');
if ($profileFrm->getField('user_url_name')) {
    $userIdFld = $profileFrm->getField('user_id');
    $userIdFld->addFieldTagAttribute('id', 'user_id');
    $user_url_name = $profileFrm->getField('user_url_name');
    $user_url_name->developerTags['col'] = 12;
    $user_url_name->htmlAfterField = '<small class=" user_url_string margin-bottom-0">' . CommonHelper::generateFullUrl('teachers', 'profile', [], CONF_WEBROOT_FRONT_URL) . '/<span class="user_url_name_span">' . $user_url_name->value . '</span></small>';
}
if ($profileFrm->getField('us_booking_before')) {
    $profileFrm->getField('us_booking_before')->htmlAfterField = "<br><small>" . Label::getLabel("htmlAfterField_booking_before_text") . ".</small>";
}
$profileFrm->developerTags['colClassPrefix'] = 'col-md-';
$profileFrm->developerTags['fld_default_col'] = 6;
$firstNameField = $profileFrm->getField('user_first_name');
$firstNameField->addFieldTagAttribute('placeholder', $firstNameField->getCaption());
$lastNameField = $profileFrm->getField('user_last_name');
$lastNameField->addFieldTagAttribute('placeholder', $lastNameField->getCaption());
$genderField = $profileFrm->getField('user_gender');
$phoneField = $profileFrm->getField('user_phone');
$countryField = $profileFrm->getField('user_country_id');
$timeZoneField = $profileFrm->getField('user_timezone');
$bookingBeforeField = $profileFrm->getField('us_booking_before');
$freeTrialField = $profileFrm->getField('us_is_trial_lesson_enabled');
$siteLangField = $profileFrm->getField('us_site_lang');
$nextButton = $profileFrm->getField('btn_next');
$nextButton->addFieldTagAttribute('onClick', 'setUpProfileInfo(this.form, true); return(false);');
$profileFrm->getField('user_phone')->addFieldTagAttribute('id', 'user_phone');
$phoneCode = $profileFrm->getField('user_phone_code');
$phoneCode->addFieldTagAttribute('id', 'user_phone_code');
$user_gender = $profileFrm->getField('user_gender');
$user_gender->setOptionListTagAttribute('class', 'list-inline list-inline--onehalf');
$jsonUserRow = FatUtility::convertToJson($userRow);

?>
<script>
    var userData = <?php echo $jsonUserRow; ?>;
    var chat_api_key = '<?php echo FatApp::getConfig('CONF_COMET_CHAT_API_KEY'); ?>';
    var userSeoBaseUrl = '<?php echo CommonHelper::generateFullUrl('teachers', 'profile') . '/'; ?>';
    var userImage = '<?php echo CommonHelper::generateFullUrl('Image', 'user', array($userRow['user_id'])); ?>';
    var selectedTimeZone = '<?php echo $userRow['user_timezone']; ?>';
    var selectedCountry = '<?php echo $userRow['user_country_id']; ?>';
    console.log(selectedTimeZone);
</script>

<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5><?php echo Label::getLabel('LBL_Manage_Profile'); ?></h5>
        </div>
    </div>
</div>
<div class="content-panel__body">
    <div class="form" id="langForm">
        <div class="form__body padding-0">
            <nav class="tabs tabs--line padding-left-6 padding-right-6">
                <ul class="tab-ul-js">
                    <li class="is-active"><a href="javascript:void(0)" onclick="profileInfoForm();"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                    <li><a href="javascript:void(0)" onclick="profileImageForm();" class="profile-imag-li"><?php echo Label::getLabel('LBL_Photos_&_Videos'); ?></a></li>
                    <?php foreach ($languages as $langId => $language) { ?>
                        <li class="profile-lang-tab"><a href="javascript:void(0)" lang-id="<?php echo $langId; ?>" class="profile-lang-li" onclick="getLangProfileInfoForm(<?php echo $langId; ?>);"><?php echo $language['language_name']; ?></a></li>
                    <?php } ?>
                </ul>
            </nav>
            <div class="tabs-data">
                <div id="profileInfoFrmBlock">
                    <div class="action-bar border-top-0">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="action-bar__media margin-right-5">
                                        <div class="g-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="31" height="31" viewBox="0 0 31 31">
                                                <path fill="#fbbb00" d="M6.87,148.63l-1.079,4.028-3.944.083a15.527,15.527,0,0,1-.114-14.474h0l3.511.644,1.538,3.49a9.25,9.25,0,0,0,.087,6.228Z" transform="translate(0 -129.896)" />
                                                <path fill="#518ef8" d="M276.516,208.176a15.494,15.494,0,0,1-5.525,14.983h0l-4.423-.226-.626-3.907a9.238,9.238,0,0,0,3.975-4.717h-8.288v-6.132h14.888Z" transform="translate(-245.787 -195.572)" />
                                                <path fill="#28b446" d="M53.865,318.262h0a15.5,15.5,0,0,1-23.356-4.742l5.023-4.112a9.219,9.219,0,0,0,13.284,4.72Z" transform="translate(-28.662 -290.675)" />
                                                <path fill="#f14336" d="M52.285,3.568,47.263,7.679a9.217,9.217,0,0,0-13.589,4.826L28.625,8.372h0a15.5,15.5,0,0,1,23.661-4.8Z" transform="translate(-26.891)" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="action-bar__content">
                                        <p class="margin-bottom-0"><?php echo Label::getLabel('Lbl_To_Sync_with_google_calendar'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-auto">
                                <a href="<?php echo CommonHelper::generateUrl('Account', 'GoogleCalendarAuthorize'); ?>" class="btn social-button social-button--google">
                                    <span class="social-button__media"><img src="<?php echo CONF_WEBROOT_URL . 'images/google.svg'; ?>" alt="<?php echo Label::getLabel('LBL_Connect_Google_Calendar'); ?>"></span>
                                    <span class="social-button__label"><?php echo Label::getLabel('LBL_Connect_Google_Calendar'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="padding-6">
                        <div class="max-width-80">
                            <?php
                            echo $profileFrm->getFormTag();
                            echo $profileFrm->getFieldHtml('user_phone_code');
                            if ($profileFrm->getField('user_id')) {
                                echo $profileFrm->getFieldHtml('user_id');
                            }
                            ?>
                            <?php if (isset($user_url_name)) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label">
                                                    <?php echo $user_url_name->getCaption(); ?>
                                                    <?php if ($user_url_name->requirement->isRequired()) { ?>
                                                        <span class="spn_must_field">*</span>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $user_url_name->getHTML(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Name'); ?>
                                                <?php if ($firstNameField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <div class="custom-cols custom-cols--onehal">
                                                    <ul>
                                                        <li><?php echo $firstNameField->getHTML('user_first_name'); ?></li>
                                                        <li><?php echo $lastNameField->getHTML('user_last_name'); ?></li>
                                                    </ul>
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
                                            <label class="field_label"><?php echo $genderField->getCaption(); ?>
                                                <?php if ($genderField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <div class="custom-cols custom-cols--onehal">
                                                    <ul class="list-inline list-inline--onehalf">
                                                        <?php foreach ($genderField->options as $id => $name) { ?>
                                                            <li class="<?php echo ($genderField->value == $id) ? 'is-active' : ''; ?>"><label><span class="radio"><input type="radio" name="<?php echo $genderField->getName(); ?>" value="<?php echo $id; ?>" <?php echo ($genderField->value == $id) ? 'checked' : ''; ?>><i class="input-helper"></i></span><?php echo $name; ?></label></li>
                                                        <?php } ?>
                                                    </ul>
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
                                            <label class="field_label">
                                                <?php echo $phoneField->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                                <?php if ($phoneField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $phoneField->getHTML(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"> <?php echo $countryField->getCaption(); ?>
                                                <?php if ($countryField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $countryField->getHTML(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"> <?php echo $timeZoneField->getCaption(); ?>
                                                <?php if ($timeZoneField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $timeZoneField->getHTML(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($bookingBeforeField) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label"> <?php echo $bookingBeforeField->getCaption(); ?>
                                                    <?php if ($bookingBeforeField->requirement->isRequired()) { ?>
                                                        <span class="spn_must_field">*</span>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <?php echo $bookingBeforeField->getHTML(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"> <?php echo $siteLangField->getCaption(); ?>
                                                <?php if ($siteLangField->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $siteLangField->getHTML(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($freeTrialField) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label"> <?php echo $freeTrialField->getCaption(); ?>
                                                    <?php if ($freeTrialField->requirement->isRequired()) { ?>
                                                        <span class="spn_must_field">*</span>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <label class="switch-group d-flex align-items-center justify-content-between">
                                                        <span class="switch-group__label free-trial-status-js"><?php echo ($freeTrialField->checked) ? Label::getLabel('LBL_Active') : Label::getLabel('LBL_In-active'); ?></span>
                                                        <span class="switch switch--small">
                                                            <input class="switch__label" type="<?php echo $freeTrialField->fldType; ?>" name="<?php echo $freeTrialField->getName(); ?>" value="<?php echo $freeTrialField->value; ?>" <?php echo ($freeTrialField->checked) ? 'checked' : ''; ?>>
                                                            <i class="switch__handle bg-green"></i>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row submit-row">
                                <div class="col-sm-auto">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $profileFrm->getFieldHtml('btn_submit'); ?>
                                                <?php echo $profileFrm->getFieldHtml('btn_next'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <?php echo $profileFrm->getExternalJS(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    var input = document.querySelector("#user_phone");
    $("#user_phone").inputmask();
    input.addEventListener("countrychange", function() {
        var dial_code = $.trim($('.iti__selected-dial-code').text());
        setPhoneNumberMask();
        $('#user_phone_code').val(dial_code);
    });
    var telInput = window.intlTelInput(input, {
        separateDialCode: true,
        initialCountry: "us",
        utilsScript: siteConstants.webroot + "js/utils.js",
    });
    setPhoneNumberMask = function() {
        let placeholder = $("#user_phone").attr("placeholder");
        if (placeholder) {
            placeholderlength = placeholder.length;
            placeholder = placeholder.replace(/[0-9.]/g, '9');
            $("#user_phone").inputmask({
                "mask": placeholder
            });
        }
    };
    $(document).ready(function() {
        var dial_code = $.trim($('.iti__selected-dial-code').text());
        $('#user_phone_code').val(dial_code);
        setTimeout(() => {
            setPhoneNumberMask();
        }, 100);
        $("[name='user_timezone'],[name='user_country_id']").select2();
        $('input[name="user_url_name"]').on('keypress', function(e) {
            if (e.which == 32) {
                return false;
            }
        });
        $('input[name="user_url_name"]').on('change', function(e) {
            var user_name = $(this).val();
            user_name = user_name.trim(user_name.toLowerCase());
            user_name = user_name.replace(/[\s,<>\/\"&#%+?$@=]/g, "-");
            user_name = user_name.replace(/[\s\s]+/g, '-');
            user_name = user_name.replace(/[\-]+/g, '-');
            $(this).val(user_name);
            $('.user_url_name_span').html(user_name);
            if (user_name != '') {
                checkUnique($(this), 'tbl_users', 'user_url_name', 'user_id', $('#user_id'), []);
            }
        });
        $('input[name="us_is_trial_lesson_enabled"]').on('change', function() {
            let status = ($(this).is(':checked')) ? statusActive : statusInActive;
            $('.free-trial-status-js').text(status);
        });

        function coun_timezone() {
            var product_code = $("[name='user_country_id'] option:selected").val();
            $.ajax({
                type: "post",
                url: fcom.makeUrl('Account', 'country'),
                data: {
                    product_code: product_code
                },
                dataType: 'json',
                success: function(data) {
                    var len = data.length;
                    $("[name='user_timezone']").empty();
                    $.each(data, function(k, v) {
                        var id = k;
                        var name = v;
                        // console.log(id);
                        if (id == selectedTimeZone) {
                            $("[name='user_timezone']").append("<option value='" + id + "' selected>" + name + "</option>");
                        } else
                            $("[name='user_timezone']").append("<option value='" + id + "'>" + name + "</option>");
                        // $("[name='user_timezone']").select2("val","Pacific/Honolulu HST");
                    })


                }
            });
        }

        function country_timezone() {
            $("[name='user_country_id']").select2().on('select2:select', function(e) {
                coun_timezone();

            });
        }
        $(document).ready(function() {
            $("[name='user_country_id']").val(selectedCountry);
            $("[name='user_country_id']").trigger('change.select2');

            country_timezone();
            coun_timezone();


            // $("[name='user_timezone']").trigger('change.select2');

        });
    });
</script>