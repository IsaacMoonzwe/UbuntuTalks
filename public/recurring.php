<?php
// Recurring file path
//include(__DIR__ . '/../application/views/group-classes/search.php');

$servername = "localhost";
$username = "ubuntu_talk";
$password = "ubuntu!@#123";
$dbname = "db_ubuntu_talk";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mon = new DateTime();
$mon->modify('next monday');
$monday = $mon->format('Y-m-d');

$tue = new DateTime();
$tue->modify('next tuesday');
$tuesday = $tue->format('Y-m-d');

$wed = new DateTime();
$wed->modify('next wednesday');
$wednesday = $wed->format('Y-m-d');

$thu = new DateTime();
$thu->modify('next thursday');
$thursday = $thu->format('Y-m-d');

$fri = new DateTime();
$fri->modify('next friday');
$friday = $fri->format('Y-m-d');

/* Start - Moday */
$sql = "INSERT INTO `tbl_group_classes`(
    `grpcls_id`,
    `grpcls_tlanguage_id`,
    `grpcls_title`,
    `grpcls_weeks`,
    `grpcls_slug`,
    `grpcls_description`,
    `grpcls_classes_type`,
    `grpcls_teacher_id`,
    `grpcls_max_learner`,
    `grpcls_entry_fee`,
    `grpcls_start_datetime`,
    `grpcls_end_datetime`,
    `grpcls_added_on`,
    `grpcls_status`,
    `grpcls_deleted`
  )
  VALUES
    (
      '',
      '23',
      'Free 30-min Chichewa Trial',
      'Monday',
      'Free-30-min-Chichewa-Trial',
      'Welcome to Ubuntu Talks 30-minute free trial session in Chichewa! Chichewa is a Bantu language spoken in southern, southeast, and east Africa. Due to a presidential decree in 1968, Malawians refer to the language as Chichewa but Zambians call the language Chinyanja. Over 12 million people speak Chichewa.',
      'Group',
      '22',
      '10',
      '0.00',
      '$monday 20:00:00',
      '$monday 20:30:00',
      '2022-06-15 14:32:40',
      '1',
      '0'
    )
  ";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

/* Update the Slug Id */
$sqls = "SELECT grpcls_id FROM `tbl_group_classes` ORDER BY grpcls_id DESC LIMIT 1";
$result = mysqli_query($conn, $sqls);
$row = mysqli_fetch_array($result, MYSQLI_NUM);

/* ALTER Table Query */
$update_Query = "UPDATE `tbl_group_classes` SET grpcls_slug='Free-30-min-Chichewa-Trial-$row[0]' WHERE grpcls_id=$row[0]";


if ($conn->query($update_Query) === TRUE) {
    echo "Record updated"."<br>";
} else {
    echo "Error: " . $update_Query . "<br>" . $conn->error;
}

/* End - Monday */



/* Start - Tuesday */
$sql = "INSERT INTO `tbl_group_classes`(
    `grpcls_id`,
    `grpcls_tlanguage_id`,
    `grpcls_title`,
    `grpcls_weeks`,
    `grpcls_slug`,
    `grpcls_description`,
    `grpcls_classes_type`,
    `grpcls_teacher_id`,
    `grpcls_max_learner`,
    `grpcls_entry_fee`,
    `grpcls_start_datetime`,
    `grpcls_end_datetime`,
    `grpcls_added_on`,
    `grpcls_status`,
    `grpcls_deleted`
  )
  VALUES
    (
      '',
      '20',
      'Free 30-min Tonga Trial',
      'Tuesday',
      'Free-30-min-Tonga-Trial',
      'Welcome to Ubuntu Talks 30-minute free trial session in Tonga! Also known as Chitonga or Zambezi, Tonga is a Bantu language primarily spoken by the Tonga people. The Tonga people mostly live in the Southern and Western provinces of Zambia, with a few in northern Zimbabwe and Mozambique.',
      'Group',
      '20',
      '10',
      '0.00',
      '$tuesday 20:00:00',
      '$tuesday 20:30:00',
      '2022-06-30 14:29:54',
      '3',
      '0'
    )
  ";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

/* Update the Slug Id */
$sqls = "SELECT grpcls_id FROM `tbl_group_classes` ORDER BY grpcls_id DESC LIMIT 1";
$result = mysqli_query($conn, $sqls);
$row = mysqli_fetch_array($result, MYSQLI_NUM);

/* ALTER Table Query */
$update_Query = "UPDATE `tbl_group_classes` SET grpcls_slug='Free-30-min-Chichewa-Trial-$row[0]' WHERE grpcls_id=$row[0]";


if ($conn->query($update_Query) === TRUE) {
    echo "Record updated"."<br>";
} else {
    echo "Error: " . $update_Query . "<br>" . $conn->error;
}

/* End - Monday */


