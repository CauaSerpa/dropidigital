<?php
    // Nome da tabela para a busca
    $tabela = 'tb_shop';

    $sql = "SELECT newsletter_modal, newsletter_modal_title, newsletter_modal_text, newsletter_modal_success_text, newsletter_modal_time, newsletter_modal_time_seconds, newsletter_modal_location, newsletter_footer, newsletter_footer_text FROM $tabela WHERE id = :id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
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
                <h2 class="title">Newsletter</h2>
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

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/newsletter.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            E-mail Marketing
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/export-newsletter.php?id=<?php echo $id; ?>" class="small text-reset text-decoration-none align-middle" style="cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
                Exportar e-mails
            </a>
        </div>
        <div class="card-body row px-4 py-3">
            <p class="fw-semibold mb-3">
                Você tem <span style="color: var(--green-color);"><?php echo $countEmails; ?></span><?php echo ($countEmails == 0 || $countEmails == 1) ? ' e-mail disponível' : ' e-mails disponíveis'; ?> para exportação
            </p>
            <p class="small">
                Não sabe como mandar seus e-mails? Crie e envie suas campanhas de e-mail marketing sem precisar de conhecimentos técnicos.<br>
                <a href="#" style="color: var(--green-color) !important;" data-bs-toggle="modal" data-bs-target="#exampleModal">Ver aplicativos</a>
            </p>
        </div>
    </div>
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Captação de e-mails</div>
        <div class="card-body row px-4 py-3">
            <div class="mb-3">
                <div class="card p-3">
                    <div class="mb-3">
                        <input class="form-check-input itemCheckbox" type="checkbox" name="modal" value="1" id="modal-1" <?php echo ($shop['newsletter_modal'] == 1) ? 'checked' : ''; ?>>
                        <label for="modal-1">Ativar caixa de captação de e-mails no popup</label>
                    </div>
                    <div id="targetDiv1" class="d-none">
                        <div class="mb-3 ms-4">
                            <div class="mb-2">
                                <p class="fw-semibold">Quando mostrar a janela?</p>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-2" type="radio" name="modal_time" id="time" value="1" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_time'] == 1) ? 'checked' : ''; ?>>
                                    <p>Após</p>
                                    <select class="form-select form-select-sm mx-2" name="modal_time_seconds" id="timeSeconds" aria-label="Default select example" style="width: 75px;">
                                        <option value="5" <?php echo ($shop['newsletter_modal_time_seconds'] == 5) ? 'selected' : ''; ?>>5</option>
                                        <option value="10" <?php echo ($shop['newsletter_modal_time_seconds'] == 10) ? 'selected' : ''; ?>>10</option>
                                        <option value="20" <?php echo ($shop['newsletter_modal_time_seconds'] == 20) ? 'selected' : ''; ?>>20</option>
                                        <option value="30" <?php echo ($shop['newsletter_modal_time_seconds'] == 30) ? 'selected' : ''; ?>>30</option>
                                    </select>
                                    <p>segundos</p>
                                </div>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input me-2" type="radio" name="modal_time" id="time-1" value="2" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_time'] == 2) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="time-1">Imediatamente (não achamos isso legal)</label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input me-2" type="radio" name="modal_time" id="time-2" value="3" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_time'] == 3) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="time-2">Visitante tentar ir embora da loja (exit popup)</label>
                            </div>
                        </div>
                        <div class="mb-3 ms-4">
                            <div class="mb-2">
                                <p class="fw-semibold">Onde mostrar a janela para o cliente?</p>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input me-2" type="radio" name="modal_location" id="location-1" value="1" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_location'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="location-1">Qualquer página</label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input me-2" type="radio" name="modal_location" id="location-2" value="2" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_location'] == 2) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="location-2">Ao acessar uma categoria ou produto</label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input me-2" type="radio" name="modal_location" id="location-3" value="3" aria-label="Radio button for following text input" <?php echo ($shop['newsletter_modal_location'] == 3) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="location-3">Ao acessar o carrinho ou checkout</label>
                            </div>
                        </div>
                        <div class="ms-4">
                            <div class="mb-2">
                                <label for="modal_title" class="form-label small">Título</label>
                                <input type="text" class="form-control" name="modal_title" id="modal_title" value="<?php echo $shop['newsletter_modal_title']; ?>">
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <label for="modal_text" class="form-label small">Texto Capa</label>
                                    <small id="textCounter" class="form-text text-muted">0 de 128 caracteres</small>
                                </div>
                                <textarea class="form-control" name="modal_text" id="modal_text" maxlength="128" rows="3"><?php echo $shop['newsletter_modal_text']; ?></textarea>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <label for="successText" class="form-label small">Texto Sucesso</label>
                                    <small id="successTextCounter" class="form-text text-muted">0 de 128 caracteres</small>
                                </div>
                                <textarea class="form-control" name="modal_success_text" id="successText" maxlength="128" rows="3"><?php echo $shop['newsletter_modal_success_text']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="card p-3">
                    <div class="mb-3">
                        <input class="form-check-input itemCheckbox" type="checkbox" name="footer" value="1" id="modal-2" <?php echo ($shop['newsletter_footer'] == 1) ? 'checked' : ''; ?>>
                        <label for="modal-2">Ativar barra de captação de e-mails</label>
                    </div>
                    <div id="targetDiv2" class="d-none">
                        <div class="ms-4">
                            <div class="mb-2">
                                <label for="footer_text" class="form-label small">Texto</label>
                                <input type="text" class="form-control" name="footer_text" id="footer_text" value="<?php echo $shop['newsletter_footer_text']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Link para descadastramento
        </div>
        <div class="card-body row px-4 py-3">
            <p>
                Caso necessite, utilize o padrão do link abaixo para realizar o descadastramento dos e-mails:
                <p style="color: var(--green-color) !important;">https://minha-loja.dropidigital.com.br/newsletter/unsubscribe/<span class="fw-semibold">EMAIL_CLIENTE</span></p>
            </p>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>newsletter" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>newsletter" class="text-decoration-none text-reset">Cancelar</a>
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