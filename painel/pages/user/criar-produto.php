<?php
    if (isset($_GET['product']) && $_GET['product'] == "kiwify") {
        // Nome da tabela dos produtos Kiwify
        $tabela = 'tb_kiwify_products';

        // Verificar se há uma busca realizada
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

        $sql = "SELECT * FROM $tabela WHERE id = :product_id ORDER BY id ASC";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':product_id', $product_id);

        // Executar a consulta
        $stmt->execute();

        // Recuperar os resultados
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $_GET['short_id'] = $product['short_id'];
            $_GET['name'] = $product['name'];
            $_GET['price'] = $product['price'];
            $_GET['product_img'] = $product['product_img'];
        }
    } else if (isset($_GET['product']) && $_GET['product'] == "clickbank") {
        // Nome da tabela dos produtos Kiwify
        $tabela = 'tb_clickbank_products';

        // Verificar se há uma busca realizada
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

        $sql = "SELECT * FROM $tabela WHERE id = :product_id ORDER BY id ASC";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':product_id', $product_id);

        // Executar a consulta
        $stmt->execute();

        // Recuperar os resultados
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $_GET['short_id'] = $product['id'];
            $_GET['name'] = $product['title'];
            $_GET['price'] = $product['price'];
            $_GET['description'] = $product['description'];
        }
    }
?>
<?php
    // Consulta SQL para contar os produtos na tabela
    // Nome da tabela para a busca
    $tabela = 'tb_products';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela WHERE shop_id = :shop_id AND status = :status";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindParam(':shop_id', $id);
    $stmt->bindValue(':status', 1);
    $stmt->execute();

    // Recupere o resultado da consulta
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // O resultado contém o total de produtos na chave 'total_produtos'
    $totalProdutos = $resultado['total_produtos'];
?>
<!-- Codigo da Imagem dos produtos -->
<style>
    label.image-container {
        background-color: #f9f9f9;
        width: 100%;
        padding: 3.12em 1.87em;
        border: 2px dashed #c4c4c4;
        border-radius: 0.5em;
        cursor: pointer;
    }
    input[type="file"] {
        display: none;
    }
    .dropzone {
        min-height: 0px;
        display: block;
        position: relative;
        color: #000;
        background: none;
        font-size: 1.1em;
        text-align: center;
        padding: 1em 0;
        border: none;
        border-radius: 0.3em;
        margin: 0 auto;
        cursor: pointer;
    }
    .sortable-container {
        overflow: hidden;
    }
    #image-display {
        position: relative;
        width: 100%;
        display: flex;
        gap: 1.25em;
        flex-wrap: wrap;
    }
    #image-display figure {
        position: relative;
        width: 118px;
        height: 118px;
        background: #f9f9f9;
    }
    #image-display img {
        width: 118px;
        height: 118px;
        object-fit: cover;
        border: 1px solid #c4c4c4;
        border-radius: .5rem;
    }
    #image-display img:hover
    {
        cursor: -webkit-grab;
        cursor: grab;
    }
    #image-display button {
        position: absolute;
        top: 10px;
        right: 10px;
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
    #image-display button::before {
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
    #image-display figcaption {
        font-size: 0.8em;
        text-align: center;
        color: #5a5861;
    }
    .sortable-image:first-of-type::before {
        content: 'Capa';
        position: absolute;
        left: 10px;
        bottom: 10px;
        color: #c4c4c4;
        font-size: .875rem;
        background: #f9f9f9;
        padding: 0 0.5rem;
        border: 1px solid #c4c4c4;
        border-radius: 0.3rem;
    }
    #affiliate-product-image.sortable-image::before {
        content: 'Img Produto' !important;
        font-size: .75rem !important;
    }
    .dropzone-active {
        border: 0.2em dashed #025bee;
    }
    #error {
        text-align: center;
        color: #ff3030;
    }
</style>

<!-- Codigo do site -->
<style>
    #imagePreviews {
        display: flex;
        flex-wrap: wrap;
    }

    .image-preview {
        position: relative;
        margin-right: 10px;
    }

    .image-preview img {
        max-width: 100px;
        height: auto;
        cursor: pointer;
    }

    .remove-image {
        position: absolute;
        top: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        padding: 2px 5px;
        cursor: pointer;
    }

    /* Image */
    #imageInput
    {
        display: none;
    }
    /* .dropzone
    {
        height: 100px;
        background: #c4c4c4;
    } */
    
    /* SEO Preview */
    .seo-preview
    {
        border: 2px dashed #ddd;
    }
</style>

