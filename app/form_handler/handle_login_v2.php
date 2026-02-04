<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/app/bd.php');

$login = mysqli_real_escape_string($bdConexao, $_POST['login']);
$senha = md5($_POST['senha']); // Note: md5 é o padrão atual do sistema original

$query = "SELECT * FROM usuarios WHERE login = '$login' AND senha = '$senha'";
$verifica = mysqli_query($bdConexao, $query) or die("Erro ao selecionar usuário");

if (mysqli_num_rows($verifica) <= 0) {
    header("Location: /login_v2.php?e=loginerror");
} else {
    session_start();
    $_SESSION['username'] = $login;
    // Redireciona para o Dashboard V2 por padrão agora
    header("Location: /app/pages/dashboard_v2.php");
}
?>
