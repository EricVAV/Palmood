<?php
require_once __DIR__ . '/../config/conexao.php';

$uid    = exigirLoginApi();
$metodo = $_SERVER['REQUEST_METHOD'];
$dados  = json_decode(file_get_contents('php://input'), true) ?? [];

// Lista cartões cadastrados do usuário
if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT id, titular, bandeira, ultimos_quatro, validade FROM formas_pagamento WHERE usuario_id = ? ORDER BY id DESC");
    $stmt->execute([$uid]);
    responderJson(['pagamentos' => $stmt->fetchAll()]);
}

// Salva referncia do cartão
if ($metodo === 'POST') {
    $titular  = trim($dados['titular'] ?? '');
    $bandeira = trim($dados['bandeira'] ?? '');
    $ultimos  = substr(preg_replace('/\D/', '', $dados['ultimos_quatro'] ?? ''), -4);
    $validade = trim($dados['validade'] ?? '');

    if ($titular === '' || $bandeira === '' || strlen($ultimos) !== 4 || $validade === '') {
        responderJson(['erro' => 'Preencha todos os dados do cartão corretamente (4 últimos dígitos).'], 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO formas_pagamento (usuario_id, titular, bandeira, ultimos_quatro, validade)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$uid, $titular, $bandeira, $ultimos, $validade]);

    responderJson(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
}

// Remove cartão
if ($metodo === 'DELETE') {
    $id = (int)($dados['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM formas_pagamento WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $uid]);
    responderJson(['ok' => true]);
}
