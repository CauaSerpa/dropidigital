<?php
    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Consulta para obter o diretório da imagem
        $query = "SELECT id, shop_id, img FROM tb_depositions WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Deletar imagens
            $shop_id = $row['shop_id'];

            // Diretório para salvar as imagens de 'img'
            $diretorioImage = "./back-end/depositions/";

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
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Depoimento deletado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum depoimento encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um depoimento!';
        header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
    }