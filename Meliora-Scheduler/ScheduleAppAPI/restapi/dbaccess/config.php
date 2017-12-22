<?php
/**
 * Database configuration
 */
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'uofr');

//App constants
define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXIST', 2);
define('INVALID_USER_TYPE', 3);
define('FAILED_TO_ENCRYPT', 4);
define('FAILED_TO_DECRYPT', 5);
define('APPOINTMENT_NOT_AVAILABLE', 6);
define('APPOINTMENT_SCHEDULED_SUCCESSFULLY', 7);
define('APPOINTMENT_SCHEDULED_FAILED', 8);
define('INVALID_ACCESS_C0DE', 9);
define( 'API_ACCESS_KEY', '*******');


$config = [
  'db' => [
     'servername' =>'localhost',
     'username' => 'root',
     'password' => '',
     'dbname' => 'uofr',
  ]
];


?>
