<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        // Obtenha o ID do banner e o usuário associado a ele
        $banner_id = $id;
    
        // Consulta para obter o diretório da imagem
        $query = "SELECT id FROM tb_banner_img WHERE banner_id = :banner_id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':banner_id', $banner_id);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            // Obtenha o ID do usuário
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Diretório do banner
            $diretorio = "./back-end/banners/$banner_id/";
    
            // Consulta para excluir a banner do banco de dados
            $query = "DELETE FROM tb_banner_img WHERE banner_id = :banner_id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':banner_id', $banner_id);
            $stmt->execute();
    
            // Consulta para excluir o banner do banco de dados
            $query = "DELETE FROM tb_banner_info WHERE id = :id";
            $stmt = $conn_pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            // Agora, exclua o banner no diretório
            $files = glob($diretorio . "*");
            foreach ($files as $file) {
                unlink($file);
            }
    
            // Exclua o diretório do usuário
            rmdir($diretorio);
    
            $_SESSION['msg'] = "<p class='green'>Banner deletado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhum banner encontrado para exclusão.</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
            exit;
        }
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um banner!';
        header("Location: " . INCLUDE_PATH_ADMIN . "sobre");
    }