<?php
    if (isset($_GET['s'])) {
        $shop_id = $id;
        
        $subscription_id = $_GET['s'];
        
        $s = base64_decode($subscription_id);

        // Consulta SQL
        $sql = "SELECT * FROM tb_subscriptions WHERE subscription_id = :s AND shop_id = :shop_id";

        // Preparação da declaração PDO
        $stmt = $conn_pdo->prepare($sql);

        // Bind do valor do ID
        $stmt->bindParam(':s', $s);
        $stmt->bindParam(':shop_id', $id);

        // Execução da consulta
        $stmt->execute();

        // Obtenção do resultado
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificação se a consulta retornou algum resultado
        if ($sub) {
            if ($sub['status'] == "OVERDUE" || $sub['status'] == "INACTIVE")
            {
                $_SESSION['msgcad'] = "<p class='red'>A cobrança já foi fechada!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "historico-de-faturas");
                exit;
            }

            if ($sub['cycle'] == "MONTHLY")
            {
                $cycle = "Mensal";
            } else {
                $cycle = "Anual";
            }

            if ($sub['billing_type'] == "CREDIT_CARD")
            {
                $billing_type = "Cartão de crédito";
            } else {
                $billing_type = "Pix";
            }

            $date = new DateTime($sub['due_date']);
            $due_date = $date->format("d/m/Y");

            $sql = "SELECT * FROM tb_plans_interval WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':id', $sub['plan_id']);

            $stmt->execute();

            $plan_interval = $stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM tb_plans WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':id', $plan_interval['plan_id']);

            $stmt->execute();

            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    /* Formatando main */
    main.main
    {
        width: calc(100% - 78px);
        left: 0 !important;
        position: absolute;
        padding: 0 !important;
    }
    main.main .container:first-child
    {
        max-width: 100%;
        padding: 0;
    }

    .success-container
    {
        height: 250px;
        padding-bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--green-color);
    }

    .container.container-details
    {
        margin: 0 .75rem;
    }
    .container.container-details
    {
        margin: 0 auto;
        position: absolute !important;
        left: 50% !important;
        top: 230px;
        transform: translateX(-50%);
    }
    
    /* Botao */
    .btn.btn-success
    {
        background: var(--green-color);
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
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
</style>

<div class="success-container w-100">
    <div class="check-circle mb-3">
        <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2.5" y="2.5" width="95" height="95" rx="47.5" stroke="white" stroke-width="5"/>
            <rect x="72.4976" y="31" width="5" height="50" transform="rotate(45 72.4976 31)" fill="white"/>
            <rect x="44.2131" y="66.3553" width="5" height="25" transform="rotate(135 44.2131 66.3553)" fill="white"/>
        </svg>
    </div>
    <h4 class="text-white fs-4 fw-medium">Pagamento confirmado!</h4>
</div>

<div class="container container-details">
    <div class="card p-0 mb-3">
        <div class="card-body row d-flex align-items-center px-4 py-3">
            <div class="col-md-6">
                <h5 class="fs-5 fw-semibold">Pagamento confirmado!</h5>
                <p>
                    Obrigado por assinar nosso plano <span class="fw-medium"><?php echo $plan['name']; ?></span>. Sua assinatura foi processada com sucesso, você já pode usar seus benefícios.
                </p>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h6 class="fs-6 fw-semibold mb-3">Detalhes da assinatura</h6>
                    <ul class="mb-0">
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">ID:</small>
                            <small><?php echo $sub['id']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Valor:</small>
                            <small>R$ <?php echo number_format($sub['value'], 2, ',', '.'); ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Plano:</small>
                            <small><?php echo $plan['name']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Ciclo:</small>
                            <small><?php echo $cycle; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Forma de Pagamento:</small>
                            <small><?php echo $billing_type; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Próximo Pagamento:</small>
                            <small><?php echo $due_date; ?></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>historico-de-faturas" class="link">Ver assinatura</a>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center mb-3" style="height: 38px;">
            Página inicial
        </a>
    </div>
</div>
<?php
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum resultado encontrado.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "historico-de-faturas");
            exit;
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>O ID da assinatura não está definido.</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD);
        exit;
    }
?>