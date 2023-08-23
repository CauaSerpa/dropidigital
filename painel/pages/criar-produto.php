<head>
    <!-- Link para o Dropzone CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.css">
    <!-- Link para o TinyMCE CSS -->
    <script src="https://cdn.tiny.cloud/1/xiqhvnpyyc1fqurimqcwiz49n6zap8glrv70bar36fbloiko/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>
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
    
    /* SEO Preview */
    .seo-preview
    {
        border: 2px dashed #ddd;
    }
</style>

<form id="myForm position-relative">

    <div class="page__header center">
        <div class="header__title">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-3">
                    <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>produtos" class="fs-5 text-decoration-none text-reset">Produtos</a></li>
                    <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Criar Produto</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label for="exampleInputEmail1" class="form-label small">Nome do produto *</label>
                    <small id="textCounter" class="form-text text-muted">0 / 120 caracteres</small>
                </div>
                <input type="email" class="form-control" id="exampleInputEmail1" maxlength="120" aria-describedby="emailHelp">
            </div>
            <!-- <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                <label class="form-check-label" for="flexSwitchCheckDefault">Default switch checkbox input</label>
            </div> -->
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Preços</div>
        <div class="card-body row px-4 py-3">
            <div class="col-md-4 mb-2">
                <label for="moneyInput1" class="form-label small">Preço de Custo</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control text-end" id="moneyInput1" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="moneyInput2" class="form-label small">Preço de Custo *</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control text-end" id="moneyInput2" placeholder="0,00">
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <label for="moneyInput3" class="form-label small">Preço promocional</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control text-end" id="moneyInput3" placeholder="0,00">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Preços</div>
        <div class="card-body px-5 py-3">
            <!-- Input File para seleção de imagem -->
            <input id="imageInput" type="file" accept="image/*" class="form-control">

            <!-- Dropzone para arrastar e soltar imagens -->
            <form action="#" class="dropzone" id="myDropzone"></form>

            <!-- Espaço para visualização das imagens -->
            <div id="imagePreviews"></div>
        </div>
        <div class="card-footer fw-semibold px-4 py-3 bg-transparent">
            <div class="d-flex justify-content-between">
                <label for="exampleInputEmail" class="form-label small">Vídeo do produto</label>
                <small class="form-text text-muted">Aceitamos apenas vídeos do YouTube</small>
            </div>
            <div class="position-relative">
                <i class='bx bxl-youtube input-icon' ></i>
                <input type="email" class="form-control icon-padding" id="exampleInputEmail" placeholder="https://www.youtube.com/watch?v=000" aria-label="https://www.youtube.com/watch?v=000" aria-describedby="emailHelp">
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Descrição do produto</div>
        <div class="card-body px-5 py-3">
            <label for="editor" class="form-label small">Descrição do produto</label>
            <textarea id="editor"></textarea>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">SKU</div>
        <div class="card-body px-5 py-3">
            <label for="skuResult" class="form-label small">
                Código SKU
                <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
            </label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="skuResult" placeholder="LEV-JN-SL-36-GN" aria-label="LEV-JN-SL-36-GN" aria-describedby="button-addon2" style="max-width: 250px;">
                <button class="btn btn-outline-dark fw-semibold px-4" type="button" id="button-addon2" onclick="generateSKU()">GERAR</button>
            </div>
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">
            Categorias
            <a href="#" class="text-decoration-none text-reset small">+ Cadastrar categoria</a>
        </div>
        <div class="card-body px-5 py-3">
            <label for="skuResult" class="form-label small">
                Código SKU
                <i class='bx bx-help-circle' data-toggle="tooltip" data-placement="top" title="Texto do Tooltip"></i>
            </label>
            <div class="input-group mb-3">
                <input type="text" class="form-control" id="skuResult" placeholder="Buscar categorias já cadastradas" aria-label="Buscar categorias já cadastradas" aria-describedby="button-addon2">
                <button class="btn btn-outline-dark fw-semibold px-4" type="button" id="button-addon2" onclick="generateSKU()">Ver Categorias</button>
            </div>
            <small class="d-flex mb-3 px-3 py-2" style="color: #4A90E2; background: #ECF3FC;">Nenhuma categoria adicionada</small>
            <!-- <div class="bd-callout bd-callout-info">
                The animation effect of this component is dependent on the <code>prefers-reduced-motion</code> media query. See the <a href="/docs/5.3/getting-started/accessibility/#reduced-motion">reduced motion section of our accessibility documentation</a>.
            </div> -->
        </div>
    </div>

    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent d-flex justify-content-between">Formato do checkout</div>
        <div class="card-body row px-4 py-3">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="moneyInput1" class="form-label small">Tipo do checkout *</label>
                    <div class="input-group">
                        <select class="form-select" aria-label="Default select example">
                            <option value="" selected disabled>Escolha o redirecionamento</option>
                            <option value="1">Link para WhatsApp</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="moneyInput1" class="form-label small">Botão *</label>
                    <div class="input-group">
                        <select class="form-select" aria-label="Default select example">
                            <option value="" selected disabled>Escolha o tipo do botão</option>
                            <option value="1">Saiba mais</option>
                            <option value="2">Chamar no WhatsApp</option>
                            <option value="3">Comprar</option>
                            <option value="4">Agenda</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="button-url" class="form-label small">URL de redirecionamento do botão *</label>
                <input type="email" class="form-control" id="button-url" placeholder="https://..." aria-label="https://..." aria-describedby="emailHelp">
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
                            <label for="textInput1" class="form-label small">Nome do produto *</label>
                            <small id="textCounter1" class="form-text text-muted">0 / 120 caracteres</small>
                        </div>
                        <input type="email" class="form-control" id="textInput1" maxlength="120" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <label for="textInput2" class="form-label small">Link da página</label>
                        <input type="email" class="form-control" id="textInput2" placeholder="link-da-pagina" aria-label="link-da-pagina" aria-describedby="emailHelp">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label for="textInput3" class="form-label small">Descrição da página</label>
                            <small id="textCounter3" class="form-text text-muted">0 / 160 caracteres</small>
                        </div>
                        <textarea class="form-control" id="textInput3" maxlength="160" rows="3"></textarea>
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

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed; width: 100%; left: 78px; bottom: 0; display: none; z-index: 99999;">
        <div class="container-save-button container card-header fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>produtos" class="text-decoration-none text-reset">Cancelar</a>
            <button href="#" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>

