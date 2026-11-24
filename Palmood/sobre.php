<?php require_once __DIR__ . '/config/conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palmood-Sobre</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <div id="ab">
            <div class="flogo"></div>
            <h1 id="sobree">QUEM SOMOS?</h1>
            <p>Somos a palmood recife, uma empresa especializada em plantas nativas e exóticas que surgiu atravez do
                sonho de ver um mundo mais verde e vivo, presamos por sempre manter nossas plantas bem, até porque elas
                também são seres vivos não é mesmo? Ficou curioso(a) pra saber mais sobre agente? continua lendo nossa
                página que vamos te responder tudooo!</p>

            <h1 id="objetivoo">NOSSO OBJETIVO</h1>
            <p>Nosso objetivo é 100% coerente com nossas diretrizes iniciais que são tornar o mundo mais verde e mais
                vivo! Mas como vamos fazer isso? Acreditamos que tornando mais facíl o acesso a uma variedade gigantesca
                de exemplares da nossa flora nativa e também exóticas de forma responsável vamos conseguir alcançar
                ainda mais palmooders e conscientizalos sobre a importancia de matermos nossa natureza sempre viva!</p>

            <h1 id="juntee">JUNTE-SE A NÓS</h1>
            <p>Eae gostou da nossa ideia não foi? Então se quiser se juntar ao nosso time a hora é agora, fale com
                agente nos nossos canais oficiais listados logo mais abaixo nesta página, e nos fale mais sobre você,
                vamos adorar te conhecer melhor e te oferecer uma vaga que se encaixe no seu perfil sem aquela
                buracracia chata e sem espera infinita! Não é pra você mais conhece alguem que gostaria de fazer parte
                do nosso time? Compartilha nossos contatos com ele agora mesmo!</p>

            <h1 id="falee">FALE CONOSCO</h1>
            <p>Oxe! ainda não falou com agente? ta esperando oque? vem ser Palmooder!</p>

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
