<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
//print_r($_FILES);

/**
 * Response in case of success.
 */

//$response = array(
//    'success' => 1,
//    'message' => 'Your Data is captured',
//);

/**
 * Response in case of failure
 */
//$response = array(
//    'success' => 0,
//    'message' => 'Unable to connect with server',
//);

/**
 * Response in case of server side validation failure
 */
$response = array(
    'success' => 0,
    'message' => 'Your Data is not captured',
    'validation_errors' => array(
        'email' => 'This email already taken by some one else',
        'password' => 'please use special chars',
    ),
);

echo json_encode($response);

exit();
