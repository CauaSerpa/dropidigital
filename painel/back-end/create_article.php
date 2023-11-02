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
        $sql = "INSERT INTO $tabela (shop_id, status, emphasis, name, link, content, seo_name, seo_link, seo_description) VALUES 
                                    (:shop_id, :status, :emphasis, :name, :link, :content, :seo_name, :seo_link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['id']);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':emphasis', $emphasis);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':link', $dados['link']);
        $stmt->bindParam(':content', $dados['content']);
        $stmt->bindParam(':seo_name', $dados['seo_name']);
        $stmt->bindParam(':seo_link', $dados['seo_link']);
        $stmt->bindParam(':seo_description', $dados['seo_description']);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $conn_pdo->lastInsertId();

        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./articles/$ultimo_id/";

        // Cria o diretório com o id do artigo
        mkdir($diretorioImage, 0755, true);

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
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
                $stmt->bindParam(':id', $ultimo_id);

                $stmt->execute();
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Artigo criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao criar o artigo!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
    }