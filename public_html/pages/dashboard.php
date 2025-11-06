<?php
// ===================================
// CONEXÃO COM O BANCO
// ===================================
$servername = "localhost";
$username = "rspdiecast_usrmaster";
$password = "X7OjyzhHH2";
$database = "rspdiecast_dbsystem";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// =======================
// COLETA DE DADOS
// =======================

// Total de miniaturas
$total = $conn->query("SELECT COUNT(*) AS total FROM carros")->fetch_assoc()['total'];

// Contagem por fabricante
$fabricantes = $conn->query("
    SELECT f.nome AS fabricante, COUNT(c.id) AS total
    FROM carros c
    JOIN fabricantes f ON c.fabricante_id = f.id
    GROUP BY f.nome
");

// Contagem por equipe
$equipes = $conn->query("
    SELECT e.nome AS equipe, COUNT(c.id) AS total
    FROM carros c
    JOIN equipes e ON c.equipe_id = e.id
    GROUP BY e.nome
");

// Contagem por categoria
$categorias = $conn->query("
    SELECT ca.nome AS categoria, COUNT(c.id) AS total
    FROM carros c
    JOIN categorias ca ON c.categoria_id = ca.id
    GROUP BY ca.nome
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Dashboard | RSP Diecast</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --cinza: #CFCFCF;
  }

  body {
    background: var(--azul-escuro);
    color: var(--branco);
    font-family: "Montserrat", sans-serif;
    margin: 0;
    padding-top: 80px;
  }

  .menu {
    position: fixed;
    top: 0;
    left: 0;
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
    transition: 0.2s;
  }

  .menu a:hover {
    color: var(--azul-claro);
  }

  h1 {
    text-align: center;
    color: var(--azul-claro);
    margin-bottom: 40px;
  }

  .cards {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 25px;
    margin-bottom: 40px;
  }

  .card {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--azul-claro);
    border-radius: 12px;
    padding: 20px;
    width: 220px;
    text-align: center;
  }

  .card h2 {
    color: var(--azul-claro);
    font-size: 2.2rem;
    margin: 0;
  }

  .charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    padding: 0 40px 60px;
  }

  canvas {
    background: rgba(255,255,255,0.03);
    border-radius: 10px;
    padding: 15px;
  }
</style>
</head>
<body>

<!-- MENU FIXO -->
<nav class="menu">
  <a href="/pages/dashboard.php">📊 Dashboard</a>
  <a href="/pages/cadastro.php">➕ Cadastrar</a>
  <a href="/pages/listar_carros.php">📋 Listagem</a>
</nav>


<h1>📊 Painel de Estatísticas</h1>

<div class="cards">
  <div class="card">
    <p>Total de Miniaturas</p>
    <h2><?= $total ?></h2>
  </div>
</div>

<div class="charts">
  <div>
    <canvas id="graficoFabricantes"></canvas>
  </div>
  <div>
    <canvas id="graficoEquipes"></canvas>
  </div>
  <div>
    <canvas id="graficoCategorias"></canvas>
  </div>
</div>

<script>
const ctxFabricantes = document.getElementById('graficoFabricantes');
const ctxEquipes = document.getElementById('graficoEquipes');
const ctxCategorias = document.getElementById('graficoCategorias');

new Chart(ctxFabricantes, {
  type: 'pie',
  data: {
    labels: [<?php while($f = $fabricantes->fetch_assoc()) { echo "'".$f['fabricante']."',"; } ?>],
    datasets: [{
      label: 'Por Fabricante',
      data: [<?php
        $fabricantes->data_seek(0);
        while($f = $fabricantes->fetch_assoc()) { echo $f['total'].","; }
      ?>],
      backgroundColor: ['#00AEEF', '#005EB8', '#0076CE', '#A2D5F2', '#89CFF0']
    }]
  }
});

new Chart(ctxEquipes, {
  type: 'bar',
  data: {
    labels: [<?php while($e = $equipes->fetch_assoc()) { echo "'".$e['equipe']."',"; } ?>],
    datasets: [{
      label: 'Por Equipe',
      data: [<?php
        $equipes->data_seek(0);
        while($e = $equipes->fetch_assoc()) { echo $e['total'].","; }
      ?>],
      backgroundColor: '#00AEEF'
    }]
  },
  options: { scales: { y: { beginAtZero: true } } }
});

new Chart(ctxCategorias, {
  type: 'doughnut',
  data: {
    labels: [<?php while($c = $categorias->fetch_assoc()) { echo "'".$c['categoria']."',"; } ?>],
    datasets: [{
      label: 'Por Categoria',
      data: [<?php
        $categorias->data_seek(0);
        while($c = $categorias->fetch_assoc()) { echo $c['total'].","; }
      ?>],
      backgroundColor: ['#00AEEF', '#005EB8', '#0076CE', '#A2D5F2', '#89CFF0']
    }]
  }
});
</script>

</body>
</html>
