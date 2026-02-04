<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";
$familyId = 1;

if (!$bdConexao) {
    die("Erro na conexão com o banco: " . mysqli_connect_error());
}

// Verifica se as tabelas de família existem, se não, cria-as
$checkTable = mysqli_query($bdConexao, "SHOW TABLES LIKE \"membros_familia\"");
if (mysqli_num_rows($checkTable) == 0) {
    mysqli_query($bdConexao, "CREATE TABLE IF NOT EXISTS familias (id_familia INT AUTO_INCREMENT PRIMARY KEY, nome_familia VARCHAR(100))");
    mysqli_query($bdConexao, "CREATE TABLE IF NOT EXISTS membros_familia (id_membro INT AUTO_INCREMENT PRIMARY KEY, id_familia INT, id_usuario INT, papel VARCHAR(20))");
    mysqli_query($bdConexao, "INSERT IGNORE INTO familias (id_familia, nome_familia) VALUES (1, \"Família Geral\")");
    // Vincula o usuário logado à família se não estiver vinculado
    $uName = $_SESSION["username"];
    $resU = mysqli_query($bdConexao, "SELECT id_usuario FROM usuarios WHERE login = \"$uName\"");
    if ($userRow = mysqli_fetch_assoc($resU)) {
        $uid = $userRow["id_usuario"];
        mysqli_query($bdConexao, "INSERT IGNORE INTO membros_familia (id_familia, id_usuario, papel) VALUES (1, $uid, \"admin\")");
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Família - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; padding: 20px 10px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 1.5rem; padding: 2rem; border: 1px solid var(--border); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .member-row { display: flex; align-items: center; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border); }
        .avatar { width: 45px; height: 45px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .btn { padding: 0.8rem 1.5rem; border-radius: 1rem; border: 1px solid var(--border); background: var(--card); color: var(--txt); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1>👨‍👩‍👧‍👦 Gestão Familiar</h1>
        <a href="dashboard_v2.php" class="btn">Voltar</a>
    </div>

    <!-- Resumo -->
    <div class="card" style="background: linear-gradient(135deg, var(--primary) 0%, #312E81 100%); color: white; border: none; margin-bottom: 2rem;">
        <div style="font-size:0.8rem; opacity:0.8; font-weight:700;">SALDO TOTAL DA FAMÍLIA</div>
        <?php
        $resFam = mysqli_query($bdConexao, "SELECT SUM(CASE WHEN tipo=\"receita\" THEN valor ELSE -valor END) as total FROM extrato WHERE id_familia=$familyId");
        $famTotal = mysqli_fetch_assoc($resFam)["total"]??0;
        ?>
        <div style="font-size:2.5rem; font-weight:800; margin-top:0.5rem;">R$ <?php echo number_format($famTotal, 2, ",", "."); ?></div>
    </div>

    <div class="card">
        <h3 style="margin-bottom:1.5rem;">👥 Membros da Família</h3>
        <?php
        $sql = "SELECT u.login, u.id_usuario FROM usuarios u JOIN membros_familia mf ON u.id_usuario = mf.id_usuario WHERE mf.id_familia = $familyId";
        $membros = mysqli_query($bdConexao, $sql);
        if ($membros) {
            while($m = mysqli_fetch_assoc($membros)) {
                $uid = $m["id_usuario"];
                $resInd = mysqli_query($bdConexao, "SELECT SUM(CASE WHEN tipo=\"receita\" THEN valor ELSE -valor END) as total FROM extrato WHERE id_usuario=$uid AND id_familia=$familyId");
                $indTotal = mysqli_fetch_assoc($resInd)["total"]??0;
                
                echo "<div class=\"member-row\">
                    <div class=\"avatar\">".strtoupper(substr($m["login"],0,1))."</div>
                    <div style=\"flex:1\">
                        <div style=\"font-weight:700\">".htmlspecialchars($m["login"])."</div>
                        <div style=\"font-size:0.8rem; color:var(--sub)\">Contribuição Individual</div>
                    </div>
                    <div style=\"font-weight:800; text-align:right\">R$ ".number_format($indTotal, 2, ",", ".")."</div>
                </div>";
            }
        } else {
            echo "<p style=\"color:red\">Erro ao carregar membros: " . mysqli_error($bdConexao) . "</p>";
        }
        ?>
    </div>
</div>
<script>
    const saved = localStorage.getItem(\"contta-theme\");
    if(saved) document.documentElement.setAttribute(\"data-theme\", saved);
</script>
</body>
</html>
