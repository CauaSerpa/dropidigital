<?php
$shop_id = $id;

//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_pages";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $page = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($page) {
?>
<!-- Codigo do site -->
<style>
    /* SEO Preview */
    .seo-preview
    {
        border: 2px dashed #ddd;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>paginas" class="fs-5 text-decoration-none text-reset">Páginas</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Página</li>
            </ol>
        </nav>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_page.php" method="post" enctype="multipart/form-data">

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Página</div>
        <div class="card-body px-5 py-3">
            <div class="mb-3">
                <label for="status" class="form-label small">Página ativa?</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="status" id="status" value="1" <?php echo ($page['status'] == 1) ? "checked" : ""; ?>>
                    <label class="form-check-label" id="activePage" for="status"><?php echo ($page['status'] == 1) ? "Sim" : "Não"; ?></label>
                </div>
            </div>
            <div class="mb-3">
                <label for="linkInput" class="form-label small">Nome da página *</label>
                <input type="text" class="form-control" name="name" id="linkInput" maxlength="120" aria-describedby="nameHelp" value="<?php echo $page['name']; ?>" required>
                <p class="small text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/pagina/<span class="fw-semibold" id="linkPreview"><?php echo $page['link']; ?></span></p>
            </div>
            <div class="mb-3">
                <label for="editor" class="form-label small">Conteúdo da Página</label>
                <textarea name="content" id="editor"><?php echo $page['content']; ?></textarea>
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
                            <label for="textInput1" class="form-label small">Nome da página *</label>
                            <small id="textCounter1" class="form-text text-muted">0 de 70 caracteres</small>
                        </div>
                        <input type="text" class="form-control" name="seo_name" id="textInput1" maxlength="70" aria-describedby="emailHelp" value="<?php echo $page['seo_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da página</label>
                        <input type="text" class="form-control" name="seo_link" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp" value="<?php echo $page['seo_link']; ?>">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da página</label>
                            <small id="textCounter3" class="form-text text-muted">0 de 160 caracteres</small>
                        </div>
                        <textarea class="form-control" name="seo_description" id="textInput3" maxlength="160" rows="3"><?php echo $page['seo_description']; ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="exampleInputEmail2" class="form-label small">Visualização</label>
                    <div class="seo-preview p-3 rounded-2">
                        <h5 class="mb-0" id="textPreview1"><?php echo ($page['seo_name'] !== "") ? $page['seo_name'] : "Título da categoria"; ?></h5>
                        <p class="text-decoration-none" style="color: #01C89B;">https://sua-loja.dropidigital.com.br/<span class="fw-semibold" id="textPreview2"><?php echo ($page['seo_link'] !== "") ? $page['seo_link'] : "link-da-categoria"; ?></span></p>
                        <p class="small" id="textPreview3"><?php echo ($page['seo_description'] !== "") ? $page['seo_description'] : "Descrição da categoria"; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="link" id="link" value="">
    <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="text-decoration-none text-reset">Cancelar</a>
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

    const activePage = document.getElementById("activePage");
    const status = document.getElementById("status");
    updateCheckboxText(status, activePage, "Sim", "Não");
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

<!-- Link -->
<script>
    // Aguarde o documento estar pronto
    $(document).ready(function() {
        // Selecione o campo de entrada e o span
        var input = $("#linkInput");
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
</script>

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
        var seoName = "<?php echo $page['seo_name']; ?>";
        var seoDescription = "<?php echo $page['seo_description']; ?>";

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
<?php

    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um produto!";
}
?>