// Inicia tudo quando o HTML carregar
document.addEventListener('DOMContentLoaded', () => {
    animarPainelLogin();
    controlarBotoesFlutuantes();
    configurarMenusDropdown();
    configurarModalPerfil();
    configurarLogout();
    configurarFiltrosProdutos();
    inicializarPalmood();
});

const Palmood = {
    itensCarrinho: [],
    descontoAtual: 0,
    enderecoSelecionado: null,
    pagamentoSelecionado: null,

    // chama as APIs
    async api(url, metodo = 'GET', corpo = null) {
        const opcoes = {
            method: metodo,
            headers: { 'Content-Type': 'application/json' }
        };
        if (corpo) opcoes.body = JSON.stringify(corpo);

        const resposta = await fetch(url, opcoes);
        const dados = await resposta.json().catch(() => ({}));

        if (dados.redirecionar) {
            location.href = dados.redirecionar;
            return null;
        }
        if (!resposta.ok) {
            throw new Error(dados.erro || 'Erro ao processar requisição.');
        }
        return dados;
    },

    // Formatar números para (R$)
    money(valor) {
        return Number(valor || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },

    // Mostra avisos na tela
    toast(mensagem) {
        let el = document.getElementById('palmood-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'palmood-toast';
            el.style.cssText = 'position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:#1e1e1e;color:#e9ecef;padding:12px 20px;border-radius:8px;z-index:9999';
            document.body.append(el);
        }
        el.textContent = mensagem;
        el.style.display = 'block';
        clearTimeout(this.toastTimer);
        this.toastTimer = setTimeout(() => { el.style.display = 'none'; }, 2600);
    },

    // Abre janelas para cadastrar endereço ou cartão
    modal(titulo, campos, aoSalvar) {
        document.getElementById('palmood-modal')?.remove();

        const overlay = document.createElement('div');
        overlay.id = 'palmood-modal';
        overlay.className = 'palmood-modal-overlay';

        const form = document.createElement('form');
        form.className = 'palmood-modal-form';
        form.innerHTML = `<div class="palmood-modal-head"><h2>${titulo}</h2><button type="button" class="fechar_c" aria-label="Fechar">×</button></div>`;

        campos.forEach(c => {
            const label = document.createElement('label');
            label.className = 'palmood-modal-label';
            label.textContent = c.label;

            const input = document.createElement('input');
            input.className = 'input_field palmood-modal-input';
            input.name = c.name;
            input.type = c.type || 'text';
            input.placeholder = c.placeholder || '';
            input.required = !!c.required;
            input.maxLength = c.maxLength || 150;
            input.autocomplete = 'off';

            label.append(input);
            form.append(label);
        });

        const acoes = document.createElement('div');
        acoes.className = 'palmood-modal-actions';
        acoes.innerHTML = '<button type="button" class="Limpar-btn">Cancelar</button><button type="submit" class="checkout-btn">Salvar</button>';
        form.append(acoes);

        overlay.append(form);
        document.body.append(overlay);

        const fechar = () => overlay.remove();
        form.querySelector('.fechar_c').onclick = fechar;
        form.querySelector('.Limpar-btn').onclick = fechar;
        overlay.addEventListener('click', e => { if (e.target === overlay) fechar(); });

        form.onsubmit = async e => {
            e.preventDefault();
            try {
                const dados = Object.fromEntries(new FormData(form));
                await aoSalvar(dados);
                fechar();
            } catch (err) {
                this.toast(err.message);
            }
        };
    },

    // Adiciona produto no carrinho via PHP
    async add(produtoId, qtd = 1) {
        try {
            const res = await this.api('api/carrinho.php', 'POST', { produto_id: produtoId, quantidade: qtd });
            if (res) this.toast(res.mensagem);
        } catch (err) {
            this.toast(err.message);
        }
    },

    // Altera quantidade do produto no carrinho
    async change(produtoId, delta) {
        try {
            await this.api('api/carrinho.php', 'PATCH', { produto_id: produtoId, delta });
            await this.carregarCarrinho();
        } catch (err) {
            this.toast(err.message);
        }
    },

    // Remove item do carrinho
    async remove(produtoId) {
        try {
            await this.api('api/carrinho.php', 'DELETE', { produto_id: produtoId });
            await this.carregarCarrinho();
        } catch (err) {
            this.toast(err.message);
        }
    },

    // Busca e mostra os itens no carrinho
    async carregarCarrinho() {
        const host = document.querySelector('.cart .products');
        if (!host) return;

        try {
            const res = await this.api('api/carrinho.php');
            this.itensCarrinho = res.itens || [];
            host.innerHTML = '';

            if (!this.itensCarrinho.length) {
                host.innerHTML = '<p>Seu carrinho está vazio.</p>';
            }

            let subtotal = 0;
            this.itensCarrinho.forEach(item => {
                subtotal += item.preco * item.qtd;

                const el = document.createElement('div');
                el.className = 'product';
                el.innerHTML = `
                    <div class="del"><button type="button" class="cart-icon-btn" aria-label="Remover"></button></div>
                    <img class="pimg" src="${item.imagem_url}" alt="">
                    <div class="cart-product-info"><span></span><p></p><p></p></div>
                    <div class="quantity">
                        <button type="button" class="quantity-btn">−</button>
                        <label></label>
                        <button type="button" class="quantity-btn">+</button>
                    </div>
                    <div class="price_small"></div>
                `;

                el.querySelector('span').textContent = item.nome;
                el.querySelectorAll('p')[0].textContent = item.origem;
                el.querySelectorAll('p')[1].textContent = item.altura;
                el.querySelector('label').textContent = item.qtd;
                el.querySelector('.price_small').textContent = this.money(item.preco * item.qtd);

                el.querySelector('.del button').onclick = () => this.remove(item.id);
                el.querySelectorAll('.quantity button')[0].onclick = () => this.change(item.id, -1);
                el.querySelectorAll('.quantity button')[1].onclick = () => this.change(item.id, 1);

                host.append(el);
            });

            const total = Math.max(0, subtotal - this.descontoAtual);
            const valores = document.querySelectorAll('.checkout .details span');
            if (valores.length >= 4) {
                valores[1].textContent = this.money(subtotal);
                valores[3].textContent = this.money(this.descontoAtual);
            }

            const rodape = document.querySelector('.checkout .checkout--footer .price');
            if (rodape) rodape.textContent = this.money(total);

            const detalhes = document.querySelector('#Detail .details');
            if (detalhes) {
                detalhes.innerHTML = `
                    <span>Subtotal:</span><span>${this.money(subtotal)}</span>
                    <span>Desconto:</span><span>${this.money(this.descontoAtual)}</span>
                    <span>Total:</span><strong>${this.money(total)}</strong>
                `;
            }
        } catch (err) {
            this.toast(err.message);
        }
    },

    // Abre janela para cadastrar endereço
    addAddress() {
        this.modal('Adicionar endereço', [
            { name: 'tipo', label: 'Identificação', required: true, placeholder: 'Casa' },
            { name: 'cep', label: 'CEP', required: true, maxLength: 9, placeholder: '50000-000' },
            { name: 'logradouro', label: 'Rua / Avenida', required: true },
            { name: 'numero', label: 'Número', required: true },
            { name: 'complemento', label: 'Complemento' },
            { name: 'cidade', label: 'Cidade', required: true },
            { name: 'estado', label: 'Estado (UF)', required: true, maxLength: 2, placeholder: 'PE' },
            { name: 'ponto_referencia', label: 'Ponto de referência' },
            { name: 'telefone', label: 'Telefone', required: true }
        ], async dados => {
            await this.api('api/enderecos.php', 'POST', dados);
            await this.carregarEnderecos();
            this.toast('Endereço salvo.');
        });
    },

    // Carrega endereços no checkout e no perfil
    async carregarEnderecos() {
        const containers = [...document.querySelectorAll('#Endere .adrow, #Meus_Enderecos .adrow')];
        if (!containers.length) return;

        try {
            const res = await this.api('api/enderecos.php');
            const lista = res.enderecos || [];

            // Seleciona o primeiro endereço automaticamente
            if (lista.length && !lista.some(e => Number(e.id) === Number(this.enderecoSelecionado))) {
                this.enderecoSelecionado = Number(lista[0].id);
            }

            containers.forEach(row => {
                row.querySelectorAll('.ad').forEach(x => x.remove());
                lista.forEach(a => {
                    const endId = Number(a.id);
                    const card = document.createElement('div');
                    card.className = 'ad' + (endId === Number(this.enderecoSelecionado) ? ' item-selecionado' : '');
                    card.innerHTML = `
                        <div class="t_bar">
                            <div class="hh"><div class="p_hm"></div><span></span></div>
                            <button type="button" class="ad_edit" aria-label="Remover endereço">×</button>
                        </div>
                        <div class="a_bd"><span>Endereço:</span><p></p><p></p><p></p><p></p></div>
                    `;

                    card.querySelector('.hh span').textContent = a.tipo;
                    const ps = card.querySelectorAll('.a_bd p');
                    ps[0].textContent = `${a.logradouro}, ${a.numero}${a.complemento ? ', ' + a.complemento : ''}`;
                    ps[1].textContent = `${a.cidade}, ${a.estado}`;
                    ps[2].textContent = `CEP: ${a.cep}`;
                    ps[3].textContent = `Tel: ${a.telefone || '-'}`;

                    // permite clicar no card para selecionar o endereço
                    card.onclick = () => {
                        this.enderecoSelecionado = endId;
                        this.carregarEnderecos();
                    };

                    card.querySelector('.ad_edit').onclick = async e => {
                        e.stopPropagation();
                        if (confirm('Remover este endereço?')) {
                            try {
                                await this.api('api/enderecos.php', 'DELETE', { id: endId });
                                if (Number(this.enderecoSelecionado) === endId) this.enderecoSelecionado = null;
                                await this.carregarEnderecos();
                                this.toast('Endereço removido.');
                            } catch (err) {
                                this.toast(err.message);
                            }
                        }
                    };

                    row.append(card);
                });
            });
        } catch (err) {
            // Ignora erro se o usuário ainda não estiver logado
        }
    },

    // Abre janela para cadastrar cartão
    addPayment() {
        this.modal('Adicionar forma de pagamento', [
            { name: 'titular', label: 'Nome do titular', required: true },
            { name: 'bandeira', label: 'Bandeira', required: true, placeholder: 'Visa, Mastercard...' },
            { name: 'ultimos_quatro', label: 'Últimos 4 dígitos do cartão', required: true, maxLength: 4, placeholder: '1234' },
            { name: 'validade', label: 'Validade (MM/AA)', required: true, maxLength: 5, placeholder: '12/29' }
        ], async dados => {
            await this.api('api/pagamentos.php', 'POST', dados);
            await this.carregarPagamentos();
            this.toast('Forma de pagamento adicionada.');
        });
    },

    // Carrega cartões no checkout e no perfil
    async carregarPagamentos() {
        const containers = [...document.querySelectorAll('#Method .Select_met, #Meus_pag .Select_met')];
        if (!containers.length) return;

        try {
            const res = await this.api('api/pagamentos.php');
            const lista = res.pagamentos || [];

            if (lista.length && !lista.some(p => Number(p.id) === Number(this.pagamentoSelecionado))) {
                this.pagamentoSelecionado = Number(lista[0].id);
            }

            containers.forEach(row => {
                row.querySelectorAll('.pa').forEach(x => x.remove());
                lista.forEach(p => {
                    const pagId = Number(p.id);
                    const el = document.createElement('div');
                    el.className = 'pa' + (pagId === Number(this.pagamentoSelecionado) ? ' item-selecionado' : '');
                    el.innerHTML = '<div class="p_im"></div><span></span><button type="button" class="ad_edit" aria-label="Remover cartão">×</button>';
                    el.querySelector('span').textContent = `${p.bandeira} •••• ${p.ultimos_quatro}`;

                    // peermite selecionar qual cartão sera usatilazado
                    el.onclick = () => {
                        this.pagamentoSelecionado = pagId;
                        this.carregarPagamentos();
                    };

                    el.querySelector('.ad_edit').onclick = async e => {
                        e.stopPropagation();
                        if (confirm('Remover esta forma de pagamento?')) {
                            try {
                                await this.api('api/pagamentos.php', 'DELETE', { id: pagId });
                                if (Number(this.pagamentoSelecionado) === pagId) this.pagamentoSelecionado = null;
                                await this.carregarPagamentos();
                                this.toast('Forma de pagamento removida.');
                            } catch (err) {
                                this.toast(err.message);
                            }
                        }
                    };

                    row.append(el);
                });
            });
        } catch (err) {
            // Ignora erro se o usuário ainda não estiver logado
        }
    }
};

// Liga compra, cupom, formulário e checkout
function inicializarPalmood() {
    const estaLogado = !!document.getElementById('ppp');

    // Botões "+" e "Comprar" dos cards de produtos
    document.querySelectorAll('.produto').forEach(card => {
        const id = Number(card.dataset.id);
        const botoes = card.querySelectorAll('.c_but button, .b_but button');
        if (botoes[0]) botoes[0].onclick = () => Palmood.add(id);
        if (botoes[1]) botoes[1].onclick = () => Palmood.add(id);
    });

    // Botão flutuante do carrinho
    const cartButton = document.getElementById('p_cart');
    if (cartButton) {
        cartButton.hidden = !estaLogado;
        cartButton.style.display = estaLogado ? 'flex' : 'none';
        cartButton.onclick = () => { location.href = estaLogado ? 'carrinho.php' : 'login.php'; };
    }

    // Botões de adicionar endereço ou cartão
    document.querySelectorAll('.Btn1').forEach(btn => {
        btn.addEventListener('click', () => {
            const texto = btn.textContent.toLowerCase();
            texto.includes('endereço') ? Palmood.addAddress() : Palmood.addPayment();
        });
    });

    // Botão fechar da tela de checkout
    document.querySelector('.fechar_c')?.addEventListener('click', () => {
        const anterior = document.referrer;
        if (anterior && new URL(anterior, location.href).origin === location.origin && !anterior.endsWith('carrinho.php')) {
            history.back();
        } else {
            location.href = estaLogado ? 'dashboard.php' : 'index.php';
        }
    });

    // Validação de cupom no servidor PH
    document.querySelector('.form1')?.addEventListener('submit', async e => {
        e.preventDefault();
        const codigo = e.currentTarget.querySelector('input').value.trim();
        try {
            const res = await Palmood.api('api/cupom.php', 'POST', { codigo });
            Palmood.descontoAtual = res.desconto || 0;
            Palmood.toast(res.mensagem);
            await Palmood.carregarCarrinho();
        } catch (err) {
            Palmood.toast(err.message);
        }
    });

    // finaliza r o pedido no banco de dados
    document.querySelector('.checkout-btn')?.addEventListener('click', async () => {
        if (!Palmood.itensCarrinho.length) return Palmood.toast('Adicione produtos ao carrinho.');
        if (!Palmood.enderecoSelecionado) return Palmood.toast('Adicione ou selecione um endereço de entrega.');
        if (!Palmood.pagamentoSelecionado) return Palmood.toast('Adicione ou selecione uma forma de pagamento.');

        try {
            const res = await Palmood.api('api/pedidos.php', 'POST', {
                endereco_id: Palmood.enderecoSelecionado,
                pagamento_id: Palmood.pagamentoSelecionado
            });
            Palmood.toast(res.mensagem);
            setTimeout(() => { location.href = 'dashboard.php'; }, 1400);
        } catch (err) {
            Palmood.toast(err.message);
        }
    });

    // Envia do formulário de cadastro
    document.querySelectorAll('.container-cadastro form').forEach(form => {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const inputs = form.querySelectorAll('input');
            try {
                const res = await Palmood.api('api/auth.php', 'POST', {
                    acao: 'cadastrar',
                    nome: inputs[0].value,
                    email: inputs[1].value,
                    senha: inputs[2].value
                });
                location.href = res.destino;
            } catch (err) {
                alert(err.message);
            }
        });
    });

    // Envio do formulário de login
    document.querySelectorAll('.container-login form').forEach(form => {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const inputs = form.querySelectorAll('input');
            try {
                const res = await Palmood.api('api/auth.php', 'POST', {
                    acao: 'login',
                    email: inputs[0].value,
                    senha: inputs[1].value
                });
                location.href = res.destino;
            } catch (err) {
                alert(err.message);
            }
        });
    });

    document.querySelectorAll('.link-esqueceu-senha').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            Palmood.toast('Entre em contato pelo suporte Palmood para redefinir sua senha.');
        });
    });

    // Carrega dados se estiver logado
    if (estaLogado) {
        Palmood.carregarCarrinho();
        Palmood.carregarEnderecos();
        Palmood.carregarPagamentos();
    }
}

