<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="table-scroll">
    <table class="table table--styled table--responsive">
        <tr class="title-row">
            <th><?php echo $orderIdLabel = Label::getLabel('LBL_Order_id'); ?></th>
            <th><?php echo $giftCardLabel = Label::getLabel('LBL_Gift_Card_Code'); ?></th>
            <th><?php echo $amountLabel = Label::getLabel('LBL_Amount'); ?></th>
            <th><?php echo $recipientLabel = Label::getLabel('LBL_Recepient_Details'); ?></th>
            <th><?php echo $statusLabel = Label::getLabel('LBL_Status'); ?></th>
        </tr>
        <?php foreach ($giftcardList as $giftcard) { ?>
            <tr>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $orderIdLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $giftcard['order_id']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $giftCardLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $giftcard['giftcard_code']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $giftCardLabel; ?></div>
                        <div class="flex-cell__content"><?php echo CommonHelper::displayMoneyFormat($giftcard['giftcard_amount']); ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $recipientLabel; ?></div>
                        <div class="flex-cell__content">
                            <div class="data-group">
                                <span><?php echo Label::getLabel('lbl_Name') . ' - ' . $giftcard['recipient_name']; ?></span><br>
                                <span><?php echo Label::getLabel('lbl_Email') . ' - ' . $giftcard['recipient_email'] ?></span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $statusLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php $spnCls = ($giftcard['giftcard_status'] == Giftcard::GIFTCARD_USED_STATUS) ? 'secondary' : 'primary'; ?>
                            <span class="badge color-<?php echo $spnCls; ?> badge--curve"><?php echo $giftCardStatus[$giftcard['giftcard_status']]; ?></span>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmSearchPaging'
));
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
</div>
<?php
if (empty($giftcardList)) {
    $this->includeTemplate('_partial/no-record-found.php');
}
?>
