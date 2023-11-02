<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($_POST['status']) && $_POST['status'] == '1') {
        $status = $_POST['status'];
    } else {
        $status = 0;
    }

    if (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') {
        $emphasis = $_POST['emphasis'];
    } else {
        $emphasis = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_articles';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET status = :status, emphasis = :emphasis, name = :name, content = :content, link = :link, seo_name = :seo_name, seo_link = :seo_link, seo_description = :seo_description WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':emphasis', $emphasis);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':content', $dados['content']);
        $stmt->bindParam(':link', $dados['link']);
        $stmt->bindParam(':seo_name', $dados['seo_name']);
        $stmt->bindParam(':seo_link', $dados['seo_link']);
        $stmt->bindParam(':seo_description', $dados['seo_description']);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();
        
        // Recupere o ID da categoria
        $id = $dados['id'];

        // Consulta para obter o nome das imagens
        $query = "SELECT (image) FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        // Obtenha o nome das imagens
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./articles/$id/";

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
            // Nome da imagem antiga
            $imagemAntiga = $row['image'];

            // Diretório para deletar as imagens antigas de 'image'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Excluir a imagem existente de 'image'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Cadastra nova imagem para 'image'
            $fileName = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sql = "UPDATE $tabela SET image = :image WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':image', $fileName);
                
                // Id que sera editado
                $stmt->bindParam(':id', $id);

                $stmt->execute();
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Artigo editado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar o artigo!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
    }