<!-- Estilo do celular -->
<style>
    .text-preview
    {
        width: 150px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .text-preview i
    {
        font-size: 4rem;
    }

    .cell-phone
    {
        width: 300px;
        height: 500px;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: .6rem;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, .25);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cell-phone .screen
    {
        position: relative;
        width: 280px;
        height: 420px;
        background: url("<?php echo INCLUDE_PATH; ?>assets/images/whatsapp/wallpaper.jpg") no-repeat;
        background-size: cover;
    }
    .cell-phone .screen .header
    {
        width: 100%;
        height: 40px;
        background: #ededed;
        display: flex;
        align-items: center;
        padding: 0 .75rem;
    }
    .cell-phone .screen .header .profile-picture
    {
        width: 30px;
        height: 30px;
        margin-right: .5rem;
    }
    .cell-phone .screen .header .profile-picture img
    {
        border-radius: 50%;
    }
    .cell-phone .screen .screen-container
    {
        position: relative;
        width: 100%;
        height: calc(420px - 90px);
    }
    .cell-phone .screen .screen-container .preview-message
    {
        position: absolute;
        right: 15px;
        bottom: 10px;
        max-width: 240px;
        height: min-content;
        background: #DCF8C6;
        padding: .5rem;
        border-radius: .3rem;
        z-index: 1;
    }
    .cell-phone .screen .screen-container .preview-message::before
    {
        content: "";
        width: 0;
        height: 0;
        border-right: 20px solid transparent;
        border-top: 20px solid #DCF8C6;
        position: absolute;
        right: -10px;
        top: 0;
        z-index: 0;
    }
    .cell-phone .screen .message-container
    {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: max-content;
        background: #f0f0f0;
        display: flex;
        align-items: flex-end;
        padding: .5rem .75rem;
    }
    .cell-phone .screen .message-container .message
    {
        width: 85%;
        min-height: 30px;
        height: max-content;
        padding: 0 1rem;
        background: #fff;
        border-radius: 15px;
    }
    .cell-phone .screen .message-container .papper-plane
    {
        width: 15%;
        display: flex;
        justify-content: flex-end;
    }
    .cell-phone .screen .message-container .papper-plane i.bx
    {
        font-size: 1.5rem;
        color: #cacdcf;
    }

    .link
    {
        cursor: pointer;
    }

    .btn.btn-success
    {
        background: var(--green-color);
        border-color: var(--green-color);
    }
</style>

<style>
    #categoriasModal table tbody tr td.checkbox
    {
        width: 16px;
    }

    #categoriesTable tbody tr td.remove
    {
        width: 20px;
    }

    .mainCategory
    {
        display: none;
        color: var(--green-color);
        cursor: pointer;
    }
    td:hover .mainCategory
    {
        display: inline-block;
    }
    .mainActive
    {
        display: inline-flex !important;
    }
</style>

<!-- Modal de Categorias -->
<div class="modal fade" id="criarCategoriasModal" tabindex="-1" role="dialog" aria-labelledby="categoriasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_category-in-product.php" method="post" id="createCategory">
                <div class="modal-header px-4 py-3 bg-transparent">
                    <div class="fw-semibold py-2">
                        Cadastrar categoria
                    </div>
                </div>
                <div class="modal-body px-4 py-3">
                    <div>
                        <label for="categoryName" class="form-label small">Nome da categoria *</label>
                        <input type="text" class="form-control" name="name" id="categoryName" aria-describedby="categoryNameHelp" required>
                    </div>
                </div>
                <input type="hidden" name="link" id="categoryLink">
                <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                <div class="modal-footer fw-semibold px-4">
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Categorias -->
<div class="modal fade" id="categoriasModal" tabindex="-1" role="dialog" aria-labelledby="categoriasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Escolher categorias
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <!-- Adicione aqui a lógica para exibir as categorias do banco de dados e a funcionalidade de pesquisa -->
                <input type="text" id="searchCategoria" class="form-control mb-3" placeholder="Pesquisar Categorias">
                <p class="fw-semibold d-none" id="noResultCategories">Nenhuma categoria encontrada</p>
                <table class="table" id="resultCategories">
                    <tbody id="listaCategorias">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                            </tr>
                        </thead>
                        <!-- Categorias serão exibidas aqui -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success fw-semibold px-4 py-2 small" onclick="adicionarCategorias()">Selecionar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Produtos -->
<div class="modal fade" id="produtosModal" tabindex="-1" role="dialog" aria-labelledby="produtosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Escolher produto
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="text" id="searchProduto" class="form-control mb-3" placeholder="Pesquisar Produtos">
                <p class="fw-semibold d-none" id="noResultProducts">Nenhum produto encontrado</p>
                <table class="table" id="resultProducts">
                    <tbody id="listaProdutos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <!-- Produtos serão exibidos aqui -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="adicionarProdutos()">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_product.php" method="post" enctype="multipart/form-data">

    <div class="page__header center">
        <div class="header__title">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-3">
                    <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>produtos" class="fs-5 text-decoration-none text-reset">Produtos</a></li>
                    <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Produto</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label for="name" class="form-label small">Nome do produto *</label>
                    <small id="nameCounter" class="form-text text-muted">0 de 120 caracteres</small>
                </div>
                <input type="text" class="form-control" name="name" id="name" maxlength="120" aria-describedby="nameHelp" value="<?php echo @$_GET['name']; ?>" require>
                <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/produto/<span class="fw-semibold" id="linkPreview">...</span></p>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="language" class="form-label small">Idioma</label>
                    <select class="form-select" id="language" name="language">
                        <option value="pt" selected>Português</option>
                        <option value="en">Inglês</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex justify-content-between mb-3">
                    <div>
                        <label for="activeProduct" class="form-label small">
                            Produto ativo?
                            <i class="bx bx-help-circle <?php echo ($limitProducts <= $totalProdutos) ? "" : "d-none"; ?>" data-toggle="tooltip" data-placement="top" aria-label="Você ultrapassou o limite de produtos ativos. Para habilitar novos produtos, considere a contratação de um plano com maior capacidade!" data-bs-original-title="Você ultrapassou o limite de produtos ativos. Para habilitar novos produtos, considere a contratação de um plano com maior capacidade!"></i>
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="activeProduct" <?php echo ($limitProducts <= $totalProdutos) ? "value='0' disabled" : "value='1' checked"; ?>>
                            <label class="form-check-label" id="activeCheckbox" for="activeProduct"><?php echo ($limitProducts <= $totalProdutos) ? "Não" : "Sim"; ?></label>
                        </div>
                    </div>
                    <div id="containerEmphasis">
                        <label for="emphasisProduct" class="form-label small">Destacar no menu?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasisProduct" value="1">
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasisProduct">Não</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Preços</div>
        <div class="card-body row px-4 py-3">
            <div class="col-md-6">
                <label for="moneyInput1" class="form-label small">Preço de Custo *</label>
                <div class="input-group mb-2">
                    <span class="input-group-text" id="currencySymbol">R$</span>
                    <input type="number" step="0.01" class="form-control text-end" name="price" id="moneyInput1" placeholder="0,00" value="<?php echo @$_GET['price']; ?>">
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="without_price" id="withoutPrice">
                    <label class="form-check-label" for="withoutPrice">Sem preço</label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="moneyInput2" class="form-label small">Preço promocional</label>
                <div class="input-group">
                    <span class="input-group-text" id="currencySymbolPromo">R$</span>
                    <input type="number" step="0.01" class="form-control text-end" name="discount" id="moneyInput2" placeholder="0,00">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Imagens
            <label for="upload-button" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="upload-button" class="image-container mt-3">
                <input type="file" name="imagens[]" id="upload-button" multiple accept="image/*" />
                <div for="upload-button" class="dropzone">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                </div>
                <div id="error"></div>
            </label>
            <div class="sortable-container mt-3">
                <div id="image-display">
                    <?php if (isset($_GET) && !empty($_GET['product_img'])) { ?>
                    <figure class="sortable-image" id="affiliate-product-image">
                        <img src="<?php echo $_GET['product_img']; ?>" alt="Product Image">
                        <button type="button" id="remove-affiliate-product-image" class="remove-image-btn"></button>
                    </figure>
                    <?php } ?>
                </div>
            </div>
            <!-- Link imagem do produto kiwify -->
            <input type="hidden" name="product_img" id="product_img" value="<?php echo @$_GET['product_img']; ?>">
        </div>
        <div class="card-footer fw-semibold px-4 py-3 bg-transparent">
            <div class="d-flex justify-content-between">
                <label for="exampleInputEmail" class="form-label small">Vídeo do produto</label>
                <p class="form-text text-muted fw-normal small">Aceitamos apenas vídeos do YouTube</p>
            </div>
            <div class="position-relative">
                <i class='bx bxl-youtube input-icon' ></i>
                <input type="text" class="form-control icon-padding" name="video" id="video-url" placeholder="https://www.youtube.com/watch?v=000" aria-label="https://www.youtube.com/watch?v=000">
            </div>
            <div id="video-display" class="d-flex justify-content-center"></div>
        </div>
    </div>

    <style>
        #loaderButton {
            display: flex;
            justify-content: center;
        }

        .loader {
            width: 14px;
            height: 14px;
            border: 1.5px solid var(--green-color);
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Descrição do produto
            <label id="generate-description" class="d-flex align-items-center small" style="color: var(--green-color); cursor: pointer;"><i class='bx bxs-magic-wand me-1'></i> Gerar com IA <div class="loader ms-1 d-none"></div></label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="editor" class="form-label small">Descrição do produto</label>
            <textarea name="description" id="editor"><?php echo @$_GET['description']; ?></textarea>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">
            Categorias
            <p class="link text-decoration-none text-reset small" data-bs-toggle="modal" data-bs-target="#criarCategoriasModal">+ Cadastrar categoria</p>
        </div>
        <div class="card-body px-5 py-3">
            <label for="searchOutsideModal" class="form-label small">
                Categorias
                <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
            </label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="searchOutsideModal" placeholder="Buscar categorias já cadastradas" aria-label="Buscar categorias já cadastradas">
                <button type="button" class="btn btn-outline-dark fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#categoriasModal">Ver Categorias</button>
            </div>
            <small class="d-flex mb-3 px-3 py-2" id="noCategories" style="color: #4A90E2; background: #ECF3FC;">Nenhuma categoria adicionada</small>
            <table class="table table-hover d-none" id="categoriesTable">
                <thead class="table-light">
                    <tr>
                        <th class="small">Nome da Categoria</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="categoriasSelecionadas"></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">SKU</div>
        <div class="card-body px-5 py-3">
            <div class="mb-3">
                <label for="skuResult" class="form-label small">
                    Código SKU
                    <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" name="sku" id="skuResult" placeholder="SKU" aria-label="SKU" aria-describedby="skuResult" style="max-width: 250px;">
                    <button class="btn btn-outline-dark fw-semibold px-4" type="button" id="gerarSKU">GERAR</button>
                </div>
                <small class="text-decoration-none" id="error-sku" style="color: rgb(229, 15, 56);"></small>
            </div>
        </div>
    </div>
    
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">Chamada de ação</div>
        <div class="card-body px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="button_type" class="form-label small">Tipo do botão *</label>
                        <div class="input-group">
                            <select class="form-select" name="button_type" id="buttonType" aria-label="Default select example" required>
                                <option value="" <?php echo (!isset($_GET['link'])) ? "selected" : ""; ?> disabled>-- Selecione uma opção --</option>
                                <option value="1" id="buyText" <?php echo (isset($_GET['link'])) ? "selected" : ""; ?>>Comprar</option>
                                <option value="2">Número de whatsapp - Mensagem Padrão</option>
                                <option value="3">Número de whatsapp - Mensagem Personalizada</option>
                                <option value="4">Saiba mais</option>
                                <option value="5">Agenda</option>
                                <option value="6">Cadastrar</option>
                            </select>
                        </div>
                    </div>
                    <!-- Opcao do whatsapp -->
                    <div class="d-none" id="container-whatsapp">
                        <div class="mb-3 row">
                            <label for="phone-number" class="form-label small">Número do WhatsApp *</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="country-code" id="country-code" placeholder="+55" value="+55">
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="phone-number" id="phone-number" placeholder="(00) 00000-0000">
                            </div>
                            <small class="m-0">Lembre-se de verificar o código de seu país</small>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label small">Mensagem personalizada</label>
                            <textarea class="form-control" name="message" id="message" rows="3" placeholder="Use esse espaço para adicionar uma mensagem personalizada que será enviada pelo seu link de WhatsApp :)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="linkWhatsapp" class="form-label small">Link gerado</label>
                            <p><a href="" target="_blank" class="small text-decoration-none" id="linkWhatsapp" style="color: #01C89B;"></a></p>
                        </div>
                        <input type="hidden" name="redirect_link_whatsapp" id="inputLinkWhatsapp">
                    </div>
                </div>

                <!-- Celular para opcao do whatsapp -->
                <div class="col-md-6 d-none" id="container-cell-phone">
                    <div class="preview-cell-phone d-flex align-items-center justify-content-around">
                        <div class="text-preview">
                            <div class="arrow">
                                <i class='bx bx-chevron-right' ></i>
                            </div>
                            <small>É assim que seus usuários o verão</small>    
                        </div>
                        <div class="cell-phone">
                            <div class="screen">
                                <div class="header">
                                    <div class="profile-picture">
                                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/whatsapp/user.svg" alt="Foto de perfil">
                                    </div>
                                    <div class="phone-number" id="preview-phone-number">
                                        +55 (00) 00000-0000
                                    </div>
                                </div>
                                <div class="screen-container">
                                    <div class="preview-message d-none" id="preview-message"></div>
                                </div>
                                <div class="message-container">
                                    <div class="message" id="writing-message"></div>
                                    <div class="papper-plane">
                                        <i class='bx bxs-paper-plane'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3 <?php echo (!isset($_GET['link'])) ? "d-none" : ""; ?>" id="container-redirect-link">
                <label for="redirectLink" class="form-label small">Link de redirecionamento *</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="redirect_link" id="redirectLink" aria-describedby="redirect-linkHelp" value="<?php echo @$_GET['link']; ?>">
                    <button type="button" class="btn btn-secondary px-4" id="botaoColar">Colar Link</button>
                </div>
            </div>

            <div class="mb-3 d-none" id="container-whatsapp-standard">
                <p class="form-label small">Whatsapp - Mensagem padrão</p>
                <a href="" class="fw-semibold text-decoration-none" id="linkWhatsappStandard" target="_black" style="color: #01C89B;"></a>
                <input type="hidden" name="redirect_link_whatsapp_standard" id="inputLinkWhatsappStandard">
            </div>

        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">
            Produtos Relacionados
        </div>
        <div class="card-body px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="selectMode" class="form-label small">Modo de Relacionamento de Produtos</label>
                        <select class="form-select mb-3" name="selectMode" id="selectMode">
                            <option value="automatic" selected>Automático</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Campo de seleção de produtos -->
            <div id="campoSelecaoProdutos" style="display: none;">
                <label for="searchProductOutsideModal" class="form-label small">
                    Produtos
                    <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
                </label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchProductOutsideModal" placeholder="Buscar produtos já cadastrados" aria-label="Buscar produtos já cadastrados">
                    <button type="button" class="btn btn-outline-dark fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#produtosModal">Ver Produtos</button>
                </div>
                <small class="d-flex mb-3 px-3 py-2" id="noProducts" style="color: #4A90E2; background: #ECF3FC;">Nenhum produto adicionado</small>
                <table class="table table-hover d-none" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Nome do Produto</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="produtosSelecionados"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">Google / SEO</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput1" class="form-label small">Nome do produto *</label>
                            <small id="textCounter1" class="form-text text-muted">0 de 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp" value="<?php echo @$_GET['name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da página</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da página</label>
                            <small id="textCounter3" class="form-text text-muted">0 de 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1">Título da página</h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="textPreview2">link-da-pagina</span></p>
                        <p class="small" id="textPreview3">Descrição da página</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <!-- Adicione esses campos ocultos no seu formulário -->
    <input type="hidden" name="categoriasSelecionadas[]" id="categoriasSelecionadasInput">

    <!-- Adicione esses campos ocultos no seu formulário -->
    <input type="hidden" name="produtosSelecionados[]" id="produtosSelecionadosInput">

    <!-- Categoria principal -->
    <input type="hidden" name="inputMainCategory" id="inputMainCategory">

    <!-- Id do produto kiwify -->
    <input type="hidden" name="product_id" value="<?php echo @$_GET['short_id']; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>

</form>

<!-- Link para o TinyMCE CSS -->
<script src="https://cdn.tiny.cloud/1/<?= $tinyKey; ?>/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Alterar idioma do produto -->
<script>
    $(document).ready(function () {
        $('#language').on('change', function () {
            const selectedLanguage = $(this).val();
            const currencySymbol = $('#currencySymbol, #currencySymbolPromo');
            const buyText = $('#buyText');

            if (selectedLanguage === 'en') {
                currencySymbol.text('$');
                buyText.text('Buy (Comprar)');
            } else {
                currencySymbol.text('R$');
                buyText.text('Comprar');
            }
        });
    });
</script>

<!-- Remover imagem vinda do produto -->
<script>
    $(document).ready(function() {
        // Função para remover a imagem e limpar o campo hidden
        $('#remove-affiliate-product-image').on('click', function() {
            // Remove a imagem do display
            $('#affiliate-product-image').remove();
            // Limpa o campo hidden de product_img
            $('#product_img').val('');
        });
    });
</script>

<!-- Gerador de descricao com ia -->
<script>
    $(document).ready(function () {
        $("#generate-description").click(function () {
            var type = "description";
            var productName = $('#name').val();
            if (productName !== "") {
                generateProductDescription(type, productName);
            } else {
                alert("Por favor, insira um nome para o produto antes de gerar a descrição com IA.");
            }
        });

        function generateProductDescription(type, keyword) {
            $('#generate-description .loader').removeClass('d-none');

            $.ajax({
                url: "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/chat-gpt-api.php",
                method: "POST",
                dataType: "json",
                data: {
                    action: type,
                    shop_id: <?php echo $id; ?>,
                    type: type,
                    keyword: keyword,
                    plan: <?php echo $plan_id; ?>
                },
                success: function (response) {
                    $('#generate-description .loader').addClass('d-none');

                    if (response.error) {
                        alert("Erro ao processar a requisição: " + response.error);
                        return;
                    }

                    tinymce.get('editor').setContent(response.description);
                },
                error: function (xhr, status, error) {
                    $('#generate-description .loader').addClass('d-none');

                    console.error("Erro na requisição AJAX:", error);
                    alert("Erro ao processar a requisição. Por favor, tente novamente mais tarde.");
                }
            });
        }
    });
</script>

<!-- Link para criar category -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#categoryName");
        var link = $("#categoryLink");

        input.on("input", function() {
            var value = input.val();

            // Remover acentos e substituir espaços por traço
            value = removerAcentosEespacos(value);

            link.val(value);
        });

        function removerAcentosEespacos(texto) {
            // Remove acentos usando normalize e substitui espaços por traço
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, "-").toLowerCase();
        }
    });
