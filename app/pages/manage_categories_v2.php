<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Categorias - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; padding: 20px 10px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 20px; padding: 20px; border: 1px solid var(--border); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        input { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border); background: var(--bg); color: var(--txt); font-size: 1rem; outline: none; }
        .btn { padding: 12px 20px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; background: var(--primary); color: white; width: 100%; }
        .cat-list { margin-top: 2rem; display: grid; gap: 0.8rem; }
        .cat-item { display: flex; justify-content: space-between; align-items: center; background: var(--card); padding: 1rem; border-radius: 12px; border: 1px solid var(--border); }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1>🏷️ Categorias</h1>
        <a href="dashboard_v2.php" style="color:var(--sub); text-decoration:none; font-weight:600;">Voltar</a>
    </div>

    <div class="card">
        <form action="/app/form_handler/handle_category.php" method="POST">
            <div class="form-group">
                <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem; color:var(--sub);">NOME DA CATEGORIA</label>
                <input type="text" name="nome_cat" placeholder="Ex: 🍿 Streaming, 🐶 Pet..." required>
            </div>
            <button type="submit" class="btn">Adicionar Categoria</button>
        </form>
    </div>

    <div class="cat-list">
        <?php
        $res = mysqli_query($bdConexao, "SELECT * FROM categorias ORDER BY nome_cat");
        while($c = mysqli_fetch_assoc($res)) {
            echo "<div class=\"cat-item\">
                <span style=\"font-weight:600\">".htmlspecialchars($c["nome_cat"])."</span>
                <span style=\"font-size:0.8rem; color:var(--sub)\">Ativa</span>
            </div>";
        }
        ?>
    </div>
</div>
<script>
    const saved = localStorage.getItem("contta-theme");
    if(saved) document.documentElement.setAttribute("data-theme", saved);
</script>
</body>
</html>
