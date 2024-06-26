<?php

function asaas_ObterQRCodePix($subscription_id, $payment_id, $config) {
    include('config.php');

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

        if ($subscription_id !== false) {
            $tabela = 'tb_subscriptions';
        } else {
            $tabela = 'tb_payments';
        }

        if ($subscription_id !== false) {
            $stmt = $conn->prepare("UPDATE $tabela SET pix_expirationDate = :pix_expirationDate, pix_encodedImage = :pix_encodedImage, pix_payload = :pix_payload WHERE subscription_id = :subscription_id");
        } else {
            $stmt = $conn->prepare("UPDATE $tabela SET pix_expirationDate = :pix_expirationDate, pix_encodedImage = :pix_encodedImage, pix_payload = :pix_payload WHERE payment_id = :payment_id");
        }

        // Bind dos parâmetros
        $stmt->bindValue(':pix_expirationDate', $retorno['expirationDate']);
        $stmt->bindValue(':pix_encodedImage', $retorno['encodedImage']);
        $stmt->bindValue(':pix_payload', $retorno['payload']);

        if ($subscription_id !== false) {
            $stmt->bindValue(':subscription_id', $subscription_id);
        } else {
            $stmt->bindValue(':payment_id', $payment_id);
        }

        // Executando o update
        $stmt->execute();

    } else {
        echo $response;
        exit();
    }
}