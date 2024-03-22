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
        content: 'Banner';
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

<!-- Multi Select -->
<style>
    /* Estilo para o select personalizado */
    .categoria-select {
        position: relative;
    }

    .categoria-select .select-wrapper {
        display: inline-block;
        width: 100%;
    }

    .categoria-select .custom-select {
        cursor: pointer;
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        padding: 5px;
        position: relative;
        min-width: 100%;
        height: 38px;
    }

    .categoria-select .options {
        position: absolute;
        display: none;
        list-style-type: none;
        padding: 0;
        margin: 0;
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
        top: calc(100% + 10px);
        left: 0;
        background-color: #fff;
        width: 100%;
        z-index: 9999;
    }

    .categoria-select .options li {
        padding: 5px;
        cursor: pointer;
    }

    .categoria-select .options li:hover {
        background-color: #f0f0f0;
    }

    /* Estilo para as categorias selecionadas */
    .categoria-select .selected-categorias {
        display: flex;
        flex-wrap: wrap;
    }

    .categoria-select .categoria {
        background-color: #007bff;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        margin-right: 5px;
        display: flex;
        align-items: center;
        cursor: pointer;
        height: 28px;
    }

    .categoria-select .categoria .remove {
        margin-left: 5px;
        cursor: pointer;
    }

    /* Estilo para o option selecionado */
    .categoria-select .options li.selected {
        background-color: #007bff;
        color: #fff;
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

    /* Button */
    .btn.btn-success
    {
        background: var(--green-color);
        border-color: var(--green-color);
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color);
        border-color: var(--dark-green-color);
    }
</style>
<!-- Banner Selecionado -->
<style>
    .selecionado
    {
        color: white !important;
        background-color: var(--green-color) !important;
    }
    .banner-size
    {
        display: none;
        position: absolute;
        right: 10px;
        top: 10px;
        transition: .3s;
    }
    .selecionado .banner-size,
    .banner:hover .banner-size
    {
        display: block;
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

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>banners" class="fs-5 text-decoration-none text-reset">Banners</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Banner</li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-banner" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_banner.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="name" class="form-label small">Nome do Banner *</label>
                        </div>
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" require>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label small">Posição do banner *</label>
                        <div class="input-group">
                            <select class="form-select" name="location" id="location" aria-label="Default select example">
                                <option value="" selected disabled>Selecione a posição</option>
                                <option value="full-banner">Full Banner</option>
                                <option value="shelf-banner">Banner prateleira</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="moneyInput1" class="form-label small">Página de publicação *</label>
                        <div class="categoria-select">
                            <div class="select-wrapper">
                                <div class="custom-select">
                                    <div class="selected-option">
                                        <div class="selected-categorias">
                                            <!-- As categorias selecionadas serão exibidas aqui -->
                                        </div>
                                    </div>
                                    <ul class="options">
                                        <li data-value="categoria1">Categoria 1</li>
                                        <li data-value="categoria2">Categoria 2</li>
                                        <li data-value="categoria3">Categoria 3</li>
                                        <li data-value="categoria4">Categoria 4</li>
                                        <li data-value="categoria5">Categoria 5</li>
                                        <!-- Adicione mais opções de categoria conforme necessário -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label small">Definir categoria</label>
                        <div class="input-group">
                            <select class="form-select" name="category" aria-label="Default select example">
                                <option value="" selected disabled>Selecione a categoria</option>
                                <option value="1">[ Raiz ]</option>
                                <?php
                                    // Aqui você pode popular a tabela com dados do banco de dados
                                    // Vamos supor que cada linha tem um ID único
                                    
                                    // Nome da tabela para a busca
                                    $tabela = 'tb_categories';

                                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                                    // Preparar e executar a consulta
                                    $stmt = $conn_pdo->prepare($sql);
                                    $stmt->bindParam(':shop_id', $id);
                                    $stmt->execute();

                                    // Recuperar os resultados
                                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt->rowCount() > 0) {
                                        // Loop através dos resultados e exibir todas as colunas
                                        foreach ($resultados as $usuario) {
                                            echo "<option value='" . $usuario['id'] . "'>" . $usuario['name'] . "</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="link" class="form-label small">Link do Banner</label>
                        </div>
                        <input type="text" class="form-control" name="link" id="link" placeholder="https://..." aria-describedby="linkHelp" require>
                    </div>
                    <div class="mb-3">
                        <label for="target" class="form-label small">Quando clicar no link</label>
                        <div class="input-group">
                            <select class="form-select" name="target" aria-label="Default select example">
                                <option value="_self">Abrir na mesma janela</option>
                                <option value="_blank">Abrir em nova janela</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="title" class="form-label small">Título do Banner</label>
                        </div>
                        <textarea class="form-control" name="title" id="title" maxlength="4000" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label small">Banner ativo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" value="1" checked>
                            <label class="form-check-label" id="textCheckbox1" for="status">Sim</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <p class="fw-semibold">Posição do banner</p>
                        <p class="small mb-4">
                            Na nova estrutura o tamaho dos banners variam de acordo com a disposição escolhida no menu configurar tema e a resolução da tela, <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda" style="color: var(--green-color) !important;">clique aqui</a> para saber mais detalhes sobre as dimensões.
                        </p>
                        <div class="banner full-banner card bg-body-secondary text-center p-5 mb-3">
                            <i class='bx bx-help-circle banner-size' data-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="Desktop: 1920 x 535px<br>Celular: 500 x 375px"></i>
                            <p class="small fw-semibold">FULL BANNER</p>
                        </div>
                        <div class="banner row g-3 mb-3">
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center">
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center">
                                    <i class='bx bx-help-circle banner-size' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Largura recomendada: 500px Altura recomendada: 200px"></i>
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center">
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center">
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Upload da imagem
            <label for="file-input-1" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <div class="mb-1">
                <label for="file-input-1" class="image-preview-container">
                    <img src="#" alt="Image Preview" class="image-preview" id="image-preview-1">
                    <div class="center-text" id="text-1">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                        <small id="dimensions">Dimensões: 1920 x 535px</small>
                    </div>
                </label>
                <input type="file" name="image" accept="image/*" class="file-input" id="file-input-1">
                <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
            </div>
            
            <button type="button" id="addMobileBanner" class="btn btn-success small d-none">
                Adicionar banner para celular
            </button>
            
            <div class="d-none" id="mobileBanner">
                <p class="fs-5 fw-semibold mb-4">Banner para celular</p>

                <label for="file-input-2" class="image-preview-container">
                    <img src="#" alt="Image Preview" class="image-preview" id="image-preview-2">
                    <div class="center-text" id="text-2">
                        <i class='bx bx-image fs-1'></i>
                        <p class="fs-5 fw-semibold">Faça upload da imagem aqui</p>
                        <small>Dimensões: 500 x 375px</small>
                    </div>
                </label>
                <input type="file" name="mobile" accept="image/*" class="file-input" id="file-input-2">
                <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
            </div>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>banners" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>

</form>

<!-- Link para o TinyMCE CSS -->
<script src="https://cdn.tiny.cloud/1/xiqhvnpyyc1fqurimqcwiz49n6zap8glrv70bar36fbloiko/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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

        inputCounter('description', 'descriptionCounter');
    });
