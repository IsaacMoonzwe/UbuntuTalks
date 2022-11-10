<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_ISSUE_LOGS', $adminLangId); ?></h4>
        <div>
            <h4>
                <?php echo Label::getLabel('LBL_ISSUE_STATUS'); ?>:</strong> 
                <?php echo ReportedIssue::getStatusArr($issue['repiss_status']); ?>
            </h4>  
        </div>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <thead>
                <tr>
                    <th><?php echo Label::getLabel('LBL_ACTION_BY'); ?></th>
                    <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
                    <th><?php echo Label::getLabel('LBL_COMMENT'); ?></th>
                    <th><?php echo Label::getLabel('LBL_ACTION_ON'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php echo $issue['reporter_username']; ?>
                        <?php echo '(' . ReportedIssue::getUserTypeArr(ReportedIssue::USER_TYPE_LEARNER) . ')'; ?>
                    </td>
                    <td><?php echo $issue['repiss_title']; ?></td>
                    <td><?php echo nl2br($issue['repiss_comment']); ?></td>
                    <td><?php echo $issue['repiss_reported_on']; ?></td>
                </tr>
                <?php foreach ($logs as $log) { ?>
                    <tr>
                        <td>
                            <?php echo $log['user_fullname']; ?>
                            <?php echo '(' . ReportedIssue::getUserTypeArr($log['reislo_added_by_type']) . ')'; ?>
                        </td>
                        <td><?php echo $actionArr[$log['reislo_action']]; ?></td>
                        <td><?php echo nl2br($log['reislo_comment']); ?></td>
                        <td><?php echo $log['reislo_added_on']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Lesson_Details', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Language', $adminLangId); ?>:</strong>  <?php echo $issue['tlanguage_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Free_Trail', $adminLangId); ?>:</strong> <?php echo applicationConstants::getYesNoArr()[$issue['op_lpackage_is_free_trial']]; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Order_Id', $adminLangId); ?>:</strong> <?php echo $issue['sldetail_order_id']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_Lesson_Id', $adminLangId); ?>:</strong>  <?php echo $issue['repiss_sldetail_id']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_Lesson_Id', $adminLangId); ?>:</strong>  <?php echo $issue['slesson_id']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Total_Lesson', $adminLangId); ?>:</strong> <?php echo $issue['op_qty']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Lesson_Price', $adminLangId); ?>:</strong>   <?php echo CommonHelper::displayMoneyFormat($issue['op_unit_price'], true, true); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Order_Net_Amount', $adminLangId); ?>:</strong>  <?php echo CommonHelper::displayMoneyFormat($issue['order_net_amount'], true, true); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Order_Discount_Total', $adminLangId); ?>:</strong>   <?php echo CommonHelper::displayMoneyFormat($issue['order_discount_total'], true, true); ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_Name', $adminLangId); ?>:</strong>  <?php echo $issue['teacher_username']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_Join_Time', $adminLangId); ?>:</strong> <?php echo MyDate::format($issue['slesson_teacher_join_time'], true, true, Admin::getAdminTimeZone()); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Teacher_End_Time', $adminLangId); ?>:</strong>  <?php echo MyDate::format($issue['slesson_teacher_end_time'], true, true, Admin::getAdminTimeZone()); ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_Name', $adminLangId); ?>:</strong>  <?php echo $issue['learner_username']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_Join_Time', $adminLangId); ?>:</strong>  <?php echo MyDate::format($issue['sldetail_learner_join_time'], true, true, Admin::getAdminTimeZone()); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Learner_end_Time', $adminLangId); ?>:</strong> <?php echo MyDate::format($issue['sldetail_learner_end_time'], true, true, Admin::getAdminTimeZone()); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