// Filtra os produtos por pesquisa, origem e altura
function configurarFiltrosProdutos() {
    const busca = document.getElementById('busca_produto');
    const selOrigem = document.getElementById('origem');
    const selAltura = document.getElementById('altura');
    const cards = [...document.querySelectorAll('#grade .produto')];

    if (!busca || !selOrigem || !selAltura || !cards.length) return;

    const filtrar = () => {
        const termo = busca.value.trim().toLowerCase();
        const origem = selOrigem.value;
        const altura = selAltura.value;

        cards.forEach(card => {
            const nomeCard = card.dataset.nome || '';
            const origemCard = card.dataset.origem || '';
            const alturaCm = Number(card.dataset.altura || 0);

            const bateNome = !termo || nomeCard.includes(termo) || origemCard.includes(termo);

            let bateOrigem = true;
            if (origem === 'asia') bateOrigem = origemCard.includes('ásia') || origemCard.includes('asiático');
            else if (origem === 'africa') bateOrigem = origemCard.includes('áfrica') || origemCard.includes('madagascar');
            else if (origem === 'brasil') bateOrigem = origemCard.includes('brasil');
            else if (origem === 'caribe') bateOrigem = origemCard.includes('antilhas') || origemCard.includes('américa') || origemCard.includes('méxico');
            else if (origem === 'ilhas') bateOrigem = origemCard.includes('canárias') || origemCard.includes('malásia') || origemCard.includes('grécia');

            let bateAltura = true;
            if (altura === 'ate10') bateAltura = alturaCm <= 1000;
            else if (altura === '10a20') bateAltura = alturaCm > 1000 && alturaCm <= 2000;
            else if (altura === '20a40') bateAltura = alturaCm > 2000;

            card.style.display = (bateNome && bateOrigem && bateAltura) ? 'flex' : 'none';
        });
    };

    busca.addEventListener('input', filtrar);
    selOrigem.addEventListener('change', filtrar);
    selAltura.addEventListener('change', filtrar);
}