</script>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_categories';

    $sql = "SELECT id, name FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    // Fetch all retorna um array contendo todas as linhas do conjunto de resultados
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Script jQuery para manipular o modal e as categorias -->
<script>
    $(document).ready(function() {
        // Array de categorias (substitua com a lógica do seu banco de dados)
        var categoriasDisponiveis = <?php echo json_encode($categories); ?>;

        // Categorias selecionadas
        var categoriasSelecionadas = [];

        // Usando jQuery para lidar com o envio do formulário de criação de categoria
        $('#createCategory').submit(function (event) {
            event.preventDefault();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        var novaCategoria = {
                            id: parseInt(response.data.id), // Converte o ID para um número
                            name: response.data.name
                        };

                        categoriasDisponiveis.push(novaCategoria);
                        categoriasSelecionadas.push(novaCategoria);

                        adicionarCategoriaNaTabela(novaCategoria);

                        $('#criarCategoriasModal').modal('hide');
                    } else {
                        alert('Erro ao criar a categoria.');
                    }
                },
                error: function () {
                    alert('Erro ao enviar a requisição.');
                }
            });
        });

        // Função para adicionar uma nova categoria à tabela de categorias
        function adicionarCategoriaNaTabela(categoria) {
            var tabelaCategorias = $("#categoriesTable");
            tabelaCategorias.removeClass('d-none');

            var semCategoria = $("#noCategories");
            semCategoria.addClass('d-none');

            var categoriasSelecionadasDiv = $("#categoriasSelecionadas");
            categoriasSelecionadasDiv.append('<tr><td>' + categoria.name +
                '<span class="mainCategory ms-2" data-categoria="' + categoria.id + '"><i class="bx bx-star" ></i></span></td><td class="remove"><span class="remover-categoria" data-categoria="' + categoria.id + '"><i class="bx bx-x fs-5"></i></span></td></tr>');

            $(".remover-categoria").click(function () {
                var categoriaRemover = $(this).data("categoria");
                removerCategoria(categoriaRemover);
            });

            $(".mainCategory").click(function () {
                var isActive = $(this).hasClass("mainActive");

                // Remove as classes de todas as categorias
                $('.mainCategory i').removeClass("bxs-star");
                $('.mainCategory').removeClass("mainActive");
                $('#inputMainCategory').val("");

                if (!isActive) {
                    // Adiciona as classes se a categoria não estiver ativa
                    $(this).addClass("mainActive");
                    $(this).find('i').addClass("bxs-star");

                    var mainCategoryId = $(this).data("categoria");
                    $('#inputMainCategory').val(mainCategoryId);
                }
            });
        }

        // Função para exibir categorias selecionadas
        function exibirCategoriasSelecionadas() {
            var semCategoria = $("#noCategories");
            var tabelaCategorias = $("#categoriesTable");
            var categoriasSelecionadasDiv = $("#categoriasSelecionadas");
            categoriasSelecionadasDiv.empty();

            if (categoriasSelecionadas.length === 0) {
                // Se nenhuma categoria estiver selecionada, adiciona a classe d-none
                tabelaCategorias.addClass('d-none');
                semCategoria.removeClass('d-none');
            } else {
                tabelaCategorias.removeClass('d-none');
                semCategoria.addClass('d-none');

                categoriasSelecionadas.forEach(function(categoria) {
                    categoriasSelecionadasDiv.append('<tr><td>' + categoria.name +
                        '<span class="mainCategory ms-2" data-categoria="' + categoria.id + '"><i class="bx bx-star" ></i></span></td><td class="remove"><span class="remover-categoria" data-categoria="' + categoria.id + '"><i class="bx bx-x fs-5"></i></span></td></tr>');
                });

                // Adiciona o evento de clique para remover a categoria
                $(".remover-categoria").click(function() {
                    var categoriaRemover = $(this).data("categoria");
                    removerCategoria(categoriaRemover);
                });

                // Adiciona o evento de clique para alternar a categoria principal
                $(".mainCategory").click(function () {
                    var isActive = $(this).hasClass("mainActive");

                    // Remove as classes de todas as categorias
                    $('.mainCategory i').removeClass("bxs-star");
                    $('.mainCategory').removeClass("mainActive");
                    $('#inputMainCategory').val("");

                    if (!isActive) {
                        // Adiciona as classes se a categoria não estiver ativa
                        $(this).addClass("mainActive");
                        $(this).find('i').addClass("bxs-star");

                        var mainCategoryId = $(this).data("categoria");
                        $('#inputMainCategory').val(mainCategoryId);
                    }
                });
            }
        }

        // Função para exibir categorias no modal
        function exibirCategorias() {
            var listaCategorias = $("#listaCategorias");
            listaCategorias.empty();

            // Verificar se o array categoriasDisponiveis está vazio
            if (categoriasDisponiveis.length === 0) {
                $("#noResultCategories").removeClass("d-none");
                $("#resultCategories").addClass("d-none");
            } else {
                $("#noResultCategories").addClass("d-none");
                $("#resultCategories").removeClass("d-none");

                categoriasDisponiveis.forEach(function(categoria) {
                    var isChecked = categoriasSelecionadas.some(cs => cs.id === categoria.id);

                    listaCategorias.append('<tr><td class="checkbox" scope="row">' +
                        '<input class="form-check-input" type="checkbox" id="' + categoria.id + '" value="' + categoria.id + '" ' + (isChecked ? 'checked' : '') + '>' +
                        '</td><td><label for="' + categoria.id + '" class="form-check-label">' + categoria.name + '</label></td></tr>');
                });
            }
        }

        // Atualizar categorias ao abrir o modal
        $('#categoriasModal').on('show.bs.modal', function () {
            exibirCategorias();
        });

        // Adicionar categorias selecionadas ao formulário
        window.adicionarCategorias = function() {
            $("input[type='checkbox']:checked").each(function() {
                var categoriaId = parseInt($(this).val());
                var categoria = categoriasDisponiveis.find(c => c.id === categoriaId);

                if (categoria && !categoriasSelecionadas.some(cs => cs.id === categoria.id)) {
                    categoriasSelecionadas.push(categoria);
                }
            });

            // Remover categoria se o checkbox for desmarcado no modal
            $("#listaCategorias input[type='checkbox']").each(function() {
                var categoriaId = parseInt($(this).val());
                var categoria = categoriasDisponiveis.find(c => c.id === categoriaId);

                if (!$(this).prop("checked") && categoria) {
                    removerCategoria(categoria.id);
                }
            });

            exibirCategoriasSelecionadas();
            atualizarCampoCategorias();

            // Fechar o modal
            $('#categoriasModal').modal('hide');
        };

        // Função para remover uma categoria
        window.removerCategoria = function(categoriaId) {
            categoriasSelecionadas = categoriasSelecionadas.filter(cs => cs.id !== categoriaId);

            // Atualizar o campo de categorias oculto no formulário
            atualizarCampoCategorias();

            exibirCategoriasSelecionadas();
        };

        // Função para atualizar o campo de categorias oculto no formulário
        function atualizarCampoCategorias() {
            var categoriasIds = categoriasSelecionadas.map(cs => cs.id);
            $("#categoriasSelecionadasInput").val(categoriasIds.join(','));
        }

        // Adiciona um ouvinte de evento de entrada ao campo #searchOutsideModal
        $('#searchOutsideModal').on('input', function() {
            var valorPesquisa = $(this).val();

            // Define o valor no campo #searchCategoria
            $('#searchCategoria').val(valorPesquisa);

            // Abre o modal e foca no campo #searchCategoria
            $('#categoriasModal').modal('show').on('shown.bs.modal', function () {
                $('#searchCategoria').focus();
            });
        });

        // Filtrar categorias com base na pesquisa
        $("#searchCategoria").on("input", function() {
            var termoPesquisa = $(this).val().toLowerCase();

            if (termoPesquisa === "") {
                exibirCategorias();
            } else {
                var categoriasFiltradas = categoriasDisponiveis.filter(function(categoria) {
                    return categoria.name.toLowerCase().includes(termoPesquisa);
                });

                pesquisarCategoria = categoriasFiltradas;

                pesquisarCategorias();

                // Verificar se não há categorias na pesquisa
                if (pesquisarCategoria.length === 0) {
                    $("#noResultCategories").removeClass("d-none");
                    $("#resultCategories").addClass("d-none");
                } else {
                    $("#noResultCategories").addClass("d-none");
                    $("#resultCategories").removeClass("d-none");
                }
            }
        });

        // Função para exibir categorias no modal
        function pesquisarCategorias() {
            var listaCategorias = $("#listaCategorias");
            listaCategorias.empty();

            pesquisarCategoria.forEach(function(categoria) {
                var isChecked = categoriasSelecionadas.some(cs => cs.id === categoria.id);

                listaCategorias.append('<tr><td class="checkbox" scope="row">' +
                    '<input class="form-check-input" type="checkbox" id="' + categoria.id + '" value="' + categoria.id + '" ' + (isChecked ? 'checked' : '') + '>' +
                    '</td><td><label for="' + categoria.id + '" class="form-check-label">' + categoria.name + '</label></td></tr>');
            });

            // Certificar-se de remover a classe d-none ao exibir todas as categorias
            $("#noResultCategories").addClass("d-none");
            $("#resultCategories").removeClass("d-none");
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Monitorar alterações no select
        $("#selectMode").change(function() {
            var selectedMode = $(this).val();
            if (selectedMode === "manual") {
                // Exibir campo de seleção de produtos
                $("#campoSelecaoProdutos").show();
            } else {
                // Ocultar campo de seleção de produtos
                $("#campoSelecaoProdutos").hide();
            }
        });

        // Inicializar estado com base no valor selecionado
        $("#selectMode").trigger("change");
    });
