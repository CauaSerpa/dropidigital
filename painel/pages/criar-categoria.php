<style>
    .image-container
    {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    /* Image Preview */
    .person-image .image-preview
    {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: #ccc;
    }
    .person-image .image-preview.icon
    {
        border-radius: 0%;
        width: 40px;
        height: 40px;
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_category.php" method="post" enctype="multipart/form-data">

    <div class="page__header center">
        <div class="header__title">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-3">
                    <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>categorias" class="fs-5 text-decoration-none text-reset">Categorias</a></li>
                    <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Categoria</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body px-4 py-3">
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label for="name" class="form-label small">Nome da categoria *</label>
                </div>
                <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" require>
                <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="linkPreview">...</span></p>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label for="name" class="form-label small">Descrição *</label>
                    <small id="descriptionCounter" class="form-text text-muted">0 de 4000 caracteres</small>
                </div>
                <textarea class="form-control" name="description" id="description" maxlength="4000" rows="3"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="moneyInput1" class="form-label small">Categoria pai *</label>
                    <div class="input-group">
                        <select class="form-select" name="parent_category" id="parentCategory" aria-label="Default select example">
                            <option value="1" selected>[ Raiz ]</option>
                            <?php
                                // Aqui você pode popular a tabela com dados do banco de dados
                                // Vamos supor que cada linha tem um ID único
                                
                                // Nome da tabela para a busca
                                $tabela = 'tb_categories';

                                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND parent_category = :parent_category ORDER BY id DESC";

                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindParam(':shop_id', $id);
                                $stmt->bindValue(':parent_category', 1);
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
            </div>
            <div class="row">
                <div class="col-md-6 d-flex justify-content-between mb-3">
                    <div>
                        <label for="activeCategory1" class="form-label small">Categoria ativa?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="activeCategory1" value="1" checked>
                            <label class="form-check-label" id="textCheckbox1" for="activeCategory1">Sim</label>
                        </div>
                    </div>

                    <?php
                        // Nome da tabela para a busca
                        $tabela = 'tb_categories';

                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND emphasis = :emphasis ORDER BY id DESC";

                        // Preparar e executar a consulta
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $id);
                        $stmt->bindValue(':emphasis', 1);
                        $stmt->execute();

                        $countCategoriesEmphasis = $stmt->rowCount();
                    ?>

                    <style>
                        input.disabled, input:disabled
                        {
                            background-image: var(--bs-form-switch-bg) !important;
                            background-position: left center !important;
                            background-repeat: no-repeat !important;
                        }
                    </style>

                    <div id="containerEmphasis">
                        <label for="emphasis" class="form-label small">
                            Destacar no menu?
                            <i class="bx bx-help-circle" data-toggle="tooltip" data-placement="top" aria-label="É possível destacar até 6 categorias no menu." data-bs-original-title="É possível destacar até 6 categorias no menu."></i>
                        </label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasis" value="1" <?php echo ($countCategoriesEmphasis >= 4) ? "disabled" : ""; ?>>
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasis">Não</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0" id="images">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Imagens</div>
        <div class="card-body row px-4 py-3">
            <div class="col-md-6 image-container" id="shop-banner">
                <p class="fw-semibold mb-3">Imagem para banner</p>
                <div class="person-image mb-3">
                    <img id="imagemPreview" class="image-preview object-fit-cover" src="../assets/images/services/service-next.jpg" alt="Preview da imagem">
                </div>
                <label for="imagemInput" class="btn btn btn-outline-secondary d-flex align-items-center fw-semibold mb-1">
                    <i class='bx bx-image fs-5 me-2'></i>
                    Adicionar Foto
                </label>
                <small class="mb-1">
                    Imagem em .jpg com<br>
                    150 px x 150 px
                </small>
                <input type="file" name="image" id="imagemInput" accept="image/*" class="d-none">
            </div>
            <div class="col-md-6 image-container" id="header-icon">
                <p class="fw-semibold mb-3">Ícone para header</p>
                <div class="person-image mb-3">
                    <img id="iconPreview" class="image-preview icon object-fit-cover" src="../assets/images/blog/author.jpg" alt="Preview da imagem">
                </div>
                <label for="iconInput" class="btn btn btn-outline-secondary d-flex align-items-center fw-semibold mb-1">
                    <i class='bx bx-image fs-5 me-2'></i>
                    Adicionar Foto
                </label>
                <small class="mb-1">
                    Imagem em .png com<br>
                    40 px x 40 px
                </small>
                <input type="file" name="icon" id="iconInput" accept="image/*" class="d-none">
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
                            <label for="textInput1" class="form-label small">Nome da categoria *</label>
                            <small id="textCounter1" class="form-text text-muted">0 de 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da categoria</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da categoria</label>
                            <small id="textCounter3" class="form-text text-muted">0 de 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1">Título da categoria</h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="textPreview2">link-da-categoria</span></p>
                        <p class="small" id="textPreview3">Descrição da categoria</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="link" id="link" value="">
    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="display: none; position: fixed; width: 100%; left: 78px; bottom: 0; z-index: 99999;">
        <div class="container-save-button container card-header fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>categorias" class="text-decoration-none text-reset">Cancelar</a>
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

<!-- Imagens -->
<script>
    $(document).ready(function() {
        // Função para verificar e atualizar o estado dos elementos
        function verificarMostrando() {
            // Verifica o valor selecionado no parentCategory
            if ($("#parentCategory").val() === "1") {
                $("#shop-banner").show();
                $("#containerEmphasis").show();
            } else {
                $("#shop-banner").hide();
                $("#containerEmphasis").hide();
            }

            // Verifica se o checkbox emphasis está marcado
            if ($("#emphasis").is(":checked")) {
                $("#header-icon").show();
            } else {
                $("#header-icon").hide();
            }

            // Verifica se algum dos dois elementos está sendo mostrado e atualiza #images
            if ($("#emphasis").is(":checked") || $("#parentCategory").val() === "1") {
                $("#images").show();
            } else {
                $("#images").hide();
            }
        }

        // Quando ocorrer uma mudança no parentCategory ou no estado do checkbox emphasis
        $("#parentCategory, #emphasis").change(function() {
            verificarMostrando();
        });

        // Chame a função inicialmente para verificar o estado na carga da página
        verificarMostrando();
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

        inputCounter('description', 'descriptionCounter');
    });
</script>

<!-- Image Preview -->
<script>
    // Função para ativar a visualização de imagem em um elemento de entrada de arquivo
    function ativarVisualizacaoImagem(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        input.addEventListener('change', function () {
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                preview.src = '../assets/images/services/service-next.jpg'; // Defina a imagem de fallback aqui
            }
        });
    }

    // Chame a função para ativar a visualização de imagem para cada input
    ativarVisualizacaoImagem('imagemInput', 'imagemPreview');
    ativarVisualizacaoImagem('iconInput', 'iconPreview');
