<?php
    session_start();
    ob_start();
    include_once('../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        $sql = "UPDATE $tabela SET logo = :logo, logo_mobile = :logo_mobile, favicon = :favicon WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':logo', $dados['logo']);
        $stmt->bindParam(':logo_mobile', $dados['logo_mobile']);
        $stmt->bindParam(':favicon', $dados['favicon']);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['shop_id']);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $dados['shop_id'];

        // Processar o upload de imagens
        $uploadDir = "logos/$ultimo_id/";

        foreach ($_FILES['logo']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['logo']['name'][$key];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sql = "UPDATE $tabela SET logo = :logo WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);

                // Substituir os links pelos valores do formulário
                $stmt->bindParam(':logo', $fileName);

                // Id que sera editado
                $stmt->bindValue(':id', $dados['shop_id']);

                $stmt->execute();
            }
        }

        foreach ($_FILES['logo_mobile']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['logo_mobile']['name'][$key];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                $sql = "UPDATE $tabela SET logo_mobile = :logo_mobile WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
        
                // Substituir os links pelos valores do formulário
                $stmt->bindParam(':logo_mobile', $fileName);

                // Id que sera editado
                $stmt->bindValue(':id', $dados['shop_id']);

                $stmt->execute();
            }
        }

        foreach ($_FILES['favicon']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['favicon']['name'][$key];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                $sql = "UPDATE $tabela SET favicon = :favicon WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
        
                // Substituir os links pelos valores do formulário
                $stmt->bindParam(':favicon', $fileName);

                // Id que sera editado
                $stmt->bindValue(':id', $dados['shop_id']);

                $stmt->execute();
            }
        }
        
        $_SESSION['msgcad'] = "<p class='green'>Logo adicionada com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "logo");
        exit;
    }