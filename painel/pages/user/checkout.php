<?php
$shop_id = $id;
$shop_plan = $plan_id;

// Nome da tabela para a busca
$tabela = 'tb_invoice_info';

$sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':shop_id', $shop_id);
$stmt->execute();

// Obter o resultado como um array associativo
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$selectedServices = $_POST['selectedServices'];

// Decodificar os dados JSON para um array PHP
$service = json_decode($selectedServices, true);

$type = $service[0]['type'];

if ($type == "ready-site") {
    // Id do site
    $id = $_POST['ready_site_id'];

    // Tabela que sera feita a consulta
    $tabela = "tb_ready_sites";

    // Nome da tabela para a busca
    $tabelaAssociatedServices = 'tb_ready_site_services';

    // Nome da pasta
    $path = "site-pronto/";
} elseif ($type == "service") {
    // Id do site
    $id = $service[0]['id'];

    // Tabela que sera feita a consulta
    $tabela = "tb_services";

    // Nome da tabela para a busca
    $tabelaAssociatedServices = 'tb_service_services';

    // Nome da pasta
    $path = "servico/";
}







// Consulta SQL
$sql = "SELECT * FROM $tabela WHERE id = :id";

// Preparar a consulta
$stmt = $conn_pdo->prepare($sql);

// Vincular o valor do parâmetro
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Obter o resultado como um array associativo
$site = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o ID é válido (maior que zero)
if ($site) {
    if ($type == "ready-site") {
        $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/ready-website/" . $site['id'] . "/card-image/" . $site['card_image'];
    } elseif ($type == "service") {
        $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/service/" . $site['id'] . "/card-image/" . $site['card_image'];
    }






    if ($site['cycle'] == "recurrent") {
        $priceNoFormat = ($site['discount'] == "0.00") ? $site['price'] : $site['discount'];
    
        $amountDiscount = $site['price'] - $site['discount'];
        
        $discount = "R$ " . number_format($site['price'], 2, ",", ".");
        
        $amountDiscountValue = "R$ " . number_format($amountDiscount, 2, ",", ".");
        
        // Nome da tabela para a busca
        $tabela = 'tb_rewards';
        
        // Obter a data atual no formato YYYY-MM-DD
        $dueDate = date('Y-m-d');
        
        $sql = "SELECT COUNT(*) as total_purchases FROM $tabela WHERE indicator_id = :indicator_id AND due_date >= :due_date";
        
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':indicator_id', $user_id);
        $stmt->bindParam(':due_date', $dueDate);
        $stmt->execute();
        
        // Recuperar os resultados
        $indication['total_purchases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_purchases'];
        
        // Calcular o desconto total
        $discount_percentage = 10; // 10% por compra
        $total_purchases = $indication['total_purchases'];
        $total_discount = $total_purchases * $discount_percentage;
        
        // Limitar o desconto a no máximo 100%
        if ($total_discount > 100) {
            $total_discount = 100;
        }
        
        // Calcular o preço final após o desconto
        $original_price = $priceNoFormat;
        $discount_amount = ($total_discount / 100) * $original_price;
        $finalPrice = $original_price - $discount_amount;
        
        // Garante que o preço final não seja negativo
        $final_price = max($finalPrice, 0);
        
        $original_price = $_POST['ready_site_price'];
        $_POST['ready_site_price'] = $final_price;
    }





?>
<style>
    .disabled
    {
        color: #a0a8b6;
        background: #f8f8f9;
        pointer-events: none;
    }

    .type
    {
        cursor: pointer;
    }
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

<!-- Modal -->
<div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 pb-3 pt-4 border-0">
                <h6 class="modal-title fs-6" id="exampleModalLabel">Informações da fatura</h6>
            </div>
            <div class="modal-body px-4 pb-3 pt-0">
                Para a compra do Site Afiliado Hotmart, rever contratação do plano Avançado Dropi Digital.
                <!-- <a href="ajuda.dropidigital.com.br/" class="link" target="_blank">Saiba mais aqui!</a> -->
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" id="modalExitButton">Sair</button>
                <button type="button" class="btn btn-danger fw-semibold px-4 py-2 small" id="modalContinueButton">Continuar</button>
            </div>
        </div>
    </div>
</div>