</script>

<!-- Link -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#name");
        var span = $("#linkPreview");

        // Adicione um ouvinte de evento de entrada ao campo de entrada
        input.on("input", function() {
            // Obtenha o valor atual do campo de entrada
            var valor = input.val();

            if (valor === '') {
                valor = '...';
            }
            
            // Substitua espaços extras por um único traço e converta para letras minúsculas
            valor = valor.replace(/\s+/g, "-").toLowerCase();
            
            // Atualize o texto no span com o valor formatado
            span.text(valor);

            $('#link').val(valor);
        });
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

    const emphasis = document.getElementById("emphasis");
    const emphasisCheckbox = document.getElementById("emphasisCheckbox");
    updateCheckboxText(emphasis, emphasisCheckbox, "Sim", "Não");
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

        inputText2.on("input", function() {
            var text = inputText2.val();
            if (text === '') {
                text = 'link-da-categoria';
            }
            newText = text.replace(/\s+/g, "-").toLowerCase();
            $(this).val($(this).val().replace(/\s+/g, "-").toLowerCase());
            textPreview2.text(newText);
            $('#link').val(newText);
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
            $('.main.container').addClass('save-button-show');
        });

        $('#saveButton button').click(function () {
            if (formChanged) {
                formChanged = false;
                $('#saveButton').hide();
                $('.main.container').removeClass('save-button-show');
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
                $('.main.container').removeClass('save-button-show');
            }
        });
    });
</script>