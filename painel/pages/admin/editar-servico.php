<?php
// Pega o id da loja
$shop_id = $id;

//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_services";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($service) {
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

<!-- Codigo do Servico -->
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

    a.form-label
    {
        color: #212529;
        text-decoration: none;
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

<style>
    .image-preview-container {
        position: relative;
        text-align: center;

        background-color: #f9f9f9;
        width: 100%;
        padding: 3.12em 1.87em;
        border: 2px dashed #c4c4c4;
        border-radius: 0.5em;
        cursor: pointer;
    }

    .image-preview {
        max-width: 100%;
        max-height: 400px;
        margin: 0 auto;
        display: none; /* A imagem está oculta inicialmente */
    }

    .file-input {
        display: none; /* Ocultar o input de arquivo original */
    }
</style>

<style>
    .select-container
    {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .select
    {
        padding: 3rem 2rem;
        margin: 0 2rem;
        border: 1px solid var(--border-color);
        border-radius: .3rem;
        background-color: #f9f9f9;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>

<!-- Modal de Servicos -->
<div class="modal fade" id="categoriasModal" tabindex="-1" role="dialog" aria-labelledby="categoriasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Escolher Serviço
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <!-- Adicione aqui a lógica para exibir as categorias do banco de dados e a funcionalidade de pesquisa -->
                <input type="text" id="searchCategoria" class="form-control mb-3" placeholder="Pesquisar Serviços">
                <p class="fw-semibold d-none" id="noResultCategories">Nenhum Serviço Encontrado</p>
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

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>servicos" class="fs-5 text-decoration-none text-reset">Serviços</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Editar Serviço</li>
            </ol>
        </nav>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/edit_service.php" method="post" enctype="multipart/form-data">

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="mb-3">
                <label for="name" class="form-label small">Nome do Serviço *</label>
                <input type="text" class="form-control" name="name" id="name" maxlength="120" aria-describedby="nameHelp" required value="<?php echo $service['name']; ?>">
                <p class="small text-decoration-none" style="color: #01C89B;">https://dropidigital.com.br/servicos/<span class="fw-semibold" id="linkPreview"><?php echo $service['link']; ?></span></p>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div>
                        <label for="activeService" class="form-label small">Serviço ativo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="activeService" value='1' <?php echo ($service['status'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="activeCheckbox" for="activeService"><?php echo ($service['status'] == 1) ? "Sim" : "Não"; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div id="containerEmphasis">
                        <label for="emphasisService" class="form-label small">Destacar?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasisService" value="1" <?php echo ($service['emphasis'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasisService"><?php echo ($service['emphasis'] == 1) ? "Sim" : "Não"; ?></label>
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
                    <input type="number" step="0.01" class="form-control text-end" name="price" id="moneyInput1" placeholder="0,00" value="<?php echo $service['price']; ?>" <?php echo ($service['without_price'] == 1) ? "disabled" : ""; ?>>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="without_price" id="withoutPrice" <?php echo ($service['without_price'] == 1) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="withoutPrice">Sem preço</label>
                </div>
            </div>
            <div class="col-md-6">
                <label for="moneyInput2" class="form-label small">Preço promocional</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control text-end" name="discount" id="moneyInput2" placeholder="0,00" value="<?php echo $service['discount']; ?>" <?php echo ($service['without_price'] == 1) ? "disabled" : ""; ?>>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Imagens
            <label for="file-input-1" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="file-input-1" class="image-preview-container">
                <img src="<?= INCLUDE_PATH_DASHBOARD . 'back-end/admin/service/' . $service['id'] . '/card-image/' . $service['card_image']; ?>" alt="Image Preview" class="image-preview <?= (isset($service['card_image'])) ? "d-block" : ""; ?>" id="image-preview-1">
                <div class="center-text <?= (isset($service['card_image'])) ? "d-none" : ""; ?>" id="text-1">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                    <small id="dimensions">Dimensões: 310 x 310px</small>
                </div>
            </label>
            <input type="file" name="card_image" accept="image/*" class="file-input" id="file-input-1">
            <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <input type="radio" class="d-none" name="select" id="selectImage" value="image" <?= (isset($service['image'])) ? "checked" : ""; ?>>
    <input type="radio" class="d-none" name="select" id="selectVideo" value="video" <?= (isset($service['video'])) ? "checked" : ""; ?>>

    <div class="card mb-3 p-0 <?= (isset($service['image']) || isset($service['video'])) ? "d-none" : ""; ?>" id="select">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Selecionar</div>
        <div class="card-body px-5 py-3">
            <div class="select-container">
                <label for="selectImage" class="select" id="select-image-button">
                    <i class='bx bx-image fs-1 mb-2'></i>
                    <p class="fw-semibold">Upload da imagem</p>
                </label>
                <label for="selectVideo" class="select" id="select-video-button">
                    <i class='bx bxl-youtube fs-1 mb-2'></i>
                    <p class="fw-semibold">Adicionar vídeo</p>
                </label>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0 <?= (!isset($service['image'])) ? "d-none" : ""; ?>" id="select-image">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Upload da imagem
            <label for="selectVideo" id="back-video" class="small" style="cursor: pointer;">Selecionar vídeo</label>
        </div>
        <div class="card-body px-5 py-3">
            <div class="mb-1">
                <label for="file-input-2" class="image-preview-container">
                    <img src="<?= INCLUDE_PATH_DASHBOARD . 'back-end/admin/service/' . $service['id'] . '/image/' . $service['image']; ?>" alt="Image Preview" class="image-preview <?= (isset($service['image'])) ? "d-block" : ""; ?>" id="image-preview-2">
                    <div class="center-text <?= (isset($service['image'])) ? "d-none" : ""; ?>" id="text-2">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                        <small id="dimensions">Dimensões: 1920 x 535px</small>
                    </div>
                </label>
                <input type="file" name="image" accept="image/*" class="file-input" id="file-input-2">
                <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0 <?= (!isset($service['video'])) ? "d-none" : ""; ?>" id="select-video">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Adicionar vídeo
            <label for="selectImage" id="back-image" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <div class="mb-1">
                <div class="d-flex justify-content-between">
                    <label for="exampleInputEmail" class="form-label small">Vídeo do produto</label>
                    <p class="form-text text-muted fw-normal small">Aceitamos apenas vídeos do YouTube</p>
                </div>
                <div class="position-relative">
                    <i class='bx bxl-youtube input-icon' ></i>
                    <input type="text" class="form-control icon-padding" name="video" id="video-url" placeholder="https://www.youtube.com/watch?v=000" aria-label="https://www.youtube.com/watch?v=000" value="<?php echo $service['video']; ?>">
                </div>
                <div id="video-display" class="d-flex justify-content-center"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#select-image-button, #back-image').on('click', function() {
                $('#select').addClass('d-none');
                $('#select-image').removeClass('d-none');
                $('#select-video').addClass('d-none');
            });

            $('#select-video-button, #back-video').on('click', function() {
                $('#select').addClass('d-none');
                $('#select-video').removeClass('d-none');
                $('#select-image').addClass('d-none');
            });
        });
    </script>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Descrição do Serviço
            <a href="https://www.base64-image.de" class="form-label small" target="_blank">
                Converter Imagem
                <i class="bx bx-help-circle edited" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="Para incluir uma imagem na descrição, há duas opções disponíveis:<br><br>• Convertê-la em um código base64 ou utilizar a URL direta da imagem. Atualmente, não suportamos o upload direto de imagens a partir do seu computador;<br><br>• Você também pode inserir a URL de imagens hospedadas online." data-bs-original-title="Para incluir uma imagem na descrição, há duas opções disponíveis:<br><br>• Convertê-la em um código base64 ou utilizar a URL direta da imagem. Atualmente, não suportamos o upload direto de imagens a partir do seu computador;<br><br>• Você também pode inserir a URL de imagens hospedadas online."></i>
            </a>
        </div>
        <div class="card-body px-5 py-3">
            <label for="editor" class="form-label small">Descrição do Serviço</label>
            <textarea name="description" id="editor"><?php echo $service['description']; ?></textarea>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Tooltip</div>
        <div class="card-body px-5 py-3">
            <div class="d-flex justify-content-between">
                <label for="tooltipContent" class="form-label small">Descrição para tooltip do Serviço</label>
                <small id="tooltipContentCounter" class="form-text text-muted">0 de 160 caracteres</small>
            </div>
            <textarea class="form-control" name="tooltip_content" id="tooltipContent" maxlength="160" rows="3"><?php echo $service['tooltip_content']; ?></textarea>
        </div>
    </div>

    <style>
        #serviceList .icon
        {
            color: var(--green-color);
        }

        #serviceList li
        {
            position: relative;
            display: flex;
            align-items: center;
            margin: .25rem 0;
        }
        #serviceList .actions
        {
            display: none;
            align-items: center;
            margin-left: .3rem;
        }
        #serviceList li:hover .actions
        {
            display: flex;
        }
        #serviceList .actions button
        {
            border: none;
            background: none;
        }
        #serviceList .save 
        {
            height: 38px;
            padding: 0 1rem;
            color: white;
            background: var(--green-color);
            border: none;
            border-radius: .3rem;
            cursor: pointer;
            margin-left: .3rem;
        }
        #serviceList .save:hover
        {
            background: var(--dark-green-color);
        }
    </style>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Benefícios</div>
        <div class="card-body row px-5 py-3">
            <div class="col-md-6">
                <label for="servico" class="form-label small">
                    Benefícios
                    <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Selecione Benefícios que serão exibidos na compra do Site Pronto."></i>
                </label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="servico" name="servico" placeholder="Adicionar Benefícios" aria-label="Adicionar Benefícios">
                    <button type="button" class="btn btn-outline-dark fw-semibold px-4" id="addService">Adicionar</button>
                </div>
                <small class="d-flex mb-3 px-3 py-2" id="noItems" style="color: #4A90E2; background: #ECF3FC;">Nenhum Item Adicionado</small>
                <ul class="list-style-one" id="serviceList">
                </ul>
            </div>
        </div>
    </div>

    <!-- Campo oculto para armazenar os serviços adicionados -->
    <input type="hidden" id="itemsIncludedArray" name="itemsIncludedArray" value='<?php echo $service['items_included']; ?>'>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var services = JSON.parse($('#itemsIncludedArray').val() || '[]');

            function updateServiceDisplay() {
                if (services.length === 0) {
                    $('#noItems').removeClass('d-none').addClass('d-block');
                } else {
                    $('#noItems').removeClass('d-block').addClass('d-none');
                }
            }

            function refreshServiceList() {
                $('#serviceList').empty();
                $.each(services, function(index, service) {
                    $('#serviceList').append(`<li><i class='bx bx-check icon me-1'></i>${service}<div class="actions"><button class="edit"><i class='bx bx-pencil'></i></button><button class="remove"><i class='bx bx-x'></i></button></div></li>`);
                });
                updateServiceDisplay();
            }

            refreshServiceList();  // Preenche a lista na inicialização

            $('#addService').click(function() {
                var newService = $('#servico').val().trim();
                if (newService) {
                    services.push(newService);
                    $('#itemsIncludedArray').val(JSON.stringify(services));
                    $('#servico').val('');
                    refreshServiceList();  // Atualiza a lista e a visibilidade do #noItems
                } else {
                    alert("Por favor, insira um nome de item válido.");
                }
            });

            $('#serviceList').on('click', '.edit', function() {
                var li = $(this).closest('li');
                var text = li.text().trim();
                li.html(`<input type='text' class='form-control editInput' value='${text}'><button class='save'>Salvar</button>`);
            });

            $('#serviceList').on('click', '.save', function() {
                var input = $(this).siblings('.editInput');
                var newValue = input.val().trim();
                var li = $(this).closest('li');
                li.html(`<i class='bx bx-check icon me-1' ></i> ${newValue} <div class="actions"><button class="edit"><i class='bx bx-pencil'></i></button> <button class="remove"><i class='bx bx-x'></i></button></div>`);
                services = $('#serviceList li').map(function() {
                    return $(this).contents().not($(this).children()).text().trim();
                }).get();
                $('#itemsIncludedArray').val(JSON.stringify(services));
                updateServiceDisplay();  // Atualiza a visibilidade do #noItems
            });

            $('#serviceList').on('click', '.remove', function() {
                var li = $(this).closest('li');
                var index = li.index();
                services.splice(index, 1);
                $('#itemsIncludedArray').val(JSON.stringify(services));
                refreshServiceList();  // Atualiza a lista e a visibilidade do #noItems
            });
        });
    </script>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ofertas</div>
        <div class="card-body px-5 py-3">
            <label for="searchOutsideModal" class="form-label small">
                Serviços
                <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Selecione serviços que serão oferecidos na compra do Site Pronto."></i>
            </label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="searchOutsideModal" placeholder="Buscar serviços já cadastradas" aria-label="Buscar serviços já cadastradas">
                <button type="button" class="btn btn-outline-dark fw-semibold px-4" data-bs-toggle="modal" data-bs-target="#categoriasModal">Ver Serviços</button>
            </div>
            <small class="d-flex mb-3 px-3 py-2" id="noCategories" style="color: #4A90E2; background: #ECF3FC;">Nenhum Serviço Adicionado</small>
            <table class="table table-hover d-none" id="categoriesTable">
                <thead class="table-light">
                    <tr>
                        <th class="small">Nome do Serviço</th>
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
                    <input type="text" class="form-control" name="sku" id="skuResult" placeholder="SKU" aria-label="SKU" aria-describedby="skuResult" style="max-width: 250px;" value="<?php echo $service['sku']; ?>">
                    <button class="btn btn-outline-dark fw-semibold px-4" type="button" id="gerarSKU">GERAR</button>
                </div>
                <small class="text-decoration-none" id="error-sku" style="color: rgb(229, 15, 56);"></small>
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
                            <label for="textInput1" class="form-label small">Nome do Serviço *</label>
                            <small id="textCounter1" class="form-text text-muted">0 de 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp" value="<?php echo $service['seo_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link do Serviço</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-do-servico" aria-label="link-do-servico" aria-describedby="emailHelp" value="<?php echo $service['link']; ?>">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição do Serviço</label>
                            <small id="textCounter3" class="form-text text-muted">0 de 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"><?php echo $service['seo_description']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1"><?php echo $service['seo_name']; ?></h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://dropidigital.com.br/servicos/<span class="fw-semibold" id="textPreview2"><?php echo $service['link']; ?></span></p>
                        <p class="small" id="textPreview3"><?php echo $service['seo_description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados da loja -->
    <input type="hidden" name="delete_images" id="delete-images-input">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Adicione esses campos ocultos no seu formulário -->
    <input type="hidden" name="categoriasSelecionadas[]" id="categoriasSelecionadasInput" value="<?php
        // Nome da tabela para a busca
        $tabela = 'tb_service_services';

        $sql = "SELECT * FROM $tabela WHERE service_id = :service_id ORDER BY (main = 1) DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':service_id', $service['id']);
        $stmt->execute();

        // Recuperar os resultados
        $serviceServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $primeiroElemento = false;
        $main = 0;

        if ($serviceServices) {
            // Loop através dos resultados e exibir todas as colunas
            foreach ($serviceServices as $serviceService) {
                $main = ($serviceService['main'] == "1") ? $serviceService['associated_service_id'] : "";

                echo ($primeiroElemento) ? "," : "";

                echo $serviceService['associated_service_id'];

                $primeiroElemento = true;
            }
        }
    ?>">

    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_service_services';

        $sql = "SELECT associated_service_id FROM $tabela WHERE service_id = :service_id AND main = :main ORDER BY id DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':service_id', $service['id']);
        $stmt->bindValue(':main', 1);
        $stmt->execute();

        // Recuperar os resultados
        $serviceServices = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>

    <!-- Categoria principal -->
    <input type="hidden" name="inputMainCategory" id="inputMainCategory" value="<?php echo @$serviceServices['associated_service_id']; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>servicos" class="text-decoration-none text-reset">Cancelar</a>
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
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
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
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
        }
    });
