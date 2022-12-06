<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script>
    var order_id = '<?php echo $order["order_id"] ?>';
</script>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Order_Detail', $adminLangId); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <?php
                        $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
                        $li = $ul->appendElement("li", array('class' => 'droplink'));
                        $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                        $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                        $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                        $innerLi = $innerUl->appendElement('li');
                        $innerLi->appendElement('a', array('href' => FatUtility::generateUrl('PurchasedLessons'), 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_Back_To_Purchased_Lessons', $adminLangId)), Label::getLabel('LBL_Back_To_Purchased_Lessons', $adminLangId), true);
                        echo $ul->getHtml();
                        ?>
                        <h4><?php echo Label::getLabel('LBL_Customer_Order_Detail', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody">
                        <?php
                        $pendingAmount = 0;
                        $totalPaidAmount = array_sum(array_column($orderPayments, 'opayment_amount'));
                        if ($totalPaidAmount < $order["order_net_amount"]) {
                            $pendingAmount = $order["order_net_amount"] - $totalPaidAmount;
                        }
                        ?>
                        <table class="table table--details">
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_Order/Invoice_ID', $adminLangId); ?>:</strong> <?php echo $order["order_id"]; ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_Order_Date', $adminLangId); ?>: </strong> <?php echo MyDate::format($order['order_date_added'], true); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_Payment_Status', $adminLangId); ?>:</strong> <?php echo Order::getPaymentStatusArr($adminLangId)[$order['order_is_paid']] ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_Order_Amount', $adminLangId); ?>: </strong> <?php echo CommonHelper::displayMoneyFormat($order["order_net_amount"], true, true); ?> </td>
                                <td><strong><?php echo Label::getLabel('LBL_Order_Amount_Paid', $adminLangId); ?>: </strong><?php echo CommonHelper::displayMoneyFormat($totalPaidAmount, true, true); ?></td>
                                <td><strong><?php echo Label::getLabel('LBL_Order_Amount_Pending', $adminLangId); ?>: </strong><?php echo CommonHelper::displayMoneyFormat($pendingAmount, true, true); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo Label::getLabel('LBL_Order_Discount', $adminLangId); ?>: </strong> <?php echo CommonHelper::displayMoneyFormat($order["order_discount_total"], true, true); ?> </td>
                                <td><strong><?php echo Label::getLabel('LBL_ORDER_DISCOUNT_COUPON', $adminLangId); ?>: </strong><?php echo (!empty($order['order_discount_coupon_code'])) ? $order['order_discount_coupon_code'] : Label::getLabel('LBL_N/A'); ?></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </section>
                <div class="row row--cols-group">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel('Lbl_Student_Details'); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group">
                                    <h5><?php echo Label::getLabel('Lbl_Student_Details'); ?></h5>
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_Name'); ?> : </strong><?php echo $order['userFullName']; ?><br>
                                        <strong><?php echo Label::getLabel('LBL_Email'); ?> : </strong><?php echo $order['userEmail']; ?><br> 
                                        <strong><?php echo Label::getLabel('LBL_Country'); ?> : </strong><?php echo $order['uCountryName']; ?><br>                                
                                        <strong><?php echo Label::getLabel('LBL_USER_ID'); ?> : </strong><?php echo $order['order_user_id']; ?><br>                               
                                        <strong><?php echo Label::getLabel('LBL_User_Timezone'); ?> : </strong><?php echo $order['userTimezone'] . " ( " . Label::getLabel('LBL_TIMEZONE_STRING') . " " . MyDate::getOffset($order['userTimezone']) . ")"; ?><br>                               
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel('Lbl_Teacher_Details'); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group">
                                    <h5><?php echo Label::getLabel('Lbl_Teacher_Details'); ?></h5>
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_Name'); ?> : </strong><?php echo $order['teacherFullName']; ?><br>
                                        <strong><?php echo Label::getLabel('LBL_Email'); ?> : </strong><?php echo $order['teacherEmail']; ?><br>                               
                                        <strong><?php echo Label::getLabel('LBL_Country'); ?> : </strong><?php echo $order['tCountryName']; ?><br>                               
                                        <strong><?php echo Label::getLabel('LBL_User_Id'); ?> : </strong><?php echo $order['op_teacher_id']; ?><br>   
                                        <strong><?php echo Label::getLabel('LBL_User_Timezone'); ?> : </strong><?php echo $order['teacherTimezone'] . " (" . Label::getLabel('LBL_TIMEZONE_STRING') . " " . MyDate::getOffset($order['teacherTimezone']) . ")"; ?><br>                              
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <section class="section">
                            <div class="sectionhead">
                                <h4><?php echo Label::getLabel('Lbl_Class_Details'); ?></h4>
                            </div>
                            <div class="row space">
                                <div class="addresas-group ">
                                    <h5><?php echo Label::getLabel('Lbl_Class_Details'); ?></h5>
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_Class_Type'); ?> : </strong><?php echo ($order['op_grpcls_id'] > 0) ? Label::getLabel('LBL_GROUP_CLASS') : Label::getLabel('LBL_ONE_TO_ONE'); ?><br>
                                        <?php if ($order['op_grpcls_id'] > 0) { ?>
                                            <strong><?php echo Label::getLabel('LBL_Name'); ?> : </strong><?php echo $order['grpcls_title']; ?><br>                               
                                            <strong><?php echo Label::getLabel('LBL_Start_Date_time'); ?> : </strong> <?php echo MyDate::format($order['slesson_date'] . ' ' . $order['slesson_start_time'], true); ?><br>                               
                                            <strong><?php echo Label::getLabel('LBL_End_Date_time'); ?> : </strong> <?php echo MyDate::format($order['slesson_end_date'] . ' ' . $order['slesson_end_time'], true); ?><br>                                                             
                                            <strong><?php echo Label::getLabel('LBL_Class_Status'); ?> : </strong> <?php echo TeacherGroupClasses::getStatusArr($adminLangId)[$order['grpcls_status']]; ?><br>                                                             
                                        <?php } else { ?>
                                            <strong><?php echo Label::getLabel('LBL_Free_Trial'); ?> : </strong><?php echo ($order['op_lpackage_is_free_trial'] == applicationConstants::YES) ? Label::getLabel('LBL_YES') : Label::getLabel('LBL_NO'); ?><br> 
                                            <strong><?php echo Label::getLabel('LBL_NO._OF_LESSONS'); ?> : </strong><?php echo $order['op_qty']; ?><br> 
                                            <strong><?php echo Label::getLabel('LBL_LESSON_DURATION'); ?> : </strong><?php echo $order['op_lesson_duration'] . ' ' . Label::getLabel('LBL_MINS') . '/' . Label::getLabel('LBL_PER_LESSON'); ?><br> 
                                            <strong><?php echo Label::getLabel('LBL_LESSON_PRICE'); ?> : </strong><?php echo $order['op_unit_price'] . '/' . Label::getLabel('LBL_PER_LESSON'); ?><br> 
                                            <strong><?php echo Label::getLabel('LBL_Admin_Commission_(%)'); ?> : </strong><?php echo $order['op_commission_percentage'] . '%'; ?><br> 
                                        <?php } ?>
                                        <strong><?php echo Label::getLabel('LBL_Teach_Language'); ?> : </strong><?php echo $order['teachLang']; ?><br> 
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Lesson_details', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody" id="lesson-deatils-js">
                    </div>
                </section>
                <?php if (!empty($order["comments"]) && count($order["comments"]) > 0) { ?>
                    <section class="section">
                        <div class="sectionhead">
                            <h4><?php echo Label::getLabel('LBL_Order_Status_History', $adminLangId); ?></h4>
                        </div>
                        <div class="sectionbody">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th width="10%"><?php echo Label::getLabel('LBL_Date_Added', $adminLangId); ?></th>
                                        <th width="15%"><?php echo Label::getLabel('LBL_Customer_Notified', $adminLangId); ?></th>
                                        <th width="15%"><?php echo Label::getLabel('LBL_Payment_Status', $adminLangId); ?></th>
                                        <th width="60%"><?php echo Label::getLabel('LBL_Comments', $adminLangId); ?></th>
                                    </tr>
                                    <?php foreach ($order["comments"] as $key => $row) { ?>
                                        <tr>
                                            <td><?php echo MyDate::format($row['oshistory_date_added']); ?></td>
                                            <td><?php echo $yesNoArr[$row['oshistory_customer_notified']]; ?></td>
                                            <td><?php echo ($row['oshistory_orderstatus_id'] > 0) ? $orderStatuses[$row['oshistory_orderstatus_id']] : CommonHelper::displayNotApplicable($adminLangId, ''); ?></td>
                                            <td><div class="break-me"><?php echo nl2br($row['oshistory_comments']); ?></div></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php } ?>
                <?php if (!empty($orderPayments)) { ?>
                    <section class="section">
                        <div class="sectionhead">
                            <h4><?php echo Label::getLabel('LBL_Order_Payment_History', $adminLangId); ?></h4>
                        </div>
                        <div class="sectionbody">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th width="10%"><?php echo Label::getLabel('LBL_Date_Added', $adminLangId); ?></th>
                                        <th width="10%"><?php echo Label::getLabel('LBL_Txn_ID', $adminLangId); ?></th>
                                        <th width="15%"><?php echo Label::getLabel('LBL_Payment_Method', $adminLangId); ?></th>
                                        <th width="10%"><?php echo Label::getLabel('LBL_Amount', $adminLangId); ?></th>
                                        <th width="15%"><?php echo Label::getLabel('LBL_Comments', $adminLangId); ?></th>
                                        <th width="40%"><?php echo Label::getLabel('LBL_Gateway_Response', $adminLangId); ?></th>
                                    </tr>
                                    <?php foreach ($orderPayments as $key => $row) { ?>
                                        <tr>
                                            <td><?php echo MyDate::format($row['opayment_date']); ?></td>
                                            <td><?php echo $row['opayment_gateway_txn_id']; ?></td>
                                            <td><?php echo $row['opayment_method']; ?></td>
                                            <td><?php echo CommonHelper::displayMoneyFormat($row['opayment_amount'], true, true); ?></td>
                                            <td><div class="break-me"><?php echo nl2br($row['opayment_comments']); ?></div></td>
                                            <td><div class="break-me collapse-text"><?php echo nl2br($row['opayment_gateway_response']); ?>
                                                </div> <a class="collapse-btn" href="javascript:void(0)" >Show more</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php } ?>
                <?php if (!$order["order_is_paid"] && $canEdit) { ?>
                    <section class="section">
                        <div class="sectionhead">
                            <h4><?php echo Label::getLabel('LBL_Order_Payments', $adminLangId); ?></h4>
                        </div>
                        <div class="sectionbody space">
                            <?php
                            $form->setFormTagAttribute('onsubmit', 'updatePayment(this); return(false);');
                            $form->setFormTagAttribute('class', 'web_form');
                            $form->developerTags['colClassPrefix'] = 'col-md-';
                            $form->developerTags['fld_default_col'] = 12;
                            $paymentFld = $form->getField('opayment_method');
                            $paymentFld->developerTags['col'] = 4;
                            $gatewayFld = $form->getField('opayment_gateway_txn_id');
                            $gatewayFld->developerTags['col'] = 4;
                            $amountFld = $form->getField('opayment_amount');
                            $amountFld->developerTags['col'] = 4;
                            $submitFld = $form->getField('btn_submit');
                            $submitFld->developerTags['col'] = 4;
                            echo $form->getFormHtml();
                            ?>
                        </div>
                    </section>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
