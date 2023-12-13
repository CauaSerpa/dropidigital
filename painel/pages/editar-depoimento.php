<?php
$shop_id = $id;

//Apagar Card
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if(!empty($id)){
    // Tabela que sera feita a consulta
    $tabela = "tb_depositions";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $deposition = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($deposition) {
?>
<style>
    .image-container
    {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    /* Image Preview */
    .person-image .image-preview
    {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: #ccc;
    }
</style>
<style>
    /* Estilos para as estrelas */
    .star {
        font-size: 24px;
        cursor: pointer;
        color: #ccc;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>depoimentos" class="fs-5 text-decoration-none text-reset">Depoimentos</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Depoimento</li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-depoimento" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_deposition.php" method="post" enctype="multipart/form-data">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-5 image-container">
                    <div class="person-image mb-3">
                        <img id="imagemPreview" class="image-preview object-fit-cover" src="<?php echo INCLUDE_PATH_DASHBOARD . "back-end/depositions/" . $deposition['img']; ?>" alt="Preview da imagem">
                    </div>
                    <label for="imagemInput" class="btn btn btn-outline-secondary d-flex align-items-center fw-semibold mb-1">
                        <i class='bx bx-image fs-5 me-2'></i>
                        Adicionar Foto
                    </label>
                    <small class="mb-1">
                        Imagem em .jpg com<br>
                        90 px x 90 px
                    </small>
                    <small>
                        <b>OBS:</b> Usar depoimentos reais!
                    </small>
                    <input type="file" name="img" id="imagemInput" accept="image/*" class="d-none">
                </div>
                <div class="col-md-7">
                    <div class="mb-3 d-flex align-items-center">
                        <div class="rating me-2">
                            <span class="star" data-rating="1">&#9733;</span>
                            <span class="star" data-rating="2">&#9733;</span>
                            <span class="star" data-rating="3">&#9733;</span>
                            <span class="star" data-rating="4">&#9733;</span>
                            <span class="star" data-rating="5">&#9733;</span>
                        </div>
                        <p class="fw-semibold" id="rating-value"><?php echo $deposition['qualification']; ?></p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="name" class="form-label small">Nome da pessoa *</label>
                        </div>
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="<?php echo $deposition['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="testimony" class="form-label small">Depoimento</label>
                            <small id="testimonyCounter" class="form-text text-muted">0 de 150 caracteres</small>
                        </div>
                        <textarea class="form-control" name="testimony" id="testimony" maxlength="150" rows="3"><?php echo $deposition['testimony']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="qualification" id="qualification" value="<?php echo $deposition['qualification']; ?>">
    <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 99999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>depoimentos" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>

</form>

<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Estrelas -->
<script>
    // Seleciona todas as estrelas
    const stars = document.querySelectorAll(".star");

    // Seleciona o elemento de exibição do valor da avaliação
    const ratingValue = document.getElementById("rating-value");

    // Input onde sera salvo o valor
    const qualification = document.getElementById("qualification");

    // Inicializa o valor da avaliação a partir do PHP
    let currentRating = parseInt(ratingValue.innerText);

    // Adiciona ouvintes de evento às estrelas
    stars.forEach((star) => {
        star.addEventListener("click", () => {
            // Obtém o valor da estrela clicada
            const rating = parseInt(star.getAttribute("data-rating"));

            // Define o valor da avaliação atual
            currentRating = rating;

            // Atualiza a exibição do valor da avaliação
            ratingValue.innerText = `${rating}`;

            // Adiciona o valor no input
            qualification.value = `${rating}`;

            // Define a cor das estrelas selecionadas
            stars.forEach((s) => {
                const starRating = parseInt(s.getAttribute("data-rating"));
                if (starRating <= rating) {
                    s.style.color = "#ffdd00";
                } else {
                    s.style.color = "#ccc";
                }
            });
        });
    });

    // Adiciona um ouvinte de evento para destacar as estrelas correspondentes ao valor da qualificação
    stars.forEach((star) => {
        const rating = parseInt(star.getAttribute("data-rating"));
        if (rating <= currentRating) {
            star.style.color = "#ffdd00";
        } else {
            star.style.color = "#ccc";
        }
    });

    // Adiciona um ouvinte de evento para redefinir a avaliação quando o mouse sai da área de avaliação
    document.querySelector(".rating").addEventListener("click", () => {
        // Redefine a cor das estrelas
        stars.forEach((s) => {
            const starRating = parseInt(s.getAttribute("data-rating"));
            if (starRating <= currentRating) {
                s.style.color = "#ffdd00";
            } else {
                s.style.color = "#ccc";
            }
        });
    });
</script>

<!-- Image Preview -->
<script>
    // Função para exibir a imagem selecionada como prévia
    function exibirImagemPreview() {
        const input = document.getElementById('imagemInput');
        const preview = document.getElementById('imagemPreview');

        input.addEventListener('change', function () {
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                preview.src = '../assets/images/services/service-next.jpg';
            }
        });
    }

    // Chame a função para ativar a visualização de imagem
    exibirImagemPreview();
</script>

<!-- Text Counter -->
<script>
    $(document).ready(function() {
        $('#testimony').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));
            $('#testimonyCounter').text(currentLength + ' de ' + maxLength + ' caracteres');
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
<?php

    } else {
        // ID não encontrado ou não existente
        echo "ID não encontrado.";
    }
} else {
    echo "É necessário selecionar um produto!";
}
?>