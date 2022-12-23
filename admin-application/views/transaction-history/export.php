<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Transaction_History_Information_' . date('Y-m-d') . '.csv"');
echo 'ORDER ID, VISITOR NAME, VISITOR EMAIL, VISITOR PHONE CODE, VISITOR PHONE NUMBER, PAYMENT TYPE, TOTAL AMOUNT, ORDER DISCOUNT TOTAL, PAID AMOUNT,PAYMENT DATE, WALLET AMOUNT CHARGE, DISCOUNT COUPON CODE' . "\r\n";
foreach ($TransactionHistoryInformationCategoriesList as $value) {

  $netAmount = $value['opayment_amount'];
  $DiscountAmt = $value['order_discount_total'];
  $total = $netAmount + $DiscountAmt;
  $nombre_format_francais = number_format($total, 2, '.', '');
  
  $order_id = $value['opayment_order_id'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $email = $value['user_email'];
  $code = $value['user_phone_code'];
  $phoneno = $value['user_phone'];
  $paymentType = $value['opayment_method'];
  $totalAmount = $nombre_format_francais;
  $orderDiscountTotal = $value['order_discount_total'];
  $paidAmount = $value['order_net_amount'];
  $paymentDate = $value['opayment_date'];
  $walletAmountCharge = $value['order_wallet_amount_charge'];
  $discountCouponCode = $value['order_discount_coupon_code'];

  echo '"' . $order_id . '","' . $name . '","' . $email . '","' . $code . '","' . $phoneno . '","' . $paymentType . '","' . $totalAmount . '","' . $orderDiscountTotal . '","' . $paidAmount . '","' . $paymentDate . '","' . $walletAmountCharge . '","' . $discountCouponCode . '"' . "\r\n";
}
exit();
