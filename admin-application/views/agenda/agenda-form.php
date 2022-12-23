<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$agendafrm->setFormTagAttribute('class', 'web_form form_horizontal');
$agendafrm->setFormTagAttribute('onsubmit', 'agendaSetupTestimonial(this); return(false);');
$agendafrm->developerTags['colClassPrefix'] = 'col-md-';
$agendafrm->developerTags['fld_default_col'] = 6;
$dateformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT', FatUtility::VAR_STRING, 'Y-m-d');
$timeformat = FatApp::getConfig('CONF_DATEPICKER_FORMAT_TIME', FatUtility::VAR_STRING, 'H:i');
$agendafrm->getField('agenda_start_time')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
$agendafrm->getField('agenda_end_time')->setFieldTagAttribute('data-fatdatetimeformat', $dateformat . ' ' . $timeformat);
$agendafrm->getField('agenda_start_time')->setFieldTagAttribute('data-type', 'start');
$agendafrm->getField('agenda_end_time')->setFieldTagAttribute('data-type', 'end');
$agendafrm->getField('event_starting_days')->setFieldTagAttribute('id', 'event_starting_days');
$agendafrm->getField('event_listing')->setFieldTagAttribute('id', 'event_listing');

?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Agenda_Setup', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="editTestimonialForm(<?php echo $testimonial_id ?>);"><?php echo Label::getLabel('LBL_Agenda', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php //echo $agendafrm->getFormHtml(); 
                            ?>
                            <?php echo $agendafrm->getFormTag() ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Events_Listing', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('event_listing'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Events_Starting_Days', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('event_starting_days'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Agenda_Starting_Days_(Title)', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_starting_days'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row dynamic-field" id="dynamic-field-1">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Agenda_Start_Time', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_start_time'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Agenda_end_Time', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_end_time'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Agenda_Schedule', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_schedule'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Event_location', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_event_location'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Event_Information', $siteLangId) ?></label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_information'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php echo $agendafrm->getFieldHTML('agenda_id'); ?>
                                                <?php echo $agendafrm->getFieldHTML('btn_submit'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                function event_days_data() {
                    var product_code = $("[name='event_listing'] option:selected").val();
                    console.log("product", product_code);
                    $.ajax({
                        type: "post",
                        url: fcom.makeUrl('Agenda', 'event_days'),
                        data: {
                            product_code: product_code
                        },
                        dataType: 'json',
                        success: function(data) {
                            var len = data.length;
                            console.log(data);
                            $("[name='event_starting_days']").empty();
                            $.each(data, function(k, v) {
                                var id = k;
                                var name = v;
                                $("[name='event_starting_days']").append("<option value='" + id + "'>" + name + "</option>");
                            })
                        }
                    });
                }

                function event_data() {
                    $("[name='event_listing']").change(function(e) {
                        event_days_data();
                    });
                }
                $(document).ready(function() {
                    event_data();
                    event_days_data();
                    var buttonAdd = $("#add-button");
                    var buttonRemove = $("#remove-button");
                    var className = ".dynamic-field";
                    var count = 0;
                    var field = "";
                    var maxFields = 50;

                    function totalFields() {
                        return $(className).length;
                    }

                    function addNewField() {
                        count = totalFields() + 1;
                        field = $("#dynamic-field-1").clone();
                        field.attr("id", "dynamic-field-" + count);
                        field.children("label").text("Field " + count);
                        field.find("input").val("");
                        $(className + ":last").after($(field));
                        jQuery('#agenda_start_time,#agenda_end_time').each(function() {
                            var dateType = $(this).attr('data-type');
                            $(this).datetimepicker({
                                format: 'Y-m-d H:i',
                                onClose: function(date) {

                                    onChangeDateTime(date, dateType);
                                },
                            });
                        });

                    }


                    function removeLastField() {
                        if (totalFields() > 1) {
                            $(className + ":last").remove();
                        }
                    }

                    function enableButtonRemove() {
                        if (totalFields() === 2) {
                            buttonRemove.removeAttr("disabled");
                            buttonRemove.addClass("shadow-sm");
                        }
                    }

                    function disableButtonRemove() {
                        if (totalFields() === 1) {
                            buttonRemove.attr("disabled", "disabled");
                            buttonRemove.removeClass("shadow-sm");
                        }
                    }

                    function disableButtonAdd() {
                        if (totalFields() === maxFields) {
                            buttonAdd.attr("disabled", "disabled");
                            buttonAdd.removeClass("shadow-sm");
                        }
                    }

                    function enableButtonAdd() {
                        if (totalFields() === (maxFields - 1)) {
                            buttonAdd.removeAttr("disabled");
                            buttonAdd.addClass("shadow-sm");
                        }
                    }

                    buttonAdd.click(function() {
                        addNewField();
                        enableButtonRemove();
                        disableButtonAdd();
                    });

                    buttonRemove.click(function() {
                        removeLastField();
                        disableButtonRemove();
                        enableButtonAdd();
                    });
                });
                var startTime = '';
                var endTime = '';

                // $("[name='event_listing'],[name='event_starting_days']").select2();
            </script>