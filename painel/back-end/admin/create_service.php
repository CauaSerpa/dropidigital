<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $name = $_POST['name'];

        if (isset($_POST['status'])) {
            $status = 1;
        } else {
            $status = 0;
        }
        
        if (isset($_POST['emphasis'])) {
            $emphasis = 0;
        } else {
            $emphasis = 0;
        }

        // Checkbox sem preco
        if (isset($_POST["without_price"]))
        {
            $price = 0;
            $discount = 0;
            $without_price = 1;
        } else {
            $price = $_POST['price'];
            $discount = $_POST['discount'];
            $without_price = 0;
        }

        $video = $_POST['video'];
        $description = $_POST['description'];
        $tooltip_content = $_POST['tooltip_content'];
        $items_included = $_POST['itemsIncludedArray'];
        $sku = $_POST['sku'];
        $seo_name = $_POST['seo_name'];
        $link = $_POST['seo_link'];
        $seo_description = $_POST['seo_description'];

        //Tabela que será solicitada
        $tabela = 'tb_services';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (status, emphasis, name, price, without_price, discount, video, description, tooltip_content, items_included, sku, seo_name, link, seo_description) VALUES 
                                    (:status, :emphasis, :name, :price, :without_price, :discount, :video, :description, :tooltip_content, :items_included, :sku, :seo_name, :link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':emphasis', $emphasis);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':without_price', $without_price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':video', $video);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':tooltip_content', $tooltip_content);
        $stmt->bindParam(':items_included', $items_included);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':seo_name', $seo_name);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':seo_description', $seo_description);
        $stmt->execute();

        // Recebendo id da loja
        $service_id = $conn_pdo->lastInsertId();

        // Imagens
        $total = count($_FILES['imagens']['name']);

        // Loop através de cada arquivo
        for ($i = 0; $i < $total; $i++) {
            // Certifique-se de que a pasta para as imagens exista
            $uploadDir = "service/$service_id/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = $_FILES['imagens']['name'][$i];
            $tmp_name = $_FILES['imagens']['tmp_name'][$i];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sqlInsertImagem = "INSERT INTO tb_service_img (service_id, image) VALUES (:service_id, :image)";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':service_id', $service_id);
                $stmtInsertImagem->bindParam(':image', $fileName);

                if ($stmtInsertImagem->execute()) {
                    echo "Imagem " . $fileName . ", salva com sucesso";
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao salvar imagem do Serviço!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao salvar imagem do Serviço!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Serviço criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");
        exit;
    }
?>