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

// Busca dados das tabelas auxiliares
$anos = $conn->query("SELECT ano FROM anos ORDER BY ano DESC");
$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
$equipes = $conn->query("SELECT id, nome FROM equipes ORDER BY nome ASC");
$pilotos = $conn->query("SELECT id, nome FROM pilotos ORDER BY nome ASC");
$marcas = $conn->query("SELECT id, nome FROM marcas ORDER BY nome ASC");
$fabricantes = $conn->query("SELECT id, nome FROM fabricantes ORDER BY nome ASC");
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
      padding-bottom: 80px;
    }

    h1 {
      text-align: center;
      color: var(--azul-claro);
      margin-top: 40px;
    }

    form {
      max-width: 900px;
      margin: 30px auto;
      background: rgba(255,255,255,0.05);
      padding: 25px 40px;
      border-radius: 15px;
      border: 1px solid var(--azul-claro);
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

    a.voltar {
      display: block;
      text-align: center;
      margin-top: 25px;
      color: var(--azul-claro);
      text-decoration: none;
      font-weight: bold;
    }

    a.voltar:hover { color: var(--branco); }
  </style>
</head>
<body>

<h1>Cadastro de Miniatura</h1>

<form action="salvar_carro.php" method="POST" enctype="multipart/form-data">

  <!-- BLOCO 1: DADOS DO CARRO -->
  <fieldset>
    <legend>🏎️ Dados do Carro</legend>

    <label for="ano">Ano</label>
    <select name="ano" id="ano" required>
      <option value="">Selecione o ano...</option>
      <?php while ($a = $anos->fetch_assoc()): ?>
        <option value="<?= $a['ano'] ?>"><?= $a['ano'] ?></option>
      <?php endwhile; ?>
    </select>

    <label for="modelo">Modelo</label>
    <input type="text" id="modelo" name="modelo" maxlength="255" required>

    <label for="categoria_id">Categoria</label>
    <select name="categoria_id" id="categoria_id" required>
      <option value="">Selecione...</option>
      <?php while ($c = $categorias->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="equipe_id">Equipe</label>
    <select name="equipe_id" id="equipe_id" required>
      <option value="">Selecione...</option>
      <?php while ($e = $equipes->fetch_assoc()): ?>
        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="piloto_id">Piloto</label>
    <select name="piloto_id" id="piloto_id" required>
      <option value="">Selecione...</option>
      <?php while ($p = $pilotos->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
      <?php endwhile; ?>
    </select>
  </fieldset>

  <!-- BLOCO 2: DADOS DA MINIATURA -->
  <fieldset>
    <legend>🧱 Dados da Miniatura</legend>

    <label for="codigo">Código</label>
    <input type="text" id="codigo" name="codigo" maxlength="50" required>

    <label for="escala">Escala</label>
    <input type="text" id="escala" name="escala" placeholder="Ex: 1:18" maxlength="10" required>

    <label for="marca_id">Marca</label>
    <select name="marca_id" id="marca_id" required>
      <option value="">Selecione...</option>
      <?php while ($m = $marcas->fetch_assoc()): ?>
        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="fabricante_id">Fabricante</label>
    <select name="fabricante_id" id="fabricante_id" required>
      <option value="">Selecione...</option>
      <?php while ($f = $fabricantes->fetch_assoc()): ?>
        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="comentario">Comentário</label>
    <textarea name="comentario" id="comentario" maxlength="1024" placeholder="Insira detalhes adicionais, curiosidades ou observações sobre a miniatura..."></textarea>

    <label>Fotos (até 3)</label>
    <div class="fotos">
      <input type="file" name="foto1" accept="image/*">
      <input type="file" name="foto2" accept="image/*">
      <input type="file" name="foto3" accept="image/*">
    </div>
  </fieldset>

  <button type="submit">Salvar Miniatura</button>
  <a href="listar_carros.php" class="voltar">← Voltar à Listagem</a>
</form>

</body>
</html>
