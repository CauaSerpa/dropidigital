<?php
    // Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {
        // Obtenha o ID do produto e o usuário associado a ele
        $service_id = $id;

        // Consulta para obter o diretório da imagem
        $query = "SELECT id FROM tb_service_img WHERE service_id = :service_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_services WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $service_id);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_site_service WHERE service_id = :service_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':service_id', $service_id);
        $stmt->execute();

        $_SESSION['msg'] = "<p class='green'>Serviço deletado com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");

        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Diretório das imagens
            $diretorio = "./back-end/admin/service/$service_id/";

            // Consulta para excluir as imagens do banco de dados
            $query = "DELETE FROM tb_service_img WHERE service_id = :service_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':service_id', $service_id);
            $stmt->execute();

            // Agora, exclua as imagens no diretório
            $files = glob($diretorio . "*");
            foreach ($files as $file) {
                unlink($file);
            }

            // Exclua o diretório do usuário
            rmdir($diretorio);
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum Serviço encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");
            exit;
        }
        exit;
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um Serviço!';
        header("Location: " . INCLUDE_PATH_ADMIN . "servicos");
    }