<?php

require_once('../../config/db.php');
require_once('../../models/task.php');

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){
    $data = json_decode(file_get_contents("php://input"));

    $task = new Task($conn);

    session_start();

    if(isset($_SESSION['logged'])){
        if(isset($data->title) && isset($data->date_time)){
            $task->user_id = $_SESSION['id'];
            $task->title = $data->title;
            $task->date_time = $data->date_time;

            $response = $task->add();
        } else {
            $response = array('message' => 'failed');
        }

    } else {
        $response = array('message' => 'Not logged.');
    }

} else {
    $response = $conn;
}


echo json_encode($response);

?>