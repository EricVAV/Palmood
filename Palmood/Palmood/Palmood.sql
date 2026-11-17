create database Palmood;

use Palmood;

create table usuarios (
    id int primary key auto_increment,
    nome varchar(150) not null,
    cpf varchar(14) unique not null,
    data_nascimento date not null,
    email varchar(100) unique not null,
    senha varchar(255) not null,
    criado_em timestamp default current_timestamp
);

create table enderecos (
    id int primary key auto_increment,
    usuario_id int not null,
    tipo varchar(50) default 'casa',
    logradouro varchar(255) not null,
    numero varchar(20),
    complemento varchar(100),
    cidade varchar(100) not null,
    estado char(2) not null,
    cep varchar(9) not null,
    ponto_referencia varchar(255),
    telefone varchar(20),
    foreign key (usuario_id) references usuarios(id) on delete cascade
);

create table categorias (
    id int primary key auto_increment,
    nome varchar(100) not null unique
);

create table produtos (
    id int primary key auto_increment,
    categoria_id int not null,
    nome varchar(150) not null,
    preco decimal(10,2) not null,
    altura_maxima_cm int not null,
    origem varchar(100) not null,
    frete_gratis boolean default false,
    nativa boolean default false,
    facil_cultivo boolean default false,
    exotica boolean default false,
    imagem_url varchar(255),
    foreign key (categoria_id) references categorias(id)
);

create table carrinho (
    id int primary key auto_increment,
    usuario_id int not null,
    produto_id int not null,
    quantidade int not null default 1,
    foreign key (usuario_id) references usuarios(id) on delete cascade,
    foreign key (produto_id) references produtos(id) on delete cascade,
    unique(usuario_id, produto_id)
);

create table cupons (
    id int primary key auto_increment,
    codigo varchar(50) not null unique,
    tipo varchar(20) not null,
    valor decimal(10,2) not null,
    valor_minimo_pedido decimal(10,2) default 0.00,
    limite_usos int default null,
    usos_atuais int default 0,
    data_inicio datetime not null,
    data_fim datetime not null,
    ativo boolean default true
);

create table pedidos (
    id int primary key auto_increment,
    usuario_id int not null,
    cupom_id int null,
    endereco_id int not null,
    forma_pagamento varchar(50) not null,
    subtotal decimal(10,2) not null,
    frete decimal(10,2) not null default 0.00,
    desconto decimal(10,2) not null default 0.00,
    total decimal(10,2) not null,
    status varchar(50) not null default 'pendente',
    data_entrega date,
    criado_em timestamp default current_timestamp,
    foreign key (usuario_id) references usuarios(id),
    foreign key (endereco_id) references enderecos(id),
    foreign key (cupom_id) references cupons(id) on delete set null
);

create table itens_pedido (
    id int primary key auto_increment,
    pedido_id int not null,
    produto_id int not null,
    quantidade int not null,
    preco_unitario decimal(10,2) not null,
    desconto_unitario decimal(10,2) not null default 0.00,
    avaliacao_estrelas int check (avaliacao_estrelas between 1 and 5),
    foreign key (pedido_id) references pedidos(id) on delete cascade,
    foreign key (produto_id) references produtos(id)
);

insert into categorias (id, nome) VALUES
(1, 'Palmeiras e Coqueiros'),
(2, 'Árvores Frutíferas'),
(3, 'Plantas Ornamentais'),
(4, 'Árvores de Sombra');

