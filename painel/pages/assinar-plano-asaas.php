<?php
$shop_id = $id;
$shop_plan = $plan_id;

// Obtém o ID do parâmetro GET
$plan_id = isset($_GET['p']) ? intval($_GET['p']) : 0;

// Nome da tabela para a busca
$tabela = 'tb_invoice_info';

$sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $id);
$stmt->execute();

// Obter o resultado como um array associativo
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o ID é válido (maior que zero)
if ($id > 0) {
    // Tabela que sera feita a consulta
    $tabela = "tb_plans_interval";

    // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
    $sql = "SELECT id, plan_id, billing_interval FROM $tabela WHERE id = :id";

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
    .disabled
    {
        color: #a0a8b6;
        background: #f8f8f9;
        pointer-events: none;
    }

    .frequency,
    .type
    {
        cursor: pointer;
    }
    .frequency.active,
    .type.active
    {
        fill: white;
        color: white;
        border-color: var(--dark-green-color);
        background: var(--green-color);
        cursor: default;
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
                    <h6 class="modal-title fs-6" id="exampleModalLabel">Informações da fatura</h6>
                </div>
                <div class="modal-body px-4 pb-3 pt-0">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label small">E-mail do responsável *</label>
                            <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelp" value="<?php echo $user['email']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label small">Nome do responsável *</label>
                            <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="<?php echo $user['name']; ?>">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cpf" class="form-label small">CPF *</label>
                            <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpfHelp" placeholder="000.000.000-00" value="<?php echo $user['docNumber']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label small">Telefone de contato *</label>
                            <input type="text" class="form-control" name="mobilePhone" id="phone" aria-describedby="phoneHelp" placeholder="(00) 0000-0000" value="<?php echo $user['phone']; ?>">
                        </div>
                    </div>
                    <h6 class="fs-6 mb-3">Endereço</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cep" class="form-label small">CEP *</label>
                            <input type="text" class="form-control" name="postalCode" id="cep" aria-describedby="cepHelp" value="<?php echo $user['cep']; ?>" onblur="getCepData()">
                        </div>
                        <div class="col-md-8">
                            <label for="endereco" class="form-label small">
                                Endereço *
                                <i class='bx bx-help-circle' data-toggle="tooltip" data-bs-placement="top" data-bs-title="Os dados serão mostrados no site devido ao Decreto Federal 7962/13"></i>
                            </label>
                            <input type="text" class="form-control" name="endereco" id="endereco" aria-describedby="enderecoHelp" value="<?php echo $user['endereco']; ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="numero" class="form-label small">Número *</label>
                            <input type="text" class="form-control" name="numero" id="numero" aria-describedby="numeroHelp" value="<?php echo $user['numero']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="complemento" class="form-label small">Complemento (opcional)</label>
                            <input type="text" class="form-control" name="complemento" id="complemento" aria-describedby="complementoHelp" value="<?php echo $user['complemento']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="bairro" class="form-label small">Bairro *</label>
                            <input type="text" class="form-control" name="bairro" id="bairro" aria-describedby="bairroHelp" value="<?php echo $user['bairro']; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="cidade" class="form-label small">Cidade *</label>
                            <input type="text" class="form-control" name="cidade" id="cidade" aria-describedby="cidadeHelp" value="<?php echo $user['cidade']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label small">Estado *</label>
                            <input type="text" class="form-control" name="estado" id="estado" aria-describedby="estadoHelp" value="<?php echo $user['estado']; ?>">
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

<form id="myForm" class="position-relative" action="submit">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Escolha o período da sua assinatura Crescimento</div>
                <div class="card-body px-4 py-3">
                    <input type="radio" name="period" id="1" value="anual" class="d-none" <?php echo ($billing_interval == 'yearly') ? "checked" : ""; ?>>
                    <input type="radio" name="period" id="2" value="mensal" class="d-none" <?php echo ($billing_interval == 'monthly') ? "checked" : ""; ?>>
                    <label id="labelYearly" class="card frequency mb-3 <?php echo ($billing_interval == 'yearly') ? "active" : ""; ?>" for="1">
                        <div class="row">

                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_plans_interval";

                                // Consulta SQL
                                $sql = "SELECT id, mpago_id, price FROM $tabela WHERE plan_id = :id AND billing_interval = :billing_interval";

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
                                    $yearly_id = $price['id'];
                                    $mpago_id_yearly = $price['mpago_id'];
                                    $yearly_price = $price['price'];
                            ?>

                            <p class="d-flex align-items-center col-md-4">Assinatura anual</p>
                            <div class="pricing col-md-8">
                                <h5 class="lh-1">R$ <?php echo number_format($yearly_price, 2, ',', ''); ?> à vista</h5>
                                <p>ou em até 6x sem juros no cartão</p>
                            </div>

                            <?php
                                }
                            ?>

                        </div>
                    </label>
                    <label id="labelMonthly" class="card frequency <?php echo ($billing_interval == 'monthly') ? "active" : ""; ?>" for="2">
                        <div class="row" style="height: 52px;">

                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_plans_interval";

                                // Consulta SQL
                                $sql = "SELECT id, mpago_id, price FROM $tabela WHERE plan_id = :id AND billing_interval = :billing_interval";

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
                                    $monthly_id = $price['id'];
                                    $mpago_id_monthly = $price['mpago_id'];
                                    $monthly_price = $price['price'];
                            ?>

                            <p class="d-flex align-items-center col-md-4">Assinatura mensal</p>
                            <div class="pricing d-flex align-items-center col-md-8">
                                <h5 class="lh-1 mb-0">R$ <?php echo number_format($monthly_price, 2, ',', ''); ?> por mês</h5>
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
                        <!-- Adicione IDs aos spans para referenciá-los diretamente na função JavaScript -->
                        <li><span id="span-name"><?php echo $user['name']; ?></span></li>
                        <li><span id="span-email"><?php echo $user['email']; ?></span></li>
                        <li>
                            <span class="mr-1" id="span-cpf">CPF: <?php echo $user['docNumber']; ?></span>
                            <span class="ml-1" id="span-tel">Tel: <?php echo $user['phone']; ?></span>
                        </li>
                        <li>
                            <span id="span-address"><?php echo $user['endereco']; ?>,</span>
                            <span id="span-number"><?php echo $user['numero']; ?></span>
                            <span>-</span>
                            <span id="span-district"><?php echo $user['bairro']; ?></span>
                        </li>
                        <?php
                            $complemento = $user['complemento'];
                            echo ($complemento != '') ? "<li><span id='span-complement'>Complemento: $complemento</span></li>" : "";
                        ?>
                        <li><span id='span-more-info'><?php echo $user['cidade']; ?>/<?php echo $user['estado']; ?> - <?php echo $user['cep']; ?></span></li>
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
                        <input type="radio" name="type" id="pix" value="pix" class="d-none">
                        <div class="col-md-8">
                            <label for="creditCard" class="card type d-flex align-items-center justify-content-center active">
                                <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 0 576 512" id="creditCard"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M512 80c8.8 0 16 7.2 16 16v32H48V96c0-8.8 7.2-16 16-16H512zm16 144V416c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V224H528zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm56 304c-13.3 0-24 10.7-24 24s10.7 24 24 24h48c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm128 0c-13.3 0-24 10.7-24 24s10.7 24 24 24H360c13.3 0 24-10.7 24-24s-10.7-24-24-24H248z"/></svg>
                                Cartão de crédito
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="pix" class="card type d-flex align-items-center justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 0 512 512" id="pix"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M242.4 292.5C247.8 287.1 257.1 287.1 262.5 292.5L339.5 369.5C353.7 383.7 372.6 391.5 392.6 391.5H407.7L310.6 488.6C280.3 518.1 231.1 518.1 200.8 488.6L103.3 391.2H112.6C132.6 391.2 151.5 383.4 165.7 369.2L242.4 292.5zM262.5 218.9C256.1 224.4 247.9 224.5 242.4 218.9L165.7 142.2C151.5 127.1 132.6 120.2 112.6 120.2H103.3L200.7 22.76C231.1-7.586 280.3-7.586 310.6 22.76L407.8 119.9H392.6C372.6 119.9 353.7 127.7 339.5 141.9L262.5 218.9zM112.6 142.7C126.4 142.7 139.1 148.3 149.7 158.1L226.4 234.8C233.6 241.1 243 245.6 252.5 245.6C261.9 245.6 271.3 241.1 278.5 234.8L355.5 157.8C365.3 148.1 378.8 142.5 392.6 142.5H430.3L488.6 200.8C518.9 231.1 518.9 280.3 488.6 310.6L430.3 368.9H392.6C378.8 368.9 365.3 363.3 355.5 353.5L278.5 276.5C264.6 262.6 240.3 262.6 226.4 276.6L149.7 353.2C139.1 363 126.4 368.6 112.6 368.6H80.78L22.76 310.6C-7.586 280.3-7.586 231.1 22.76 200.8L80.78 142.7H112.6z"/></svg>
                                Pix
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
                                <label for="creditCardExpiration" class="form-label small">Vencimento do cartão *</label>
                                <input type="text" class="form-control" name="credit_card_expiration" id="creditCardExpiration" aria-describedby="creditCardExpirationHelp" placeholder="MM / AA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="creditCardCCV" class="form-label small">Código de segurança *</label>
                                <input type="text" class="form-control" name="credit_card_ccv" id="creditCardCCV" aria-describedby="creditCardCCVHelp" placeholder="CVV" required>
                            </div>
                        </div>
                        <div class="mb-3 <?php echo ($billing_interval == 'monthly') ? "d-none" : ""; ?>" id="installmentContainer">
                            <label for="installment" class="form-label small">Parcelas</label>
                            <div class="input-group">
                                <select class="form-select" name="installment" id="installment" aria-label="Default select example" required>
                                    <option value="1|<?php echo $yearly_price; ?>" selected>À vista</option>
                                    <?php for ($i = 2; $i <= 6; $i++): ?>
                                        <?php $installmentValue = round($yearly_price / $i, 2); ?>
                                        <option value="<?php echo $i . '|' . $installmentValue; ?>"> <?php echo $i; ?>x de R$ <?php echo number_format($installmentValue, 2, ',', ''); ?> sem juros</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

					<input type="hidden" name="value" id="value" value="<?php echo ($billing_interval == 'monthly') ? $monthly_price : $yearly_price; ?>">

                    <div class="user-data">
                        <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
                        <input type="hidden" name="name" value="<?php echo $user['name']; ?>">
                        <input type="hidden" name="docType" value="<?php echo $user['docType']; ?>">
                        <input type="hidden" name="docNumber" value="<?php echo $user['docNumber']; ?>">
                        <input type="hidden" name="mobilePhone" value="<?php echo $user['phone']; ?>">
    
                        <input type="hidden" name="postalCode" value="<?php echo $user['cep']; ?>">
                        <input type="hidden" name="address" value="<?php echo $user['endereco']; ?>">
                        <input type="hidden" name="addressNumber" value="<?php echo $user['numero']; ?>">
                        <input type="hidden" name="complement" value="<?php echo $user['complemento']; ?>">
                        <input type="hidden" name="province" value="<?php echo $user['bairro']; ?>">
                        <input type="hidden" name="city" value="<?php echo $user['cidade']; ?>">
                        <input type="hidden" name="state" value="<?php echo $user['estado']; ?>">
                    </div>

                    <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">

                    <input type="hidden" name="id_plan" value="<?php echo ($billing_interval == 'monthly') ? $mpago_id_monthly : $mpago_id_yearly; ?>">

                    <!-- Aqui está o seu botão. Eu adicionei um ID para poder referenciá-lo no script JavaScript -->
                    <button type="submit" class="btn btn-success fw-semibold w-100 px-4 py-2 small mb-3" id="submitButton">
                        <?php echo "Pagar 1x de R$ " . number_format($yearly_price, 2, ',', ''); ?>
                    </button>

                    <style>
                        #loaderButton {
                            display: flex;
                            justify-content: center;
                        }

                        .loader {
                            width: 24px;
                            height: 24px;
                            border: 2.5px solid #FFF;
                            border-bottom-color: transparent;
                            border-radius: 50%;
                            display: inline-block;
                            box-sizing: border-box;
                            animation: rotation 1s linear infinite;
                        }

                        @keyframes rotation {
                            0% {
                                transform: rotate(0deg);
                            }
                            100% {
                                transform: rotate(360deg);
                            }
                        }

                    </style>

                    <button class="btn btn-success fw-semibold w-100 px-4 py-2 small mb-3 d-none" id="loaderButton">
                        <div class="loader"></div>
                    </button>

                    <small class="lh-1" id="creditCartText">Autorizo o débito <span id="creditCartTextType"><?php echo ($billing_interval == 'yearly') ? "anual" : "mensal"; ?></span> em meu cartão no valor do plano referente a 12 meses, garantindo a continuidade dos serviços.</small>

                    <small class="lh-1" id="pixText" style="display: none;">Pagamentos por Pix têm aprovação instantânea.</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        var shopPlan = <?php echo json_encode($shop_plan); ?>;
        var monthlyId = <?php echo json_encode($monthly_id); ?>;
        var yearlyId = <?php echo json_encode($yearly_id); ?>;

        if (shopPlan === monthlyId) {
            // Se shop_plan é igual ao monthly_id
            $('#labelMonthly').addClass('disabled'); // Adiciona a classe disabled
            $('input[name="period"][value="mensal"]').prop('disabled', true); // Desabilita o input radio
        } else if (shopPlan === yearlyId) {
            // Se shop_plan é igual ao yearly_id
            $('#labelYearly').addClass('disabled'); // Adiciona a classe disabled
            $('input[name="period"][value="anual"]').prop('disabled', true); // Desabilita o input radio
        }
    });
