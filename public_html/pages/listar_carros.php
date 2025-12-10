<?php
// ======================================================
// LOGIN OBRIGATÓRIO
// ======================================================
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

// ======================================================
// CONEXÃO COM O BANCO
// ======================================================
$servername = "localhost";
$username   = "rspdiecast_usrmaster";
$password   = "X7OjyzhHH2";
$database   = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Consulta simples na tabela carros
$sql    = "SELECT * FROM carros ORDER BY id DESC";
$result = $conn->query($sql);
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
  :root {
    --azul-escuro: #001433;
    --azul-escuro-alt: #000d24;
    --azul-claro: #00AEEF;
    --azul-neon: #28CFFF;
    --azul-soft: #6FE0FF;
    --branco: #FFFFFF;
    --cinza: #CFCFCF;
    --bg-body: radial-gradient(circle at top center, #00224d 0%, #00112c 40%, #000814 100%);
    --vermelho: #FF6666;
  }

  * { box-sizing:border-box; font-family:"Montserrat",sans-serif; }

  body{
    margin:0;
    min-height:100vh;
    background:var(--bg-body);
    color:var(--branco);
  }

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
    box-shadow:0 0 20px rgba(0,0,0,.7);
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
    color:var(--azul-soft);
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

  .nav-link:hover{
    background:rgba(40,207,255,0.18);
    transform:translateX(2px);
    opacity:1;
    box-shadow:0 0 12px rgba(40,207,255,0.4);
  }

  .nav-link.active{
    background:linear-gradient(90deg,var(--azul-neon),var(--azul-soft));
    color:var(--azul-escuro);
    box-shadow:0 0 22px rgba(40,207,255,0.7);
  }

  .nav-link.logout{
    color:#ff9c9c;
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
  .sidebar-footer strong{color:var(--azul-soft);}

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
    background:radial-gradient(circle at top left,rgba(40,207,255,.22) 0,rgba(0,0,0,.6) 55%,rgba(0,0,0,.9) 100%);
    backdrop-filter:blur(12px);
    position:sticky;
    top:0;
    z-index:5;
  }
  .topbar-left{
    display:flex;
    flex-direction:column;
  }
  .topbar-title{
    font-size:1.05rem;
    font-weight:600;
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
    width:24px;
    height:24px;
    border-radius:50%;
    background:radial-gradient(circle at 30% 20%,#fff 0,#00AEEF 45%,#00205B 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.8rem;
    font-weight:700;
  }

  .btn-outline{
    border-radius:999px;
    border:1px solid rgba(255,255,255,.3);
    background:transparent;
    color:var(--branco);
    padding:7px 14px;
    font-size:.8rem;
    font-weight:600;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:6px;
    transition:.15s;
  }
  .btn-outline:hover{
    opacity:.85;
    transform:translateY(-1px);
  }

  /* CONTEÚDO */
  .content{
    flex:1;
    padding:24px 26px 32px;
  }

  .page-title{
    margin:0 0 6px;
    font-size:1.6rem;
    color:var(--azul-soft);
  }
  .page-subtitle{
    margin:0 0 20px;
    font-size:.9rem;
    color:rgba(255,255,255,.75);
  }

  /* LISTA DE CARDS */
  .cards-grid{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
  }

  .card{
    width:320px;
    background:radial-gradient(circle at top,#07173a 0,#01040d 56%,#000000 100%);
    border-radius:14px;
    padding:16px 16px 14px;
    border:1px solid rgba(40,207,255,0.55);
    box-shadow:0 12px 24px rgba(0,0,0,.75);
    position:relative;
    transition:.16s transform,.16s box-shadow,.16s border-color;
  }

  .card:hover{
    transform:translateY(-3px);
    border-color:rgba(40,207,255,0.9);
    box-shadow:0 18px 32px rgba(0,0,0,.85),0 0 20px rgba(40,207,255,0.45);
  }

  /* Destaque para nova miniatura */
  .card.nova{
    box-shadow:0 0 0 2px var(--azul-neon),0 18px 32px rgba(0,0,0,.9),0 0 26px rgba(40,207,255,0.7);
  }
  .card.nova::before{
    content:"Nova!";
    position:absolute;
    top:8px;
    right:12px;
    background:var(--azul-neon);
    color:var(--azul-escuro);
    font-size:.7rem;
    font-weight:700;
    padding:3px 8px;
    border-radius:999px;
  }

  .card h2{
    margin:0 0 4px;
    font-size:1.05rem;
  }
  .card p{
    margin:2px 0;
    font-size:.85rem;
  }

  .fotos{
    display:flex;
    justify-content:flex-start;
    gap:8px;
    margin:10px 0 6px;
  }

  .fotos img{
    width:90px;
    height:70px;
    object-fit:cover;
    border-radius:8px;
    cursor:zoom-in;
    border:2px solid transparent;
    transition:.15s;
  }
  .fotos img:hover{
    border-color:var(--azul-soft);
  }

  .no-image{
    width:90px;
    height:70px;
    border-radius:8px;
    background:var(--cinza);
    color:#001433;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:.75rem;
  }

  .comentario{
    margin-top:6px;
    padding:8px 9px;
    border-radius:8px;
    background:rgba(255,255,255,0.05);
    font-size:.8rem;
    color:var(--cinza);
  }

  .acoes{
    margin-top:10px;
    font-size:.85rem;
  }

  .acoes a{
    text-decoration:none;
    font-weight:600;
    margin-right:10px;
  }
  .acoes a.editar{color:var(--azul-soft);}
  .acoes a.excluir{color:var(--vermelho);}

  .btn-nova{
    margin-top:24px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:11px 18px;
    border-radius:999px;
    background:linear-gradient(90deg,var(--azul-neon),var(--azul-soft));
    color:var(--azul-escuro);
    font-weight:700;
    font-size:.9rem;
    text-decoration:none;
    box-shadow:0 10px 24px rgba(40,207,255,0.45);
    transition:.15s;
  }
  .btn-nova:hover{
    transform:translateY(-2px);
    box-shadow:0 14px 30px rgba(40,207,255,0.7);
  }

  /* OVERLAY IMAGEM */
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
    gap:20px;
  }
  .overlay img{
    max-width:90vw;
    max-height:90vh;
    border-radius:10px;
    box-shadow:0 0 20px rgba(255,255,255,0.3);
  }
  .nav-btn{
    background:rgba(0,0,0,0.6);
    border:1px solid var(--branco);
    color:var(--branco);
    font-size:2rem;
    width:50px;
    height:50px;
    border-radius:50%;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    user-select:none;
  }
  .nav-btn:hover{
    background:rgba(255,255,255,0.2);
  }

  @media(max-width:960px){
    .layout{flex-direction:column;}
    .sidebar{
      width:100%;
      flex-direction:row;
      align-items:center;
      justify-content:space-between;
      padding:14px 18px;
      border-right:none;
      border-bottom:1px solid rgba(40,207,255,.7);
    }
    .nav-section-title{display:none;}
    .nav{flex-direction:row;flex-wrap:wrap;justify-content:flex-end;}
    .nav-link{font-size:.8rem;padding:7px 9px;}
    .sidebar-footer{display:none;}
  }

  @media(max-width:640px){
    .content{padding:18px 16px 26px;}
    .cards-grid{justify-content:center;}
    .card{width:100%;max-width:360px;}
  }
</style>
</head>
<body>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-title">🏁 RSP Diecast</div>
      <div class="brand-subtitle">Racing Collection</div>
    </div>

    <div>
      <div class="nav-section-title">Navegação</div>
      <nav class="nav">
        <a href="index.php" class="nav-link">
          <span>🏠</span><span>Início</span>
        </a>
        <a href="dashboard.php" class="nav-link">
          <span>📊</span><span>Dashboard</span>
        </a>
        <a href="cadastro.php" class="nav-link">
          <span>➕</span><span>Cadastrar</span>
        </a>
        <a href="listar_carros.php" class="nav-link active">
          <span>📋</span><span>Listagem</span>
        </a>
        <a href="/logout.php" class="nav-link logout">
          <span>🚪</span><span>Sair</span>
        </a>
      </nav>
    </div>

    <div class="sidebar-footer">
      Sessão ativa em<br><strong>RSP Diecast Studio</strong>
    </div>
  </aside>

  <!-- ÁREA PRINCIPAL -->
  <div class="main-area">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="topbar-title">Miniaturas cadastradas</div>
        <div class="topbar-subtitle">Visualize, edite ou exclua itens do acervo</div>
      </div>
      <div class="topbar-right">
        <div class="user-pill">
          <div class="user-pill-avatar">
            <?php
              $u = (string)($_SESSION['usuario'] ?? 'U');
              echo strtoupper(substr($u, 0, 1));
            ?>
          </div>
          <span><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuário'); ?></span>
        </div>
        <a href="/logout.php" class="btn-outline">Sair</a>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <main class="content">
      <h1 class="page-title">📋 Lista de miniaturas</h1>
      <p class="page-subtitle">
        Cada card representa uma miniatura cadastrada. Clique nas fotos para ver em destaque
        e use os botões de ação para editar ou remover.
      </p>

      <section class="cards-grid">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              $classeCard = 'card';
              if ($novoId && $novoId == (int)$row['id']) {
                  $classeCard .= ' nova';
              }
              $escalaExibida = !empty($row['escala']) ? $row['escala'] : '';
              $idCarro = (int)$row['id'];
            ?>
            <article class="<?= $classeCard ?>" id="carro-<?= $idCarro ?>">
              <h2><?= htmlspecialchars($row['modelo'] ?? '') ?></h2>
              <p><strong>Ano:</strong> <?= htmlspecialchars($row['ano'] ?? '') ?></p>
              <?php if ($escalaExibida !== ''): ?>
                <p><strong>Escala:</strong> <?= htmlspecialchars($escalaExibida) ?></p>
              <?php endif; ?>
              <p><strong>Código:</strong> <?= htmlspecialchars($row['codigo'] ?? '') ?></p>

              <?php
                $fotos = [];
                if (!empty($row['fotos'])) {
                    $tmp = json_decode($row['fotos'], true);
                    if (is_array($tmp)) $fotos = $tmp;
                }
              ?>

              <?php if (!empty($fotos)): ?>
                <div class="fotos">
                  <?php foreach ($fotos as $foto): ?>
                    <?php
                      $fotoLimpa = ltrim($foto, '/');
                      $caminhoServidor = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $fotoLimpa;
                      if (!file_exists($caminhoServidor)) {
                          echo '<div class="no-image">Sem Foto</div>';
                      } else {
                          $caminhoWeb = '/' . $fotoLimpa;
                          echo '<img src="' . htmlspecialchars($caminhoWeb) . '" alt="Miniatura" onclick="abrirOverlay(this)">';
                      }
                    ?>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="no-image">Sem Foto</div>
              <?php endif; ?>

              <?php if (!empty($row['comentario'])): ?>
                <div class="comentario"><?= nl2br(htmlspecialchars($row['comentario'])) ?></div>
              <?php endif; ?>

              <div class="acoes">
                <a href="editar_carro.php?id=<?= $idCarro ?>" class="editar">✏️ Editar</a>
                <a href="deletar_carro.php?id=<?= $idCarro ?>" class="excluir">🗑️ Excluir</a>
              </div>
            </article>
          <?php endwhile; ?>
        <?php else: ?>
          <p>Nenhuma miniatura cadastrada ainda.</p>
        <?php endif; ?>
      </section>

      <a href="cadastro.php" class="btn-nova">➕ Cadastrar nova miniatura</a>
    </main>
  </div>
</div>

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
  const overlay      = document.getElementById("overlay");
  const imgAmpliada  = document.getElementById("imgAmpliada");
  const containerFotos = imgEl.closest('.fotos');

  listaAtual  = Array.from(containerFotos.querySelectorAll('img'));
  indiceAtual = listaAtual.indexOf(imgEl);
  if (indiceAtual < 0) indiceAtual = 0;

  imgAmpliada.src = listaAtual[indiceAtual].src;
  overlay.style.display = "flex";
}

function atualizarImagemOverlay() {
  if (!listaAtual.length) return;
  document.getElementById("imgAmpliada").src = listaAtual[indiceAtual].src;
}

function proximaFoto(event) {
  event.stopPropagation();
  if (!listaAtual.length) return;
  indiceAtual = (indiceAtual + 1) % listaAtual.length;
  atualizarImagemOverlay();
}

function fotoAnterior(event) {
  event.stopPropagation();
  if (!listaAtual.length) return;
  indiceAtual = (indiceAtual - 1 + listaAtual.length) % listaAtual.length;
  atualizarImagemOverlay();
}

function fecharOverlay(event) {
  const overlay = document.getElementById("overlay");
  if (!event || event.target === overlay) {
    overlay.style.display = "none";
  }
}

// Se veio novo_id, rola suavemente até o card destacado
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
