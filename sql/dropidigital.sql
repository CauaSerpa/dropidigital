-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 10/07/2024 às 05:06
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

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
-- Estrutura para tabela `imagens`
--

CREATE TABLE `imagens` (
  `id` int(11) NOT NULL,
  `nome_imagem` varchar(255) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_address`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ai_historic`
--

CREATE TABLE `tb_ai_historic` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ai_request`
--

CREATE TABLE `tb_ai_request` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `request` varchar(255) NOT NULL,
  `number_keywords` int(11) NOT NULL,
  `response` text NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_articles`
--

CREATE TABLE `tb_articles` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `emphasis` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `seo_name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `seo_description` varchar(255) NOT NULL,
  `last_modification` datetime DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_banner_img`
--

CREATE TABLE `tb_banner_img` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `mobile_banner` varchar(255) DEFAULT NULL,
  `banner_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_banner_info`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_blog`
--

CREATE TABLE `tb_blog` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `emphasis` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `description` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `seo_name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `seo_description` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_categories`
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
  `seo_description` text NOT NULL,
  `last_modification` datetime DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_depositions`
--

CREATE TABLE `tb_depositions` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `testimony` varchar(200) NOT NULL,
  `qualification` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_domains`
--

CREATE TABLE `tb_domains` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `subdomain` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `configure` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `register_date` datetime NOT NULL,
  `configure_date` datetime DEFAULT NULL,
  `active_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_home`
--

CREATE TABLE `tb_home` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `title-1` varchar(255) NOT NULL,
  `content-1` text NOT NULL,
  `image-1` varchar(255) NOT NULL,
  `subtitle-2` varchar(255) NOT NULL,
  `title-2` varchar(255) NOT NULL,
  `about-subtitle-3` varchar(255) NOT NULL,
  `about-title-3` varchar(255) NOT NULL,
  `about-content-3` text NOT NULL,
  `about-image-1` varchar(255) NOT NULL,
  `about-image-2` varchar(255) NOT NULL,
  `service-icon-1` varchar(255) NOT NULL,
  `service-title-1` varchar(255) NOT NULL,
  `service-description-1` varchar(255) NOT NULL,
  `service-icon-2` varchar(255) NOT NULL,
  `service-title-2` varchar(255) NOT NULL,
  `service-description-2` varchar(255) NOT NULL,
  `subtitle-4` varchar(255) NOT NULL,
  `title-4` varchar(255) NOT NULL,
  `subtitle-5` varchar(255) NOT NULL,
  `title-5` varchar(255) NOT NULL,
  `subtitle-6` varchar(255) NOT NULL,
  `title-6` varchar(255) NOT NULL,
  `subtitle-7` varchar(255) NOT NULL,
  `title-7` varchar(255) NOT NULL,
  `subtitle-8` varchar(255) NOT NULL,
  `title-8` varchar(255) NOT NULL,
  `subtitle-9` varchar(255) NOT NULL,
  `title-9` varchar(255) NOT NULL,
  `image-9` varchar(255) NOT NULL,
  `subtitle-10` varchar(255) NOT NULL,
  `title-10` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_home`
--

INSERT INTO `tb_home` (`id`, `logo`, `description`, `email`, `whatsapp`, `location`, `facebook`, `twitter`, `instagram`, `linkedin`, `title-1`, `content-1`, `image-1`, `subtitle-2`, `title-2`, `about-subtitle-3`, `about-title-3`, `about-content-3`, `about-image-1`, `about-image-2`, `service-icon-1`, `service-title-1`, `service-description-1`, `service-icon-2`, `service-title-2`, `service-description-2`, `subtitle-4`, `title-4`, `subtitle-5`, `title-5`, `subtitle-6`, `title-6`, `subtitle-7`, `title-7`, `subtitle-8`, `title-8`, `subtitle-9`, `title-9`, `image-9`, `subtitle-10`, `title-10`) VALUES
(1, 'logo-one.jpeg', 'Somos uma empresa de desenvolvimento de software com uma equipe altamente capacitada e dedicada. Acreditamos que as soluções de tecnologia devem ser acessíveis a todos e oferecer resultados concretos.', 'suporte@dropidigital.com.br', '+55 11 94049-6818', 'São Paulo', 'https://facebook.com/dropidigital', 'https://twitter.com/dropidigital', 'https://instagram.com/dropidigital', 'https://linkedin.com/dropidigital', '#DropiDigital', 'Crie seu site 5 em minutos na Dropi Digital e coloque seu serviço na Internet ainda hoje. Serviço autônomo, comércio físico, dropshipping de Infoprodutos ou produto físicos.\r\n\r\nTodas as possibilidades e um únicos lugar. Dropi Digital.\r\n\r\nSomos o Integrador com melhores programas de afiliados do mercado. Hotmart, kiwify, Eduzz, Monetizee, Amazon, Shopee, Magazine Luiza, shein, Clickbank entre outros.\r\n\r\nClique em criar conta e comece agora, mesmo que seja iniciante.\r\nÉ grátis.', 'hero-one.jpg', 'Conheça os melhores programas de afiliados do mercado', 'Empresas para gerar seus links de afiliados e montar sua loja aqui na Dropi Digital', 'SOBRE NOS!', 'Bem-vindos a nossa fábrica de Sites', 'Somos uma plataforma que permite aos seus clientes / usuários à criarem seus sites de serviços, dropshipping de produtos digitais e produtos físicos.\r\n\r\nNa Dropi Digital você criar seu site, cadastrando o seus links de afiliados permitindo a divulgação de diversos produtos ao mesmo tempo.\r\n\r\nAlém da possíbilidade de dropshipping, aqui na Dropi Digital você pode criar um site de catálogo e divulgar sua empresa e serviços, com chamada de ação para compra, conversa no WhatsApp, botão saber mais e agenda.', 'about-2.jpg', 'about-1.jpg', 'icon2.jpg', 'Afiliados', 'Monte sua loja virtual em poucos cliques com os produtos do qual é afiliados. E comece a vender ainda hoje.', 'icon1.jpg', 'Site de catálogo', 'Monte seu site cadastrando seus produtos e seviços totalmente otimizado para o google.', 'Vá direto ao ponto!', 'Pare de perde tempo com blogs e ficar torcendo por cliques no seu link de afiliado. Aqui na Dropi Digital você cria uma loja com as palavras que o cliente está pesquisando.', 'Siga esse passo a passo para criar sua loja de Dropshipping na Dropi Digital e venda todos os dias como afiliado mesmos sem estoque.', '5 Passos simples para vender como afiliado na Dropi Digital', 'Como montar meu site de serviços na Dropi Digital e ter um negócio lucrativo na Internet', '5 Passos comprovados para ter um negócio na internet e receber contatos todos os dias.', 'Planos da Dropi Digital', 'Crie sua conta e escolha o melhor plano para você', 'Chame os especialistas', 'UM TIME DE PESO, CRIANDO SOLUÇÕES DE SOFTWARE INCRÍVEIS', 'Faqs', 'Perguntas frequentes', 'faq-right.jpg', 'Dúvidas?', 'Tem um projeto? Gostaríamos muito de ouvir de você.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_home_services`
--

CREATE TABLE `tb_home_services` (
  `id` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_home_services`
--

INSERT INTO `tb_home_services` (`id`, `icon`, `title`, `description`, `date_create`) VALUES
(1, '', 'Consultoria Digital', 'Informe o seu projeto e tediremos como você pode aplica-lo aqui na Dropi Digital. Afiliado, Dropshipping de produtos físicos e serviços.\r\n\r\n', '2024-04-05 05:49:20'),
(2, '', 'Criamos a sua loja', 'A plataforma é totalmente intuitiva, e você pode sozinho montar sua loja, mas caso precise que alguém monte seu site aqui. Montamos para você, com banners, produtos cadastrados e totalmente otimizado para os mecanismos de busca. Consultar valores.', '2024-04-05 05:50:15'),
(3, '', 'Criamos os Banners da sua Loja/Site', 'Precisa da criação dos banners para sua loja? Fique tranquilo, fazemos para você. Envie as especificações e montamos as suas artes. Consultar valores.', '2024-04-05 05:50:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_invoice_info`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_login`
--

CREATE TABLE `tb_login` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `first_used_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_newsletter`
--

CREATE TABLE `tb_newsletter` (
  `shop_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_orders`
--

CREATE TABLE `tb_orders` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_order_requests`
--

CREATE TABLE `tb_order_requests` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pages`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_partners`
--

CREATE TABLE `tb_partners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_payments`
--

CREATE TABLE `tb_payments` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `installmentCount` int(11) DEFAULT NULL,
  `installmentValue` decimal(10,2) DEFAULT NULL,
  `billing_type` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `due_date` date DEFAULT NULL,
  `undefined` tinyint(1) NOT NULL DEFAULT 0,
  `cycle` varchar(255) NOT NULL,
  `pix_expirationDate` datetime DEFAULT NULL,
  `pix_encodedImage` longtext DEFAULT NULL,
  `pix_payload` longtext DEFAULT NULL,
  `credit_card_number` varchar(255) DEFAULT NULL,
  `credit_card_flag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_plans`
--

CREATE TABLE `tb_plans` (
  `id` int(11) NOT NULL,
  `plan_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sub_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `resources` text NOT NULL,
  `link_checkout` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_plans_interval`
--

CREATE TABLE `tb_plans_interval` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `billing_interval` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_products`
--

CREATE TABLE `tb_products` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `emphasis` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `without_price` tinyint(1) NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL,
  `video` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sku` varchar(255) NOT NULL,
  `checkout` varchar(255) NOT NULL,
  `button_type` tinyint(1) NOT NULL,
  `redirect_link` varchar(255) NOT NULL,
  `seo_name` varchar(70) NOT NULL,
  `link` varchar(255) NOT NULL,
  `seo_description` varchar(160) NOT NULL,
  `last_modification` datetime DEFAULT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_product_categories`
--

CREATE TABLE `tb_product_categories` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `main` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ready_sites`
--

CREATE TABLE `tb_ready_sites` (
  `id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `emphasis` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `support` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `without_price` tinyint(1) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `card_image` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `items_included` text DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `seo_name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `seo_description` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ready_site_img`
--

CREATE TABLE `tb_ready_site_img` (
  `id` int(11) NOT NULL,
  `ready_site_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ready_site_services`
--

CREATE TABLE `tb_ready_site_services` (
  `id` int(11) NOT NULL,
  `ready_site_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `main` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_scripts`
--

CREATE TABLE `tb_scripts` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `script` text NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_services`
--

CREATE TABLE `tb_services` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `emphasis` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `without_price` tinyint(1) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `card_image` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `tooltip_content` varchar(160) DEFAULT NULL,
  `items_included` text DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `seo_name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `seo_description` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_services`
--

INSERT INTO `tb_services` (`id`, `status`, `emphasis`, `name`, `price`, `without_price`, `discount`, `card_image`, `image`, `video`, `description`, `tooltip_content`, `items_included`, `sku`, `seo_name`, `link`, `seo_description`, `date_create`) VALUES
(1, 1, 0, 'Criação de Logo', 199.00, 0, 0.00, 'foto_teste.png', '1717110078.jpg', '', '<h3>Cria&ccedil;&atilde;o de Logo</h3>', 'Teste para tooltip', '', 'D6A8KMOBNX', 'Criação de Logo', 'criacao-de-logo', 'Criação de Logo', '2024-04-20 01:15:18'),
(2, 1, 0, 'Teste 1', 99.00, 0, 0.00, '1717110662.jpg', '1717110662.jpg', '', '<p>Teste 1</p>', 'Esse servico faz tal coisa', '[\"Teste 1\"]', 'FR4NWX9OHO', 'Teste 1', 'teste-1', 'Teste 1', '2024-05-30 23:11:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_service_img`
--

CREATE TABLE `tb_service_img` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_service_services`
--

CREATE TABLE `tb_service_services` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `associated_service_id` int(11) NOT NULL,
  `main` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_shop`
--

CREATE TABLE `tb_shop` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
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
  `tiktok` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `token_instagram` varchar(255) DEFAULT NULL,
  `segment` varchar(255) NOT NULL,
  `detailed_segment` varchar(255) DEFAULT NULL,
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
  `center_highlight_images` varchar(255) DEFAULT NULL,
  `last_modification` datetime DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_shop_users`
--

CREATE TABLE `tb_shop_users` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_subscriptions`
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
  `due_date` date DEFAULT NULL,
  `undefined` tinyint(1) NOT NULL DEFAULT 0,
  `cycle` varchar(255) NOT NULL,
  `pix_expirationDate` datetime DEFAULT NULL,
  `pix_encodedImage` longtext DEFAULT NULL,
  `pix_payload` longtext DEFAULT NULL,
  `credit_card_number` varchar(255) DEFAULT NULL,
  `credit_card_flag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `permissions` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active_email` tinyint(1) NOT NULL DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `docType` varchar(255) NOT NULL,
  `docNumber` varchar(255) NOT NULL,
  `razaoSocial` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `recup_password` varchar(255) DEFAULT NULL,
  `two_factors` tinyint(1) NOT NULL DEFAULT 0,
  `two_factors_token` varchar(255) DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `last_shop_login` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_visits`
--

CREATE TABLE `tb_visits` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `data` date NOT NULL,
  `contagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_warning`
--

CREATE TABLE `tb_warning` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `imagens`
--
ALTER TABLE `imagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_address`
--
ALTER TABLE `tb_address`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_ai_historic`
--
ALTER TABLE `tb_ai_historic`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_ai_request`
--
ALTER TABLE `tb_ai_request`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_articles`
--
ALTER TABLE `tb_articles`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_blog`
--
ALTER TABLE `tb_blog`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_depositions`
--
ALTER TABLE `tb_depositions`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_domains`
--
ALTER TABLE `tb_domains`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_home`
--
ALTER TABLE `tb_home`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_home_services`
--
ALTER TABLE `tb_home_services`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_invoice_info`
--
ALTER TABLE `tb_invoice_info`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_login`
--
ALTER TABLE `tb_login`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_orders`
--
ALTER TABLE `tb_orders`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_order_requests`
--
ALTER TABLE `tb_order_requests`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_partners`
--
ALTER TABLE `tb_partners`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_payments`
--
ALTER TABLE `tb_payments`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_plans`
--
ALTER TABLE `tb_plans`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_plans_interval`
--
ALTER TABLE `tb_plans_interval`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_product_categories`
--
ALTER TABLE `tb_product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_ready_sites`
--
ALTER TABLE `tb_ready_sites`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_ready_site_img`
--
ALTER TABLE `tb_ready_site_img`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_ready_site_services`
--
ALTER TABLE `tb_ready_site_services`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_scripts`
--
ALTER TABLE `tb_scripts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_services`
--
ALTER TABLE `tb_services`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_service_img`
--
ALTER TABLE `tb_service_img`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_service_services`
--
ALTER TABLE `tb_service_services`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_shop_users`
--
ALTER TABLE `tb_shop_users`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_subscriptions`
--
ALTER TABLE `tb_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_visits`
--
ALTER TABLE `tb_visits`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_warning`
--
ALTER TABLE `tb_warning`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `imagens`
--
ALTER TABLE `imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_address`
--
ALTER TABLE `tb_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_ai_historic`
--
ALTER TABLE `tb_ai_historic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_ai_request`
--
ALTER TABLE `tb_ai_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_articles`
--
ALTER TABLE `tb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_blog`
--
ALTER TABLE `tb_blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_categories`
--
ALTER TABLE `tb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_depositions`
--
ALTER TABLE `tb_depositions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_domains`
--
ALTER TABLE `tb_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_home`
--
ALTER TABLE `tb_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_home_services`
--
ALTER TABLE `tb_home_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tb_invoice_info`
--
ALTER TABLE `tb_invoice_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_login`
--
ALTER TABLE `tb_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_orders`
--
ALTER TABLE `tb_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_order_requests`
--
ALTER TABLE `tb_order_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_pages`
--
ALTER TABLE `tb_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_partners`
--
ALTER TABLE `tb_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_payments`
--
ALTER TABLE `tb_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_plans`
--
ALTER TABLE `tb_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_plans_interval`
--
ALTER TABLE `tb_plans_interval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_product_categories`
--
ALTER TABLE `tb_product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_ready_sites`
--
ALTER TABLE `tb_ready_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_ready_site_img`
--
ALTER TABLE `tb_ready_site_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_ready_site_services`
--
ALTER TABLE `tb_ready_site_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_scripts`
--
ALTER TABLE `tb_scripts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_services`
--
ALTER TABLE `tb_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tb_service_img`
--
ALTER TABLE `tb_service_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_service_services`
--
ALTER TABLE `tb_service_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_shop_users`
--
ALTER TABLE `tb_shop_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_subscriptions`
--
ALTER TABLE `tb_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_visits`
--
ALTER TABLE `tb_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_warning`
--
ALTER TABLE `tb_warning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
