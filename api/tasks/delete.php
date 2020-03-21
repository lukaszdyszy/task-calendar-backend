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
                $response = $task->delete();
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