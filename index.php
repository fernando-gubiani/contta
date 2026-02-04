<?php
session_start();
if (isset($_SESSION["username"])) {
    header("Location: /app/pages/dashboard_v2.php");
} else {
    header("Location: /login_v2.php");
}
exit();
?>
