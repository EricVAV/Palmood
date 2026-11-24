<?php
// Inicia a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dados de acesso ao MySQL (ajuste se usar senha no XAMPP/WAMP)
$host = 'localhost';
$db   = 'Palmood';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Erro ao conectar no banco de dados: ' . $e->getMessage());
}

// Escapa textos para evitar XSS no HTML
function h($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Retorna os dados do usuário logado ou null
function usuarioLogado() {
    return $_SESSION['usuario'] ?? null;
}

// Envia resposta padrão em JSON para as APIs
function responderJson($dados, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

// Bloqueia chamadas de API se o usuário não estiver logado
function exigirLoginApi() {
    if (!usuarioLogado()) {
        responderJson(['erro' => 'Você precisa entrar na conta primeiro.'], 401);
    }
    return (int)$_SESSION['usuario']['id'];
}
