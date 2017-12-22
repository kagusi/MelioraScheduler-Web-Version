<!DOCTYPE html>
<html lang="en">
<?php
function __autoload($className) {
    $file = '../Models'."/".$className.'.php';
    if(file_exists($file)) {
        require_once $file;
    }
}

if(!isset($_SESSION))session_start(); #start session.
if (isset($_SESSION['userDetail']) && !empty($_SESSION['userDetail']) ) {
	$userdetail = $_SESSION['userDetail'];
	$name = explode(" ", $userdetail["name"]);
} else {
   header ('Location: login.html');
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="../JavaScript/semantic/dist/semantic.min.css">
	<script
	  src="https://code.jquery.com/jquery-3.1.1.min.js"
	  integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
	  crossorigin="anonymous"></script>
	<script src="../JavaScript/semantic/dist/semantic.min.js"></script> 
	
	<script type="text/javascript" src="../JavaScript/form_validator.js"></script>
	<script type="text/javascript" src="../JavaScript/node_modules/moment/moment.js"></script>
	
	<link rel="stylesheet" type="text/css" href="../CSS/snack.css">
	
		 
<style> 
* {box-sizing: border-box}
body {
    font-family: "Lato", sans-serif;
}
.card {
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
    width: 40%;
    border-radius: 5px;
}

.card:hover {
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

img {
    border-radius: 5px 5px 0 0;
	
}

.container, #canceAPPErr {
    padding: 2px 16px;
}
#fg {
	float:left;
	width:100%;
}
.li{
	color: white;
	background: #555555;
}
#menu_li{	
	
}
input[type=text], select, textarea{
    width: 50%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
		
}
textarea{
    width: 70%;
	height: 100px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    resize: vertical|horizontal;	
}
input[type=submit], #addmore {
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    //float: right;
}

#msg_badge, #msg_li{	
	cursor: pointer;
}

#view_cn, .view_cn{
    background-color: red;
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    //float: right;
}

input[type=submit]:hover {
    background-color: #45a049;
}
.profile{
	//display: none;
}
.appointments{
	display: none;
}
.schedule{
	display: none;
}
.pet_signup{
	display: none;
}
#td1{
	width: 50%;
}
table{
	width: 100%;
}
#schedule_table, #appointment_table{
	font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
	
}
#pet_td, #pet_th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

#schedule_tr:nth-child(even) {
    background-color: #dddddd;
}	
#schedule_td{
	padding: 8px;
	
}
#pet_th{
	background-color:   #154360;
	height: 40px;
	padding-left: 20px;
	color: white;
	font-weight: bold;
}
.error {color: #FF0000;}

span#sub_button{
	padding-right:10px;
}
span.semester_date{
	padding:30px;
	font-weight: bold;
}
#office{
	padding-top: 30px;
}

.up_app, #hd{
	width: 400px;
	display: block;
	margin-left: auto;
	margin-right: auto;
}

textarea{
    width: 100%;
	height: 100px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    resize: vertical|horizontal;	
}
</style>


<script>
checkCookie();
	
function checkCookie() {
	var user = getCookie("User");
	if (user == "") {
		window.location.replace("login.html");
	} 
}




 $(document).ready(function(){	
		$("#footer").load("../headers/footer.html"); 
		$("#header").load("../headers/login_header.html");
		
		
		//Display professors office hrs/schedule
		
		var table = document.getElementById("schedule_table");
		var hasSchedule = "<?php echo $userdetail["office_locatn"]; ?>";
		var ar = [];		
		/* check whether user has provided his/her officehrs
		* If already provided then display it
		*/
		if(hasSchedule != ""){
			$("#semester_start").val("<?php echo $userdetail["semester_start"]; ?>");
			$("#semester_end").val("<?php echo $userdetail["semester_end"]; ?>");
			$("#meeting_interval").val("<?php echo $userdetail["meeting_interval"]; ?>");
			$("#office_loc").val("<?php echo $userdetail["office_locatn"]; ?>");	
			ar.push(<?php echo $userdetail["office_hrs"]; ?>);		
			loadSchedule(ar, table);			
		}
		else{
			//Display empty row
			addRow();
		}
		
		
		//Display appointments (both cancelled and non-cancelled
		retrieveAppointments("<?php echo $userdetail["id"]; ?>" , "<?php echo $userdetail["api_key"]; ?>");
		
    });
	