</script>

<!-- Adicione este script JavaScript -->
<script>
    // Função para atualizar o texto do botão com base na opção selecionada
    function updateButtonText() {
        var selectedPaymentType = document.querySelector('input[name="type"]:checked').value;
        var selectedOption = document.querySelector('input[name="period"]:checked').value;

        if (selectedOption === "anual") {
            if (selectedPaymentType === "creditCard") {
                // Se a opção selecionada for "anual", chame a função do installment
                updateInstallmentText();
            } else if (selectedPaymentType === "pix") {
                // Lógica para a opção "PIX"
                var yearlyPrice = <?php echo $yearly_price; ?>;
                var formattedYearlyPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(yearlyPrice);
                document.getElementById('submitButton').innerText = 'Pagar de ' + formattedYearlyPrice;
            }
        } else if (selectedOption === "mensal") {
            // Lógica para a opção "mensal"
            var monthlyPrice = <?php echo $monthly_price; ?>;
            var formattedMonthlyPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(monthlyPrice);
            document.getElementById('submitButton').innerText = 'Pagar ' + formattedMonthlyPrice;
        }
    }

    // Função para atualizar o texto do botão com base no installment
    function updateInstallmentText() {
        var selectedOption = document.getElementById('installment').options[document.getElementById('installment').selectedIndex].value;
        var optionParts = selectedOption.split('|');
        var numberOfInstallments = optionParts[0];
        var installmentValue = optionParts[1];
        var formattedInstallmentValue = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(installmentValue);
        document.getElementById('submitButton').innerText = 'Pagar ' + numberOfInstallments + 'x de ' + formattedInstallmentValue;
    }

    // Adiciona um ouvinte de evento para atualizar o texto do botão quando a opção é alterada
    document.querySelectorAll('input[name="period"]').forEach(function (input) {
        input.addEventListener('change', updateButtonText);
    });

    // Adiciona um ouvinte de evento para o select installment
    document.getElementById('installment').addEventListener('change', updateInstallmentText);

    // Função para atualizar o texto do botão com base na opção selecionada
    function updateButtonTextPaymentType() {
        var selectedPaymentType = document.querySelector('input[name="type"]:checked').value;
        var selectedOption = document.querySelector('input[name="period"]:checked').value;

        if (selectedPaymentType === "creditCard" && selectedOption === "anual") {
            // Se a opção selecionada for "anual", chame a função do installment
            updateInstallmentText();
        } else if (selectedPaymentType === "pix" && selectedOption === "anual") {
            // Lógica para a opção "PIX"
            var yearlyPrice = <?php echo $yearly_price; ?>;
            var formattedYearlyPrice = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(yearlyPrice);
            document.getElementById('submitButton').innerText = 'Pagar de ' + formattedYearlyPrice;
        }
    }

    // Adiciona um ouvinte de evento para atualizar o texto do botão quando a opção de pagamento é alterada
    document.querySelectorAll('input[name="type"]').forEach(function (input) {
        input.addEventListener('change', updateButtonTextPaymentType);
    });

    // Atualiza o texto do botão inicialmente
    updateButtonText();
