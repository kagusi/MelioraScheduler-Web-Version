
//Regex to validate email
//Note: email will also be revalidated on server side
var emailValid = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; 
var API_URL = "https://localhost/IndependentProject";
//Retrieve user cookier	
function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
	

//set user cookier
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


//Validate singup form field data	
function signUpValidate(){
	
	 var form_name = document.signup;	 
     var email = form_name.email.value;
	 var pass = form_name.password.value; 
	 var passC = form_name.passwordC.value; 
	 var name = form_name.name.value;	  
	 var department = form_name.dept.value;	  
	 var phoneValid = /^[1-9]{1}[0-9]{9}$/;
	 var valid = true;
	   
	 	 	 
	 if (name == "" || name == " ") {		 
		 $("#nameErr").html("* First name is required");
		 valid = false;
	 }else{
		 $("#nameErr").html("*");		 		 
	 }
	 
	 
	 if (emailValid.test(email) == false) {		 
		 $("#emailErr").html("* Email is not valid");
		 valid = false;
	 }else{
		 $("#emailErr").html("*");		 		 
	 }
	 	 
	 if (pass == "" || pass == " ") {		 
		 $("#passErr").html("* Password is required");
		 valid = false;
	 }else{
		 $("#passErr").html("*");		 		 
	 }
	 
	 if (passC == "" || passC == " ") {		 
		 $("#pass_Err").html("* Password is required");
		 valid = false;
	 }else{
		 $("#pass_Err").html("*");		 		 
	 }
	 
	 if (pass != passC) {		 
		 $("#pass_Err").html("* Password does NOT match");
		 valid = false;
	 }else{
		 $("#pass_Err").html("*");		 		 
	 }
	 
	 if (department == "" || department == " ") {		 
		 $("#deptErr").html("* Department is required");
		 valid = false;
	 }else{
		 $("#deptErr").html("*");		 		 
	 }
	 
	 if (valid == false){
		 return false;
	 }
	 
	 return true;
	
}

//Signup user by Sending form data to REST API 
function signUp(forminfo){
	
	//validate form data
	var validate = signUpValidate();
	var urlinfo = API_URL +"/ScheduleAppAPI/restapi/professor";
	
	
		  
	if(validate == true){
		var form = {}; 
		$.each($(forminfo).serializeArray(), function (i, field) { form[field.name] = field.value || ""; });
			
		messaging.getToken() //Retrieve firebase message token used in sending notifications to user
		.then(function(currentToken) {
		  if (currentToken) {
				//console.log(currentToken);
				form["mesage_api_key"] = currentToken;
				var form_data = JSON.stringify(form);
				
				//Sending ajax request to REST Api
				$.ajax({
					type: 'POST',
					contentType: 'application/json; charset=utf-8',
					url: urlinfo,
					dataType: "json",
					data: form_data,
					beforeSend: function (xhr) {
						// Authorization header 
						//xhr.setRequestHeader("Authorization", "d2d623908c1456ddf076ba68d7d4d611");
						//xhr.setRequestHeader("X-Mobile", "false");
					},
					success: function(res, textStatus, jqXHR){
						var error = res.error;
						if(error == true){
							$("#signupErr").html(res.message);
						}
						else{
							//Save user msg api key in local storage
							if (typeof(Storage) !== "undefined") {
								localStorage.setItem("msg_key", currentToken);
							}
							window.location.replace("login.html?success=true");
						}
							

						},
					error: function(jqXHR, textStatus, errorThrown){
						$("#signupErr").html(errorThrown);
					}
				});
			
		  } 
		})
		.catch(function(err) {
		  alert("error");
		});
	
	
	}
	
}
//Validates signin form fields
function signInValidate(){
	var form_name = document.login;	 
    var email = form_name.email.value;
	var pass = form_name.password.value;
	var valid = true;
	
	if (emailValid.test(email) == false) {		 
		 $("#emailErr").html("* Email is not valid");
		 valid = false;
	 }else{
		 $("#emailErr").html("*");		 		 
	 }
	 	 
	 if (pass == "" || pass == " ") {		 
		 $("#passErr").html("* Password is required");
		 valid = false;
	 }else{
		 $("#passErr").html("*");		 		 
	 }
	 
	  if (valid == false){
		 return false;
	 }
	 
	 return true;
}

