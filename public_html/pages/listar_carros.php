<?php
$servername = "localhost";
$username = "rspdiecast_usrmaster";
$password = "X7OjyzhHH2";
$database = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

/**
 * Consulta simples, sem JOIN, usando apenas os campos da tabela carros.
 * Se futuramente você criar a coluna escala_id, aí podemos reativar o JOIN.
 */
$sql = "SELECT * FROM carros ORDER BY id DESC";
$result = $conn->query($sql);

// Se der erro na query, mostra pra gente ver o problema
if ($result === false) {
    die("Erro na consulta SQL: " . $conn->error);
}

// id da miniatura recém-cadastrada (vindo de salvar_carro.php)
$novoId = isset($_GET['novo_id']) ? (int)$_GET['novo_id'] : null;
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
    --cinza: #CFCFCF;
  }

  * { box-sizing: border-box; font-family: "Montserrat", sans-serif; }

  body {
    background: var(--azul-escuro);
    color: var(--branco);
    margin: 0;
    padding-top: 80px;
  }

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

  .menu a:hover { color: var(--azul-claro); }

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

  /* Destaque para a miniatura recém-cadastrada */
  .card.nova {
    box-shadow: 0 0 0 3px var(--azul-claro);
    background: rgba(0, 174, 239, 0.15);
    position: relative;
  }

  .card.nova::before {
    content: "Nova!";
    position: absolute;
    top: 8px;
    right: 12px;
    background: var(--azul-claro);
    color: var(--azul-escuro);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 999px;
  }

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
    cursor: zoom-in;
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

  .overlay-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .overlay img {
    max-width: 90vw;
    max-height: 90vh;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(255,255,255,0.3);
  }

  .overlay .nav-btn {
    background: rgba(0,0,0,0.6);
    border: 1px solid var(--branco);
    color: var(--branco);
    font-size: 2rem;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
  }

  .overlay .nav-btn:hover {
    background: rgba(255,255,255,0.2);
  }

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

  .no-image {
    width: 90px;
    height: 70px;
    background: var(--cinza);
    color: var(--azul-escuro);
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 0.8rem;
    border-radius: 8px;
  }

  .comentario {
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
    padding: 10px;
    font-size: 0.9rem;
    margin-top: 10px;
    color: var(--cinza);
  }
</style>
</head>
<body>

<nav class="menu">
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>
</nav>

<h1>Miniaturas Cadastradas</h1>

<div class="container">
<?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <?php
      // classe do card (normal ou destacado)
      $classeCard = 'card';
      if ($novoId && $novoId == $row['id']) {
          $classeCard .= ' nova';
      }

      // Escala: usa apenas o campo texto atual da tabela carros
      $escalaExibida = !empty($row['escala']) ? $row['escala'] : '';
    ?>
    <div class="<?= $classeCard ?>" id="carro-<?= $row['id'] ?>">
      <h2><?= htmlspecialchars($row['modelo']) ?></h2>
      <p><strong>Ano:</strong> <?= htmlspecialchars($row['ano']) ?></p>

      <?php if ($escalaExibida !== ''): ?>
        <p><strong>Escala:</strong> <?= htmlspecialchars($escalaExibida) ?></p>
      <?php endif; ?>

      <p><strong>Código:</strong> <?= htmlspecialchars($row['codigo']) ?></p>

      <?php 
      $fotos = json_decode($row['fotos'], true);
      if (!empty($fotos)): ?>
        <div class="fotos">
          <?php foreach ($fotos as $foto): 
            $caminhoFoto = '/' . htmlspecialchars($foto);
            $caminhoServidor = $_SERVER['DOCUMENT_ROOT'] . '/' . $foto;
            if (!file_exists($caminhoServidor)) {
              echo '<div class="no-image">Sem Foto</div>';
            } else {
              // AQUI: mudamos o onclick para passar o elemento, não só o src
              echo '<img src="' . $caminhoFoto . '" alt="Miniatura" onclick="abrirOverlay(this)">';
            }
          endforeach; ?>
        </div>
      <?php else: ?>
        <div class="no-image">Sem Foto</div>
      <?php endif; ?>

      <?php if (!empty($row['comentario'])): ?>
        <div class="comentario"><?= nl2br(htmlspecialchars($row['comentario'])) ?></div>
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

<!-- OVERLAY PARA ZOOM DE IMAGEM COM NAVEGAÇÃO -->
<div class="overlay" id="overlay" onclick="fecharOverlay(event)">
  <div class="overlay-content">
    <button class="nav-btn" onclick="fotoAnterior(event)">&#10094;</button>
    <img id="imgAmpliada" src="">
    <button class="nav-btn" onclick="proximaFoto(event)">&#10095;</button>
  </div>
</div>

<script>
let listaAtual = [];
let indiceAtual = 0;

function abrirOverlay(imgEl) {
  const overlay = document.getElementById("overlay");
  const imgAmpliada = document.getElementById("imgAmpliada");
  
  // pega todas as imagens do mesmo bloco .fotos
  const containerFotos = imgEl.closest('.fotos');
  listaAtual = Array.from(containerFotos.querySelectorAll('img'));
  indiceAtual = listaAtual.indexOf(imgEl);

  if (indiceAtual < 0) indiceAtual = 0;

  imgAmpliada.src = listaAtual[indiceAtual].src;
  overlay.style.display = "flex";
}

function atualizarImagemOverlay() {
  const imgAmpliada = document.getElementById("imgAmpliada");
  if (!listaAtual.length) return;
  imgAmpliada.src = listaAtual[indiceAtual].src;
}

function proximaFoto(event) {
  // impede que o clique feche o overlay
  event.stopPropagation();
  if (!listaAtual.length) return;
  indiceAtual = (indiceAtual + 1) % listaAtual.length;
  atualizarImagemOverlay();
}

function fotoAnterior(event) {
  // impede que o clique feche o overlay
  event.stopPropagation();
  if (!listaAtual.length) return;
  indiceAtual = (indiceAtual - 1 + listaAtual.length) % listaAtual.length;
  atualizarImagemOverlay();
}

function fecharOverlay(event) {
  const overlay = document.getElementById("overlay");
  // só fecha se o clique for no fundo escuro (não nos botões / imagem)
  if (!event || event.target === overlay) {
    overlay.style.display = "none";
  }
}

// Se veio um novo_id, rola suavemente até o card destacado
<?php if ($novoId): ?>
  window.addEventListener('load', function () {
    const el = document.getElementById('carro-<?= $novoId ?>');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });
<?php endif; ?>
</script>

</body>
</html>
