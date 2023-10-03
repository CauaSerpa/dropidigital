<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $token = $_POST['token'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET token_instagram = :token_instagram WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':token_instagram', $token);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Instagram adicionado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "feed-instagram");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar o token do instagram!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "feed-instagram");
            exit;
        }
    }