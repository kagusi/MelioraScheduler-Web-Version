
$(document).ready(function(){
	checkCookie();
	
	
	$(function(){
		$("#footer").load("footer.html"); 
		$("#header").load("loggedin_header.html");
	});
	
	 $.ajax({
			type: 'GET',
			url: '../Server/process.php',
			beforeSend: function (xhr) {
				/* Authorization header */
				xhr.setRequestHeader("What", "home");
			},
			success: function(res, textStatus, jqXHR){
				var json = JSON.parse(res);
				var h = 0;
				
				for(var i =0; i<json.length; i++){
					var js = json[i];
					var ind = i;
					var img = js["pet_pic"]
					var nm = js["pet_name"]					
					var dec = js["pet_description"]
					var ht = js["pet_height"]
					var wt = js["pet_weight"]
										
					display(ind, img, nm, dec, ht, wt);

				}

			},
			error: function(jqXHR, textStatus, errorThrown){
				
			}
		});
			   
});

function setCookie(cname,cvalue,exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires=" + d.toGMTString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

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
	
function checkCookie() {
	var user = getCookie("User");
	if (user == "") {		
		window.location.replace("login.html");
	} 
}


function logOut(){
	
	$.ajax({
			type: 'GET',
			url: '../Server/process.php',
			beforeSend: function (xhr) {
				/* Authorization header */
				xhr.setRequestHeader("What", "logout");
			},
			success: function (result) {
				checkCookie();
			}
		});	
}
	
	//Build and display pets using cards
	function display(index, pet_img, name, pet_desc, ht, wt){
		
		var newdiv = document.createElement("div");
		
		newdiv.className = "ui card";
		newdiv.id = "mn"+index; //use last known id+1;
		var imgdiv = document.createElement("div");
		imgdiv.className = "image";
		
		var imgg = document.createElement("IMG");
		imgg.setAttribute("src", pet_img); //use correct image source from database		
		imgdiv.append(imgg);

		newdiv.append(imgdiv);
		 
		var divcont = document.createElement("div");
		divcont.className = "content";
		var divhead = document.createElement("div");
		divhead.className = "header";
		divhead.innerText = name; //User correct name from database
		divcont.append(divhead);
		var divdesc = document.createElement("div"); 
		divdesc.className = "description";
		divdesc.innerText = pet_desc.substring(0, 38); ////User correct info from database
		var aTag = document.createElement('a');
		aTag.setAttribute('href',"#");
		aTag.innerHTML = "...Read more";
		divdesc.append(aTag);
		divcont.append(divdesc);
		newdiv.append(divcont);
		var stackdiv = document.getElementById("stack_cards");
		stackdiv.append(newdiv);
		
		
		 var cd = document.getElementById(newdiv.id);

				//Build and Display modal box on click
				cd.addEventListener("click", function() {
	
					$( "#mod_cont" ).empty(); //clear content in mod_cont div tag
					var mod_h = document.getElementById("mod_h");
					  mod_h.innerText = name; //Use info from database;
					  var mod_img = document.getElementById("mod_img");
					  mod_img.src = pet_img; //Use info from databse
					  var mod_cont = document.getElementById("mod_cont");
					  $("#mod_cont").append("<b>Owner</b><br>");
					  var namep = name +"<br><br>";  //Use info from database
					  $("#mod_cont").append(namep);
				  $("#mod_cont").append("<b>Pet Description</b><br>");
				  //alert(pet_desc);

				  var decrip = "Buttercup is a lovely and friendly dog"; //Use info from database
				  var descrip_app = pet_desc.trim() + "<br><br>";
				  $("#mod_cont").append(descrip_app);
				  $("#mod_cont").append("<b>Height</b><br>");
				  var height = ht + "cm<br><br>";  //Use info from database
				  $("#mod_cont").append(height);
				  $("#mod_cont").append("<b>Weight</b><br>");
				  var weight = wt + "<br><br>";  //Use info from database
				  $("#mod_cont").append(weight);
				

				//Display modal box
				$('.ui.modal').modal({			  				  
				  onApprove: function (e) {
					if (e.data("value") == "easy") {
					   
					}
				  },
				}).modal('show')
				.modal('refresh')
				.modal('refresh');
				
				
			});
		
	
		
	}