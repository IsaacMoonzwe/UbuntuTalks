<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_View_User_Detail', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td ><strong><?php echo Label::getLabel('LBL_Username', $adminLangId); ?>:</strong>  <?php echo $data['user_first_name'] . ' ' . $data['user_last_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_User_Phone', $adminLangId); ?>:</strong> <?php echo$data['user_phone_code'].' '.$data['user_phone']; ?></td>
                    <td ><strong><?php echo Label::getLabel('LBL_User_Date', $adminLangId); ?>:</strong>  <?php echo MyDate::format($data['user_added_on'], true, true, Admin::getAdminTimeZone()); ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Email', $adminLangId); ?>:</strong> <?php echo $data['credential_email']; ?></td>
                    <td ><strong><?php echo Label::getLabel('LBL_Country', $adminLangId); ?>:</strong>  <?php echo $data['country_name']; ?></td>              
                    <td ><strong><?php echo Label::getLabel('LBL_Profile_Info', $adminLangId); ?>:</strong>  <?php echo $data['user_profile_info']; ?></td>                        
                </tr>
            </tbody>
        </table>
    </div>
</section>