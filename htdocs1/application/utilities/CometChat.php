<?php

class CometChat
{

    private $apiKey;

    public const BASE_URL = 'https://api.cometondemand.net/api/v2/';
    public const GROUP_TYPE_PRIVATE = 4;

    public function __construct()
    {
        $this->apiKey = FatApp::getConfig('CONF_COMET_CHAT_API_KEY', FatUtility::VAR_STRING, '');
        if (empty($this->apiKey)) {
            throw new Exception(Label::getLabel('LBL_COMETCHAT_API_KEY_NOT_DEFINED'));
        }
    }

    public function createGroup($params)
    {
        $url = self::BASE_URL . 'createGroup';
        $curl_method = 'POST';
        $curl = new Curl();
        $curl->http_header('api-key', $this->apiKey);
        return $curl->request($curl_method, $url, $params);
    }

}
