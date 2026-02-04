<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";

$userId = 1;
// Traduzindo meses para português
$meses_pt = [1=>"Janeiro", 2=>"Fevereiro", 3=>"Março", 4=>"Abril", 5=>"Maio", 6=>"Junho", 7=>"Julho", 8=>"Agosto", 9=>"Setembro", 10=>"Outubro", 11=>"Novembro", 12=>"Dezembro"];

$mes = isset($_GET["mes"]) ? intval($_GET["mes"]) : intval(date("n"));
$ano = isset($_GET["ano"]) ? intval($_GET["ano"]) : intval(date("Y"));
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; }
        .container { max-width: 700px; margin: 0 auto; padding: 2rem 1rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .filters { display: flex; gap: 0.5rem; margin-bottom: 2rem; }
        select { padding: 0.6rem; border-radius: 0.8rem; border: 1px solid var(--border); background: var(--card); color: var(--txt); font-weight: 600; cursor: pointer; }
        .day-group { margin-bottom: 2rem; }
        .day-title { font-size: 0.8rem; font-weight: 800; color: var(--sub); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 5px; }
        .trans-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--card); border-radius: 1rem; border: 1px solid var(--border); margin-bottom: 0.8rem; transition: 0.2s; }
        .trans-item:hover { border-color: var(--primary); transform: translateX(4px); }
        .icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .icon.receita { background: rgba(16, 185, 129, 0.1); color: #10B981; }
        .icon.despesa { background: rgba(239, 68, 68, 0.1); color: #EF4444; }
        .btn { padding: 0.6rem 1.2rem; border-radius: 0.6rem; text-decoration: none; font-weight: 600; border: 1px solid var(--border); color: var(--txt); }
        .btn-primary { background: var(--primary); color: white; border: none; }
        @media (max-width: 500px) { .header { flex-direction: column; gap:1rem; align-items: flex-start; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📑 Extrato</h1>
        <div style="display:flex; gap:0.5rem;">
            <a href="new_transaction_v2.php" class="btn btn-primary">+ Novo</a>
            <a href="dashboard_v2.php" class="btn">Voltar</a>
        </div>
    </div>

    <form method="GET" class="filters">
        <select name="mes" onchange="this.form.submit()">
            <?php foreach($meses_pt as $num => $nome) echo "<option value=\"$num\" ".($mes==$num?"selected":"").">$nome</option>"; ?>
        </select>
        <select name="ano" onchange="this.form.submit()">
            <?php for($y=date("Y"); $y>=2020; $y--) echo "<option value=\"$y\" ".($ano==$y?"selected":"").">$y</option>"; ?>
        </select>
    </form>

    <?php
    $sql = "SELECT e.*, c.nome_cat FROM extrato e LEFT JOIN categorias c ON e.id_categoria = c.id_cat WHERE MONTH(e.data) = $mes AND YEAR(e.data) = $ano ORDER BY e.data DESC, e.id DESC";
    $result = mysqli_query($bdConexao, $sql);

    if (!$result) {
        echo "<div class=\"card\" style=\"padding:2rem; color:#EF4444; background:rgba(239,68,68,0.1); border-radius:1rem;\">
            <b>Erro no Banco de Dados:</b><br>".mysqli_error($bdConexao)."
        </div>";
    } else {
        if(mysqli_num_rows($result) == 0) {
            echo "<div style=\"text-align:center; padding:4rem; color:var(--sub);\">Nenhum registro encontrado para este mês.</div>";
        } else {
            $lastDate = "";
            while($t = mysqli_fetch_assoc($result)) {
                $d = date("d/m/Y", strtotime($t["data"]));
                $label = ($d == date("d/m/Y")) ? "Hoje" : (($d == date("d/m/Y", strtotime("-1 day"))) ? "Ontem" : $d);

                if($lastDate != $label) {
                    if($lastDate != "") echo "</div>";
                    echo "<div class=\"day-group\"><div class=\"day-title\">$label</div>";
                    $lastDate = $label;
                }

                $isRec = $t["tipo"] == "receita";
                echo "<div class=\"trans-item\">
                    <div class=\"icon ".($isRec?"receita":"despesa")."\">".($isRec?"💰":"💸")."</div>
                    <div style=\"flex:1\">
                        <div style=\"font-weight:600\">".htmlspecialchars($t["descricao"])."</div>
                        <div style=\"font-size:0.8rem; color:var(--sub)\">".($t["nome_cat"]??"Geral")."</div>
                    </div>
                    <div style=\"font-weight:700; color:".($isRec?"#10B981":"#EF4444")."\">
                        ".($isRec?"+":"-")." R$ ".number_format($t["valor"], 2, ",", ".")."
                    </div>
                </div>";
            }
            echo "</div>";
        }
    }
    ?>
</div>
<script>
    const saved = localStorage.getItem("contta-theme");
    if(saved) document.documentElement.setAttribute("data-theme", saved);
</script>
</body>
</html>