//Sends user data to server for signin verification
function signIn(forminfo){
	//validate form data
	var validate = signInValidate();
	var url = API_URL +"/ScheduleAppAPI/restapi/professor/login";
		  
	if(validate == true){
		var form = {}; 
		$.each($(forminfo).serializeArray(), function (i, field) { form[field.name] = field.value || ""; });
		var form_data = JSON.stringify(form);

		//Sending ajax request to REST Api
		$.ajax({
			type: 'POST',
			contentType: 'application/json; charset=utf-8',
			url: url,
			dataType: "json",
			data: form_data,
			beforeSend: function (xhr) {
				/* Authorization header */
				//xhr.setRequestHeader("Authorization", "d2d623908c1456ddf076ba68d7d4d611");
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error == true)
					$("#signinErr").html(res.message);
					//alert("Error " +res.message);
				else
					window.location.replace("account.php");
				},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});
	
	}		
}

//Restores a user session if he/she has a valid cookie
function restoreSession(api_key){
	var info = {};
	info["user"] = api_key;
	var form_data = JSON.stringify(info);
	var url = API_URL +"/ScheduleAppAPI/restapi/professor/login/restore";
	
	//Sending ajax request to REST Api
		$.ajax({
			type: 'POST',
			contentType: 'application/json; charset=utf-8',
			url: url,
			dataType: "json",
			data: form_data,
			beforeSend: function (xhr) {
				/* Authorization header */
				//xhr.setRequestHeader("Authorization", "d2d623908c1456ddf076ba68d7d4d611");
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error == true)
					$("#signinErr").html(res.message);
					//alert("Error " +res.message);
				else
					window.location.replace("account.php");
				},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});
	
}

//Retrieve and send user's password to his/her email address
function forgotPass(forminfo){
	var form_name = document.forgot_pass;	 
    var email = form_name.email.value;
	
	if (emailValid.test(email) == false) {		 
		$("#emailErr").html("* Email is not valid");
		return false;
	}else{
		 $("#emailErr").html("*");	

		 var urlInfo = API_URL +"/ScheduleAppAPI/restapi/professor/forgot_pass/"+email;
		 
		//Sending ajax request to REST Api
		$.ajax({
			type: 'GET',
			contentType: 'application/json; charset=utf-8',
			url: urlInfo,
			dataType: "json",
			beforeSend: function (xhr) {
				/* Authorization header */
				//xhr.setRequestHeader("Authorization", "d2d623908c1456ddf076ba68d7d4d611");
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error == true)
					$("#signinErr").html(res.message);
					//alert("Error " +res.message);
				else
					forgotPassSnack();
					//alert("Welcome " +res.message);
					//$("#signinErr").html("Welcome " +res.name);
				},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});
	}
	
}

//Logout a user and clear session and cookier
function logOut(){	
	var url = API_URL +"/ScheduleAppAPI/restapi/professor/logout/";
	$.ajax({
			type: 'GET',
			contentType: 'application/json; charset=utf-8',
			dataType: "json",
			url: url,
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error != true)
					window.location.replace("login.html");

			},

		});	
}

