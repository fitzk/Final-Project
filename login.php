<?php
	require 'methods.php';

	session_start();
	$mysqli = connectToServer();
	createTable($mysqli);
	
	if($_POST['type'] === "login"){
		echo "login";	
	}
	if($_POST['type'] === "newUser"){
		addUser($_POST['email'],$_POST['username'],$_POST['password']);
	}
	$mysli->close();
	
?>