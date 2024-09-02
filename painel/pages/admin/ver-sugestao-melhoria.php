<?php
$improvement_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!empty($improvement_id)) {
    // Tabela que será feita a consulta
    $tabela = "tb_improvement";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $improvement_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $improvement = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($improvement) {
        // Recuperar nome do autor
        $sql = "SELECT name FROM tb_users WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $improvement['author']);
        $stmt->execute();
        $improvement['author'] = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
        
        // Tabela que será feita a consulta
        $tabela = 'tb_improvement_likes';

        // Recuperar nome do autor
        $sql = "SELECT COUNT(*) AS total_likes FROM $tabela WHERE improvement_id = :improvement_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':improvement_id', $improvement['id']);
        $stmt->execute();
        $improvement['total_likes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_likes'];

        $improvement['date_create'] = date("d/m/Y", strtotime($improvement['date_create']));
?>
<!-- Código do site -->
<style>
    .image-container {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .improvement-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 1px solid #c4c4c4;
        border-radius: .5rem;
        cursor: pointer; /* Adiciona cursor pointer para indicar clicável */
    }

    /* Estilo para o ícone de full size */
    .full-size-icon {
        position: absolute;
        display: none;
        top: 10px;
        right: 10px;
        width: 24px;
        height: 24px;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: .3s;
    }
    .image-container:hover .full-size-icon {
        display: flex;
    }
</style>
<!-- SEO Preview -->
<style>
    .seo-preview {
        border: 2px dashed #ddd;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 pb-3 pt-4 border-0">
                <h5 class="modal-title fs-6">Enviar Melhoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" alt="Imagem Grande" id="modalImage">
            </div>
        </div>
    </div>
</div>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>sugestoes-melhorias" class="fs-5 text-decoration-none text-reset">Sugestões Melhorias</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page"><?= $improvement['title']; ?></li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-artigo" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1'></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card p-0">
            <div class="card-header fw-semibold px-4 py-3 bg-transparent">Sugestão de Melhoria</div>
            <div class="card-body px-5 py-3">
                <div class="row">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex flex-column justify-content-center">
                            <p class="fs-5 fw-semibold"><?= $improvement['title']; ?></p>
                            <p class="text-secondary mb-2"><?= $improvement['author']; ?></p>
                        </div>
                        <div class="d-flex flex-column justify-content-center text-end">
                            <p><?= $improvement['date_create']; ?></p>
                            <small class="fw-semibold">
                                <i class='bx bx-like'></i>
                                <?= $improvement['total_likes']; ?>
                            </small>
                        </div>
                    </div>
                    <p class="mb-3"><?= $improvement['description']; ?></p>

                    <div class="improvement-image-container d-flex w-100">
                        <?php
                            // Tabela que será feita a consulta
                            $tabela = "tb_improvement_img";

                            // Consulta SQL para selecionar todas as colunas com base no ID
                            $sql = "SELECT * FROM $tabela WHERE improvement_id = :improvement_id ORDER BY id ASC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':improvement_id', $improvement['id']);
                            $stmt->execute();

                            // Recuperar os resultados
                            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Loop através dos resultados e exibir todas as colunas
                            foreach ($images as $image) {
                                echo '<div class="image-container me-3 mb-3">';
                                echo '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/improvement/' . $improvement['id'] . '/' . $image['image_name'] . '" alt="Imagem" class="improvement-image" onclick="openModal(this.src)">';
                                echo '<span class="full-size-icon">&#x26F6;</span>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-0">
            <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ações</div>
            <div class="card-body px-5 py-3">
                <div class="row">
                    <div class="justify-content-between mb-3 <?= ($improvement['status'] == 1) ? "d-none" : "d-flex"; ?>">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Aprovar Sugestão</h6>
                            <small>Clique em aprovar para marcar sugestão como iniciada</small>
                        </div>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>aprovar-sugestao-melhoria?id=<?php echo $improvement['id']; ?>" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small">
                            Aprovar
                        </a>
                    </div>
                    <div class="justify-content-between mb-3 <?= ($improvement['status'] == 2) ? "d-none" : "d-flex"; ?>">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Reprovar Sugestão</h6>
                            <small>Clique em reprovar para cancelar a sugestão</small>
                        </div>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>reprovar-sugestao-melhoria?id=<?php echo $improvement['id']; ?>" class="btn btn-danger d-flex align-items-center fw-semibold px-4 py-2 small">
                            Reprovar
                        </a>
                    </div>
                    <div class="justify-content-between mb-3 <?= ($improvement['finished'] == 1) ? "d-none" : "d-flex"; ?>">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Concluído Sugestão</h6>
                            <small>Clique em concluído para marcar sugestão como iniciada</small>
                        </div>
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>concluir-sugestao-melhoria?id=<?php echo $improvement['id']; ?>" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small">
                            Concluído
                        </a>
                    </div>
                    <!-- <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Enviar uma Mensagem</h6>
                            <small>Clique em mensagem para enviar uma mensagem para o autor</small>
                        </div>
                        <button class="btn btn-secondary d-flex align-items-center fw-semibold px-4 py-2 small" data-bs-toggle="modal" data-bs-target="#approve">
                            Mensagem
                        </button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Full size -->
<script>
    $(document).ready(function() {
        $('.full-size-icon').click(function() {
            // Encontra a imagem dentro do mesmo contêiner
            var src = $(this).siblings('img').attr('src');
            // Define o src da imagem no modal
            $('#modalImage').attr('src', src);
            // Mostra o modal
            $('#imageModal').modal('show');
        });
    });
</script>

<!-- Checkbox -->
<script>
    function updateCheckboxText(checkbox, contentText, trueText, falseText) {
        checkbox.addEventListener("change", function () {
            const text = this.checked ? trueText : falseText;
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

<?php
    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um artigo!";
}
?>
