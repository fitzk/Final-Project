$("document").ready(function(){
  
 $("#login").submit(function(event){
	var params = $(this).serializeArray();
	var name = params[0]["value"];
	var pass = params[1]["value"];
	event.preventDefault();
	
	$.ajax({	
		method: "POST",
		url: "login.php",		
		data: { username : name, password : pass}
	})
	.done(function(msg){
		console.log("Data Saved: "+ msg);
	}); 
  });
});
	


//create ajax post method