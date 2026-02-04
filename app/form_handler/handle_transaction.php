<?php
session_start();
require_once __DIR__ . "/../bd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = 1; // Ajustar conforme sua sessão
    $familiaId = 1; // Ajustar conforme vínculo familiar do usuário
    
    $descricao = mysqli_real_escape_string($bdConexao, $_POST["descricao"]);
    $valor = floatval($_POST["valor"]);
    $tipo = mysqli_real_escape_string($bdConexao, $_POST["tipo"]); // receita ou despesa
    $data = mysqli_real_escape_string($bdConexao, $_POST["data"]);
    $id_categoria = intval($_POST["id_categoria"]);
    $id_conta = intval($_POST["id_conta"]);
    
    // SQL corrigido para incluir data_insert que o seu banco exige
    $sql = "INSERT INTO extrato (id_usuario, id_familia, descricao, valor, tipo, data, data_insert, id_categoria, id_conta, visivel_para) 
            VALUES ($userId, $familiaId, \"$descricao\", $valor, \"$tipo\", \"$data\", CURDATE(), $id_categoria, $id_conta, \"familia\")";
    
    if (mysqli_query($bdConexao, $sql)) {
        header("Location: /app/pages/dashboard_v2.php?success=transaction_added");
    } else {
        echo "Erro ao salvar transação: " . mysqli_error($bdConexao);
        echo "<br><br><a href=\"javascript:history.back()\">Voltar e tentar novamente</a>";
    }
}
