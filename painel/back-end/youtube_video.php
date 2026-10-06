<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $video = $_POST['video'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET video = :video WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':video', $video);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Vídeo editado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "video-youtube");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar o vídeo!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "video-youtube");
            exit;
        }
    }