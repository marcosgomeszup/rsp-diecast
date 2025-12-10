<?php
// ===================================
// RSP DIECAST - INDEX PRINCIPAL
// ===================================

session_start();

// 🔐 Proteção de acesso
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // volta para o login
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
    margin: 0;
    background: var(--azul-escuro);
    color: var(--branco);
    display: flex;
    min-height: 100vh;
  }

  /* ===== MENU LATERAL ===== */
  .menu {
    width: 240px;
    background: #001B47;
    border-right: 2px solid var(--azul-claro);
    padding: 24px 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .logo {
    color: var(--azul-claro);
    font-weight: 800;
    font-size: 1.3rem;
    margin-bottom: 4px;
  }

  .sub {
    color: #89cfff;
    font-size: 0.9rem;
    margin-bottom: 18px;
  }

  .menu a {
    display: block;
    color: var(--branco);
    text-decoration: none;
    font-weight: 700;
    padding: 10px 12px;
    border-radius: 8px;
    transition: 0.2s;
  }

  .menu a:hover {
    background: var(--azul-claro);
    color: var(--azul-escuro);
  }

  /* ===== RESUMO ===== */
  .resumo {
    margin-top: 24px;
  }

  .resumo h3 {
    color: var(--azul-claro);
    margin: 0 0 8px 0;
    font-size: 1rem;
  }

  .resumo table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 10px;
    overflow: hidden;
  }

  .resumo th,
  .resumo td {
    border-bottom: 1px solid var(--azul-claro);
    padding: 10px 12px;
    text-align: left;
  }

  .resumo th {
    background: var(--azul-claro);
    color: var(--branco);
  }

  /* ===== BOTÃO LOGOUT ===== */
  .logout {
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid var(--azul-claro);
  }

  .logout a {
    color: #FF6666;
    font-weight: bold;
    text-decoration: none;
    display: block;
    padding: 10px 12px;
    border-radius: 8px;
    transition: 0.2s;
  }

  .logout a:hover {
    background: #FF6666;
    color: var(--branco);
  }

  /* ===== CONTEÚDO ===== */
  .content {
    flex: 1;
    padding: 28px;
  }

  h1 {
    margin: 0 0 18px 0;
    color: var(--azul-claro);
  }

  .grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
  }

  /* Link que envolve o card */
  .card-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  .card {
    width: 260px;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 12px;
    padding: 14px;
    transition: 0.2s;
    cursor: pointer;
  }

  .card:hover {
    transform: scale(1.02);
    background: rgba(255,255,255,0.08);
  }

  .thumb {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
    background: #0A1E4C;
  }

  .meta {
    font-size: 0.95rem;
    line-height: 1.35;
  }

  .muted {
    opacity: 0.85;
  }

  /* ===== RESPONSIVO ===== */
  @media (max-width: 900px) {
    .menu { width: 200px; }
    .content { margin-left: 200px; }
    .card { width: calc(50% - 10px); }
  }

  @media (max-width: 600px) {
    .menu { width: 180px; }
    .content { margin-left: 180px; }
    .card { width: 100%; }
  }
</style>
</head>
<body>

<!-- MENU LATERAL -->
<aside class="menu">
  <div class="logo">🏁 RSP Diecast</div>
  <div class="sub">Racing Collection</div>

  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>

  <div class="resumo">
    <h3>📈 Resumo</h3>
    <table>
      <tr><th>Total de Miniaturas</th></tr>
      <tr><td><strong><?= (int)$total ?></strong></td></tr>
    </table>
  </div>

  <div class="logout">
    <a href="logout.php">🚪 Sair</a>
  </div>
</aside>

<!-- CONTEÚDO PRINCIPAL -->
<main class="content">
  <h1>🧱 Últimas 10 Miniaturas</h1>

  <?php if ($ultimas && $ultimas->num_rows > 0): ?>
    <section class="grid">
      <?php while($r = $ultimas->fetch_assoc()): ?>
        <?php $src = foto_principal($r['fotos']); ?>

        <!-- LINK indo direto para a página da miniatura -->
        <a class="card-link" href="ver_miniatura.php?id=<?= (int)$r['id'] ?>">
          <article class="card">
            <img class="thumb" src="<?= htmlspecialchars($src) ?>" alt="Miniatura">
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
    <p>Nenhuma miniatura cadastrada ainda.</p>
  <?php endif; ?>
</main>

</body>
</html>
