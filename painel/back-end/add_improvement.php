<?php
session_start();
ob_start();
include_once('../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessa o IF quando o usuário clicar no botão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tags = isset($dados['tags']) ? json_encode($dados['tags']) : json_encode([]);

        $sql = "INSERT INTO tb_improvement (author, title, description, tags) VALUES (:author, :title, :description, :tags)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindValue(':author', $dados['user_id']);
        $stmt->bindParam(':title', $dados['title']);
        $stmt->bindParam(':description', $dados['description']);
        $stmt->bindParam(':tags', $tags);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $improvement_id = $conn_pdo->lastInsertId();

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            // Imagens
            $total = count($_FILES['images']['name']);
            $uploadDir = "improvement/$improvement_id/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
    
            for ($i = 0; $i < $total; $i++) {
                $fileName = $_FILES['images']['name'][$i];
                $tmp_name = $_FILES['images']['tmp_name'][$i];
                $uploadFile = $uploadDir . basename($fileName);
    
                if (move_uploaded_file($tmp_name, $uploadFile)) {
                    // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                    $sqlInsertImagem = "INSERT INTO tb_improvement_img (improvement_id, image_name) VALUES (:improvement_id, :image_name)";
                    $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                    $stmtInsertImagem->bindParam(':improvement_id', $improvement_id);
                    $stmtInsertImagem->bindParam(':image_name', $fileName);
    
                    if (!$stmtInsertImagem->execute()) {
                        echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar melhoria!']);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar a melhoria!']);
                    exit;
                }
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Melhoria enviada com sucesso!</p>";
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
    }
}