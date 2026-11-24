<?php
require_once __DIR__ . '/../config/conexao.php';

$uid   = exigirLoginApi();
$dados = json_decode(file_get_contents('php://input'), true) ?? [];
$codigo = mb_strtoupper(trim($dados['codigo'] ?? ''));

// Calcula o subtotal
$stmtSub = $pdo->prepare("
    SELECT COALESCE(SUM(c.quantidade * p.preco), 0) AS subtotal
    FROM carrinho c
    JOIN produtos p ON p.id = c.produto_id
    WHERE c.usuario_id = ?
");
$stmtSub->execute([$uid]);
$subtotal = (float)$stmtSub->fetchColumn();

if ($codigo === '') {
    unset($_SESSION['cupom_aplicado']);
    responderJson(['ok' => false, 'desconto' => 0, 'mensagem' => 'Digite um código de cupom.']);
}

$stmt = $pdo->prepare("
    SELECT * FROM cupons
    WHERE codigo = ? AND ativo = 1 AND NOW() BETWEEN data_inicio AND data_fim
");
$stmt->execute([$codigo]);
$cupom = $stmt->fetch();

if (!$cupom) {
    unset($_SESSION['cupom_aplicado']);
    responderJson(['ok' => false, 'desconto' => 0, 'mensagem' => 'Cupom inválido ou expirado.']);
}

if ($cupom['limite_usos'] !== null && (int)$cupom['usos_atuais'] >= (int)$cupom['limite_usos']) {
    unset($_SESSION['cupom_aplicado']);
    responderJson(['ok' => false, 'desconto' => 0, 'mensagem' => 'Este cupom atingiu o limite de usos.']);
}

if ($subtotal < (float)$cupom['valor_minimo_pedido']) {
    responderJson([
        'ok'       => false,
        'desconto' => 0,
        'mensagem' => 'Valor mínimo de R$ ' . number_format((float)$cupom['valor_minimo_pedido'], 2, ',', '.') . ' não atingido.'
    ]);
}

$desconto = $cupom['tipo'] === 'porcentagem'
    ? round($subtotal * ((float)$cupom['valor'] / 100), 2)
    : min($subtotal, (float)$cupom['valor']);

$_SESSION['cupom_aplicado'] = $cupom['codigo'];

responderJson([
    'ok'       => true,
    'desconto' => $desconto,
    'mensagem' => 'Cupom aplicado com sucesso!'
]);