//Send professor's schedule to RESTFul Service API (for DB storage)
function submitSchedule(autorization, id){

	var form_name = document.schedule_form;
	var form = {};
	form["id"] = id;
	var start = form_name.start.value;
	form["start"] = form_name.start.value;
	form["end"] = form_name.end.value;
	form["interval"] = form_name.interval.value;
	form["officeLoc"] = form_name.officeLoc.value;
	var check1 = false;
	var check2 = false;
	
	var schedule = {};
	//schedule["2"] = [];
	//schedule["3"] = [];
	//schedule["4"] = [];
	//schedule["5"] = [];
	//schedule["6"] = [];
			
	var table = document.getElementById("schedule_table");
	//Retrieve all table rows
	var rows = table.rows;
	var row_length = rows.length;
	
	if(row_length >1){
		$("#submitScheduleErr").html("");
		//Iterate over table rows to retrieve table data (ie schedule times)
		
		for(var i = 1; i<row_length; i++){
			//Retrieve all cells in row[i]
			var cells = rows[i].cells;
			var cell = cells[1];
			var children = cell.children;
			var start_time = children[0].value;
			var end_time = children[2].value;
			if(start_time != "" && end_time != ""){
				check1 = true;
				//Convert 24hr to 12hr time
				var start_time = moment(start_time, ["HH:mm"]).format("h:mm A");
				var end_time = moment(end_time, ["HH:mm"]).format("h:mm A");
				var time = start_time +" - " +end_time;				
				if(schedule.hasOwnProperty("2"))
					schedule["2"].push(time);
				else{
					schedule["2"] = [];
					schedule["2"].push(time);
				}
				
			}
				
			var cell = cells[2];
			var children = cell.children;
			var start_time = children[0].value;
			var end_time = children[2].value;
			if(start_time != "" && end_time != ""){
				check1 = true;
				//Convert 24hr to 12hr time
				var start_time = moment(start_time, ["HH:mm"]).format("h:mm A");
				var end_time = moment(end_time, ["HH:mm"]).format("h:mm A");
				var time = start_time +" - " +end_time;
				if(schedule.hasOwnProperty("3"))
					schedule["3"].push(time);
				else{
					schedule["3"] = [];
					schedule["3"].push(time);
				}
					
			}
				
			var cell = cells[3];
			var children = cell.children;
			var start_time = children[0].value;
			var end_time = children[2].value;
			if(start_time != "" && end_time != ""){
				check1 = true;
				//Convert 24hr to 12hr time
				var start_time = moment(start_time, ["HH:mm"]).format("h:mm A");
				var end_time = moment(end_time, ["HH:mm"]).format("h:mm A");
				var time = start_time +" - " +end_time;
				if(schedule.hasOwnProperty("4"))
					schedule["4"].push(time);
				else{
					schedule["4"] = [];
					schedule["4"].push(time);
				}
					
			}
				
			var cell = cells[4];
			var children = cell.children;
			var start_time = children[0].value;
			var end_time = children[2].value;
			if(start_time != "" && end_time != ""){
				check1 = true;
				//Convert 24hr to 12hr time
				var start_time = moment(start_time, ["HH:mm"]).format("h:mm A");
				var end_time = moment(end_time, ["HH:mm"]).format("h:mm A");
				var time = start_time +" - " +end_time;
				if(schedule.hasOwnProperty("5"))
					schedule["5"].push(time);
				else{
					schedule["5"] = [];
					schedule["5"].push(time);
				}
					
			}
				
			var cell = cells[5];
			var children = cell.children;
			var start_time = children[0].value;
			var end_time = children[2].value;
			if(start_time != "" && end_time != ""){
				check1 = true;
				//Convert 24hr to 12hr time
				var start_time = moment(start_time, ["HH:mm"]).format("h:mm A");
				var end_time = moment(end_time, ["HH:mm"]).format("h:mm A");
				var time = start_time +" - " +end_time;
				if(schedule.hasOwnProperty("6"))
					schedule["6"].push(time);
				else{
					schedule["6"] = [];
					schedule["6"].push(time);
				}
					
			}
		
		}
		
		if(check1 == true){
			form["schedule"] = schedule;
			var form_data = JSON.stringify(form);
			//alert(form_data);
			var url = API_URL +"/ScheduleAppAPI/restapi/professor/schedule";
			
			$.ajax({
				type: 'POST',
				contentType: 'application/json; charset=utf-8',
				url: url,
				dataType: "json",
				data: form_data,
				beforeSend: function (xhr) {
					/* Authorization header */
					xhr.setRequestHeader("Authorization", autorization);
					//xhr.setRequestHeader("X-Mobile", "false");
				},
				success: function(res, textStatus, jqXHR){
					var error = res.error;
					if(error == true)
						$("#submitScheduleErr").html(res.message);
						//alert("Error " +res.message);
					else
						alert(res.message);
						//window.location.replace("account.php");
					},
				error: function(jqXHR, textStatus, errorThrown){
					$("#signupErr").html(errorThrown);
				}
			});
		}
		else
			$("#submitScheduleErr").html("Please enter atleast one office hour");
			
	}
	else
		$("#submitScheduleErr").html("Please enter atleast one office hour");
	
	
		
}

