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
// CAPTURA DO ID
// ===================================
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID do carro não informado.");
}

// ===================================
// CONSULTA AS FOTOS ANTES DE EXCLUIR
// ===================================
$sql = "SELECT fotos, modelo FROM carros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$carro = $result->fetch_assoc();

if (!$carro) {
    die("Carro não encontrado.");
}

// ===================================
// PROCESSAMENTO DA CONFIRMAÇÃO
// ===================================
if (isset($_POST['confirmar']) && $_POST['confirmar'] == 'sim') {
    // Apaga as imagens físicas
    if (!empty($carro['fotos'])) {
        $fotos = json_decode($carro['fotos'], true);
        if (is_array($fotos)) {
            foreach ($fotos as $foto) {
                $path = __DIR__ . '/../' . $foto;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }

    // Exclui o registro no banco
    $delete = $conn->prepare("DELETE FROM carros WHERE id = ?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        $mensagem = "✅ Miniatura excluída com sucesso!";
    } else {
        $mensagem = "❌ Erro ao excluir: " . $delete->error;
    }
    $delete->close();
    $conn->close();
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <title>Exclusão | RSP Diecast</title>
    <style>
      :root {
        --azul-escuro: #00205B;
        --azul-claro: #00AEEF;
        --branco: #FFFFFF;
      }
      body {
        background: var(--azul-escuro);
        color: var(--branco);
        font-family: "Montserrat", sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
      }
      h1 { color: var(--azul-claro); }
      a {
        color: var(--azul-claro);
        text-decoration: none;
        margin-top: 20px;
        font-weight: bold;
      }
      a:hover { color: var(--branco); }
    </style>
    </head>
    <body>
      <h1><?= $mensagem ?></h1>
      <a href="listar_carros.php">← Voltar à listagem</a>
    </body>
    </html>
    <?php
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Confirmar Exclusão | RSP Diecast</title>
<style>
  :root {
    --azul-escuro: #00205B;
    --azul-claro: #00AEEF;
    --branco: #FFFFFF;
    --vermelho: #C62828;
  }
  body {
    background: var(--azul-escuro);
    color: var(--branco);
    font-family: "Montserrat", sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    text-align: center;
  }
  h1 { color: var(--azul-claro); margin-bottom: 20px; }
  p { font-size: 1.2rem; margin-bottom: 25px; }
  form { display: flex; gap: 20px; }
  button {
    padding: 12px 25px;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
  }
  .confirmar { background: var(--vermelho); color: var(--branco); }
  .cancelar { background: var(--azul-claro); color: var(--branco); }
  button:hover { opacity: 0.9; }
</style>
</head>
<body>

<h1>Excluir Miniatura</h1>
<p>Tem certeza que deseja excluir <strong><?= htmlspecialchars($carro['modelo']) ?></strong>?</p>

<form method="POST">
  <input type="hidden" name="confirmar" value="sim">
  <button type="submit" class="confirmar">Sim, excluir</button>
  <a href="listar_carros.php"><button type="button" class="cancelar">Cancelar</button></a>
</form>

</body>
</html>
