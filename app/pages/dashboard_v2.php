<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";
$mes = date("n"); $ano = date("Y");
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --bg: #F9FAFB; --sidebar: #FFFFFF; --card: #FFFFFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --sidebar: #111827; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        html, body { overflow-x: hidden; width: 100%; position: relative; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; display: block; min-height: 100vh; }
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid var(--border); padding: 2rem 1rem; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; left: 0; top: 0; }
        .logo { display: flex; align-items: center; gap: 10px; margin-bottom: 2.5rem; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 0.8rem 1rem; border-radius: 0.8rem; text-decoration: none; color: var(--sub); font-weight: 500; transition: 0.2s; margin-bottom: 0.3rem; }
        .nav-item:hover, .nav-item.active { background: rgba(79, 70, 229, 0.1); color: var(--primary); font-weight: 600; }
        .main-content { margin-left: 260px; padding: 2rem 3rem; min-width: 0; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; width: 100%; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card); padding: 1.5rem; border-radius: 1.2rem; border: 1px solid var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .main-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; width: 100%; }
        .card { background: var(--card); border-radius: 1.2rem; padding: 1.8rem; border: 1px solid var(--border); width: 100%; overflow: hidden; }
        .mobile-nav { display: none; position: fixed; bottom: 0; left: 0; right: 0; height: 75px; background: var(--sidebar); border-top: 1px solid var(--border); justify-content: space-around; align-items: center; z-index: 200; padding: 0 5px 15px 5px; }
        .mobile-item { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: var(--sub); font-size: 0.65rem; font-weight: 600; flex: 1; }
        .mobile-item.active { color: var(--primary); }
        @media (max-width: 1024px) { .sidebar { display: none; } .main-content { margin-left: 0; width: 100vw; padding: 1.5rem 1rem 100px 1rem; } .mobile-nav { display: flex; } .main-grid { grid-template-columns: 1fr; } .stat-grid { grid-template-columns: 1fr; } }
        .transaction-item { display: flex; align-items: center; gap: 0.8rem; padding: 1rem 0; border-bottom: 1px solid var(--border); width: 100%; }
        .icon-box { width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0; display: flex; align-items:center; justify-content:center; background: var(--bg); font-size: 1.2rem; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">
        <div style="width:30px; height:30px; background:var(--primary); border-radius:6px; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">C</div>
        <span><b>Contta</b> V2.0</span>
    </div>
    <nav>
        <a href="dashboard_v2.php" class="nav-item active">🏠 <span>Dashboard</span></a>
        <a href="statement_v2.php" class="nav-item">📑 <span>Extrato</span></a>
        <a href="credit_cards_v2.php" class="nav-item">💳 <span>Cartões</span></a>
        <a href="investments_v2.php" class="nav-item">📈 <span>Investimentos</span></a>
        <a href="family_dashboard_v2.php" class="nav-item">👨‍👩‍👧‍👦 <span>Família</span></a>
        <a href="manage_categories_v2.php" class="nav-item">🏷️ <span>Categorias</span></a>
        <a href="new_account_v2.php" class="nav-item">🏦 <span>Nova Conta</span></a>
    </nav>
</aside>

<nav class="mobile-nav">
    <a href="dashboard_v2.php" class="mobile-item active">🏠<span>Início</span></a>
    <a href="statement_v2.php" class="mobile-item">📑<span>Extrato</span></a>
    <a href="new_transaction_v2.php" class="mobile-item" style="transform:translateY(-20px); flex: 0 0 70px;"><div style="width:58px; height:58px; background:var(--primary); color:white; border-radius:18px; display:flex; align-items:center; justify-content:center; font-size:2rem; box-shadow:0 8px 15px rgba(79,70,229,0.4); border: 4px solid var(--bg);">+</div></a>
    <a href="credit_cards_v2.php" class="mobile-item">💳<span>Cartões</span></a>
    <a href="manage_categories_v2.php" class="mobile-item">🏷️<span>Categorias</span></a>
</nav>

<main class="main-content">
    <div class="header">
        <h1>Overview</h1>
        <button onclick="toggleTheme()" class="btn" style="border-radius:12px; width:45px; height:45px; border:1px solid var(--border); background:var(--card); cursor:pointer;" id="theme-btn">🌙</button>
    </div>

    <div class="stat-grid">
        <?php
        $res = mysqli_query($bdConexao, "SELECT SUM(CASE WHEN tipo=\"receita\" THEN valor ELSE 0 END) as r, SUM(CASE WHEN tipo=\"despesa\" THEN valor ELSE 0 END) as d FROM extrato WHERE MONTH(data)=$mes AND YEAR(data)=$ano");
        $d = mysqli_fetch_assoc($res); $rec = $d["r"]??0; $des = $d["d"]??0;
        ?>
        <div class="stat-card">
            <div style="font-size:0.75rem; color:var(--sub); font-weight:700; text-transform:uppercase;">Saldo no Mês</div>
            <div style="font-size:2rem; font-weight:800; margin-top:5px; color:<?php echo ($rec-$des)>=0?"#10B981":"#EF4444"; ?>">R$ <?php echo number_format($rec-$des, 2, ",", "."); ?></div>
        </div>
        <div class="stat-card">
            <div style="font-size:0.75rem; color:var(--sub); font-weight:700; text-transform:uppercase;">Ganhos</div>
            <div style="font-size:2rem; font-weight:800; color:#10B981; margin-top:5px;">R$ <?php echo number_format($rec, 2, ",", "."); ?></div>
        </div>
        <div class="stat-card">
            <div style="font-size:0.75rem; color:var(--sub); font-weight:700; text-transform:uppercase;">Gastos</div>
            <div style="font-size:2rem; font-weight:800; color:#EF4444; margin-top:5px;">R$ <?php echo number_format($des, 2, ",", "."); ?></div>
        </div>
    </div>

    <div class="main-grid">
        <div class="card">
            <h3 style="margin-bottom:1.5rem;">🎯 Gastos por Categoria</h3>
            <div style="height:300px; width:100%;"><canvas id="catChart"></canvas></div>
        </div>
        <div class="card">
            <h3 style="margin-bottom:1.5rem;">📋 Atividades Recentes</h3>
            <?php
            $q = mysqli_query($bdConexao, "SELECT e.*, c.nome_cat FROM extrato e LEFT JOIN categorias c ON e.id_categoria = c.id_cat ORDER BY e.data DESC LIMIT 6");
            while($t = mysqli_fetch_assoc($q)) {
                $isRec = $t["tipo"]=="receita";
                echo "<div class=\"transaction-item\">
                    <div class=\"icon-box\">".($isRec?"💰":"💸")."</div>
                    <div style=\"flex:1; min-width:0;\">
                        <div style=\"font-weight:600;\" class=\"text-truncate\">".htmlspecialchars($t["descricao"])."</div>
                        <div style=\"font-size:0.8rem; color:var(--sub)\">".($t["nome_cat"]??"Geral")."</div>
                    </div>
                    <div style=\"color:".($isRec?"#10B981":"#EF4444")."; font-weight:700;\">".($isRec?"+":"-")." R$ ".number_format($t["valor"], 2, ",", ".")."</div>
                </div>";
            }
            ?>
        </div>
    </div>
</main>

<script>
    <?php
    $resCat = mysqli_query($bdConexao, "SELECT COALESCE(c.nome_cat, \"Geral\") as nome, SUM(e.valor) as total FROM extrato e LEFT JOIN categorias c ON e.id_categoria = c.id_cat WHERE MONTH(e.data)=$mes AND YEAR(e.data)=$ano AND e.tipo=\"despesa\" GROUP BY COALESCE(c.nome_cat, \"Geral\")");
    $labels = []; $values = [];
    while($row = mysqli_fetch_assoc($resCat)) { $labels[] = $row["nome"]; $values[] = $row["total"]; }
    ?>
    new Chart(document.getElementById("catChart"), {
        type: "doughnut",
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{ data: <?php echo json_encode($values); ?>, backgroundColor: ["#4F46E5", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6", "#EC4899"], borderWidth: 0 }]
        },
        options: { maintainAspectRatio: false, cutout: "75%", plugins: { legend: { position: "bottom" } } }
    });

    function toggleTheme() {
        const d = document.documentElement;
        const t = d.getAttribute("data-theme") === "light" ? "dark" : "light";
        d.setAttribute("data-theme", t);
        localStorage.setItem("contta-theme", t);
        document.getElementById("theme-btn").innerHTML = t === "light" ? "🌙" : "☀️";
    }
    const saved = localStorage.getItem("contta-theme");
    if(saved) {
        document.documentElement.setAttribute("data-theme", saved);
        document.getElementById("theme-btn").innerHTML = saved === "light" ? "🌙" : "☀️";
    }
</script>
</body>
</html>
