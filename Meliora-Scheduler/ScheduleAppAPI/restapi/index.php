<?php

require 'vendor/autoload.php';
require_once 'dbaccess/DbHandler.php';
require_once 'Models/Student.php';
require_once 'Models/RSA.php';
require_once 'Models/Appointment.php';
require_once 'Models/Professor.php';
require_once 'Models/School.php';
include 'dbaccess/config.php';


$app = new Slim\App();
//Handle Dependencies
$container = $app->getContainer();

$user_id = NULL;


//This function validates/check student's Api key in http header
$authenticateStd = function ($request, $response, $next) {

	//Get http request headers
	$status = 200;
    $headers = $request->getHeaders();
	$res = array();	

	
	//Validate Api key retrieved from http header
    if (isset($headers['HTTP_AUTHORIZATION'])) {

			//Initialize database object
			$db = new DbHandler();

			//Retrive Api key from http header
			$api_key = $headers['HTTP_AUTHORIZATION'][0];		
			//Check whether Api key is present in user's table
			if (!$db->isValidApiKey($api_key, "Student")) {            
				$res["error"] = true;
				$res["message"] = "Access Denied. Invalid Api key";
				$status = 401;
			} 
			else {
				//$user_id = $db->getUserId($api_key);
				$res = $next($request, $response);
				return $res;
			}
		
	}
	else {
        //Api key not present in user's table
        $res["error"] = true;
        $res["message"] = "Api key not found";
        $status = 400;
    }

    return $response->withJson($res,$status);
};



//This function validates/check professor's Api key in http header
$authenticateProf = function ($request, $response, $next) {

	//Get http request headers
	$status = 200;
    $headers = $request->getHeaders();
	$res = array();	

	
	//Validate Api key retrieved from http header
    if (isset($headers['HTTP_AUTHORIZATION'])) {

			//Initialize database object
			$db = new DbHandler();

			//Retrive Api key from http header
			$api_key = $headers['HTTP_AUTHORIZATION'][0];		
			//Check whether Api key is present in user's table
			if (!$db->isValidApiKey($api_key, "Professor")) {            
				$res["error"] = true;
				$res["message"] = "Access Denied. Invalid Api key";
				$status = 401;
			} 
			else {
				$resp = $next($request, $response);
				return $resp;
			}
		
	}
	else {
        //Api key not present in user's table
        $res["error"] = true;
        $res["message"] = "Api key not found";
        $status = 400;
    }

    return $response->withJson($res,$status);
};

/* This function can be used to mail users*/
//Send password to user (This is for password recovery)
function sendEmail($message, $receiver_email, $subject){
	
	
	// Create the Transport
	$transport = (new Swift_SmtpTransport('smtp.1and1.com', 587))
	  ->setUsername('admin@meliorascheduler.com')
	  ->setPassword('************')
	;

	// Create the Mailer using your created Transport
	$mailer = new Swift_Mailer($transport);

	// Create a message
	$message = (new Swift_Message($subject))
	  ->setFrom(['admin@meliorascheduler.com' => 'Meliora Scheduler'])
	  ->setTo([$receiver_email])
	  ->setBody($message,'text/html')
	  ;

	// Send the message
	$result = $mailer->send($message);
	return $result;

	/*
try {
	 // Configuring SMTP server settings
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = 'smtp.1and1.com';
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth = true;
	$mail->Username = "admin@meliorascheduler.com";
	$mail->Password = "Masterjesus@12";
	$mail->addCustomHeader('MIME-Version: 1.0');
	$mail->addCustomHeader('Content-Type: text/html; charset=ISO-8859-1');

	// Email Sending Details
	$mail->SetFrom('admin@meliorascheduler.com', 'Melior Scheduler');
	$mail->addAddress($receiver_email);
	$mail->Subject = $subject;
	$mail->isHTML(true);
	$mail->Body = $message;
	
	$mail->send();
	
	$response = array();

	$response["error"] = false;
	return $response;
	
} catch (Exception $e) {
	$response = array();
	$response["error"] = true;
	$response["message"] = $mail->ErrorInfo;
	return $response;
}
*/	
	
}


