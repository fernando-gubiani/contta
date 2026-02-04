<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /?p=login");
    exit();
}

require_once __DIR__ . "/../bd.php";

$mes = date("n");
$ano = date("Y");
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Contta V2</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: "Inter", -apple-system, sans-serif;
            background: #F9FAFB;
            color: #111827;
            line-height: 1.5;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        h1 { font-size: 1.875rem; font-weight: 700; margin-bottom: 0.25rem; }
        .subtitle { color: #6B7280; font-size: 0.875rem; }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 150ms;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: #4F46E5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #4338CA;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #F3F4F6;
            color: #111827;
            border-color: #E5E7EB;
        }
        
        .btn-secondary:hover {
            background: #E5E7EB;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            border-left: 4px solid #4F46E5;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card.success { border-left-color: #10B981; }
        .stat-card.error { border-left-color: #EF4444; }
        
        .stat-label {
            font-size: 0.75rem;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 0.5rem;
            color: #111827;
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        
        .card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .transaction-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            transition: all 150ms;
        }
        
        .transaction-item:hover {
            border-color: #A5B4FC;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .transaction-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .transaction-icon.receita {
            background: #ECFDF5;
            color: #047857;
        }
        
        .transaction-icon.despesa {
            background: #FEF2F2;
            color: #B91C1C;
        }
        
        .transaction-details {
            flex: 1;
            min-width: 0;
        }
        
        .transaction-desc {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .transaction-meta {
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 0.25rem;
        }
        
        .transaction-value {
            font-size: 1.125rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .transaction-value.receita { color: #059669; }
        .transaction-value.despesa { color: #DC2626; }
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .highlight {
            border: 2px solid #4F46E5;
            background: linear-gradient(135deg, #EEF2FF 0%, white 100%);
        }
        
        .empty {
            text-align: center;
            color: #9CA3AF;
            padding: 3rem 1rem;
        }
        
        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<main class="container">

  <div class="header">
    <div>
      <h1>📊 Dashboard</h1>
      <p class="subtitle">Bem-vindo de volta!</p>
    </div>
    <button class="btn btn-secondary" style="font-size: 1.5rem; padding: 0.5rem;">🌙</button>
  </div>

  <div class="stat-grid">
    <?php
    $queryReceitas = "SELECT SUM(valor) as total FROM extrato WHERE MONTH(data) = $mes AND YEAR(data) = $ano AND tipo = \"receita\"";
    $resultReceitas = mysqli_query($bdConexao, $queryReceitas);
    $receitas = mysqli_fetch_assoc($resultReceitas)["total"] ?? 0;
    
    $queryDespesas = "SELECT SUM(valor) as total FROM extrato WHERE MONTH(data) = $mes AND YEAR(data) = $ano AND tipo = \"despesa\"";
    $resultDespesas = mysqli_query($bdConexao, $queryDespesas);
    $despesas = mysqli_fetch_assoc($resultDespesas)["total"] ?? 0;
    
    $saldo = $receitas - $despesas;
    $saldoClass = $saldo >= 0 ? "success" : "error";
    ?>
    
    <div class="stat-card <?php echo $saldoClass; ?>">
      <div class="stat-label">Saldo do Mês</div>
      <div class="stat-value">R$ <?php echo number_format($saldo, 2, ",", "."); ?></div>
    </div>
    
    <div class="stat-card success">
      <div class="stat-label">Receitas</div>
      <div class="stat-value">R$ <?php echo number_format($receitas, 2, ",", "."); ?></div>
    </div>
    
    <div class="stat-card error">
      <div class="stat-label">Despesas</div>
      <div class="stat-value">R$ <?php echo number_format($despesas, 2, ",", "."); ?></div>
    </div>
  </div>

  <div class="main-grid">
    <div class="card">
      <div class="card-header">
        <h2 class="card-title">📋 Transações Recentes</h2>
      </div>
      <?php
      $queryTransacoes = "SELECT e.*, c.nome_cat 
          FROM extrato e 
          LEFT JOIN categorias c ON e.id_categoria = c.id_cat
          ORDER BY e.data DESC 
          LIMIT 5";
      $resultTransacoes = mysqli_query($bdConexao, $queryTransacoes);
      
      if (mysqli_num_rows($resultTransacoes) == 0) {
          echo "<div class=\"empty\">Nenhuma transação registrada.</div>";
      } else {
          while ($trans = mysqli_fetch_assoc($resultTransacoes)) {
              $type = $trans["tipo"];
              $desc = htmlspecialchars($trans["descricao"]);
              $value = number_format($trans["valor"], 2, ",", ".");
              $date = date("d/m/Y", strtotime($trans["data"]));
              $category = $trans["nome_cat"] ?? "Sem categoria";
              
              $icon = $type === "receita" ? "💰" : "💸";
              $prefix = $type === "receita" ? "+" : "-";
              
              echo "<div class=\"transaction-item\">
                  <div class=\"transaction-icon $type\">$icon</div>
                  <div class=\"transaction-details\">
                      <div class=\"transaction-desc\">$desc</div>
                      <div class=\"transaction-meta\">$category · $date</div>
                  </div>
                  <div class=\"transaction-value $type\">{$prefix}R$ $value</div>
              </div>";
          }
      }
      ?>
    </div>

    <div class="sidebar">
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">⚡ Ações Rápidas</h2>
        </div>
        <div class="actions">
          <a href="/app/pages/credit_cards.php" class="btn btn-primary">💳 Meus Cartões</a>
          <a href="/app/pages/family_dashboard.php" class="btn btn-secondary">👨‍👩‍👧‍👦 Dashboard Familiar</a>
          <a href="/?p=statement" class="btn btn-secondary">📋 Ver Extrato</a>
        </div>
      </div>

      <div class="card highlight">
        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">🎨 Novo Design!</h3>
        <p style="color: #6B7280; font-size: 0.875rem;">Este é o novo dashboard do Contta com design moderno.</p>
      </div>
    </div>
  </div>
</main>
</body>
</html>