// Alterna animação de Login e Cadastro
function animarPainelLogin() {
    const c = document.getElementById('caixa-principal');
    const a = document.getElementById('btn-cadastrar');
    const b = document.getElementById('btn-entrar');
    if (a && b && c) {
        a.onclick = () => c.classList.add('painel-ativo');
        b.onclick = () => c.classList.remove('painel-ativo');
    }
}

// mostra ou esconde botões de voltar ao topo e carrinho
function controlarBotoesFlutuantes() {
    const b = document.getElementById('voltar');
    const cart = document.getElementById('p_cart');
    const estaLogado = !!document.getElementById('ppp');

    const atualizar = () => {
        const mostrar = window.scrollY > 100;
        if (b) {
            b.style.width = mostrar ? '48px' : '0px';
            b.style.height = mostrar ? '48px' : '0px';
            b.style.opacity = mostrar ? '1' : '0';
        }
        if (cart && estaLogado) {
            cart.style.display = 'flex';
            cart.style.width = mostrar ? '48px' : '0px';
            cart.style.height = mostrar ? '48px' : '0px';
            cart.style.opacity = mostrar ? '1' : '0';
        } else if (cart) {
            cart.style.display = 'none';
        }
    };

    window.addEventListener('scroll', atualizar);
    atualizar();
    if (b) b.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Abre e fecha menu
function configurarMenusDropdown() {
    const pares = [['botao_contato', 'c_card'], ['botao_sobre', 's_card'], ['botao_perfil', 'p_card']];
    pares.forEach(([bid, cid]) => {
        const b = document.getElementById(bid);
        const c = document.getElementById(cid);
        if (!b || !c) return;

        b.addEventListener('mouseenter', () => { c.style.display = 'flex'; });
        const fechar = () => { if (!b.matches(':hover') && !c.matches(':hover')) c.style.display = 'none'; };
        b.addEventListener('mouseleave', () => setTimeout(fechar, 150));
        c.addEventListener('mouseleave', fechar);
    });
}

// Controla abertura e cards no Perfil
function configurarModalPerfil() {
    const profile = document.getElementById('Profile_card');
    const main = document.querySelector('main');
    if (!profile || !main) return;

    const abas = { mcmc: 'Minhas_compras', enen: 'Meus_Enderecos', pgpg: 'Meus_pag' };
    Object.entries(abas).forEach(([btnId, abaId]) => {
        document.getElementById(btnId)?.addEventListener('click', () => {
            Object.values(abas).forEach(x => { document.getElementById(x).style.display = 'none'; });
            document.getElementById(abaId).style.display = 'flex';
        });
    });

    document.getElementById('perfil')?.addEventListener('click', () => {
        main.style.display = 'none';
        profile.style.display = 'flex';
        Object.values(abas).forEach(x => { document.getElementById(x).style.display = 'none'; });
        document.getElementById('Minhas_compras').style.display = 'flex';
    });

    document.getElementById('fechar')?.addEventListener('click', () => {
        main.style.display = 'flex';
        profile.style.display = 'none';
    });
}

// Encerra a sessão no servidor PHP
function configurarLogout() {
    document.querySelectorAll('.sair').forEach(b => {
        b.onclick = async () => {
            if (confirm('Deseja realmente sair?')) {
                const res = await Palmood.api('api/auth.php', 'POST', { acao: 'logout' });
                location.href = res.destino;
            }
        };
    });
}