//Validate schedule form data
function validateSchedule(){
	 var form_name = document.schedule_form;	 
     var start = form_name.start.value;
	 var end = form_name.end.value; 
	 var interval = form_name.interval.value; 
	 var officeLoc = form_name.officeLoc.value;	  
	 var valid = true;
	 var message = "The following data is missing<br>";
	  	 
	 if (start == "" || start == " ") {		 
		 message += "Semester Start Date<br>";
		 valid = false;
	 }else{
		 
	 }
	 if (end == "" || end == " ") {		 
		 message += "Semester End Date<br>";
		 valid = false;
	 }else{
			 		 
	 }
	 if (interval == "" || interval == " ") {		 
		 message += "Appointment Duration<br>";
		 valid = false;
	 }else{
			 		 
	 }
	 if (officeLoc == "" || officeLoc == " ") {		 
		 message += "Office Location<br>";
		 valid = false;
	 }else{
			 		 
	 }
	 
	 if(valid == false){
		$("#submitScheduleErr").html(message);
		return false;
	 }
	 else{
		$("#submitScheduleErr").html(""); 
		return true;
	 }
	
}

//Display professors office hrs/schedule
function loadSchedule(office_hrs, table){
	
	office_hrs = office_hrs[0];
	//Retrieve monday office hrs
	if(office_hrs.hasOwnProperty("2"))		
		var monday = office_hrs["2"];
	else
		var monday = [];
	//Retrieve tuesday office hrs
	if(office_hrs.hasOwnProperty("3"))		
		var tuesday = office_hrs["3"];
	else
		var tuesday = [];
	//Retrieve wednesday office hrs
	if(office_hrs.hasOwnProperty("4"))		
		var wednesday = office_hrs["4"];
	else
		var wednesday = [];
	//Retrieve thursday office hrs
	if(office_hrs.hasOwnProperty("5"))		
		var thursday = office_hrs["5"];
	else
		var thursday = [];
	//Retrieve friday office hrs
	if(office_hrs.hasOwnProperty("6"))		
		var friday = office_hrs["6"];
	else
		var friday = [];
	
	//Due to poor design choose I made at initial stage, I now have to do this in order to loop through office_hrs
	var ar = [monday.length, tuesday.length, wednesday.length, thursday.length, monday.length]; 
	ar.sort(function(a, b){return b-a});
	var max = ar[0];
	
	for(var i = 0; i<max; i++){
		//var table = document.getElementById("schedule_table");
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
		
		//Display monday office hrs
		if(monday.length >i){
			var time = monday[i].split(" - ");	
			var startTime = moment(time[0], ["h:mm A"]).format("HH:mm");
			var endTime = moment(time[1], ["h:mm A"]).format("HH:mm");
			cell2.innerHTML = "<input type="+"time" +" name="+"schedule_time"+ " value="+startTime +">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+ " value="+endTime +">";
			//alert(momentObj);
		}
		else
			cell2.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		
		//Display tuesday office hrs
		if(tuesday.length >i){
			var time = tuesday[i].split(" - ");	
			var startTime = moment(time[0], ["h:mm A"]).format("HH:mm");
			var endTime = moment(time[1], ["h:mm A"]).format("HH:mm");
			cell3.innerHTML = "<input type="+"time" +" name="+"schedule_time"+ " value="+startTime +">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+ " value="+endTime +">";
			//alert(momentObj);
		}
		else
			cell3.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		
		//Display wednesday office hrs
		if(wednesday.length >i){
			var time = wednesday[i].split(" - ");	
			var startTime = moment(time[0], ["h:mm A"]).format("HH:mm");
			var endTime = moment(time[1], ["h:mm A"]).format("HH:mm");
			cell4.innerHTML = "<input type="+"time" +" name="+"schedule_time"+ " value="+startTime +">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+ " value="+endTime +">";
			//alert(momentObj);
		}
		else
			cell4.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		
		//Display thursday office hrs
		if(thursday.length >i){
			var time = thursday[i].split(" - ");	
			var startTime = moment(time[0], ["h:mm A"]).format("HH:mm");
			var endTime = moment(time[1], ["h:mm A"]).format("HH:mm");
			cell5.innerHTML = "<input type="+"time" +" name="+"schedule_time"+ " value="+startTime +">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+ " value="+endTime +">";
			//alert(momentObj);
		}
		else
			cell5.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		
		//Display friday office hrs
		if(friday.length >i){
			var time = friday[i].split(" - ");	
			var startTime = moment(time[0], ["h:mm A"]).format("HH:mm");
			var endTime = moment(time[1], ["h:mm A"]).format("HH:mm");
			cell6.innerHTML = "<input type="+"time" +" name="+"schedule_time"+ " value="+startTime +">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+ " value="+endTime +">";
			//alert(momentObj);
		}
		else
			cell6.innerHTML = "<input type="+"time" +" name="+"schedule_time"+">"+"<br>"+"<input type="+"time" +" name="+"schedule_time"+">";
		
	}
	
	
}