</script>

<?php
    // Nome da tabela para a busca
    $tabelaProductCategories = 'tb_service_services';
    $tabelaCategories = 'tb_services';

    $sqlProductCategories = "SELECT * FROM $tabelaProductCategories WHERE service_id = :service_id ORDER BY (main = 1) DESC";

    // Preparar e executar a consulta
    $stmtProductCategories = $conn_pdo->prepare($sqlProductCategories);
    $stmtProductCategories->bindParam(':service_id', $service['id']);
    $stmtProductCategories->execute();

    // Recuperar os resultados
    $serviceServices = $stmtProductCategories->fetchAll(PDO::FETCH_ASSOC);

    $resultArray = [];

    if ($serviceServices) {
        // Loop através dos resultados de tb_product_categories
        foreach ($serviceServices as $serviceService) {
            // Consultar tb_categories para obter o nome da categoria
            $sqlCategories = "SELECT * FROM $tabelaCategories WHERE id = :id ORDER BY id DESC";

            // Preparar e executar a consulta
            $stmtCategories = $conn_pdo->prepare($sqlCategories);
            $stmtCategories->bindParam(':id', $serviceService['associated_service_id']);
            $stmtCategories->execute();

            // Recuperar os resultados
            $services = $stmtCategories->fetch(PDO::FETCH_ASSOC);

            // Convertendo o id para formato numérico
            $serviceId = (int)$serviceService['associated_service_id'];

            // Adicionar ao array de resultados
            $resultArray[] = [
                'id' => $serviceId,
                'name' => $services['name']
            ];
        }
    }
