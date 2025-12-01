-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 28, 2025 at 03:03 PM
-- Server version: 5.7.24
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `teste2`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`id`, `nome`) VALUES
(4, 'ESTÉTICA'),
(5, 'FISIOTERAPIA');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `tipo` enum('PF','PJ') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_razao` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CPF ou CNPJ',
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade_estado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `potencial` enum('Baixo','Medio','Alto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Medio',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes`
--

CREATE TABLE `configuracoes` (
  `chave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `configuracoes`
--

INSERT INTO `configuracoes` (`chave`, `valor`) VALUES
('menu_access_roles', '{\"dashboard\":[],\"mercadorias\":[\"admin\",\"visualizador\"],\"movimentacao_estoque\":[\"admin\",\"despachante\"],\"despachos\":[\"admin\",\"despachante\",\"visualizador\"],\"transportadoras\":[\"admin\"],\"categorias\":[\"admin\"],\"clientes\":[\"admin\"],\"oportunidades\":[\"admin\"],\"pedidos_venda\":[\"admin\"],\"plano_contas\":[\"admin\"],\"contas_a_pagar\":[\"admin\"],\"contas_a_receber\":[\"admin\"],\"usuarios\":[\"admin\"],\"configuracoes\":[\"admin\"],\"perfil\":[]}');

-- --------------------------------------------------------

--
-- Table structure for table `contas_a_pagar`
--

CREATE TABLE `contas_a_pagar` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `status` enum('Aberto','Pago','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aberto',
  `fornecedor_nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plano_conta_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contas_a_receber`
--

CREATE TABLE `contas_a_receber` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_recebimento` date DEFAULT NULL,
  `status` enum('Aberto','Recebido','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aberto',
  `cliente_id` int(11) DEFAULT NULL,
  `plano_conta_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `despachos`
--

CREATE TABLE `despachos` (
  `id` int(11) NOT NULL,
  `codigo_rastreio` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nº Sedex / Rastreio',
  `data_envio` date NOT NULL,
  `data_prevista_entrega` date DEFAULT NULL,
  `status` enum('Em Processamento','Em Trânsito','Aguardando Retirada','Entregue','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Em Processamento',
  `origem_nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem_cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origem_endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino_cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destino_telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_nota` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anotacao1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anotacao2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportadora_id` int(11) DEFAULT NULL,
  `mercadoria_principal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estoque_movimentacao`
--

CREATE TABLE `estoque_movimentacao` (
  `id` int(11) NOT NULL,
  `mercadoria_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` int(11) NOT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mercadorias`
--

CREATE TABLE `mercadorias` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_unitario` decimal(10,2) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `quantidade_estoque` int(11) NOT NULL DEFAULT '0',
  `localizacao_estoque` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estoque_minimo` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oportunidades`
--

CREATE TABLE `oportunidades` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_estimado` decimal(10,2) DEFAULT NULL,
  `status` enum('Nova','Qualificacao','Proposta','Em Negociação','Fechada Ganha','Fechada Perdida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nova',
  `data_fechamento_prevista` date DEFAULT NULL,
  `responsavel_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pedidos_venda`
--

CREATE TABLE `pedidos_venda` (
  `id` int(11) NOT NULL,
  `numero_pedido` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `data_pedido` date NOT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('Pendente','Confirmado','Em Despacho','Faturado','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendente',
  `vendedor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plano_contas`
--

CREATE TABLE `plano_contas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `tipo` enum('RECEITA','DESPESA') NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `plano_de_contas`
--

CREATE TABLE `plano_de_contas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('Receita','Despesa') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rastreio_historico`
--

CREATE TABLE `rastreio_historico` (
  `id` int(11) NOT NULL,
  `despacho_id` int(11) NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localizacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transportadoras`
--

CREATE TABLE `transportadoras` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','despachante','visualizador','vendedor','estoquista','logistica','financeiro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha_hash`, `role`, `foto_perfil`) VALUES
(1, 'Administrador', 'info@kld.com.br', '$2y$10$G4bjaqpChBa0U9vH1NXIfeD3vTUf.cbWlxEvTe8WeLdpR45uZg.ti', 'admin', 'uploads/profile_1_1764242165.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`);

--
-- Indexes for table `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`chave`);

--
-- Indexes for table `contas_a_pagar`
--
ALTER TABLE `contas_a_pagar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plano_conta_id` (`plano_conta_id`);

--
-- Indexes for table `contas_a_receber`
--
ALTER TABLE `contas_a_receber`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `plano_conta_id` (`plano_conta_id`);

--
-- Indexes for table `despachos`
--
ALTER TABLE `despachos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_rastreio` (`codigo_rastreio`),
  ADD KEY `transportadora_id` (`transportadora_id`),
  ADD KEY `mercadoria_principal_id` (`mercadoria_principal_id`);

--
-- Indexes for table `estoque_movimentacao`
--
ALTER TABLE `estoque_movimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mercadoria_id` (`mercadoria_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mercadorias`
--
ALTER TABLE `mercadorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indexes for table `oportunidades`
--
ALTER TABLE `oportunidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Indexes for table `pedidos_venda`
--
ALTER TABLE `pedidos_venda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_pedido` (`numero_pedido`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Indexes for table `plano_contas`
--
ALTER TABLE `plano_contas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indexes for table `plano_de_contas`
--
ALTER TABLE `plano_de_contas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indexes for table `rastreio_historico`
--
ALTER TABLE `rastreio_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `despacho_id` (`despacho_id`);

--
-- Indexes for table `transportadoras`
--
ALTER TABLE `transportadoras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contas_a_pagar`
--
ALTER TABLE `contas_a_pagar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contas_a_receber`
--
ALTER TABLE `contas_a_receber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `despachos`
--
ALTER TABLE `despachos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `estoque_movimentacao`
--
ALTER TABLE `estoque_movimentacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mercadorias`
--
ALTER TABLE `mercadorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `oportunidades`
--
ALTER TABLE `oportunidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pedidos_venda`
--
ALTER TABLE `pedidos_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plano_contas`
--
ALTER TABLE `plano_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plano_de_contas`
--
ALTER TABLE `plano_de_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rastreio_historico`
--
ALTER TABLE `rastreio_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transportadoras`
--
ALTER TABLE `transportadoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contas_a_pagar`
--
ALTER TABLE `contas_a_pagar`
  ADD CONSTRAINT `contas_a_pagar_ibfk_1` FOREIGN KEY (`plano_conta_id`) REFERENCES `plano_de_contas` (`id`);

--
-- Constraints for table `contas_a_receber`
--
ALTER TABLE `contas_a_receber`
  ADD CONSTRAINT `contas_a_receber_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `contas_a_receber_ibfk_2` FOREIGN KEY (`plano_conta_id`) REFERENCES `plano_de_contas` (`id`);

--
-- Constraints for table `despachos`
--
ALTER TABLE `despachos`
  ADD CONSTRAINT `despachos_ibfk_1` FOREIGN KEY (`transportadora_id`) REFERENCES `transportadoras` (`id`),
  ADD CONSTRAINT `despachos_ibfk_2` FOREIGN KEY (`mercadoria_principal_id`) REFERENCES `mercadorias` (`id`);

--
-- Constraints for table `estoque_movimentacao`
--
ALTER TABLE `estoque_movimentacao`
  ADD CONSTRAINT `estoque_movimentacao_ibfk_1` FOREIGN KEY (`mercadoria_id`) REFERENCES `mercadorias` (`id`),
  ADD CONSTRAINT `estoque_movimentacao_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `mercadorias`
--
ALTER TABLE `mercadorias`
  ADD CONSTRAINT `mercadorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Constraints for table `oportunidades`
--
ALTER TABLE `oportunidades`
  ADD CONSTRAINT `oportunidades_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `oportunidades_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `pedidos_venda`
--
ALTER TABLE `pedidos_venda`
  ADD CONSTRAINT `pedidos_venda_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `pedidos_venda_ibfk_2` FOREIGN KEY (`vendedor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rastreio_historico`
--
ALTER TABLE `rastreio_historico`
  ADD CONSTRAINT `rastreio_historico_ibfk_1` FOREIGN KEY (`despacho_id`) REFERENCES `despachos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
