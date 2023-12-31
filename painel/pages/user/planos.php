<div class="page__header center d-flex justify-content-center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title d-block">
                <h2 class="title">Temos o plano ideal pra cada momento da sua jornada</h2>
                <p>Tenha mais recursos assinando um dos nossos planos e leve sua loja para outro nível</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Switch */
    .switch-container
    {
        width: 210px;
        height: 40px;
        padding: 4px !important;
        background: rgb(232, 233, 235);
        border-radius: 20px;
    }
    .switch-container .nav-item
    {
        width: 50%;
        height: 100%;
        cursor: pointer;
    }
    .switch-container .nav-item a
    {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 18px;
        text-decoration: none;
        color: var(--bs-heading-color);
        font-size: .875rem;
        font-weight: 600;
    }
    .switch-container .nav-item a.active
    {
        background: white;
    }

    /* Tab container */
    .tab-pane.fade
    {
        display: none;
    }
    .tab-pane.fade.show
    {
        display: block;
    }

    .price-container
    {
        height: 51px;
    }

    .btn
    {
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
    }
    .btn.btn-success
    {
        background: var(--green-color);
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color);
    }
    .btn.current
    {
        color: var(--bs-heading-color);
        background: #e8e9eb !important;
    }

    /* Outline */
    .card.outline
    {
        color: #a0a8b6;
        background: #f8f8f9;
    }

    /* Check */
    .list-style-one li
    {
        display: flex;
        font-size: .875rem;
        margin-bottom: .5rem;
    }
    .list-style-one li i
    {
        color: var(--green-color);
        font-size: 1.25rem;
        margin-right: .5rem;
    }
</style>

<div class="d-flex justify-content-center">
    <ul class="switch-container nav nav-pills nav-fill mb-80 rmb-50 wow fadeInUp delay-0-4s">
        <li class="nav-item">
            <a href="#plans-tap1" class="active" data-bs-toggle="tab">Mensal</a>
        </li>
        <li class="nav-item">
            <a href="#plans-tap2" data-bs-toggle="tab">Anual</a>
        </li>
    </ul>
</div>

<?php
// Sua consulta SQL
$sql = "SELECT t2.id, t2.plan_id, t1.name, t1.sub_name, t2.price, t2.billing_interval, t1.resources
        FROM tb_plans t1
        JOIN tb_plans_interval t2 ON t1.id = t2.plan_id";

// Executar a consulta
$stmt = $conn_pdo->query($sql);
$planos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicialize variáveis para verificar a primeira iteração
$monthlyPlans = [];
$annualPlans = [];

// Separe os planos mensais e anuais
foreach ($planos as $plano) {
    if ($plano['billing_interval'] == 'monthly') {
        $monthlyPlans[] = $plano;
    } elseif ($plano['billing_interval'] == 'yearly') {
        $annualPlans[] = $plano;
    }
}

