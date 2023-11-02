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
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label for="name" class="form-label small">Descrição *</label>
                    <small id="descriptionCounter" class="form-text text-muted">0 de 4000 caracteres</small>
                </div>
                <textarea class="form-control" name="description" id="description" maxlength="4000" rows="3"></textarea>
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