</script>

<?php
    $sql = "SELECT p.id, i.nome_imagem AS image, p.name, 
                CASE 
                    WHEN p.status = 1 THEN 'Ativo' 
                    WHEN p.status = 0 THEN 'Inativo' 
                END AS status 
            FROM tb_products p 
            LEFT JOIN imagens i ON p.id = i.usuario_id
            WHERE p.shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    // Fetch all retorna um array contendo todas as linhas do conjunto de resultados
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
    $(document).ready(function() {
        var produtosDisponiveis = <?php echo json_encode($products); ?>;
        var produtosSelecionados = [];

        function exibirProdutos() {
            var listaProdutos = $("#listaProdutos");
            listaProdutos.empty();

            if (produtosDisponiveis.length === 0) {
                $("#noResultProducts").removeClass("d-none");
                $("#resultProducts").addClass("d-none");
            } else {
                $("#noResultProducts").addClass("d-none");
                $("#resultProducts").removeClass("d-none");

                produtosDisponiveis.forEach(function(produto) {
                    var isChecked = produtosSelecionados.some(ps => ps.id === produto.id);
                    var imagem = produto.image 
                        ? "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/" + produto.id + "/" + produto.image 
                        : "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/no-image.jpg";

                    listaProdutos.append(`
                        <tr class="align-middle">
                            <td class="checkbox" scope="row">
                                <input class="form-check-input" type="checkbox" id="${produto.id}" value="${produto.id}" ${isChecked ? 'checked' : ''}>
                            </td>
                            <td>
                                <label for="${produto.id}" class="form-check-label d-flex align-items-center">
                                    <img src="${imagem}" class="me-3" alt="Imagem do produto ${produto.name}" style="width: 40px; height: 40px; object-fit: cover;">
                                    ${produto.name}
                                </label>
                            </td>
                            <td>
                                <label for="${produto.id}" class="form-check-label">${produto.status}</label>
                            </td>
                        </tr>
                    `);
                });

                // Limitar seleção a 8 produtos
                $(".form-check-input").off("change").on("change", function() {
                    var produtoId = parseInt($(this).val());
                    var produto = produtosDisponiveis.find(p => p.id === produtoId);

                    if ($(this).prop("checked")) {
                        if (produtosSelecionados.length >= 8) {
                            $(this).prop("checked", false);
                            alert("Você pode selecionar no máximo 8 produtos.");
                        } else if (produto && !produtosSelecionados.some(ps => ps.id === produto.id)) {
                            produtosSelecionados.push(produto);
                        }
                    } else {
                        removerProduto(produtoId);
                    }

                    atualizarCampoProdutos();
                    exibirProdutosSelecionados();
                });
            }
        }

        // Adiciona um ouvinte de evento de entrada ao campo #searchOutsideModal
        $('#searchProductOutsideModal').on('input', function() {
            var valorPesquisa = $(this).val();

            // Define o valor no campo #searchProduto
            $('#searchProduto').val(valorPesquisa);

            // Abre o modal e foca no campo #searchProduto
            $('#produtosModal').modal('show').on('shown.bs.modal', function () {
                $('#searchProduto').focus();
            });
        });

        $('#produtosModal').on('show.bs.modal', function() {
            exibirProdutos();
        });

        window.adicionarProdutos = function() {
            $('#produtosModal').modal('hide');
        };

        function exibirProdutosSelecionados() {
            var semProduto = $("#noProducts");
            var tabelaProdutos = $("#productsTable");
            var produtosSelecionadosDiv = $("#produtosSelecionados");
            produtosSelecionadosDiv.empty();

            if (produtosSelecionados.length === 0) {
                tabelaProdutos.addClass('d-none');
                semProduto.removeClass('d-none');
            } else {
                tabelaProdutos.removeClass('d-none');
                semProduto.addClass('d-none');

                produtosSelecionados.forEach(function(produto) {
                    produtosSelecionadosDiv.append(`
                        <tr>
                            <td>${produto.name}</td>
                            <td class="remove">
                                <span class="remover-produto" data-produto="${produto.id}">
                                    <i class="bx bx-x fs-5"></i>
                                </span>
                            </td>
                        </tr>
                    `);
                });

                $(".remover-produto").click(function() {
                    var produtoId = $(this).data("produto");
                    removerProduto(produtoId);
                });
            }
        }

        window.removerProduto = function(produtoId) {
            produtosSelecionados = produtosSelecionados.filter(ps => ps.id !== produtoId);
            atualizarCampoProdutos();
            exibirProdutosSelecionados();
        };

        function atualizarCampoProdutos() {
            var produtosIds = produtosSelecionados.map(ps => ps.id);
            $("#produtosSelecionadosInput").val(produtosIds.join(','));
        }

        $("#searchProduto").on("input", function() {
            var termoPesquisa = $(this).val().toLowerCase();

            if (termoPesquisa === "") {
                exibirProdutos();
            } else {
                var produtosFiltrados = produtosDisponiveis.filter(function(produto) {
                    return produto.name.toLowerCase().includes(termoPesquisa);
                });

                atualizarListaProdutos(produtosFiltrados);
            }
        });

        function atualizarListaProdutos(lista) {
            var listaProdutos = $("#listaProdutos");
            listaProdutos.empty();

            lista.forEach(function(produto) {
                var isChecked = produtosSelecionados.some(ps => ps.id === produto.id);
                var imagem = produto.image 
                    ? "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/" + produto.id + "/" + produto.image 
                    : "<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/imagens/no-image.jpg";

                listaProdutos.append(`
                    <tr class="align-middle">
                        <td class="checkbox" scope="row">
                            <input class="form-check-input" type="checkbox" id="${produto.id}" value="${produto.id}" ${isChecked ? 'checked' : ''}>
                        </td>
                        <td>
                            <label for="${produto.id}" class="form-check-label d-flex align-items-center">
                                <img src="${imagem}" class="me-3" alt="Imagem do produto ${produto.name}" style="width: 40px; height: 40px; object-fit: cover;">
                                ${produto.name}
                            </label>
                        </td>
                        <td>
                            <label for="${produto.id}" class="form-check-label">${produto.status}</label>
                        </td>
                    </tr>
                `);
            });
        }
    });
