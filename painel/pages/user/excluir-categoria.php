<?php
    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Consulta para obter o diretório da imagem
        $query = "SELECT id, shop_id, icon, image FROM tb_categories WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Deletar imagens
            $shop_id = $row['shop_id'];

            // Diretório para salvar as imagens de 'image'
            $diretorioImage = "./back-end/category/$shop_id/image/";

            // Diretório para salvar as imagens de 'icon'
            $diretorioIcon = "./back-end/category/$shop_id/icon/";

            // Nome da imagem antiga
            $imagemAntiga = $row['image'];

            // Nome da imagem antiga
            $iconeAntigo = $row['icon'];

            // Diretório para deletar as imagens antigas de 'image'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Diretório para deletar as imagens antigas de 'icon'
            $caminhoIconeAntigo = $diretorioIcon . basename($iconeAntigo);

            // Excluir a imagem existente de 'image'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Excluir a imagem existente de 'icon'
            if (file_exists($caminhoIconeAntigo)) {
                unlink($caminhoIconeAntigo);
            }
        }

        // Consulta para excluir a categoria do banco de dados
        $query = "DELETE FROM tb_categories WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "<p class='green'>Categoria deletada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhuma categoria encontrada para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar uma categoria!';
        header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
    }