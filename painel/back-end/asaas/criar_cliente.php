<?php
	function asaas_CriarCliente($dataForm, $config) {
		include('config.php');

		// Criando novo array para nao alterar o principal
		$filteredDataForm = $dataForm;

		// Removendo campos que não serão usados para incluir o cliente
		unset($filteredDataForm['period']);
		unset($filteredDataForm['type']);
		unset($filteredDataForm['installment']);
		unset($filteredDataForm['id_plan']);
		unset($filteredDataForm['value']);
		unset($filteredDataForm['credit_card_number']);
		unset($filteredDataForm['credit_card_owner']);
		unset($filteredDataForm['credit_card_expiration']);
		unset($filteredDataForm['credit_card_ccv']);
		unset($filteredDataForm['shop_id']);

		// Passando valor do cpfCnpj
		$filteredDataForm['cpfCnpj'] = $filteredDataForm['docNumber'];

		unset($filteredDataForm['docType']);
		unset($filteredDataForm['docNumber']);
		unset($filteredDataForm['city']);
		unset($filteredDataForm['state']);
    
		// Tabela que sera feita a consulta
		$tabela = "tb_invoice_info";

		// Consulta SQL
		$sql = "SELECT customer_id FROM $tabela WHERE email = :email";

		// Preparar a consulta
		$stmt = $conn->prepare($sql);

		// Vincular o valor do parâmetro
		$stmt->bindParam(':email', $dataForm["email"], PDO::PARAM_STR);

		// Executar a consulta
		$stmt->execute();

		// Obter o resultado como um array associativo
		$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

		// Verifica se a consulta retornou algum resultado
		if ($stmt->rowCount() > 0) {
			// Verificar se o resultado foi encontrado
			if ($resultado) {
				// Atribuir o valor da coluna à variável, ex.: "nome" = $nome
				$retorno['id'] = $resultado['customer_id'];
				return $retorno['id'];
			}
		} else {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $config['asaas_api_url'].'customers',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($filteredDataForm),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'access_token: '.$config['asaas_api_key']
				)
			));
			$response = curl_exec($curl);
			curl_close($curl);

			$retorno = json_decode($response, true);

			if($retorno['object'] == 'customer') {

				$tabela = 'tb_invoice_info';

				$stmt = $conn->prepare("INSERT INTO $tabela (shop_id, customer_id, name, email, phone, docType, docNumber, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES (
					:shop_id, :id, :name, :email, :phone, :docType, :docNumber, :cep, :address, :addressNumber, :complement, :province, :city, :state)");

				// Bind dos parâmetros
				$stmt->bindParam(':shop_id', $dataForm['shop_id'], PDO::PARAM_STR);
				$stmt->bindParam(':id', $retorno['id'], PDO::PARAM_STR);
				$stmt->bindParam(':name', $retorno['name'], PDO::PARAM_STR);
				$stmt->bindParam(':email', $retorno['email'], PDO::PARAM_STR);
				$stmt->bindParam(':phone', $dataForm['mobilePhone'], PDO::PARAM_STR);
				$stmt->bindParam(':docType', $dataForm['docType'], PDO::PARAM_STR);
				$stmt->bindParam(':docNumber', $dataForm['docNumber'], PDO::PARAM_STR);
				$stmt->bindParam(':cep', $dataForm['postalCode'], PDO::PARAM_STR);
				$stmt->bindParam(':address', $retorno['address'], PDO::PARAM_STR);
				$stmt->bindParam(':addressNumber', $retorno['addressNumber'], PDO::PARAM_INT);
				$stmt->bindParam(':complement', $retorno['complement'], PDO::PARAM_STR);
				$stmt->bindParam(':province', $retorno['province'], PDO::PARAM_STR);
				$stmt->bindParam(':city', $dataForm['city'], PDO::PARAM_STR);
				$stmt->bindParam(':state', $dataForm['state'], PDO::PARAM_STR);

				// Executando o update
				$stmt->execute();

				return $retorno['id'];
			} else {
				echo $response;
				exit();
			}
		}
	}