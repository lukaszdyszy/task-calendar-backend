<?php

session_start();

$resp = array();

if(isset($_SESSION['logged'])){
    session_regenerate_id();
    $resp = array('logged' => $_SESSION['logged'], 'id' => $_SESSION['id'], 'email' => $_SESSION['email']);
} else {
    $resp = array('logged' => false);
}

echo json_encode($resp);

?>