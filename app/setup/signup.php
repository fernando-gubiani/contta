<!DOCTYPE html>
<html lang="pt-BT">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/style/style.css">
  <link rel="stylesheet" href="/assets/style/login-cadastro-setup.css">
  <title>Contta | Cadastro</title>
</head>
<body>
  <?php include($_SERVER["DOCUMENT_ROOT"] . "/app/pages/modules/unlogged-header.php"); ?>
  <main class="main-cadastro">
    <div class="box login">
      <form method="POST" action="/app/form_handler/handle_signup.php">
        <label>Usuário:</label><input type="text" name="login" id="login">
        <label>Senha:</label><input type="password" name="senha" id="senha">
        <div class="container-botao-entrar">
          <input class="botao-acao-principal entrar" type="submit" value="Cadastrar" id="cadastrar" name="cadastrar">
        </div>
        <p class="text-cadastro"><a href="/">Já tem um cadastro? Faça login.</a></p>
      </form>
    </div>
  </main>
</body>
</html>
