<?php
// Busca o usuario logado para alternar entre acesso e perfil
$usuario = usuarioLogado();
$paginaInicio = $usuario ? 'dashboard.php' : 'index.php';
?>
<div id="topmenu">
    <div id="logo"></div>

    <div id="ini">
        <form action="<?= $paginaInicio ?>"><button class="top_but" type="submit">Inicio</button></form>
        <div id="inv_card"></div>
    </div>

    <div id="contact">
        <form action="contatos.php"><button id="botao_contato" class="top_but" type="submit">Contatos</button></form>
        <div id="c_card">
            <div class="ftt1">
                <form action="contatos.php"><button class="f_ln1" type="submit"><div class="ico-zap"></div>(81)940028922</button></form>
                <form action="contatos.php"><button class="f_ln1" type="submit"><div class="ico-ins"></div>@palmood_rec</button></form>
                <form action="contatos.php"><button class="f_ln1" type="submit"><div class="ico-tw"></div>palmood_rec</button></form>
                <form action="contatos.php"><button class="f_ln1" type="submit"><div class="ico-gm"></div>palmood@rec</button></form>
            </div>
        </div>
    </div>

    <div id="bout">
        <form action="sobre.php"><button id="botao_sobre" class="top_but" type="submit">Sobre</button></form>
        <div id="s_card">
            <div class="ftt1">
                <form action="sobre.php#sobree"><button class="f_ln1" type="submit">Quem somos ?</button></form>
                <form action="sobre.php#objetivoo"><button class="f_ln1" type="submit">Nosso Objetivo</button></form>
                <form action="sobre.php#juntee"><button class="f_ln1" type="submit">Se junte a nós</button></form>
                <form action="sobre.php#falee"><button class="f_ln1" type="submit">Fale conosco</button></form>
            </div>
        </div>
    </div>

    <div id="prof">
        <?php if ($usuario): ?>
            <!-- Menu do usuaario logado -->
            <div id="ppp">
                <button id="botao_perfil" class="top_p_but" type="button"><span><?= h($usuario['nome']) ?></span></button>
                <div id="pic"></div>
            </div>
            <div id="p_card">
                <div class="ftt1">
                    <button id="perfil" class="f_ln1" type="button">
                        <div class="ico-pro"></div>Meu perfil
                    </button>
                </div>
                <div id="quit"><button class="sair" type="button">Sair</button></div>
            </div>
        <?php else: ?>
            <!-- Botão entrar -->
            <div id="acess">
                <form action="login.php"><button type="submit">Entrar</button></form>
            </div>
        <?php endif; ?>
    </div>
</div>
