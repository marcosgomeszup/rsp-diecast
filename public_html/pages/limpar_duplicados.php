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
// FUNÇÃO PARA LIMPAR DUPLICADOS
// ===================================
function limparDuplicados($conn, $tabela, $campo) {
    $deleteSql = "
        DELETE FROM $tabela
        WHERE id NOT IN (
            SELECT * FROM (
                SELECT MIN(id)
                FROM $tabela
                GROUP BY $campo
            ) AS manter
        );
    ";
    if ($conn->query($deleteSql) === TRUE) {
        return "✅ Duplicados em <strong>$tabela</strong> foram removidos com sucesso!";
    } else {
        return "❌ Erro ao limpar duplicados em $tabela: " . $conn->error;
    }
}

// ===================================
// SEÇÃO DE CONFIRMAÇÃO DE LIMPEZA
// ===================================
$mensagem = null;
if (isset($_POST['tabela'])) {
    $tabela = $_POST['tabela'];
    $mensagem = limparDuplicados($conn, $tabela, 'nome');
}

// ===================================
// FUNÇÃO PARA CONSULTAR DUPLICADOS
// ===================================
function listarDuplicados($conn, $tabela, $campo) {
    $sql = "
        SELECT $campo AS nome, COUNT(*) AS qtd
        FROM $tabela
        GROUP BY $campo
        HAVING COUNT(*) > 1
        ORDER BY qtd DESC, nome ASC
    ";
    return $conn->query($sql);
}

// ===================================
// LISTA DE TABELAS A VERIFICAR
// ===================================
$tabelas = [
    "categorias" => "Categorias",
    "equipes" => "Equipes",
    "pilotos" => "Pilotos",
    "marcas" => "Marcas",
    "fabricantes" => "Fabricantes",
    "escalas" => "Escalas"
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Limpeza de Duplicados | RSP Diecast</title>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --vermelho: #FF6666;
    --cinza: #CFCFCF;
  }

  body {
    background: var(--azul-escuro);
    color: var(--branco);
    font-family: "Montserrat", sans-serif;
    padding: 40px;
  }

  h1 {
    text-align: center;
    color: var(--azul-claro);
    margin-bottom: 20px;
  }

  .mensagem {
    text-align: center;
    margin: 20px auto;
    font-weight: bold;
  }

  table {
    margin: 20px auto;
    border-collapse: collapse;
    width: 70%;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
  }

  th, td {
    border: 1px solid var(--azul-claro);
    padding: 10px;
    text-align: left;
  }

  th {
    background: var(--azul-claro);
    color: var(--branco);
  }

  tr:nth-child(even) {
    background: rgba(255,255,255,0.05);
  }

  h2 {
    text-align: center;
    margin-top: 40px;
    color: var(--azul-claro);
  }

  form {
    text-align: center;
  }

  button {
    background: var(--vermelho);
    color: var(--branco);
    border: none;
    padding: 10px 25px;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    margin: 20px 0;
    transition: 0.2s;
  }

  button:hover {
    background: var(--branco);
    color: var(--vermelho);
  }

  a {
    display: block;
    text-align: center;
    color: var(--azul-claro);
    font-weight: bold;
    margin-top: 30px;
    text-decoration: none;
  }

  a:hover { color: var(--branco); }
</style>
</head>
<body>

<h1>🧹 Painel de Limpeza de Duplicados</h1>

<?php if ($mensagem): ?>
  <p class="mensagem"><?= $mensagem ?></p>
<?php endif; ?>

<?php foreach ($tabelas as $tabela => $titulo): ?>
  <h2><?= $titulo ?></h2>

  <?php $result = listarDuplicados($conn, $tabela, 'nome'); ?>

  <?php if ($result->num_rows > 0): ?>
    <p style="text-align:center;">Foram encontrados <strong><?= $result->num_rows ?></strong> duplicados na tabela <strong><?= $titulo ?></strong>.</p>

    <table>
      <tr>
        <th>Nome</th>
        <th>Quantidade</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= $row['qtd'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <form method="POST">
      <input type="hidden" name="tabela" value="<?= $tabela ?>">
      <button type="submit">🧽 Limpar Duplicados em <?= $titulo ?></button>
    </form>

  <?php else: ?>
    <p style="text-align:center;">✅ Nenhum duplicado encontrado em <?= $titulo ?>.</p>
  <?php endif; ?>
<?php endforeach; ?>

<a href="dashboard.php">← Voltar ao Dashboard</a>

</body>
</html>
