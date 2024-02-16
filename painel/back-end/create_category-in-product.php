<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Verifique se a solicitação é do tipo POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        $name = $_POST['name'];
        $link = $_POST['link'];

        $status = 1;
        $parent_category = 1;

        // Tabela que será solicitada
        $tabela = 'tb_categories';

        // Insere a categoria no banco de dados da loja
        $sql = "INSERT INTO $tabela (shop_id, name, link, parent_category, status, seo_name, seo_link) VALUES 
                                    (:shop_id, :name, :link, :parent_category, :status, :seo_name, :seo_link)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':link', $link);
        $stmt->bindValue(':parent_category', $parent_category);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':seo_name', $name);
        $stmt->bindValue(':seo_link', $link);

        $stmt->execute();

        $id = $conn_pdo->lastInsertId();

        // Construa a resposta JSON
        $response = array(
            'success' => true,
            'data' => array(
                'id' => $id,
                'name' => $name
            )
        );

        // Saída da resposta JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        // Se a solicitação não for do tipo POST, retorne uma resposta de erro
        $response = array('success' => false, 'message' => 'Método de solicitação inválido.');
        echo json_encode($response);
    }
?>