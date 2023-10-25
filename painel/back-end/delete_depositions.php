<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $selectedId) {
            // Certifique-se de que o ID seja um número inteiro válido
            $categoryId = (int) $selectedId;

            // Verifique se o ID é maior que zero (um ID válido)
            if ($categoryId > 0) {
                // Consulta para obter o diretório da imagem
                $query = "SELECT id, shop_id, img FROM tb_depositions WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $categoryId);
                $stmt->execute();
        
                if ($stmt->rowCount() > 0) {
                    // Obtenha o ID do usuário
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    // Deletar imagens
                    $shop_id = $row['shop_id'];
        
                    // Diretório para salvar as imagens de 'img'
                    $diretorioImage = "./depositions/";
        
                    // Nome da imagem antiga
                    $imagemAntiga = $row['img'];

                    // Diretório para deletar as imagens antigas de 'img'
                    $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

                    // Excluir a imagem existente de 'img'
                    if (file_exists($caminhoImagemAntiga)) {
                        unlink($caminhoImagemAntiga);
                    }
                }

                // Consulta para excluir a depoimento do banco de dados
                $query = "DELETE FROM tb_depositions WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $categoryId);

                if ($stmt->execute()) {
                    $_SESSION['msg'] = "<p class='green'>Depoimento deletado com sucesso!</p>";
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao deletar depoimento.</p>";
                }
            }
        }

        // Redirecionar após a exclusão
        header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
    } else {
        $_SESSION['msg'] = "<p class='red'>Nenhuma depoimento selecionada para exclusão.</p>";
        // Redirecionar se nenhum ID de depoimento foi passado
        header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
        exit;
    }