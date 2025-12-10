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
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ===================================
// DADOS DO FORMULÁRIO
// ===================================
$ano            = isset($_POST['ano']) ? (int)$_POST['ano'] : null;
$modelo         = $_POST['modelo']        ?? null;
$codigo         = $_POST['codigo']        ?? null;
$categoria_id   = isset($_POST['categoria_id'])   ? (int)$_POST['categoria_id']   : null;
$equipe_id      = isset($_POST['equipe_id'])      ? (int)$_POST['equipe_id']      : null;
$piloto_id      = isset($_POST['piloto_id'])      ? (int)$_POST['piloto_id']      : null;
$marca_id       = isset($_POST['marca_id'])       ? (int)$_POST['marca_id']       : null;
$fabricante_id  = isset($_POST['fabricante_id'])  ? (int)$_POST['fabricante_id']  : null;
$escala_id      = isset($_POST['escala_id'])      ? (int)$_POST['escala_id']      : null;
$comentario     = $_POST['comentario']    ?? null;

// ===================================
// BUSCAR TEXTO DA ESCALA NA TABELA ESCALAS
// (armazenamos o NOME na coluna `escala` de `carros`)
// ===================================
$escala = null;
if ($escala_id) {
    $stmtEsc = $conn->prepare("SELECT nome FROM escalas WHERE id = ?");
    if ($stmtEsc) {
        $stmtEsc->bind_param("i", $escala_id);
        $stmtEsc->execute();
        $stmtEsc->bind_result($escalaNome);
        if ($stmtEsc->fetch()) {
            $escala = $escalaNome;
        }
        $stmtEsc->close();
    }
}

// ===================================
// UPLOAD DE FOTOS
// ===================================

// pasta física no servidor (salvar)
$uploadDirFs = __DIR__ . "/../uploads/";   // /public_html/uploads
// caminho que será guardado no banco e usado no src da <img>
$uploadDirWeb = "uploads/";

if (!is_dir($uploadDirFs)) {
    mkdir($uploadDirFs, 0755, true);
}

$fotos = [];
for ($i = 1; $i <= 3; $i++) {
    if (!empty($_FILES["foto$i"]["name"])) {
        $ext = pathinfo($_FILES["foto$i"]["name"], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $nomeArquivo = time() . "_$i_" . uniqid() . "." . $ext;

        $destinoFs  = $uploadDirFs . $nomeArquivo;      // físico
        $destinoWeb = $uploadDirWeb . $nomeArquivo;     // para o navegador

        if (move_uploaded_file($_FILES["foto$i"]["tmp_name"], $destinoFs)) {
            $fotos[] = $destinoWeb;
        }
    }
}

$fotosJSON = !empty($fotos) ? json_encode($fotos) : null;

// ===================================
// INSERT NA TABELA CARROS
// (repare que usamos a coluna `escala`, NÃO `escala_id`)
// ===================================
$sql = "INSERT INTO carros 
        (ano, modelo, codigo, categoria_id, equipe_id, piloto_id, 
         marca_id, fabricante_id, escala, comentario, fotos)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação do INSERT: " . $conn->error);
}

$stmt->bind_param(
    "issiiiiisss",
    $ano,
    $modelo,
    $codigo,
    $categoria_id,
    $equipe_id,
    $piloto_id,
    $marca_id,
    $fabricante_id,
    $escala,
    $comentario,
    $fotosJSON
);

if (!$stmt->execute()) {
    die("Erro ao salvar: " . $stmt->error);
}

// ID da nova miniatura
$novoId = $stmt->insert_id;

$stmt->close();
$conn->close();

// ===================================
// REDIRECIONA PARA A LISTAGEM,
// DESTACANDO A MINIATURA NOVA
// ===================================
header("Location: listar_carros.php?novo_id=" . $novoId);
exit;
