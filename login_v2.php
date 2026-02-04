<?php
session_start();
// Se já estiver logado, vai direto pro dashboard
if (isset($_SESSION["username"])) {
    header("Location: /app/pages/dashboard_v2.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login | Contta V2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --text: #1E293B;
            --text-sub: #64748B;
            --border: #E2E8F0;
        }

        [data-theme="dark"] {
            --bg: #0F172A;
            --card: #1E293B;
            --text: #F8FAFC;
            --text-sub: #94A3B8;
            --border: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }

        body {
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            transition: 0.3s;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
        }

        /* Decorative blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.2) 0%, rgba(16, 185, 129, 0.2) 100%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
        }
        .blob-1 { top: -100px; right: -100px; }
        .blob-2 { bottom: -100px; left: -100px; }

        .login-card {
            background: var(--card);
            padding: 40px;
            border-radius: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .logo {
            width: 64px;
            height: 64px;
            background: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 800;
            margin: 0 auto 24px;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        h1 { font-size: 28px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.5px; }
        p.subtitle { color: var(--text-sub); margin-bottom: 32px; font-size: 16px; }

        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: var(--text-sub); margin-bottom: 8px; margin-left: 4px; }
        
        .input-wrapper { position: relative; }
        input {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text);
            font-size: 16px;
            font-weight: 500;
            outline: none;
            transition: 0.2s;
        }
        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
        .btn-login:hover { background: var(--primary-hover); transform: translateY(-2px); }
        .btn-login:active { transform: translateY(0); }

        .forgot-password {
            display: block;
            margin-top: 24px;
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }
        .forgot-password:hover { color: var(--primary); }

        .error-msg {
            background: #FEE2E2;
            color: #EF4444;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            display: none;
        }

        /* Mobile specific adjustments */
        @media (max-width: 480px) {
            .login-card { padding: 32px 24px; border-radius: 24px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo">C</div>
            <h1>Bem-vindo de volta</h1>
            <p class="subtitle">Gerencie suas finanças com inteligência.</p>

            <?php if (isset($_GET['e']) && $_GET['e'] == 'loginerror'): ?>
                <div class="error-msg" style="display: block;">Usuário ou senha incorretos.</div>
            <?php endif; ?>

            <form action="/app/form_handler/handle_login_v2.php" method="POST">
                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="login" placeholder="Seu nome de usuário" required autofocus>
                </div>

                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-login">Entrar no Contta</button>
            </form>

            <a href="/app/setup/signup.php" class="forgot-password">Não tem uma conta? Cadastre-se</a>
        </div>
    </div>

    <script>
        // Carrega o tema preferido do usuário
        const saved = localStorage.getItem("contta-theme") || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute("data-theme", saved);
    </script>
</body>
</html>
