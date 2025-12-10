<?php
session_start();

// Remove todas as variáveis de sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Saindo...</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta http-equiv="refresh" content="2; URL=/index.php"> <!-- redireciona após 2 segundos -->

<style>
  :root {
    --azul-escuro: #001433;
    --azul-neon: #28CFFF;
    --branco: #FFF;
  }

  *{
    box-sizing: border-box;
    font-family: Montserrat, Arial, Helvetica, sans-serif;
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

  .logout-container{
    width:380px;
    padding:35px;
    border-radius:16px;
    text-align:center;

    background:linear-gradient(180deg,#020d24 0%,#001226 100%);
    border:2px solid var(--azul-neon);
    box-shadow:
      0 0 18px rgba(40,207,255,.4),
      0 18px 40px rgba(0,0,0,.7);
  }

  .logout-title{
    font-size:1.3rem;
    color:var(--azul-neon);
    margin-bottom:10px;
    font-weight:700;
  }

  .logout-text{
    font-size:.95rem;
    opacity:.85;
    margin-bottom:18px;
  }

  .loading{
    border:4px solid rgba(255,255,255,.15);
    border-top:4px solid var(--azul-neon);
    border-radius:50%;
    width:38px;
    height:38px;
    margin:0 auto;
    animation:spin 1s linear infinite;
  }

  @keyframes spin{
    0%{ transform:rotate(0deg); }
    100%{ transform:rotate(360deg); }
  }
</style>
</head>
<body>

<div class="logout-container">
  <div class="logout-title">Sessão Encerrada</div>
  <div class="logout-text">Você está sendo redirecionado para a tela de login...</div>
  <div class="loading"></div>
</div>

</body>
</html>
