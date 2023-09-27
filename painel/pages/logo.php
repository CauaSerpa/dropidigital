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
    #image-display-1,
    #image-display-2,
    #image-display-3 {
        position: relative;
        width: 100%;
        display: flex;
        gap: 1.25em;
        flex-wrap: wrap;
    }
    #image-display-1 figure,
    #image-display-2 figure,
    #image-display-3 figure {
        position: relative;
        width: 118px;
        height: 118px;
        background: #f9f9f9;
    }
    #image-display-1 img,
    #image-display-2 img,
    #image-display-3 img {
        width: 118px;
        height: 118px;
        object-fit: cover;
        border: 1px solid #c4c4c4;
        border-radius: .5rem;
    }
    #image-display-1 img:hover,
    #image-display-2 img:hover,
    #image-display-3 img:hover {
        cursor: -webkit-grab;
        cursor: grab;
    }
    #image-display-1 button,
    #image-display-2 button,
    #image-display-3 button {
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
    #image-display-1 button::before,
    #image-display-2 button::before,
    #image-display-3 button::before {
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
    #image-display-1 figcaption,
    #image-display-2 figcaption,
    #image-display-3 figcaption {
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
    #error-1,
    #error-2,
    #error-3 {
        text-align: center;
        color: #ff3030;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Logo</h2>
            </div>
        </div>
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/add_logo.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Logo
            <label for="upload-button-1" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="upload-button-1" class="image-container mt-3">
                <input type="file" name="logo[]" id="upload-button-1" multiple accept="image/*" />
                <div for="upload-button-1" class="dropzone">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                </div>
                <div id="error-1"></div>
            </label>
            <div class="sortable-container mt-3">
                <div id="image-display-1"></div>
            </div>
            <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Logo para Celular
            <label for="upload-button-2" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="upload-button-2" class="image-container mt-3">
                <input type="file" name="logo_mobile[]" id="upload-button-2" multiple accept="image/*" />
                <div for="upload-button-2" class="dropzone">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                </div>
                <div id="error-2"></div>
            </label>
            <div class="sortable-container mt-3">
                <div id="image-display-2"></div>
            </div>
            <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Ícone da página (Favicon)
            <label for="upload-button-3" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="upload-button-3" class="image-container mt-3">
                <input type="file" name="favicon[]" id="upload-button-3" multiple accept="image/*" />
                <div for="upload-button-3" class="dropzone">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Arraste e solte as imagens aqui</p>
                </div>
                <div id="error-3"></div>
            </label>
            <div class="sortable-container mt-3">
                <div id="image-display-3"></div>
            </div>
            <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="display: none; position: fixed; width: 100%; left: 78px; bottom: 0; z-index: 99999;">
        <div class="container-save-button container card-header fw-semibold bg-transparent d-flex align-items-center justify-content-between">
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

<!-- Imagens -->
<!-- <script>
    let uploadButton = document.getElementById("upload-button");
    let container = document.querySelector(".image-container");
    let error = document.getElementById("error");
    let imageDisplay = document.getElementById("image-display");
    let loadedImages = [];
    const maxImages = 1; // Define o número máximo de imagens

    const fileHandler = (file, name, type, index) => {
        if (loadedImages.length >= maxImages) {
            // Imagens atingiram o limite
            error.innerText = `Você só pode fazer upload de até ${maxImages} imagens.`;
            return false;
        }

        if (type.split("/")[0] !== "image") {
            //File Type Error
            error.innerText = "Por favor carregue um arquivo de imagem";
            return false;
        }

        if (loadedImages.includes(name)) {
            // Image already loaded
            error.innerText = "Esta imagem já foi carregada";
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
</script> -->

<script>
    const maxImages = 1; // Define o número máximo de imagens para cada campo

    function initializeFileInput(inputId, imageDisplayId, errorId) {
        let uploadButton = document.getElementById(inputId);
        let imageDisplay = document.getElementById(imageDisplayId);
        let error = document.getElementById(errorId);
        let loadedImages = [];

        const fileHandler = (file, name, type, index) => {
            if (loadedImages.length >= maxImages) {
                // Imagens atingiram o limite
                error.innerText = `Você só pode fazer upload de até ${maxImages} imagens.`;
                return false;
            }

            if (type.split("/")[0] !== "image") {
                // File Type Error
                error.innerText = "Por favor, carregue um arquivo de imagem";
                return false;
            }

            if (loadedImages.includes(name)) {
                // Imagem já carregada
                error.innerText = "Esta imagem já foi carregada";
                return false;
            }

            loadedImages.push(name);
            error.innerText = "";
            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = () => {
                // Image and file name
                let imageContainer = document.createElement("figure");
                let img = document.createElement("img");
                img.src = reader.result;
                imageContainer.appendChild(img);

                // Adicionar um botão de exclusão
                let deleteButton = document.createElement("button");
                deleteButton.addEventListener("click", () => {
                    loadedImages = loadedImages.filter(imgName => imgName !== name);
                    imageContainer.remove();
                });
                imageContainer.appendChild(deleteButton);

                // Adicionar classe para classificação
                imageContainer.className = "sortable-image";

                imageDisplay.appendChild(imageContainer);
            };

            // Inicializar classificável
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
        imageDisplay.addEventListener("dragenter", (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageDisplay.classList.add("dropzone-active");
        }, false);

        imageDisplay.addEventListener("dragleave", (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageDisplay.classList.remove("dropzone-active");
        }, false);

        imageDisplay.addEventListener("dragover", (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageDisplay.classList.add("dropzone-active");
        }, false);

        imageDisplay.addEventListener("drop", (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageDisplay.classList.remove("dropzone-active");
            let draggedData = e.dataTransfer;
            let files = draggedData.files;
            handleFiles(files);
        }, false);

        window.onload = () => {
            error.innerText = "";
        };
    }

    // Inicialize os campos de entrada de arquivo
    initializeFileInput("upload-button-1", "image-display-1", "error-1");
    initializeFileInput("upload-button-2", "image-display-2", "error-2");
    initializeFileInput("upload-button-3", "image-display-3", "error-3");
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
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