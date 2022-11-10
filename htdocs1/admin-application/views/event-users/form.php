<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($user_id > 0) {
    $fld_credential_username = $frmUser->getField('credential_username');
    $fld_credential_username->setFieldTagAttribute('disabled', 'disabled');
    $user_email = $frmUser->getField('credential_email');
    $user_email->setFieldTagAttribute('disabled', 'disabled');
}
$frmUser->developerTags['colClassPrefix'] = 'col-md-';
$frmUser->developerTags['fld_default_col'] = 12;
$frmUser->setFormTagAttribute('class', 'web_form form_horizontal');
$frmUser->setFormTagAttribute('onsubmit', 'setupUsers(this); return(false);');
$countryFld = $frmUser->getField('user_country_id');
$countryFld->setFieldTagAttribute('id', 'user_country_id');
$frmUser->getField('user_phone')->addFieldTagAttribute('id', 'user_phone');
$frmUser->getField('user_phone_code')->addFieldTagAttribute('id', 'user_phone_code');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_USER_SETUP', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frmUser->getFormHtml(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
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
        var dial_code = $.trim($('.iti__selected-dial-code').text());
        $('#user_phone_code').val(dial_code);
        setTimeout(() => {
            setPhoneNumberMask();
        }, 100);
   
    });
</script>