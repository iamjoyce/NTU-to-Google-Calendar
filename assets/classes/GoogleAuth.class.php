<?php

define('AUTHCONFIGFILE', '');
define('REDIRECTURI', '');

class GoogleAuth {
    
    protected static $client;
    protected static $service;
    protected static $batch;
    
    function __construct($code = null, $enable_batch = true) {
        
        $client = new Google_Client();
        $client->setAuthConfigFile(AUTHCONFIGFILE);
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setRedirectUri(REDIRECTURI);

        if (! $code && ! (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {

            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));

        } else {

            if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

                $client->setAccessToken($_SESSION['access_token']);

            } else if ($code) {

                $client->authenticate($code);
                $access_token = $client->getAccessToken();
                $_SESSION['access_token'] = $access_token;
                
            } else {
                
                return;
            }
            
            if ($enable_batch) {
                $client->setUseBatch(true);
            }
            
            self::$client = $client;
            self::$service = new Google_Service_Calendar(self::$client);
        }
    }
}

?>