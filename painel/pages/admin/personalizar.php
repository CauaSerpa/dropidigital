<?php
    echo verificaPermissaoPagina($permissions);
?>
<?php
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
?>
<style>
    .main
    {
        padding: 0;
    }
    .container.grid
    {
        max-width: calc(1920px - 78px);
        width: 100%;
        padding: 0;
    }
</style>
<head>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Flaticon -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/flaticon.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/fontawesome-5.14.0.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/bootstrap.min.css">
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
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    .editable-wrapper
    {
        position: relative;
        display: table;
    }
    .editable-wrapper .edit-icon,
    .editable-wrapper .edit-image-icon
    {
        position: absolute;
        right: -18px;
        top: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        color: white;
        background: #30f0b6;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn.btn-success
    {
        background: var(--green-color);
        border-color: var(--green-color);
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color);
    }

    .theme-btn
    {
        text-decoration: none !important;
    }
</style>
<style>
    a:hover
    {
        color: var(--primary-color);
        text-decoration: none;
    }

    .main .container.grid,
    .home .container
    {
        top: var(--header-height);
    }
    .main .container.grid p
    {
        margin-top: 0;
        margin-bottom: 1rem !important;
    }
    .main .container.grid .content
    {
        font-size: inherit;
        text-align: inherit;
        line-height: inherit;
        height: inherit;
        overflow: inherit;
    }

    .main-header.fixed-header .header-upper
    {
        left: 78px;
        top: var(--header-height);
    }
    .container.clearfix
    {
        top: 0;
    }
    .main-menu .navbar-collapse > ul
    {
        margin-bottom: 0;
    }
    .main-menu .navbar-collapse li
    {
        color: var(--base-color);
    }

    .add-partner .partner-item
    {
        color: #c4c4c4;
        text-decoration: none;
        cursor: pointer;
    }
    .add-partner .partner-item:hover
    {
        color: #30f0b6;
        border: 4px solid #30f0b6;
    }

    .nav-pills .nav-link.active
    {
        color: inherit !important;
        background: transparent !important;
    }

    .container
    {
        top: inherit;
    }
</style>

<!-- Modal para edição -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Texto</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Container para o input ou textarea -->
        <div id="editContainer"></div>
        <div class="char-count">0/0</div>
        <input type="hidden" id="editableId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-success fw-semibold px-4 py-2 small" id="saveChanges">Salvar Alterações</button>
      </div>
    </div>
  </div>
</div>

<style>
    #addPartnerModal input[type="file"]
    {
        display: none;
    }
    #addPartnerModal .modal-body .image-container
    {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3rem;
        border: 2px dotted #ced4da;
        border-radius: 0.3rem;
    }
    .main .container.grid #addPartnerModal .modal-body .image-container .dropzone p
    {
        margin-bottom: 0 !important;
    }
</style>

<!-- Modal para edição -->
<div class="modal fade" id="addPartnerModal" tabindex="-1" role="dialog" aria-labelledby="addPartnerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartnerModalLabel">Adicionar Parceiro</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Container para o input ou textarea -->
                <div class="mb-3">
                    <label for="namePartner" class="form-label small">Parceiro *</label>
                    <input type="text" class="form-control" name="name_partner" id="namePartner" aria-describedby="namePartnerHelp" required>
                </div>
                <div class="mb-3">
                    <label for="linkPartner" class="form-label small">Link para site do parceiro *</label>
                    <input type="text" class="form-control" name="link_partner" id="linkPartner" aria-describedby="linkPartnerHelp" required>
                </div>
                <!-- <label for="upload-button" class="image-container mt-3">
                    <input type="file" name="imagem" id="upload-button" accept="image/*" />
                    <div for="upload-button" class="dropzone">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                    </div>
                    <div id="error"></div>
                </label>
                <div class="sortable-container mt-3">
                    <div id="image-display"></div>
                </div> -->
                <label for="partner-image" class="image-container mt-3">
                    <img src="#" alt="Image Preview" class="image-preview" id="partner-image-preview" style="display: none;">
                    <div class="dropzone" id="text-1">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                        <small id="dimensions">Dimensões: 160 x 60px</small>
                    </div>
                </label>
                <input type="file" name="image" id="partner-image" accept="image/*" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success fw-semibold px-4 py-2 small" id="addPartner" data-bs-dismiss="modal">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<style>
    #updateImage input[type="file"]
    {
        display: none;
    }
    #updateImage .modal-body .image-container
    {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3rem;
        border: 2px dotted #ced4da;
        border-radius: 0.3rem;
    }
    .main .container.grid #updateImage .modal-body .image-container .dropzone p
    {
        margin-bottom: 0 !important;
    }
</style>

<!-- Modal para edição de imagens -->
<div class="modal fade" id="updateImage" tabindex="-1" role="dialog" aria-labelledby="updateImageLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateImageLabel">Editar Imagem</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="image" class="image-container mt-3">
                    <img src="#" alt="Image Preview" class="image-preview" id="preview" style="display: none;">
                    <div class="dropzone" id="text-2">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                    </div>
                </label>
                <input type="file" name="image" id="image" accept="image/*" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success fw-semibold px-4 py-2 small" id="updateImageButton" data-bs-dismiss="modal">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function imagePreview(fileInputId, imagePreviewId, textId) {
        const imagePreview = document.getElementById(imagePreviewId);
        const fileInput = document.getElementById(fileInputId);
        const text = document.getElementById(textId);
        let previousImageSrc = "";

        fileInput.addEventListener("change", function () {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previousImageSrc = imagePreview.src; // Armazenar imagem anterior
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = "block";
                    text.style.display = "none";
                };
                reader.readAsDataURL(file);
            } else {
                // Reverter para a imagem anterior
                imagePreview.src = previousImageSrc;
                imagePreview.style.display = "none"; // Exibir a imagem anterior
                text.style.display = "none";
            }
        });
    }

    // Inicialize para os três pares de botão de upload e visualização de imagem
    imagePreview("partner-image", "partner-image-preview", "text-1");
    imagePreview("image", "image-preview", "text-2");
