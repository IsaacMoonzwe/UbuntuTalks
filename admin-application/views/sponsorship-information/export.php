<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sponsorship_information_' . date('Y-m-d') . '.csv"');
echo 'USER ID,VISITOR NAME,VISITOR PHONE CODE,VISITOR PHONE,SPONSORSHIP EVENT NAME,SPONSORSHIP PLAN TYPE,SPONSORSHIP ENDING TIME' . "\r\n";
foreach ($sponserEventData as $value) {
  $user_id = $value['sponsorship'][0]['event_user_id'];
  $name = $value['sponsorship'][0]['user_first_name'] . " " . $value['sponsorship'][0]['user_last_name'];;
  $phone_code = $value['sponsorship'][0]['user_phone_code'];
  $phone = $value['sponsorship'][0]['user_phone'];
  $event_name = $value['event_name'];
  $plan_type = $value['plan'];
  $ending_date = $value['event_ending_time'];


  echo '"' . $user_id . '","' . $name . '","' . $phone_code . '","' . $phone . '","' . $event_name . '","' . $plan_type . '","' . $ending_date . '"' . "\r\n";
}
exit();
