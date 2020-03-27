<?php

require_once('../../config/db.php');
require_once('../../config/secret.php');
require_once('../../models/user.php');

$data = json_decode(file_get_contents("php://input"));

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){

    $siteKey = $data->captcha;
    $userIP = $_SERVER['REMOTE_ADDR'];
    $verify = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$siteKey&remoteip=$userIP"));

    if(!$verify->success){
        $response['error'] = 1;
        $response['message'] = 'Confirm, you are not a robot.';
    } else {
        $user = new User($conn);
        
        $user->email = $data->email;
        $user->password = $data->password;

        $response = $user->register();
    }
} else {
    $response = $conn;
}

echo json_encode($response);

?>