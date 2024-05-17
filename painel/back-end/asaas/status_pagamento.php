<?php
    include('../../../config.php');

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $asaas_url;
    $config['asaas_api_key'] = $asaas_key;

    if (isset($_POST['subscription_id'])) {
        $id = $_POST['subscription_id'];
    } else {
        $id = $_POST['payment_id'];
    }

    $paymentStatus = "";

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url']."payments?invoice={$id}",
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
        if (isset($_POST['subscription_id'])) {
            $asaas_id = @$payment['subscription'];
        } else {
            $asaas_id = $payment['id'];
        }
        $status = $payment['status'];
    
        // Verificar se o pagamento foi concluído
        if ($asaas_id == $id && $status == 'RECEIVED') {
            include_once("../copy_site_shop.php");
            copyReadySiteToShop($_POST['params']);

            // O pagamento foi recebido, você pode prosseguir com a atualização no banco de dados
            // Chame uma função para atualizar o banco de dados com o status do pagamento
            atualizarBancoDeDados($id, 'RECEIVED');

            $paymentStatus = 'pago'; // Atualiza o status para 'pago'
        }
    }

    function atualizarBancoDeDados($id, $status) {
        include('config.php');

        if (isset($_POST['subscription_id'])) {
            // Atualize o status do pagamento na tabela do banco de dados
            $stmt = $conn->prepare("UPDATE tb_subscriptions SET status = :status WHERE subscription_id = :id");
        } else {
            // Atualize o status do pagamento na tabela do banco de dados
            $stmt = $conn->prepare("UPDATE tb_payments SET status = :status WHERE payment_id = :id");
        }

        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Responda ao cliente
    echo json_encode(['status' => $paymentStatus]);