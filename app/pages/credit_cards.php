<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /?p=login");
    exit();
}
require_once __DIR__ . "/../bd.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartões de Crédito - Contta</title>
    <link rel="stylesheet" href="/assets/style/design-system-base.css">
</head>
<body>
    <div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-8);">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-8);">
            <h1 style="font-size: var(--text-3xl); font-weight: var(--font-bold);">💳 Cartões de Crédito</h1>
            <button class="btn btn-primary" onclick="showAddCardForm()">+ Novo Cartão</button>
        </div>

        <!-- Lista de Cartões -->
        <div class="card-grid">
            <?php
            $userId = 1; // TODO: pegar da sessão
            $query = "SELECT * FROM cartoes_credito WHERE id_usuario = $userId AND ativo = 1 ORDER BY nome_cartao";
            $result = mysqli_query($bdConexao, $query);
            
            if (mysqli_num_rows($result) == 0) {
                echo "<div class=\"card\" style=\"grid-column: 1 / -1; text-align: center; padding: var(--space-12);\">
                    <p style=\"color: var(--text-secondary); font-size: var(--text-lg);\">Nenhum cartão cadastrado ainda.</p>
                    <p style=\"color: var(--text-tertiary); margin-top: var(--space-2);\">Clique em \"Novo Cartão\" para começar.</p>
                </div>";
            }
            
            while ($card = mysqli_fetch_assoc($result)) {
                $cardId = $card["id_cartao"];
                $cardName = htmlspecialchars($card["nome_cartao"]);
                $brand = htmlspecialchars($card["bandeira"]);
                $limit = number_format($card["limite_total"], 2, ",", ".");
                $closeDay = $card["dia_fechamento"];
                $dueDay = $card["dia_vencimento"];
                
                // Buscar fatura atual
                $month = date("n");
                $year = date("Y");
                $invoiceQuery = "SELECT * FROM faturas_cartao WHERE id_cartao = $cardId AND mes_referencia = $month AND ano_referencia = $year";
                $invoiceResult = mysqli_query($bdConexao, $invoiceQuery);
                $invoice = mysqli_fetch_assoc($invoiceResult);
                
                $used = $invoice ? $invoice["valor_total"] : 0;
                $available = $card["limite_total"] - $used;
                $usedPercent = $card["limite_total"] > 0 ? ($used / $card["limite_total"]) * 100 : 0;
                
                echo "<div class=\"card\">
                    <div style=\"display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-4);\">
                        <div>
                            <h3 style=\"font-size: var(--text-xl); font-weight: var(--font-semibold); margin-bottom: var(--space-1);\">$cardName</h3>
                            <p style=\"font-size: var(--text-sm); color: var(--text-secondary);\">$brand</p>
                        </div>
                        <span style=\"font-size: var(--text-2xl);\">💳</span>
                    </div>
                    
                    <div style=\"margin-bottom: var(--space-4);\">
                        <div style=\"display: flex; justify-content: space-between; margin-bottom: var(--space-2);\">
                            <span style=\"font-size: var(--text-sm); color: var(--text-secondary);\">Disponível</span>
                            <span style=\"font-size: var(--text-sm); font-weight: var(--font-semibold);\">R$ " . number_format($available, 2, ",", ".") . "</span>
                        </div>
                        <div style=\"height: 8px; background: var(--gray-200); border-radius: var(--radius-full); overflow: hidden;\">
                            <div style=\"height: 100%; width: {$usedPercent}%; background: var(--primary-500); transition: width var(--transition-base);\"></div>
                        </div>
                        <div style=\"display: flex; justify-content: space-between; margin-top: var(--space-2);\">
                            <span style=\"font-size: var(--text-xs); color: var(--text-tertiary);\">R$ " . number_format($used, 2, ",", ".") . " usado</span>
                            <span style=\"font-size: var(--text-xs); color: var(--text-tertiary);\">Limite: R$ $limit</span>
                        </div>
                    </div>
                    
                    <div style=\"border-top: 1px solid var(--border-color); padding-top: var(--space-4);\">
                        <div style=\"display: flex; gap: var(--space-4); font-size: var(--text-sm);\">
                            <div>
                                <span style=\"color: var(--text-secondary);\">Fechamento:</span>
                                <strong> Dia $closeDay</strong>
                            </div>
                            <div>
                                <span style=\"color: var(--text-secondary);\">Vencimento:</span>
                                <strong> Dia $dueDay</strong>
                            </div>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
