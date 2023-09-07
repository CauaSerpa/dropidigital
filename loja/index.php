<?php
    //Url Amigavel
    $url = isset($_GET['url']) ? $_GET['url'] : 'painel';

    session_start();
    ob_start();
    include('../painel/config.php');

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
<html lang="pt-BR">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Sua Loja</title>
    <!--Favicon-->
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH; ?>assets/images/favicon.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
</head>
<body>
    <header class="header fixed-top">
        <div class="stripe text-center text-light" style="background-color: rgb(35, 35, 35);"><i class='bx bxs-discount me-2' ></i>Toda a loja com <b>descontos de até 50%</b></div>
        <nav class="navbar bg-white navbar-expand-lg border-bottom border-body" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <a class="navbar-brand" href="#">Logo</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex me-3" role="search">
                        <input class="form-control py-1 px-3" type="search" placeholder="Search" aria-label="Search" style="border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius);">
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
        <nav class="navbar bg-white navbar-expand-lg border-bottom border-body" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <a class="navbar-brand" href="#">Navbar w/ text</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex" role="search">
                        <input class="form-control py-1 px-3 me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
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
                    <span class="navbar-text">
                        Navbar text with an inline element
                    </span>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div id="myCarousel" class="carousel slide" data-bs-ride="carousel" style="margin-top: 170px;">
            <!-- Indicators (pontos de navegação) -->
            <ol class="carousel-indicators">
                <li data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#myCarousel" data-bs-slide-to="1"></li>
                <li data-bs-target="#myCarousel" data-bs-slide-to="2"></li>
            </ol>

            <!-- Slides (itens do carrossel) -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="../assets/images/background/banner.jpg" alt="Imagem 1">
                </div>
                <div class="carousel-item">
                    <img src="../assets/images/background/banner.jpg" alt="Imagem 1">
                </div>
                <div class="carousel-item">
                    <img src="../assets/images/background/banner.jpg" alt="Imagem 1">
                </div>
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
                        <img src="../assets/images/background/video.jpg" class="card-img-top" alt="Produto 1">
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

        <div>
            <div class="justify-content-center mb-3" style="text-align: -webkit-center;">
                <div class="d-flex align-items-center justify-content-center">
                    <p class="d-flex align-items-center fs-4 mb-3 me-3">
                        <i class='bx bxl-instagram fs-1 me-2' ></i>
                        Siga nosso instagram
                    </p>
                    <h4>
                        @dropidigital
                    </h4>
                </div>
                <div style="width: 100px; height: 5px; background: #000;"></div>
            </div>

            <div id="carouselInstagram" class="container carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row p-4">
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row p-4">
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="card">
                                    <img src="../assets/images/shop/thumb1.jpg" class="card-img-top" alt="Produto 1">
                                </div>
                            </div>
                            <!-- Adicione mais produtos aqui -->
                        </div>
                    </div>
                </div>

                <a class="carousel-control-prev" href="#carouselInstagram" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </a>
                <a class="carousel-control-next" href="#carouselInstagram" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </a>
            </div>
        </div>
        
        <div class="container">
            <div class="d-flex justify-content-between p-4">
                <p class="d-flex align-items-center fs-4">
                    <i class='bx bx-mail-send fs-2 me-2'></i>
                    Receba Ofertas e Novidades de nossa loja
                </p>
                <form class="d-flex" role="text">
                    <input class="form-control py-1 px-3 me-2" type="text" placeholder="E-mail" aria-label="E-mail">
                    <button class="btn btn-dark" type="submit" style="width: 300px;">
                        Quero receber!
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer id="rodape" style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; display: block !important; margin-bottom: 2rem;">
    <div class="container">
                <h1 class="logo text-primary">
                    <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/" title="Alpha Shoes Max">
                        <img src="https://cdn.awsli.com.br/400x300/2544/2544943/logo/logo-83b1c4f692.png" alt="Alpha Shoes Max">
                    </a>
                </h1>
        <div class="row">
            <div class="col-md-4" style="margin-bottom: 4rem;">
                <span class="titulo fw-semibold">Sobre a loja</span>
                <p class="mt-3">Conheça o Alpha Shoes Max, o tema mais completo desenvolvido para a Loja Integrada. São mais de 20 incríveis funcionalidades para que sua loja fique, além de linda, ultra vendedora!</p>
                <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/sobre-este-tema.html" class="btn btn-dark more mb-4">Saiba mais</a>
                <div class="lista-redes">
                    <h3 class="fw-semibold">Siga-nos</h3>
                    <ul class="d-flex">
                        <li class="me-2">
                            <a href="https://twitter.com/lojaintegrada" class="btn btn-dark fs-6" target="_blank" aria-label="Siga nos no Twitter">
                                <i class='bx bxl-twitter' ></i>
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="https://youtube.com.br/lojaintegrada" class="btn btn-dark fs-6" target="_blank" aria-label="Siga nos no YouTube">
                                <i class='bx bxl-youtube' ></i>
                            </a>
                        </li>
                        <li class="me-2">
                            <a href="https://instagram.com/lojaintegrada" class="btn btn-dark fs-6" target="_blank" aria-label="Siga nos no Instagram">
                                <i class='bx bxl-instagram' ></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-2">
                <span class="titulo fw-semibold">Categorias</span>
                <ul class="total-itens_8 mt-3">
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/botas">Botas</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/chinelos">Chinelos</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/mocassim">Mocassim</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/sandalias">Sandálias</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/sapatenis">Sapatênis</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/scarpin">Scarpin</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/social">Social</a>
                    </li>
                    <li>
                        <a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/tenis">Tênis</a>
                    </li>
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
                    <li id="liRodape_faleconosco"><a href="#modalContato" data-toggle="modal" data-target="#modalContato">Fale Conosco</a></li>
                    <li id="liRodape_sobreestetema"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/sobre-este-tema.html">Sobre este Tema</a></li>
                    <li id="liRodape_artigos"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/artigos.html">Artigos</a></li>
                    <li id="liRodape_formasdeenvio"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/formas-de-envio.html">Formas de Envio</a></li>
                    <li id="liRodape_formasdepagamento"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/formas-de-pagamento.html">Formas de Pagamento</a></li>
                    <li id="liRodape_politicadetroca"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/politica-de-troca.html">Política de Troca</a></li>
                    <li id="liRodape_segurancaeprivacidade"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/seguranca-e-privacidade.html">Segurança e Privacidade</a></li>
                    <li id="liRodape_verartigo"><a href="https://alpha-shoes-max-demo.lojaintegrada.com.br/pagina/ver-artigo.html">Ver Artigo</a></li>
                </ul>
            </div>

            <div class="col-md-3">
                <span class="titulo fw-semibold">Atendimento</span>
                <ul class="contact mt-3">
                    <li>
                        <a href="tel:(11) 1234-5678">
                            <i class='bx bxs-phone' ></i> Telefone: (11) 1234-5678
                        </a>
                    </li>
                    <li class="tel-whatsapp">
                        <a href="https://api.whatsapp.com/send?phone=551112345678" target="_blank">
                            <i class="bx bxl-whatsapp"></i> Whatsapp: (11) 1234-5678
                        </a>
                    </li>
                    <li class="tel-skype">
                        <a href="skype:meu.skype">
                            <i class="bx bxl-skype"></i> Skype: meu.skype
                        </a>
                    </li>
                    <li>
                        <a href="mailto:email@meuemail.com.br">
                            <i class="bx bxs-envelope"></i> E-mail: email@meuemail.com.br
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="location">
                    <div class="title-location mb-2">
                        <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="14.593" height="20.008" viewBox="0 0 14.593 20.008">
                            <g id="placeholder-for-map" transform="translate(0.5 0.5)">
                                <path id="Path_6073" data-name="Path 6073" d="M80.353,0A6.8,6.8,0,0,0,73.323,6.8c0,4.347,4.172,7.5,6.511,12.04a.321.321,0,0,0,.57,0c2.115-4.083,5.731-6.821,6.4-10.754A6.893,6.893,0,0,0,80.353,0Zm-.235,10.35A3.559,3.559,0,1,1,83.677,6.8,3.559,3.559,0,0,1,80.118,10.354Z" transform="translate(-73.323 0)" fill="none" stroke="#000" stroke-width="1"></path>
                            </g>
                        </svg>
                        <span class="fw-semibold me-1">Endereço</span>
                        <a href="#" id="viewMap" class="link text-dark">Ver mapa</a>
                    </div>

                    <p class="small lh-sm">
                        Avenida Paulista - Bela Vista, São Paulo - SP, 01310-000
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
                <img src="../assets/images/logos/logo-one.png" alt="Logo DropiDigital" style="width: 150px;">
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
</body>
</html>