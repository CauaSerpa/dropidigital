<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $facebook = $_POST['facebook'];
        $x = $_POST['x'];
        $pinterest = $_POST['pinterest'];
        $instagram = $_POST['instagram'];
        $youtube = $_POST['youtube'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET facebook = :facebook, x = :x, pinterest = :pinterest, instagram = :instagram, youtube = :youtube WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':facebook', $facebook);
        $stmt->bindValue(':x', $x);
        $stmt->bindValue(':pinterest', $pinterest);
        $stmt->bindValue(':instagram', $instagram);
        $stmt->bindValue(':youtube', $youtube);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Redes sociais editadas com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "redes-sociais");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar as redes sociais!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "redes-sociais");
            exit;
        }
    }