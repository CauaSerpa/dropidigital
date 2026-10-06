<?php
function asaas_CancelarAntigasAssinaturas($dataForm, $subscription_id, $config) {
    include('config.php');

	// Procura uma possivel assinatura anterior
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    $sql = "SELECT * FROM $tabela WHERE status IN (:status1, :status2) AND shop_id = :shop_id AND subscription_id != :current_subscription ORDER BY id DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn->prepare($sql);
	$stmt->bindValue(':status1', 'ACTIVE');
	$stmt->bindValue(':status2', 'RECEIVED');
    $stmt->bindParam(':shop_id', $dataForm["shop_id"]);
    $stmt->bindParam(':current_subscription', $subscription_id);
    $stmt->execute();

    // Recuperar os resultados
    $subs = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se existir uma assinatura anterior entra
    if ($stmt->rowCount() > 0) {
		// Alterando o status da assinatura anterior na tabela tb_subscriptions
		// Nome da tabela para a busca
		$tabela = 'tb_subscriptions';

		$sql = "UPDATE $tabela SET status = :status WHERE subscription_id = :subscription_id";

		// Preparar e executar a consulta
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(':status', 'INACTIVE');
		$stmt->bindParam(':subscription_id', $subs["subscription_id"]);
		$stmt->execute();

		// Alterar status na Asaas
		$curl = curl_init();

		$fields = [
			"status" => "INACTIVE"
		];

		curl_setopt_array($curl, array(
			CURLOPT_URL => $config['asaas_api_url'].'subscriptions/'.$subs["subscription_id"],
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
			// Status alterado com sucesso
			// echo "Status alterado na Asaas com sucesso! " . $subs["subscription_id"];
		} else {
			// echo "Erro ao alterar o status na Asaas com sucesso!";

			// echo $response;
			exit();
		}
	}
}