insert into produtos (id, categoria_id, nome, preco, altura_maxima_cm, origem, frete_gratis, nativa, facil_cultivo, exotica, imagem_url) VALUES
(1, 1, 'Muda de Phoenix Canarienses 45 cm', 249.99, 1500, 'Ilhas Canárias', false, false, true, true, 'https://palmood.com'),
(2, 1, 'Muda de Palmeira Imperial 1,5 metro', 149.99, 4000, 'Antilhas', false, false, true, true, 'https://palmood.com'),
(3, 3, 'Muda de Ficus Elástica 1 metro', 99.99, 3000, 'Ásia Tropical', true, false, true, true, 'https://palmood.com'),
(4, 1, 'Coco para plantio 1 unidade', 8.50, 3000, 'Sudeste Asiático', false, false, true, false, 'https://palmood.com'),
(5, 2, 'Muda de mangueira 1 metro', 89.99, 3000, 'Ásia (Índia)', false, false, true, false, 'https://palmood.com'),
(6, 4, 'Muda de amendoeira da praia 1 metro', 79.99, 3500, 'Malásia e Austrália', true, false, true, true, 'https://palmood.com'),
(7, 1, 'Muda de Palmeira Azul 60 cm', 189.00, 1200, 'Madagascar', false, false, false, true, 'https://palmood.com'),
(8, 1, 'Muda de Coqueiro Anão Verde', 45.00, 1200, 'Brasil', false, true, true, false, 'https://palmood.com'),
(9, 2, 'Muda de Jabuticaba Híbrida Produzindo', 120.00, 400, 'Brasil', false, true, true, false, 'https://palmood.com'),
(10, 2, 'Muda de Limão Siciliano', 55.50, 400, 'Ásia', false, false, true, false, 'https://palmood.com'),
(11, 2, 'Muda de Amora Gigante Portuguesa', 39.90, 500, 'Ásia', true, false, true, false, 'https://palmood.com'),
(12, 3, 'Muda de Costela de Adão (Monstera)', 69.90, 300, 'México', false, false, true, true, 'https://palmood.com'),
(13, 3, 'Muda de Espada de São Jorge', 25.00, 90, 'África', false, false, true, false, 'https://palmood.com'),
(14, 3, 'Muda de Jasmim Manga Adulta', 150.00, 600, 'América Central', false, false, true, true, 'https://palmood.com'),
(15, 4, 'Muda de Ipê Amarelo Cascudo', 35.00, 2000, 'Brasil', true, true, true, false, 'https://palmood.com'),
(16, 4, 'Muda de Ipê Roxo Bola', 35.00, 2500, 'Brasil', true, true, true, false, 'https://palmood.com'),
(17, 2, 'Muda de Pitanga Vermelha do Cerrado', 32.00, 600, 'Brasil', false, true, true, false, 'https://palmood.com'),
(18, 2, 'Muda de Acerola Clone BRS', 29.90, 400, 'Antilhas', false, false, true, false, 'https://palmood.com'),
(19, 1, 'Muda de Palmeira Areca Bambu', 49.90, 900, 'Madagascar', false, false, true, true, 'https://palmood.com'),
(20, 1, 'Muda de Palmeira Leque', 115.00, 400, 'Ásia', false, false, false, true, 'https://palmood.com'),
(21, 3, 'Muda de Cróton Brasileirinho', 22.00, 250, 'Ásia Oriental', false, false, true, false, 'https://palmood.com'),
(22, 2, 'Muda de Romã Gigante', 42.00, 500, 'Grécia', false, false, true, true, 'https://palmood.com'),
(23, 2, 'Muda de Videira Uva Niágara Rosada', 38.00, 600, 'América do Norte', false, false, false, true, 'https://palmood.com'),
(24, 4, 'Muda de Jacarandá Mimoso', 40.00, 1500, 'Brasil', false, true, true, false, 'https://palmood.com'),
(25, 3, 'Muda de Samambaia Americana Cuia', 45.00, 100, 'Américas', false, true, true, false, 'https://palmood.com');