//Retrieve all scheduled appointments from database 
function retrieveAppointments(user, auth){
	//Retrieve all scheduled appointments from database
	var urlInfo = API_URL +"/ScheduleAppAPI/restapi/professor/appointment/booked/"+user;
	var count = 0;
		//Sending ajax request to REST Api
		$.ajax({
			type: 'GET',
			contentType: 'application/json; charset=utf-8',
			url: urlInfo,
			dataType: "json",
			beforeSend: function (xhr) {
				/* Authorization header */
				xhr.setRequestHeader("Authorization", auth);
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var found = res.Found;
				if(found == false){
				
				}
					//$("#signinErr").html(res.message);
					//alert("Error " +res.message);
				else{
					var appointments = res.Appointment;
					for(var i = 0; i<appointments.length; i++){
						var appointment = appointments[i];
						var appointID = appointment["appointmentID"];
						var stdName = appointment["stdName"];
						var appoinDate = appointment["appointment_date"];
						var appTime = appointment["appointment_time"];
						var reason_app = appointment["reason_for_appointment"];
						var isCancelled = appointment["is_cancelled"];
						if(isCancelled == "no")
							count++;
						var reason_cancel = appointment["reason_cancel"];
						
						showAppointments(i, stdName, appoinDate, appTime, reason_app, isCancelled, reason_cancel, appointID, auth);
					}
					if(count > 0)
						updateAppointmentBadge(count);
				}
					
				},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});
		
		
	
}

//used when "Appointment" is clicked on header
function viewAppointMents(){
	$("a#appointm").click();
}

function updateAppointmentBadge(count){
		
	$("span#msg_badge").text(count);
	$("span#msg_badge").css('background-color',"green")
		//x.innerHTML = count;

}

function showAppointments(i, stdName, appoinDate, appTime, reason_app, isCancelled, reason_cancel, appointID, auth){
		var table = document.getElementById("appointment_table");
		var row = table.insertRow(-1);
		row.id = "schedule_tr";
		var cell1 = row.insertCell(0);		
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		cell1.id = "schedule_td";
		cell2.id = "schedule_td";
		cell3.id = "schedule_td";
		cell4.id = "schedule_td";
		cell5.id = "schedule_td";
		//Display student's name
		cell1.innerHTML = stdName;
		//Display appointment date
		cell2.innerHTML = appoinDate;
		//Display appointment time
		cell3.innerHTML = appTime;
		//Display reson for appointment
		cell4.innerHTML = reason_app;
		
		//check if appointment has been cancelled. If cancelled display "cancelled" text with link to view details
		if(isCancelled == "yes"){			
			cell5.innerHTML = "<span class="+"error"+">Cancelled</span>" +"<br>" +"<span class=" +"error"+" id="+"view_cancelled"+i+">" +"<a href="+"#"+">View details</a></span>" ;
			var id = "view_cancelled"+i;
			//alert(id);
			var cd = document.getElementById(id);
			cd.addEventListener("click", function() {
				$('#mod_reason').val(''); //clear content in mod_cont div tag			
				$('#mod_reason').val(reason_cancel); 
				$("#mod_reason").attr("disabled","disabled"); //Disable textarea
				$("#cancelled").show(); //show "cancelled" div
				$("#not_cancelled").hide(); //hide "not_cancelled" div
				viewModal(appointID, auth, cell5, id);
				
			});			
		}
		//If appointment is not cancelled, display cancel button 
		else{
			cell5.innerHTML = "<span class=" +"view_cn"+ " id="+"view_cancelled"+i+">Cancel</span>";			
			var id = "view_cancelled"+i;
			var x = document.getElementById(id);
			x.addEventListener("click", function() {
				$("#mod_reason").attr("disabled",false);
				$("#cancelled").hide();
				$("#not_cancelled").show();
				$('#mod_reason').val(''); //clear content in mod_cont div tag
				viewModal(appointID, auth, cell5, id);
				
			});	
		}

}

