<?php
    include('../../../config.php');

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $asaas_url;
    $config['asaas_api_key'] = $asaas_key;

    $subscription_id = $_POST['subscription_id'];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url']."payments?invoice={$subscription_id}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: ' . $config['asaas_api_key']
        )
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $payments = json_decode($response, true);

    // Aqui, você terá informações sobre os pagamentos associados à fatura
    // print_r($payments);

    foreach ($payments['data'] as $payment) {
        $status = $payment['status'];
    
        // Verificar se o pagamento foi concluído
        if ($status === 'RECEIVED') {
            // O pagamento foi recebido, você pode prosseguir com a atualização no banco de dados
            // Chame uma função para atualizar o banco de dados com o status do pagamento
            atualizarBancoDeDados($subscription_id, 'RECEIVED');

            $paymentStatus = 'pago'; // Atualiza o status para 'pago'
        }
    }

    function atualizarBancoDeDados($subscription_id, $status) {
        include('config.php');

        // Atualize o status do pagamento na tabela do banco de dados
        $stmt = $conn->prepare("UPDATE tb_subscriptions SET status = :status WHERE subscription_id = :subscription_id");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':subscription_id', $subscription_id, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Responda ao cliente
    echo json_encode(['status' => $paymentStatus]);