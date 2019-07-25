<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once('../../config/db.php');
require_once('../../models/task.php');

$response = array();

$db = new Database();
$conn = $db->connect();

if(gettype($conn) == 'object'){

    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"));

    $task = new Task($conn);

    if($method == 'POST') {
        if(isset($data->user_id) && isset($data->title) && isset($data->date_time)){
            $task->user_id = $data->user_id;
            $task->title = $data->title;
            $task->date_time = $data->date_time;

            $response = $task->add();
        } else {
            $response = array('message' => 'failed');
        }
    } else if($method == 'GET') {
        $task->user_id = $_GET['user_id'];
        $task->date_time = $_GET['date_time'];

        $response = $task->getTasksFromUser();
    } else if($method == 'PUT') {
        if(isset($data->id)){
            $task->id = $data->id;
            $task->title = $data->title;
            $task->date_time = $data->date_time;
            $task->done = $data->done;

            $response = $task->edit();
        } else {
            $response = array('message' => 'failed');
        }
    } else if($method == 'DELETE') {
        if(isset($data->id)){
            $task->id = $data->id;

            $response = $task->delete();
        } else {
            $response = array('message' => 'failed');
        }
    }
} else {
    $response = $conn;
}


echo json_encode($response);

?>