$("document").ready(function(){
  
  //NEW USER
  //on submit, send request to login.php
  //to bind params to query DB
 $("#newUser").submit(function(event){
	var params = $(this).serializeArray();
	//store username, email, and password
	var mail = params[0]["value"];
	var name = params[1]["value"];
	var pass = params[2]["value"];
	event.preventDefault();
	//error handling
	if( name === "" || mail == ""|| pass === ""){
		$("#loginMessage").html("<p> Please fill in all three fields </p>");	
		return 1;
	}
	//newUser allows php script to 
	//determine type of request
	var newUser = {
		type: "newUser",
		email : mail,
		username : name,
		password : pass
	}
	//ajax request 
	postToServer(newUser);
	
  });
  
  //LOG IN
  //on submit, send request to login.php
  //to bind params to query DB
 $("#login").submit(function(event){
	var params = $(this).serializeArray();
	//store username and password
	var name = params[0]["value"];
	var pass = params[1]["value"];
	event.preventDefault();
	//error handling
	if( name === "" || pass === ""){
		$("#loginMessage").empty();
		$("#loginMessage").append("<p> Please enter your username and password </p>");	
		return 1;
	}
	//loginReq allows php script to 
	//determine type of request
	var loginReq = {
		type: "login",
		username : name,
		password : pass
	}
	
	//ajax request 
	$.ajax({	
		url: "login.php",
		method: "POST",
		async: true,
		data: loginReq
	})
	.done(function(msg){
		var user = JSON.parse(msg);
		
		if(user['id'] === ""){
			$("#loginMessage").empty();
			$("#loginMessage").append("<p> Incorrect Username or Passowrd. </p>");	
			return 1;
		}else{
			// Save data to sessionStorage
			sessionStorage.setItem('username', user['username']);
			sessionStorage.setItem('email', user['email']);
			window.location = "http://web.engr.oregonstate.edu/~fitzsimk/Final-Project/home.html";
		}
		console.log("Data Saved: "+ msg);
	}); 

	$("#login").each(function(){
		this.reset();
	});
  });
});

function postToServer(info){
	$.ajax({	
		url: "login.php",
		method: "POST",
		async: true,
		data: info
	})
	.done(function(msg){
		console.log("Data Saved: "+ msg);
	}); 
}	
