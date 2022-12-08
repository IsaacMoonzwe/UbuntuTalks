<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Benefit_Concert_information_' . date('Y-m-d') . '.csv"');
echo 'CONCERT ID,VISITOR NAME,VISITOR EMAIL,VISITOR PHONE CODE,VISITOR PHONE,TICKET CATEGORY,STARTING DATE,ENDING DATE,	TOTAL TICKET' . "\r\n";
foreach ($BenefitConcertCategoriesList as $value) {
  $concert_id = $value['event_user_concert_id'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $email = $value['user_email'];
  $phone_code = $value['user_phone_code'];
  $phone = $value['user_phone'];
  $ticket_category = $value['registration_plan_title'];
  $starting_date = $value['registration_starting_date'];
  $ending_date = $value['registration_ending_date'];
  $total_ticket = $value['event_user_ticket_count'];
  
  echo '"' . $concert_id . '","' . $name . '","' . $email . '","' . $phone_code . '","' . $phone . '","' . $ticket_category . '","' . $starting_date . '","' . $ending_date . '","' . $total_ticket . '"' . "\r\n";
}
exit();
