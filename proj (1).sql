-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/09/2026 às 04:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `proj`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `BuscarProdutos` ()   SELECT
    id_produto,
    nome_produto,
    preco,
    estoque,
    imagem,
    id_categoria,
    nome_categoria
FROM vw_produtos
ORDER BY nome_produto$$

--
-- Funções
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_valor_estoque` (`p_preco` DECIMAL(10,2), `p_estoque` INT) RETURNS DECIMAL(12,2) DETERMINISTIC RETURN p_preco * p_estoque$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`) VALUES
(1, 'Whisk'),
(2, 'Cerveja'),
(3, 'Vinho'),
(4, 'Gin'),
(5, 'Vodka');

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id_evento` int(11) NOT NULL,
  `data_evento` date NOT NULL,
  `descricao` text NOT NULL,
  `hora_evento` time NOT NULL,
  `imagem` varchar(100) NOT NULL,
  `nome_evento` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id_evento`, `data_evento`, `descricao`, `hora_evento`, `imagem`, `nome_evento`) VALUES
(1, '2026-09-20', 'Domingueira', '19:30:00', '', 'DOMINGADAS'),
(2, '2026-09-22', 'Terçanejo', '19:30:00', '', 'TERÇOU'),
(3, '2026-09-23', 'QUARTANDO E CANTANDO', '19:30:00', '', 'QUARTOU'),
(4, '2026-09-24', 'DJ AO VIVO', '19:30:00', '', 'QUINTOU'),
(5, '2026-09-25', 'SEXTOU NO BRASA', '19:30:00', '', 'SEXTOU'),
(6, '2026-09-26', 'PAGODIN', '19:30:00', '', 'SABADEIRA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `evento_produto`
--

CREATE TABLE `evento_produto` (
  `id_evento` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `estoque` int(11) NOT NULL,
  `imagem` varchar(100) NOT NULL,
  `nome_produto` varchar(100) NOT NULL,
  `preco` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `id_categoria`, `estoque`, `imagem`, `nome_produto`, `preco`) VALUES
(1, 1, 20, 'jack-daniel.png', 'jack daniels', 149),
(2, 1, 30, 'jack-apple.png', 'Jack Daniels Apple', 149),
(3, 2, 50, 'itaipava.png', 'itaipava', 12),
(4, 2, 50, 'antartica.png', 'antartica', 12),
(5, 5, 20, 'skyy.png', 'Skyy', 159.01),
(6, 5, 20, 'intencion-vodka.png', 'intencion', 169),
(7, 3, 30, 'perola.png', 'Perola', 129),
(8, 3, 20, 'reservado.png', 'Reservado', 129),
(9, 4, 40, 'rocks.png', 'Rocks', 139),
(10, 4, 30, 'intencion-blue.png', 'Intencion Blue', 169);

--
-- Acionadores `produtos`
--
DELIMITER $$
CREATE TRIGGER `trg_estoque_positivo` BEFORE UPDATE ON `produtos` FOR EACH ROW SET NEW.estoque = GREATEST(NEW.estoque, 0)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_dashboard`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_dashboard` (
`total_produtos` bigint(21)
,`total_estoque` decimal(32,0)
,`preco_medio` double
,`maior_preco` double
,`menor_preco` double
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_produtos`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_produtos` (
`id_produto` int(11)
,`nome_produto` varchar(100)
,`preco` double
,`estoque` int(11)
,`imagem` varchar(100)
,`id_categoria` int(11)
,`nome_categoria` varchar(100)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_dashboard`
--
DROP TABLE IF EXISTS `vw_dashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_dashboard`  AS SELECT count(0) AS `total_produtos`, coalesce(sum(`produtos`.`estoque`),0) AS `total_estoque`, coalesce(avg(`produtos`.`preco`),0) AS `preco_medio`, coalesce(max(`produtos`.`preco`),0) AS `maior_preco`, coalesce(min(`produtos`.`preco`),0) AS `menor_preco` FROM `produtos` ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_produtos`
--
DROP TABLE IF EXISTS `vw_produtos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_produtos`  AS SELECT `p`.`id_produto` AS `id_produto`, `p`.`nome_produto` AS `nome_produto`, `p`.`preco` AS `preco`, `p`.`estoque` AS `estoque`, `p`.`imagem` AS `imagem`, `p`.`id_categoria` AS `id_categoria`, `c`.`nome_categoria` AS `nome_categoria` FROM (`produtos` `p` join `categorias` `c` on(`p`.`id_categoria` = `c`.`id_categoria`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id_evento`);

--
-- Índices de tabela `evento_produto`
--
ALTER TABLE `evento_produto`
  ADD PRIMARY KEY (`id_evento`,`id_produto`),
  ADD KEY `id_produto` (`id_produto`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `evento_produto`
--
ALTER TABLE `evento_produto`
  ADD CONSTRAINT `evento_produto_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`),
  ADD CONSTRAINT `evento_produto_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`);

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
