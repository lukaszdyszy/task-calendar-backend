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

    session_start();

    if(isset($_SESSION['logged'])){

        if($method == 'POST') {
            if(isset($_SESSION['id']) && isset($data->title) && isset($data->date_time)){
                $task->user_id = $_SESSION['id'];
                $task->title = $data->title;
                $task->date_time = $data->date_time;
    
                $response = $task->add();
            } else {
                $response = array('message' => 'failed');
            }
        } else if($method == 'GET') {
            $task->user_id = $_SESSION['id'];
            $task->date_time = $_GET['date_time'];
    
            $response = $task->getTasksFromUser();
        } else if($method == 'PUT') {
            if(isset($data->id)){
                $task->id = $data->id;
    
                $userID = $task->getTaskById();
    
                if($userID['user_id'] == $_SESSION['id']){
                    if(isset($data->title)){$task->title = $data->title;}else{$task->title = null;}
                    if(isset($data->date_time)){$task->date_time = $data->date_time;}else{$task->date_time = null;}
                    if(isset($data->done)){$task->done = $data->done;}else{$task->done = null;}
                    $response = $task->edit();
                } else {
                    $response = array('message' => 'Not logged.');
                }
            } else {
                $response = array('message' => 'failed');
            }
        } else if($method == 'DELETE') {
            if(isset($data->id)){
                $task->id = $data->id;
    
                $userID = $task->getTaskById();
                if($userID['user_id'] == $_SESSION['id']){
                    $response = $task->delete();
                } else {
                    $response = array('message' => 'Not logged.');
                }
            } else {
                $response = array('message' => 'failed');
            }
        }

    } else {
        $response = array('message' => 'Not logged.');
    }

} else {
    $response = $conn;
}


echo json_encode($response);

?>