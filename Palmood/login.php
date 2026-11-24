<?php
require_once __DIR__ . '/config/conexao.php';

if (usuarioLogado()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <div class="caixa-principal" id="caixa-principal">
            <div class="container-formulario container-cadastro">
                <form class="formulario" action="#">
                    <h1>Criar Conta</h1>
                    <input class="campo-texto" type="text" name="nome" placeholder="Nome" autocomplete="name" required />
                    <input class="campo-texto" type="email" name="email" placeholder="E-mail" autocomplete="email" required />
                    <input class="campo-texto" type="password" name="senha" placeholder="Senha (mínimo 6 caracteres)" autocomplete="new-password" minlength="6" required />
                    <button class="botao-principal" type="submit">Cadastrar</button>
                </form>
            </div>

            <div class="container-formulario container-login">
                <form class="formulario" action="#">
                    <h1>Entrar</h1>
                    <input class="campo-texto" type="email" name="email" placeholder="E-mail" autocomplete="email" required />
                    <input class="campo-texto" type="password" name="senha" placeholder="Senha" autocomplete="current-password" required />
                    <a class="link-esqueceu-senha" href="#">Esqueceu sua senha?</a>
                    <button class="botao-principal" type="submit">Entrar</button>
                </form>
            </div>

            <div class="container-sobreposicao">
                <div class="sobreposicao">
                    <div class="painel-sobreposicao painel-esquerda">
                        <h1>Bem-vindo(a) de volta!</h1>
                        <p class="texto-painel">Para se manter conectado conosco, faça login com suas informações pessoais</p>
                        <button class="botao-principal botao-transparente" id="btn-entrar" type="button">Entrar</button>
                    </div>
                    <div class="painel-sobreposicao painel-direita">
                        <h1>Olá, amigo(a)!</h1>
                        <p class="texto-painel">Insira seus dados pessoais e comece sua jornada conosco</p>
                        <button class="botao-principal botao-transparente" id="btn-cadastrar" type="button">Cadastrar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
