<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'methods.php';


// if session is not set
	if( isset($_POST['type']) &&  $_POST['type'] === "checkSess"){
		if(!isset($_SESSION['email'])){
			echo "not set";
			
		}else{
			echo $_SESSION['username'];
		}
	}
	
 	if(isset($_POST['type']) && $_POST['type']=== "postTask"){
		addTask($_SESSION['email'],$_POST['task'],$_POST['course'],$_POST['estimate']);
	}
/*	 
	 if( isset($_POST['type']) &&  $_POST['type'] === "updateTime"){
		 $result = getTasks($_SESSION['email']);
		  $arr = json_decode($result, true);
		//  var_dump($arr);
		  foreach ($arr as $task){
			  $total = getTotal($_SESSION['email'],$task['id']);
			//updateTime($_SESSION['email'],$task['id'],$total);
		  }
	}  */
	
	if( isset($_POST['type']) &&  $_POST['type'] === "getCourses"){
		$result = getCourses($_SESSION['email']);
		echo $result;
	}
	if( isset($_POST['type']) &&  $_POST['type'] === "getCourseData"){
		$result = getCourseData($_SESSION['email']);
		echo $result;
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "getTasks"){
		$result = getTasks($_SESSION['email']);
		echo $result;
	}
	if(isset($_POST['type']) && $_POST['type']=== "getAllTasks"){
		$result = getAllTasks($_SESSION['email']);
		echo $result;
	}
	if(isset($_POST['type']) && $_POST['type']=== "getLog"){
		$result = getLog($_SESSION['email'],$_POST['id']);
		echo $result;
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "addLog"){
		addLog($_SESSION['email'],$_POST['id']);
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "updateLog"){
		updateLog($_SESSION['email'],$_POST['id']);
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "getAllUserTasks"){
		$result= all($_SESSION['email']);
		echo $result;
	}
	
	if(isset($_POST['type']) && $_POST['type']=== "finish"){
		finish($_SESSION['email'],$_POST['id']);
	}
?>