<?php
    include('../../../config.php');

    // Acessa as variáveis de ambiente
    $config['asaas_api_url'] = $asaas_url;
    $config['asaas_api_key'] = $asaas_key;

    include('../asaas/config.php');

    // Metodo de pagamento
    $billing_type = "PIX";

    // Data
    $current_date = date('d/m/Y');

    // Pesquisa pelas assinaturas que ja venceram ou vao vencer hoje
    $tabela = "tb_subscriptions";

    $subscription_id = "sub_6o75jawm18jytgpe";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE subscription_id = :subscription_id AND billing_type = :billing_type AND due_date > :current_date ORDER BY id DESC LIMIT 1";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':subscription_id', $subscription_id);
    $stmt->bindParam(':billing_type', $billing_type);
    $stmt->bindParam(':current_date', $current_date);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cria um foreach com os resultados
    if ($subscription) {
        echo "Subs ".$subscription['subscription_id']."<br><br>";
        // Recria a assinatura
        // Chama arquivos para criação da nova assinatura
        function verificarFaturasNaoPagas($subscription_id, $config) {
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
                CURLOPT_URL => $config['asaas_api_url'].'subscriptions/'.$subscription_id.'/payments', // Consulta faturas vencidas
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'access_token: '.$config['asaas_api_key']
                )
            ));
        
            $response = curl_exec($curl);
        
            curl_close($curl);
        
            $faturasNaoPagas = json_decode($response, true);
        
            return $faturasNaoPagas;
        }

        function gerarQrCode($payment_id, $config) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $config["asaas_api_url"]."payments/$payment_id/pixQrCode",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'access_token: '.$config["asaas_api_key"]
                )
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $retorno = json_decode($response, true);

            if($retorno["success"] == true) {
        
                return $retorno;

            } else {
                echo $response;
                exit();
            }
        }
        
        $subscription_id = $subscription['subscription_id']; // Substitua pelo ID da assinatura desejada
        
        $faturasNaoPagas = verificarFaturasNaoPagas($subscription_id, $config);

        echo "<pre>";
        print_r($faturasNaoPagas);
        echo "</pre>";
        echo "<br><br>";
        
        if (!empty($faturasNaoPagas)) {
            echo "Faturas não pagas encontradas:<br><br>";
            foreach ($faturasNaoPagas['data'] as $fatura) {
                if ($fatura['status'] == 'PENDING' || $fatura['status'] == 'OVERDUE') {
                    echo "ID da Fatura: ".$fatura['id']."<br>";
                    echo "Valor: ".$fatura['value']."<br>";
                    echo "Data de Vencimento: ".$fatura['dueDate']."<br>";
                    echo "Status: ".$fatura['status']."<br>";

                    $qr_code = gerarQrCode($fatura['id'], $config);
                    echo "Qr Code: <img src='data:image/png;base64,".$qr_code['encodedImage']."' alt='QR Code Pix' style='width: 350px;'><br><br>";

                }
            }
        } else {
            echo "Nenhuma fatura não paga encontrada.<br>";
        }
    }