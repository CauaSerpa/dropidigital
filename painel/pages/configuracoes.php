<?php
    // Nome da tabela para a busca
    $tabela = 'tb_users';

    $sql = "SELECT (name) FROM $tabela WHERE id = :id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Recuperar os resultados
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nome da tabela para a busca
    $tabela = 'tb_shop';

    $sql = "SELECT * FROM $tabela WHERE user_id = :user_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':user_id', $id);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nome da tabela para a busca
    $tabela = 'tb_address';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop['id']);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="page__header center">
    <div class="header__title">
        <h2 class="title">Dados da Loja</h2>
    </div>
</div>

<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_settings.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
        <div class="card-body row px-4 py-3">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label small">Nome da sua loja *</label>
                    <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="<?php echo $shop['name']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label small">Nome da loja no &lt;title&gt;</label>
                    <input type="text" class="form-control" name="title" id="title" aria-describedby="titleHelp" value="<?php echo $shop['title']; ?>">
                    <small>Será mostrado na aba do seu navegador e na página do Google.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label small">Descrição da página</label>
                    <textarea class="form-control" name="description" id="description" maxlength="160" rows="3"><?php echo $shop['description']; ?></textarea>
                    <small>Preencha o campo com uma breve descrição sobre sua loja. Esta informação ficará disponível na página principal e para o Google.</small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="shopType" class="form-label small">Tipo de cadastro da loja *</label>
                    <div class="input-group">
                        <select class="form-select" name="shopType" id="shopType" aria-label="Default select example">
                            <option value="pf">Pessoa Física</option>
                            <option value="pj">Pessoa Jurídica</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="responsible" class="form-label small">Nome do responsável *</label>
                    <input type="text" class="form-control" name="responsible" id="responsible" aria-describedby="responsibleHelp" value="<?php echo $user['name']; ?>" required>
                </div>
            </div>
            <div class="row" id="pf" style="display: flex;">
                <div class="col-md-6 mb-3">
                    <label for="cpf" class="form-label small">CPF *</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpfHelp" placeholder="000.000.000-00">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label small">Telefone de contato *</label>
                    <input type="text" class="form-control" name="phone" id="phone" aria-describedby="phoneHelp" placeholder="(00) 0000-0000" value="<?php echo $shop['phone']; ?>" required>
                </div>
            </div>
            <div class="row" id="pj" style="display: none;">
                <div class="col-md-4 mb-3">
                    <label for="razaoSocial" class="form-label small">Razão Social *</label>
                    <input type="text" class="form-control" name="razaoSocial" id="razaoSocial" aria-describedby="razaoSocialHelp">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cnpj" class="form-label small">CNPJ *</label>
                    <input type="text" class="form-control" name="cnpj" id="cnpj" aria-describedby="cnpjHelp" placeholder="00.000.000/0000-00">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="phone" class="form-label small">Telefone de contato *</label>
                    <input type="text" class="form-control" name="phone" id="phone" aria-describedby="phoneHelp" placeholder="(00) 0000-0000" value="<?php echo $shop['phone']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="segment" class="form-label small">Segmento *</label>
                    <div class="input-group">
                        <select name="segment" id="segment" class="form-select">
                            <option value="0" <?php echo ($shop['segment'] == 0) ? "selected" : ""; ?>>Dropshipping Infoproduto</option>
                            <option value="1" <?php echo ($shop['segment'] == 1) ? "selected" : ""; ?>>Dropshipping produto físico</option>
                            <option value="2" <?php echo ($shop['segment'] == 2) ? "selected" : ""; ?>>Site divulgação de serviços</option>
                            <option value="3" <?php echo ($shop['segment'] == 3) ? "selected" : ""; ?>>Site comércio físico</option>
                            <option value="4" <?php echo ($shop['segment'] == 4) ? "selected" : ""; ?>>Site para agendamento</option>
                        </select>
                    </div>
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
                    <input type="text" class="form-control" name="cep" id="cep" aria-describedby="cepHelp" value="<?php echo $address['cep']; ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="endereco" class="form-label small">
                        Endereço *
                        <i class='bx bx-help-circle' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Os dados serão mostrados no site devido ao Decreto Federal 7962/13"></i>
                    </label>
                    <input type="text" class="form-control" name="endereco" id="endereco" aria-describedby="enderecoHelp" value="<?php echo $address['endereco']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="numero" class="form-label small">Número *</label>
                    <input type="text" class="form-control" name="numero" id="numero" aria-describedby="numeroHelp" value="<?php echo $address['numero']; ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="complemento" class="form-label small">Complemento (opcional)</label>
                    <input type="text" class="form-control" name="complemento" id="complemento" aria-describedby="complementoHelp" value="<?php echo $address['complemento']; ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="bairro" class="form-label small">Bairro *</label>
                    <input type="text" class="form-control" name="bairro" id="bairro" aria-describedby="bairroHelp" value="<?php echo $address['bairro']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cidade" class="form-label small">Cidade *</label>
                    <input type="text" class="form-control" name="cidade" id="cidade" aria-describedby="cidadeHelp" value="<?php echo $address['cidade']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="estado" class="form-label small">Estado *</label>
                    <input type="text" class="form-control" name="estado" id="estado" aria-describedby="estadoHelp" value="<?php echo $address['estado']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="activeMaps" class="form-label small">Exibir este endereço no mapa?</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activeMaps" role="switch" id="activeMaps" value="1" <?php echo ($shop['map'] == 1) ? "checked" : ""; ?>>
                        <label class="form-check-label" id="textMaps" for="activeMaps"><?php echo ($shop['map'] == 1) ? "Sim" : "Não"; ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">

    <div class="save-button bg-white px-6 py-3 align-item-right" id="saveButton" style="position: fixed;width: calc(100% - 78px);left: 78px;bottom: 0px;z-index: 99999; display: none;">
        <div class="container-save-button container fw-semibold bg-transparent d-flex align-items-center justify-content-between">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>configuracoes" class="text-decoration-none text-reset">Cancelar</a>
            <button type="submit" name="SendAddProduct" class="btn btn-success fw-semibold px-4 py-2 small">Salvar</button>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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