<?php
function asaas_CancelarAntigasCobrancas($subscription_id, $config) {
    include('config.php');

	$curl = curl_init();
	
	$fields = [
		"customer" => $customer_id,
		"billingType" => "CREDIT_CARD",
		"nextDueDate" => date('Y-m-d'),
		"value" => $dataForm["value"],
		"cycle" => $dataForm["period"],
		"description" => "Plano de assinatura",
		"creditCard" => [
			"holderName" => $dataForm["credit_card_owner"],
			"number" => $dataForm["credit_card_number"],
			"expiryMonth" => trim($expiry[0]),
			"expiryYear" => trim($expiry[1]),
			"ccv" => $dataForm["credit_card_ccv"]
		],
		"creditCardHolderInfo" => [
			"name" => $dataForm["name"],
			"email" => $dataForm["email"],
			"cpfCnpj" => $dataForm["cpfCnpj"],
			"postalCode" => $dataForm["postalCode"],
			"addressNumber" => $dataForm["addressNumber"],
			"phone" => $dataForm["mobilePhone"]
		]
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

        $stmt = $conn->prepare("INSERT INTO $tabela (shop_id, customer_id, subscription_id, value, billing_type, status, due_date, cycle, credit_card_number, credit_card_flag) VALUES (
            :shop_id, :customer_id, :subscription_id, :value, :billing_type, :status, :due_date, :cycle, :credit_card_number, :credit_card_flag)");
        
        // Bind dos parâmetros
        $stmt->bindParam(':shop_id', $dataForm["shop_id"], PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':subscription_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
        $stmt->bindParam(':billing_type', $retorno['billingType'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $retorno['nextDueDate'], PDO::PARAM_STR);
        $stmt->bindParam(':cycle', $retorno['cycle'], PDO::PARAM_STR);
        $stmt->bindParam(':credit_card_number', $retorno['creditCard']['creditCardNumber'], PDO::PARAM_STR);
    	$stmt->bindParam(':credit_card_flag', $retorno['creditCard']['creditCardBrand'], PDO::PARAM_STR);
    
        // Executando o update
        $stmt->execute();

		return $retorno['id'];
	} else {
		echo $response;
		exit();
	}
}