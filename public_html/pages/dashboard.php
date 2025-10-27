<?php
// ===============================================
// RSP Diecast – Dashboard Principal
// ===============================================

session_start();

// Verifica se há sessão ativa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// Dados do usuário logado
$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RSP Diecast | Painel</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      font-family: "Montserrat", sans-serif;
      background-color: #001B44;
      color: #fff;
      margin: 0;
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 240px;
      background-color: #00205B;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px;
      box-shadow: 3px 0 10px rgba(0, 0, 0, 0.3);
    }

    .sidebar img {
      width: 160px;
      margin: 0 auto 30px;
      display: block;
    }

    .menu a {
      display: block;
      color: #CFCFCF;
      text-decoration: none;
      padding: 12px 15px;
      border-radius: 8px;
      margin: 6px 0;
      transition: 0.3s;
    }

    .menu a:hover {
      background-color: #00AEEF;
      color: #fff;
    }

    .content {
      flex: 1;
      padding: 40px;
      background: linear-gradient(145deg, #00205B, #001B44);
      overflow-y: auto;
    }

    .content h1 {
      color: #00AEEF;
      font-size: 1.8rem;
      margin-bottom: 10px;
    }

    .content p {
      color: #CFCFCF;
      font-size: 1rem;
      margin-bottom: 25px;
    }

    .quick-actions {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .card {
      background-color: #0A2A6B;
      border: 1px solid #00AEEF;
      border-radius: 12px;
      padding: 20px;
      width: 260px;
      text-align: center;
      color: #fff;
      text-decoration: none;
      transition: 0.3s;
    }

    .card:hover {
      background-color: #00AEEF;
      color: #001B44;
      transform: translateY(-4px);
    }

    footer {
      text-align: center;
      padding: 10px;
      font-size: 0.8rem;
      color: #9FA3A9;
    }

    .logout {
      display: block;
      text-align: center;
      color: #FF6B6B;
      text-decoration: none;
      margin-top: 10px;
      font-weight: bold;
    }

    .logout:hover {
      color: #FF4444;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <div>
      <img src="../imagens/logo.png" alt="RSP Diecast Logo">
      <nav class="menu">
        <a href="dashboard.php">🏠 Início</a>
        <a href="cadastro.php">📝 Cadastrar Carro</a>
        <a href="listagem.php">📋 Listagem</a>
        <a href="relatorios.php">📊 Relatórios</a>
      </nav>
    </div>

    <div>
      <hr style="border-color:#004080;">
      <p style="text-align:center; font-size:0.9rem;">Olá, <strong><?= htmlspecialchars($usuario['nome']); ?></strong></p>
      <a href="../logout.php" class="logout">Sair</a>
    </div>
  </div>

  <div class="content">
    <h1>Bem-vindo, <?= htmlspecialchars($usuario['nome']); ?> 👋</h1>
    <p>Escolha uma das opções no menu lateral para gerenciar sua coleção.</p>

    <div class="quick-actions">
      <a href="cadastro.php" class="card">
        <h3>➕ Cadastrar Carro</h3>
        <p>Adicione um novo carro ou miniatura à coleção.</p>
      </a>

      <a href="listagem.php" class="card">
        <h3>📋 Listagem</h3>
        <p>Visualize todos os carros cadastrados e suas informações.</p>
      </a>

      <a href="relatorios.php" class="card">
        <h3>📊 Relatórios</h3>
        <p>Exporte dados em formato CSV ou XLS.</p>
      </a>
    </div>

    <footer>
      <p>© 2025 RSP Diecast • Coleção Racing</p>
    </footer>
  </div>

</body>
</html>