<style>
    #warningModal .loader {
        width: 32px;
        height: 32px;
        border: 2.5px solid var(--green-color) !important;
        border-bottom-color: transparent !important;
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
<!-- Modal -->
<div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header px-4 pb-3 pt-4 border-0">
                <h6 class="modal-title fs-6" id="exampleModalLabel">Aviso!</h6>
            </div>
            <div class="modal-body d-flex flex-column align-items-center justify-content-center px-4 pb-3 pt-0">
                <div class="loader"></div>
                <p class="fs-5 fw-semibold mt-2">Seu tema está sendo instalado!</p>
                <p>Por favor não saia desta página ou feche o navegador.</p>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD . $path . $site['link']; ?>" class="title text-reset text-decoration-none">Escolher <?php echo ($type == "ready-site") ? "Site Pronto" : "Serviço"; ?></a></li>
            <li class="breadcrumb-item fw-semibold text-body-secondary active" aria-current="page">Informações de pagamento</li>
        </ol>
    </nav>
</div>

<?php
    // Remover todas as tags HTML
    $description_formated = strip_tags($site['description']);

    // Remover quebras de linha
    $description_transformed = str_replace(array("\r", "\n"), ' ', $description_formated);

    // Verifica se o texto é maior que 50 caracteres
    if (mb_strlen($description_transformed, 'UTF-8') > 50) {
        $description = mb_substr($description_transformed, 0, 47, 'UTF-8') . "...";
    } else {
        $description = $description_transformed;
    }

    $price = (isset($_POST['ready_site_price'])) ? $_POST['ready_site_price'] : $_POST['service_price'];
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

