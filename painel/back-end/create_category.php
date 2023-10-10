<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_categories';

        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];
        $name = $_POST['name'];
        $link = $_POST['link'];
        $description = $_POST['description'];
        $parent_category = $_POST['parent_category'];

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

        echo $status;
        echo $emphasis;

        $seo_name = $_POST['seo_name'];
        $seo_link = $_POST['seo_link'];
        $seo_description = $_POST['seo_description'];

        // Processar o upload de imagens
        $uploadDir = "category/$shop_id/";

        // Verifique se o diretório de upload existe, se não, crie-o
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = time() . '.jpg';
        $iconFileName = uniqid() . '.jpg';
        $uploadFile = $uploadDir . basename($fileName);
        $uploadIconFile = $uploadDir . basename($iconFileName);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile) && move_uploaded_file($_FILES['icon']['tmp_name'], $uploadIconFile)) {
            // Insere a categoria no banco de dados
            $sql = "INSERT INTO $tabela (shop_id, name, icon, image, link, description, parent_category, status, emphasis, seo_name, seo_link, seo_description) VALUES 
                                        (:shop_id, :name, :icon, :image, :link, :description, :parent_category, :status, :emphasis, :seo_name, :seo_link, :seo_description)";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':icon', $iconFileName);
            $stmt->bindValue(':image', $fileName);
            $stmt->bindValue(':link', $link);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':parent_category', $parent_category);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':emphasis', $emphasis);
            $stmt->bindValue(':seo_name', $seo_name);
            $stmt->bindValue(':seo_link', $seo_link);
            $stmt->bindValue(':seo_description', $seo_description);

            if ($stmt->execute()) {
                $_SESSION['msgcad'] = "<p class='green'>Categoria cadastrada com sucesso!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a categoria!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
                exit;
            }
        } else {
            // Insere a categoria no banco de dados
            $sql = "INSERT INTO $tabela (shop_id, name, link, description, parent_category, status, emphasis, seo_name, seo_link, seo_description) VALUES 
                                        (:shop_id, :name, :link, :description, :parent_category, :status, :emphasis, :seo_name, :seo_link, :seo_description)";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':link', $link);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':parent_category', $parent_category);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':emphasis', $emphasis);
            $stmt->bindValue(':seo_name', $seo_name);
            $stmt->bindValue(':seo_link', $seo_link);
            $stmt->bindValue(':seo_description', $seo_description);

            if ($stmt->execute()) {
                $_SESSION['msgcad'] = "<p class='green'>Categoria cadastrada com sucesso!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
                exit;
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a categoria!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
                exit;
            }
        }
    }