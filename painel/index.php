<?php
    //Url Amigavel
    $url = isset($_GET['url']) ? $_GET['url'] : 'painel';

    //Edita o escrito da url para ser colocado no title
    if ($url == "") {
        $title = "Painel";
    } else {
        // Obtém a URL atual
        $urlPath = $url;

        // Remove qualquer string de consulta, se houver
        $urlPath = parse_url($urlPath, PHP_URL_PATH);

        // Divide a URL em partes
        $urlParts = explode('/', trim($urlPath, '/'));

        // Verifica se há partes suficientes na URL
        if (count($urlParts) >= 2) {
            $urlTitle = $urlParts[1]; // O nome do arquivo é a segunda parte
        } else {
            $urlTitle = $url;
        }

        $title = ucwords(str_replace("-", " ", $urlTitle));
    }

    // Caminho para o diretório pai
    $parentDir = dirname(__DIR__);

	require $parentDir . '/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
	$dotenv->load();

    // Informacoes para PHPMailer
	$recaptcha_token = $_ENV['RECAPTCHA_CHAVE_DE_SITE'];

    session_start();
    ob_start();
    include('../config.php');

    if ($url !== "login" && $url !== "dois-fatores" && $url !== "recuperar-senha" && $url !== "atualizar-senha" && $url !== "assinar" && $url !== "criar-loja" && $url !== "404") {
        // Verifica se esta logado
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['msg'] = "Por favor faça login para acessar essa página!";
            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'login');
            exit();
        }
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_users";

    // ID que você deseja pesquisar
    $id = @$_SESSION['user_id'];

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    $permissions = "";

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $permissions = (isset($_SESSION['ready_site'])) ? 0 : $resultado['permissions'];
        $name = $resultado['name'];
        $email = $resultado['email'];
        $docType = $resultado['docType'];
        $docNumber = $resultado['docNumber'];
        $razaoSocial = $resultado['razaoSocial'];
        $phone = $resultado['phone'];
        $last_shop_login = $resultado['last_shop_login'];
    }

    // Usuario Loja

    // Verifica se há um shop_id definido na sessão
    @$shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id'] : $last_shop_login;

    // Tabela que sera feita a consulta
    $tabela = "tb_shop_users";

    // Ajusta a consulta SQL para dar prioridade ao shop_id se ele existir
    if ($shop_id) {
        $sql = "SELECT * FROM $tabela WHERE user_id = :user_id AND shop_id = :shop_id LIMIT 1";
    } else {
        $sql = "SELECT * FROM $tabela WHERE user_id = :user_id ORDER BY id DESC LIMIT 1";
    }

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
    if ($shop_id) {
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    }

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        $shop_id = $resultado['shop_id'];
    }

    // Pesquisar Loja

    // Tabela que será feita a consulta
    $tabela = "tb_shop";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id LIMIT 1";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $shop_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $user_id = $id;
        $id = $resultado['id'];
        // $plan_id = $resultado['plan_id'];
        $loja = $resultado['name'];
        $phone = $resultado['phone'];
        $whatsapp = $resultado['whatsapp'];
        $detailed_segment = $resultado['detailed_segment'];
    }

    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT plan_id FROM $tabela WHERE (status = :status OR status = :status1) AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindValue(':status', 'ACTIVE');
    $stmt->bindValue(':status1', 'RECEIVED');
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    $plan_id = (isset($plan['plan_id'])) ? $plan['plan_id'] : 1;

    // Nome da tabela para a busca
    $tabela = 'tb_plans_interval';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT plan_id FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':id', $plan_id);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Definir o limite de produtos baseado no plano
    $planLimits = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 900,
        5 => 5000,
    ];
    
    $limitProducts = $planLimits[$plan['plan_id']] ?? 10;

    // Nome da tabela para a busca
    $tabela = 'tb_plans';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT name FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':id', $plan['plan_id']);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $plan_name = $plan_info['name'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo $title; ?> | Dropidigital</title>

    <!--Favicon-->
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH; ?>assets/images/favicon.png" type="image/x-icon">
    <!--CSS-->
    <?php echo ($url == 'login' || $url == 'dois-fatores' || $url == 'recuperar-senha' || $url == 'atualizar-senha' || $url == 'assinar' || $url == 'criar-loja' || $url == '404') ? '<link rel="stylesheet" href="'.INCLUDE_PATH_DASHBOARD.'assets/css/login.css">' : '<link rel="stylesheet" href="'.INCLUDE_PATH_DASHBOARD.'assets/css/style.css">';?>
    <!--Box Icons-->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Intro JS -->
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.0.1/minified/introjs.min.css" rel="stylesheet">
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Inclua as folhas de estilo do Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Mercado pago -->
    <?php
        if ($url == 'assinar-plano')
        {
            echo '<script src="https://www.mercadopago.com/v2/security.js" view="checkout" output="deviceId"></script>';
        } else {
            echo '<script src="https://www.mercadopago.com/v2/security.js" view="home"></script>';
        }
    ?>
</head>
<body>
    <?php
        if ($url == 'login' || $url == 'dois-fatores' || $url == 'recuperar-senha' || $url == 'atualizar-senha' || $url == 'assinar' || $url == 'criar-loja' || $url == '404')
        {
    ?>
    <header class="l-header login">
        <nav class="nav bd-grid">
            <a href="<?php echo INCLUDE_PATH; ?>" class="nav__logo center">
                <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
            </a>
        </nav>
    </header>
    <main class="main">
        <div class="container__box">
            <div class="box__slide">
                <div class="carroussel">
                    <div class="container__carroussel">
                        <div class="slide">
                            <div class="background">
                            </div>
                            <div class="slides">
                                <div id="atual" class="image">
                                </div>
                                <div class="image">
                                </div>
                                <div class="image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container__description">
                    <div class="description">
                        <h2 class="title">Sobre a DropiDigital</h2>
                        <p class="description">
                            Crie seu site 5 em minutos na Dropi Digital e coloque seu serviço na Internet ainda hoje.<br>
                            Serviço autônomo, comércio físico, dropshipping de Infoprodutos ou produto físicos.<br><br>
                            Todas as possibilidades e um únicos lugar. Dropi Digital.<br><br>
                            Clique em criar conta e comece agora, mesmo que seja iniciante. É grátis.</p>
                    </div>
                    <div class="balls"></div>
                </div>
            </div>
    <?php
        }
        elseif ($permissions == 1 || $permissions == 2)
        {
    ?>
    <div class="tutorial__bg__"></div>
    <header class="l-header painel">
        <nav class="nav bd-grid">
            <div class="left">
                <div class="toggle">
                    <i class='bx bx-menu' id="mobileBtn"></i>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="nav__logo">
                    <img class="logo" src="" alt="Logo" id="logo">
                </a>
            </div>
            <div class="right">
                <div class="header__icon help">
                    <a href="https://suporte.dropidigital.com.br">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M12 6a3.939 3.939 0 0 0-3.934 3.934h2C10.066 8.867 10.934 8 12 8s1.934.867 1.934 1.934c0 .598-.481 1.032-1.216 1.626a9.208 9.208 0 0 0-.691.599c-.998.997-1.027 2.056-1.027 2.174V15h2l-.001-.633c.001-.016.033-.386.441-.793.15-.15.339-.3.535-.458.779-.631 1.958-1.584 1.958-3.182A3.937 3.937 0 0 0 12 6zm-1 10h2v2h-2z"></path><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path></svg>
                    </a>
                </div>
                <div class="container__user">
                    <div class="user__info" onclick="toggleUser()">
                        <img id="imagemUsuario" alt="Imagem de Perfil">
                        <div class="info">
                            <p><?php echo $name; ?></p>
                            <span>Administrador</span>
                        </div>
                        <i class='bx bx-chevron-down' ></i>
                    </div>
                    <div class="user" id="userWrap">
                        <div class="modal__user__info">
                            <h5 class="fs-5 mb-0"><?php echo $name; ?></h5>
                            <p class="small"><?php echo $email; ?></p>
                        </div>
                        <div class="theme__dark small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M20.742 13.045a8.088 8.088 0 0 1-2.077.271c-2.135 0-4.14-.83-5.646-2.336a8.025 8.025 0 0 1-2.064-7.723A1 1 0 0 0 9.73 2.034a10.014 10.014 0 0 0-4.489 2.582c-3.898 3.898-3.898 10.243 0 14.143a9.937 9.937 0 0 0 7.072 2.93 9.93 9.93 0 0 0 7.07-2.929 10.007 10.007 0 0 0 2.583-4.491 1.001 1.001 0 0 0-1.224-1.224zm-2.772 4.301a7.947 7.947 0 0 1-5.656 2.343 7.953 7.953 0 0 1-5.658-2.344c-3.118-3.119-3.118-8.195 0-11.314a7.923 7.923 0 0 1 2.06-1.483 10.027 10.027 0 0 0 2.89 7.848 9.972 9.972 0 0 0 7.848 2.891 8.036 8.036 0 0 1-1.484 2.059z"></path></svg>
                            <p>Tema Escuro</p>
                        </div>
                        <div class="account">
                            <h5 class="fs-5 mb-1">Minha Conta</h5>
                            <ul class="mb-0">
                                <li class="small">
                                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">
                                        Editar Conta
                                    </a>
                                </li>
                                <li class="small">
                                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">
                                        Sair
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <style>
        .sidebar
        {
            width: 266px !important;
            background: none !important;
        }
        .sidebar.close
        {
            width: 266px !important;
            border-right: none !important;
        }
        .sidebar .nav-links
        {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .sidebar.close .nav-links::before
        {
            content: "";
            position: absolute;
            left: 77px;
            height: 100%;
            width: 1px;
            background: var(--border-color);
        }

        .sidebar.close .nav-links li
        {
            width: min-content;
        }
        .sidebar.close .nav-links li .iocn-link
        {
            width: 78px !important;
            border-right: 1px solid var(--border-color) !important;
        }
        .sidebar.close .nav-links li .sub-menu
        {
            border-left: none !important;
        }

        .sidebar .sidebar_bottom
        {
            transition: none !important;
        }

        .offcanvas-filter
        {
            left: 266px !important;
        }
        .offcanvas-backdrop.fade.show
        {
            left: 266px !important;
        }
    </style>

    <nav class="sidebar close">
        <ul class="nav-links">
            <li class="<?php activeSidebarLink(''); ?> <?php activeSidebarLink('painel'); ?> <?php activeSidebarLink(''); ?> <?php activeSidebarLink('painel'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>">
                            <i class='bx bx-grid-alt' ></i>
                        </a>
                        <span class="link_name">Dashboard</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>">Dashboard</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('personalizar'); ?>" <?php verificaPermissaoMenu($permissions); ?>>
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>personalizar">
                            <i class='bx bx-layout'></i>
                        </a>
                        <span class="link_name">Personalizar</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>personalizar">Personalizar</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('lojas'); ?> <?php activeSidebarLink('ver-loja'); ?>" <?php verificaPermissaoMenu($permissions); ?>>
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>lojas">
                            <i class='bx bx-store' ></i>
                        </a>
                        <span class="link_name">Lojas</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>lojas">Lojas</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('dominios'); ?> <?php activeSidebarLink('dominios-proprios'); ?> <?php showSidebarLinks('dominios'); ?> <?php showSidebarLinks('dominios-proprios'); ?>" <?php verificaPermissaoMenu($permissions); ?>>
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dominios" class="sidebar_link">
                                <i class='bx bx-globe' ></i>
                            </a>
                            <span class="link_name">Domínios</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dominios">Domínios</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dominios" class="<?php activeSidebarLink('dominios'); ?>">Domínios</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dominios-proprios" class="<?php activeSidebarLink('dominios-proprios'); ?>">Domínios Próprios</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('site-catalogo'); ?> <?php activeSidebarLink('site-catalogo'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>site-catalogo">
                            <i class='bx bx-customize'></i>
                        </a>
                        <span class="link_name">Site Catálogo</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>site-catalogo">Site Catálogo</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('sugestoes-melhorias'); ?> <?php activeSidebarLink('ver-sugestao-melhoria'); ?> <?php activeSidebarLink('sugestoes-melhorias'); ?> <?php activeSidebarLink('ver-sugestao-melhoria'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sugestoes-melhorias">
                            <i class='bx bx-message-square-add' ></i>
                        </a>
                        <span class="link_name">Melhorias</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sugestoes-melhorias">Melhorias</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('novidades'); ?> <?php activeSidebarLink('novidades'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>novidades">
                            <i class='bx bx-bulb' ></i>
                        </a>
                        <span class="link_name">Novidades</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>novidades">Novidades</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('sites-prontos'); ?> <?php activeSidebarLink('criar-site-pronto'); ?> <?php activeSidebarLink('editar-site-pronto'); ?> <?php activeSidebarLink('relatorio-sites-prontos'); ?> <?php showSidebarLinks('sites-prontos'); ?> <?php showSidebarLinks('editar-site-pronto'); ?> <?php showSidebarLinks('criar-site-pronto'); ?> <?php showSidebarLinks('relatorio-sites-prontos'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sites-prontos" class="sidebar_link">
                                <i class='bx bx-windows'></i>
                            </a>
                            <span class="link_name">Sites Prontos</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sites-prontos">Sites Prontos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sites-prontos" class="<?php activeSidebarLink('sites-prontos'); ?> <?php activeSidebarLink('criar-site-pronto'); ?> <?php activeSidebarLink('editar-site-pronto'); ?>">Sites Prontos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>relatorio-sites-prontos" class="<?php activeSidebarLink('relatorio-sites-prontos'); ?>">Relatórios</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('servicos'); ?> <?php activeSidebarLink('criar-servico'); ?> <?php activeSidebarLink('editar-servico'); ?> <?php activeSidebarLink('relatorio-servicos'); ?> <?php showSidebarLinks('servicos'); ?> <?php showSidebarLinks('criar-servico'); ?> <?php showSidebarLinks('editar-servico'); ?> <?php showSidebarLinks('relatorio-servicos'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="sidebar_link">
                                <i class='bx bx-wrench' ></i>
                            </a>
                            <span class="link_name">Serviços</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos">Serviços</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="<?php activeSidebarLink('servicos'); ?> <?php activeSidebarLink('criar-servico'); ?> <?php activeSidebarLink('editar-servico'); ?>">Serviços</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>relatorio-servicos" class="<?php activeSidebarLink('relatorio-servicos'); ?>">Relatórios</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('artigos'); ?> <?php activeSidebarLink('criar-artigo'); ?> <?php activeSidebarLink('editar-artigo'); ?> <?php showSidebarLinks('artigos'); ?> <?php showSidebarLinks('criar-artigo'); ?> <?php showSidebarLinks('editar-artigo'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="sidebar_link">
                                <i class='bx bx-desktop' ></i>
                            </a>
                            <span class="link_name">Blog</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos">Blog</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="<?php activeSidebarLink('artigos'); ?> <?php activeSidebarLink('editar-artigo'); ?>">Listar Artigos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-artigo" class="<?php activeSidebarLink('criar-artigo'); ?>">+ Criar Artigo</a></li>
                </ul>
            </li>
            <div class="sidebar_bottom">
                <li>
                    <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">
                                <i class='bx bx-cog' ></i>
                            </a>
                            <span class="link_name">Configurações</span>
                        </p>
                    </div>
                    <ul class="sub-menu blank">
                        <li><a class="link_name ms-0" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">Configurações</a></li>
                    </ul>
                </li>
                <li>
                    <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">
                                <i class='bx bx-log-out' ></i>
                            </a>
                            <span class="link_name">Sair</span>
                        </p>
                    </div>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">Sair</a></li>
                    </ul>
                </li>
			</div>
		</ul>
    </nav>
    <?php
        }
        else
        {
    ?>
    <div class="tutorial__bg__"></div>
    <header class="l-header painel">
        <nav class="nav bd-grid">
            <div class="left">
                <div class="toggle">
                    <i class='bx bx-menu' id="mobileBtn"></i>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="nav__logo">
                    <img class="logo" src="" alt="Logo" id="logo">
                </a>
                <form class="search__form">
                    <div class="search__container">
                        <input type="text" name="search" id="search" class="search" placeholder="Buscar produto..." title="Buscar produto..." autocomplete="off">
                        <button type="button" class="button" id="buttonSearch">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="right">
                <div class="header__icon shop-link">
                    <?php
                        // Nome da tabela para a busca
                        $tabela = 'tb_domains';

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain <> 'dropidigital.com.br' ORDER BY (domain = 'dropidigital.com.br') DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $id);
                        $stmt->execute();

                        // Recuperar os resultados
                        $domain = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Se não houver resultados, realizar outra consulta
                        if (empty($domain)) {
                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain = 'dropidigital.com.br'";
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $id);
                            $stmt->execute();
                            $domain = $stmt->fetch(PDO::FETCH_ASSOC);
                        }

                        $subdomain = ($domain['subdomain'] !== "www") ? $domain['subdomain'] . "." : "";
                        $domain_url = "https://" . $subdomain . $domain['domain'];
                    ?>
                    <a href="<?php echo $domain_url; ?>" target="_blank" class="text-dark text-decoration-none fs-6 fw-semibold">
                        <span class="me-1">Ver o site</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
                    </a>
                </div>
                <div class="header__icon help">
                    <a href="https://suporte.dropidigital.com.br">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M12 6a3.939 3.939 0 0 0-3.934 3.934h2C10.066 8.867 10.934 8 12 8s1.934.867 1.934 1.934c0 .598-.481 1.032-1.216 1.626a9.208 9.208 0 0 0-.691.599c-.998.997-1.027 2.056-1.027 2.174V15h2l-.001-.633c.001-.016.033-.386.441-.793.15-.15.339-.3.535-.458.779-.631 1.958-1.584 1.958-3.182A3.937 3.937 0 0 0 12 6zm-1 10h2v2h-2z"></path><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path></svg>
                    </a>
                </div>
                <div class="header__icon container__notifications">
                    <div class="notifications__icon" onclick="toggleNotifications()" data-hint=" Confira suas notificações" data-hint-position="right">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19 13.586V10c0-3.217-2.185-5.927-5.145-6.742C13.562 2.52 12.846 2 12 2s-1.562.52-1.855 1.258C7.185 4.074 5 6.783 5 10v3.586l-1.707 1.707A.996.996 0 0 0 3 16v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L19 13.586zM19 17H5v-.586l1.707-1.707A.996.996 0 0 0 7 14v-4c0-2.757 2.243-5 5-5s5 2.243 5 5v4c0 .266.105.52.293.707L19 16.414V17zm-7 5a2.98 2.98 0 0 0 2.818-2H9.182A2.98 2.98 0 0 0 12 22z"></path></svg>
                        <span class="number">1<?php //echo $row_n['num_result']; ?></span>
                    </div>
                    <div class="notifications" id="notificationsWrap">
                        <ul>
                            <?php
                                // $sql = "SELECT * FROM `tb_notifications` WHERE visibility LIKE $_SESSION[cargo] or visibility LIKE 2 ORDER BY id DESC";

                                // $network = $conn->query($sql);

                                // while($data = mysqli_fetch_assoc($network))
                                // {
                                //     $visibility = $data['visibility'];
                                //     if ($visibility == 0)
                                //     {
                                //         $visibility_value = 'Pessoas';
                                //     }
                                //     else if ($visibility == 1)
                                //     {
                                //         $visibility_value = 'Empresas';
                                //     }
                                //     else if ($visibility == 2)
                                //     {
                                //         $visibility_value = 'Global';
                                //     }

                                //     $dias_aberto_entrada = new DateTime($data['creation_date']);
                                //     $dias_aberto_saida = new DateTime();
    
                                //     $dias_aberto_intervalo = $dias_aberto_entrada->diff($dias_aberto_saida);
                                //     $time_calc_days = $dias_aberto_intervalo->days;
                                //     $time_calc_week = $time_calc_days/7;
                                //     $time_calc_month = $time_calc_days/30;

                                //     if ($time_calc_days == 1)
                                //     {
                                //         $time_calc = "há 1 dia";
                                //     }
                                //     else if ($time_calc_days < 7)
                                //     {
                                //         $time_calc = "há ".$time_calc_week." dias";
                                //     }
                                //     else if ($time_calc_days >= 7)
                                //     {
                                //         $time_calc = "há 1 semana";
                                //     }
                                //     else if ($time_calc_days > 14)
                                //     {
                                //         $time_calc = "há ".$time_calc_week." semanas";
                                //     }
                                //     else if ($time_calc_month == 1)
                                //     {
                                //         $time_calc = "há 1 mês";
                                //     }
                                //     else if ($time_calc_month > 1)
                                //     {
                                //         $time_calc = "há ".$time_calc_month." meses";
                                //     }
                                    
                                //     echo '<li class="new">';
                                //     echo '<div class="container__title">';
                                //     echo '<p class="title">'.$data['name'].'</p>';
                                //     echo '<span class="date">'.$time_calc.'</span>';
                                //     echo '</div>';
                                //     echo '<span class="description">'.$data['description'].'</span>';
                                //     echo '<a href="'.$data['url'].'" class="url-link">Clique aqui!</a>';
                                //     echo '</li>';
                                //     echo '<div class="line"></div>';
                                // }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="container__user">
                    <div class="user__info" onclick="toggleUser()">
                        <img id="imagemUsuario" alt="Imagem de Perfil">
                        <div class="info">
                            <p><?php echo $name; ?></p>
                            <span><?php echo $loja; ?></span>
                        </div>
                        <i class='bx bx-chevron-down' ></i>
                    </div>
                    <div class="user" id="userWrap">
                        <div class="modal__user__info">
                            <h5 class="fs-5 mb-0"><?php echo $name; ?></h5>
                            <p class="small"><?php echo $email; ?></p>
                        </div>
                        <div class="theme__dark small">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M20.742 13.045a8.088 8.088 0 0 1-2.077.271c-2.135 0-4.14-.83-5.646-2.336a8.025 8.025 0 0 1-2.064-7.723A1 1 0 0 0 9.73 2.034a10.014 10.014 0 0 0-4.489 2.582c-3.898 3.898-3.898 10.243 0 14.143a9.937 9.937 0 0 0 7.072 2.93 9.93 9.93 0 0 0 7.07-2.929 10.007 10.007 0 0 0 2.583-4.491 1.001 1.001 0 0 0-1.224-1.224zm-2.772 4.301a7.947 7.947 0 0 1-5.656 2.343 7.953 7.953 0 0 1-5.658-2.344c-3.118-3.119-3.118-8.195 0-11.314a7.923 7.923 0 0 1 2.06-1.483 10.027 10.027 0 0 0 2.89 7.848 9.972 9.972 0 0 0 7.848 2.891 8.036 8.036 0 0 1-1.484 2.059z"></path></svg>
                            <p>Tema Escuro</p>
                        </div>
                        <div class="shop">
                            <h5 class="fs-5 mb-1">Loja</h5>
                            <?php
                                // Tabela que será feita a consulta
                                $tabela = "tb_shop";

                                // Consulta SQL
                                $sql = "SELECT id, name FROM $tabela WHERE user_id = :user_id ORDER BY (id = :shop_id) DESC";

                                // Preparar a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Vincular o valor do parâmetro
                                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);

                                // Executar a consulta
                                $stmt->execute();

                                // Contar o numero de contas
                                $countShops = $stmt->rowCount();

                                // Obter todos os resultados como um array associativo
                                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <ul class="mb-0">
                                <?php foreach ($resultados as $loja): ?>
                                    <li class="small <?php echo $loja['id'] == $id ? 'active' : ''; ?>">
                                        <a href="<?= INCLUDE_PATH_DASHBOARD; ?>alterar-loja?id=<?= $loja['id']; ?>" class="text-body text-decoration-none">
                                            <?php echo $loja['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="improvement small" data-bs-toggle="offcanvas" data-bs-target="#improvementOffcanvas" aria-controls="improvementOffcanvasExample">
                            <i class='bx bx-bulb'></i>
                            <p>Enviar uma Melhoria</p>
                        </div>
                        <div class="account">
                            <h5 class="fs-5 mb-1">Minha Conta</h5>
                            <ul class="mb-0">
                                <li class="small">
                                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">
                                        Editar Conta
                                    </a>
                                </li>
                                <?php
                                    if ($countShops < 5) {
                                        $href = "href='" . INCLUDE_PATH_DASHBOARD . "criar-nova-loja'";
                                        $css = "";
                                        $i = "";
                                    } else {
                                        $href = "";
                                        $css = 'class="text-body-secondary text-decoration-none"';
                                        $i = '<i class="bx bx-help-circle" data-toggle="tooltip" data-placement="top" aria-label="Sua conta chegou no limite máximo de sites" data-bs-original-title="Sua conta chegou no limite máximo de sites"></i>';
                                    }
                                ?>
                                <li class="small">
                                    <a <?= $href; ?> <?= $css; ?>>
                                        Criar Site <?= $i; ?>
                                    </a>
                                </li>
                                <li class="small">
                                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">
                                        Sair
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                    if(isset($_SESSION['admin_id'])){
                ?>
                    <div class="back">
                        <a class="back-link btn btn-success rounded-1 fw-semibold px-4 py-2 small d-flex align-items-center" href="<?php echo INCLUDE_PATH_DASHBOARD . "back-end/admin/return.php" ?>">
                            <span class="me-1">Voltar</span>
                            <i class='bx bx-log-in fs-5' ></i>
                        </a>
                    </div>
                <?php
                    }
                ?>
            </div>
        </nav>
    </header>

    <style>
        .sidebar
        {
            width: 266px !important;
            background: none !important;
        }
        .sidebar.close
        {
            width: 266px !important;
            border-right: none !important;
        }
        .sidebar .nav-links
        {
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .sidebar.close .nav-links::before
        {
            content: "";
            position: absolute;
            left: 77px;
            height: 100%;
            width: 1px;
            background: var(--border-color);
        }

        .sidebar.close .nav-links li
        {
            width: min-content;
        }
        .sidebar.close .nav-links li .iocn-link
        {
            width: 78px !important;
            border-right: 1px solid var(--border-color) !important;
        }
        .sidebar.close .nav-links li .sub-menu
        {
            border-left: none !important;
        }

        .sidebar .sidebar_bottom
        {
            transition: none !important;
        }

        .offcanvas-filter
        {
            left: 266px !important;
        }
        .offcanvas-backdrop.fade.show
        {
            left: 266px !important;
        }
    </style>

    <nav class="sidebar close">
        <ul class="nav-links">
            <li class="<?php activeSidebarLink(''); ?> <?php activeSidebarLink('painel'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>">
                            <i class='bx bx-grid-alt' ></i>
                        </a>
                        <span class="link_name">Dashboard</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>">Dashboard</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('produtos'); ?> <?php activeSidebarLink('criar-produto'); ?> <?php activeSidebarLink('editar-produto'); ?> <?php activeSidebarLink('categorias'); ?> <?php activeSidebarLink('criar-categoria'); ?> <?php showSidebarLinks('produtos'); ?> <?php showSidebarLinks('criar-produto'); ?> <?php showSidebarLinks('editar-produto'); ?> <?php showSidebarLinks('categorias'); ?> <?php showSidebarLinks('criar-categoria'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="sidebar_link">
                                <i class='bx bx-package' ></i>
                            </a>
                            <span class="link_name">Produtos</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos">Produtos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="<?php activeSidebarLink('produtos'); ?> <?php activeSidebarLink('editar-produto'); ?>">Listar Produtos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto" class="<?php activeSidebarLink('criar-produto'); ?>" style="border-bottom: 1px solid #c4c4c4;">+ Criar Produto</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>categorias" class="<?php activeSidebarLink('categorias'); ?> <?php activeSidebarLink('criar-categoria'); ?>">Categorias</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('criar-banner'); ?> <?php activeSidebarLink('editar-banner'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php activeSidebarLink('video-youtube'); ?> <?php activeSidebarLink('tarja'); ?> <?php activeSidebarLink('codigos-html'); ?> <?php activeSidebarLink('incluir-codigo-html'); ?> <?php activeSidebarLink('editar-codigo-html'); ?> <?php activeSidebarLink('paginas'); ?> <?php activeSidebarLink('criar-pagina'); ?> <?php activeSidebarLink('editar-pagina'); ?>
                    <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('criar-banner'); ?> <?php showSidebarLinks('editar-banner'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?> <?php showSidebarLinks('video-youtube'); ?> <?php showSidebarLinks('tarja'); ?> <?php showSidebarLinks('codigos-html'); ?> <?php showSidebarLinks('incluir-codigo-html'); ?> <?php showSidebarLinks('editar-codigo-html'); ?> <?php showSidebarLinks('paginas'); ?> <?php showSidebarLinks('criar-pagina'); ?> <?php showSidebarLinks('editar-pagina'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners" class="sidebar_link">
                                <i class='bx bx-layout'></i>
                            </a>
                            <span class="link_name">Personalizar</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners">Personalizar</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners" class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('criar-banner'); ?> <?php activeSidebarLink('editar-banner'); ?>">Banners</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>logo" class="<?php activeSidebarLink('logo'); ?>">Logo</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>feed-instagram" class="<?php activeSidebarLink('feed-instagram'); ?>">Feed Instagram</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>video-youtube" class="<?php activeSidebarLink('video-youtube'); ?>">Vídeo do Youtube</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>tarja" class="<?php activeSidebarLink('tarja'); ?>">Tarja Superior / Central</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>codigos-html" class="<?php activeSidebarLink('codigos-html'); ?> <?php activeSidebarLink('incluir-codigo-html'); ?> <?php activeSidebarLink('editar-codigo-html'); ?>">Incluir código HTML</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="<?php activeSidebarLink('paginas'); ?> <?php activeSidebarLink('criar-pagina'); ?> <?php activeSidebarLink('editar-pagina'); ?>">Incluir pág. conteúdo</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('planos'); ?> <?php activeSidebarLink('assinar-plano'); ?> <?php activeSidebarLink('dados-para-pagamento'); ?> <?php activeSidebarLink('pagamento'); ?> <?php activeSidebarLink('historico-de-faturas'); ?> <?php showSidebarLinks('planos'); ?> <?php showSidebarLinks('assinar-plano'); ?> <?php showSidebarLinks('dados-para-pagamento'); ?> <?php showSidebarLinks('pagamento'); ?> <?php showSidebarLinks('historico-de-faturas'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="sidebar_link">
                                <i class='bx bx-wallet' ></i>
                            </a>
                            <span class="link_name">Financeiro</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos">Financeiro</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="<?php activeSidebarLink('planos'); ?> <?php activeSidebarLink('assinar-plano'); ?> <?php activeSidebarLink('pagamento'); ?>">Planos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dados-para-pagamento" class="<?php activeSidebarLink('dados-para-pagamento'); ?>">Dados para pagamento</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>historico-de-faturas" class="<?php activeSidebarLink('historico-de-faturas'); ?>">Histórico de Faturas</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('depoimentos'); ?> <?php activeSidebarLink('criar-depoimento'); ?> <?php activeSidebarLink('editar-depoimento'); ?> <?php showSidebarLinks('depoimentos'); ?> <?php showSidebarLinks('criar-depoimento'); ?> <?php showSidebarLinks('editar-depoimento'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>depoimentos" class="sidebar_link">
                                <i class='bx bx-message-alt-detail' ></i>
                            </a>
                            <span class="link_name">Depoimentos</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>depoimentos">Depoimentos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>depoimentos" class="<?php activeSidebarLink('depoimentos'); ?> <?php activeSidebarLink('editar-depoimento'); ?>">Depoimento</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-depoimento" class="<?php activeSidebarLink('criar-depoimento'); ?>">Criar Depoimento</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('configurar-dominio'); ?> <?php activeSidebarLink('email-profissional'); ?> <?php showSidebarLinks('configurar-dominio'); ?> <?php showSidebarLinks('email-profissional'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configurar-dominio" class="sidebar_link">
                                <i class='bx bx-globe' ></i>
                            </a>
                            <span class="link_name">Domínio</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configurar-dominio">Domínio</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configurar-dominio" class="<?php activeSidebarLink('configurar-dominio'); ?>">Configurar</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>email-profissional" class="<?php activeSidebarLink('email-profissional'); ?>">Conf. E-mail profissional</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('atendimento'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>atendimento">
                            <i class='bx bx-conversation' ></i>
                        </a>
                        <span class="link_name">Atendimento</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>atendimento">Atendimento</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('indique-e-ganhe'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>indique-e-ganhe">
                            <i class='bx bx-dollar-circle' ></i>
                        </a>
                        <span class="link_name">Indique e Ganhe</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>indique-e-ganhe">Indique e Ganhe</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('redes-sociais'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais">
                            <i class='bx bx-user-circle' ></i>
                        </a>
                        <span class="link_name">Redes Sociais</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais">Redes Sociais</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('sites-prontos'); ?> <?php activeSidebarLink('site-pronto'); ?> <?php activeSidebarLink('servicos'); ?> <?php activeSidebarLink('servico'); ?> <?php showSidebarLinks('sites-prontos'); ?> <?php showSidebarLinks('site-pronto'); ?> <?php showSidebarLinks('servicos'); ?> <?php showSidebarLinks('servico'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="sidebar_link">
                                <i class='bx bx-bulb' ></i>
                            </a>
                            <span class="link_name">Soluções</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos">Soluções</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="<?php activeSidebarLink('servicos'); ?> <?php activeSidebarLink('servico'); ?>">Serviços</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sites-prontos" class="<?php activeSidebarLink('sites-prontos'); ?> <?php activeSidebarLink('site-pronto'); ?>">Sites Prontos</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('artigos'); ?> <?php activeSidebarLink('criar-artigo'); ?> <?php activeSidebarLink('editar-artigo'); ?> <?php showSidebarLinks('artigos'); ?> <?php showSidebarLinks('criar-artigo'); ?> <?php showSidebarLinks('editar-artigo'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="sidebar_link">
                                <i class='bx bx-desktop' ></i>
                            </a>
                            <span class="link_name">Blog</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos">Blog</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="<?php activeSidebarLink('artigos'); ?> <?php activeSidebarLink('editar-artigo'); ?>">Listar Artigos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-artigo" class="<?php activeSidebarLink('criar-artigo'); ?>">+ Criar Artigo</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('grupo-whatsapp'); ?> <?php showSidebarLinks('grupo-whatsapp'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>grupo-whatsapp" class="sidebar_link">
                                <i class='bx bxl-whatsapp' ></i>
                            </a>
                            <span class="link_name">Grupo WhatsApp</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>grupo-whatsapp">Grupo WhatsApp</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('influenciadores'); ?>">
                <div class="iocn-link">
                    <p>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>influenciadores">
                            <i class='bx bx-group'></i>
                        </a>
                        <span class="link_name">Influenciadores</span>
                    </p>
                </div>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>influenciadores">Influenciadores</a></li>
                </ul>
            </li>
            <div class="sidebar_bottom">
                <li>
                    <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto">
                                <i class='bx bx-plus' ></i>
                                <span class="link_name">Criar produto</span>
                            </a>
                        </p>
                    </div>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto">Criar produto</a></li>
                    </ul>
                </li>
                <div class="line"></div>
                <li>
                    <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">
                                <i class='bx bx-cog' ></i>
                            </a>
                            <span class="link_name">Configurações</span>
                        </p>
                    </div>
                    <ul class="sub-menu blank">
                        <li><a class="link_name ms-0" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">Configurações</a></li>
                    </ul>
                </li>
                <li>
                    <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">
                                <i class='bx bx-log-out' ></i>
                            </a>
                            <span class="link_name">Sair</span>
                        </p>
                    </div>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>sair">Sair</a></li>
                    </ul>
                </li>
			</div>
		</ul>
    </nav>

    <style>
        @media screen and (max-width: 768px) {
            .mobile-nav
            {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                height: 3.5rem;
                display: flex;
                align-items: center;
                justify-content: space-around;
                border: 1px solid var(--border-color);
                background: var(--card-color);
                z-index: 9;
            }
            .mobile-nav .mobile-itens
            {
                font-size: 1.5rem;
                color: black;
                text-decoration: none;
            }
            .mobile-nav .create-shop
            {
                width: 45px;
                height: 45px;
                color: white;
                background: var(--green-color);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                bottom: 25px;
            }
        }
    </style>

    <div class="mobile-nav">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="mobile-itens">
            <i class='bx bx-grid-alt' ></i>
        </a>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="mobile-itens">
            <i class='bx bx-package' ></i>
        </a>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto" class="mobile-itens create-shop">
            <i class='bx bx-plus'></i>
        </a>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda" class="mobile-itens">
            <i class='bx bx-help-circle' ></i>
        </a>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes" class="mobile-itens">
            <i class='bx bx-cog' ></i>
        </a>
    </div>

    <?php
        }
    ?>

    <main class="main <?php echo ($url == "login" || $url == 'dois-fatores' || $url == 'recuperar-senha' || $url == 'atualizar-senha' || $url == 'assinar' || $url == 'criar-loja' || $url == "404") ? 'box' : ''; ?>">

        <div class="container grid">

        <div class="offcanvas offcanvas-start offcanvas-filter" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Filtar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="dropdown">
        <div class="fw-semibold bg-transparent">Preço</div>
        <div class="row">
            <div class="col-md-6">
                <label for="moneyInput1" class="form-label small">Min.</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control text-end" id="moneyInput1" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-6">
                <label for="moneyInput2" class="form-label small">Máx.</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control text-end" id="moneyInput2" placeholder="0,00">
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>

<!-- Modal de Histórico de Melhorias Enviadas -->
<div class="modal fade" id="improvementHistoryModal" tabindex="-1" role="dialog" aria-labelledby="improvementHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Histórico de melhorias
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="improvementHistoryTable">
                        <thead>
                            <tr>
                                <th>Melhoria</th>
                                <th>Situação</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Nome da tabela para a busca
                                $tabela = 'tb_improvement';

                                // Preparar a consulta com base na pesquisa (se houver)
                                $sql = "SELECT * FROM $tabela WHERE author = :author";

                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindParam(':author', $user_id);
                                $stmt->execute();

                                // Recuperar os resultados
                                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Loop através dos resultados e exibir todas as colunas
                                if ($resultados) {
                                    foreach ($resultados as $improvement) {
                                        if ($improvement['status'] == 2) {
                                            $improvement['status'] = "Finalizado";
                                        } elseif ($improvement['status'] == 1) {
                                            $improvement['status'] = "Em desenvolvimento";
                                        } elseif ($improvement['status'] == 0) {
                                            $improvement['status'] = "Em análise";
                                        } else {
                                            $improvement['status'] = "Recusado";
                                        }

                                        $formattedDateCreate = DateTime::createFromFormat('Y-m-d H:i:s', $improvement['date_create']);
                                        $improvement['date_create'] = $formattedDateCreate->format('d/m/Y H:i');
                            ?>
                                <tr>
                                    <td class='w-100'><?= $improvement['title']; ?></td>
                                    <td><?= $improvement['status']; ?></td>
                                    <td><?= $improvement['date_create']; ?></td>
                                </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-end fw-semibold px-4">
                <button type="button" class="btn btn-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Denunciar melhoria -->
<div class="modal fade" id="reportImprovement" tabindex="-1" aria-labelledby="reportImprovementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reportImprovementForm" method="post">
                <div class="modal-header px-4 pb-3 pt-4 border-0">
                    <h6 class="modal-title fs-6" id="reportImprovementModalLabel">Denunciar Melhoria</h6>
                </div>
                <div class="modal-body px-4 pb-3 pt-0">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report" value="already_solved" id="already_solved">
                        <label class="form-check-label" for="already_solved">
                            Já Resolvido
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report" value="inappropriate_content" id="inappropriate_content">
                        <label class="form-check-label" for="inappropriate_content">
                            Conteúdo Impróprio
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report" value="spam" id="spam">
                        <label class="form-check-label" for="spam">
                            Spam
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report" value="incorrect_information" id="incorrect_information">
                        <label class="form-check-label" for="incorrect_information">
                            Informação Incorreta
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="report" value="other" id="other">
                        <label class="form-check-label" for="other">
                            Outro
                        </label>
                    </div>
                    <div class="form-group mt-2" id="other_description_div" style="display: none;">
                        <input type="text" class="form-control" name="other_description" id="other_description" maxlength="150" placeholder="Descreva a denúncia">
                    </div>
                </div>
                <input type="hidden" name="improvement_id" id="improvement_id">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small">Enviar Denúncia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-image {
        position: relative;
        width: 100px;
        height: 100px;
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
    }

    .preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-image button {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 30px;
        height: 30px;
        border: 1px solid #c4c4c4;
        border-radius: 0.3rem;
        background: #f9f9f9;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: .3s;
    }

    .preview-image button::before {
        -webkit-text-stroke: 2px #f4f6f8;
        align-items: center;
        border-radius: 8px;
        color: #666;
        content: "\f00d";
        display: flex;
        font-family: Font Awesome\ 5 Free;
        font-size: 22px;
        font-weight: 700;
        height: 32px;
        justify-content: center;
    }
</style>

<!-- Enviar melhoria -->
<div class="modal fade" id="sendImprovement" tabindex="-1" aria-labelledby="sendImprovementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="improvementForm" method="post" enctype="multipart/form-data">
                <div class="modal-header px-4 pb-3 pt-4 border-0">
                    <h6 class="modal-title fs-6" id="sendImprovementModalLabel">Enviar Melhoria</h6>
                </div>
                <div class="modal-body px-4 pb-3 pt-0">
                    <div class="alert-container" id="error-improvement"></div>
                    <div class="mb-3">
                        <label for="title" class="form-label small">Título *</label>
                        <input type="text" class="form-control" name="title" id="title" aria-describedby="titleHelp" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="description" class="form-label small">Descrição da melhoria *</label>
                            <small id="descriptionCounter" class="form-text text-muted">0 de 1000 caracteres</small>
                        </div>
                        <textarea class="form-control mb-2" name="description" id="description" maxlength="1000" rows="3" required></textarea>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="images" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold d-flex align-items-center justify-content-center px-3 py-1 small">
                                    <i class='bx bx-paperclip me-2' ></i>
                                    Anexar
                                </label>
                                <input type="file" class="d-none" id="images" name="images[]" multiple accept="image/*">
                            </div>
                            <div class="col-md-10 d-flex justify-content-end">
                                <div id="preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="form-label small">Tags <span class="text-secondary small">(Máx. 3)</span></label>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="suggestion" id="suggestion">
                                <label class="form-check-label" for="suggestion">Sugestão</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="development" id="development">
                                <label class="form-check-label" for="development">Desenvolvimento</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="error" id="error">
                                <label class="form-check-label" for="error">Erro</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="404" id="404">
                                <label class="form-check-label" for="404">404</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="modify" id="modify">
                                <label class="form-check-label" for="modify">Modificar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input checkbox-limit" type="checkbox" name="tags[]" value="integration" id="integration">
                                <label class="form-check-label" for="integration">Integração</label>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small">Enviar Melhoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Description Counter -->
<script>
    $(document).ready(function() {
        $('#description').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));
            $('#descriptionCounter').text(currentLength + ' de ' + maxLength + ' caracteres');
        });
    });
</script>

<!-- Image Preview -->
<script>
    $(document).ready(function() {
        const dataTransfer = new DataTransfer();
        
        $('#images').on('change', handleFileSelect);

        function handleFileSelect(event) {
            const files = event.target.files;
            
            // Verifica se o total de arquivos é maior que 5
            if (dataTransfer.files.length + files.length > 5) {
                alert('Você pode enviar no máximo 5 imagens.');
                $(event.target).val(''); // Reseta o input de arquivos
                return;
            }

            Array.from(files).forEach((file) => {
                dataTransfer.items.add(file);
            });

            updatePreview();
            updateInputFiles();
        }

        function updatePreview() {
            const $previewContainer = $('#preview');
            $previewContainer.empty(); // Limpa o preview antes de adicionar novos arquivos

            Array.from(dataTransfer.files).forEach((file) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const $previewImage = $('<div class="preview-image"></div>');
                    const $img = $('<img>').attr('src', e.target.result);
                    const $removeBtn = $('<button class="remove-btn"></button>');

                    $removeBtn.on('click', function() {
                        removeFile(file);
                    });

                    $previewImage.append($img).append($removeBtn);
                    $previewContainer.append($previewImage);
                };
                
                reader.readAsDataURL(file);
            });
        }

        function updateInputFiles() {
            const $input = $('#images');
            $input[0].files = dataTransfer.files;
        }

        function removeFile(fileToRemove) {
            const items = Array.from(dataTransfer.items);
            dataTransfer.items.clear();
            
            items.forEach((item) => {
                if (item.getAsFile() !== fileToRemove) {
                    dataTransfer.items.add(item.getAsFile());
                }
            });

            updatePreview();
            updateInputFiles();
        }
    });
</script>

<!-- Checkbox max. 3 itens -->
<script>
    $('input.checkbox-limit').on('change', function(evt) {
        if ($('input.checkbox-limit:checked').length > 3) {
            this.checked = false;
        }
    });
</script>

<!-- Form Ajax -->
<script>
    $(document).ready(function() {
        // AJAX form submission
        $('#improvementForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            
            $.ajax({
                url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/add_improvement.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        // Exibir a mensagem de erro
                        var errorMessage = '<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">'
                                            + res.message +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button>' +
                                            '</div>';
                        $('#error-improvement').html(errorMessage);
                    }
                },
                error: function() {
                    // Exibir a mensagem de erro
                    var errorMessage = '<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">'
                                        + res.message +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button>' +
                                        '</div>';
                    $('#error-improvement').html(errorMessage);
                }
            });
        });
    });
