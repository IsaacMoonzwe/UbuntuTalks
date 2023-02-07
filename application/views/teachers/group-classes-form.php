<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$userTimezone = MyDate::getUserTimeZone();
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', date('Y-m-d H:i:s'), true, $userTimezone);
$curDateTimeUnix = strtotime($curDateTime);
$frmOnlineContact->setFormTagAttribute('onSubmit', 'contactSubmit(this); return false;');
$frmOnlineContact->setFormTagAttribute('class', 'form');
$frmOnlineContact->developerTags['colClassPrefix'] = 'col-sm-';
$frmOnlineContact->developerTags['fld_default_col'] = 6;
$countryField = $frmOnlineContact->getField('user_country_id[]');
$timeZoneField = $frmOnlineContact->getField('user_timezone[]');
$dateformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
$timeformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
$frmOnlineContact->getField('grpcls_start_datetime')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
$frmOnlineContact->getField('grpcls_end_datetime')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_Contact_Form'); ?></h2>
    <?php //echo $frmOnlineContact->getFormHtml(); 
    ?>
    <?php echo $frmOnlineContact->getFormTag() ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('first_name'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('last_name'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Email_Address', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('email_address'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Phone_No', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('phone_number'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Start_time', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('grpcls_start_datetime'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_End_time', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('grpcls_end_datetime'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Organisation_Name', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('organisation_name'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Organisation_Url', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('organisation_url'); ?>
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
        <input type="hidden" name="timezone"/>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Objective', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('objective_lesson'); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Group_Size', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('group_size'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Group_Type', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('group_type'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Language', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('Language'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="field-set">
                <div class="caption-wraper">
                    <label class="field_label">
                        <?php echo Label::getLabel('LBL_Others', $siteLangId) ?>
                    </label>
                </div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('others'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '') { ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="field-set">
                    <div class="field-wraper">
                        <div class="g-recaptcha" data-sitekey="<?php echo FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, ''); ?>"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-12">
            <div class="field-set">
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frmOnlineContact->getFieldHTML('btn_submit'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $frmOnlineContact->getExternalJS(); ?>
    </form>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js" integrity="sha512-RtZU3AyMVArmHLiW0suEZ9McadTdegwbgtiQl5Qqo9kunkVg1ofwueXD8/8wv3Af8jkME3DDe3yLfR8HSJfT2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script>
    // setTimeout(function() {
    // $(".select2-hidden-accessible").select2({
    //         multiple: true,
    //     });
    // }, 100);
    $(document).ready(function() {
        $("[name='user_country_id[]']").select2({
            multiple: true,
        });
        $("[name='user_timezone[]']").select2({
            multiple:true,
        });
        var selectedTimeZone = $("[name='user_timezone[]'] option:selected").val();
        var selectedCountry = $("[name='user_country_id[]'] option:selected").val();

        function coun_timezone() {
            var product_code = $("[name='user_country_id[]'] option:selected").val();
            $.ajax({
                type: "post",
                url: fcom.makeUrl('Teachers', 'country'),
                data: {
                    product_code: product_code
                },
                dataType: 'json',
                success: function(data) {
                    var len = data.length;
                    $("[name='user_timezone[]']").empty();
                    $.each(data, function(k, v) {
                        var id = k;
                        var name = v;
                        if (id == selectedTimeZone) {
                            $("[name='user_timezone[]']").append("<option value='" + id + "' selected>" + name + "</option>");
                        } else
                            $("[name='user_timezone[]']").append("<option value='" + id + "'>" + name + "</option>");
                        // $("[name='user_timezone']").select2("val","Pacific/Honolulu HST");
                    })
                }
            });
        }
        var country_name = selectedCountry;

        function country_timezone() {
            var product_code = $("[name='user_country_id[]'] option:selected").val();
            $("[name='user_country_id[]']").select2().on('select2:select', function(e) {
                coun_timezone();
            });
        }
        
        $(document).ready(function() {
            $("[name='user_country_id[]']").val(selectedCountry);
            $("[name='user_country_id[]']").trigger('change.select2');
            country_timezone();
            coun_timezone();
            // $("[name='user_timezone[]']").trigger('change.select2');

        });
    });
    jQuery('#grpcls_start_datetime,#grpcls_end_datetime').each(function() {
        $(this).datetimepicker({
            format: 'Y-m-d H:i'
        });
    });
</script>