<?php
    //Url Amigavel
    $url = isset($_GET['url']) ? $_GET['url'] : 'painel';

    session_start();
    ob_start();
    include('../config.php');

    // Tabela que sera feita a consulta
    $tabela = "tb_users";

    // ID que você deseja pesquisar
    $id = 1;

    // Consulta SQL
    $sql = "SELECT name, email FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $name = $resultado['name'];
        $email = $resultado['email'];
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }

    // Pesquisar Loja

    // Tabela que sera feita a consulta
    $tabela = "tb_shop";

    // Consulta SQL
    $sql = "SELECT name FROM $tabela WHERE user_id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $loja = $resultado['name'];
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Painel | Dropidigital</title>

    <!--Favicon-->
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH; ?>assets/images/favicon.png" type="image/x-icon">
    <!--CSS-->
    <?php echo ($url == 'login' || $url == 'assinar' || $url == 'dois-fatores' || $url == 'criar-loja' || $url == '404') ? '<link rel="stylesheet" href="'.INCLUDE_PATH_DASHBOARD.'assets/css/login.css">' : '<link rel="stylesheet" href="'.INCLUDE_PATH_DASHBOARD.'assets/css/style.css">';?>
    <!--Box Icons-->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Intro JS -->
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.0.1/minified/introjs.min.css" rel="stylesheet">
</head>
<body>
    <?php
        if ($url == 'login' || $url == 'dois-fatores' || $url == 'assinar' || $url == 'criar-loja' || $url == '404')
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
                        <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla lobortis lectus ac risus vulputate volutpat. Vestibulum tempor ultricies lobortis. Sed tempus diam eu laoreet iaculis. Nulla volutpat ultrices mauris, ac volutpat mi auctor at.</p>
                    </div>
                    <div class="balls"></div>
                </div>
            </div>
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
                <a href="<?php echo INCLUDE_PATH; ?>" class="nav__logo">
                    <img class="logo" src="" alt="Logo" id="logo">
                </a>
                <form action="" class="search__form">
                    <div class="search__container">
                        <select name="search_select" id="search-option" class="search__option">
                            <option value="product">Produto</option>
                            <option value="order">Pedido</option>
                        </select>
                        <input type="text" name="search" id="search" class="search" placeholder="Buscar..." title="Buscar..." autocomplete="off">
                        <button type="button" class="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="right">
                <div class="header__icon shop-link">
                    <a href="https://minha-loja.dropidigital.com.br">
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
                            <h3><?php echo $name; ?></h3>
                            <p><?php echo $email; ?></p>
                        </div>
                        <div class="theme__dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M20.742 13.045a8.088 8.088 0 0 1-2.077.271c-2.135 0-4.14-.83-5.646-2.336a8.025 8.025 0 0 1-2.064-7.723A1 1 0 0 0 9.73 2.034a10.014 10.014 0 0 0-4.489 2.582c-3.898 3.898-3.898 10.243 0 14.143a9.937 9.937 0 0 0 7.072 2.93 9.93 9.93 0 0 0 7.07-2.929 10.007 10.007 0 0 0 2.583-4.491 1.001 1.001 0 0 0-1.224-1.224zm-2.772 4.301a7.947 7.947 0 0 1-5.656 2.343 7.953 7.953 0 0 1-5.658-2.344c-3.118-3.119-3.118-8.195 0-11.314a7.923 7.923 0 0 1 2.06-1.483 10.027 10.027 0 0 0 2.89 7.848 9.972 9.972 0 0 0 7.848 2.891 8.036 8.036 0 0 1-1.484 2.059z"></path></svg>
                            <p>Tema Escuro</p>
                        </div>
                        <div class="shop">
                            <h3>Loja</h3>
                            <ul>
                                <li class="active">Loja 1</li>
                                <li>Loja 2</li>
                            </ul>
                        </div>
                        <div class="account">
                            <h3>Minha Conta</h3>
                            <ul>
                                <li>
                                    <a href="#">
                                        Editar Conta
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        Criar Loja
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
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
    <nav class="sidebar close">
        <ul class="nav-links">
            <li class="<?php activeSidebarLink(''); ?> <?php activeSidebarLink('painel'); ?>">
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>">
                    <i class='bx bx-grid-alt' ></i>
                    <span class="link_name">Dashboard</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="#">Dashboard</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('produtos'); ?> <?php activeSidebarLink('criar-produto'); ?> <?php activeSidebarLink('categorias'); ?> <?php activeSidebarLink('avaliacoes'); ?> <?php showSidebarLinks('produtos'); ?> <?php showSidebarLinks('criar-produto'); ?> <?php showSidebarLinks('avaliacoes'); ?> <?php showSidebarLinks('categorias'); ?>">
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
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="<?php activeSidebarLink('produtos'); ?>">Listar Produtos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto" class="<?php activeSidebarLink('criar-produto'); ?>" style="border-bottom: 1px solid #c4c4c4;">+ Criar Produto</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>categorias" class="<?php activeSidebarLink('categorias'); ?>">Categorias</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>avaliacoes" class="<?php activeSidebarLink('avaliacoes'); ?>">Avaliações</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php activeSidebarLink('video-youtube'); ?> <?php activeSidebarLink('tarja'); ?> <?php activeSidebarLink('paginas'); ?>
                    <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?> <?php showSidebarLinks('video-youtube'); ?> <?php showSidebarLinks('tarja'); ?> <?php showSidebarLinks('paginas'); ?>">
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
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners" class="<?php activeSidebarLink('banners'); ?>">Banners</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>logo" class="<?php activeSidebarLink('logo'); ?>">Logo</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>feed-instagram" class="<?php activeSidebarLink('feed-instagram'); ?>">Feed Instagram</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>botao-whatsapp" class="<?php activeSidebarLink('botao-whatsapp'); ?>">Botão WhatsApp</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>video-youtube" class="<?php activeSidebarLink('video-youtube'); ?>">Vídeo do Youtube</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>tarja" class="<?php activeSidebarLink('tarja'); ?>">Tarja Superior / Central</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>incluir-codigo-html" class="<?php activeSidebarLink('incluir-codigo-html'); ?>">Incluir código HTML</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="<?php activeSidebarLink('paginas'); ?>">Incluir pág. conteúdo</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?>">
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
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="<?php activeSidebarLink('planos'); ?>">Planos</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dados-para-pagamento" class="<?php activeSidebarLink('dados-para-pagamento'); ?>">Dados para pagamento</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>historico-de-faturas" class="<?php activeSidebarLink('historico-de-faturas'); ?>">Histórico de Faturas</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?>">
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
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>depoimentos" class="<?php activeSidebarLink('depoimentos'); ?>">Criar Depoimento</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?>">
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
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>telefone" class="sidebar_link">
                                <i class='bx bx-conversation' ></i>
                            </a>
                            <span class="link_name">Atendimento</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>telefone">Atendimento</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>telefone" class="<?php activeSidebarLink('telefone'); ?>">Telefone</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>email" class="<?php activeSidebarLink('email'); ?>">E-mail</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>endereco" class="<?php activeSidebarLink('endereco'); ?>">Endereço</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('redes-sociais'); ?> <?php showSidebarLinks('redes-sociais'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais" class="sidebar_link">
                                <i class='bx bx-user-circle' ></i>
                            </a>
                            <span class="link_name">Redes Sociais</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais">Redes Sociais</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais" class="<?php activeSidebarLink('redes-sociais'); ?>">Instagram</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais" class="<?php activeSidebarLink('redes-sociais'); ?>">Facebook</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais" class="<?php activeSidebarLink('redes-sociais'); ?>">Tiktok</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>redes-sociais" class="<?php activeSidebarLink('redes-sociais'); ?>">YouTube</a></li>
                </ul>
            </li>
            <li class="<?php activeSidebarLink('banners'); ?> <?php activeSidebarLink('logo'); ?> <?php activeSidebarLink('feed-instagram'); ?> <?php showSidebarLinks('banners'); ?> <?php showSidebarLinks('logo'); ?> <?php showSidebarLinks('feed-instagram'); ?>">
                <div class="iocn-link">
                        <p>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-artigo" class="sidebar_link">
                                <i class='bx bx-desktop' ></i>
                            </a>
                            <span class="link_name">Blog</span>
                        </p>
                    <i class='bx bxs-chevron-down arrow' ></i>
                </div>
                <ul class="sub-menu">
                    <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-artigo">Blog</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-artigo" class="<?php activeSidebarLink('criar-artigo'); ?>">Criar Artigo</a></li>
                    <li><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>lista-de-artigos" class="<?php activeSidebarLink('lista-de-artigos'); ?>">Lista de Artigos</a></li>
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
            <div class="sidebar_bottom">
                <li>
                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto">
                        <i class='bx bx-plus' ></i>
                        <span class="link_name">Criar produto</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto">Criar produto</a></li>
                    </ul>
                </li>
                <div class="line"></div>
                <li>
                    <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">
                        <i class='bx bx-cog' ></i>
                        <span class="link_name">Configurações</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name ms-0" href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes">Configurações</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class='bx bx-log-out' ></i>
                        <span class="link_name">Sair</span>
                    </a>
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="#">Sair</a></li>
                    </ul>
                </li>
			</div>
		</ul>
    </nav>
    <?php 
            echo ""; 
        }
    ?>
    
    <main class="main container grid <?php echo ($url == "login" || $url == 'dois-fatores' || $url == 'assinar' || $url == 'criar-loja' || $url == "404") ? 'box' : ''; ?>">
        

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
            if(file_exists('pages/'.$url.'.php')){
                include('pages/'.$url.'.php');
            }else{
                //a pagina nao existe
                header('Location: '.INCLUDE_PATH_DASHBOARD.'404');
            }
        ?>
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
        if ($url == 'login' || $url == 'dois-fatores' || $url == 'assinar' || $url == 'criar-loja' || $url == '404') {
            echo '
                <script src="https://www.google.com/recaptcha/api.js?render=6LcRUkUnAAAAAJGzCTc4KTbgqgsEmwYZCTZtNp9i"></script>
                <script>
                    function onClick(e) {
                        e.preventDefault();
                        grecaptcha.ready(function() {
                            grecaptcha.execute("6LcRUkUnAAAAAJGzCTc4KTbgqgsEmwYZCTZtNp9i", {action: "submit"}).then(function(token) {
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
</body>
</html>