/* Shows Reason why student cancelled an appointment modal
* Also Shows modal for professor to enter reason for cancellation
*/
function viewModal(appoinID, auth, cell, cellID){
	$('.ui.mini.modal').modal({
        onShow: function(){
            //console.log('shown');
        },
        onApprove: function() {
            var reason = $('#mod_reason').val();
			if(reason =="" || reason == " "){
				$("#canceAPPErr").html("  Please enter reason for appointment cancellation");	
				return false;
			}
			else{
				$("#canceAPPErr").html("");
				cancelAppointment(appoinID, auth, cell, cellID);
			}
        },
		onDeny: function(){
			$("#canceAPPErr").html("");
		}
    }).modal('show')
	.modal('refresh')
	.modal('refresh');	
}

//Send cancellation information to server
function cancelAppointment(appoinID, auth, cell, cellID){

		var form_data = {};
		form_data["cancelledBy"] = "Professor";
		form_data["appointment_id"] = appoinID;
		form_data["reason"] = $('#mod_reason').val();
		var reasn_cancel = $('#mod_reason').val();
		var form_data = JSON.stringify(form_data);
		var url = API_URL +"/ScheduleAppAPI/restapi/professor/appointment/cancel";
			//alert(form_data);
			
		$.ajax({
			type: 'POST',
			contentType: 'application/json; charset=utf-8',
			url: url,
			dataType: "json",
			data: form_data,
			beforeSend: function (xhr) {
				/* Authorization header */
				xhr.setRequestHeader("Authorization", auth);
				//xhr.setRequestHeader("X-Mobile", "false");
			},
			success: function(res, textStatus, jqXHR){
				var error = res.error;
				if(error == true)
					$("#canceAPPErr").html(res.message);
					//alert("Error " +res.message);
				else{
					var appointmentCount = $("span#msg_badge").text();
					appointmentCount = appointmentCount - 1;
					$("span#msg_badge").text(appointmentCount);
					if(appointmentCount == 0)
						$("span#msg_badge").css('background-color',"grey");
					cell.innerHTML = "<span class="+"error"+">Cancelled</span>" +"<br>" +"<span class=" +"error"+" id="+cellID+">" +"<a href="+"#"+">View details</a></span>" ;
					var cd = document.getElementById(cellID);
					cd.addEventListener("click", function() {
						$('#mod_reason').val(''); //clear content in mod_cont div tag			
						$('#mod_reason').val(reasn_cancel); 
						$("#mod_reason").attr("disabled","disabled");
						$("#cancelled").show(); //show "cancelled" div
						$("#not_cancelled").hide(); //hide "not_cancelled" div
						viewModal(appoinID, auth, cell, cellID);
											
					});	
					
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				$("#signupErr").html(errorThrown);
			}
		});

}


function mySignUpSnack() {
    var x = document.getElementById("signup_snackbar")
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}

function forgotPassSnack() {
    var x = document.getElementById("forgot_snackbar")
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}

function accountSnack(message, id) {	
    var x = document.getElementById(id)
    x.className = "show";
	x.innerHTML = message;
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
}


