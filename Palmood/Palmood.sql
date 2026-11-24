CREATE DATABASE IF NOT EXISTS Palmood CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Palmood;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de endereços de entrega
CREATE TABLE enderecos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo VARCHAR(50) DEFAULT 'Casa',
    logradouro VARCHAR(255) NOT NULL,
    numero VARCHAR(20),
    complemento VARCHAR(100),
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    ponto_referencia VARCHAR(255),
    telefone VARCHAR(20),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de cartões
CREATE TABLE formas_pagamento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    titular VARCHAR(150) NOT NULL,
    bandeira VARCHAR(40) NOT NULL,
    ultimos_quatro CHAR(4) NOT NULL,
    validade VARCHAR(7) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Categorias das planta
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE
);

-- grade de produtos
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    altura_maxima_cm INT NOT NULL,
    origem VARCHAR(100) NOT NULL,
    frete_gratis BOOLEAN DEFAULT FALSE,
    nativa BOOLEAN DEFAULT FALSE,
    facil_cultivo BOOLEAN DEFAULT FALSE,
    exotica BOOLEAN DEFAULT FALSE,
    imagem_url VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Itens salvos no carrinho
CREATE TABLE carrinho (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    UNIQUE(usuario_id, produto_id)
);

-- Cupons disponíveis
CREATE TABLE cupons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    tipo VARCHAR(20) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo_pedido DECIMAL(10,2) DEFAULT 0.00,
    limite_usos INT DEFAULT NULL,
    usos_atuais INT DEFAULT 0,
    data_inicio DATETIME NOT NULL,
    data_fim DATETIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

-- Pedidos finalizados
CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    cupom_id INT NULL,
    endereco_id INT NULL,
    forma_pagamento VARCHAR(80) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'confirmado',
    data_entrega DATE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (endereco_id) REFERENCES enderecos(id) ON DELETE SET NULL,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
);

-- Produtos de cada pedido
CREATE TABLE itens_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    desconto_unitario DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    avaliacao_estrelas INT CHECK (avaliacao_estrelas BETWEEN 1 AND 5),
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

INSERT INTO categorias (id, nome) VALUES
(1, 'Palmeiras e Coqueiros'),
(2, 'Árvores Frutíferas'),
(3, 'Plantas Ornamentais'),
(4, 'Árvores de Sombra');

INSERT INTO produtos (id, categoria_id, nome, preco, altura_maxima_cm, origem, frete_gratis, nativa, facil_cultivo, exotica, imagem_url) VALUES
(1, 1, 'Muda de Phoenix Canarienses 45 cm', 249.99, 1500, 'Ilhas Canárias', false, false, true, true, 'Img/palmeira-das-canarias.webp'),
(2, 1, 'Muda de Palmeira Imperial 1,5 metro', 149.99, 4000, 'Antilhas', false, false, true, true, 'Img/palmeira-imperial.webp'),
(3, 3, 'Muda de Ficus Elástica 1 metro', 99.99, 3000, 'Ásia Tropical', true, false, true, true, 'Img/ficus_elastica_burgundy.webp'),
(4, 1, 'Coco para plantio 1 unidade', 8.50, 3000, 'Sudeste Asiático', false, false, true, false, 'Img/coco.webp'),
(5, 2, 'Muda de mangueira 1 metro', 89.99, 3000, 'Ásia (Índia)', false, false, true, false, 'Img/manga.webp'),
(6, 4, 'Muda de amendoeira da praia 1 metro', 79.99, 3500, 'Malásia e Austrália', true, false, true, true, 'Img/amendoeira-da-praia.webp'),
(7, 1, 'Muda de Palmeira Azul 60 cm', 189.00, 1200, 'Madagascar', false, false, false, true, 'Img/palmeira-azul.webp'),
(8, 1, 'Muda de Coqueiro Anão Verde', 45.00, 1200, 'Brasil', false, true, true, false, 'Img/coqueiro-anao.webp'),
(9, 2, 'Muda de Jabuticaba Híbrida Produzindo', 120.00, 400, 'Brasil', false, true, true, false, 'Img/jabuticaba-hibrida.webp'),
(10, 2, 'Muda de Limão Siciliano', 55.50, 400, 'Ásia', false, false, true, false, 'Img/limao-siciliano.webp'),
(11, 2, 'Muda de Amora Gigante Portuguesa', 39.90, 500, 'Ásia', true, false, true, false, 'Img/amora.webp'),
(12, 3, 'Muda de Costela de Adão (Monstera)', 69.90, 300, 'México', false, false, true, true, 'Img/costela-de-adao.webp'),
(13, 3, 'Muda de Espada de São Jorge', 25.00, 90, 'África', false, false, true, false, 'Img/espada-de-sao-jorge.webp'),
(14, 3, 'Muda de Jasmim Manga Adulta', 150.00, 600, 'América Central', false, false, true, true, 'Img/jasmim-manga.webp'),
(15, 4, 'Muda de Ipê Amarelo Cascudo', 35.00, 2000, 'Brasil', true, true, true, false, 'Img/ipe-amarelo.webp'),
(16, 4, 'Muda de Ipê Roxo Bola', 35.00, 2500, 'Brasil', true, true, true, false, 'Img/ipe-roxo-de-bola.webp'),
(17, 2, 'Muda de Pitanga Vermelha do Cerrado', 32.00, 600, 'Brasil', false, true, true, false, 'Img/pitanga.webp'),
(18, 2, 'Muda de Acerola Clone BRS', 29.90, 400, 'Antilhas', false, false, true, false, 'Img/acerola.webp'),
(19, 1, 'Muda de Palmeira Areca Bambu', 49.90, 900, 'Madagascar', false, false, true, true, 'Img/areca-bambu.webp'),
(20, 1, 'Muda de Palmeira Leque', 115.00, 400, 'Ásia', false, false, false, true, 'Img/palmeira-leque-australiana.webp'),
(21, 3, 'Muda de Cróton Brasileirinho', 22.00, 250, 'Ásia Oriental', false, false, true, false, 'Img/croton.webp'),
(22, 2, 'Muda de Romã Gigante', 42.00, 500, 'Grécia', false, false, true, true, 'Img/roma.webp'),
(23, 2, 'Muda de Videira Uva Niágara Rosada', 38.00, 600, 'América do Norte', false, false, false, true, 'Img/uva.webp'),
(24, 4, 'Muda de Jacarandá Mimoso', 40.00, 1500, 'Brasil', false, true, true, false, 'Img/jacaranda-mimoso.webp'),
(25, 3, 'Muda de Samambaia Americana Cuia', 45.00, 100, 'Américas', false, true, true, false, 'Img/Samambaia-Americana-C21.png');

INSERT INTO cupons (codigo, tipo, valor, valor_minimo_pedido, limite_usos, usos_atuais, data_inicio, data_fim, ativo) VALUES
('BOASVINDAS5', 'porcentagem', 5.00, 0.00, 500, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', true),
('PALMOOD10', 'porcentagem', 10.00, 100.00, 200, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', true),
('SUPERPLANTAS50', 'porcentagem', 50.00, 300.00, 50, 0, '2026-01-01 00:00:00', '2027-06-30 23:59:59', true);

INSERT INTO usuarios(nome, email, senha) VALUES
('eric', 'eric@email.com', '$2y$10$yyV16e.zjljype/ZJr8VMu/HBPLNN/TgEuSfxJ6Xf1qh3REY6gD5S')