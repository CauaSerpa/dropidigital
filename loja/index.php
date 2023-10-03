<?php
    // Pega o dominio da pagina
    $dominioCompleto = $_SERVER['HTTP_HOST'];
    
    // Divide o domínio completo em partes usando o ponto como separador
    $partesDoDominio = explode('.', $dominioCompleto);
    
    // O subdomínio estará na primeira parte do array (índice 0)
    $subdominio = $partesDoDominio[0];
    
    // Verifique se há www como subdomínio padrão e o remova, se presente
    if ($subdominio === 'www') {
        $subdominio = '';
    }

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
    $sql = "SELECT * FROM $tabela WHERE id = :id";

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
        $user_id = $resultado['id'];
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
    $sql = "SELECT * FROM $tabela WHERE url = :url";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':url', $subdominio, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $shop_id = $resultado['id'];
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
        $phone = $resultado['phone'];
        $top_highlight_bar = $resultado['top_highlight_bar'];
        $top_highlight_bar_location = $resultado['top_highlight_bar_location'];
        $top_highlight_bar_text = $resultado['top_highlight_bar_text'];
        $center_highlight_images = $resultado['center_highlight_images'];
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
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
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
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
<style>
    a:hover
    {
        color: inherit !important;
        text-decoration: underline;
    }
    a.nav-link:hover,
    a.btn:hover
    {
        text-decoration: none !important;
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
                <a class="navbar-brand" href="#"><?php echo ($logo !== "") ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" alt="Logo ' . $loja . '" style="width: 150px;">' : $loja; ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex me-3" role="search">
                        <input class="form-control py-1 px-3" type="search" placeholder="Buscar..." aria-label="Search" style="border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius);">
                        <button class="btn btn-outline-success" type="submit" style="border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0;">
                            <i class='bx bx-search-alt-2'></i>
                        </button>
                    </form>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item me-2">
                            <a class="nav-link d-flex align-items-center active" aria-current="page" href="#">
                                <i class='bx bxs-discount fs-1 me-2' ></i>
                                <small class="fw-semibold lh-sm">Retire seu cupom<br>de desconto</small>
                            </a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="nav-link d-flex align-items-center active" aria-current="page" href="#">
                                <i class='bx bxl-whatsapp fs-1 me-2' ></i>
                                <small class="fw-semibold lh-sm">Central de<br>Suporte</small>
                            </a>
                        </li>
                    </ul>
                    <span class="navbar-text">
                        Navbar text with an inline element
                    </span>
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
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip para produtos">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Pricing</a>
                        </li>
                        










                        <style>
                            .categorias {
                                list-style: none;
                            }

                            .categorias li {
                                position: relative;
                                margin-right: 20px;
                            }

                            .subcategorias {
                                width: 400px;
                                display: none;
                                position: absolute;
                                top: 100%;
                                left: 0;
                                background-color: #fff;
                                padding: 10px;
                                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                                z-index: 1;
                            }

                            .categorias li:hover .subcategorias {
                                display: flex;
                            }

                            /* Estilo do tooltip */
                            .subcategorias li {
                                margin: 5px;
                            }
                        </style>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Categoria</a>
                            <ul class="subcategorias">
                                <div style="display: flex; flex-direction: column; margin-right: 5px;">
                                    <li><a href="#">Subcategoria 1</a></li>
                                    <li><a href="#">Subcategoria 2</a></li>
                                    <li><a href="#">Subcategoria 3</a></li>
                                    <li><a href="#">Subcategoria 4</a></li>
                                    <li><a href="#">Subcategoria 5</a></li>
                                    <li><a href="#">Subcategoria 6</a></li>
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <li><a href="#">Subcategoria 7</a></li>
                                    <li><a href="#">Subcategoria 8</a></li>
                                    <li><a href="#">Subcategoria 9</a></li>
                                    <li><a href="#">Subcategoria 10</a></li>
                                    <!-- Adicione mais subcategorias aqui -->
                                </div>
                            </ul>
                        </li>
                    </ul>
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

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $id);
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
                } else {
                    echo "Nenhum ID encontrado.";
                }
            ?>





            </ol>

            <!-- Slides (itens do carrossel) -->
            <div class="carousel-inner">











                <?php
                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $shop_id);
                    $stmt->execute();

                    // Recuperar os resultados
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Inicialize uma variável de controle
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
                            $active = $primeiroElemento ? 'active' : '';

                            echo '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '">';
                            echo    '<div class="carousel-item ' . $active . '">';
                            echo        '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] . '" alt="' . $banner['name'] . '" class="w-100" style="height: 535px; object-fit: cover;">';
                            echo    '</div>';
                            echo '</a>';

                            // Marque que o primeiro elemento foi processado
                            $primeiroElemento = false;
                        }
                    }
                ?>
































            </div>

            <!-- Controles (setas de navegação) -->
            <a class="carousel-control-prev" href="#myCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#myCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>

        <div class="container highlight-center">
            <div class="row px-4">
                <?php
                    // Valores possíveis para os checkboxes
                    $valoresPossiveis = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];

                    // Valores do banco de dados (simulando)
                    $valoresDoBanco = $center_highlight_images;

                    // Convertendo a string do banco de dados em um array
                    $valoresArray = explode(", ", $valoresDoBanco);

                    // Array de URLs de imagens correspondentes aos valores
                    $imagens = [
                        "1" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "2" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "3" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "4" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "5" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "6" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "7" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "8" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "9" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "10" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png"
                    ];

                    // Função para marcar os checkboxes
                    function marcarCheckbox1($valor, $valoresArray, $imagens) {
                        if (in_array($valor, $valoresArray)) {
                            echo "<div class='col-sm-4 highlight'>";
                            echo    "<div class='d-flex justify-content-center'>";
                            echo        "<img src='{$imagens[$valor]}' alt='Tarja {$valor}'>";
                            echo    "</div>";
                            echo "</div>";
                        }
                    }

                    // Criando os checkboxes e marcando aqueles cujos valores correspondem aos do banco de dados
                    foreach ($valoresPossiveis as $valor) {
                        marcarCheckbox1($valor, $valoresArray, $imagens);
                    }
                ?>
            </div>
        </div>

        <div class="container">
            <div class="row p-4">
                <div class="col-sm-4">
                    <div class="card">
                        <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card">
                        <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                    </div>
                </div>
            </div>
        </div>

        <div id="carouselCategorias" class="container carousel slide" data-bs-ride="carousel">
            <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
                <h4 class="mb-3">Navegue por Departamento</h4>
                <div style="width: 100px; height: 5px; background: #000;"></div>
            </div>    
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row p-4">
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row p-4">
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="card border-0">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top rounded-circle" alt="Produto 1">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Produto 1</h5>
                                </div>
                            </div>
                        </div>
                        <!-- Adicione mais produtos aqui -->
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#carouselCategorias" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#carouselCategorias" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>

        <div id="carouselProdutos" class="container carousel slide" data-bs-ride="carousel">
            <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
                <div class="d-flex justify-content-center">
                    <h4 class="mb-3 me-3">Super Ofertas</h4>
                    <p class="text-black-50">Produtos com preços imperdíveis</p>
                </div>
                <div style="width: 100px; height: 5px; background: #000;"></div>
            </div>

            <ol class="carousel-indicators">
                <li data-bs-target="#carouselProdutos" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#carouselProdutos" data-bs-slide-to="1"></li>
                <!-- Adicione mais indicadores conforme necessário -->
            </ol>
        
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row p-4">
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row p-4">
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="card">
                                <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                <div class="card-body">
                                    <h5 class="card-title">Produto 1</h5>
                                    <p class="card-text">Descrição do Produto 1.</p>
                                    <a href="#" class="btn btn-primary">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                        <!-- Adicione mais produtos aqui -->
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#carouselProdutos" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#carouselProdutos" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>

        <div class="container">
            <div class="row p-4">
                <div class="col-sm-6">
                    <div class="card">
                        <img src="../assets/images/background/video.jpg" class="card-img-top" alt="Produto 1">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <img src="../assets/images/background/video.jpg" class="card-img-top" alt="Produto 1">
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row p-4">
                <div class="col-sm-12">
                    <div class="card">
                        <div id="video-display" class="d-flex justify-content-center">
                            <div class="d-flex justify-content-center">
                                <?php
                                    // Função para extrair o código do vídeo do URL do YouTube
                                    function getYoutubeEmbedCode($url) {
                                        // Verifica se o URL é um link válido do YouTube
                                        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                            $videoCode = $matches[1];

                                            // Gera o código de incorporação
                                            $embedCode = '<iframe width="1240" height="527" src="https://www.youtube.com/embed/' . $videoCode . '" frameborder="0" allowfullscreen></iframe>';

                                            return $embedCode;
                                        } else {
                                            // URL inválido do YouTube
                                            return 'URL do YouTube inválido.';
                                        }
                                    }

                                    // Exemplo de uso:
                                    $youtubeURL = $video;
                                    $embedCode = getYoutubeEmbedCode($youtubeURL);

                                    if ($embedCode !== 'URL do YouTube inválido.') {
                                        echo $embedCode;
                                    } else {
                                        echo 'O URL do YouTube não é válido.';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="carouselDepoimentos" class="container carousel slide" data-bs-ride="carousel">
            <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
                <div class="d-flex justify-content-center">
                    <h4 class="mb-3 me-3">Vejam o que dizem nossos clientes</h4>
                    <p class="text-black-50">Depoimentos</p>
                </div>
                <div style="width: 100px; height: 5px; background: #000;"></div>
            </div>

            <ol class="carousel-indicators">
                <li data-bs-target="#carouselDepoimentos" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#carouselDepoimentos" data-bs-slide-to="1"></li>
                <!-- Adicione mais indicadores conforme necessário -->
            </ol>
        
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row p-4">
                        <div class="col-sm-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <img src="../assets/images/blog/post-next.jpg" class="rounded-circle mb-2" alt="Produto 1">
                                    <p class="card-title">Produto 1</p>
                                    <p class="card-text text-black-50 mb-2">"Descrição do Produto 1."</p>
                                    <span class="dep-stars text-warning">
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <img src="../assets/images/blog/post-next.jpg" class="rounded-circle mb-2" alt="Produto 1">
                                    <p class="card-title">Produto 1</p>
                                    <p class="card-text text-black-50 mb-2">"Descrição do Produto 1."</p>
                                    <span class="dep-stars text-warning">
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star'></i>
                                        <i class='bx bxs-star-half'></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <img src="../assets/images/blog/post-next.jpg" class="rounded-circle mb-2" alt="Produto 1">
                                    <p class="card-title">Produto 1</p>
                                    <p class="card-text text-black-50 mb-2">"Descrição do Produto 1."</p>
                                    <span class="dep-stars text-warning">
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star-half'></i>
                                        <i class='bx bx-star'></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row p-4">
                        <div class="col-sm-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <img src="../assets/images/blog/post-next.jpg" class="rounded-circle mb-2" alt="Produto 1">
                                    <p class="card-title">Produto 1</p>
                                    <p class="card-text text-black-50 mb-2">"Descrição do Produto 1."</p>
                                    <span class="dep-stars text-warning">
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card border-0">
                                <div class="card-body text-center">
                                    <img src="../assets/images/blog/post-next.jpg" class="rounded-circle mb-2" alt="Produto 1">
                                    <p class="card-title">Produto 1</p>
                                    <p class="card-text text-black-50 mb-2">"Descrição do Produto 1."</p>
                                    <span class="dep-stars text-warning">
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star' ></i>
                                        <i class='bx bxs-star-half'></i>
                                        <i class='bx bx-star' ></i>
                                        <i class='bx bx-star'></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Adicione mais produtos aqui -->
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev" href="#carouselDepoimentos" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#carouselDepoimentos" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>

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
            <div class="d-flex justify-content-between p-4">
                <p class="d-flex align-items-center fs-4">
                    <i class='bx bx-mail-send fs-2 me-2'></i>
                    Receba Ofertas e Novidades de nossa loja
                </p>
                <form class="d-flex" role="text">
                    <input class="form-control py-1 px-3 me-2" type="text" placeholder="E-mail" aria-label="E-mail">
                    <button class="btn btn-dark" type="submit" style="width: 270px;">
                        Quero receber!
                    </button>
                </form>
            </div>
        </div>

        <a href="#" class="to-top btn btn-dark p-2 rounded-1">
            <i class='bx bx-chevron-up fs-2' ></i>
        </a>

        <a href="#" class="whatsapp-button btn btn-dark p-2 rounded-1">
            <i class='bx bxl-whatsapp fs-2'></i>
        </a>
    </main>

    <!-- Footer -->
    <footer id="rodape" style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; display: block !important; margin-bottom: 2rem;">
    <div class="container">
                <h1 class="logo text-primary">
                    <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/" title="Alpha Shoes Max">
                        <?php echo ($logo !== "") ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" alt="Logo ' . $loja . '" style="width: 250px;">' : $loja; ?>
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

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop_id);
                        $stmt->execute();

                        // Recuperar os resultados
                        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($stmt->rowCount() > 0) {
                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($resultados as $usuario) {
                                echo "<li>";
                                echo "<a href='https://" . $subdominio . "dropidigital.com.br/" . $usuario['link'] . "'>" . $usuario['name'] . "</a>";
                                echo "</li>";
                            }
                        }
                    ?>









                    <li class="mt-2" id="menu_blog">
                        <a href="/pagina/artigos.html" class="btn btn-light">
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

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop_id);
                        $stmt->execute();

                        // Recuperar os resultados
                        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($stmt->rowCount() > 0) {
                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($resultados as $usuario) {
                                echo "<li>";
                                echo "<a href='https://" . $subdominio . "dropidigital.com.br/" . $usuario['link'] . "'>" . $usuario['name'] . "</a>";
                                echo "</li>";
                            }
                        }
                    ?>



                </ul>
            </div>

            <div class="col-md-3">
                <span class="titulo fw-semibold">Atendimento</span>
                <ul class="contact mt-3">
                    <li>
                        <i class='bx bxs-phone' ></i> Telefone: 
                        <a href="tel:<?php echo $phone; ?>">
                            <?php echo $phone; ?>
                        </a>
                    </li>
                    <li class="tel-whatsapp">
                        <i class="bx bxl-whatsapp"></i> Whatsapp: 
                        <a href="https://api.whatsapp.com/send?phone=<?php echo $phone; ?>" target="_blank">
                            <?php echo $phone; ?>
                        </a>
                    </li>
                    <li>
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
                    Singularis Tecnologia Web LTDA - CNPJ: 32.155.999/0001-34 © Todos os direitos reservados. 2023
                </p>
                <a href="<?php echo INCLUDE_PATH; ?>" target="_blank">
                    <img src="../assets/images/logos/logo-one.png" alt="Logo DropiDigital" style="width: 150px;">
                </a>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <!-- Inclua o JavaScript do Bootstrap (certifique-se de que jQuery esteja incluído) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 100) {
                categories.classList.add("scroll");
            } else {
                categories.classList.remove("scroll");
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