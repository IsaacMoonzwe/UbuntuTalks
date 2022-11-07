<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .view-information-section {
        padding: 25px 0px 25px 0px;
        text-align: center !important;
    }
</style>
<section class="section">
    <div class="sectionbody">

        <!-- Personal Information -->
        <table class="table table--details">
            <h2 class="view-information-section"><?php echo Label::getLabel('LBL_Personal_Info'); ?></h2>
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Username', $adminLangId); ?>:</strong> <?php echo $data['user_first_name'] . ' ' . $data['user_last_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_User_Phone', $adminLangId); ?>:</strong> <?php echo $data['user_phone_code'] . ' ' . $data['user_phone']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_User_Date', $adminLangId); ?>:</strong> <?php echo MyDate::format($data['user_added_on'], true, true, Admin::getAdminTimeZone()); ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Email', $adminLangId); ?>:</strong> <?php echo $data['credential_email']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Country', $adminLangId); ?>:</strong> <?php echo $data['country_name']; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Food Information -->
        <table class="table table--details">
            <h2 class="view-information-section"><?php echo Label::getLabel('LBL_Food_Info'); ?></h2>
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Diet_Requirement', $adminLangId); ?>:</strong> <?php echo $data['user_food_department']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Food_Allergies', $adminLangId); ?>:</strong> <?php echo $data['user_food_allergies']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Other_Food_Restrictions', $adminLangId); ?>:</strong> <?php echo $data['user_other_food_restriction']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Other_Food_Requirement', $adminLangId); ?>:</strong> <?php echo $data['user_other_requirement']; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Events Information -->
        <table class="table table--details">
            <h2 class="view-information-section"><?php echo Label::getLabel('LBL_Events_Info'); ?></h2>
            <tbody>
                <tr>
                    <th><?php echo Label::getLabel('LBL_Events_Name'); ?></th>
                    <th><?php echo Label::getLabel('LBL_Total_Tickets'); ?></th>
                    <th><?php echo Label::getLabel('LBL_Tickets'); ?></th>
                </tr>
                <?php
                $sr_no = 1;
                if (!empty($EventplanResult)) {
                    foreach ($EventplanResult as $key => $value) {
                ?>
                        <tr>
                            <td><?php echo " (" . $sr_no . ") " . $value['plan_name']; ?></td>
                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                            <td><a href="<?php echo $value['event_user_ticket_download_url']; ?>" download="<?php echo $value['plan_name'] . '.jpeg'; ?>"><i class="fa fa-ticket" style="font-size:24px;color:red"></i></a></td>
                        </tr>
                <?php
                        $sr_no++;
                    }
                } else {
                    echo Label::getLabel('LBL_No_Records');
                }
                ?>
            </tbody>
        </table>

        <!-- Sponsorship Information -->
        <table class="table table--details">
            <h2 class="view-information-section"><?php echo Label::getLabel('LBL_Sponsorship_Info'); ?></h2>
            <tbody>
                <tr>
                    <th><?php echo Label::getLabel('LBL_Sponsorship_Plan'); ?></th>
                    <th><?php echo Label::getLabel('LBL_Qty'); ?></th>
                </tr>
                <?php
                $sr_no = 1;
                if (!empty($PurchaseSponserShip)) {
                    foreach ($PurchaseSponserShip as $key => $value) {
                ?>
                        <tr>
                            <td><?php echo " (" . $sr_no . ") " . $key; ?></td>
                            <td><?php echo $value; ?></td>
                        </tr>
                <?php
                        $sr_no++;
                    }
                } else {
                    echo Label::getLabel('LBL_No_Records');
                }
                ?>
            </tbody>
        </table>

        <!-- Donation Information -->
        <table class="table table--details">
            <h2 class="view-information-section"><?php echo Label::getLabel('LBL_Donation_Info'); ?></h2>
            <tbody>
                <tr>
                    <td>
                        <?php
                        $total = 0;
                        foreach ($DonationplanResult as $value) {
                            $total = $total + $value['event_user_donation_amount'];
                        }
                        echo "Total Donation Amount : $" . $total;
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>