<?php
session_start();
$_SESSION = array();
require 'methods.php';



	//echo "<div><p>username: ".$_SESSION['username']."   email: ".$_SESSION['email']."</p></div>";
	createUsersTasks();
	
	if(isset($_POST['type']) &&  $_POST['type'] === "login"){
		$result = findUser($_POST['username'],$_POST['password']);
		$_SESSION['username'] = $result['username'];
		$_SESSION['email']= $result['email'];
		echo '{ "id" : "'.$result['id'].'",  "username" : "'.$result['username'].'", "email" : "'.$result['email'].'" }';
	}

	
	if(isset($_POST['type']) &&  $_POST['type'] === "newUser"){
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['email']= $_POST['email'];
		$result= addUser($_POST['email'],$_POST['username'],$_POST['password']);
		echo $result;
	}
	
	
	
?>