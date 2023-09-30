-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Set-2023 às 03:48
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dropidigital`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `imagens`
--

CREATE TABLE `imagens` (
  `id` int(11) NOT NULL,
  `nome_imagem` varchar(255) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_address`
--

CREATE TABLE `tb_address` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_address`
--

INSERT INTO `tb_address` (`id`, `shop_id`, `cep`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`) VALUES
(1, 1, '11111-222', 'Endereço', 999, 'L. Ipsum', 'Bairro', 'Cidade', 'ED'),
(2, 0, '11111-222', 'Endereço', 999, '', 'Bairro', 'Cidade', 'ED');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_banner_img`
--

CREATE TABLE `tb_banner_img` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `banner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_banner_info`
--

CREATE TABLE `tb_banner_info` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `category` int(11) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_categories`
--

CREATE TABLE `tb_categories` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `parent_category` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `emphasis` tinyint(1) NOT NULL DEFAULT 0,
  `seo_name` varchar(70) NOT NULL,
  `seo_link` varchar(255) NOT NULL,
  `seo_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_categories`
--

INSERT INTO `tb_categories` (`id`, `shop_id`, `name`, `description`, `parent_category`, `status`, `emphasis`, `seo_name`, `seo_link`, `seo_description`) VALUES
(1, NULL, 'Raiz', 'Essa é a categoria raiz de todos os sites', NULL, 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_newsletter`
--

CREATE TABLE `tb_newsletter` (
  `shop_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_pages`
--

CREATE TABLE `tb_pages` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `seo_name` varchar(70) NOT NULL,
  `seo_link` varchar(255) NOT NULL,
  `seo_description` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_pages`
--

INSERT INTO `tb_pages` (`id`, `shop_id`, `status`, `name`, `link`, `content`, `seo_name`, `seo_link`, `seo_description`) VALUES
(1, 1, 1, 'Política de Troca e Devolução', '', '<h4 style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; color: rgba(0, 0, 0, 0.87); font-family: Roboto; font-size: 16px; background-color: #ffffff;\">POL&Iacute;TICA DE TROCA, DEVOLU&Ccedil;&Atilde;O E ARREPENDIMENTO</h4>', 'Política de Troca e Devolução', 'política-de-troca-e-devolução', 'POLÍTICA DE TROCA, DEVOLUÇÃO E ARREPENDIMENTO');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_products`
--

CREATE TABLE `tb_products` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `video` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sku` varchar(255) NOT NULL,
  `categories` text NOT NULL,
  `checkout` varchar(255) NOT NULL,
  `button` varchar(255) NOT NULL,
  `redirect_url` varchar(255) NOT NULL,
  `seo_name` varchar(70) NOT NULL,
  `seo_link` varchar(255) NOT NULL,
  `seo_description` varchar(160) NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_shop`
--

CREATE TABLE `tb_shop` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `logo_mobile` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `pinterest` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `token_instagram` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `segment` varchar(255) NOT NULL,
  `cpf_cnpj` varchar(255) NOT NULL,
  `razao_social` varchar(255) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `map` tinyint(1) NOT NULL DEFAULT 0,
  `newsletter_modal` tinyint(1) DEFAULT 0,
  `newsletter_modal_title` varchar(255) DEFAULT NULL,
  `newsletter_modal_text` varchar(255) DEFAULT NULL,
  `newsletter_modal_success_text` varchar(255) DEFAULT NULL,
  `newsletter_modal_time` tinyint(1) DEFAULT NULL,
  `newsletter_modal_time_seconds` varchar(255) DEFAULT NULL,
  `newsletter_modal_location` varchar(255) DEFAULT NULL,
  `newsletter_footer` tinyint(1) DEFAULT 1,
  `newsletter_footer_text` varchar(255) DEFAULT 'Receba Ofertas e Novidades de nossa loja',
  `top_highlight_bar` tinyint(1) DEFAULT 1,
  `top_highlight_bar_location` varchar(255) DEFAULT NULL,
  `top_highlight_bar_text` varchar(255) DEFAULT 'Toda a loja com descontos de até 50%',
  `top_highlight_bar_link` varchar(255) DEFAULT NULL,
  `center_highlight_images` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_shop`
--

INSERT INTO `tb_shop` (`id`, `user_id`, `name`, `title`, `description`, `logo`, `logo_mobile`, `favicon`, `facebook`, `x`, `pinterest`, `instagram`, `youtube`, `video`, `token_instagram`, `url`, `segment`, `cpf_cnpj`, `razao_social`, `phone`, `map`, `newsletter_modal`, `newsletter_modal_title`, `newsletter_modal_text`, `newsletter_modal_success_text`, `newsletter_modal_time`, `newsletter_modal_time_seconds`, `newsletter_modal_location`, `newsletter_footer`, `newsletter_footer_text`, `top_highlight_bar`, `top_highlight_bar_location`, `top_highlight_bar_text`, `top_highlight_bar_link`, `center_highlight_images`) VALUES
(1, 1, 'Minha Loja', 'Minha Loja', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur eu rutrum diam, ut facilisis justo. Praesent sollicitudin fringilla tellus, sed mollis ipsum consectetur eget. Nulla lobortis mauris vitae enim semper, quis convallis nulla cursus. Praesent dapibus semper lacus at bibendum.', 'logo-one.png', 'logo-mobile.png', 'logo-mobile.png', '', '', '', '', '', '', '0', 'minha-loja.dropidigital.com.br', '3', '000.111.222-33', NULL, '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, '3', 'Toda a loja com descontos de até 50%', '', '1, 5, 9'),
(2, 0, 'minha-loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '0', '', NULL, '(21) 97277-5758', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(3, 0, 'Minerva', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '3', '11122233344', '', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(4, 0, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '3', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(5, 0, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '0', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(6, 4, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja', '4', '111.222.333-44', '', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(7, 4, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'harry-potter', '3', '11122233344', '', '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(8, 8, 'ddaw', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'ddaw', '1', '', '', '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(9, 8, 'dawd', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'dawd', '1', '', '', '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(10, 8, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-2', '1', '000.111.222-33', '', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL),
(11, 8, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-3', '0', '111.222.333-44', '', '(11) 98765-4321', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `permissions` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `recup_password` varchar(255) DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_users`
--

INSERT INTO `tb_users` (`id`, `permissions`, `name`, `email`, `password`, `recup_password`, `date_create`, `last_login`) VALUES
(1, 755, 'Admin', 'admin@admin.com', '8923r45j89uj3we2nrftufnrieuw3nrfw-rj3jrijo3rmi9mkofsefesf', NULL, '2023-07-25 22:21:42', '2023-07-25 22:21:42'),
(2, 0, 'Cauã', 'cauaserpa007@gmail.com', '$2y$10$lTWruPe3OSS3BGhiey8FjuQDBj2IfkYmS50pALl07z5XONAf83dcu', NULL, '2023-08-01 15:43:57', '2023-08-01 15:43:57'),
(3, 0, 'Admin', 'adminA1@gmail.com', '$2y$10$j2n74pJXaKubs87NVzTeo.Zd1DcX0/xrniZ9DyYCK4eFs1YBQ.QIi', NULL, '2023-08-05 02:58:22', '2023-08-05 02:58:22'),
(4, 0, 'Admin', 'adminA2@gmail.com', '$2y$10$fJDtLRNGchUUQIThcJttYugwysI6jEwOv./PlFRFepd/nPPGBAbf2', NULL, '2023-08-05 03:01:34', '2023-08-05 03:01:34'),
(5, 0, 'awdawd', 'awdawd', '$2y$10$OcFTYw9ah1VBnUqckA7O3evJAx8fwfHi9Gwr3lN1b4pB5r.Y9V20C', NULL, '2023-08-13 03:50:29', '2023-08-13 03:50:29'),
(6, 0, 'awdawd', 'awdawdawd', '$2y$10$AW3lXQH4MSK5DjH.kplvBOkEjdHTKossnODbMlS/xCC9puCqxdYAe', NULL, '2023-08-13 03:50:54', '2023-08-13 03:50:54'),
(7, 0, 'Cauã', 'admin@gmail.com', '$2y$10$mBuhBMZ1a/thzBCmv2MOA.08WcezEUwtIvov14XwcU8O24Ox0lu9y', NULL, '2023-08-13 04:17:06', '2023-08-13 04:17:06'),
(8, 0, 'Cauã', 'adminB1@gmail.com', '$2y$10$KwAE77y/fnNo5tvLpBmPtOmHTG9QxF2KE5pU7XdM0A4WCeMBdB72i', NULL, '2023-08-15 05:56:01', '2023-08-15 05:56:01'),
(9, 0, 'Cauã', 'adminC1@gmail.com', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(10, 0, 'Cauã', 'adminC1@gmail.com', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(11, 0, 'Cauã', 'adminC2@gmail.com', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(12, 0, 'Cauã', 'adminC2@gmail.com', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(13, 0, 'Cauã', 'adminC3@gmail.com', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(14, 0, 'Cauã', 'adminC3@gmail.com', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(15, 0, 'Cauã', 'adminC4@gmail.com', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(16, 0, 'Cauã', 'adminC4@gmail.com', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(17, 0, 'Cauã', 'adminC5@gmail.com', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(18, 0, 'Cauã', 'adminC5@gmail.com', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(19, 0, 'Lucas Nascimento', 'adminC6@gmail.com', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(20, 0, 'Lucas Nascimento', 'adminC6@gmail.com', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(21, 0, 'Artur Matos', 'adminC7@gmail.com', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(22, 0, 'Artur Matos', 'adminC7@gmail.com', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(23, 0, 'Cauã', 'cauaserpa091@gmail.com', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(24, 0, 'Cauã', 'cauaserpa091@gmail.com', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(25, 0, 'Cauã', 'adminD1@gmail.com', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56'),
(26, 0, 'Cauã', 'adminD1@gmail.com', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `imagens`
--
ALTER TABLE `imagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_address`
--
ALTER TABLE `tb_address`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `imagens`
--
ALTER TABLE `imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `tb_address`
--
ALTER TABLE `tb_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
