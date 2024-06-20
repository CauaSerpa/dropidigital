<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $id = $_POST['id'];
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

        // Radio select
        if ($_POST['select'] == 'video') {
            $video = $_POST['video'];
            $image = null;
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
        $sql = "UPDATE $tabela SET status = :status, emphasis = :emphasis, name = :name, price = :price, without_price = :without_price, discount = :discount,
                                    video = :video, description = :description, tooltip_content = :tooltip_content, items_included = :items_included,
                                    sku = :sku, seo_name = :seo_name, link = :link, seo_description = :seo_description
                WHERE id = :id";
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

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $service_id = $_POST['id'];

        // Diretório para salvar a imagem de 'card_image'
        $diretorioImage = "./service/$service_id/card-image/";

        // Certifique-se de que os diretórios de destino existam
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['card_image']['error'] !== 4) {
            // Deletar todas as imagens existentes na pasta
            $files = glob($diretorioImage . '*'); // Obtém todos os arquivos na pasta
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Exclui o arquivo
                }
            }

            // Cadastra nova imagem para 'image'
            $fileName = time() . '.jpg';
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($_FILES['card_image']['tmp_name'], $uploadFile)) {
                $tabela = "tb_services";
                $sql = "UPDATE $tabela SET card_image = :card_image WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);

                $stmt->bindValue(':card_image', $fileName);
                $stmt->bindValue(':id', $service_id);

                $stmt->execute();
            }
        }

        // Imagem
        if ($_POST['select'] == 'image') {
            // Diretório para salvar as imagens de 'image'
            $diretorioImage = "./service/$service_id/image/";

            // Certifique-se de que os diretórios de destino existam
            if (!is_dir($diretorioImage)) {
                mkdir($diretorioImage, 0755, true);
            }

            // Verifique se o campo de upload de imagens não está vazio para 'image'
            if ($_FILES['image']['error'] !== 4) {
                // Deletar todas as imagens existentes na pasta
                $files = glob($diretorioImage . '*'); // Obtém todos os arquivos na pasta
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); // Exclui o arquivo
                    }
                }

                // Cadastra nova imagem para 'image'
                $fileName = time() . '.jpg';
                $uploadFile = $diretorioImage . basename($fileName);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $tabela = "tb_services";
                    $sql = "UPDATE $tabela SET image = :image WHERE id = :id";
                    $stmt = $conn_pdo->prepare($sql);
    
                    $stmt->bindValue(':image', $fileName);
                    $stmt->bindValue(':id', $service_id);
    
                    $stmt->execute();
                }
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Site Pronto criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "servicos");
        exit;
    }
?>