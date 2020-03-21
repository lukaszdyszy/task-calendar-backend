<?php

require_once('../../config/db.php');
require_once('../../models/user.php');

$data = json_decode(file_get_contents("php://input"));

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){
    $user = new User($conn);

    $user->email = $data->email;
    $user->password = $data->password;

    $resp = $user->login();
    if($resp['error']==0 && $resp['logged']){
        session_start();
        $_SESSION['logged'] = true;
        $_SESSION['id'] = $resp['id'];
        $_SESSION['email'] = $resp['email'];
    }

    $response = $resp;
} else {
    $response = $conn;
}

echo json_encode($response);

?>