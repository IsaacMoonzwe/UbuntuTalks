<div class="row events-tickets-section">
    <div class="col-lg-12">
        <div class="top-tab">
            <div class="agenda-tabs">
                <div class="left-item">
                    <div class="agenda-tab-list">
                        <ul class="nav nav-tabs" id="agenda-tab" role="tablist">
                            <?php
                            $num = 0;
                            array_multisort(array_column($AgendaEventsList, 'registration_starting_days'), $AgendaEventsList);
                            // echo "<pre>";
                            // print_r($AgendaEventsList);

                            foreach ($AgendaEventsList as $key => $value) {
                                if ($value['available_data'] > 0) {
                                    $splitTimeStamp = explode(" ", $value['agenda_start_time']);
                                    $date = $splitTimeStamp[0];
                                    $DiffTime = $splitTimeStamp[1];
                                    $nameOfDay = date('D', strtotime($date));
                                    if ($num == 0) {
                                        $activeClass = "active";
                                    } else {
                                        $activeClass = "";
                                    }
                                    $Starting_date = $value['registration_starting_date'];
                                    $CreatedDate_Convert = date("d F", strtotime($Starting_date));
                                    $Starting_date_title = $value['registration_starting_days'];
                                    $Agenda_Title = $CreatedDate_Convert . "-" . $Starting_date_title;
                            ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $activeClass; ?>" id="<?php echo "agenda" . $value['three_reasons_id'] . "-tab"; ?>" data-toggle="tab" onclick="tabClick(<?php echo $value['three_reasons_id']; ?>);" href="#<?php echo $value['three_reasons_id']; ?>" role="tab" aria-controls="agendaOne" aria-selected="true">
                                            <h6 class="event-name"><?php echo $Agenda_Title ?></h6>
                                        </a>
                                    </li>
                            <?php $num++;
                                }
                            } ?>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <div class="row agenda-section-title">
            <div class="col-md-3">
                <h4><?php echo Label::getLabel('LBL_Time'); ?></h4>
            </div>
            <div class="col-md-3">
                <h4><?php echo Label::getLabel('LBL_Diet_Sessions'); ?></h4>
            </div>
            <div class="col-md-3">
                <h4><?php echo Label::getLabel('LBL_Location'); ?></h4>
            </div>
            <div class="col-md-3">
                <h4><?php echo Label::getLabel('LBL_Agenda'); ?></h4>
            </div>
        </div>


        <div class="tab-content" id="agendaContent">
            <?php
            $agendaArray = array();
            $agenda_index = 0;
            foreach ($FullAgendaCategoriesList as $value) {
                $duration = '';
                $splitTimeStamp = explode(" ", $value['agenda_start_time']);
                $StartTime = $splitTimeStamp[1];

                $splitTimeStampEndTime = explode(" ", $value['agenda_end_time']);
                $EndTime = $splitTimeStampEndTime[1];


                $time_in_12_hour_format  = date("g:i a", strtotime($StartTime));
                $dateDiff = intval((strtotime($value['agenda_end_time']) - strtotime($value['agenda_start_time'])) / 60);
                $hours = intval($dateDiff / 60);
                $minutes = $dateDiff % 60;
                $origin = new DateTime($value['agenda_start_time']);
                $target = new DateTime($value['agenda_end_time']);
                $interval = $origin->diff($target);
                $minutes_diff = ($interval->format('%i'));
                $hour_diff = ($interval->format('%h'));
                $day_diff = (($interval->format('%d')));
                if ($day_diff > 0) {
                    $duration = $day_diff . " days ";
                }
                if ($hour_diff > 0) {
                    $duration = $hour_diff . " hours ";
                }
                if ($minutes_diff > 0) {
                    $duration .= $minutes_diff . " minutes";
                }
                if ($duration == '') {
                    $duration = "0 hours";
                }

            ?>
                <div class="tab-pane tab_agenda fade show" id="<?php echo $value['event_id']; ?>" role="tabpanel" aria-labelledby="<?php echo "agenda" . $value['event_id'] . "-tab"; ?>">
                    <div class="timeline-item">
                        <div class="col-md-3 timeline">
                            <div class="timeline-sticky timezone">
                                <h5><?php echo $StartTime . " - " . $EndTime; ?></h5>
                            </div>
                        </div>
                        <div class="col-md-3 timeline-track">
                            <h5 class="timeline-title">
                                <a class="#"><?php echo $value['agenda_schedule']; ?></a>
                            </h5>

                        </div>
                        <div class="col-md-3">
                            <div class="timeline-track-sub-text">
                                <p>
                                    <span><img src="https://iili.io/SXubX2.png" alt=""><?php echo $value['agenda_event_location']; ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="timeline-track-sub-text">
                                <p>
                                    <span><?php echo $value['agenda_information']; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<script>
    var list_data = document.getElementsByClassName("tab_agenda");

    var initial_list_id = document.getElementsByClassName("tab_agenda")[0].id;
    tabClick(initial_list_id);

    function tabClick(id) {
        var list = document.getElementsByClassName("tab_agenda");
        for (var i = 0; i < list.length; i++) {
            var tab_id = list[i].getAttribute("id");
            if (tab_id == id) {
                list[i].classList.add("active");
            } else {
                list[i].classList.remove("active");
            }
        }
    }
</script>