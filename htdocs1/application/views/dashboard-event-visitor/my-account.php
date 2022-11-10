<?php
$profileFrm->setFormTagAttribute('id', 'profileInfoFrm');
$profileFrm->setFormTagAttribute('class', 'form form--horizontal');
$profileFrm->setFormTagAttribute('onsubmit', 'setUpProfileInfo(this, false); return(false);');
$profileFrm->developerTags['colClassPrefix'] = 'col-md-';
$profileFrm->developerTags['fld_default_col'] = 6;
$firstNameField = $profileFrm->getField('user_first_name');
$firstNameField->addFieldTagAttribute('placeholder', $firstNameField->getCaption());
$foodAllergiesNameField = $profileFrm->getField('user_food_allergies');
$foodAllergiesNameField->addFieldTagAttribute('placeholder', $foodAllergiesNameField->getCaption());
$otherFoodrestrictionField = $profileFrm->getField('user_other_food_restriction');
$otherFoodrestrictionField->addFieldTagAttribute('placeholder', $otherFoodrestrictionField->getCaption());
$lastNameField = $profileFrm->getField('user_last_name');
$lastNameField->addFieldTagAttribute('placeholder', $lastNameField->getCaption());
$registerPlanField = $profileFrm->getField('user_sponsorship_plan');
$registerPlanField->addFieldTagAttribute('readonly', true);
$user_become_sponsership_plan = $profileFrm->getField('user_become_sponsership_plan');
$user_become_sponsership_plan->addFieldTagAttribute('readonly', true);
$genderField = $profileFrm->getField('user_gender');
$phoneField = $profileFrm->getField('user_phone');
$countryField = $profileFrm->getField('user_country_id');
$timeZoneField = $profileFrm->getField('user_timezone');
$profileFrm->getField('user_phone')->addFieldTagAttribute('id', 'user_phone');
$phoneCode = $profileFrm->getField('user_phone_code');
$phoneCode->addFieldTagAttribute('id', 'user_phone_code');
$user_gender = $profileFrm->getField('user_gender');
$dietField = $profileFrm->getField('user_food_department');
$user_gender->setOptionListTagAttribute('class', 'list-inline list-inline--onehalf');
$user_food_department = $profileFrm->getField('user_food_department');
$user_food_department->setOptionListTagAttribute('class', 'diet-boxes');
$jsonUserRow = FatUtility::convertToJson($userRow);
$frm->setFormTagAttribute('onsubmit', 'setUpEmail(this); return(false);');
$currentEmail = $frm->getField('user_email');
$newEmail = $frm->getField('new_email');
$currentPassword = $frm->getField('current_password');
$submitBtn = $frm->getField('btn_submit');
$submitBtn->setFieldTagAttribute('form', $frm->getFormTagAttribute('id'));
$ChnagePasswordfrm->setFormTagAttribute('onsubmit', 'setUpPassword(this); return(false);');
$ChangecurrentPassword = $ChnagePasswordfrm->getField('current_password');
$ChangenewPassword = $ChnagePasswordfrm->getField('new_password');
$ChangeconfNewPassword = $ChnagePasswordfrm->getField('conf_new_password');
$ChangesubmitBtn = $ChnagePasswordfrm->getField('btn_submit');
$ChangesubmitBtn->setFieldTagAttribute('form', $ChnagePasswordfrm->getFormTagAttribute('id'));

// Profile Image
$profileImgFrm->setFormTagAttribute('action', CommonHelper::generateUrl('DashboardEventVisitor', 'setUpProfileImage'));
$profileImgFrm->setFormTagAttribute('onsubmit', 'sumbmitProfileImage(false); return(false);');
$profileImgFrm->setFormTagAttribute('id', 'frmProfile');
$profileImgFrm->setFormTagAttribute('class', 'form form--horizontal');
$profileImageField = $profileImgFrm->getField('user_profile_image');

?>

