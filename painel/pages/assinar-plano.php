<?php
$shop_id = $id;

// Obtém o ID do parâmetro GET
$plan_id = isset($_GET['p']) ? intval($_GET['p']) : 0;

// Nome da tabela para a busca
$tabela = 'tb_users';

$sql = "SELECT name, email, cpf, phone FROM $tabela WHERE id = :id";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Recuperar os resultados
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Nome da tabela para a busca
$tabela = 'tb_address';

$sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $id);
$stmt->execute();

// Obter o resultado como um array associativo
$address = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o ID é válido (maior que zero)
if ($id > 0) {
    // Tabela que sera feita a consulta
    $tabela = "tb_plans_interval";

    // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
    $sql = "SELECT plan_id, billing_interval FROM $tabela WHERE id = :id";

    // Prepara a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Binde o parâmetro
    $stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);

    // Executa a consulta
    $stmt->execute();

    // Obtém os resultados
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($plan) {
        $id = $plan['plan_id'];
        $billing_interval = $plan['billing_interval'];
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_plans";

    // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
    $sql = "SELECT id, name, sub_name, resources FROM $tabela WHERE id = :id";

    // Prepara a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Binde o parâmetro
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Executa a consulta
    $stmt->execute();

    // Obtém os resultados
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($plan) {
?>
<style>
    .frequency,
    .type
    {
        cursor: pointer;
    }
    .frequency.active,
    .type.active
    {
        color: white;
        border-color: var(--dark-green-color);
        background: var(--green-color);
    }

    .btn.btn-success
    {
        background: var(--green-color);
        border: none;
    }
    .btn.btn-success:hover {
        background: var(--dark-green-color);
    }
</style>

<div class="modal fade" id="personInfo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_payment-data.php" method="post" id="seu_formulario_id">
                <div class="modal-header px-4 pb-3 pt-4 border-0">
                    <h1 class="modal-title fs-6" id="exampleModalLabel">Informações da fatura</h1>
                </div>
                <div class="modal-body px-4 pb-3 pt-0">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label small">E-mail do responsável *</label>
                            <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelp" value="<?php echo $user['email']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="responsible" class="form-label small">Nome do responsável *</label>
                            <input type="text" class="form-control" name="responsible" id="responsible" aria-describedby="responsibleHelp" value="<?php echo $user['name']; ?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cpf" class="form-label small">CPF *</label>
                            <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpfHelp" placeholder="000.000.000-00" value="<?php echo $user['cpf']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label small">Telefone de contato *</label>
                            <input type="text" class="form-control" name="phone" id="phone" aria-describedby="phoneHelp" placeholder="(00) 0000-0000" value="<?php echo $user['phone']; ?>">
                        </div>
                    </div>
                    <h6 class="fs-6 mb-3">Endereço</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cep" class="form-label small">CEP *</label>
                            <input type="text" class="form-control" name="cep" id="cep" aria-describedby="cepHelp" value="<?php echo $address['cep']; ?>">
                        </div>
                        <div class="col-md-8">
                            <label for="endereco" class="form-label small">
                                Endereço *
                                <i class='bx bx-help-circle' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Os dados serão mostrados no site devido ao Decreto Federal 7962/13"></i>
                            </label>
                            <input type="text" class="form-control" name="endereco" id="endereco" aria-describedby="enderecoHelp" value="<?php echo $address['endereco']; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="numero" class="form-label small">Número *</label>
                            <input type="text" class="form-control" name="numero" id="numero" aria-describedby="numeroHelp" value="<?php echo $address['numero']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="complemento" class="form-label small">Complemento (opcional)</label>
                            <input type="text" class="form-control" name="complemento" id="complemento" aria-describedby="complementoHelp" value="<?php echo $address['complemento']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="bairro" class="form-label small">Bairro *</label>
                            <input type="text" class="form-control" name="bairro" id="bairro" aria-describedby="bairroHelp" value="<?php echo $address['bairro']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="cidade" class="form-label small">Cidade *</label>
                            <input type="text" class="form-control" name="cidade" id="cidade" aria-describedby="cidadeHelp" value="<?php echo $address['cidade']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label small">Estado *</label>
                            <input type="text" class="form-control" name="estado" id="estado" aria-describedby="estadoHelp" value="<?php echo $address['estado']; ?>">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small">Salvar Dados</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="title text-reset text-decoration-none">Escolher plano</a></li>
            <li class="breadcrumb-item fw-semibold text-body-secondary active" aria-current="page">Informações de pagamento</li>
        </ol>
    </nav>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_product.php" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Escolha o período da sua assinatura Crescimento</div>
                <div class="card-body px-4 py-3">
                    <input type="radio" name="period" id="1" value="anual" class="d-none" <?php echo ($billing_interval == 'yearly') ? "checked" : ""; ?>>
                    <input type="radio" name="period" id="2" value="mensal" class="d-none" <?php echo ($billing_interval == 'monthly') ? "checked" : ""; ?>>
                    <label class="card frequency mb-3 <?php echo ($billing_interval == 'yearly') ? "active" : ""; ?>" for="1">
                        <div class="row">

                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_plans_interval";

                                // Consulta SQL
                                $sql = "SELECT price FROM $tabela WHERE plan_id = :id AND billing_interval = :billing_interval";

                                // Preparar a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Vincular o valor do parâmetro
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->bindValue(':billing_interval', 'yearly', PDO::PARAM_STR);

                                // Executar a consulta
                                $stmt->execute();

                                // Obter o resultado como um array associativo
                                $price = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Verificar se o resultado foi encontrado
                                if ($price) {
                            ?>

                            <p class="d-flex align-items-center col-md-4">Assinatura anual</p>
                            <div class="pricing col-md-8">
                                <h5 class="lh-1">R$ <?php echo $price['price']; ?> à vista</h5>
                                <p>ou em até 6x sem juros no cartão</p>
                            </div>

                            <?php
                                }
                            ?>

                        </div>
                    </label>
                    <label class="card frequency <?php echo ($billing_interval == 'monthly') ? "active" : ""; ?>" for="2">
                        <div class="row" style="height: 52px;">

                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_plans_interval";

                                // Consulta SQL
                                $sql = "SELECT price FROM $tabela WHERE plan_id = :id AND billing_interval = :billing_interval";

                                // Preparar a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Vincular o valor do parâmetro
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->bindValue(':billing_interval', 'monthly', PDO::PARAM_STR);

                                // Executar a consulta
                                $stmt->execute();

                                // Obter o resultado como um array associativo
                                $price = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Verificar se o resultado foi encontrado
                                if ($price) {
                            ?>

                            <p class="d-flex align-items-center col-md-4">Assinatura mensal</p>
                            <div class="pricing d-flex align-items-center col-md-8">
                                <h5 class="lh-1 mb-0">R$ <?php echo $price['price']; ?> por mês</h5>
                            </div>

                            <?php
                                }
                            ?>

                        </div>
                    </label>
                </div>
            </div>
            <div class="card mb-4 p-0">
                <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
                    Informações da fatura
                    <label for="upload-button" class="d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#personInfo" style="cursor: pointer;">
                        <i class='bx bx-pencil fs-5 me-1'></i>
                        Editar
                    </label>
                </div>
                <div class="card-body px-4 py-3">
                    <ul class="text-sm font-light text-inverted-2 pt-6 truncate">
                        <li><span><?php echo $user['name']; ?></span></li>
                        <li><span><?php echo $user['email']; ?></span></li>
                        <li>
                            <span class="mr-1">CPF: <?php echo $user['cpf']; ?></span>
                            <span class="ml-1">Tel: <?php echo $user['phone']; ?></span>
                        </li>
                        <li>
                            <span><?php echo $address['endereco']; ?>,</span>
                            <span><?php echo $address['numero']; ?></span>
                            <span>-</span>
                            <span><?php echo $address['bairro']; ?></span>
                        </li>
                        <?php
                            $complemento = $address['complemento'];
                            echo ($complemento != '') ? "<li><span>Complemento: $complemento</span></li>" : "";
                        ?>
                        <li><span><?php echo $address['cidade']; ?>/<?php echo $address['estado']; ?> - <?php echo $address['cep']; ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">Como deseja pagar a assinatura do seu plano?</div>
                <div class="card-body px-4 py-3">
                    <div class="row mb-3 g-3">
                        <input type="radio" name="type" id="creditCard" value="creditCard" class="d-none" checked>
                        <input type="radio" name="type" id="boleto" value="boleto" class="d-none">
                        <div class="col-md-8">
                            <label for="creditCard" class="card type d-flex align-items-center justify-content-center active">
                                <i class='bx bx-credit-card fs-3' ></i>
                                Cartão de crédito
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="boleto" class="card type d-flex align-items-center justify-content-center">
                                <i class='bx bx-barcode-reader fs-3' ></i>
                                Boleto bancário
                            </label>
                        </div>
                    </div>
                    
                    <div id="container_credit_card">
                        <div class="mb-3">
                            <label for="creditCardNumber" class="form-label small">Número do cartão *</label>
                            <input type="text" class="form-control" name="credit_card_number" id="creditCardNumber" aria-describedby="creditCardNumberHelp" placeholder="Ex. 0000 0000 0000 0000" required>
                        </div>
                        <div class="mb-3">
                            <label for="creditCardOwner" class="form-label small">Nome impresso no cartão *</label>
                            <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="Nome impresso no cartão" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="creditCardOwner" class="form-label small">Vencimento do cartão *</label>
                                <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="MM / AAAA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="creditCardOwner" class="form-label small">Código de segurança *</label>
                                <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="CVV" required>
                            </div>
                        </div>
                        <div class="mb-3 <?php echo ($billing_interval == 'monthly') ? "d-none" : ""; ?>" id="installmentContainer">
                            <label for="installment" class="form-label small">Parcelas</label>
                            <div class="input-group">
                                <select class="form-select" name="installment" id="installment" aria-label="Default select example" required>
                                    <option value="1" selected>À vista</option>
                                    <option value="2">2x de R$ 258,00 sem juros</option>
                                    <option value="3">3x de R$ 172,00 sem juros</option>
                                    <option value="4">4x de R$ 129,00 sem juros</option>
                                    <option value="5">5x de R$ 103,20 sem juros</option>
                                    <option value="6">6x de R$ 86,00 sem juros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success fw-semibold w-100 px-4 py-2 small mb-3" id="button_text">À vista</button>

                    <small class="lh-1" id="creditCartText">Autorizo o débito anual em meu cartão no valor do plano referente a 12 meses, garantindo a continuidade dos serviços.</small>

                    <small class="lh-1" id="boletoText" style="display: none;">A confirmação do pagamento da assinatura por boleto pode levar até 3 dias úteis. Os novos recursos e limites do plano só serão liberados após a aprovação. Pagamentos por cartão de crédito e Pix têm aprovação instantânea.</small>
                </div>
            </div>
        </div>
    </div>
</form>










<!-- Adicione este script na parte inferior da sua página HTML -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $('#seu_formulario_id').submit(function (event) {
            event.preventDefault();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Atualize as informações na página com os novos dados
                        updateUserInfo(response.data);
                    } else {
                        alert('Erro ao atualizar as informações.');
                    }
                },
                error: function () {
                    alert('Erro ao enviar a requisição.');
                }
            });
        });

        function updateUserInfo(data) {
            $('ul.text-sm span').eq(0).text(data.name);
            $('ul.text-sm span').eq(1).text(data.email);
            $('ul.text-sm span').eq(2).html('CPF: ' + data.cpf + '<span class="ml-1">Tel: ' + data.phone + '</span>');
            $('ul.text-sm span').eq(3).html(data.endereco + ', ' + data.numero + ' - ' + data.bairro);

            // Remove o complemento se existir
            $('ul.text-sm li:contains("Complemento:")').remove();

            // Verifica se há um complemento
            if (data.complemento) {
                $('ul.text-sm').append('<li><span>Complemento: ' + data.complemento + '</span></li>');
            }

            $('ul.text-sm span').eq(4).html(data.cidade + '/' + data.estado + ' - ' + data.cep);
        }
    });
