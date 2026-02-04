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
    <title>Investimentos - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; padding-bottom: 4rem; }
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .inv-card { background: var(--card); border-radius: 1rem; padding: 1.5rem; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .tag { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 12px; }
        .tag-rf { background: #E0E7FF; color: #4338CA; }
        .tag-rv { background: #FEF3C7; color: #92400E; }
        .tag-fundos { background: #D1FAE5; color: #065F46; }
        .summary-bar { display: flex; gap: 1rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 10px; }
        .sum-item { background: var(--card); padding: 1.5rem; border-radius: 1rem; min-width: 250px; border: 1px solid var(--border); }
        .btn { padding: 0.6rem 1.2rem; border-radius: 0.6rem; cursor: pointer; border: 1px solid var(--border); font-weight: 600; text-decoration: none; color: var(--txt); }
        .btn-primary { background: var(--primary); color: white; border: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📈 Investimentos</h1>
        <div style="display:flex; gap:1rem;">
            <button class="btn btn-primary">+ Novo Ativo</button>
            <a href="dashboard_v2.php" class="btn">← Voltar</a>
        </div>
    </div>

    <!-- Totais -->
    <?php
    $resSums = mysqli_query($bdConexao, "SELECT SUM(valor_aplicado) as total_apl FROM investimentos WHERE id_usuario = $userId AND ativo = 1");
    $sums = mysqli_fetch_assoc($resSums);
    $totalAplicado = $sums["total_apl"] ?? 0;
    ?>
    <div class="summary-bar">
        <div class="sum-item">
            <div style="font-size:0.75rem; color:var(--sub); font-weight:700;">TOTAL APLICADO</div>
            <div style="font-size:1.8rem; font-weight:800; margin-top:5px;">R$ <?php echo number_format($totalAplicado, 2, ",", "."); ?></div>
        </div>
        <div class="sum-item">
            <div style="font-size:0.75rem; color:var(--sub); font-weight:700;">RENDIMENTO TOTAL</div>
            <div style="font-size:1.8rem; font-weight:800; margin-top:5px; color:#10B981;">+ R$ 0,00</div>
        </div>
    </div>

    <div class="grid">
        <?php
        $res = mysqli_query($bdConexao, "SELECT * FROM investimentos WHERE id_usuario = $userId AND ativo = 1");
        if(mysqli_num_rows($res) == 0) {
            echo "<div style=\"grid-column:1/-1; text-align:center; padding:4rem; background:var(--card); border-radius:1rem; border:2px dashed var(--border)\">
                <p style=\"font-size:1.2rem; color:var(--sub)\">Você ainda não cadastrou investimentos.</p>
                <p style=\"font-size:0.9rem; color:var(--sub); margin-top:5px;\">Comece a acompanhar seu patrimônio agora!</p>
            </div>";
        }
        while($i = mysqli_fetch_assoc($res)) {
            $tagColor = "tag-rf"; $tipoNome = "Renda Fixa";
            if($i["tipo"] == "renda_variavel") { $tagColor = "tag-rv"; $tipoNome = "Ações/FIIs"; }
            if($i["tipo"] == "fundos") { $tagColor = "tag-fundos"; $tipoNome = "Fundos"; }
            
            echo "<div class=\"inv-card\">
                <span class=\"tag $tagColor\">$tipoNome</span>
                <h3 style=\"margin-bottom:10px;\">".htmlspecialchars($i["nome_investimento"])."</h3>
                <div style=\"display:flex; justify-content:space-between; margin-bottom:15px;\">
                    <div>
                        <div style=\"font-size:0.7rem; color:var(--sub)\">VALOR INVESTIDO</div>
                        <div style=\"font-weight:700\">R$ ".number_format($i["valor_aplicado"], 2, ",", ".")."</div>
                    </div>
                    <div style=\"text-align:right\">
                        <div style=\"font-size:0.7rem; color:var(--sub)\">RENTAB. ESPERADA</div>
                        <div style=\"font-weight:700; color:var(--primary)\">".($i["rentabilidade_esperada"]??0)."% a.a</div>
                    </div>
                </div>
                <div style=\"padding-top:15px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;\">
                   <div style=\"font-size:0.8rem; color:var(--sub)\">Aplicação: ".date("d/m/Y", strtotime($i["data_aplicacao"]))."</div>
                   <button class=\"btn\" style=\"padding:4px 10px; font-size:0.75rem;\">Detalhes</button>
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
