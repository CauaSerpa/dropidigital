<?php
    // Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {
        // Obtenha o ID do produto e o usuário associado a ele
        $ready_site_id = $id;
        
        // Consulta para obter o diretório da imagem
        $query = "SELECT shop_id FROM tb_ready_sites WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Obtenha o Shop ID
        $site = $stmt->fetch(PDO::FETCH_ASSOC);

        // Consulta para obter o diretório da imagem
        $query = "SELECT id FROM tb_ready_site_img WHERE ready_site_id = :ready_site_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':ready_site_id', $ready_site_id);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_ready_sites WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_shop WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $site['shop_id']);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_domains WHERE shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':shop_id', $site['shop_id']);
        $stmt->execute();

        // Consulta para excluir o produto do banco de dados
        $query = "DELETE FROM tb_site_services WHERE ready_site_id = :ready_site_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':ready_site_id', $id);
        $stmt->execute();

        $_SESSION['msg'] = "<p class='green'>Site Pronto deletado com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");

        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Diretório das imagens
            $diretorio = "./back-end/admin/ready-website/$ready_site_id/";

            // Consulta para excluir as imagens do banco de dados
            $query = "DELETE FROM tb_ready_site_img WHERE ready_site_id = :ready_site_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':ready_site_id', $ready_site_id);
            $stmt->execute();

            // Agora, exclua as imagens no diretório
            $files = glob($diretorio . "*");
            foreach ($files as $file) {
                unlink($file);
            }

            // Exclua o diretório do usuário
            rmdir($diretorio);
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum Site Pronto encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "sites-prontos");
            exit;
        }
        exit;
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um Site Pronto!';
        header("Location: " . INCLUDE_PATH_ADMIN . "sites-prontos");
    }