insert into cupons (codigo, tipo, valor, valor_minimo_pedido, limite_usos, usos_atuais, data_inicio, data_fim, ativo) VALUES
('BOASVINDAS5', 'porcentagem', 5.00, 0.00, 500, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', true),
('PALMOOD10', 'porcentagem', 10.00, 100.00, 200, 0, '2026-01-01 00:00:00', '2027-12-31 23:59:59', true),
('SUPERPLANTAS50', 'porcentagem', 50.00, 300.00, 50, 0, '2026-01-01 00:00:00', '2027-06-30 23:59:59', true);


insert into usuarios (nome, cpf, data_nascimento, email, senha) values
('ana silva', '123.456.789-01', '1990-05-15', 'ana.silva@email.com', 'minicabrito'),
('bruno santos', '987.654.321-02', '1985-11-22', 'bruno.santos@email.com', '12345'),
('carla lari', '111.222.333-44', '1993-08-04', 'carla.ira@email.com', 'lula13'),
('Carlos Silva', '555.016.312-88', '1985-04-12', 'carlos.silva85@gmail.com', 'lula13'),
('Ana Oliveira', '249.479.655-50', '1993-11-28', 'ana.oliveira93@outlook.com', 'lula13'),
('Lucas Santosi', '361.077.076-70', '2001-07-05', 'lucas.santos01@hotmail.com', 'lula13'),
('Beatriz Lima', '919.726.169-60', '1978-02-14', 'beatriz.lima78@yahoo.com', 'lula13'),
('Rafael Costa', '676.457.465-05', '1990-09-22', 'rafael.costa90@gmail.com', 'lula13'),
('Maria Ferreira', '069.605.535-04', '1965-12-03', 'maria.ferreira65@uol.com.br', 'lula13'),
('Gabriel Almeida', '073.776.387-61', '2004-05-19', 'gabriel.almeida04@gmail.com', 'lula13'),
('Julia Ribeiro', '022.193.366-20', '1989-01-31', 'julia.ribeiro89@icloud.com', 'lula13'),
('Thiago Carvalho', '134.853.798-16', '1997-08-08', 'thiago.carvalho97@outlook.com', 'lula13'),
('Amanda Rodrigues', '776.020.877-47', '1973-06-25', 'amanda.rodrigues73@bol.com.br', 'lula13'),
('Felipe Martins', '997.229.668-71', '2000-03-17', 'felipe.martins00@gmail.com', 'lula13'),
('Camila Gomes', '785.237.733-55', '1982-10-10', 'camila.gomes82@hotmail.com', 'lula13'),
('Rodrigo Pinto', '793.481.908-06', '1995-04-02', 'rodrigo.pinto95@yahoo.com', 'lula13'),
('Larissa Mendes', '285.790.610-24', '2006-11-12', 'larissa.mendes06@gmail.com', 'lula13'),
('Antonio Nunes', '450.419.826-60', '1969-07-29', 'antonio.nunes69@uol.com.br', 'lula13'),
('Bruna Souza', '513.562.782-05', '1991-05-24', 'bruna.souza91@outlook.com', 'lula13'),
('Diego Fernandes', '544.586.421-97', '1998-02-15', 'diego.fernandes88@gmail.com', 'lula13'),
('carla lari', '137.883.748-17', '1988-12-15', 'carla.fernandes88@gmail.com', 'lula13');

insert into enderecos (usuario_id, tipo, logradouro, numero, complemento, cidade, estado, cep, telefone) values
(1, 'casa', 'rua das flores', '123', 'apto 42', 'são paulo', 'sp', '01234-567', '(11) 99999-1111'),
(2, 'trabalho', 'avenida central', '1000', 'bloco b', 'rio de janeiro', 'rj', '20000-000', '(21) 98888-2222'),
(3, 'casa', 'rua dos ipês', '45', null, 'belo horizonte', 'mg', '30000-111', '(31) 97777-3333'),
(4, 'casa', 'rua das palmeiras', '120', null, 'belo horizonte', 'mg', '30130-010', '(31) 98888-1111'),
(5, 'apartamento', 'avenida afonso pena', '1500', 'apto 302', 'belo horizonte', 'mg', '30130-003', '(31) 98888-2222'),
(6, 'casa', 'rua dos guajajaras', '455', null, 'belo horizonte', 'mg', '30140-060', '(31) 98888-3333'),
(7, 'apartamento', 'rua da bahia', '1020', 'bloco b apto 14', 'belo horizonte', 'mg', '30160-011', '(31) 98888-4444'),
(8, 'casa', 'rua dos timbiras', '85', null, 'belo horizonte', 'mg', '30140-080', '(31) 98888-5555'),
(9, 'apartamento', 'avenida do contorno', '4200', 'apto 1104', 'belo horizonte', 'mg', '30110-017', '(31) 98888-6666'),
(10, 'casa', 'rua sergipe', '310', null, 'belo horizonte', 'mg', '30130-170', '(31) 98888-7777'),
(11, 'apartamento', 'rua claudio manoel', '645', 'apto 501', 'belo horizonte', 'mg', '30140-100', '(31) 98888-8888'),
(12, 'casa', 'rua pernambuco', '780', null, 'belo horizonte', 'mg', '30130-151', '(31) 98888-9999'),
(13, 'apartamento', 'avenida brasil', '2015', 'apto 22', 'belo horizonte', 'mg', '30140-002', '(31) 97777-1111'),
(14, 'casa', 'rua alagoas', '940', null, 'belo horizonte', 'mg', '30130-160', '(31) 97777-2222'),
(15, 'apartamento', 'rua dos aimorés', '1125', 'bloco a apto 403', 'belo horizonte', 'mg', '30140-071', '(31) 97777-4444'),
(16, 'casa', 'rua paraíba', '550', null, 'belo horizonte', 'mg', '30130-140', '(31) 97777-5555'),
(17, 'apartamento', 'avenida getúlio vargas', '890', 'apto 702', 'belo horizonte', 'mg', '30112-021', '(31) 97777-6666'),
(18, 'casa', 'rua rio de janeiro', '1340', null, 'belo horizonte', 'mg', '30160-042', '(31) 97777-7777'),
(19, 'apartamento', 'rua dos goitacazes', '715', 'apto 1201', 'belo horizonte', 'mg', '30190-030', '(31) 97777-8888'),
(20, 'casa', 'rua espírito santo', '2050', null, 'belo horizonte', 'mg', '30160-032', '(31) 97777-9999');
