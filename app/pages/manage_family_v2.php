<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";

if (!$bdConexao) { die("Erro de conexão: " . mysqli_connect_error()); }

mysqli_query($bdConexao, "CREATE TABLE IF NOT EXISTS familias (id_familia INT AUTO_INCREMENT PRIMARY KEY, nome_familia VARCHAR(100))");
mysqli_query($bdConexao, "CREATE TABLE IF NOT EXISTS membros_familia (id_membro INT AUTO_INCREMENT PRIMARY KEY, id_familia INT, id_usuario INT, papel VARCHAR(20))");

$uName = $_SESSION["username"];
$resUser = mysqli_query($bdConexao, "SELECT ID FROM usuarios WHERE login = \"$uName\"");
$userData = mysqli_fetch_assoc($resUser);
$myId = $userData["ID"] ?? 0;

if ($myId == 0) { die("Erro: Usuário não encontrado no banco."); }

$resFam = mysqli_query($bdConexao, "SELECT id_familia FROM membros_familia WHERE id_usuario = $myId LIMIT 1");
$famData = mysqli_fetch_assoc($resFam);
$familyId = $famData["id_familia"] ?? 0;

if ($familyId == 0) {
    mysqli_query($bdConexao, "INSERT INTO familias (nome_familia) VALUES (\"Minha Família\")");
    $familyId = mysqli_insert_id($bdConexao);
    mysqli_query($bdConexao, "INSERT INTO membros_familia (id_familia, id_usuario, papel) VALUES ($familyId, $myId, \"admin\")");
}

$famInfo = mysqli_fetch_assoc(mysqli_query($bdConexao, "SELECT * FROM familias WHERE id_familia = $familyId"));
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gerenciar Família - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; padding: 20px 10px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 20px; padding: 25px; border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--sub); text-transform: uppercase; margin-bottom: 8px; }
        input { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border); background: var(--bg); color: var(--txt); outline: none; font-size: 1rem; }
        .btn { padding: 12px 20px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; text-align: center; text-decoration: none; display: block; width: 100%; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--txt); padding: 8px 15px; border-radius:10px; text-decoration:none; font-size:0.9rem; }
        .member-list { margin-top: 1.5rem; }
        .member-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .remove-btn { color: #EF4444; font-size: 0.8rem; font-weight: 700; text-decoration: none; cursor: pointer; background:none; border:none; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1>⚙️ Configurar Família</h1>
        <a href="family_dashboard_v2.php" class="btn-outline">Voltar</a>
    </div>

    <div class="card">
        <form action="/app/form_handler/handle_family.php" method="POST">
            <input type="hidden" name="action" value="rename_family">
            <input type="hidden" name="id_familia" value="<?php echo $familyId; ?>">
            <div class="form-group">
                <label>Nome do Grupo Familiar</label>
                <input type="text" name="nome_familia" value="<?php echo htmlspecialchars($famInfo["nome_familia"] ?? ""); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Nome</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom:1.5rem;">➕ Adicionar Membro</h3>
        <p style="font-size:0.85rem; color:var(--sub); margin-bottom:1.5rem;">Digite o login exato da pessoa.</p>
        <form action="/app/form_handler/handle_family.php" method="POST">
            <input type="hidden" name="action" value="add_member">
            <input type="hidden" name="id_familia" value="<?php echo $familyId; ?>">
            <div class="form-group">
                <label>Login do Usuário</label>
                <input type="text" name="username_to_add" placeholder="Ex: fernando" required>
            </div>
            <button type="submit" class="btn btn-primary">Adicionar ao Grupo</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom:1rem;">👥 Membros Atuais</h3>
        <div class="member-list">
            <?php
            $sql = "SELECT u.login, u.ID as user_id, mf.id_membro FROM usuarios u JOIN membros_familia mf ON u.ID = mf.id_usuario WHERE mf.id_familia = $familyId";
            $res = mysqli_query($bdConexao, $sql);
            if ($res) {
                while($m = mysqli_fetch_assoc($res)) {
                    echo "<div class=\"member-item\">
                        <div>
                            <div style=\"font-weight:600\">".htmlspecialchars($m["login"])."</div>
                            <div style=\"font-size:0.75rem; color:var(--sub)\">Status: Ativo</div>
                        </div>";
                    if ($m["user_id"] != $myId) {
                        echo "<form action=\"/app/form_handler/handle_family.php\" method=\"POST\" style=\"margin:0\">
                                <input type=\"hidden\" name=\"action\" value=\"remove_member\">
                                <input type=\"hidden\" name=\"id_membro\" value=\"{$m["id_membro"]}\">
                                <button type=\"submit\" class=\"remove-btn\" onclick=\"return confirm(\'Remover este membro?\')\">Remover</button>
                              </form>";
                    } else {
                        echo "<span style=\"font-size:0.75rem; color:var(--primary); font-weight:700;\">Você (Admin)</span>";
                    }
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</div>
<script>
    const saved = localStorage.getItem("contta-theme");
    if(saved) document.documentElement.setAttribute("data-theme", saved);
</script>
</body>
</html>
