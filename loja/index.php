<?php
    // Pega o dominio da pagina
    $dominioCompleto = $_SERVER['HTTP_HOST'];
    
    function temSubdominio($dominio) {
        // Divide o domínio em partes usando o ponto como delimitador
        $partes = explode('.', $dominio);
    
        // Verifica se há mais de uma parte (subdomínios)
        return count($partes) > 2;
    }
    
    function separarDominioSubdominio($url) {
        // Divide a URL em partes usando o ponto como delimitador
        $partes = explode('.', $url);
    
        // Verifica se há mais de uma parte (subdomínios presentes)
        if (count($partes) > 2) {
            // Subdomínio é a primeira parte
            $subdominio = $partes[0];
    
            // Domínio é a parte a partir da segunda até a última
            $dominio = implode('.', array_slice($partes, 1));
        } else {
            // Não há subdomínios, o subdomínio é vazio
            $subdominio = 'www';
            
            // Domínio é a URL completa
            $dominio = $url;
        }
    
        return [
            'subdominio' => $subdominio,
            'dominio' => $dominio,
        ];
    }
    
    if (temSubdominio($dominioCompleto)) {
        $resultadoComSubdominio = separarDominioSubdominio($dominioCompleto);
        $subdomain = $resultadoComSubdominio['subdominio'];
        $domain = $resultadoComSubdominio['dominio'];

        // Combina todas as partes para obter a URL completa
        $urlCompleta = "https://$subdomain.$domain/";
    } else {
        $resultadoSemSubdominio = separarDominioSubdominio($dominioCompleto);
        $subdomain = $resultadoSemSubdominio['subdominio'];
        $domain = $resultadoSemSubdominio['dominio'];

        // Combina todas as partes para obter a URL completa
        $urlCompleta = "https://$domain/";
    }

    // echo $subdomain;
    // echo $domain;

    $subdomain = "minha-loja";
    $domain = "dropidigital.com.br";

    define('INCLUDE_PATH_LOJA', $urlCompleta);

    session_start();
    include('../config.php');

    // Pesquisar dominio
    // Tabela que sera feita a consulta
    $tabela = "tb_domains";

    // Consulta SQL
    $sql = "SELECT shop_id FROM $tabela WHERE subdomain = :subdomain AND domain = :domain";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':subdomain', $subdomain, PDO::PARAM_STR);
    $stmt->bindParam(':domain', $domain, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $shop_id = $resultado['shop_id'];
    }

    // Pesquisar Loja
    // Tabela que sera feita a consulta
    $tabela = "tb_shop";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $shop_id, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $shop_id = $resultado['id'];
        $user_id = $resultado['user_id'];
        $loja = $resultado['name'];
        $title = $resultado['title'];
        $description = $resultado['description'];
        $logo = $resultado['logo'];
        $video = $resultado['video'];
        $facebook = $resultado['facebook'];
        $x = $resultado['x'];
        $pinterest = $resultado['pinterest'];
        $instagram = $resultado['instagram'];
        $youtube = $resultado['youtube'];
        $token_instagram = $resultado['token_instagram'];

        $cpf_cnpj = $resultado['cpf_cnpj'];
        $razao_social = $resultado['razao_social'];

        $phone = $resultado['phone'];

        if (isset($resultado['whatsapp'])) {
            $whatsapp = $resultado['whatsapp'];
            // Fomatando celular
            $formatted_whatsapp = preg_replace('/\D/', '', $resultado['whatsapp']);
        } else {
            $whatsapp = "+55 " . $resultado['phone'];
            // Fomatando celular
            $formatted_whatsapp = preg_replace('/\D/', '', $resultado['phone']);
        }

        $email = $resultado['email'];

        $top_highlight_bar = $resultado['top_highlight_bar'];
        $top_highlight_bar_location = $resultado['top_highlight_bar_location'];
        $top_highlight_bar_text = $resultado['top_highlight_bar_text'];
        $center_highlight_images = $resultado['center_highlight_images'];
    } else {
        // Página de error 404
        include_once('pages/404.php');
        die;
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_users";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $user_id = $resultado['id'];
        $name = $resultado['name'];
        $email = $resultado['email'];
    }

    //Pesquisa endereco
    // Tabela que sera feita a consulta
    $tabela = "tb_address";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $cep = $resultado['cep'];
        $endereco = $resultado['endereco'];
        $numero = $resultado['numero'];
        $complemento = $resultado['complemento'];
        $bairro = $resultado['bairro'];
        $cidade = $resultado['cidade'];
        $estado = $resultado['estado'];
    }
