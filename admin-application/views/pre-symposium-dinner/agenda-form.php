<?php
    defined('SYSTEM_INIT') or die('Invalid Usage.');
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
                        <li><a class="active" href="javascript:void(0)" onclick="editTestimonialForm(<?php echo $testimonial_id ?>);"><?php echo Label::getLabel('LBL_View_Agenda_Schedule', $adminLangId); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                             <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Start time</th>
                                        <th>End time</th>
                                        <th>Schedule</th>
                                        <th>Event location</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                    <?php if(!empty($EventListingCategoriesList)){
                                        foreach ($EventListingCategoriesList as  $value) {  ?>
                                            <tr>
                                                <td><?php echo $value['agenda_start_time']; ?></td>
                                                <td><?php echo $value['agenda_end_time']; ?></td>
                                                <td><?php echo $value['agenda_schedule']; ?></td>
                                                <td><?php echo $value['agenda_event_location']; ?></td>
                                            </tr>
                                        <?php }}
                                            else{ echo "No Records found"; } ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
