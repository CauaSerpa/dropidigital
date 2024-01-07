<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Shop
        $subdomain = $_POST['subdomain'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET url = :url WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':url', $subdomain);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Subdomínio editado com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar o subdomínio!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        }
    }