</script>

<style>
    #improvementOffcanvas .dropdown-toggle::after
    {
        display: none;
    }

    #improvementOffcanvas .dropdown-item.active,
    #improvementOffcanvas .dropdown-item:active
    {
        color: #212529 !important;
        background-color: #f8f9fa !important;
    }

    #improvementOffcanvas .nav.nav-tabs button
    {
        border: none;
        color: #6c757d !important;
        background-color: transparent;
    }

    #improvementOffcanvas .nav.nav-tabs button:hover,
    #improvementOffcanvas .nav.nav-tabs button.active
    {
        color: inherit !important;
    }

    #improvementOffcanvas .nav.nav-tabs button.active
    {
        position: relative;
        font-weight: 500;
    }

    #improvementOffcanvas .nav.nav-tabs button.active::before
    {
        content: "";
        width: 100%;
        height: 2px;
        background: #000;
        position: absolute;
        left: 0;
        bottom: 0;
    }
</style>

<!-- Offcanvas enviar melhorias -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="improvementOffcanvas" aria-labelledby="improvementOffcanvasLabel">
    <div class="offcanvas-header bg-success-subtle p-4">
        <h5 class="offcanvas-title" id="improvementOffcanvasLabel">Dropi Digital</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <nav class="bg-success-subtle border-bottom border-success-subtle px-4">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="improvement-tab" data-bs-toggle="tab" data-bs-target="#improvement" type="button" role="tab" aria-controls="improvement" aria-selected="true">Sugestões de Melhorias</button>
            <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab" aria-controls="news" aria-selected="false" disabled>Novidades</button>
        </div>
    </nav>
    <div class="offcanvas-body p-0">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="improvement" role="tabpanel" aria-labelledby="improvement-tab" tabindex="0">
                <div class="d-flex justify-content-end px-3 py-2">
                    <button type="button" class="btn btn-secondary d-flex align-items-center justify-content-center me-2" style="width: 34px;" data-bs-toggle="modal" data-bs-target="#improvementHistoryModal" data-toggle="tooltip" data-placement="top" title="Minhas Sugestões"><i class='bx bx-history'></i></button>
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-3 py-1 small" data-bs-toggle="modal" data-bs-target="#sendImprovement">+ Adicionar Melhoria</button>
                </div>
                <?php
                    // Formatar nome
                    function formatName($fullName) {
                        // Divide o nome completo em partes
                        $nameParts = explode(' ', $fullName);

                        // Se houver mais de um nome (nome e sobrenome)
                        if (count($nameParts) > 1) {
                            // Obter o primeiro nome
                            $firstName = $nameParts[0];
                            
                            // Obter o primeiro sobrenome
                            $lastName = $nameParts[1];

                            // Formatar o nome
                            return $firstName . ' ' . strtoupper($lastName[0]) . '.';
                        }

                        // Se não houver sobrenome, apenas retorne o nome
                        return $fullName;
                    }

                    // Limit text
                    function limitarPalavras($texto, $limite) {
                        // Quebrar o texto em palavras
                        $palavras = explode(' ', $texto);
                    
                        // Contar o número de palavras
                        $numPalavras = count($palavras);
                    
                        // Se o número de palavras for maior que o limite, cortar o texto
                        if ($numPalavras > $limite) {
                            // Pegar apenas as palavras até o limite
                            $palavras = array_slice($palavras, 0, $limite);
                    
                            // Juntar as palavras de volta em um texto
                            $texto = implode(' ', $palavras) . '...'; // Adicionar reticências ou outro indicativo de truncamento
                        }
                    
                        return $texto;
                    }

                    // Nome da tabela para a busca
                    $tabelaImprovement = 'tb_improvement';
                    $tabelaLikes = 'tb_improvement_likes';

                    // Preparar a consulta com base na pesquisa (se houver)
                    $sql = "SELECT i.*, COUNT(l.improvement_id) AS total_likes
                            FROM $tabelaImprovement i
                            LEFT JOIN $tabelaLikes l ON i.id = l.improvement_id
                            WHERE i.status = 1
                            GROUP BY i.id
                            ORDER BY total_likes DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->execute();

                    // Recuperar os resultados
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    if ($resultados) {
                        foreach ($resultados as $improvement) {
                            // Recuperar nome do autor
                            $sql_author = "SELECT name FROM tb_users WHERE id = :id";
                            $stmt_author = $conn_pdo->prepare($sql_author);
                            $stmt_author->bindParam(':id', $improvement['author']);
                            $stmt_author->execute();
                            $improvement['author'] = formatName($stmt_author->fetch(PDO::FETCH_ASSOC)['name']);

                            // Formatação da data
                            $meses = [
                                1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
                                5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
                                9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
                            ];
                            $data = new DateTime($improvement['date_create']);
                            $dia = $data->format('d');
                            $mes = (int)$data->format('m');
                            $ano = $data->format('Y');
                            $mesPortugues = $meses[$mes];
                            $improvement['date_create'] = sprintf('%d de %s de %d', $dia, $mesPortugues, $ano);

                            // Tags
                            // Array com os textos das tags em português
                            $tagTranslations = array(
                                'suggestion' => 'Sugestão',
                                'development' => 'Desenvolvimento',
                                'error' => 'Erro',
                                '404' => '404',
                                'modify' => 'Modificar',
                                'integration' => 'Integração'
                            );

                            // Array com os dados do CSS para cada tipo de tag
                            $tagColors = array(
                                'suggestion' => 'text-warning-emphasis bg-warning-subtle border border-warning-subtle',
                                'development' => 'text-danger-emphasis bg-danger-subtle border border-danger-subtle',
                                'error' => 'text-primary-emphasis bg-primary-subtle border border-primary-subtle',
                                '404' => 'text-success-emphasis bg-success-subtle border border-success-subtle',
                                'modify' => 'text-secondary-emphasis bg-secondary-subtle border border-secondary-subtle',
                                'integration' => 'text-info-emphasis bg-info-subtle border border-info-subtle'
                            );

                            // Decodificar o JSON para obter um array de tags
                            $tagsArray = json_decode($improvement['tags']);

                            // Limit text
                            $improvement['description'] = strlen($improvement['description']) > 125 ? substr($improvement['description'], 0, 125) . '...' : $improvement['description'];
                ?>
                    <div class="improvement border-bottom px-4 py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5>#<?= $improvement['id']; ?> <?= $improvement['title']; ?></h5>
                            <div class="dropdown">
                                <button class="btn btn-outline-light border border-secondary-subtle text-secondary d-flex align-items-center justify-content-center dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 30px;">
                                    <i class='bx bx-dots-vertical-rounded'></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#reportImprovement" data-improvement="<?= $improvement['id']; ?>">
                                            <i class='bx bx-flag'></i>
                                            Denunciar
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tags d-table mb-2">
                            <?php
                                if ($tagsArray) {
                                    // Exibir as tags com seus textos em português
                                    foreach ($tagsArray as $tag) {
                                        if (isset($tagTranslations[$tag])) {
                                            $tagName = $tagTranslations[$tag];
                                            $tagClass = isset($tagColors[$tag]) ? $tagColors[$tag] : '';

                                            // Exibir a tag usando as classes CSS correspondentes
                                            echo '<small class="d-inline-flex me-1 px-2 py-0 fw-semibold ' . $tagClass . ' rounded-1">' . $tagName . '</small>';
                                        }
                                    }
                                }
                            ?>
                        </div>
                        <p class="text-secondary mb-2"><?= $improvement['description']; ?></p>
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="small"><span class="fw-semibold">Por <?= $improvement['author']; ?></span> • <?= $improvement['date_create']; ?></p>
                            <button type="button" class="like-btn btn btn-outline-light border border-secondary-subtle text-secondary d-flex align-items-center justify-content-center px-2 py-0" data-id="<?= $improvement['id']; ?>" data-value="<?= $improvement['total_likes']; ?>">
                                <i class='bx <?= ($improvement['total_likes'] > 0) ? "bxs-like" : "bx-like"; ?> me-2'></i>
                                <p class="number-likes small fw-semibold"><?= $improvement['total_likes']; ?></p>
                            </button>
                        </div>
                    </div>
                <?php
                        }
                    } else {
                ?>
                    <div class="px-4 py-3">
                        <p class="text-center mb-2">Nenhuma melhoria cadastrada até agora, seja o primeiro!</p>
                    </div>
                <?php
                    }
                ?>
            </div>
            <div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="news-tab" tabindex="0">
                Novidades
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".like-btn").click(function() {
            var improvementId = $(this).data("id");
            var improvementValue = $(this).data("value");
            var shopId = <?php echo $shop_id; ?>;

            // Armazenar a referência do elemento em uma variável para uso dentro do AJAX
            var button = $(this);

            $.ajax({
                url: "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/improvement_like.php",
                method: "POST",
                data: {
                    improvement_id: improvementId,
                    shop_id: shopId
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        // Alternar entre os ícones de like e atualizar o contador de likes
                        button.find("i").toggleClass("bx-like bxs-like");
                        var totalLikes = improvementValue + 1;
                        button.data("value", totalLikes);
                        button.find(".number-likes").text(totalLikes);
                    } else if (response.status === "removed") {
                        // Alternar entre os ícones de like e atualizar o contador de likes
                        button.find("i").toggleClass("bx-like bxs-like");
                        var totalLikes = improvementValue - 1;
                        button.data("value", totalLikes);
                        button.find(".number-likes").text(totalLikes);
                    } else {
                        alert("Erro ao curtir a melhoria.");
                    }
                },
                error: function() {
                    alert("Erro na solicitação AJAX.");
                }
            });
        });
    });
