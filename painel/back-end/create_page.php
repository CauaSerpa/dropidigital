<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_pages';
        
        //Id da loja
        $shop_id = $_POST['shop_id'];

        if (isset($_POST['status']) && $_POST['status'] == '1') {
            $status = $_POST['status'];
        } else {
            $status = 0;
        }

        // Recebe os dados do formulário
        $name = $_POST['name'];
        $link = $_POST['link'];
        $content = $_POST['content'];

        $seo_name = $_POST['seo_name'];
        $seo_link = $_POST['seo_link'];
        $seo_description = $_POST['seo_description'];

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        // Insere a categoria no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, status, name, link, content, seo_name, seo_link, seo_description) VALUES 
                                    (:shop_id, :status, :name, :link, :content, :seo_name, :seo_link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':link', $link);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':seo_name', $seo_name);
        $stmt->bindValue(':seo_link', $seo_link);
        $stmt->bindValue(':seo_description', $seo_description);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Página criada com sucesso!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao criar a página!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
            exit;
        }
    }