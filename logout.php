<?php
session_start();
session_unset();
unset($_SESSION['username']);
unset($_SESSION['email']);
session_destroy();
header("location: login.html");

exit();


?>
