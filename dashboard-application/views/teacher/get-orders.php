<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$user_timezone = MyDate::getUserTimeZone();
?>
<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <th><?php echo $orderIdLabel = Label::getLabel('LBL_Order_Id'); ?></th>
            <th><?php echo $freeTrialLabel = Label::getLabel('LBL_Free_trial'); ?></th>
            <th><?php echo $learnerLabel = Label::getLabel('LBL_Learner'); ?></th>
            <th><?php echo $orderAmountLabel = Label::getLabel('LBL_Order_Amount'); ?></th>			
            <th><?php echo $orderStatusLabel = Label::getLabel('LBL_Order_Status'); ?></th>
            <th><?php echo $orderDateLabel = Label::getLabel('LBL_Order_Date'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_Actions'); ?></th>
        </tr>
        <?php foreach ($ordersData['Orders'] as $order) { ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $orderIdLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $order['order_id']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $freeTrialLabel; ?></div>
                        <div class="flex-cell__content"><?php echo ($order['op_lpackage_is_free_trial'] == applicationConstants::YES ) ? 'Yes' : 'No'; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $learnerLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $order['learner_name']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $orderAmountLabel; ?></div>
                        <div class="flex-cell__content"><?php echo CommonHelper::displayMoneyFormat($order['order_net_amount']); ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $orderStatusLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php $spnCls = ($order['order_is_paid'] == Order::ORDER_IS_PAID) ? 'green' : (($order['order_is_paid'] == Order::ORDER_IS_CANCELLED) ? 'red' : 'yellow'); ?>
                            <span class="badge color-<?php echo $spnCls; ?> badge--curve"><?php echo $statusArr[$order['order_is_paid']]; ?></span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $orderDateLabel; ?></div>
                        <div class="flex-cell__content"><?php echo MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $order['order_date_added'], true, $user_timezone); ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $actionLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="actions-group">
                                <a href="javascript:void(0);" onClick="generateThread(<?php echo $order['order_user_id']; ?>);" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--messaging">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#message'; ?>"></use>
                                    </svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Message'); ?></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmOrderSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $ordersData['pagingArr'], false);
if (empty($ordersData['Orders'])) {
    $this->includeTemplate('_partial/no-record-found.php');
} 