</script>

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

                        // Usando jQuery para lidar com o clique no botão para fechar o modal
                        $('#personInfo').modal('hide');
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
            // Atualize as informações diretamente com base nos IDs dos spans
            $('#span-name').text(data.name);
            $('#span-email').text(data.email);

            $('#span-cpf').text(`CPF: ${data.cpf}`);
            $('#span-tel').text(`Tel: ${data.phone}`);

            $('#span-address').text(`${data.endereco},`);
            $('#span-number').text(`${data.numero}`);
            $('#span-district').text(`${data.bairro}`);

            if (data.complemento !== "") {
                // Exibe o complemento se estiver preechido
                $('#span-complement').parent().removeClass('d-none');

                $('#span-complement').text(`Complemento: ${data.complemento}`);
            } else {
                // Oculta o complemento se nao estiver preechido
                $('#span-complement').parent().addClass('d-none');
            }

            $('#span-more-info').text(`${data.cidade}/${data.estado} - ${data.cep}`);

            // Passar os valores para os inputs
            $('input[name="email"]').val(`${data.email}`);
            $('input[name="name"]').val(`${data.name}`);
            $('input[name="docNumber"]').val(`${data.cpf}`);
            $('input[name="mobilePhone"]').val(`${data.phone}`);

            $('input[name="postalCode"]').val(`${data.cep}`);
            $('input[name="address"]').val(`${data.endereco}`);
            $('input[name="addressNumber"]').val(`${data.numero}`);
            $('input[name="complement"]').val(`${data.complemento}`);
            $('input[name="province"]').val(`${data.bairro}`);
            $('input[name="city"]').val(`${data.cidade}`);
            $('input[name="state"]').val(`${data.estado}`);
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
                    $('#creditCartTextType').text('anual');

                    $('input[name="id_plan"]').val('<?php echo $mpago_id_yearly; ?>');

                    $('input[name="value"]').val('<?php echo $yearly_price; ?>');
                } else if ($(this).val() === "mensal") {
                    $('#installmentContainer').addClass('d-none');
                    $('#creditCartTextType').text('mensal');

                    $('input[name="id_plan"]').val('<?php echo $mpago_id_monthly; ?>');

                    $('input[name="value"]').val('<?php echo $monthly_price; ?>');
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

                    // Adicionando required dos inputs
                    $('#creditCardNumber').prop('required', true);
                    $('#creditCardOwner').prop('required', true);
                    $('#creditCardExpiration').prop('required', true);
                    $('#creditCardCCV').prop('required', true);
                    $('#installment').prop('required', true);

                    $('#creditCartText').show();
                    $('#pixText').hide();
                } else if ($(this).val() === "pix") {
                    $('#container_credit_card').hide();

                    // Removendo required dos inputs
                    $('#creditCardNumber').prop('required', false);
                    $('#creditCardOwner').prop('required', false);
                    $('#creditCardExpiration').prop('required', false);
                    $('#creditCardCCV').prop('required', false);
                    $('#installment').prop('required', false);

                    $('#creditCartText').hide();
                    $('#pixText').show();
                }
            });
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
    //CEP Mask
    new Cleave('#cep', {
        delimiters: ['-'],
        blocks: [5, 3],
        numericOnly: true
    });
