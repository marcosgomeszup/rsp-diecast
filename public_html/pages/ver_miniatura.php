<?php
// Se quiser debugar, descomente as linhas abaixo:
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// ===================================
// LOGIN OBRIGATÓRIO
// ===================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// ===================================
// CONEXÃO COM O BANCO
// ===================================
$servername = "localhost";
$username   = "rspdiecast_usrmaster";
$password   = "X7OjyzhHH2";
$database   = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ===================================
// OBTÉM ID DA MINIATURA
// ===================================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: listar_carros.php");
    exit;
}

// ===================================
// HELPER: MESMA LÓGICA DO foto_principal()
// (só que para UMA foto em vez do JSON todo)
// ===================================
function resolver_foto($foto) {
    if (!$foto) return null;

    $foto = trim($foto, " \t\n\r\0\x0B\"'");
    if ($foto === '') return null;

    // tenta o caminho exatamente como veio do banco
    $srcWeb  = $foto;
    $absPath = __DIR__ . '/' . ltrim($srcWeb, '/');

    if (file_exists($absPath)) {
        return $srcWeb;     // exemplo: "../uploads/xxx.jpg" ou "uploads/xxx.jpg"
    }

    // se começar com ../, testa a versão sem ../ (igual index faz)
    if (strpos($srcWeb, '../') === 0) {
        $alt = substr($srcWeb, 3); // remove "../"
        $absAlt = __DIR__ . '/' . ltrim($alt, '/');
        if (file_exists($absAlt)) {
            return $alt;    // exemplo: "uploads/xxx.jpg"
        }
    }

    // se não achar, devolve null (sem imagem)
    return null;
}

// ===================================
// CARREGA DADOS DA MINIATURA
// ===================================
$erroCarro = '';
$carro     = null;

$sql = "
SELECT 
  c.*,
  cat.nome AS categoria_nome,
  e.nome   AS equipe_nome,
  p.nome   AS piloto_nome,
  m.nome   AS marca_nome,
  f.nome   AS fabricante_nome,
  c.escala AS escala_nome
FROM carros c
LEFT JOIN categorias  cat ON cat.id = c.categoria_id
LEFT JOIN equipes     e   ON e.id   = c.equipe_id
LEFT JOIN pilotos     p   ON p.id   = c.piloto_id
LEFT JOIN marcas      m   ON m.id   = c.marca_id
LEFT JOIN fabricantes f   ON f.id   = c.fabricante_id
WHERE c.id = $id
LIMIT 1
";

$result = $conn->query($sql);
if ($result === false) {
    $erroCarro = "Erro na consulta com JOIN: " . $conn->error;
    $sqlSimple = "SELECT * FROM carros WHERE id = $id LIMIT 1";
    $result2   = $conn->query($sqlSimple);
    if ($result2 && $result2->num_rows > 0) {
        $carro = $result2->fetch_assoc();
    }
} else {
    if ($result->num_rows > 0) {
        $carro = $result->fetch_assoc();
    }
}

$conn->close();

// ===================================
// FOTOS (JSON -> array) + PRINCIPAL
// ===================================
$fotos = [];
if ($carro && !empty($carro['fotos'])) {
    $tmp = json_decode($carro['fotos'], true);
    if (is_array($tmp)) {
        $fotos = $tmp;
    }
}