</script>

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

    const activeCategory1 = document.getElementById("activeCategory1");
    const textCheckbox1 = document.getElementById("textCheckbox1");
    updateCheckboxText(activeCategory1, textCheckbox1, "Sim", "Não");

    const activeCategory2 = document.getElementById("activeCategory2");
    const textCheckbox2 = document.getElementById("textCheckbox2");
    updateCheckboxText(activeCategory2, textCheckbox2, "Sim", "Não");
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

<!-- Mobile Banner -->
<script>
    $(document).ready(function() {
        $("#addMobileBanner").click(function() {
            $("#addMobileBanner").addClass("d-none");
            $("#mobileBanner").removeClass("d-none");
        });

        $("#location").change(function() {
            if ($(this).val() === "full-banner") {
                $('#dimensions').text('Dimensões: 1920 x 535px');

                $("#addMobileBanner").removeClass("d-none");
                $("#addMobileBanner").addClass("d-flex");
            } else {
                $('#dimensions').text('Dimensões: 500 x 200px');

                $("#addMobileBanner").removeClass("d-flex");
                $("#addMobileBanner").addClass("d-none");

                $("#mobileBanner").addClass("d-none");
            }
        });
    });
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
<script>
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
        var textPreview3 = $('#textPreview3');

        inputText1.on('input', function () {
            var newText = inputText1.val();
            if (newText === '') {
                newText = 'Título da página';
            }
            textPreview1.text(newText);
        });

        inputText2.on('input', function () {
            var newText = inputText2.val();
            if (newText === '') {
                newText = 'link-da-pagina';
            }
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
            $('.main .container.grid').addClass('save-button-show');
        });

        $('#saveButton button').click(function () {
            if (formChanged) {
                formChanged = false;
                $('#saveButton').hide();
                $('.main .container.grid').removeClass('save-button-show');
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
                $('.main .container.grid').removeClass('save-button-show');
            }
        });
    });
</script>

<!-- Multi Select -->
<script>
    $(document).ready(function () {
        $(".custom-select").click(function () {
            $(".options").toggle();
        });

        $(".options li").click(function () {
            var selectedCategoria = $(this).data("value");
            var selectedCategoriaText = $(this).text();

            if (!$(this).hasClass("selected")) {
                // Adicionar categoria selecionada
                $(this).addClass("selected");
                $(".selected-categorias").append(
                    '<div class="categoria" data-value="' + selectedCategoria + '">' +
                        selectedCategoriaText +
                        '<span class="remove d-flex align-items-center" onclick="removeCategoria(this)"><i class="bx bx-x-circle"></i></span>' +
                    "</div>"
                );
            } else {
                // Remover categoria selecionada
                $(this).removeClass("selected");
                $(".selected-categorias .categoria[data-value='" + selectedCategoria + "']").remove();
            }
        });
    });

    function removeCategoria(element) {
        var categoriaValue = $(element).parent().data("value");
        $(".options li[data-value='" + categoriaValue + "']").removeClass("selected");
        $(element).parent().remove();
    }
</script>

<!-- Cor no Banner Selecionado -->
<script>
    $(document).ready(function () {
        // Detecta a mudança na seleção do <select>
        $("#location").change(function () {
            // Obtém o valor selecionado
            var selectedValue = $(this).val();

            // Remove a classe "selecionado" de todas as divs
            $(".selecionado").removeClass("selecionado");
            
            // Adiciona a classe "selecionado" apenas à div correspondente à opção selecionada
            $("." + selectedValue).addClass("selecionado");
        });
    });
</script>