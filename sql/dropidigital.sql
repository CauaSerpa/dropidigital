-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3312
-- Tempo de geração: 09-Jan-2024 às 00:06
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

--
-- Extraindo dados da tabela `imagens`
--

INSERT INTO `imagens` (`id`, `nome_imagem`, `usuario_id`) VALUES
(28, 'foto_teste.png', 23),
(79, 'foto_teste.png', 33),
(80, 'logo.png', 33),
(81, 'pngwing.com.png', 33),
(82, 'produto-teste.jpg', 33),
(83, 'shipment-delivery-by-truck-bell-notification-delivery-transportation-concept-3d-rendering.jpg', 33);

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
(1, 1, '24736-295', 'Rua Cardeal Sebastião Leme', 6, '', 'Lagoinha', 'São Gonçalo', 'Lagoinha'),
(3, 1, '24736-295', 'Rua Cardeal Sebastião Leme', 6, '', 'Lagoinha', 'São Gonçalo', 'Lagoinha'),
(5, 15, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(6, 16, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(7, 17, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(9, 19, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(10, 20, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(11, 21, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(12, 22, '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_articles`
--

CREATE TABLE `tb_articles` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `emphasis` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `seo_name` varchar(255) NOT NULL,
  `seo_link` varchar(255) NOT NULL,
  `seo_description` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_articles`
--

INSERT INTO `tb_articles` (`id`, `shop_id`, `status`, `emphasis`, `name`, `link`, `image`, `content`, `seo_name`, `seo_link`, `seo_description`, `date_create`) VALUES
(13, 1, 1, 1, 'Moda Verão 2024', 'moda-verão-2024', 'Banner Liquidação de Verão.jpg', '<h3>Esse &eacute; o conte&uacute;do do meu artigo teste</h3>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum at leo vitae massa rhoncus pulvinar ac eu dui. Praesent venenatis placerat est, ac pulvinar ligula elementum at. Aliquam quis ipsum metus. Mauris egestas mauris non elit laoreet, quis tempor neque semper. Sed tristique suscipit risus sit amet auctor. Proin faucibus nulla vel feugiat ullamcorper. Donec eu nisi vehicula, sodales nisl at, maximus est.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">In tincidunt elementum tellus, ut varius est cursus non. Mauris rutrum dignissim sagittis. Ut vitae sapien in tortor volutpat sagittis ut varius nisl. Aliquam erat volutpat. Phasellus gravida venenatis arcu, ac commodo felis sagittis venenatis. Fusce suscipit nibh risus, quis luctus lectus fermentum vel.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Quer ver mais? <a href=\"../50-off\" target=\"_blank\" rel=\"noopener\">Clique aqui!</a></p>', 'Moda verão 2024', 'moda-verão-2024', 'Mauris egestas mauris non elit laoreet, quis tempor neque semper. Sed tristique suscipit risus sit amet auctor.', '2023-11-01 22:41:14'),
(14, 1, 1, 1, 'Como fazer tal coisa', 'como-fazer-tal-coisa', 'shipping.jpg', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Aliquam et lacinia dolor, at venenatis ipsum. Phasellus sollicitudin porta tortor.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Interdum et malesuada fames ac ante ipsum primis in faucibus. Nulla maximus metus massa, in porta nibh fringilla a. Aliquam vel fringilla lectus. Maecenas fringilla augue vitae leo mattis, nec bibendum purus eleifend. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed eget lacus lacus. Morbi mattis vestibulum metus, vehicula efficitur mi dignissim vitae. In ex felis, aliquam tristique nulla at, tristique egestas ex. Duis ornare nunc ut neque egestas gravida. Proin vel sagittis mauris. Integer congue elit id nisl lacinia, id faucibus dui finibus. Aenean commodo risus nec varius tincidunt. Cras rutrum malesuada dui id iaculis.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Proin pretium sodales ipsum non vestibulum. Morbi quis urna quam. Ut cursus lacus non tincidunt consequat. Morbi eget quam a purus condimentum commodo vel in neque. Proin at nulla in risus dapibus aliquet. Sed blandit lacus eget justo dictum viverra. Sed et ante vestibulum, placerat mi congue, interdum elit.</p>', 'Como fazer tal coisa', 'como-fazer-tal-coisa', 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed eget lacus lacus. Morbi mattis vestibulum metus, vehicula effi', '2023-11-01 23:54:22'),
(15, 1, 1, 0, 'Artigo para o blog', 'artigo-para-o-blog', 'shipment-delivery-by-truck-bell-notification-delivery-transportation-concept-3d-rendering.jpg', '<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ut convallis enim. Donec hendrerit porttitor ante et convallis. Donec faucibus eget lorem id imperdiet. Aliquam augue orci, semper a pellentesque non, efficitur tincidunt odio. Proin vitae libero nisi. Aliquam ultricies convallis purus eget pellentesque. Proin ut lacus vestibulum, convallis leo at, feugiat tortor.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Ut quis ullamcorper felis. Nunc fringilla fermentum lacus id imperdiet. Phasellus mattis gravida libero, ac pharetra massa ultrices commodo. Ut egestas, elit eget rutrum ultrices, sem dui iaculis augue, commodo consectetur lorem libero sit amet neque. Maecenas a enim tempor, porta enim suscipit, ornare nisl. Nam et arcu justo. Fusce nec felis et libero efficitur malesuada in at neque.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Donec vel magna felis. Morbi sit amet condimentum nulla, non ullamcorper purus. Donec ullamcorper scelerisque nisi sed cursus. Morbi imperdiet dignissim nulla vitae cursus. Integer turpis dolor, imperdiet et tristique ut, semper nec urna. In sem diam, viverra nec est a, vehicula tempus elit. Nunc posuere elementum massa eu accumsan. Fusce in varius turpis. Morbi non euismod lorem, sit amet elementum ex. Integer nibh velit, fermentum non elementum non, porta vel elit. Aenean at vestibulum tellus. Vestibulum ullamcorper tempus lacus, molestie semper nisi tincidunt id. Fusce sit amet risus sodales, elementum nisi id, finibus ex.</p>\r\n<p style=\"margin: 0px 0px 15px; padding: 0px; text-align: justify; font-family: \'Open Sans\', Arial, sans-serif; font-size: 14px; background-color: #ffffff;\">Phasellus non dictum nisi, non fringilla lectus. Proin ac volutpat ipsum, vel bibendum lorem. In varius felis non lobortis gravida. Donec mauris nulla, varius non placerat at, aliquet at quam. Ut congue luctus turpis vitae molestie. Vivamus elit ante, ultrices vel rhoncus in, ullamcorper id nisl. Phasellus eu malesuada orci. Etiam auctor nulla eu risus luctus porttitor. Morbi at elit at arcu posuere finibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse sed turpis interdum, commodo mauris sed, blandit dolor. Aliquam sed augue vitae metus ultricies gravida a quis orci. Pellentesque semper hendrerit arcu, id dapibus sapien imperdiet in.</p>', 'Artigo para o blog', 'artigo-para-o-blog', 'Donec ullamcorper scelerisque nisi sed cursus. Morbi imperdiet dignissim nulla vitae cursus. Integer turpis dolor, imperdiet et tristique ut, semper nec urna.', '2023-11-05 03:32:36');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_banner_img`
--

CREATE TABLE `tb_banner_img` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `banner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_banner_img`
--

INSERT INTO `tb_banner_img` (`id`, `image_name`, `banner_id`) VALUES
(9, 'Banner Black Fridey.jpg', 11),
(10, 'Banner Liquidação de Verão.jpg', 12);

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

--
-- Extraindo dados da tabela `tb_banner_info`
--

INSERT INTO `tb_banner_info` (`id`, `shop_id`, `name`, `location`, `category`, `link`, `target`, `title`, `status`, `date_create`) VALUES
(11, 1, '50% OFF', 'full-banner', 1, 'http://minha-loja.localhost/dropidigital/app/loja/50-off', '_blank', '50% OFF', 1, '2023-09-30 02:57:45'),
(12, 1, 'Verão', 'full-banner', 1, 'http://minha-loja.localhost/dropidigital/app/loja/verao', '_blank', 'Descontos de verão', 1, '2023-09-30 03:09:41');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_categories`
--

CREATE TABLE `tb_categories` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `link` varchar(255) NOT NULL,
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

INSERT INTO `tb_categories` (`id`, `shop_id`, `name`, `icon`, `image`, `description`, `link`, `parent_category`, `status`, `emphasis`, `seo_name`, `seo_link`, `seo_description`) VALUES
(1, NULL, 'Raiz', NULL, '', 'Essa é a categoria raiz de todos os sites', '', NULL, 0, 0, '', '', ''),
(23, 1, '50% OFF', '6524ab53ce92c.jpg', '1696901971.jpg', '50% OFF', '50-off', '1', 1, 1, 'Desconto | 50% OFF | Dropidigital', '50-off', '50% OFF'),
(24, 1, 'Subcategoria 1', NULL, NULL, 'Subcategoria 1', 'subcategoria-1', '23', 1, 0, 'Subcategoria 1', 'subcategoria-1', 'Subcategoria 1'),
(34, 1, 'Categoria de teste 2', '654715eaca1c9.jpg', '1699157482.jpg', 'Categoria de teste 2', 'categoria-de-teste-2', '1', 1, 1, 'Categoria de teste 2', 'categoria-de-teste-2', 'Categoria de teste 2'),
(35, 1, 'Categoria de teste 3', '6547160fea0b5.jpg', '1699157519.jpg', 'Categoria de teste 3', 'categoria-de-teste-3', '1', 1, 1, 'Categoria de teste 3', 'categoria-de-teste-3', 'Categoria de teste 3');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_depositions`
--

CREATE TABLE `tb_depositions` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `testimony` varchar(200) NOT NULL,
  `qualification` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_depositions`
--

INSERT INTO `tb_depositions` (`id`, `shop_id`, `img`, `name`, `testimony`, `qualification`) VALUES
(1, 1, 'imagem_2023-10-07_223055645.png', 'Gilherme', 'Boa loja', 4),
(3, 1, 'imagem_2023-10-07_235049408.png', 'Pedro', 'Produto chegou mal embalado', 2),
(4, 1, 'imagem_2023-10-07_235149007.png', 'Samuel', 'Loja muito boa. Ótimos produtos!', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_invoice_info`
--

CREATE TABLE `tb_invoice_info` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `docType` varchar(255) NOT NULL,
  `docNumber` varchar(255) NOT NULL,
  `cep` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `estado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_invoice_info`
--

INSERT INTO `tb_invoice_info` (`id`, `shop_id`, `customer_id`, `name`, `email`, `phone`, `docType`, `docNumber`, `cep`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`) VALUES
(3, 1, 'cus_000005797346', 'Cauã Serpa', 'cauaserpa092@gmail.com', '(21) 97277-5758', 'cpf', '205.532.407-14', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(4, 15, NULL, 'Pedro Construções', 'mateus@gmail.com', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(5, 16, NULL, 'Pedro Construções', 'mateus@gmail.com', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(6, 17, NULL, 'Pedro Construções', 'mateus@gmail.com', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(8, 19, NULL, 'Apple', '', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(9, 20, NULL, 'Aurora', '', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ'),
(10, 21, NULL, 'Ariel', '', '(21) 97277-5758', 'cpf', '111.222.333-44', '24736-295', 'Rua Cardeal Sebastião Leme', 6, 'Apto 202', 'Lagoinha', 'São Gonçalo', 'RJ');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_login`
--

CREATE TABLE `tb_login` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `first_used_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_login`
--

INSERT INTO `tb_login` (`id`, `user_id`, `ip_address`, `first_used_at`) VALUES
(1, 35, '::1', '2024-01-04 22:57:55'),
(3, 1, '::1', '2024-01-04 23:08:41'),
(4, 2, '::1', '2024-01-04 23:28:55'),
(5, 3, '::1', '2024-01-04 23:29:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_newsletter`
--

CREATE TABLE `tb_newsletter` (
  `shop_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_newsletter`
--

INSERT INTO `tb_newsletter` (`shop_id`, `email`) VALUES
(1, 'cauaserpa007@gmail.com'),
(1, 'cauaserpa092@gmail.com');

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
  `seo_description` varchar(160) NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_pages`
--

INSERT INTO `tb_pages` (`id`, `shop_id`, `status`, `name`, `link`, `content`, `seo_name`, `seo_link`, `seo_description`, `date_create`) VALUES
(1, 1, 1, 'Política de Troca e Devolução', 'política-de-troca-e-devolução', '<h4 style=\"margin: 0px 0px 1em; padding: 0px; box-sizing: border-box; color: rgba(0, 0, 0, 0.87); font-family: Roboto; font-size: 16px; background-color: #ffffff;\">POL&Iacute;TICA DE TROCA, DEVOLU&Ccedil;&Atilde;O E ARREPENDIMENTO</h4>', 'Política de Troca e Devolução', 'política-de-troca-e-devolução', 'POLÍTICA DE TROCA, DEVOLUÇÃO E ARREPENDIMENTO', '2023-10-18 23:37:28'),
(2, 1, 1, 'Segurança e Privacidade', 'seguranca-e-privacidade', '<p>Pol&iacute;tica de Troca e Devolu&ccedil;&atilde;o</p>', 'Segurança e Privacidade', 'seguranca-e-privacidade', 'Política de Troca e Devolução', '2023-10-18 23:37:28');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_plans`
--

CREATE TABLE `tb_plans` (
  `id` int(11) NOT NULL,
  `plan_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `resources` text NOT NULL,
  `link_checkout` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_plans`
--

INSERT INTO `tb_plans` (`id`, `plan_id`, `name`, `sub_name`, `description`, `resources`, `link_checkout`) VALUES
(1, '', 'Básico', 'Conhecendo', 'Descrição do plano básico', '[\"10 produtos\", \"5.000 visitas/mês\", \"Sem limite de pedidos ou orçamentos\", \"Sem comissão sobre vendas\", \"Conta protegida\", \"Botão WhatsApp\"]', 'https://link-checkout.com'),
(2, '', 'Iniciante', 'Já faço vendas', 'Descrição do plano iniciante', '[\"50 produtos\", \"25.000 visitas/mês\", \"Sem limite de pedidos\", \"Sem comissão sobre vendas\", \"Conta protegida\", \"Botão WhatsApp\", \"Suporte humanizado\"]', 'https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c9380848a039ec9018a04bd6bde010c'),
(3, '', 'Intermeriário', 'Pedidos diários', 'Descrição do plano intermeriário', '[\"250 produtos\", \"50.000 visitas/mês\", \"Sem limite de pedidos\", \"Sem comissão sobre vendas\", \"Conta protegida\", \"Botão WhatsApp\", \"Suporte humanizado\", \"Palavras chave do seu nicho\"]', 'https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c9380848a039ebe018a04c511fa0102'),
(4, '', 'Avançado', 'Muitas vendas', 'Descrição do plano avançado', '[\"750 produtos\", \"100.000 visitas/mês\", \"Sem limite de pedidos\", \"Sem comissão sobre vendas\", \"Conta protegida\", \"Botão WhatsApp\", \"Suporte humanizado\", \"Palavras chave do seu nicho\", \"Atendimento prioritário\"]', 'https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c9380848a039ec9018a04cfc5bd0120'),
(5, '', 'Expert', 'Voando alto', 'Descrição do plano expert', '[\"Produtos ilimitados\", \"300.000 visitas/mês\", \"Sem limite de pedidos\", \"Sem comissão sobre vendas\", \"Conta protegida\", \"Botão WhatsApp\", \"Suporte humanizado\", \"Palavras chave do seu nicho\", \"Atendimento prioritário\", \"Mentoria inicial do projeto\", \"Serviço de SEO incluso\"]', 'https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c9380848a039ec9018a04d7cac70128');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_plans_interval`
--

CREATE TABLE `tb_plans_interval` (
  `id` int(11) NOT NULL,
  `mpago_id` varchar(255) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `billing_interval` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_plans_interval`
--

INSERT INTO `tb_plans_interval` (`id`, `mpago_id`, `plan_id`, `billing_interval`, `price`) VALUES
(1, '', 1, 'monthly', '0'),
(2, '', 1, 'yearly', '0'),
(3, '2c9380848bebed8e018c0d812b51157c', 2, 'monthly', '47'),
(4, '2c9380848bebed8e018c0f588e4e168d', 2, 'yearly', '470'),
(5, '', 3, 'monthly', '79'),
(6, '', 3, 'yearly', '790'),
(7, '', 4, 'monthly', '127'),
(8, '', 4, 'yearly', '1270'),
(9, '', 5, 'monthly', '191'),
(10, '', 5, 'yearly', '1910');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_products`
--

CREATE TABLE `tb_products` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `emphasis` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `video` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sku` varchar(255) NOT NULL,
  `categories` int(11) NOT NULL,
  `checkout` varchar(255) NOT NULL,
  `button_type` varchar(255) NOT NULL,
  `redirect_link` varchar(255) NOT NULL,
  `seo_name` varchar(70) NOT NULL,
  `seo_link` varchar(255) NOT NULL,
  `seo_description` varchar(160) NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_products`
--

INSERT INTO `tb_products` (`id`, `shop_id`, `status`, `emphasis`, `name`, `link`, `price`, `discount`, `video`, `description`, `sku`, `categories`, `checkout`, `button_type`, `redirect_link`, `seo_name`, `seo_link`, `seo_description`, `date_create`) VALUES
(23, 1, 1, 1, 'Produto 2', 'produto-2', '249.90', '199.90', '', '<p>Descricao do produto</p>', 'SKU', 23, '1', '4', 'https://api.whatsapp.com/send?phone=(11)%2098765-4321', 'Produto 2 | Minha loja', '50-off', 'Descricao do produto', '2023-10-12 15:11:02'),
(33, 1, 1, 0, 'Teste redirect link', 'teste-redirect-link', '97.00', '0.00', '', '<h1>Teste</h1>', 'SKU', 24, '', '4', 'http://localhost/dropidigital/app/loja/#', 'Teste', 'teste', 'Teste', '2023-10-29 02:25:01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_scripts`
--

CREATE TABLE `tb_scripts` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `script` text NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_scripts`
--

INSERT INTO `tb_scripts` (`id`, `shop_id`, `status`, `name`, `script`, `date_create`) VALUES
(1, 1, 0, 'Google ADS', '<script>alert(\'Google ADS\');</script>', '2023-10-24 00:40:31'),
(3, 1, 0, 'Pixel Facebook', '<script>alert(\'Pixel Facebook\');</script>', '2023-10-24 00:55:06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_shop`
--

CREATE TABLE `tb_shop` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL DEFAULT 1,
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
  `phone` varchar(15) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
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
  `center_highlight_images` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_shop`
--

INSERT INTO `tb_shop` (`id`, `user_id`, `plan_id`, `name`, `title`, `description`, `logo`, `logo_mobile`, `favicon`, `facebook`, `x`, `pinterest`, `instagram`, `youtube`, `video`, `token_instagram`, `url`, `segment`, `cpf_cnpj`, `razao_social`, `phone`, `whatsapp`, `email`, `map`, `newsletter_modal`, `newsletter_modal_title`, `newsletter_modal_text`, `newsletter_modal_success_text`, `newsletter_modal_time`, `newsletter_modal_time_seconds`, `newsletter_modal_location`, `newsletter_footer`, `newsletter_footer_text`, `top_highlight_bar`, `top_highlight_bar_location`, `top_highlight_bar_text`, `center_highlight_images`) VALUES
(1, 1, 3, 'Minha Loja', '', '', 'logo_659392ea00a634.39095083.png', 'logo_mobile_659392ea01ad32.25855813.png', 'favicon_659392ea021447.48162307.png', '', 'https://twitter.com/seutwitter', '', 'https://facebook.com/seuinstagram', 'https://www.youtube.com/seuyoutube', NULL, '', 'minha-loja', '0', '000.111.222-33', '', '(21) 97277-5758', NULL, 'seu-email@mail.com', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '1', 'Toda a loja com descontos de até 50%', '1, 2, 3'),
(2, 0, 1, 'minha-loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '0', '', NULL, '(21) 97277-5758', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(3, 0, 1, 'Minerva', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '3', '11122233344', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(4, 0, 1, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '3', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(5, 0, 1, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '0', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(6, 4, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja', '4', '111.222.333-44', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(7, 4, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'harry-potter', '3', '11122233344', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(8, 8, 1, 'ddaw', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'ddaw', '1', '', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(9, 8, 1, 'dawd', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'dawd', '1', '', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(10, 8, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-2', '1', '000.111.222-33', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(11, 8, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-3', '0', '111.222.333-44', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(12, 13, 1, 'Minha Loja', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'minha-loja-4', '0', '111.222.333-44', '', '', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, NULL, 'Toda a loja com descontos de até 50%', NULL),
(13, 13, 1, 'Cauã', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cauã', '1', '111.222.333-44', '', '', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, NULL, 'Toda a loja com descontos de até 50%', NULL),
(17, 32, 1, 'Pedro Construções', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'harry-potter3', '2', '111.222.333-44', '', '(21) 97277-5758', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, '1', 'Toda a loja com descontos de até 50%', NULL),
(19, 34, 1, 'Apple', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'apple', '3', '111.222.333-44', 'APPLE COMPUTERS LTDA.', '(21) 97277-5758', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, NULL, 'Toda a loja com descontos de até 50%', NULL),
(20, 35, 1, 'Aurora', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'aurora', '2', '111.222.333-44', '', '(21) 97277-5758', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, NULL, 'Toda a loja com descontos de até 50%', NULL),
(21, 36, 1, 'Ariel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ariel', '2', '111.222.333-44', '', '(21) 97277-5758', NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Receba Ofertas e Novidades de nossa loja', 1, NULL, 'Toda a loja com descontos de até 50%', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_subscriptions`
--

CREATE TABLE `tb_subscriptions` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `subscription_id` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `billing_type` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `due_date` date NOT NULL,
  `cycle` varchar(255) NOT NULL,
  `pix_expirationDate` datetime DEFAULT NULL,
  `pix_encodedImage` longtext DEFAULT NULL,
  `pix_payload` longtext DEFAULT NULL,
  `credit_card_number` varchar(255) DEFAULT NULL,
  `credit_card_flag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_subscriptions`
--

INSERT INTO `tb_subscriptions` (`id`, `shop_id`, `plan_id`, `customer_id`, `subscription_id`, `value`, `billing_type`, `status`, `start_date`, `due_date`, `cycle`, `pix_expirationDate`, `pix_encodedImage`, `pix_payload`, `credit_card_number`, `credit_card_flag`) VALUES
(52, 1, 2, 'cus_000005797346', 'sub_vb1xo1wqxslymg9j', '47', 'PIX', 'INACTIVE', '0000-00-00 00:00:00', '2023-12-29', 'MONTHLY', '2023-12-06 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(53, 1, 2, 'cus_000005797346', 'sub_0lx3fr40xt3b2jqk', '47', 'PIX', 'INACTIVE', '0000-00-00 00:00:00', '2024-01-06', 'MONTHLY', '2023-12-06 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(54, 1, 3, 'cus_000005797346', 'sub_i4n04uzh5s7zrm02', '79', 'CREDIT_CARD', 'INACTIVE', '2023-12-06 00:00:00', '2024-01-06', 'MONTHLY', NULL, NULL, NULL, '4242', 'VISA'),
(55, 1, 2, 'cus_000005797346', 'sub_z570k8i4l3q49dt3', '47', 'CREDIT_CARD', 'INACTIVE', '2023-12-06 00:00:00', '2024-01-06', 'MONTHLY', NULL, NULL, NULL, '4242', 'VISA'),
(56, 1, 3, 'cus_000005797346', 'sub_4d1g0n3d831ht3rd', '79', 'PIX', 'INACTIVE', '0000-00-00 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540579.005802BR5905ASAAS6009JOINVILLE62070503***6304499C', NULL, NULL),
(57, 1, 4, 'cus_000005797346', 'sub_sglph41dxs98muml', '127', 'PIX', 'INACTIVE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca255204000053039865406127.005802BR5905ASAAS6009JOINVILLE62070503***6304C345', NULL, NULL),
(58, 1, 2, 'cus_000005797346', 'sub_xpnfz7jkbzfyqkpn', '47', 'PIX', 'INACTIVE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(59, 1, 3, 'cus_000005797346', 'sub_vuv6dj225i5jl6l1', '79', 'PIX', 'OVERDUE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540579.005802BR5905ASAAS6009JOINVILLE62070503***6304499C', NULL, NULL),
(60, 1, 2, 'cus_000005797346', 'sub_sut57h26revbmgj0', '47', 'PIX', 'OVERDUE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(61, 1, 9, 'cus_000005797346', 'sub_1pztr94tzcm3f4wz', '191', 'PIX', 'INACTIVE', '2023-12-19 00:00:00', '2024-01-09', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca255204000053039865406191.005802BR5905ASAAS6009JOINVILLE62070503***630447A6', NULL, NULL),
(62, 1, 3, 'cus_000005797346', 'sub_wwm0fndgizhr6zo9', '47', 'PIX', 'OVERDUE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL);
INSERT INTO `tb_subscriptions` (`id`, `shop_id`, `plan_id`, `customer_id`, `subscription_id`, `value`, `billing_type`, `status`, `start_date`, `due_date`, `cycle`, `pix_expirationDate`, `pix_encodedImage`, `pix_payload`, `credit_card_number`, `credit_card_flag`) VALUES
(63, 1, 3, 'cus_000005797346', 'sub_2slup598sheyj2ov', '47', 'PIX', 'RECEIVED', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(64, 1, 7, 'cus_000005797346', 'sub_fza1cuvoh21a7zxo', '127', 'PIX', 'OVERDUE', '2023-12-19 00:00:00', '2024-01-19', 'MONTHLY', '2023-12-19 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca255204000053039865406127.005802BR5905ASAAS6009JOINVILLE62070503***6304C345', NULL, NULL),
(65, 17, 1, NULL, NULL, '0', NULL, 'RECEIVED', '2023-12-20 17:35:54', '2024-01-20', 'MONTHLY', NULL, NULL, NULL, NULL, NULL),
(67, 19, 1, NULL, NULL, '0', NULL, 'RECEIVED', '2023-12-27 08:00:59', '2024-01-27', 'MONTHLY', NULL, NULL, NULL, NULL, NULL),
(68, 20, 1, NULL, NULL, '0', NULL, 'RECEIVED', '2023-12-27 08:05:27', '2024-01-27', 'MONTHLY', NULL, NULL, NULL, NULL, NULL),
(69, 21, 1, NULL, NULL, '0', NULL, 'RECEIVED', '2023-12-27 08:11:31', '2024-01-27', 'MONTHLY', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `permissions` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active_email` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `docType` varchar(255) NOT NULL,
  `docNumber` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `recup_password` varchar(255) DEFAULT NULL,
  `two_factors` tinyint(1) NOT NULL DEFAULT 0,
  `two_factors_token` varchar(255) DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_users`
--

INSERT INTO `tb_users` (`id`, `permissions`, `name`, `email`, `active_email`, `token`, `docType`, `docNumber`, `phone`, `password`, `recup_password`, `two_factors`, `two_factors_token`, `date_create`, `last_login`) VALUES
(1, 1, 'Admin', 'admin@admin.com', 1, NULL, 'cpf', '205.532.407-14', '', '$2y$10$VhpuKRv8TjJxFwbocN0u6.pa4v9vVd3oNgSllOQ/.FyC34uURZd1m', '$2y$10$52DRLL.QyiqG/jcMlhTzSeSKVvSR1ijJC/kPK0hf88UCHZDeNQBFK', 1, NULL, '2023-07-25 22:21:42', '2023-07-25 22:21:42'),
(2, 0, 'Cauã', 'cauaserpa007@gmail.com', 0, NULL, '', '', '', '$2y$10$keyURwp4bKuE50OyZ3a16uJH1VgMHg8hw2xETjX2T8RqQxXIBfW8G', NULL, 1, NULL, '2023-08-01 15:43:57', '2023-08-01 15:43:57'),
(3, 0, 'Admin', 'adminA1@gmail.com', 0, NULL, '', '', '', '$2y$10$j2n74pJXaKubs87NVzTeo.Zd1DcX0/xrniZ9DyYCK4eFs1YBQ.QIi', NULL, 0, NULL, '2023-08-05 02:58:22', '2023-08-05 02:58:22'),
(4, 0, 'Admin', 'adminA2@gmail.com', 0, NULL, '', '', '', '$2y$10$fJDtLRNGchUUQIThcJttYugwysI6jEwOv./PlFRFepd/nPPGBAbf2', NULL, 1, NULL, '2023-08-05 03:01:34', '2023-08-05 03:01:34'),
(5, 0, 'awdawd', 'awdawd', 0, NULL, '', '', '', '$2y$10$OcFTYw9ah1VBnUqckA7O3evJAx8fwfHi9Gwr3lN1b4pB5r.Y9V20C', NULL, 1, NULL, '2023-08-13 03:50:29', '2023-08-13 03:50:29'),
(6, 0, 'awdawd', 'awdawdawd', 0, NULL, '', '', '', '$2y$10$AW3lXQH4MSK5DjH.kplvBOkEjdHTKossnODbMlS/xCC9puCqxdYAe', NULL, 1, NULL, '2023-08-13 03:50:54', '2023-08-13 03:50:54'),
(7, 0, 'Cauã', 'admin@gmail.com', 0, NULL, '', '', '', '$2y$10$mBuhBMZ1a/thzBCmv2MOA.08WcezEUwtIvov14XwcU8O24Ox0lu9y', NULL, 1, NULL, '2023-08-13 04:17:06', '2023-08-13 04:17:06'),
(8, 0, 'Cauã', 'adminB1@gmail.com', 0, NULL, '', '', '', '$2y$10$KwAE77y/fnNo5tvLpBmPtOmHTG9QxF2KE5pU7XdM0A4WCeMBdB72i', NULL, 1, NULL, '2023-08-15 05:56:01', '2023-08-15 05:56:01'),
(9, 0, 'Cauã', 'adminC1@gmail.com', 0, NULL, '', '', '', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, 1, NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(10, 0, 'Cauã', 'adminC1@gmail.com', 0, NULL, '', '', '', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, 1, NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(11, 0, 'Cauã', 'adminC2@gmail.com', 0, NULL, '', '', '', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, 1, NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(12, 0, 'Cauã', 'adminC2@gmail.com', 0, NULL, '', '', '', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, 1, NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(13, 0, 'Cauã', 'adminC3@gmail.com', 0, NULL, '', '', '', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, 1, NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(14, 0, 'Cauã', 'adminC3@gmail.com', 0, NULL, '', '', '', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, 1, NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(15, 0, 'Cauã', 'adminC4@gmail.com', 0, NULL, '', '', '', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, 1, NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(16, 0, 'Cauã', 'adminC4@gmail.com', 0, NULL, '', '', '', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, 1, NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(17, 0, 'Cauã', 'adminC5@gmail.com', 0, NULL, '', '', '', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, 1, NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(18, 0, 'Cauã', 'adminC5@gmail.com', 0, NULL, '', '', '', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, 1, NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(19, 0, 'Lucas Nascimento', 'adminC6@gmail.com', 0, NULL, '', '', '', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, 1, NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(20, 0, 'Aurora', 'adminC6@gmail.com', 0, NULL, '', '', '', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, 1, NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(21, 0, 'Artur Matos', 'adminC7@gmail.com', 0, NULL, '', '', '', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, 1, NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(22, 0, 'Artur Matos', 'adminC7@gmail.com', 0, NULL, '', '', '', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, 1, NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(23, 0, 'Cauã', 'cauaserpa091@gmail.com', 0, NULL, '', '', '', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, 1, NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(24, 0, 'Cauã', 'cauaserpa091@gmail.com', 0, NULL, '', '', '', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, 1, NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(25, 0, 'Cauã', 'adminD1@gmail.com', 0, NULL, '', '', '', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, 1, NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56'),
(26, 0, 'Cauã', 'adminD1@gmail.com', 0, NULL, '', '', '', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, 1, NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56'),
(27, 0, 'Cauã Serpa da Silva', 'cauaserpa092@gmail.com', 0, NULL, '', '', '', '$2y$10$gRGC6jgguOcwj6SLIFcST.U0TM2ynjcNgTvmwwhUbxYw.Mx.G8ovq', NULL, 1, NULL, '2023-12-16 23:52:55', '2023-12-16 23:52:55'),
(28, 0, '', '', 0, NULL, '', '', '', '$2y$10$H0oE7U1gRsCQUCug4zYR1OEw81cyj62L/TFb0G3ilqG2r.zVUEW.K', NULL, 1, NULL, '2023-12-20 16:54:46', '2023-12-20 16:54:46'),
(29, 0, 'Mateus', 'mateus@gmail.com', 0, NULL, '', '', '', '$2y$10$/JtHCmLr4d7/Vcj.oDwnA.jxs37CZku6KIK8MDi7e8oWEpTKYFCDW', NULL, 1, NULL, '2023-12-20 17:05:37', '2023-12-20 17:05:37'),
(30, 0, 'Felipe', 'felipe@gmail.com', 0, NULL, '', '', '', '$2y$10$5JPYagRa6Ct7jBBPntOPcOoIm0FeuK6l4Wz1rOy9Xc6nlohIix9Di', NULL, 1, NULL, '2023-12-20 17:12:41', '2023-12-20 17:12:41'),
(31, 0, 'Gustavo', 'gustavo@gmail.com', 0, NULL, '', '', '', '$2y$10$ZGuNKHM6ecK3zVRHxSI5seH5aq2zejU0ILxqoysecl5lrrRYoD0tu', NULL, 1, NULL, '2023-12-20 17:25:42', '2023-12-20 17:25:42'),
(32, 0, 'Pedro', 'pedro@gmail.com', 0, NULL, '', '', '', '$2y$10$EaozEryfCWyc6C2rVtYNGODMBpUeOw4Y4jYmqcRC1eqGNIViqI0sa', NULL, 1, NULL, '2023-12-20 17:26:45', '2023-12-20 17:26:45'),
(34, 0, 'Steve Jobs', 'stevejobs@apple.com', 0, '099791ae1c37663c61c4ad625a48ce77', '', '', '', '$2y$10$hfIW./jmCSTlXWBTcXx/deu5UiGHhhYBpm/jT9wraM/O0n5pxcm6q', NULL, 0, NULL, '2023-12-27 07:59:25', '2023-12-27 07:59:25'),
(35, 0, 'Aurora', 'aurora@gmail.com', 1, NULL, '', '', '', '$2y$10$/2h2I15baSgLFCCQRESiH.oAMwtdP9tnMOmKkANQpfwotiTg.7Ium', '$2y$10$UzgxS3JYQiH.7XO7Tz9W2udQOpOBHHGhrC7WwbuMTG2eIqoV6mIDS', 0, NULL, '2023-12-27 08:04:59', '2023-12-27 08:04:59'),
(36, 0, 'Ariel', 'ariel@gmail.com', 1, NULL, '', '', '', '$2y$10$KEO0FwaBTdilSOGFviXZpOwTHx7sgYE5rqOa287k78M2lnmwe9m0a', NULL, 1, NULL, '2023-12-27 08:10:46', '2023-12-27 08:10:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_visits`
--

CREATE TABLE `tb_visits` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `data` date NOT NULL,
  `contagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_visits`
--

INSERT INTO `tb_visits` (`id`, `shop_id`, `page`, `data`, `contagem`) VALUES
(1, 0, '', '2023-10-09', 17),
(2, 0, '', '2023-10-08', 5),
(3, 0, '', '2023-10-07', 28),
(4, 0, '', '2023-10-06', 13),
(5, 0, '', '2023-10-05', 27),
(6, 0, '', '2023-09-11', 15),
(7, 0, '', '2023-09-15', 24),
(8, 0, '', '2023-08-24', 14),
(9, 0, '', '2023-09-11', 150),
(10, 0, '', '2023-10-04', 64),
(11, 0, '', '2023-10-11', 42),
(12, 0, '', '2023-10-12', 336),
(13, 0, '', '2023-10-17', 26),
(14, 0, '', '2023-10-18', 24),
(15, 0, '', '2023-10-19', 5),
(16, 0, '', '2023-10-24', 5),
(17, 0, '', '2023-10-25', 45),
(18, 0, '', '2023-10-26', 459),
(19, 0, '', '2023-10-27', 18),
(21, 0, '', '2023-10-28', 2),
(22, 0, '', '2023-10-29', 1296),
(23, 0, '', '2023-10-30', 18),
(24, 0, '', '2023-10-31', 1),
(25, 0, '', '2023-11-01', 290),
(26, 0, '', '2023-11-02', 16),
(27, 0, '', '2023-11-03', 1),
(28, 0, '', '2023-11-04', 1196),
(29, 1, 'shop', '2023-11-05', 418),
(30, 1, 'shop', '2023-11-06', 3),
(31, 1, 'shop', '2023-11-07', 3),
(32, 1, 'shop', '2023-11-14', 2),
(33, 1, 'shop', '2023-11-15', 1),
(34, 1, 'shop', '2023-11-27', 2),
(35, 1, 'shop', '2023-12-06', 2),
(36, 1, 'shop', '2023-12-16', 3),
(37, 1, 'shop', '2023-12-20', 1),
(41, 0, '', '2023-12-27', 44),
(42, 0, '', '2023-12-29', 3),
(43, 0, '', '2024-01-05', 2);

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
-- Índices para tabela `tb_articles`
--
ALTER TABLE `tb_articles`
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
-- Índices para tabela `tb_depositions`
--
ALTER TABLE `tb_depositions`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_invoice_info`
--
ALTER TABLE `tb_invoice_info`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_login`
--
ALTER TABLE `tb_login`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_plans`
--
ALTER TABLE `tb_plans`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_plans_interval`
--
ALTER TABLE `tb_plans_interval`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_scripts`
--
ALTER TABLE `tb_scripts`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_subscriptions`
--
ALTER TABLE `tb_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tb_visits`
--
ALTER TABLE `tb_visits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `imagens`
--
ALTER TABLE `imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de tabela `tb_address`
--
ALTER TABLE `tb_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `tb_articles`
--
ALTER TABLE `tb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `tb_depositions`
--
ALTER TABLE `tb_depositions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tb_invoice_info`
--
ALTER TABLE `tb_invoice_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `tb_login`
--
ALTER TABLE `tb_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_plans`
--
ALTER TABLE `tb_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_plans_interval`
--
ALTER TABLE `tb_plans_interval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `tb_scripts`
--
ALTER TABLE `tb_scripts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `tb_subscriptions`
--
ALTER TABLE `tb_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `tb_visits`
--
ALTER TABLE `tb_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
