<?php

session_start();

$resp = array();

if(isset($_SESSION['logged'])){
    $resp = array('logged' => $_SESSION['logged'], 'id' => $_SESSION['id'], 'email' => $_SESSION['email']);
} else {
    $resp = array('logged' => false);
}

echo json_encode($resp);

?>