</script>

<script>
    // Script para capturar o ID da melhoria e passá-lo para o campo oculto no modal
    document.addEventListener('DOMContentLoaded', function() {
        $('#reportImprovement').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Botão que acionou o modal
            var improvementId = button.data('improvement'); // Extrair o valor do atributo data-improvement
            var modal = $(this);
            modal.find('#improvement_id').val(improvementId); // Definir o valor do campo oculto no modal

            // Verificar o estado do radio "other" ao abrir o modal
            var otherRadio = modal.find('#other');
            var otherDescriptionDiv = modal.find('#other_description_div');
            otherDescriptionDiv.hide().removeAttr('required');

            // Adicionar evento de clique nos rádios para mostrar/esconder o campo de descrição
            modal.find('input[name="report"]').on('change', function() {
                if (otherRadio.is(':checked')) {
                    otherDescriptionDiv.show().attr('required', true);
                } else {
                    otherDescriptionDiv.hide().removeAttr('required');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // AJAX form submission
        $('#reportImprovementForm').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            
            $.ajax({
                url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/report_improvement.php',
                type: 'POST',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === "success") {
                        location.reload();
                    } else {
                        // Exibir a mensagem de erro
                        var errorMessage = '<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">'
                                            + response.message +
                                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button>' +
                                            '</div>';
                        $('#error-improvement').html("errorMessage");
                    }
                },
                error: function() {
                    // Exibir a mensagem de erro
                    var errorMessage = '<div class="alert alert-danger alert-dismissible fade show py-2" role="alert">'
                                        + response.message +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button>' +
                                        '</div>';
                    $('#error-improvement').html("errorMessage");
                }
            });
        });
    });
