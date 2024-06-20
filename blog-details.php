<?php
    include('./config.php');

    // // Obtém a URL atual
    // $urlPath = $_GET['url'];

    // // Remove qualquer string de consulta, se houver
    // $urlPath = parse_url($urlPath, PHP_URL_PATH);

    // // Divide a URL em partes
    // $urlParts = explode('/', trim($urlPath, '/'));

    // // Verifica se há partes suficientes na URL
    // if (count($urlParts) >= 2) {
    //     $link = $urlParts[1]; // O nome do arquivo é a segunda parte
    // } else {
    //     $_SESSION['msg'] = "<p class='red'>Nenhum Artigo encontrado!</p>";
    //     header('Location: ' . INCLUDE_PATH . 'blog');
    //     exit();
    // }

    $link = $_GET['url'];
    
    // Tabela que sera feita a consulta
    $tabela = "tb_home";

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
    $home = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($home) {
        // Remove tudo que não seja dígito ou o sinal de mais (+)
        $formatted_number = preg_replace('/[^\d+]/', '', $home['whatsapp']);

        // Cria link com o numero formatado
        $whatsapp_link = "https://api.whatsapp.com/send?phone=" . $formatted_number;

        // Tabela que sera feita a consulta
        $tabela = "tb_blog";

        // Consulta SQL
        $sql = "SELECT * FROM $tabela WHERE link = :link LIMIT 1";

        // Preparar a consulta
        $stmt = $conn_pdo->prepare($sql);

        // Vincular o valor do parâmetro
        $stmt->bindParam(':link', $link, PDO::PARAM_STR);

        // Executar a consulta
        $stmt->execute();

        // Obter o resultado como um array associativo
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            // Tabela que sera feita a consulta
            $tabela = "tb_users";

            // Consulta SQL
            $sql = "SELECT (name) FROM $tabela WHERE id = :author";

            // Preparar a consulta
            $stmt = $conn_pdo->prepare($sql);

            // Vincular o valor do parâmetro
            $stmt->bindParam(':author', $article['author'], PDO::PARAM_INT);

            // Executar a consulta
            $stmt->execute();

            // Obter o resultado como um array associativo
            $article['author'] = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

            // Array com os nomes dos meses em português
            $meses = array(
                1 => 'janeiro',
                2 => 'fevereiro',
                3 => 'março',
                4 => 'abril',
                5 => 'maio',
                6 => 'junho',
                7 => 'julho',
                8 => 'agosto',
                9 => 'setembro',
                10 => 'outubro',
                11 => 'novembro',
                12 => 'dezembro'
            );

            // Cria um objeto DateTime a partir da data original
            $data = new DateTime($article['date_create']);

            // Extrai o dia, o mês e o ano da data
            $dia = $data->format('d');
            $mes = (int)$data->format('m');
            $ano = $data->format('Y');

            // Converte o mês para português
            $mesPortugues = $meses[$mes];

            // Formata a data no formato desejado 'd de F de Y'
            $article['date_create'] = sprintf('%d de %s de %d', $dia, $mesPortugues, $ano);
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="title" content="Dropi Digital | Empreender na Internet">
    <meta name="description" content="Crie seu site em 5 minutos e venda seus serviços e produtos na Internet. Assuma o controle de sua presença online com o construtor de sites da Dropi Digital. Aprenda como construir seu site sem complicações. Comece agora!">
    <meta property="og:image" content="<?= INCLUDE_PATH; ?>assets/images/logos/<?= $home['logo']; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title -->
    <title>Dropi Digital | Empreender na Internet</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="<?= INCLUDE_PATH; ?>assets/images/favicon.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Flaticon -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/flaticon.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/fontawesome-5.14.0.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/bootstrap.min.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/magnific-popup.min.css">
    <!-- Nice Select -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/nice-select.min.css">
    <!-- Animate -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/animate.min.css">
    <!-- Slick -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/slick.min.css">
    <!-- Main Style -->
    <link rel="stylesheet" href="<?= INCLUDE_PATH; ?>assets/css/style.css">
    
</head>
<body>
    <div class="page-wrapper">

        <!-- Preloader -->
        <div class="preloader"><div class="custom-loader"></div></div>

        <!-- main header -->
        <header class="main-header header-one menu-white">

           <div class="header header-top-wrap bgc-gray">
           </div>
           
            <!--Header-Upper-->
            <div class="header-upper bgc-black">
                <div class="container clearfix">

                    <div class="header-inner rel d-flex align-items-center">
                        <div class="logo-outer">
                            <div class="logo"><a href="<?php echo INCLUDE_PATH; ?>"><img src="<?= INCLUDE_PATH; ?>assets/images/logos/<?= $home['logo']; ?>" alt="Logo" title="Logo"></a></div>
                        </div>

                        <div class="nav-outer mx-auto clearfix">
                            <!-- Main Menu -->
                            <nav class="main-menu navbar-expand-lg">
                                <div class="navbar-header">
                                   <div class="mobile-logo">
                                       <a href="<?php echo INCLUDE_PATH; ?>">
                                            <img src="<?= INCLUDE_PATH; ?>assets/images/logos/<?= $home['logo']; ?>" alt="Logo" title="Logo">
                                       </a>
                                   </div>
                                   
                                    <!-- Toggle Button -->
                                    <button type="button" class="navbar-toggle" data-bs-toggle="collapse" data-bs-target=".navbar-collapse">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>

                                <div class="navbar-collapse collapse clearfix header-top">









                                    <ul class="navigation clearfix">
                                        <li><i class="far fa-envelope"></i> <a href="mailto:<?= $home['email']; ?>"><?= $home['email']; ?></a></li>
                                        <li><i class="fab fa-whatsapp"></i><a href="<?= $whatsapp_link; ?>" target="_blank"><?= $home['whatsapp']; ?></a></li>
                                        <li class="for-none"><i class="far fa-clock"></i><?= $home['location']; ?></li>
                                        <li>
                                            <div class="social-style-one">
                                                <a href="<?= $home['facebook']; ?>"><i class="fab fa-facebook-f"></i></a>
                                                <a href="<?= $home['twitter']; ?>"><i class="fab fa-twitter"></i></a>
                                                <a href="<?= $home['instagram']; ?>"><i class="fab fa-instagram"></i></a>
                                                <a href="<?= $home['linkedin']; ?>"><i class="fab fa-linkedin-in"></i></a>
                                            </div>
                                        </li>
                                    </ul>









                                </div>      

                            </nav>
                            <!-- Main Menu End-->
                        </div>
                        
                        <!-- Menu Button -->
                        <div class="menu-btns">
                           <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="theme-btn">Login <i class="fas fa-long-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Header Upper-->
        </header>
       
        
        <!-- Page Banner Start -->
        <section class="page-banner-area bgs-cover py-135 rpy-100" style="background-image: url(<?php echo INCLUDE_PATH; ?>assets/images/background/banner.jpg);">
            <div class="container">
                <div class="banner-inner text-white text-center">
                    <h1 class="page-title wow fadeInUp delay-0-2s">Detalhes do Blog</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-5 wow fadeInUp delay-0-4s">
                            <li class="breadcrumb-item me-1"><a href="<?php echo INCLUDE_PATH; ?>">Início</a></li>
                            <li class="breadcrumb-item me-1"><a href="<?php echo INCLUDE_PATH; ?>blog.php">Blog</a></li>
                            <li class="breadcrumb-item active">Detalhes do Blog</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </section>
        <!-- Page Banner End -->
        
        
        <!-- Detalhes do Blog Area start -->
        <section class="blog-details-area py-130 rpy-100">
            <div class="container">
                <div class="row gap-60">
                    <div class="col-lg-8">
                        <div class="blog-details-content wow fadeInUp delay-0-2s">
                            <div class="image mb-40">
                                <img src="<?= INCLUDE_PATH_DASHBOARD . "back-end/admin/articles/" . $article['id'] . "/" . $article['image']; ?>" alt="Blog Single" style="width: 850px; height: 565px; object-fit: cover;">
                            </div>
                            <div class="blog-meta-two pb-15">
                                <a class="tag" href="blog.html"><?= $article['tag']; ?></a>
                                <a class="author" href="blog.html"><?= $article['author']; ?></a>
                                <a class="date" href="#"><i class="far fa-calendar-alt"></i> <?= $article['date_create']; ?></a>
                            </div>
                            <div class="title mb-20">
                                <h3><?= $article['name']; ?></h3>
                            </div>
                            <?= $article['content']; ?>
                        </div>
                        <hr class="mt-50">
                        <div class="tag-share pt-25 pb-55 wow fadeInUp delay-0-2s">
                            <div class="item">
                                <h5>Tags</h5>
                                <div class="tag-coulds">
                                    <a href="blog.html">Course</a>
                                    <a href="blog.html">Design</a>
                                    <a href="blog.html">marketing</a>
                                </div>
                            </div>
                            <div class="item">
                                <h5>Share :</h5>
                                <div class="social-style-three">
                                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#"><i class="fab fa-twitter"></i></a>
                                    <a href="#"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="admin-comment mb-40 wow fadeInUp delay-0-2s">
                            <div class="comment-body">
                                <div class="author-thumb">
                                    <img src="assets/images/blog/admin-author.jpg" alt="Author">
                                </div>
                                <div class="content">
                                    <h4><?= $article['author']; ?></h4>
                                    <p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam pehiles molestiae consequatur vel illum qui dolorem eum fugiat quo voluptas</p>
                                    <div class="social-style-three">
                                        <a href="contact.html"><i class="fab fa-facebook-f"></i></a>
                                        <a href="contact.html"><i class="fab fa-twitter"></i></a>
                                        <a href="contact.html"><i class="fab fa-instagram"></i></a>
                                        <a href="contact.html"><i class="fab fa-behance"></i></a>
                                        <a href="contact.html"><i class="fab fa-dribbble"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="next-prev-post py-40 wow fadeInUp delay-0-2s">
                            <div class="post-item">
                                <div class="image">
                                    <img src="assets/images/blog/post-prev.jpg" alt="Post">
                                </div>
                                <div class="content">
                                    <h5><a href="blog-details.html">Build Group Chat Apps Vanilla Twilio Node</a></h5>
                                    <span class="date">
                                        <i class="far fa-calendar-alt"></i>
                                        <a href="#">25 June 2022</a>
                                    </span>
                                </div>
                            </div>
                            <div class="post-item">
                                <div class="image">
                                    <img src="assets/images/blog/post-next.jpg" alt="Post">
                                </div>
                                <div class="content">
                                    <h5><a href="blog-details.html">Expand Horiz to Desktop Wall Edition See</a></h5>
                                    <span class="date">
                                        <i class="far fa-calendar-alt"></i>
                                        <a href="#">25 June 2022</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4 class="comment-title mt-75 mb-25">People Comments</h4>
                        <div class="comments">
                            <div class="comment-body wow fadeInUp delay-0-2s">
                                <div class="author-thumb">
                                    <img src="assets/images/blog/comment-author1.jpg" alt="Author">
                                </div>
                                <div class="content">
                                    <h5>John F. Medina <a href="#" class="theme-btn style-two">Reply <i class="fas fa-angle-double-right"></i></a></h5>
                                    <span class="date">25 Feb 2022</span>
                                    <p>On the other hand, we denounce with righteous indignation and dislike men who are beguiled and demoralized by the charms of pleasure of the moment so blinded</p>
                                </div>
                            </div>
                            <div class="comment-body wow fadeInUp delay-0-2s">
                                <div class="author-thumb">
                                    <img src="assets/images/blog/comment-author2.jpg" alt="Author">
                                </div>
                                <div class="content">
                                    <h5>Patrick V. Spears <a href="#" class="theme-btn style-two">Reply <i class="fas fa-angle-double-right"></i></a></h5>
                                    <span class="date">25 Feb 2022</span>
                                    <p>On the other hand, we denounce with righteous indignation and dislike men who are beguiled and demoralized by the charms of pleasure of the moment so blinded</p>
                                </div>
                            </div>
                            <div class="comment-body wow fadeInUp delay-0-2s">
                                <div class="author-thumb">
                                    <img src="assets/images/blog/comment-author3.jpg" alt="Author">
                                </div>
                                <div class="content">
                                    <h5>Kevin S. Larsen <a href="#" class="theme-btn style-two">Reply <i class="fas fa-angle-double-right"></i></a></h5>
                                    <span class="date">25 Feb 2022</span>
                                    <p>On the other hand, we denounce with righteous indignation and dislike men who are beguiled and demoralized by the charms of pleasure of the moment so blinded</p>
                                </div>
                            </div>
                        </div>
                        <form id="comment-form" class="comment-form bgc-lighter mt-80 wow fadeInUp delay-0-2s" name="comment-form" action="#" method="post">
                           <h4>Leave a Message</h4>
                           <p>Have any question? Ready to talk to us! </p>
                            <div class="row mt-15">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" id="full-name" name="full-name" class="form-control" value="" placeholder="Full Name" required="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="email" id="blog-email" name="blog-email" class="form-control" value="" placeholder="Email Address" required="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="message"><i class="fas fa-pencil-alt"></i></label>
                                        <textarea name="message" id="message" class="form-control" rows="4" placeholder="Write Message" required=""></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <div class="form-check">
                                          <input class="form-check-input" type="radio" name="flexRadioDefault" id="agreement">
                                          <label class="form-check-label" for="agreement">
                                            I Agree with the trams & conditions
                                          </label>
                                        </div>
                                        <button type="submit" class="theme-btn style-two">Send Comment <i class="fas fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 col-md-7 col-sm-9">
                        <div class="main-sidebar rmt-75">
                            <div class="widget widget-search wow fadeInUp delay-0-2s">
                                <h4 class="widget-title">Pesquisar</h4>
                                <form action="#" class="default-search-form">
                                    <input type="text" placeholder="Encontre palavras-chave" required>
                                    <button type="submit" class="searchbutton far fa-search"></button>
                                </form>
                            </div>
                            <div class="widget widget-category wow fadeInUp delay-0-4s">
                                <h4 class="widget-title">Categorias</h4>
                                <ul>
                                    <li><a href="blog.html">Digital Solutions</a> <span>(25)</span></li>
                                    <li><a href="blog.html">Saas Landing</a> <span>(09)</span></li>
                                    <li><a href="blog.html">WordPress</a> <span>(18)</span></li>
                                    <li><a href="blog.html">Graphics Design</a> <span>(05)</span></li>
                                    <li><a href="blog.html">Business Consulting</a> <span>(03)</span></li>
                                    <li><a href="blog.html">SEO Optimization</a> <span>(04)</span></li>
                                    <li><a href="blog.html">Marketing</a> <span>(05)</span></li>
                                </ul>
                            </div>
                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_blog";

                                // Consulta SQL
                                $sql = "SELECT * FROM $tabela WHERE id != :id ORDER BY date_create DESC LIMIT 5";

                                // Preparar a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Vincular o valor do parâmetro
                                $stmt->bindParam(':id', $article['id'], PDO::PARAM_INT);

                                // Executar a consulta
                                $stmt->execute();

                                // Obter o resultado como um array associativo
                                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Verificar se o resultado foi encontrado
                                if ($articles) {
                                    echo '<div class="widget widget-recent-news wow fadeInUp delay-0-2s">';
                                    echo '<h4 class="widget-title">Notícias Recentes</h4>';
                                    echo '<ul>';

                                    foreach ($articles as $article) {
                                        // Array com os nomes dos meses em português
                                        $meses = array(
                                            1 => 'janeiro',
                                            2 => 'fevereiro',
                                            3 => 'março',
                                            4 => 'abril',
                                            5 => 'maio',
                                            6 => 'junho',
                                            7 => 'julho',
                                            8 => 'agosto',
                                            9 => 'setembro',
                                            10 => 'outubro',
                                            11 => 'novembro',
                                            12 => 'dezembro'
                                        );

                                        // Cria um objeto DateTime a partir da data original
                                        $data = new DateTime($article['date_create']);

                                        // Extrai o dia, o mês e o ano da data
                                        $dia = $data->format('d');
                                        $mes = (int)$data->format('m');
                                        $ano = $data->format('Y');

                                        // Converte o mês para português
                                        $mesPortugues = $meses[$mes];

                                        // Formata a data no formato desejado 'd de F de Y'
                                        $article['date_create'] = sprintf('%d de %s de %d', $dia, $mesPortugues, $ano);
                            ?>
                                    <li>
                                        <div class="image">
                                            <img src="<?= INCLUDE_PATH_DASHBOARD; ?>back-end/admin/articles/<?= $article['id'] . "/" . $article['image']; ?>" alt="News Image">
                                        </div>
                                        <div class="content">
                                            <h5><a href="blog-details.html"><?= $article['name']; ?></a></h5>
                                            <span class="date">
                                                <i class="far fa-calendar-alt"></i>
                                                <a href="#"><?= $article['date_create']; ?></a>
                                            </span>
                                        </div>
                                    </li>
                            <?php
                                    }

                                    echo "</ul>";
                                    echo "</div>";
                                }
                            ?>
                            <div class="widget widget-cta wow fadeInUp delay-0-2s">
                                <h4>Build Awesome Website/Template</h4>
                                <a href="contact.html" class="theme-btn style-two">Contact Us <i class="fas fa-angle-double-right"></i></a>
                                <img src="assets/images/widgets/cta.png" alt="CTA">
                                <img class="cta-bg-line" src="assets/images/widgets/cta-bg-line.png" alt="CTA bg line">
                                <img class="cta-bg-dots" src="assets/images/widgets/cta-bg-dots.png" alt="CTA bg Dots">
                            </div>
                            <div class="widget widget-tag-cloud wow fadeInUp delay-0-2s">
                                <h4 class="widget-title">Popular Tags</h4>
                                <div class="tag-coulds">
                                    <a href="blog.html">Design</a>
                                    <a href="blog.html">Landing</a>
                                    <a href="blog.html">software</a>
                                    <a href="blog.html">web</a>
                                    <a href="blog.html">education</a>
                                    <a href="blog.html">email marketing</a>
                                    <a href="blog.html">SEO</a>
                                    <a href="blog.html">development</a>
                                    <a href="blog.html">wordpress</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Detalhes do Blog Area end -->
        
        
        <!-- footer area start -->
        <footer class="main-footer bgc-gray footer-white rel z-1">
            <div class="footer-cta-wrap">
                <div class="container">
                    <div class="footer-cta-inner bgs-cover" style="background-image: url(<?= INCLUDE_PATH; ?>assets/images/footer/footer-cta-bg.jpg);">
                        <div class="section-title wow fadeInLeft delay-0-2s">
                            <span class="sub-title"></span>
                            <h2>Estamos prontos para o crescimento dos seus negócios!</h2>
                        
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row medium-gap">
                    <div class="col-xl-3 col-sm-6">
                        <div class="footer-widget widget_about wow fadeInUp delay-0-2s">
                            <div class="footer-logo mb-30">
                                <a href="<?php echo INCLUDE_PATH; ?>"><img src="<?= INCLUDE_PATH; ?>assets/images/logos/<?= $home['logo']; ?>" alt="Logo"></a>
                            </div>
                            <p>Somos uma empresa de desenvolvimento de software com uma equipe altamente capacitada e dedicada. Acreditamos que as soluções de tecnologia devem ser acessíveis a todos e oferecer resultados concretos.</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 order-xl-2">
                        <div class="footer-widget widget_newsletter wow fadeInUp delay-0-6s">
                            <h4 class="footer-title">Newsletter</h4>
                            <p>Saiba das novidades em primeira mão.</p>
                            <form action="#">
                                <label for="email"><i class="far fa-envelope"></i></label>
                                <input id="email" type="email" placeholder="suporte@dropidigital.com.br" required>
                                <button>Enviar!</button>
                            </form>
                            <h5>Siga-nos</h5>
                            <div class="social-style-one">
                                <a href="<?= $home['facebook']; ?>"><i class="fab fa-facebook-f"></i></a>
                                <a href="<?= $home['twitter']; ?>"><i class="fab fa-twitter"></i></a>
                                <a href="<?= $home['instagram']; ?>"><i class="fab fa-instagram"></i></a>
                                <a href="<?= $home['linkedin']; ?>"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="row">
                            <div class="col-md-4 col-6 col-small">
                                <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-3s">
                                    <h4 class="footer-title">Links</h4>
                                    <ul class="list-style-two">
                                        <li><a href="#"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 col-6 col-small">
                                <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-4s">
                                    <h4 class="footer-title">Serviços</h4>
                                    <ul class="list-style-two">
                                        <li><a href="#">Aplicativos</a></li>
										<li><a href="#">Software</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 col-6 col-small">
                                <div class="footer-widget widget_nav_menu wow fadeInUp delay-0-5s">
                                    <h4 class="footer-title">Suporte</h4>
                                    <ul class="list-style-two">
                                        <li><a href="#">Faqs</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom bgc-black mt-20 pt-20">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="footer-bottom-menu mb-10 wow fadeInRight delay-0-2s">
                                <ul>
                                    <li><a href="#">Termos de Uso</a></li>
                                    <li><a href="#">Política de Privacidade</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="copyright-text text-lg-end wow fadeInLeft delay-0-2s">
                                <p>TODOS OS DIREITOS RESERVADOS ©<?= date('Y'); ?> Dropi Digital. <br>Uma empresa especialista em soluções digitais.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-shapes">
                <img class="shape one" src="<?= INCLUDE_PATH; ?>assets/images/footer/footer-bg-weve-shape.png" alt="Shape">
                <img class="shape two" src="<?= INCLUDE_PATH; ?>assets/images/footer/footer-bg-line-shape.png" alt="Shape">
                <img class="shape three wow fadeInRight delay-0-8s" src="<?= INCLUDE_PATH; ?>assets/images/footer/footer-right.png" alt="Shape">
            </div>
        </footer>
        <!-- footer area end -->
        
        <!-- WhatsApp Button -->
        <a href="<?= $whatsapp_link; ?>" target="_blank" class="home-page whatsapp-button">
            <i class="fab fa-whatsapp"></i>
        </a>
        
        <!-- Scroll Top Button -->
        <button class="scroll-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></button>

    </div>
    <!--End pagewrapper-->
   
    
    <!-- Jquery -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/bootstrap.min.js"></script>
    <!-- Appear Js -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/appear.min.js"></script>
    <!-- Slick -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/slick.min.js"></script>
    <!-- Magnific Popup -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Nice Select -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/jquery.nice-select.min.js"></script>
    <!-- Image Loader -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- Circle Progress -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/circle-progress.min.js"></script>
    <!-- Isotope -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/isotope.pkgd.min.js"></script>
    <!--  WOW Animation -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/wow.min.js"></script>
    <!-- Custom script -->
    <script src="<?= INCLUDE_PATH; ?>assets/js/script.js"></script>

</body>
</html>
<?php
        }
    }
?>