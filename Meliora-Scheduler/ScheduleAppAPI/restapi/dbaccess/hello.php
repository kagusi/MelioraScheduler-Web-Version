<?php

date_default_timezone_set("America/New_York");


require 'DbHandler.php';

 //echo md5(uniqid("welcome1", true)).'<br>';
// echo md5("welcome1").'<br>';
 //echo md5("welcome1").'<br>';
 
 //$se = array();
 //$se["we"] = "Hello";
 
 //echo date("Y-m-d-l h:i:sa").'<br>';
 //echo date('w', strtotime("11/2/2017")).'<br>';
 
 //if(!array_key_exists("we", $se))
	 //echo "Sup";
 
 $db = new DbHandler();
 /** 
 $prof = new Professor();
 $prof->setName("James Martin");
 $prof->setEmail("smith4@yahoo.com");
 $prof->setPassword("welcome1");
 $prof->setDateCreated(date("Y/m/d"));
 
  $create = $db->createProfessor($prof);
 echo $create.'<br>';

 $std = new Student();
 $std->setName("Kennedy Jones");
 $std->setEmail("money1@yahoo.com");
 $std->setPassword("welcome1");
 $d = date('Y/m/d');
// echo $d.'<br>';
 $std->setDateCreated($d);
 
 $appoin = new Appointment();
 $appoin->create(5, 2, "12:15 PM", "2017/11/02", "To see them");
 $schApp = $db->scheduleAppointment($appoin);
 echo $schApp. "<br>";
 //$appoin->setProf_id(5);
 //$appoin->setStudent_id(2);
 //$appoin->setAppointment_time("12:15 PM");
 //$appoin->setAppointment_date("2017/11/02");
 //$aapoint->setReason_for_appointment("To see them");
 
 //$stdd = $std->expose();
 //echo json_encode($stdd).'<br>';
 //echo $std->dateCreated.'<br>';
 //$stdf = $prof->expose();
 //echo json_encode($stdf).'<br>';
 //echo $prof->dateCreated.'<br>';
 
 //$create = $db->createStudent($std);
 //echo $create.'<br>';
 //$create = $db->createProfessor($prof);
 //echo $create.'<br>';
 
//jsonTest();
$sch = $db->getSchedulePlusAvailablity(5, "2017/11/02");
//$sch = $db->getFullSchedule(5);
//echo date('w', strtotime("2017/11/02")) . "<br>";
//echo date('w', strtotime("11/02/2017")) . "<br>";

echo json_encode($sch) . "<br>" . "<br>";
 
function jsonTest(){
		$response = array();
		//$response["1"] = array();
		//$response["2"] = array();
		//$response["5"] = array();
		
		$temp = ["10:10 AM","12:30 PM"];
		$response["1"] =  $temp;
		$temp1 = ["9:15 AM","9:45 AM","11:15 AM"];
		$response["2"] = $temp1;
		$temp2 = ["8:15 AM","10:45 AM","12:15 PM"];
		$response["5"] = $temp2;
		
		$rsp = json_encode($response);
		
		echo $rsp;
}

$pp = $db->getAllCurrentAppointment(2);
//$sd = $pp["Appointments"];
//$qw = stripslashes(json_encode($sd[0]));

 //echo stripslashes(json_encode($sd[0])). "<br>";
 echo stripslashes(json_encode($pp)) . "<br>" . "<br>";
 
 $login = $db->studLogin("money1@yahoo.com", "welcome1");
 //$error1 = $login["message"];
 $rt = $login["message"];
 $er = $rt[0];
 echo stripslashes(json_encode($login)) . "<br>". "<br>";
 **/
 
 $sh = array();
 $time = array();
 $sh["start"] = "2017/08/01";
 $sh["end"] = "2017/12/15";
 $sh["interval"] = 20;
 $sh["officeLoc"] = "125 B CSC Building";
 $sh["id"] = 1;
 $time["1"] = array("7:15 AM - 10:45 AM");
 $time["2"] = array("10:10 AM - 1:25 PM", "1:30 PM - 2:25 PM");
 $time["4"] = array("9:15 AM - 11:25 AM", "2:45 PM - 4:00 PM");
 $time["5"] = array("8:15 AM - 10:45 AM");
 $time["7"] = array("8:15 AM - 10:45 AM", "2:45 PM - 4:00 PM");
 $sh["schedule"] =   $time;



//$fg = json_encode($sh);
 //$cr =$db->createProfSchedule($sh);
//echo $cr;

 $sch = $db->getSchedulePlusAvailablity(1, "2017/11/02");
 echo json_encode($sch) . "<br>" . "<br>";
 
 $cancell = array();
 $cancell["id"] = 5;
 $cancell["reason"] = "I am sick";
 $cancell["cancelledBy"] = "Professor";
 //$can = $db->cancelAppointment($cancell);
 //echo $can;
 
 
 $cc = array();
 $cc["reason"] = "I am out from town";
 $cc["cancelledBy"] = "Professor";
 $cc["profID"] = 1;
 $cc["date"] = "2017/11/02";
 //$bydate = $db->cancelAppointmentByDate($cc);

 $appoin = new Appointment();
 $appoin->create(1, 2, "09:15 AM", "2017/11/02", "To see them");
 //$schApp = $db->scheduleAppointment($appoin);
 //echo $schApp. "<br>";
 
 $appoin = new Appointment();
 $appoin->create(1, 2, "08:15 AM", "2017/11/05", "To see them");
 //$schApp = $db->scheduleAppointment($appoin);
 //echo $schApp. "<br>";
 
 $appoin = new Appointment();
 $appoin->create(1, 2, "07:15 AM", "2017/11/06", "To see them");
 //$schApp = $db->scheduleAppointment($appoin);
 //echo $schApp. "<br>";
 
 //$curr = $db->getAllCurrentAppointment(2);
 //echo json_encode($curr). "<br>";
 
 //$curr = $db->getAllProfAppointment(1);
 //echo json_encode($curr). "<br>";
 
 
?>