?>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_services';

    $sql = "SELECT id, name FROM $tabela WHERE id != :id ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $service['id']);
    $stmt->execute();

    // Fetch all retorna um array contendo todas as linhas do conjunto de resultados
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Script jQuery para manipular o modal e as categorias -->
<script>
    $(document).ready(function () {
        // Array de categorias disponíveis (substitua com a lógica do seu banco de dados)
        var categoriasDisponiveis = <?php echo json_encode($services); ?>;

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

            var mainCategoryId = <?php echo (isset($serviceServices['service_id'])) ? $serviceServices['service_id'] : 0; ?>;

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
            if ($(this).val() === "1" || $(this).val() === "4"|| $(this).val() === "5") {
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
        inputCounter('tooltipContent', 'tooltipContentCounter');
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

    const activeService = document.getElementById("activeService");
    const activeCheckbox = document.getElementById("activeCheckbox");
    updateCheckboxText(activeService, activeCheckbox, "Sim", "Não");

    const emphasisService = document.getElementById("emphasisService");
    const emphasisCheckbox = document.getElementById("emphasisCheckbox");
    updateCheckboxText(emphasisService, emphasisCheckbox, "Sim", "Não");
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
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#name");
        var url = $("#url");
        var span = $("#linkPreview");

        var inputText2 = $('#textInput2');
        var textPreview2 = $('#textPreview2');

        input.on("input", function() {
            var value = input.val();

            // Remover acentos e substituir espaços por traço
            value = removerAcentosEespacos(value);
            
            span.text(value);

            url.val(value);

            inputText2.val(value);
            textPreview2.text(value);

            if (value === '') {
                span.text("...");
                textPreview2.text("link-da-pagina");
            }
        });

        function removerAcentosEespacos(texto) {
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
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
        });
    });
