<?php

//This is model class for 'Students' table in DB
class Student {

	var $stdID;
    var $name;
    var $email;
    var $password;
    var $api_key;
    var $dateCreated;

    //Empty constructor
    public function __construct() {
        
    }

    public function create($name, $email, $password, $dateCreated) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->dateCreated = $dateCreated;
    }
	
		//This function updates members variables with MySQL query result
	public function make($query_result){
		$this->name = $query_result["student_name"];
        $this->email = $query_result["student_email"];
        //$this->password = $query_result["password"];
		$this->api_key = $query_result["api_key"];
        $this->dateCreated = $query_result["date_created"];
	}

	public function getStdID(){
		return $this->stdID;
	}
	
    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getApi_key() {
        return $this->api_key;
    }

    public function getDateCreated() {
        return $this->dateCreated;
    }
	
	public function setStdID($id){
		$this->stdID = $id;
	}

    public function setName($name) {
        $this->name = $name;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setApi_key($api_key) {
        $this->api_key = $api_key;
    }

    public function setDateCreated($dateCreated) {
        $this->dateCreated = $dateCreated;
    }

    //Convert this class instance to json
    public function expose() {
        return get_object_vars($this);
    }

}

?>