<?php

class Zoom
{

    const ROLE_HOST = 1;
    const ROLE_ATTENDEE = 0;
    const BASE_URL = 'https://api.zoom.us/v2';

    private $token, $apiKey, $apiSecret;

    public function __construct()
    {
        $this->token = FatApp::getConfig('CONF_ZOOM_JWT_TOKEN', FatUtility::VAR_STRING, '');
        $this->apiKey = FatApp::getConfig('CONF_ZOOM_API_KEY', FatUtility::VAR_STRING, '');
        $this->apiSecret = FatApp::getConfig('CONF_ZOOM_API_SECRET', FatUtility::VAR_STRING, '');
        if (empty($this->token)) {
            throw new Exception(Label::getLabel('LBL_ZOOM_API_TOKEN_NOT_DEFINED'));
        }
        if (empty($this->apiKey)) {
            throw new Exception(Label::getLabel('LBL_ZOOM_API_KEY_NOT_DEFINED'));
        }
        if (empty($this->apiSecret)) {
            throw new Exception(Label::getLabel('LBL_ZOOM_API_SECRET_NOT_DEFINED'));
        }
    }

    public function checkUserAndGetUserId($email)
    {
        $page_number = 1;
        do {
            $response = $this->getUsers($page_number);
            $emailIds = array_column($response['users'], 'email');
            $idx = array_search(strtolower($email), array_map('strtolower', $emailIds));
            if ($idx !== false) {
                return $response['users'][$idx]['id'];
            }
            $page_number++;
        } while ($page_number <= $response['page_count']);
        return false;
    }

    public function getUsers($page_number = 1)
    {
        $url = self::BASE_URL . "/users/";
        $params = ["page_number" => $page_number, "page_size" => 300];
        $curl = new Curl();
        $curl_method = 'GET';
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Bearer ' . $this->token);
        $response = $curl->request($curl_method, $url, $params);
        if (!$response) {
            throw new Exception($curl->getError());
        }
        return json_decode($response, true);
    }

    public function createUser($teacherData)
    {
        if ($id = $this->checkUserAndGetUserId($teacherData['email'])) {
            return $id;
        }
        $url = self::BASE_URL . "/users";
        $params = json_encode([
            "action" => "custCreate",
            "user_info" => [
                "first_name" => $teacherData['first_name'],
                "last_name" => $teacherData['last_name'],
                "email" => $teacherData['email'],
                "type" => 1, // const 
            ]
        ]);
        $curl = new Curl();
        $curl_method = 'POST';
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Bearer ' . $this->token);
        $response = $curl->request($curl_method, $url, $params);
        if (!$response) {
            throw new Exception($curl->getError());
        }
        $res = json_decode($response, true);
        if (empty($res['id'])) {
            throw new Exception($res['message']);
        }
        return $res['id'];
    }

    public function createMeeting($meeting_data)
    {
        // create User
        $url = self::BASE_URL . "/users/$meeting_data[zoomTeacherId]/meetings";
        $params = json_encode([
            "topic" => $meeting_data['title'],
            "type" => 2, //always a scheduled meeting for now
            "start_time" => date('c', strtotime($meeting_data['start_time'])),
            "duration" => $meeting_data['duration'],
            "timezone" => MyDate::getTimeZone(),
            "agenda" => $meeting_data['description']
        ]);
        $curl = new Curl();
        $curl_method = 'POST';
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Bearer ' . $this->token);
        $response = $curl->request($curl_method, $url, $params);
        if (!$response) {
            throw new Exception($curl->getError());
        }
        return json_decode($response, true);
    }

    public function generateSignature($meeting_number, $role)
    {
        $time = time() * 1000 - 30000; //time in milliseconds (or close enough)
        $data = base64_encode($this->apiKey . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $this->apiSecret, true);
        $_sig = $this->apiKey . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        //return signature, url safe base64 encoded
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    public function getMeetingDetails($meetingId)
    {
        $url = self::BASE_URL . "/meetings/" . $meetingId;
        $curl = new Curl();
        $curl_method = 'GET';
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Bearer ' . $this->token);
        $response = $curl->request($curl_method, $url);
        if (!$response) {
            throw new Exception($curl->getError());
        }
        return json_decode($response, true);
    }

    public function endMeeting($meetingId)
    {
        $meetingData = $this->getMeetingDetails($meetingId);
        if (empty($meetingData)) {
            throw new Exception(Label::getLabel('MSG_Meeting_does_not_exists'));
        }
        $url = self::BASE_URL . "/meetings/$meetingId/status";
        $params = json_encode(["action" => "end"]);
        $curl = new Curl();
        $curl_method = 'PUT';
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Bearer ' . $this->token);
        $response = $curl->request($curl_method, $url);
        if (!$response) {
            throw new Exception($curl->getError());
        }
        return json_decode($response, true);
    }

}