</script>

<!-- Link -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#url");

        input.on("input", function() {
            var value = input.val();

            // Remover acentos e substituir espaços por traço
            value = removerAcentosEespacos(value);

            input.val(value);
        });

        function removerAcentosEespacos(texto) {
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
        }
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

<!-- Imagem -->
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
    imagePreview("file-input-1", "image-preview-1", "text-1");
    imagePreview("file-input-2", "image-preview-2", "text-2");
</script>

<!-- Video Preview -->
<script>
    // Função para processar a URL
    function processarURL() {
        const videoForm = document.getElementById("video-url");
        const videoDisplay = document.getElementById("video-display");

        const videoUrl = videoForm.value.trim();
        if (videoUrl === "") {
            videoDisplay.innerHTML = "";
            return;
        }

        const videoId = getYouTubeVideoId(videoUrl);

        if (videoId) {
            const embedCode = `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen class="mt-3"></iframe>`;
            videoDisplay.innerHTML = embedCode;
        } else {
            videoDisplay.innerHTML = "<p class='fw-normal small mt-3'>URL de vídeo inválida.</p>";
        }
    }

    // Função para obter o ID do vídeo do YouTube
    function getYouTubeVideoId(url) {
        const regex = /(?:\?v=|\/embed\/|\.be\/)([a-zA-Z0-9_-]+)/;
        const matches = url.match(regex);
        return matches ? matches[1] : null;
    }

    // Chame a função processarURL quando a página for carregada
    document.addEventListener("DOMContentLoaded", processarURL);
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
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
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
<?php

    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um Serviço!";
}
?>