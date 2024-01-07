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

    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        $sql = "INSERT INTO tb_banner_info (shop_id, name, location, category, link, target, title, status) VALUES 
                                    (:shop_id, :name, :location, :category, :link, :target, :title, :status)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':location', $dados['location']);
        $stmt->bindParam(':category', $dados['category']);
        $stmt->bindParam(':link', $dados['link']);
        $stmt->bindParam(':target', $dados['target']);
        $stmt->bindParam(':title', $dados['title']);
        $stmt->bindParam(':status', $status);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $conn_pdo->lastInsertId();

        // Processar o upload de image
        $uploadDir = "banners/$ultimo_id/";

        // Criar o diretório
        mkdir($uploadDir, 0755);

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
            // Cadastra imagem para 'image'
            $fileName = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sql = "INSERT INTO tb_banner_img (banner_id, image_name) VALUES (:banner_id, :image_name)";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':banner_id', $ultimo_id);
                $stmt->bindParam(':image_name', $fileName);

                $stmt->execute();
            }
        }

        print_r($_FILES['image']);

        $_SESSION['msgcad'] = "<p class='green'>Banner criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao criar o banner!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
    }