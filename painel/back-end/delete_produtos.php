<?php
    session_start();
    ob_start();

    include_once('../config.php');
    
    if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        // Loop através dos IDs selecionados e exclua as linhas correspondentes
        foreach ($_POST['selected_ids'] as $selectedId) {
            // Consulta para obter o diretório da imagem
            $query = "SELECT id FROM imagens WHERE usuario_id = :usuario_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':usuario_id', $selectedId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Obtenha o ID do usuário
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Diretório das imagens
                $diretorio = "./imagens/$selectedId/";

                // Consulta para excluir as imagens do banco de dados
                $query = "DELETE FROM imagens WHERE usuario_id = :usuario_id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':usuario_id', $selectedId);
                $stmt->execute();

                // Consulta para excluir o produto do banco de dados
                $query = "DELETE FROM tb_products WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // Agora, exclua as imagens no diretório
                $files = glob($diretorio . "*");
                foreach ($files as $file) {
                    unlink($file);
                }

                // Exclua o diretório do usuário
                rmdir($diretorio);

                $_SESSION['msg'] = "<p class='green'>Produtos deletados com sucesso!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Nenhum produto encontrado para exclusão.</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                exit;
            }
        }
    }