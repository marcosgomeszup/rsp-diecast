<?php
// ===================================
// CONFIGURAÇÃO DE CONEXÃO AO BANCO
// ===================================
$servername = "localhost";
$username = "rspdiecast_usrmaster";
$password = "X7OjyzhHH2";
$database = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ===================================
// VERIFICA SE FOI PASSADO UM ID
// ===================================
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do carro não informado.");
}

// ===================================
// CONSULTA O CARRO
// ===================================
$sql = "
SELECT * FROM carros WHERE id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$carro = $result->fetch_assoc();

if (!$carro) {
    die("Carro não encontrado.");
}

// ===================================
// CONSULTAS AUXILIARES PARA OS SELECTS
// ===================================
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
<title>Editar Miniatura | RSP Diecast</title>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --cinza: #CFCFCF;
  }
  * { box-sizing: border-box; font-family: "Montserrat", sans-serif; }
  body {
    background: var(--azul-escuro);
    color: var(--branco);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
  }
  h1 { color: var(--azul-claro); margin-bottom: 20px; }
  form {
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 12px;
    max-width: 700px;
    width: 100%;
  }
  label { display: block; margin-top: 15px; color: var(--azul-claro); }
  input, select {
    width: 100%; padding: 10px; margin-top: 5px;
    border-radius: 8px; border: none; background: #fff; color: #000;
  }
  .upload { display: flex; gap: 10px; margin-top: 10px; }
  button {
    background: var(--azul-claro); color: var(--branco);
    border: none; padding: 12px 20px; border-radius: 8px;
    cursor: pointer; margin-top: 20px;
  }
  button:hover { background: #007bbd; }
</style>
</head>
<body>

<h1>Editar Miniatura</h1>

<form action="salvar_edicao.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $carro['id'] ?>">

  <label for="ano">Ano</label>
  <input type="number" id="ano" name="ano" value="<?= htmlspecialchars($carro['ano']) ?>" required>

  <label for="modelo">Modelo</label>
  <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($carro['modelo']) ?>" required>

  <label for="codigo">Código</label>
  <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($carro['codigo']) ?>" required>

  <label for="categoria">Categoria</label>
  <select id="categoria" name="categoria_id" required>
    <option value="">Selecione...</option>
    <?php while ($cat = $categorias->fetch_assoc()): ?>
      <option value="<?= $cat['id'] ?>" <?= $carro['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
        <?= $cat['nome'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label for="equipe">Equipe</label>
  <select id="equipe" name="equipe_id">
    <option value="">Selecione...</option>
    <?php while ($eq = $equipes->fetch_assoc()): ?>
      <option value="<?= $eq['id'] ?>" <?= $carro['equipe_id'] == $eq['id'] ? 'selected' : '' ?>>
        <?= $eq['nome'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label for="piloto">Piloto</label>
  <select id="piloto" name="piloto_id">
    <option value="">Selecione...</option>
    <?php while ($p = $pilotos->fetch_assoc()): ?>
      <option value="<?= $p['id'] ?>" <?= $carro['piloto_id'] == $p['id'] ? 'selected' : '' ?>>
        <?= $p['nome'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label for="marca">Marca</label>
  <select id="marca" name="marca_id">
    <option value="">Selecione...</option>
    <?php while ($m = $marcas->fetch_assoc()): ?>
      <option value="<?= $m['id'] ?>" <?= $carro['marca_id'] == $m['id'] ? 'selected' : '' ?>>
        <?= $m['nome'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label for="fabricante">Fabricante</label>
  <select id="fabricante" name="fabricante_id">
    <option value="">Selecione...</option>
    <?php while ($f = $fabricantes->fetch_assoc()): ?>
      <option value="<?= $f['id'] ?>" <?= $carro['fabricante_id'] == $f['id'] ? 'selected' : '' ?>>
        <?= $f['nome'] ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label>Substituir fotos (opcional, até 3)</label>
  <div class="upload">
    <input type="file" name="foto1" accept="image/*">
    <input type="file" name="foto2" accept="image/*">
    <input type="file" name="foto3" accept="image/*">
  </div>

  <button type="submit">Salvar Alterações</button>
</form>

<a href="listar_carros.php" style="color: var(--azul-claro); margin-top: 15px; text-decoration: none;">← Voltar à listagem</a>

</body>
</html>

<?php $conn->close(); ?>
