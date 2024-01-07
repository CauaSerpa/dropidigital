<?php
    // Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {
        // Obtenha o ID do produto e o usuário associado a ele
        $usuario_id = $id;

        // Consulta para obter o diretório da imagem
        $query = "SELECT id FROM imagens WHERE usuario_id = :usuario_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_products WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $_SESSION['msg'] = "<p class='green'>Produto deletado com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");

        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Diretório das imagens
            $diretorio = "./back-end/imagens/$usuario_id/";

            // Consulta para excluir as imagens do banco de dados
            $query = "DELETE FROM imagens WHERE usuario_id = :usuario_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();

            // Agora, exclua as imagens no diretório
            $files = glob($diretorio . "*");
            foreach ($files as $file) {
                unlink($file);
            }

            // Exclua o diretório do usuário
            rmdir($diretorio);
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum produto encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
            exit;
        }
        exit;
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um produto!';
        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
    }