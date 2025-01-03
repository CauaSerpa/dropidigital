<?php
    $shop_id = $id;

    if (isset($_GET['s'])) {
        $subscription_id = $_GET['s'];

        $s = base64_decode($subscription_id);
        
        // Consulta SQL
        $sql = "SELECT * FROM tb_subscriptions WHERE subscription_id = :s AND shop_id = :shop_id";
    } else {
        $payment_id = $_GET['p'];

        $p = base64_decode($payment_id);

        // Consulta SQL
        $sql = "SELECT * FROM tb_payments WHERE payment_id = :p AND shop_id = :shop_id";
    }

    // Preparação da declaração PDO
    $stmt = $conn_pdo->prepare($sql);

    // Bind do valor do ID
    if (isset($_GET['s'])) {
        $stmt->bindParam(':s', $s);
    } else {
        $stmt->bindParam(':p', $p);
    }

    $stmt->bindParam(':shop_id', $id);

    // Execução da consulta
    $stmt->execute();

    // Obtenção do resultado
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificação se a consulta retornou algum resultado
    if ($result) {
        $id = $result['id'];
        $valor = $result['value'];
        $status = $result['status'];
        $pix_encodedImage = $result['pix_encodedImage'];
        $pix_payload = $result['pix_payload'];
        $pix_expirationDate = $result['pix_expirationDate'];
    } else {
        echo "Nenhum resultado encontrado.";
    }

    if ($status == "RECEIVED" || $status == "OVERDUE" || $status == "INACTIVE")
    {
        $_SESSION['msgcad'] = "<p class='red'>A cobrança já foi fechada!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "historico-de-faturas");
        exit;
    }
?>

<style>
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
</style>

<div class="d-flex justify-content-center">
    <div class="border rounded p-4 d-flex flex-column align-items-center" style="width: 425px;">
        <p class="fw-semibold">Validade do pagamento:</p>
        <div id="temporizador" class="fs-1 fw-semibold"></div>
        <p class="fs-5 fw-semibold">Total a pagar: <span class="fs-4 text-success fw-semibold">R$ <?php echo number_format($valor, 2, ",", ".") ?></span></p>

        <img src="data:image/png;base64,<?php echo $pix_encodedImage ?>" alt="QR Code Pix" style="width: 350px;">

        <div class="d-flex" style="width: 375px;">
            <div class="w-100 me-2">
                <input type="text" class="form-control" id="meuInput" value="<?php echo $pix_payload ?>" readonly>
            </div>
            <button type="submit" id="botaoCopiar" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Copiar</button>
        </div>
    </div>
</div>


<style>
    .loader {
        width: 32px;
        height: 32px;
        border: 2.5px solid var(--green-color);
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

                // Remover o item do localStorage
                localStorage.removeItem("tempoRestante_" + produtoId);

                // Envia o id para alterar status para cancelado
                window.location.href = "<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/asaas/pagamento_expirado.php?shop=<?php echo $shop_id; ?>&subs=<?php echo $subscription_id; ?>";
            }
        }, 1000); // Atualizar a cada segundo
    }

    // Função para exibir o temporizador no formato "HH:MM:SS"
    function exibirTemporizador(segundos) {
        var minutos = Math.floor((segundos % 3600) / 60);
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

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    // Função para realizar a consulta AJAX
    function realizarConsulta() {
        <?php
            if ($_GET['site']) {
                if (isset($p)) {
                    $payment = "payment_id: '$p'";
                } else {
                    $payment = "subscription_id: '$s'";
                }
        ?>
            var params = {
                shop_id: <?php echo $shop_id; ?>,
                ready_site_id: <?php echo $_GET['site']; ?>
            };
        <?php
                $ajaxData = "{ params: params, $payment }";
            } else {
                if (isset($p)) {
                    $ajaxData = "payment_id: '$p'";
                } else {
                    $ajaxData = "subscription_id: '$s'";
                }
            }
        ?>

        // Enviar uma solicitação AJAX para verificar o pagamento
        $.ajax({
            type: 'POST',
            url: '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/asaas/status_pagamento.php', // Crie um arquivo PHP para lidar com a verificação
            data: <?php echo $ajaxData; ?>,
            dataType: 'JSON',
            success: function(response) {
                if (response.status == 'pago') {
                    clearInterval(intervalId);  // Parar o intervalo
                    
                    $('#warningModal').modal('show');
                    
                    var redirect = <?= (isset($_GET['r']) == 1) ? 1 : 0; ?>;

                    <?php
                        if (!isset($_GET['s'])) {
                    ?>


                    var params = {
                        shop_id: <?php echo $shop_id; ?>,
                        ready_site_id: <?php echo $_GET['site']; ?>
                    };

                    // Enviar uma solicitação AJAX para verificar o pagamento
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/copy_site_shop.php', // Crie um arquivo PHP para lidar com a verificação
                        data: params,
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.status == 'sucesso') {
                                if (redirect == 1) {
                                    // Se o pagamento foi aprovado, redirecione para a página desejada
                                    window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano-asaas?p=<?php echo $plan_id; ?>&r=1';
                                } else {
                                    // Se o pagamento foi aprovado, redirecione para a página desejada
                                    window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>pagamento-confirmado?<?php echo (isset($payment_id)) ? "p=$payment_id" : "s=$subscription_id"; ?>';
                                }
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



                    if (redirect == 1) {
                        // Se o pagamento foi aprovado, redirecione para a página desejada
                        window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano-asaas?p=<?php echo $plan_id; ?>&r=1';
                    } else {
                        // Se o pagamento foi aprovado, redirecione para a página desejada
                        window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>pagamento-confirmado?<?php echo (isset($payment_id)) ? "p=$payment_id" : "s=$subscription_id"; ?>';
                    }




                    <?php
                        }
                    ?>
                } else {
                    // Se o pagamento não foi aprovado, você pode tomar alguma ação aqui
                    console.log('O pagamento ainda não foi aprovado.');
                }
            },
            error: function(error) {
                console.error('Erro ao verificar o pagamento:', error);
            }
        });
    }

    // Guardar o ID do intervalo para poder pará-lo posteriormente
    var intervalId = setInterval(realizarConsulta, 10000);
</script>