// Função para exibir os detalhes do plano
function displayPlanDetails($id, $plan_id, $associated_plan, $shop_plan, $alterar_ciclo, $name, $sub_name, $price, $billing_interval, $resources) {
    ?>

    <div class="col d-grid">
        <div class="card <?php echo ($associated_plan > $plan_id) ? "outline" : ""; ?>">
            <div class="title mb-3">
                <h4 class="lh-1 mb-0"><?php echo $name; ?></h4>
                <p><?php echo $sub_name; ?></p>
            </div>
            <div class="price-container mb-3">
                <span class="fs-5 fw-semibold">R$ <?php echo $price; ?></span>
                <small>por <?php echo ($billing_interval == "monthly") ? "mês" : "ano"; ?></small>
            </div>

            <?php
                if ($id == $shop_plan) {
                    // Se o plano já estiver assinado ou for igual a 2, mostre o botão "Atual"
                    echo '<button type="button" class="btn current rounded small fw-semibold mb-3" data-toggle="tooltip" data-placement="top" aria-label="Este já é o seu plano." data-bs-original-title="Este já é o seu plano.">Plano atual</button>';
                } else if ($id == $alterar_ciclo && $alterar_ciclo == 2) {
                    // Se o alterar ciclo for igual ao id do plano, mostre o botão "Alterar ciclo"
                    echo '<button type="button" class="btn current rounded small fw-semibold mb-3" data-toggle="tooltip" data-placement="top" aria-label="Este já é o seu plano." data-bs-original-title="Este já é o seu plano.">Plano atual</button>';
                } else if ($id == $alterar_ciclo) {
                    // Se o alterar ciclo for igual ao id do plano, mostre o botão "Alterar ciclo"
                    echo '<a href="' . INCLUDE_PATH_DASHBOARD . 'assinar-plano-asaas?p=' . $id . '" class="btn btn-success rounded small fw-semibold mb-3">Alterar ciclo</a>';
                } else if ($associated_plan > $plan_id && $plan_id == 1) {
                    // Se o id do plano for menor que o id do plano ativo e o id do plano for igual a 1, mostre o botão "Descer de plano"
                    echo '<button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary small fw-semibold mb-3">Descer de plano</button>';
                } else if ($associated_plan > $plan_id) {
                    // Se o id do plano for menor que o id do plano ativo, mostre o botão "Descer de plano"
                    echo '<a href="' . INCLUDE_PATH_DASHBOARD . 'assinar-plano-asaas?p=' . $id . '" class="btn btn-outline-light border border-secondary-subtle text-secondary small fw-semibold mb-3">Descer de plano</a>';
                } else if ($associated_plan < $plan_id) {
                    // Se o id do plano for maior que o id do plano ativo, mostre o botão "Assinar plano"
                    echo '<a href="' . INCLUDE_PATH_DASHBOARD . 'assinar-plano-asaas?p=' . $id . '" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>';
                }
            ?>

            <!-- Exiba os recursos na página -->
            <ul class="list-style-one mb-0">
                <?php
                    $decoded_resources = json_decode($resources);
                    if (!empty($decoded_resources)) {
                        foreach ($decoded_resources as $recurso) {
                            echo "<li><i class='bx bx-check'></i>$recurso</li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </div>

    <?php
}

$tabela = "tb_plans_interval";
$sql = "SELECT id, plan_id FROM tb_plans_interval WHERE id = :id";
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);
$stmt->execute();
$price = $stmt->fetch(PDO::FETCH_ASSOC);
if ($price) {
    // Consulta SQL
    $sql = "SELECT id, plan_id FROM tb_plans_interval WHERE plan_id = :plan_id AND id <> :current_plan";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':plan_id', $price['plan_id'], PDO::PARAM_INT);
    $stmt->bindParam(':current_plan', $price['id'], PDO::PARAM_INT);
    $stmt->execute();
    $price = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($price) {
        $alterar_ciclo = $price['id'];
        $associated_plan = $price['plan_id'];
    }
}

// Exibir planos mensais
if (!empty($monthlyPlans)) {
    echo '
        <div class="tab-pane fade show active" id="plans-tap1">
            <div class="row g-3">';

    foreach ($monthlyPlans as $monthlyPlan) {
        displayPlanDetails($monthlyPlan['id'], $monthlyPlan['plan_id'], $associated_plan, $plan_id, $alterar_ciclo, $monthlyPlan['name'], $monthlyPlan['sub_name'], $monthlyPlan['price'], 'monthly', $monthlyPlan['resources']);
    }

    echo '
            </div>
        </div>
    '; // Fechar a div mensal
}

// Exibir planos anuais
if (!empty($annualPlans)) {
    echo '
        <div class="tab-pane fade" id="plans-tap2">
            <div class="row g-3">';

    foreach ($annualPlans as $annualPlan) {
        displayPlanDetails($annualPlan['id'], $annualPlan['plan_id'], $associated_plan, $plan_id, $alterar_ciclo, $annualPlan['name'], $annualPlan['sub_name'], $annualPlan['price'], 'yearly', $annualPlan['resources']);
    }

    echo '
            </div>
        </div>
    '; // Fechar a div anual
}
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>