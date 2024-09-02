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
<!-- SEO Preview -->
<style>
    .seo-preview
    {
        border: 2px dashed #ddd;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>artigos" class="fs-5 text-decoration-none text-reset">Artigos</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Artigo</li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-artigo" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_article.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Artigo</div>
        <div class="card-body px-5 py-3">
            <div class="row">
                <div class="col-md-6 d-flex justify-content-between mb-3">
                    <div>
                        <label for="activeArticle" class="form-label small">Artigo ativo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="activeArticle" value="1" checked>
                            <label class="form-check-label" id="activeCheckbox" for="activeArticle">Sim</label>
                        </div>
                    </div>
                    <div>
                        <label for="emphasisArticle" class="form-label small">Destacar artigo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasisArticle" value="1">
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasisArticle">Não</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label small">Nome do artigo *</label>
                <input type="text" class="form-control" name="name" id="name" maxlength="120" aria-describedby="nameHelp" require>
                <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/blog/<span class="fw-semibold" id="linkPreview">...</span></p>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Conteúdo do artigo</div>
        <div class="card-body px-5 py-3">
            <div class="mb-3">
                <label for="editor" class="form-label small">Conteúdo do artigo</label>
                <textarea name="content" id="editor"></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Upload da imagem
            <label for="file-input" class="small" style="cursor: pointer;">Selecionar imagem</label>
        </div>
        <div class="card-body px-5 py-3">
            <label for="file-input" class="image-preview-container mt-3">
                <img src="" alt="Image Preview" class="image-preview" id="image-preview">
                <div class="center-text" style="padding: 1em 0;">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Faça upload das imagens aqui</p>
                </div>
            </label>
            <input type="file" name="image" accept="image/*" class="file-input" id="file-input">
            <p class="small text-end mt-3">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>
    
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">Google / SEO</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput1" class="form-label small">Nome da página *</label>
                            <small id="textCounter1" class="form-text text-muted">0 de 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp">
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

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="image_id" value="<?php echo $image_id; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>artigos" class="text-decoration-none text-reset">Cancelar</a>
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

    const activeArticle = document.getElementById("activeArticle");
    const activeCheckbox = document.getElementById("activeCheckbox");
    updateCheckboxText(activeArticle, activeCheckbox, "Sim", "Não");
    
    const emphasisArticle = document.getElementById("emphasisArticle");
    const emphasisCheckbox = document.getElementById("emphasisCheckbox");
    updateCheckboxText(emphasisArticle, emphasisCheckbox, "Sim", "Não");
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

<!-- Editor de texto -->
<script>
    tinymce.init({
        selector: '#editor',
        plugins: 'link image lists',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
        width: '100%',
        height: 300,
        menubar: false,
        images_upload_url: '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/upload_image.php',
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/upload_image.php');

            xhr.onload = function() {
                var json;

                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }

                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                success(json.location);
            };

            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('shop_id', '<?php echo $shop_id; ?>'); // Passando o id da loja
            xhr.send(formData);
        },
        setup: function (editor) {
            editor.on('RemoveNode', function (e) {
                if (e.node.nodeName === 'IMG') {
                    var src = e.node.src;
                    deleteImage(src);
                }
            });
        }
    });

    function deleteImage(src) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/delete_image.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    console.log('Imagem deletada com sucesso');
                } else {
                    console.error('Erro ao deletar imagem: ' + response.message);
                }
            }
        };
        xhr.send('src=' + encodeURIComponent(src));
    }
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