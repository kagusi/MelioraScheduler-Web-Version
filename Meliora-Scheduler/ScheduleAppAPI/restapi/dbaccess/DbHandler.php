<?php

date_default_timezone_set("America/New_York");
session_start();
/**
 * Class to handle all DB operations
 * @author Kennedy Agusi
 * Department of Computer Science
 * University of Rochester
 */
class DbHandler {

    public $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

/***************************** For Students (Used for Mobile APP)*********************************************************************/
    /**
     * Create new Student (ie insert new student into DB)
     * @param $student An instance of Student Class (ie new student)
     */
    public function createStudent($student) {
        $newStudent = $student;
        $response = array();
		$table = "students";
        $query = "INSERT INTO students(student_name, student_email, password, api_key, date_created) values(?, ?, ?, ?, ?)";

        // First check if user already exist in DB
        if (!$this->isUserExists($newStudent->email, $table)) {

            //-------- Encrypt password using RSA Algorithm before inserting into database -----------------------		
            $rsa = new RSA();
            //Encrypt password using 'dateCreated' as salt
            $encryptedPassword = $rsa->encrypt($newStudent->password, $newStudent->dateCreated);

            if ($encryptedPassword == FAILED_TO_ENCRYPT) {
                return FAILED_TO_ENCRYPT;
            }

            // Generate API key
            //$api_key = $this->generateApiKey();

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssss", $newStudent->name, $newStudent->email, $encryptedPassword, $newStudent->api_key, $newStudent->dateCreated);
            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted into DB
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to insert into DB
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already exists in the DB
            return USER_ALREADY_EXIST;
        }

        return $response;
    }
	
	
		/**
     * Verifies a Student's identity and return user data
     * @param String $email User login email id
     * @param String $password User login password
     * @return user data
     */
    public function studLogin($email, $password) {
		$response = array();
        //Retrieve user data using email
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE student_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		//$user = $result->fetch_assoc();
		
		
        if ($result) {
            // Found user with the email
            // Now verify the password
			
			$encryptedPass = $result["password"];
			$salt = $result["date_created"];
			$ID = $result["id"];
			$unencrypted = $password . $salt;
			$rsa = new RSA();
			$decrypted = $rsa->decrypt($encryptedPass);
			
			$stmt->close();

            if ($decrypted == $unencrypted) {
                // User password is correct
				$response["error"] = false;
				$response["message"] = array();
			
				$user = new Student();
				$user->setStdID($ID);
				$user->setPassword($password);
				$user->make($result);
				
				//Retrieve user data from query result
				$userData = $user->expose();
				array_push($response["message"], $userData);
                return $response;
            } else {
                // user password is incorrect
				$response["error"] = true;
				$response["message"] = "Incorrect password";
                return $response;
            }
        } else {
			$stmt->close();
            // user not found
			$response["error"] = true;
			$response["message"] = "User NOT Found!";
            return $response;
        }
    }
	
	
	/**
     * Login student using api key
     * @param String $stdID User id in DB
     * @return user data
     */
    public function loginByID($api_key) {
		$response = array();
        //Retrieve user data using email
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		//$user = $result->fetch_assoc();
		
		
        if ($result) {
            // Found user with the specified id
            // Now verify the password
			
			$encryptedPass = $result["password"];
			$ID = $result["id"];
			$rsa = new RSA();
			$decrypted = $rsa->decrypt($encryptedPass);
			
			$stmt->close();
			
				$response["error"] = false;
				$response["message"] = array();
			
				$user = new Student();
				$user->setStdID($ID);
				$user->setPassword($$decrypted);
				$user->make($result);
				
				//Retrieve user data from query result
				$userData = $user->expose();
				array_push($response["message"], $userData);
                return $response;

        } else {
			$stmt->close();
            // user not found
			$response["error"] = true;
			$response["message"] = "User NOT Found!";
            return $response;
        }
    }

	/**
     * Search Professor
     * @param $name Partial or full name of professor
     */
	 public function searchProf($name) {
		 //$name = "%" .$name ."%";
		 //CONCAT('%', ?, '%')
		$response = array();	
		$response["Found"] = false;
		$response["message"] = array();
		
		$query = "SELECT id, prof_name, department, prof_email, office_loc, office_hrs FROM professors WHERE prof_name LIKE CONCAT('%', ?, '%')";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name);
        $stmt->execute();
		$result = $stmt->get_result();
        $stmt->close();
		
