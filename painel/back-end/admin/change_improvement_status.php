<?php
session_start();
ob_start();
include_once('../../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o IF quando o usuário clicar no botão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Pesquisar produtos
        $tabela = "tb_improvement";

        // Conta o número de produtos ativos
        $sql = "SELECT status FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $dados['improvement_id']);
        $stmt->execute();
        $improvement = $stmt->fetch(PDO::FETCH_ASSOC);

        // Define o status com base nos limites de produtos
        $status = ($improvement['status'] == 1) ? 0 : 1;

        $statusAction = ($status == 1) ? "activated" : "disabled";
        $statusDescription = ($status == 1) ? "Ativar" : "Desativar";

        // Já existe um like, então remova-o
        $sql = "UPDATE $tabela SET status = :status WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $dados['improvement_id']);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'action' => $statusAction]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao ' . $statusDescription . ' o produto']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
    }
}