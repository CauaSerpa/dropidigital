<?php
session_start();
ob_start();
include_once('../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o IF quando o usuário clicar no botão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dados['other_description'] = !empty($dados['other_description']) ? $dados['other_description'] : NULL;

        // Não existe um like, então adicione um novo like
        $sql_add = "INSERT INTO tb_improvement_report (improvement_id, author, report, other_description) VALUES (:improvement_id, :author, :report, :other_description)";
        $stmt_add = $conn_pdo->prepare($sql_add);
        $stmt_add->bindParam(':improvement_id', $dados['improvement_id']);
        $stmt_add->bindParam(':author', $dados['user_id']);
        $stmt_add->bindParam(':report', $dados['report']);
        $stmt_add->bindParam(':other_description', $dados['other_description']);
        $stmt_add->execute();

        $_SESSION['msgcad'] = "<p class='green'>Sua denúncia foi enviada com sucesso!</p>";
        echo json_encode(['status' => 'success', 'message' => 'Sua denúncia foi enviada com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
    }
}