<form id="myForm" class="position-relative" action="submit">
    <div class="row">
        <div class="col-md-6">
            <?php
                if ($site['cycle'] == "recurrent" && $type == "ready-site") {
                    // Tabela que sera feita a consulta
                    $tabela = "tb_plans_interval";

                    // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
                    $sql = "SELECT id, plan_id, billing_interval FROM $tabela WHERE id = :id";

                    // Prepara a consulta
                    $stmt = $conn_pdo->prepare($sql);

                    // Binde o parâmetro
                    $stmt->bindParam(':id', $site['plan_id'], PDO::PARAM_INT);

                    // Executa a consulta
                    $stmt->execute();

                    // Obtém os resultados
                    $planData = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Verificar se o resultado foi encontrado
                    if ($planData) {
                        $id = $planData['plan_id'];
                        $billing_interval = $planData['billing_interval'];
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
                    $planData = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Verificar se o resultado foi encontrado
                    if ($planData) {
            ?>
            <div class="card mb-4 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Plano contratado</div>
                <div class="card-body px-4 py-3">
                    <label class="card frequency active">
                        <div class="row" style="height: 52px;">

                            <?php
                                // Tabela que sera feita a consulta
                                $tabela = "tb_plans_interval";

                                // Consulta SQL
                                $sql = "SELECT id, price FROM $tabela WHERE plan_id = :id AND billing_interval = :billing_interval";

                                // Preparar a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Vincular o valor do parâmetro
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->bindValue(':billing_interval', 'monthly', PDO::PARAM_STR);

                                // Executar a consulta
                                $stmt->execute();

                                // Obter o resultado como um array associativo
                                $planPrice = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Verificar se o resultado foi encontrado
                                if ($planPrice) {
                                    $monthly_id = $planPrice['id'];
                                    $monthly_price = $planPrice['price'];
                            ?>

                            <p class="d-flex align-items-center col-md-4">Assinatura mensal</p>
                            <div class="pricing d-flex flex-column justify-content-center col-md-8">
                                <div class="d-flex mb-1">
                                    <p class="fw-semibold text-decoration-line-through lh-1">
                                        R$ <?= number_format($monthly_price, 2, ",", "."); ?> por mês
                                    </p>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <span class="fw-semibold small me-1">R$</span>
                                    <h5 class="lh-1 mb-0">
                                        <?= number_format($original_price, 2, ",", "."); ?> por mês
                                    </h5>
                                    <i class="bx bx-help-circle edited ms-1" data-toggle="tooltip" data-placement="top" title="O valor de <?= number_format($price, 2, ",", "."); ?> é valido apenas para primeira cobrança, nas próximas será cobrado o valor de <?= number_format($site['price'], 2, ",", "."); ?>."></i>
                                </div>
                            </div>

                            <?php
                                }
                            ?>

                        </div>
                    </label>
                </div>
            </div>
            <?php
                    }
                }
            ?>
            <div class="card mb-4 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent"><?= ($site['cycle'] == "recurrent") ? "Site Pronto Bônus" : "Site Pronto"; ?></div>
                <div class="card-body px-4 py-3">
                    <div class="card mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="<?= $image; ?>" alt="Imagem Site Pronto" style="width: 100px;">
                                <div class="ms-2">
                                    <h5 class="mb-0"><?= $site['name']; ?></h5>
                                    <p class="small" title="<?= $description_formated; ?>"><?= $description; ?></p>
                                </div>
                            </div>
                            <?php
                                if ($site['cycle'] == "recurrent") {
                            ?>
                                <p class="fw-semibold fs-5">Grátis</p>
                            <?php
                                } else {
                            ?>
                                <div>
                                    <span class="fw-semibold small">R$</span>
                                    <span class="fw-semibold fs-5"> <?= number_format($price, 2, ",", "."); ?></span>
                                </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if ($type == "ready-site") {
                    // Sua consulta SQL com JOIN para obter os serviços associados a um site
                    $sql = "SELECT ss.*, s.* FROM $tabelaAssociatedServices ss JOIN tb_services s ON ss.service_id = s.id WHERE ss.ready_site_id = :id";
                } elseif ($type == "service") {
                    // Sua consulta SQL com JOIN para obter os serviços associados a um site
                    $sql = "SELECT ss.*, s.* FROM $tabelaAssociatedServices ss JOIN tb_services s ON ss.associated_service_id = s.id WHERE ss.service_id = :id";
                }

                // Prepara a consulta
                $stmt = $conn_pdo->prepare($sql);

                // Binde o parâmetro
                $stmt->bindParam(':id', $site['id'], PDO::PARAM_INT);

                // Executa a consulta
                $stmt->execute();

                // Obtém os resultados
                $siteServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Obtém o numero de resultados
                $countSiteServices = $stmt->rowCount();

                $isActive = false;

                if ($countSiteServices == 0) {
                    if ($site['cycle'] == "recurrent" || !isset($site['plan_id']) || $site['plan_id'] == 1) {
                        $isActive = " d-none";
                    }
                }
            ?>
            <div class="card mb-4 p-0<?= $isActive; ?>">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ofertas</div>
                <div class="card-body px-4 py-3">
                    <?php
                        $selectedServices = $_POST['selectedServices'];

                        // Decodificar os dados JSON para um array PHP
                        $servicesArray = json_decode($selectedServices, true);

                        $services = array_shift($servicesArray);

                        // Array para armazenar apenas os IDs dos itens selecionados
                        $selectedIds = array();

                        // Verificar se $_POST['selectedServices'] está definido e é um array
                        if (isset($_POST['selectedServices']) && is_array($servicesArray)) {
                            // Iterar sobre os itens e obter os IDs dos itens selecionados
                            foreach ($servicesArray as $service) {
                                if ($service['type'] == "service") {
                                    $selectedIds[] = $service['id'];
                                } elseif ($service['type'] == "subscription") {
                                    $selectedPlanId = $service['id'];
                                }
                            }
                        }

                        if (isset($site['plan_id']) && $site['cycle'] !== "recurrent") {
                            if ($site['plan_id'] != 1 || $site['plan_id'] != 2) {
                                // Tabela que será feita a consulta
                                $tabela = "tb_plans_interval";

                                // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
                                $sql = "SELECT id, plan_id, billing_interval, price FROM $tabela WHERE id = :id";

                                // Prepara a consulta
                                $stmt = $conn_pdo->prepare($sql);

                                // Binde o parâmetro
                                $stmt->bindParam(':id', $site['plan_id'], PDO::PARAM_INT);

                                // Executa a consulta
                                $stmt->execute();

                                // Obtém os resultados
                                $plan_interval = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Verificar se o resultado foi encontrado
                                if ($plan_interval) {
                                    if ($site['plan_id'] > $plan_interval['id']) {
                                        // Tabela que será feita a consulta
                                        $tabela = "tb_plans";

                                        // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
                                        $sql = "SELECT id, name, sub_name, resources FROM $tabela WHERE id = :id";

                                        // Prepara a consulta
                                        $stmt = $conn_pdo->prepare($sql);

                                        // Binde o parâmetro
                                        $stmt->bindParam(':id', $plan['plan_id'], PDO::PARAM_INT);

                                        // Executa a consulta
                                        $stmt->execute();

                                        // Obtém os resultados
                                        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                                        // Verificar se o resultado foi encontrado
                                        if ($plan) {
                                            if ($plan_interval['billing_interval'] == "monthly") {
                                                $billing_interval = "(mensal)";
                                                $price = $plan_interval['price'];
                                            } else {
                                                $totalPrice = $plan_interval['price'];
                                                $billing_interval = "($totalPrice/anual)";
                                                $price = $plan_interval['price'] / 12;
                                            }
                    ?>

                                <div class="card mb-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" id="planCheckbox" class="form-check-input me-2 mt-0" data-type="subscrition" data-plan-id="<?= $plan_interval['id']; ?>" data-value="<?= $plan_interval['price']; ?>" checked disabled>
                                            <p class="d-flex align-items-center fw-semibold">
                                                Plano <?= $plan['name']; ?>
                                                <i class="bx bx-help-circle edited ms-1" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="Assinatura do Plano <?= $plan['name']; ?> com pagamento mensal para usufruir de todos os benefícios do Site Pronto." data-bs-original-title="Assinatura do Plano <?= $plan['name']; ?> com pagamento mensal para usufruir de todos os benefícios do Site Pronto."></i>
                                            </p>
                                        </div>
                                        <p class="fw-semibold">
                                            R$ 
                                            <?= number_format($price, 2, ",", "."); ?> 
                                            <small><?= $billing_interval; ?></small>
                                        </p>
                                    </div>
                                </div>

                                <input type="hidden" name="plan_id" value="<?php echo $site['plan_id']; ?>">
            					<input type="hidden" name="plan_period" id="plan_period" value="<?php echo $plan_interval['billing_interval']; ?>">

                    <?php
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                    <?php
                        // Verificar se o resultado foi encontrado
                        if ($siteServices) {
                            foreach ($siteServices as $siteService) {
                                // Formatação preço
                                $servicePreco = $siteService['price'];

                                // Transforma o número no formato "R$ 149,90"
                                $servicePrice = "R$ " . number_format($servicePreco, 2, ",", ".");

                                // Formatação preço com desconto
                                $serviceDesconto = $siteService['discount'];

                                // Transforma o número no formato "R$ 149,90"
                                $serviceDiscount = "R$ " . number_format($serviceDesconto, 2, ",", ".");

                                // Calcula a porcentagem de desconto
                                if ($siteService['price'] != 0) {
                                    $servicePorcentagemDesconto = (($siteService['price'] - $siteService['discount']) / $siteService['price']) * 100;
                                } else {
                                    // Lógica para lidar com o caso em que $siteService['price'] é zero
                                    $servicePorcentagemDesconto = 0; // Ou outro valor padrão
                                }

                                // Arredonda o resultado para duas casas decimais
                                $servicePorcentagemDesconto = round($servicePorcentagemDesconto, 0);

                                if ($siteService['discount'] == "0.00") {
                                    $serviceActiveDiscount = "d-none";

                                    $servicePriceAfterDiscount = $servicePrice;

                                    $servicePriceNoFormat = $siteService['price'];
                                } else {
                                    $serviceActiveDiscount = "";

                                    $servicePriceAfterDiscount = $serviceDiscount;
                                    $serviceDiscount = $servicePrice;

                                    $servicePriceNoFormat = $siteService['discount'];
                                }

                                if ($siteService['without_price'] == 1) {
                                    $serviceActiveDiscount = "d-none";
                                    $servicePriceAfterDiscount = "";
                                }


                                // Verificar se o ID está presente nos itens selecionados
                                $isChecked = in_array($siteService['id'], $selectedIds);

                                // Adiciona o atributo "checked" se estiver presente nos itens selecionados
                                $checkedAttribute = $isChecked ? 'checked' : '';
                    ?>

                    <div class="card mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-2" data-type="service" data-service-id="<?= $siteService['id']; ?>" data-value="<?= $servicePriceNoFormat; ?>" <?= $checkedAttribute ?>>
                                <p class="d-flex align-items-center fw-semibold">
                                    <?= $siteService['name']; ?>
                                    <i class="bx bx-help-circle edited ms-1" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="<?= $siteService['seo_description']; ?>" data-bs-original-title="<?= $siteService['seo_description']; ?>"></i>
                                </p>
                            </div>
                            <p class="fw-semibold">
                                <small class="text-secondary text-decoration-line-through <?= $serviceActiveDiscount; ?>"><?= $serviceDiscount; ?></small>
                                <?= $servicePriceAfterDiscount; ?>
                            </p>
                        </div>
                    </div>

                    <?php
                            }
                        }
                    ?>
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
        <?php
            if ($site['cycle'] == "recurrent") {
                $enabledPix = "d-none";
                $enabled = "col-md-12";
            } else {
                $enabledPix = "";
                $enabled = "col-md-8";
            }
        ?>
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">Informações de pagamento</div>
                <div class="card-body px-4 py-3">
                    <div class="row mb-3 g-3">
                        <input type="radio" name="type" id="creditCard" value="creditCard" class="d-none" checked>
                        <input type="radio" name="type" id="pix" value="pix" class="d-none">
                        <div class="<?= $enabled; ?>">
                            <label for="creditCard" class="card type d-flex align-items-center justify-content-center active">
                                <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 0 576 512" id="creditCard"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M512 80c8.8 0 16 7.2 16 16v32H48V96c0-8.8 7.2-16 16-16H512zm16 144V416c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V224H528zM64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V96c0-35.3-28.7-64-64-64H64zm56 304c-13.3 0-24 10.7-24 24s10.7 24 24 24h48c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm128 0c-13.3 0-24 10.7-24 24s10.7 24 24 24H360c13.3 0 24-10.7 24-24s-10.7-24-24-24H248z"/></svg>
                                Cartão de crédito
                            </label>
                        </div>
                        <div class="col-md-4 <?= $enabledPix; ?>">
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
                        <div class="mb-3 <?= ($site['cycle'] == "recurrent") ? "d-none" : ""; ?>" id="installmentContainer">
                            <label for="installment" class="form-label small">Parcelas</label>
                            <div class="input-group">
                                <select class="form-select" name="installment" id="installment" aria-label="Default select example" required>
                                    <option value="1|<?php echo $_POST['price']; ?>" selected>À vista</option>
                                    <?php for ($i = 2; $i <= 12; $i++): ?>
                                        <?php $installmentValue = round($_POST['price'] / $i, 2); ?>
                                        <option value="<?php echo $i . '|' . $installmentValue; ?>"> <?php echo $i; ?>x de R$ <?php echo number_format($installmentValue, 2, ',', ''); ?> sem juros</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

					<input type="hidden" name="value" id="value" value="<?php echo $_POST['price']; ?>">
                    <?php if (!empty($_POST['ready_site_id'])) { ?>
                        <input type="hidden" name="ready_site_id" id="ready_site_id" value="<?php echo $site['shop_id']; ?>">
                        <input type="hidden" name="ready_site_price" id="ready_site_price" value="<?php echo $_POST['ready_site_price']; ?>">
                        <input type="hidden" name="cycle" id="cycle" value="<?php echo $site['cycle']; ?>">
                        <?php if ($site['cycle'] == "recurrent") { ?>
                            <input type="hidden" name="plan_id" id="plan_id" value="<?= $site['plan_id']; ?>">
                            <input type="hidden" name="ready_site_original_price" id="ready_site_original_price" value="<?php echo $site['price']; ?>">
                        <?php } ?>
                    <?php } ?>
					<input type="hidden" name="selectedServices" id="selectedServices" value='<?php echo $_POST['selectedServices']; ?>'>
                    <?php if (isset($plan_interval)) { ?>
                        <input type="hidden" name="plan_price" id="after_plan_price" value="<?php echo $plan_interval['price']; ?>">
                    <?php } ?>

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

                    <?php
                        if ($site['cycle'] == "recurrent") {
                            $priceNoFormat = ($site['discount'] == "0.00") ? $site['price'] : $site['discount'];

                            $amountDiscount = $site['price'] - $site['discount'];

                            $discount = "R$ " . number_format($site['price'], 2, ",", ".");

                            $amountDiscountValue = "R$ " . number_format($amountDiscount, 2, ",", ".");

                            // Nome da tabela para a busca
                            $tabela = 'tb_rewards';

                            // Obter a data atual no formato YYYY-MM-DD
                            $dueDate = date('Y-m-d');

                            $sql = "SELECT COUNT(*) as total_purchases FROM $tabela WHERE indicator_id = :indicator_id AND due_date >= :due_date";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':indicator_id', $user_id);
                            $stmt->bindParam(':due_date', $dueDate);
                            $stmt->execute();

                            // Recuperar os resultados
                            $indication['total_purchases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_purchases'];

                            // Calcular o desconto total
                            $discount_percentage = 10; // 10% por compra
                            $total_purchases = $indication['total_purchases'];
                            $total_discount = $total_purchases * $discount_percentage;

                            // Limitar o desconto a no máximo 100%
                            if ($total_discount > 100) {
                                $total_discount = 100;
                            }

                            // Calcular o preço final após o desconto
                            $original_price = $priceNoFormat;
                            $discount_amount = ($total_discount / 100) * $original_price;
                            $finalPrice = $original_price - $discount_amount;

                            // Garante que o preço final não seja negativo
                            $final_price = max($finalPrice, 0);
                            
                            $discountAmount = "R$ " . number_format($discount_amount, 2, ",", ".");
                            $finalPrice = "R$ " . number_format($final_price, 2, ",", ".");
                    ?>

                    <p class="fw-semibold">Resumo da compra</p>
                    <hr class="my-2">
                    <div class="d-flex align-items-end justify-content-between mb-1">
                        <p class="small">Valor:</p>
                        <span class="small"><?= $discount; ?></span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mb-1">
                        <p class="small">Site Pronto:</p>
                        <span class="text-success small">Grátis</span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mb-1">
                        <p class="small">Desconto:</p>
                        <span class="text-success small">- <?= $amountDiscountValue; ?></span>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mb-1">
                        <p class="small">Desconto por indicação:</p>
                        <span class="text-success small">- <?= $discountAmount; ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-end justify-content-between mb-3">
                        <p class="fw-semibold">Total:</p>
                        <p class="fw-semibold"><?= $finalPrice; ?><small>/mês</small></p>
                    </div>

                    <?php
                        }
                    ?>

                    <!-- Aqui está o seu botão. Eu adicionei um ID para poder referenciá-lo no script JavaScript -->
                    <button type="submit" class="btn btn-success fw-semibold w-100 px-4 py-2 small mb-3" id="submitButton">
                        <?php echo "Pagar R$ " . number_format($_POST['price'], 2, ",", ".");; ?>
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

                    <small class="lh-1" id="pixText" style="display: none;">Pagamentos por Pix têm aprovação instantânea.</small>

                    <small class="lh-1" class="<?= ($site['cycle'] == "recurrent") ? "d-none" : ""; ?>">Valor válido apenas para a primeira fatura.</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>















<script>
    $(document).ready(function() {
        let prevCheckedState;

        // Função para atualizar o array de serviços selecionados no input hidden
        function updateSelectedServices() {
            var selectedServices = [];

            // Adicionar informações do ready-site
            selectedServices.push({
                type: "<?= $type; ?>",
                id: <?= $site['id']; ?>,
                value: <?= $_POST['ready_site_price']; ?>
            });

            $('.form-check-input:checked').each(function() {
                var type = $(this).data('type');
                var id = $(this).data('service-id');
                var value = $(this).data('value');
                selectedServices.push({ type: type, id: id, value: value });
            });
            $('#selectedServices').val(JSON.stringify(selectedServices));
        }

        // Inicializa ou atualiza o texto do botão baseado nas seleções
        function updateButtonAndInstallments() {
            var basePrice = parseFloat(<?= $_POST['ready_site_price']; ?>); // Preço base sem os adicionais

            // Soma os valores dos checkboxes selecionados ao total
            var total = basePrice;
            $('.form-check-input:checked').each(function() {
                var value = parseFloat($(this).data('value'));
                total += value;
            });

            // Atualiza o valor e o texto das opções do select de parcelamento
            $('#installment option').each(function(index) {
                var optionValue = $(this).val().split('|');
                var numberOfInstallments = parseInt(optionValue[0]);
                var installmentValue = total / numberOfInstallments;

                // Formatação do valor total
                var formattedInstallmentValue = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(installmentValue);

                if (index === 0) {
                    $(this).text('À Vista');
                } else {
                    $(this).text(`${numberOfInstallments}x de R$ ${formattedInstallmentValue} sem juros`);
                }

                $(this).val(`${numberOfInstallments}|${installmentValue.toFixed(2)}`); // Atualiza o valor da opção
            });

            if ($('input[type="radio"][name="type"]:checked').val() === "creditCard") {
                // Obtém a quantidade de parcelas selecionada
                var totalInstallment = $('#installment').val().split('|')[1];
                
                // Formatação do valor total
                var formattedTotalInstallment = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalInstallment);
                
                // Obtém a quantidade de parcelas selecionada
                var selectedInstallments = $('#installment').val().split('|')[0];

                $('#value').val(total);
                
                if ($('input[name="cycle"]').val() === "recurrent") {
                    // Atualização do texto do botão com o valor total e a quantidade de parcelas
                    $('#submitButton').text(`Pagar ${formattedTotalInstallment}`);
                } else {
                    // Atualização do texto do botão com o valor total e a quantidade de parcelas
                    $('#submitButton').text(`Pagar ${selectedInstallments}x de ${formattedTotalInstallment}`);
                }
            } else {
                // Formatação do valor total
                var formattedTotal = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(total);

                $('#value').val(total);

                // Atualização do texto do botão com o valor total e a quantidade de parcelas
                $('#submitButton').text(`Pagar ${formattedTotal}`);
            }

            // Chama a função para atualizar os serviços selecionados
            updateSelectedServices();
        }

        // Evento de mudança nos checkboxes
        $('.form-check-input, #installment, input[type="radio"][name="type"]').change(updateButtonAndInstallments);

        // Alerta desativar checkbox plan
        $('input[type="checkbox"]#planCheckbox').change(function() {
            if (!this.checked) {
                $('#planModal').modal('show');
            }
        });

        $('#modalExitButton').click(function() {
            // Fecha o modal e mantém o checkbox marcado
            $('input[type="checkbox"]#planCheckbox').prop('checked', true);
            $('#planModal').modal('hide');
            updateButtonAndInstallments();
        });

        $('#modalContinueButton').click(function() {
            // Fecha o modal e desmarca o checkbox
            $('input[type="checkbox"]#planCheckbox').prop('checked', false);
            $('#planModal').modal('hide');
            updateButtonAndInstallments();
        });

        // Esconde o modal quando o botão de fechar (x) é clicado
        $('.close').click(function() {
            $('input[type="checkbox"]#planCheckbox').prop('checked', true);
            $('#planModal').modal('hide');
            updateButtonAndInstallments();
        });

        // Chame updateSelectedServices() para incluir as informações do ready-site
        updateSelectedServices();

        // Chama a função inicialmente para definir o valor
        updateButtonAndInstallments();
    });
</script>


















<!-- Alerta desativar checkbox -->
<script>
    $(document).ready(function() {
        $('input[type="checkbox"]#planCheckbox').change(function() {
            if (!this.checked) {
                $('#planModal').modal('show');
            }
        });

        $('#modalExitButton').click(function() {
            // Fecha o modal e mantém o checkbox marcado
            $('input[type="checkbox"]#planCheckbox').prop('checked', true);
            $('#planModal').modal('hide');
        });

        $('#modalContinueButton').click(function() {
            // Fecha o modal e desmarca o checkbox
            $('input[type="checkbox"]#planCheckbox').prop('checked', false);
            $('#planModal').modal('hide');
        });

        // Esconde o modal quando o botão de fechar (x) é clicado
        $('.close').click(function() {
            $('input[type="checkbox"]#planCheckbox').prop('checked', true);
            $('#planModal').modal('hide');
        });
    });
</script>

<!-- Adicione este script JavaScript -->
<script>
    $(document).ready(function() {
        // Função para atualizar o array de serviços selecionados no input hidden
        function updateSelectedServices() {
            var selectedServices = [];

            // Adicionar informações do ready-site
            selectedServices.push({
                type: "<?= $type; ?>",
                id: <?= $site['id']; ?>,
                value: <?= $_POST['ready_site_price']; ?>
            });

            $('.form-check-input:checked').each(function() {
                var type = $(this).data('type');
                var id = $(this).data('service-id');
                var value = $(this).data('value');
                selectedServices.push({ type: type, id: id, value: value });
            });
            $('#selectedServices').val(JSON.stringify(selectedServices));
        }

        // Inicializa ou atualiza o texto do botão baseado nas seleções
        function updateButtonAndInstallments() {
            var basePrice = parseFloat(<?= $_POST['ready_site_price']; ?>); // Preço base sem os adicionais

            // Soma os valores dos checkboxes selecionados ao total
            var total = basePrice;
            $('.form-check-input:checked').each(function() {
                var value = parseFloat($(this).data('value'));
                total += value;
            });

            // Atualiza o valor e o texto das opções do select de parcelamento
            $('#installment option').each(function(index) {
                var optionValue = $(this).val().split('|');
                var numberOfInstallments = parseInt(optionValue[0]);
                var installmentValue = total / numberOfInstallments;

                // Formatação do valor total
                var formattedInstallmentValue = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(installmentValue);

                if (index === 0) {
                    $(this).text('À Vista');
                } else {
                    $(this).text(`${numberOfInstallments}x de R$ ${formattedInstallmentValue} sem juros`);
                }

                $(this).val(`${numberOfInstallments}|${installmentValue.toFixed(2)}`); // Atualiza o valor da opção
            });

            if ($('input[type="radio"][name="type"]:checked').val() === "creditCard") {
                // Obtém a quantidade de parcelas selecionada
                var totalInstallment = $('#installment').val().split('|')[1];
                
                // Formatação do valor total
                var formattedTotalInstallment = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(totalInstallment);
                
                // Obtém a quantidade de parcelas selecionada
                var selectedInstallments = $('#installment').val().split('|')[0];
                
                if ($('input[name="cycle"]').val() === "recurrent") {
                    // Atualização do texto do botão com o valor total e a quantidade de parcelas
                    $('#submitButton').text(`Pagar ${formattedTotalInstallment}`);
                } else {
                    // Atualização do texto do botão com o valor total e a quantidade de parcelas
                    $('#submitButton').text(`Pagar ${selectedInstallments}x de ${formattedTotalInstallment}`);
                }
            } else {
                // Formatação do valor total
                var formattedTotal = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(total);

                // Atualização do texto do botão com o valor total e a quantidade de parcelas
                $('#submitButton').text(`Pagar ${formattedTotal}`);
            }

            // Chama a função para atualizar os serviços selecionados
            updateSelectedServices();
        }

        // Evento de mudança nos checkboxes
        $('.form-check-input, #installment, input[type="radio"][name="type"]').change(updateButtonAndInstallments);

        // Chame updateSelectedServices() para incluir as informações do ready-site
        updateSelectedServices();

        // Chama a função inicialmente para definir o valor
        updateButtonAndInstallments();
    });
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

                    $('input[name="plan_id"]').val('<?php echo $yearly_id; ?>');
                } else if ($(this).val() === "mensal") {
                    $('#installmentContainer').addClass('d-none');
                    $('#creditCartTextType').text('mensal');

                    $('input[name="id_plan"]').val('<?php echo $mpago_id_monthly; ?>');

                    $('input[name="value"]').val('<?php echo $monthly_price; ?>');

                    $('input[name="plan_id"]').val('<?php echo $monthly_id; ?>');
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

        // Obter o order_id da resposta da primeira requisição
        var orderId = ''; // Defina o valor do order_id aqui

        var ajaxData = {
            method: method,
            params: btoa($(dataForm).serialize()),
            order_id: orderId // Incluir o order_id nos parâmetros
        };

        $.ajax({
            url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/orders.php',
            method: 'POST',
            data: ajaxData,
            dataType: 'JSON',
            success: function (response) {
                // Definir o order_id da resposta
                orderId = response.id;

                // Adicionar o order_id aos parâmetros antes de enviar para o endpoint de pagamentos
                ajaxData.order_id = orderId;
                
                // Enviar para o endpoint de pagamentos
                sendToPayments(ajaxData);
                

                // alert("Pedido criado com sucesso! ID: " + response.id);
            }
        });
    }

    function sendToPayments(ajaxData) {
                console.log("Teste");
                console.log(ajaxData);
        $.ajax({
            url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/payments.php',
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

                // $.ajax({
                // 	url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/asaas/alterar_plano.php',
                // 	method: 'POST',
                // 	data: {
                //         plan_id: planId,
                //         shop_id: shopId
                //     },
                // 	dataType: 'JSON',
                //     success: function(response) {
                //         console.log("Plano alterado com sucesso!");
                //     }
                // })

                <?php
                    if ((!empty($_POST['ready_site_id']))) {
                ?>

                $('#warningModal').modal('show');

                var params = {
                    shop_id: <?php echo $shop_id; ?>,
                    ready_site_id: <?php echo $site['shop_id']; ?>
                };

                // Enviar uma solicitação AJAX para verificar o pagamento
                $.ajax({
                    type: 'POST',
                    url: '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/copy_site_shop.php', // Crie um arquivo PHP para lidar com a verificação
                    data: params,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 'sucesso') {
                            window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>pagamento-confirmado?p=" + encodedCode;
                        } else {
                            // Se o pagamento não foi aprovado, você pode tomar alguma ação aqui
                            console.log('O pagamento ainda não foi aprovado.');
                        }
                    },
                    error: function(error) {
                        console.error('Erro ao verificar o pagamento:', error);
                    }
                });
                
                <?php
                    } else {
                ?>

                if ($('input[type="checkbox"]#planCheckbox').is(':checked')) {
                    var redirect = "&r=1";
                } else {
                    var redirect = "";
                }

                if (selectedPaymentType === "creditCard") {
                    // Redirecionar para página de pagamento
                    window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>pagamento-confirmado?p=" + encodedCode;
                } else if (selectedPaymentType === "pix") {
                    // Redirecionar para página de pagamento
                    window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>pagamento?p=" + encodedCode + redirect + "&id=<?= $site['plan_id']; ?>&site=<?= $site['shop_id']; ?>";
                }

                <?php
                    }
                ?>
            } else {
                // Exibir mensagem de erro ao usuário
                if (response.errors && response.errors.length > 0) {
                    var errorMessage = response.errors[0].description;
                    alert("Erro: " + errorMessage);
        
                    //Botão carregando
                    $("#loaderButton").removeClass('d-flex').addClass('d-none');
                    $("#submitButton").removeClass('d-none').addClass('d-block');
                }
            }
        })
        .fail(function (jqXHR) {
            // Capturar e exibir o erro retornado pelo Asaas
            if (jqXHR.responseJSON && jqXHR.responseJSON.errors && jqXHR.responseJSON.errors.length > 0) {
                var errorMessage = jqXHR.responseJSON.errors[0].description;
                alert("Erro: " + errorMessage);
        
                //Botão carregando
                $("#loaderButton").removeClass('d-flex').addClass('d-none');
                $("#submitButton").removeClass('d-none').addClass('d-block');
            } else {
                alert("Erro desconhecido. Tente novamente mais tarde.");
        
                //Botão carregando
                $("#loaderButton").removeClass('d-flex').addClass('d-none');
                $("#submitButton").removeClass('d-none').addClass('d-block');
            }
        });
    }


















</script>

<?php
} else {
    echo "É necessário selecionar um produto!";
}
?>