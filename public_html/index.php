<?php
session_start();

// Se já estiver logado, manda direto pro painel
if (isset($_SESSION['usuario'])) {
    header("Location: /pages/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>RSP Diecast | Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<style>
  :root {
    --azul-escuro: #001433;
    --azul-neon: #28CFFF;
    --branco: #FFF;
  }
  *{
    box-sizing:border-box;
    font-family:Montserrat,Arial,Helvetica,sans-serif;
  }
  body{
    margin:0;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(circle at top center,#00224d 0%,#00112c 40%,#000814 100%);
    color:var(--branco);
  }
  .login-container{
    width:380px;
    padding:40px 36px 32px;
    border-radius:16px;
    background:linear-gradient(180deg,#020d24 0%,#001226 100%);
    border:2px solid var(--azul-neon);
    box-shadow:0 0 18px rgba(40,207,255,.4),0 18px 40px rgba(0,0,0,.7);
    text-align:center;
  }
  .login-title{
    font-size:1.3rem;
    margin:18px 0 20px;
    color:var(--azul-neon);
    font-weight:700;
  }
  .input-field{
    width:100%;
    padding:12px;
    margin-bottom:10px;
    border-radius:8px;
    border:none;
    font-size:.9rem;
    background:#e9eef5;
  }
  .btn-submit{
    width:100%;
    padding:12px;
    margin-top:8px;
    border-radius:8px;
    border:none;
    background:linear-gradient(90deg,#00AEEF,var(--azul-neon));
    color:#00205B;
    font-weight:700;
    cursor:pointer;
    box-shadow:0 4px 14px rgba(0,174,239,.4);
    transition:.2s;
  }
  .btn-submit:hover{
    transform:scale(1.03);
    box-shadow:0 6px 20px rgba(0,174,239,.6);
  }
  .divider{
    margin:18px 0 12px;
    font-size:.85rem;
    opacity:.75;
  }
  .btn-google{
    padding:10px 16px;
    border-radius:8px;
    border:none;
    background:#fff;
    color:#000;
    font-weight:600;
    cursor:pointer;
    transition:.2s;
  }
  .btn-google:hover{background:#f2f2f2;}

  /* BLOCO DE ERRO */
  .login-error {
      background: rgba(255, 70, 70, 0.12);
      border: 1px solid rgba(255, 120, 120, 0.45);
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 14px;
      font-size: .85rem;
      color: #ffb3b3;
      text-align: center;
      box-shadow: 0 0 8px rgba(255, 0, 0, 0.25);
    }
</style>
</head>
<body>

<div class="login-container">
  <img src="logo.png" alt="RSP Diecast" style="width:150px;">
  <div class="login-title">Login do Sistema</div>

  <!-- EXIBE ERRO DE LOGIN (SE HOUVER) -->
  <?php if (!empty($_SESSION['erro_login'])): ?>
      <div class="login-error">
          <?= htmlspecialchars($_SESSION['erro_login']) ?>
      </div>
      <?php unset($_SESSION['erro_login']); ?>
  <?php endif; ?>

  <form method="POST" action="login_processa.php">
    <input class="input-field" type="email" name="email" placeholder="E-mail" required>
    <input class="input-field" type="password" name="senha" placeholder="Senha" required>
    <button class="btn-submit" type="submit">ENTRAR</button>
  </form>

  <div class="divider">ou</div>

  <button class="btn-google" type="button">
    Entrar com Google
  </button>
</div>

</body>
</html>
