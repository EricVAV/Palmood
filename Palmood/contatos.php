<?php require_once __DIR__ . '/config/conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palmood-Contatos</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <div id="ab">
            <div class="flogo"></div>
            <h1>CONTATOS</h1>
            <div id="cont">
                <button class="f_ln1" type="button"><div class="ico-zap"></div>(81)940028922</button>
                <button class="f_ln1" type="button"><div class="ico-ins"></div>@palmood_rec</button>
                <button class="f_ln1" type="button"><div class="ico-tw"></div>palmood_rec</button>
                <button class="f_ln1" type="button"><div class="ico-gm"></div>palmood@rec</button>
            </div>
        </div>

        <div id="nav_bs">
            <button id="voltar" type="button"></button>
            <button id="p_cart" type="button"></button>
        </div>
    </main>

    <?php include __DIR__ . '/includes/perfil_modal.php'; ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