<script>
    var userData = <?php echo $jsonUserRow; ?>;
    var selectedTimeZone = '<?php echo $userRow['user_timezone']; ?>';
    var selectedCountry = '<?php echo $userRow['user_country_id']; ?>';
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="tab-content" id="myTabContent">
    <div class="dashboard-section account-section">

        <div class="tab">
            <button class="tablinks" onclick="openCity(event, 'PersonalInfo')" id="defaultOpen">Personal Info</button>
            <button class="tablinks" onclick="openCity(event, 'ProfileImage')">Photos</button>
            <button class="tablinks" onclick="openCity(event, 'Email')">Email</button>
            <button class="tablinks" onclick="openCity(event, 'Password')">Password</button>
            <button class="tablinks" onclick="getCookieConsentForm(false)">Cookie Consent</button>
        </div>

        <div id="PersonalInfo" class="tabcontent">
            <!-- Contact Form Information -->
            <div class="padding-6 events-tickets-section">
                <div class="max-width-80">
                    <?php
                    echo $profileFrm->getFormTag();
                    echo $profileFrm->getFieldHtml('user_phone_code');
                    if ($profileFrm->getField('user_id')) {
                        echo $profileFrm->getFieldHtml('user_id');
                    }
                    ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Personal_Info'); ?></h4>
                            </div>

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
                                            <ul class="event-profile-name">
                                                <li class="event-first-name"><?php echo $firstNameField->getHTML('user_first_name'); ?></li>
                                                <li class="event-second-name"><?php echo $lastNameField->getHTML('user_last_name'); ?></li>
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
                                            <ul class="list-inline list-inline--onehalf radio-button">
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

                    <div class="row">
                        <div class="col-sm-auto">
                            <div class="field-set">
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <?php echo $profileFrm->getFieldHtml('btn_submit'); ?>
                                        <!-- <?php echo $profileFrm->getFieldHtml('btn_next'); ?> -->
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


        <div id="ProfileImage" class="tabcontent">
            <div class="padding-6">
                <div class="max-width-80">
                    <?php
                    echo $profileImgFrm->getFormTag();
                    echo $profileImgFrm->getFieldHtml('update_profile_img');
                    echo $profileImgFrm->getFieldHtml('rotate_left');
                    echo $profileImgFrm->getFieldHtml('rotate_right');
                    echo $profileImgFrm->getFieldHtml('remove_profile_img');
                    echo $profileImgFrm->getFieldHtml('action');
                    echo $profileImgFrm->getFieldHtml('img_data');
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label"><?php echo $profileImageField->getCaption(); ?>
                                        <?php if ($profileImageField->requirement->isRequired()) { ?>
                                            <span class="spn_must_field">*</span>
                                        <?php } ?>
                                    </label>
                                    <small class="margin-0"><?php echo Label::getLabel('LBL_PROFILE_IMAGE_FIELD_INFO_TEXT'); ?></small>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <div class="profile-media">
                                            <div class="avtar avtar--xlarge" data-title="<?php echo CommonHelper::getFirstChar($userFirstName); ?>">
                                                <?php
                                                if ($isProfilePicUploaded) {
                                                    echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM'), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"  alt="' . $userFirstName . '" />';
                                                }
                                                ?>
                                            </div>
                                            <div class="buttons-group margin-top-4">
                                                <span class="btn btn--bordered color-primary btn--small btn--fileupload btn--wide margin-right-2">
                                                    <?php
                                                    echo $profileImageField->getHTML();
                                                    echo ($isProfilePicUploaded) ? Label::getLabel('LBL_Edit') : Label::getLabel('LBL_Add');
                                                    ?>
                                                </span>
                                                <?php if (true == $isProfilePicUploaded) { ?>
                                                    <a class="btn btn--bordered color-red btn--small btn--wide" href="javascript:void(0);" onClick="removeProfileImage()"><?php echo Label::getLabel('LBL_Remove'); ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
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
                                        echo $profileImgFrm->getFieldHtml('btn_submit');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                    <?php echo $profileImgFrm->getExternalJS(); ?>
                </div>
            </div>
        </div>

        <div id="Email" class="tabcontent">
            <!-- Email Form Functionlity -->
            <div class="form events-tickets-section">
                <div class="form__body padding-0">
                    <div class="tabs-data">
                        <div class="">
                            <?php
                            echo $frm->getFormTag();
                            echo $frm->getFieldHTML('user_id');
                            ?>
                            <div>
                                <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Change_Your_Email'); ?></h4>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $currentEmail->getCaption(); ?>
                                                <?php if ($currentEmail->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $currentEmail->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $newEmail->getCaption(); ?>
                                                <?php if ($newEmail->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $newEmail->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $currentPassword->getCaption(); ?>
                                                <?php if ($currentPassword->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $currentPassword->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <?php echo $frm->getExternalJS(); ?>
                        </div>
                    </div>
                </div>
                <div class="form__actions">
                    <div class="align-items-center justify-content-between">
                        <div>
                            <!-- <input type="button" value="Back"> -->
                        </div>
                        <div>
                            <?php echo $frm->getFieldHTML('btn_submit'); ?>
                            <!-- <input type="button" value="Next"> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="Password" class="tabcontent">
            <div class="form events-tickets-section">
                <div class="form__body padding-0">
                    <div class="tabs-data">
                        <div class="">
                            <?php echo $ChnagePasswordfrm->getFormTag(); ?>
                            <div>
                                <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Change_Your_Password'); ?></h4>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $ChangecurrentPassword->getCaption(); ?>
                                                <?php if ($ChangecurrentPassword->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $ChangecurrentPassword->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $ChangenewPassword->getCaption(); ?>
                                                <?php if ($ChangenewPassword->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $ChangenewPassword->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $ChangeconfNewPassword->getCaption(); ?>
                                                <?php if ($ChangeconfNewPassword->requirement->isRequired()) { ?>
                                                    <span class="spn_must_field">*</span>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $ChangeconfNewPassword->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <?php echo $ChnagePasswordfrm->getExternalJS(); ?>
                        </div>
                    </div>
                </div>
                <div class="form__actions">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <!-- <input type="button" value="Back"> -->
                        </div>
                        <div>
                            <?php echo $ChnagePasswordfrm->getFieldHTML('btn_submit'); ?>
                            <!-- <input type="button" value="Next"> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var userData = <?php echo $jsonUserRow; ?>;
    var selectedTimeZone = '<?php echo $userRow['user_timezone']; ?>';
    var selectedCountry = '<?php echo $userRow['user_country_id']; ?>';
    console.log(selectedTimeZone);
</script>
<script>
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
        document.getElementById("defaultOpen").click();
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
                checkUnique($(this), 'tbl_event_users', 'user_url_name', 'user_id', $('#user_id'), []);
            }
        });

        function coun_timezone() {
            var product_code = $("[name='user_country_id'] option:selected").val();
            $.ajax({
                type: "post",
                url: fcom.makeUrl('DashboardEventVisitor', 'country'),
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
                        $("[name='user_timezone']").append("<option value='" + id + "'>" + name + "</option>");
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
            country_timezone();
            coun_timezone();
        });
    });

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;

        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    // document.getElementById("defaultOpen").click();
</script>