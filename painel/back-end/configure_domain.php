<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    date_default_timezone_set('America/Sao_Paulo');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $id = $_POST['id'];
        $shop_id = $_POST['shop_id'];

        // Obtem a data e hora atual
        $current_date = date('Y-m-d H:i:s');

        //Tabela que será solicitada
        $tabela = 'tb_domains';

        // Insere o dominio no banco de dados
        $sql = "UPDATE $tabela SET configure = :configure, configure_date = :configure_date WHERE id = :id AND shop_id = :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':configure', 1);
        $stmt->bindValue(':configure_date', $current_date);

        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':shop_id', $shop_id);

        if ($stmt->execute()) {
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao ativar o domínio!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
            exit;
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Por favor preencha todos os campos!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
        exit;
    }