<?php
    include_once('../../../config.php');

    if (!empty($_SESSION['user_id'])) {
        session_start();

        $_SESSION['create_new_shop'] = true;
        $_SESSION['user_id_for_create_shop'] = $_SESSION['user_id'];

        // Tabela que sera feita a consulta
        $tabela = "tb_users";

        // Consulta SQL
        $sql = "SELECT email FROM $tabela WHERE id = :id";

        // Preparar a consulta
        $stmt = $conn_pdo->prepare($sql);

        // Vincular o valor do parâmetro
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        // Executar a consulta
        $stmt->execute();

        // Obter o resultado como um array associativo
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o resultado foi encontrado
        if ($resultado) {
            $_SESSION['email'] = $resultado['email'];
        }

        header("Location: " . INCLUDE_PATH_DASHBOARD . "criar-loja");
        exit();
    }