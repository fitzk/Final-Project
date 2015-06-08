<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'methods.php';

if(isset($_POST['type']) && $_POST['type'] === "removeAll"){
	removeAll($_SESSION['email'],$_POST['id']);

}

if(isset($_POST['type']) && $_POST['type'] === "removeOneLog"){
	removeOneLog($_SESSION['email'],$_POST['id']);
}

?>