</form>

<!-- Link para o Bootstrap JS (junto com jQuery) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $('#exampleInputEmail1').on('input', function() {
            var currentText = $(this).val();
            var currentLength = currentText.length;
            var maxLength = parseInt($(this).attr('maxlength'));
            $('#textCounter').text(currentLength + ' de ' + maxLength + ' caracteres');
        });
    });
</script>

<script>
    function formatMoneyInput(input) {
        input.addEventListener("input", function () {
            const value = this.value.replace(/\D/g, ""); // Remove não números
            this.value = value.substring(0, value.length - 2) + "," + value.substring(value.length - 2);
        });
    }

    const moneyInput1 = document.getElementById("moneyInput1");
    formatMoneyInput(moneyInput1);

    const moneyInput2 = document.getElementById("moneyInput2");
    formatMoneyInput(moneyInput2);

    const moneyInput3 = document.getElementById("moneyInput3");
    formatMoneyInput(moneyInput3);
</script>

<!-- Link para o Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js"></script>
<script>
    // Inicializa o Dropzone
    Dropzone.options.myDropzone = {
        acceptedFiles: "image/*",
        init: function() {
            this.on("addedfile", function(file) {
                // Cria um elemento de div para a nova pré-visualização
                const div = document.createElement("div");
                div.classList.add("image-preview");

                // Cria um elemento de imagem para a nova pré-visualização
                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.classList.add("img-fluid");
                img.addEventListener("click", function() {
                    // Ao clicar na imagem, remove-a
                    div.parentNode.removeChild(div);
                });

                // Cria um botão de remoção
                const removeBtn = document.createElement("button");
                removeBtn.innerText = "Remove";
                removeBtn.classList.add("remove-image");
                removeBtn.addEventListener("click", function() {
                    div.parentNode.removeChild(div);
                });

                // Adiciona a imagem e o botão à div de pré-visualização
                div.appendChild(img);
                div.appendChild(removeBtn);

                // Adiciona a div à área de pré-visualização
                document.getElementById("imagePreviews").appendChild(div);
            });
        }
    };

    // Ao selecionar uma imagem usando o input file
    document.getElementById("imageInput").addEventListener("change", function() {
        const file = this.files[0];

        // Cria um elemento de div para a nova pré-visualização
        const div = document.createElement("div");
        div.classList.add("image-preview");

        // Cria um elemento de imagem para a nova pré-visualização
        const img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        img.classList.add("img-fluid");
        img.addEventListener("click", function() {
            // Ao clicar na imagem, remove-a
            div.parentNode.removeChild(div);
        });

        // Cria um botão de remoção
        const removeBtn = document.createElement("button");
        removeBtn.innerText = "Remove";
        removeBtn.classList.add("remove-image");
        removeBtn.addEventListener("click", function() {
            div.parentNode.removeChild(div);
        });

        // Adiciona a imagem e o botão à div de pré-visualização
        div.appendChild(img);
        div.appendChild(removeBtn);

        // Adiciona a div à área de pré-visualização
        document.getElementById("imagePreviews").appendChild(div);
    });
</script>

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

<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    $(document).ready(function(){
        var inputText = $('#textInput'); // Referência ao input text
        var textPreview = $('#textPreview'); // Referência ao elemento de preview

        inputText.on('input', function () {
            var newText = inputText.val(); // Obtém o valor do input text
            if (newText === '') {
                newText = 'Título da página'; // Texto padrão quando o input estiver vazio
            }
            textPreview.text(newText); // Atualiza o texto do preview
        });
    });
</script>
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
            // Aqui você pode adicionar a lógica para salvar os dados do formulário
            alert('Dados salvos!');
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