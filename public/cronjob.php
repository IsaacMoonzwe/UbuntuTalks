<?php 

    // Event API from Eventbrite.com
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.eventbriteapi.com/v3/organizations/1069163820893/events/?token=WP4DRCXH2Q3FDHJ2TZDH',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: G=v%3D2%26i%3D32199bf8-49a7-4312-b610-f5e4d9ea40d6%26a%3D100a%26s%3D7f70548dd0f18624d12c217451805d7d5616d29c; eblang=lo%3Den_US%26la%3Den-us; mgref=typeins; mgrefby='
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $event_data = json_decode($response);
    foreach($event_data as $value){
        foreach ($value as  $data) {

            $event_id = $data->id;
            $event_name ="'".$data->name->text."'";
            $event_description = "'".$data->description->text."'";
            $event_ticket_url = "'".$data->url."'";
            $event_start_time = "'".$data->start->utc."'";
            $event_end_time = "'".$data->end->utc."'";
            $total_capacity = $data->capacity;
            $event_poster = "'".$data->logo->url."'";
           
            //Database Connection
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

            //Fetch Data and Match EventId.
            $sqls = "SELECT * FROM tbl_event_and_agenda WHERE event_id=".$event_id; // Fetch data from the table customers using id
            $result=mysqli_query($conn,$sqls);
            $singleRow = mysqli_fetch_assoc($result);
            // echo "<pre>";
            // print_r($singleRow);
            if($singleRow){
                if($singleRow['event_name']!=$event_name || $singleRow['event_description']!=$event_description || $singleRow['event_ticket_url']!=$event_ticket_url || $singleRow['event_start_time']!=$event_start_time || $singleRow['event_end_time']!=$event_end_time || $singleRow['total_capacity']!=$total_capacity ||$singleRow['event_poster']!=$event_poster ){
                $update_sql = "UPDATE `tbl_event_and_agenda` SET `event_name`=$event_name,`event_description`=$event_description,`event_ticket_url`=$event_ticket_url,`event_start_time`=$event_start_time,`event_end_time`=$event_end_time,`total_capacity`=$total_capacity,`event_poster`=$event_poster WHERE event_id=".$event_id; // Fetch data from the table 
                if ($conn->query($update_sql) === TRUE) {
                    echo "New record created successfully";
                } 
                else {
                    echo "Error: " . $update_sql . "<br>" . $conn->error;
                }
                }
                else{
                    echo "No changes Found".$event_id." ";
                }
                
            }
            if(empty($singleRow)){

                // Insert Query
                $sql = "INSERT INTO  tbl_event_and_agenda (event_id, event_name, event_description, event_ticket_url, event_start_time, event_end_time, total_capacity, event_poster) VALUES ($event_id, $event_name, $event_description, $event_ticket_url, $event_start_time, $event_end_time, $total_capacity, $event_poster)";
                if ($conn->query($sql) === TRUE) {
                    echo "New record created successfully";
                } 
                else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
           }
        }
    }

?>