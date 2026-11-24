<?php
require_once __DIR__ . '/../config/conexao.php';

$uid   = exigirLoginApi();
$dados = json_decode(file_get_contents('php://input'), true) ?? [];

$enderecoId  = (int)($dados['endereco_id'] ?? 0);
$pagamentoId = (int)($dados['pagamento_id'] ?? 0);

// Valida se o endereco pertence ao usuário logado
$stmtEnd = $pdo->prepare("SELECT id FROM enderecos WHERE id = ? AND usuario_id = ?");
$stmtEnd->execute([$enderecoId, $uid]);
if (!$stmtEnd->fetch()) {
    responderJson(['erro' => 'Selecione um endereço de entrega válido.'], 400);
}

// Valida se a forma de pagamento pertence ao usuá´rio logado
$stmtPag = $pdo->prepare("SELECT id, bandeira, ultimos_quatro FROM formas_pagamento WHERE id = ? AND usuario_id = ?");
$stmtPag->execute([$pagamentoId, $uid]);
$pagamento = $stmtPag->fetch();
if (!$pagamento) {
    responderJson(['erro' => 'Selecione uma forma de pagamento válida.'], 400);
}

// Busca os itens reais do carrinho no banco
$stmtCarrinho = $pdo->prepare("
    SELECT c.produto_id, c.quantidade, p.preco
    FROM carrinho c
    JOIN produtos p ON p.id = c.produto_id
    WHERE c.usuario_id = ?
");
$stmtCarrinho->execute([$uid]);
$itens = $stmtCarrinho->fetchAll();

if (empty($itens)) {
    responderJson(['erro' => 'Seu carrinho está vazio.'], 400);
}

// Recalcula o subtotal no servidor (segurança)
$subtotal = 0.0;
foreach ($itens as $item) {
    $subtotal += (float)$item['preco'] * (int)$item['quantidade'];
}

// Recalcula o cupom se houver algum aplicado
$cupomId  = null;
$desconto = 0.0;
if (!empty($_SESSION['cupom_aplicado'])) {
    $stmtCup = $pdo->prepare("
        SELECT * FROM cupons
        WHERE codigo = ? AND ativo = 1 AND NOW() BETWEEN data_inicio AND data_fim
    ");
    $stmtCup->execute([$_SESSION['cupom_aplicado']]);
    $cupom = $stmtCup->fetch();

    if ($cupom && $subtotal >= (float)$cupom['valor_minimo_pedido']) {
        $cupomId  = (int)$cupom['id'];
        $desconto = $cupom['tipo'] === 'porcentagem'
            ? round($subtotal * ((float)$cupom['valor'] / 100), 2)
            : min($subtotal, (float)$cupom['valor']);
    }
}

$total = max(0, round($subtotal - $desconto, 2));
$descricaoPagamento = $pagamento['bandeira'] . ' •••• ' . $pagamento['ultimos_quatro'];

try {
    $pdo->beginTransaction();

    // Cria o pedido
    $insPedido = $pdo->prepare("
        INSERT INTO pedidos (usuario_id, cupom_id, endereco_id, forma_pagamento, subtotal, frete, desconto, total, status, data_entrega)
        VALUES (?, ?, ?, ?, ?, 0.00, ?, ?, 'confirmado', DATE_ADD(CURDATE(), INTERVAL 3 DAY))
    ");
    $insPedido->execute([$uid, $cupomId, $enderecoId, $descricaoPagamento, $subtotal, $desconto, $total]);
    $pedidoId = (int)$pdo->lastInsertId();

    // Grava cada produto no historico do pedido
    $insItem = $pdo->prepare("
        INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($itens as $item) {
        $insItem->execute([$pedidoId, $item['produto_id'], $item['quantidade'], $item['preco']]);
    }

    // Incrementa uso do cupom se for utilizado
    if ($cupomId) {
        $updCup = $pdo->prepare("UPDATE cupons SET usos_atuais = usos_atuais + 1 WHERE id = ?");
        $updCup->execute([$cupomId]);
    }

    // Limpa o carrinho
    $limpa = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = ?");
    $limpa->execute([$uid]);

    unset($_SESSION['cupom_aplicado']);
    $pdo->commit();

    responderJson(['ok' => true, 'pedido_id' => $pedidoId, 'mensagem' => 'Pedido #' . $pedidoId . ' finalizado com sucesso!']);
} catch (Exception $e) {
    $pdo->rollBack();
    responderJson(['erro' => 'Falha ao finalizar o pedido. Tente novamente.'], 500);
}
