<?php
require_once __DIR__ . '/../config/conexao.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$dados  = json_decode(file_get_contents('php://input'), true) ?? [];

// Se for visitante tentando adicionar item, guarda na sessão e manda para o login
if ($metodo === 'POST' && !usuarioLogado()) {
    $prodId = (int)($dados['produto_id'] ?? 0);
    if ($prodId > 0) {
        $_SESSION['produto_pendente'] = [
            'id'  => $prodId,
            'qty' => max(1, (int)($dados['quantidade'] ?? 1))
        ];
    }
    $_SESSION['destino_apos_login'] = 'dashboard.php';
    responderJson(['redirecionar' => 'login.php'], 401);
}

$uid = exigirLoginApi();

// Lista todos os itens do carrinho do usuário
if ($metodo === 'GET') {
    $stmt = $pdo->prepare("
        SELECT c.produto_id AS id, c.quantidade AS qtd,
               p.nome, p.origem, p.altura_maxima_cm, p.preco, p.imagem_url
        FROM carrinho c
        JOIN produtos p ON p.id = c.produto_id
        WHERE c.usuario_id = ?
        ORDER BY c.id ASC
    ");
    $stmt->execute([$uid]);
    $itens = $stmt->fetchAll();

    foreach ($itens as &$item) {
        $metros = $item['altura_maxima_cm'] / 100;
        $item['altura'] = rtrim(rtrim(number_format($metros, 1, ',', ''), '0'), ',') . ' metros';
        $item['preco']  = (float)$item['preco'];
        $item['qtd']    = (int)$item['qtd'];
        $item['id']     = (int)$item['id'];
    }

    responderJson(['itens' => $itens]);
}

// Adiciona produto ao carrinho
if ($metodo === 'POST') {
    $prodId = (int)($dados['produto_id'] ?? 0);
    $qtd    = max(1, (int)($dados['quantidade'] ?? 1));

    $existe = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $existe->execute([$prodId]);
    if (!$existe->fetch()) {
        responderJson(['erro' => 'Produto não encontrado.'], 404);
    }

    $stmt = $pdo->prepare("
        INSERT INTO carrinho (usuario_id, produto_id, quantidade)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)
    ");
    $stmt->execute([$uid, $prodId, $qtd]);

    responderJson(['ok' => true, 'mensagem' => 'Produto adicionado ao carrinho.']);
}

// Aumenta ou diminui quantidade (+1 ou -1)
if ($metodo === 'PATCH') {
    $prodId = (int)($dados['produto_id'] ?? 0);
    $delta  = (int)($dados['delta'] ?? 0);

    $stmt = $pdo->prepare("SELECT id, quantidade FROM carrinho WHERE usuario_id = ? AND produto_id = ?");
    $stmt->execute([$uid, $prodId]);
    $item = $stmt->fetch();

    if ($item) {
        $novaQtd = (int)$item['quantidade'] + $delta;
        if ($novaQtd <= 0) {
            $del = $pdo->prepare("DELETE FROM carrinho WHERE id = ?");
            $del->execute([$item['id']]);
        } else {
            $upd = $pdo->prepare("UPDATE carrinho SET quantidade = ? WHERE id = ?");
            $upd->execute([$novaQtd, $item['id']]);
        }
    }

    responderJson(['ok' => true]);
}

// Remove o item inteiro do carrinho
if ($metodo === 'DELETE') {
    $prodId = (int)($dados['produto_id'] ?? 0);
    $del = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = ? AND produto_id = ?");
    $del->execute([$uid, $prodId]);

    responderJson(['ok' => true]);
}
