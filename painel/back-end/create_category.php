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

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        // Insere a categoria no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, name, description, parent_category, status, emphasis, seo_name, seo_link, seo_description) VALUES 
                                    (:shop_id, :name, :description, :parent_category, :status, :emphasis, :seo_name, :seo_link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':name', $name);
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