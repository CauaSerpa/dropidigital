<?php
function asaas_CriarCobrancaPix($customer_id, $dataForm, $config) {
    include('config.php');

    $curl = curl_init();

    $fields = [
        "customer" => $customer_id,
        "billingType" => "PIX",
		"dueDate" => date('Y-m-d'),
        "description" => "Serviço",
        "value" => $dataForm["value"],
        "nextDueDate" => date('Y-m-d')
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => $config['asaas_api_url'].'payments',
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

	if($retorno['object'] == 'payment') {

        $tabela = 'tb_payments';

        $stmt = $conn->prepare("INSERT INTO $tabela (shop_id, order_id, customer_id, payment_id, value, billing_type, status, start_date, due_date) VALUES (
            :shop_id, :order_id, :customer_id, :payment_id, :value, :billing_type, :status, :start_date, :due_date)");

        // Bind dos parâmetros
        $stmt->bindParam(':shop_id', $dataForm['shop_id'], PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $dataForm["order_id"], PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
        $stmt->bindParam(':billing_type', $retorno['billingType'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $retorno['dateCreated'], PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $retorno['nextDueDate'], PDO::PARAM_STR);

        // Executando o update
        $stmt->execute();

		return $retorno['id'];
	} else {
		echo $response;
		exit();
	}
}