</script>
<script>
    //Card Number Mask
    new Cleave('#creditCardNumber', {
        delimiters: [' ', ' ', ' '],
        blocks: [4, 4, 4, 4],
        numericOnly: true
    });
</script>
<script>
    //Card Number Mask
    new Cleave('#creditCardExpiration', {
        delimiters: [' / ',],
        blocks: [2, 2],
        numericOnly: true
    });
</script>
<script>
    //Card Number Mask
    new Cleave('#creditCardCCV', {
        blocks: [4],
        numericOnly: true
    });
</script>
<!-- Criar assinatura Asaas -->
<script>
    $('#myForm').submit(function (event) {
        event.preventDefault();
        
		//Botão carregando
		$("#loaderButton").addClass('d-flex').removeClass('d-none');
		$("#submitButton").addClass('d-none').removeClass('d-block');

        processForm(this);
    });

    function processForm(dataForm) {
        var typePayment = $('input[name="type"]:checked').val();
        method = typePayment;

        var ajaxData = {
            method: method,
            params: btoa($(dataForm).serialize())
        };

        $.ajax({
            url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/subscription_asaas.php',
            method: 'POST',
            data: ajaxData,
            dataType: 'JSON',
            success: function (response) {
                window.respostaGlobal = response.id;
            }
        })
        .done(function (response) {
            if (response.status == 200) {
                var selectedPaymentType = document.querySelector('input[name="type"]:checked').value;
                var encodedCode = btoa(response.code);

                // Informacoes da loja e do plano
                var planId = <?php echo $plan_id; ?>;
                var shopId = <?php echo $shop_id; ?>;

                $.ajax({
					url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/asaas/alterar_plano.php',
					method: 'POST',
					data: {
                        plan_id: planId,
                        shop_id: shopId
                    },
					dataType: 'JSON',
                    success: function(response) {
                        console.log("Plano alterado com sucesso!");
                    }
				})

                if (selectedPaymentType === "creditCard") {
                    // Redirecionar para página de pagamento
                    window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>historico-de-faturas";
                } else if (selectedPaymentType === "pix") {
                    // Redirecionar para página de pagamento
                    window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>pagamento?s=" + encodedCode;
                }
            }
        })
    }
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