function processSchedule(){
	$('form').on('submit', function (e) {
		
		e.preventDefault();
		//check whether all form data is filled
		var valid = validateSchedule();
		if(valid == true){	
			var id = <?php echo $userdetail["id"]; ?>;
			var autorization = "<?php echo $userdetail["api_key"]; ?>";
			
			submitSchedule(autorization, id);
		}	
				
	});	
}
  function submitProfile() {
	$('form').on('submit', function (e) {
		e.preventDefault();  
		var form = {}; 
		$.each($(this).serializeArray(), function (i, field) { form[field.name] = field.value || ""; });
		form["id"] = <?php echo $userdetail["id"]; ?>;
		//
		var form_data = JSON.stringify(form);

		//Sending ajax request to REST Api
		$.ajax({
			type: 'POST',
			contentType: 'application/json; charset=utf-8',
			url: 'https://localhost/IndependentProject/ScheduleAppAPI/restapi/professor/update',
			dataType: "json",
			data: form_data,
			beforeSend: function (xhr) {
				/* Authorization header */
				xhr.setRequestHeader("Authorization", "<?php echo $userdetail["api_key"]; ?>");
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error == true)
					$("#submitProfileErr").html(res.message);
					//alert("Error " +res.message);
				else{
					$("#submitProfileErr").html(" ");
					accountSnack("Profile was successfully updated!!", "account_snackbar"); 
				}
					
				},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});
	
	});	
}
 
 	//Display menu contents
	function displayContent(m, n, l){
		var x = document.getElementById(m.id);
		var y = document.getElementById(n.id);
		var z = document.getElementById(l.id);
		//var p = document.getElementById(p.id);
		//alert(x.class);		
		x.style.display = "block";
		y.style.display = "none";
		z.style.display = "none";
		//p.style.display = "none";
					
	}
	
	//Add new table row
	function addRow(){
		var table = document.getElementById("schedule_table");
		var row = table.insertRow(-1);
		row.id = "schedule_tr";
		var cell1 = row.insertCell(0);		
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		var cell6 = row.insertCell(5);
		cell1.id = "schedule_td";
		cell2.id = "schedule_td";
		cell3.id = "schedule_td";
		cell4.id = "schedule_td";
		cell5.id = "schedule_td";
		cell6.id = "schedule_td";
		cell1.innerHTML = "Start Time:"+"<br>"+"End Time:";
		cell2.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		cell3.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		cell4.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		cell5.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		cell6.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
	}
 </script>

<title>Appointment Wizard</title>
</head>
<body >
<div id="header"></div>

<div class="ui internally celled grid">
  <div class="row">
    <div class="three wide column">
      <div class="ui card">

	  
	  <div class="content">
		<span class="header" ><?php echo "Welcome " .$name[0] ."!"; ?></span>
		<div class="meta">
		  <span></span>
		</div>
	  </div>
	</div>
	
	<nav class="navbar" id="menu_li">
		<ul class="nav" id="menu_li">
		  <li><a href="#" class="li" onclick="displayContent(profile, schedule, appointments)"><b >Profile</b></a></li><br>
		  <li><a href="#" class="li" onclick="displayContent(schedule, profile, appointments)"><b >Office Hours</b></a></li><br>
		  <li><a href="#" class="li"  onclick="displayContent(appointments, profile, schedule)" id="appointm"><b >Appointments</b></a></li>
		</ul>
	</nav>
    </div>
	
	<div class="thirteen wide column" >

<!--------------------------------------------- Displays profile info ------------------------------------------------------------------------------------>	
	  <div class="profile" id="profile">
	  <form name="upload" enctype=multipart/form-data >
	  <input type="hidden" name="form_name" value="upload_profil_pic"/>
		  <table>
			  <tr>
				  <td id="td1">
					<div class="fname">
						<a class="ui header"><b>Full Name</b></a><br>			
						<input type="text" placeholder="First Name" name="name" value="<?php echo $userdetail["name"]; ?>"  required><br><br>
					</div>
					<div class="email">
						<a class="ui header"><b>Email</b></a><br>
						<input type="text" placeholder="Email" name="email" value="<?php echo $userdetail["email"]; ?>" disabled><br><br>
					</div>
					<div class="department">
						<a class="ui header"><b>Department</b></a><br>
						<input type="text" placeholder="department" name="department" id="department" value="<?php echo $userdetail["department"]; ?>" required><br><br>
						<p><span class="error" id="submitProfileErr"></span></p>
					</div>
				
					
					
				  </td>	  
				  <td id="td1">
					<div class="about">
						<a class="ui header"><b></b></a><br>
						</div>
				  </td>	
			  </tr>	
			  <tr>
				<td id="td2">
					<div >
						<input type="submit" value="Save Profile" onclick="submitProfile()">				
					</div>
				</td>
			  </tr>
		  </table>	
		  </form>
     </div>	