</script>

    <div class="page-wrapper">

        <!-- main header -->
        <header class="main-header header-one menu-white">

           <div class="header header-top-wrap bgc-gray">
           </div>
           
            <!--Header-Upper-->
            <div class="header-upper bgc-black">
                <div class="container clearfix">

                    <div class="header-inner rel d-flex align-items-center">
                        <div class="logo-outer">
                            <div class="logo"><a href="index.html"><img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.jpeg" alt="Logo" title="Logo"></a></div>
                        </div>

                        <div class="nav-outer mx-auto clearfix">
                            <!-- Main Menu -->
                            <nav class="main-menu navbar-expand-lg">
                                <div class="navbar-header">
                                   <div class="mobile-logo">
                                       <a href="index.html">
                                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.jpeg" alt="Logo" title="Logo">
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
                                        <li><i class="far fa-envelope"></i> <a href="mailto:support@gmail.com">suporte@dropidigital.com.br</a></li>
                                        <li><i class="fa-brands fa-whatsapp"></i><a href="https://api.whatsapp.com/send?phone=5511940496818" target="_blank">+55 11 94049-6818</a></li>
                                        <li class="for-none"><i class="far fa-clock"></i>São Paulo</li>
                                        <li>
                                            <div class="social-style-one">
                                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                                <a href="#"><i class="fab fa-twitter"></i></a>
                                                <a href="#"><i class="fab fa-instagram"></i></a>
                                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
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
       
        
        <!-- Hero Section Start -->
        <section class="home hero-area bgc-gray rel z-1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 align-self-center">
                        <div class="home hero-content pt-80 pb-125 rpb-0 wow fadeInUp delay-0-4s">
                            <div class="editable-wrapper">
                                <div class="editable-content">
                                    <h1 class="editable" data-editable-id="1" data-input-type="text" data-max-length="70"><?= $home['title-1']; ?></h1>
                                </div>
                                <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                            <div class="editable-wrapper">
                                <div class="editable-content">
                                    <p class="editable" data-editable-id="2" data-input-type="textarea" data-max-length="1000"><?= nl2br(htmlspecialchars($home['content-1'])); ?></p>
                                </div>
                                <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar" class="theme-btn mt-20 wow fadeInUp delay-0-6s">Criar site Grátis<i class="fas fa-long-arrow-right"></i></a>
                            <div class="hero-shapes">
                                <img class="shape one" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/dabble-plus.png" alt="Shape">
                                <img class="shape two" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/dabble-plus.png" alt="Shape">
                                <img class="shape three" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/plus.png" alt="Shape">
                            </div>
                        </div>
                    </div>
                    <div class="hero-images-container col-lg-6 align-self-end">
                        <div class="hero-images mt-80 wow fadeInLeft delay-0-2s">
                            <div class="editable-wrapper">
                                <div class="editable-content">
                                    <img class="editable-image" data-editable-id="1" data-input-type="image" src="<?php echo INCLUDE_PATH; ?>assets/images/hero/hero-one.jpg" alt="Hero">
                                </div>
                                <span class="edit-image-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-shapes">
                <img class="shape bg-lines" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/new-shape/shape.png" alt="Shape">
            </div>
        </section>
        <!-- Hero Section End -->
        
        
        <!-- Partners Area start -->
        <section class="partners-area mt-60 pt-150 pb-100 rmt-30 rpb-70 rel z-1">
            <div class="container">
               <div class="section-title text-center mb-50 wow fadeInUp delay-0-2s">
                    <div class="editable-wrapper d-inline-block">
                        <div class="editable-content">
                            <span class="sub-title mb-15 editable" data-editable-id="3" data-input-type="text" data-max-length="255"><?= $home['title-2']; ?></span>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                    <div class="editable-wrapper">
                        <div class="editable-content">
                            <h2 class="editable" data-editable-id="4" data-input-type="text" data-max-length="255"><?= $home['title-2']; ?></h2>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                </div>
                <style>
                    .col-custom-5 {
                        flex: 0 0 auto !important;
                        width: 20% !important;
                    }
                </style>
                <div class="row g-2" id="partnersContainer">
                    <?php
                        // Tabela que sera feita a consulta
                        $tabela = "tb_partners";

                        // Consulta SQL
                        $sql = "SELECT * FROM $tabela ORDER BY id ASC";

                        // Preparar a consulta
                        $stmt = $conn_pdo->prepare($sql);

                        // Executar a consulta
                        $stmt->execute();

                        // Obter o resultado como um array associativo
                        $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Contador para dividir em linhas
                        $contador = 0;
                    ?>
                    <!-- Loop para exibir os parceiros -->
                    <?php foreach ($partners as $partner) : ?>
                        <div class="col col-custom-5">
                            <a href="<?= $partner['link']; ?>" class="partner-item wow fadeInUp delay-0-<?= $contador % 5 + 3 ?>s">
                                <img src="<?= INCLUDE_PATH; ?>assets/images/partners/<?= $partner['image']; ?>" alt="Parceiro <?= $partner['name']; ?>">
                            </a>
                        </div>
                        <?php $contador++; ?>
                    <?php endforeach; ?>
                    <!-- <div class="col col-custom-5">
                        <a href="https://hotmart.com/pt-br" class="partner-item wow fadeInUp delay-0-3s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-1.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.eduzz.com/pt-br" class="partner-item wow fadeInUp delay-0-4s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-2.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.monetizze.com.br" class="partner-item wow fadeInUp delay-0-5s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-3.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://associados.amazon.com.br" class="partner-item wow fadeInUp delay-0-6s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-4.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://shopee.com.br/m/afiliados" class="partner-item wow fadeInUp delay-0-7s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-5.jpg" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://site.braip.com/afiliado/" class="partner-item wow fadeInUp delay-0-3s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-6.svg" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.clickbank.com/affiliates-v2/" class="partner-item wow fadeInUp delay-0-4s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-7.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://m.shein.com/br/campus-affiliate-a-1500.html" class="partner-item wow fadeInUp delay-0-5s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-8.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.lomadee.com/pt_br/afiliados/" class="partner-item wow fadeInUp delay-0-6s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-9.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.parceiromagalu.com.br" class="partner-item wow fadeInUp delay-0-7s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-10.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://afilio.com.br/afiliados/" class="partner-item wow fadeInUp delay-0-3s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-11.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.leadsmarket.com/payday-loan-publisher-program" class="partner-item wow fadeInUp delay-0-4s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-12.svg" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://portals.aliexpress.com/affiportals/web/portals.htm#/home" class="partner-item wow fadeInUp delay-0-5s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-13.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://kiwify.com.br" class="partner-item wow fadeInUp delay-0-6s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-14.png" alt="Partner">
                        </a>
                    </div>
                    <div class="col col-custom-5">
                        <a href="https://www.mercadolivre.com.br/l/afiliados-home?isSparkleRedirect=true#variant_sparkle/afiliados=26141&origin=sparkle" class="partner-item wow fadeInUp delay-0-7s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/partners/partner-15.png" alt="Partner">
                        </a>
                    </div> -->
                    <div class="col col-custom-5 add-partner">
                        <a href="#" class="partner-item wow fadeInUp delay-0-3s" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                            <i class="fa-solid fa-plus fs-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Partners Area end -->
        
        
        <!-- About Area start -->
        <section class="about-area pb-130 rpb-100 rel z-1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="about-content rmb-65 wow fadeInLeft delay-0-2s">
                            <div class="section-title mb-30">
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <span class="sub-title mb-15 editable" data-editable-id="5" data-input-type="text" data-max-length="255"><?= $home['about-subtitle-3']; ?></span>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <h2 class="editable" data-editable-id="6" data-input-type="text" data-max-length="255"><?= $home['about-title-3']; ?></h2>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                            </div>
                            <div class="editable-wrapper">
                                <div class="editable-content">
                            <p class="editable" data-editable-id="7" data-input-type="textarea" data-max-length="1000"><?= nl2br(htmlspecialchars($home['about-content-3'])); ?></p>
                                </div>
                                <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                            <div class="about-btns mb-45">
                                <a href="about.html" class="theme-btn mt-15">Cadastrar<i class="fas fa-long-arrow-right"></i></a>
                                <div class="hotline mt-15">
                                    <i class="fa-brands fa-whatsapp fs-4"></i>
                                    <div class="content">
                                        <span>Contato</span><br>
                                        <a href="callto:+5511992890194">+55 11 99289-0194</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row no-gap for-active">
                                <div class="col-sm-6">
                                    <div class="service-item active">
                                        <div class="icon">
                                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/services/<?= $home['service-icon-1']; ?>" alt="Icon">
                                        </div>
                                        <div class="editable-wrapper">
                                            <div class="editable-content">
                                                <h4><a href="service-details.html" class="editable" data-editable-id="8" data-input-type="text" data-max-length="255"><?= $home['service-title-1']; ?></a></h4>
                                            </div>
                                            <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                        </div>
                                        <div class="editable-wrapper">
                                            <div class="editable-content">
                                                <p class="editable" data-editable-id="9" data-input-type="textarea" data-max-length="1000"><?= nl2br(htmlspecialchars($home['service-description-1'])); ?></p>
                                            </div>
                                            <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="service-item">
                                        <div class="icon">
                                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/services/<?= $home['service-icon-2']; ?>" alt="Icon">
                                        </div>
                                        <div class="editable-wrapper">
                                            <div class="editable-content">
                                                <h4><a href="service-details.html" class="editable" data-editable-id="10" data-input-type="text" data-max-length="255"><?= $home['service-title-2']; ?></a></h4>
                                            </div>
                                            <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                        </div>
                                        <div class="editable-wrapper">
                                            <div class="editable-content">
                                                <p class="editable" data-editable-id="11" data-input-type="textarea" data-max-length="1000"><?= nl2br(htmlspecialchars($home['service-description-2'])); ?></p>
                                            </div>
                                            <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .about-images .top-part .editable-image
                        {
                            margin-left: 15px !important;
                            width: 100% !important;
                            max-width: 100% !important;
                            margin-top: 0 !important;
                        }
                        .about-images .top-part .logo
                        {
                            margin-left: 15px !important;
                        }
                        .about-images .bottom-part .editable-image
                        {
                            margin-top: -50% !important;
                            width: 100% !important;
                        }
                        .about-images .bottom-part .edit-image-icon
                        {
                            margin-top: -50% !important;
                        }
                    </style>
                    <div class="col-lg-6">
                        <div class="about-images">
                            <div class="top-part">
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <img class="editable-image wow fadeInRight delay-0-3s" data-editable-id="2" data-input-type="image" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-2.jpg" alt="About">
                                    </div>
                                    <span class="edit-image-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                                <!-- <img class="wow fadeInRight delay-0-3s" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-2.jpg" alt="About"> -->
                                <img class="logo wow zoomIn delay-0-5s" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-logo.jpeg" alt="About">
                            </div>
                            <div class="bottom-part">
                                <img class="wow fadeInDown delay-0-5s" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-dots.png" alt="About">
                                <!-- <img class="wow fadeInDown delay-0-3s" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-1.jpg" alt="About"> -->
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <img class="editable-image wow fadeInDown delay-0-3s" data-editable-id="3" data-input-type="image" src="<?php echo INCLUDE_PATH; ?>assets/images/about/about-1.jpg" alt="About">
                                    </div>
                                    <span class="edit-image-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Area end -->
        
        
        <!-- Project Area start -->
        <section class="project-area overflow-hidden bgc-lighter pt-130 rpt-100 rel z-1">
            <div class="container">
               <div class="section-title text-center mb-55 wow fadeInUp delay-0-2s">
                    <div class="editable-wrapper d-inline-block">
                        <div class="editable-content">
                            <span class="sub-title mb-15 editable" data-editable-id="12" data-input-type="text" data-max-length="255"><?= $home['subtitle-4']; ?></span>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                    <div class="editable-wrapper">
                        <div class="editable-content">
                            <h2 class="editable" data-editable-id="13" data-input-type="text" data-max-length="255"><?= $home['title-4']; ?></h2>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                </div>
                <div class="project-slider-active">
                    <div class="project-slider-item">
                       <div class="video">
                           <img src="<?php echo INCLUDE_PATH; ?>assets/images/projects/project-video1.png" alt="Video">
                           <a href="https://www.youtube.com/watch?v=_4ulL5doXW8" class="mfp-iframe video-play" tabindex="-1"><i class="fas fa-play"></i></a>
                       </div>
                       <div class="content">
                           <h4>Aqui você pode criar também um site otimizado de catálogo para seus produtos e serviços. Para ser encontrado no Google</h4>
                           <p>Crie aqui seu site de serviços e seja encontrado no Google. Alguns exemplos de negóocio que pode ter seu site aqui.</p>
                           <div class="row g-2">
                               <ul class="list-style-one col-md-4">
                                   <li>Clínica de estética</li>
                                   <li>Empreiteira</li>
                                   <li>Imobiliária</li>
                                   <li>Mecânica</li>
                                   <li>Clínica médica</li>
                                   <li>Academia</li>
                                   <li>Advocacia</li>
                               </ul>
                               <ul class="list-style-one col-md-4">
                                   <li>Dentista</li>
                                   <li>Restaurante</li>
                                   <li>Cabeleireiro</li>
                                   <li>Eletricista</li>
                                   <li>Lojas de roupa</li>
                                   <li>Empresa de segurança</li>
                                   <li>Farmárcia</li>
                               </ul>
                               <ul class="list-style-one col-md-4">
                                   <li>Pizzaria</li>
                                   <li>Bares</li>
                                   <li>PetShop</li>
                                   <li>Igrejas</li>
                                   <li>Loja de carro</li>
                                   <li>Madereira</li>
                                   <li>Autopeças</li>
                               </ul>
                           </div>
                           <a href="#" class="theme-btn style-two mt-15">Saiba mais!<i class="fas fa-long-arrow-right"></i></a>
                       </div>
                    </div>
                    <div class="project-slider-item">
                       <div class="content">
                           <h4>Revolucione a forma de fazer negócios!</h4>
                           <p>Faça o cadastro em uma das palaformas citadas a cima, gere seus links de afiliados e cadastre na Dropi Digital. Com palavras chaves por Exemplo:</p>
                           <div class="row g-2">
                               <ul class="list-style-one col-md-6">
                                   <li>Curso de marketing digital</li>
                                   <li>Livros digitais</li>
                                   <li>EBooks</li>
                                   <li>Produtos para emagrecimento</li>
                                   <li>Roupas femininas</li>
                                   <li>Produtos para relacionamento</li>
                                   <li>Eletrodomésticos</li>
                               </ul>
                               <ul class="list-style-one col-md-6">
                                   <li>Curso de tráfego pago</li>
                                   <li>Curso de Desenho</li>
                                   <li>Curso de Inglês</li>
                                   <li>Curso de finanças</li>
                                   <li>Ganhar dinheiro na Internet</li>
                                   <li>Curso para Investimento</li>
                                   <li>Curso de empreendedorismo</li>
                               </ul>
                           </div>
                           <a href="#" class="theme-btn style-two mt-15">Saiba mais!<i class="fas fa-long-arrow-right"></i></a>
                       </div>
                       <div class="video">
                           <img src="<?php echo INCLUDE_PATH; ?>assets/images/projects/project-video.png" alt="Video">
                           <a href="https://www.youtube.com/watch?v=TgXUduH1FxA" class="mfp-iframe video-play" tabindex="-1"><i class="fas fa-play"></i></a>
                       </div>
                    </div>
                </div>
            </div>
            <div class="project-shapes">
                <img class="shape one" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/project-left.png" alt="shape">
                <img class="shape two" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/project-right.png" alt="shape">
            </div>
        </section>
        <!-- Project Area end -->
        
        
        <!-- Services Area start -->
        <section class="services-area bgc-gray text-white pt-75 pb-10 rel z-1">
            <div class="container">
                <div class="row medium-gap">
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-4s">
                            <div class="icon">
                                <i class="flaticon-networking"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Consultoria Digital</a></h4>
                                <p>Informe o seu projeto e tediremos como você pode aplica-lo aqui na Dropi Digital. Afiliado, Dropshipping de produtos físicos e serviços.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-6s">
                            <div class="icon">
                                <i class="flaticon-app-development"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Criamos a sua loja</a></h4>
                                <p>A plataforma é totalmente intuitiva, e você pode sozinho montar sua loja, mas caso precise que alguém monte seu site aqui. Montamos para você, com banners, produtos cadastrados e totalmente otimizado para os mecanismos de busca. Consultar valores.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-3s">
                            <div class="icon">
                                <i class="flaticon-coding"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Criamos os Banners da sua Loja/Site</a></h4>
                                <p>Precisa da criação dos banners para sua loja? Fique tranquilo, fazemos para você. Envie as especificações e montamos as suas artes. Consultar valores.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-5s">
                            <div class="icon">
                                <i class="flaticon-logo"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Criação da sua Logo</a></h4>
                                <p>Quer montar sua loja/site e não tem uma logo do seu negócio? Desenvolvemos um logotipo para você, informe nome e segmento que desenvolvemos um para você. Consultar valores.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-5s">
                            <div class="icon">
                                <i class="flaticon-seo"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Serviço de tráfego pago</a></h4>
                                <p>Cadastrando seus produtos e serviços com palavras-chave, certamente será encontrado pelo Google, mas se precisar aparecer de forma imediata, temos o serviço de link patrocinado com Googles Ads e Facebook Ads</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="service-two-item wow fadeInUp delay-0-7s">
                            <div class="icon">
                                <i class="flaticon-seo"></i>
                            </div>
                            <div class="content">
                                <h4><a href="service-details.html">Otimização de site SEO</a></h4>
                                <p>Criação de Link building em sites parceiros, para aumentar a relevância da sua loja, e rápido posicionamento da sua loja para os mecanismos de busca. Consultar valores.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services Area end -->
        

        <!-- Work Process Area start -->
        <section class="work-process-area pt-130 pb-100 rpt-100 rpb-70 rel z-1">
            <div class="section-title text-center mb-70 wow fadeInUp delay-0-2s">
                <div class="editable-wrapper d-inline-block">
                    <div class="editable-content">
                        <span class="sub-title mb-15 editable" data-editable-id="14" data-input-type="text" data-max-length="255"><?= $home['subtitle-5']; ?></span>
                    </div>
                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                </div>
                <div class="editable-wrapper d-inline-block">
                    <div class="editable-content">
                        <h2 class="editable" data-editable-id="15" data-input-type="text" data-max-length="255"><?= $home['title-5']; ?></h2>
                    </div>
                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                </div>
            </div>
            <div class="work-process-line text-center">
                <img src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/work-process-line.png" alt="line">
            </div>
            <div class="container">
                <div class="row row-cols-xl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 justify-content-center">
                    <div class="col">
                        <div class="work-process-item mt-40 wow fadeInUp delay-0-2s">
                            <div class="number">01</div>
                            <div class="content">
                                <h4>Definir um Nicho</h4>
                                <p>Hoje como afiliado você pode divulgar diversos produtos. Entre eles: Infoprodutos/Cursos digitais, Produtos físicos, Eletro domésticos, Livros, roupas, etc. Então o primeiro passo é definir quais produtos irá vender.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-10 wow fadeInDown delay-0-2s">
                            <div class="number">02</div>
                            <div class="content">
                                <h4>Criarconta em programas de afiados</h4>
                                <p>Existem diversos programas de afiliados para vender sem estoque. Alguns deles são: Hotmart, Eduzz, Monetizze, Amazon Afiliados, Shopee, Braip, ClickBank, Shein, Parceiro Magazine Luíza, AliExpress, Alibaba, Mercado Livre entre outros.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-40 wow fadeInUp delay-0-2s">
                            <div class="number">03</div>
                            <div class="content">
                                <h4>Traga seus links de afiliado para a Dropi Digital</h4>
                                <p>Após criar seus links de afiliado. Faça o cadastro na Dropi Digital e cadastre seus produtos com nome, descrição e imagem.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item wow fadeInDown delay-0-2s">
                            <div class="number">04</div>
                            <div class="content">
                                <h4>Cadastre os produtos com palavras chave mais pesquisadas.</h4>
                                <p>Exemplo: Se sua loja é de Cursos Digitais. Você vai cadastrar todas as variáveis de Cursos. Exemplo: Curso de marketing digital, Curso de tráfego pago, Curso de Inglês, Treinamento para emagrecer, Curso de música, Curso de estética, Entre muitos outros. Recomendamos usar o planejador de palavras-chave do Google para saber as palavras mais pesquisas ou site Ubersuggest</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-50 wow fadeInUp delay-0-2s">
                            <div class="number">05</div>
                            <div class="content">
                                <h4>Venda todos os dias</h4>
                                <p>A nossa plataforma irá indexar os seus produtos/serviços automaticamente no Google e você será encontrado na busca organica nas primeiras posições.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Work Process Area start -->
        <section class="work-process-area pt-130 pb-100 rpt-100 rpb-70 rel z-1">
            <div class="section-title text-center mb-70 wow fadeInUp delay-0-2s">
                <div class="editable-wrapper d-inline-block">
                    <div class="editable-content">
                        <span class="sub-title mb-15 editable" data-editable-id="15" data-input-type="text" data-max-length="255"><?= $home['subtitle-6']; ?></span>
                    </div>
                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                </div>
                <div class="editable-wrapper d-inline-block">
                    <div class="editable-content">
                        <h2 class="editable" data-editable-id="16" data-input-type="text" data-max-length="255"><?= $home['title-6']; ?></h2>
                    </div>
                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                </div>
            </div>
            <div class="work-process-line text-center">
                <img src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/work-process-line.png" alt="line">
            </div>
            <div class="container">
                <div class="row row-cols-xl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 justify-content-center">
                    <div class="col">
                        <div class="work-process-item mt-40 wow fadeInUp delay-0-2s">
                            <div class="number">01</div>
                            <div class="content">
                                <h4>Definir qual seguimento será seu site</h4>
                                <p>Você pode divulgar o serviço do qual você já atua. Ou escolher uma das diversas ideias de negócio sugeridas aqui em nossa plataforma.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-10 wow fadeInDown delay-0-2s">
                            <div class="number">02</div>
                            <div class="content">
                                <h4>Criar sua conta na Dropi Digital</h4>
                                <p>Crie sua conta na Dropi Digital com nome de usuário, descrição, banners, domínio dropidigital ou domínio próprio.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-40 wow fadeInUp delay-0-2s">
                            <div class="number">03</div>
                            <div class="content">
                                <h4>Pesquisar as melhores palavras chave</h4>
                                <p>Crie sua conta na Dropi Digital com nome de usuário, descrição, banners, domínio dropidigital ou domínio próprio.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item wow fadeInDown delay-0-2s">
                            <div class="number">04</div>
                            <div class="content">
                                <h4>Cadastrar os serviços na Dropi Digital</h4>
                                <p>Use o planejador de palavra chave do Google ou site https://neilpatel.com/br/ubersuggest/paraescolharas palavras chave mais pesquisadas. Exemplo se você é <span class="fw-semibold">Advogado em São Paulo.</span> Alguns dos serviços que irá cadastrar são: Advogado em São Paulo, Advogado trabalhista São Paulo, Advogado criminalista São Paulo, Advogado imobiliario São Paulo, Advogado civil São Paulo, Advogado famíliar São Paulo, e assim por diante. Temos também o serviço de envio das palavras-chave para seu nicho ou o serviço de cadastro de palavras. Chame no WhatsApp e consulte.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="work-process-item mt-50 wow fadeInUp delay-0-2s">
                            <div class="number">05</div>
                            <div class="content">
                                <h4>Receba orçamentos diários através da busca organica do Google.</h4>
                                <p>Após o cadastro das palavras mais buscadas, agora é só aguardar a indexação, e seu site irá aparecer para seus cliente, e você receberá contatos todos os dias.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Plans Area start -->
        <section class="work-process-area pt-130 pb-100 rpt-100 rpb-70 rel z-1">
            <div class="container">
                <div class="section-title text-center mb-70 wow fadeInUp delay-0-2s">
                    <div class="editable-wrapper d-inline-block">
                        <div class="editable-content">
                            <span class="sub-title mb-15 editable" data-editable-id="17" data-input-type="text" data-max-length="255"><?= $home['subtitle-7']; ?></span>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                    <div class="editable-wrapper d-block">
                        <div class="editable-content">
                            <h2 class="editable" data-editable-id="18" data-input-type="text" data-max-length="255"><?= $home['title-7']; ?></h2>
                        </div>
                        <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                    </div>
                </div>
                <div class="why-choose-tab">
                    <ul class="nav nav-pills nav-fill mb-80 rmb-50 wow fadeInUp delay-0-4s">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#plans-tap1">
                                <span>Mensal</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#plans-tap2">
                                <span>Anual</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="plans-tap1">
                            <div class="row no-gap row-cols-xl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 for-active justify-content-center">
                                <?php
                                    // Consulta SQL para obter todos os planos
                                    $query = "SELECT * FROM tb_plans";
                                    $stmt = $conn_pdo->query($query);
                                    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Loop através dos resultados e exibir cada plano
                                    foreach ($plans as $plan) {
                                ?>
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="lh-1 mb-0"><?php echo $plan['name']; ?></h3>
                                                <p class="sub-title fs-5"><?php echo $plan['sub_name']; ?></p>
                                                <div class="pricing-content monthly text-center theme-btn d-block mb-3">
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ <?php echo $plan['price']; ?></h3>
                                                    <p class="sub-title lh-1 fw-normal  mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <?php
                                                // Exiba os recursos na página
                                                $resources = json_decode($plan['resources']);

                                                if (!empty($resources)) {
                                                    echo "<ul class='list-style-one pt-5'>";
                                                    foreach ($resources as $recurso) {
                                                        echo "<li>$recurso</li>";
                                                    }
                                                    echo "</ul>";
                                                }
                                            ?>
                                        </div>
                                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="plans-tap2">
                            <div class="row no-gap row-cols-xl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 for-active justify-content-center">
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between active">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="lh-1 mb-0">Básico</h3>
                                                <p class="sub-title fs-5">Conhecendo</p>
                                                <div class="pricing-content yearly free text-center theme-btn d-block mb-3">
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ 0</h3>
                                                    <p class="sub-title lh-1 fw-normal  mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <ul class="list-style-one pt-5">
                                                <li>10 produtos</li>
                                                <li>5.000 visitas/mês</li>
                                                <li>Sem limite de pedidos ou orçamentos</li>
                                                <li>Sem comissão sobre vendas</li>
                                                <li>Conta protegida</li>
                                                <li>Botão WhatsApp</li>
                                            </ul>
                                        </div>
                                        <a href="#" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="lh-1 mb-0">Iniciante</h3>
                                                <p class="sub-title fs-5">Já faço vendas</p>
                                                <div class="pricing-content yearly text-center theme-btn d-block mb-3">
                                                    <p class="fs-5 fw-normal text-decoration-line-through lh-1 text-nowrap mb-0">R$ 47</p>
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ 39</h3>
                                                    <p class="sub-title lh-1 fw-normal mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <ul class="list-style-one pt-5">
                                                <li>250 produtos</li>
                                                <li>50.000 visitas/mês</li>
                                                <li>Sem limite de pedidos</li>
                                                <li>Sem comissão sobre vendas</li>
                                                <li>Conta protegida</li>
                                                <li>Botão WhatsApp</li>
                                                <li>Suporte humanizado</li>
                                                <li>Palavras chave do seu nicho</li>
                                            </ul>
                                        </div>
                                        <a href="#" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="fs-3 lh-1 mb-0">Intermeriário</h3>
                                                <p class="sub-title fs-5">Pedidos diários</p>
                                                <div class="pricing-content yearly text-center theme-btn d-block mb-3">
                                                    <p class="fs-5 fw-normal text-decoration-line-through lh-1 text-nowrap mb-0">R$ 79</p>
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ 59</h3>
                                                    <p class="sub-title lh-1 fw-normal  mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <ul class="list-style-one pt-5">
                                                <li>250 produtos</li>
                                                <li>50.000 visitas/mês</li>
                                                <li>Sem limite de pedidos</li>
                                                <li>Sem comissão sobre vendas</li>
                                                <li>Conta protegida</li>
                                                <li>Botão WhatsApp</li>
                                                <li>Suporte humanizado</li>
                                                <li>Palavras chave do seu nicho</li>
                                            </ul>
                                        </div>
                                        <a href="#" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="lh-1 mb-0">Avançado</h3>
                                                <p class="sub-title fs-5">Muitas vendas</p>
                                                <div class="pricing-content yearly text-center theme-btn d-block mb-3">
                                                    <p class="fs-5 fw-normal text-decoration-line-through lh-1 text-nowrap mb-0">R$ 127</p>
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ 119</h3>
                                                    <p class="sub-title lh-1 fw-normal  mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <ul class="list-style-one pt-5">
                                                <li>750 produtos</li>
                                                <li>100.000 visitas/mês</li>
                                                <li>Sem limite de pedidos</li>
                                                <li>Sem comissão sobre vendas</li>
                                                <li>Conta protegida</li>
                                                <li>Botão WhatsApp</li>
                                                <li>Suporte humanizado</li>
                                                <li>Palavras chave do seu nicho</li>
                                                <li>Atendimento prioritário</li>
                                            </ul>
                                        </div>
                                        <a href="#" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="pricing service-item h-100 d-flex flex-column justify-content-between">
                                        <div class="content">
                                            <div class="title text-center">
                                                <h3 class="lh-1 mb-0">Expert</h3>
                                                <p class="sub-title fs-5">Voando alto</p>
                                                <div class="pricing-content yearly text-center theme-btn d-block mb-3">
                                                    <p class="fs-5 fw-normal text-decoration-line-through lh-1 text-nowrap mb-0">R$ 191</p>
                                                    <h3 class="lh-1 text-nowrap mb-0">R$ 179</h3>
                                                    <p class="sub-title lh-1 fw-normal  mb-0 fs-6">Por mês</p>
                                                </div>
                                            </div>
                                            <ul class="list-style-one pt-5">
                                                <li>Produtos ilimitados</li>
                                                <li>300.000 visitas/mês</li>
                                                <li>Sem limite de pedidos</li>
                                                <li>Sem comissão sobre vendas</li>
                                                <li>Conta protegida</li>
                                                <li>Botão WhatsApp</li>
                                                <li>Suporte humanizado</li>
                                                <li>Palavras chave do seu nicho</li>
                                                <li>Atendimento prioritário</li>
                                                <li>Mentoria inicial do projeto</li>
                                                <li>Serviço de SEO incluso</li>
                                            </ul>
                                        </div>
                                        <a href="#" class="theme-btn style-two mt-15" tabindex="0">Assinar<i class="fas fa-long-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Work Process Area end -->
        <!-- Why Choose Us Area start -->
        <section class="why-choose-us-area py-130 rpy-100 rel z-1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="section-title text-center mb-45 wow fadeInUp delay-0-2s">
                            <div class="editable-wrapper d-inline-block">
                                <div class="editable-content">
                                    <span class="sub-title mb-15 editable" data-editable-id="19" data-input-type="text" data-max-length="255"><?= $home['subtitle-8']; ?></span>
                                </div>
                                <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                            <div class="editable-wrapper d-inline-block">
                                <div class="editable-content">
                                    <h2 class="editable" data-editable-id="20" data-input-type="text" data-max-length="255"><?= $home['title-8']; ?></h2>
                                </div>
                                <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="why-choose-tab">
                    <ul class="nav nav-pills nav-fill mb-80 rmb-50 wow fadeInUp delay-0-4s">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#wc-tap1">
                                <i class="flaticon-creativity"></i> <span>.Net Core e PHP</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#wc-tap2">
                                <i class="flaticon-test"></i> <span>Cloud Computing</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#wc-tap3">
                                <i class="flaticon-cyber-security-1"></i> <span>Javascript</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#wc-tap4">
                                <i class="flaticon-support"></i> <span>Mobile: React Native, iOS e Android</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="wc-tap1">
                            <div class="row gap-90 align-items-center">
                                <div class="col-lg-6">
                                    <div class="why-choose-image rmb-55">
                                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose1.jpg" alt="Why Choose">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="why-choose-content">
                                        <h3>.Net Core e PHP</h3>
                                        <p>Duas plataformas maduras e robustas, trabalhamos sempre com as últimas versões para garantir a segurança da informação, além de potencializar a agilidade de desenvolvimento. Garantimos compatibilidade total com as versões antigas.</p>
                                        <ul class="list-style-one pt-5">
                                            <li>.NET Core é uma plataforma para desenvolvimento de aplicações desenvolvida e mantida pela Microsoft como um projeto open source</li>
                                            <li>PHP é uma linguagem de programação utilizada por programadores e desenvolvedores para construir sites dinâmicos</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wc-tap2">
                            <div class="row gap-90 align-items-center">
                                <div class="col-lg-6">
                                    <div class="why-choose-content">
                                        <h3>Cloud Computing</h3>
                                        <p>Mais do que uma inovação tecnológica, é uma ferramenta para viabilização de novas oportunidades de negócio ao permitir maior volume de dados armazenados e protegidos, podendo ser acessados de qualquer dispositivo ao redor do mundo.</p>
                                        <ul class="list-style-one pt-5">
                                            <li>Software como Serviço (SaaS)</li>
                                            <li>Plataforma como Serviço (PaaS)</li>
                                            <li>Infraestrutura como Serviço (IaaS)</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="why-choose-image rmt-55">
                                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose2.jpg" alt="Why Choose">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wc-tap3">
                            <div class="row gap-90 align-items-center">
                                <div class="col-lg-6">
                                    <div class="why-choose-image rmb-55">
                                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose3.jpg" alt="Why Choose">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="why-choose-content">
                                        <h3>Javascript</h3>
                                        <p>Com as principais tecnologias que revolucionaram o modo de criar aplicações para web, desenvolvemos interfaces ricas que consomem serviços leves, tudo com total cuidado no desempenho e nos pequenos detalhes da aplicação.</p>
                                        <ul class="list-style-one pt-5">
                                            <li>Com ajuda de frameworks no desenvolvimento de aplicativos e de recursos que são integrados na oferta de sistemas operacionais para mobile;</li>
                                            <li>Em games, graças à facilidade que Javascript oferece no desenvolvimento de animações e ambientes 3D, mercados com alta procura atualmente;</li>
                                            <li>Sistemas e programas desktop tradicionais.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wc-tap4">
                            <div class="row gap-90 align-items-center">
                                <div class="col-lg-6">
                                    <div class="why-choose-content">
                                        <h3>React Native, iOS e Android</h3>
                                        <p>Criamos aplicações mobile e construimos toda a estrutura tecnológica necessária para a viabilização, sustentação e evolução do seu produto e do seu negócio. Utilizando ferramentas atuais e atualizadas, proporcionamos produtos de qualidade.</p>
                                        <ul class="list-style-one pt-5">
                                            <li>O React Native combina as melhores partes do desenvolvimento nativo com o React, a melhor biblioteca JavaScript da categoria para criar interfaces de usuário.</li>
                                            <li>Você pode usar o React Native hoje em seus projetos Android e iOS existentes ou pode criar um aplicativo totalmente novo do zero.</li>
                                            
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="why-choose-image rmt-55">
                                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose4.jpg" alt="Why Choose">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="why-choose-shapes">
                <img class="shape one" src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose-shape1.png" alt="Shape">
                <img class="shape two" src="<?php echo INCLUDE_PATH; ?>assets/images/about/why-choose-shape2.png" alt="Shape">
            </div>
        </section>
        <!-- Why Choose Us Area end -->
        
        <!-- Team Area start -->
        <!-- <section class="team-area pb-100 rpb-70 rel z-1">
            <div class="container">
                <div class="section-title text-center mb-60 wow fadeInUp delay-0-2s">
                    <span class="sub-title mb-15">Membro da equipe</span>
                    <h2>Time Dropi Digital</h2>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="team-member wow fadeInUp delay-0-2s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/team/renan.jpg" alt="Team">
                            <h4>Renan Araujo</h4>
                            <span class="designation">CEO</span>
                            <div class="social-style-two">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="team-member wow fadeInUp delay-0-4s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/team/roberto.jpg" alt="Team">
                            <h4>Roberto Costa Jr</h4>
                            <span class="designation">CTO</span>
                            <div class="social-style-two">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="team-member wow fadeInUp delay-0-6s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/team/bruna.jpg" alt="Team">
                            <h4>Bruna Monteiro</h4>
                            <span class="designation">Estrategia de négocio</span>
                            <div class="social-style-two">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="team-member wow fadeInUp delay-0-8s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/team/caua.jpg" alt="Team">
                            <h4>Cauã Serpa</h4>
                            <span class="designation">UX/UI</span>
                            <div class="social-style-two">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Team Area end -->
        
        
        <!-- Statistics Area start -->
        <section class="statistics-area rel z-2">
            <div class="container">
                <div class="statistics-inner bgs-cover text-white p-80 pb-20" style="background-image: url(<?php echo INCLUDE_PATH; ?>assets/images/background/statistics.jpg);">
                    <div class="row align-items-xl-start align-items-center">
                        <div class="col-xl-5 col-lg-6">
                            <div class="statistics-content mb-55 wow fadeInUp delay-0-2s">
                                <div class="section-title mb-30">
                                    <span class="sub-title mb-15">Estatísticas da empresa</span>
                                    <h2>Transformar suas ideias em soluções.</h2>
                                </div>
                                <a href="about.html" class="read-more">Saiba Mais!<i class="fas fa-long-arrow-right"></i></a>
                            </div>
                        </div>
                        <div class="col-xl-7 col-lg-6">
                            <div class="row">
                                <div class="col-xl-3 col-small col-6">
                                    <div class="counter-item counter-text-wrap wow fadeInDown delay-0-3s">
                                        <i class="flaticon-target"></i>
                                        <span class="count-text plus" data-speed="3000" data-stop="10">0</span>
                                        <span class="counter-title">Projetos entregues</span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-small col-6">
                                    <div class="counter-item counter-text-wrap wow fadeInUp delay-0-3s">
                                        <i class="flaticon-target-audience"></i>
                                        <span class="count-text percent" data-speed="3000" data-stop="98.9">0</span>
                                        <span class="counter-title">Clientes Satisfeitos</span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-small col-6">
                                    <div class="counter-item counter-text-wrap wow fadeInDown delay-0-3s">
                                        <i class="flaticon-customer-experience"></i>
                                        <span class="count-text plus" data-speed="3000" data-stop="10">0</span>
                                        <span class="counter-title">Anos de experiência</span>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-small col-6">
                                    <div class="counter-item counter-text-wrap wow fadeInUp delay-0-3s">
                                        <i class="flaticon-medal"></i>
                                        <span class="count-text plus" data-speed="3000" data-stop="4">0</span>
                                        <span class="counter-title">Premiação</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Statistics Area end -->
        
        
        <!-- Pricing Plan Area start -->
        <!-- <section class="price-plan-area bgc-lighter mt-30 rmt-0 pt-220 pb-100 rpb-70 rel z-1">
            <div class="container pt-20">
                <div class="section-title text-center mb-55 wow fadeInUp delay-0-2s">
                    <span class="sub-title mb-15">Amazing Pricing Plan</span>
                    <h2>Affordable Pricing Packages</h2>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-md-6">
                        <div class="pricing-plan-item wow fadeInUp delay-0-2s">
                            <span class="badge">Best Package</span>
                            <h4 class="title">Basic Plan</h4>
                            <span class="price-count">5 Services Included</span>
                            <span class="price">29.85</span>
                            <a href="pricing.html" class="theme-btn style-two">Choose Package <i class="fas fa-long-arrow-right"></i></a>
                            <h5>This Plan Included :</h5>
                            <ul>
                                <li><a href="#">Premium Quality Supports (24/7)</a></li>
                                <li><a href="#">IT Consultations (Business Growth)</a></li>
                                <li><a href="#">Web Design & Development</a></li>
                                <li><a href="#">Search Engine Optimization (SEO )</a></li>
                                <li><a href="#">User & Market Research</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="pricing-plan-item wow fadeInUp delay-0-4s">
                            <span class="badge">Best Package</span>
                            <h4 class="title">standard Plan</h4>
                            <span class="price-count">7 Services Included</span>
                            <span class="price">49.64</span>
                            <a href="pricing.html" class="theme-btn style-two">Choose Package <i class="fas fa-long-arrow-right"></i></a>
                            <h5>This Plan Included :</h5>
                            <ul>
                                <li><a href="#">Premium Quality Supports (24/7)</a></li>
                                <li><a href="#">IT Consultations (Business Growth)</a></li>
                                <li><a href="#">Web Design & Development</a></li>
                                <li><a href="#">Search Engine Optimization (SEO )</a></li>
                                <li><a href="#">User & Market Research</a></li>
                                <li><a href="#">UX/UI Strategy (Design & Develop)</a></li>
                                <li><a href="#">Product Engineering</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="pricing-plan-item wow fadeInUp delay-0-6s">
                            <span class="badge">Best Package</span>
                            <h4 class="title">Golden Package</h4>
                            <span class="price-count">7 Services Included</span>
                            <span class="price">98.73</span>
                            <a href="pricing.html" class="theme-btn style-two">Choose Package <i class="fas fa-long-arrow-right"></i></a>
                            <h5>This Plan Included :</h5>
                            <ul>
                                <li><a href="#">Premium Quality Supports (24/7)</a></li>
                                <li><a href="#">IT Consultations (Business Growth)</a></li>
                                <li><a href="#">Web Design & Development</a></li>
                                <li><a href="#">Search Engine Optimization (SEO )</a></li>
                                <li><a href="#">User & Market Research</a></li>
                                <li><a href="#">UX/UI Strategy (Design & Develop)</a></li>
                                <li><a href="#">Product Engineering</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="price-shapes">
                <img class="shape one wow fadeInLeft delay-0-5s" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/price-left.png" alt="Shape">
                <img class="shape two" src="<?php echo INCLUDE_PATH; ?>assets/images/shapes/price-right.png" alt="Shape">
            </div>
        </section>
        <!-- Pricing Plan Area end -->
        
        
        <!-- Testimonials Area start -->
        <!-- <section class="testimonials-area py-130 rpy-100 rel z-1">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-xl-5 col-lg-6">
                        <div class="testimonial-left-part rmb-85 wow fadeInLeft delay-0-2s">
                            <div class="section-title mb-45">
                                <span class="sub-title mb-15">Our Testimonials</span>
                                <h2>What Our Clients Say About Solutions</h2>
                            </div>
                            <div class="testi-image-slider">
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author1.jpg" alt="Author">
                                </div>
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author2.jpg" alt="Author">
                                </div>
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author3.jpg" alt="Author">
                                </div>
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author4.jpg" alt="Author">
                                </div>
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author5.jpg" alt="Author">
                                </div>
                                <div class="testi-image-item">
                                    <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testi-author1.jpg" alt="Author">
                                </div>
                            </div>
                            <div class="testi-content-slider">
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                                <div class="testi-content-item">
                                    <p>Sed ut perspiciatis unde omnis iste natus voluptatem accus antiume dolorem queauy antium totam aperiam eaque quae abillosa inventore veritatis etuarchite beatae vitaec voluptas sit aspernatur autodit</p>
                                    <div class="author">
                                        <span class="h4">Andrew D. Bricker</span>
                                        <span>CEO & Founder</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="testimonial-right-part wow fadeInRight delay-0-2s">
                            <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testimonial.jpg" alt="Testimonial">
                            <div class="testi-image-over">
                                <h3>We Have More 3248+ Reviews From Global Clients</h3>
                                <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/signature.png" alt="Signature">
                            </div>
                            <div class="dot-shapes">
                                <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testimonial-dots.png" alt="Dots">
                                <img src="<?php echo INCLUDE_PATH; ?>assets/images/testimonials/testimonial-dots.png" alt="Dots">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonials Area end -->
		<br><br><br><br><br><br>
		<!-- FAQs Area start -->
        <section class="faq-area pb-130 rpb-100 rel z-1">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-xl-5 col-lg-6">
                        <div class="faq-content rmb-65 wow fadeInLeft delay-0-2s">
                            <div class="section-title mb-30 mt-30">
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <span class="sub-title mb-15 editable" data-editable-id="21" data-input-type="text" data-max-length="255"><?= $home['subtitle-9']; ?></span>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                                <div class="editable-wrapper">
                                    <div class="editable-content">
                                        <h2 class="editable" data-editable-id="22" data-input-type="text" data-max-length="255"><?= $home['title-9']; ?></h2>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                            </div>
                            <div class="faq-accordion style-two pt-20" id="faq-accordion">
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            Quais são os serviços e produtos que a Dropi Digital oferece? 
                                        </button>
                                    </h5>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
										<p>A Dropi Digital oferece principalmente serviços de desenvolvimento de sistemas de software.<br>
Tem uma forte atuação em sistemas corporativos de empresas com grandes necessidade de investimentos em Tecnologia da Informação (TI). Tem uma grande experiência em tecnologias muito usadas nesses sistemas. Mas também a Dropi Digital tem experiência no desenvolvimento de sistemas para o mundo moderno de web apps e aplicativos móveis.<br>
Na área de produtos a Dropi Digital conta com um produto próprio de Gestão de Conteúdo (ou o que é conhecido em inglês como CMS - Content Management System). </p>

                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            Quais são os serviços que fornecemos ?
                                        </button>
                                    </h5>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
										<p>A Dropi Digital oferece principalmente serviços de desenvolvimento de sistemas de software. Tem uma forte atuação em sistemas corporativos de empresas com grandes necessidade de investimentos em Tecnologia da Informação (TI). Tem uma grande experiência em tecnologias muito usadas nesses sistemas. Mas também a Visionnaire tem experiência no desenvolvimento de sistemas para o mundo moderno de web apps e aplicativos móveis.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            O que significa Outsourcing de desenvolvimento ?
                                        </button>
                                    </h5>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
                                            <p>Por outsourcing entendemos: out = fora, sourcing = fonte ou fornecimento.<br>
Ou seja, fornecimento de fora, que é o que fazemos fornecendo esses serviços para nossos clientes que não tem como principal negócio o desenvolvimento de sistemas. É uma expressão em inglês normalmente traduzida para português como terceirização.
Portanto, para uma empresa, outsourcing de desenvolvimento significa obter de uma fonte de fora da empresa o desenvolvimento dos sistemas de software.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h5 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            O que significa Outsourcing de aplicação ?
                                        </button>
                                    </h5>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                                        <div class="accordion-body">
 										<p>Para definição de outsourcing ver a resposta anterior.<br>Por aplicação entendemos qualquer produto de software usado por empresas, como por exemplo Sistemas de Gestão (ou em inglês o que é conhecido como ERP - Enterprise Resource Planning), Sistemas de Relacionamento com Clientes (ou em inglês o que é conhecido como CRM - Customer Relationship Management), sistemas internos de uma empresa (como por exemplo, sistemas internos de Recursos Humanos que muitas empresas têm), entre outros. Aplicação também pode ser chamado de aplicativos em alguns casos, apesar de que aplicativos nos dias de hoje leva ao entendimento de aplicações para dispositivos móveis ou web (em inglês é abreviado para apps). Sistemas de infraestrutura como os sistemas operacionais dos computadores (Windows, Linux, OSX) não são considerados aplicações, as aplicações executam “em cima” da infraestrutura já existente.</p>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="faq-images wow fadeInRight delay-0-2s">
                            <div class="logo"><a href="index.html"><img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.jpeg" alt="Logo" title="Logo"></a></div>
                            <div class="editable-wrapper" style="z-index: -1;">
                                <div class="editable-content">
                                    <img class="editable-image" data-editable-id="4" data-input-type="image" src="<?php echo INCLUDE_PATH; ?>assets/images/about/faq-right.jpg" alt="FAQs">
                                </div>
                                <span class="edit-image-icon"><i class='bx bxs-pencil' ></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- FAQs Area end -->
        
        
        
        <br><br>
       <!-- Contact Form Section Start -->
        <section class="contact-form-area py-130 rpy-100 bgs-cover" style="background-image: url(<?php echo INCLUDE_PATH; ?>assets/images/background/contact-form-bg.jpg);">
            <div class="container">
                <div class="row gap-100 align-items-center">
                    <div class="col-lg-7">
                        <div class="contact-form bg-white p-80 rmb-55 wow fadeInRight delay-0-2s">
                            <div class="section-title mb-30">
                                <h3>Entre em contato conosco</h3>
                            </div>
                            <form class="form-style-one" action="#" name="contact-form" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" value="" placeholder="Nome Completo" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" id="phone" name="phone" class="form-control" value="" placeholder="Contato" >
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="email" id="emailid" name="email" class="form-control" value="" placeholder="E-mail" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea name="message" id="message" class="form-control" rows="3" placeholder="Messagem" required=""></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group mb-0">
                                            <button type="submit" class="theme-btn style-two mt-15 w-100">Enviar mensagem! <i class="far fa-long-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="contact-info-wrap wow fadeInLeft delay-0-2s">
                            <div class="section-title mb-40">
                                <div class="editable-wrapper d-inline-block">
                                    <div class="editable-content">
                                        <span class="sub-title mb-10 editable" data-editable-id="23" data-input-type="text" data-max-length="255"><?= $home['subtitle-10']; ?></span>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                                <div class="editable-wrapper d-inline-block">
                                    <div class="editable-content">
                                        <h2 class="editable" data-editable-id="24" data-input-type="text" data-max-length="255"><?= $home['title-10']; ?></h2>
                                    </div>
                                    <span class="edit-icon"><i class='bx bxs-pencil' ></i></span>
                                </div>
                            </div>
                            <div class="contact-info-part">
                                <div class="contact-info-item">
                                    <div class="icon">
                                        <i class="fa-regular fa-map"></i>
                                    </div>
                                    <div class="content">
                                        <span>Localização</span>
                                        <h5>São Paulo</h5>
                                    </div>
                                </div>
                                <div class="contact-info-item">
                                    <div class="icon">
                                        <i class="fa-regular fa-envelope"></i>
                                    </div>
                                    <div class="content">
                                        <span>E-mail</span>
                                        <h5><a href="mailto:suporte@dropidigital.com.br">suporte@dropidigital.com.br</a></h5>
                                    </div>
                                </div>
                                <div class="contact-info-item">
                                    <div class="icon">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </div>
                                    <div class="content">
                                        <span>Contato</span>
                                        <h5><a href="https://api.whatsapp.com/send?phone=5511940496818">+55 11 94049-6818</a></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact Form Section End -->
        
        <BR><BR><BR><BR><BR><BR>
        <!-- footer area start -->
        <footer class="main-footer bgc-gray footer-white rel z-1">
            <div class="footer-cta-wrap">
                <div class="container">
                    <div class="footer-cta-inner bgs-cover" style="background-image: url(<?php echo INCLUDE_PATH; ?>assets/images/footer/footer-cta-bg.jpg);">
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
                                <a href="index.html"><img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.jpeg" alt="Logo"></a>
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
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
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
                                <p>TODOS OS DIREITOS RESERVADOS ©2023 Dropi Digital. <br>Uma empresa especialista em soluções digitais.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-shapes">
                <img class="shape one" src="<?php echo INCLUDE_PATH; ?>assets/images/footer/footer-bg-weve-shape.png" alt="Shape">
                <img class="shape two" src="<?php echo INCLUDE_PATH; ?>assets/images/footer/footer-bg-line-shape.png" alt="Shape">
                <img class="shape three wow fadeInRight delay-0-8s" src="<?php echo INCLUDE_PATH; ?>assets/images/footer/footer-right.png" alt="Shape">
            </div>
        </footer>
        <!-- footer area end -->
        
        <!-- WhatsApp Button -->
        <a href="https://api.whatsapp.com/send?phone=5511940496818" target="_blank" class="home-page whatsapp-button">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
        
        <!-- Scroll Top Button -->
        <button class="scroll-top scroll-to-target" data-target="html"><span class="fas fa-angle-double-up"></span></button>

    </div>
    <!--End pagewrapper-->
   
    
    <!-- Jquery -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/bootstrap.min.js"></script>
    <!-- Appear Js -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/appear.min.js"></script>
    <!-- Slick -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/slick.min.js"></script>
    <!-- Magnific Popup -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Nice Select -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/jquery.nice-select.min.js"></script>
    <!-- Image Loader -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/imagesloaded.pkgd.min.js"></script>
    <!-- Circle Progress -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/circle-progress.min.js"></script>
    <!-- Isotope -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/isotope.pkgd.min.js"></script>
    <!--  WOW Animation -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/wow.min.js"></script>
    <!-- Custom script -->
    <script src="<?php echo INCLUDE_PATH; ?>assets/js/script.js"></script>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
  // Quando o ícone de edição é clicado
  $('.edit-icon').on('click', function() {
    // Obtém o texto atual, o ID do elemento e o limite de caracteres
    var currentText = $(this).prev('.editable-content').find('.editable').text().trim();
    var editableId = $(this).prev('.editable-content').find('.editable').data('editable-id');
    var inputType = $(this).prev('.editable-content').find('.editable').data('input-type');
    var maxLength = $(this).prev('.editable-content').find('.editable').data('max-length');

    // Define o ID no modal
    $('#editableId').val(editableId);

    // Decide se usar um input ou textarea com base no limite de caracteres
    var inputType = (inputType == "text") ? 'input type="text"' : 'textarea';

    // Cria dinamicamente o input ou textarea
    var inputElement = '<' + inputType + ' class="form-control" id="editedText"></' + inputType + '>';
    $('#editContainer').html(inputElement);

    // Adiciona um evento de input para atualizar a contagem de caracteres
    $('#editedText').on('input', function() {
      updateCharCount(this, maxLength);
    });

    // Define o texto atual e o limite no modal
    $('#editedText').val(currentText).attr('maxlength', maxLength);

    // Mostra o modal
    $('#editModal').modal('show');

    // Atualiza a contagem de caracteres inicial
    updateCharCount($('#editedText')[0], maxLength);
  });

  // Quando o botão de salvar é clicado
  $('#saveChanges').on('click', function() {
    // Obtém o texto editado, o ID do elemento e o limite de caracteres
    var editedText = $('#editedText').val();
    var editableId = $('#editableId').val();
    var maxLength = $('.editable[data-editable-id="' + editableId + '"]').data('max-length');

    // Verifica se o texto excede o limite de caracteres
    if (editedText.length > maxLength) {
      alert('O texto não pode exceder ' + maxLength + ' caracteres.');
      return;
    }

    // Atualiza o texto na página
    $('.editable[data-editable-id="' + editableId + '"]').html(editedText.replace(/\n/g, '<br>'));

    // Fecha o modal
    $('#editModal').modal('hide');

    // Faça uma chamada AJAX para salvar as alterações no servidor (PHP)
    $.ajax({
      url: 'salvar_alteracoes.php', // Substitua pelo seu arquivo PHP
      method: 'POST',
      data: { id: editableId, texto: editedText },
      success: function(response) {
        // Processar a resposta do servidor, se necessário
        console.log(response);
      },
      error: function(error) {
        console.error('Erro ao salvar as alterações:', error);
      }
    });
  });

  // Função para atualizar a contagem de caracteres
  function updateCharCount(element, maxLength) {
    var charCount = element.value.length;
    $('.char-count').text(charCount + '/' + maxLength);
  }
});
</script>

