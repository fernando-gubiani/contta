<?php
require_once "app/bd.php";
$message = "";
$statusClass = "";
if (isset($_POST["test_db"])) {
    if (!$bdConexao) {
        $message = "❌ Erro ao conectar: " . mysqli_connect_error();
        $statusClass = "error";
    } else {
        $message = "✅ Conexão realizada com sucesso!";
        $statusClass = "success";
        $message .= "<br>Servidor: " . mysqli_get_server_info($bdConexao);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste de Conexão DB</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f7f6; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        button { background: #007bff; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background 0.3s; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; padding: 15px; border-radius: 6px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Contta - DB Debug</h1>
        <p>Clique no botão para testar a conexão configurada em <code>app/bd.php</code></p>
        <form method="post">
            <button type="submit" name="test_db">Testar Conexão</button>
        </form>
        <?php if ($message): ?>
            <div class="result <?php echo $statusClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
