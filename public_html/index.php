<?php
// ===============================================
// RSP Diecast – Sistema Web
// Página inicial (Login)
// Autor: Equipe RSP Diecast
// ===============================================

session_start();

// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: pages/dashboard.php");
    exit;
}

// Inclui conexão com o banco
require_once __DIR__ . '/includes/conexao.php';

// Processa login (formulário POST)
$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            // Se for login local
            if ($usuario['tipo_login'] === 'local' && hash('sha256', $senha) === $usuario['senha']) {
                $_SESSION['usuario'] = $usuario;
                header("Location: pages/dashboard.php");
                exit;
            } else {
                $erro = "E-mail ou senha inválidos.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RSP Diecast | Login</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background-color: #00205B;
      font-family: 'Montserrat', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #fff;
    }

    .login-box {
      background: #0c1a3c;
      border: 1px solid #00AEEF;
      border-radius: 12px;
      padding: 40px;
      width: 100%;
      max-width: 380px;
      text-align: center;
      box-shadow: 0 0 20px rgba(0, 174, 239, 0.3);
    }

    .logo {
      width: 180px;
      margin-bottom: 20px;
    }

    h2 {
      color: #00AEEF;
      margin-bottom: 20px;
      font-weight: 700;
    }

    input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: none;
      outline: none;
    }

    input[type="email"],
    input[type="password"] {
      background: #1b2e5a;
      color: #fff;
    }

    input[type="submit"] {
      background: #00AEEF;
      color: #fff;
      cursor: pointer;
      font-weight: bold;
      text-transform: uppercase;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: #008fcc;
    }

    .erro {
      color: #ff6b6b;
      margin-bottom: 15px;
    }

    footer {
      position: absolute;
      bottom: 20px;
      width: 100%;
      text-align: center;
      color: #9FA3A9;
      font-size: 0.8rem;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <img src="imagens/logo.png" alt="RSP Diecast" class="logo">
    <h2>Login do Sistema</h2>

    <?php if ($erro): ?>
      <div class="erro"><?= $erro; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <input type="submit" value="Entrar">
    </form>

    <p style="margin-top:15px; font-size:0.9rem;">ou</p>
    <button onclick="alert('Login com Google ainda em desenvolvimento.')" style="background:#fff; color:#00205B; border:none; padding:10px 20px; border-radius:8px; cursor:pointer;">
      Entrar com Google
    </button>
  </div>

  <footer>
    © 2025 RSP Diecast • contato@rspdiecast.com.br
  </footer>

</body>
</html>