</script>

<!-- Funcao sem preço -->
<script>
    $(document).ready(function() {
        // Adiciona um listener ao checkbox withoutPrice
        $('#withoutPrice').on('change', function() {
            // Verifica se o checkbox está marcado
            if ($(this).prop('checked')) {
                // Desabilita os inputs moneyInput1 e moneyInput2
                $('#moneyInput1, #moneyInput2').val('');
                $('#moneyInput1, #moneyInput2').prop('disabled', true);
            } else {
                // Habilita os inputs moneyInput1 e moneyInput2
                $('#moneyInput1, #moneyInput2').prop('disabled', false);
            }
        });
    });
</script>

<!-- Mostrar container com base no input select -->
<script>
    $(document).ready(function() {
        $('#buttonType').change(function() {
            if ($(this).val() === "1" || $(this).val() === "4" || $(this).val() === "5" || $(this).val() === "6") {
                //Se for comprar, saiba mais ou agenda
                //Mostra container link
                $('#container-redirect-link').removeClass("d-none");

                $('#container-whatsapp').addClass("d-none");
                $('#container-cell-phone').addClass("d-none");
                $('#container-redirect-link').removeClass("d-none");
            } else if ($(this).val() === "2") {
                $('#container-whatsapp-standard').removeClass("d-none");

                $('#container-redirect-link').addClass("d-none");
                $('#container-whatsapp').addClass("d-none");
                $('#container-cell-phone').addClass("d-none");
            } else {
                //Se for whatsapp
                //Mostra container whatsapp
                $('#container-whatsapp').removeClass("d-none");
                $('#container-cell-phone').removeClass("d-none");

                $('#container-redirect-link').addClass("d-none");
                $('#container-whatsapp-standard').addClass("d-none");
            }
        });
    });
