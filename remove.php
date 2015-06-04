<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'methods.php';

if(isset($_POST['type']) && $_POST['type'] === "removeAll"){
	removeAll($_POST['email'],$_POST['id']);
//	removeAll("kfitzsimmons@live.com",1);
}

?>