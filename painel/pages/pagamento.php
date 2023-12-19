<?php
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
    .btn
    {
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
    }
</style>

<div class="d-flex justify-content-center">
    <div class="border rounded p-4 d-flex flex-column align-items-center" style="width: 425px;">
        <p class="fw-semibold">Validade do pagamento:</p>
        <div id="temporizador" class="fs-1 fw-semibold"></div>
        <p class="fs-5 fw-semibold">Total a pagar: <span class="fs-4 text-success fw-semibold">R$ <?php echo $valor ?></span></p>

        <img src="data:image/png;base64,<?php echo $pix_encodedImage ?>" alt="QR Code Pix" style="width: 350px;">

        <div class="d-flex" style="width: 375px;">
            <div class="w-100 me-2">
                <input type="text" class="form-control" id="meuInput" value="<?php echo $pix_payload ?>" readonly>
            </div>
            <button type="submit" id="botaoCopiar" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Copiar</button>
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
        // Enviar uma solicitação AJAX para verificar o pagamento
        $.ajax({
            type: 'POST',
            url: '<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/asaas/status_pagamento.php', // Crie um arquivo PHP para lidar com a verificação
            data: { subscription_id: "<?php echo $s; ?>" },
            dataType: 'JSON',
            success: function(response) {
                if (response.status == 'pago') {
                    // Se o pagamento foi aprovado, redirecione para a página desejada
                    window.location.href = '<?php echo INCLUDE_PATH_DASHBOARD; ?>pagamento-confirmado?s=<?php echo $subscription_id; ?>';
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

    // Executar a função de consulta a cada 10 segundos
    setInterval(realizarConsulta, 10000);
</script>