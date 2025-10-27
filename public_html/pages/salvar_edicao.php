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
// CAPTURA DE DADOS
// ===================================
$id = $_POST['id'] ?? null;
$ano = $_POST['ano'] ?? null;
$modelo = $_POST['modelo'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;
$equipe_id = $_POST['equipe_id'] ?? null;
$piloto_id = $_POST['piloto_id'] ?? null;
$marca_id = $_POST['marca_id'] ?? null;
$fabricante_id = $_POST['fabricante_id'] ?? null;

if (!$id) {
    die("ID do carro não informado para edição.");
}

// ===================================
// TRATA UPLOAD DE NOVAS FOTOS (OPCIONAL)
// ===================================
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fotos = [];
for ($i = 1; $i <= 3; $i++) {
    if (!empty($_FILES["foto$i"]["name"])) {
        $fileName = time() . "_edit" . $i . "_" . basename($_FILES["foto$i"]["name"]);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES["foto$i"]["tmp_name"], $targetPath)) {
            $fotos[] = $targetPath;
        }
    }
}

$fotosJSON = !empty($fotos) ? json_encode($fotos) : null;

// ===================================
// ATUALIZA OS DADOS NO BANCO
// ===================================
if ($fotosJSON) {
    $stmt = $conn->prepare("
        UPDATE carros SET 
            ano = ?, modelo = ?, codigo = ?, categoria_id = ?, 
            equipe_id = ?, piloto_id = ?, marca_id = ?, fabricante_id = ?, fotos = ?
        WHERE id = ?
    ");
    $stmt->bind_param("issiiiiisi", $ano, $modelo, $codigo, $categoria_id, $equipe_id, $piloto_id, $marca_id, $fabricante_id, $fotosJSON, $id);
} else {
    $stmt = $conn->prepare("
        UPDATE carros SET 
            ano = ?, modelo = ?, codigo = ?, categoria_id = ?, 
            equipe_id = ?, piloto_id = ?, marca_id = ?, fabricante_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("issiiiiii", $ano, $modelo, $codigo, $categoria_id, $equipe_id, $piloto_id, $marca_id, $fabricante_id, $id);
}

if ($stmt->execute()) {
    $mensagem = "✅ Miniatura atualizada com sucesso!";
} else {
    $mensagem = "❌ Erro ao atualizar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Resultado da Edição | RSP Diecast</title>
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
    margin-top: 20px;
    text-decoration: none;
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
