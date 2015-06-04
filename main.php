<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
	require 'methods.php';
	

	if(isset($_POST['type']) && $_POST['type']=== "postTask"){
		addTask($_POST['email'],$_POST['task'],$_POST['course'],$_POST['estimate']);
	}
	
/* 	if( isset($_POST['type']) &&  $_POST['type'] === "updateTime"){
		$result = getTotal($_POST['email'],$_POST['id']);
		echo $result;
	} */
	
	if( isset($_POST['type']) &&  $_POST['type'] === "getCourses"){
		$result = getCourses($_POST['email']);
		echo $result;
	}
	if( isset($_POST['type']) &&  $_POST['type'] === "getCourseData"){
		$result = getCourseData($_POST['email']);
		echo $result;
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "getTasks"){
		$result = getTasks($_POST['email']);
		echo $result;
	}
	if(isset($_POST['type']) && $_POST['type']=== "getLog"){
		$result = getLog($_POST['email'],$_POST['id']);
		echo $result;
		
	}
	if(isset($_POST['type']) && $_POST['type']=== "addLog"){
		addLog($_POST['email'],$_POST['id']);
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "updateLog"){
		updateLog($_POST['email'],$_POST['id']);
	}
?>