		while ($professor = $result->fetch_assoc()) {
			$temp = array();
			$response["Found"] = true;
            $temp["id"] = $professor["id"];
			$temp["name"] = $professor["prof_name"];
			$temp["department"] = $professor["department"];			
			$temp["email"] = $professor["prof_email"];
			$temp["officLoc"] = $professor["office_loc"];
			$temp["officeHrs"] = json_decode($professor["office_hrs"]);
			array_push($response["message"], $temp);
        }
		
		if(!$response["message"])
			$response["message"] = "Professor NOT Found!";
					
		return $response; 
		 
	 }
	
	/**
     * Create Appointment (Insert new appointment into DB)
     * @param $appointment An instance of Appointment Class (ie new appointment)
     */
	 public function scheduleAppointment($appointment) {
		 
		$newAppointment = $appointment;
        $response = array();
        $query = "INSERT INTO appointments(prof_id, student_id, appointment_time, appointment_date, reason_for_appointment) values(?, ?, ?, ?, ?)";

        // First check if professor is available at specified appointment time and date
        if (!$this->isAppointmentAvailable($appointment)) {

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssss", $newAppointment->prof_id, $newAppointment->student_id, $newAppointment->appointment_time, $newAppointment->appointment_date, $newAppointment->reason_for_appointment);
            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // Appointment successfully created/inserted into DB
                return APPOINTMENT_SCHEDULED_SUCCESSFULLY;
            } else {
                // Failed to insert into DB
                return APPOINTMENT_SCHEDULED_FAILED;
            }
        } else {
            //Professor is  booked for specified appointment time and date
            return APPOINTMENT_NOT_AVAILABLE;
        }

        return $response;
	 }
	 
	 
	
	 /**
     * Checking whether professor is available at specified appointment time and date
     */
	private function isAppointmentAvailable($appointment){

        $query = "SELECT * FROM appointments WHERE prof_id = ? AND appointment_time = ? AND appointment_date = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $appointment->prof_id, $appointment->appointment_time, $appointment->appointment_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
		$found = false;
		
		/**
		*Check whether the speficied an appointment has been booked at specified time and date
		*If appointment is found, then check whether it has been cancelled either by professor or student
		*If it was cancelled by a student, then the specified time and date can be re-booked by another student
		*If it was cancelled by a professor, then the specified time and date can NOT be re-booked by any student
		*/
		while ($appointment = $result->fetch_assoc()) {
            $isCancelled = $appointment["is_cancelled"];
			if($isCancelled == "yes"){
				$cancelledBy = $appointment["cancelled_by"];
				if($cancelledBy == "Student")
					$found = false;
				else
					$found = true;
			}
        }
		
        return $found;
	
	}
	
	 /**
     * Retrieve all currently booked appointment time (ie appointment already booked by a student) of a particular professor for a specific date
	 * @param String $prof_id Professor id in DB
     * @param String $date Date of appointment
     */
	private function getAllBookedAppointmentTime($prof_id, $date){
		$response = array();	
		$isCompleted = "no";
		
		$query = "SELECT appointment_time FROM appointments WHERE prof_id = ? AND appointment_date = ? AND is_completed = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $prof_id, $date, $isCompleted);
        $stmt->execute();
		$result = $stmt->get_result();
        $stmt->close();
		
		while ($time = $result->fetch_assoc()) {
            $response[$time["appointment_time"]] = $time["appointment_time"];
        }
		
		return $response;
		
	}
	
	/**
     * Retrieve professor's full schedule (ie office hours or availability)
	 * "full_schedule" row contains professor's schedule splitted into intervals
	 * @param String $prof_id Professor id in DB
	 * This function returns an array
     */
	public function getFullSchedule($prof_id){	
		$query = "SELECT full_schedule FROM professors WHERE id = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $prof_id);
        $stmt->execute();
		$result = $stmt->get_result();
        $stmt->close();
		
		$schedule = $result->fetch_assoc();
		
        $response = json_decode($schedule["full_schedule"], true); //convert data to json object
		
		return $response;
		
	}
	
	/**
     * Retrieve professor's schedule for specified date showing open slots and booked slots
	 * @param String $prof_id Professor id in DB
	 * This function returns an array
     */
	public function getSchedulePlusAvailablity($prof_id, $date){	
		$booked = $this->getAllBookedAppointmentTime($prof_id, $date);
		$schedule = $this->getFullSchedule($prof_id);
		//Retrieve "day of week" from the specified date
		$date = explode("-", $date);
		$date = implode("/", $date);
		$day = date('w', strtotime($date))+1;
		
		//$keys = array_keys($schedule);
		
		//Select professor's schedule for specified day
		$currentSchedule = $schedule[$day];
		$response = array();
		$response["schedule"] = array();
		$response["days"] = array_keys($schedule);
		$arrlength = count($currentSchedule);

		for($x = 0; $x < $arrlength; $x++) {
			$temp = array();
			//Check if a schedule is already booked
			if(array_key_exists($currentSchedule[$x], $booked))
				$temp["isBooked"] = true;
			else
				$temp["isBooked"] = false;		
			$temp["time"] = $currentSchedule[$x];			
			array_push($response["schedule"], $temp);
		}
		$response["error"] = false;
		return $response;
	}
	
	/**
     * This function retrieve all appointments currently booked by a specific student
	 * @param String $studID Student id in DB
	 * This function returns an array
     */
	public function getAllCurrentAppointment($studID){
		$response = array();	
		$isCompleted = "no";
		$query = "SELECT a.prof_name, a.prof_email, a.office_loc, b.id, b.prof_id, b.student_id, b.appointment_time, b.appointment_date, "
                . "b.reason_for_appointment, b.is_cancelled, b.reason_cancel, b.cancelled_by, b.is_completed FROM professors a "
                . "INNER JOIN appointments b ON a.id = b.prof_id WHERE b.student_id = ? AND b.is_completed = ? AND appointment_date >= CURDATE()";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $studID, $isCompleted);
        $stmt->execute();		
		$result = $stmt->get_result();
        $stmt->close();

		$response["Appointments"] = array();
		$response["Found"] = false;
		
		
		while ($temp = $result->fetch_assoc()) {
			$isCancelled = $temp["is_cancelled"];
			//Check if a professor has cancelled this appointment
			if($isCancelled == "no"){
				$response["Found"] = true;
				$appointment = new Appointment();
				$appointment->make($temp);
				$dt = $appointment->getAppointment_date();
				$dtarry = explode("-", $dt);
				$date = implode("/", $dtarry);
				$appointment->setAppointment_date($date);
				//Retrieve appointment data from query result
				$data = $appointment->expose();		 
				array_push($response["Appointments"], $data);			
			}			
        }
			
		return $response;
		
	}
	
	/**
     * This function retrieves appointment history of a specific student
	 * @param String $studID Student id in DB
	 * This function returns an array
     */
	public function getAppointmentHistory($studID){
		$response = array();
		$isCompleted = "yes";
		$query = "SELECT a.prof_name, a.prof_email, a.office_loc, b.id, b.prof_id, b.student_id, b.appointment_time, b.appointment_date, "
                . "b.reason_for_appointment, b.is_cancelled, b.reason_cancel, b.cancelled_by, b.is_completed FROM professors a "
                . "INNER JOIN appointments b ON a.id = b.prof_id WHERE b.student_id = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $studID);
        $stmt->execute();
		$result = $stmt->get_result();
        $stmt->close();
		
		$response["Appointments"] = array();
		$response["Found"] = false;		
	
		while ($temp = $result->fetch_assoc()) {
			$isCancelled = $temp["is_cancelled"];
			$isCompleted = $temp["is_completed"];
			if($isCompleted == "no"){
				if($isCancelled == "yes"){
					$response["Found"] = true;
					$appointment = new Appointment();
					$appointment->make($temp);
					//Retrieve appointment data from query result
					$data = $appointment->expose();		 
					array_push($response["Appointments"], $data);
				}
			}
			else{
				$response["Found"] = true;
				$appointment = new Appointment();
				$appointment->make($temp);
				//Retrieve appointment data from query result
				$data = $appointment->expose();		 
				array_push($response["Appointments"], $data);
			}
			
			
        }
				
		return $response;
		
	}
	
	
		/*This function is used to cancel an appointment
	* @param Array $data Array containing appointment id, reason for cancelation and cancelledBy(student or professor)
	*/
	public function cancelAppointment($data){
		$appointmentID = $data["appointment_id"];
		$reason = $data["reason"];
		$cancelledBy = $data["cancelledBy"];
		$name = NULL;
		$query = NULL;
		$appointQuery = NULL;
		$response = array();
		$response["error"] = false;
		//$response["appointment"] = array();
		/* If an appointment is cancelled by a Student, the spot can be re-booked by another student
		* If an appointment is cancelled by a Professor, the spot can NOT be re-booked by another student
		*/
		if($cancelledBy == "Student"){
			$name = "student_name";
			$query = "UPDATE appointments set is_cancelled = 'yes', is_completed = 'yes', reason_cancel = ?, cancelled_by = ? WHERE id = ?";
			//Statement for select details used in sending notification to a user about canceled appointment
			$appointQuery = "SELECT a.student_name, b.id, b.prof_id, b.appointment_time, b.appointment_date, c.mesage_api_key "
                . "FROM students a INNER JOIN appointments b ON a.id = b.student_id INNER JOIN professors c ON c.id = b.prof_id WHERE b.id = ?";
		}			
		else if($cancelledBy == "Professor"){
			$name = "prof_name";
			$query = "UPDATE appointments set is_cancelled = 'yes', is_completed = 'no', reason_cancel = ?, cancelled_by = ? WHERE id = ?";
			//Statement for select details used in sending notification to a user about canceled appointment
			$appointQuery = "SELECT a.prof_name, b.id, b.student_id, b.appointment_time, b.appointment_date, c.api_key "
                . "FROM professors a INNER JOIN appointments b ON a.id = b.prof_id INNER JOIN students c ON c.id = b.student_id WHERE b.id = ?";
		}
			
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $reason, $cancelledBy, $appointmentID);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
		
		/* If appointment was successfully cancelled, then send a notification to student
		*/
        if($num_affected_rows > 0){
			$stmt = $this->conn->prepare($appointQuery);
			$stmt->bind_param("s",$appointmentID);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
			
			while ($temp = $result->fetch_assoc()) {
				$data = array();
				$data["name"] = $temp[$name];
				$data["time"] = $temp["appointment_time"];
				$data["date"] = $temp["appointment_date"];
				if($cancelledBy == "Student")
					$data["api_key"] = $temp["mesage_api_key"];
				else
					$data["api_key"] = $temp["api_key"];
				$response["appointment"] = $data;
			}					
		}
		else
			$response["error"] = true;
		
		return $response;
		
	}
	
		

