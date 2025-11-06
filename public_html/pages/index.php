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

// ===================================
// BUSCA AS 10 ÚLTIMAS MINIATURAS
// ===================================
$sql = "SELECT * FROM carros ORDER BY id DESC LIMIT 10";
$ultimas = $conn->query($sql);

// ===================================
// TOTAL DE MINIATURAS
// ===================================
$total = $conn->query("SELECT COUNT(*) AS total FROM carros")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>RSP Diecast | Sistema de Miniaturas</title>
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
    height: 100vh;
  }

  /* ===== MENU LATERAL ===== */
  .menu-lateral {
    width: 220px;
    background: #001B47;
    display: flex;
    flex-direction: column;
    padding: 20px;
    border-right: 2px solid var(--azul-claro);
  }

  .menu-lateral h2 {
    color: var(--azul-claro);
    text-align: center;
    margin-bottom: 20px;
  }

  .menu-lateral a {
    display: block;
    color: var(--branco);
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    margin: 6px 0;
    border-radius: 6px;
    transition: 0.2s;
  }

  .menu-lateral a:hover {
    background: var(--azul-claro);
    color: var(--azul-escuro);
  }

  /* ===== CONTEÚDO PRINCIPAL ===== */
  .conteudo {
    flex: 1;
    padding: 40px;
    overflow-y: auto;
  }

  h1 {
    color: var(--azul-claro);
    margin-bottom: 20px;
  }

  .miniaturas {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
  }

  .card {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 12px;
    padding: 15px;
    width: 250px;
    text-align: center;
    transition: 0.3s;
  }

  .card:hover {
    transform: scale(1.03);
    background: rgba(255,255,255,0.08);
  }

  .card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .resumo {
    margin-top: 40px;
    text-align: center;
  }

  .resumo table {
    margin: 0 auto;
    border-collapse: collapse;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 10px;
  }

  .resumo th, .resumo td {
    border: 1px solid var(--azul-claro);
    padding: 10px 20px;
  }

  .resumo th {
    background: var(--azul-claro);
    color: var(--branco);
  }
</style>
</head>
<body>

<!-- MENU LATERAL -->
<div class="menu-lateral">
  <h2>📦 RSP Diecast</h2>
  <a href="dashboard.php">📊 Dashboard</a>
  <a href="cadastro.php">➕ Cadastrar</a>
  <a href="listar_carros.php">📋 Listagem</a>

  <div class="resumo">
    <h3 style="margin-top:40px;">📈 Resumo</h3>
    <table>
      <tr><th>Total de Miniaturas</th></tr>
      <tr><td><strong><?= $total ?></strong></td></tr>
    </table>
  </div>
</div>

<!-- CONTEÚDO PRINCIPAL -->
<div class="conteudo">
  <h1>🧱 Últimas 10 Miniaturas Cadastradas</h1>

  <?php if ($ultimas && $ultimas->num_rows > 0): ?>
  <div class="miniaturas">
    <?php while ($row = $ultimas->fetch_assoc()):
      $fotos = json_decode($row['fotos'], true);
      $fotoPrincipal = (!empty($fotos) && file_exists($fotos[0])) ? $fotos[0] : "https://via.placeholder.com/250x150?text=Sem+Imagem";
    ?>
      <div class="card">
        <img src="<?= htmlspecialchars($fotoPrincipal) ?>" alt="Miniatura">
        <h3><?= htmlspecialchars($row['modelo']) ?></h3>
        <p><strong>Ano:</strong> <?= htmlspecialchars($row['ano']) ?></p>
        <p><strong>Código:</strong> <?= htmlspecialchars($row['codigo']) ?></p>
      </div>
    <?php endwhile; ?>
  </div>
  <?php else: ?>
    <p style="text-align:center;">Nenhuma miniatura cadastrada ainda.</p>
  <?php endif; ?>
</div>

</body>
</html>