</script>

<!-- Convertendo numero e text em link -->
<script>
    // Função para gerar o link do WhatsApp
    function generateWhatsAppLink() {
        var countryNumberInput = $("#country-code");
        var phoneNumberInput = $("#phone-number");
        var messageTextArea = $("#message");

        var countryCode = countryNumberInput.val();
        var phoneNumber = phoneNumberInput.val();
        var message = encodeURIComponent(messageTextArea.val());

        countryCode = countryCode.replace(/\D/g, "");
        phoneNumber = phoneNumber.replace(/[^\d]/g, "");

        phoneNumber = countryCode + phoneNumber;

        var whatsappLink;
        if (message === '') {
            whatsappLink = "https://wa.me/" + phoneNumber;
        } else {
            whatsappLink = "https://wa.me//" + phoneNumber + "?text=" + message;
        }

        $('#linkWhatsapp').text(whatsappLink);
        $('#linkWhatsapp').attr("href", whatsappLink);
        $('#inputLinkWhatsapp').val(whatsappLink);
    }

    $(document).ready(function() {
        // Adicione um ouvinte de evento change ao campo <select>
        $("#buttonType").on("change", function() {
            // Verifique o novo valor do campo <select>
            var selectValue = $(this).val(); // O valor do campo <select> alterado

            if (selectValue === "3") {
                // Chame a função se o novo valor do campo <select> for igual a 2
                generateWhatsAppLink();

                // Adicione ouvintes de evento de entrada aos campos relevantes
                $("#country-code, #phone-number, #message").on("input", generateWhatsAppLink);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        function createWhatsappLink(name) {
            var phoneNumber = "<?php echo (!empty($whatsapp)) ? $whatsapp : "55" . $phone ?>";
            var messageTextArea = "Olá, tenho interesse nesse produto/serviço";
            var productName = " " + name;
            var message = messageTextArea + productName;
            var message = encodeURIComponent(message);

            phoneNumber = phoneNumber.replace(/[^\d]/g, "");

            whatsappLink = "https://wa.me//" + phoneNumber + "?text=" + message;

            $('#linkWhatsappStandard').text(whatsappLink);
            $('#linkWhatsappStandard').attr("href", whatsappLink);
            $('#inputLinkWhatsappStandard').val(whatsappLink);
        }

        // Adicione um ouvinte de evento input ao campo name
        $("#name").on("input", function() {
            // Verifique o novo valor do campo <select>
            var selectValue = $("#buttonType").val(); // O valor do campo <select> alterado
            var name = $(this).val(); // O valor do campo name

            if (selectValue === "2") {
                createWhatsappLink(name);
            }
        });

        // Adicione um ouvinte de evento change ao campo <select>
        $("#buttonType").on("change", function() {
            // Verifique o novo valor do campo <select>
            var selectValue = $(this).val(); // O valor do campo <select> alterado
            var name = $("#name").val(); // O valor do campo name

            if (selectValue === "2") {
                createWhatsappLink(name);
            }
        });
    });
</script>

<!-- Colar texto copiado -->
<script>
    document.getElementById("botaoColar").addEventListener("click", function () {
        // Verifique se a área de transferência (clipboard) é suportada pelo navegador
        if (navigator.clipboard) {
            navigator.clipboard.readText().then(function (text) {
                // Coloque o texto copiado no campo de entrada
                document.getElementById("redirectLink").value = text;
            });
        } else {
            // Fallback para navegadores que não suportam a área de transferência
            alert("A funcionalidade de área de transferência não é suportada neste navegador.");
        }
    });
</script>

<!-- Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
    //Country Mask
    new Cleave('#country-code', {
        delimiters: ['+'],
        blocks: [0, 4],
        numericOnly: true
    });
</script>
<script>
    //Phone Mask
    new Cleave('#phone-number', {
        delimiters: ['(', ')', ' ', '-'],
        blocks: [0, 2, 0, 5, 4],
        numericOnly: true
    });
</script>

<!-- Phone -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#phone-number");
        var span = $("#preview-phone-number");
        var countryCodeInput = $("#country-code"); // Adicione esta linha

        // Adicione um ouvinte de evento de entrada ao campo de entrada
        input.on("input", function() {
            // Obtenha o valor atual do campo de entrada            
            var valor = input.val();

            if (valor === '') {
                valor = countryCodeInput.val() + ' (00) 00000-0000'; // Adicione o código do país
            } else {
                valor = countryCodeInput.val() + ' ' + valor; // Adicione o código do país
            }
            
            // Atualize o texto no span com o valor formatado
            span.text(valor);
        });

        // Adicione um ouvinte de evento de entrada ao campo de código do país
        countryCodeInput.on("input", function() {
            var valor = countryCodeInput.val();

            if (valor === '') {
                valor = '+55 (00) 00000-0000';
            } else {
                valor = valor + ' (00) 00000-0000';
            }

            // Atualize o texto no span com o valor formatado
            var phoneNumber = valor + input.val();
            span.text(phoneNumber);
        });
    });
</script>

<!-- Message -->
<script>
    $(document).ready(function(){
        var timeout;

        $('#message').on('input', function(){
            clearTimeout(timeout);
            var textoDigitado = $(this).val();
            $('#writing-message').text(textoDigitado);

            $('#preview-message').text('');
            $('#preview-message').removeClass("d-block");
            $('#preview-message').addClass("d-none");

            timeout = setTimeout(function(){
                $('#writing-message').text('');

                $('#preview-message').text(textoDigitado);

                // Remover a mensagem se estiver vazio
                if ($('#message').val() === '') {
                    $('#preview-message').removeClass("d-block");
                    $('#preview-message').addClass("d-none");
                } else {
                    $('#preview-message').removeClass("d-none");
                    $('#preview-message').addClass("d-block");
                }
            }, 2000);
        });
    });
</script>

<script>
    $(document).ready(function() {
        function inputCounter(inputId, textId) {
            $('#' + inputId).on('input', function() {
                var currentText = $(this).val();
                var currentLength = currentText.length;
                var maxLength = parseInt($(this).attr('maxlength'));
                $('#' + textId).text(currentLength + ' de ' + maxLength + ' caracteres');
            });
        }

        inputCounter('name', 'nameCounter');
    });
</script>

<!-- Checkbox -->
<script>
    function updateCheckboxText(checkbox, contentText, trueText, falseText) {
        checkbox.addEventListener("change", function () {
            const text = this.checked ? trueText : falseText;
            // Aqui você pode atualizar o elemento de texto desejado com o texto correspondente
            // Por exemplo, se você tiver um <span id="checkboxText">Texto</span>
            // Pode ser atualizado assim:
            contentText.textContent = text;
        });
    }

    const activeProduct = document.getElementById("activeProduct");
    const activeCheckbox = document.getElementById("activeCheckbox");
    updateCheckboxText(activeProduct, activeCheckbox, "Sim", "Não");

    const emphasisProduct = document.getElementById("emphasisProduct");
    const emphasisCheckbox = document.getElementById("emphasisCheckbox");
    updateCheckboxText(emphasisProduct, emphasisCheckbox, "Sim", "Não");
</script>

<!-- Validacao de valores -->
<!-- <script>
    function formatMoneyInput(input) {
        input.addEventListener("input", function () {
            const value = this.value.replace(/\D/g, ""); // Remove não números
            this.value = value.substring(0, value.length - 2) + "," + value.substring(value.length - 2);
        });
    }

    const moneyInput1 = document.getElementById("moneyInput1");
    formatMoneyInput(moneyInput1);

    const moneyInput2 = document.getElementById("moneyInput2");
    formatMoneyInput(moneyInput2);
</script> -->

<!-- Name -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#name");

        var seoName = $('#textInput1');
        var seoNamePreview = $('#textPreview1');

        input.on("input", function() {
            var value = input.val();

            // Verifica se a string excede 67 caracteres
            if (value.length > 67) {
                // Limita a string aos primeiros 67 caracteres
                value = value.substring(0, 67);
            }

            if (value.length < 67) {
                seoName.val(value);
                seoNamePreview.text(value);
            } else if (value.length >= 67) {
                value = value.substring(0, 67);

                seoName.val(value + "...");
                seoNamePreview.text(value + "...");
            }

            if (value === '') {
                seoNamePreview.text("Título da página");
            }
        });
    });
