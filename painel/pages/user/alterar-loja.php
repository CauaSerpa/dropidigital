<?php
    $shop_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $id = $_SESSION['user_id'];

    if (!empty($shop_id)) {
        // Altera a tabela tb_users com o shop_id selecionado
        // Tabela que será solicitada
        $tabela = 'tb_users';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET last_shop_login = :last_shop_login WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':last_shop_login', $shop_id);

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        // Passa o shop_id no session
        $_SESSION['shop_id'] = $shop_id;

        header("Location: " . INCLUDE_PATH_DASHBOARD);
        exit();
    } else {
        header("Location: " . INCLUDE_PATH_DASHBOARD);
        exit();
    }