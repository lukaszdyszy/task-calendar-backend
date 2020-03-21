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

    $response = $user->register();
} else {
    $response = $conn;
}

echo json_encode($response);

?>