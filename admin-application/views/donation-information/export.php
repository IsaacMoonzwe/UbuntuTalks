<?php
$filename = "Booking_Data_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="donation_information_' . date('Y-m-d') . '.csv"');
echo 'Donation Id,User Id,Email,Name,Phone Code,Phone,Amount' . "\r\n";
foreach ($DonationInformationCategoriesList as $value) {
  $donation_id = $value['event_user_donation_id'];
  $user_id = $value['event_user_user_id'];
  $email = $value['user_email'];
  $name = $value['user_first_name'] . " " . $value['user_last_name'];
  $phone_code = $value['user_phone_code'];
  $phone = $value['user_phone'];
  $amount = "$" . $value['event_user_donation_amount'];
  echo '"' . $donation_id . '","' . $user_id . '","' . $email . '","' . $name . '","' . $phone_code . '","' . $phone . '","' . $amount . '"' . "\r\n";
}
exit();
