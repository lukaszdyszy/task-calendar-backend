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
        
        $task->user_id = $_SESSION['id'];
        $task->date_time = $_GET['date_time'];
        
        $response = $task->getTasksFromUser();
        
    } else {
        $response = array('message' => 'Not logged.');
    }
    
} else {
    $response = $conn;
}


echo json_encode($response);

?>