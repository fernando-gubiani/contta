<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /?p=login");
    exit();
}
require_once __DIR__ . "/../bd.php";

// TODO: Buscar família do usuário logado
$userId = 1;
$familyId = 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Familiar - Contta</title>
    <link rel="stylesheet" href="/assets/style/design-system-base.css">
</head>
<body>
    <div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-8);">
        <!-- Header -->
        <div style="margin-bottom: var(--space-8);">
            <h1 style="font-size: var(--text-3xl); font-weight: var(--font-bold); margin-bottom: var(--space-2);">👨‍👩‍👧‍👦 Dashboard Familiar</h1>
            <p style="color: var(--text-secondary);">Visão consolidada das finanças da família</p>
        </div>

        <!-- Cards de Resumo -->
        <div class="card-grid cols-3" style="margin-bottom: var(--space-8);">
            <?php
            // Calcular totais da família
            $month = date("n");
            $year = date("Y");
            
            // Receitas
            $queryReceitas = "SELECT SUM(valor) as total FROM extrato 
                WHERE MONTH(data) = $month AND YEAR(data) = $year 
                AND tipo = \"receita\" AND id_familia = $familyId";
            $resultReceitas = mysqli_query($bdConexao, $queryReceitas);
            $receitas = mysqli_fetch_assoc($resultReceitas)["total"] ?? 0;
            
            // Despesas
            $queryDespesas = "SELECT SUM(valor) as total FROM extrato 
                WHERE MONTH(data) = $month AND YEAR(data) = $year 
                AND tipo = \"despesa\" AND id_familia = $familyId";
            $resultDespesas = mysqli_query($bdConexao, $queryDespesas);
            $despesas = mysqli_fetch_assoc($resultDespesas)["total"] ?? 0;
            
            $saldo = $receitas - $despesas;
            $saldoClass = $saldo >= 0 ? "success" : "error";
            ?>
            
            <div class="stat-card">
                <div class="stat-card-label">Saldo do Mês</div>
                <div class="stat-card-value" style="color: var(--<?php echo $saldoClass; ?>-600);">
                    R$ <?php echo number_format($saldo, 2, ",", "."); ?>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-card-label">Receitas</div>
                <div class="stat-card-value">R$ <?php echo number_format($receitas, 2, ",", "."); ?></div>
            </div>
            
            <div class="stat-card error">
                <div class="stat-card-label">Despesas</div>
                <div class="stat-card-value">R$ <?php echo number_format($despesas, 2, ",", "."); ?></div>
            </div>
        </div>

        <!-- Transações Recentes -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Transações Recentes da Família</h2>
            </div>
            <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                <?php
                $queryTransacoes = "SELECT e.*, c.nome_cat 
                    FROM extrato e 
                    LEFT JOIN categorias c ON e.id_categoria = c.id_cat
                    WHERE e.id_familia = $familyId 
                    ORDER BY e.data DESC 
                    LIMIT 5";
                $resultTransacoes = mysqli_query($bdConexao, $queryTransacoes);
                
                if (mysqli_num_rows($resultTransacoes) == 0) {
                    echo "<p style=\"color: var(--text-secondary); text-align: center; padding: var(--space-8);\">Nenhuma transação registrada ainda.</p>";
                }
                
                while ($trans = mysqli_fetch_assoc($resultTransacoes)) {
                    $type = $trans["tipo"];
                    $desc = htmlspecialchars($trans["descricao"]);
                    $value = number_format($trans["valor"], 2, ",", ".");
                    $date = date("d/m/Y", strtotime($trans["data"]));
                    $category = $trans["nome_cat"] ?? "Sem categoria";
                    
                    $icon = $type === "receita" ? "💰" : ($type === "despesa" ? "💸" : "🔄");
                    $valuePrefix = $type === "receita" ? "+" : ($type === "despesa" ? "-" : "");
                    
                    echo "<div class=\"transaction-card\">
                        <div class=\"transaction-icon $type\">$icon</div>
                        <div class=\"transaction-details\">
                            <div class=\"transaction-description\">$desc</div>
                            <div class=\"transaction-meta\">$category · $date</div>
                        </div>
                        <div class=\"transaction-value $type\">{$valuePrefix}R$ $value</div>
                    </div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
