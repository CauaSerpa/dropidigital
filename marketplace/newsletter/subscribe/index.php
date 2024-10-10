<?php
    // Conecte-se ao banco de dados aqui
    require('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_newsletter';

        // Recebe os dados do formulário
        $shop_id = $_POST['id'];
        $email = $_POST['email'];

        $query = "SELECT email FROM $tabela WHERE shop_id = :shop_id AND email = :email";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->execute();

        if ($stmt->rowCount() < 1) {
            // Insere a categoria no banco de dados
            $sql = "INSERT INTO $tabela (shop_id, email) VALUES (:shop_id, :email)";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':shop_id', $shop_id);
            $stmt->bindValue(':email', $email);

            if ($stmt->execute()) {
                echo 'success'; // Envie 'success' como resposta se a inscrição for bem-sucedida
            } else {
                echo 'error';
            }
        } else {
            echo "error";
        }
    }