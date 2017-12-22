<?php

class School {

    var $schoolName;
    var $SecreteQuestion;
    var $secretAnswer;
    var $accessCode;
    var $aPI_Link;
    var $dateJoined;

    public function __construct() {
        
    }

    public function create($schoolName, $SecreteQuestion, $secretAnswer, $accessCode, $aPI_Link, $dateJoined) {
        $this->schoolName = $schoolName;
        $this->SecreteQuestion = $SecreteQuestion;
        $this->secretAnswer = $secretAnswer;
        $this->accessCode = $accessCode;
        $this->aPI_Link = $aPI_Link;
        $this->dateJoined = $dateJoined;
    }
	
	//This function updates members variables with MySQL query result
	public function make($query_result){
		$this->schoolName = $query_result["school_name"];
        $this->SecreteQuestion = $query_result["secrete_question"];
        $this->secretAnswer = $query_result["secret_answer"];
        $this->accessCode = $query_result["access_code"];
        $this->aPI_Link = $query_result["API_link"];
        $this->dateJoined = $query_result["date_joined"];
	}

    public function getSchoolName() {
        return $this->schoolName;
    }

    public function getSecreteQuestion() {
        return $this->SecreteQuestion;
    }

    public function getSecretAnswer() {
        return $this->secretAnswer;
    }

    public function getAccessCode() {
        return $this->accessCode;
    }

    public function getAPI_Link() {
        return $this->aPI_Link;
    }

    public function getDateJoined() {
        return $this->dateJoined;
    }

    public function setSchoolName($schoolName) {
        $this->schoolName = $schoolName;
    }

    public function setSecreteQuestion($SecreteQuestion) {
        $this->SecreteQuestion = $SecreteQuestion;
    }

    public function setSecretAnswer($secretAnswer) {
        $this->secretAnswer = $secretAnswer;
    }

    public function setAccessCode($accessCode) {
        $this->accessCode = $accessCode;
    }

    public function setAPI_Link($aPI_Link) {
        $this->aPI_Link = $aPI_Link;
    }

    public function setDateJoined($dateJoined) {
        $this->dateJoined = $dateJoined;
    }
	
	//Convert this class instance to json
	public function expose() {
		return get_object_vars($this);
	}

}

?>