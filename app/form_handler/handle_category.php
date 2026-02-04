<?php
session_start();
require_once __DIR__ . "/../bd.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($bdConexao, $_POST["nome_cat"]);
    // Usando as colunas que vimos no print
    $sql = "INSERT INTO categorias (nome_cat, eh_cat_principal, cat_principal) VALUES (\"$nome\", 1, \"Geral\")";
    
    if (mysqli_query($bdConexao, $sql)) {
        header("Location: /app/pages/manage_categories_v2.php?success=category_added");
    } else {
        echo "Erro ao cadastrar categoria: " . mysqli_error($bdConexao);
    }
}
