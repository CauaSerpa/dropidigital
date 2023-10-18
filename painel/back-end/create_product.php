<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        $sql = "INSERT INTO tb_products (shop_id, name, link, price, discount, video, description, categories, sku, checkout, button, redirect_url, seo_name, seo_link, seo_description) VALUES 
                                    (:shop_id, :name, :link, :price, :discount, :video, :description, :categories, :sku, :checkout, :button, :redirect_url, :seo_name, :seo_link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['shop_id']);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':link', $dados['link']);
        $stmt->bindParam(':price', $dados['price']);
        $stmt->bindParam(':discount', $dados['discount']);
        $stmt->bindParam(':video', $dados['video']);
        $stmt->bindParam(':description', $dados['description']);
        $stmt->bindParam(':categories', $dados['categories']);
        $stmt->bindParam(':sku', $dados['sku']);
        $stmt->bindParam(':checkout', $dados['checkout']);
        $stmt->bindParam(':button', $dados['button']);
        $stmt->bindParam(':redirect_url', $dados['redirect_url']);
        $stmt->bindParam(':seo_name', $dados['seo_name']);
        $stmt->bindParam(':seo_link', $dados['seo_link']);
        $stmt->bindParam(':seo_description', $dados['seo_description']);

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $conn_pdo->lastInsertId();

        $total = count($_FILES['imagens']['name']);

        // Loop através de cada arquivo
        for ($i = 0; $i < $total; $i++) {
            // Certifique-se de que a pasta para as imagens exista
            $uploadDir = "imagens/$ultimo_id/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = $_FILES['imagens']['name'][$i];
            $tmp_name = $_FILES['imagens']['tmp_name'][$i];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sqlInsertImagem = "INSERT INTO imagens (usuario_id, nome_imagem) VALUES (:usuario_id, :nome_imagem)";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':usuario_id', $ultimo_id);
                $stmtInsertImagem->bindParam(':nome_imagem', $fileName);

                if ($stmtInsertImagem->execute()) {
                    $_SESSION['msgcad'] = "<p class='green'>Usuário cadastrado com sucesso!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a imagem!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a imagem!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
            }
        }
    }