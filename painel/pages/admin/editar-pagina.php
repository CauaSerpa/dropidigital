<?php
//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_routes";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $route = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($route) {
?>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>paginas" class="fs-5 text-decoration-none text-reset">Páginas</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Editar Página</li>
            </ol>
        </nav>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/edit_page.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Editar Página</div>
        <div class="card-body px-5 py-3">
            <div class="row">
                <!-- Campo para editar o nome da página -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label small">Nome da página *</label>
                        <input type="text" class="form-control" name="name" id="name" maxlength="120" value="<?php echo $route['name']; ?>" required>
                    </div>
                </div>

                <!-- Campo para editar o título da página -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label small">Título da página *</label>
                        <input type="text" class="form-control" name="title" id="title" maxlength="120" value="<?php echo $route['title']; ?>">
                    </div>
                </div>

                <!-- Campo para editar a descrição da página -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label small">Descrição *</label>
                        <textarea class="form-control" name="description" id="description" rows="3" maxlength="160"><?php echo $route['description']; ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="video_location" class="form-label small">Posição do vídeo *</label>
                        <div class="input-group">
                            <select class="form-select" name="video_location" id="video_location" aria-label="Default select example" required>
                                <option value="" disabled>-- Selecione uma opção --</option>
                                <option value="disabled" <?php echo (!$route['video_location']) ? "selected" : ""; ?>>Desativado</option>
                                <option value="0" <?php echo ($route['video_location'] == 0) ? "selected" : ""; ?>>Topo da página</option>
                                <option value="1" <?php echo ($route['video_location'] == 1) ? "selected" : ""; ?>>Parte inferior da página</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Campo para editar o link do vídeo tutorial -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tutorial_video" class="form-label small">Link do vídeo tutorial (YouTube)</label>
                        <input type="url" class="form-control" name="tutorial_video" id="tutorial_video" value="<?php echo $route['tutorial_video']; ?>">
                    </div>
                </div>

                <!-- Campo para editar o link da página de ajuda -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="help_page" class="form-label small">Link da página de ajuda</label>
                        <input type="url" class="form-control" name="help_page" id="help_page" value="<?php echo $route['help_page']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $route['id']; ?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendUpdPage" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>paginas" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendUpdPage" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
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
    $(document).ready(function() {
        // Função para ativar/desativar o input baseado no select
        function toggleTutorialVideo() {
            var videoLocation = $('#video_location').val(); // Pega o valor do select
            var tutorialVideoInput = $('#tutorial_video'); // Campo de input

            if (videoLocation === "") {
                // Se não houver valor selecionado, desativa o input
                tutorialVideoInput.prop('disabled', true);
            } else {
                // Caso contrário, ativa o input
                tutorialVideoInput.prop('disabled', false);
            }
        }

        // Executa a função ao carregar a página
        toggleTutorialVideo();

        // Monitora as mudanças no select e aplica a lógica
        $('#video_location').on('change', function() {
            toggleTutorialVideo();
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
        $_SESSION['msgcad'] = 'Página não encontrada.';
        header("Location: ".INCLUDE_PATH_DASHBOARD."paginas");
    }
} else {
    $_SESSION['msgcad'] = 'É necessário selecionar uma página.';
    header("Location: ".INCLUDE_PATH_DASHBOARD."paginas");
}
?>