<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box">
    <div class="box__head">
        <h6><?php echo Label::getLabel('LBL_Issue_Detail'); ?></h6>
    </div>
    <div class="box__body">
        <div class="content-repeated-container">
            <table class="table table--details">
                <tbody>
                    <tr><td colspan="2"><h4><?php echo $issue['repiss_title']; ?></h4></td></tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Detail'); ?></strong></td>
                        <td style="max-width: 350px;"><?php echo nl2br($issue['repiss_comment']); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Reported_By'); ?></strong></td>
                        <td><?php echo $issue['reporter_username']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Reported_Time'); ?></strong></td>
                        <td><?php echo MyDate::format($issue['repiss_reported_on'], true, true, $userTimezone); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Current_Status'); ?></strong></td>
                        <td><?php echo ReportedIssue::getStatusArr($issue['repiss_status']); ?></td>
                    </tr>
                    <?php if ($canEsclate || $issue['repiss_status'] == ReportedIssue::STATUS_ESCLATED) { ?>
                        <tr>
                            <td><strong><?php echo Label::getLabel('LBL_NOT_HAPPY_WITH_SOLUTION?'); ?></strong></td>
                            <td>
                                <?php if ($canEsclate) { ?>
                                    <button onclick="esclateForm(<?php echo $issue['repiss_id']; ?>)" class="btn btn-small btn--primary">
                                        <?php echo Label::getLabel('LBL_ESCLATE_TO_SUPPORT_TEAM'); ?>
                                    </button>
                                <?php } elseif ($issue['repiss_status'] == ReportedIssue::STATUS_ESCLATED) { ?>
                                    <?php echo Label::getLabel('LBL_ESCLATED_TO_SUPPORT_TEAM'); ?>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr/>
    <div class="box__head">
        <h4><?php echo Label::getLabel('LBL_ISSUE_LOGS'); ?></h4>
    </div>
    <div class="box__body">
        <div class="content-repeated-container">
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
    </div>
    <hr/>
    <div class="box__head">
        <h6><?php echo Label::getLabel('LBL_Lesson_Details'); ?></h6>
    </div>
    <div class="box__body">
        <div class="content-repeated-container">
            <table class="table table--details">
                <tbody>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Language'); ?>:</strong>  <?php echo $issue['tlanguage_name']; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Free_Trail'); ?>:</strong> <?php echo applicationConstants::getYesNoArr()[$issue['op_lpackage_is_free_trial']]; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Order_Id'); ?>:</strong> <?php echo $issue['sldetail_order_id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Learner_Lesson_Id'); ?>:</strong>  <?php echo $issue['repiss_sldetail_id']; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Teacher_Lesson_Id'); ?>:</strong>  <?php echo $issue['slesson_id']; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Total_Lesson'); ?>:</strong> <?php echo $issue['op_qty']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Lesson_Price'); ?>:</strong>   <?php echo CommonHelper::displayMoneyFormat($issue['op_unit_price'], true, true); ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Order_Net_Amount'); ?>:</strong>  <?php echo CommonHelper::displayMoneyFormat($issue['order_net_amount'], true, true); ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Order_Discount_Total'); ?>:</strong>   <?php echo CommonHelper::displayMoneyFormat($issue['order_discount_total'], true, true); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Teacher_Name'); ?>:</strong>  <?php echo $issue['teacher_username']; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Teacher_Join_Time'); ?>:</strong> <?php echo MyDate::format($issue['slesson_teacher_join_time'], true, true, $userTimezone); ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Teacher_End_Time'); ?>:</strong>  <?php echo MyDate::format($issue['slesson_teacher_end_time'], true, true, $userTimezone); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo Label::getLabel('LBL_Learner_Name'); ?>:</strong>  <?php echo $issue['learner_username']; ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Learner_Join_Time'); ?>:</strong>  <?php echo MyDate::format($issue['sldetail_learner_join_time'], true, true, $userTimezone); ?></td>
                        <td><strong><?php echo Label::getLabel('LBL_Learner_end_Time'); ?>:</strong> <?php echo MyDate::format($issue['sldetail_learner_end_time'], true, true, $userTimezone); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>