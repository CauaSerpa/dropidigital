<?php
$shop_id = $id;

//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_banner_info";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($banner) {
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

<!-- Codigo do site -->
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

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>banners" class="fs-5 text-decoration-none text-reset">Banners</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Editar Banner</li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/editar-banner" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_banner.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="name" class="form-label small">Nome do Banner *</label>
                        </div>
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="<?php echo $banner['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label small">Posição do banner *</label>
                        <div class="input-group">
                            <select class="form-select" name="location" id="location" aria-label="Default select example">
                                <option value="" disabled>Selecione a posição</option>
                                <option value="full-banner" <?php echo ($banner['location'] == "full-banner") ? "selected" : ""; ?>>Full Banner</option>
                                <option value="shelf-banner" <?php echo ($banner['location'] == "shelf-banner") ? "selected" : ""; ?>>Banner prateleira</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label small">Definir categoria</label>
                        <div class="input-group">
                            <select class="form-select" name="category" aria-label="Default select example">
                                <option value="1">[ Raiz ]</option>
                                <?php
                                    // Aqui você pode popular a tabela com dados do banco de dados
                                    // Vamos supor que cada linha tem um ID único
                                    
                                    // Nome da tabela para a busca
                                    $tabela = 'tb_categories';

                                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND parent_category = :parent_category ORDER BY id DESC";

                                    // Preparar e executar a consulta
                                    $stmt = $conn_pdo->prepare($sql);
                                    $stmt->bindParam(':shop_id', $shop_id);
                                    $stmt->bindValue(':parent_category', 1);
                                    $stmt->execute();

                                    // Recuperar os resultados
                                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if ($stmt->rowCount() > 0) {
                                        // Loop através dos resultados e exibir todas as colunas
                                        foreach ($resultados as $usuario) {
                                            if ($banner['category'] == $usuario['id']) {
                                                $selected = "selected";
                                            }

                                            echo "<option value='" . $usuario['id'] . "' $selected>" . $usuario['name'] . "</option>";
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
                        <input type="text" class="form-control" name="link" id="link" placeholder="https://..." aria-describedby="linkHelp" value="<?php echo $banner['link']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="target" class="form-label small">Quando clicar no link</label>
                        <div class="input-group">
                            <select class="form-select" name="target" aria-label="Default select example">
                                <option value="_self" <?php echo ($banner['target'] == "_self") ? "selected" : ""; ?>>Abrir na mesma janela</option>
                                <option value="_blank" <?php echo ($banner['target'] == "_blank") ? "selected" : ""; ?>>Abrir em nova janela</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="title" class="form-label small">Título do Banner</label>
                        </div>
                        <textarea class="form-control" name="title" id="title" maxlength="4000" rows="3"><?php echo $banner['title']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label small">Banner ativo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" value="1" <?php echo ($banner['status'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="textCheckbox1" for="status"> <?php echo ($banner['status'] == 1) ? "Sim" : "Não"; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <p class="fw-semibold">Posição do banner</p>
                        <p class="small mb-4">
                            Na nova estrutura o tamaho dos banners variam de acordo com a disposição escolhida no menu configurar tema e a resolução da tela, <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda" style="color: var(--green-color) !important;">clique aqui</a> para saber mais detalhes sobre as dimensões.
                        </p>
                        <div class="banner full-banner card bg-body-secondary text-center p-5 mb-3 <?php echo ($banner['location'] == "full-banner") ? "selecionado" : ""; ?>">
                            <i class='bx bx-help-circle banner-size' data-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="Largura recomendada: 1920px<br>Altura recomendada: 535px"></i>
                            <p class="small fw-semibold">FULL BANNER</p>
                        </div>
                        <div class="banner row g-3 mb-3">
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center <?php echo ($banner['location'] == "shelf-banner") ? "selecionado" : ""; ?>">
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center <?php echo ($banner['location'] == "shelf-banner") ? "selecionado" : ""; ?>">
                                    <i class='bx bx-help-circle banner-size' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Largura recomendada: 500px Altura recomendada: 200px"></i>
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center <?php echo ($banner['location'] == "shelf-banner") ? "selecionado" : ""; ?>">
                                    <p class="small fw-semibold">BANNER PRATELEIRA</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-grid">
                                <div class="shelf-banner card p-4 bg-body-secondary text-center <?php echo ($banner['location'] == "shelf-banner") ? "selecionado" : ""; ?>">
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
            <label for="file-input" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="file-input" class="image-preview-container">
                <?php
                    // Consulta SQL para selecionar todas as imagens com base no ID
                    $sql = "SELECT * FROM tb_banner_img WHERE banner_id = :banner_id";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':banner_id', $banner['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagem = $stmt->fetch(PDO::FETCH_ASSOC);

                    $image_id = $imagem['id'];
                ?>
                <img src="<?php echo INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name']; ?>" alt="Image Preview" class="image-preview" id="image-preview" <?php echo (!empty($imagem['image_name'])) ? "style='display: block;'" : ""; ?>>
                <div class="center-text" <?php echo (!empty($imagem['image_name'])) ? "style='display: none;'" : ""; ?>>
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Faça upload das imagens aqui</p>
                </div>
            </label>
            <input type="file" name="image" accept="image/*" class="file-input" id="file-input">
            <p class="small text-end">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="image_id" value="<?php echo $image_id; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 99999; display: none;">
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
    const imagePreview = document.getElementById("image-preview");
    const fileInput = document.getElementById("file-input");
    
    fileInput.addEventListener("change", function () {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = "block";
                document.querySelector(".center-text").style.display = "none";
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = "";
            imagePreview.style.display = "none";
            document.querySelector(".center-text").style.display = "block";
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
<?php

    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um produto!";
}
?>