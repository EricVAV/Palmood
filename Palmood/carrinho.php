<?php
require_once __DIR__ . '/config/conexao.php';

// Exige login para acessar o carrinho
if (!usuarioLogado()) {
    $_SESSION['destino_apos_login'] = 'carrinho.php';
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Palmood-Carrinho</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
</head>

<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <div id="Checkout_container">
            <div class="master-container">
                <div class="card cart">
                    <div class="title">
                        <p>Carrinho</p>
                    </div>
                    <div class="products"></div>
                </div>

                <div class="card cupom">
                    <label class="title">Aplicar cupom</label>
                    <form class="form1">
                        <input type="text" placeholder="Digite seu cupom" class="input_field">
                        <button type="submit">Aplicar</button>
                    </form>
                </div>

                <div class="card checkout">
                    <label class="title">Checkout</label>
                    <div class="details">
                        <span>Subtotal atual:</span>
                        <span>R$ 0,00</span>
                        <span>Desconto aplicado:</span>
                        <span>R$ 0,00</span>
                    </div>
                    <div class="checkout--footer">
                        <label class="price">R$ 0,00</label>
                    </div>
                </div>
            </div>

            <div id="Check">
                <div id="Method">
                    <div id="close">
                        <label class="title">Método de pagamento</label>
                        <button class="fechar_c" type="button">x</button>
                    </div>

                    <div class="Select_met">
                        <div>
                            <button class="Btn1" type="button">
                                <div class="sign">+</div>
                                <div class="text">Adicionar Cartão</div>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="Endere">
                    <label class="title">Endereço para entrega</label>
                    <div class="adrow">
                        <div>
                            <button class="Btn1" type="button">
                                <div class="sign">+</div>
                                <div class="text">Adicionar endereço</div>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="Detail">
                    <label class="title">Detalhes</label>
                    <div class="details"></div>
                    <div id="Desc">
                        <div><img src="Img/folha.svg" alt=""></div>
                        <span>O veículo utilizado na entrega é 100% elétrico garantindo uma entrega limpa, sustentável e silenciosa!</span>
                    </div>
                    <div id="Frete">
                        <span>Entregue por:</span>
                        <span>Pmpelivery</span>
                        <span>Frete:</span>
                        <span>GRATIS</span>
                    </div>
                    <div class="checkout--footer">
                        <button class="checkout-btn" type="button">Finalizar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="nav_bs">
        <button id="voltar" type="button"></button>
        <button id="p_cart" type="button"></button>
    </div>

    <?php include __DIR__ . '/includes/perfil_modal.php'; ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>

</html>
