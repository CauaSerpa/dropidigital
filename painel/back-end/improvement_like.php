<?php
session_start();
ob_start();
include_once('../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o IF quando o usuário clicar no botão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar se já existe um like para esta combinação de improvement_id e shop_id
        $sql_check = "SELECT * FROM tb_improvement_likes WHERE improvement_id = :improvement_id AND shop_id = :shop_id";
        $stmt_check = $conn_pdo->prepare($sql_check);
        $stmt_check->bindParam(':improvement_id', $dados['improvement_id']);
        $stmt_check->bindParam(':shop_id', $dados['shop_id']);
        $stmt_check->execute();

        // Se já existe um like, remova-o; caso contrário, adicione um novo like
        if ($stmt_check->rowCount() > 0) {
            // Já existe um like, então remova-o
            $sql_remove = "DELETE FROM tb_improvement_likes WHERE improvement_id = :improvement_id AND shop_id = :shop_id";
            $stmt_remove = $conn_pdo->prepare($sql_remove);
            $stmt_remove->bindParam(':improvement_id', $dados['improvement_id']);
            $stmt_remove->bindParam(':shop_id', $dados['shop_id']);
            $stmt_remove->execute();

            echo json_encode(['status' => 'removed']);
        } else {
            // Não existe um like, então adicione um novo like
            $sql_add = "INSERT INTO tb_improvement_likes (improvement_id, shop_id) VALUES (:improvement_id, :shop_id)";
            $stmt_add = $conn_pdo->prepare($sql_add);
            $stmt_add->bindParam(':improvement_id', $dados['improvement_id']);
            $stmt_add->bindParam(':shop_id', $dados['shop_id']);
            $stmt_add->execute();

            echo json_encode(['status' => 'success']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
    }
}