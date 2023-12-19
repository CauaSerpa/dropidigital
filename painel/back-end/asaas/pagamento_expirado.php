<?php
    session_start();
    ob_start();
    include("../../../config.php");

    $shop_id = $_GET['shop'];
    $s = base64_decode($_GET['subs']);

    $plano_anterior = false;

    // Altera o status da assinatura para cancelada

    // Alterando o id do plano na tabela tb_subscriptions
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    $sql = "UPDATE $tabela SET status = :status WHERE subscription_id = :subscription_id AND shop_id = :shop_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 'OVERDUE');
    $stmt->bindValue(':subscription_id', $s);
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->execute();

    // Procura uma possivel assinatura anterior
    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    $sql = "SELECT * FROM $tabela WHERE status = :status AND shop_id = :shop_id AND subscription_id != :current_subscription ORDER BY id DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 'INACTIVE');
    $stmt->bindParam(':shop_id', $shop_id);
    $stmt->bindParam(':current_subscription', $s);
    $stmt->execute();

    // Recuperar os resultados
    $subs = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se existir uma assinatura anterior entra
    if ($stmt->rowCount() > 0) {
        // Verifica se ja passou do vencimento
        $dueDate = new DateTime($subs['due_date']);  // Substitua '2023-01-31' pela sua data de vencimento
        $today = new DateTime();

        // Comparação da data de vencimento com a data atual
        if ($dueDate >= $today) {
            // Ainda não venceu. Volta para o plano anterior.

            // Alterando o id do plano na tabela tb_shop
            // Nome da tabela para a busca
            $tabela = 'tb_shop';

            $sql = "UPDATE $tabela SET plan_id = :plan_id WHERE id = :shop_id";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':plan_id', $subs['plan_id']);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->execute();

            $plano_anterior = true;

            // Alterando o status da assinatura na tabela tb_shop
            // Nome da tabela para a busca
            $tabela = 'tb_subscriptions';

            $sql = "UPDATE $tabela SET status = :status WHERE subscription_id = :subscription_id";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':status', 'RECEIVED');
            $stmt->bindParam(':subscription_id', $subs["subscription_id"]);
            $stmt->execute();

            echo "Status atualizado com sucesso!";

            // Ativando a assinatura na Asaas
            // Alterar status na Asaas
            $curl = curl_init();

            $fields = [
                "status" => "ACTIVE"
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $asaas_url.'subscriptions/'.$subs["subscription_id"],
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
                    'access_token: '.$asaas_key
                )
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $retorno = json_decode($response, true);

            if($retorno['object'] == 'subscription') {
                // Status alterado com sucesso
                echo "Status alterado na Asaas com sucesso!";
            } else {
                echo "Erro ao alterar o status na Asaas com sucesso!";

                echo $response;
                exit();
            }
        
        } else {
            // Já venceu. Volta para o plano grátis

            // Alterando o id do plano na tabela tb_shop
            // Nome da tabela para a busca
            $tabela = 'tb_shop';

            $sql = "UPDATE $tabela SET plan_id = :plan_id WHERE id = :shop_id";

            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':plan_id', 1);
            $stmt->bindParam(':shop_id', $shop_id);
            $stmt->execute();
        }
    } else {
        // O cliente não possui nenhuma outra assinatura. Volta para o plano grátis

        // Alterando o id do plano na tabela tb_shop
        // Nome da tabela para a busca
        $tabela = 'tb_shop';

        $sql = "UPDATE $tabela SET plan_id = :plan_id WHERE id = :shop_id";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':plan_id', 1);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->execute();
    }

    if ($plano_anterior == true) {
        $_SESSION['msgcad'] = "<p class='green'>Tempo expirado! Você voltou para seu plano anterior.</p>";
        // Redireciona para a página de historico de faturas e exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "historico-de-faturas");
        exit;
    } else {
        $_SESSION['msg'] = "<p class='red'>Tempo expirado! Por favor gere uma nova cobrança.</p>";
        // Redireciona para a página de historico de faturas e exibe uma mensagem de erro
        header("Location: " . INCLUDE_PATH_DASHBOARD . "historico-de-faturas");
        exit;
    }