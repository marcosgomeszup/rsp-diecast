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
    header("Location: /index.php");
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
        $alt    = substr($srcWeb, 3); // remove "../"
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
    --azul-escuro: #001433;
    --azul-escuro-alt: #000d24;
    --azul-claro: #00AEEF;
    --azul-neon: #28CFFF;
    --azul-claro-soft: #4FC3F7;
    --branco: #FFFFFF;
    --cinza: #CFCFCF;
    --cinza-escuro: #1A1F33;
    --vermelho: #FF6666;
    --bg-body: radial-gradient(circle at top center, #00224d 0%, #00112c 40%, #000814 100%);
    --shadow-card: 0 12px 25px rgba(0,0,0,0.65);
    --radius-card: 14px;
  }

  *{box-sizing:border-box;font-family:"Montserrat",sans-serif}

  body{
    margin:0;
    background:var(--bg-body);
    color:var(--branco);
    min-height:100vh;
  }

  /* LAYOUT GERAL */
  .layout{
    display:flex;
    min-height:100vh;
  }

  /* SIDEBAR */
  .sidebar{
    width:260px;
    background:linear-gradient(180deg,#020d24 0%,#001226 100%);
    border-right:1px solid rgba(40,207,255,0.7);
    padding:24px 18px 18px;
    display:flex;
    flex-direction:column;
    gap:18px;
    box-shadow:0 0 18px rgba(0,0,0,.7);
  }

  .brand{
    display:flex;
    flex-direction:column;
    gap:2px;
  }

  .brand-title{
    color:var(--azul-neon);
    font-weight:800;
    font-size:1.3rem;
    letter-spacing:.08em;
    text-transform:uppercase;
  }

  .brand-subtitle{
    color:var(--azul-claro-soft);
    font-size:.8rem;
    opacity:.85;
  }

  .nav-section-title{
    margin-top:10px;
    font-size:.7rem;
    text-transform:uppercase;
    letter-spacing:.12em;
    color:rgba(255,255,255,.55);
  }

  .nav{
    display:flex;
    flex-direction:column;
    margin-top:4px;
    gap:4px;
  }

  .nav-link{
    display:flex;
    align-items:center;
    gap:10px;
    padding:9px 11px;
    border-radius:10px;
    color:var(--branco);
    text-decoration:none;
    font-size:.9rem;
    font-weight:600;
    opacity:.9;
    transition:background .18s ease, transform .18s ease, opacity .18s ease, box-shadow .18s ease;
  }

  .nav-link span.icon{font-size:1.05rem;}

  .nav-link:hover{
    background:rgba(40,207,255,0.18);
    transform:translateX(2px);
    opacity:1;
    box-shadow:0 0 12px rgba(40,207,255,0.4);
  }

  .nav-link.active{
    background:linear-gradient(90deg,var(--azul-neon) 0%,#6fe0ff 100%);
    color:var(--azul-escuro);
    box-shadow:0 0 24px rgba(40,207,255,0.75);
  }

  .nav-link.logout{
    color:#FFB3B3;
  }

  .nav-link.logout:hover{
    background:rgba(255,102,102,0.18);
    box-shadow:0 0 12px rgba(255,102,102,0.4);
  }

  .sidebar-footer{
    margin-top:auto;
    padding-top:10px;
    border-top:1px solid rgba(255,255,255,0.08);
    font-size:.75rem;
    color:rgba(255,255,255,.55);
  }

  .sidebar-footer strong{color:var(--azul-claro-soft);}

  /* ÁREA PRINCIPAL */
  .main-area{
    flex:1;
    display:flex;
    flex-direction:column;
  }

  /* TOPBAR */
  .topbar{
    height:64px;
    padding:0 26px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    border-bottom:1px solid rgba(255,255,255,0.06);
    background:radial-gradient(circle at top left,rgba(40,207,255,.22) 0,rgba(0,0,0,.65) 55%,rgba(0,0,0,.9) 100%);
    backdrop-filter:blur(14px);
    position:sticky;
    top:0;
    z-index:5;
  }

  .topbar-left{display:flex;flex-direction:column;}

  .topbar-title{
    font-size:1.05rem;
    font-weight:600;
    color:var(--branco);
  }

  .topbar-subtitle{
    font-size:.8rem;
    color:rgba(255,255,255,.7);
  }

  .topbar-right{
    display:flex;
    align-items:center;
    gap:12px;
  }

  .user-pill{
    padding:6px 12px;
    border-radius:999px;
    background:rgba(0,0,0,.45);
    border:1px solid rgba(255,255,255,.16);
    font-size:.8rem;
    display:flex;
    align-items:center;
    gap:8px;
  }

  .user-pill-avatar{
    width:24px;height:24px;
    border-radius:50%;
    background:radial-gradient(circle at 30% 20%,#fff 0,#00AEEF 40%,#00205B 100%);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:.8rem;
    font-weight:700;
  }

  .btn{
    border:none;
    border-radius:999px;
    padding:7px 14px;
    font-size:.8rem;
    font-weight:600;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:6px;
    text-decoration:none;
    transition:transform .15s ease, box-shadow .15s ease, opacity .15s ease;
  }

  .btn-outline{
    background:transparent;
    border:1px solid rgba(255,255,255,.25);
    color:var(--branco);
  }

  .btn-outline:hover{
    opacity:.85;
    transform:translateY(-1px);
  }

  .btn-primary{
    background:linear-gradient(90deg,var(--azul-neon) 0%,#6fe0ff 100%);
    color:var(--azul-escuro);
    box-shadow:0 6px 16px rgba(40,207,255,0.5);
  }

  .btn-primary:hover{
    transform:translateY(-1px);
    box-shadow:0 9px 22px rgba(40,207,255,0.75);
  }

  .btn-secondary{
    background:transparent;
    border:1px solid var(--azul-claro-soft);
    color:var(--azul-claro-soft);
  }

  .btn-secondary:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(79,195,247,0.4);
  }

  .content{
    flex:1;
    padding:22px 26px 26px;
  }

  .page-title{
    margin:0 0 10px 0;
    font-size:1.4rem;
    color:var(--azul-claro-soft);
  }

  .page-subtitle{
    font-size:.85rem;
    color:rgba(255,255,255,.7);
    margin-bottom:18px;
  }

  .msg-erro{
    max-width:900px;
    margin:0 auto 20px auto;
    padding:12px 16px;
    border-radius:8px;
    background:rgba(255,102,102,0.1);
    border:1px solid #FF6666;
    color:#FFB3B3;
    font-size:.9rem;
  }

  /* CARD PRINCIPAL */
  .card{
    max-width:1000px;
    margin:0 auto;
    background:radial-gradient(circle at top,#07173a 0,#01040d 56%,#000000 100%);
    border-radius:var(--radius-card);
    padding:20px 25px;
    border:1px solid rgba(40,207,255,0.55);
    box-shadow:var(--shadow-card),0 0 18px rgba(40,207,255,0.22);
  }

  .topo{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    align-items:flex-start;
  }

  .foto-principal{
    width:340px;
    max-width:100%;
    border-radius:10px;
    object-fit:cover;
    background:#0a1e4c;
    cursor:zoom-in;
    border:1px solid rgba(0,0,0,.5);
  }

  .dados{
    flex:1;
    min-width:230px;
  }

  .dados h2{
    margin:0 0 10px 0;
  }

  .linha{
    margin:4px 0;
    font-size:.95rem;
  }

  .linha strong{
    color:var(--azul-claro-soft);
  }

  .miniaturas{
    margin-top:18px;
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
    background:#0a1e4c;
  }
  .miniaturas img:hover{border-color:var(--azul-claro-soft);}

  .comentario{
    margin-top:18px;
    background:rgba(255,255,255,.03);
    border-radius:8px;
    padding:10px 12px;
    font-size:.95rem;
    color:var(--cinza);
  }

  .acoes{
    margin-top:20px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
  }

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

  @media (max-width:960px){
    .layout{flex-direction:column;}
    .sidebar{
      width:100%;
      flex-direction:row;
      align-items:center;
      justify-content:space-between;
      padding:14px 18px;
      border-right:none;
      border-bottom:1px solid rgba(40,207,255,.7);
      gap:10px;
    }
    .brand{max-width:50%;}
    .nav-section-title{display:none;}
    .nav{flex-direction:row;flex-wrap:wrap;justify-content:flex-end;}
    .nav-link{font-size:.8rem;padding:7px 9px;}
    .sidebar-footer{display:none;}
  }

  @media (max-width:640px){
    .content{padding:18px 16px 22px;}
    .topo{flex-direction:column;}
    .foto-principal{width:100%;}
  }
</style>
</head>
<body>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-title">🏁 RSP Diecast</div>
      <div class="brand-subtitle">Racing Collection System</div>
    </div>

    <div>
      <div class="nav-section-title">Navegação</div>
      <nav class="nav">
        <a href="index.php" class="nav-link">
          <span class="icon">🏠</span>
          <span>Início</span>
        </a>
        <a href="dashboard.php" class="nav-link">
          <span class="icon">📊</span>
          <span>Dashboard</span>
        </a>
        <a href="cadastro.php" class="nav-link">
          <span class="icon">➕</span>
          <span>Cadastrar</span>
        </a>
        <a href="listar_carros.php" class="nav-link">
          <span class="icon">📋</span>
          <span>Listagem</span>
        </a>
        <a href="/logout.php" class="nav-link logout">
          <span class="icon">🚪</span>
          <span>Sair</span>
        </a>
      </nav>
    </div>

    <div class="sidebar-footer">
      Sessão ativa em<br>
      <strong>RSP Diecast Studio</strong>
    </div>
  </aside>

  <!-- ÁREA PRINCIPAL -->
  <div class="main-area">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="topbar-title">Detalhes da Miniatura</div>
        <div class="topbar-subtitle">Visualização completa do item selecionado</div>
      </div>
      <div class="topbar-right">
        <div class="user-pill">
          <div class="user-pill-avatar">
            <?= strtoupper(substr($_SESSION['usuario'] ?? 'U', 0, 1)) ?>
          </div>
          <span><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuário') ?></span>
        </div>
        <a href="/logout.php" class="btn btn-outline">Sair</a>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <main class="content">
      <h1 class="page-title">Miniatura #<?= htmlspecialchars($id) ?></h1>
      <p class="page-subtitle">
        Consulte os dados detalhados, imagens e informações adicionais desta miniatura.
      </p>

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

      <section class="card">
        <div class="topo">
          <div>
            <?php if ($srcPrincipal): ?>
              <img src="<?= htmlspecialchars($srcPrincipal) ?>"
                   alt="Miniatura"
                   class="foto-principal foto-zoom"
                   onclick="abrirOverlay(this)">
            <?php else: ?>
              <div style="width:340px;height:220px;background:#0a1e4c;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#CFCFCF;">
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
          <a href="index.php" class="btn btn-primary">⬅ Início</a>
          <a href="listar_carros.php" class="btn btn-secondary">📋 Ver listagem</a>
          <a href="editar_carro.php?id=<?= (int)$carro['id'] ?>" class="btn btn-outline">✏️ Editar</a>
        </div>
      </section>

      <?php endif; ?>
    </main>
  </div>
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
  const img     = document.getElementById('overlay-img');

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