/***************************** For Professors (Used for Desktop APP)*********************************************************************************/	
	
	/**
     * Create new Professor (ie insert new professor into DB)
     * @param $professor An instance of Professor Class (ie new professor)
     */
    public function createProfessor($professor, $msg_key) {
        $newProfessor = $professor;
        $response = array();
		$table = "professors";
        $query = "INSERT INTO professors(prof_name, prof_email, password, api_key, department, date_created, mesage_api_key) values(?, ?, ?, ?, ?, ?, ?)";
        // First check if user already exist in DB
        if (!$this->isUserExists($newProfessor->email, $table)) {

            //-------- Encrypt password using RSA Algorithm before inserting into database -----------------------		
            $rsa = new RSA();
            //Encrypt password using 'dateCreated' as salt
            $encryptedPassword = $rsa->encrypt($newProfessor->password, $newProfessor->dateCreated);

            if ($encryptedPassword == FAILED_TO_ENCRYPT) {
                return FAILED_TO_ENCRYPT;
            }

            // Generate API key
            $api_key = $this->generateApiKey();
			

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssssss", $newProfessor->name, $newProfessor->email, $encryptedPassword, $api_key,  $newProfessor->department, $newProfessor->dateCreated, $msg_key);
            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted into DB
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to insert into DB
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already exists in the DB
            return USER_ALREADY_EXIST;
        }

        return $response;
    }
	
	
	 /**
     * Verifies a Professor's identity and return user data
     * @param String $email User login email id
     * @param String $password User login password
     * @return user data
     */
    public function profLogin($email, $password) {
		$response = array();
        //Retrieve user data using email
        $stmt = $this->conn->prepare("SELECT * FROM professors WHERE prof_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		//$user = $result->fetch_assoc();
		
		
        if ($result) {
            // Found user with the email
            // Now verify the password
			
			$encryptedPass = $result["password"];
			$salt = $result["date_created"];
			$unencrypted = $password . $salt;
			$rsa = new RSA();
			$decrypted = $rsa->decrypt($encryptedPass);
			
			$stmt->close();

            if ($decrypted == $unencrypted) {
                // User password is correct
				$response["error"] = false;
				$response["message"] = array();
	
				$user = new Professor();
				$user->setPassword($password);
				$user->make($result);
				//Retrieve user data from query result
				$userData = $user->expose();
				array_push($response["message"], $userData);
				setcookie("User", $user->api_key, time() + (10*365*24*60*60), "/"); 
				$_SESSION['userDetail'] = $userData;
                return $response;
            } else {
                // user password is incorrect
				$response["error"] = true;
				$response["message"] = "Incorrect password";
                return $response;
            }
        } else {
			$stmt->close();
            // user not existed with the email
			$response["error"] = true;
			$response["message"] = "User NOT Found!";
            return $response;
        }
    }
	
	
	/**Login in Professor without email and password
	* This function is used when a user still has an active cookie
     * Verifies a Professor's identity and return user data
     * @return user data
     */
    public function profRestore($api_key) {
		$response = array();
        //Retrieve user data using email
        $stmt = $this->conn->prepare("SELECT * FROM professors WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		//$user = $result->fetch_assoc();
		
		
		// Found user with the specified api_key
        if ($result) {
			
			$stmt->close();
			
            // User password is correct
			$response["error"] = false;
			$response["message"] = array();
	
			$user = new Professor();
			$user->make($result);
			//Retrieve user data from query result
			$userData = $user->expose();
			array_push($response["message"], $userData);
			setcookie("User", $user->api_key, time() + (10*365*24*60*60), "/"); 
			$_SESSION['userDetail'] = $userData;
            return $response;

        } else {
			$stmt->close();
            // user not existed with the email
			$response["error"] = true;
			$response["message"] = "User NOT Found!";
            return $response;
        }
    }
	
	public function logOut(){
		unset($_COOKIE['User']);
		setcookie('User', "", time() - 3600, "/");
		// remove all session variables
		session_unset(); 
		// destroy the session 
		session_destroy(); 
		header ('Location: login.html');	
		die();
	}
	
	/**
     * Create Professor's schedule
     * @param String $id Professor's ID in DB
     * @param Array $schedule Array of professor's schedule
     * @(NOTE: This operation is same as updating schedule
     */
    public function createProfSchedule($data) {	
		$schedule = $data["schedule"];
		$length = count($schedule);
		//This holds the schedule splitted into meeting intervals
		$fullSchedule = array();
		$keys = array_keys($schedule);
		//loop through schedule
		for($i = 0; $i<$length; $i++){
			//Compute professor's schedule by splitting it into appointment duration (intervals) using specified appointment duration(interval)
			$fullSchedule[$keys[$i]] = $this->computeTimeIntervals($schedule[$keys[$i]], $data["interval"]);			
		}
		
		//Convert array to json string below inserting into DB
		$fullSchedule = json_encode($fullSchedule);
		$schedule = json_encode($schedule);
		
		$query = "UPDATE professors set office_loc = ?, office_hrs = ?, meeting_interval = ?, full_schedule = ?," 
				. " semester_start = ?, semester_end = ? WHERE id = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssss", $data["officeLoc"], $schedule, $data["interval"], $fullSchedule, $data["start"], $data["end"], $data["id"]);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        if($num_affected_rows > 0)
			return true;
		else
			return false;

	}
	
	//Updates message api key on firebase token reshresh 
	public function updateMsgAPIKey($data){
		$msg_key = $data["msg_key"];
		$oldToken = $data["oldToken"];
		
		$query = "UPDATE professors set mesage_api_key = ? WHERE mesage_api_key = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $msg_key, $oldToken);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        if($num_affected_rows > 0)
			return true;
		else
			return false;
			
	}
	
	
	
	 /**
     * Retrieve messag api key from DB
     */
	public function retrieveMsgAPIKey($profID){

        $stmt = $this->conn->prepare("SELECT mesage_api_key FROM professors WHERE id = ?");
        $stmt->bind_param("i", $profID);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key["mesage_api_key"];
        } else {
            return "Not Found";
        }
	
	}
	
	//Update professor profile
	public function updateProfessor($prof){
		$query = "UPDATE professors set prof_name = ?, department = ? WHERE id = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $prof->name, $prof->department, $prof->id);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        if($num_affected_rows > 0){
			//update session
			$userData = $_SESSION['userDetail'];
			$userData["name"] = $prof->name;
			$userData["department"] = $prof->department;
			$_SESSION['userDetail'] = $userData;
			return true;
		}
			
		else
			return false;
	}
	
	/**
     * This function splits the supplied time into meeting intervals
     * @param Array $time Professor's meeting time (schedule)
     * @param int $interval Professor's meeting interval
     * @return 
     */
	private function computeTimeIntervals($time, $interval){
		$computed = array();
		//convert interval to milli
		$interval = $interval * 60;
		
		foreach($time as $value) {
			
			list($start, $end) = explode("-", $value);
			//convert time to milli (ie epoch time)
			$start = strtotime($start);
			$end = strtotime($end);
			
			array_push($computed, $start);
			
			while($start != $end){
				
				if(($end-$start) > $interval){				
					$start = $start + $interval;
					array_push($computed, $start);
				}
				else{
					$start = $end;
				}
									
			}

		}
		//sort array
		sort($computed);
		
		//convert time to 12hrs format and return
		return $this->convertTime($computed);
	}
	
	/*convert epoch(milli) to 12hrs time format
	* @param Array $time Array of time to be sorted
	*/
	private function convertTime($time){
		$comp = array();
		foreach($time as $value) {
			//convert to 12hrs format
			$rs = date('h:i A', $value);
			array_push($comp, $rs);
		}		
		return $comp;
	}
	

	
	/*This function is used to cancel professor's schedule for a specified date
	* @param Array $data Array containing appointment details 
	* (NOTE: This is a costly transaction, take note of the three(3) MySQL operations)
	*/
	public function cancelAppointmentByDate($data){
		$reason = $data["reason"];
		$cancelledBy = $data["cancelledBy"];
		$profID = $data["profID"];
		$date = $data["date"];
		$isCancelled = "yes";
		$isCompleted = "no";		
		$reasonForAppoint = "none";
		//Get day from specified date
		$day = date('w', strtotime($date));
		
		//Update(cancel) all aready booked appointment on specified date
		$query = "UPDATE appointments set is_cancelled = 'yes', reason_cancel = ?, cancelled_by = ? WHERE prof_id = ? AND appointment_date = ?";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $reason, $cancelledBy, $profID, $date);
        $stmt->execute();
		
		
		//Retrieve professor's full schedule 
		$query = "SELECT full_schedule FROM professors WHERE id = ?";
		$stmt = $this->conn->prepare($query);
		$stmt->bind_param("s", $profID);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		
		//Convert JSON object  to  PHP array
		$schedule = json_decode($result["full_schedule"], true);
		//Retrieve professor's schedule on specified day
		$daySchedule = $schedule[$day];
		//This shows that no student booked this appointment
		$stdID = 0;

		$query = "INSERT INTO appointments(student_id, prof_id, appointment_time, appointment_date, is_cancelled, reason_cancel, cancelled_by, reason_for_appointment, is_completed)" 
				."values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$status = false;
		//loop through the specified schedule and insert into appointment as cancelled appointment
		foreach($daySchedule as $value) {
			$stmt = $this->conn->prepare($query);
			$stmt->bind_param("sssssssss", $stdID, $profID, $value , $date, $isCancelled, $reason, $cancelledBy, $reasonForAppoint, $isCompleted);
			$result = $stmt->execute();
			if($result)
				$status = true;
			else
				$status = false;
		}
	
        $stmt->close();
		
        return $status;
		
	}
	
	
	/*Retrieve all currently booked appointment for a specific professor
	* @param String $profID Professor's ID in DB
	*/
	public function getAllProfAppointment($profID){
		$response = array();
		$response["Appointment"] = array();
		$response["Found"] = false;

		$query = "SELECT a.student_name, a.student_email, b.id, b.prof_id, b.student_id, b.appointment_time, b.appointment_date, "
                . "b.reason_for_appointment, b.is_cancelled, b.reason_cancel, b.cancelled_by, b.is_completed FROM students a "
                . "INNER JOIN appointments b ON a.id = b.student_id WHERE b.prof_id = ? AND appointment_date >= CURDATE()";
		$stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $profID);
        $stmt->execute();		
		$result = $stmt->get_result();
        $stmt->close();

		
		while ($temp = $result->fetch_assoc()) {
			
			$response["Found"] = true;
			$appointment = new Appointment();
			//Checking to make sure that the professor didnt cancel his/her schedule on this appointment date
			//Please refer to cancelAppointmentByDate()
			if($temp["student_id"] != 0){
				$appointment->makeProfAppointment($temp);
				$data = $appointment->expose();
				array_push($response["Appointment"], $data);
			}
			
        }
			
		return $response;
	
	}
	
	
	 /**
     * Retrieve user's password using email
     * @param String $email Email to check in DB
     */
    public function recoverPass($email, $table) {		
		$query = NULL;		
		if($table == "students")
			$query = "SELECT password, date_created from " . $table . " WHERE student_email = ? ";
		else if($table == "professors")
			$query = "SELECT password, date_created from " . $table . " WHERE prof_email = ? ";
		
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
		
		$response = array();
		$response["Found"] = false;
		
		if($result){
			
			$encryptedPass = $result["password"];
			$rsa = new RSA();
			$decrypted = $rsa->decrypt($encryptedPass);
			
			//Remove salt from password
			$date = $result["date_created"];
			$password = explode($date, $decrypted)[0]; 
			
			$response["Found"] = true;
			$response["password"] = $password;
			
		}
			
        return $response;
		
    }
	
	

    /**
     * Checking for duplicate user
     * @param String $email Email to check in DB
	 * @param String $table Table to check in DB (either professors or students table)
     * @return boolean
     */
    private function isUserExists($email, $table) {		
		$query = NULL;		
		if($table == "students")
			$query = "SELECT id from " . $table . " WHERE student_email = ? ";
		else if($table == "professors")
			$query = "SELECT id from " . $table . " WHERE prof_email = ? ";
		
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
		
        return $num_rows > 0;
    }
	
	
	 /**
     * Create new school
     * @param $school An instance of School Class (ie new professor)
     */
    public function createSchool($school) {
		
        $newSchool = $school;
        $response = array();
        $query = "INSERT INTO schools(school_name, secrete_question, secret_answer, access_code, API_link, date_joined) values(?, ?, ?, ?, ?, ?)";
  
        //Verify access code
        if ($this->verifyAccessCode($newSchool->accessCode)) {

            //-------- Encrypt secrete answer using RSA Algorithm before inserting into database -----------------------		
            $rsa = new RSA();
            //Encrypt secrete answer using 'dateCreated' as salt
            $encryptedAnswer = $rsa->encrypt($newSchool->secretAnswer, $newSchool->dateJoined);

            if ($encryptedAnswer == FAILED_TO_ENCRYPT) {
                return FAILED_TO_ENCRYPT;
            }
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssss", $newSchool->schoolName, $newSchool->SecreteQuestion, $encryptedAnswer, $$newSchool->accessCode, $newSchool->aPI_Link, $newSchool->dateJoined);
            $result = $stmt->execute();
            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted into DB
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to insert into DB
                return USER_CREATE_FAILED;
            }
        } else {
            // Invalid access code
            return INVALID_ACCESS_C0DE;
        }

        return $response;
    }
	
	
	/**
     * Verify Access code
     */
    private function verifyAccessCode($accessCode) {
		
		$query = NULL;
		$query = "SELECT id from Access WHERE access_code = ?";		
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $accessCode);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
		
        return $num_rows > 0;
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $api_key = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key, $userType) {
		$query = NULL;
		if($userType == "Student")
			$query = "SELECT id from students WHERE api_key = ?";
		else if($userType == "Professor")
			$query = "SELECT id from professors WHERE api_key = ?";
			
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

   
}
?>