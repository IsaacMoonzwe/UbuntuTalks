<?php

class Lessonspace
{

    const LESSON_ID_PREFIX = "LESSON_";
    const API_BASE_URL = "https://api.thelessonspace.com/v2/";
    const LAUNCH_API_URL = "spaces/launch/";

    private $apikey;

    public function __construct()
    {
        $this->apikey = FatApp::getConfig('CONF_LESSONSPACE_API_KEY', FatUtility::VAR_STRING, '');
        if (empty($this->apikey)) {
            throw new Exception(Label::getLabel('LBL_LESSONSPACE_API_KEY_NOT_DEFINED'));
        }
    }

    public function launch(array $params): array
    {
        $url = self::API_BASE_URL . self::LAUNCH_API_URL;
        $curl = new Curl();
        $curl_method = 'POST';
        $curl->http_header('accept', 'application/json');
        $curl->http_header('Content-Type', 'application/json');
        $curl->http_header('Authorization', 'Organisation ' . $this->apikey);
        $response = $curl->request($curl_method, $url, json_encode($params));
        if (!$response) {
            throw new Exception($curl->getError());
        }
        return json_decode($response, true);
    }

}
