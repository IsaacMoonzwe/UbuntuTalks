<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="box -padding-20">
    <div class="d-flex justify-content-between align-items-center">
        <div><h4><?php echo Label::getLabel("LBL_Suggested_time_list") ?></h4></div>
    </div>
</div>
<?php if ($rows) { ?>
    <div class="row justify-content-center align-items-center">
        <div class="col-md-12">
            <div class="-padding-20">
                <table class="table">
                    <thead>
                        <th><?php echo Label::getLabel("LBL_Time"); ?></th>
                        <th><?php echo Label::getLabel("LBL_Total_Votes"); ?></th>
                        <th><?php echo Label::getLabel("LBL_Action"); ?></th>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) { ?>
                            <tr>
                                <?php
                                $user_timezone = MyDate::getUserTimeZone();
                                $reqts_time = MyDate::convertTimeFromSystemToUserTimezone('M d, Y H:i', $row['reqts_time'], true, $user_timezone);
                                ?>
                                <td><?php echo $reqts_time ?></td>
                                <td><?php echo $row['total_followers'] ?></td>
                                <td>
                                    <label class="statustab <?php echo $row['reqts_status'] == ApplicationConstants::ACTIVE ? 'active' : 'inactive' ?>" onclick="changeInterstListStatus(this, <?php echo $row['reqts_id'] ?>)">
                                        <span data-off="Active" data-on="Inactive" class="switch-labels"></span>
                                        <span class="switch-handles"></span>
                                    </label>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <div class="message-display">
        <div class="message-display__icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
            </svg>
        </div>
        <h5><?php echo Label::getLabel("LBL_No_Result_Found!!") ?></h5>
    </div>
<?php } ?>