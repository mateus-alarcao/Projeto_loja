CREATE DATABASE `loja_musica`;
USE `loja_musica`;

CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  `email` varchar(200) UNIQUE NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` varchar(30) DEFAULT 'vendedor',
  PRIMARY KEY (`id`)
);

CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(80) UNIQUE NOT NULL,
  `descricao` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `descricao` text,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int DEFAULT 0,
  `categoria_id` int,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`)
);

CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(200) UNIQUE,
  `telefone` varchar(20),
  `cpf` char(11) UNIQUE,
  PRIMARY KEY (`id`)
);

CREATE TABLE `vendas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int,
  `usuario_id` int,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(30) DEFAULT 'pendente',
  `forma_pagamento` varchar(50) NOT NULL,
  `criado_em` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
);

CREATE TABLE `itens_venda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venda_id` int,
  `produto_id` int,
  `quantidade` int NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`venda_id`) REFERENCES `vendas`(`id`),
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`)
);

INSERT INTO `usuarios` (nome, email, senha, perfil) VALUES
('Administrador', 'admin@loja.com', md5('123456'), 'admin'),
('Vendedor', 'vendedor@loja.com', md5('123456'), 'vendedor');

INSERT INTO `categorias` (nome, descricao) VALUES
('Guitarras', 'Guitarras elétricas e acústicas'),
('Teclados', 'Teclados e pianos digitais'),
('Baterias', 'Baterias acústicas e eletrônicas'),
('Acessórios', 'Cabos, palhetas, suportes e mais');

INSERT INTO `produtos` (nome, descricao, preco, estoque, categoria_id) VALUES
('Guitarra Fender Stratocaster', 'Guitarra elétrica clássica', 3500.00, 10, 1),
('Teclado Yamaha PSR', 'Teclado 61 teclas com ritmos', 1200.00, 5, 2),
('Bateria Pearl Export', 'Bateria acústica completa', 4800.00, 3, 3),
('Cabo P10 2m', 'Cabo instrumento mono 2 metros', 35.00, 50, 4);

-- Usuário com perfil cliente (senha: 123456)
INSERT INTO `usuarios` (nome, email, senha, perfil) VALUES
('Cliente Exemplo', 'cliente@loja.com', md5('123456'), 'cliente');
