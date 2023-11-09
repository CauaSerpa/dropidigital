<?php
    // Nome da tabela para a busca
    $tabela = 'tb_shop';

    $sql = "SELECT top_highlight_bar, top_highlight_bar_location, top_highlight_bar_text, center_highlight_images FROM $tabela WHERE user_id = :user_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':user_id', $id);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nome da tabela para a busca
    $tabela = 'tb_newsletter';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    $countEmails = $stmt->rowCount();
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

<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Tarja</h2>
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/highlight.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Tarja Superior</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="top_highlight_bar" class="form-label small">Tarja ativa?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="top_highlight_bar" role="switch" id="top_highlight_bar" value="1" <?php echo ($shop['top_highlight_bar'] == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label" id="topHighlightBarText" for="top_highlight_bar"><?php echo ($shop['top_highlight_bar'] == 1) ? 'Sim' : 'Não'; ?></label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <p class="fw-semibold">Onde mostrar a tarja para o cliente?</p>
                    </div>
                    <div class="mb-2">
                        <input class="form-check-input me-2" type="radio" name="top_highlight_bar_location" id="location-1" value="1" aria-label="Radio button for following text input" <?php echo ($shop['top_highlight_bar_location'] == '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="location-1">Todas as páginas</label>
                    </div>
                    <div class="mb-2">
                        <input class="form-check-input me-2" type="radio" name="top_highlight_bar_location" id="location-2" value="2" aria-label="Radio button for following text input" <?php echo ($shop['top_highlight_bar_location'] == '2') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="location-2">Ao acessar uma categoria ou produto</label>
                    </div>
                    <div class="mb-2">
                        <input class="form-check-input me-2" type="radio" name="top_highlight_bar_location" id="location-3" value="3" aria-label="Radio button for following text input" <?php echo ($shop['top_highlight_bar_location'] == '3') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="location-3">Ao acessar o carrinho ou checkout</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-2">
                        <label for="top_highlight_bar_text" class="form-label small">Texto</label>
                        <textarea class="form-control" name="top_highlight_bar_text" id="top_highlight_bar_text" maxlength="128" rows="3"><?php echo $shop['top_highlight_bar_text']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Tarja Central</div>
        <div class="card-body px-4 py-3">
            <div class="mb-3">
                <p>Selecione no máximo 3 imagens para compor a tarja central da sua loja</p>
            </div>
            <div class="row g-3">
                <?php
                    // Valores possíveis para os checkboxes
                    $valoresPossiveis = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];

                    // Valores do banco de dados (simulando)
                    $valoresDoBanco = $shop['center_highlight_images'];

                    // Convertendo a string do banco de dados em um array
                    $valoresArray = explode(", ", $valoresDoBanco);

                    // Array de URLs de imagens correspondentes aos valores
                    $imagens = [
                        "1" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "2" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "3" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "4" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "5" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "6" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "7" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png",
                        "8" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_02.png",
                        "9" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_03.png",
                        "10" => "https://cdn.awsli.com.br/2544/2544943/arquivos/Tarja_Pers_01.png"
                    ];

                    // Função para marcar os checkboxes
                    function marcarCheckbox1($valor, $valoresArray, $imagens) {
                        if (in_array($valor, $valoresArray)) {
                            echo "<div class='col-sm-4 position-relative'>";
                            echo "<input class='form-check-input itemCheckbox' type='checkbox' name='center_highlight[]' value='{$valor}' id='centerHighlight' style='position: absolute; top: 5px; left: 20px;' checked>";
                            echo "<img src='{$imagens[$valor]}' alt='Tarja {$valor}' class='w-100 border rounded-2'>";
                            echo "</div>";
                        } else {
                            echo "<div class='col-sm-4 position-relative'>";
                            echo "<input class='form-check-input itemCheckbox' type='checkbox' name='center_highlight[]' value='{$valor}' id='centerHighlight' style='position: absolute; top: 5px; left: 20px;'>";
                            echo "<img src='{$imagens[$valor]}' alt='Tarja {$valor}' class='w-100 border rounded-2'>";
                            echo "</div>";
                        }
                    }

                    // Criando os checkboxes e marcando aqueles cujos valores correspondem aos do banco de dados
                    foreach ($valoresPossiveis as $valor) {
                        marcarCheckbox1($valor, $valoresArray, $imagens);
                    }
                ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="display: none; position: fixed; width: 100%; left: 78px; bottom: 0;">
        <div class="container-save-button container card-header fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>feed-instagram" class="text-decoration-none text-reset">Cancelar</a>
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

<!-- Colar texto copiado -->
<script>
    document.getElementById("botaoColar").addEventListener("click", function () {
        // Verifique se a área de transferência (clipboard) é suportada pelo navegador
        if (navigator.clipboard) {
            navigator.clipboard.readText().then(function (text) {
                // Coloque o texto copiado no campo de entrada
                document.getElementById("token").value = text;
            });
        } else {
            // Fallback para navegadores que não suportam a área de transferência
            alert("A funcionalidade de área de transferência não é suportada neste navegador.");
        }
    });
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- Modal -->
<script>
    const myModal = document.getElementById('myModal')
    const myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', () => {
        myInput.focus()
    })
</script>

<!-- Ativo ou nao -->
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

    const top_highlight_bar = document.getElementById("top_highlight_bar");
    const topHighlightBarText = document.getElementById("topHighlightBarText");
    updateCheckboxText(top_highlight_bar, topHighlightBarText, "Sim", "Não");
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

        inputCounter('text', 'textCounter');
        inputCounter('successText', 'successTextCounter');
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

        $('button').on('click', function () {
            formChanged = true;
            $('#saveButton').show();
            $('.main.container').addClass('save-button-show');
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

<!-- Checkbox -->
<script>
    // Função para mostrar/ocultar a div com base no estado do checkbox
    function toggleDivVisibility(checkboxId, targetDivId) {
        var checkbox = document.getElementById(checkboxId);
        var targetDiv = document.getElementById(targetDivId);

        if (checkbox.checked) {
            // Se o checkbox estiver marcado, remova a classe "d-none" para mostrar a div
            targetDiv.classList.remove("d-none");
        } else {
            // Se o checkbox não estiver marcado, adicione a classe "d-none" para ocultar a div
            targetDiv.classList.add("d-none");
        }
    }

    // Adicione um ouvinte de evento a cada checkbox para chamar a função quando o estado mudar
    var checkbox1 = document.getElementById("modal-1");
    checkbox1.addEventListener("change", function() {
        toggleDivVisibility("modal-1", "targetDiv1");
    });

    var checkbox2 = document.getElementById("modal-2");
    checkbox2.addEventListener("change", function() {
        toggleDivVisibility("modal-2", "targetDiv2");
    });

    // Chame a função para verificar o estado inicial dos checkboxes quando a página é carregada
    document.addEventListener("DOMContentLoaded", function() {
        toggleDivVisibility("modal-1", "targetDiv1");
        toggleDivVisibility("modal-2", "targetDiv2");
    });
</script>

<!-- Max select checkbox -->
<script>
    var checkboxes = document.querySelectorAll(".itemCheckbox");
    var maxChecked = 3;

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener("change", function() {
            var checkedCheckboxes = document.querySelectorAll(".itemCheckbox:checked");

            if (checkedCheckboxes.length > maxChecked) {
                this.checked = false; // Desmarca o checkbox se exceder o limite
            }
        });
    });
</script>