<?php
    if (isset($_GET['id'])) {
        $shop_id = $id;
        
        $id = $_GET['id'];

        // Consulta SQL
        $sql = "SELECT * FROM tb_subscriptions WHERE id = :id AND shop_id = :shop_id";

        // Preparação da declaração PDO
        $stmt = $conn_pdo->prepare($sql);

        // Bind do valor do ID
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':shop_id', $shop_id);

        // Execução da consulta
        $stmt->execute();

        // Obtenção do resultado
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sub['status'] == "RECEIVED") {
            $bullet = "<span class='bullet success me-2'></span>";
            $status = "Paga";
        } else if ($sub['status'] == "ACTIVE" && $sub['billing_type'] == "CREDIT_CARD") {
            $bullet = "<span class='bullet success me-2'></span>";
            $status = "Paga";
        } else if ($sub['status'] == "ACTIVE") {
            $bullet = "<span class='bullet warning me-2'></span>";
            $status = "Aguardando pagamento";
        } else if ($sub['status'] == "OVERDUE" || $sub['status'] == "INACTIVE") {
            $bullet = "<span class='bullet danger me-2'></span>";
            $status = "Cancelada";
        }

        // Verificação se a consulta retornou algum resultado
        if ($sub) {
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

            // Formatando datas
            $startDate = new DateTime($sub['start_date']);
            $start_date = $startDate->format("d/m/Y");

            $dueDate = new DateTime($sub['due_date']);
            $due_date = $dueDate->format("d/m/Y");

            // Pesquisa o id do plan_id em  tb_plans_interval
            $sql = "SELECT * FROM tb_plans_interval WHERE id = :plan_id";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':plan_id', $sub['plan_id']);

            $stmt->execute();

            $plan = $stmt->fetch(PDO::FETCH_ASSOC);

            // Pesquisa o nome em tb_plans com o id retornado na tb_plans_interval
            $sql = "SELECT * FROM tb_plans WHERE id = :plan_id";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':plan_id', $plan['plan_id']);

            $stmt->execute();

            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<style>
    /* Botao */
    .btn-success
    {
        background: var(--green-color);
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
    }
    .btn-success:hover
    {
        background: var(--dark-green-color);
    }
    .btn-success.current
    {
        color: var(--bs-heading-color);
        background: #e8e9eb !important;
    }

    /* Bullet */
    .bullet
    {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet.success
    {
        background: rgb(1, 200, 155);
    }
    .bullet.warning
    {
        background: rgb(251, 188, 5);
    }
    .bullet.danger
    {
        background: rgb(229, 15, 56);
    }

    .line
    {
        width: 1px;
        height: calc(100% - 40px);
        background: var(--bs-card-border-color);
        padding: 0;
    }
</style>

<style>
    .circled-list li {
        display: flex;
    }

    .circled-list li span {
        display: inline-block;
        margin-left: 16px;
        margin-bottom: 2rem;
    }

    .circled-list li::before {
        content: counter(list-item);
        counter-increment: list-item;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        color: white;
        border: 1px solid var(--green-color);
        border-radius: 50%;
        line-height: 28px;
        background-color: var(--green-color); /* Preenchimento branco para cobrir a lista padrão */
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>historico-de-faturas" class="fs-5 text-decoration-none text-reset">Faturas</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Detalhes da fatura</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Dados da fatura #<?php echo $sub['id']; ?></div>
    <div class="card-body row px-4 py-3">
        <div class="col-md-6">
            <ul class="mb-0">
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">ID:</small>
                    <small><?php echo $sub['id']; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Status:</small>
                    <small><?php echo $bullet . $status; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Valor:</small>
                    <small>R$ <?php echo number_format($sub['value'], 2, ',', '.'); ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Forma de Pagamento:</small>
                    <small><?php echo $billing_type; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Ciclo:</small>
                    <small><?php echo $cycle; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">E-mail para NFe:</small>
                    <small><?php echo $email; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Data da Emissão:</small>
                    <small><?php echo $start_date; ?></small>
                </li>
                <li class="d-flex justify-content-between">
                    <small class="fw-semibold">Próximo Pagamento:</small>
                    <small><?php echo $due_date; ?></small>
                </li>
            </ul>
        </div>
        <div class="col-md-6">
            <div class="card">
                <ul class="mb-0">
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Plano:</small>
                        <small><?php echo $plan['name']; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Valor:</small>
                        <small>R$ <?php echo number_format($sub['value'], 2, ',', '.'); ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Ciclo:</small>
                        <small><?php echo $cycle; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Data:</small>
                        <small><?php echo $start_date . ' até ' . $due_date; ?></small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 p-0 <?php echo ($sub['billing_type'] == "CREDIT_CARD") ? "d-none" : ""; ?> <?php echo ($sub['status'] == "RECEIVED" || $sub['status'] == "OVERDUE" || $sub['status'] == "INACTIVE") ? "d-none" : ""; ?> <?php echo ($sub['status'] == "RECEIVED" && $sub['billing_type'] == "CREDIT_CARD") ? "d-none" : ""; ?>">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Pix</div>
    <div class="card-body row px-4 py-3">
        <div class="col-md-5 d-flex align-items-center">
            <ul class="circled-list mb-0">
                <li><span>Abra o app do banco e escolha a opção PIX.</span></li>
                <li><span>Copie e cole o código ou escaneie o QR-code com a câmera do seu celular.</span></li>
                <li><span>Confira os dados e confirme o pagamento. Você vai receber a confirmação por e-mail e todos os benefícios do seu plano estarão disponíveis!</span></li>
            </ul>
        </div>
        <div class="col-md-2 d-flex align-items-center justify-content-center">
            <div class="line"></div>
        </div>
        <div class="col-md-5 d-flex flex-column align-items-center">
            <p class="fw-semibold">Validade do pagamento:</p>
            <div id="temporizador" class="fs-1 fw-semibold mb-2"></div>

            <img src="data:image/png;base64,<?php echo $sub['pix_encodedImage']; ?>" alt="QR Code Pix" class="mb-3" style="width: 250px;">

            <input type="hidden" class="form-control" id="meuInput" value="<?php echo $sub['pix_payload']; ?>">
            <button type="button" id="botaoCopiar" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center mb-3" style="height: 38px;">
                <i class='bx bxs-copy me-2'></i>
                Copiar código Pix Copia e Cola
            </button>
            <small class="text-center">Você também pode pagar escolhendo a opção Pix copia e cola no aplicativo que você usa.</small>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        // Associar um manipulador de eventos ao botão
        $("#botaoCopiar").on("click", function() {
            // Selecionar o valor do input
            var valorDoInput = $("#meuInput").val();
            
            // Criar um elemento temporário (input) para armazenar o valor
            var tempInput = $("<input>");
            $("body").append(tempInput);
            
            // Definir o valor do input temporário
            tempInput.val(valorDoInput).select();
            
            // Copiar o valor para a área de transferência
            document.execCommand("copy");
            
            // Remover o input temporário
            tempInput.remove();

            // Alerta opcional para indicar que o valor foi copiado
            alert("Valor copiado: " + valorDoInput);
        });
    });
