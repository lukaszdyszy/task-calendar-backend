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
        $resp = array('error' => 0);

        try{
            $result = $this->conn->prepare("SELECT * FROM $this->table WHERE email = ?");
            if(!$result){
                throw new Exception('Invalid query');
            }
            $result->bind_param("s", $this->email);
            if(!$result->execute()){
                throw new Exception('Query execution error');
            }

            $result->bind_result($id, $email, $password);
            $result->store_result();
            if($result->num_rows == 1){
                while($result->fetch()){
                    if(password_verify($this->password, $password)){
                        $resp['logged'] = true;
                        $resp['id'] = $id;
                        $resp['email'] = $email;
                    } else {
                        $resp['logged'] = false;
                        $resp['message'] = 'Wrong email or password';
                    }
                }
            } else {
                $resp['logged'] = false;
                $resp['message'] = 'Wrong email or password';
            }
            $result->close();
        } catch(Exception $e){
            $resp['error'] = 1;
            $resp['message'] = $e->getMessage();
        }

        return $resp;
    }


    private function passwordValidate(){
        $response = array('valid' => false);

        $uppercase = preg_match('@[A-Z]@', $this->password);
        $lowercase = preg_match('@[a-z]@', $this->password);
        $number    = preg_match('@[0-9]@', $this->password);
        $specials  = preg_match('@[\\!\\@\\$\\%\\&\\?\\#\\_\\-]@', $this->password);

        if(!$uppercase || !$lowercase || !$number || !$specials || strlen($this->password)<8){
            $response['valid'] = false;
        } else {
            $response['valid'] = true;
        }

        return $response;
    }

    private function emailValidate(){
        $response = array('valid' => false);

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            $response['valid'] = false;
            $response['message'] = 'Invalid e-mail';
        } else {
            try{
                $stmt = $this->conn->prepare("SELECT email FROM $this->table WHERE email=?");
                if(!$stmt){
                    throw new Exception('Invalid query (e-mail validation)');
                }
                $stmt->bind_param("s", $this->email);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows != 0){
                    $response['valid'] = false;
                    $response['message'] = 'E-mail already exist';
                } else {
                    $response['valid'] = true;
                }
                $stmt->close();
            } catch(Exception $e){
                $response['valid'] = false;
                $response['message'] = $e->getMessage();
            }
        }

        return $response;
    }

    public function register(){
        $resp = array('Error' => 0);

        $v_email = $this->emailValidate();
        $v_pass = $this->passwordValidate();

        if(!$v_email['valid']){
            $resp['Error'] = 1;
            $resp['message'] = $v_email['message'];
        } else if(!$v_pass['valid']){
            $resp['Error'] = 1;
            $resp['message'] = 'Password does not match requirements.';
        } else {
            try{
                $result = $this->conn->prepare("INSERT INTO $this->table VALUES(null, ?, ?)");

                if(!$result){
                    throw new Exception('Invalid query');
                }
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
                $result->bind_param("ss", $this->email, $this->password);
                if(!$result->execute()){
                    throw new Exception('Query execution error');
                }

                $resp['message'] = 'User registered';
            } catch(Exception $e){
                $resp['Error'] = 1;
                $resp['message'] = $e->getMessage();
            }
        }

        return $resp;
    }
}

?>