<!--------------------------------------------- End of profile info ------------------------------------------------------------------------------------>	






<!--------------------------------------------- Displays Schedule  ------------------------------------------------------------------------------------>	
	<div class="schedule" id="schedule"> 
		<form name="schedule_form">
		<div>
		<span class="semester_date">Semester Start:<input type="date" name="start" id="semester_start"></span>
		<span class="semester_date">Semester End:<input type="date" name="end" id="semester_end"></span>	
		<span class="semester_date">Appointment Duration:<input type="number"  name="interval" id="meeting_interval" min="1">Mins</span><br><br>
		<span class="semester_date" id="office">Office Location:<input type="text" name="officeLoc" id="office_loc"></span>
		</div>
		<br>
		<table id="schedule_table">
			<tr id="pet_tr">
				<th id="pet_th"></th>
				<th id="pet_th">Mon</th>
				<th id="pet_th">Tue</th>
				<th id="pet_th">Wed</th>
				<th id="pet_th">Thur</th>
				<th id="pet_th">Fri</th>
			</tr>	
			<!----
			<tr id="schedule_tr">
				<td id="schedule_td"><input type="text" name="schedule_time"></td>
				<td id="schedule_td"><input type="text" name="schedule_time"></td>
				<td id="schedule_td"><input type="text" name="schedule_time"></td>
				<td id="schedule_td"><input type="text" name="schedule_time"></td>
				<td id="schedule_td"><input type="text" name="schedule_time"></td>
			</tr>
			---->

		</table>	
		<div>
			<span id="sub_button" class="sch_bt"><input type="submit" value="Submit" onclick="processSchedule()"></span><span></span>
			<span class="sch_bt" onclick="addRow()" id="addmore">Add Row</span>
			<p><span class="error" id="submitScheduleErr"></span></p>				
		</div>
		</form>
	</div>
<!--------------------------------------------- End of Schedule  ------------------------------------------------------------------------------------>	







<!--------------------------------------------- Displays Appointments ------------------------------------------------------------------------------------>	
	<div class="appointments" id="appointments">
		<div>
		<span class="up_app"><h3>Upcoming Appointments</h3></span>
		</div>
		<br>

		<table id="appointment_table">
			<tr id="pet_tr">
				<th id="pet_th">Student Name</th>
				<th id="pet_th">Date</th>
				<th id="pet_th">Time</th>
				<th id="pet_th">Reason</th>
				<th id="pet_th"></th>
			</tr>	
			<!----
			<tr id="schedule_tr">
				<td id="schedule_td"></td>
				<td id="schedule_td"></td>
				<td id="schedule_td"></td>
				<td id="schedule_td"><span class="view_cancelled" onclick="cancelAppointment()" id="view_cn">Cancel</span></td>
			</tr>
			---->

		</table>	
     </div>	

<!--------------------------------------------- End of Appointments ------------------------------------------------------------------------------------>	


	 
  </div>
    
</div>

</div>



<div id="account_snackbar" ></div>

<div  id="footer"></div>


<!-- The following Model Box displays when "cancel" appointment button is clicked-->

<div class="ui mini modal" id="view_cancelled">
  <i class="close icon"></i>
  <div class="header" id="hd">
    Reason for Cancellation
  </div>
  <div class="content">
    <div class="description">
      <div class="ui header"></div>
      <textarea id="mod_reason" name="about_me" placeholder="Write something.." style="height:200px" ></textarea>					
    </div>
  </div>
  <div class="actions" id="not_cancelled">
    <div class="ui black deny button">
      No
    </div>
    <div class="ui positive right labeled icon button">
      Yes, cancel
      <i class="checkmark icon"></i>
    </div>
  </div>
  
  <div class="actions" id="cancelled">
    <div class="ui black deny button">
      OK
    </div>
  </div>
  
  <p><span class="error" id="canceAPPErr"></span></p><br>
</div>






</body>
</html>