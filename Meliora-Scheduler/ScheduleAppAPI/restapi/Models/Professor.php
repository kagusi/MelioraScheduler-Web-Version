<?php

class Professor {
	
	var $id;
    var $name;
    var $email;
    var $password;
	var $department;
    var $api_key;
    var $office_locatn;
    var $office_hrs;
    var $meeting_interval;
    var $full_office_hrs;
    var $semester_start;
    var $semester_end;
    var $dateCreated;

    public function __construct() {
        
    }

    public function create($name, $email, $password, $api_key, $office_locatn, $office_hrs, 
							$meeting_interval, $full_office_hrs, $semester_start, $semester_end, $dateCreated) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->api_key = $api_key;
        $this->office_locatn = $office_locatn;
        $this->office_hrs = $office_hrs;
        $this->meeting_interval = $meeting_interval;
        $this->full_office_hrs = $full_office_hrs;
        $this->semester_start = $semester_start;
        $this->semester_end = $semester_end;
        $this->dateCreated = $dateCreated;
    }
	
	//This function updates members variables with MySQL query result
	public function make($query_result){
		$this->id = $query_result["id"];
		$this->name = $query_result["prof_name"];
        $this->email = $query_result["prof_email"];
		$this->department = $query_result["department"];
        //$this->password = $query_result["password"];
        $this->api_key = $query_result["api_key"];
        $this->office_locatn = $query_result["office_loc"];
        $this->office_hrs = $query_result["office_hrs"];
        $this->meeting_interval = $query_result["meeting_interval"];
        $this->full_office_hrs = $query_result["full_schedule"];
        $this->semester_start = $query_result["semester_start"];
        $this->semester_end = $query_result["semester_end"];
        $this->dateCreated = $query_result["date_created"];
	}
	
	public function getID(){
		return $this->id;
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
	
	public function getDepartment(){
		return $this->department;
	}

    public function getApi_key() {
        return $this->api_key;
    }

    public function getOffice_locatn() {
        return $this->office_locatn;
    }

    public function getOffice_hrs() {
        return $this->office_hrs;
    }

    public function getMeeting_interval() {
        return $this->meeting_interval;
    }

    public function getFull_office_hrs() {
        return $this->full_office_hrs;
    }

    public function getSemester_start() {
        return $this->semester_start;
    }

    public function getSemester_end() {
        return $this->semester_end;
    }

    public function getDateCreated() {
        return $this->dateCreated;
    }
	
	public function setID($Id){
		$this->id = $Id;
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
	
	public function setDepartment($department){
		$this->department = $department;
	}

    public function setApi_key($api_key) {
        $this->api_key = $api_key;
    }

    public function setOffice_locatn($office_locatn) {
        $this->office_locatn = $office_locatn;
    }

    public function setOffice_hrs($office_hrs) {
        $this->office_hrs = $office_hrs;
    }

    public function setMeeting_interval($meeting_interval) {
        $this->meeting_interval = $meeting_interval;
    }

    public function setFull_office_hrs($full_office_hrs) {
        $this->full_office_hrs = $full_office_hrs;
    }

    public function setSemester_start($semester_start) {
        $this->semester_start = $semester_start;
    }

    public function setSemester_end($semester_end) {
        $this->semester_end = $semester_end;
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