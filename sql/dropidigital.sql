-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Nov-2023 às 19:46
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
(70, 'foto_teste.png', 30),
(71, 'produto-teste.jpg', 30),
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
(1, 1, '24736295', 'Rua Teste', 999, '', 'Teste', 'Teste', 'TS'),
(3, 1, '11111-222', 'Rua Teste', 999, '', 'Teste', 'Teste', 'TS');

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
(35, 1, 'Categoria de teste 3', '6547160fea0b5.jpg', '1699157519.jpg', 'Categoria de teste 3', 'categoria-de-teste-3', '1', 1, 1, 'Categoria de teste 3', 'categoria-de-teste-3', 'Categoria de teste 3'),
(36, 1, 'Categoria de teste 4', '654716839d830.jpg', '1699157635.jpg', 'Categoria de teste 4', 'categoria-de-teste-4', '1', 1, 1, 'Categoria de teste 4', 'categoria-de-teste-4', 'Categoria de teste 4');

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
  `customer_id` varchar(255) NOT NULL,
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
(3, 1, 'cus_000005797346', 'Admin', 'admin@admin.com', '(11) 98765-4321', 'cpf', '20553240714', '24736295', 'Rua Teste', 999, NULL, 'Teste', 'Teste', 'TS');

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
(30, 1, 1, 1, 'Produto 1', 'produto-1', '149.90', '97.00', 'https://www.youtube.com/watch?v=AVVdFT6CTZQ', '<h2>Descricao do produto</h2>', 'SKU', 23, '1', '1', 'https://api.whatsapp.com/send?phone=(11)%2098765-4321', 'Produto 4 | Minha Loja', 'produto-4', 'Descricao do produto', '2023-10-17 02:16:11'),
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
(1, 1, 1, 'Minha Loja', 'Home | Minha Loja', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur eu rutrum diam, ut facilisis justo. Praesent sollicitudin fringilla tellus, sed mollis ipsum consectetur eget. Nulla lobortis mauris vitae enim semper, quis convallis nulla cursus. Praesent dapibus semper lacus at bibendum.', 'logo-one.png', 'logo-mobile.png', 'logo-mobile.png', '', 'https://twitter.com/seutwitter', '', 'https://facebook.com/seuinstagram', 'https://www.youtube.com/seuyoutube', '', '', 'minha-loja', '3', '000.111.222-33', NULL, '(11) 98765-4321', NULL, 'seu-email@mail.com', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '1', 'Toda a loja com descontos de até 50%', '1, 2, 3'),
(2, 0, 1, 'minha-loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '0', '', NULL, '(21) 97277-5758', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(3, 0, 1, 'Minerva', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '', '3', '11122233344', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(4, 0, 1, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '3', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(5, 0, 1, 'Minerva Bookstrore', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minerva-bookstrore', '0', 'XX.XXX.XXX/0001-XX', 'Minerva Bookstore LTDA.', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(6, 4, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja', '4', '111.222.333-44', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(7, 4, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'harry-potter', '3', '11122233344', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(8, 8, 1, 'ddaw', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'ddaw', '1', '', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(9, 8, 1, 'dawd', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'dawd', '1', '', '', '', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(10, 8, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-2', '1', '000.111.222-33', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL),
(11, 8, 1, 'Minha Loja', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'minha-loja-3', '0', '111.222.333-44', '', '(11) 98765-4321', NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '', 1, 'all', 'Toda a loja com descontos de até 50%', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_subscriptions`
--

CREATE TABLE `tb_subscriptions` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `subscription_id` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `billing_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `due_date` datetime NOT NULL,
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

INSERT INTO `tb_subscriptions` (`id`, `shop_id`, `customer_id`, `subscription_id`, `value`, `billing_type`, `status`, `due_date`, `cycle`, `pix_expirationDate`, `pix_encodedImage`, `pix_payload`, `credit_card_number`, `credit_card_flag`) VALUES
(1, 1, 'cus_000005797346', 'sub_dugufxpk0mxdhenb', '47', 'PIX', 'ACTIVE', '2023-12-30 00:00:00', 'MONTHLY', NULL, NULL, NULL, NULL, NULL),
(2, 1, 'cus_000005797346', 'sub_ku7jyf2bwnthae1y', '47', 'PIX', 'ACTIVE', '2023-12-30 00:00:00', 'MONTHLY', '2023-11-29 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(3, 1, 'cus_000005797346', 'sub_930tn012jnguj749', '47', 'PIX', 'ACTIVE', '2023-12-30 00:00:00', 'MONTHLY', '2023-11-29 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(4, 1, 'cus_000005797346', 'sub_fjex4c4i7vlfor5u', '47', 'PIX', 'ACTIVE', '2023-12-30 00:00:00', 'MONTHLY', '2023-11-29 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL),
(5, 1, 'cus_000005797346', 'sub_u99wjdcl3erlxsb0', '127', 'PIX', 'RECEIVED', '2023-12-30 00:00:00', 'MONTHLY', '2023-11-29 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca255204000053039865406127.005802BR5905ASAAS6009JOINVILLE62070503***6304C345', NULL, NULL),
(6, 1, 'cus_000005797346', 'sub_5umzdl8w6rrkg2r5', '1910', 'CREDIT_CARD', 'ACTIVE', '2024-11-30 00:00:00', 'YEARLY', NULL, NULL, NULL, '4242', 'VISA'),
(7, 1, 'cus_000005797346', 'sub_vapkew6oazmjuxl3', '79', 'CREDIT_CARD', 'ACTIVE', '2023-12-30 00:00:00', 'MONTHLY', NULL, NULL, NULL, '4242', 'VISA'),
(8, 1, 'cus_000005797346', 'sub_299ubqop4nsfoxpu', '1910', 'PIX', 'RECEIVED', '2024-11-30 00:00:00', 'YEARLY', '2023-11-30 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca2552040000530398654071910.005802BR5905ASAAS6009JOINVILLE62070503***63049C58', NULL, NULL),
(9, 1, 'cus_000005797346', 'sub_aqtgkbjxn3e0vrqu', '47', 'PIX', 'RECEIVED', '2023-12-30 00:00:00', 'MONTHLY', '2023-11-30 23:59:59', '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', '00020101021226820014br.gov.bcb.pix2560qrpix-h.bradesco.com.br/9d36b84f-c70b-478f-b95c-12729b90ca25520400005303986540547.005802BR5905ASAAS6009JOINVILLE62070503***6304E546', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `permissions` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `docType` varchar(255) NOT NULL,
  `docNumber` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `recup_password` varchar(255) DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_users`
--

INSERT INTO `tb_users` (`id`, `permissions`, `name`, `email`, `docType`, `docNumber`, `phone`, `password`, `recup_password`, `date_create`, `last_login`) VALUES
(1, 755, 'Admin', 'admin@admin.com', 'cpf', '20553240714', '(11) 98765-4321', '8923r45j89uj3we2nrftufnrieuw3nrfw-rj3jrijo3rmi9mkofsefesf', NULL, '2023-07-25 22:21:42', '2023-07-25 22:21:42'),
(2, 0, 'Cauã', 'cauaserpa007@gmail.com', '', '', '', '$2y$10$lTWruPe3OSS3BGhiey8FjuQDBj2IfkYmS50pALl07z5XONAf83dcu', NULL, '2023-08-01 15:43:57', '2023-08-01 15:43:57'),
(3, 0, 'Admin', 'adminA1@gmail.com', '', '', '', '$2y$10$j2n74pJXaKubs87NVzTeo.Zd1DcX0/xrniZ9DyYCK4eFs1YBQ.QIi', NULL, '2023-08-05 02:58:22', '2023-08-05 02:58:22'),
(4, 0, 'Admin', 'adminA2@gmail.com', '', '', '', '$2y$10$fJDtLRNGchUUQIThcJttYugwysI6jEwOv./PlFRFepd/nPPGBAbf2', NULL, '2023-08-05 03:01:34', '2023-08-05 03:01:34'),
(5, 0, 'awdawd', 'awdawd', '', '', '', '$2y$10$OcFTYw9ah1VBnUqckA7O3evJAx8fwfHi9Gwr3lN1b4pB5r.Y9V20C', NULL, '2023-08-13 03:50:29', '2023-08-13 03:50:29'),
(6, 0, 'awdawd', 'awdawdawd', '', '', '', '$2y$10$AW3lXQH4MSK5DjH.kplvBOkEjdHTKossnODbMlS/xCC9puCqxdYAe', NULL, '2023-08-13 03:50:54', '2023-08-13 03:50:54'),
(7, 0, 'Cauã', 'admin@gmail.com', '', '', '', '$2y$10$mBuhBMZ1a/thzBCmv2MOA.08WcezEUwtIvov14XwcU8O24Ox0lu9y', NULL, '2023-08-13 04:17:06', '2023-08-13 04:17:06'),
(8, 0, 'Cauã', 'adminB1@gmail.com', '', '', '', '$2y$10$KwAE77y/fnNo5tvLpBmPtOmHTG9QxF2KE5pU7XdM0A4WCeMBdB72i', NULL, '2023-08-15 05:56:01', '2023-08-15 05:56:01'),
(9, 0, 'Cauã', 'adminC1@gmail.com', '', '', '', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(10, 0, 'Cauã', 'adminC1@gmail.com', '', '', '', '$2y$10$jGj75zuwai81V1fp5.SuK.8JLy1Hm7cOzFcLwIgrVEFwuWbbUMY9O', NULL, '2023-08-16 14:55:08', '2023-08-16 14:55:08'),
(11, 0, 'Cauã', 'adminC2@gmail.com', '', '', '', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(12, 0, 'Cauã', 'adminC2@gmail.com', '', '', '', '$2y$10$UXCEA4jRTLgAkQYLYOJd4eWTVpV9oF8WmeR1HQdRxQpdFcJsOYpSa', NULL, '2023-08-16 14:55:55', '2023-08-16 14:55:55'),
(13, 0, 'Cauã', 'adminC3@gmail.com', '', '', '', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(14, 0, 'Cauã', 'adminC3@gmail.com', '', '', '', '$2y$10$su2Pv.pw6YpEO1NJ6oxyIOx3aSyKTF36pH4MmcCXJViN.B4jQTlG6', NULL, '2023-08-16 14:59:28', '2023-08-16 14:59:28'),
(15, 0, 'Cauã', 'adminC4@gmail.com', '', '', '', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(16, 0, 'Cauã', 'adminC4@gmail.com', '', '', '', '$2y$10$e5AidIVCaodhvwSRWhivPu2R7Zn97eYF10AP4BApCbZiNqJlCkrLq', NULL, '2023-08-16 15:00:27', '2023-08-16 15:00:27'),
(17, 0, 'Cauã', 'adminC5@gmail.com', '', '', '', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(18, 0, 'Cauã', 'adminC5@gmail.com', '', '', '', '$2y$10$eifCz.ORusNAuSAq6vGY1.a/1ecpDZyW1DCrFEm865jPtg6KQ/BGu', NULL, '2023-08-16 15:04:56', '2023-08-16 15:04:56'),
(19, 0, 'Lucas Nascimento', 'adminC6@gmail.com', '', '', '', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(20, 0, 'Lucas Nascimento', 'adminC6@gmail.com', '', '', '', '$2y$10$No.v.EoCNJGMO8GrE58BBeQ4UlU1zUYwVLS6DzYOu5NLtixhZtP2O', NULL, '2023-08-16 15:05:52', '2023-08-16 15:05:52'),
(21, 0, 'Artur Matos', 'adminC7@gmail.com', '', '', '', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(22, 0, 'Artur Matos', 'adminC7@gmail.com', '', '', '', '$2y$10$KXlNX0F0bM6iUj/KHQymru873TPOHqBlFeM83CoTZUMMs8Gvnvgy6', NULL, '2023-08-16 15:15:14', '2023-08-16 15:15:14'),
(23, 0, 'Cauã', 'cauaserpa091@gmail.com', '', '', '', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(24, 0, 'Cauã', 'cauaserpa091@gmail.com', '', '', '', '$2y$10$eVvWA.QW8FKzkVAS.ge4IutS.cTd9NJ7wPuXcQsnshIvfSLFg4kE6', NULL, '2023-08-17 23:33:16', '2023-08-17 23:33:16'),
(25, 0, 'Cauã', 'adminD1@gmail.com', '', '', '', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56'),
(26, 0, 'Cauã', 'adminD1@gmail.com', '', '', '', '$2y$10$..5QHxl91b11m7//oJmGHehFu9AeGDGIEfvrSUCCKLzA6Z5WlK.8S', NULL, '2023-08-17 23:43:56', '2023-08-17 23:43:56');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_visits`
--

CREATE TABLE `tb_visits` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `contagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `tb_visits`
--

INSERT INTO `tb_visits` (`id`, `data`, `contagem`) VALUES
(1, '2023-10-09', 17),
(2, '2023-10-08', 5),
(3, '2023-10-07', 28),
(4, '2023-10-06', 13),
(5, '2023-10-05', 27),
(6, '2023-09-11', 15),
(7, '2023-09-15', 24),
(8, '2023-08-24', 14),
(9, '2023-09-11', 150),
(10, '2023-10-04', 64),
(11, '2023-10-11', 42),
(12, '2023-10-12', 336),
(13, '2023-10-17', 26),
(14, '2023-10-18', 24),
(15, '2023-10-19', 5),
(16, '2023-10-24', 5),
(17, '2023-10-25', 45),
(18, '2023-10-26', 459),
(19, '2023-10-27', 18),
(21, '2023-10-28', 2),
(22, '2023-10-29', 1296),
(23, '2023-10-30', 18),
(24, '2023-10-31', 1),
(25, '2023-11-01', 290),
(26, '2023-11-02', 16),
(27, '2023-11-03', 1),
(28, '2023-11-04', 1196),
(29, '2023-11-05', 418),
(30, '2023-11-06', 3),
(31, '2023-11-07', 3),
(32, '2023-11-14', 2),
(33, '2023-11-15', 1),
(34, '2023-11-27', 2);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de tabela `tb_address`
--
ALTER TABLE `tb_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tb_articles`
--
ALTER TABLE `tb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `tb_banner_img`
--
ALTER TABLE `tb_banner_img`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `tb_banner_info`
--
ALTER TABLE `tb_banner_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `tb_scripts`
--
ALTER TABLE `tb_scripts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_shop`
--
ALTER TABLE `tb_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `tb_subscriptions`
--
ALTER TABLE `tb_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `tb_visits`
--
ALTER TABLE `tb_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
