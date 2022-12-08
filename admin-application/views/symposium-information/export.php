<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="UT_Symposium_information_' . date('Y-m-d') . '.csv"');
echo 'SYMPOSIUM ID,VISITOR NAME,VISITOR EMAIL,VISITOR PHONE CODE,VISITOR PHONE,SYMPOSIUM TITLE (EVENTS),STARTING DATE,ENDING DATE,	TOTAL TICKET' . "\r\n";
foreach ($SymposiumInformationCategoriesList as $value) {
  $symposium_id = $value['event_user_ticket_plan_id'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $email = $value['user_email'];
  $phone_code = $value['user_phone_code'];
  $phone = $value['user_phone'];
  $event_name = $value['registration_plan_title'];
  $starting_date = $value['registration_starting_date'];
  $ending_date = $value['registration_ending_date'];
  $total_ticket = $value['event_user_ticket_count'];
  
  echo '"' . $symposium_id . '","' . $name . '","' . $email . '","' . $phone_code . '","' . $phone . '","' . $event_name . '","' . $starting_date . '","' . $ending_date . '","' . $total_ticket . '"' . "\r\n";
}
exit();