/*Send Android notification to app user
* @param $reg_id: This is an app user's api key stored in database during signup
*/
function sendNotification($reg_id, $message, $msg_title){
	#prep the bundle
		$data = array
			(
				'message' 	=> $message,
				'title'	=> $msg_title,
				//'sound' => 'mySound'/*Default sound*/
			 );
		 $notification = array
			  (
				'body' 	=> $message,
				'title'	=> $msg_title,
				'icon'	=> 'ic_notify_icon',
				//'sound' => 'mySound'/*Default sound*/
			  );
		$fields = array
				(
					'to' => $reg_id,
					'notification'	=> $notification,
					'data'	=> $data,
				);
			
		$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY, //This is my app server's API KEY from Firebase defined in config.php
					'Content-Type: application/json'
				);
	#Send Reponse To FireBase Server	
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
	#Echo Result Of FireBase Server
	return $result;			
}




/*Send web notification to we app user
* @param $reg_id: This is an app user's api key stored in database during signup
*/
function sendWebNotification($reg_id, $message, $msg_title){

		 $notification = array
			  (
				'body' 	=> $message,
				'title'	=> $msg_title,
				'click_action' => "https://localhost/IndependentProject/prof/pages/account.php",
				'icon'	=> 'IndependentProject/prof/Images/logo.png',
				//'sound' => 'mySound'/*Default sound*/
			  );
		$fields = array
				(
					'to' => $reg_id,
					'notification'	=> $notification,
				);
			
		$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY, //This is my app server's API KEY from Firebase defined in config.php
					'Content-Type: application/json'
				);
	#Send Reponse To FireBase Server	
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch );
			curl_close( $ch );
	#Echo Result Of FireBase Server
	return $result;			
}




/************************************Handles student operations*******************************************************************/
//create student
$app->post('/student', function ($request, $response) {
   
   try{
		//This verifies whether a user submitted all the required form-fields
		$res1 = verifyRequiredParams(array('name', 'email', 'password', 'api_key'), json_decode($request->getBody(), true));
		if($res1["error"] == true){
			return $response->withJson($res1, 200);
		}
				
        //Get form data
        $name = $request->getParam('name');
        $email = $request->getParam('email');
        $password = $request->getParam('password');
		$api_key = $request->getParam('api_key');
		$date = date("Y/m/d");

        // validating email address
        $res1 = validateEmail($email);
		if($res1["error"] == true){
			return $response->withJson($res1, 200);
		}
		
		//Creat new Student object
		$student = new Student();
		$student->create($name, $email, $password, $date);
		$student->setApi_key($api_key);
		$res = array();
		//Instantiate database object and create new student (ie insert new student into DB
        $db = new DbHandler();
        $create = $db->createStudent($student);

        if ($create == USER_CREATED_SUCCESSFULLY) {
            $res["error"] = false;
            $res["message"] = "You are successfully registered";
        } else if ($create == USER_CREATE_FAILED) {
            $res["error"] = true;
            $res["message"] = "Oops! An error occurred while registereing";
        } else if ($create == USER_ALREADY_EXIST) {
            $res["error"] = true;
            $res["message"] = "Sorry, this email already exist";
        }
		else if($create == FAILED_TO_ENCRYPT){
			$res["error"] = true;
            $res["message"] = "Sorry, failed to encrypt password";
		}
	   
       return $response->withJson($res,201);
       
   }
   catch(\Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }
   
});



/*Recover's user's password
* User's passoword will be emailed to them
*/
$app->get('/student/forgot_pass/{email}', function ($request, $response, $args) {
	$email = $args['email'];
	
	// validating email address
    $res1 = validateEmail($email);
	if($res1["error"] == true){
		return $response->withJson($res1, 200);
	}
	
	//Instantiate database object and retrieve user's password
    $db = new DbHandler();
	$pass = $db->recoverPass($email,"students"); 
	
	$res = array();
	
	if($pass["Found"] == true){
		$message = "<b>Your password is " .$pass["password"] ."<b>";
		$subject = "You requested for password";		
		//send email
		$send = sendEmail($message, $email, $subject); 
		if($send["error"] == false)
		{
			$res["error"] = false;
			$res["message"] = "Password has been sent to " .$email;
			return $response->withJson($res, 200);
		}			
		else
			return $response->withJson($send, 200);	
	}		
	else{
		$res["error"] = true;
		$res["message"] = "User NOT Found!";
		return $response->withJson($res, 200);
	}
		
});

