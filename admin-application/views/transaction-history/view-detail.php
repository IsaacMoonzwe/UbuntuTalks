<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$adminTimezone = Admin::getAdminTimeZone();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_View_Lesson_Detail', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="details-title">
                    <?php echo Label::getLabel('LBL_Order_Details'); ?>
                </div>
                <div class="tabs_panel">
                    <?php //if ($lessonRow['grpcls_title']): 
                    ?>
                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Order_Id', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['opayment_order_id']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_First_Name', $adminLangId); ?>
                                    </label>
                                    :<?php
                                        echo $TransactionHistoryInformationCategoriesList[0]['user_first_name'];
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Last_Name', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['user_last_name']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Email', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['user_email']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Phone_Code', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['user_phone_code']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Phone_Number', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['user_phone']; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="details-title">
                        <?php echo Label::getLabel('LBL_Payment_Details'); ?>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Payment_Methood', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['opayment_method']; ?>
                                </div>
                            </div>
                        </div>
                        <?php //endif; 
                        ?>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Total_Amount', $adminLangId); ?>
                                    </label>
                                    : <?php
                                        $netAmount = $TransactionHistoryInformationCategoriesList[0]['opayment_amount'];
                                        $DiscountAmt = $TransactionHistoryInformationCategoriesList[0]['order_discount_total'];
                                        $total = $netAmount + $DiscountAmt;
                                        $nombre_format_francais = number_format($total, 2, '.', '');
                                        echo $nombre_format_francais;
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Order_Discount_Total', $adminLangId); ?>
                                    </label> :
                                    <?php echo $TransactionHistoryInformationCategoriesList[0]['order_discount_total']; ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Paid_Amount', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['opayment_amount']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Payment_Date', $adminLangId); ?>
                                    </label>
                                    : <?php echo $TransactionHistoryInformationCategoriesList[0]['opayment_date']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Wallet_Amount_Charge', $adminLangId); ?>
                                    </label> :

                                    <?php echo $TransactionHistoryInformationCategoriesList[0]['order_wallet_amount_charge']; ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_Discount_Coupon_Code', $adminLangId); ?>
                                    </label>
                                    :
                                    <?php echo $TransactionHistoryInformationCategoriesList[0]['order_discount_coupon_code']; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>