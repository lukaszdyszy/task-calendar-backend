<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once('../../config/db.php');
require_once('../../config/secret.php');
require_once('../../models/user.php');

$data = json_decode(file_get_contents("php://input"));

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){
    $user = new User($conn);

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $parameters = [
        'secret' => $secret,
        'response' => $data->token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($parameters)
        )
      );
    $context  = stream_context_create($options);
    $captcha = file_get_contents($url, false, $context);
    $captcha = json_decode($captcha, true);

    if($captcha['success']){
        if(filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            $user->email = $data->email;
            $user->password = $data->password;

            $response = $user->register();
        } else {
            $response = array('message' => 'Invalid email');
        }
    } else {
        $response = array('message' => 'Invalid captcha');
    }
} else {
    $response = $conn;
}

echo json_encode($response);

?>