$srcPrincipal = null;
if (!empty($fotos)) {
    foreach ($fotos as $f) {
        $res = resolver_foto($f);
        if ($res !== null) {
            $srcPrincipal = $res;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Miniatura #<?= htmlspecialchars($id) ?> | RSP Diecast</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root {
    --azul-escuro:#00205B;
    --azul-claro:#00AEEF;
    --branco:#FFFFFF;
    --cinza:#CFCFCF;
    --vermelho:#FF6666;
  }
  *{box-sizing:border-box;font-family:"Montserrat",sans-serif}

  body{
    margin:0;
    background:var(--azul-escuro);
    color:var(--branco);
    display:flex;
    min-height:100vh;
  }

  .sidebar{
    width:220px;
    background:#001B48;
    border-right:2px solid var(--azul-claro);
    display:flex;
    flex-direction:column;
    padding:20px 0;
    position:fixed;
    top:0;left:0;bottom:0;
  }
  .sidebar a{
    color:var(--branco);
    text-decoration:none;
    font-weight:bold;
    padding:12px 25px;
    transition:.2s;
    display:block;
  }
  .sidebar a:hover{
    background:var(--azul-claro);
    color:var(--azul-escuro);
  }
  .sidebar a.logout{color:var(--vermelho);}

  .content{
    margin-left:240px;
    padding:30px;
    flex:1;
  }

  h1{
    margin-top:0;
    color:var(--azul-claro);
  }

  .card{
    max-width:900px;
    margin:0 auto;
    background:rgba(255,255,255,.05);
    border:1px solid var(--azul-claro);
    border-radius:12px;
    padding:20px 25px;
  }

  .topo{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    align-items:flex-start;
  }

  .foto-principal{
    width:320px;
    max-width:100%;
    border-radius:10px;
    object-fit:cover;
    background:#0a1e4c;
    cursor:zoom-in;
  }

  .dados{
    flex:1;
    min-width:220px;
  }

  .dados h2{
    margin:0 0 10px 0;
  }

  .linha{
    margin:4px 0;
    font-size:.95rem;
  }

  .miniaturas{
    margin-top:15px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
  }

  .miniaturas img{
    width:90px;
    height:70px;
    border-radius:8px;
    object-fit:cover;
    cursor:zoom-in;
    border:2px solid transparent;
  }
  .miniaturas img:hover{border-color:var(--azul-claro);}

  .comentario{
    margin-top:15px;
    background:rgba(255,255,255,.05);
    border-radius:8px;
    padding:10px 12px;
    font-size:.95rem;
    color:var(--cinza);
  }

  .acoes{
    margin-top:20px;
  }

  .acoes a{
    display:inline-block;
    margin-right:10px;
    text-decoration:none;
    font-weight:bold;
    padding:8px 14px;
    border-radius:8px;
  }
  .btn-voltar{background:var(--azul-claro);color:var(--branco);}
  .btn-editar{background:transparent;border:1px solid var(--azul-claro);color:var(--azul-claro);}
  .btn-lista{background:transparent;border:1px solid var(--cinza);color:var(--cinza);}

  /* Overlay zoom imagem com navegação */
  .overlay{
    position:fixed;
    display:none;
    top:0;left:0;
    width:100%;height:100%;
    background:rgba(0,0,0,0.9);
    justify-content:center;
    align-items:center;
    z-index:9999;
  }

  .overlay-content{
    display:flex;
    align-items:center;
    gap:25px;
  }

  .overlay img{
    max-width:90%;
    max-height:90%;
    border-radius:10px;
    box-shadow:0 0 20px rgba(255,255,255,.3);
  }

  .nav-btn{
    width:55px;height:55px;
    border-radius:50%;
    background:rgba(255,255,255,0.1);
    border:2px solid var(--branco);
    color:var(--branco);
    font-size:2rem;
    cursor:pointer;
    user-select:none;
    display:flex;
    align-items:center;
    justify-content:center;
  }
  .nav-btn:hover{
    background:rgba(255,255,255,0.3);
  }

  .msg-erro{
    max-width:900px;
    margin:20px auto;
    padding:12px 16px;
    border-radius:8px;
    background:rgba(255,102,102,0.1);
    border:1px solid #FF6666;
    color:#FFB3B3;
    font-size:.9rem;
  }

  @media (max-width:700px){
    .content{margin-left:0;padding:15px;}
    .sidebar{display:none;}
    .card{padding:15px;}
  }
</style>
</head>
<body>

<div class="sidebar">
  <a href="index.php">🏠 Início</a>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>
  <a href="logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">
  <h1>Miniatura #<?= htmlspecialchars($id) ?></h1>

  <?php if ($erroCarro): ?>
    <div class="msg-erro">
      <?= htmlspecialchars($erroCarro) ?>
    </div>
  <?php endif; ?>

  <?php if (!$carro): ?>
    <div class="msg-erro">
      Nenhuma miniatura encontrada com este ID.
    </div>
  <?php else: ?>

  <div class="card">
    <div class="topo">
      <div>
        <?php if ($srcPrincipal): ?>
          <img src="<?= htmlspecialchars($srcPrincipal) ?>"
               alt="Miniatura"
               class="foto-principal foto-zoom"
               onclick="abrirOverlay(this)">
        <?php else: ?>
          <div style="width:320px;height:200px;background:#0a1e4c;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#CFCFCF;">
            Sem imagem
          </div>
        <?php endif; ?>
      </div>

      <div class="dados">
        <h2><?= htmlspecialchars($carro['modelo'] ?? '') ?></h2>
        <div class="linha"><strong>Ano:</strong> <?= htmlspecialchars($carro['ano'] ?? '') ?></div>
        <div class="linha"><strong>Escala:</strong> <?= htmlspecialchars($carro['escala_nome'] ?? '') ?></div>
        <div class="linha"><strong>Código:</strong> <?= htmlspecialchars($carro['codigo'] ?? '') ?></div>
        <div class="linha"><strong>Categoria:</strong> <?= htmlspecialchars($carro['categoria_nome'] ?? '') ?></div>
        <div class="linha"><strong>Equipe:</strong> <?= htmlspecialchars($carro['equipe_nome'] ?? '') ?></div>
        <div class="linha"><strong>Piloto:</strong> <?= htmlspecialchars($carro['piloto_nome'] ?? '') ?></div>
        <div class="linha"><strong>Marca:</strong> <?= htmlspecialchars($carro['marca_nome'] ?? '') ?></div>
        <div class="linha"><strong>Fabricante:</strong> <?= htmlspecialchars($carro['fabricante_nome'] ?? '') ?></div>
      </div>
    </div>

    <?php if (!empty($fotos)): ?>
      <div class="miniaturas">
        <?php foreach ($fotos as $f): ?>
          <?php $resolved = resolver_foto($f); ?>
          <?php if ($resolved !== null): ?>
            <img src="<?= htmlspecialchars($resolved) ?>"
                 alt="Miniatura"
                 class="foto-zoom"
                 onclick="abrirOverlay(this)">
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($carro['comentario'])): ?>
      <div class="comentario">
        <?= nl2br(htmlspecialchars($carro['comentario'])) ?>
      </div>
    <?php endif; ?>

    <div class="acoes">
      <a href="index.php" class="btn-voltar">⬅ Início</a>
      <a href="listar_carros.php" class="btn-lista">📋 Ver listagem</a>
      <a href="editar_carro.php?id=<?= (int)$carro['id'] ?>" class="btn-editar">✏️ Editar</a>
    </div>
  </div>

  <?php endif; ?>
</div>

<!-- Overlay para zoom de imagem com setas -->
<div class="overlay" id="overlay-imagem" onclick="fecharOverlay(event)">
  <div class="overlay-content">
    <button class="nav-btn" onclick="fotoAnterior(event)">&#10094;</button>
    <img id="overlay-img" src="">
    <button class="nav-btn" onclick="proximaFoto(event)">&#10095;</button>
  </div>
</div>

<script>
let listaAtual = [];
let indiceAtual = 0;

function montarLista() {
  listaAtual = Array.from(document.querySelectorAll('.foto-zoom'));
}

function abrirOverlay(imgEl) {
  if (!listaAtual.length) montarLista();

  const overlay = document.getElementById('overlay-imagem');
  const img = document.getElementById('overlay-img');

  indiceAtual = listaAtual.indexOf(imgEl);
  if (indiceAtual < 0) indiceAtual = 0;

  img.src = imgEl.src;
  overlay.style.display = 'flex';
}

function atualizarOverlay() {
  if (!listaAtual.length) return;
  document.getElementById('overlay-img').src = listaAtual[indiceAtual].src;
}

function proximaFoto(event) {
  event.stopPropagation();
  if (!listaAtual.length) montarLista();
  if (!listaAtual.length) return;

  indiceAtual = (indiceAtual + 1) % listaAtual.length;
  atualizarOverlay();
}

function fotoAnterior(event) {
  event.stopPropagation();
  if (!listaAtual.length) montarLista();
  if (!listaAtual.length) return;

  indiceAtual = (indiceAtual - 1 + listaAtual.length) % listaAtual.length;
  atualizarOverlay();
}

function fecharOverlay(event) {
  const overlay = document.getElementById('overlay-imagem');
  if (!event || event.target === overlay) {
    overlay.style.display = 'none';
  }
}
</script>

</body>
</html>
