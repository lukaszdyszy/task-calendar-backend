<?php

require_once('../../config/db.php');
require_once('../../models/user.php');

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){
    $data = json_decode(file_get_contents("php://input"));

    $user = new User($conn);

    session_start();

    if(isset($_SESSION['logged'])){
        
        $user->id = $_SESSION['id'];
        $user->password = $data->password;

        $response = $user->changePassword();
    } else {
        $response['Error'] = 1;
        $response['message'] = 'Not logged!';
    }
} else {
    $response = $conn;
}

echo json_encode($response);

?>