<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Pre_Symposium_Dinner_information_' . date('Y-m-d') . '.csv"');
echo 'PRE SYMPOSIUM DINNER ID,VISITOR NAME,VISITOR EMAIL,VISITOR PHONE CODE,VISITOR PHONE,TICKET CATEGORY,STARTING DATE,ENDING DATE,	TOTAL TICKET' . "\r\n";
foreach ($PreSymposiumDinnerInformationCategoriesList as $value) {
  $pre_symposium_dinner_id = $value['pre_symposium_dinner_ticket_plan_id'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $email = $value['user_email'];
  $phone_code = $value['user_phone_code'];
  $phone = $value['user_phone'];
  $ticket_category = $value['registration_plan_title'];
  $starting_date = $value['registration_starting_date'];
  $ending_date = $value['registration_ending_date'];
  $total_ticket = $value['event_user_ticket_count'];
  
  echo '"' . $pre_symposium_dinner_id . '","' . $name . '","' . $email . '","' . $phone_code . '","' . $phone . '","' . $ticket_category . '","' . $starting_date . '","' . $ending_date . '","' . $total_ticket . '"' . "\r\n";
}
exit();