<!-- Update Image -->
<script>
$(document).ready(function() {
  // Quando o ícone de edição de imagem é clicado
  $('.edit-image-icon').on('click', function() {
    var currentImage = $(this).prev('.editable-content').find('.editable-image');
    var editableId = currentImage.data('editable-id');
    var currentSrc = currentImage.attr('src');

    // Define o ID e a fonte de imagem atual no modal para referência
    $('#updateImage').data('editableId', editableId);
    $('#preview').attr('src', currentSrc).show();
    $('#text-2').css('display', 'none');

    // Mostra o modal
    $('#updateImage').modal('show');
  });

  // Quando seleciona uma imagem para upload
  $('#image').change(function() {
    if (this.files && this.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#preview').attr('src', e.target.result).show();
      }

      reader.readAsDataURL(this.files[0]);
    }
  });

  // Quando o botão de atualizar imagem é clicado
  $('#updateImageButton').on('click', function() {
    var editableId = $('#updateImage').data('editableId');
    var newImageSrc = $('#preview').attr('src');

    // Atualiza a imagem na página
    $('.editable-image[data-editable-id="' + editableId + '"]').attr('src', newImageSrc);

    // Fecha o modal
    $('#updateImage').modal('hide');

    // Faça uma chamada AJAX para salvar a nova imagem no servidor
    $.ajax({
      url: 'salvar_imagem.php', // Substitua pelo seu arquivo PHP para processar o upload da imagem
      method: 'POST',
      data: { id: editableId, imagem: newImageSrc },
      processData: false,
      contentType: false,
      success: function(response) {
        // Processar a resposta do servidor, se necessário
        console.log(response);
      },
      error: function(error) {
        console.error('Erro ao salvar a imagem:', error);
      }
    });
  });
});
</script>

