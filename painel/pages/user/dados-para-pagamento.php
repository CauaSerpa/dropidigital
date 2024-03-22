<?php
    // Nome da tabela para a busca
    $tabela = 'tb_invoice_info';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $id);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>planos" class="fs-5 text-decoration-none text-reset">Financeiro</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Dados para pagamento</li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/dados-para-pagamento" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_invoice_info.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações gerais</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label small">Nome do responsável *</label>
                    <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label small">E-mail do responsável *</label>
                    <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelp" value="<?php echo $user['email']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label small">Telefone de contato *</label>
                    <input type="text" class="form-control" name="phone" id="phone" aria-describedby="phoneHelp" placeholder="(00) 0000-0000" value="<?php echo $user['phone']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="shopType" class="form-label small">Tipo de cadastro da loja *</label>
                    <div class="input-group">
                        <select class="form-select" name="shopType" id="shopType" aria-label="Default select example">
                            <option value="pf">Pessoa Física</option>
                            <option value="pj">Pessoa Jurídica</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="pf" style="display: flex;">
                <div class="col-md-6 mb-3">
                    <label for="cpf" class="form-label small">CPF *</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpfHelp" placeholder="000.000.000-00" value="<?php echo $user['docNumber']; ?>">
                </div>
            </div>
            <div class="row" id="pj" style="display: none;">
                <div class="col-md-6 mb-3">
                    <label for="razaoSocial" class="form-label small">Razão Social *</label>
                    <input type="text" class="form-control" name="razaoSocial" id="razaoSocial" aria-describedby="razaoSocialHelp">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cnpj" class="form-label small">CNPJ *</label>
                    <input type="text" class="form-control" name="cnpj" id="cnpj" aria-describedby="cnpjHelp" placeholder="00.000.000/0000-00">
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Endereço da Loja</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="cep" class="form-label small">CEP *</label>
                    <input type="text" class="form-control" name="cep" id="cep" aria-describedby="cepHelp" value="<?php echo $user['cep']; ?>" onblur="getCepData()" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="endereco" class="form-label small">
                        Endereço *
                        <i class='bx bx-help-circle' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Os dados serão mostrados no site devido ao Decreto Federal 7962/13"></i>
                    </label>
                    <input type="text" class="form-control" name="endereco" id="endereco" aria-describedby="enderecoHelp" value="<?php echo $user['endereco']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="numero" class="form-label small">Número *</label>
                    <input type="text" class="form-control" name="numero" id="numero" aria-describedby="numeroHelp" value="<?php echo $user['numero']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="complemento" class="form-label small">Complemento (opcional)</label>
                    <input type="text" class="form-control" name="complemento" id="complemento" aria-describedby="complementoHelp" value="<?php echo $user['complemento']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="bairro" class="form-label small">Bairro *</label>
                    <input type="text" class="form-control" name="bairro" id="bairro" aria-describedby="bairroHelp" value="<?php echo $user['bairro']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cidade" class="form-label small">Cidade *</label>
                    <input type="text" class="form-control" name="cidade" id="cidade" aria-describedby="cidadeHelp" value="<?php echo $user['cidade']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label small">Estado *</label>
                    <input type="text" class="form-control" name="estado" id="estado" aria-describedby="estadoHelp" value="<?php echo $user['estado']; ?>" required>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">

    <!-- Botao salvar -->
    <div class="container-save-button save fw-semibold bg-transparent d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dados-para-pagemento" class="text-decoration-none text-reset">Cancelar</a>
        <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
    </div>

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>dados-para-pagemento" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<script>
    $(document).ready(function() {
        $('#shopType').change(function() {
            if ($(this).val() === 'pf') {
                //Se for pf
                //Mostra pf
                $('#pf').css('display', 'flex');
                //Oculta pj
                $('#pj').css('display', 'none');
                //Adiciona required no input
                $('#cpf').prop('required', true);
                $('#razaoSocial, #cnpj').prop('required', false);
            } else {
                //Se for pj
                //Mostra pj
                $('#pj').css('display', 'flex');
                //Oculta pf
                $('#pf').css('display', 'none');
                //Adiciona required no input
                $('#razaoSocial, #cnpj').prop('required', true);
                $('#cpf').prop('required', false);
            }
        });
    });
</script>

<!-- VIACEP -->
<script>
    function getCepData()
    {
        let cep = $('#cep').val();
        cep = cep.replace(/\D/g, "");
        if(cep.length<8)
        {
            $("#div-errors-price").html("CEP deve conter no mínimo 8 dígitos").slideDown('fast').effect( "shake" );
            $("#cep").addClass('is-invalid').focus();
            return;
        }
        $("#cep").removeClass('is-invalid');
        $("#div-errors-price").slideUp('fast');


        if(cep != "")
        {
            $("#endereco").val("Carregando...");
            $("#bairro").val("Carregando...");
            $("#cidade").val("Carregando...");
            $("#estado").val("...");
            $.getJSON( "https://viacep.com.br/ws/"+cep+"/json/", function( data )
            {
                $("#endereco").val(data.logradouro);
                $("#bairro").val(data.bairro);
                $("#cidade").val(data.localidade);
                $("#estado").val(data.uf);
                $("#numero").focus();
            }).fail(function()
            {
                $("#endereco").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#estado").val("");
            });
        }
    }
</script>

<script>
    //Phone Mask
    new Cleave('#phone', {
        delimiters: ['(', ')', ' ', '-'],
        blocks: [0, 2, 0, 5, 4],
        numericOnly: true
    });
</script>
<script>
    //CPF Mask
    new Cleave('#cpf', {
        delimiters: ['.', '.', '-'],
        blocks: [3, 3, 3, 2],
        numericOnly: true
    });
</script>
<script>
    //CPF Mask
    new Cleave('#cnpj', {
        delimiters: ['.', '.', '/', '-'],
        blocks: [2, 3, 3, 4, 2],
        numericOnly: true
    });
</script>
<script>
    //CEP Mask
    new Cleave('#cep', {
        delimiters: ['-'],
        blocks: [5, 3],
        numericOnly: true
    });
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

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

    const activeMaps = document.getElementById("activeMaps");
    const textMaps = document.getElementById("textMaps");
    updateCheckboxText(activeMaps, textMaps, "Sim", "Não");
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