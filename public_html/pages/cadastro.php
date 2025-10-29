<?php
// ==============================
// CONFIGURAÇÃO DE CONEXÃO MYSQL
// ==============================
$servername = "localhost";
$username = "rspdiecast_usrmaster";
$password = "X7OjyzhHH2";
$database = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// ==============================
// CONSULTAS PARA OS SELECTS
// ==============================
$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome");
$equipes = $conn->query("SELECT id, nome FROM equipes ORDER BY nome");
$pilotos = $conn->query("SELECT id, nome FROM pilotos ORDER BY nome");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome");
$fabricantes = $conn->query("SELECT id, nome FROM fabricantes ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Miniaturas | RSP Diecast</title>

  <style>
    :root {
      --azul-escuro: #00205B;
      --azul-claro: #00AEEF;
      --branco: #FFFFFF;
      --cinza: #CFCFCF;
    }
    * {
      box-sizing: border-box;
      font-family: "Montserrat", sans-serif;
    }
    body {
      background-color: var(--azul-escuro);
      color: var(--branco);
      margin: 0;
      padding: 40px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    h1 {
      color: var(--azul-claro);
      margin-bottom: 20px;
    }
    form {
      background: rgba(255, 255, 255, 0.05);
      padding: 30px;
      border-radius: 12px;
      max-width: 700px;
      width: 100%;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
      color: var(--azul-claro);
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: none;
      background: #fff;
      color: #000;
    }
    .upload {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    button {
      background-color: var(--azul-claro);
      color: var(--branco);
      border: none;
      border-radius: 8px;
      padding: 12px 20px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 20px;
    }
    button:hover {
      background-color: #007bbd;
    }
  </style>
</head>
<body>

  <h1>Cadastro de Miniatura</h1>

  <form action="salvar_carro.php" method="POST" enctype="multipart/form-data">
 <label for="ano">Ano</label>
<select id="ano" name="ano" required>
  <option value="">Selecione o ano...</option>
  <?php
  // Garante que existe uma conexão com o banco ativa
  $servername = "localhost";
  $username = "rspdiecast_usrmaster";
  $password = "X7OjyzhHH2";
  $database = "rspdiecast_dbsystem";
  $conn = new mysqli($servername, $username, $password, $database);
  if ($conn->connect_error) {
      die("Erro de conexão: " . $conn->connect_error);
  }

  $anos = $conn->query("SELECT ano FROM anos ORDER BY ano DESC");
  while ($a = $anos->fetch_assoc()):
  ?>
    <option value="<?= $a['ano'] ?>"><?= $a['ano'] ?></option>
  <?php endwhile; ?>
</select>


    <label for="modelo">Modelo</label>
    <input type="text" id="modelo" name="modelo" required>

    <label for="codigo">Código</label>
    <input type="text" id="codigo" name="codigo" required>

    <label for="categoria">Categoria</label>
    <select id="categoria" name="categoria_id" required>
      <option value="">Selecione...</option>
      <?php while ($cat = $categorias->fetch_assoc()): ?>
        <option value="<?= $cat['id'] ?>"><?= $cat['nome'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="equipe">Equipe</label>
    <select id="equipe" name="equipe_id">
      <option value="">Selecione...</option>
      <?php while ($eq = $equipes->fetch_assoc()): ?>
        <option value="<?= $eq['id'] ?>"><?= $eq['nome'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="piloto">Piloto</label>
    <select id="piloto" name="piloto_id">
      <option value="">Selecione...</option>
      <?php while ($p = $pilotos->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="marca">Marca</label>
    <select id="marca" name="marca_id">
      <option value="">Selecione...</option>
      <?php while ($m = $marcas->fetch_assoc()): ?>
        <option value="<?= $m['id'] ?>"><?= $m['nome'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="fabricante">Fabricante</label>
    <select id="fabricante" name="fabricante_id">
      <option value="">Selecione...</option>
      <?php while ($f = $fabricantes->fetch_assoc()): ?>
        <option value="<?= $f['id'] ?>"><?= $f['nome'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Fotos (até 3 imagens)</label>
    <div class="upload">
      <input type="file" name="foto1" accept="image/*">
      <input type="file" name="foto2" accept="image/*">
      <input type="file" name="foto3" accept="image/*">
    </div>

    <button type="submit">Salvar Miniatura</button>
  </form>

</body>
</html>