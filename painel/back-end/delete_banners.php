<?php
    session_start();
    ob_start();

    include_once('../config.php');
    
    if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        // Loop através dos IDs selecionados e exclua as linhas correspondentes
        foreach ($_POST['selected_ids'] as $selectedId) {
            // Consulta para obter o diretório da imagem
            $query = "SELECT id FROM tb_banner_img WHERE banner_id = :banner_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':banner_id', $selectedId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Obtenha o ID do usuário
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Diretório das imagens
                $diretorio = "./banners/$selectedId/";

                // Consulta para excluir as imagens do banco de dados
                $query = "DELETE FROM tb_banner_img WHERE banner_id = :banner_id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':banner_id', $selectedId);
                $stmt->execute();

                // Consulta para excluir o produto do banco de dados
                $query = "DELETE FROM tb_banner_info WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $selectedId);
                $stmt->execute();

                // Agora, exclua as imagens no diretório
                $files = glob($diretorio . "*");
                foreach ($files as $file) {
                    unlink($file);
                }

                // Exclua o diretório do usuário
                rmdir($diretorio);

                $_SESSION['msg'] = "<p class='green'>Banners deletados com sucesso!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Nenhum banner encontrado para exclusão.</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
                exit;
            }
        }
    }