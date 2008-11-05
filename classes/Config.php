<?php

class Config {
    const DB_SERVER   = 'localhost';
    const DB_NAME     = 'the_twitbot_database';
    const DB_USERNAME = 'the_twitbot_user';
    const DB_PASSWORD = 'the_twitbot_password';

    public function __construct() {
        ini_set('display_errors', false);
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/London');
    }
}

?>