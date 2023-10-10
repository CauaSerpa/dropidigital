<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_depositions';
        
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];
        $img = $_POST['img'];
        $name = $_POST['name'];
        $testimony = $_POST['testimony'];
        $qualification = $_POST['qualification'];

        // Processar o upload de imagens
        $uploadDir = "depositions/";

        $fileName = $_FILES['img']['name'];
        $uploadFile = $uploadDir . basename($fileName);

        if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadFile)) {
            // Inserir informações da imagem no banco de dados, associando-a ao registro principal
            // Insere a categoria no banco de dados
            $sql = "INSERT INTO $tabela (shop_id, img, name, testimony, qualification) VALUES 
                                        (:shop_id, :img, :name, :testimony, :qualification)";
            $stmt = $conn_pdo->prepare($sql);

            // Substituir os links pelos valores do formulário
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->bindValue(':img', $fileName);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':testimony', $testimony);
            $stmt->bindValue(':qualification', $qualification);

            if ($stmt->execute()) {
                $_SESSION['msgcad'] = "<p class='green'>Depoimento cadastrado com sucesso!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar o depoimento!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
                exit;
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar o depoimento!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
            exit;
        }
    }