</script>

<!-- Link -->
<script>
    // Função que faz a lógica de remover acentos e atualizar a pré-visualização
    function atualizarPrevia() {
        var input = $("#name"); // Selecione o campo de entrada
        var span = $("#linkPreview"); // Selecione o span de pré-visualização
        var inputText2 = $('#textInput2'); // Outro campo de entrada que também será atualizado
        var textPreview2 = $('#textPreview2'); // Outro span de pré-visualização

        var value = input.val(); // Pegue o valor do campo de entrada

        // Remover acentos e substituir espaços por traço
        value = removerAcentosEespacos(value);

        // Atualiza o texto no span e no input extra
        span.text(value);
        inputText2.val(value);
        textPreview2.text(value);

        // Se o valor estiver vazio, exibe valores padrão
        if (value === '') {
            span.text("...");
            textPreview2.text("link-da-pagina");
        }
    }

    // Função para remover acentos e substituir espaços por traços
    function removerAcentosEespacos(texto) {
        return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, "-").toLowerCase();
    }

    // Quando o documento estiver pronto, execute a função
    $(document).ready(function() {
        // Executa no carregamento da página
        atualizarPrevia();

        // Atualiza sempre que houver uma interação no campo de entrada
        $("#name").on("input", function() {
            atualizarPrevia();
        });
    });
</script>

<!-- Description -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Substitua 'meuTextarea' pelo ID real do seu textarea configurado com o TinyMCE
        var meuTextarea = tinymce.get('editor');

        meuTextarea.on('change', function() {
            // Selecione o campo de entrada e o span
            var editor = $("#editor");
            var seoDescription = $("#textInput3");
            var seoDescriptionPreview = $("#textPreview3");

            var value = tinymce.get('editor').getContent({ format: 'text' });

            // Verifica se a string excede 157 caracteres
            if (value.length > 157) {
                // Limita a string aos primeiros 157 caracteres
                value = value.substring(0, 157);
            }

            if (value.length < 157) {
                seoDescription.val(value);
                seoDescriptionPreview.text(value);
            } else if (value.length >= 157) {
                value = value.substring(0, 157);

                seoDescription.val(value + "...");
                seoDescriptionPreview.text(value + "...");
            }

            if (value === '') {
                seoDescriptionPreview.text("Descrição da página");
            }
        });
    });
</script>

<!-- Imagens -->
<script>
    let uploadButton = document.getElementById("upload-button");
    let container = document.querySelector(".image-container");
    let error = document.getElementById("error");
    let imageDisplay = document.getElementById("image-display");
    let loadedImages = [];
    const maxImages = 5; // Define o número máximo de imagens

    const fileHandler = (file, name, type, index) => {
        if (loadedImages.length >= maxImages) {
            // Imagens atingiram o limite
            error.innerText = `You can only upload up to ${maxImages} images.`;
            return false;
        }

        if (type.split("/")[0] !== "image") {
            //File Type Error
            error.innerText = "Please upload an image file";
            return false;
        }

        if (loadedImages.includes(name)) {
            // Image already loaded
            error.innerText = "This image has already been loaded";
            return false;
        }

        loadedImages.push(name);
        error.innerText = "";
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = () => {
            //image and file name
            let imageContainer = document.createElement("figure");
            let img = document.createElement("img");
            img.src = reader.result;
            imageContainer.appendChild(img);
            
            // Add a delete button
            let deleteButton = document.createElement("button");
            deleteButton.addEventListener("click", () => {
                loadedImages = loadedImages.filter(imgName => imgName !== name);
                imageContainer.remove();
            });
            imageContainer.appendChild(deleteButton);

            // Add class for sorting
            imageContainer.className = "sortable-image";

            imageDisplay.appendChild(imageContainer);
        };

        // Initialize sortable
        $(".sortable-container").sortable({
            items: ".sortable-image",
            cursor: "grabbing"
        });
    };

    const handleFiles = (files) => {
        // Dentro da função handleFiles
        Array.from(files).forEach((file, index) => {
            fileHandler(file, file.name, file.type, index);
        });
    };

    // Upload Button
    uploadButton.addEventListener("change", () => {
        handleFiles(uploadButton.files);
    });

    // Container Drag Events
    container.addEventListener("dragenter", (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.add("dropzone-active");
    }, false);

    container.addEventListener("dragleave", (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove("dropzone-active");
    }, false);

    container.addEventListener("dragover", (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.add("dropzone-active");
    }, false);

    container.addEventListener("drop", (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove("dropzone-active");
        let draggedData = e.dataTransfer;
        let files = draggedData.files;
        handleFiles(files);
    }, false);

    window.onload = () => {
        error.innerText = "";
    };
