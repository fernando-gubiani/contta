<?php
session_start();
if (!isset($_SESSION["username"])) { header("Location: /?p=login"); exit(); }
require_once __DIR__ . "/../bd.php";
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Conta - Contta V2</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #F9FAFB; --card: #FFF; --txt: #111827; --sub: #6B7280; --primary: #4F46E5; --border: #E5E7EB; }
        [data-theme="dark"] { --bg: #030712; --card: #111827; --txt: #F9FAFB; --sub: #9CA3AF; --border: #1F2937; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: "Inter", sans-serif; background: var(--bg); color: var(--txt); transition: 0.3s; }
        .container { max-width: 500px; margin: 0 auto; padding: 4rem 1rem; }
        .card { background: var(--card); border-radius: 1.5rem; padding: 2rem; border: 1px solid var(--border); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--sub); text-transform: uppercase; margin-bottom: 0.5rem; }
        input, select { width: 100%; padding: 0.8rem; border-radius: 0.8rem; border: 1px solid var(--border); background: var(--bg); color: var(--txt); font-size: 1rem; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem; }
        .btn { padding: 1rem; border-radius: 0.8rem; cursor: pointer; border: none; font-weight: 700; text-align: center; font-size: 1rem; transition: 0.2s; text-decoration: none; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }
        .btn-cancel { background: transparent; color: var(--sub); border: 1px solid var(--border); }
        .icon-selector { display: flex; justify-content: space-around; background: var(--bg); padding: 1rem; border-radius: 1rem; margin-bottom: 1.5rem; }
        .icon-box { font-size: 1.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.5rem; transition: 0.2s; }
        .icon-box.active { background: var(--primary); color: white; }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem;">🏦 Nova Conta</h1>
        <p style="color: var(--sub)">Cadastre um banco ou carteira</p>
    </div>

    <div class="card">
        <form action="/app/form_handler/handle_account.php" method="POST">
            <div class="form-group">
                <label>Nome da Conta</label>
                <input type="text" name="nome_conta" placeholder="Ex: Nubank, Carteira, Itaú..." required>
            </div>

            <div class="form-group">
                <label>Tipo de Conta</label>
                <select name="tipo_conta">
                    <option value="corrente">Conta Corrente</option>
                    <option value="poupanca">Poupança</option>
                    <option value="investimento">Investimentos</option>
                    <option value="dinheiro">Dinheiro vivo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Saldo Inicial (R$)</label>
                <input type="number" step="0.01" name="saldo_inicial" placeholder="0,00" value="0.00">
            </div>

            <div class="btn-row">
                <a href="dashboard_v2.php" class="btn btn-cancel">Cancelar</a>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
<script>
    const saved = localStorage.getItem("contta-theme");
    if(saved) document.documentElement.setAttribute("data-theme", saved);
</script>
</body>
</html>
