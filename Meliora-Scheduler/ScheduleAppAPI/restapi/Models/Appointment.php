<?php

/**
 * Creating new user (either professor OR student)
 * @param int $prof_id Professor "id" in Database
 * @param int $student_id Student "id" in Database
 * @param String $password User password
 */
class Appointment {

	var $appointmentID;
    var $prof_id;
	var $profName;
	var $profEmail;
	var $profOfficeLoc;
	var $stdName;
	var $stdEmail;
    var $student_id;
    var $appointment_time;
    var $appointment_date;
    var $reason_for_appointment;
    var $is_completed;
    var $is_cancelled;
    var $reason_cancel;
    var $cancelled_by;
    
    public function __construct(){
        
    }

    public function create($prof_id, $student_id, $appointment_time, $appointment_date, $reason_for_appointment) {
        $this->prof_id = $prof_id;
        $this->student_id = $student_id;
        $this->appointment_time = $appointment_time;
        $this->appointment_date = $appointment_date;
        $this->reason_for_appointment = $reason_for_appointment;
    }
	
	//This function updates members variables with MySQL query result
	public function make($query_result){
		$this->appointmentID = $query_result["id"];
		$this->prof_id = $query_result["prof_id"];
        $this->student_id = $query_result["student_id"];
		$this->profName = $query_result["prof_name"];
		$this->profOfficeLoc = $query_result["office_loc"];
        $this->profEmail = $query_result["prof_email"];
        $this->appointment_time = $query_result["appointment_time"];
        $this->appointment_date = $query_result["appointment_date"];
        $this->reason_for_appointment = $query_result["reason_for_appointment"];
        $this->is_completed = $query_result["is_completed"];
        $this->is_cancelled = $query_result["is_cancelled"];
        $this->reason_cancel = $query_result["reason_cancel"];
        $this->cancelled_by = $query_result["cancelled_by"];		
	}
	
	
	//This function updates members variables with MySQL query result
	//This will make an appointment booked by a student for a specific professor
	public function makeProfAppointment($query_result){
		$this->appointmentID = $query_result["id"];
		$this->prof_id = $query_result["prof_id"];
        $this->student_id = $query_result["student_id"];
		$this->stdName = $query_result["student_name"];
        $this->stdEmail = $query_result["student_email"];
        $this->appointment_time = $query_result["appointment_time"];
        $this->appointment_date = $query_result["appointment_date"];
        $this->reason_for_appointment = $query_result["reason_for_appointment"];
        $this->is_completed = $query_result["is_completed"];
        $this->is_cancelled = $query_result["is_cancelled"];
        $this->reason_cancel = $query_result["reason_cancel"];
        $this->cancelled_by = $query_result["cancelled_by"];		
	}
    
	public function getAppointmentID(){
		return $this->appointmentID;
	}
	
	public function setAppointmentID($id){
		$this->appointmentID = $id;
	}
	
    public function getProf_id() {
        return $this->prof_id;
    }

    public function getStudent_id() {
        return $this->student_id;
    }

    public function getAppointment_time() {
        return $this->appointment_time;
    }

    public function getAppointment_date() {
        return $this->appointment_date;
    }

    public function getReason_for_appointment() {
        return $this->reason_for_appointment;
    }

    public function getIs_completed() {
        return $this->is_completed;
    }

    public function getIs_cancelled() {
        return $this->is_cancelled;
    }

    public function getReason_cancel() {
        return $this->reason_cancel;
    }

    public function getCancelled_by() {
        return $this->cancelled_by;
    }

    public function setProf_id($prof_id) {
        $this->prof_id = $prof_id;
    }

    public function setStudent_id($student_id) {
        $this->student_id = $student_id;
    }

    public function setAppointment_time($appointment_time) {
        $this->appointment_time = $appointment_time;
    }

    public function setAppointment_date($appointment_date) {
        $this->appointment_date = $appointment_date;
    }

    public function setReason_for_appointment($reason_for_appointment) {
        $this->reason_for_appointment = $reason_for_appointment;
    }

    public function setIs_completed($is_completed) {
        $this->is_completed = $is_completed;
    }

    public function setIs_cancelled($is_cancelled) {
        $this->is_cancelled = $is_cancelled;
    }

    public function setReason_cancel($reason_cancel) {
        $this->reason_cancel = $reason_cancel;
    }

    public function setCancelled_by($cancelled_by) {
        $this->cancelled_by = $cancelled_by;
    }
	
	//Convert this class instance to json
	public function expose() {
		return get_object_vars($this);
	}

}

?>