</script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Ferramentas para E-mail Marketing</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3 px-4 py-3">
            <a href="#" target="_black" class="col-sm-4">
                <div class="card">
                    <img src="https://cdn.awsli.com.br/parceiros/bling.png" alt="Bling">
                </div>
            </a>
            <a href="#" target="_black" class="col-sm-4">
                <div class="card">
                    <img src="https://cdn.awsli.com.br/production/static-v2/painel/img/comparadores-de-preco/googlemerchant.png?v=fc240ec" alt="Google Merchant">
                </div>
            </a>
            <a href="https://mailchimp.com/pt-br/landers/email-marketing-platform/?ds_c=DEPT_AOC_Google_Search_BR_POR_Brand_Acquire_Exact_MKAG_T4&gclid=Cj0KCQjw06-oBhC6ARIsAGuzdw29513Q_IL4QCxW-5gIOEAIVMGx-gQ5IMyk08sRf00g1F_VuOUFfRQaAiV5EALw_wcB&gclsrc=aw.ds" target="_black" class="col-sm-4">
                <div class="card">
                    <img src="https://www.cdnlogo.com/logos/m/86/mailchimp.svg" alt="Mailchimp" style="height: 125.05px;">
                </div>
            </a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Chamada Para atualizar o perfil -->
<div class="modal fade" id="callToUpdProfileModal" tabindex="-1" role="dialog" aria-labelledby="callToUpdProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 pb-3 pt-4 border-0">
                <div class="fw-semibold py-2">Atualização de Perfil Necessária</div>
            </div>
            <div class="modal-body d-flex flex-column align-items-center justify-content-center px-4 py-3">
                <div><i class='bx bx-error fs-1'></i></div>
                <p class="fw-semibold">Você tem campos em branco.</p>
                <p class="small">Clique no botão abaixo para inserir os campos vazios.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" id="closeCallToUpdProfileModal" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes" class="btn btn-secondary fw-semibold px-4 py-2 small">Ir para Configurações</a>
            </div>
        </div>
    </div>
