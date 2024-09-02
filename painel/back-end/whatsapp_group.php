<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $whatsapp_group = $_POST['whatsapp_group'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET whatsapp_group = :whatsapp_group WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':whatsapp_group', $whatsapp_group);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Link do grupo editado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "grupo-whatsapp");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar o link do grupo!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "grupo-whatsapp");
            exit;
        }
    }