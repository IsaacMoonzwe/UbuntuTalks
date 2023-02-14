<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Corporate_Concert_information_' . date('Y-m-d') . '.csv"');
echo 'CONCERT ID,VISITOR NAME,VISITOR EMAIL,VISITOR PHONE CODE,VISITOR PHONE,TICKET CATEGORY,DISCOUNT,	TOTAL TICKET' . "\r\n";
foreach ($CorporateCategoriesList as $value) {
  $concert_id = $value['event_user_corporate_id'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $email = $value['user_email'];
  $phone_code = $value['user_phone_code'];
  $phone = $value['user_phone'];
  $ticket_category = $value['corporate_ticket_title'];
  $discount = $value['corporate_discount'].'%';
  $total_ticket = $value['event_user_sponsership_qty'];
  
  echo '"' . $concert_id . '","' . $name . '","' . $email . '","' . $phone_code . '","' . $phone . '","' . $ticket_category . '","' . $discount . '","' . $total_ticket . '"' . "\r\n";
}
exit();
