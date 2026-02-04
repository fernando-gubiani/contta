<?php
session_start();
require_once __DIR__ . "/../bd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($bdConexao, $_POST["nome_conta"]);
    $tipo = mysqli_real_escape_string($bdConexao, $_POST["tipo_conta"]);
    $saldo = floatval($_POST["saldo_inicial"]);
    $userId = 1; // Ajustar conforme sua sessão

    // Colunas corrigidas: "conta" em vez de "nome_conta" e "saldo_inicial"
    $sql = "INSERT INTO contas (id_usuario, conta, tipo_conta, saldo_inicial, exibir) VALUES ($userId, \"$nome\", \"$tipo\", $saldo, 1)";
    
    if (mysqli_query($bdConexao, $sql)) {
        header("Location: /app/pages/dashboard_v2.php?success=account_created");
    } else {
        echo "Erro ao cadastrar conta: " . mysqli_error($bdConexao);
    }
}