//Student Login
$app->post('/student/login', function ($request, $response) {
		//This verifies whether a user submitted all the required form-fields
        $res1 = verifyRequiredParams(array('email', 'password'), json_decode($request->getBody(), true));
		if($res1["error"] == true){
			return $response->withJson($res1, 200);
		}
		
		// validating email address
		$res1 = validateEmail($request->getParam('email'));
		if($res1["error"] == true){
			return $response->withJson($res1, 200);
		}

		$res = array();
         //Get form data
        $email = $request->getParam('email');
        $password = $request->getParam('password');
			

		//Instantiate database object and try to verify user
        $db = new DbHandler();
		$res = $db->studLogin($email, $password); 
        	
		//$response->getBody()->write(json_encode($res));
        return $response->withJson($res,200);
	
});


//Student Login by ID
$app->post('/student/login/id', function ($request, $response) {
		//This verifies whether a user submitted all the required form-fields
        $res1 = verifyRequiredParams(array('api_key'), json_decode($request->getBody(), true));
		if($res1["error"] == true){
			return $response->withJson($res1, 200);
		}
		
		$res = array();
         //Get form data
        $id = $request->getParam('api_key');

		//Instantiate database object and try to verify user
        $db = new DbHandler();
		$res = $db->loginByID($id); 
        	
		//$response->getBody()->write(json_encode($res));
        return $response->withJson($res,200);
	
})->add($authenticateStd);

//Student - search professor
$app->get('/professor/{name}', function ($request, $response, $args) {

		$res = array();
         //Get request parameter
        $name = $args['name'];

		//Instantiate database object and lookup professor
        $db = new DbHandler();
		$search = $db->searchProf($name); 
        	
		//$response->getBody()->write(json_encode($res));
        return $response->withJson($search,200);
	
})->add($authenticateStd);