</div>

<?php
    if (isset($id) && $permissions == 0) {
        if (empty($_SESSION['close_upd_modal'])) {
            if (!isset($detailed_segment)) {
                echo "
                    <script>
                        $(document).ready(function() {
                            $('#callToUpdProfileModal').modal('show');
                        });
                    </script>
                ";
            }
        }
    }
?>

<script>
    $(document).ready(function () {
        function closeCallToUpdProfileModal() {
            $.ajax({
                url: "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/close_upd_modal.php",
                method: "POST",
                dataType: "json"
            });
        }
        
        $("#closeCallToUpdProfileModal").click(function () {
            closeCallToUpdProfileModal();
        });
    });
</script>

    <?php
        if (isset($_SESSION['admin_id'])) {
            // Tabela que sera feita a consulta
            $tabela = "tb_warning";

            // Consulta SQL
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND type = :type ORDER BY id DESC";

            // Preparar a consulta
            $stmt = $conn_pdo->prepare($sql);

            // Type 1 = "modal"
            $type = 1;

            // Vincular o valor do parâmetro
            $stmt->bindParam(':shop_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':type', $type, PDO::PARAM_INT);

            // Executar a consulta
            $stmt->execute();

            // Obter o resultado como um array associativo
            $warnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar se o resultado foi encontrado
            foreach ($warnings as $warning) {
    ?>
        <style>
            #warningModal .btn.btn-success
            {
                background: var(--green-color);
                border: none;
            }
        </style>

        <div class="modal fade" id="warningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="warningModalLabel">Aviso</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fs-6 fw-semibold"><?= $warning['title']; ?></p>
                        <span class="small"><?= nl2br(htmlspecialchars($warning['content'])); ?></span>
                    </div>
                    <div class="modal-footer fw-semibold px-4">
                        <button type="button" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                // Mostra o modal com id 'warningModal'
                $("#warningModal").modal('show');
            });
        </script>
    <?php
            }
        }
    ?>

    <?php
        if (isset($_SESSION['admin_id'])) {
            // Tabela que sera feita a consulta
            $tabela = "tb_warning";

            // Consulta SQL
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND type = :type ORDER BY id DESC";

            // Preparar a consulta
            $stmt = $conn_pdo->prepare($sql);

            // Type 2 = "Texto"
            $type = 2;

            // Vincular o valor do parâmetro
            $stmt->bindParam(':shop_id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':type', $type, PDO::PARAM_INT);

            // Executar a consulta
            $stmt->execute();

            // Obter o resultado como um array associativo
            $warnings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar se o resultado foi encontrado
            foreach ($warnings as $warning) {
                if ($warning['level'] == 1) {
                    $level = "info";
                } elseif ($warning['level'] == 2) {
                    $level = "warning";
                } else {
                    $level = "danger";
                }
    ?>
        <style>
            .bd-callout {
                --bs-link-color-rgb: var(--bd-callout-link);
                --bs-code-color: var(--bd-callout-code-color);
                padding: 1.25rem;
                color: var(--bd-callout-color, inherit);
                background-color: var(--bd-callout-bg, var(--bs-gray-100));
                border-left: 0.25rem solid var(--bd-callout-border, var(--bs-gray-300))
            }

            .bd-callout-info {
                --bd-callout-color: var(--bs-info-text-emphasis);
                --bd-callout-bg: var(--bs-info-bg-subtle);
                --bd-callout-border: var(--bs-info-border-subtle);
            }

            .bd-callout-warning {
                --bd-callout-color: var(--bs-warning-text-emphasis);
                --bd-callout-bg: var(--bs-warning-bg-subtle);
                --bd-callout-border: var(--bs-warning-border-subtle);
            }

            .bd-callout-danger {
                --bd-callout-color: var(--bs-danger-text-emphasis);
                --bd-callout-bg: var(--bs-danger-bg-subtle);
                --bd-callout-border: var(--bs-danger-border-subtle);
            }
        </style>

        <div class="bd-callout bd-callout-<?= $level; ?>">
            <p class="fs-6 fw-semibold"><?= $warning['title']; ?></p>
            <span class="small"><?= nl2br(htmlspecialchars($warning['content'])); ?></span>
        </div>
    <?php
            }
        }
    ?>

    <?php
        if(isset($_SESSION['admin_id'])){
    ?>
        <div class="card info-card">
            <div class="row mb-2">
                <div class="col-md-10 d-flex align-items-center">
                    <i class='bx bx-info-circle fs-5 me-2' ></i>
                    <p class="text">Voltar para o painel adminitrativo</p>
                </div>
                <button type="button" class="close col-md-2">
                    <i class='bx bx-x fs-4'></i>
                </button>
            </div>
            <div class="button p-0">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle rounded-1 text-secondary fw-semibold px-3 py-1 small me-2">Cancelar</button>
                <a class="btn btn-success rounded-1 fw-semibold px-3 py-1 small d-flex align-items-center" href="<?php echo INCLUDE_PATH_DASHBOARD . "back-end/admin/return.php" ?>">
                    Voltar
                    <i class='bx bx-log-in fs-5 ms-1' ></i>
                </a>
            </div>
        </div>
    <?php
        }
    ?>
    
        <?php
            // Iniciando variável $tab
            $tab = "";

            // Verifica se a url contém uma barra
            if (strpos($url, '/') !== false) {
                // Divide a url usando a barra como delimitador
                list($url, $tab) = explode('/', $url, 2);
            }

            if ($permissions == 1 || $permissions == 2) {
                // Administrador
                $permission = 'admin';
            } elseif ($permissions == 0) {
                // Usuario
                $permission = 'user';
            } else {
                // None
                $permission = 'login';
            }

            if ($url == "login" || $url == 'dois-fatores' || $url == 'recuperar-senha' || $url == 'atualizar-senha' || $url == 'assinar' || $url == 'criar-loja' || $url == "404" || $url == "sair") {
                if (file_exists('pages/login/' . $url . '.php')) {
                    include('pages/login/' . $url . '.php');
                } else {
                    // A página não existe
                    header('Location: ' . INCLUDE_PATH_DASHBOARD . '404');
                }
            } else {
                if (file_exists('pages/' . $permission . '/' . $url . '.php')) {
                    include('pages/' . $permission . '/' . $url . '.php');
                } else {
                    // A página não existe
                    header('Location: ' . INCLUDE_PATH_DASHBOARD . '404');
                }
            }
        ?>

        </div>
    </main>

    <div class='card__info'>
        <div class='info'>
            <?php
                if(isset($_SESSION['msg'])){
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                if(isset($_SESSION['msgcad'])){
                    echo $_SESSION['msgcad'];
                    unset($_SESSION['msgcad']);
                }
            ?>
        </div>
    </div>

    <div class="backdrop"></div>

    <style>
        .fixed-whatsapp-button
        {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 9999999999999;
            border: none;
        }
        .fixed-whatsapp-button .whatsapp-button svg {
            width: 80px;
            height: 80px;
        }
    </style>

    <div class="fixed-whatsapp-button <?= (!isset($shop_id)) ? "d-none" : ""; ?>">
        <a class="whatsapp-button" href="https://wa.me/11940496818" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="800" width="1200" viewBox="-93.2412 -156.2325 808.0904 937.395"><defs><linearGradient x1=".5" y1="0" x2=".5" y2="1" id="a"><stop stop-color="#20B038" offset="0%"/><stop stop-color="#60D66A" offset="100%"/></linearGradient><linearGradient x1=".5" y1="0" x2=".5" y2="1" id="b"><stop stop-color="#F9F9F9" offset="0%"/><stop stop-color="#FFF" offset="100%"/></linearGradient><linearGradient xlink:href="#a" id="f" x1="270.265" y1="1.184" x2="270.265" y2="541.56" gradientTransform="scale(.99775 1.00225)" gradientUnits="userSpaceOnUse"/><linearGradient xlink:href="#b" id="g" x1="279.952" y1=".811" x2="279.952" y2="560.571" gradientTransform="scale(.99777 1.00224)" gradientUnits="userSpaceOnUse"/><filter x="-.056" y="-.062" width="1.112" height="1.11" filterUnits="objectBoundingBox" id="c"><feGaussianBlur stdDeviation="2" in="SourceGraphic"/></filter><filter x="-.082" y="-.088" width="1.164" height="1.162" filterUnits="objectBoundingBox" id="d"><feOffset dy="-4" in="SourceAlpha" result="shadowOffsetOuter1"/><feGaussianBlur stdDeviation="12.5" in="shadowOffsetOuter1" result="shadowBlurOuter1"/><feComposite in="shadowBlurOuter1" in2="SourceAlpha" operator="out" result="shadowBlurOuter1"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.21 0" in="shadowBlurOuter1"/></filter><path d="M576.337 707.516c-.018-49.17 12.795-97.167 37.15-139.475L574 423.48l147.548 38.792c40.652-22.23 86.423-33.944 133.002-33.962h.12c153.395 0 278.265 125.166 278.33 278.98.025 74.548-28.9 144.642-81.446 197.373C999 957.393 929.12 986.447 854.67 986.48c-153.42 0-278.272-125.146-278.333-278.964z" id="e"/></defs><g fill="none" fill-rule="evenodd"><g transform="matrix(1 0 0 -1 -542.696 1013.504)" fill="#000" fill-rule="nonzero" filter="url(#c)"><use filter="url(#d)" xlink:href="#e" width="100%" height="100%"/><use fill-opacity=".2" xlink:href="#e" width="100%" height="100%"/></g><path transform="matrix(1 0 0 -1 41.304 577.504)" fill-rule="nonzero" fill="url(#f)" d="M2.325 274.421c-.014-47.29 12.342-93.466 35.839-134.166L.077 1.187l142.314 37.316C181.6 17.133 225.745 5.856 270.673 5.84h.12c147.95 0 268.386 120.396 268.447 268.372.03 71.707-27.87 139.132-78.559 189.858-50.68 50.726-118.084 78.676-189.898 78.708-147.968 0-268.398-120.386-268.458-268.358"/><path transform="matrix(1 0 0 -1 31.637 586.837)" fill-rule="nonzero" fill="url(#g)" d="M2.407 283.847c-.018-48.996 12.784-96.824 37.117-138.983L.072.814l147.419 38.654c40.616-22.15 86.346-33.824 132.885-33.841h.12c153.26 0 278.02 124.724 278.085 277.994.026 74.286-28.874 144.132-81.374 196.678-52.507 52.544-122.326 81.494-196.711 81.528-153.285 0-278.028-124.704-278.09-277.98zm87.789-131.724l-5.503 8.74C61.555 197.653 49.34 240.17 49.36 283.828c.049 127.399 103.73 231.044 231.224 231.044 61.74-.025 119.765-24.09 163.409-67.763 43.639-43.67 67.653-101.726 67.635-163.469-.054-127.403-103.739-231.063-231.131-231.063h-.09c-41.482.022-82.162 11.159-117.642 32.214l-8.444 5.004L66.84 66.86z"/><path d="M242.63 186.78c-5.205-11.57-10.684-11.803-15.636-12.006-4.05-.173-8.687-.162-13.316-.162-4.632 0-12.161 1.74-18.527 8.693-6.37 6.953-24.322 23.761-24.322 57.947 0 34.19 24.901 67.222 28.372 71.862 3.474 4.634 48.07 77.028 118.694 104.88 58.696 23.146 70.64 18.542 83.38 17.384 12.74-1.158 41.11-16.805 46.9-33.03 5.791-16.223 5.791-30.128 4.054-33.035-1.738-2.896-6.37-4.633-13.319-8.108-6.95-3.475-41.11-20.287-47.48-22.603-6.37-2.316-11.003-3.474-15.635 3.482-4.633 6.95-17.94 22.596-21.996 27.23-4.053 4.643-8.106 5.222-15.056 1.747-6.949-3.485-29.328-10.815-55.876-34.485-20.656-18.416-34.6-41.16-38.656-48.116-4.053-6.95-.433-10.714 3.052-14.178 3.12-3.113 6.95-8.11 10.424-12.168 3.467-4.057 4.626-6.953 6.942-11.586 2.316-4.64 1.158-8.698-.579-12.172-1.737-3.475-15.241-37.838-21.42-51.576" fill="#FFF"/></g></svg>
        </a>
    </div>

    <?php
        if ($url == 'login' || $url == 'dois-fatores' || $url == 'recuperar-senha' || $url == 'atualizar-senha' || $url == 'assinar' || $url == 'criar-loja' || $url == '404') {
            echo '
                <script src="https://www.google.com/recaptcha/api.js?render=' . $recaptcha_token . '"></script>
                <script>
                    function onClick(e) {
                        e.preventDefault();
                        grecaptcha.ready(function() {
                            grecaptcha.execute("' . $recaptcha_token . '", {action: "submit"}).then(function(token) {
                                // Add your logic to submit to your backend server here.
                            });
                        });
                    }
                </script>
            ';
        }
    ?>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <!-- Assets -->
    <script src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/js/form-steps.js"></script>
    <script src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/js/main.js"></script>
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function obterTamanhoDaTela() {
            // Obtém a largura da pagina
            var largura = window.innerWidth;

            // Obtém o elemento de imagem pelo ID
            var imagemElement = document.getElementById('logo');

            // Desktop Logo
            var desktopLogo = '<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png';
            // Mobile Logo
            var mobileLogo = '<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-mobile.png';

            if (largura <= 768) {
                imagemElement.src = mobileLogo;
            }
            else
            {
                imagemElement.src = desktopLogo;
            }
        }

        // Adiciona um ouvinte de evento para o redimensionamento da janela
        window.addEventListener("resize", obterTamanhoDaTela);

        // Chama a função para obter o tamanho da tela inicial
        obterTamanhoDaTela();
    </script>
    <script>
        // Função para obter o primeiro nome do usuário
        function obterPrimeiroNome(nomeCompleto) {
            return nomeCompleto.split(' ')[0].charAt(0).toUpperCase();
        }

        // Obtém o nome completo do usuário (substitua por sua lógica de obtenção do nome)
        var nomeCompletoUsuario = "<?php echo $name; ?>";

        // Obtém o primeiro nome do usuário
        var primeiroNome = obterPrimeiroNome(nomeCompletoUsuario);
        var primeiroNome = primeiroNome + ".";

        // Cria um elemento <canvas> temporário para desenhar a imagem
        var canvas = document.createElement('canvas');
        canvas.width = 50; // Largura da imagem
        canvas.height = 50; // Altura da imagem

        // Obtém o contexto 2D do canvas
        var ctx = canvas.getContext('2d');

        // Define o estilo da imagem (fundo branco e texto preto)
        ctx.fillStyle = '#DDDDDD';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';
        ctx.font = '24px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(primeiroNome, canvas.width / 2, canvas.height / 2);

        // Converte o canvas para uma imagem em base64
        var imagemBase64 = canvas.toDataURL();

        // Atribui a imagem gerada ao atributo src do elemento <img>
        var imagemUsuarioElement = document.getElementById('imagemUsuario');
        imagemUsuarioElement.src = imagemBase64;
    </script>

    <script>
        // Obtém todos os títulos e opções
        const titulos = document.querySelectorAll('.sidebar p');
        const opcoes = document.querySelectorAll('.sidebar .opcoes');

        // Adiciona um ouvinte de evento de clique a cada título
        titulos.forEach((titulo, index) => {
            titulo.addEventListener('click', () => {
                // Verifica se as opções estão visíveis e as esconde, ou vice-versa
                if (opcoes[index].style.display === 'none') {
                    opcoes[index].style.display = 'block';
                } else {
                    opcoes[index].style.display = 'none';
                }
            });
        });
    </script>

    <script>
        let arrow = document.querySelectorAll(".arrow");
        for (var i = 0; i < arrow.length; i++) {
            arrow[i].addEventListener("click", (e)=>{
                let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
                arrowParent.classList.toggle("showMenu");
            });
        }
        let body = document.querySelector("body");
        let backdrop = document.querySelector(".backdrop");
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector("#mobileBtn");
        sidebarBtn.addEventListener("click", ()=>{
            sidebar.classList.toggle("close");

            sidebarBtn.classList.toggle("bx-menu");
            sidebarBtn.classList.toggle("bx-x");

            body.classList.toggle("overflow-hidden");
            backdrop.classList.toggle("show");
        });
    </script>

    <script>
        $(document).ready(function () {
            // Adiciona um evento de clique ao botão com a classe 'close'
            $('.info-card :button').on('click', function () {
                // Remove o elemento pai do botão, que é o card
                $(".info-card").addClass("d-none");
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            // Adiciona um evento de clique ao botão com a classe 'close'
            $('.dropdown').on('click', function () {
                // Remove o elemento pai do botão, que é o card
                $(".dropdown").toggleClass("selected");
            });
        });
    </script>

    <!-- Tooltip -->
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <!-- Search products -->
    <script>
        $(document).ready(function() {
            // Ao clicar no botão de pesquisa
            $('#buttonSearch').click(function() {
                // Obtenha o valor do campo de entrada de pesquisa
                var searchTerm = $('#search').val();

                // Verifique se o campo de pesquisa não está vazio
                if (searchTerm && searchTerm.trim() !== '') {
                    // Atualize a URL do navegador com os parâmetros de pesquisa
                    window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos?search=' + searchTerm;
                } else {
                    // Se o campo de pesquisa estiver vazio, remova o parâmetro de pesquisa da URL
                    window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos';
                }
            });
        });
    </script>
</body>
</html>