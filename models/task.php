<?php

class Task {
    public $id;
    public $user_id;
    public $title;
    public $date_time;
    public $done;

    private $conn;
    private $table = 'tasks';

    public function __construct($db){
        $this->conn = $db;
    }

    public function add(){
        $resp = array();

        try {
            $result = $this->conn->prepare("INSERT INTO $this->table VALUES(null, ?, ?, ?, 0)");
            if($result == false){
                throw new Exception('Prepared query failure');
            }

            $result->bind_param("iss", $this->user_id, $this->title, $this->date_time);
            if($result->execute() == false){
                throw new Exception('Query execute failure');
            }

            $resp = array('message' => 'success');
        } catch (Exception $e){
            $resp = array('Exception' => $e->getMessage());
        }

        return $resp;
    }

    public function getTasksFromUser(){
        $resp = array();

        try {
            $result = $this->conn->prepare("SELECT * FROM $this->table WHERE `user_id` = ? AND `date_time` LIKE '$this->date_time%' ORDER BY `date_time` ASC");
            if($result == false){
                throw new Exception('Prepared query failure');
            }

            $result->bind_param('i', $this->user_id);
            if($result->execute() == false){
                throw new Exception('Query execute failure');
            }

            $result->bind_result($id, $user_id, $title, $date_time, $done);
            $result->store_result();

            while($result->fetch()){
                $resp[] = array('ID' => $id, 'user_id' => $user_id, 'title' => $title, 'date_time' => $date_time, 'done' => $done);
            }
        } catch(Exception $e){
            $resp = array('message' => $e->getMessage());
        }

        return $resp;
    }

    public function getTaskById(){
        $resp = array();

        try{
            $result = $this->conn->prepare("SELECT `user_id` FROM $this->table WHERE `ID` = ?");
            if($result == false){
                throw new Exception('Prepared query failure');
            }

            $result->bind_param('i', $this->id);
            if($result->execute() == false){
                throw new Exception('Query execute failure');
            }

            $result->bind_result($user_id);
            $result->store_result();
            $result->fetch();

            $resp = array('user_id' => $user_id);
        } catch(Exception $e){
            $resp = array('message' => $e->getMessage());
        }

        return $resp;
    }

    public function edit(){
        $resp = array();

        try {
            $result = $this->conn->prepare("UPDATE $this->table SET 
            `title`=ifnull(?, title), 
            `date_time`=ifnull(?, date_time), 
            `done`=ifnull(?, done) WHERE `ID`=?");

            if($result == false){
                throw new Exception('Prepared query failure');
            }

            $result->bind_param('ssii', $this->title, $this->date_time, $this->done, $this->id);
            if($result->execute() == false){
                throw new Exception('Query execute failure');
            }

            $resp = array('message' => 'success');
        } catch(Exception $e){
            $resp = array('message' => $e->getMessage());
        }

        return $resp;
    }

    public function delete(){
        $resp = array();

        try {
            $result = $this->conn->prepare("DELETE FROM $this->table WHERE `ID`=?");
            if($result == false){
                throw new Exception('Prepared query failure');
            }

            $result->bind_param('i', $this->id);
            if($result->execute() == false){
                throw new Exception('Query execute failure');
            }

            $resp = array('message' => 'success');
        } catch(Exception $e){
            $resp = array('message' => $e->getMessage());
        }

        return $resp;
    }
}

?>