//Student - Schedule appointment
$app->post('/student/appointment', function ($request, $response) {
	
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('profID', 'stdID', 'appointmentTime', 'appointmentDate', 'reason'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	
	 //Get form data
	$profID = $request->getParam('profID');
    $stdID = $request->getParam('stdID');
	$appointmentTime = $request->getParam('appointmentTime');
    $appointmentDate= $request->getParam('appointmentDate');
	$reason = $request->getParam('reason');
	
	//Initialize appointment
	$appointment = new Appointment();
	$appointment->create($profID, $stdID, $appointmentTime, $appointmentDate, $reason);
	
	//Instantiate database object and insert appointment
    $db = new DbHandler();
	$create = $db->scheduleAppointment($appointment);
	
	$res = array();
	if ($create == APPOINTMENT_SCHEDULED_SUCCESSFULLY) {
        $res["error"] = false;
        $res["message"] = "You have successfully schedule appointment";
		
		//Send notification to professor
		$recipient_apiKey = $db->retrieveMsgAPIKey($profID);
		$message = "A student has scheduled an appointment with you!";
		$msg_title = "New Appointment";
					
		//Send cancel notification to professor
		$notify = sendWebNotification($recipient_apiKey, $message, $msg_title);
		
		
    } else if ($create == APPOINTMENT_NOT_AVAILABLE) {
        $res["error"] = true;
        $res["message"] = "Appointment NOT available for specified date and time";
    } else if ($create == APPOINTMENT_SCHEDULED_FAILED) {
        $res["error"] = true;
        $res["message"] = "Failed to schedule appointment, please try again";
    }
	   
    return $response->withJson($res,200);
		
	
})->add($authenticateStd);



/*Student - Retrieve professor's schedule for specified date, showing open slots and booked slots
* @param String $profID Professor's ID in DB
* @param String $date Date of schedule
*/
$app->get('/professor/schedule/{profID}/{date}', function ($request, $response, $args) {
	
	$profID = $args['profID'];
	$date = $args['date'];
	
	//Instantiate database object and query professor's schedule
    $db = new DbHandler();
	$schedule  = $db->getSchedulePlusAvailablity($profID, $date);

	return $response->withJson($schedule,200);

})->add($authenticateStd);


/*Student - View all appointment currently scheduled by a student (ie upcoming appointments)
* @param String $stdID Student ID in DB
*/
$app->get('/student/appointment/{stdID}', function ($request, $response, $args) {
	$stdID = $args['stdID'];

	//Instantiate database object and query student's appointment schedule
    $db = new DbHandler();
	$appointment  = $db->getAllCurrentAppointment($stdID);
		
	return $response->withJson($appointment, 200);

})->add($authenticateStd);


/*Student - View appointment history of a specific student
* @param String $stdID Student ID in DB
*/
$app->get('/student/appointment/all/{stdID}', function ($request, $response, $args) {
	$stdID = $args['stdID'];

	//Instantiate database object and query student's appointment history
    $db = new DbHandler();
	$appointment  = $db->getAppointmentHistory($stdID);
		
	return $response->withJson($appointment, 200);

})->add($authenticateStd);


/*Student - Cancel an appointment
*/
$app->post('/student/appointment/cancel', function ($request, $response) {
	
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('reason', 'cancelledBy', 'appointment_id'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	
	$data = json_decode($request->getBody(), true);
	
	//Instantiate database object and cancel appointment specified in $date
    $db = new DbHandler();
	$cancel  = $db->cancelAppointment($data);
	
	$error = $cancel["error"];
	$res = array();
	
	/* If appointment was successfully cancelled, then send a notification to professor
	*/
	if(!$error){		
		$appointment = $cancel["appointment"];
		$std_name = $appointment["name"];
		$appoint_time = $appointment["time"];
		$appoint_date = $appointment["date"];
		$recipient_apiKey = $appointment["api_key"];
		$message = "Your appointment with " .$std_name ." on " .$appoint_date ." has been cancelled";
		$msg_title = "Appointment Cancelled";
					
		//Send cancel notification to professor
		$notify = sendWebNotification($recipient_apiKey, $message, $msg_title);
		
		$res["error"] = false;
		$res["message"] = "Your appointment was successfully cancelled!";	
	}
	else{
		$res["error"] = true;
		$res["message"] = "An error occured please try again later";	
	}
		
	return $response->withJson($res, 200);

})->add($authenticateStd);





/************************************Handles Professors operations**********************************************************************************/
//Create professor
$app->post('/professor', function ($request, $response) {
	try{
		//This verifies whether a user submitted all the required form-fields
		$res1 = verifyRequiredParams(array('name', 'email', 'password', 'department'), json_decode($request->getBody(), true));
		if($res1["error"] == true){
			return $response->withJson($res1, 400);
		}
				
        //Get form data
        $name = test_input($request->getParam('name'));
        $email = test_input($request->getParam('email'));
        $password = test_input($request->getParam('password'));
		$department = test_input($request->getParam('department'));
		$msg_key = $request->getParam('mesage_api_key');
		$date = date("Y/m/d");
			

        // validating email address
        $res1 = validateEmail($email);
		if($res1["error"] == true){
			return $response->withJson($res1, 400);
		}
		
		//create Professor object
		$prof = new Professor();
		$prof->setName($name);
		$prof->setEmail($email);
		$prof->setPassword($password);
		$prof->setDepartment($department);
		$prof->setDateCreated(date("Y/m/d"));

		$res = array();
		//Instantiate database object and create new professor (ie insert new student into DB
        $db = new DbHandler();
        $create = $db->createProfessor($prof, $msg_key);

        if ($create == USER_CREATED_SUCCESSFULLY) {
            $res["error"] = false;
            $res["message"] = "You are successfully registered";
        } else if ($create == USER_CREATE_FAILED) {
            $res["error"] = true;
            $res["message"] = "Oops! An error occurred while registereing";
        } else if ($create == USER_ALREADY_EXIST) {
            $res["error"] = true;
            $res["message"] = "Sorry, this email already exist";
        }
		else if($create == FAILED_TO_ENCRYPT){
			$res["error"] = true;
            $res["message"] = "Sorry, failed to encrypt password";
		}
	   
       return $response->withJson($res,201);
       
   }
   catch(\Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }

});


//Update professor account
$app->post('/professor/update', function ($request, $response) {
	try{
		//This verifies whether a user submitted all the required form-fields
		$res1 = verifyRequiredParams(array('name', 'department', 'id'), json_decode($request->getBody(), true));
		if($res1["error"] == true){
			return $response->withJson($res1, 400);
		}
				
        //Get form data
        $name = test_input($request->getParam('name'));
		$department = test_input($request->getParam('department'));
		$id = $request->getParam('id');
		
		//create Professor object
		$prof = new Professor();
		$prof->setName($name);
		$prof->setDepartment($department);
		$prof->setID($id);

		$res = array();
		//Instantiate database object and create new professor (ie insert new student into DB
        $db = new DbHandler();
        $create = $db->updateProfessor($prof);

        if ($create == true) {
            $res["error"] = false;
            $res["message"] = "Profile was successfully updated";
		}
		else{
			$res["error"] = true;
            $res["message"] = "Sorry, profile could not be update, please try again";
		}
	   
       return $response->withJson($res,201);
       
   }
   catch(\Exception $ex){
       return $response->withJson(array('error' => $ex->getMessage()),422);
   }

})->add($authenticateProf);

/*Recover's user's password
* User's passoword will be emailed to them
*/
$app->get('/professor/forgot_pass/{email}', function ($request, $response, $args) {
	$email = $args['email'];
	
	//Instantiate database object and retrieve user's password
    $db = new DbHandler();
	$pass = $db->recoverPass($email,"professors"); 
	
	$res = "Password has been sent to " .$email;
	$error = "User NOT Found!";
	
	if($pass["Found"] == true){
		$message = "<b>Your password is " .$pass["password"] ."<b>";
		$subject = "You requested for password";		
		//send email
		$send = sendEmail($message, $email, $subject); 
		if($send["error"] == false)
			return $response->withJson($res, 200);
		else
			return $response->withJson($send["message"], 200);	
		//return $response->withJson($send, 200);
	}		
	else
		return $response->withJson($error, 200);
});


//Professor - Login
$app->post('/professor/login', function ($request, $response) {
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('email', 'password'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}

	$res = array();
     //Get form data
    $email = $request->getParam('email');
    $password = $request->getParam('password');

	//Instantiate database object and try to verify user
    $db = new DbHandler();
	$res = $db->profLogin($email, $password);         

    return $response->withJson($res,200);

});


/*Professor - Restore user session 
* That is if a user cookie is still validate
*/
$app->post('/professor/login/restore', function ($request, $response) {
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('user'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}

	$res = array();
     //Get form data
    $api_key = $request->getParam('user');

	//Instantiate database object and try to verify user
    $db = new DbHandler();
	$res = $db->profRestore($api_key);         

    return $response->withJson($res,200);

});


//Professor - Logout
$app->get('/professor/logout/', function ($request, $response) {


	//Instantiate database object and try to verify user
    $db = new DbHandler();
	$res = $db->logOut(); 
	$res = array();
	$res["error"] = false;

    return $response->withJson($res,200);

});


//Professor - Create schedule
$app->post('/professor/schedule', function ($request, $response) {
	/*
	//This verifies whether a user submitted all the required form-fields
	$res1 = verifyRequiredParams(array('id', 'start', 'end', 'duration', 'office_loc', 'schedule'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	*/

	$res = array();
     //Get form data
    $data = json_decode($request->getBody(), true);

	//Instantiate database object and try to verify user
    $db = new DbHandler();
	$schedul = $db->createProfSchedule($data);  

	if($schedul == true){
		$res["error"] = false;
		$res["message"] = "Schedule created successfully";		
	}
	else{		
		$res["error"] = true;
		$res["message"] = "Schedule was NOT updated";	
    }		

    return $response->withJson($res,200);
	

})->add($authenticateProf);



//Professor - Update message token whenever firebase token changes
$app->post('/professor/token/update', function ($request, $response) {
	/*
	//This verifies whether a user submitted all the required form-fields
	$res1 = verifyRequiredParams(array('id', 'start', 'end', 'duration', 'office_loc', 'schedule'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	*/

	$res = array();
     //Get form data
    $data = json_decode($request->getBody(), true);

	//Instantiate database object and try to verify user
    $db = new DbHandler();
	$schedul = $db->updateMsgAPIKey($data);  

	if($schedul == true){
		$res["error"] = false;
		$res["message"] = "Token successfully updated";		
	}
	else{		
		$res["error"] = true;
		$res["message"] = "Token was NOT updated";	
    }		

    return $response->withJson($res,200);

});


//Professor - Cancel a specific appointment
$app->post('/professor/appointment/cancel', function ($request, $response) {
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('reason', 'cancelledBy', 'appointment_id'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	
	$data = json_decode($request->getBody(), true);
	
	//Instantiate database object and cancel appointment specified in $date
    $db = new DbHandler();
	$cancel  = $db->cancelAppointment($data);
	$error = $cancel["error"];
	$res = array();
	
	
	
	/* If appointment was successfully cancelled, then send a notification to student
	*/
	if(!$error){		
		$appointment = $cancel["appointment"];
		$prof_name = $appointment["name"];
		$appoint_time = $appointment["time"];
		$appoint_date = $appointment["date"];
		$recipient_apiKey = $appointment["api_key"];
		$message = "Your appointment with " .$prof_name ." on " .$appoint_date ." has been cancelled";
		$msg_title = "Appointment Cancelled";
					
		//Send cancel notification to student
		$notify = sendNotification($recipient_apiKey, $message, $msg_title);
		
		$res["error"] = false;
		//$res["message"] = "Sent";	
		$res["message"] = $notify;	
	}
	else{
		$res["error"] = true;
		$res["message"] = "An error occured please try again later";	
	}
		
	return $response->withJson($res, 200);

})->add($authenticateProf);


/*Professor - Cancel all appointment on a specific date
* (NOTE: date should be in this format "YYYY-MM-DD")
*/
$app->post('/professor/appointment/cancel/{date}', function ($request, $response, $args) {
	//This verifies whether a user submitted all the required form-fields
    $res1 = verifyRequiredParams(array('reason', 'cancelledBy', 'profID'), json_decode($request->getBody(), true));
	if($res1["error"] == true){
		return $response->withJson($res1, 400);
	}
	
	$data = array();
	$date = explode("-", $args['date']);
	$date = implode("/", $date);
	$data["date"] = $date;
	$data["profID"] = $request->getParam('profID');
	$data["reason"] = $request->getParam('reason');
	$data["cancelledBy"] = $request->getParam('cancelledBy');
	
	//Instantiate database object and cancel appointment specified in $date
    $db = new DbHandler();
	$cancel  = $db->cancelAppointmentByDate($data);
	
	$res = array();
	if($cancel == true){
		$res["error"] = false;
		$res["message"] = "Appointment was successfully cancelled";		
	}
	else{
		$res["error"] = true;
		$res["message"] = "An error occured please try again later";	
	}

	return $response->withJson($res, 200);
	
})->add($authenticateProf);



/*Professor - Retrieve all booked appointments
*/
$app->get('/professor/appointment/booked/{profID}', function ($request, $response, $args) {
	
	$profID = $args['profID'];
	
	//Instantiate database object and retieve all currently booked appointment
    $db = new DbHandler();
	$result  = $db->getAllProfAppointment($profID);
	//echo $result;
	return $response->withJson($result, 200);


})->add($authenticateProf);






/**
 * Check whether required parameters are posted
 */
function verifyRequiredParams($required_fields, $request_body) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $request_body;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        parse_str($request_body, $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        return $response;
    }
	else{
		
		$response = array();
        $response["error"] = false;
		return $response;
	}
}
 
/**
 * Validating email address
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        return $response;
    }
	else{
		
		$response = array();
        $response["error"] = false;
		return $response;
	}
}

function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
 
$app->run();

?>