/* Start - Wednesday */
$sql = "INSERT INTO `tbl_group_classes`(
    `grpcls_id`,
    `grpcls_tlanguage_id`,
    `grpcls_title`,
    `grpcls_weeks`,
    `grpcls_slug`,
    `grpcls_description`,
    `grpcls_classes_type`,
    `grpcls_teacher_id`,
    `grpcls_max_learner`,
    `grpcls_entry_fee`,
    `grpcls_start_datetime`,
    `grpcls_end_datetime`,
    `grpcls_added_on`,
    `grpcls_status`,
    `grpcls_deleted`
  )
  VALUES
    (
      '',
      '23',
      'Free 30-min Chichewa Trial',
      'Wednesday',
      'Free-30-min-Chichewa-Trial',
      'Welcome to Ubuntu Talks 30-minute free trial session in Chichewa! Chichewa is a Bantu language spoken in southern, southeast, and east Africa. Due to a presidential decree in 1968, Malawians refer to the language as Chichewa but Zambians call the language Chinyanja. Over 12 million people speak Chichewa.',
      'Group',
      '22',
      '10',
      '0.00',
      '$wednesday 20:00:00',
      '$wednesday 20:30:00',
      '2022-06-15 14:32:40',
      '1',
      '0'
    )
  ";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

/* Update the Slug Id */
$sqls = "SELECT grpcls_id FROM `tbl_group_classes` ORDER BY grpcls_id DESC LIMIT 1";
$result = mysqli_query($conn, $sqls);
$row = mysqli_fetch_array($result, MYSQLI_NUM);

/* ALTER Table Query */
$update_Query = "UPDATE `tbl_group_classes` SET grpcls_slug='Free-30-min-Chichewa-Trial-$row[0]' WHERE grpcls_id=$row[0]";


if ($conn->query($update_Query) === TRUE) {
    echo "Record updated"."<br>";
} else {
    echo "Error: " . $update_Query . "<br>" . $conn->error;
}

/* End - Wednesday */


/* Start - Thursday */
$sql = "INSERT INTO `tbl_group_classes`(
    `grpcls_id`,
    `grpcls_tlanguage_id`,
    `grpcls_title`,
    `grpcls_weeks`,
    `grpcls_slug`,
    `grpcls_description`,
    `grpcls_classes_type`,
    `grpcls_teacher_id`,
    `grpcls_max_learner`,
    `grpcls_entry_fee`,
    `grpcls_start_datetime`,
    `grpcls_end_datetime`,
    `grpcls_added_on`,
    `grpcls_status`,
    `grpcls_deleted`
  )
  VALUES
    (
      '',
      '20',
      'Free 30-min Tonga Trial',
      'Thursday',
      'Free-30-min-Tonga-Trial',
      'Welcome to Ubuntu Talks 30-minute free trial session in Tonga! Also known as Chitonga or Zambezi, Tonga is a Bantu language primarily spoken by the Tonga people. The Tonga people mostly live in the Southern and Western provinces of Zambia, with a few in northern Zimbabwe and Mozambique.',
      'Group',
      '20',
      '10',
      '0.00',
      '$thursday 20:00:00',
      '$thursday 20:30:00',
      '2022-06-15 14:32:40',
      '1',
      '0'
    )
  ";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

/* Update the Slug Id */
$sqls = "SELECT grpcls_id FROM `tbl_group_classes` ORDER BY grpcls_id DESC LIMIT 1";
$result = mysqli_query($conn, $sqls);
$row = mysqli_fetch_array($result, MYSQLI_NUM);

/* ALTER Table Query */
$update_Query = "UPDATE `tbl_group_classes` SET grpcls_slug='Free-30-min-Chichewa-Trial-$row[0]' WHERE grpcls_id=$row[0]";


if ($conn->query($update_Query) === TRUE) {
    echo "Record updated"."<br>";
} else {
    echo "Error: " . $update_Query . "<br>" . $conn->error;
}

/* End - Thursday */

/* Start - Friday */
$sql = "INSERT INTO `tbl_group_classes`(
    `grpcls_id`,
    `grpcls_tlanguage_id`,
    `grpcls_title`,
    `grpcls_weeks`,
    `grpcls_slug`,
    `grpcls_description`,
    `grpcls_classes_type`,
    `grpcls_teacher_id`,
    `grpcls_max_learner`,
    `grpcls_entry_fee`,
    `grpcls_start_datetime`,
    `grpcls_end_datetime`,
    `grpcls_added_on`,
    `grpcls_status`,
    `grpcls_deleted`
  )
  VALUES
    (
      '',
      '23',
      'Free 30-min Chichewa Trial',
      'Friday',
      'Free-30-min-Chichewa-Trial',
      'Welcome to Ubuntu Talks 30-minute free trial session in Chichewa! Chichewa is a Bantu language spoken in southern, southeast, and east Africa. Due to a presidential decree in 1968, Malawians refer to the language as Chichewa but Zambians call the language Chinyanja. Over 12 million people speak Chichewa.',
      'Group',
      '27',
      '10',
      '0.00',
      '$friday 20:00:00',
      '$friday 20:30:00',
      '2022-06-15 14:32:40',
      '1',
      '0'
    )
  ";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully"."<br>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

/* Update the Slug Id */
$sqls = "SELECT grpcls_id FROM `tbl_group_classes` ORDER BY grpcls_id DESC LIMIT 1";
$result = mysqli_query($conn, $sqls);
$row = mysqli_fetch_array($result, MYSQLI_NUM);

/* ALTER Table Query */
$update_Query = "UPDATE `tbl_group_classes` SET grpcls_slug='Free-30-min-Chichewa-Trial-$row[0]' WHERE grpcls_id=$row[0]";


if ($conn->query($update_Query) === TRUE) {
    echo "Record updated"."<br>";
} else {
    echo "Error: " . $update_Query . "<br>" . $conn->error;
}
$conn->close();
/* End - Friday */