?>
<?php
    // Fuso horario Sao Paulo
    date_default_timezone_set('America/Sao_Paulo');
    // Data atual
    $dataAtual = date("Y-m-d");

    // Consulta para verificar se já há uma entrada para a data atual
    $sql = "SELECT * FROM tb_visits WHERE data = :data";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':data', $dataAtual);
    $stmt->execute();

    // Se não houver entrada para a data atual, insira uma nova entrada
    if ($stmt->rowCount() == 0) {
        $sql = "INSERT INTO tb_visits (data, contagem) VALUES (:data, 1)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':data', $dataAtual);
        $stmt->execute();
    } else {
        // Se houver entrada para a data atual, apenas atualize a contagem
        $sql = "UPDATE tb_visits SET contagem = contagem + 1 WHERE data = :data";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':data', $dataAtual);
        $stmt->execute();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title><?php echo ($title == "") ? $loja : $title; ?></title>
    <!--Favicon-->
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH; ?>assets/images/favicon.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/magnific-popup.min.css">
    <!-- Nice Select -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/nice-select.min.css">
    <!-- Animate -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/animate.min.css">
    <!-- Slick -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/slick.min.css">
    <!-- Main Style -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/style.css">
    <!--Box Icons-->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Feed Instagram -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
</head>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_scripts';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindValue(':status', 1);
    $stmt->execute();

    // Recuperar os resultados
    $scripts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop através dos resultados e exibir todas as colunas
    foreach ($scripts as $script) {
        echo "<script>";
        echo $script['script'];
        echo "</script>";
    }
?>

