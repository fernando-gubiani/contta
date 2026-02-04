<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['username'])) { header('Location: /?p=login'); exit(); }

require_once __DIR__ . '/../bd.php';

if (!$bdConexao) {
    die('Erro na conexão com o banco remoto: ' . mysqli_connect_error());
}

$resCats = mysqli_query($bdConexao, 'SELECT * FROM categorias ORDER BY nome_cat');

$userId = 1; 
$resContas = mysqli_query($bdConexao, "SELECT * FROM contas WHERE id_usuario = $userId");
?>
<!DOCTYPE html>
<html lang='pt-BR' data-theme='light'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'>
    <title>Novo Lançamento - Contta V2</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap' rel='stylesheet'>
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme='dark'] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; -webkit-tap-highlight-color: transparent; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; padding: 20px 10px; width: 100vw; overflow-x: hidden; }
        .container { max-width: 480px; margin: 0 auto; }
        .card { background: var(--card); border-radius: 24px; padding: 24px; border: 1px solid var(--border); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--sub); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px; }
        input, select { width: 100%; padding: 14px; border-radius: 16px; border: 1px solid var(--border); background: var(--bg); color: var(--txt); font-size: 1rem; outline: none; transition: 0.2s; -webkit-appearance: none; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        .type-toggle { display: flex; gap: 8px; background: var(--bg); padding: 6px; border-radius: 18px; margin-bottom: 24px; border: 1px solid var(--border); }
        .type-btn { flex: 1; padding: 12px; text-align: center; border-radius: 14px; cursor: pointer; font-weight: 700; transition: 0.2s; font-size: 0.9rem; }
        .type-btn.active.despesa { background: #EF4444; color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
        .type-btn.active.receita { background: #10B981; color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
        .btn-save { width: 100%; padding: 16px; border-radius: 16px; background: var(--primary); color: white; border: none; font-weight: 700; font-size: 1.1rem; margin-top: 10px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: var(--sub); text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    </style>
</head>
<body>
<div class='container'>
    <div style='text-align:center; margin-bottom:30px;'>
        <h1 style='font-size:1.8rem;'>💸 Novo Registro</h1>
        <p style='color:var(--sub)'>Adicione uma movimentação</p>
    </div>

    <div class='card'>
        <form action='/app/form_handler/handle_transaction.php' method='POST'>
            <div class='type-toggle'>
                <div class='type-btn active despesa' id='btn-despesa' onclick='setType("despesa")'>Despesa</div>
                <div class='type-btn' id='btn-receita' onclick='setType("receita")'>Receita</div>
            </div>
            <input type='hidden' name='tipo' id='tipo-input' value='despesa'>

            <div class='form-group'>
                <label>Descrição</label>
                <input type='text' name='descricao' placeholder='Ex: Mercado, Uber...' required>
            </div>

            <div style='display:grid; grid-template-columns: 1fr 1fr; gap: 15px'>
                <div class='form-group'>
                    <label>Valor (R$)</label>
                    <input type='number' step='0.01' name='valor' placeholder='0,00' required>
                </div>
                <div class='form-group'>
                    <label>Data</label>
                    <input type='date' name='data' value='<?php echo date("Y-m-d"); ?>' required>
                </div>
            </div>

            <div class='form-group'>
                <label>Categoria</label>
                <select name='id_categoria'>
                    <?php if($resCats) while($c = mysqli_fetch_assoc($resCats)) { ?>
                        <option value='<?php echo $c["id_cat"]; ?>'><?php echo htmlspecialchars($c["nome_cat"]); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class='form-group'>
                <label>Conta / Carteira</label>
                <select name='id_conta'>
                    <?php if($resContas) while($co = mysqli_fetch_assoc($resContas)) { ?>
                        <option value='<?php echo $co["id_con"]; ?>'><?php echo htmlspecialchars($co["conta"]); ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type='submit' class='btn-save'>Salvar Registro</button>
            <a href='dashboard_v2.php' class='btn-back'>Cancelar e Voltar</a>
        </form>
    </div>
</div>

<script>
    function setType(type) {
        document.getElementById('tipo-input').value = type;
        const btnD = document.getElementById('btn-despesa');
        const btnR = document.getElementById('btn-receita');
        if(type === 'despesa') {
            btnD.classList.add('active', 'despesa'); btnR.classList.remove('active', 'receita');
        } else {
            btnR.classList.add('active', 'receita'); btnD.classList.remove('active', 'despesa');
        }
    }
    const saved = localStorage.getItem('contta-theme');
    if(saved) document.documentElement.setAttribute('data-theme', saved);
</script>
</body>
</html>