<!-- Partner -->
<script>
    $(document).ready(function() {
        var partners = []; // Array para armazenar os parceiros temporariamente

        $('#addPartnerModal .btn-success').click(function() {
            var name = $('#namePartner').val().trim();
            var link = $('#linkPartner').val().trim();
            var fileInput = $('#partner-image')[0];

            if (name === "" || link === "" || fileInput.files.length === 0) {
                alert("Por favor, preencha todos os campos e selecione uma imagem.");
                return;
            }

            var file = fileInput.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                var imageUrl = e.target.result;

                // Adiciona ao array
                partners.push({
                    name: name,
                    link: link,
                    image: imageUrl // Este é um Data URL
                });

                console.log(partners);

                // Aqui, vamos criar e adicionar o HTML do novo parceiro ao container
                var newPartnerHTML = '<div class="col col-custom-5">' +
                                    '<a href="' + link + '" class="partner-item wow fadeInUp delay-0-2s" target="_blank">' +
                                    '<img src="' + imageUrl + '" alt="Partner" style="width:100%; height:auto;">' +
                                    '</a></div>';

                // Insere o novo parceiro antes do botão de adicionar novo parceiro
                $(newPartnerHTML).insertBefore('.add-partner');

                // Limpa o modal
                $('#addPartnerModal').find('input').val('');
                $('#partner-image').val('');

                // Limpa o preview
                $('#partner-image-preview').css('display', 'none');
                $('#partner-image-preview').css('display', 'none');
                $('#text-1').css('display', 'block');

                // Oculta o modal
                $('#addPartnerModal').modal('hide');
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        // Função para enviar todos os parceiros de uma vez
        $('#save-all').click(function() {
            if (partners.length === 0) {
                alert("Adicione pelo menos um parceiro antes de salvar.");
                return;
            }

            $.ajax({
                url: 'seu_script_de_salvamento.php', // Substitua pela URL do seu script de backend
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ partners: partners }),
                success: function(response) {
                    // Tratamento de sucesso
                    alert('Parceiros salvos com sucesso!');
                    partners = []; // Limpa o array após o sucesso
                },
                error: function() {
                    // Tratamento de erro
                    alert('Erro ao salvar parceiros. Tente novamente.');
                }
            });
        });
    });
</script>
<?php
    }
?>