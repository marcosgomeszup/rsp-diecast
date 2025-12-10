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
// CONFIGURAÇÃO DE CONEXÃO AO BANCO
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
// FUNÇÃO AUXILIAR
// ===================================
function fetchAll($result) {
    if (!$result) return [];
    $dados = [];
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $k => $v) {
            $row[$k] = trim(htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'));
        }
        $dados[] = $row;
    }
    return $dados;
}

// ===================================
// CONSULTAS
// ===================================
$anos        = fetchAll($conn->query("SELECT ano FROM anos ORDER BY ano DESC"));
$categorias  = fetchAll($conn->query("SELECT id, nome FROM categorias ORDER BY nome ASC"));
$equipes     = fetchAll($conn->query("SELECT id, nome FROM equipes ORDER BY nome ASC"));
$pilotos     = fetchAll($conn->query("SELECT id, nome FROM pilotos ORDER BY nome ASC"));
$marcas      = fetchAll($conn->query("SELECT id, nome FROM marcas ORDER BY nome ASC"));
$fabricantes = fetchAll($conn->query("SELECT id, nome FROM fabricantes ORDER BY nome ASC"));
$escalas     = fetchAll($conn->query("SELECT id, nome FROM escalas ORDER BY nome ASC"));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Miniatura | RSP Diecast</title>
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
  }

  * { box-sizing:border-box; font-family:"Montserrat",sans-serif; }

  body{
    margin:0;
    min-height:100vh;
    background:var(--bg-body);
    color:var(--branco);
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
    margin:0 0 22px;
    font-size:.9rem;
    color:rgba(255,255,255,.75);
  }

  /* FORMULÁRIO */
  .form-card{
    max-width:900px;
    background:rgba(255,255,255,0.06);
    border-radius:16px;
    border:1px solid rgba(40,207,255,0.5);
    padding:24px 26px 26px;
    box-shadow:0 12px 26px rgba(0,0,0,.6);
  }

  fieldset{
    border:1px solid rgba(40,207,255,0.6);
    border-radius:12px;
    padding:18px 18px 20px;
    margin-bottom:18px;
    background:rgba(0,0,0,.35);
  }
  legend{
    padding:0 8px;
    font-size:1rem;
    color:var(--azul-soft);
    font-weight:600;
  }

  label{
    display:block;
    margin-top:10px;
    font-size:.9rem;
    font-weight:600;
  }

  input, select, textarea{
    width:100%;
    margin-top:5px;
    padding:10px;
    border-radius:10px;
    border:1px solid rgba(40,207,255,0.45);
    background:rgba(10,24,56,0.8);
    color:var(--branco);
    font-size:.9rem;
  }
  textarea{min-height:100px;resize:vertical;}
  option{background:#fff;color:#001433;}

  .fotos{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:6px;
  }
  input[type="file"]{
    border:none;
    background:transparent;
    padding:0;
    color:var(--azul-soft);
  }

  .btn-submit{
    width:100%;
    margin-top:10px;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(90deg,var(--azul-neon),var(--azul-soft));
    color:var(--azul-escuro);
    font-weight:700;
    font-size:1rem;
    cursor:pointer;
    box-shadow:0 10px 24px rgba(40,207,255,0.4);
    transition:.15s;
  }
  .btn-submit:hover{
    transform:translateY(-2px);
    box-shadow:0 14px 30px rgba(40,207,255,0.65);
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
    }
    .nav-section-title{display:none;}
    .nav{flex-direction:row;flex-wrap:wrap;justify-content:flex-end;}
    .nav-link{font-size:.8rem;padding:7px 9px;}
    .sidebar-footer{display:none;}
    .main-area{margin-left:0;}
  }

  @media (max-width:640px){
    .content{padding:18px 16px 22px;}
    .form-card{padding:18px 16px 22px;}
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
          <span class="icon">🏠</span><span>Início</span>
        </a>
        <a href="dashboard.php" class="nav-link">
          <span class="icon">📊</span><span>Dashboard</span>
        </a>
        <a href="cadastro.php" class="nav-link active">
          <span class="icon">➕</span><span>Cadastrar</span>
        </a>
        <a href="listar_carros.php" class="nav-link">
          <span class="icon">📋</span><span>Listagem</span>
        </a>
        <a href="/logout.php" class="nav-link logout">
          <span class="icon">🚪</span><span>Sair</span>
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
        <div class="topbar-title">Cadastro de Miniatura</div>
        <div class="topbar-subtitle">Preencha os dados para adicionar um novo item à coleção</div>
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
      <h1 class="page-title">➕ Nova miniatura</h1>
      <p class="page-subtitle">
        Utilize os campos abaixo para registrar modelo, equipe, piloto, escala, fabricante e fotos da miniatura.
      </p>

      <form class="form-card" action="salvar_carro.php" method="POST" enctype="multipart/form-data">
        <fieldset>
          <legend>🏎️ Dados do carro</legend>

          <label for="ano">Ano</label>
          <select name="ano" id="ano" required>
            <option value="">Selecione...</option>
            <?php foreach ($anos as $a): ?>
              <option value="<?= $a['ano'] ?>"><?= $a['ano'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="modelo">Modelo</label>
          <input type="text" id="modelo" name="modelo" maxlength="255" required>

          <label for="categoria_id">Categoria</label>
          <select name="categoria_id" id="categoria_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($categorias as $c): ?>
              <option value="<?= $c['id'] ?>"><?= $c['nome'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="equipe_id">Equipe</label>
          <select name="equipe_id" id="equipe_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($equipes as $e): ?>
              <option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="piloto_id">Piloto</label>
          <select name="piloto_id" id="piloto_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($pilotos as $p): ?>
              <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
            <?php endforeach; ?>
          </select>
        </fieldset>

        <fieldset>
          <legend>🧱 Dados da miniatura</legend>

          <label for="codigo">Código</label>
          <input type="text" id="codigo" name="codigo" maxlength="50" required>

          <label for="escala_id">Escala</label>
          <select name="escala_id" id="escala_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($escalas as $e): ?>
              <option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="marca_id">Marca</label>
          <select name="marca_id" id="marca_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($marcas as $m): ?>
              <option value="<?= $m['id'] ?>"><?= $m['nome'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="fabricante_id">Fabricante</label>
          <select name="fabricante_id" id="fabricante_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($fabricantes as $f): ?>
              <option value="<?= $f['id'] ?>"><?= $f['nome'] ?></option>
            <?php endforeach; ?>
          </select>

          <label for="comentario">Comentário</label>
          <textarea name="comentario" id="comentario" maxlength="1024"
            placeholder="Detalhes adicionais, curiosidades ou observações sobre a miniatura..."></textarea>

          <label>Fotos (até 3)</label>
          <div class="fotos">
            <input type="file" name="foto1" accept="image/*">
            <input type="file" name="foto2" accept="image/*">
            <input type="file" name="foto3" accept="image/*">
          </div>
        </fieldset>

        <button type="submit" class="btn-submit">💾 Salvar miniatura</button>
      </form>
    </main>
  </div>
</div>

</body>
</html>
