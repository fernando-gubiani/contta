<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
echo "<h1>Debug Step 1: Init</h1>";
session_start();
echo "<h1>Debug Step 2: Session Started</h1>";
echo "Username: " . ($_SESSION["username"] ?? "NOT SET") . "<br>";
require_once __DIR__ . "/../bd.php";
echo "<h1>Debug Step 3: DB Required</h1>";
if (!$bdConexao) {
    echo "Connection Failed: " . mysqli_connect_error();
} else {
    echo "Connection Success!<br>";
    $uName = $_SESSION["username"] ?? "";
    $resUser = mysqli_query($bdConexao, "SELECT ID FROM usuarios WHERE login = \"$uName\"");
    if ($resUser) {
        $userData = mysqli_fetch_assoc($resUser);
        echo "User ID Found: " . ($userData["ID"] ?? "NULL") . "<br>";
    } else {
        echo "Query User Failed: " . mysqli_error($bdConexao) . "<br>";
    }
}
echo "<h1>Debug Step 4: End of script</h1>";
?>
