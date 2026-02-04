<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";
$userId = 1; // Ajustar conforme sessão
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartões - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #111827; --card: #1F2937; --txt: #F9FAFB; --sub: #9CA3AF; --border: #374151; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); }
        .container { max-width: 1000px; margin: 0 auto; padding: 2rem 1rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
        .cc-card { background: var(--card); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid var(--border); position: relative; overflow: hidden; }
        .cc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
        .cc-chip { width: 40px; height: 30px; background: #fbbf24; border-radius: 4px; }
        .cc-limit-bar { height: 8px; background: var(--border); border-radius: 4px; margin: 1rem 0; overflow: hidden; }
        .cc-progress { height: 100%; background: var(--primary); transition: width 0.5s; }
        .btn { padding: 0.6rem 1.2rem; border-radius: 0.5rem; cursor: pointer; border: none; font-weight: 600; text-decoration: none; display: inline-flex; }
        .btn-primary { background: var(--primary); color: white; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1>💳 Meus Cartões</h1>
        <a href="dashboard_v2.php" class="btn" style="background:var(--card); color:var(--txt); border:1px solid var(--border)">← Voltar</a>
    </div>
    <div class="grid">
        <?php
        $res = mysqli_query($bdConexao, "SELECT * FROM cartoes_credito WHERE id_usuario = $userId");
        if(mysqli_num_rows($res) == 0) {
            echo "<div style=\"grid-column:1/-1; text-align:center; padding:4rem; background:var(--card); border-radius:1rem; border:2px dashed var(--border)\">
                <p style=\"font-size:1.2rem; color:var(--sub)\">Nenhum cartão encontrado.</p>
                <button class=\"btn btn-primary\" style=\"margin-top:1rem\">+ Adicionar Primeiro Cartão</button>
            </div>";
        }
        while($c = mysqli_fetch_assoc($res)) {
            $limite = $c["limite_total"];
            $usado = 0; // TODO: Query faturas
            $percent = ($limite > 0) ? ($usado / $limite) * 100 : 0;
            echo "<div class=\"cc-card\">
                <div class=\"cc-header\">
                    <div>
                        <div style=\"font-size:0.8rem; text-transform:uppercase; color:var(--sub)\">Bandeira: ".($c["bandeira"]??"UNKN")."</div>
                        <div style=\"font-size:1.4rem; font-weight:700\">".htmlspecialchars($c["nome_card"]??"Cartão")."</div>
                    </div>
                    <div class=\"cc-chip\"></div>
                </div>
                <div style=\"font-size:0.9rem; color:var(--sub)\">Limite Disponível</div>
                <div style=\"font-size:1.2rem; font-weight:700\">R$ ".number_format($limite - $usado, 2, ",", ".")."</div>
                <div class=\"cc-limit-bar\"><div class=\"cc-progress\" style=\"width: $percent%\"></div></div>
                <div style=\"display:flex; justify-content:space-between; font-size:0.8rem; color:var(--sub)\">
                    <span>Usado: R$ ".number_format($usado, 2, ",", ".")."</span>
                    <span>Total: R$ ".number_format($limite, 2, ",", ".")."</span>
                </div>
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
