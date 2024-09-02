<?php
session_start();
ob_start();
include_once('../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o IF quando o usuário clicar no botão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Nome da tabela para a busca
        $tabela = 'tb_subscriptions';

        // Consulta SQL para contar os produtos na tabela
        $sql = "SELECT plan_id FROM $tabela WHERE (status = :status OR status = :status1) AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
        $stmt->bindValue(':status', 'ACTIVE');
        $stmt->bindValue(':status1', 'RECEIVED');
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->execute();

        // Recupere o resultado da consulta
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        $plan_id = (isset($plan['plan_id'])) ? $plan['plan_id'] : 1;

        // Pesquisar plano da Loja
        $tabela = "tb_plans_interval";

        // Consulta SQL para obter o plano da loja
        $sql = "SELECT plan_id FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);
        $stmt->execute();
        $shop = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pesquisar produtos
        $tabela = "tb_products";

        // Conta o número de produtos ativos
        $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela
                        WHERE shop_id = :shop_id AND status = :status";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->bindValue(':status', 1);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Define os limites de produtos com base no plano
        $limitProductsMap = [
            1 => 10,
            2 => 50,
            3 => 250,
            4 => 750,
            5 => 5000,
        ];

        $limitProducts = $limitProductsMap[$shop['plan_id']] ?? "ilimitado";

        if ($limitProducts <= $product['total_produtos']) {
            echo json_encode(['status' => 'error', 'message' => 'O limite de produtos ativos foi atingido.']);
            exit;
        }

        // Pesquisar produtos
        $tabela = "tb_products";

        // Conta o número de produtos ativos
        $sql = "SELECT status FROM $tabela WHERE id = :id AND shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $dados['product_id']);
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Define o status com base nos limites de produtos
        $status = ($product['status'] == 1) ? 0 : 1;

        $statusAction = ($status == 1) ? "activated" : "disabled";
        $statusDescription = ($status == 1) ? "Ativar" : "Desativar";

        // Já existe um like, então remova-o
        $sql = "UPDATE $tabela SET status = :status WHERE id = :id AND shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $dados['product_id']);
        $stmt->bindParam(':shop_id', $dados['shop_id']);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'action' => $statusAction]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao ' . $statusDescription . ' o produto']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
    }
}