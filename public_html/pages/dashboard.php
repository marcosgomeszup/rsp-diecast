<?php
// ===================================
// LOGIN OBRIGATÓRIO
// ===================================
session_start();
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
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// =======================
// COLETA DE DADOS
// =======================

// Total de miniaturas
$total = $conn->query("SELECT COUNT(*) AS total FROM carros")
              ->fetch_assoc()['total'] ?? 0;

// Contagem por fabricante
$resFabricantes = $conn->query("
    SELECT f.nome AS fabricante, COUNT(c.id) AS total
    FROM carros c
    JOIN fabricantes f ON c.fabricante_id = f.id
    GROUP BY f.nome
    ORDER BY total DESC
");

// Contagem por equipe
$resEquipes = $conn->query("
    SELECT e.nome AS equipe, COUNT(c.id) AS total
    FROM carros c
    JOIN equipes e ON c.equipe_id = e.id
    GROUP BY e.nome
    ORDER BY total DESC
");

// Contagem por categoria
$resCategorias = $conn->query("
    SELECT ca.nome AS categoria, COUNT(c.id) AS total
    FROM carros c
    JOIN categorias ca ON c.categoria_id = ca.id
    GROUP BY ca.nome
    ORDER BY total DESC
");

// Contagem por marca (campo texto na tabela carros: c.marca)
$resMarcas = $conn->query("
    SELECT c.marca AS marca, COUNT(c.id) AS total
    FROM carros c
    WHERE c.marca IS NOT NULL AND c.marca <> ''
    GROUP BY c.marca
    ORDER BY total DESC
");

// Contagem por escala (campo texto na tabela carros: c.escala)
$resEscalas = $conn->query("
    SELECT c.escala AS escala, COUNT(c.id) AS total
    FROM carros c
    WHERE c.escala IS NOT NULL AND c.escala <> ''
    GROUP BY c.escala
    ORDER BY total DESC
");

// Monta arrays para usar no JS
$fabricantesLabels = $fabricantesData = [];
if ($resFabricantes) {
    while ($row = $resFabricantes->fetch_assoc()) {
        $fabricantesLabels[] = $row['fabricante'];
        $fabricantesData[]   = (int)$row['total'];
    }
}

$equipesLabels = $equipesData = [];
if ($resEquipes) {
    while ($row = $resEquipes->fetch_assoc()) {
        $equipesLabels[] = $row['equipe'];
        $equipesData[]   = (int)$row['total'];
    }
}

$categoriasLabels = $categoriasData = [];
if ($resCategorias) {
    while ($row = $resCategorias->fetch_assoc()) {
        $categoriasLabels[] = $row['categoria'];
        $categoriasData[]   = (int)$row['total'];
    }
}

$marcasLabels = $marcasData = [];
if ($resMarcas) {
    while ($row = $resMarcas->fetch_assoc()) {
        $marcasLabels[] = $row['marca'];
        $marcasData[]   = (int)$row['total'];
    }
}

$escalasLabels = $escalasData = [];
if ($resEscalas) {
    while ($row = $resEscalas->fetch_assoc()) {
        $escalasLabels[] = $row['escala'];
        $escalasData[]   = (int)$row['total'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Dashboard | RSP Diecast</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  :root {
    --azul-escuro: #001433;
    --azul-escuro-alt: #000d24;
    --azul-claro: #00AEEF;
    --azul-neon: #28CFFF;
    --azul-soft: #6FE0FF;
    --bg-body: radial-gradient(circle at top center, #00224d 0%, #00112c 40%, #000814 100%);
    --border-subtle: rgba(148, 163, 184, 0.4);
    --text-main: #e5e7eb;
    --text-muted: #9ca3af;
  }

  * {
    box-sizing: border-box;
    font-family: "Montserrat", sans-serif;
  }

  body {
    margin: 0;
    min-height: 100vh;
    background: var(--bg-body);
    color: var(--text-main);
  }

  .layout {
    display: flex;
    min-height: 100vh;
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
    color:#fff;
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

  /* MAIN AREA */
  .main-area{
    flex:1;
    display:flex;
    flex-direction:column;
  }

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
    color:#fff;
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

  .content{
    flex:1;
    padding:24px 26px 32px;
  }

  /* PAGE WIDTH */
  .page{
    max-width:1200px;
    margin:0 auto;
  }

  .header{
    display:flex;
    flex-wrap:wrap;
    align-items:flex-end;
    justify-content:space-between;
    gap:16px;
    margin-bottom:28px;
  }

  .header-left h1{
    font-size:1.9rem;
    margin:0 0 4px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:10px;
  }
  .header-left h1 span.icon{font-size:1.7rem;}
  .header-left p{
    margin:0;
    font-size:.95rem;
    color:var(--text-muted);
  }

  .header-right{
    text-align:right;
    font-size:.8rem;
    color:var(--text-muted);
  }

  .badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:4px 10px;
    border-radius:999px;
    border:1px solid var(--border-subtle);
    background:radial-gradient(circle at top left, rgba(56,189,248,0.2), transparent);
    font-size:.75rem;
    text-transform:uppercase;
    letter-spacing:.08em;
  }

  /* KPI CARDS */
  .cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(210px,1fr));
    gap:18px;
    margin-bottom:30px;
  }

  .card{
    background:radial-gradient(circle at top,rgba(56,189,248,0.10),rgba(15,23,42,0.98));
    border-radius:16px;
    border:1px solid var(--border-subtle);
    padding:18px 18px 16px;
    box-shadow:
      0 18px 35px rgba(15,23,42,0.9),
      0 0 0 1px rgba(15,23,42,0.6);
    position:relative;
    overflow:hidden;
  }
  .card::after{
    content:"";
    position:absolute;
    inset:-40%;
    background:radial-gradient(circle at top right, rgba(56,189,248,0.10), transparent);
    opacity:.7;
    pointer-events:none;
  }
  .card-content{position:relative;z-index:1;}
  .card-label{
    font-size:.8rem;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--text-muted);
    margin-bottom:6px;
  }
  .card-value{
    font-size:2.2rem;
    font-weight:600;
    line-height:1.1;
    margin-bottom:4px;
  }
  .card-footer{
    font-size:.8rem;
    color:var(--text-muted);
    display:flex;
    align-items:center;
    gap:6px;
  }
  .pill{
    padding:2px 8px;
    border-radius:999px;
    font-size:.75rem;
    border:1px solid rgba(148,163,184,0.5);
  }
  .pill-accent{
    border-color:rgba(56,189,248,0.7);
    color:#38bdf8;
  }

  /* CHARTS */
  .charts-section-title{
    font-size:.95rem;
    text-transform:uppercase;
    letter-spacing:.14em;
    color:var(--text-muted);
    margin:10px 2px 12px;
  }
  .charts{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
    gap:20px;
  }
  .chart-card{
    background:#0f172a;
    border-radius:16px;
    border:1px solid var(--border-subtle);
    padding:16px 16px 10px;
    box-shadow:0 14px 30px rgba(15,23,42,0.85);
  }
  .chart-title{
    font-size:.9rem;
    font-weight:500;
    margin:0 0 4px;
  }
  .chart-subtitle{
    font-size:.8rem;
    color:var(--text-muted);
    margin:0 0 6px;
  }
  .chart-wrapper{
    position:relative;
    height:260px;
  }
  canvas{
    background:radial-gradient(circle at top, rgba(15,23,42,0.8), rgba(15,23,42,1));
    border-radius:12px;
    padding:10px;
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

  @media(max-width:768px){
    .content{padding:18px 16px 26px;}
    .header{flex-direction:column;align-items:flex-start;}
    .header-right{text-align:left;}
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
          <span>🏠</span><span>Início</span>
        </a>
        <a href="dashboard.php" class="nav-link active">
          <span>📊</span><span>Dashboard</span>
        </a>
        <a href="cadastro.php" class="nav-link">
          <span>➕</span><span>Cadastrar</span>
        </a>
        <a href="listar_carros.php" class="nav-link">
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

  <!-- MAIN AREA -->
  <div class="main-area">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-left">
        <div class="topbar-title">Dashboard da coleção</div>
        <div class="topbar-subtitle">Visão geral por fabricantes, marcas, equipes e escalas</div>
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

    <!-- CONTENT -->
    <main class="content">
      <div class="page">
        <!-- CABEÇALHO -->
        <header class="header">
          <div class="header-left">
            <h1>
              <span class="icon">🏁</span>
              RSP Diecast • Dashboard
            </h1>
            <p>Visão geral da coleção de miniaturas: fabricantes, marcas, equipes, categorias e escalas.</p>
          </div>
          <div class="header-right">
            <div class="badge">
              <span style="width:8px;height:8px;border-radius:999px;background:#22c55e;"></span>
              Coleção ativa
            </div>
            <div style="margin-top:4px;">
              Atualizado em: <?= date('d/m/Y H:i'); ?>
            </div>
          </div>
        </header>

        <!-- CARDS KPI -->
        <section class="cards">
          <div class="card">
            <div class="card-content">
              <div class="card-label">Total de miniaturas</div>
              <div class="card-value"><?= number_format($total, 0, ',', '.') ?></div>
              <div class="card-footer">
                <span class="pill pill-accent">Coleção geral</span>
                <span>Registro completo do acervo.</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-content">
              <div class="card-label">Fabricantes diferentes</div>
              <div class="card-value"><?= count($fabricantesLabels) ?></div>
              <div class="card-footer">
                <span class="pill">Fabricantes</span>
                <span>Diversidade de marcas produtoras.</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-content">
              <div class="card-label">Marcas de carros</div>
              <div class="card-value"><?= count($marcasLabels) ?></div>
              <div class="card-footer">
                <span class="pill">Marca</span>
                <span>Ferrari, McLaren, etc.</span>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-content">
              <div class="card-label">Escalas cadastradas</div>
              <div class="card-value"><?= count($escalasLabels) ?></div>
              <div class="card-footer">
                <span class="pill">Escala</span>
                <span>1:18, 1:24, 1:43...</span>
              </div>
            </div>
          </div>
        </section>

        <!-- GRÁFICOS -->
        <section>
          <div class="charts-section-title">Distribuições gerais</div>
          <div class="charts">
            <div class="chart-card">
              <h2 class="chart-title">Miniaturas por Fabricante</h2>
              <p class="chart-subtitle">Resumo da coleção por marca de fabricação.</p>
              <div class="chart-wrapper">
                <canvas id="graficoFabricantes"></canvas>
              </div>
            </div>

            <div class="chart-card">
              <h2 class="chart-title">Miniaturas por Equipe</h2>
              <p class="chart-subtitle">Comparação do volume por equipe de corrida.</p>
              <div class="chart-wrapper">
                <canvas id="graficoEquipes"></canvas>
              </div>
            </div>

            <div class="chart-card">
              <h2 class="chart-title">Miniaturas por Categoria</h2>
              <p class="chart-subtitle">Visão por tipo/categoria de miniatura.</p>
              <div class="chart-wrapper">
                <canvas id="graficoCategorias"></canvas>
              </div>
            </div>

            <div class="chart-card">
              <h2 class="chart-title">Miniaturas por Marca</h2>
              <p class="chart-subtitle">Distribuição por marca do carro (Ferrari, McLaren, etc.).</p>
              <div class="chart-wrapper">
                <canvas id="graficoMarcas"></canvas>
              </div>
            </div>

            <div class="chart-card">
              <h2 class="chart-title">Miniaturas por Escala</h2>
              <p class="chart-subtitle">Equilíbrio de escalas na coleção.</p>
              <div class="chart-wrapper">
                <canvas id="graficoEscalas"></canvas>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

  </div>
</div>

<script>
  const fabricantesLabels = <?= json_encode($fabricantesLabels, JSON_UNESCAPED_UNICODE); ?>;
  const fabricantesData   = <?= json_encode($fabricantesData, JSON_NUMERIC_CHECK); ?>;

  const equipesLabels     = <?= json_encode($equipesLabels, JSON_UNESCAPED_UNICODE); ?>;
  const equipesData       = <?= json_encode($equipesData, JSON_NUMERIC_CHECK); ?>;

  const categoriasLabels  = <?= json_encode($categoriasLabels, JSON_UNESCAPED_UNICODE); ?>;
  const categoriasData    = <?= json_encode($categoriasData, JSON_NUMERIC_CHECK); ?>;

  const marcasLabels      = <?= json_encode($marcasLabels, JSON_UNESCAPED_UNICODE); ?>;
  const marcasData        = <?= json_encode($marcasData, JSON_NUMERIC_CHECK); ?>;

  const escalasLabels     = <?= json_encode($escalasLabels, JSON_UNESCAPED_UNICODE); ?>;
  const escalasData       = <?= json_encode($escalasData, JSON_NUMERIC_CHECK); ?>;

  const ctxFabricantes = document.getElementById('graficoFabricantes');
  const ctxEquipes     = document.getElementById('graficoEquipes');
  const ctxCategorias  = document.getElementById('graficoCategorias');
  const ctxMarcas      = document.getElementById('graficoMarcas');
  const ctxEscalas     = document.getElementById('graficoEscalas');

  const palette = [
    '#0ea5e9', '#22c55e', '#eab308', '#6366f1', '#f97316',
    '#ec4899', '#a855f7', '#14b8a6', '#f43f5e', '#4b5563'
  ];

  new Chart(ctxFabricantes, {
    type: 'doughnut',
    data: {
      labels: fabricantesLabels,
      datasets: [{
        label: 'Miniaturas',
        data: fabricantesData,
        backgroundColor: fabricantesLabels.map((_, i) => palette[i % palette.length]),
        borderWidth: 1,
        borderColor: '#020617'
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#e5e7eb',
            usePointStyle: true,
            pointStyle: 'circle',
            font: { size: 11 }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const value = context.parsed;
              const pct   = total ? ((value / total) * 100).toFixed(1) : 0;
              return `${context.label}: ${value} (${pct}%)`;
            }
          }
        }
      },
      cutout: '55%'
    }
  });

  new Chart(ctxEquipes, {
    type: 'bar',
    data: {
      labels: equipesLabels,
      datasets: [{
        label: 'Miniaturas',
        data: equipesData,
        backgroundColor: '#0ea5e9',
        borderRadius: 6,
        maxBarThickness: 26
      }]
    },
    options: {
      indexAxis: 'y',
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      },
      scales: {
        x: {
          grid: { color: 'rgba(148,163,184,0.25)' },
          ticks: { color: '#9ca3af' }
        },
        y: {
          grid: { display: false },
          ticks: {
            color: '#e5e7eb',
            font: { size: 11 }
          }
        }
      }
    }
  });

  new Chart(ctxCategorias, {
    type: 'pie',
    data: {
      labels: categoriasLabels,
      datasets: [{
        label: 'Miniaturas',
        data: categoriasData,
        backgroundColor: categoriasLabels.map((_, i) => palette[(i + 2) % palette.length]),
        borderWidth: 1,
        borderColor: '#020617'
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#e5e7eb',
            usePointStyle: true,
            pointStyle: 'rectRounded',
            font: { size: 11 }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const value = context.parsed;
              const pct   = total ? ((value / total) * 100).toFixed(1) : 0;
              return `${context.label}: ${value} (${pct}%)`;
            }
          }
        }
      }
    }
  });

  new Chart(ctxMarcas, {
    type: 'bar',
    data: {
      labels: marcasLabels,
      datasets: [{
        label: 'Miniaturas',
        data: marcasData,
        backgroundColor: marcasLabels.map((_, i) => palette[(i + 1) % palette.length]),
        borderRadius: 6,
        maxBarThickness: 32
      }]
    },
    options: {
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: '#e5e7eb',
            font: { size: 10 },
            maxRotation: 45,
            minRotation: 0
          }
        },
        y: {
          grid: { color: 'rgba(148,163,184,0.25)' },
          ticks: { color: '#9ca3af' }
        }
      }
    }
  });

  new Chart(ctxEscalas, {
    type: 'polarArea',
    data: {
      labels: escalasLabels,
      datasets: [{
        label: 'Miniaturas',
        data: escalasData,
        backgroundColor: escalasLabels.map((_, i) => palette[(i + 3) % palette.length]),
        borderWidth: 1,
        borderColor: '#020617'
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#e5e7eb',
            usePointStyle: true,
            font: { size: 11 }
          }
        }
      },
      scales: {
        r: {
          grid: { color: 'rgba(148,163,184,0.25)' },
          ticks: { color: '#9ca3af' },
          angleLines: { color: 'rgba(148,163,184,0.25)' }
        }
      }
    }
  });
</script>

</body>
</html>
