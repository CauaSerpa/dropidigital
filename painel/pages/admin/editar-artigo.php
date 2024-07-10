<?php
//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_blog";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($article) {
?>
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/edit_article.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Artigo</div>
        <div class="card-body px-5 py-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label small">Nome do artigo *</label>
                        <input type="text" class="form-control" name="name" id="name" maxlength="120" aria-describedby="nameHelp" value="<?php echo $article['name']; ?>" required>
                        <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/blog/<span class="fw-semibold" id="linkPreview"><?php echo $article['link']; ?></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="tag" class="form-label small">Tag do artigo *</label>
                        <input type="text" class="form-control" name="tag" id="tag" aria-describedby="tagHelp" value="<?php echo $article['tag']; ?>" required>
                    </div>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="description" class="form-label small">Descrição do artigo</label>
                    <textarea class="form-control" name="description" id="description" maxlength="160" rows="3" required><?php echo $article['description']; ?></textarea>
                </div>
                <div class="col-md-6 d-flex justify-content-between mb-3">
                    <div>
                        <label for="activeArticle" class="form-label small">Artigo ativo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="activeArticle" value="1" <?php echo ($article['status'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="activeCheckbox" for="activeArticle"><?php echo ($article['status'] == 1) ? "Sim" : "Não"; ?></label>
                        </div>
                    </div>
                    <div>
                        <label for="emphasisArticle" class="form-label small">Destacar artigo?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="emphasis" role="switch" id="emphasisArticle" value="1" <?php echo ($article['emphasis'] == 1) ? "checked" : ""; ?>>
                            <label class="form-check-label" id="emphasisCheckbox" for="emphasisArticle"><?php echo ($article['emphasis'] == 1) ? "Sim" : "Não"; ?></label>
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
            <label for="file-input" class="image-preview-container mt-3">
                <img src="<?php echo INCLUDE_PATH_DASHBOARD . 'back-end/admin/articles/' . $article['id'] . '/' . $article['image']; ?>" alt="Image Preview" class="image-preview" id="image-preview" <?php echo (!empty($article['image'])) ? "style='display: block;'" : ""; ?>>
                <div class="center-text <?php echo (!empty($article['image'])) ? "d-none" : ""; ?>" style="padding: 1em 0;">
                    <i class='bx bx-image fs-1'></i>
                    <p class="fs-5 fw-semibold">Faça upload das imagens aqui</p>
                </div>
            </label>
            <input type="file" name="image" accept="image/*" class="file-input" id="file-input">
            <p class="small text-end mt-3">Máximo de 1 imagem. Tamanho máximo 500KB. Para maior qualidade envie a imagem no formato JPG ou PNG.</p>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Conteúdo do artigo</div>
        <div class="card-body px-5 py-3">
            <label for="editor" class="form-label small">Conteúdo do artigo</label>
            <textarea name="content" id="editor"><?php echo $article['content']; ?></textarea>
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
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp" value="<?php echo $article['seo_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da página</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp" value="<?php echo $article['link']; ?>">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da página</label>
                            <small id="textCounter3" class="form-text text-muted">0 de 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"><?php echo $article['seo_description']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1"><?php echo ($article['seo_name'] == "") ? $article['seo_name'] : "Título da página"; ?></h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="textPreview2"><?php echo ($article['link'] == "") ? $article['link'] : "link-da-pagina"; ?></span></p>
                        <p class="small" id="textPreview3"><?php echo ($article['seo_description'] == "") ? $article['seo_description'] : "Descrição da página"; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

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
<script src="https://cdn.tiny.cloud/1/xiqhvnpyyc1fqurimqcwiz49n6zap8glrv70bar36fbloiko/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
            // Remove acentos usando normalize, substitui espaços por traço e remove barras
            return texto.normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')  // Remove acentos
                        .replace(/\s+/g, "-")            // Substitui espaços por traço
                        .replace(/\//g, "")              // Remove barras
                        .toLowerCase();
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
        menubar: false
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
        $('input, textarea, input[type="checkbox"]').each(function () {
            if ($(this).is('input[type="checkbox"]')) {
                originalFormValues[$(this).attr('id')] = $(this).is(':checked');
            } else {
                originalFormValues[$(this).attr('id')] = $(this).val();
            }
        });

        // Função para verificar alterações
function checkChanges() {
    formChanged = false;
    $('input, textarea, input[type="checkbox"]').each(function () {
        if ($(this).is('input[type="checkbox"]')) {
            if ($(this).is(':checked') !== originalFormValues[$(this).attr('id')]) {
                formChanged = true;
            }
        } else {
            if ($(this).val() !== originalFormValues[$(this).attr('id')]) {
                formChanged = true;
            }
        }
    });

    if (formChanged) {
        $('#saveButton').show();
        $('.main.container').addClass('save-button-show');
    } else {
        $('#saveButton').hide();
        $('.main.container').removeClass('save-button-show');
    }
}

        // Atualize a função de verificação quando um campo do formulário for modificado
        $('input, textarea, input[type="checkbox"]').on('input change', function () {
            checkChanges();
        });

        // Verifica quando os campos do formulário voltam ao valor original
        $('input, textarea, input[type="checkbox"]').on('input change', function () {
            checkChanges();
        });

        // Inicialize a verificação das alterações
        checkChanges();

        // Manipulador para o botão de salvar
        $('#saveButton button').click(function () {
            if (formChanged) {
                formChanged = false;
                $('#saveButton').hide();
                $('.main.container').removeClass('save-button-show');
                // Atualize os valores originais
                $('input, textarea, input[type="checkbox"]').each(function () {
                    if ($(this).is('input[type="checkbox"]')) {
                        originalFormValues[$(this).attr('id')] = $(this).is(':checked');
                    } else {
                        originalFormValues[$(this).attr('id')] = $(this).val();
                    }
                });
            }
        });
    });
</script>

<!-- Cor no article Selecionado -->
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
    echo "É necessário selecionar um artigo!";
}
?>