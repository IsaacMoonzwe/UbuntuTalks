<?php

class SocialMedia
{

    public static function addEventOnGoogleCalendar($token, $data)
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/GoogleAPI/vendor/autoload.php'; // include the required calss files for google login
        $client = new Google_Client();
        $client->setClientId(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_ID")); // paste the client id which you get from google API Console
        $client->setClientSecret(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_SECRET")); // set the client secret
        $client->refreshToken($token);
        $event_data = [
            'title' => $data['title'],
            'summary' => $data['summary'],
            'description' => $data['description'],
            'start' => ['dateTime' => $data['start_time'], 'timeZone' => $data['timezone']],
            'end' => ['dateTime' => $data['end_time'], 'timeZone' => $data['timezone']],
            'sendUpdates' => 'all',
            'reminders' => [
                'useDefault' => false,
                'overrides' => [['method' => 'email', 'minutes' => 10]],
            ],
            'source' => ['title' => $data['title'], 'url' => $data['url']],
        ];
        try {
            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event($event_data);
            $calendarId = 'primary';
            $event = $service->events->insert($calendarId, $event);
        } catch (exception $e) {
            return false;
        }
        return $event->id;
    }

    public static function isGoogleAccessTokenExpired($token)
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/GoogleAPI/vendor/autoload.php'; // include the required calss files for google login
        $client = new Google_Client();
        $client->setScopes(['https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.events']); // set scope during user login
        $client->setClientId(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_ID")); // paste the client id which you get from google API Console
        $client->setClientSecret(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_SECRET")); // set the client secret
        $currentPageUri = CommonHelper::generateFullUrl('Account', 'GoogleCalendarAuthorize', [], '', false);
        $client->setRedirectUri($currentPageUri);
        $client->refreshToken($token);
        return $client->isAccessTokenExpired();
    }

    public static function deleteEventOnGoogleCalendar($token, $eventId)
    {
        require_once CONF_INSTALLATION_PATH . 'library/third-party/GoogleAPI/vendor/autoload.php'; // include the required calss files for google login
        $client = new Google_Client();
        $client->setClientId(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_ID")); // paste the client id which you get from google API Console
        $client->setClientSecret(FatApp::getConfig("CONF_GOOGLEPLUS_CLIENT_SECRET")); // set the client secret
        $client->refreshToken($token);
        try {
            $service = new Google_Service_Calendar($client);
            $calendarId = 'primary';
            $service->events->delete($calendarId, $eventId);
        } catch (exception $e) {
            return false;
        }
        return true;
    }

}