</script>

<script>
    // Função para iniciar o temporizador de 15 minutos
    function iniciarTemporizador(produtoId) {
        // Verificar se há um valor armazenado no localStorage
        var tempoRestante = localStorage.getItem("tempoRestante_" + produtoId);

        // Se não houver valor armazenado ou o valor for menor que 0, começar de novo
        if (!tempoRestante || tempoRestante < 0) {
            tempoRestante = 900; // 15 minutos em segundos
        }

        exibirTemporizador(tempoRestante);

        // Iniciar o temporizador
        var temporizador = setInterval(function() {
            tempoRestante--;

            exibirTemporizador(tempoRestante);

            // Salvar o tempo restante no localStorage
            localStorage.setItem("tempoRestante_" + produtoId, tempoRestante);

            // Quando o temporizador chegar a zero, você pode executar alguma ação aqui
            if (tempoRestante <= 0) {
                clearInterval(temporizador);
                alert("Tempo expirado para o Cobrança ID: " + produtoId);

                // Envia o id para alterar status para cancelado
                window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/asaas/pagamento_expirado.php?shop=<?php echo $shop_id; ?>&subs=<?php echo $sub['id']; ?>";
            }
        }, 1000); // Atualizar a cada segundo
    }

    // Função para exibir o temporizador no formato "HH:MM:SS"
    function exibirTemporizador(segundos) {
        var minutos = Math.floor((segundos % 360) / 60);
        var segundosRestantes = segundos % 60;

        // Formatar as horas, minutos e segundos para o formato "MM:SS"
        var formatoMinutos = minutos < 10 ? "0" + minutos : minutos;
        var formatoSegundos = segundosRestantes < 10 ? "0" + segundosRestantes : segundosRestantes;

        document.getElementById("temporizador").innerText = formatoMinutos + ":" + formatoSegundos;
    }

    // Iniciar o temporizador quando a página carregar
    window.onload = function() {
        iniciarTemporizador(<?php echo $id; ?>);
    };
</script>

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