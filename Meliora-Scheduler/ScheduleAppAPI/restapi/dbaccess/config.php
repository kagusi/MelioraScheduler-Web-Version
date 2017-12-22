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
define( 'API_ACCESS_KEY', 'AAAAdpRtDpM:APA91bHKqrRFB6tuN0htwCEio_-m0e-BiTBmEFUy_Wh0EyQAcX3L1JxfFvxwJ7lUGoOzt-3dV_Csey-4rI4OABgwZFxK7XrGH4y4fL33US1Vb5xUiYfRyws-uet_jMbm0snTpSwqCKCM');


$config = [
  'db' => [
     'servername' =>'localhost',
     'username' => 'root',
     'password' => '',
     'dbname' => 'uofr',
  ]
];


?>