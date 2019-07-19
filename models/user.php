<?php

class User {
    public $id;
    public $email;
    public $password;

    private $conn;
    private $table = 'users';

    public function __construct($db){
        $this->conn = $db;
    }

    public function login(){
        $resp = array();

        $result = $this->conn->prepare("SELECT * FROM $this->table WHERE email = ?");
        $result->bind_param("s", $this->email);
        $result->execute();
        $result->bind_result($id, $email, $password);
        $result->store_result();
        if($result->num_rows == 1){
            while($result->fetch()){
                if(password_verify($this->password, $password)){
                    $resp = array("logged" => true, "id" => $id, "email" => $email);
                } else {
                    $resp = array("logged" => false, "message" => "Wrong email or password");
                }
            }
        } else {
            $resp = array("logged" => false, "message" => "Wrong email or password");
        }

        return $resp;
    }

    public function register(){
        $resp = array();

        $check_email = $this->conn->prepare("SELECT email FROM $this->table WHERE email = ?");
        if($check_email == false){
            $resp = array('message' => 'Error. Problem with query.');
        } else {
            $check_email->bind_param("s", $this->email);
            $check_email->execute();
            $check_email->store_result();
            if($check_email->num_rows > 0){
                $resp = array('message' => 'Email already exist');
            } else {
                $result = $this->conn->prepare("INSERT INTO $this->table VALUES(null, ?, ?)");
                if($result == false){
                    $resp = array('message' => 'Error. Problem with query.');
                } else {
                    $this->password = password_hash($this->password, PASSWORD_DEFAULT);
                    $result->bind_param("ss", $this->email, $this->password);
                    $result->execute();

                    $resp = array('message' => 'User registered.');
                }
            }
        }

        return $resp;
    }
}

?>