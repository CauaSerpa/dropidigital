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

    // Pesquisar Loja

    // Verifica se há um shop_id definido na sessão
    @$shop_id = isset($_SESSION['shop_id']) ? $_SESSION['shop_id'] : $last_shop_login;

    // Tabela que será feita a consulta
    $tabela = "tb_shop";

    // Ajusta a consulta SQL para dar prioridade ao shop_id se ele existir
    if ($shop_id) {
        $sql = "SELECT * FROM $tabela WHERE user_id = :id ORDER BY id = :shop_id DESC, id DESC LIMIT 1";
    } else {
        $sql = "SELECT * FROM $tabela WHERE user_id = :id ORDER BY id DESC LIMIT 1";
    }

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($shop_id) {
        $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);
    }

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $user_id = $id;
        $id = $resultado['id'];
        $plan_id = $resultado['plan_id'];
        $loja = $resultado['name'];
        $phone = $resultado['phone'];
        $whatsapp = $resultado['whatsapp'];
    }

    // Nome da tabela para a busca
    $tabela = 'tb_plans_interval';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT plan_id FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':id', $plan_id);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (@$plan['plan_id'] == 1)
    {
        $limitProducts = 10;
    }
    else if (@$plan['plan_id'] == 2)
    {
        $limitProducts = 50;
    }
    else if (@$plan['plan_id'] == 3)
    {
        $limitProducts = 250;
    }
    else if (@$plan['plan_id'] == 4)
    {
        $limitProducts = 750;
    }
    else
    {
        $limitProducts = "ilimitado";
    }

    // Nome da tabela para a busca
    $tabela = 'tb_plans';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT name FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':id', $plan['plan_id']);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan_info = $stmt->fetch(PDO::FETCH_ASSOC);

    $plan_name = @$plan_info['name'];
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
            echo "";
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
            echo "";
        }
        elseif ($permissions == 1)
        {
            echo "";
    ?>
    <div class="tutorial__bg__"></div>
    <header class="l-header painel">
        <nav class="nav bd-grid">
            <div class="left">
                <div class="toggle" onclick="toggle()">
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
            <li class="<?php activeSidebarLink('personalizar'); ?>">
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
            <li class="<?php activeSidebarLink('lojas'); ?> <?php activeSidebarLink('ver-loja'); ?>">
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
            <li class="<?php activeSidebarLink('dominios'); ?> <?php activeSidebarLink('dominios-proprios'); ?> <?php showSidebarLinks('dominios'); ?> <?php showSidebarLinks('dominios-proprios'); ?>">
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
            echo "";
        }
        else
        {
            echo "";
    ?>
    <div class="tutorial__bg__"></div>
    <header class="l-header painel">
        <nav class="nav bd-grid">
            <div class="left">
                <div class="toggle" onclick="toggle()">
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
                        Ver o site
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="ms-1" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
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
                        <a class="btn btn-success rounded-1 fw-semibold px-4 py-2 small d-flex align-items-center" href="<?php echo INCLUDE_PATH_DASHBOARD . "back-end/admin/return.php" ?>">
                            Voltar
                            <i class='bx bx-log-in fs-5 ms-1' ></i>
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
                                <i class='bx bx-dollar-circle' ></i>
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
            <li class="<?php activeSidebarLink('newsletter'); ?> <?php showSidebarLinks('newsletter'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>newsletter" class="sidebar_link">
                                <i class='bx bx-envelope-open' ></i>
                            </a>
                            <span class="link_name">Newsletter</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>newsletter">Newsletter</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>newsletter" class="<?php activeSidebarLink('newsletter'); ?>">E-mails Cadastrados</a></li>
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
    <?php 
            echo ""; 
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

            if ($permissions == 1) {
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
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".bx-menu");
        sidebarBtn.addEventListener("click", ()=>{
            sidebar.classList.toggle("close");
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