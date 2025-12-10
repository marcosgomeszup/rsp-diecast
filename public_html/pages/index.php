<?php
// ===================================
// RSP DIECAST - INDEX PRINCIPAL (PÓS-LOGIN)
// ===================================

session_start();

// 🔐 Proteção de acesso
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php"); // volta para o login na raiz
    exit;
}

// ==============================
// CONFIGURAÇÃO DO BANCO
// ==============================
$servername = "localhost";
$username   = "rspdiecast_usrmaster";
$password   = "X7OjyzhHH2";
$database   = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ==============================
// CONSULTAS
// ==============================

// Últimas 10 miniaturas
$ultimas = $conn->query("
    SELECT id, ano, modelo, codigo, fotos 
    FROM carros 
    ORDER BY id DESC 
    LIMIT 10
");

// Total de miniaturas
$totalRow = $conn->query("SELECT COUNT(*) AS t FROM carros")->fetch_assoc();
$total    = $totalRow['t'] ?? 0;

// ==============================
// Função para retornar a imagem principal
// ==============================
function foto_principal($jsonFotos) {
    $placeholder = "https://via.placeholder.com/250x150?text=Sem+Imagem";
    if (!$jsonFotos) return $placeholder;

    $arr = json_decode($jsonFotos, true);
    if (!is_array($arr) || empty($arr)) return $placeholder;

    $srcWeb = $arr[0];

    // remove barra inicial se houver
    $srcWeb = ltrim($srcWeb, '/');

    $absPath = __DIR__ . '/' . $srcWeb;

    if (file_exists($absPath)) {
        return $srcWeb; // caminho relativo a /pages
    }

    // Corrige se tiver "../uploads/..."
    if (strpos($srcWeb, '../') === 0) {
        $alt = substr($srcWeb, 3);
        if (file_exists(__DIR__ . '/' . $alt)) return $alt;
    }

    return $placeholder;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RSP Diecast | Sistema</title>

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
    --bg-body: radial-gradient(circle at top center, #00224d 0%, #00112c 40%, #000814 100%);
    --shadow-card: 0 12px 25px rgba(0,0,0,0.65);
    --radius-card: 14px;
  }

  * {
    box-sizing: border-box;
    font-family: "Montserrat", sans-serif;
  }

  body {
    margin: 0;
    min-height: 100vh;
    background: var(--bg-body);
    color: var(--branco);
  }

  /* LAYOUT GERAL */
  .layout {
    display: flex;
    min-height: 100vh;
  }

  /* SIDEBAR / MENU LATERAL */
  .sidebar {
    width: 260px;
    background: linear-gradient(180deg, #020d24 0%, #001226 100%);
    border-right: 1px solid rgba(40, 207, 255, 0.7);
    padding: 24px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    box-shadow: 0 0 18px rgba(0, 0, 0, .7);
  }

  .brand {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .brand-title {
    color: var(--azul-neon);
    font-weight: 800;
    font-size: 1.3rem;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .brand-subtitle {
    color: var(--azul-claro-soft);
    font-size: .8rem;
    opacity: .85;
  }

  .nav-section-title {
    margin-top: 10px;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: rgba(255,255,255,.55);
  }

  .nav {
    display: flex;
    flex-direction: column;
    margin-top: 4px;
    gap: 4px;
  }

  .nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 11px;
    border-radius: 10px;
    color: var(--branco);
    text-decoration: none;
    font-size: .9rem;
    font-weight: 600;
    opacity: .9;
    transition: background .18s ease, transform .18s ease, opacity .18s ease, box-shadow .18s ease;
  }

  .nav-link span.icon {
    font-size: 1.05rem;
  }

  .nav-link:hover {
    background: rgba(40, 207, 255, 0.18);
    transform: translateX(2px);
    opacity: 1;
    box-shadow: 0 0 12px rgba(40, 207, 255, 0.4);
  }

  .nav-link.active {
    background: linear-gradient(90deg, var(--azul-neon) 0%, #6fe0ff 100%);
    color: var(--azul-escuro);
    box-shadow: 0 0 24px rgba(40, 207, 255, 0.75);
  }

  .nav-link.active span.icon {
    filter: drop-shadow(0 0 4px rgba(0,0,0,.4));
  }

  .sidebar-footer {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.08);
    font-size: .75rem;
    color: rgba(255,255,255,.55);
  }

  .sidebar-footer strong {
    color: var(--azul-claro-soft);
  }

  /* CARD RESUMO */
  .sidebar-resumo {
    margin-top: 8px;
  }

  .sidebar-resumo-title {
    font-size: .85rem;
    color: var(--azul-claro-soft);
    margin-bottom: 6px;
    font-weight: 600;
  }

  .sidebar-resumo-card {
    border-radius: 12px;
    padding: 10px 12px;
    background: radial-gradient(circle at top, #06224d 0, #010716 100%);
    border: 1px solid rgba(40, 207, 255, 0.7);
    box-shadow:
      0 8px 18px rgba(0,0,0,0.85),
      0 0 18px rgba(40, 207, 255, 0.3);
  }

  .sidebar-resumo-label {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .14em;
    color: rgba(255,255,255,.6);
    margin-bottom: 4px;
  }

  .sidebar-resumo-value {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--branco);
  }

  /* ÁREA PRINCIPAL */
  .main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  /* TOPBAR */
  .topbar {
    height: 64px;
    padding: 0 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    background: radial-gradient(circle at top left, rgba(40, 207, 255, .22) 0, rgba(0,0,0,.65) 55%, rgba(0,0,0,.9) 100%);
    backdrop-filter: blur(14px);
    position: sticky;
    top: 0;
    z-index: 5;
  }

  .topbar-left {
    display: flex;
    flex-direction: column;
  }

  .topbar-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--branco);
  }

  .topbar-subtitle {
    font-size: .8rem;
    color: rgba(255,255,255,.7);
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .user-pill {
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(0,0,0,.45);
    border: 1px solid rgba(255,255,255,.16);
    font-size: .8rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .user-pill-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: radial-gradient(circle at 30% 20%, #fff 0, #00AEEF 40%, #00205B 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
    font-weight: 700;
  }

  .btn {
    border: none;
    border-radius: 999px;
    padding: 7px 14px;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
  }

  .btn-primary {
    background: linear-gradient(90deg, var(--azul-neon) 0%, #6fe0ff 100%);
    color: var(--azul-escuro);
    box-shadow: 0 6px 16px rgba(40, 207, 255, 0.5);
  }

  .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 9px 22px rgba(40, 207, 255, 0.75);
  }

  .btn-outline {
    background: transparent;
    border: 1px solid rgba(255,255,255,.25);
    color: var(--branco);
  }

  .btn-outline:hover {
    opacity: .85;
    transform: translateY(-1px);
  }

  /* CONTEÚDO */
  .content {
    flex: 1;
    padding: 22px 26px 26px;
  }

  .content-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }

  .page-title {
    margin: 0;
    font-size: 1.4rem;
    color: var(--azul-claro-soft);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .page-title span.emoji {
    font-size: 1.5rem;
  }

  .page-subtitle {
    font-size: .85rem;
    color: rgba(255,255,255,.7);
  }

  .grid {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
  }

  /* Link que envolve o card */
  .card-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  .card-miniatura {
    width: 260px;
    background: radial-gradient(circle at top, #07173a 0, #01040d 56%, #000000 100%);
    border-radius: var(--radius-card);
    padding: 13px;
    border: 1px solid rgba(40, 207, 255, 0.55);
    box-shadow:
      var(--shadow-card),
      0 0 18px rgba(40, 207, 255, 0.22);
    transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
    position: relative;
    overflow: hidden;
  }

  .card-miniatura::before {
    content: "";
    position: absolute;
    inset: -30%;
    background: radial-gradient(circle at top, rgba(40, 207, 255, 0.16), transparent 55%);
    opacity: 0;
    transition: opacity .25s ease;
  }

  .card-miniatura:hover {
    transform: translateY(-4px);
    border-color: rgba(40, 207, 255, 0.9);
    box-shadow:
      0 16px 32px rgba(0,0,0,0.8),
      0 0 26px rgba(40, 207, 255, 0.55);
  }

  .card-miniatura:hover::before {
    opacity: 1;
  }

  .thumb {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 10px;
    background: #0a1e4c;
    border: 1px solid rgba(0,0,0,.5);
  }

  .meta {
    font-size: .9rem;
    line-height: 1.4;
    position: relative;
    z-index: 1;
  }

  .meta strong {
    font-size: .98rem;
  }

  .muted {
    opacity: .85;
    font-size: .82rem;
  }

  .empty-state {
    padding: 28px;
    border-radius: 18px;
    border: 1px dashed rgba(255,255,255,.18);
    background: rgba(0,0,0,.35);
    max-width: 460px;
  }

  .empty-state-title {
    font-size: 1.05rem;
    margin-bottom: 6px;
    color: var(--azul-claro-soft);
  }

  .empty-state-text {
    font-size: .9rem;
    color: rgba(255,255,255,.78);
    margin-bottom: 12px;
  }

  /* RESPONSIVO */
  @media (max-width: 960px) {
    .layout {
      flex-direction: column;
    }

    .sidebar {
      width: 100%;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
      border-right: none;
      border-bottom: 1px solid rgba(40, 207, 255, .7);
      gap: 10px;
    }

    .brand {
      max-width: 50%;
    }

    .nav-section-title {
      display: none;
    }

    .nav {
      flex-direction: row;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .nav-link {
      font-size: .8rem;
      padding: 7px 9px;
    }

    .sidebar-resumo {
      display: none;
    }

    .sidebar-footer {
      display: none;
    }
  }

  @media (max-width: 640px) {
    .content-header {
      flex-direction: column;
      align-items: flex-start;
    }
    .grid {
      justify-content: center;
    }
    .card-miniatura {
      width: 100%;
      max-width: 320px;
    }
  }

  @media (max-width: 420px) {
    .topbar {
      padding-inline: 16px;
    }
    .content {
      padding-inline: 16px;
    }
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
        <a href="index.php" class="nav-link active">
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
      </nav>

      <div class="sidebar-resumo">
        <div class="sidebar-resumo-title">📈 Resumo rápido</div>
        <div class="sidebar-resumo-card">
          <div class="sidebar-resumo-label">Total de Miniaturas</div>
          <div class="sidebar-resumo-value"><?= (int)$total ?></div>
        </div>
      </div>
    </div>

    <div class="sidebar-footer">
      Sessão ativa em<br>
      <strong>RSP Diecast Studio</strong><br><br>
      <a href="/logout.php" class="btn btn-outline">🚪 Sair</a>
    </div>
  </aside>

  <!-- ÁREA PRINCIPAL -->
  <div class="main-area">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="topbar-title">Coleção de Miniaturas</div>
        <div class="topbar-subtitle">Visão geral das últimas adições ao acervo</div>
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
      <div class="content-header">
        <div>
          <h1 class="page-title">
            <span class="emoji">🧱</span>
            <span>Últimas 10 Miniaturas</span>
          </h1>
          <p class="page-subtitle">
            Acompanhe as miniaturas mais recentes cadastradas no sistema.
          </p>
        </div>

        <div>
          <a href="cadastro.php" class="btn btn-primary">
            ➕ Nova miniatura
          </a>
        </div>
      </div>

      <?php if ($ultimas && $ultimas->num_rows > 0): ?>
        <section class="grid">
          <?php while($r = $ultimas->fetch_assoc()): ?>
            <?php $src = foto_principal($r['fotos']); ?>

            <a class="card-link" href="ver_miniatura.php?id=<?= (int)$r['id'] ?>">
              <article class="card-miniatura">
                <img class="thumb"
                     src="<?= htmlspecialchars($src) ?>"
                     alt="Miniatura"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/250x150?text=Sem+Imagem';">
                <div class="meta">
                  <strong><?= htmlspecialchars($r['modelo'] ?: '—') ?></strong><br>
                  <span class="muted"><b>Ano:</b> <?= htmlspecialchars($r['ano'] ?: '—') ?></span><br>
                  <span class="muted"><b>Código:</b> <?= htmlspecialchars($r['codigo'] ?: '—') ?></span>
                </div>
              </article>
            </a>

          <?php endwhile; ?>
        </section>
      <?php else: ?>
        <section class="empty-state">
          <div class="empty-state-title">Nenhuma miniatura cadastrada ainda.</div>
          <div class="empty-state-text">
            Assim que você incluir os primeiros modelos, eles aparecerão aqui para facilitar o acesso rápido.
          </div>
          <a href="cadastro.php" class="btn btn-primary">
            ➕ Cadastrar primeira miniatura
          </a>
        </section>
      <?php endif; ?>
    </main>

  </div>
</div>

</body>
</html>