<style>
    a:hover
    {
        color: var(--heading-color) !important;
        text-decoration: underline;
    }
    a.nav-link:hover,
    a.btn:hover
    {
        text-decoration: none !important;
        color: var(--heading-color) !important;
    }

    .show-categories
    {
        padding: var(--bs-navbar-toggler-padding-y) var(--bs-navbar-toggler-padding-x);
        font-size: var(--bs-navbar-toggler-font-size);
        line-height: 1;
        color: var(--bs-navbar-color);
        background-color: transparent;
        border: var(--bs-border-width) solid var(--bs-navbar-toggler-border-color);
        border-radius: var(--bs-navbar-toggler-border-radius);
        transition: var(--bs-navbar-toggler-transition);
    }
    .categories.nav-show-categories
    {
        transform: translateY(0px) !important;
    }

    /* Carrossel */
    .carousel-control-next:not(.banner-full),
    .carousel-control-prev:not(.banner-full)
    {
        width: initial;
    }
    .carousel-control-prev-icon:not(.banner-full span)
    {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    }
    .carousel-control-next-icon:not(.banner-full span)
    {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .container.highlight-center .highlight
    {
        position: relative;
    }
    .container.highlight-center .highlight::before
    {
        content: "";
        position: absolute;
        right: 0;
        top: 50%;
        transform: translate(50%, -50%);
        width: 10px;
        height: 10px;
        background: #e0e0e0;
        border-radius: 50%;
    }
    .container.highlight-center .highlight:last-child:before
    {
        display: none;
    }

    a.category-link,
    a.product-link
    {
        text-decoration: none;
    }

    /* Categories */
    .category-link img
    {
        width: 182px;
        height: 182px;
        object-fit: cover;
    }

    /* Product */
    .product-image
    {
        position: relative;
    }
    .product-image .card-discount
    {
        position: absolute;
        left: 15px;
        top: 10px;
        width: 100px;
        header: 5px;
        font-weight: 600;
        border-radius: .6rem;
        text-align: center;
        background: #000;
        color: #fff;
    }
    .product-image .card-img-top
    {
        width: 286px;
        height: 286px;
        object-fit: cover;
    }

    .social-medias li a
    {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    
    .categories
    {
        transition: .3s all;
    }
    .categories.scroll
    {
        transform: translateY(-65px);
    }

    .to-top
    {
        position: fixed;
        right: 20px;
        bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;

        opacity: 0;
        pointer-events: none;
        transition: .3s all;
    }
    .to-top.scroll
    {
        opacity: 1;
        pointer-events: all;
    }
    .whatsapp-button
    {
        position: fixed;
        left: 20px;
        bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
    }
</style>
<body>
    <header class="header fixed-top">
        <div class="stripe text-center text-light <?php echo ($top_highlight_bar == 0) ? "d-none" : ""; ?> <?php echo ($top_highlight_bar_location == 1) ? "" : "d-none"; ?>" style="background-color: rgb(35, 35, 35);"><?php echo $top_highlight_bar_text; ?></div>
        <nav class="navbar bg-white navbar-expand-lg border-bottom border-body z-3" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <a class="navbar-brand logo" href="<?php echo INCLUDE_PATH_LOJA; ?>"><?php echo (isset($logo)) ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" alt="Logo ' . $loja . '" style="width: 150px;">' : $loja; ?></a>
                <button class="show-categories me-3 d-none" id="show-categories" type="button">
                    <i class='bx bx-menu fs-3' id="toggle-icon"></i>
                </button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex me-3" role="search">
                        <input class="form-control py-1 px-3" type="search" placeholder="Buscar produto" aria-label="Search" style="border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius);">
                        <button class="btn btn-dark" type="submit" style="border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0;">
                            <i class='bx bx-search-alt-2'></i>
                        </button>
                    </form>
                    <style>
                        /* Tooltip */
                        .service,
                        .discount {
                            position: relative;
                        }

                        .service-tooltip,
                        .discount-tooltip {
                            width: 220px;
                            display: none;
                            position: absolute;
                            top: 100%;
                            left: 0;
                            background-color: #fff;
                            text-align: center;
                            line-height: 1.5;
                            padding: 10px;
                            border-radius: .3rem;
                            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                            z-index: 1;
                        }

                        .service-tooltip {
                            width: max-content;
                            text-align: start;
                        }

                        .service:hover .service-tooltip,
                        .discount:hover .discount-tooltip {
                            display: block;
                        }
                    </style>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="discount nav-item me-2">
                            <a class="nav-link d-flex align-items-center active" aria-current="page" href="#">
                                <i class='bx bxs-discount fs-1 me-2' ></i>
                                <small class="fw-semibold lh-sm">Retire seu cupom<br>de desconto</small>
                            </a>
                            <div class="discount-tooltip">
                                <span class="small text-black fw-semibold m-0">Ainda não comprou na loja?</span>
                                <h5 class="fw-semibold mb-2 mt-2">QUERODESCONTO10</h5>
                                <span class="small text-secondary fw-semibold m-0">Use o cupom acima para ganhar 10% off em sua primeira compra</span>
                            </div>
                        </li>
                        <li class="service nav-item me-2">
                            <a class="nav-link d-flex align-items-center active" aria-current="page" href="#">
                                <i class='bx bxl-whatsapp fs-1 me-2' ></i>
                                <small class="fw-semibold lh-sm">Central de<br>Suporte</small>
                            </a>
                            <div class="service-tooltip">
                                <ul class="p-2">
                                    <span class="text-black fw-semibold">Atendimento:</span>
                                    <li class="text-secondary mt-2 mb-2 <?php echo ($phone == "") ? "d-none" : ""; ?>">
                                        <i class='bx bxs-phone' ></i> Telefone: 
                                        <a href="tel:<?php echo $phone; ?>">
                                            <?php echo $phone; ?>
                                        </a>
                                    </li>
                                    <li class="text-secondary tel-whatsapp mb-2 <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
                                        <i class="bx bxl-whatsapp"></i> Whatsapp: 
                                        <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank">
                                            <?php echo $whatsapp; ?>
                                        </a>
                                    </li>
                                    <li class="text-secondary <?php echo ($email == "") ? "d-none" : ""; ?>">
                                        <i class="bx bxs-envelope"></i> E-mail: 
                                        <a href="mailto:<?php echo $email; ?>">
                                            <?php echo $email; ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="categories navbar bg-white navbar-expand-lg border-bottom border-body z-2" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav categorias me-auto mb-2 mb-lg-0">
                        <style>
                            .categorias {
                                list-style: none;
                            }

                            .categorias li {
                                position: relative;
                                margin-right: 20px;
                            }

                            .nav-link i.bx#toggle-categories
                            {
                                line-height: inherit;
                                display: flex;
                            }

                            .todas-categorias,
                            .subcategorias {
                                width: max-content;
                                display: none;
                                position: absolute;
                                top: 100%;
                                left: 0;
                                background-color: #fff;
                                padding: 10px;
                                border-radius: .3rem;
                                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                                z-index: 1;
                            }

                            .categorias li:hover .todas-categorias,
                            .categorias li:hover .subcategorias {
                                display: flex;
                            }

                            /* Estilo do tooltip */
                            .todas-categorias li,
                            .subcategorias li {
                                margin: 5px;
                            }

                            /* Para tooltip todas as categorias */
                            .todas-categorias
                            {
                                width: 300px;
                            }
                            .todas-categorias li a
                            {
                                font-weight: 500;
                            }
                        </style>
                        <li class="nav-item">
                            <a class="nav-link d-flex" href="#">
                                <i class='bx bx-menu fs-3 me-2' id="toggle-categories"></i>
                                Todas as Categorias
                            </a>
                            <ul class="todas-categorias">
                                <?php
                                    // Nome da tabela para a busca
                                    $tabela = 'tb_categories';

                                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category ORDER BY id DESC";

                                    // Preparar e executar a consulta
                                    $stmt = $conn_pdo->prepare($sql);
                                    $stmt->bindParam(':shop_id', $shop_id);
                                    $stmt->bindValue(':status', 1);
                                    $stmt->bindValue(':parent_category', 1);
                                    $stmt->execute();

                                    // Recuperar os resultados
                                    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt->rowCount() > 0) {
                                        // Inicialize um contador
                                        $contador = 0;

                                        // Loop através dos resultados e exibir todas as colunas
                                        foreach ($categories as $category) {
                                            // Se o contador for um múltiplo de 6, adicione a div
                                            if ($contador % 6 === 0) {
                                                echo '<div style="display: flex; flex-direction: column; margin-right: 5px;">';
                                            }

                                            echo "<li>";
                                            echo "<a href='" . INCLUDE_PATH_LOJA . $category['link'] . "'>" . $category['name'] . "</a>";
                                            echo "</li>";

                                            // Se o contador for um múltiplo de 6, feche a div
                                            if ($contador % 6 === 5) {
                                                echo '</div>';
                                            }

                                            // Incrementar o contador
                                            $contador++;
                                        }

                                        // Certifique-se de fechar a div se o total de itens não for um múltiplo de 6
                                        if ($contador % 6 !== 0) {
                                            echo '</div>';
                                        }
                                    }
                                ?>
                            </ul>
                        </li>
                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_categories';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis AND parent_category = :parent_category ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->bindValue(':emphasis', 1);
                            $stmt->bindValue(':parent_category', 1);
                            $stmt->execute();

                            // Recuperar os resultados
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Loop através dos resultados e exibir todas as colunas
                            $contador = 0; // Inicializa o contador
                            foreach ($categories as $category) {
                                // Consulta SQL para selecionar todas as colunas com base no ID
                                $sql = "SELECT * FROM tb_categories WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category";
        
                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindParam(':shop_id', $shop_id);
                                $stmt->bindValue(':status', 1);
                                $stmt->bindParam(':parent_category', $category['id']);
                                $stmt->execute();
        
                                // Recuperar os resultados
                                $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                                echo "<li class='nav-item d-flex'>";
                                echo "<a class='nav-link d-flex align-items-center' href='" . INCLUDE_PATH_LOJA . $category['link'] . "'>";
                                echo "<img src='" . INCLUDE_PATH_DASHBOARD . "back-end/category/" . $shop_id . "/icon/" . $category['icon'] . "' alt='Ícone " . $category['name'] . "' class='me-2' style='width: 32px; height: 32px;'>";
                                echo $category['name'];
                                echo "</a>";
                                if (!empty($subcategories)) { // Verifica se há resultados em $subcategories
                                    echo "<ul class='subcategorias'>"; // Abre a lista apenas se houver resultados
                                
                                    $contador = 0; // Inicializa o contador
                                
                                    // Loop através dos resultados e exibir todas as colunas
                                    foreach ($subcategories as $subcategory) {
                                        if ($contador % 6 == 0) {
                                            echo "<div style='display: flex; flex-direction: column; margin-right: 5px;'>";
                                        }
                                        echo "<li>";
                                        echo "<a href='" . INCLUDE_PATH_LOJA . $subcategory['link'] . "'>" . $subcategory['name'] . "</a>";
                                        echo "</li>";
                                        if (($contador + 1) % 6 == 0 || ($contador == count($subcategories) - 1)) {
                                            echo "</div>"; // Fecha a <div> a cada 6 elementos ou no último elemento
                                        }
                                        $contador++;
                                    }
                                
                                    echo "</ul>"; // Fecha a lista
                                }
                                echo "</li>";
                            }
                        ?>
                    </ul>

                    <?php
                        // Nome da tabela para a busca
                        $tabela = 'tb_articles';

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop_id);
                        $stmt->execute();

                        $countArticles = $stmt->rowCount();
                    ?>
                    <a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="btn btn-light d-flex justify-content-end align-items-center <?php echo ($countArticles == 0) ? "d-none" : ""; ?>">
                        <img style="height: 28px; margin-right: 7px;" src="https://cdn.awsli.com.br/2544/2544943/arquivos/blog.svg">
                        <strong class="titulo text-dark">Blog</strong>
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div id="myCarousel" class="carousel slide mb-4" data-bs-ride="carousel" style="margin-top: <?php echo ($top_highlight_bar == 1) ? "186.39px" : "154.39px"; ?>;">
            <!-- Indicators (pontos de navegação) -->
            <ol class="carousel-indicators">
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_banner_info';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND location = :location ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':location', 'full-banner');
                $stmt->execute();

                $countBanners = $stmt->rowCount();

                // Inicializa uma variável para contar os IDs
                $contador = 0;

                // Verifica se há IDs para evitar um loop vazio
                if ($stmt->rowCount() > 0) {
                    // Faça um loop para contar os IDs sequencialmente
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $currentId = $row['id'];

                        $active = ($contador == 0) ? 'active' : ''; // Adicione a classe active ao ID 1
                        echo "<li data-bs-target='#myCarousel' data-bs-slide-to='" . $contador . "' class='" . $active . "'></li>";

                        $contador++;
                    }
                }
            ?>
            </ol>

            <script>
                // Função para verificar a largura da tela e exibir a imagem apropriada
                function switchImage(responsiveImage, desktopBanner, mobileBanner) {
                    // Verificar a largura da tela
                    if (window.innerWidth < 768 && mobileBanner !== 'null') {
                        // Se a largura da tela for menor que 768px e houver uma imagem móvel, exiba-a
                        responsiveImage.src = mobileBanner;
                    } else {
                        // Se a largura da tela for 768px ou mais ou não houver uma imagem móvel, exiba a imagem do banner padrão
                        responsiveImage.src = desktopBanner;
                    }
                }

                // Executar a função ao carregar a página e quando a janela for redimensionada
                window.onload = switchImage;
                window.onresize = switchImage;
            </script>

            <!-- Slides (itens do carrossel) -->
            <div class="carousel-inner">
                <?php
                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND location = :location ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':location', 'full-banner');
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $primeiroElemento = true;

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $banner) {
                    // Consulta SQL para selecionar todas as colunas com base no ID
                    $sql = "SELECT * FROM tb_banner_img WHERE banner_id = :banner_id ORDER BY id ASC LIMIT 1";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':banner_id', $banner['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($imagens as $imagem) {
                        // Adicione a classe especial apenas ao primeiro elemento
                        $active = ($primeiroElemento) ? 'active' : '';
                        $mobileBannerSrc = (isset($imagem['mobile_banner'])) ? INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['mobile_banner'] : 'null';

                        ?>
                        <a href="<?= $banner['link'] ?>" target="<?= $banner['target'] ?>">
                            <div class="carousel-item <?= $active ?>">
                                <img src="<?= INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] ?>" alt="<?= $banner['name'] ?>" onload="switchImage(this, '<?= INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] ?>', '<?= $mobileBannerSrc ?>')" class="w-100" style="height: 535px; object-fit: cover;">
                            </div>
                        </a>
                        <?php

                        // Marque que o primeiro elemento foi processado
                        $primeiroElemento = false;
                    }
                }
                ?>
            </div>

            <!-- Controles (setas de navegação) -->
            <a class="carousel-control-prev banner-full" href="#myCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next banner-full" href="#myCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>

        <?php
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            
            $url = $protocol . "://" . $host . $uri;
            
            $substring_product = "produto/";
            $substring_page = "atendimento/";
            $substring_article = "blog/";
            
            // Obtenha a rota da URL
            $route = isset($_GET['url']) ? $_GET['url'] : '';
            
            $categoria = null; // Inicializa $categoria fora do bloco condicional
            
            if (strpos($url, $substring_product) !== false) {
                // Removendo "produto/" da URL
                $link = preg_replace("/^produto\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_products";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o produto como um array associativo
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($product) {
                    $product_id = $product['id'];
                }
            } elseif (strpos($url, $substring_page) !== false) {
                // Removendo "atendimento/" da URL
                $link = preg_replace("/^atendimento\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_pages";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o atendimento como um array associativo
                $page = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($page) {
                    $page_id = $page['id'];
                }
            } elseif (strpos($url, $substring_article) !== false) {
                // Removendo "atendimento/" da URL
                $link = preg_replace("/^blog\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_articles";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o atendimento como um array associativo
                $article = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($article) {
                    $article_id = $article['id'];
                }
            } else {
                // Tabela que será pesquisada
                $tabela = "tb_categories";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $route, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o categoria como um array associativo
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($category) {
                    $category_id = $category['id'];
                }
            }
            
            // Analise a rota e determine qual página carregar
            if (empty($route)) {
                // Página inicial da loja
                include_once('pages/loja.php');
            } elseif (@$product) {
                // Página de detalhes do produto
                include_once('pages/produto.php');
            } elseif (@$page) {
                // Página de detalhes da página
                include_once('pages/pagina.php');
            } elseif ($route === "blog" || $route === "blog/") {
                // Remove o banner padrao da loja
                echo '<script>document.getElementById("myCarousel").classList.add("d-none");</script>';

                // Página de detalhes da página
                include_once('pages/blog.php');
            } elseif (@$article) {
                // Remove o banner padrao da loja
                echo '<script>document.getElementById("myCarousel").classList.add("d-none");</script>';

                // Página de detalhes da página
                include_once('pages/artigo.php');
            } elseif ($stmt->rowCount() > 0) {
                // Página de detalhes da categoria
                include_once('pages/categoria.php');
            } else {
                // Página de erro 404 para rotas não encontradas
                include_once('pages/404.php');
            }
        ?>

        <div class="justify-content-center <?php echo ($token_instagram == "") ? "d-none" : "mb-3"; ?>" style="text-align: -webkit-center;">
            <div class="d-flex align-items-center justify-content-center">
                <p class="d-flex align-items-center fs-4 mb-3 me-2">
                    <i class='bx bxl-instagram fs-1 me-2' ></i>
                    Siga nosso instagram
                </p>
                <h4 class="mb-3" id="username"></h4>
            </div>
            <div style="width: 100px; height: 5px; background: #000;"></div>
        </div>

        <!-- Feed Instagram -->
        <style>
            .item::before
            {
                content: '';
                position: absolute;
                width: 100%;
                height: 100%;
                background: black;
                opacity: 0;
                transition: .6s;
                pointer-events: none;
            }
            .item .bxl-instagram
            {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%,-50%);
                font-size: 2.5rem;
                color: white;
                opacity: 0;
                transition: .3s;
                pointer-events: none;
            }
            .item:hover .bxl-instagram
            {
                opacity: 1;
            }
            .item:hover::before
            {
                opacity: .5;
            }
        </style>

        <!-- Div para mostrar as imagens do feed do Instagram -->
        <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag carousel-inner mb-4"></div>
    
        <div class="container">
            <div class="row p-4" id="newsletterContainer">
                <p class="col-md-6 d-flex align-items-center fs-4">
                    <i class='bx bx-mail-send fs-2 me-2'></i>
                    Receba Ofertas e Novidades de nossa loja
                </p>
                <form class="col-md-6 d-flex" role="text" id="newsletterForm">
                    <input class="form-control py-1 px-3 me-2" type="text" name="email" id="email" placeholder="E-mail" aria-label="E-mail">
                    <button class="btn btn-dark" id="btn-newsletter" type="submit" style="width: 270px;">
                        Quero receber!
                    </button>
                </form>
            </div>
            <p class="d-none fs-4 justify-content-center p-4" id="success">
                <i class='bx bx-check fs-2 me-2' style="color: rgb(1, 200, 155);"></i>
                Obrigado por se inscrever! Aguarde novidades da nossa loja em breve.
            </p>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
            $(document).ready(function() {
                // Quando o formulário for enviado
                $('#newsletterForm').submit(function(e) {
                    e.preventDefault();

                    // Coleta o email inserido pelo usuário
                    var email = $('#email').val();
                    var id = <?php echo $shop_id; ?>

                    // Envia uma solicitação AJAX para o servidor
                    $.ajax({
                        url: './newsletter/subscribe/index.php', // Nome do arquivo PHP para processar a inscrição
                        type: 'POST',
                        data: { id: id, email: email },
                        success: function(response) {
                            // Trata a resposta do servidor
                            if (response === 'success') {
                                $('#newsletterContainer').removeClass('d-flex');
                                $('#newsletterContainer').addClass('d-none');
                                
                                $('#success').removeClass('d-none');
                                $('#success').addClass('d-flex');
                            } else {
                                $('#btn-newsletter').text('Erro!');
                                $('#btn-newsletter').removeClass('btn-dark');
                                $('#btn-newsletter').addClass('btn-danger');

                                setTimeout(resetarBotao, 3000); // 3000 milissegundos = 3 segundos
                            }
                        }
                    });
                });
            });

            function resetarBotao() {
                $("#btn-newsletter").removeClass("btn-danger");

                $('#btn-newsletter').addClass('btn-dark');
                $('#btn-newsletter').text('Quero receber!');

                // Define o valor do campo como uma string vazia
                $("#email").val("");
            }
        </script>

        <a href="#" class="to-top btn btn-dark p-2 rounded-1">
            <i class='bx bx-chevron-up fs-2' ></i>
        </a>

        <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank" class="whatsapp-button btn btn-dark p-2 rounded-1 <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
            <i class='bx bxl-whatsapp fs-2'></i>
        </a>
    </main>

    <!-- Footer -->
    <footer id="rodape" style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; display: block !important; margin-bottom: 2rem;">
        <div class="container">
            <h1 class="logo text-primary">
                <a href="<?php echo INCLUDE_PATH_LOJA; ?>" title="<?php echo $loja; ?>">
                    <?php echo (isset($logo)) ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" alt="Logo ' . $loja . '" style="width: 250px;">' : $loja; ?>
                </a>
            </h1>
            <div class="row">
                <div class="col-md-4" style="margin-bottom: 4rem;">
                    <span class="titulo fw-semibold">Sobre a loja</span>
                    <p class="mt-3" id="meuParagrafo"></p>
                    <button class="btn btn-dark more mb-4" id="verMaisBotao">Ver Mais</button>

                    <script>
                        function limitarTexto(texto, limite) {
                            if (texto.length > limite) {
                                return {
                                    texto: texto.slice(0, limite),
                                    cortado: true
                                };
                            } else {
                                return {
                                    texto: texto,
                                    cortado: false
                                };
                            }
                        }

                        function toggleTexto() {
                            var paragrafo = document.getElementById("meuParagrafo");
                            var textoCompleto = paragrafo.getAttribute("data-texto-completo");
                            var estadoAtual = paragrafo.getAttribute("data-estado");

                            if (estadoAtual === "colapsado") {
                                // Expandir o texto
                                paragrafo.textContent = textoCompleto;
                                paragrafo.setAttribute("data-estado", "expandido");
                                document.getElementById("verMaisBotao").textContent = "Ver Menos";
                            } else {
                                // Colapsar o texto
                                var limiteInicial = 200; // Limite de caracteres inicial
                                var resultado = limitarTexto(textoCompleto, limiteInicial);
                                paragrafo.textContent = resultado.texto + "...";
                                paragrafo.setAttribute("data-estado", "colapsado");
                                document.getElementById("verMaisBotao").textContent = "Ver Mais";
                            }
                        }

                        // Exemplo de uso:
                        var textoCompleto = "<?php echo $description; ?>";
                        var limiteInicial = 200; // Limite de caracteres inicial
                        var resultado = limitarTexto(textoCompleto, limiteInicial);

                        // Selecione o elemento <p> pelo ID
                        var paragrafo = document.getElementById("meuParagrafo");

                        if (resultado.cortado) {
                            paragrafo.textContent = resultado.texto + "...";
                            paragrafo.setAttribute("data-texto-completo", textoCompleto);
                            paragrafo.setAttribute("data-estado", "colapsado");

                            // Adicione um botão "Ver Mais"
                            var verMaisBotao = document.getElementById("verMaisBotao");
                            verMaisBotao.addEventListener("click", toggleTexto);
                        } else {
                            paragrafo.textContent = resultado.texto;
                            document.getElementById("verMaisBotao").style.display = "none"; // Esconde o botão se o texto não for cortado
                        }
                    </script>

                    <div class="lista-redes">
                        <?php if (!empty($facebook) || !empty($x) || !empty($pinterest) || !empty($instagram) || !empty($youtube)) : ?>
                            <h5 class="fw-semibold">Siga-nos</h5>
                        <?php endif; ?>

                        <ul class="social-medias d-flex">
                            <?php if (!empty($facebook)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $facebook; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Facebook">
                                        <i class='bx bxl-facebook'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($x)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $x; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Twitter">
                                        <i class="fa-brands fa-x-twitter" style="font-size: 14px;"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($pinterest)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $pinterest; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Pinterest">
                                        <i class='bx bxl-pinterest'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($instagram)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $instagram; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Instagram">
                                        <i class='bx bxl-instagram'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($youtube)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $youtube; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no YouTube">
                                        <i class='bx bxl-youtube'></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2">
                    <span class="titulo fw-semibold">Categorias</span>
                    <ul class="total-itens_8 mt-3">

                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_categories';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->bindValue(':parent_category', 1);
                            $stmt->execute();

                            // Recuperar os categories
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                // Loop através dos resultados e exibir todas as colunas
                                foreach ($categories as $category) {
                                    echo "<li>";
                                    echo "<a href='" . INCLUDE_PATH_LOJA . $category['link'] . "'>" . $category['name'] . "</a>";
                                    echo "</li>";
                                }
                            }
                        ?>

                        <li id="menu_blog" class="mt-2 <?php echo ($countArticles == 0) ? "d-none" : ""; ?>">
                            <a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="btn btn-light">
                                <img style="height: 28px; margin-right: 7px;" src="https://cdn.awsli.com.br/2544/2544943/arquivos/blog.svg">
                                <strong class="titulo text-dark">Blog</strong>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <span class="titulo fw-semibold">Institucional</span>
                    <ul class="mt-3">
                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_pages';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->execute();

                            // Recuperar os pages
                            $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                // Loop através dos pages e exibir todas as colunas
                                foreach ($pages as $page) {
                                    echo "<li>";
                                    echo "<a href='" . INCLUDE_PATH_LOJA . "atendimento/" . $page['link'] . "'>" . $page['name'] . "</a>";
                                    echo "</li>";
                                }
                            }
                        ?>
                    </ul>
                </div>

                <div class="col-md-3">
                    <span class="titulo fw-semibold">Atendimento</span>
                    <ul class="contact mt-3">
                        <li class="<?php echo ($phone == "") ? "d-none" : ""; ?>">
                            <i class='bx bxs-phone' ></i> Telefone: 
                            <a href="tel:<?php echo $phone; ?>">
                                <?php echo $phone; ?>
                            </a>
                        </li>
                        <li class="tel-whatsapp <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
                            <i class="bx bxl-whatsapp"></i> Whatsapp: 
                            <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank">
                                <?php echo $whatsapp; ?>
                            </a>
                        </li>
                        <li class="<?php echo ($email == "") ? "d-none" : ""; ?>">
                            <i class="bx bxs-envelope"></i> E-mail: 
                            <a href="mailto:<?php echo $email; ?>">
                                <?php echo $email; ?>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="location">
                        <div class="title-location mb-2">
                            <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="14.593" height="20.008" viewBox="0 0 14.593 20.008">
                                <g id="placeholder-for-map" transform="translate(0.5 0.5)">
                                    <path id="Path_6073" data-name="Path 6073" d="M80.353,0A6.8,6.8,0,0,0,73.323,6.8c0,4.347,4.172,7.5,6.511,12.04a.321.321,0,0,0,.57,0c2.115-4.083,5.731-6.821,6.4-10.754A6.893,6.893,0,0,0,80.353,0Zm-.235,10.35A3.559,3.559,0,1,1,83.677,6.8,3.559,3.559,0,0,1,80.118,10.354Z" transform="translate(-73.323 0)" fill="none" stroke="#838694" stroke-width="1.5"></path>
                                </g>
                            </svg>
                            <span class="fw-semibold me-1">Endereço</span>
                            <a href="https://www.google.com/maps/place/<?php echo $endereco . ", " . $numero . " - " . $bairro . ", " . $cidade . " - " . $estado . ", " . $cep; ?>" target="_blank">Ver mapa</a>
                        </div>
                        <p class="small lh-sm">
                            <?php echo $endereco . ", " . $numero . " - " . $bairro . ", " . $cidade . " - " . $estado . ", " . $cep; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="payment-seals mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-9 seals">
                    <span class="titulo text-dark">Selos</span>
                    <ul>
                        <li>
                            <img loading="lazy" src="https://cdn.awsli.com.br/production/static/img/struct/stamp_encryptssl.png" alt="Site Seguro">
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; font-size: 11px; display: block !important; margin-bottom: 2rem;">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-12 text-center" style="min-height: 20px; width: 100%;">
                    <p style="margin-bottom: 0;">
                        <?php echo ($razao_social !== "") ? $razao_social : $loja; ?> - <?php echo ($razao_social !== "") ? "CNPJ: " : "CPF: "; ?> <?php echo $cpf_cnpj; ?> &#169; Todos os direitos reservados. <?php echo date("Y") ?>
                    </p>
                    <a href="https://dropidigital.com.br" target="_blank">
                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo DropiDigital" style="width: 150px;">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <!-- Inclua o JavaScript do Bootstrap (certifique-se de que jQuery esteja incluído) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Mostrar menu categorias -->
    <script>
        $(document).ready(function() {
            // Variável para controlar o estado do botão
            let botaoAtivo = false;

            // Função para alternar a classe do botão e a classe "minha-classe"
            function alternarClasse() {
                botaoAtivo = !botaoAtivo; // Inverte o estado do botão
                $("#show-categories i").toggleClass("bx-menu bx-x"); // Alterna entre as classes
                $(".categories").toggleClass("nav-show-categories"); // Alterna a classe "minha-classe"
            }

            // Adiciona um ouvinte de evento de clique ao botão
            $("#show-categories").click(function() {
                alternarClasse();
            });
        });
    </script>

    <script>
        // Ative o carrossel
        var meuCarrossel = new bootstrap.Carousel(document.getElementById('myCarousel'), {
            interval: 2000, // Tempo de exibição de cada slide em milissegundos (opcional)
            wrap: true // Se o carrossel deve voltar ao primeiro slide após o último (opcional)
        });
    </script>
    <script>
        var carouselProdutos = new bootstrap.Carousel(document.getElementById('carouselProdutos'), {
            interval: 3000, // Tempo de exibição de cada produto em milissegundos (opcional)
            wrap: true // Se o carrossel deve voltar ao primeiro produto após o último (opcional)
        });
    </script>
    <script>
        var blogCarrossel = new bootstrap.Carousel(document.getElementById('blogCarousel'), {
            interval: 2000, // Tempo de exibição de cada slide em milissegundos (opcional)
            wrap: true // Se o carrossel deve voltar ao primeiro slide após o último (opcional)
        });
    </script>

    <!-- Tooltips -->
    <script>
        // Ative todos os tooltips na página
        $(document).ready(function(){
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    <!-- Nav Categorias -->
    <script>
        $(document).ready(function () {
            $('.categorias li').hover(
                function () {
                    $(this).find('.subcategorias').fadeIn(200);
                },
                function () {
                    $(this).find('.subcategorias').fadeOut(200);
                }
            );
        });
    </script>

    <!-- Feed Instagram -->
    <script type="text/javascript" src="js/instafeed.min.js"></script>

    <script src="js/jquery.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>

    <script type="text/javascript">
        var feed = new Instafeed({
            accessToken: "<?php echo $token_instagram; ?>",
            limit: 8,
            template: '<div class="item"><i class="bx bxl-instagram"></i><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
            after: function () {
                $('.owl-carousel').owlCarousel({
                    loop: true,
                    nav: true,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 3
                        },
                        1000: {
                            items: 5
                        }
                    }
                });
            }
        });

        feed.run();

        // Função para obter o nome de usuário e exibi-lo
        function getUsername() {
            const accessToken = "<?php echo $token_instagram; ?>";
            const profileUrl = `https://graph.instagram.com/v12.0/me?fields=username&access_token=${accessToken}`;

            fetch(profileUrl)
                .then(response => response.json())
                .then(data => {
                    const username = data.username;
                    // Exiba o nome de usuário na div com o id "username"
                    document.getElementById('username').innerHTML = "<a href='https://www.instagram.com/" + username + "/' target='_blank'>@" + username + "</a>";
                })
        }

        // Chame a função para obter e exibir o nome de usuário
        getUsername();
    </script>

    <!-- Ocultar categorias -->
    <script>
        const categories = document.querySelector(".categories");
        const showButton = document.querySelector("#show-categories");

        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 100) {
                categories.classList.add("scroll");

                showButton.classList.remove("d-none");
                showButton.classList.add("d-flex");
            } else {
                categories.classList.remove("scroll");
                
                showButton.classList.remove("d-flex");
                showButton.classList.add("d-none");
            }
        });
    </script>

    <!-- Back to top -->
    <script>
        const toTop = document.querySelector(".to-top");

        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 100) {
                toTop.classList.add("scroll");
            } else {
                toTop.classList.remove("scroll");
            }
        });
    </script>
</body>
</html>