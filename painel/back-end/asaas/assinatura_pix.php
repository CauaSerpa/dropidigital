<?php
function asaas_CriarAssinaturaPix($customer_id, $dataForm, $config) {
    include('config.php');

    $curl = curl_init();

    $fields = [
        "customer" => $customer_id,
        "billingType" => "PIX",
        "description" => "Plano de assinatura",
        "value" => $dataForm["value"],
        "nextDueDate" => date('Y-m-d'),
        "cycle" => $dataForm["period"]
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url'].'subscriptions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($fields),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'access_token: '.$config['asaas_api_key']
        )
        ));

    $response = curl_exec($curl);

    curl_close($curl);

    $retorno = json_decode($response, true);

	if($retorno['object'] == 'subscription') {

        $tabela = 'tb_subscriptions';

        $stmt = $conn->prepare("INSERT INTO $tabela (shop_id, customer_id, subscription_id, value, billing_type, status, due_date, cycle) VALUES (
            :shop_id, :customer_id, :subscription_id, :value, :billing_type, :status, :due_date, :cycle)");

        // Bind dos parâmetros
        $stmt->bindParam(':shop_id', $dataForm['shop_id'], PDO::PARAM_STR);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':subscription_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
        $stmt->bindParam(':billing_type', $retorno['billingType'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $retorno['nextDueDate'], PDO::PARAM_STR);
        $stmt->bindParam(':cycle', $retorno['cycle'], PDO::PARAM_STR);

        // Executando o update
        $stmt->execute();

		return $retorno['id'];
	} else {
		echo $response;
		exit();
	}
}