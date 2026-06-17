-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 17-Jun-2026 às 20:05
-- Versão do servidor: 5.7.24
-- versão do PHP: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `clube_tenis_padel`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `atleta`
--

CREATE TABLE `atleta` (
  `id` int(11) NOT NULL,
  `jogador` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_tipo` enum('Cartão de Cidadão','Passaporte','Outro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento_numero` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nif` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_atleta` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `atleta`
--

INSERT INTO `atleta` (`id`, `jogador`, `nome`, `documento_tipo`, `documento_numero`, `nif`, `estado_atleta`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'João Silva', 'João Silva', 'Cartão de Cidadão', '12345678', NULL, 'ativo', 'joao@teste.pt', '$2y$10$R8hWxmw9jf8imJG23UmiFumCqd4MmScYYPughdrV0IO035sLmQuAq', '2026-06-16 23:08:26', '2026-06-16 23:08:26'),
(2, 'Maria Santos', 'Maria Santos', 'Passaporte', '123456789', NULL, 'ativo', 'maria@teste.pt', '$2y$10$UndGfi929krmY8oMKvq9W.Y4zIRQokO5sqPRZld.BH/i2pMhJiRZi', '2026-06-17 19:30:47', '2026-06-17 19:30:47'),
(3, 'Pedro Costa', 'Pedro Costa', 'Outro', '88779977', NULL, 'ativo', 'pedro@teste.pt', '$2y$10$rfq9JIGNjddc0Bdxn7gw8Oi6jjY20OxWL7GJf1Obobrp/3zVtPij6', '2026-06-17 19:31:57', '2026-06-17 19:31:57');

-- --------------------------------------------------------

--
-- Estrutura da tabela `campo`
--

CREATE TABLE `campo` (
  `id` int(11) NOT NULL,
  `numero_identificador` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_campo` enum('Pádel Coberto','Pádel Descoberto','Ténis Terra Batida','Ténis Rápido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_campo` enum('disponivel','manutencao') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponivel',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `valor` decimal(8,2) NOT NULL,
  `iluminacao` decimal(8,2) NOT NULL DEFAULT '0.00',
  `horario` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponibilidade` tinyint(1) NOT NULL DEFAULT '1',
  `aluguer_material` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `campo`
--

INSERT INTO `campo` (`id`, `numero_identificador`, `tipo_campo`, `estado_campo`, `descricao`, `valor`, `iluminacao`, `horario`, `disponibilidade`, `aluguer_material`, `created_at`) VALUES
(1, 'P1', 'Pádel Coberto', 'disponivel', 'Campo pádel coberto, piso sintético', '15.00', '3.00', '08:00-23:00', 1, '5.00', '2026-06-16 00:20:04'),
(2, 'P2', 'Pádel Coberto', 'disponivel', 'Campo pádel coberto, piso sintético', '15.00', '3.00', '08:00-23:00', 1, '5.00', '2026-06-16 00:20:04'),
(3, 'P3', 'Pádel Descoberto', 'disponivel', 'Campo pádel ao ar livre', '10.00', '4.00', '08:00-21:00', 1, '5.00', '2026-06-16 00:20:04'),
(4, 'T1', 'Ténis Terra Batida', 'disponivel', 'Campo ténis terra batida', '12.00', '5.00', '08:00-21:00', 1, '8.00', '2026-06-16 00:20:04'),
(5, 'T2', 'Ténis Terra Batida', 'disponivel', 'Campo ténis terra batida', '12.00', '5.00', '08:00-21:00', 1, '8.00', '2026-06-16 00:20:04'),
(6, 'T3', 'Ténis Rápido', 'disponivel', 'Campo ténis piso rápido coberto', '18.00', '0.00', '08:00-23:00', 1, '8.00', '2026-06-16 00:20:04'),
(7, '1', 'Pádel Coberto', 'manutencao', 'Campo molhado', '15.00', '3.00', '08:00-17', 1, '2.50', '2026-06-17 17:36:56');

-- --------------------------------------------------------

--
-- Estrutura da tabela `operador`
--

CREATE TABLE `operador` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('gestor','rececionista') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `operador`
--

INSERT INTO `operador` (`id`, `nome`, `email`, `password`, `tipo`, `estado`, `created_at`) VALUES
(1, 'Gestor Principal', 'gestor@clube.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor', 'ativo', '2026-06-16 00:20:04'),
(2, 'Rececionista Ana', 'rececionista@clube.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rececionista', 'ativo', '2026-06-16 00:20:04'),
(3, 'João Silva', 'joao@teste.pt', '$2y$10$qQXCxae4ZMBWpZC0YnGJFO05BfCHwOE8VierXK/pPZnhUe9r/uX5K', 'rececionista', 'ativo', '2026-06-17 18:21:39');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamento`
--

CREATE TABLE `pagamento` (
  `id` int(11) NOT NULL,
  `reserva_id` int(11) NOT NULL,
  `montante` decimal(8,2) NOT NULL,
  `data_pagamento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` enum('parcial','total') COLLATE utf8mb4_unicode_ci NOT NULL,
  `operador_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pagamento`
--

INSERT INTO `pagamento` (`id`, `reserva_id`, `montante`, `data_pagamento`, `tipo`, `operador_id`) VALUES
(1, 1, '20.00', '2026-06-17 19:13:33', 'parcial', 1),
(2, 1, '88.00', '2026-06-17 19:14:02', 'parcial', 1),
(3, 2, '50.00', '2026-06-17 20:44:39', 'total', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `reserva`
--

CREATE TABLE `reserva` (
  `id` int(11) NOT NULL,
  `atleta_id` int(11) NOT NULL,
  `campo_id` int(11) NOT NULL,
  `campo_escolhido` enum('Pádel Coberto','Pádel Descoberto','Ténis Terra Batida','Ténis Rápido') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_jogo` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fim` time NOT NULL,
  `estado_reserva` enum('ativa','cancelada','concluida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativa',
  `checkin` tinyint(1) NOT NULL DEFAULT '0',
  `suplemento_iluminacao` tinyint(1) NOT NULL DEFAULT '0',
  `suplemento_aluguer_raquetes` int(11) NOT NULL DEFAULT '0',
  `suplemento_aluguer_bolas` int(11) NOT NULL DEFAULT '0',
  `valor_total` decimal(8,2) NOT NULL,
  `nif_faturacao` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `reserva`
--

INSERT INTO `reserva` (`id`, `atleta_id`, `campo_id`, `campo_escolhido`, `data_jogo`, `hora_inicio`, `hora_fim`, `estado_reserva`, `checkin`, `suplemento_iluminacao`, `suplemento_aluguer_raquetes`, `suplemento_aluguer_bolas`, `valor_total`, `nif_faturacao`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Pádel Coberto', '2026-06-18', '08:30:00', '17:00:00', 'ativa', 1, 1, 4, 10, '88.00', NULL, '2026-06-17 18:07:28', '2026-06-17 18:07:52'),
(2, 2, 3, 'Pádel Descoberto', '2026-06-20', '10:00:00', '11:00:00', 'ativa', 1, 0, 4, 4, '50.00', NULL, '2026-06-17 19:34:35', '2026-06-17 19:43:26'),
(3, 3, 4, 'Ténis Terra Batida', '2026-06-18', '14:00:00', '15:00:00', 'ativa', 1, 0, 2, 0, '28.00', NULL, '2026-06-17 19:58:09', '2026-06-17 20:00:10'),
(4, 3, 6, 'Ténis Rápido', '2026-06-20', '16:00:00', '19:00:00', 'ativa', 1, 1, 0, 0, '18.00', NULL, '2026-06-17 19:58:49', '2026-06-17 20:00:11');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `atleta`
--
ALTER TABLE `atleta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `campo`
--
ALTER TABLE `campo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_identificador` (`numero_identificador`);

--
-- Índices para tabela `operador`
--
ALTER TABLE `operador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserva_id` (`reserva_id`),
  ADD KEY `operador_id` (`operador_id`);

--
-- Índices para tabela `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atleta_id` (`atleta_id`),
  ADD KEY `campo_id` (`campo_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atleta`
--
ALTER TABLE `atleta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `campo`
--
ALTER TABLE `campo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `operador`
--
ALTER TABLE `operador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reserva` (`id`),
  ADD CONSTRAINT `pagamento_ibfk_2` FOREIGN KEY (`operador_id`) REFERENCES `operador` (`id`);

--
-- Limitadores para a tabela `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`atleta_id`) REFERENCES `atleta` (`id`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`campo_id`) REFERENCES `campo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
