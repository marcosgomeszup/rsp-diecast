<?php
$servername = "localhost";
$username = "rspdiecast_usrmaster";
$password = "X7OjyzhHH2";
$database = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$sql = "SELECT * FROM carros ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Listagem de Miniaturas | RSP Diecast</title>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --vermelho: #FF6666;
  }

  * { box-sizing: border-box; font-family: "Montserrat", sans-serif; }

  body {
    background: var(--azul-escuro);
    color: var(--branco);
    margin: 0;
    padding-top: 80px; /* espaço para o menu fixo */
  }

  /* ===== MENU SUPERIOR ===== */
  .menu {
    position: fixed;
    top: 0; left: 0;
    width: 100%;
    background: var(--azul-escuro);
    border-bottom: 2px solid var(--azul-claro);
    display: flex;
    justify-content: center;
    gap: 40px;
    padding: 15px 0;
    z-index: 1000;
  }

  .menu a {
    color: var(--branco);
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    transition: 0.2s;
  }

  .menu a:hover {
    color: var(--azul-claro);
  }

  h1 {
    text-align: center;
    color: var(--azul-claro);
    margin-bottom: 30px;
  }

  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 0 15px;
  }

  .card {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 12px;
    padding: 20px;
    width: 320px;
    transition: 0.3s;
    text-align: center;
  }

  .card:hover { background: rgba(255,255,255,0.1); transform: scale(1.02); }

  .fotos {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 10px 0;
  }

  .fotos img {
    width: 90px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.2s;
  }

  .fotos img:hover { border-color: var(--azul-claro); }

  .overlay {
    position: fixed;
    display: none;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.9);
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }

  .overlay img { max-width: 90%; max-height: 90%; border-radius: 10px; }

  .acoes { margin-top: 12px; }

  .acoes a {
    text-decoration: none;
    font-weight: bold;
    margin: 0 8px;
  }

  .acoes a.editar { color: var(--azul-claro); }
  .acoes a.excluir { color: var(--vermelho); }

  .voltar {
    display: block;
    text-align: center;
    background: var(--azul-claro);
    color: var(--branco);
    text-decoration: none;
    font-weight: bold;
    padding: 12px 20px;
    width: 260px;
    margin: 40px auto;
    border-radius: 10px;
    transition: 0.2s;
  }

  .voltar:hover { background: var(--branco); color: var(--azul-escuro); }
</style>
</head>
<body>

<!-- ===== MENU FIXO ===== -->
<nav class="menu">
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>
</nav>

<h1>Miniaturas Cadastradas</h1>

<div class="container">
<?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
      <h2><?= htmlspecialchars($row['modelo']) ?></h2>
      <p><strong>Ano:</strong> <?= htmlspecialchars($row['ano']) ?></p>
      <p><strong>Código:</strong> <?= htmlspecialchars($row['codigo']) ?></p>

      <?php $fotos = json_decode($row['fotos'], true);
      if (!empty($fotos)): ?>
      <div class="fotos">
        <?php foreach ($fotos as $foto): ?>
          <img src="../<?= htmlspecialchars($foto) ?>" alt="Miniatura" onclick="ampliarImagem(this)">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="acoes">
        <a href="editar_carro.php?id=<?= $row['id'] ?>" class="editar">✏️ Editar</a>
        <a href="deletar_carro.php?id=<?= $row['id'] ?>" class="excluir">🗑️ Excluir</a>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="text-align:center;">Nenhuma miniatura cadastrada ainda.</p>
<?php endif; ?>
</div>

<a href="cadastro.php" class="voltar">+ Cadastrar Nova Miniatura</a>

<div class="overlay" id="overlay" onclick="fecharOverlay()">
  <img id="imgAmpliada" src="">
</div>

<script>
function ampliarImagem(el) {
  const overlay = document.getElementById("overlay");
  const img = document.getElementById("imgAmpliada");
  img.src = el.src;
  overlay.style.display = "flex";
}
function fecharOverlay() {
  document.getElementById("overlay").style.display = "none";
}
</script>

</body>
</html>
