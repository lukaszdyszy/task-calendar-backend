<?php

class Database {
    private $host = '';
    private $user = '';
    private $password = '';
    private $name = '';

    public $conn;

    function connect(){
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->name);
        if($this->conn->connect_errno == 0){
            $this->conn->query("SET NAMES utf8");
            return $this->conn;
        } else {
            return array("message" => "Error: ".$this->conn->connect_errno);
        }
    }
}

?>