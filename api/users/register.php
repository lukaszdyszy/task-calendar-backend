<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

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

    $resp = $user->register();

    $response = $resp;
} else {
    $response = $conn;
}

echo json_encode($response);

?>