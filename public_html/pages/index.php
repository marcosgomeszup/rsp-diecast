<?php
// ==============================
// DB
// ==============================
$servername = "localhost";
$username   = "rspdiecast_usrmaster";
$password   = "X7OjyzhHH2";
$database   = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

// Últimas 10
$ultimas = $conn->query("SELECT id, ano, modelo, codigo, fotos FROM carros ORDER BY id DESC LIMIT 10");

// Total
$total = $conn->query("SELECT COUNT(*) AS t FROM carros")->fetch_assoc()['t'] ?? 0;

// helper para foto
function foto_principal($jsonFotos) {
  $placeholder = "https://via.placeholder.com/250x150?text=Sem+Imagem";
  if (!$jsonFotos) return $placeholder;

  $arr = json_decode($jsonFotos, true);
  if (!is_array($arr) || empty($arr)) return $placeholder;

  // caminho relativo para o navegador
  $srcWeb = $arr[0];

  // validar no disco (considerando index.php em public_html)
  $absPath = __DIR__ . '/' . ltrim($srcWeb, '/');
  if (file_exists($absPath)) return $srcWeb;

  // fallback: se salvaram com "../uploads/..."
  if (strpos($srcWeb, '../') === 0) {
    $alt = substr($srcWeb, 3); // remove "../"
    if (file_exists(__DIR__ . '/' . $alt)) return $alt;
  }

  return $placeholder;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>RSP Diecast | Sistema</title>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<style>
  :root {
    --azul-escuro:#00205B; --azul-claro:#00AEEF; --branco:#FFF; --cinza:#CFCFCF;
  }
  *{box-sizing:border-box;font-family:Montserrat,Arial,Helvetica,sans-serif}
  body{margin:0;background:var(--azul-escuro);color:var(--branco);display:flex;min-height:100vh}
  /* Lateral */
  .menu{width:240px;background:#001B47;border-right:2px solid var(--azul-claro);padding:24px 18px;display:flex;flex-direction:column;gap:10px}
  .logo{color:var(--azul-claro);font-weight:800;font-size:1.2rem;margin-bottom:4px}
  .sub{color:#89cfff;font-size:.85rem;margin-bottom:18px}
  .menu a{display:block;color:var(--branco);text-decoration:none;font-weight:700;padding:10px 12px;border-radius:8px;transition:.2s}
  .menu a:hover{background:var(--azul-claro);color:var(--azul-escuro)}
  .resumo{margin-top:24px}
  .resumo h3{color:var(--azul-claro);margin:0 0 8px 0;font-size:1rem}
  .resumo table{width:100%;border-collapse:collapse;background:rgba(255,255,255,.05);border:1px solid var(--azul-claro);border-radius:10px;overflow:hidden}
  .resumo th,.resumo td{border-bottom:1px solid var(--azul-claro);padding:10px 12px;text-align:left}
  .resumo th{background:var(--azul-claro);color:var(--branco)}
  /* Conteúdo */
  .content{flex:1;padding:28px}
  h1{margin:0 0 18px 0;color:var(--azul-claro)}
  .grid{display:flex;flex-wrap:wrap;gap:20px}
  .card{width:260px;background:rgba(255,255,255,.05);border:1px solid var(--azul-claro);border-radius:12px;padding:14px;transition:.2s}
  .card:hover{transform:scale(1.02);background:rgba(255,255,255,.08)}
  .thumb{width:100%;height:150px;object-fit:cover;border-radius:8px;margin-bottom:10px;background:#0a1e4c}
  .meta{font-size:.95rem;line-height:1.35}
  .muted{opacity:.85}
  /* Responsivo */
  @media (max-width: 900px){
    .menu{position:fixed;left:0;top:0;bottom:0;z-index:10;transform:translateX(0)}
    .content{margin-left:240px}
  }
  @media (max-width: 640px){
    .menu{width:200px}
    .content{margin-left:200px}
    .card{width:calc(50% - 10px)}
  }
  @media (max-width: 480px){
    .card{width:100%}
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
</aside>

<!-- CONTEÚDO -->
<main class="content">
  <h1>🧱 Últimas 10 Miniaturas</h1>

  <?php if ($ultimas && $ultimas->num_rows > 0): ?>
    <section class="grid">
      <?php while($r = $ultimas->fetch_assoc()): ?>
        <?php $src = foto_principal($r['fotos']); ?>
        <article class="card">
          <img class="thumb" src="<?= htmlspecialchars($src) ?>" alt="Miniatura">
          <div class="meta">
            <strong><?= htmlspecialchars($r['modelo'] ?: '—') ?></strong><br>
            <span class="muted"><b>Ano:</b> <?= htmlspecialchars($r['ano'] ?: '—') ?></span><br>
            <span class="muted"><b>Código:</b> <?= htmlspecialchars($r['codigo'] ?: '—') ?></span>
          </div>
        </article>
      <?php endwhile; ?>
    </section>
  <?php else: ?>
    <p>Nenhuma miniatura cadastrada ainda.</p>
  <?php endif; ?>
</main>

</body>
</html>
