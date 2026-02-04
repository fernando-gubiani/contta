<?php
require_once __DIR__ . "/app/bd.php";
echo "=== CONTTA - MIGRACAO PARA V2.0 ===\n\n";

// 1. Criar tabela de familias
echo "1. Criando tabela familias...\n";
$sqlFamilias = "CREATE TABLE IF NOT EXISTS familias (id_familia INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, nome_familia VARCHAR(100) NOT NULL, data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlFamilias)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

// 2. Criar tabela de membros da familia
echo "2. Criando tabela membros_familia...\n";
$sqlMembros = "CREATE TABLE IF NOT EXISTS membros_familia (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, id_usuario INT(11) NOT NULL, id_familia INT(11) NOT NULL, papel ENUM(\"admin\", \"membro\") DEFAULT \"membro\", pode_ver_tudo BOOLEAN DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlMembros)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

// 3. Modificar extrato
echo "3. Modificando tabela extrato...\n";
$checkExtrato = mysqli_query($bdConexao, "SHOW COLUMNS FROM extrato LIKE \"id_usuario\"");
if (mysqli_num_rows($checkExtrato) == 0) {
    $sqlExtrato = "ALTER TABLE extrato ADD COLUMN id_usuario INT(11) DEFAULT NULL, ADD COLUMN id_familia INT(11) DEFAULT NULL, ADD COLUMN visivel_para ENUM(\"pessoal\", \"familia\") DEFAULT \"familia\";";
    if (mysqli_query($bdConexao, $sqlExtrato)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }
} else { echo "   JA EXISTE\n\n"; }

// 4. Modificar contas
echo "4. Modificando tabela contas...\n";
$checkContas = mysqli_query($bdConexao, "SHOW COLUMNS FROM contas LIKE \"id_usuario\"");
if (mysqli_num_rows($checkContas) == 0) {
    $sqlContas = "ALTER TABLE contas ADD COLUMN id_usuario INT(11) DEFAULT NULL, ADD COLUMN compartilhada BOOLEAN DEFAULT 0;";
    if (mysqli_query($bdConexao, $sqlContas)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }
} else { echo "   JA EXISTE\n\n"; }

// 5. Criar tabela de cartoes de credito
echo "5. Criando tabela cartoes_credito...\n";
$sqlCartoes = "CREATE TABLE IF NOT EXISTS cartoes_credito (id_cartao INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, id_usuario INT(11) NOT NULL, nome_cartao VARCHAR(100) NOT NULL, bandeira VARCHAR(30), limite_total DECIMAL(15,2) DEFAULT 0, dia_fechamento INT(2), dia_vencimento INT(2), ativo BOOLEAN DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlCartoes)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

// 6. Criar tabela de faturas
echo "6. Criando tabela faturas_cartao...\n";
$sqlFaturas = "CREATE TABLE IF NOT EXISTS faturas_cartao (id_fatura INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, id_cartao INT(11) NOT NULL, mes_referencia INT(2) NOT NULL, ano_referencia INT(4) NOT NULL, valor_total DECIMAL(15,2) DEFAULT 0, valor_pago DECIMAL(15,2) DEFAULT 0, status ENUM(\"aberta\", \"fechada\", \"paga\") DEFAULT \"aberta\") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlFaturas)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

// 7. Criar tabela de investimentos
echo "7. Criando tabela investimentos...\n";
$sqlInvestimentos = "CREATE TABLE IF NOT EXISTS investimentos (id_investimento INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, id_usuario INT(11) NOT NULL, nome_investimento VARCHAR(200) NOT NULL, tipo ENUM(\"renda_fixa\", \"renda_variavel\", \"fundos\", \"outro\") DEFAULT \"outro\", valor_aplicado DECIMAL(15,2) DEFAULT 0, data_aplicacao DATE, data_vencimento DATE, rentabilidade_esperada DECIMAL(5,2), ativo BOOLEAN DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlInvestimentos)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

// 8. Criar tabela de historico de investimentos
echo "8. Criando tabela historico_investimentos...\n";
$sqlHistorico = "CREATE TABLE IF NOT EXISTS historico_investimentos (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, id_investimento INT(11) NOT NULL, data_registro DATE NOT NULL, valor_atual DECIMAL(15,2) DEFAULT 0, rendimento DECIMAL(15,2) DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if (mysqli_query($bdConexao, $sqlHistorico)) { echo "   OK\n\n"; } else { echo "   ERRO: " . mysqli_error($bdConexao) . "\n\n"; }

echo "\n=== MIGRACAO CONCLUIDA ===\n";
echo "Total de tabelas criadas/modificadas com sucesso!\n";
?>
