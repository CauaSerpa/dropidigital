<?php
function asaas_CriarCobrancaCartao($customer_id, $dataForm, $config) {
    include('config.php');

	$expiry = explode("/", $dataForm["credit_card_expiration"]);

	// Passando valor do cpfCnpj
	$dataForm['cpfCnpj'] = $dataForm['docNumber'];

    // Installments
	$installment = explode("|", $dataForm["installment"]);
    $installmentCount = trim($installment[0]);
    $installmentValue = trim($installment[1]);

	// print_r($dataForm);

	$curl = curl_init();
	
	$fields = [
		"customer" => $customer_id,
		"billingType" => "CREDIT_CARD",
		"dueDate" => date('Y-m-d'),
		"value" => $dataForm["value"],
		"description" => "Serviço",
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

    if ($installmentCount != 1) {
        $fields['installmentCount'] = $installmentCount;
        $fields['installmentValue'] = $installmentValue;
    }

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

        $stmt = $conn->prepare("INSERT INTO $tabela (shop_id, order_id, customer_id, payment_id, value, billing_type, status, start_date, due_date, credit_card_number, credit_card_flag) VALUES (
            :shop_id, :order_id, :customer_id, :payment_id, :value, :billing_type, :status, :start_date, :due_date, :credit_card_number, :credit_card_flag)");
        
        // Bind dos parâmetros
        $stmt->bindParam(':shop_id', $dataForm["shop_id"], PDO::PARAM_INT);
        $stmt->bindParam(':order_id', $dataForm["order_id"], PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindParam(':payment_id', $retorno['id'], PDO::PARAM_STR);
        $stmt->bindParam(':value', $retorno['value'], PDO::PARAM_STR);
        $stmt->bindParam(':billing_type', $retorno['billingType'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $retorno['status'], PDO::PARAM_STR);
        $stmt->bindParam(':start_date', $retorno['dateCreated'], PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $retorno['nextDueDate'], PDO::PARAM_STR);
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