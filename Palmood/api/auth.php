<?php
require_once __DIR__ . '/../config/conexao.php';

$dados = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$acao  = $dados['acao'] ?? '';

// Função auxiliar para salvar item pendente
function processarItemPendente($pdo, $usuarioId) {
    if (!empty($_SESSION['produto_pendente']) && !empty($_SESSION['produto_pendente']['id'])) {
        $prodId = (int)$_SESSION['produto_pendente']['id'];
        $qtd    = max(1, (int)($_SESSION['produto_pendente']['qty'] ?? 1));

        // Confirma se o produto realmente existe no banco antes de inserir no carrinho
        $checkProd = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
        $checkProd->execute([$prodId]);

        if ($checkProd->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO carrinho (usuario_id, produto_id, quantidade)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantidade = quantidade + VALUES(quantidade)
            ");
            $stmt->execute([$usuarioId, $prodId, $qtd]);
        }
    }
    unset($_SESSION['produto_pendente']);
}

if ($acao === 'cadastrar') {
    $nome  = trim($dados['nome'] ?? '');
    $email = mb_strtolower(trim($dados['email'] ?? ''));
    $senha = $dados['senha'] ?? '';

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($senha) < 6) {
        responderJson(['erro' => 'Preencha todos os campos. A senha precisa ter no mínimo 6 caracteres.'], 400);
    }

    // Verifica se o e-mail já existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        responderJson(['erro' => 'Este e-mail já está cadastrado.'], 409);
    }

    // Criptografa a senha antes de salvar no banco
    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $hash]);

    $novoId = (int)$pdo->lastInsertId();
    $_SESSION['usuario'] = ['id' => $novoId, 'nome' => $nome, 'email' => $email];

    processarItemPendente($pdo, $novoId);

    $destino = $_SESSION['destino_apos_login'] ?? 'dashboard.php';
    unset($_SESSION['destino_apos_login']);

    responderJson(['ok' => true, 'destino' => $destino]);
}

if ($acao === 'login') {
    $email = mb_strtolower(trim($dados['email'] ?? ''));
    $senha = $dados['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($senha, $user['senha'])) {
        responderJson(['erro' => 'E-mail ou senha incorretos.'], 401);
    }

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'    => (int)$user['id'],
        'nome'  => $user['nome'],
        'email' => $user['email']
    ];

    processarItemPendente($pdo, (int)$user['id']);

    $destino = $_SESSION['destino_apos_login'] ?? 'dashboard.php';
    unset($_SESSION['destino_apos_login']);

    responderJson(['ok' => true, 'destino' => $destino]);
}

if ($acao === 'logout') {
    session_unset();
    session_destroy();
    responderJson(['ok' => true, 'destino' => 'index.php']);
}

responderJson(['erro' => 'Ação inválida.'], 400);
