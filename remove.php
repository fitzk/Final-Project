<?php
session_start();

require 'methods.php';

if(isset($_POST['type']) && $_POST['type'] === "removeAll"){
	removeAll($_SESSION['email'],$_POST['id']);

}

if(isset($_POST['type']) && $_POST['type'] === "removeOneLog"){
	removeOneLog($_SESSION['email'],$_POST['id'],$_POST['taskId']);
}

?>