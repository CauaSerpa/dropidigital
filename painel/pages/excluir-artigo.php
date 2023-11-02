<?php

    //Apagar Card
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(!empty($id)){
        $tabela = "tb_articles";

        // Consulta para obter o diretório da imagem
        $query = "SELECT image FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Obtenha o ID do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nome da imagem antiga
        $image = $row['image'];

        // Diretório do banner
        $diretorio = "./back-end/articles/$id/";
        
        // Diretório para deletar as imagens antigas de 'image'
        $caminhoImagem = $diretorio . basename($image);
        
        // Excluir a imagem existente de 'image'
        if (file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }

        // Exclua o diretório do usuário
        rmdir($diretorio);

        // Consulta para excluir o banner do banco de dados
        $query = "DELETE FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $_SESSION['msg'] = "<p class='green'>Artigo deletado com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
        exit;
    } else {
        // Mensagem de falha
        $_SESSION['msgcad'] = 'É necessário selecionar um artigo!';
        header("Location: " . INCLUDE_PATH_ADMIN . "artigo");
        exit;
    }