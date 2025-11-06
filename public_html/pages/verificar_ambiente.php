<?php
// ===================================
// RSP DIECAST - Diagnóstico de Ambiente
// ===================================

$results = [];
$baseDir = __DIR__;
$uploadDir = $baseDir . '/uploads';
$dbName = 'rspdiecast_dbsystem';
$dbUser = 'rspdiecast_usrmaster';
$dbPass = 'X7OjyzhHH2';
$dbHost = 'localhost';

// Função de checagem rápida
function check($label, $condition) {
    return [
        'label' => $label,
        'status' => $condition ? '✅ OK' : '❌ FALHA'
    ];
}

// ===================================
// 1️⃣ Teste de conexão ao banco
// ===================================
$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    $results[] = ['label' => 'Conexão MySQL', 'status' => '❌ ' . $conn->connect_error];
} else {
    $results[] = ['label' => 'Conexão MySQL', 'status' => '✅ OK'];
}

// ===================================
// 2️⃣ Teste de tabelas essenciais
// ===================================
$tables = ['carros', 'categorias', 'equipes', 'pilotos', 'marcas', 'fabricantes', 'anos'];
foreach ($tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    $exists = ($check && $check->num_rows > 0);
    $results[] = check("Tabela: $table", $exists);
}

// ===================================
// 3️⃣ Teste de permissões de pasta uploads
// ===================================
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}
$writable = is_writable($uploadDir);
$results[] = check("Permissão de escrita em /uploads", $writable);

// ===================================
// 4️⃣ Extensões PHP essenciais
// ===================================
$requiredExtensions = ['mysqli', 'json', 'fileinfo', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    $results[] = check("Extensão PHP: $ext", extension_loaded($ext));
}

// ===================================
// 5️⃣ Limites de upload e post
// ===================================
$uploadMax = ini_get('upload_max_filesize');
$postMax = ini_get('post_max_size');
$fileUploads = ini_get('file_uploads');

$results[] = check("Upload de arquivos ativado (file_uploads)", (bool)$fileUploads);
$results[] = ['label' => 'Limite de upload (upload_max_filesize)', 'status' => $uploadMax];
$results[] = ['label' => 'Limite POST (post_max_size)', 'status' => $postMax];

// ===================================
// SAÍDA VISUAL
// ===================================
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Diagnóstico de Ambiente | RSP Diecast</title>
<style>
  body {
    font-family: "Montserrat", sans-serif;
    background: #00205B;
    color: #FFFFFF;
    padding: 40px;
  }
  h1 { color: #00AEEF; text-align: center; }
  table {
    width: 80%;
    margin: 30px auto;
    border-collapse: collapse;
    background: rgba(255,255,255,0.05);
  }
  th, td {
    border: 1px solid #00AEEF;
    padding: 12px;
    text-align: left;
  }
  th { background: #00AEEF; color: #00205B; }
  tr:hover { background: rgba(255,255,255,0.1); }
  .ok { color: #00FFAA; }
  .fail { color: #FF6666; }
  footer {
    text-align: center;
    margin-top: 40px;
    color: #CFCFCF;
  }
</style>
</head>
<body>
  <h1>🔍 Diagnóstico de Ambiente - RSP Diecast</h1>
  <table>
    <tr><th>Verificação</th><th>Status</th></tr>
    <?php foreach ($results as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['label']) ?></td>
        <td class="<?= strpos($r['status'], 'OK') !== false ? 'ok' : 'fail' ?>">
          <?= htmlspecialchars($r['status']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <footer>© <?= date('Y') ?> RSP Diecast • Diagnóstico Técnico</footer>
</body>
</html>
