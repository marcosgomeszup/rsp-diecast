<?php
// ===================================
// PROCESSA LOGIN
// ===================================
session_start();

// ATIVAR EXIBIÇÃO DE ERROS (APENAS PARA DEBUG, DEPOIS PODE COMENTAR)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Se não veio via POST, volta pro login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /index.php");
    exit;
}

// Campos enviados do formulário
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$senha = trim(isset($_POST['senha']) ? $_POST['senha'] : '');

// Validação básica
if ($email === '' || $senha === '') {
    $_SESSION['erro_login'] = "Informe e-mail e senha.";
    header("Location: /index.php");
    exit;
}

// Conexão com o banco
require_once __DIR__ . '/includes/conexao.php';

// Tabela: users (id, nome, email, senha, tipo_login, ...)
$sql  = "SELECT id, nome, email, senha, tipo_login FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['erro_login'] = "Erro ao preparar consulta: " . $conn->error;
    header("Location: /index.php");
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// --------------------------------------
// 1) VERIFICA SE O USUÁRIO EXISTE
// --------------------------------------
if (!$result || $result->num_rows === 0) {
    $_SESSION['erro_login'] = "E-mail ou senha inválidos."; // usuário não encontrado
    header("Location: /index.php");
    exit;
}

$user = $result->fetch_assoc();

// --------------------------------------
// 2) VALIDAÇÃO FLEXÍVEL DE SENHA
// --------------------------------------
$hash_banco = $user['senha'];
$senha_ok   = false;

// Garante que é string
$hash_banco = (string)$hash_banco;

// 2.1 - Tenta password_hash / password_verify (bcrypt, argon, etc.)
// (hashs desse tipo começam com "$")
if (!$senha_ok && isset($hash_banco[0]) && $hash_banco[0] === '$') {
    if (function_exists('password_verify') && password_verify($senha, $hash_banco)) {
        $senha_ok = true;
    }
}

// 2.2 - Tenta texto puro
if (!$senha_ok && $senha === $hash_banco) {
    $senha_ok = true;
}

// 2.3 - Se for string hexa, testa alguns algoritmos
if (!$senha_ok && ctype_xdigit($hash_banco)) {
    $len = strlen($hash_banco);

    // MD5 – 32 chars
    if ($len === 32 && md5($senha) === $hash_banco) {
        $senha_ok = true;
    }

    // SHA1 – 40 chars
    if (!$senha_ok && $len === 40 && sha1($senha) === $hash_banco) {
        $senha_ok = true;
    }

    // SHA256 – 64 chars
    if (!$senha_ok && $len === 64 && hash('sha256', $senha) === $hash_banco) {
        $senha_ok = true;
    }

    // SHA512 – 128 chars
    if (!$senha_ok && $len === 128 && hash('sha512', $senha) === $hash_banco) {
        $senha_ok = true;
    }
}

// Se ainda assim não bateu:
if (!$senha_ok) {
    $_SESSION['erro_login'] = "E-mail ou senha inválidos.";
    header("Location: /index.php");
    exit;
}

// --------------------------------------
// 3) LOGIN OK → CRIA SESSÃO
// --------------------------------------
$_SESSION['usuario_id']   = $user['id'];
$_SESSION['usuario']      = $user['nome'] ?: $user['email'];
$_SESSION['usuario_mail'] = $user['email'];

// Limpa erro, se houver
unset($_SESSION['erro_login']);

// Redireciona para o painel
header("Location: /pages/index.php");
exit;
