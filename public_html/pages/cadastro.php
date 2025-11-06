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

// ===============================
// BUSCA DE DADOS AUXILIARES
// ===============================
$anos = $conn->query("SELECT ano FROM anos WHERE ano IS NOT NULL ORDER BY ano DESC");
$categorias = $conn->query("SELECT id, nome FROM categorias WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
$equipes = $conn->query("SELECT id, nome FROM equipes WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
$pilotos = $conn->query("SELECT id, nome FROM pilotos WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
$marcas = $conn->query("SELECT id, nome FROM marcas WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
$fabricantes = $conn->query("SELECT id, nome FROM fabricantes WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
$escalas = $conn->query("SELECT id, nome FROM escalas WHERE nome IS NOT NULL AND TRIM(nome) <> '' ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Miniatura | RSP Diecast</title>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --cinza: #CFCFCF;
    --vermelho: #FF6666;
  }

  * { box-sizing: border-box; font-family: "Montserrat", sans-serif; }

  body {
    background: var(--azul-escuro);
    color: var(--branco);
    margin: 0;
    display: flex;
    min-height: 100vh;
  }

  /* ===== MENU LATERAL ===== */
  .sidebar {
    width: 220px;
    background: #001B48;
    border-right: 2px solid var(--azul-claro);
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
  }

  .sidebar a {
    color: var(--branco);
    text-decoration: none;
    font-weight: bold;
    padding: 12px 25px;
    transition: 0.2s;
    display: block;
  }

  .sidebar a:hover {
    background: var(--azul-claro);
    color: var(--azul-escuro);
  }

  .content {
    margin-left: 240px;
    padding: 40px;
    flex: 1;
  }

  h1 {
    color: var(--azul-claro);
    text-align: left;
  }

  form {
    max-width: 900px;
    background: rgba(255,255,255,0.05);
    padding: 25px 40px;
    border-radius: 15px;
    border: 1px solid var(--azul-claro);
    margin-top: 20px;
  }

  fieldset {
    border: 1px solid var(--azul-claro);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
  }

  legend {
    color: var(--azul-claro);
    font-weight: bold;
    padding: 0 10px;
    font-size: 1.1rem;
  }

  label {
    display: block;
    margin-top: 10px;
    color: var(--branco);
    font-weight: 600;
  }

  input, select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid var(--cinza);
    background: rgba(255,255,255,0.1);
    color: var(--branco);
  }

  textarea {
    min-height: 100px;
    resize: vertical;
  }

  .select-wrapper { position: relative; width: 100%; }

  .fotos {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  input[type="file"] {
    border: none;
    background: none;
    color: var(--azul-claro);
  }

  button {
    background: var(--azul-claro);
    color: var(--branco);
    font-weight: bold;
    border: none;
    border-radius: 10px;
    padding: 15px 30px;
    cursor: pointer;
    margin-top: 20px;
    width: 100%;
    transition: 0.2s;
  }

  button:hover {
    background: var(--branco);
    color: var(--azul-escuro);
  }
</style>
</head>
<body>

<!-- MENU LATERAL -->
<div class="sidebar">
  <a href="index.php">🏠 Início</a>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>
</div>

<!-- CONTEÚDO PRINCIPAL -->
<div class="content">
  <h1>Cadastro de Miniatura</h1>

  <form action="salvar_carro.php" method="POST" enctype="multipart/form-data">
    <fieldset>
      <legend>🏎️ Dados do Carro</legend>

      <label for="ano">Ano</label>
      <div class="select-wrapper">
        <select name="ano" id="ano" required>
          <option value="">Selecione o ano...</option>
          <?php while ($a = $anos->fetch_assoc()): ?>
            <option value="<?= $a['ano'] ?>"><?= $a['ano'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="modelo">Modelo</label>
      <input type="text" id="modelo" name="modelo" maxlength="255" required>

      <label for="categoria_id">Categoria</label>
      <div class="select-wrapper">
        <select name="categoria_id" id="categoria_id" required>
          <option value="">Selecione...</option>
          <?php while ($c = $categorias->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="equipe_id">Equipe</label>
      <div class="select-wrapper">
        <select name="equipe_id" id="equipe_id" required>
          <option value="">Selecione...</option>
          <?php while ($e = $equipes->fetch_assoc()): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="piloto_id">Piloto</label>
      <div class="select-wrapper">
        <select name="piloto_id" id="piloto_id" required>
          <option value="">Selecione...</option>
          <?php while ($p = $pilotos->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
    </fieldset>

    <fieldset>
      <legend>🧱 Dados da Miniatura</legend>

      <label for="codigo">Código</label>
      <input type="text" id="codigo" name="codigo" maxlength="50" required>

      <label for="escala_id">Escala</label>
      <div class="select-wrapper">
        <select name="escala_id" id="escala_id" required>
          <option value="">Selecione...</option>
          <?php while ($e = $escalas->fetch_assoc()): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="marca_id">Marca</label>
      <div class="select-wrapper">
        <select name="marca_id" id="marca_id" required>
          <option value="">Selecione...</option>
          <?php while ($m = $marcas->fetch_assoc()): ?>
            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="fabricante_id">Fabricante</label>
      <div class="select-wrapper">
        <select name="fabricante_id" id="fabricante_id" required>
          <option value="">Selecione...</option>
          <?php while ($f = $fabricantes->fetch_assoc()): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <label for="comentario">Comentário</label>
      <textarea name="comentario" id="comentario" maxlength="1024" placeholder="Insira detalhes adicionais, curiosidades ou observações sobre a miniatura..."></textarea>

      <label>Fotos (até 3)</label>
      <div class="fotos">
        <input type="file" name="foto1" accept="image/*">
        <input type="file" name="foto2" accept="image/*">
        <input type="file" name="foto3" accept="image/*">
      </div>
    </fieldset>

    <button type="submit">💾 Salvar Miniatura</button>
  </form>
</div>

</body>
</html>
