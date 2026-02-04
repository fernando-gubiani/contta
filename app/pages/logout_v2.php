<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: /login_v2.php");
exit();
?>
