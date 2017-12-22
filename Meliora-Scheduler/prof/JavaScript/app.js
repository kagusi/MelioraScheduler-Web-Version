 // Initialize Firebase

  var config = {
    apiKey: "AIzaSyBIRmobhZmynzwrLrDFcIKMUjzBQ__UW0I",
    authDomain: "meliorascheduler.firebaseapp.com",
    databaseURL: "https://meliorascheduler.firebaseio.com",
    projectId: "meliorascheduler",
    storageBucket: "meliorascheduler.appspot.com",
    messagingSenderId: "509296316051"
  };
  firebase.initializeApp(config);
  
const messaging = firebase.messaging();

var token = "";


messaging.requestPermission()
  .then(function(){ //Permission has been granted
	  console.log('Permission Granted');
  })
 
  .catch(function(err){ //Permission not granted
	  alert("This app works well if notification is enable!");
  });
  
  messaging.onTokenRefresh(function() {
    messaging.getToken()
    .then(function(refreshedToken) {
      //console.log(refreshedToken);
	  
	  token = refreshedToken; //declare token global variable automatically
	  
	//Compare token with already store token, if different then send new token to server
	if (typeof(Storage) !== "undefined") {
		if (localStorage.msg_key) { //check if token already store
			var oldToken  = localStorage.getItem("msg_key");
			if(oldToken != refreshedToken)
				sendTokenToServer(refreshedToken);			
		}

	}

    })
    .catch(function(err) {
      console.log('Unable to retrieve refreshed token ', err);
      showToken('Unable to retrieve refreshed token ', err);
    });
  });
  
  function sendTokenToServer(refreshedToken){
	var form = {};   
	form["msg_key"] = refreshedToken;
	form["msg_key"] = oldToken;
	var form_data = JSON.stringify(form);
	
			//Sending ajax request to REST Api
				$.ajax({
					type: 'POST',
					contentType: 'application/json; charset=utf-8',
					url: 'https://localhost/IndependentProject/ScheduleAppAPI/restapi/professor/token/update',
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
							//$("#signupErr").html(res.message);
						}
						else{
							//Save user msg api key in local storage
							if (typeof(Storage) !== "undefined") {
								localStorage.setItem("msg_key", refreshedToken);
							}
							//window.location.replace("login.html?success=true");
						}
							

						},
					error: function(jqXHR, textStatus, errorThrown){
						//$("#signupErr").html(errorThrown);
					}
				});
	  
  }

function retrieveToken(){

  messaging.getToken()
    .then(function(currentToken) {
      if (currentToken) {
        console.log(currentToken);
		token = currentToken;
      } 
    })
    .catch(function(err) {
      alert("error");
    });
	//alert(token);
	//return token;
}
  
  messaging.onMessage(function(payload){
	  console.log('onMessage: ', payload);
  });
  
  