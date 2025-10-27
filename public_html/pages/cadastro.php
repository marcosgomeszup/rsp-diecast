<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: ../index.php");
  exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RSP Diecast | Cadastro de Carro</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      background-color: #001B44;
      color: #fff;
      font-family: "Montserrat", sans-serif;
      margin: 0;
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 240px;
      background-color: #00205B;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar img {
      width: 150px;
      margin-bottom: 30px;
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
      overflow-y: auto;
    }

    h1 {
      color: #00AEEF;
      margin-bottom: 20px;
    }

    form {
      background: #0A2A6B;
      border: 1px solid #00AEEF;
      border-radius: 12px;
      padding: 30px;
      max-width: 800px;
      margin: 0 auto;
      box-shadow: 0 0 20px rgba(0, 174, 239, 0.2);
    }

    label {
      display: block;
      margin-top: 15px;
      font-weight: 500;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: none;
      border-radius: 6px;
      background: #1b2e5a;
      color: #fff;
      font-size: 1rem;
    }

    input[type="file"] {
      background: none;
      border: none;
      color: #CFCFCF;
    }

    .preview {
      display: flex;
      gap: 15px;
      margin-top: 15px;
    }

    .preview img {
      width: 100px;
      height: 70px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #00AEEF;
    }

    button {
      background-color: #00AEEF;
      color: #fff;
      font-weight: bold;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      margin-top: 20px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #008fcc;
    }

    footer {
      text-align: center;
      margin-top: 40px;
      font-size: 0.9rem;
      color: #9FA3A9;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <div>
      <img src="../imagens/logo.png" alt="RSP Diecast">
      <nav class="menu">
        <a href="dashboard.php">🏠 Início</a>
        <a href="cadastro.php">📝 Cadastrar Carro</a>
        <a href="listagem.php">📋 Listagem</a>
        <a href="relatorios.php">📊 Relatórios</a>
      </nav>
    </div>
  </div>

  <div class="content">
    <h1>Cadastro de Carros e Miniaturas</h1>

    <form id="formCadastro" action="../api/salvar_carro.php" method="POST" enctype="multipart/form-data">
      <h3>📄 Referências do Carro</h3>

      <label for="ano">Ano</label>
      <input type="number" name="ano" id="ano" required>

      <label for="equipe">Equipe</label>
      <input type="text" name="equipe" id="equipe" required>

      <label for="modelo">Modelo</label>
      <input type="text" name="modelo" id="modelo" required>

      <label for="categoria">Categoria</label>
      <select name="categoria" id="categoria" required>
        <option value="">Selecione</option>
        <option>Fórmula 1</option>
        <option>Rally</option>
        <option>Endurance</option>
        <option>Stock Car</option>
      </select>

      <label for="piloto">Piloto</label>
      <input type="text" name="piloto" id="piloto" required>

      <h3>🏎️ Miniatura</h3>

      <label for="marca">Marca</label>
      <input type="text" name="marca" id="marca" required>

      <label for="codigo">Código</label>
      <input type="text" name="codigo" id="codigo" required>

      <label for="fabricante">Fabricante</label>
      <input type="text" name="fabricante" id="fabricante" required>

      <h3>🖼️ Fotos (até 3 imagens)</h3>
      <input type="file" name="fotos[]" id="fotos" accept="image/*" multiple required>
      <div class="preview" id="preview"></div>

      <button type="submit">Salvar Cadastro</button>
    </form>

    <footer>© 2025 RSP Diecast • Coleção Racing</footer>
  </div>

  <script>
    // Preview das imagens
    document.getElementById('fotos').addEventListener('change', function(event) {
      const preview = document.getElementById('preview');
      preview.innerHTML = '';
      const files = event.target.files;

      for (let i = 0; i < Math.min(files.length, 3); i++) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          preview.appendChild(img);
        };
        reader.readAsDataURL(files[i]);
      }
    });
  </script>

</body>
</html>
