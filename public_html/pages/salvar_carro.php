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
// TRATAMENTO DE DADOS DO FORMULÁRIO
// ===================================
$ano = $_POST['ano'] ?? null;
$modelo = $_POST['modelo'] ?? null;
$codigo = $_POST['codigo'] ?? null;
$categoria_id = $_POST['categoria_id'] ?? null;
$equipe_id = $_POST['equipe_id'] ?? null;
$piloto_id = $_POST['piloto_id'] ?? null;
$marca_id = $_POST['marca_id'] ?? null;
$fabricante_id = $_POST['fabricante_id'] ?? null;

// ===================================
// UPLOAD DE FOTOS
// ===================================
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fotos = [];
for ($i = 1; $i <= 3; $i++) {
    if (!empty($_FILES["foto$i"]["name"])) {
        $fileName = time() . "_foto$i_" . basename($_FILES["foto$i"]["name"]);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES["foto$i"]["tmp_name"], $targetPath)) {
            $fotos[] = $targetPath;
        }
    }
}

// Transforma as fotos em texto JSON para salvar no banco
$fotosJSON = !empty($fotos) ? json_encode($fotos) : null;

// ===================================
// INSERÇÃO NO BANCO
// ===================================
$stmt = $conn->prepare("INSERT INTO carros 
(ano, modelo, codigo, categoria_id, equipe_id, piloto_id, marca_id, fabricante_id, fotos)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issiiiiis", $ano, $modelo, $codigo, $categoria_id, $equipe_id, $piloto_id, $marca_id, $fabricante_id, $fotosJSON);

if ($stmt->execute()) {
    $mensagem = "✅ Miniatura cadastrada com sucesso!";
} else {
    $mensagem = "❌ Erro ao salvar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Resultado do Cadastro | RSP Diecast</title>
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
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
    }
    h1 {
      color: var(--azul-claro);
    }
    a {
      margin-top: 20px;
      color: var(--azul-claro);
      text-decoration: none;
      font-weight: bold;
    }
    a:hover {
      color: var(--branco);
    }
  </style>
</head>
<body>
  <h1><?= $mensagem ?></h1>
  <a href="cadastro.php">← Voltar ao Cadastro</a>

</body>
</html>
