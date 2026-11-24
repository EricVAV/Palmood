<?php
// Só monta o modal do perfil se tiver usuário logado
if (!usuarioLogado()) return;

$uid = (int)$_SESSION['usuario']['id'];

// Busca os pedidos já finalizados com seus respectivos itens
$stmtPedidos = $pdo->prepare("
    SELECT p.id AS pedido_id, p.criado_em, ip.quantidade, ip.preco_unitario, pr.nome, pr.imagem_url
    FROM pedidos p
    JOIN itens_pedido ip ON ip.pedido_id = p.id
    JOIN produtos pr ON pr.id = ip.produto_id
    WHERE p.usuario_id = ?
    ORDER BY p.criado_em DESC, ip.id ASC
");
$stmtPedidos->execute([$uid]);
$comprasUsuario = $stmtPedidos->fetchAll();
?>
<section id="Profile_card">
    <div id="pt_bar">
        <label class="title">Perfil</label>
        <button id="fechar" type="button">x</button>
    </div>

    <div id="Profile_page">
        <div id="side_bar">
            <div id="Big_pic"></div>
            <label><?= h($_SESSION['usuario']['nome']) ?></label>
            <div class="ftt11">
                <button id="mcmc" class="f_ln11" type="button">
                    <div class="ico-com"></div>Minhas compras
                </button>
                <button id="enen" class="f_ln11" type="button">
                    <div class="ico-hom"></div>Endereços
                </button>
                <button id="pgpg" class="f_ln11" type="button">
                    <div class="ico-pag"></div>Pagamento
                </button>
            </div>
            <div class="quit-box"><button class="sair" type="button">Sair</button></div>
        </div>

        <div id="p_content">
            <!-- historico de comprass -->
            <div id="Minhas_compras">
                <div class="ttt"><span>Minhas compras</span></div>
                <div class="h_list">
                    <?php if (empty($comprasUsuario)): ?>
                        <div class="l_elem"><p>Você ainda não realizou nenhuma compra.</p></div>
                    <?php else: ?>
                        <?php foreach ($comprasUsuario as $item): ?>
                            <div class="l_elem">
                                <div class="p_ph" style="background-image: url('<?= h($item['imagem_url']) ?>');"></div>
                                <div class="p_info">
                                    <div class="Pr">
                                        <?= h($item['nome']) ?>
                                        <div class="price">
                                            <div><span>R$<?= number_format((float)$item['preco_unitario'], 2, ',', '.') ?></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p_qnt">
                                    <label>x<?= (int)$item['quantidade'] ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- endereços cadastrados -->
            <div id="Meus_Enderecos">
                <div class="ttt"><span>Endereços</span></div>
                <div class="h_list">
                    <div class="l_elem">
                        <div class="adrow">
                            <div>
                                <button class="Btn1" type="button">
                                    <div class="sign">+</div>
                                    <div class="text">Adicionar endereço</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- cartões cadastrados -->
            <div id="Meus_pag">
                <div class="ttt"><span>Pagamento</span></div>
                <div class="h_list">
                    <div class="l_elem">
                        <div class="Select_met">
                            <div>
                                <button class="Btn1" type="button">
                                    <div class="sign">+</div>
                                    <div class="text">Adicionar Cartão</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
