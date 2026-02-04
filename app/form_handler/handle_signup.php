<?php 
include_once($_SERVER["DOCUMENT_ROOT"] . "/app/bd.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/assets/style/style.css">
  <link rel="stylesheet" href="/assets/style/login-cadastro-setup.css">
  <script src="/plugin/sweetalert2/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
  <title>Contta | Cadastro</title>
</head>
<body>
<?php
$login = $_POST["login"];
$senha = MD5($_POST["senha"]);

$query_select = "SELECT login FROM usuarios WHERE login = \"$login\"";
$select = mysqli_query($bdConexao, $query_select);
$array = mysqli_fetch_array($select);
$logarray = isset($array["login"]) ? $array["login"] : "";

if(empty($login)){
    echo "<script>Swal.fire({title: \"O nome de usuário deve ser preenchido\", icon: \"error\", confirmButtonText: \"Voltar\", didClose: () => { window.location.href=\"/app/setup/signup.php\"; }});</script>";
} else if($logarray == $login){
    echo "<script>Swal.fire({title: \"Usuário já existe\", text: \"Escolha outro nome.\", icon: \"error\", confirmButtonText: \"Voltar\", didClose: () => { window.location.href=\"/app/setup/signup.php\"; }});</script>";
} else if(strlen($_POST["senha"]) < 4){
    echo "<script>Swal.fire({title: \"Senha muito curta\", text: \"Mínimo 4 caracteres.\", icon: \"error\", confirmButtonText: \"Voltar\", didClose: () => { window.location.href=\"/app/setup/signup.php\"; }});</script>";
} else {
    $query = "INSERT INTO usuarios (login,senha,administrador,codigo_autorizacao) VALUES (\"$login\",\"$senha\",0,\"\")";
    $insert = mysqli_query($bdConexao, $query);
    if($insert){
        echo "<script>Swal.fire({title: \"Cadastrado!\", text: \"Faça login agora.\", icon: \"success\", confirmButtonText: \"Login\", didClose: () => { window.location.href=\"/\"; }});</script>";
    } else {
        echo "<script>Swal.fire({title: \"Erro no cadastro\", icon: \"error\", confirmButtonText: \"Voltar\", didClose: () => { window.location.href=\"/app/setup/signup.php\"; }});</script>";
    }
}
?>
</body></html>
