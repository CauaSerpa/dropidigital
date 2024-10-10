<?php
// Pega o id da loja
$shop_id = $id;

//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Consulta SQL para contar os produtos na tabela
$sql = "SELECT COUNT(*) AS total_produtos FROM tb_products WHERE status = 1";
$stmt = $conn_pdo->query($sql);

// Recupere o resultado da consulta
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

// O resultado contém o total de produtos na chave 'total_produtos'
$totalProdutos = $resultado['total_produtos'];

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_products";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($product) {
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_product.php" method="post" enctype="multipart/form-data">

    <div class="page__header center">
        <div class="header__title">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-3">
                    <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>produtos" class="fs-5 text-decoration-none text-reset">Produtos</a></li>
                    <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Editar Produto</li>
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
                    <small id="textCounter" class="form-text text-muted">0 de 120 caracteres</small>
                </div>
                <input type="text" class="form-control" name="name" id="name" maxlength="120" aria-describedby="nameHelp" require value="<?php echo $product['name']; ?>">
                <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="linkPreview">...</span></p>
            </div>
            <?php
                if ($product['status'] == 1)
                {
                    $statusActive = "checked";
                    $activeCheckbox = "Sim";
                } else if ($limitProducts <= $totalProdutos) {
                    $statusActive = "disabled";
                    $activeCheckbox = "Não";
                } else {
                    $statusActive = "";
                    $activeCheckbox = "Não";
                }
            ?>
            <div class="row">
                <div class="col-md-6 d-flex justify-content-between mb-3">
                    <div>
                        <label for="activeProduct" class="form-label small">
                            Produto ativo?
                            <i class="bx bx-help-circle <?php echo ($limitProducts <= $totalProdutos && $product['status'] == 0) ? "" : "d-none"; ?>" data-toggle="tooltip" data-placement="top" aria-label="Você ultrapassou o limite de produtos ativos. Para habilitar novos produtos, considere a contratação de um plano com maior capacidade!" data-bs-original-title="Você ultrapassou o limite de produtos ativos. Para habilitar novos produtos, considere a contratação de um plano com maior capacidade!"></i>
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="activeProduct" value="1" <?php echo $statusActive; ?>>
                            <label class="form-check-label" id="activeCheckbox" for="activeProduct"><?php echo $activeCheckbox; ?></label>
                        </div>
                    </div>
                    <div id="containerEmphasis">
                        <label for="emphasisProduct" class="form-label small">Destacar no menu?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasisProduct" value="1" <?php echo ($product['emphasis'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasisProduct"><?php echo ($product['emphasis'] == 1) ? "Sim" : "Não"; ?></label>
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
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control text-end" name="price" id="moneyInput1" placeholder="0,00" value="<?php echo $product['price']; ?>" <?php echo ($product['without_price'] == 1) ? "disabled" : ""; ?>>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="without_price" id="withoutPrice" <?php echo ($product['without_price'] == 1) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="withoutPrice">Sem preço</label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="moneyInput2" class="form-label small">Preço promocional</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control text-end" name="discount" id="moneyInput2" placeholder="0,00" value="<?php echo $product['discount']; ?>" <?php echo ($product['without_price'] == 1) ? "disabled" : ""; ?>>
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
                <input type="file" name="imagens[]" id="upload-button" multiple accept="image/*">
                <div for="upload-button" class="dropzone">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                </div>
                <div id="error"></div>
            </label>
            <div class="sortable-container mt-3">
                <div id="image-display">
                    <?php
                        // Consulta SQL para selecionar todas as colunas com base no ID
                        $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':usuario_id', $product['id']);
                        $stmt->execute();

                        // Recuperar os resultados
                        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Loop através dos resultados e exibir todas as colunas
                        foreach ($imagens as $imagem) {
                            echo '
                                <figure class="sortable-image">
                                    <img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/imagens/' . $imagem['usuario_id'] . '/' . $imagem['nome_imagem'] . '">
                                    <button class="remove-image" data-image-id="' . $imagem['id'] . '"></button>
                                </figure>';
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="card-footer fw-semibold px-4 py-3 bg-transparent">
            <div class="d-flex justify-content-between">
                <label for="exampleInputEmail" class="form-label small">Vídeo do produto</label>
                <p class="form-text text-muted fw-normal small">Aceitamos apenas vídeos do YouTube</p>
            </div>
            <div class="position-relative">
                <i class='bx bxl-youtube input-icon' ></i>
                <input type="text" class="form-control icon-padding" name="video" id="video-url" placeholder="https://www.youtube.com/watch?v=000" aria-label="https://www.youtube.com/watch?v=000" value="<?php echo $product['video']; ?>">
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
            <textarea name="description" id="editor"><?php echo $product['description']; ?></textarea>
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
            <label for="skuResult" class="form-label small">
                Código SKU
                <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
            </label>
            <div class="input-group">
                <input type="text" class="form-control" name="sku" id="skuResult" placeholder="SKU" aria-label="SKU" aria-describedby="skuResult" style="max-width: 250px;" value="<?php echo $product['sku']; ?>">
                <button class="btn btn-outline-dark fw-semibold px-4" type="button" id="gerarSKU">GERAR</button>
            </div>
            <small class="text-decoration-none" id="error-sku" style="color: rgb(229, 15, 56);"></small>
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
                                <option value="" disabled>-- Selecione uma opção --</option>
                                <option value="1" <?php echo ($product['button_type'] == 1) ? "selected" : ""; ?>>Comprar</option>
                                <option value="2" <?php echo ($product['button_type'] == 2) ? "selected" : ""; ?>>Número de whatsapp - Mensagem Padrão</option>
                                <option value="3" <?php echo ($product['button_type'] == 3) ? "selected" : ""; ?>>Número de whatsapp - Mensagem Personalizada</option>
                                <option value="4" <?php echo ($product['button_type'] == 4) ? "selected" : ""; ?>>Saiba mais</option>
                                <option value="5" <?php echo ($product['button_type'] == 5) ? "selected" : ""; ?>>Agenda</option>
                                <option value="6" <?php echo ($product['button_type'] == 6) ? "selected" : ""; ?>>Cadastrar</option>
                            </select>
                        </div>
                    </div>
                    <!-- Opcao do whatsapp -->
                    <div class="<?php echo ($product['button_type'] !== 3) ? "d-none" : ""; ?>" id="container-whatsapp">
                        <div class="mb-3 row">
                            <label for="phone-number" class="form-label small">Número do WhatsApp *</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="country-code" id="country-code" placeholder="+55">
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
                            <p><a href="<?php echo ($product['button_type'] == 3) ? $product['redirect_link'] : ""; ?>" target="_blank" class="small text-decoration-none" id="linkWhatsapp" style="color: #01C89B;"><?php echo ($product['button_type'] == 3) ? $product['redirect_link'] : ""; ?></a></p>
                        </div>
                        <input type="hidden" name="redirect_link_whatsapp" id="inputLinkWhatsapp" value="<?php echo ($product['button_type'] == 3) ? $product['redirect_link'] : ""; ?>">
                    </div>
                </div>

                <!-- Celular para opcao do whatsapp -->
                <div class="col-md-6 <?php echo ($product['button_type'] !== 3) ? "d-none" : ""; ?>" id="container-cell-phone">
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

            <div class="mb-3 <?php echo ($product['button_type'] == 2 || $product['button_type'] == 3) ? "d-none" : ""; ?>" id="container-redirect-link">
                <label for="redirectLink" class="form-label small">Link de redirecionamento *</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="redirect_link" id="redirectLink" aria-describedby="redirect-linkHelp" value="<?php echo ($product['button_type'] !== 2) ? $product['redirect_link'] : ""; ?>">
                    <button type="button" class="btn btn-secondary px-4" id="botaoColar">Colar Link</button>
                </div>
            </div>

            <div class="mb-3 <?php echo ($product['button_type'] !== 2) ? "d-none" : ""; ?>" id="container-whatsapp-standard">
                <p class="form-label small">Whatsapp - Mensagem padrão</p>
                <a href="<?php echo ($product['button_type'] == 2) ? $product['redirect_link'] : ""; ?>" class="fw-semibold text-decoration-none" id="linkWhatsappStandard" target="_black" style="color: #01C89B;"><?php echo ($product['button_type'] == 2) ? $product['redirect_link'] : ""; ?></a>
                <input type="hidden" name="redirect_link_whatsapp_standard" id="inputLinkWhatsappStandard">
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
                            <small id="textCounter1" class="form-text text-muted">0 / 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp" value="<?php echo $product['seo_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da página</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp" value="<?php echo $product['link']; ?>">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da página</label>
                            <small id="textCounter3" class="form-text text-muted">0 / 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"><?php echo $product['seo_description']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1"><?php echo ($product['seo_name'] == "") ? $product['seo_name'] : "Título da página"; ?></h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="textPreview2"><?php echo ($product['link'] == "") ? $product['link'] : "link-da-pagina"; ?></span></p>
                        <p class="small" id="textPreview3"><?php echo ($product['seo_description'] == "") ? $product['seo_description'] : "Descrição da página"; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="delete_images" id="delete-images-input">
    <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Adicione esses campos ocultos no seu formulário -->
    <input type="hidden" name="categoriasSelecionadas[]" id="categoriasSelecionadasInput" value="<?php
        // Nome da tabela para a busca
        $tabela = 'tb_product_categories';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id ORDER BY (main = 1) DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->execute();

        // Recuperar os resultados
        $productsCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $primeiroElemento = false;
        $main = 0;

        if ($productsCategories) {
            // Loop através dos resultados e exibir todas as colunas
            foreach ($productsCategories as $productCategory) {
                $main = ($productCategory['main'] == "1") ? $productCategory['category_id'] : "";

                echo ($primeiroElemento) ? "," : "";

                echo $productCategory['category_id'];

                $primeiroElemento = true;
            }
        }
    ?>">

    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_product_categories';

        $sql = "SELECT category_id FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id AND main = :main ORDER BY id DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->bindValue(':main', 1);
        $stmt->execute();

        // Recuperar os resultados
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    <!-- Categoria principal -->
    <input type="hidden" name="inputMainCategory" id="inputMainCategory" val="<?php echo @$category['category_id']; ?>">

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

<!-- Link para o Bootstrap JS (junto com jQuery) -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

<!-- Link para o TinyMCE CSS -->
<script src="https://cdn.tiny.cloud/1/<?= $tinyKey; ?>/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

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
    $tabelaProductCategories = 'tb_product_categories';
    $tabelaCategories = 'tb_categories';

    $sqlProductCategories = "SELECT * FROM $tabelaProductCategories WHERE shop_id = :shop_id AND product_id = :product_id ORDER BY (main = 1) DESC";

    // Preparar e executar a consulta
    $stmtProductCategories = $conn_pdo->prepare($sqlProductCategories);
    $stmtProductCategories->bindParam(':shop_id', $shop_id);
    $stmtProductCategories->bindParam(':product_id', $product['id']);
    $stmtProductCategories->execute();

    // Recuperar os resultados
    $productsCategories = $stmtProductCategories->fetchAll(PDO::FETCH_ASSOC);

    $resultArray = [];

    if ($productsCategories) {
        // Loop através dos resultados de tb_product_categories
        foreach ($productsCategories as $productCategory) {
            // Consultar tb_categories para obter o nome da categoria
            $sqlCategories = "SELECT * FROM $tabelaCategories WHERE id = :id AND shop_id = :shop_id ORDER BY id DESC";

            // Preparar e executar a consulta
            $stmtCategories = $conn_pdo->prepare($sqlCategories);
            $stmtCategories->bindParam(':id', $productCategory['category_id']);
            $stmtCategories->bindParam(':shop_id', $shop_id);
            $stmtCategories->execute();

            // Recuperar os resultados
            $categories = $stmtCategories->fetch(PDO::FETCH_ASSOC);

            // Convertendo o id para formato numérico
            $categoryId = (int)$productCategory['category_id'];

            // Adicionar ao array de resultados
            $resultArray[] = [
                'id' => $categoryId,
                'name' => $categories['name']
            ];
        }
    }
?>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_categories';

    $sql = "SELECT id, name FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    // Fetch all retorna um array contendo todas as linhas do conjunto de resultados
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Script jQuery para manipular o modal e as categorias -->
<script>
    $(document).ready(function () {
        // Array de categorias disponíveis (substitua com a lógica do seu banco de dados)
        var categoriasDisponiveis = <?php echo json_encode($categories); ?>;

        // Array de categorias selecionadas (substitua com a lógica do seu banco de dados)
        var categoriasSelecionadas = <?php echo json_encode($resultArray); ?>;

        // Garante que categoriasSelecionadas seja sempre um array
        if (!Array.isArray(categoriasSelecionadas)) {
            categoriasSelecionadas = [];
        }

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

                        // Adiciona a nova categoria às arrays
                        categoriasDisponiveis.push(novaCategoria);
                        categoriasSelecionadas.push(novaCategoria);

                        // Adiciona a nova categoria à tabela
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
            
            var mainCategoria = $("#inputMainCategory").val();

            if (!Array.isArray(categoriasSelecionadas) || categoriasSelecionadas.length === 0) {
                // Se nenhuma categoria estiver selecionada, adiciona a classe d-none
                tabelaCategorias.addClass('d-none');
                semCategoria.removeClass('d-none');
            } else {
                tabelaCategorias.removeClass('d-none');
                semCategoria.addClass('d-none');

                categoriasSelecionadas.forEach(function (categoria) {
                    categoriasSelecionadasDiv.append('<tr><td>' + categoria.name +
                        '<span class="mainCategory ms-2" data-categoria="' + categoria.id + '"><i class="bx bx-star" ></i></span></td><td class="remove"><span class="remover-categoria" data-categoria="' + categoria.id + '"><i class="bx bx-x fs-5"></i></span></td></tr>');
                });

                // Adiciona o evento de clique para remover a categoria
                $(".remover-categoria").click(function () {
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

                categoriasDisponiveis.forEach(function (categoria) {
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

        // Função para exibir categorias selecionadas
        function exibirCategoriasSelecionadasQuandoCarregar() {
            var semCategoria = $("#noCategories");
            var tabelaCategorias = $("#categoriesTable");
            var categoriasSelecionadasDiv = $("#categoriasSelecionadas");

            var mainCategoryId = <?php echo (isset($category['category_id'])) ? $category['category_id'] : 0; ?>;

            categoriasSelecionadasDiv.empty();

            if (categoriasSelecionadas.length === 0) {
                // Se nenhuma categoria estiver selecionada, adiciona a classe d-none
                tabelaCategorias.addClass('d-none');
                semCategoria.removeClass('d-none');
            } else {
                tabelaCategorias.removeClass('d-none');
                semCategoria.addClass('d-none');

                categoriasSelecionadas.forEach(function(categoria) {
                    var mainCategoryClass = (categoria.id === mainCategoryId) ? 'mainCategory mainActive' : 'mainCategory';
                    var mainCategoryIcon = (categoria.id === mainCategoryId) ? 'bxs-star' : 'bx-star';

                    categoriasSelecionadasDiv.append('<tr><td>' + categoria.name +
                        '<span class="' + mainCategoryClass + ' ms-2" data-categoria="' + categoria.id + '"><i class="bx ' + mainCategoryIcon + '" ></i></span></td><td class="remove"><span class="remover-categoria" data-categoria="' + categoria.id + '"><i class="bx bx-x fs-5"></i></span></td></tr>');
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

        $(document).ready(function () {
            // Chama a função para carregar as categorias selecionadas ao reiniciar a página
            exibirCategoriasSelecionadasQuandoCarregar();
        });

        // Adicionar categorias selecionadas ao formulário
        window.adicionarCategorias = function () {
            $("input[type='checkbox']:checked").each(function () {
                var categoriaId = parseInt($(this).val());
                var categoria = categoriasDisponiveis.find(c => c.id === categoriaId);

                if (categoria && !categoriasSelecionadas.some(cs => cs.id === categoria.id)) {
                    categoriasSelecionadas.push(categoria);
                }
            });

            // Remover categoria se o checkbox for desmarcado no modal
            $("#listaCategorias input[type='checkbox']").each(function () {
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
        window.removerCategoria = function (categoriaId) {
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
        $('#searchOutsideModal').on('input', function () {
            var valorPesquisa = $(this).val();

            // Define o valor no campo #searchCategoria
            $('#searchCategoria').val(valorPesquisa);

            // Abre o modal e foca no campo #searchCategoria
            $('#categoriasModal').modal('show').on('shown.bs.modal', function () {
                $('#searchCategoria').focus();
            });
        });

        // Filtrar categorias com base na pesquisa
        $("#searchCategoria").on("input", function () {
            var termoPesquisa = $(this).val().toLowerCase();

            if (termoPesquisa === "") {
                exibirCategorias();
            } else {
                var categoriasFiltradas = categoriasDisponiveis.filter(function (categoria) {
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

            pesquisarCategoria.forEach(function (categoria) {
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
        // Verifique se o <select> já tem a opção 3 selecionada quando a página carrega
        var selectValue = $("#buttonType").val();
        
        if (selectValue === "3") {
            generateWhatsAppLink();

            // Adicione ouvintes de evento de entrada aos campos relevantes
            $("#country-code, #phone-number, #message").on("input", generateWhatsAppLink);
        }

        // Adicione um ouvinte de evento change ao campo <select>
        $("#buttonType").on("change", function() {
            // Verifique o novo valor do campo <select>
            var selectValue = $(this).val();

            if (selectValue === "3") {
                // Chame a função se o novo valor do campo <select> for igual a 3
                generateWhatsAppLink();

                // Adicione ouvintes de evento de entrada aos campos relevantes
                $("#country-code, #phone-number, #message").on("input", generateWhatsAppLink);
            }
        });
    });
</script>

<!-- Separando o numero de whatsapp e o texto do link -->
<script>
    // O link do WhatsApp
    var whatsappLink = "<?php echo $product['redirect_link']; ?>";

    // Use uma expressão regular para extrair o número de telefone e o texto
    var regex = /(?:https?:\/\/)?(?:wa.me\/|wa.me\/\/)?(\d+)(?:\?text=([^&]+))?/;
    var match = whatsappLink.match(regex);

    if (match) {
        // Se houver uma correspondência na expressão regular
        var phoneNumber = match[1]; // Extrai o número de telefone

        // Decodifique o texto da URL, se presente
        var text = match[2] ? decodeURIComponent(match[2]) : '';

        // Comprimento do código do país (no caso do Brasil, são 2 dígitos)
        var countryCodeLength = 2;

        // Extrair o código do país e o número restante
        var countryCode = phoneNumber.substring(0, countryCodeLength);
        var restOfNumber = phoneNumber.substring(countryCodeLength);

        // Atualize os valores nos campos e no preview
        $("#country-code").val(countryCode);
        $("#phone-number").val(restOfNumber);
        $("#preview-phone-number").text(phoneNumber);

        if (text) {
            $("#message").text(text);
            $("#preview-message").removeClass("d-none");
            $("#preview-message").text(text);
        }
    } else {
        // Se não houver correspondência na expressão regular, trate-o conforme necessário
        console.log("Link do WhatsApp inválido.");

        whatsappLink = "https://wa.me/";

        $('#linkWhatsapp').text(whatsappLink);
        $('#linkWhatsapp').attr("href", whatsappLink);
        $('#inputLinkWhatsapp').val(whatsappLink);
    }
</script>

<script>
    $(document).ready(function() {
        function createWhatsappLink(name) {
            var phoneNumber = "<?php echo (!empty($whatsapp)) ? $whatsapp : "55" . $phone ?>";
            var messageTextArea = "Olá, tenho interesse nesse produto/serviço";
            var productName = name;
            var message = messageTextArea + " " + productName;
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
    // Função para formatar o número de telefone
    function formatPhoneNumber() {
        var input = $("#phone-number");
        var span = $("#preview-phone-number");
        var countryCodeInput = $("#country-code");

        var valor = input.val();
        if (valor === '') {
            valor = countryCodeInput.val() + ' (00) 00000-0000';
        } else {
            valor = countryCodeInput.val() + ' ' + valor;
        }
        span.text(valor);
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Chame a função quando o documento estiver pronto
        formatPhoneNumber();

        // Adicione um ouvinte de evento de entrada ao campo de entrada
        $("#phone-number").on("input", formatPhoneNumber);

        // Adicione um ouvinte de evento de entrada ao campo de código do país
        $("#country-code").on("input", formatPhoneNumber);
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

<!-- Link -->
<script>
    function atualizarLink() {
        var input = $("#name");
        var span = $("#linkPreview");

        var valor = input.val();

        if (valor === '') {
            valor = '...';
        }

        valor = valor.replace(/\s+/g, "-").toLowerCase();

        span.text(valor);
    }

    $(document).ready(function() {
        // Chame a função quando a página estiver pronta
        atualizarLink();

        // Adicione um ouvinte de evento de entrada ao campo de entrada
        $("#name").on("input", atualizarLink);
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

<script>
    $(document).ready(function() {
        function nameCounter() {
            var name = $('#name');
            var currentText = name.val();
            var currentLength = currentText.length;
            var maxLength = parseInt(name.attr('maxlength'));
            $('#textCounter').text(currentLength + ' de ' + maxLength + ' caracteres');
        }

        $('#name').on('input', function() {
            nameCounter();
        });

        $(document).ready(function() {
            nameCounter();
        });
    });
</script>

<script>
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
</script>

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

            // Contador de caracteres
            var currentText = $('#textInput1').val();
            var currentLength = currentText.length;
            var maxLength = parseInt($('#textInput1').attr('maxlength'));
            $('#textCounter1').text(currentLength + ' de ' + maxLength + ' caracteres');
        });
    });
</script>

<!-- Link -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#name");
        var span = $("#linkPreview");

        var inputText2 = $('#textInput2');
        var textPreview2 = $('#textPreview2');

        input.on("input", function() {
            var value = input.val();

            // Remover acentos e substituir espaços por traço
            value = removerAcentosEespacos(value);
            
            span.text(value);

            inputText2.val(value);
            textPreview2.text(value);

            if (value === '') {
                span.text("...");
                textPreview2.text("link-da-pagina");
            }
        });

        function removerAcentosEespacos(texto) {
            // Remove acentos usando normalize e substitui espaços por traço
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, "-").toLowerCase();
        }
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

            // Contador de caracteres
            var currentText = $('#textInput3').val();
            var currentLength = currentText.length;
            var maxLength = parseInt($('#textInput3').attr('maxlength'));
            $('#textCounter3').text(currentLength + ' de ' + maxLength + ' caracteres');
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

    // Adicione um evento de clique para remover a imagem
    imageDisplay.addEventListener("click", (event) => {
        if (event.target.classList.contains("remove-image")) {
            const imageName = event.target.getAttribute("data-image-name");
            // Adicione lógica aqui para remover a imagem do servidor ou do banco de dados
            // Você também deve remover a imagem da lista no front-end
            const imageFigure = event.target.parentElement;
            imageFigure.remove();
        }
    });

    // Inicialize o sortable
    $(".sortable-container").sortable({
        items: ".sortable-image",
        cursor: "grabbing"
    });
</script>

<script>
// Dentro do evento de clique para remover a imagem
imageDisplay.addEventListener("click", (event) => {
    if (event.target.classList.contains("remove-image")) {
        //Input onde sera inserido o valor
        const deleteImagesInput = document.getElementById("delete-images-input");

        const imageId = event.target.getAttribute("data-image-id");

        // Obtém o valor atual no campo de entrada
        const valorAtual = deleteImagesInput.value;
        
        // Valor que você deseja adicionar
        const novoValor = imageId;

        // Verifica se o campo já possui um valor
        if (valorAtual.trim() !== "") {
            // Se o campo já tem um valor, adicione uma vírgula e o novo valor
            deleteImagesInput.value = valorAtual + ", " + novoValor;
        } else {
            // Se o campo estiver vazio, defina apenas o novo valor
            deleteImagesInput.value = novoValor;
        }

        // Remove a imagem da lista no front-end
        const imageFigure = event.target.parentElement;
        imageFigure.remove();
    }
});
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

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- SEO -->
<!-- <script>
    $(document).ready(function(){
        var inputText1 = $('#textInput1');
        var inputText2 = $('#textInput2');
        var inputText3 = $('#textInput3');
        var textPreview1 = $('#textPreview1');
        var textPreview2 = $('#textPreview2');
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
                text = 'link-da-pagina';
            }
            newText = text.replace(/\s+/g, "-").toLowerCase();
            $(this).val($(this).val().replace(/\s+/g, "-").toLowerCase());
            textPreview2.text(newText);
        });

        inputText3.on('input', function () {
            var newText = inputText3.val();
            if (newText === '') {
                newText = 'Descrição da Página';
            }
            textPreview3.text(newText);
        });
    });
</script> -->

<!-- Text Counter -->
<script>
    $(document).ready(function() {
        // Esta função será chamada quando a página carregar
        $('#textInput1, #textInput3').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));

            if ($(this).is('#textInput1')) {
                $('#textCounter1').text(currentLength + ' de ' + maxLength + ' caracteres');
            } else if ($(this).is('#textInput3')) {
                $('#textCounter3').text(currentLength + ' de ' + maxLength + ' caracteres');
            }
        });

        // Defina o valor inicial do contador quando a página carregar
        var seoName = "<?php echo $product['seo_name']; ?>";
        var seoDescription = "<?php echo $product['seo_description']; ?>";

        var currentLength1 = seoName.length;
        var currentLength3 = seoDescription.length;

        var maxLength1 = parseInt($('#textInput1').attr('maxlength'));
        var maxLength3 = parseInt($('#textInput3').attr('maxlength'));

        $('#textCounter1').text(currentLength1 + ' de ' + maxLength1 + ' caracteres');
        $('#textCounter3').text(currentLength3 + ' de ' + maxLength3 + ' caracteres');
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
                // Aqui você pode adicionar a lógica para salvar os dados do formulário
                // alert('Dados salvos!');
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

<?php

    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um produto!";
}
?>