</script>












<!-- Inclua a biblioteca jQuery em seu HTML -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<!-- Adiciona a class active no "label" referente ao "input:radio" -->
<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "period" for alterado
        $('input[type="radio"][name="period"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.frequency').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="period"]:checked').each(function() {
                $('label[for="' + $(this).attr('id') + '"]').addClass('active');
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "period" for alterado
        $('input[type="radio"][name="period"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.frequency').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="period"]:checked').each(function() {
                const $label = $('label[for="' + $(this).attr('id') + '"]');
                $label.addClass('active');
                
                // Mostra ou oculta o select e atualiza o texto do botão com base no valor do input radio
                if ($(this).val() === "anual") {
                    $('#installmentContainer').removeClass('d-none');
                    $('#button_text').text("À vista");
                } else if ($(this).val() === "mensal") {
                    $('#installmentContainer').addClass('d-none');
                    $('#button_text').text($label.find('h5').text());
                }
            });
        });
    });
</script>

<!-- Faz o toggle no metodo de pagamento -->
<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "type" for alterado
        $('input[type="radio"][name="type"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.type').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="type"]:checked').each(function() {
                $('label[for="' + $(this).attr('id') + '"]').addClass('active');
                
                // Mostra ou oculta o conteúdo com base na seleção do método de pagamento
                if ($(this).val() === "creditCard") {
                    $('#container_credit_card').show();
                    $('#creditCartText').show();
                    $('#boletoText').hide();
                } else if ($(this).val() === "boleto") {
                    $('#container_credit_card').hide();
                    $('#creditCartText').hide();
                    $('#boletoText').show();
                }
            });
        });
    });
</script>

<script>
    //Phone Mask
    new Cleave('#creditCardNumber', {
        delimiters: [' ', ' ', ' '],
        blocks: [4, 4, 4, 4],
        numericOnly: true
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