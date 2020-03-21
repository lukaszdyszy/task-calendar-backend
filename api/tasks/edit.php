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
        
    } else {
        $response = array('message' => 'Not logged.');
    }
    
} else {
    $response = $conn;
}


echo json_encode($response);

?>