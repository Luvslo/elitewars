<?php

/**
* Game configuration file.
*/ 

error_reporting(E_ALL);
ini_set("display_errors", 1);

//sets class path for auto loading class.
define('CLASS_PATH', 'classes/');

define('COOKIE_RUNTIME', 1209600); 
define('COOKIE_DOMAIN', '.localhost');

define('DB_TYPE', 'mysql');
define('DB_HOST', 'host');
define('DB_NAME', 'database');
define('DB_USER', 'username');
define('DB_PASS', 'password');

//define all errors
define("ERROR_UNKNOWN_ERROR", "An unknown error occurred.");
define("ERROR_3_FAILED_LOGINS", "You have failed to login 3 times, please try again in 10 minutes.");
define("ERROR_WRONG_PASSWORD", "Password was incorrect.");
define("ERROR_USER_DOES_NOT_EXIST", "That user does not exist.");
define("ERROR_USERNAME_BLANK", "You must enter a username");
define("ERROR_PASSWORD_BLANK", "You must enter a password");

?>
