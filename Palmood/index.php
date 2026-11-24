<?php
require_once __DIR__ . '/config/conexao.php';

// Se já estiver logado, manda direto para o dashboard
if (usuarioLogado()) {
    header('Location: dashboard.php');
    exit;
}

// Carrega todos os produtos cadastrados no banco
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palmood-Inicio</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <div id="carrosel">
            <div id="mask"></div>
        </div>

        <div id="filtro">
            <div id="search"><input type="text" id="busca_produto" placeholder="Pesquisar"></div>
            <div class="seletores">
                <div class="sel_for">
                    <label for="origem">Origem:</label>
                    <select name="origem" id="origem" class="seletor">
                        <option value="todos" selected>Todos</option>
                        <option value="asia">Ásia</option>
                        <option value="africa">África</option>
                        <option value="brasil">Brasil</option>
                        <option value="caribe">Caribe / Antilhas</option>
                        <option value="ilhas">Ilhas / Outros</option>
                    </select>
                </div>
                <div class="sel_for">
                    <label for="altura">Altura:</label>
                    <select name="altura" id="altura" class="seletor">
                        <option value="todos" selected>Todos</option>
                        <option value="ate10">Até 10 metros</option>
                        <option value="10a20">10 a 20 metros</option>
                        <option value="20a40">20 a 40 metros</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="grade">
            <?php foreach ($produtos as $p): ?>
                <div class="produto"
                     data-id="<?= (int)$p['id'] ?>"
                     data-nome="<?= h(mb_strtolower($p['nome'])) ?>"
                     data-origem="<?= h(mb_strtolower($p['origem'])) ?>"
                     data-altura="<?= (int)$p['altura_maxima_cm'] ?>">
                    <div class="Pd"><img src="<?= h($p['imagem_url']) ?>" alt="<?= h($p['nome']) ?>"></div>
                    <div class="Pr">
                        <?= h($p['nome']) ?>
                        <div class="price_p">
                            <div><span>R$<?= number_format((float)$p['preco'], 2, ',', '.') ?></span></div>
                            <div class="but_p">
                                <div class="c_but"><button type="button">+</button></div>
                                <div class="b_but"><button type="button">Comprar</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="nav_bs">
            <button id="voltar" type="button"></button>
            <button id="p_cart" type="button" hidden></button>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