</script>

<!-- Video Preview -->
<script>
    const videoForm = document.getElementById("video-url");
    const videoDisplay = document.getElementById("video-display");

    videoForm.addEventListener("input", function(event) {
        event.preventDefault();

        const videoUrl = document.getElementById("video-url").value.trim(); // Remove espaços em branco no início e fim
        if (videoUrl === "") {
            videoDisplay.innerHTML = ""; // Não mostrar nada se o input estiver vazio
            return;
        }

        const videoId = getYouTubeVideoId(videoUrl);

        if (videoId) {
            const embedCode = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen class="mt-3"></iframe>`;
            videoDisplay.innerHTML = embedCode;
        } else {
            videoDisplay.innerHTML = "<p class='fw-normal small mt-3'>URL de vídeo inválida.</p>";
        }
    });

    function getYouTubeVideoId(url) {
        const regex = /(?:\?v=|\/embed\/|\.be\/)([a-zA-Z0-9_-]+)/;
        const matches = url.match(regex);
        return matches ? matches[1] : null;
    }
</script>

<!-- Editor de texto -->
<script>
    tinymce.init({
        selector: '#editor',
        plugins: 'link image lists',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
        width: '100%',
        height: 300,
        menubar: false
    });
</script>

<!-- SKU -->
<!-- <script>
    document.getElementById('skuForm').addEventListener('submit', function(event) {
        event.preventDefault();

        // Obtenha os valores dos campos
        const brand = document.getElementById('brand').value.toUpperCase();
        const product = document.getElementById('product').value.toUpperCase();
        const style = document.getElementById('style').value.toUpperCase();
        const size = document.getElementById('size').value.toUpperCase();
        const color = document.getElementById('color').value.toUpperCase();

        // Combine os valores para formar o código SKU
        const sku = `${brand.substring(0, 3)}-${product.substring(0, 2)}-${style.substring(0, 2)}-${size}-${color.substring(0, 2)}`;

        // Exiba o código SKU no campo de resultado
        document.getElementById('skuResult').value = sku;
    });
</script> -->

<script>
    $(document).ready(function() {
        // Função para gerar um SKU aleatório
        function gerarSKU() {
            // Caracteres permitidos no SKU
            var caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

            // Comprimento do SKU desejado
            var comprimentoSKU = 10;

            // Variável para armazenar o SKU gerado
            var sku = '';

            // Gera o SKU aleatório
            for (var i = 0; i < comprimentoSKU; i++) {
            var indiceAleatorio = Math.floor(Math.random() * caracteres.length);
            sku += caracteres.charAt(indiceAleatorio);
            }

            return sku;
        }

        // Manipula o clique no botão para gerar o SKU
        $('#gerarSKU').on('click', function() {
            var skuGerado = gerarSKU();
            $('#skuResult').val(skuGerado);
        });
    });
  </script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- SEO -->
<script>
    $(document).ready(function(){
        var inputText1 = $('#textInput1');
        var inputText2 = $('#textInput2');
        var inputText3 = $('#textInput3');
        var textPreview1 = $('#textPreview1');
        var textPreview2 = $('#textPreview2');
        var linkPreview = $('#linkPreview');
        var textPreview3 = $('#textPreview3');

        inputText1.on('input', function () {
            var newText = inputText1.val();
            if (newText === '') {
                newText = 'Título da página';
            }
            textPreview1.text(newText);
        });

        inputText2.on("input", function() {
            var text = inputText2.val();
            if (text === '') {
                text = 'link-da-categoria';
                linkPreview.text('...');
            }
            // Remover acentos e substituir espaços por traço
            newText = removerAcentosEespacos(text);
            $(this).val($(this).val().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, "-").toLowerCase());
            textPreview2.text(newText);
            linkPreview.text(newText);
        });

        inputText3.on('input', function () {
            var newText = inputText3.val();
            if (newText === '') {
                newText = 'Descrição da Página';
            }
            textPreview3.text(newText);
        });

        function removerAcentosEespacos(texto) {
            // Remove acentos usando normalize e substitui espaços por traço
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, "-").toLowerCase();
        }
    });
</script>

<!-- Text Counter -->
<script>
    $(document).ready(function() {
        $('#textInput1').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));
            $('#textCounter1').text(currentLength + ' de ' + maxLength + ' caracteres');
        });
    });
</script>

<!-- Text Counter -->
<script>
    $(document).ready(function() {
        $('#textInput3').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));
            $('#textCounter3').text(currentLength + ' de ' + maxLength + ' caracteres');
        });
    });
</script>

<!-- Save Button -->
<script>
    $(document).ready(function(){
        var originalFormValues = {};
        var formChanged = false;

        // Salva os valores originais dos campos do formulário
        $('input, textarea').each(function () {
            originalFormValues[$(this).attr('id')] = $(this).val();
        });

        $('input, textarea').on('input', function () {
            formChanged = true;
            $('#saveButton').show();
            $('.main .container').addClass('save-button-show');
        });

        $('#saveButton button').click(function () {
            if (formChanged) {
                formChanged = false;
                $('#saveButton').hide();
                $('.main .container').removeClass('save-button-show');
            }
        });

        // Verifica quando os campos do formulário voltam ao valor original
        $('input, textarea').on('input', function () {
            if ($(this).val() === originalFormValues[$(this).attr('id')]) {
                $(this).removeClass('changed');
            } else {
                $(this).addClass('changed');
            }

            // Verifica se há algum campo com alterações
            var hasChanges = $('input.changed, textarea.changed').length > 0;
            if (!hasChanges) {
                $('#saveButton').hide();
                $('.main .container').removeClass('save-button-show');
            }
        });
    });
</script>

<!-- Valor de desconto menor que o valor normal -->
<script>
    // Adicione um manipulador de eventos para o evento blur
    $("#moneyInput1").blur(function () {
        // Dentro deste manipulador, você pode acessar o valor atual do campo de entrada
        var valorInserido = $(this).val();
        
        $('#moneyInput1').addClass(valorInserido);
        // Faça algo com o valor (por exemplo, exiba-o em um alerta)
        alert("Você inseriu: " + valorInserido);

    });
</script>

<script>
    let formModified = false;
    let formSubmitting = false;

    // Verifica se há algum valor modificado nos inputs ao carregar a página
    $(document).ready(function() {
        // Percorre todos os inputs do formulário com id 'myForm'
        $('#myForm input').each(function() {
            if ($(this).val() !== '') {
                formModified = true;
                return false; // Interrompe o loop assim que encontrar um input modificado
            }
        });
    });

    // Marca o formulário como modificado quando o usuário faz uma alteração
    $('#myForm').on('input', 'input', function() {
        formModified = true;
    });

    // Marca o formulário como sendo submetido quando o usuário clica em enviar
    $('#myForm').on('submit', function() {
        formSubmitting = true;
    });

    // Adiciona o evento beforeunload para avisar o usuário sobre alterações não salvas
    $(window).on('beforeunload', function(e) {
        if (formModified && !formSubmitting) {
            // Define a mensagem de aviso
            const message = 'Você tem alterações não salvas. Tem certeza de que deseja sair desta página?';

            // Para navegadores que suportam a especificação mais recente
            e.preventDefault();
            e.returnValue = message;

            // Para navegadores mais antigos
            return message;
        }
    });
</script>