<?php
require_once __DIR__ . '/../config/conexao.php';

$uid    = exigirLoginApi();
$metodo = $_SERVER['REQUEST_METHOD'];
$dados  = json_decode(file_get_contents('php://input'), true) ?? [];

// Lista os endereços do usuárii
if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY id DESC");
    $stmt->execute([$uid]);
    responderJson(['enderecos' => $stmt->fetchAll()]);
}

// Salva um novo endereço
if ($metodo === 'POST') {
    $tipo        = trim($dados['tipo'] ?? 'Casa');
    $cep         = trim($dados['cep'] ?? '');
    $logradouro  = trim($dados['logradouro'] ?? '');
    $numero      = trim($dados['numero'] ?? '');
    $complemento = trim($dados['complemento'] ?? '');
    $cidade      = trim($dados['cidade'] ?? '');
    $estado      = mb_strtoupper(trim($dados['estado'] ?? ''));
    $referencia  = trim($dados['ponto_referencia'] ?? '');
    $telefone    = trim($dados['telefone'] ?? '');

    if ($cep === '' || $logradouro === '' || $cidade === '' || mb_strlen($estado) !== 2) {
        responderJson(['erro' => 'Preencha corretamente CEP, rua, cidade e UF (2 letras).'], 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO enderecos (usuario_id, tipo, logradouro, numero, complemento, cidade, estado, cep, ponto_referencia, telefone)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$uid, $tipo, $logradouro, $numero, $complemento, $cidade, $estado, $cep, $referencia, $telefone]);

    responderJson(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
}

// Remove endereco do usuário (desvinculando de pedidos antigos)
if ($metodo === 'DELETE') {
    $id = (int)($dados['id'] ?? 0);

    try {
        $pdo->beginTransaction();

        // Garante que pedidos antigos não bloqueiem a exclusão do endereço
        $desvincular = $pdo->prepare("UPDATE pedidos SET endereco_id = NULL WHERE endereco_id = ? AND usuario_id = ?");
        $desvincular->execute([$id, $uid]);

        $stmt = $pdo->prepare("DELETE FROM enderecos WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$id, $uid]);

        $pdo->commit();
        responderJson(['ok' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        responderJson(['erro' => 'Não foi possível remover este endereço.'], 500);
    }
}
