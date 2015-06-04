<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
	require 'methods.php';


	createUsersTasks();
	
	if(isset($_POST['type']) && $_POST['type']=== "login"){
		$result = findUser($_POST['username'],$_POST['password']);
		echo '{ "id" : "'.$result['id'].'",  "username" : "'.$result['username'].'", "email" : "'.$result['email'].'" }';
	}
	if(isset($_POST['type']) && $_POST['type']=== "newUser"){
		addUser($_POST['email'],$_POST['username'],$_POST['password']);
	}
	
	
	
?>